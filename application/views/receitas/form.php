<?php
$isEdit = !empty($recipe);
$action = $isEdit ? $url('receitas/' . (int)$recipe['id'] . '/update') : $url('receitas');
$items = $items ?? [];
$products = $products ?? [];
$paProducts = $paProducts ?? [];
$units = $units ?? ['kg', 'g', 'l', 'ml', 'un'];
$nextSku = $nextSku ?? '';
$skuValue = trim((string)($recipe['sku'] ?? ''));
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
                            <?= (int)($recipe['produto_pa_id'] ?? 0) === $paId ? 'selected' : ''; ?>>
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
                <input type="number" step="0.001" min="0" name="yield_qty" value="<?= htmlspecialchars((string)($recipe['yield_qty'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
            </label>
            <label>
                Unidade do rendimento
                <select name="yield_unit" required>
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
            <div class="table-wrap">
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
                                        <select name="item_product_id[]" required>
                                            <option value="">Selecione</option>
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?= (int)$product['id']; ?>" <?= (int)$item['stock_product_id'] === (int)$product['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($product['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
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

    const productOptions = `<?= implode('', array_map(static function ($product) {
        $id = (int)($product['id'] ?? 0);
        $name = htmlspecialchars($product['nome'] ?? '', ENT_QUOTES, 'UTF-8');
        return "<option value=\"{$id}\">{$name}</option>";
    }, $products)); ?>`;

    const unitOptions = `<?= implode('', array_map(static function ($unit) {
        $label = htmlspecialchars($unit, ENT_QUOTES, 'UTF-8');
        return "<option value=\"{$label}\">{$label}</option>";
    }, $units)); ?>`;

    const buildRow = () => {
        return `
            <tr>
                <td>
                    <select name="item_product_id[]" required>
                        <option value="">Selecione</option>
                        ${productOptions}
                    </select>
                </td>
                <td><input type="number" step="0.001" min="0" name="item_qty[]" required></td>
                <td>
                    <select name="item_unit[]" required>
                        ${unitOptions}
                    </select>
                </td>
                <td><input type="number" step="0.01" min="0" name="item_loss[]" value="0"></td>
                <td><button type="button" class="btn ghost js-remove">Remover</button></td>
            </tr>
        `;
    };

    const tbody = table.querySelector('tbody');

    addBtn.addEventListener('click', () => {
        const emptyRow = tbody.querySelector('tr td[colspan="5"]');
        if (emptyRow) {
            emptyRow.closest('tr').remove();
        }
        tbody.insertAdjacentHTML('beforeend', buildRow());
    });

    table.addEventListener('click', (event) => {
        const btn = event.target.closest('.js-remove');
        if (!btn) return;
        event.preventDefault();
        const row = btn.closest('tr');
        row?.remove();
        if (tbody.querySelectorAll('tr').length === 0) {
            tbody.innerHTML = '<tr><td colspan="5">Adicione os ingredientes da receita.</td></tr>';
        }
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
