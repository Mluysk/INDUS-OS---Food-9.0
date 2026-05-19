<?php
namespace App\Modules\Estoque\Controllers;

use App\Core\Controller;
use App\Services\EstoqueService;
use App\Repositories\MapaEntradasRepository;
use App\Services\ConfigService;

class EstoqueController extends Controller
{
    private EstoqueService $estoque;
    private MapaEntradasRepository $mapaEntradas;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        EstoqueService $estoque,
        MapaEntradasRepository $mapaEntradas
    ) {
        parent::__construct($container, $auth, $audit);
        $this->estoque = $estoque;
        $this->mapaEntradas = $mapaEntradas;
    }

    public function posicao(): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        [$posicao, $status, $faixa] = $this->carregarPosicao();

        if ($this->wantsJson()) {
            $this->json($posicao);
            return;
        }

        $alertas = [];
        try {
            $dias = (int)($this->container->getConfig()['validade_alerta_dias'] ?? 15);
            $alertas = $this->estoque->alertasValidade($dias);
        } catch (\Throwable $e) {
            $alertas = [];
        }

        $estoqueAvancado = [];
        try {
            $sql = 'SELECT
                        e.id AS estoque_id,
                        p.id AS produto_id,
                        p.sku,
                        p.nome,
                        p.tipo,
                        e.lote,
                        e.validade,
                        e.quantidade,
                        e.reservado,
                        (e.quantidade - e.reservado) AS saldo,
                        pl.fornecedor,
                        pl.data_entrega,
                        pl.data_fabricacao,
                        pl.data_vencimento,
                        consumo.ultimo_consumo
                    FROM estoques e
                    JOIN produtos p ON p.id = e.produto_id
                    LEFT JOIN (
                        SELECT pl1.*
                        FROM produtos_lancamentos pl1
                        JOIN (
                            SELECT produto_id, lote, MAX(id) AS max_id
                            FROM produtos_lancamentos
                            GROUP BY produto_id, lote
                        ) latest ON latest.max_id = pl1.id
                    ) pl ON pl.produto_id = e.produto_id AND pl.lote <=> e.lote
                    LEFT JOIN (
                        SELECT produto_id, lote, MAX(created_at) AS ultimo_consumo
                        FROM movimentos_estoque
                        WHERE tipo = "saida" AND status = "efetivado"
                        GROUP BY produto_id, lote
                    ) consumo ON consumo.produto_id = e.produto_id AND consumo.lote <=> e.lote
                    ORDER BY p.nome ASC, e.lote ASC';
            $stmt = $this->estoque->getConnection()->query($sql);
            $estoqueAvancado = $stmt->fetchAll();
        } catch (\Throwable $e) {
            $estoqueAvancado = [];
        }

        $this->render('estoque/posicao', [
            'posicao' => $posicao,
            'alertas' => $alertas,
            'estoqueAvancado' => $estoqueAvancado,
            'status' => $status,
            'faixaBaixa' => $faixa,
        ], [
            'title' => 'Posição de estoque',
            'activeMenu' => 'estoque',
            'breadcrumbs' => ['Operações', 'Estoque'],
        ]);
    }

    public function imprimir(): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        [$posicao, , $faixa] = $this->carregarPosicao();

        $filters = [
            'q' => trim((string)($_GET['q'] ?? '')),
            'tipo' => strtoupper(trim((string)($_GET['tipo'] ?? ''))),
            'status' => strtoupper(trim((string)($_GET['status'] ?? ''))),
        ];

        $filtered = [];
        foreach ($posicao as $linha) {
            $saldo = (float)($linha['saldo'] ?? 0);
            $statusKey = $this->classificarSaldo($saldo, $faixa);

            if ($filters['q'] !== '') {
                $haystack = strtolower(($linha['sku'] ?? '').' '.($linha['nome'] ?? ''));
                if (!str_contains($haystack, strtolower($filters['q']))) {
                    continue;
                }
            }

            if (in_array($filters['tipo'], ['PA','MP'], true) && ($linha['tipo'] ?? '') !== $filters['tipo']) {
                continue;
            }

            if (in_array($filters['status'], ['NEG','ZERO','LOW','OK'], true) && $filters['status'] !== $statusKey) {
                continue;
            }

            $linha['status_key'] = $statusKey;
            $filtered[] = $linha;
        }

        $status = ['negativos' => 0, 'zerados' => 0, 'baixos' => 0];
        foreach ($filtered as $linha) {
            $saldo = (float)($linha['saldo'] ?? 0);
            if ($saldo < 0) {
                $status['negativos']++;
                continue;
            }
            if ($saldo === 0.0) {
                $status['zerados']++;
                continue;
            }
            if ($saldo >= ($faixa['min'] ?? 0) && $saldo <= ($faixa['max'] ?? 0)) {
                $status['baixos']++;
            }
        }
        $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'documento', 'estoque', null, [
            'documento' => 'posicao_estoque',
            'quantidade' => count($filtered),
            'filtros' => $filters,
        ]);

        $this->render('estoque/print', [
            'posicao' => $filtered,
            'status' => $status,
            'faixaBaixa' => $faixa,
            'filters' => $filters,
        ], [
            'title' => 'Posição de estoque',
            'layout' => 'layouts/print',
        ]);
    }

    private function carregarPosicao(): array
    {
        /** @var ConfigService $configService */
        $configService = $this->container->get(ConfigService::class);
        $sql = 'SELECT
                    p.id   AS produto_id,
                    p.nome,
                    p.sku,
                    p.tipo,
                    COALESCE(m.saldo, 0)      AS quantidade,
                    COALESCE(m.reservado, 0)  AS reservado
                FROM produtos p
                LEFT JOIN (
                    SELECT
                        produto_id,
                        SUM(CASE
                                WHEN status = "efetivado" AND tipo = "entrada" THEN quantidade
                                WHEN status = "efetivado" AND tipo = "saida" THEN -quantidade
                                ELSE 0
                            END) AS saldo,
                        SUM(CASE WHEN status = "reservado" THEN quantidade ELSE 0 END) AS reservado
                    FROM movimentos_estoque
                    GROUP BY produto_id
                ) m ON m.produto_id = p.id
                ORDER BY p.nome ASC';
        $stmt = $this->estoque->getConnection()->query($sql);
        $posicao = $stmt->fetchAll();

        $planejado = $this->mapaEntradas->planejadoFuturo();
        foreach ($posicao as &$linha) {
            $pid = (int)($linha['produto_id'] ?? 0);
            $saldo = (float)($linha['quantidade'] ?? 0) - (float)($linha['reservado'] ?? 0);
            $linha['saldo'] = $saldo;
            $linha['planejado_mapa'] = (float)($planejado[$pid] ?? 0);
        }
        unset($linha);

        $lowMin = (int)($configService->get('ESTOQUE_BAIXO_MIN', '4') ?: 4);
        $lowMax = (int)($configService->get('ESTOQUE_BAIXO_MAX', '8') ?: 8);
        if ($lowMin < 0) {
            $lowMin = 0;
        }
        if ($lowMax < $lowMin) {
            $lowMax = $lowMin;
        }

        $status = ['negativos' => 0, 'zerados' => 0, 'baixos' => 0];
        foreach ($posicao as &$linha) {
            $statusKey = $this->classificarSaldo((float)($linha['saldo'] ?? 0), ['min' => $lowMin, 'max' => $lowMax]);
            $linha['status_key'] = $statusKey;
            if ($statusKey === 'NEG') {
                $status['negativos']++;
            } elseif ($statusKey === 'ZERO') {
                $status['zerados']++;
            } elseif ($statusKey === 'LOW') {
                $status['baixos']++;
            }
        }
        unset($linha);

        return [$posicao, $status, ['min' => $lowMin, 'max' => $lowMax]];
    }

    private function classificarSaldo(float $saldo, array $faixa): string
    {
        if ($saldo < 0) {
            return 'NEG';
        }
        if ($saldo === 0.0) {
            return 'ZERO';
        }
        if ($saldo >= ($faixa['min'] ?? 0) && $saldo <= ($faixa['max'] ?? 0)) {
            return 'LOW';
        }
        return 'OK';
    }

    public function movimentar(): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = $this->estoque->registrarMovimento($data, $this->auth->user()['id']);
        if ($this->wantsJson()) {
            $this->json(['id' => $id], 201);
        } else {
            $this->setFlash('success', 'Movimento registrado no estoque.');
            $this->redirect('/estoque/posicao');
        }
    }
}
