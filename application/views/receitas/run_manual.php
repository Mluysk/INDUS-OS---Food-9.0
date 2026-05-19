<?php
$recipe = $recipe ?? [];
?>
<style>
.panel { background: var(--surface, #fff); border-radius: 14px; padding: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); margin-bottom: 16px; }
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
.form-grid label { display: flex; flex-direction: column; gap: 6px; font-size: 0.85rem; color: var(--muted, #6b7280); }
.form-grid input { border: 1px solid var(--border, #e5e7eb); border-radius: 10px; padding: 8px 10px; background: var(--surface-alt, #f8fafc); }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; text-decoration: none; }
.btn.primary { background: var(--primary, #4b5563); color: #fff; }
.btn.ghost { background: transparent; border: 1px solid var(--border, #e5e7eb); color: inherit; }
</style>

<section class="panel">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
        <div>
            <h2>Executar receita</h2>
            <p class="panel-description"><?= htmlspecialchars($recipe['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div style="display:flex; gap:8px;">
            <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)($recipe['id'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">Voltar</a>
        </div>
    </div>

    <form method="post" action="<?= htmlspecialchars($url('receitas/' . (int)($recipe['id'] ?? 0) . '/executar'), ENT_QUOTES, 'UTF-8'); ?>" style="margin-top: 14px;">
        <div class="form-grid">
            <label>
                Quantidade produzida
                <input type="number" step="0.001" min="0.001" name="produced_qty" value="<?= htmlspecialchars((string)($recipe['yield_qty'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
            </label>
            <label>
                Preço unitário de venda
                <input type="number" step="0.01" min="0" name="sale_unit_price" value="0">
            </label>
        </div>

        <div style="margin-top: 16px; display:flex; gap:8px;">
            <button class="btn primary" type="submit">Executar</button>
            <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)($recipe['id'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">Cancelar</a>
        </div>
    </form>
</section>
