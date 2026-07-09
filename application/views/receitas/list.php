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
.pa-recipe-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px; margin-top: 14px; }
.pa-recipe-card { border: 1px solid rgba(148,163,184,0.32); border-radius: 12px; padding: 14px; background: rgba(248,250,252,0.76); display: flex; flex-direction: column; gap: 12px; min-height: 176px; }
.pa-recipe-card__head { display: flex; justify-content: space-between; gap: 10px; align-items: flex-start; }
.pa-recipe-card__sku { display: inline-flex; width: max-content; border-radius: 999px; padding: 3px 9px; background: rgba(15,23,42,0.08); font-size: 0.72rem; font-weight: 800; color: #334155; margin-bottom: 6px; }
.pa-recipe-card__name { margin: 0; font-size: 0.96rem; line-height: 1.25; color: #0f172a; }
.pa-recipe-card__meta { margin: 0; color: var(--muted, #64748b); font-size: 0.82rem; line-height: 1.35; }
.pa-recipe-card__actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: auto; }
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

    <div class="pa-recipe-grid" id="bom-card-grid">
        <?php if (!empty($paRecipeRows)): ?>
            <?php foreach ($paRecipeRows as $row): ?>
                <?php
                    $product = $row['product'] ?? [];
                    $recipe = $row['recipe'] ?? null;
                    $status = (string)($row['status'] ?? 'none');
                    $badgeClass = [
                        'active' => 'badge-success',
                        'review' => 'badge-warning',
                        'inactive' => 'badge-muted',
                        'none' => 'badge-danger',
                    ][$status] ?? 'badge-muted';
                ?>
                <?php
                    $searchText = trim(implode(' ', [
                        (string)($product['sku'] ?? ''),
                        (string)($product['nome'] ?? ''),
                        (string)($row['status_label'] ?? ''),
                        $recipe ? (string)($recipe['sku'] ?? '') : '',
                        $recipe ? (string)($recipe['name'] ?? '') : '',
                    ]));
                    $searchText = function_exists('mb_strtolower') ? mb_strtolower($searchText, 'UTF-8') : strtolower($searchText);
                ?>
                <article class="pa-recipe-card" data-recipe-card data-status="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>" data-search="<?= htmlspecialchars($searchText, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="pa-recipe-card__head">
                        <div>
                            <span class="pa-recipe-card__sku"><?= htmlspecialchars((string)($product['sku'] ?? 'SEM-SKU'), ENT_QUOTES, 'UTF-8'); ?></span>
                            <h4 class="pa-recipe-card__name"><?= htmlspecialchars((string)($product['nome'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h4>
                        </div>
                        <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars((string)($row['status_label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <?php if ($recipe): ?>
                        <p class="pa-recipe-card__meta">
                            Receita #<?= (int)($recipe['id'] ?? 0); ?> - rendimento
                            <?= number_format((float)($recipe['yield_qty'] ?? 0), 3, ',', '.'); ?>
                            <?= htmlspecialchars((string)($recipe['yield_unit'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <div class="pa-recipe-card__actions">
                            <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id']), ENT_QUOTES, 'UTF-8'); ?>">Detalhar</a>
                            <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id'] . '/editar'), ENT_QUOTES, 'UTF-8'); ?>">Editar</a>
                        </div>
                    <?php else: ?>
                        <p class="pa-recipe-card__meta">Sem receita vinculada para baixa automatica de MP.</p>
                        <div class="pa-recipe-card__actions">
                            <a class="btn primary" href="<?= htmlspecialchars($url('receitas/nova?produto_pa_id=' . (int)($product['id'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">Criar nova receita</a>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="panel-description">Nenhum produto P.A encontrado.</p>
        <?php endif; ?>
    </div>
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
        const grid = document.getElementById('bom-card-grid');
        const empty = document.getElementById('bom-empty');
        if (!grid) return;

        const getCards = () => Array.from(grid.querySelectorAll('[data-recipe-card]'));
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
            const cards = getCards();
            const term = normalize(search?.value);
            const wantedStatus = selectedStatus();
            let visible = 0;

            cards.forEach((card) => {
                const cardStatus = String(card.dataset.status || '');
                const haystack = normalize(card.dataset.search || card.textContent || '');
                const matchesTerm = !term || haystack.includes(term);
                const matchesStatus = !wantedStatus || cardStatus === wantedStatus;
                const show = matchesTerm && matchesStatus;
                card.hidden = !show;
                if (show) visible += 1;
            });

            if (empty) {
                empty.classList.toggle('is-visible', cards.length > 0 && visible === 0);
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
