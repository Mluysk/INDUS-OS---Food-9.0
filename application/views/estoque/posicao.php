<?php
    $status = $status ?? ['negativos' => 0, 'zerados' => 0, 'baixos' => 0];
    $faixa = $faixaBaixa ?? ['min' => 4, 'max' => 8];
    $formatDate = static function (?string $date): string {
        if (!$date) {
            return '—';
        }
        $timestamp = strtotime($date);
        return $timestamp ? date('d/m/Y', $timestamp) : '—';
    };
?>

<div class="tabs" role="tablist">
    <button type="button" class="tab-button active" data-tab="estoque" aria-selected="true">Estoque</button>
    <button type="button" class="tab-button" data-tab="avancado" aria-selected="false">Estoque avançada</button>
</div>

<section class="tab-panel active" data-tab-panel="estoque">
    <div class="panel">
        <h2>Alertas de validade</h2>
        <p class="panel-description">Itens com vencimento próximo (configuração atual).</p>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Produto</th>
                        <th>Lote</th>
                        <th>Validade</th>
                        <th>Dias para vencer</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($alertas): ?>
                        <?php foreach ($alertas as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['sku'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($item['lote'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($item['validade'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= (int)($item['dias_para_vencer'] ?? 0); ?></td>
                                <td><?= number_format((float)$item['quantidade'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nenhum alerta de validade para o período informado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <h2>Posição atual</h2>
        <div class="stock-status">
            <div class="status-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="0.9"/></svg>
            </div>
            <div class="status-copy">
                <div class="status-title">Status do estoque</div>
                <div class="status-sub">Acompanhe saldos negativos, zerados ou baixos (<?= (int)($faixa['min'] ?? 0); ?>–<?= (int)($faixa['max'] ?? 0); ?>).</div>
            </div>
            <div class="status-tags">
                <div class="status-tag negative">
                    <span>Negativo</span>
                    <strong><?= (int)($status['negativos'] ?? 0); ?></strong>
                </div>
                <div class="status-tag empty">
                    <span>Sem estoque</span>
                    <strong><?= (int)($status['zerados'] ?? 0); ?></strong>
                </div>
                <div class="status-tag low">
                    <span>Estoque baixo</span>
                    <strong><?= (int)($status['baixos'] ?? 0); ?></strong>
                </div>
            </div>
        </div>
        <div class="stock-filters">
            <div class="search-group">
                <label for="estoque-search">Buscar</label>
                <input type="search" id="estoque-search" placeholder="SKU ou produto" />
            </div>
            <div class="search-group">
                <label for="estoque-status">Status</label>
                <select id="estoque-status">
                    <option value="ALL">Todos</option>
                    <option value="NEG">Negativo</option>
                    <option value="ZERO">Sem estoque</option>
                    <option value="LOW">Estoque baixo</option>
                    <option value="OK">Normal</option>
                </select>
            </div>
            <?php $printBase = $url('estoque/posicao/print'); ?>
            <div class="search-group print-group">
                <label>&nbsp;</label>
                <button type="button" class="print-action" id="estoque-print-btn" data-print="<?= htmlspecialchars($printBase, ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="icon">🖨️</span> Imprimir posição
                </button>
            </div>
        </div>
        <div class="filter-toggle">
            <button type="button" class="filter-btn active" data-type="ALL">Todos</button>
            <button type="button" class="filter-btn" data-type="PA">Produtos acabados</button>
            <button type="button" class="filter-btn" data-type="MP">Matérias-primas</button>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table" id="estoque-posicao">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Planejado (mapa)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($posicao): ?>
                        <?php foreach ($posicao as $linha): ?>
                            <?php
                                $saldo = (float)($linha['saldo'] ?? ($linha['quantidade'] ?? 0));
                                $planejado = (float)($linha['planejado_mapa'] ?? 0);
                                $statusKey = $linha['status_key'] ?? 'OK';
                                $qtyClass = 'qty-ok';
                                if ($statusKey === 'NEG') {
                                    $qtyClass = 'qty-neg';
                                } elseif ($statusKey === 'ZERO') {
                                    $qtyClass = 'qty-zero';
                                } elseif ($statusKey === 'LOW') {
                                    $qtyClass = 'qty-low';
                                }
                                $searchText = strtolower(($linha['sku'] ?? '').' '.($linha['nome'] ?? ''));
                            ?>
                            <tr data-type="<?= htmlspecialchars($linha['tipo'] ?? 'PA', ENT_QUOTES, 'UTF-8'); ?>" data-status="<?= $statusKey; ?>" data-search="<?= htmlspecialchars($searchText, ENT_QUOTES, 'UTF-8'); ?>">
                                <td><?= htmlspecialchars($linha['sku'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($linha['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="badge <?= ($linha['tipo'] ?? 'PA') === 'PA' ? 'badge-success' : 'badge-warning'; ?>">
                                    <?= htmlspecialchars($linha['tipo'] ?? 'PA', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td><span class="qty-pill <?= $qtyClass; ?>"><?= number_format($saldo, 0, ',', '.'); ?></span></td>
                                <td><?= number_format($planejado, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Nenhum registro de estoque encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="tab-panel" data-tab-panel="avancado">
    <div class="panel">
        <h2>Estoque avançada</h2>
        <p class="panel-description">
            Detalhes por lote com fornecedor, datas de fabricação/validade/recebimento e o consumo registrado.
            As receitas de MP geram saídas no estoque (movimentos) e alimentam a coluna de consumo.
        </p>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Lote</th>
                        <th>Saldo</th>
                        <th>Validade</th>
                        <th>Fabricação</th>
                        <th>Fornecedor</th>
                        <th>Recebimento</th>
                        <th>Consumo final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($estoqueAvancado)): ?>
                        <?php foreach ($estoqueAvancado as $linha): ?>
                            <?php
                                $saldo = (float)($linha['saldo'] ?? 0);
                                $consumoFinal = $saldo <= 0 ? $formatDate($linha['ultimo_consumo'] ?? null) : '—';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($linha['sku'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($linha['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="badge <?= ($linha['tipo'] ?? 'PA') === 'PA' ? 'badge-success' : 'badge-warning'; ?>">
                                    <?= htmlspecialchars($linha['tipo'] ?? 'PA', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td><?= htmlspecialchars($linha['lote'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= number_format($saldo, 0, ',', '.'); ?></td>
                                <td><?= $formatDate($linha['validade'] ?? null); ?></td>
                                <td><?= $formatDate($linha['data_fabricacao'] ?? null); ?></td>
                                <td><?= htmlspecialchars($linha['fornecedor'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= $formatDate($linha['data_entrega'] ?? null); ?></td>
                                <td><?= $consumoFinal; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">Nenhum registro avançado disponível no momento.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<style>
.tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}
.tab-button {
    border: 1px solid var(--border, #e5e7eb);
    border-radius: 999px;
    padding: 8px 14px;
    font-weight: 700;
    background: var(--surface-alt, #f8fafc);
    cursor: pointer;
}
.tab-button.active {
    background: var(--primary, #4b5563);
    color: #fff;
    border-color: transparent;
}
.tab-panel { display: none; }
.tab-panel.active { display: block; }
.stock-filters {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin: 12px 0 4px;
}
.stock-filters .search-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 220px;
}
.stock-filters .print-group {
    align-self: flex-end;
}
.stock-filters label {
    font-weight: 600;
    color: var(--text-muted, #374151);
}
.stock-filters input,
.stock-filters select {
    border: 1px solid var(--border-muted, #e5e7eb);
    border-radius: 10px;
    padding: 10px 12px;
    background: var(--surface, #fff);
    color: var(--text, #111827);
    box-shadow: 0 6px 12px rgba(0,0,0,0.03);
}
.print-action {
    width: 100%;
    border-radius: 12px;
    border: 1px solid var(--primary-200, #c7d2fe);
    background: linear-gradient(135deg, rgba(99,102,241,0.14), rgba(99,102,241,0.08));
    color: var(--primary-800, #312e81);
    font-weight: 700;
    padding: 10px 12px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.print-action .icon { font-size: 1.05rem; }
.print-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 28px rgba(79,70,229,0.18);
}
.stock-filters input:focus,
.stock-filters select:focus {
    outline: 2px solid var(--primary-200, #c7d2fe);
}
.filter-toggle {
    display: flex;
    gap: 8px;
    margin: 12px 0 18px;
    flex-wrap: wrap;
}
.filter-btn {
    border: 1px solid var(--border-muted, #e5e7eb);
    background: var(--surface-raised, #f9fafb);
    color: var(--text-muted, #374151);
    padding: 8px 14px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.15s ease;
}
.filter-btn:hover {
    background: var(--surface-hover, #eef2ff);
    color: var(--text-primary, #111827);
}
.filter-btn.active {
    background: var(--primary-50, #eef2ff);
    color: var(--primary-700, #4338ca);
    border-color: var(--primary-200, #c7d2fe);
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}
.stock-status {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 14px;
    align-items: center;
    padding: 14px 16px;
    border: 1px solid var(--border, #e5e7eb);
    border-radius: 14px;
    background: var(--surface, #fff);
    box-shadow: var(--shadow-card, 0 10px 30px rgba(0,0,0,0.04));
    margin: 10px 0 6px;
}
.stock-status .status-icon {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(225,29,72,0.16), rgba(225,29,72,0.08));
    display: grid;
    place-items: center;
}
.stock-status .status-copy .status-title {
    font-weight: 700;
    color: var(--text, #111827);
}
.stock-status .status-copy .status-sub {
    color: var(--text-muted, #6b7280);
    font-size: 0.95rem;
}
.stock-status .status-tags {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
}
.stock-status .status-tag {
    padding: 10px 14px;
    border-radius: 12px;
    min-width: 150px;
    text-align: center;
    border: 1px solid rgba(0,0,0,0.06);
}
.stock-status .status-tag span {
    display: block;
    font-size: 0.85rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.stock-status .status-tag strong {
    font-size: 1.1rem;
}
.stock-status .status-tag.negative {
    background: rgba(225,29,72,0.12);
    border-color: rgba(225,29,72,0.28);
    color: #991b1b;
}
.stock-status .status-tag.empty {
    background: rgba(250,204,21,0.14);
    border-color: rgba(250,204,21,0.3);
    color: #854d0e;
}
.stock-status .status-tag.low {
    background: rgba(249,115,22,0.14);
    border-color: rgba(249,115,22,0.32);
    color: #9a3412;
}
.qty-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 92px;
    padding: 6px 12px;
    border-radius: 12px;
    font-weight: 700;
    background: var(--surface-muted, #f3f4f6);
    color: var(--text, #111827);
    border: 1px solid var(--border-muted, #e5e7eb);
}
.qty-pill.qty-ok {
    background: rgba(34,197,94,0.14);
    color: #166534;
    border-color: rgba(34,197,94,0.36);
    box-shadow: inset 0 0 0 1px rgba(22,101,52,0.12);
}
.qty-pill.qty-neg {
    background: rgba(225,29,72,0.12);
    color: #991b1b;
    border-color: rgba(225,29,72,0.28);
}
.qty-pill.qty-zero {
    background: rgba(250,204,21,0.14);
    color: #854d0e;
    border-color: rgba(250,204,21,0.3);
}
.qty-pill.qty-low {
    background: rgba(249,115,22,0.14);
    color: #9a3412;
    border-color: rgba(249,115,22,0.32);
}
body.theme-dark .tab-button {
    background: rgba(30, 41, 59, 0.78);
    border-color: rgba(148, 163, 184, 0.24);
    color: var(--text);
}
body.theme-dark .tab-button:not(.active):hover,
body.theme-dark .filter-btn:hover {
    background: rgba(51, 65, 85, 0.86);
    color: var(--text);
}
body.theme-dark .tab-button.active {
    background: var(--primary);
    color: #fff;
}
body.theme-dark .stock-status,
body.theme-dark .stock-filters input,
body.theme-dark .stock-filters select {
    background: rgba(15, 23, 42, 0.82);
    border-color: rgba(148, 163, 184, 0.24);
    color: var(--text);
    box-shadow: 0 18px 38px -30px rgba(0, 0, 0, 0.85);
}
body.theme-dark .filter-btn,
body.theme-dark .print-action,
body.theme-dark .qty-pill {
    background: rgba(30, 41, 59, 0.78);
    border-color: rgba(148, 163, 184, 0.24);
    color: var(--text);
}
body.theme-dark .filter-btn.active {
    background: rgba(var(--primary-rgb), 0.18);
    border-color: rgba(var(--primary-rgb), 0.38);
    color: #dbeafe;
}
body.theme-dark .print-action {
    color: #dbeafe;
}
@media (max-width: 960px) {
    .stock-status {
        grid-template-columns: 1fr;
    }
    .stock-status .status-tags {
        justify-content: flex-start;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = Array.from(document.querySelectorAll('.tab-button'));
    const tabPanels = Array.from(document.querySelectorAll('.tab-panel'));
    const buttons = Array.from(document.querySelectorAll('.filter-btn'));
    const rows = Array.from(document.querySelectorAll('#estoque-posicao tbody tr[data-type]'));
    const searchInput = document.querySelector('#estoque-search');
    const statusSelect = document.querySelector('#estoque-status');
    const printBtn = document.querySelector('#estoque-print-btn');
    const getActiveTab = () => tabButtons.find(btn => btn.classList.contains('active'))?.dataset.tab || 'estoque';

    const getActiveType = () => buttons.find(btn => btn.classList.contains('active'))?.dataset.type || 'ALL';

    const applyFilters = () => {
        const activeType = getActiveType();
        const term = (searchInput?.value || '').toLowerCase().trim();
        const status = statusSelect?.value || 'ALL';

        rows.forEach(row => {
            const rowType = row.dataset.type || 'ALL';
            const rowStatus = row.dataset.status || 'OK';
            const haystack = row.dataset.search || '';

            const matchesType = activeType === 'ALL' || rowType === activeType;
            const matchesStatus = status === 'ALL' || rowStatus === status;
            const matchesSearch = term === '' || haystack.includes(term);

            row.style.display = matchesType && matchesStatus && matchesSearch ? '' : 'none';
        });
    };

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            buttons.forEach(b => b.classList.toggle('active', b === btn));
            applyFilters();
        });
    });

    searchInput?.addEventListener('input', applyFilters);
    statusSelect?.addEventListener('change', applyFilters);

    printBtn?.addEventListener('click', () => {
        const base = printBtn.dataset.print || '';
        const params = new URLSearchParams();
        const term = (searchInput?.value || '').trim();
        const status = statusSelect?.value || 'ALL';
        const type = getActiveType();

        if (term !== '') params.set('q', term);
        if (status !== 'ALL') params.set('status', status);
        if (type !== 'ALL') params.set('tipo', type);

        const target = base + (params.toString() ? ('?' + params.toString()) : '');
        window.open(target, '_blank', 'noopener');
    });

    if (tabButtons.length && tabPanels.length) {
        const activateTab = (tab) => {
            tabButtons.forEach((btn) => {
                const isActive = btn.dataset.tab === tab;
                btn.classList.toggle('active', isActive);
                btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
            tabPanels.forEach((panel) => {
                panel.classList.toggle('active', panel.dataset.tabPanel === tab);
            });
        };

        tabButtons.forEach((btn) => {
            btn.addEventListener('click', () => activateTab(btn.dataset.tab));
        });

        activateTab(getActiveTab());
    }
});
</script>
