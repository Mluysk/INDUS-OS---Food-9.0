<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\PedidosService;
use App\Services\ProdutosService;
use App\Services\ClientesService;
use App\Services\EstoqueService;
use App\Services\ConfigService;
use App\Services\MapaService;
use DateTimeImmutable;

class DashboardController extends Controller
{
    private PedidosService $pedidos;
    private ProdutosService $produtos;
    private ClientesService $clientes;
    private EstoqueService $estoque;
    private ConfigService $configService;
    private MapaService $mapaService;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        PedidosService $pedidos,
        ProdutosService $produtos,
        ClientesService $clientes,
        EstoqueService $estoque,
        ConfigService $configService,
        MapaService $mapaService
    ) {
        parent::__construct($container, $auth, $audit);
        $this->pedidos = $pedidos;
        $this->produtos = $produtos;
        $this->clientes = $clientes;
        $this->estoque = $estoque;
        $this->configService = $configService;
        $this->mapaService = $mapaService;
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin','gestor','producao','expedicao','financeiro','qualidade']);

        $mes = (int)($this->input()['month'] ?? date('n'));
        $ano = (int)($this->input()['year'] ?? date('Y'));
        if ($mes < 1 || $mes > 12) {
            $mes = (int)date('n');
        }
        if ($ano < 2000 || $ano > 2100) {
            $ano = (int)date('Y');
        }

        $config = $this->container->getConfig();
        $stats = [
            'pedidos' => 0,
            'produtos' => 0,
            'clientes' => 0,
        ];

        $calendar = [];
        $calendarMeta = [
            'label' => '',
            'month' => $mes,
            'year' => $ano,
            'completion' => 0.0,
        ];
        $weekendLegacy = $this->configService->get('PERFORMANCE_INCLUDE_WEEKENDS', '1') === '1';
        $includeSaturday = $this->configService->get('PERFORMANCE_INCLUDE_SATURDAY', $weekendLegacy ? '1' : '0') === '1';
        $includeSunday = $this->configService->get('PERFORMANCE_INCLUDE_SUNDAY', $weekendLegacy ? '1' : '0') === '1';
        $pedidos = [];
        $alertasValidade = ['count' => 0, 'produtos' => []];
        $estoqueStatus = [
            'negativos' => 0,
            'baixos' => 0,
            'zerados' => 0,
            'produtos' => ['negativos' => [], 'baixos' => [], 'zerados' => []],
        ];
        $backup = ['full' => false, 'modules' => []];
        $agendaStatus = ['novo' => 0, 'aprovacao' => 0, 'producao' => 0, 'separacao' => 0, 'expedicao' => 0, 'faturado' => 0, 'cancelado' => 0];
        $performance = 0.0;

        try {
            $pedidos = $this->pedidos->listar();
            $stats['pedidos'] = count($pedidos);
            $stats['produtos'] = count($this->produtos->all());
            $stats['clientes'] = count($this->clientes->all());
            $alertasValidade = $this->carregarAlertasValidade();
            [$estoqueStatus, $faixa] = $this->carregarStatusEstoque();
            $backup = $this->carregarBackupConfig();
            $agendaStatus = $this->statusPedidos($pedidos);
            $performance = $this->calcularPerformance($agendaStatus);
        } catch (\Throwable $exception) {
            if (!empty($config['debug'])) {
                throw $exception;
            }
        }

        [$calendar, $calendarMeta] = $this->buildSalesCalendar($mes, $ano, $includeSaturday, $includeSunday);

        if ($this->wantsJson()) {
            $this->json([
                'dashboard' => $stats,
                'calendar' => $calendar,
                'calendar_meta' => $calendarMeta,
                'alertas' => [
                    'validade' => $alertasValidade,
                    'estoque' => $estoqueStatus,
                ],
                'backup' => $backup,
                'agenda' => $agendaStatus,
                'performance' => $performance,
            ]);
            return;
        }

        $this->render('dashboard/index', [
            'stats' => $stats,
            'calendar' => $calendar,
            'calendarMeta' => $calendarMeta,
            'alertasValidade' => $alertasValidade,
            'estoqueStatus' => $estoqueStatus,
            'backup' => $backup,
            'agendaStatus' => $agendaStatus,
            'performance' => $performance,
        ], [
            'title' => 'Dashboard',
            'subtitle' => 'Resumo dos indicadores operacionais do SCI.',
            'breadcrumbs' => ['Início', 'Dashboard'],
            'pageActions' => [
                [
                    'label' => 'Novo pedido',
                    'href' => 'pedidos',
                    'icon' => 'plus',
                    'variant' => 'primary',
                ],
            ],
            'activeMenu' => 'dashboard',
        ]);
    }

    private function buildSalesCalendar(int $mes, int $ano, bool $includeSaturday = true, bool $includeSunday = true): array
    {
        $today = new DateTimeImmutable('today');
        $start = (new DateTimeImmutable(sprintf('%04d-%02d-01', $ano, $mes)))->modify('first day of this month');
        $end = $start->modify('last day of this month');
        $weekday = ['DOM', 'SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SAB'];
        $monthNames = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

        $window = [];
        for ($date = $start; $date <= $end; $date = $date->modify('+1 day')) {
            $key = $date->format('Y-m-d');
            $window[$key] = [
                'date' => $key,
                'day' => $date->format('d'),
                'label' => $weekday[(int)$date->format('w')],
                'value' => 0.0,
                'count' => 0,
                'tooltip' => '',
                'today' => $key === $today->format('Y-m-d'),
                'ordered_qty' => 0.0,
                'entrada' => 0.0,
                'producao' => 0.0,
                'completion' => 0.0,
            ];
        }
        $orders = $this->resumoPedidosPorDia($start, $end);
        $movimentos = $this->resumoEntradasPorDia($start, $end);
        $mediasMapa = $this->mapaService->mediasConclusaoPorPeriodo($start, $end);
        $diasSemProducao = $this->mapaService->diasSemProducao($start->format('Y-m-d'), $end->format('Y-m-d'));

        foreach ($window as $key => &$entry) {
            if (isset($orders[$key])) {
                $entry['count'] = (int)($orders[$key]['pedidos'] ?? 0);
                $entry['value'] = (float)($orders[$key]['valor'] ?? 0);
                $entry['ordered_qty'] = (float)($orders[$key]['itens'] ?? 0);
            }
            if (isset($movimentos[$key])) {
                $entry['entrada'] = (float)($movimentos[$key]['entrada'] ?? 0);
                $entry['producao'] = (float)($movimentos[$key]['producao'] ?? 0);
            }

            $orderedQty = $entry['ordered_qty'];
            $completionCalc = $orderedQty > 0 ? min(100, ($entry['producao'] / max($orderedQty, 1)) * 100) : 0;
            $completionFromMapa = $mediasMapa[$key] ?? null;
            $entry['completion'] = $completionFromMapa !== null ? (float)$completionFromMapa : round($completionCalc, 1);
            $entry['sem_producao'] = isset($diasSemProducao[$key]);
            $entry['sem_producao_motivo'] = $diasSemProducao[$key]['motivo'] ?? null;

            if ($entry['sem_producao']) {
                $entry['completion'] = 0.0;
            }

            $entry['tooltip'] = $entry['sem_producao']
                ? 'Sem produção · ' . (string)($entry['sem_producao_motivo'] ?? 'Motivo não informado')
                : sprintf(
                '%s pedidos · R$ %s · %s%% conclusão produção',
                number_format($entry['count'], 0, ',', '.'),
                number_format($entry['value'], 2, ',', '.'),
                number_format($entry['completion'], 1, ',', '.')
                );
            $entry['value_formatted'] = 'R$ ' . number_format($entry['value'], 2, ',', '.');
        }

        $completionValues = [];
        foreach ($window as $key => $item) {
            if (!$this->diaConsiderado($key, $includeSaturday, $includeSunday)) {
                continue;
            }
            if (!empty($item['sem_producao'])) {
                continue;
            }
            $completionValues[] = (float)($item['completion'] ?? 0);
        }
        $monthCompletion = count($completionValues) > 0 ? round(array_sum($completionValues) / count($completionValues), 1) : 0.0;
        $label = ($monthNames[$mes - 1] ?? $start->format('m')) . ' de ' . $ano;

        return [array_values($window), [
            'label' => $label,
            'month' => $mes,
            'year' => $ano,
            'completion' => $monthCompletion,
        ]];
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

    private function resumoPedidosPorDia(DateTimeImmutable $inicio, DateTimeImmutable $fim): array
    {
        try {
            $db = $this->container->get(\PDO::class);
            $sql = 'SELECT DATE(p.created_at) AS dia, COUNT(*) AS pedidos, COALESCE(SUM(p.total),0) AS valor, COALESCE(SUM(pi.quantidade),0) AS itens
                    FROM pedidos p
                    LEFT JOIN pedido_itens pi ON pi.pedido_id = p.id
                    WHERE p.created_at IS NOT NULL AND p.created_at BETWEEN :inicio AND :fim
                    GROUP BY DATE(p.created_at)';
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'inicio' => $inicio->format('Y-m-d 00:00:00'),
                'fim' => $fim->format('Y-m-d 23:59:59'),
            ]);
            $rows = $stmt->fetchAll();
            $map = [];
            foreach ($rows as $row) {
                $map[$row['dia']] = $row;
            }
            return $map;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function resumoEntradasPorDia(DateTimeImmutable $inicio, DateTimeImmutable $fim): array
    {
        try {
            $db = $this->container->get(\PDO::class);
            $sql = 'SELECT DATE(data_referencia) AS dia,
                           SUM(CASE WHEN LOWER(tipo) = "entrada" THEN quantidade ELSE 0 END) AS entrada,
                           SUM(CASE WHEN LOWER(tipo) = "producao" THEN quantidade ELSE 0 END) AS producao
                    FROM mapa_entradas
                    WHERE data_referencia BETWEEN :inicio AND :fim
                    GROUP BY DATE(data_referencia)';
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'inicio' => $inicio->format('Y-m-d'),
                'fim' => $fim->format('Y-m-d'),
            ]);
            $rows = $stmt->fetchAll();
            $map = [];
            foreach ($rows as $row) {
                $map[$row['dia']] = $row;
            }
            return $map;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function carregarAlertasValidade(): array
    {
        $dias = (int)($this->container->getConfig()['validade_alerta_dias'] ?? 15);
        $registros = $this->estoque->alertasValidade($dias);
        if (!is_array($registros)) {
            return ['count' => 0, 'produtos' => []];
        }

        $nomes = [];
        foreach ($registros as $registro) {
            $nome = trim((string)($registro['nome'] ?? ''));
            $sku = trim((string)($registro['sku'] ?? ''));
            if ($nome === '' && $sku === '') {
                continue;
            }
            $label = $nome;
            if ($sku !== '') {
                $label .= " ({$sku})";
            }
            $nomes[] = $label;
        }

        return [
            'count' => count($registros),
            'produtos' => array_values(array_unique($nomes)),
        ];
    }

    private function carregarStatusEstoque(): array
    {
        $lowMin = (int)($this->configService->get('ESTOQUE_BAIXO_MIN', '4') ?: 4);
        $lowMax = (int)($this->configService->get('ESTOQUE_BAIXO_MAX', '8') ?: 8);
        if ($lowMin < 0) {
            $lowMin = 0;
        }
        if ($lowMax < $lowMin) {
            $lowMax = $lowMin;
        }

        $sql = 'SELECT
                    p.id,
                    p.nome,
                    p.sku,
                    COALESCE(SUM(CASE
                        WHEN m.status = "efetivado" AND m.tipo = "entrada" THEN m.quantidade
                        WHEN m.status = "efetivado" AND m.tipo = "saida" THEN -m.quantidade
                        ELSE 0 END), 0) AS saldo_fisico,
                    COALESCE(SUM(CASE WHEN m.status = "reservado" THEN m.quantidade ELSE 0 END), 0) AS reservado
                FROM produtos p
                LEFT JOIN movimentos_estoque m ON m.produto_id = p.id
                GROUP BY p.id, p.nome, p.sku';

        $stmt = $this->estoque->getConnection()->query($sql);
        $linhas = $stmt->fetchAll();

        $status = [
            'negativos' => 0,
            'baixos' => 0,
            'zerados' => 0,
            'produtos' => ['negativos' => [], 'baixos' => [], 'zerados' => []],
        ];

        foreach ($linhas as $linha) {
            $saldo = (float)($linha['saldo_fisico'] ?? 0) - (float)($linha['reservado'] ?? 0);
            $class = $this->classificarSaldo($saldo, ['min' => $lowMin, 'max' => $lowMax]);
            $label = trim(((string)($linha['sku'] ?? '')) . ' · ' . ((string)($linha['nome'] ?? '')));
            if ($label === '·') {
                $label = 'Produto sem identificação';
            }

            if ($class === 'NEG') {
                $status['negativos']++;
                $status['produtos']['negativos'][] = $label;
            } elseif ($class === 'ZERO') {
                $status['zerados']++;
                $status['produtos']['zerados'][] = $label;
            } elseif ($class === 'LOW') {
                $status['baixos']++;
                $status['produtos']['baixos'][] = $label;
            }
        }

        $status['produtos']['negativos'] = array_values(array_unique($status['produtos']['negativos']));
        $status['produtos']['zerados'] = array_values(array_unique($status['produtos']['zerados']));
        $status['produtos']['baixos'] = array_values(array_unique($status['produtos']['baixos']));

        return [$status, ['min' => $lowMin, 'max' => $lowMax]];
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

    private function carregarBackupConfig(): array
    {
        $full = $this->configService->get('BACKUP_FULL_ENABLED', '1') === '1';
        $modulesRaw = $this->configService->get('BACKUP_MODULES', 'clientes, produtos, pedidos, estoque, financeiro, producao');
        $modules = array_filter(array_map('trim', explode(',', (string)$modulesRaw)));
        if (empty($modules)) {
            $modules = ['clientes', 'produtos', 'pedidos', 'estoque', 'financeiro', 'producao'];
        }
        return [
            'full' => $full,
            'modules' => $modules,
        ];
    }

    private function statusPedidos(array $pedidos): array
    {
        $status = ['novo' => 0, 'aprovacao' => 0, 'producao' => 0, 'separacao' => 0, 'expedicao' => 0, 'faturado' => 0, 'cancelado' => 0];
        foreach ($pedidos as $pedido) {
            $key = $pedido['status'] ?? 'novo';
            if (array_key_exists($key, $status)) {
                $status[$key]++;
            }
        }
        return $status;
    }

    private function calcularPerformance(array $status): float
    {
        $total = array_sum($status);
        if ($total === 0) {
            return 0.0;
        }
        $concluidos = ($status['faturado'] ?? 0) + ($status['expedicao'] ?? 0);
        return round(($concluidos / $total) * 100, 1);
    }
}
