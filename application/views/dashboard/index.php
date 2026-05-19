<?php $activeQuickActions = array_values(array_filter(($quickActions ?? []), static fn(array $action): bool => ($action['enabled'] ?? '1') === '1')); ?>
<?php if (!empty($activeQuickActions)): ?>
<section class="quick-actions" id="card-acoes-rapidas" aria-label="Ações rápidas">
    <div class="quick-actions-header">
        <div>
            <h3>Ações rápidas</h3>
            <p class="panel-description">Use os atalhos de teclado ou clique para navegar diretamente.</p>
        </div>
    </div>
    <div style="display: none;" aria-hidden="true">
        <?php foreach ($activeQuickActions as $action): ?>
            <input type="checkbox" class="acao-rapida-checkbox" checked>
        <?php endforeach; ?>
    </div>
    <div class="action-grid">
        <?php foreach ($activeQuickActions as $action): ?>
            <a class="action-button" href="<?= htmlspecialchars($url($action['href'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                <span class="action-leading">
                    <?php if (!empty($action['shortcut'])): ?>
                        <span class="shortcut"><?= htmlspecialchars($action['shortcut'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <span class="action-icon" aria-hidden="true"><span data-lucide="<?= htmlspecialchars($action['icon'] ?? 'star', ENT_QUOTES, 'UTF-8'); ?>"></span></span>
                </span>
                <div class="action-copy">
                    <strong><?= htmlspecialchars($action['label'] ?? 'Ação rápida', ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span><?= htmlspecialchars($action['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="alert-stack" aria-label="Alertas críticos">
    <div class="alert-stack-header">
        <div>
            <h3>Alertas críticos</h3>
            <p class="panel-description">Validade, sem estoque, estoque baixo e negativo em um único card.</p>
        </div>
    </div>
    <div class="alert-stack-body">
        <div class="alert-stack-item warning">
            <div class="stack-top">
                <div class="stack-icon" aria-hidden="true"><span data-lucide="alert-triangle"></span></div>
                <div>
                    <p>Validade a vencer</p>
                    <strong><?= number_format($alertasValidade['count'] ?? 0, 0, ',', '.'); ?></strong>
                </div>
            </div>
            <p class="stack-copy">Itens com validade em alerta nos próximos dias.</p>
            <a class="stack-link" href="<?= htmlspecialchars($url('estoque/posicao'), ENT_QUOTES, 'UTF-8'); ?>">Ir para estoque</a>
            <ul class="stack-list">
                <?php $produtosValidade = $alertasValidade['produtos'] ?? []; ?>
                <?php if (!empty($produtosValidade)): ?>
                    <?php foreach (array_slice($produtosValidade, 0, 4) as $produto): ?>
                        <li><?= htmlspecialchars($produto, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                    <?php if (count($produtosValidade) > 4): ?>
                        <li class="stack-more">+<?= count($produtosValidade) - 4; ?> outros</li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="stack-empty">Nenhum produto listado.</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="alert-stack-item info">
            <div class="stack-top">
                <div class="stack-icon" aria-hidden="true"><span data-lucide="activity"></span></div>
                <div>
                    <p>Estoque baixo</p>
                    <strong><?= number_format($estoqueStatus['baixos'] ?? 0, 0, ',', '.'); ?></strong>
                </div>
            </div>
            <p class="stack-copy">Produtos dentro da faixa configurada.</p>
            <a class="stack-link" href="<?= htmlspecialchars($url('mapa'), ENT_QUOTES, 'UTF-8'); ?>">Abrir mapa</a>
            <ul class="stack-list">
                <?php $produtosBaixos = $estoqueStatus['produtos']['baixos'] ?? []; ?>
                <?php if (!empty($produtosBaixos)): ?>
                    <?php foreach (array_slice($produtosBaixos, 0, 4) as $produto): ?>
                        <li><?= htmlspecialchars($produto, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                    <?php if (count($produtosBaixos) > 4): ?>
                        <li class="stack-more">+<?= count($produtosBaixos) - 4; ?> outros</li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="stack-empty">Nenhum produto listado.</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="alert-stack-item neutral">
            <div class="stack-top">
                <div class="stack-icon" aria-hidden="true"><span data-lucide="minus-circle"></span></div>
                <div>
                    <p>Sem estoque</p>
                    <strong><?= number_format($estoqueStatus['zerados'] ?? 0, 0, ',', '.'); ?></strong>
                </div>
            </div>
            <p class="stack-copy">Itens com saldo zerado aguardando reposição.</p>
            <a class="stack-link" href="<?= htmlspecialchars($url('estoque/posicao'), ENT_QUOTES, 'UTF-8'); ?>">Ver detalhes</a>
            <ul class="stack-list">
                <?php $produtosZerados = $estoqueStatus['produtos']['zerados'] ?? []; ?>
                <?php if (!empty($produtosZerados)): ?>
                    <?php foreach (array_slice($produtosZerados, 0, 4) as $produto): ?>
                        <li><?= htmlspecialchars($produto, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                    <?php if (count($produtosZerados) > 4): ?>
                        <li class="stack-more">+<?= count($produtosZerados) - 4; ?> outros</li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="stack-empty">Nenhum produto listado.</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="alert-stack-item danger">
            <div class="stack-top">
                <div class="stack-icon" aria-hidden="true"><span data-lucide="slash"></span></div>
                <div>
                    <p>Estoque negativo</p>
                    <strong><?= number_format($estoqueStatus['negativos'] ?? 0, 0, ',', '.'); ?></strong>
                </div>
            </div>
            <p class="stack-copy">Itens com saldo abaixo de zero aguardando reposição.</p>
            <a class="stack-link" href="<?= htmlspecialchars($url('estoque/posicao'), ENT_QUOTES, 'UTF-8'); ?>">Ver detalhes</a>
            <ul class="stack-list">
                <?php $produtosNegativos = $estoqueStatus['produtos']['negativos'] ?? []; ?>
                <?php if (!empty($produtosNegativos)): ?>
                    <?php foreach (array_slice($produtosNegativos, 0, 4) as $produto): ?>
                        <li><?= htmlspecialchars($produto, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                    <?php if (count($produtosNegativos) > 4): ?>
                        <li class="stack-more">+<?= count($produtosNegativos) - 4; ?> outros</li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="stack-empty">Nenhum produto listado.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</section>

<section class="dashboard-hero" aria-label="Resumo geral">
    <div class="hero-primary">
        <span class="hero-chip">Operação em andamento</span>
        <h2>Visão geral do SCI</h2>
        <p>Monitore pedidos, produção e finanças em tempo real para manter a indústria sob controle.</p>
    </div>
    <div class="hero-secondary">
        <div class="hero-alert">
            <span class="hero-alert-icon"><span data-lucide="bell-ring"></span></span>
            <div>
                <strong>Próximas rotinas</strong>
                <small>Reforce o cron diário de validade e confirme reservas críticas.</small>
            </div>
        </div>
    </div>
</section>

<section class="dashboard-metrics" aria-label="Indicadores principais">
    <article class="metric-card">
        <span class="metric-icon"><span data-lucide="clipboard-list"></span></span>
        <div class="metric-content">
            <h2>Pedidos ativos</h2>
            <strong><?= number_format($stats['pedidos'], 0, ',', '.'); ?></strong>
            <p>Pedidos registrados e em acompanhamento.</p>
        </div>
    </article>
    <article class="metric-card">
        <span class="metric-icon"><span data-lucide="package"></span></span>
        <div class="metric-content">
            <h2>Produtos</h2>
            <strong><?= number_format($stats['produtos'], 0, ',', '.'); ?></strong>
            <p>Itens disponíveis no catálogo interno e portal.</p>
        </div>
    </article>
    <article class="metric-card">
        <span class="metric-icon"><span data-lucide="users"></span></span>
        <div class="metric-content">
            <h2>Clientes</h2>
            <strong><?= number_format($stats['clientes'], 0, ',', '.'); ?></strong>
            <p>Clientes ativos com acesso ao portal IndusOS.</p>
        </div>
    </article>
</section>

<div class="dashboard-grid split">
    <section class="calendar-card" aria-label="Agenda de vendas" data-month="<?= (int)($calendarMeta['month'] ?? date('n')); ?>" data-year="<?= (int)($calendarMeta['year'] ?? date('Y')); ?>" data-endpoint="<?= htmlspecialchars($url('dashboard'), ENT_QUOTES, 'UTF-8'); ?>">
        <?php
        $monthLabel = $calendarMeta['label'] ?? (new DateTimeImmutable('today'))->format('m/Y');
        $monthCompletion = number_format((float)($calendarMeta['completion'] ?? 0), 1, ',', '.');
        $selectedDay = $calendar[0] ?? ['day' => '--', 'label' => '', 'value_formatted' => 'R$ 0,00', 'count' => 0, 'entrada' => 0, 'producao' => 0, 'completion' => 0, 'ordered_qty' => 0, 'date' => date('Y-m-d')];
        foreach ($calendar as $entryOption) {
            if (!empty($entryOption['today'])) {
                $selectedDay = $entryOption;
                break;
            }
        }
        ?>
        <div class="calendar-card-header">
            <div class="calendar-icon">
                <span data-lucide="calendar-range"></span>
                <span class="calendar-sales" data-lucide="shopping-bag"></span>
            </div>
            <div class="calendar-header-meta">
                <div class="calendar-nav" aria-label="Selecionar mês">
                    <button type="button" class="calendar-nav-btn" data-nav="prev" aria-label="Mês anterior"><span data-lucide="chevron-left"></span></button>
                    <div class="calendar-nav-label">
                        <strong data-month-label><?= htmlspecialchars($monthLabel, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <button type="button" class="calendar-nav-btn" data-nav="next" aria-label="Próximo mês"><span data-lucide="chevron-right"></span></button>
                </div>
                <p class="panel-description">Selecione um dia para acompanhar pedidos e produção.</p>
            </div>
            <div class="calendar-meta-pill" aria-live="polite">
                <span>Conclusão de produção do mês</span>
                <strong data-month-completion-badge><?= htmlspecialchars($monthCompletion, ENT_QUOTES, 'UTF-8'); ?>%</strong>
            </div>
        </div>

        <div class="calendar-detail" id="calendar-detail" data-selected-date="<?= htmlspecialchars($selectedDay['date'], ENT_QUOTES, 'UTF-8'); ?>">
            <div>
                <p class="detail-label">Dia</p>
                <strong data-detail-date><?= htmlspecialchars($selectedDay['day'].' · '.$selectedDay['label'], ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div>
                <p class="detail-label">Pedidos</p>
                <strong data-detail-pedidos><?= (int)($selectedDay['count'] ?? 0); ?></strong>
                <small data-detail-valor><?= htmlspecialchars($selectedDay['value_formatted'] ?? 'R$ 0,00', ENT_QUOTES, 'UTF-8'); ?></small>
            </div>
            <div>
                <p class="detail-label">Produção</p>
                <strong data-detail-inflow><?= htmlspecialchars(number_format(($selectedDay['producao'] ?? 0), 0, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></strong>
                <small>Solicitado: <span data-detail-ordered><?= htmlspecialchars(number_format($selectedDay['ordered_qty'] ?? 0, 0, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></span></small>
            </div>
            <div>
                <p class="detail-label">Conclusão de produção</p>
                <strong data-detail-completion><?= htmlspecialchars(number_format($selectedDay['completion'] ?? 0, 1, ',', '.')); ?>%</strong>
                <small>Percentual médio do dia</small>
            </div>
        </div>

        <div class="calendar-month" role="list">
            <?php foreach ($calendar as $entry): ?>
                <div class="calendar-day <?= !empty($entry['today']) ? 'is-today' : ''; ?>" role="listitem" title="<?= htmlspecialchars($entry['tooltip'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                     data-day="<?= htmlspecialchars($entry['day'], ENT_QUOTES, 'UTF-8'); ?>"
                     data-label="<?= htmlspecialchars($entry['label'], ENT_QUOTES, 'UTF-8'); ?>"
                     data-date="<?= htmlspecialchars($entry['date'], ENT_QUOTES, 'UTF-8'); ?>"
                     data-count="<?= (int)($entry['count'] ?? 0); ?>"
                     data-value="<?= htmlspecialchars((string)($entry['value'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                     data-value-formatted="<?= htmlspecialchars($entry['value_formatted'] ?? 'R$ 0,00', ENT_QUOTES, 'UTF-8'); ?>"
                     data-entrada="<?= htmlspecialchars((string)($entry['entrada'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                     data-producao="<?= htmlspecialchars((string)($entry['producao'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                     data-ordered="<?= htmlspecialchars((string)($entry['ordered_qty'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                     data-completion="<?= htmlspecialchars((string)($entry['completion'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                     data-completion-label="<?= htmlspecialchars(number_format($entry['completion'] ?? 0, 1, ',', '.'), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="calendar-day-head">
                        <span class="calendar-day-number"><?= htmlspecialchars($entry['day'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="calendar-day-label"><?= htmlspecialchars($entry['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="calendar-day-meta">
                        <strong><?= (int)($entry['count'] ?? 0); ?> pedidos</strong>
                        <small><?= htmlspecialchars($entry['value_formatted'] ?? 'R$ 0,00', ENT_QUOTES, 'UTF-8'); ?></small>
                        <em><?= htmlspecialchars(number_format($entry['completion'] ?? 0, 1, ',', '.')); ?>% conclusão</em>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="timeline" aria-label="Próximos passos">
        <h3>Próximos passos</h3>
        <ul>
            <li>
                <strong>Reserve lotes a vencer</strong>
                <span>Confira o relatório de validade e acione o cron diário.</span>
            </li>
            <li>
                <strong>Revise pedidos aguardando aprovação</strong>
                <span>Garanta que as OPs foram disparadas para backorders.</span>
            </li>
            <li>
                <strong>Atualize configurações do portal</strong>
                <span>Defina política de baixa automática conforme o cliente.</span>
            </li>
        </ul>
    </section>
</div>

<style>
    .dashboard-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 420px);
        gap: 28px;
        padding: 28px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
    }
    .hero-primary h2 {
        margin: 10px 0 12px;
        font-size: 1.9rem;
        letter-spacing: -0.02em;
    }
    .hero-primary p {
        margin: 0;
        color: var(--text-muted);
        max-width: 520px;
    }
    .hero-secondary {
        display: flex;
        flex-direction: column;
        gap: 18px;
        justify-content: flex-end;
    }
    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 999px;
        background: var(--pill-bg);
        color: var(--primary);
    }
    .hero-alert {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px 20px;
        border-radius: 18px;
        background: var(--surface-alt);
        border: 1px solid var(--border);
        min-width: 260px;
    }
    .hero-alert strong {
        display: block;
        font-size: 1rem;
    }
    .hero-alert small {
        color: var(--text-muted);
    }
    .hero-alert-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: var(--primary-soft);
        color: var(--primary-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .hero-alert-icon [data-lucide],
    .metric-icon [data-lucide],
    .action-icon [data-lucide] {
        width: 24px;
        height: 24px;
    }
    .alert-config {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--surface);
        color: inherit;
        text-decoration: none;
        font-weight: 600;
        box-shadow: var(--shadow-sm);
    }
    .alert-config [data-lucide] { width: 18px; height: 18px; }
    .alert-stack {
        margin: 16px 0 22px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 16px;
        box-shadow: var(--shadow-card);
        display: grid;
        gap: 14px;
    }
    .alert-stack-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .alert-stack-body {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
    }
    .alert-stack-item {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 14px;
        background: var(--surface-2, var(--surface));
        box-shadow: var(--shadow-soft);
        display: grid;
        gap: 8px;
    }
    .stack-top {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 10px;
        align-items: center;
    }
    .stack-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(91, 97, 111, 0.08);
        color: var(--primary);
    }
    .alert-stack-item p { margin: 0; color: var(--text-muted); }
    .alert-stack-item strong { font-size: 1.6rem; letter-spacing: -0.02em; }
    .stack-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
        font-size: 0.94rem;
    }
    .stack-list {
        margin: 0;
        padding-left: 16px;
        display: grid;
        gap: 4px;
        color: var(--text-muted);
        font-size: 0.95rem;
    }
    .stack-list li { list-style: disc; }
    .stack-empty { color: var(--text-muted); opacity: 0.85; }
    .stack-more { color: var(--primary); font-weight: 600; list-style: none; padding-left: 0; }
    .alert-stack-item.warning .stack-icon { background: #fff7ed; color: #c05621; }
    .alert-stack-item.warning .stack-link { color: #c05621; }
    .alert-stack-item.info .stack-icon { background: #eef2ff; color: #4338ca; }
    .alert-stack-item.info .stack-link { color: #4338ca; }
    .alert-stack-item.neutral .stack-icon { background: #f3f4f6; color: #374151; }
    .alert-stack-item.neutral .stack-link { color: #374151; }
    .alert-stack-item.danger .stack-icon { background: #fef2f2; color: #c53030; }
    .alert-stack-item.danger .stack-link { color: #c53030; }
    .dashboard-metrics {
        margin-top: 24px;
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    }
    .metric-card {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 22px 24px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
    }
    .metric-card h2 {
        margin: 0;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--text-muted);
    }
    .metric-card strong {
        font-size: 2.2rem;
        letter-spacing: -0.03em;
        display: block;
    }
    .metric-card p {
        margin: 4px 0 0;
        color: var(--text-muted);
    }
    .metric-icon {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        background: rgba(91, 97, 111, 0.16);
        color: var(--primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    body.theme-dark .hero-alert {
        background: rgba(30, 41, 59, 0.7);
    }
    body.theme-dark .hero-alert-icon {
        background: rgba(148, 163, 184, 0.18);
        color: var(--primary-soft);
    }
    body.theme-dark .metric-icon {
        background: rgba(148, 163, 184, 0.18);
        color: var(--primary-soft);
    }
    .quick-actions {
        background: var(--surface-alt);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        padding: 22px 24px;
        margin-bottom: 28px;
    }
    .quick-actions-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .quick-actions h3 {
        margin: 0;
        font-size: 1.05rem;
        letter-spacing: -0.01em;
    }
    .action-grid {
        display: grid;
        gap: 8px;
        margin-top: 16px;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        width: 100%;
    }
    .action-button {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        padding: 16px 14px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        text-decoration: none;
        color: inherit;
        box-shadow: var(--shadow-sm);
        min-width: 0;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .action-leading {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }
    .action-icon {
        width: 46px;
        height: 46px;
        border-radius: 17px;
        background: linear-gradient(135deg, var(--primary-soft), var(--primary));
        color: rgba(0, 0, 0, 0.66);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.14);
    }
    .action-icon span[data-lucide] {
        width: 24px;
        height: 24px;
        color: inherit;
    }
    body.theme-dark .action-icon {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: rgba(12, 18, 33, 0.78);
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.22);
    }
    body.theme-dark .action-icon span[data-lucide] {
        filter: drop-shadow(0 0 1px rgba(255, 255, 255, 0.18));
    }
    .action-copy {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
    }
    .shortcut {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 10px;
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        background: linear-gradient(135deg, var(--primary-soft), var(--primary));
        color: var(--primary-dark);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.18);
    }
    body.theme-dark .shortcut {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: #f8fafc;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.22);
    }
    .action-copy strong {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text);
    }
    .action-copy span {
        display: block;
        font-size: 0.54rem;
        color: var(--text-muted);
        line-height: 1.28;
    }
    @media (max-width: 900px) {
        .action-grid {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        }
    }
    @media (max-width: 640px) {
        .quick-actions {
            padding: 18px 16px;
        }
        .action-button {
            padding: 16px 14px;
        }
    }
    .calendar-card,
    .timeline {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        padding: 24px;
    }
    .calendar-card-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }
    .calendar-header-meta {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .calendar-nav {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .calendar-nav-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text-muted);
        transition: all 0.15s ease;
    }
    .calendar-nav-btn:hover {
        color: var(--primary-dark);
        border-color: var(--primary-soft);
        background: var(--primary-soft);
    }
    .calendar-nav-label {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .calendar-nav-label strong {
        font-size: 1.05rem;
    }
    .calendar-nav-label small {
        color: var(--text-muted);
        font-weight: 600;
    }
    .calendar-meta-pill {
        margin-left: auto;
        padding: 10px 14px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8d7da, #f2b6c0);
        border: 1px solid rgba(255, 99, 132, 0.3);
        color: #5c1b24;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: var(--shadow-soft);
    }
    .calendar-meta-pill span {
        font-size: 0.9rem;
        color: rgba(92, 27, 36, 0.75);
    }
    .calendar-meta-pill strong {
        font-size: 1.2rem;
        color: #a3203f;
        letter-spacing: -0.01em;
    }
    .calendar-icon {
        position: relative;
        width: 56px;
        height: 56px;
        border-radius: 18px;
        background: var(--surface-alt);
        color: var(--primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-soft);
    }
    .calendar-icon [data-lucide] {
        width: 28px;
        height: 28px;
    }
    .calendar-sales {
        position: absolute;
        bottom: -6px;
        right: -6px;
        width: 26px;
        height: 26px;
        border-radius: 10px;
        background: var(--primary);
        color: var(--surface);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-sm);
    }
    .calendar-month {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
        margin-top: 12px;
    }
    .calendar-detail {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        padding: 14px 16px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: var(--surface-alt);
        box-shadow: var(--shadow-soft);
    }
    .detail-label {
        margin: 0 0 4px;
        color: var(--text-muted);
        font-size: 0.85rem;
        letter-spacing: 0.01em;
    }
    .calendar-detail strong {
        display: block;
        font-size: 1.05rem;
    }
    .calendar-detail small {
        color: var(--text-muted);
    }
    .calendar-day {
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 10px 12px;
        background: var(--surface-alt);
        box-shadow: var(--shadow-soft);
        min-height: 90px;
        display: grid;
        gap: 8px;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .calendar-day:hover {
        transform: translateY(-1px);
        border-color: var(--primary-soft);
        box-shadow: var(--shadow-card);
    }
    .calendar-day.is-today {
        border-color: var(--primary, #22c55e);
        background: linear-gradient(180deg, rgba(34, 197, 94, 0.08), rgba(34, 197, 94, 0.02));
        color: var(--text, #0f172a);
        box-shadow: 0 0 0 2px var(--primary, #22c55e);
    }
    .calendar-day.is-today .calendar-day-number { color: var(--primary-dark, #0f172a); }
    .calendar-day.is-today .calendar-day-label { color: var(--text, #0f172a); }
    .calendar-day.is-today .calendar-day-meta small { color: var(--text-muted, #475569); }
    .calendar-day.is-today .calendar-day-meta em { color: var(--primary, #22c55e); }
    .calendar-day.is-today .calendar-day-meta strong { color: var(--primary-dark, #0f172a); }
    .calendar-day.is-selected {
        background: var(--primary-soft, rgba(34, 197, 94, 0.12));
        border-color: var(--primary, #22c55e);
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
    }
    body.theme-dark .calendar-day.is-selected {
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.3);
    }
    .calendar-day-head {
        display: flex;
        align-items: baseline;
        gap: 6px;
        justify-content: space-between;
    }
    .calendar-day-number { font-weight: 700; font-size: 1.05rem; letter-spacing: -0.02em; }
    .calendar-day-label { color: var(--text-muted); font-weight: 600; }
    .calendar-day-meta strong { display: block; }
    .calendar-day-meta small { color: var(--text-muted); }
    .calendar-day-meta em { color: var(--primary-dark); font-style: normal; font-weight: 600; }
    .calendar-day {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 14px;
        border-radius: var(--radius-md);
        background: var(--surface-alt);
        border: 1px solid var(--border);
        min-height: 110px;
    }
    .calendar-day strong {
        font-size: 0.85rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--text-muted);
    }
    .calendar-day span {
        font-weight: 600;
        font-size: 1rem;
    }
    .calendar-day em {
        font-style: normal;
        color: var(--primary-dark);
        font-weight: 600;
    }
    .calendar-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 20px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }
    .calendar-link [data-lucide] {
        width: 18px;
        height: 18px;
    }
    .timeline h3 {
        margin: 0 0 12px;
        font-size: 1.05rem;
    }
    .timeline ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .timeline li {
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding-bottom: 14px;
        border-bottom: 1px dashed var(--border);
    }
    .timeline li:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .timeline li strong {
        font-size: 0.95rem;
    }
    .timeline li span {
        color: var(--text-muted);
        font-size: 0.85rem;
    }
    @media (max-width: 1200px) {
        .dashboard-hero {
            grid-template-columns: minmax(0, 1.2fr) minmax(260px, 1fr);
        }
        .action-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
    }
    @media (max-width: 1024px) {
        .dashboard-hero {
            grid-template-columns: 1fr;
        }
        .hero-secondary {
            order: -1;
        }
    }
    @media (max-width: 960px) {
        .action-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 640px) {
        .action-grid {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        .calendar-month {
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        }
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const detail = {
        date: document.querySelector('[data-detail-date]'),
        pedidos: document.querySelector('[data-detail-pedidos]'),
        valor: document.querySelector('[data-detail-valor]'),
        inflow: document.querySelector('[data-detail-inflow]'),
        ordered: document.querySelector('[data-detail-ordered]'),
        completion: document.querySelector('[data-detail-completion]'),
    };
    const calendarCard = document.querySelector('.calendar-card');
    const monthLabel = document.querySelector('[data-month-label]');
    const monthCompletion = document.querySelector('[data-month-completion-badge]');
    const calendarGrid = document.querySelector('.calendar-month');

    function formatPercent(value) {
        return `${Number(value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%`;
    }

    function updateDetail(day) {
        if (!day || !detail.date) return;
        const days = Array.from(calendarGrid.querySelectorAll('.calendar-day'));
        days.forEach(d => d.classList.remove('is-selected'));
        day.classList.add('is-selected');

        const entrada = Number(day.dataset.entrada || 0);
        const producao = Number(day.dataset.producao || 0);
        const ordered = Number(day.dataset.ordered || 0);
        const completion = Number(day.dataset.completion || 0);

        detail.date.textContent = `${day.dataset.day || '--'} · ${day.dataset.label || ''}`;
        detail.pedidos.textContent = day.dataset.count || '0';
        detail.valor.textContent = day.dataset.valueFormatted || 'R$ 0,00';
        detail.inflow.textContent = producao.toLocaleString('pt-BR');
        detail.ordered.textContent = ordered.toLocaleString('pt-BR');
        detail.completion.textContent = formatPercent(completion);
    }

    function renderDays(entries) {
        calendarGrid.innerHTML = entries.map(entry => `
            <div class="calendar-day ${entry.today ? 'is-today' : ''}" role="listitem" title="${entry.tooltip || ''}"
                 data-day="${entry.day}" data-label="${entry.label}" data-date="${entry.date}"
                 data-count="${entry.count}" data-value="${entry.value}" data-value-formatted="${entry.value_formatted}"
                 data-entrada="${entry.entrada}" data-producao="${entry.producao}" data-ordered="${entry.ordered_qty}"
                 data-completion="${entry.completion}" data-completion-label="${(entry.completion ?? 0).toFixed(1)}">
                <div class="calendar-day-head">
                    <span class="calendar-day-number">${entry.day}</span>
                    <span class="calendar-day-label">${entry.label}</span>
                </div>
                <div class="calendar-day-meta">
                    <strong>${entry.count} pedidos</strong>
                    <small>${entry.value_formatted}</small>
                    <em>${Number(entry.completion || 0).toLocaleString('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}% conclusão</em>
                </div>
            </div>
        `).join('');

        const days = Array.from(calendarGrid.querySelectorAll('.calendar-day'));
        days.forEach(day => day.addEventListener('click', () => updateDetail(day)));
        const initial = days.find(d => d.classList.contains('is-today')) || days[0];
        if (initial) {
            updateDetail(initial);
        }
    }

    function updateHeader(meta) {
        if (!calendarCard) return;
        if (meta.month) {
            calendarCard.dataset.month = meta.month;
        }
        if (meta.year) {
            calendarCard.dataset.year = meta.year;
        }
        if (monthLabel) {
            monthLabel.textContent = meta.label || `${meta.month}/${meta.year}`;
        }
        if (monthCompletion) {
            monthCompletion.textContent = formatPercent(meta.completion ?? 0);
        }
    }

    async function loadMonth(delta = 0) {
        if (!calendarCard) return;
        const currentMonth = parseInt(calendarCard.dataset.month, 10) || new Date().getMonth() + 1;
        const currentYear = parseInt(calendarCard.dataset.year, 10) || new Date().getFullYear();
        const baseDate = new Date(currentYear, currentMonth - 1 + delta, 1);
        const month = baseDate.getMonth() + 1;
        const year = baseDate.getFullYear();

        const endpoint = calendarCard.dataset.endpoint || '/dashboard';
        const fallbackUrl = (() => {
            const url = new URL(endpoint, window.location.origin);
            url.searchParams.set('month', month);
            url.searchParams.set('year', year);
            return url.toString();
        })();

        try {
            const response = await fetch(fallbackUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const contentType = response.headers.get('Content-Type') || '';
            if (!response.ok || contentType.indexOf('application/json') === -1) {
                window.location.href = fallbackUrl;
                return;
            }

            const data = await response.json();
            updateHeader(data.calendar_meta || {});
            renderDays(data.calendar || []);
            if (window.lucide) {
                lucide.createIcons();
            }
        } catch (e) {
            console.error('Erro ao atualizar o calendário', e);
            window.location.href = fallbackUrl;
        }
    }

    const navButtons = document.querySelectorAll('.calendar-nav-btn');
    navButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const delta = btn.dataset.nav === 'prev' ? -1 : 1;
            loadMonth(delta);
        });
    });

    renderDays(<?= json_encode($calendar, JSON_UNESCAPED_UNICODE); ?>);

    // Garantir que o calendário inicia no mês atual quando não há filtros na URL
    const urlParams = new URLSearchParams(window.location.search);
    const hasQueryMonth = urlParams.has('month') || urlParams.has('year');
    if (!hasQueryMonth && calendarCard) {
        const today = new Date();
        const dsMonth = parseInt(calendarCard.dataset.month, 10) || today.getMonth() + 1;
        const dsYear = parseInt(calendarCard.dataset.year, 10) || today.getFullYear();
        const delta = (today.getFullYear() - dsYear) * 12 + (today.getMonth() + 1 - dsMonth);
        if (delta !== 0) {
            loadMonth(delta);
        }
    }
});
</script>
