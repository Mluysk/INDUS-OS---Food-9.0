<?php
namespace App\Modules\Producao\Controllers;

use App\Core\Controller;
use App\Services\ProducaoService;

class ProducaoController extends Controller
{
    private ProducaoService $producao;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        ProducaoService $producao
    ) {
        parent::__construct($container, $auth, $audit);
        $this->producao = $producao;
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $ops = $this->producao->listar();
        $planejamento = $this->producao->planejamento();
        $inicioMapa = date('Y-m-d');
        $fimMapa = date('Y-m-d', strtotime('+4 days'));
        $planejadoMapa = $this->producao->planejadoMapa($inicioMapa, $fimMapa);
        $relatorioOp = $this->montarRelatorioOp($planejamento, $planejadoMapa, $inicioMapa, $fimMapa);
        $relatorioOp = $this->filtrarRelatorioOpComValores($relatorioOp);
        $datasSelecionadas = $relatorioOp['datas'];

        $opQuantidades = [];
        foreach ($ops as $op) {
            if (isset($op['etapa']) && in_array($op['etapa'], ['finalizada', 'cancelada'], true)) {
                continue;
            }

            $pid = (int)($op['produto_id'] ?? 0);
            if ($pid <= 0) {
                continue;
            }

            $dataBase = $op['data_op'] ?? ($op['created_at'] ?? null);
            $dataNormalizada = $dataBase && strtotime((string)$dataBase) ? date('Y-m-d', strtotime((string)$dataBase)) : date('Y-m-d');

            $opQuantidades[$pid][$dataNormalizada] = ($opQuantidades[$pid][$dataNormalizada] ?? 0) + (float)($op['quantidade'] ?? 0);
        }

        if ($this->wantsJson()) {
            $this->json([
                'ordens' => $ops,
                'planejamento' => $planejamento,
                'op_quantidades' => $opQuantidades,
                'planejado_mapa' => $planejadoMapa,
                'relatorio_op' => $relatorioOp,
                'datas_selecionadas' => $datasSelecionadas,
            ]);
            return;
        }

        $this->render('producao/index', [
            'ops' => $ops,
            'planejamento' => $planejamento,
            'opQuantidades' => $opQuantidades,
            'planejadoMapa' => $planejadoMapa,
            'relatorioOp' => $relatorioOp,
            'datasSelecionadas' => $datasSelecionadas,
        ], [
            'title' => 'Ordens de produção',
            'activeMenu' => 'producao',
            'breadcrumbs' => ['Operações', 'Produção'],
        ]);
    }

    public function store(): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = $this->producao->criar($data, $this->auth->user()['id']);
        if ($this->wantsJson()) {
            $this->json(['id' => $id], 201);
        } else {
            $this->setFlash('success', 'Ordem de produção criada.');
            $this->redirect('/producao/op');
        }
    }

    public function iniciar($id): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $this->producao->iniciar((int)$id, $this->auth->user()['id']);
        if ($this->wantsJson()) {
            $this->json(['message' => 'OP iniciada']);
        } else {
            $this->setFlash('success', 'OP iniciada com sucesso.');
            $this->redirect('/producao/op');
        }
    }

    public function finalizar($id): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $this->producao->finalizar((int)$id, $data['lote'], $data['validade'] ?? null, $this->auth->user()['id']);
        if ($this->wantsJson()) {
            $this->json(['message' => 'OP finalizada']);
        } else {
            $this->setFlash('success', 'OP finalizada e estoque atualizado.');
            $this->redirect('/producao/op');
        }
    }

    public function imprimir(): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $payload = $this->input();
        $planejamento = $this->producao->planejamento();
        $selecionados = [];

        $producoes = $payload['producao'] ?? [];

        foreach ($planejamento as $produto) {
            $pid = (int)($produto['id'] ?? 0);
            if (!isset($producoes[$pid])) {
                continue;
            }

            $entrada = $producoes[$pid];
            $dias = $entrada['dias'] ?? [];
            $somado = 0;
            $limpos = [];

            foreach ((array)$dias as $dia) {
                if (!is_array($dia)) {
                    continue;
                }

                $valor = (float)str_replace(',', '.', (string)($dia['quantidade'] ?? 0));
                if ($valor <= 0) {
                    continue;
                }

                $dataRaw = $dia['data'] ?? null;
                $timestamp = $dataRaw && strtotime((string)$dataRaw) ? strtotime((string)$dataRaw) : time();
                $dataNormalizada = date('Y-m-d', $timestamp);

                $somado += $valor;
                $limpos[] = [
                    'data' => $dataNormalizada,
                    'quantidade' => $valor,
                ];
            }

            if ($somado <= 0) {
                continue;
            }

            $produto['quantidade_produzir'] = $somado;
            $produto['volumes'] = $limpos;
            $produto['data_planejada'] = $limpos[0]['data'] ?? null;
            $selecionados[] = $produto;
        }
        $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'documento', 'producao', null, [
            'documento' => 'planejamento_producao',
            'quantidade' => count($selecionados),
        ]);

        $this->render('producao/print', [
            'items' => $selecionados,
            'emitidoEm' => date('d/m/Y H:i'),
        ], [
            'title' => 'Planejamento de produção',
            'layout' => 'layouts/print',
        ]);
    }

    public function relatorioOp(): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $payload = $this->input();
        $datasSelecionadas = array_values(array_filter((array)($payload['datas'] ?? [])));
        $inicioMapa = date('Y-m-d');
        $fimMapa = date('Y-m-d', strtotime('+4 days'));
        $planejamento = $this->producao->planejamento();
        $planejadoMapa = $this->producao->planejadoMapa($inicioMapa, $fimMapa);
        $relatorioOp = $this->montarRelatorioOp($planejamento, $planejadoMapa, $inicioMapa, $fimMapa);
        if (!empty($datasSelecionadas)) {
            $relatorioOp = $this->filtrarRelatorioOp($relatorioOp, $datasSelecionadas);
        }
        $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'documento', 'producao', null, [
            'documento' => 'relatorio_op',
            'periodo' => ['inicio' => $inicioMapa, 'fim' => $fimMapa],
            'datas_selecionadas' => $datasSelecionadas,
            'quantidade' => count($relatorioOp),
        ]);

        $this->render('producao/relatorio-op', [
            'relatorioOp' => $relatorioOp,
            'periodo' => ['inicio' => $inicioMapa, 'fim' => $fimMapa],
            'datasSelecionadas' => $datasSelecionadas,
        ], [
            'title' => 'Relatório de OP',
            'layout' => 'layouts/print',
        ]);
    }

    public function entradas(): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $planejamento = $this->producao->planejamento();

        $this->render('producao/entradas', [
            'planejamento' => $planejamento,
        ], [
            'title' => 'Produção diária',
            'activeMenu' => 'producao',
            'breadcrumbs' => ['Operações', 'Produção', 'Entrada diária'],
        ]);
    }

    private function montarRelatorioOp(array $planejamento, array $planejadoMapa, string $inicio, string $fim): array
    {
        $datas = [];
        $cursor = strtotime($inicio);
        $fimTs = strtotime($fim);
        while ($cursor !== false && $cursor <= $fimTs) {
            $datas[] = date('Y-m-d', $cursor);
            $cursor = strtotime('+1 day', $cursor);
        }

        $mapQuantidades = [];
        foreach ($planejadoMapa as $item) {
            $pid = (int)($item['produto_id'] ?? 0);
            if ($pid <= 0) {
                continue;
            }
            foreach ((array)($item['dias'] ?? []) as $dia) {
                $data = $dia['data'] ?? null;
                if (!$data) {
                    continue;
                }
                $mapQuantidades[$pid][$data] = (float)($dia['quantidade'] ?? 0);
            }
        }

        $linhas = [];
        foreach ($planejamento as $produto) {
            $pid = (int)($produto['id'] ?? 0);
            if ($pid <= 0) {
                continue;
            }
            $linhaDatas = [];
            foreach ($datas as $data) {
                $linhaDatas[$data] = (float)($mapQuantidades[$pid][$data] ?? 0);
            }
            $linhas[] = [
                'sku' => $produto['sku'] ?? '',
                'nome' => $produto['nome'] ?? '',
                'unidade' => $produto['unidade'] ?? '',
                'estoque' => (float)($produto['estoque'] ?? 0),
                'datas' => $linhaDatas,
            ];
        }

        return [
            'datas' => $datas,
            'linhas' => $linhas,
        ];
    }

    private function filtrarRelatorioOp(array $relatorio, array $datasSelecionadas): array
    {
        $datasSelecionadas = array_values(array_intersect($relatorio['datas'] ?? [], $datasSelecionadas));
        if (empty($datasSelecionadas)) {
            return $relatorio;
        }

        $linhas = [];
        foreach ((array)($relatorio['linhas'] ?? []) as $linha) {
            $datas = [];
            foreach ($datasSelecionadas as $data) {
                $datas[$data] = (float)($linha['datas'][$data] ?? 0);
            }
            $linha['datas'] = $datas;
            $linhas[] = $linha;
        }

        return [
            'datas' => $datasSelecionadas,
            'linhas' => $linhas,
        ];
    }

    private function filtrarRelatorioOpComValores(array $relatorio): array
    {
        $datas = $relatorio['datas'] ?? [];
        if (empty($datas)) {
            return $relatorio;
        }

        $datasComValores = [];
        foreach ($datas as $data) {
            $temValor = false;
            foreach ((array)($relatorio['linhas'] ?? []) as $linha) {
                if ((float)($linha['datas'][$data] ?? 0) > 0) {
                    $temValor = true;
                    break;
                }
            }
            if ($temValor) {
                $datasComValores[] = $data;
            }
        }

        if (empty($datasComValores)) {
            return $relatorio;
        }

        $linhas = [];
        foreach ((array)($relatorio['linhas'] ?? []) as $linha) {
            $datasFiltradas = [];
            foreach ($datasComValores as $data) {
                $datasFiltradas[$data] = (float)($linha['datas'][$data] ?? 0);
            }
            $linha['datas'] = $datasFiltradas;
            $linhas[] = $linha;
        }

        return [
            'datas' => $datasComValores,
            'linhas' => $linhas,
        ];
    }
}
