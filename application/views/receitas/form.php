<?php
$isEdit = !empty($recipe);
$action = $isEdit ? $url('receitas/' . (int)$recipe['id'] . '/update') : $url('receitas');
$items = $items ?? [];
$products = $products ?? [];
$paProducts = $paProducts ?? [];
$units = $units ?? ['kg', 'g', 'l', 'ml', 'un'];
$nextSku = $nextSku ?? '';
$skuValue = trim((string)($recipe['sku'] ?? ''));
$selectedPaId = (int)($selectedPaId ?? 0);
$productsById = [];
foreach ($products as $product) {
    $productsById[(int)($product['id'] ?? 0)] = $product;
}
?>
<style>
.panel { background: var(--surface, #fff); border-radius: 14px; padding: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); margin-bottom: 16px; }
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
.form-grid label { display: flex; flex-direction: column; gap: 6px; font-size: 0.85rem; color: var(--muted, #6b7280); }
.form-grid input, .form-grid select { border: 1px solid var(--border, #e5e7eb); border-radius: 10px; padding: 8px 10px; background: var(--surface-alt, #f8fafc); }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; text-decoration: none; }
.btn.primary { background: var(--primary, #4b5563); color: #fff; }
.btn.ghost { background: transparent; border: 1px solid var(--border, #e5e7eb); color: inherit; }
.table-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 0.9rem; }
.data-table th, .data-table td { border: 1px solid rgba(148,163,184,0.4); padding: 8px 10px; text-align: left; }
.data-table th { text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.08em; background: rgba(15,23,42,0.08); }
.recipe-product-picker { position: relative; display: block; width: 100%; min-width: 0; }
.recipe-items-table-wrap { overflow: visible; }
.data-table th:first-child, .data-table td:first-child { width: 42%; min-width: 380px; }
@media (max-width: 780px) { .data-table th:first-child, .data-table td:first-child { min-width: 300px; } }
.recipe-product-search { width: 100%; min-height: 46px; font-weight: 700; border: 1px solid var(--border, #e5e7eb); border-radius: 10px; padding: 8px 10px; background: var(--surface-alt, #f8fafc); color: inherit; }
.recipe-product-search:focus { outline: 0 !important; border-color: var(--primary, #dc2626); box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 220,38,38),.14); }
.recipe-product-suggestions { position: absolute; z-index: 35; left: 0; right: 0; top: calc(100% + 6px); max-height: 379px; overflow: auto; margin: 0; padding: .35rem; list-style: none; border: 1px solid var(--border-subtle, #e2e8f0); border-radius: 12px; background: var(--surface-elevated, #fff); box-shadow: 0 18px 38px rgba(15,23,42,.14); }
.recipe-product-suggestions[hidden] { display: none !important; }
.recipe-product-suggestions button { width: 100%; display: block; border: 0; border-radius: 9px; padding: .62rem .75rem; background: transparent; color: var(--text-strong, #0f172a); text-align: left; cursor: pointer; font-weight: 800; }
.recipe-product-suggestions button:hover, .recipe-product-suggestions button:focus, .recipe-product-suggestions button.is-active { outline: none; background: var(--surface-muted, #f1f5f9); }
.recipe-product-suggestions strong { display: block; margin-top: .15rem; color: var(--text-muted, #64748b); font-size: .78rem; font-weight: 650; }
.recipe-yield-highlight { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin: 12px 0 14px; padding: 12px 14px; border-left: 4px solid var(--primary, #dc2626); border-radius: 10px; background: rgba(var(--primary-rgb, 220,38,38),.08); }
.recipe-yield-highlight span { color: var(--muted, #64748b); font-size: .78rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
.recipe-yield-highlight strong { color: var(--primary, #dc2626); font-size: 1.05rem; }
.recipe-weight-summary { margin-top: 14px; padding: 18px; border: 2px solid rgba(var(--primary-rgb, 220,38,38),.28); border-radius: 16px; background: linear-gradient(135deg, rgba(var(--primary-rgb, 220,38,38),.11), var(--surface, #fff) 72%); box-shadow: 0 12px 28px rgba(15,23,42,.08); }
.recipe-weight-summary__header { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding-bottom: 14px; border-bottom: 1px solid rgba(148,163,184,.25); }
.recipe-weight-summary__title { display: flex; flex-direction: column; gap: 3px; color: var(--text-strong, #0f172a); }
.recipe-weight-summary__title span { color: var(--muted, #64748b); font-size: .75rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
.recipe-weight-summary__total { color: var(--primary, #dc2626); font-size: 1.65rem; font-weight: 900; white-space: nowrap; }
.recipe-weight-summary__grid { display: grid; grid-template-columns: repeat(4, minmax(120px,1fr)); gap: 10px; margin-top: 14px; }
.recipe-weight-metric { padding: 10px 12px; border: 1px solid rgba(148,163,184,.22); border-radius: 11px; background: rgba(255,255,255,.68); }
.recipe-weight-metric span { display: block; color: var(--muted, #64748b); font-size: .72rem; font-weight: 700; }
.recipe-weight-metric strong { display: block; margin-top: 4px; color: var(--text-strong, #0f172a); font-size: .95rem; }
.recipe-weight-summary__note { margin: 10px 0 0; color: var(--muted, #64748b); font-size: .76rem; }
@media (max-width: 780px) { .recipe-weight-summary__header { align-items: flex-start; flex-direction: column; } .recipe-weight-summary__grid { grid-template-columns: repeat(2,minmax(120px,1fr)); } }
.item-actions { display: flex; gap: 8px; align-items: center; justify-content: flex-end; }
</style>

<section class="panel">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
        <div>
            <h2><?= $isEdit ? 'Editar receita' : 'Nova receita'; ?></h2>
            <p class="panel-description">Informe rendimento e ingredientes consumidos para cada execução.</p>
        </div>
        <div style="display:flex; gap:8px;">
            <a class="btn ghost" href="<?= htmlspecialchars($url('receitas'), ENT_QUOTES, 'UTF-8'); ?>">Voltar</a>
        </div>
    </div>

    <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-grid" style="margin-top: 14px;">
            <label>
                Produto P.A associado
                <select name="produto_pa_id" id="produto_pa_id" required>
                    <option value="">Selecione um produto P.A</option>
                    <?php foreach ($paProducts as $pa): ?>
                        <?php $paId = (int)($pa['id'] ?? 0); ?>
                        <option value="<?= $paId; ?>"
                            data-sku="<?= htmlspecialchars((string)($pa['sku'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                            data-nome="<?= htmlspecialchars((string)($pa['nome'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                            <?= ((int)($recipe['produto_pa_id'] ?? 0) === $paId || (!$isEdit && $selectedPaId === $paId)) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars(((string)($pa['sku'] ?? 'SEM-SKU')).' — '.((string)($pa['nome'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small id="produto-pa-status" class="muted"></small>
                <button type="button" class="btn ghost" id="trocar-produto-pa" style="display:none; margin-top:6px; width:max-content;">Trocar produto</button>
            </label>
            <label>
                SKU <small class="muted">Gerado automaticamente</small>
                <input type="text" name="sku" value="<?= htmlspecialchars($skuValue, ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </label>
            <label>
                Rendimento
                <input type="number" step="0.001" min="0" id="recipe-yield-qty" name="yield_qty" value="<?= htmlspecialchars((string)($recipe['yield_qty'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
            </label>
            <label>
                Unidade do rendimento
                <select id="recipe-yield-unit" name="yield_unit" required>
                    <?php foreach ($units as $unit): ?>
                        <option value="<?= htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?>" <?= ($recipe['yield_unit'] ?? '') === $unit ? 'selected' : ''; ?>><?= htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Status
                <select name="active">
                    <option value="1" <?= (int)($recipe['active'] ?? 1) === 1 ? 'selected' : ''; ?>>Ativa</option>
                    <option value="0" <?= (int)($recipe['active'] ?? 1) === 0 ? 'selected' : ''; ?>>Inativa</option>
                </select>
            </label>
        </div>

        <div class="panel" style="margin-top: 16px;">
            <h3>Ingredientes</h3>
            <p class="panel-description">Quantidades por lote de rendimento informado.</p>
            <div class="recipe-yield-highlight">
                <span>Rendimento da receita</span>
                <strong id="ingredients-yield-display"><?= number_format((float)($recipe['yield_qty'] ?? 0), 3, ',', '.'); ?> <?= htmlspecialchars((string)($recipe['yield_unit'] ?? ($units[0] ?? '')), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="table-wrap recipe-items-table-wrap">
                <table class="data-table" id="items-table">
                    <thead>
                        <tr>
                            <th>Ingrediente</th>
                            <th>Quantidade</th>
                            <th>Unidade</th>
                            <th>Perda (%)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <?php
                                            $selectedIngredient = $productsById[(int)($item['stock_product_id'] ?? 0)] ?? null;
                                            $selectedIngredientLabel = $selectedIngredient
                                                ? trim((string)($selectedIngredient['sku'] ?? '') . ' — ' . (string)($selectedIngredient['nome'] ?? ''))
                                                : '';
                                        ?>
                                        <div class="recipe-product-picker" data-recipe-product-picker>
                                            <input class="recipe-product-search" type="text" value="<?= htmlspecialchars($selectedIngredientLabel, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Produto..." autocomplete="off" aria-autocomplete="list" required data-recipe-product-search>
                                            <input type="hidden" name="item_product_id[]" value="<?= (int)($item['stock_product_id'] ?? 0); ?>" data-recipe-product-id>
                                            <ul class="recipe-product-suggestions" hidden data-recipe-product-suggestions></ul>
                                        </div>
                                    </td>
                                    <td><input type="number" step="0.001" min="0" name="item_qty[]" value="<?= htmlspecialchars((string)($item['qty'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required></td>
                                    <td>
                                        <select name="item_unit[]" required>
                                            <?php foreach ($units as $unit): ?>
                                                <option value="<?= htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?>" <?= ($item['unit'] ?? '') === $unit ? 'selected' : ''; ?>><?= htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" min="0" name="item_loss[]" value="<?= htmlspecialchars((string)($item['loss_percent'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"></td>
                                    <td><button type="button" class="btn ghost js-remove">Remover</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">Adicione os ingredientes da receita.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="recipe-weight-summary" aria-live="polite">
                <div class="recipe-weight-summary__header">
                    <div class="recipe-weight-summary__title">
                        <span>Resumo dos ingredientes</span>
                        <strong>Peso total utilizado</strong>
                    </div>
                    <strong class="recipe-weight-summary__total" id="recipe-total-weight">0,000 kg</strong>
                </div>
                <div class="recipe-weight-summary__grid">
                    <div class="recipe-weight-metric"><span>Peso base</span><strong id="recipe-base-weight">0,000 kg</strong></div>
                    <div class="recipe-weight-metric"><span>Acréscimo por perdas</span><strong id="recipe-loss-weight">+ 0,000 kg</strong></div>
                    <div class="recipe-weight-metric"><span>Equivalente</span><strong id="recipe-weight-grams">0 g</strong></div>
                    <div class="recipe-weight-metric"><span>Ingredientes pesados</span><strong id="recipe-weighted-count">0</strong></div>
                </div>
                <p class="recipe-weight-summary__note" id="recipe-weight-note">Somente ingredientes em kg e g entram no peso total.</p>
            </div>
            <div class="item-actions" style="margin-top: 10px;">
                <button type="button" class="btn ghost" id="add-item">Adicionar ingrediente</button>
            </div>
        </div>

        <div style="margin-top: 16px; display:flex; gap:8px;">
            <button class="btn primary" type="submit">Salvar receita</button>
            <a class="btn ghost" href="<?= htmlspecialchars($url('receitas'), ENT_QUOTES, 'UTF-8'); ?>">Cancelar</a>
        </div>
    </form>
</section>

<script>
(() => {
    const table = document.getElementById('items-table');
    const addBtn = document.getElementById('add-item');
    if (!table || !addBtn) return;

    const yieldQtyInput = document.getElementById('recipe-yield-qty');
    const yieldUnitInput = document.getElementById('recipe-yield-unit');
    const yieldDisplay = document.getElementById('ingredients-yield-display');
    const updateYieldDisplay = () => {
        const quantity = Number(yieldQtyInput?.value) || 0;
        const unit = String(yieldUnitInput?.value || '');
        if (yieldDisplay) yieldDisplay.textContent = `${quantity.toLocaleString('pt-BR', { minimumFractionDigits: 3, maximumFractionDigits: 3 })} ${unit}`;
    };
    yieldQtyInput?.addEventListener('input', updateYieldDisplay);
    yieldUnitInput?.addEventListener('change', updateYieldDisplay);
    updateYieldDisplay();

    const products = <?= json_encode(array_values(array_map(static function ($product) {
        return ['id' => (int)($product['id'] ?? 0), 'sku' => (string)($product['sku'] ?? ''), 'nome' => (string)($product['nome'] ?? '')];
    }, $products)), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const unitOptions = `<?= implode('', array_map(static function ($unit) {
        $label = htmlspecialchars($unit, ENT_QUOTES, 'UTF-8');
        return "<option value=\"{$label}\">{$label}</option>";
    }, $units)); ?>`;
    const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('pt-BR').trim();
    const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    const productLabel = (product) => `${product.sku || 'SEM-SKU'} — ${product.nome || ''}`;

    const bindPicker = (picker) => {
        if (!picker || picker.dataset.pickerReady === '1') return;
        picker.dataset.pickerReady = '1';
        const search = picker.querySelector('[data-recipe-product-search]');
        const productId = picker.querySelector('[data-recipe-product-id]');
        const list = picker.querySelector('[data-recipe-product-suggestions]');
        let matches = [];
        let activeIndex = -1;
        const close = () => { list.hidden = true; activeIndex = -1; };
        const choose = (product) => { search.value = productLabel(product); productId.value = String(product.id); search.setCustomValidity(''); close(); };
        const render = () => {
            const term = normalize(search.value);
            matches = products.filter((product) => !term || normalize(`${product.sku} ${product.nome}`).includes(term)).slice(0, 12);
            list.innerHTML = matches.length ? matches.map((product, index) => `<li><button type="button" data-product-index="${index}"><strong>${escapeHtml(product.sku || 'SEM-SKU')}</strong><span>${escapeHtml(product.nome)}</span></button></li>`).join('') : '<li><span class="panel-description">Nenhum produto encontrado.</span></li>';
            list.hidden = false;
        };
        const highlight = () => list.querySelectorAll('button').forEach((button, index) => button.classList.toggle('is-active', index === activeIndex));
        search.addEventListener('focus', render);
        search.addEventListener('input', () => { productId.value = ''; search.setCustomValidity('Selecione um produto da lista.'); render(); });
        search.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                if (list.hidden) render();
                activeIndex = event.key === 'ArrowDown' ? Math.min(activeIndex + 1, matches.length - 1) : Math.max(activeIndex - 1, 0);
                highlight();
            } else if (event.key === 'Enter' && matches[activeIndex]) {
                event.preventDefault(); choose(matches[activeIndex]);
            } else if (event.key === 'Escape') close();
        });
        list.addEventListener('mousedown', (event) => {
            const button = event.target.closest('[data-product-index]');
            if (!button) return;
            event.preventDefault();
            const product = matches[Number(button.dataset.productIndex)];
            if (product) choose(product);
        });
        document.addEventListener('click', (event) => { if (!picker.contains(event.target)) close(); });
    };

    const buildRow = () => `<tr><td><div class="recipe-product-picker" data-recipe-product-picker><input class="recipe-product-search" type="text" placeholder="Produto..." autocomplete="off" aria-autocomplete="list" required data-recipe-product-search><input type="hidden" name="item_product_id[]" value="" data-recipe-product-id><ul class="recipe-product-suggestions" hidden data-recipe-product-suggestions></ul></div></td><td><input type="number" step="0.001" min="0" name="item_qty[]" required></td><td><select name="item_unit[]" required>${unitOptions}</select></td><td><input type="number" step="0.01" min="0" name="item_loss[]" value="0"></td><td><button type="button" class="btn ghost js-remove">Remover</button></td></tr>`;
    const tbody = table.querySelector('tbody');
    const totalWeight = document.getElementById('recipe-total-weight');
    const baseWeight = document.getElementById('recipe-base-weight');
    const lossWeight = document.getElementById('recipe-loss-weight');
    const weightGrams = document.getElementById('recipe-weight-grams');
    const weightedCount = document.getElementById('recipe-weighted-count');
    const weightNote = document.getElementById('recipe-weight-note');
    const formatKg = (value) => `${value.toLocaleString('pt-BR', { minimumFractionDigits: 3, maximumFractionDigits: 3 })} kg`;
    const updateTotalWeight = () => {
        let baseKg = 0;
        let lossKg = 0;
        let counted = 0;
        let ignored = 0;
        tbody.querySelectorAll('tr').forEach((row) => {
            const quantity = Number(row.querySelector('[name="item_qty[]"]')?.value) || 0;
            const unit = String(row.querySelector('[name="item_unit[]"]')?.value || '').trim().toLowerCase();
            const loss = Math.max(0, Number(row.querySelector('[name="item_loss[]"]')?.value) || 0);
            if (quantity <= 0) return;
            let quantityKg = 0;
            if (unit === 'kg') quantityKg = quantity;
            else if (unit === 'g') quantityKg = quantity / 1000;
            else { ignored += 1; return; }
            baseKg += quantityKg;
            lossKg += quantityKg * (loss / 100);
            counted += 1;
        });
        const totalKg = baseKg + lossKg;
        if (totalWeight) totalWeight.textContent = formatKg(totalKg);
        if (baseWeight) baseWeight.textContent = formatKg(baseKg);
        if (lossWeight) lossWeight.textContent = `+ ${formatKg(lossKg)}`;
        if (weightGrams) weightGrams.textContent = `${Math.round(totalKg * 1000).toLocaleString('pt-BR')} g`;
        if (weightedCount) weightedCount.textContent = String(counted);
        if (weightNote) weightNote.textContent = ignored > 0 ? `${ignored} ingrediente(s) em un/l/ml não entram no peso total.` : 'Todos os ingredientes com quantidade foram contabilizados em kg/g.';
    };
    tbody.querySelectorAll('[data-recipe-product-picker]').forEach(bindPicker);
    table.addEventListener('input', updateTotalWeight);
    table.addEventListener('change', updateTotalWeight);
    addBtn.addEventListener('click', () => {
        tbody.querySelector('tr td[colspan="5"]')?.closest('tr')?.remove();
        tbody.insertAdjacentHTML('beforeend', buildRow());
        bindPicker(tbody.lastElementChild?.querySelector('[data-recipe-product-picker]'));
        updateTotalWeight();
    });
    table.addEventListener('click', (event) => {
        const button = event.target.closest('.js-remove');
        if (!button) return;
        button.closest('tr')?.remove();
        if (!tbody.querySelector('tr')) tbody.innerHTML = '<tr><td colspan="5">Adicione os ingredientes da receita.</td></tr>';
        updateTotalWeight();
    });
    updateTotalWeight();
    table.closest('form')?.addEventListener('submit', (event) => {
        table.querySelectorAll('[data-recipe-product-picker]').forEach((picker) => {
            const search = picker.querySelector('[data-recipe-product-search]');
            const valid = Boolean(picker.querySelector('[data-recipe-product-id]').value);
            search.setCustomValidity(valid ? '' : 'Selecione um produto da lista.');
            if (!valid) event.preventDefault();
        });
    });
})();


(() => {
    const selectPa = document.getElementById('produto_pa_id');
    const skuInput = document.querySelector('input[name="sku"]');
    const status = document.getElementById('produto-pa-status');
    const trocarBtn = document.getElementById('trocar-produto-pa');
    if (!selectPa) return;

    const updateProdutoPAState = (lockWhenSelected = true) => {
        const option = selectPa.options[selectPa.selectedIndex];
        const hasProduct = !!(option && option.value);

        if (skuInput) {
            const sku = hasProduct ? String(option.dataset.sku || '').trim() : '';
            skuInput.value = sku;
        }

        if (status) {
            status.textContent = hasProduct
                ? `Produto associado: ${option.dataset.sku || 'SEM-SKU'} — ${option.dataset.nome || ''}`
                : 'Selecione um produto P.A para vincular a receita.';
        }

        if (lockWhenSelected && hasProduct) {
            selectPa.setAttribute('disabled', 'disabled');
            if (trocarBtn) trocarBtn.style.display = 'inline-flex';
        } else {
            selectPa.removeAttribute('disabled');
            if (trocarBtn) trocarBtn.style.display = 'none';
        }
    };

    selectPa.addEventListener('change', () => updateProdutoPAState(true));

    if (trocarBtn) {
        trocarBtn.addEventListener('click', () => {
            selectPa.removeAttribute('disabled');
            trocarBtn.style.display = 'none';
            if (status) status.textContent = 'Seleção liberada. Escolha outro produto P.A.';
            selectPa.focus();
        });
    }

    // Ensure disabled select still submits
    const form = selectPa.closest('form');
    if (form) {
        form.addEventListener('submit', () => {
            selectPa.removeAttribute('disabled');
        });
    }

    updateProdutoPAState(true);
})();

</script>
