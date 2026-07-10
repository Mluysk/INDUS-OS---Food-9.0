<style>
.panel { background: var(--surface, #fff); border-radius: 14px; padding: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); margin-bottom: 16px; }
.panel-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.panel-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; text-decoration: none; }
.btn.primary { background: var(--primary, #e52528); color: #fff; }
.btn.ghost { background: transparent; border: 1px solid var(--border, #e5e7eb); color: inherit; }
.badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; }
.badge-success { background: rgba(16,185,129,0.18); color: #0f766e; }
.badge-muted { background: rgba(148,163,184,0.18); color: #475569; }
.badge-warning { background: rgba(245,158,11,0.18); color: #92400e; }
.badge-danger { background: rgba(239,68,68,0.14); color: #b91c1c; }
.filter-grid { display: grid; grid-template-columns: minmax(180px, 1fr) minmax(180px, 1fr) minmax(160px, 1fr); gap: 12px; margin-top: 12px; align-items: end; }
.filter-grid label { font-size: 0.82rem; color: var(--muted, #6b7280); display: flex; flex-direction: column; gap: 6px; font-weight: 700; }
.filter-grid input, .filter-grid select { border: 1px solid var(--border, #e5e7eb); border-radius: 10px; padding: 10px 12px; background: var(--surface-alt, #f8fafc); min-height: 46px; font-weight: 700; }
.filter-grid .btn { min-height: 41px; width: 100%; }
.pa-recipe-table-wrap { margin-top: 14px; overflow-x: auto; border: 1px solid rgba(148,163,184,0.32); border-radius: 12px; }
.pa-recipe-table { width: 100%; border-collapse: collapse; min-width: 920px; }
.pa-recipe-table th, .pa-recipe-table td { padding: 12px 14px; border-bottom: 1px solid rgba(148,163,184,0.24); text-align: left; vertical-align: middle; }
.pa-recipe-table th { background: var(--surface-alt, #f8fafc); color: var(--muted, #64748b); font-size: 0.72rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; white-space: nowrap; }
.pa-recipe-table tbody tr:last-child td { border-bottom: 0; }
.pa-recipe-table tbody tr:hover { background: rgba(148,163,184,0.08); }
.pa-recipe-row.is-filtered-out { display: none; }
.pa-recipe-sku { display: inline-flex; border-radius: 999px; padding: 4px 9px; background: rgba(15,23,42,0.08); color: #334155; font-size: 0.75rem; font-weight: 800; white-space: nowrap; }
.pa-recipe-product { min-width: 220px; font-weight: 800; color: var(--text, #0f172a); }
.pa-recipe-detail { min-width: 190px; }
.pa-recipe-detail strong { display: block; color: var(--text, #0f172a); }
.pa-recipe-detail small { display: block; margin-top: 3px; color: var(--muted, #64748b); }
.pa-recipe-actions { display: flex; gap: 6px; flex-wrap: wrap; min-width: 260px; }
.pa-recipe-actions .btn { padding: 7px 10px; font-size: 0.78rem; white-space: nowrap; }
.pa-recipe-icon-btn { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border: 1px solid var(--border, #e5e7eb); border-radius: 9px; background: var(--surface, #fff); color: var(--text, #334155); text-decoration: none; transition: transform .15s ease, border-color .15s ease, color .15s ease; }
.pa-recipe-icon-btn:hover { transform: translateY(-1px); border-color: var(--primary, #e52528); color: var(--primary, #e52528); }
.pa-recipe-icon-btn [data-lucide] { width: 15px; height: 15px; stroke-width: 2.25; }
.pa-recipe-status-cell { text-align: center !important; vertical-align: middle !important; }
.pa-recipe-status { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 50%; cursor: help; }
.pa-recipe-status::after { content: attr(data-tooltip); position: absolute; left: 50%; bottom: calc(100% + 8px); z-index: 20; transform: translate(-50%, 4px); padding: 6px 9px; border-radius: 7px; background: #111827; color: #fff; font-size: 0.72rem; font-weight: 700; line-height: 1; white-space: nowrap; opacity: 0; pointer-events: none; transition: opacity .15s ease, transform .15s ease; }
.pa-recipe-status:hover::after, .pa-recipe-status:focus-visible::after { opacity: 1; transform: translate(-50%, 0); }
.pa-recipe-status--active { background: #22c55e; box-shadow: 0 0 0 4px rgba(34,197,94,.14); }
.pa-recipe-status--inactive { background: #f97316; box-shadow: 0 0 0 4px rgba(249,115,22,.14); }
.pa-recipe-status--review { background: #eab308; box-shadow: 0 0 0 4px rgba(234,179,8,.16); }
.pa-recipe-status--none { background: #ef4444; color: #fff; box-shadow: 0 0 0 4px rgba(239,68,68,.16); font-size: 0.8rem; font-weight: 900; }
.pa-recipe-empty { display: none; margin: 18px 0 0; padding: 18px; border: 1px dashed var(--border, #dbe3ef); border-radius: 12px; color: var(--muted, #64748b); text-align: center; font-weight: 700; }
.pa-recipe-empty.is-visible { display: block; }
@media (max-width: 780px) { .filter-grid { grid-template-columns: 1fr; } }
</style>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2>Receitas (BOM)</h2>
            <p class="panel-description">Cadastre e execute receitas com baixa automatica de estoque.</p>
        </div>
        <div class="panel-actions">
            <a class="btn primary" href="<?= htmlspecialchars($url('receitas/nova'), ENT_QUOTES, 'UTF-8'); ?>">Nova receita</a>
        </div>
    </div>

    <form class="filter-grid" id="bom-filter-form" method="get" action="<?= htmlspecialchars($url('receitas'), ENT_QUOTES, 'UTF-8'); ?>">
        <label>
            Buscar
            <input type="text" id="bom-search" name="q" value="<?= htmlspecialchars($filters['term'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nome ou SKU" autocomplete="off">
        </label>
        <label>
            Status
            <select id="bom-status" name="active">
                <option value="">Todos</option>
                <option value="1" <?= ($filters['active'] ?? '') === '1' ? 'selected' : ''; ?>>Ativo</option>
                <option value="0" <?= ($filters['active'] ?? '') === '0' ? 'selected' : ''; ?>>Inativo</option>
                <option value="review" <?= ($filters['active'] ?? '') === 'review' ? 'selected' : ''; ?>>Revisao</option>
                <option value="none" <?= ($filters['active'] ?? '') === 'none' ? 'selected' : ''; ?>>Sem receita</option>
            </select>
        </label>
        <label>
            <span>&nbsp;</span>
            <button class="btn primary" type="submit">Filtrar</button>
        </label>
    </form>

    <div class="pa-recipe-table-wrap">
        <table class="pa-recipe-table" id="bom-recipe-table">
            <thead>
                <tr>
                    <th>SKU P.A.</th>
                    <th>Produto acabado</th>
                    <th>Receita vinculada</th>
                    <th>Rendimento</th>
                    <th class="pa-recipe-status-cell">Status</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody id="bom-recipe-list">
                <?php if (!empty($paRecipeRows)): ?>
                    <?php foreach ($paRecipeRows as $row): ?>
                        <?php
                            $product = $row['product'] ?? [];
                            $recipe = $row['recipe'] ?? null;
                            $status = (string)($row['status'] ?? 'none');
                            $statusClass = [
                                'active' => 'pa-recipe-status--active',
                                'review' => 'pa-recipe-status--review',
                                'inactive' => 'pa-recipe-status--inactive',
                                'none' => 'pa-recipe-status--none',
                            ][$status] ?? 'pa-recipe-status--inactive';
                            $statusLabel = [
                                'active' => 'Ativo',
                                'inactive' => 'Inativo',
                                'review' => 'Revisão',
                                'none' => 'Sem receita',
                            ][$status] ?? 'Status';
                            $searchText = trim(implode(' ', [
                                (string)($product['sku'] ?? ''),
                                (string)($product['nome'] ?? ''),
                                (string)($row['status_label'] ?? ''),
                                $recipe ? (string)($recipe['sku'] ?? '') : '',
                                $recipe ? (string)($recipe['name'] ?? '') : '',
                            ]));
                            $searchText = function_exists('mb_strtolower') ? mb_strtolower($searchText, 'UTF-8') : strtolower($searchText);
                        ?>
                        <tr class="pa-recipe-row" data-recipe-row data-status="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>" data-search="<?= htmlspecialchars($searchText, ENT_QUOTES, 'UTF-8'); ?>">
                            <td><span class="pa-recipe-sku"><?= htmlspecialchars((string)($product['sku'] ?? 'SEM-SKU'), ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td class="pa-recipe-product"><?= htmlspecialchars((string)($product['nome'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="pa-recipe-detail">
                                <?php if ($recipe): ?>
                                    <strong><?= htmlspecialchars((string)($recipe['name'] ?? ('Receita #' . (int)($recipe['id'] ?? 0))), ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small><?= htmlspecialchars((string)($recipe['sku'] ?? ('#' . (int)($recipe['id'] ?? 0))), ENT_QUOTES, 'UTF-8'); ?></small>
                                <?php else: ?>
                                    <span class="panel-description">Sem receita vinculada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($recipe): ?>
                                    <?= number_format((float)($recipe['yield_qty'] ?? 0), 3, ',', '.'); ?>
                                    <?= htmlspecialchars((string)($recipe['yield_unit'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                <?php else: ?>
                                    &mdash;
                                <?php endif; ?>
                            </td>
                            <td class="pa-recipe-status-cell"><span class="pa-recipe-status <?= $statusClass; ?>" data-tooltip="<?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?>" title="<?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?>" tabindex="0"><?= $status === 'none' ? '&times;' : ''; ?></span></td>
                            <td>
                                <div class="pa-recipe-actions">
                                    <?php if ($recipe): ?>
                                        <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id']), ENT_QUOTES, 'UTF-8'); ?>">Detalhar</a>
                                        <a class="pa-recipe-icon-btn" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id'] . '/editar'), ENT_QUOTES, 'UTF-8'); ?>" data-tooltip="Editar" title="Editar" aria-label="Editar receita"><span data-lucide="pencil" aria-hidden="true"></span></a>
                                        <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id'] . '/executar'), ENT_QUOTES, 'UTF-8'); ?>">Executar</a>

                                    <?php else: ?>
                                        <a class="btn primary" href="<?= htmlspecialchars($url('receitas/nova?produto_pa_id=' . (int)($product['id'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">Criar receita</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($paRecipeRows)): ?>
        <p class="panel-description">Nenhum produto P.A encontrado.</p>
    <?php endif; ?>
    <div class="pa-recipe-empty" id="bom-empty" data-recipe-empty>Nenhum produto encontrado com esse filtro.</div>
</section>

<script>
(() => {
    const bootBomFilters = () => {
        const form = document.getElementById('bom-filter-form');
        if (!form || form.dataset.filterReady === '1') return;
        form.dataset.filterReady = '1';

        const search = document.getElementById('bom-search');
        const status = document.getElementById('bom-status');
        const list = document.getElementById('bom-recipe-list');
        const empty = document.getElementById('bom-empty');
        if (!list) return;

        const getRows = () => Array.from(list.querySelectorAll('[data-recipe-row]'));
        const normalize = (value) => String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim()
            .toLocaleLowerCase('pt-BR');
        const selectedStatus = () => {
            const value = String(status?.value || '');
            if (value === '1') return 'active';
            if (value === '0') return 'inactive';
            return value;
        };

        const applyFilters = () => {
            const rows = getRows();
            const term = normalize(search?.value);
            const wantedStatus = selectedStatus();
            let visible = 0;

            rows.forEach((row) => {
                const rowStatus = String(row.dataset.status || '');
                const haystack = normalize(row.dataset.search || row.textContent || '');
                const matchesTerm = !term || haystack.includes(term);
                const matchesStatus = !wantedStatus || rowStatus === wantedStatus;
                const show = matchesTerm && matchesStatus;
                row.classList.toggle('is-filtered-out', !show);
                row.setAttribute('aria-hidden', show ? 'false' : 'true');
                if (show) visible += 1;
            });

            if (empty) {
                empty.classList.toggle('is-visible', rows.length > 0 && visible === 0);
            }
        };

        search?.addEventListener('input', applyFilters);
        status?.addEventListener('change', applyFilters);
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            applyFilters();
        });
        applyFilters();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootBomFilters, { once: true });
    } else {
        bootBomFilters();
    }
    document.addEventListener('indus:content-updated', bootBomFilters);
})();
</script>
