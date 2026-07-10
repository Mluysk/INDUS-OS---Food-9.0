<?php
namespace App\Modules\Desempenho\Controllers;

use App\Core\Controller;
use App\Repositories\MapaEntradasRepository;
use App\Repositories\ProdutosRepository;
use App\Services\MapaService;
use DateInterval;
use DateTimeImmutable;

class DesempenhoController extends Controller
{
    private MapaService $mapa;
    private MapaEntradasRepository $entradas;
    private ProdutosRepository $produtos;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        MapaService $mapa,
        MapaEntradasRepository $entradas,
        ProdutosRepository $produtos
    ) {
        parent::__construct($container, $auth, $audit);
        $this->mapa = $mapa;
        $this->entradas = $entradas;
        $this->produtos = $produtos;
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin', 'gestor', 'producao', 'expedicao']);

        $input = $this->input();
        $mes = (int)($input['month'] ?? date('n'));
        $ano = (int)($input['year'] ?? date('Y'));
        if ($mes < 1 || $mes > 12) {
            $mes = (int)date('n');
        }
        if ($ano < 2000 || $ano > 2100) {
            $ano = (int)date('Y');
        }

        $hoje = new DateTimeImmutable('today');
        $semanaInicio = $hoje->modify('monday this week');
        $semanaFim = $hoje->modify('sunday this week');
        $mesInicio = (new DateTimeImmutable(sprintf('%04d-%02d-01', $ano, $mes)))->modify('first day of this month');
        $mesFim = $mesInicio->modify('last day of this month');
        $anoInicio = $hoje->setDate((int)$hoje->format('Y'), 1, 1);
        $anoFim = $hoje->setDate((int)$hoje->format('Y'), 12, 31);

        [$calendar, $calendarMeta] = $this->buildProductionCalendar($mes, $ano);
        $mediaSemana = $this->mediaPeriodo($semanaInicio, $semanaFim);
        $mediaMes = $this->mediaPeriodo($mesInicio, $mesFim);
        $mediaAno = $this->mediaPeriodo($anoInicio, $anoFim);
        $produtosMes = $this->resumoMensalPorProduto($mesInicio, $mesFim);

        if ($this->wantsJson()) {
            $this->json([
                'medias' => [
                    'semanal' => $mediaSemana,
                    'mensal' => $mediaMes,
                    'anual' => $mediaAno,
                ],
                'calendar' => $calendar,
                'calendar_meta' => $calendarMeta,
                'produtos' => $produtosMes,
            ]);
            return;
        }

        $this->render('desempenho/index', [
            'mediaSemana' => $mediaSemana,
            'mediaMes' => $mediaMes,
            'mediaAno' => $mediaAno,
            'calendar' => $calendar,
            'calendarMeta' => $calendarMeta,
            'produtosMes' => $produtosMes,
            'mes' => $mes,
            'ano' => $ano,
        ], [
            'title' => 'Desempenho',
            'pageSubtitle' => 'Acompanhe indicadores diários, semanais e mensais da operação.',
            'breadcrumbs' => ['Planejamento', 'Desempenho'],
            'activeMenu' => 'desempenho',
        ]);
    }

    private function mediaPeriodo(DateTimeImmutable $inicio, DateTimeImmutable $fim): float
    {
        $medias = $this->mapa->mediasConclusaoPorPeriodo($inicio, $fim);
        $valores = array_filter(array_map('floatval', $medias), static fn($v) => $v > 0);
        if (empty($valores)) {
            return 0.0;
        }
        return round(array_sum($valores) / count($valores), 1);
    }

    private function buildProductionCalendar(int $mes, int $ano): array
    {
        $start = (new DateTimeImmutable(sprintf('%04d-%02d-01', $ano, $mes)))->modify('first day of this month');
        $end = $start->modify('last day of this month');
        $todayKey = (new DateTimeImmutable('today'))->format('Y-m-d');

        $plannedByDay = [];
        $estoqueByDay = [];
        $entradas = $this->entradas->porJanela($start->format('Y-m-d'), $end->format('Y-m-d'));
        $diasSemProducao = $this->mapa->diasSemProducao($start->format('Y-m-d'), $end->format('Y-m-d'));
        foreach ($entradas['planejado'] ?? [] as $dias) {
            foreach ($dias as $dia => $qtd) {
                $plannedByDay[$dia] = ($plannedByDay[$dia] ?? 0) + (float)$qtd;
            }
        }
        foreach ($entradas['estoque'] ?? [] as $dias) {
            foreach ($dias as $dia => $qtd) {
                $estoqueByDay[$dia] = ($estoqueByDay[$dia] ?? 0) + (float)$qtd;
            }
        }

        $weekday = ['DOM', 'SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SAB'];
        $monthNames = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
        $window = [];
        $completionValues = [];

        for ($date = $start; $date <= $end; $date = $date->add(new DateInterval('P1D'))) {
            $key = $date->format('Y-m-d');
            $planejado = (float)($plannedByDay[$key] ?? 0);
            $entrada = (float)($estoqueByDay[$key] ?? 0);
            $completion = $planejado > 0 ? round(min(100, ($entrada / $planejado) * 100), 1) : 0.0;
            $semProducao = isset($diasSemProducao[$key]);
            if ($semProducao) {
                $completion = 0.0;
            }

            $window[] = [
                'date' => $key,
                'day' => $date->format('d'),
                'label' => $weekday[(int)$date->format('w')],
                'planned' => $planejado,
                'entrada' => $entrada,
                'completion' => $completion,
                'sem_producao' => $semProducao,
                'sem_producao_motivo' => $diasSemProducao[$key]['motivo'] ?? null,
                'today' => $key === $todayKey,
                'tooltip' => $semProducao ? 'Sem produção · ' . (string)($diasSemProducao[$key]['motivo'] ?? 'Motivo não informado') : sprintf(
                    'Planejado %s · Entrada %s · %s%%',
                    number_format($planejado, 0, ',', '.'),
                    number_format($entrada, 0, ',', '.'),
                    number_format($completion, 1, ',', '.')
                ),
            ];

            if ($planejado > 0 && !$semProducao) {
                $completionValues[] = $completion;
            }
        }

        $monthCompletion = !empty($completionValues) ? round(array_sum($completionValues) / count($completionValues), 1) : 0.0;
        $meta = [
            'label' => ($monthNames[$mes - 1] ?? $start->format('m')).' de '.$ano,
            'month' => $mes,
            'year' => $ano,
            'completion' => $monthCompletion,
        ];

        return [$window, $meta];
    }

    private function resumoMensalPorProduto(DateTimeImmutable $inicio, DateTimeImmutable $fim): array
    {
        $entradas = $this->entradas->porJanela($inicio->format('Y-m-d'), $fim->format('Y-m-d'));
        $planejado = $entradas['planejado'] ?? [];
        $estoque = $entradas['estoque'] ?? [];
        $produtos = $this->produtos->all(['tipo' => 'PA']);
        $linhas = [];

        foreach ($produtos as $produto) {
            $id = (int)($produto['id'] ?? 0);
            $planejadoTotal = array_sum($planejado[$id] ?? []);
            $entradaTotal = array_sum($estoque[$id] ?? []);
            $percent = $planejadoTotal > 0 ? round(($entradaTotal / $planejadoTotal) * 100, 1) : 0.0;

            $linhas[] = [
                'sku' => $produto['sku'] ?? '—',
                'nome' => $produto['nome'] ?? '',
                'planejado' => $planejadoTotal,
                'entrada' => $entradaTotal,
                'percentual' => $percent,
            ];
        }

        usort($linhas, static fn($a, $b) => strnatcasecmp((string)($a['sku'] ?? ''), (string)($b['sku'] ?? '')));

        return $linhas;
    }
}
