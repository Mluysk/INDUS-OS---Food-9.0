<?php
/** @var array $theme */
/** @var float $mediaSemana */
/** @var float $mediaMes */
/** @var float $mediaAno */
/** @var array $calendar */
/** @var array $calendarMeta */
/** @var array $produtosMes */
/** @var int $mes */
/** @var int $ano */
?>

<div class="page-header">
    <div>
        <p class="eyebrow">Planejamento</p>
        <h1 class="page-title">Desempenho</h1>
        <p class="text-muted">Painel de produção com médias semanais, mensais e anuais, além de calendário e acompanhamento por produto.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($url('metas'), ENT_QUOTES, 'UTF-8'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="lucide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            Abrir metas
        </a>
    </div>
</div>

<ul class="nav nav-tabs soft-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-producao" type="button" role="tab" aria-selected="true">Produção</button>
    </li>
</ul>

<div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="tab-producao" role="tabpanel">
        <div class="row g-3">
            <div class="col-12 col-lg-4">
                <div class="card kpi-card gradient-card">
                    <div class="card-body">
                        <div class="kpi-label">Média semanal</div>
                        <div class="kpi-value js-media-semana"><?= number_format($mediaSemana, 1, ',', '.'); ?>%</div>
                        <p class="kpi-helper">Percentual médio de conclusão da semana corrente.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card kpi-card gradient-card alt">
                    <div class="card-body">
                        <div class="kpi-label">Média mensal</div>
                        <div class="kpi-value js-media-mes"><?= number_format($mediaMes, 1, ',', '.'); ?>%</div>
                        <p class="kpi-helper">Conclusão de produção do mês em andamento.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card kpi-card gradient-card soft">
                    <div class="card-body">
                        <div class="kpi-label">Média anual</div>
                        <div class="kpi-value js-media-ano"><?= number_format($mediaAno, 1, ',', '.'); ?>%</div>
                        <p class="kpi-helper">Visão acumulada de produção no ano.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-lg-7">
                <div class="card calendar-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <div>
                                <p class="mb-0 text-muted small">Calendário de produção</p>
                                <h5 class="mb-0 d-flex align-items-center gap-2">
                                    <span class="js-calendar-label"><?= htmlspecialchars($calendarMeta['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="badge bg-soft-primary text-primary js-calendar-completion">Conclusão: <?= number_format((float)($calendarMeta['completion'] ?? 0), 1, ',', '.'); ?>%</span>
                                </h5>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <?php
                                $prev = (new DateTimeImmutable(sprintf('%04d-%02d-01', $ano, $mes)))->modify('-1 month');
                                $next = (new DateTimeImmutable(sprintf('%04d-%02d-01', $ano, $mes)))->modify('+1 month');
                                ?>
                                <button class="btn btn-light btn-sm js-month-change" type="button" data-dir="prev" data-month="<?= $prev->format('n'); ?>" data-year="<?= $prev->format('Y'); ?>" title="Mês anterior">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="lucide" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                                </button>
                                <button class="btn btn-light btn-sm js-month-change" type="button" data-dir="next" data-month="<?= $next->format('n'); ?>" data-year="<?= $next->format('Y'); ?>" title="Próximo mês">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="lucide" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="calendar-grid js-calendar-grid">
                            <?php foreach ($calendar as $day): ?>
                                <div class="calendar-day <?= $day['today'] ? 'is-today' : ''; ?>" title="<?= htmlspecialchars($day['tooltip'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="day-label"><?= htmlspecialchars($day['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span class="day-number"><?= htmlspecialchars($day['day'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted small">
                                        <span>Plan.</span>
                                        <span><?= number_format((float)$day['planned'], 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted small">
                                        <span>Entrada</span>
                                        <span><?= number_format((float)$day['entrada'], 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="progress mt-2" style="height:8px;">
                                        <div class="progress-bar" role="progressbar" style="width: <?= min(100, (float)$day['completion']); ?>%;" aria-valuenow="<?= (float)$day['completion']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="text-end small fw-semibold mt-1 text-primary"><?= number_format((float)$day['completion'], 1, ',', '.'); ?>%</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-start justify-content-between mb-3 gap-3 flex-wrap">
                            <div class="d-flex align-items-start gap-3 flex-wrap">
                                <div class="icon-pill bg-soft-primary text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="lucide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </div>
                                <div>
                                    <p class="mb-1 text-muted small">Resumo mensal</p>
                                    <h5 class="mb-0">Produção x entrada</h5>
                                    <p class="mb-0 text-muted small">Atual: <span class="fw-semibold js-resumo-label"><?= htmlspecialchars($calendarMeta['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></p>
                                </div>
                            </div>
                            <?php
                            $prevResumo = (new DateTimeImmutable(sprintf('%04d-%02d-01', $ano, $mes)))->modify('-1 month');
                            $nextResumo = (new DateTimeImmutable(sprintf('%04d-%02d-01', $ano, $mes)))->modify('+1 month');
                            ?>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Navegar meses">
                                <button class="btn btn-light js-month-change" type="button" data-dir="prev" data-month="<?= $prevResumo->format('n'); ?>" data-year="<?= $prevResumo->format('Y'); ?>" title="Mês anterior">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="lucide" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                                </button>
                                <span class="btn btn-outline-light text-muted px-3 fw-semibold">Atual: <span class="js-resumo-label-inline"><?= htmlspecialchars($calendarMeta['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></span>
                                <button class="btn btn-light js-month-change" type="button" data-dir="next" data-month="<?= $nextResumo->format('n'); ?>" data-year="<?= $nextResumo->format('Y'); ?>" title="Próximo mês">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="lucide" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive slim-scroll">
                            <table class="table align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Produto</th>
                                    <th class="text-end">Produção</th>
                                    <th class="text-end">Entrada</th>
                                    <th class="text-end">% concl.</th>
                                </tr>
                                </thead>
                                <tbody class="js-resumo-body">
                                <?php foreach ($produtosMes as $produto): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($produto['sku'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-end text-muted"><?= number_format((float)$produto['planejado'], 0, ',', '.'); ?></td>
                                        <td class="text-end"><?= number_format((float)$produto['entrada'], 0, ',', '.'); ?></td>
                                        <td class="text-end fw-semibold text-primary"><?= number_format((float)$produto['percentual'], 1, ',', '.'); ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($produtosMes)): ?>
                                    <tr><td colspan="5" class="text-center text-muted small">Nenhum produto acabado lançado neste mês.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$initialPayload = [
    'medias' => [
        'semanal' => $mediaSemana,
        'mensal' => $mediaMes,
        'anual' => $mediaAno,
    ],
    'calendar' => $calendar,
    'calendar_meta' => $calendarMeta,
    'produtos' => $produtosMes,
    'month' => $mes,
    'year' => $ano,
];
?>

<script>
(() => {
    const state = <?= json_encode($initialPayload, JSON_UNESCAPED_UNICODE); ?>;
    const endpoint = '<?= htmlspecialchars($url('desempenho'), ENT_QUOTES, 'UTF-8'); ?>';

    const elMediaSemana = document.querySelector('.js-media-semana');
    const elMediaMes = document.querySelector('.js-media-mes');
    const elMediaAno = document.querySelector('.js-media-ano');
    const calendarLabel = document.querySelector('.js-calendar-label');
    const calendarCompletion = document.querySelector('.js-calendar-completion');
    const calendarGrid = document.querySelector('.js-calendar-grid');
    const resumoLabel = document.querySelector('.js-resumo-label');
    const resumoLabelInline = document.querySelector('.js-resumo-label-inline');
    const resumoBody = document.querySelector('.js-resumo-body');
    const monthButtons = () => Array.from(document.querySelectorAll('.js-month-change'));

    const escapeHtml = (value) => {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    const formatNumber = (value, decimals = 0) => {
        const number = Number.parseFloat(value || 0);
        return number.toLocaleString('pt-BR', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
    };

    const updateKpis = (payload) => {
        if (!payload?.medias) return;
        elMediaSemana && (elMediaSemana.textContent = `${formatNumber(payload.medias.semanal ?? 0, 1)}%`);
        elMediaMes && (elMediaMes.textContent = `${formatNumber(payload.medias.mensal ?? 0, 1)}%`);
        elMediaAno && (elMediaAno.textContent = `${formatNumber(payload.medias.anual ?? 0, 1)}%`);
    };

    const buildCalendarDay = (day) => {
        const completion = Number(day.completion ?? 0);
        return `<div class="calendar-day ${day.today ? 'is-today' : ''}" title="${escapeHtml(day.tooltip ?? '')}">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="day-label">${escapeHtml(day.label ?? '')}</span>
                <span class="day-number">${escapeHtml(day.day ?? '')}</span>
            </div>
            <div class="d-flex justify-content-between text-muted small">
                <span>Plan.</span>
                <span>${formatNumber(day.planned ?? 0)}</span>
            </div>
            <div class="d-flex justify-content-between text-muted small">
                <span>Entrada</span>
                <span>${formatNumber(day.entrada ?? 0)}</span>
            </div>
            <div class="progress mt-2" style="height:8px;">
                <div class="progress-bar" role="progressbar" style="width: ${Math.min(100, completion)}%;" aria-valuenow="${completion}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="text-end small fw-semibold mt-1 text-primary">${formatNumber(completion, 1)}%</div>
        </div>`;
    };

    const updateCalendar = (payload) => {
        const meta = payload?.calendar_meta ?? {};
        const calendar = payload?.calendar ?? [];
        calendarLabel && (calendarLabel.textContent = meta.label ?? '');
        calendarCompletion && (calendarCompletion.textContent = `Conclusão: ${formatNumber(meta.completion ?? 0, 1)}%`);

        if (calendarGrid) {
            calendarGrid.innerHTML = calendar.map(buildCalendarDay).join('');
        }

        const prev = new Date((meta.year ?? state.year), (meta.month ?? state.month) - 2, 1);
        const next = new Date((meta.year ?? state.year), (meta.month ?? state.month), 1);
        monthButtons().forEach(btn => {
            const dir = btn.dataset.dir === 'prev' ? -1 : 1;
            const target = dir < 0 ? prev : next;
            btn.dataset.month = String(target.getMonth() + 1);
            btn.dataset.year = String(target.getFullYear());
        });
    };

    const updateResumo = (payload) => {
        resumoLabel && (resumoLabel.textContent = payload?.calendar_meta?.label ?? '');
        resumoLabelInline && (resumoLabelInline.textContent = payload?.calendar_meta?.label ?? '');

        if (!resumoBody) return;
        const produtos = payload?.produtos ?? [];
        if (!produtos.length) {
            resumoBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted small">Nenhum produto acabado lançado neste mês.</td></tr>';
            return;
        }

        resumoBody.innerHTML = produtos.map((produto) => {
            return `<tr>
                <td class="fw-semibold">${escapeHtml(produto.sku ?? '—')}</td>
                <td>${escapeHtml(produto.nome ?? '')}</td>
                <td class="text-end text-muted">${formatNumber(produto.planejado ?? 0)}</td>
                <td class="text-end">${formatNumber(produto.entrada ?? 0)}</td>
                <td class="text-end fw-semibold text-primary">${formatNumber(produto.percentual ?? 0, 1)}%</td>
            </tr>`;
        }).join('');
    };

    const render = (payload) => {
        updateKpis(payload);
        updateCalendar(payload);
        updateResumo(payload);
    };

    const fetchMonth = async (month, year) => {
        monthButtons().forEach(btn => btn.setAttribute('disabled', 'disabled'));
        try {
            const url = `${endpoint}?month=${month}&year=${year}`;
            const response = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Falha ao carregar dados');
            const data = await response.json();
            state.month = month;
            state.year = year;
            render(data);
        } catch (error) {
            console.error(error);
        } finally {
            monthButtons().forEach(btn => btn.removeAttribute('disabled'));
        }
    };

    monthButtons().forEach(btn => {
        btn.addEventListener('click', (ev) => {
            ev.preventDefault();
            const month = parseInt(btn.dataset.month || state.month, 10);
            const year = parseInt(btn.dataset.year || state.year, 10);
            fetchMonth(month, year);
        });
    });

    render(state);
})();
</script>

<style>
.kpi-card {
    border: none;
    color: #fff;
    box-shadow: var(--shadow-card);
}
.gradient-card { background: linear-gradient(135deg, #5b7bff, #7fc8ff); }
.gradient-card.alt { background: linear-gradient(135deg, #45d1b0, #2f7cf1); }
.gradient-card.soft { background: linear-gradient(135deg, #ffb76b, #ff8c6f); }
.kpi-label { font-size: 0.85rem; letter-spacing: .04em; text-transform: uppercase; opacity: .9; }
.kpi-value { font-size: 2rem; font-weight: 700; line-height: 1.2; }
.kpi-helper { margin: 0; opacity: .9; }

.calendar-card {
    border: 1px solid var(--border-subtle);
    background: linear-gradient(180deg, var(--surface), rgba(255,255,255,0.04));
    box-shadow: var(--shadow-card-soft);
}
.calendar-card .calendar-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 14px;
}
.calendar-card .calendar-day {
    border: 1px solid var(--border-subtle);
    border-radius: 14px;
    padding: 12px;
    background: var(--surface);
    box-shadow: var(--shadow-card-soft);
    min-height: 148px;
    transition: transform .15s ease, box-shadow .15s ease;
}
.calendar-card .calendar-day:hover { transform: translateY(-2px); box-shadow: var(--shadow-card); }
.calendar-card .calendar-day.is-today { border-color: var(--primary); box-shadow: 0 6px 18px rgba(64, 124, 255, 0.12); }
.calendar-card .day-label { font-size: .85rem; color: var(--muted); }
.calendar-card .day-number { font-weight: 700; }
.calendar-card .progress { background: var(--border-subtle); border-radius: 6px; }
.calendar-card .progress-bar { background: linear-gradient(135deg, var(--primary), var(--primary-strong)); }

.icon-pill {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    box-shadow: var(--shadow-card-soft);
}

.table th { color: var(--muted-strong); font-weight: 700; }
.table tbody tr + tr { border-top: 1px solid var(--border-subtle); }
.table td { vertical-align: middle; }

.slim-scroll { max-height: 420px; }

.nav.soft-tabs .nav-link { border: none; background: transparent; color: var(--muted); padding: 10px 16px; font-weight: 600; }
.nav.soft-tabs .nav-link.active { color: var(--primary); border-bottom: 2px solid var(--primary); }

.js-month-change { transition: transform .15s ease; }
.js-month-change:hover { transform: translateY(-1px); }
</style>
