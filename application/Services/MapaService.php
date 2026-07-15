<?php
namespace App\Services;

use App\Repositories\ProdutosRepository;
use App\Repositories\PedidosRepository;
use App\Repositories\EstoquesRepository;
use App\Repositories\MapaEntradasRepository;
use App\Services\EstoqueService;
use App\Services\ConfigService;
use App\Services\RecipeService;
use DateInterval;
use DateTimeImmutable;

class MapaService
{
    private ProdutosRepository $produtos;
    private PedidosRepository $pedidos;
    private EstoquesRepository $estoques;
    private MapaEntradasRepository $entradas;
    private EstoqueService $estoqueService;
    private ConfigService $config;
    private RecipeService $recipes;

    public function __construct(
        ProdutosRepository $produtos,
        PedidosRepository $pedidos,
        EstoquesRepository $estoques,
        MapaEntradasRepository $entradas,
        EstoqueService $estoqueService,
        ConfigService $config,
        RecipeService $recipes
    ) {
        $this->produtos = $produtos;
        $this->pedidos = $pedidos;
        $this->estoques = $estoques;
        $this->entradas = $entradas;
        $this->estoqueService = $estoqueService;
        $this->config = $config;
        $this->recipes = $recipes;
    }

    public function gerar(?string $referencia = null, ?string $inicio = null, bool $completo = false, ?string $mes = null): array
    {
        $hoje = new DateTimeImmutable('today');
        $base = $hoje;
        if ($referencia && ($refDate = DateTimeImmutable::createFromFormat('Y-m-d', $referencia))) {
            $base = $refDate;
        }
        if ($inicio && ($inicioDate = DateTimeImmutable::createFromFormat('Y-m-d', $inicio))) {
            $inicioJanela = $inicioDate;
            $base = $inicioDate;
        } else {
            // Janela padrão: 2 dias antes e 4 depois do dia base
            $inicioJanela = $base->modify('-2 days');
        }

        $janelaFim = $inicioJanela->modify('+6 days');

        if ($completo) {
            $mesRef = $mes && preg_match('/^\d{4}-\d{2}$/', $mes)
                ? DateTimeImmutable::createFromFormat('!Y-m', $mes)
                : false;
            if ($mesRef instanceof DateTimeImmutable) {
                $inicioJanela = $mesRef->modify('first day of this month');
                $janelaFim = $mesRef->modify('last day of this month');
            } else {
                $anoRef = (int) $inicioJanela->format('Y');
                $inicioJanela = $inicioJanela->setDate($anoRef, 1, 1);
                $janelaFim = $inicioJanela->setDate($anoRef, 12, 31);
            }
        }
        $hojeKey = $hoje->format('Y-m-d');
        $weekendLegacy = $this->config->get('PERFORMANCE_INCLUDE_WEEKENDS', '1') === '1';
        $includeSaturday = $this->config->get('PERFORMANCE_INCLUDE_SATURDAY', $weekendLegacy ? '1' : '0') === '1';
        $includeSunday = $this->config->get('PERFORMANCE_INCLUDE_SUNDAY', $weekendLegacy ? '1' : '0') === '1';
        $datas = [];
        $abreviacoes = [
            'Sun' => 'DOM',
            'Mon' => 'SEG',
            'Tue' => 'TER',
            'Wed' => 'QUA',
            'Thu' => 'QUI',
            'Fri' => 'SEX',
            'Sat' => 'SAB',
        ];

        $diasJanela = $janelaFim->diff($inicioJanela)->days ?? 0;
        for ($i = 0; $i <= $diasJanela; $i++) {
            $dia = $inicioJanela->modify('+' . $i . ' days');
            $sigla = $dia->format('D');
            $datas[$dia->format('Y-m-d')] = [
                'label' => $dia->format('d/m'),
                'semana' => $abreviacoes[$sigla] ?? strtoupper($sigla),
                'is_hoje' => $dia->format('Y-m-d') === $hojeKey,
            ];
        }
        ksort($datas);

        $janelaInicio = array_key_first($datas);
        $janelaFim = array_key_last($datas);

        $saidas = $this->buscarSaidas($janelaInicio, $janelaFim);
        $entradas = $this->entradas->porJanela($janelaInicio, $janelaFim);
        $planejadas = $entradas['planejado'] ?? [];
        $entradasEstoque = $entradas['estoque'] ?? [];
        $diasSemProducao = $this->diasSemProducao($janelaInicio, $janelaFim);
        foreach ($diasSemProducao as $dia => $registro) {
            if (isset($datas[$dia])) {
                $datas[$dia]['sem_producao'] = true;
                $datas[$dia]['sem_producao_motivo'] = $registro['motivo'] ?? 'outro';
            }
        }
        $mediasDiarias = $this->calcularMediasDiarias($planejadas, $entradasEstoque, array_keys($datas), $includeSaturday, $includeSunday, $diasSemProducao);

        $produtos = $this->produtos->all(['tipo' => 'PA']);
        usort($produtos, static fn($a, $b) => strnatcasecmp((string)($a['sku'] ?? ''), (string)($b['sku'] ?? '')));
        $estoquesPorProduto = $this->estoquesResumidos(array_values(array_filter(array_map(
            static fn(array $produto): int => (int)($produto['id'] ?? 0),
            $produtos
        ))));

        $lowMin = (int)($this->config->get('ESTOQUE_BAIXO_MIN', '4') ?: 4);
        $lowMax = (int)($this->config->get('ESTOQUE_BAIXO_MAX', '8') ?: 8);
        if ($lowMin < 0) {
            $lowMin = 0;
        }
        if ($lowMax < $lowMin) {
            $lowMax = $lowMin;
        }

        $linhas = [];
        $status = [
            'negativos' => 0,
            'zerados' => 0,
            'baixos' => 0,
        ];

        foreach ($produtos as $produto) {
            $produtoId = (int)($produto['id'] ?? 0);
            $estoque = $estoquesPorProduto[$produtoId] ?? ['estoque' => 0.0, 'reservado' => 0.0];
            $planejadoProduto = $planejadas[$produtoId] ?? [];
            $entradaEstoqueProduto = $entradasEstoque[$produtoId] ?? [];

            $diasLinha = [];
            // O mapa parte de saldo zero para não propagar entradas futuras para
            // dias anteriores; apenas o dia da entrada recebe o incremento e os
            // dias seguintes carregam o saldo já movimentado.
            $saldoReal = 0.0;
            $ultimoSaldoMovimentado = $saldoReal;
            foreach (array_keys($datas) as $dia) {
                $saidaDia = (float)($saidas[$produtoId][$dia] ?? 0);
                $entradaPlanejada = (float)($planejadoProduto[$dia] ?? 0);
                $entradaEstoqueDia = (float)($entradaEstoqueProduto[$dia] ?? 0);
                $entradaEfetiva = $entradaEstoqueDia;

                $temMovimentacao = $saidaDia !== 0.0 || $entradaPlanejada !== 0.0 || $entradaEstoqueDia !== 0.0;
                $conclusaoProducao = $entradaPlanejada > 0 ? ($entradaEfetiva / $entradaPlanejada) * 100 : 0;

                // Se houver movimentação, recalculamos; caso contrário, apenas exibimos o
                // último saldo movimentado sem criar efeito cascata.
                if ($temMovimentacao) {
                    $estoqueInicial = $ultimoSaldoMovimentado;
                    $saldoDia = $estoqueInicial + $entradaEstoqueDia - $saidaDia;
                    $ultimoSaldoMovimentado = $saldoDia;
                } else {
                    $estoqueInicial = $ultimoSaldoMovimentado;
                    $saldoDia = $ultimoSaldoMovimentado;
                }

                $estoquePosSaida = $estoqueInicial + $entradaEstoqueDia - $saidaDia;

                $diasLinha[$dia] = [
                    'saida' => $saidaDia,
                    'entrada_planejada' => $entradaPlanejada,
                    'entrada_estoque' => $entradaEstoqueDia,
                    'entrada_total' => $entradaEstoqueDia + $entradaPlanejada,
                    'producao_percentual' => $conclusaoProducao,
                    'estoque_inicial' => $estoqueInicial,
                    'estoque_pos_saida' => $estoquePosSaida,
                    'estoque_final' => $saldoDia,
                    'sobra' => $saldoDia,
                ];

            }
            $linhas[] = [
                'id' => $produtoId,
                'sku' => $produto['sku'] ?? '—',
                'nome' => $produto['nome'] ?? '',
                'categoria' => $produto['categoria'] ?? '',
                'estoque' => $estoque['estoque'],
                'reservado' => $estoque['reservado'],
                'dias' => $diasLinha,
            ];

            $avaliarSaldo = $saldoReal;
            if (isset($diasLinha[$hojeKey])) {
                $avaliarSaldo = (float)($diasLinha[$hojeKey]['estoque_final'] ?? $saldoReal);
            }

            if ($avaliarSaldo < 0) {
                $status['negativos']++;
            } elseif (abs($avaliarSaldo) < 1e-6) {
                $status['zerados']++;
            } elseif ($avaliarSaldo >= $lowMin && $avaliarSaldo <= $lowMax) {
                $status['baixos']++;
            }
        }

        $mesSoma = [];
        $mesCount = [];
        $anoSoma = [];
        $anoCount = [];

        foreach (array_keys($datas) as $dia) {
            $mediaDia = $mediasDiarias[$dia] ?? 0.0;
            $mediasDiarias[$dia] = $mediaDia;
            $datas[$dia]['media_conclusao'] = $mediaDia;

            $ts = strtotime($dia);
            $mesKey = date('Y-m', $ts);
            $anoKey = date('Y', $ts);

            if ($this->diaConsiderado($dia, $includeSaturday, $includeSunday) && !isset($diasSemProducao[$dia])) {
                $mesSoma[$mesKey] = ($mesSoma[$mesKey] ?? 0) + $mediaDia;
                $mesCount[$mesKey] = ($mesCount[$mesKey] ?? 0) + 1;

                $anoSoma[$anoKey] = ($anoSoma[$anoKey] ?? 0) + $mediaDia;
                $anoCount[$anoKey] = ($anoCount[$anoKey] ?? 0) + 1;
            }
        }

        $meses = [];
        foreach ($mesSoma as $key => $sum) {
            $parts = explode('-', $key);
            $ano = (int)($parts[0] ?? date('Y'));
            $mes = (int)($parts[1] ?? date('m'));
            $meses[$key] = [
                'label' => sprintf('%02d/%04d', $mes, $ano),
                'mes' => $mes,
                'ano' => $ano,
                'media' => round($sum / max(1, ($mesCount[$key] ?? 1)), 1),
            ];
        }

        $anos = [];
        foreach ($anoSoma as $key => $sum) {
            $anos[$key] = [
                'ano' => (int)$key,
                'media' => round($sum / max(1, ($anoCount[$key] ?? 1)), 1),
            ];
        }

        return [
            'datas' => $datas,
            'medias' => $mediasDiarias,
            'linhas' => $linhas,
            'status' => $status,
            'hoje' => $hojeKey,
            'base' => $base->format('Y-m-d'),
            'janela_inicio' => $janelaInicio,
            'janela_fim' => $janelaFim,
            'low_min' => $lowMin,
            'low_max' => $lowMax,
            'meses' => $meses,
            'anos' => $anos,
            'full' => $completo,
        ];
    }

    /**
     * Calcula a média diária de conclusão de produção (entrada de estoque / produção planejada)
     * para cada data fornecida. Apenas dias com produção planejada são considerados na média.
     *
     * @param array $planejadas     Quantidades planejadas [produto][dia] => quantidade
     * @param array $entradasEstoque Entradas reais de estoque [produto][dia] => quantidade
     * @param array $datas           Lista de datas (Y-m-d) a considerar
     * @return array<string,float>   Média percentual por dia
     */
    public function calcularMediasDiarias(array $planejadas, array $entradasEstoque, array $datas, bool $includeSaturday = true, bool $includeSunday = true, array $diasSemProducao = []): array
    {
        $planejadoTotal = [];
        $entradaTotal = [];

        foreach ($planejadas as $produtoId => $dias) {
            foreach ($dias as $dia => $planejado) {
                if (!in_array($dia, $datas, true)) {
                    continue;
                }
                if (isset($diasSemProducao[$dia])) {
                    continue;
                }
                $planejadoVal = (float)$planejado;
                if ($planejadoVal <= 0) {
                    continue;
                }
                $entradaEstoque = (float)($entradasEstoque[$produtoId][$dia] ?? 0);
                $entradaEfetiva = $entradaEstoque;
                $planejadoTotal[$dia] = ($planejadoTotal[$dia] ?? 0) + $planejadoVal;
                $entradaTotal[$dia] = ($entradaTotal[$dia] ?? 0) + $entradaEfetiva;
            }
        }

        $medias = [];
        foreach ($datas as $dia) {
            if (isset($diasSemProducao[$dia])) {
                $medias[$dia] = 0.0;
                continue;
            }
            $planejadoDia = (float)($planejadoTotal[$dia] ?? 0);
            $medias[$dia] = $planejadoDia > 0
                ? round(((float)($entradaTotal[$dia] ?? 0) / $planejadoDia) * 100, 1)
                : 0.0;
        }

        return $medias;
    }

    public function registrarEntradaManual(array $input, int $usuarioId): int
    {
        $tipo = strtolower(trim((string)($input['tipo'] ?? 'planejado')));
        $tipo = in_array($tipo, ['planejado', 'estoque'], true) ? $tipo : 'planejado';

        $data = [
            'produto_id' => (int)($input['produto_id'] ?? 0),
            'data_referencia' => $input['data_referencia'] ?? date('Y-m-d'),
            'tipo' => $tipo,
            'quantidade' => (float)($input['quantidade'] ?? 0),
            'observacao' => trim((string)($input['observacao'] ?? '')),
            'usuario_id' => $usuarioId,
        ];

        if ($data['produto_id'] <= 0 || $data['quantidade'] === 0.0) {
            throw new \InvalidArgumentException('Informe o produto e a quantidade para registrar.');
        }

        $consumirReceita = $this->deveConsumirReceita($input, $tipo, $data['produto_id']);
        $conn = $this->entradas->getConnection();

        $conn->beginTransaction();
        try {
            $entradaId = $this->entradas->gravarEntradaManual($data);

            if ($tipo === 'estoque') {
                $this->estoqueService->registrarMovimento([
                    'produto_id' => $data['produto_id'],
                    'tipo' => 'entrada',
                    'status' => 'efetivado',
                    'quantidade' => $data['quantidade'],
                    'lote' => 'MAPA-'.$data['data_referencia'],
                    'validade' => null,
                    'origem' => 'Mapa',
                    'destino' => 'Mapa',
                    'ref_tipo' => 'mapa_estoque',
                    'ref_id' => $entradaId,
                    'usuario_id' => $usuarioId,
                    'custo_unit' => 0,
                ], $usuarioId);

                if ($consumirReceita) {
                    $runId = $this->recipes->runRecipeFromMapaEntrada($entradaId, $data['produto_id'], (float)$data['quantidade'], $usuarioId);
                    if (!$runId) {
                        throw new \InvalidArgumentException('Entrada bloqueada: este produto P.A. nao tem receita ativa para baixar MP automaticamente. Cadastre ou ative a receita, ou desative a regra "Consumir MP automaticamente no mapa".');
                    }
                }
            }

            $conn->commit();
            return $entradaId;
        } catch (\Throwable $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
        }
    }

    private function deveConsumirReceita(array $input, string $tipo, int $produtoId): bool
    {
        if ($tipo !== 'estoque') {
            return false;
        }

        if ($this->config->get('MAPA_CONSUME_RECIPE_MP', '1') !== '1') {
            return false;
        }

        return $this->produtoEhPA($produtoId);
    }

    private function produtoEhPA(int $produtoId): bool
    {
        if ($produtoId <= 0) {
            return false;
        }

        $produto = $this->produtos->find($produtoId);
        if (!$produto) {
            return false;
        }

        $tipo = strtoupper(trim((string)($produto['tipo'] ?? '')));
        return $tipo === 'PA';
    }

    /**
     * Retorna a média diária de conclusão de produção para um período arbitrário.
     */
    public function mediasConclusaoPorPeriodo(\DateTimeImmutable $inicio, \DateTimeImmutable $fim): array
    {
        $datas = [];
        $cursor = $inicio;
        while ($cursor <= $fim) {
            $datas[] = $cursor->format('Y-m-d');
            $cursor = $cursor->add(new DateInterval('P1D'));
        }

        $weekendLegacy = $this->config->get('PERFORMANCE_INCLUDE_WEEKENDS', '1') === '1';
        $includeSaturday = $this->config->get('PERFORMANCE_INCLUDE_SATURDAY', $weekendLegacy ? '1' : '0') === '1';
        $includeSunday = $this->config->get('PERFORMANCE_INCLUDE_SUNDAY', $weekendLegacy ? '1' : '0') === '1';

        $entradas = $this->entradas->porJanela($inicio->format('Y-m-d'), $fim->format('Y-m-d'));
        $planejadas = $entradas['planejado'] ?? [];
        $entradasEstoque = $entradas['estoque'] ?? [];

        $diasSemProducao = $this->diasSemProducao($inicio->format('Y-m-d'), $fim->format('Y-m-d'));
        return $this->calcularMediasDiarias($planejadas, $entradasEstoque, $datas, $includeSaturday, $includeSunday, $diasSemProducao);
    }

    public function diasSemProducao(string $inicio, string $fim): array
    {
        $pdo = $this->entradas->getConnection();
        $pdo->exec("CREATE TABLE IF NOT EXISTS mapa_dias_sem_producao (data_referencia DATE PRIMARY KEY, motivo VARCHAR(80) NOT NULL DEFAULT 'outro', usuario_id INT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)");
        $stmt = $pdo->prepare('SELECT data_referencia, motivo, usuario_id FROM mapa_dias_sem_producao WHERE data_referencia BETWEEN :inicio AND :fim');
        $stmt->execute(['inicio' => $inicio, 'fim' => $fim]);
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(string)$row['data_referencia']] = $row;
        }
        return $result;
    }

    public function definirDiaSemProducao(string $dia, bool $ativo, string $motivo, int $usuarioId): void
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dia)) {
            throw new \InvalidArgumentException('Data inválida.');
        }
        $pdo = $this->entradas->getConnection();
        $this->diasSemProducao($dia, $dia);
        if (!$ativo) {
            $stmt = $pdo->prepare('DELETE FROM mapa_dias_sem_producao WHERE data_referencia = :dia');
            $stmt->execute(['dia' => $dia]);
            return;
        }
        $stmt = $pdo->prepare("INSERT INTO mapa_dias_sem_producao (data_referencia, motivo, usuario_id) VALUES (:dia, :motivo, :usuario_id) ON DUPLICATE KEY UPDATE motivo = VALUES(motivo), usuario_id = VALUES(usuario_id), updated_at = CURRENT_TIMESTAMP");
        $stmt->execute(['dia' => $dia, 'motivo' => $motivo ?: 'outro', 'usuario_id' => $usuarioId ?: null]);
    }

    private function diaConsiderado(string $dia, bool $includeSaturday, bool $includeSunday): bool
    {
        $dow = (int)date('N', strtotime($dia));
        if ($dow === 6) {
            return $includeSaturday;
        }
        if ($dow === 7) {
            return $includeSunday;
        }
        return true;
    }

    private function buscarSaidas(string $inicio, string $fim): array
    {
        $pdo = $this->pedidos->getConnection();
        $sql = "SELECT pi.produto_id, DATE(p.created_at) as dia, SUM(pi.quantidade) as total
                FROM pedido_itens pi
                JOIN pedidos p ON p.id = pi.pedido_id
                WHERE DATE(p.created_at) BETWEEN :inicio AND :fim
                  AND p.status <> 'cancelado'
                GROUP BY pi.produto_id, DATE(p.created_at)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['inicio' => $inicio, 'fim' => $fim]);
        $mapa = [];
        foreach ($stmt->fetchAll() as $row) {
            $prodId = (int)$row['produto_id'];
            $dia = $row['dia'];
            $mapa[$prodId][$dia] = (float)$row['total'];
        }
        return $mapa;
    }

    private function estoqueResumido(int $produtoId): array
    {
        $pdo = $this->estoques->getConnection();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantidade),0) as estoque, COALESCE(SUM(reservado),0) as reservado FROM estoques WHERE produto_id = :produto");
        $stmt->execute(['produto' => $produtoId]);
        $row = $stmt->fetch();
        return [
            'estoque' => (float)($row['estoque'] ?? 0),
            'reservado' => (float)($row['reservado'] ?? 0),
        ];
    }

    private function estoquesResumidos(array $produtoIds): array
    {
        if (!$produtoIds) {
            return [];
        }
        $produtoIds = array_values(array_unique(array_map('intval', $produtoIds)));
        $placeholders = implode(',', array_fill(0, count($produtoIds), '?'));
        $stmt = $this->estoques->getConnection()->prepare(
            "SELECT produto_id, COALESCE(SUM(quantidade), 0) AS estoque, COALESCE(SUM(reservado), 0) AS reservado "
            ."FROM estoques WHERE produto_id IN ($placeholders) GROUP BY produto_id"
        );
        $stmt->execute($produtoIds);
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(int)$row['produto_id']] = [
                'estoque' => (float)($row['estoque'] ?? 0),
                'reservado' => (float)($row['reservado'] ?? 0),
            ];
        }
        return $result;
    }

    private function limitesPedidos(): array
    {
        $pdo = $this->pedidos->getConnection();
        $stmt = $pdo->query("SELECT MIN(DATE(created_at)) as min_data, MAX(DATE(created_at)) as max_data FROM pedidos WHERE status <> 'cancelado'");
        $row = $stmt->fetch() ?: [];

        return [$row['min_data'] ?? null, $row['max_data'] ?? null];
    }
}
