<?php
$recipe = $recipe ?? [];
$simulation = $simulation ?? [];
$items = $simulation['items'] ?? [];
$producedQty = $simulation['produced_qty'] ?? ($recipe['yield_qty'] ?? 0);
$totalCost = $simulation['total_cost'] ?? 0;
?>
<style>
.panel { background: var(--surface, #fff); border-radius: 14px; padding: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); margin-bottom: 16px; }
.card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
.card { background: var(--surface-alt, #f8fafc); border-radius: 12px; padding: 12px; border: 1px solid var(--border, #e5e7eb); }
.data-table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 0.9rem; }
.data-table th, .data-table td { border: 1px solid rgba(148,163,184,0.4); padding: 8px 10px; text-align: left; }
.data-table th { text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.08em; background: rgba(15,23,42,0.08); }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; text-decoration: none; }
.btn.primary { background: var(--primary, #4b5563); color: #fff; }
.btn.ghost { background: transparent; border: 1px solid var(--border, #e5e7eb); color: inherit; }
.sim-form { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
.sim-form label { display: flex; flex-direction: column; gap: 6px; font-size: 0.82rem; color: var(--muted, #6b7280); }
.sim-form input { border: 1px solid var(--border, #e5e7eb); border-radius: 10px; padding: 8px 10px; background: var(--surface-alt, #f8fafc); }
</style>

<section class="panel">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
        <div>
            <h2><?= htmlspecialchars($recipe['name'] ?? 'Receita', ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="panel-description">SKU: <?= htmlspecialchars($recipe['sku'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div style="display:flex; gap:8px;">
            <a class="btn ghost" href="<?= htmlspecialchars($url('receitas'), ENT_QUOTES, 'UTF-8'); ?>">Voltar</a>
            <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id'] . '/editar'), ENT_QUOTES, 'UTF-8'); ?>">Editar</a>
            <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id'] . '/runs'), ENT_QUOTES, 'UTF-8'); ?>">Histórico</a>
        </div>
    </div>

    <div class="card-grid" style="margin-top: 12px;">
        <div class="card">
            <small>Rendimento</small>
            <strong><?= number_format((float)($recipe['yield_qty'] ?? 0), 3, ',', '.'); ?> <?= htmlspecialchars($recipe['yield_unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
        </div>
        <div class="card">
            <small>Status</small>
            <strong><?= (int)($recipe['active'] ?? 1) === 1 ? 'Ativa' : 'Inativa'; ?></strong>
        </div>
        <div class="card">
            <small>Custo total estimado</small>
            <strong>R$ <?= number_format((float)$totalCost, 2, ',', '.'); ?></strong>
        </div>
    </div>
</section>

<section class="panel">
    <h3>Simulador de custo</h3>
    <form class="sim-form" method="get" action="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id']), ENT_QUOTES, 'UTF-8'); ?>">
        <label>
            Quantidade produzida
            <input type="number" step="0.001" min="0.001" name="qty" value="<?= htmlspecialchars((string)$producedQty, ENT_QUOTES, 'UTF-8'); ?>">
        </label>
        <button class="btn primary" type="submit">Simular</button>
    </form>

    <table class="data-table">
        <thead>
            <tr>
                <th>Ingrediente</th>
                <th>Quantidade usada</th>
                <th>Custo unitário</th>
                <th>Custo total</th>
                <th>Perda</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars(($item['sku'] ?? '') . ' - ' . ($item['nome'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= number_format((float)$item['qty_used'], 3, ',', '.'); ?> <?= htmlspecialchars($item['unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>R$ <?= number_format((float)$item['cost_unit'], 4, ',', '.'); ?></td>
                        <td>R$ <?= number_format((float)$item['cost_total'], 2, ',', '.'); ?></td>
                        <td><?= number_format((float)$item['loss_percent'], 2, ',', '.'); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Nenhum item calculado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
