<?php
$recipe = $recipe ?? [];
$simulation = $simulation ?? [];
$items = $simulation['items'] ?? [];
$producedQty = $simulation['produced_qty'] ?? ($recipe['yield_qty'] ?? 0);
$baseYieldQty = (float)($recipe['yield_qty'] ?? 0);
$isSimulationAdjusted = isset($_GET['qty']) && (float)$_GET['qty'] > 0;
$displayYieldQty = $isSimulationAdjusted ? (float)$producedQty : $baseYieldQty;
$totalCost = $simulation['total_cost'] ?? 0;
$warnings = $simulation['warnings'] ?? [];
$simulationWeightKg = 0.0;
$simulationWeightIgnored = 0;
foreach ($items as $simulationItem) {
    $itemQty = (float)($simulationItem['qty_used'] ?? 0);
    $itemUnit = strtolower(trim((string)($simulationItem['unit'] ?? '')));
    if ($itemUnit === 'kg') {
        $simulationWeightKg += $itemQty;
    } elseif ($itemUnit === 'g') {
        $simulationWeightKg += $itemQty / 1000;
    } elseif ($itemQty > 0) {
        $simulationWeightIgnored++;
    }
}
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
.detail-yield-highlight { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin: 14px 0; padding: 13px 15px; border-left: 4px solid var(--primary, #dc2626); border-radius: 10px; background: rgba(var(--primary-rgb, 220,38,38),.08); }
.detail-yield-highlight span { color: var(--muted, #64748b); font-size: .78rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
.detail-yield-highlight strong { color: var(--primary, #dc2626); font-size: 1.08rem; }
.simulation-totals { display: grid; grid-template-columns: repeat(2,minmax(220px,1fr)); gap: 14px; margin-top: 16px; }
.simulation-total-card { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 17px 18px; border: 2px solid rgba(var(--primary-rgb, 220,38,38),.28); border-radius: 14px; background: linear-gradient(135deg, rgba(var(--primary-rgb, 220,38,38),.11), var(--surface, #fff) 75%); box-shadow: 0 10px 24px rgba(15,23,42,.08); }
.simulation-total-card span { display: block; color: var(--muted, #64748b); font-size: .75rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
.simulation-total-card strong { display: block; color: var(--primary, #dc2626); font-size: 1.4rem; font-weight: 900; white-space: nowrap; }
.simulation-total-card small { display: block; margin-top: 3px; color: var(--muted, #64748b); font-size: .74rem; }
@media (max-width: 720px) { .simulation-totals { grid-template-columns: 1fr; } .simulation-total-card { align-items: flex-start; } }
.recipe-warning { margin-top: 12px; padding: 12px 14px; border: 1px solid rgba(245,158,11,.35); border-radius: 10px; background: rgba(245,158,11,.12); color: #92400e; }
.recipe-warning ul { margin: 6px 0 0; padding-left: 20px; }
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
            <small><?= $isSimulationAdjusted ? 'Rendimento simulado' : 'Rendimento'; ?></small>
            <strong><?= number_format($displayYieldQty, 3, ',', '.'); ?> <?= htmlspecialchars($recipe['yield_unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
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
    <?php if (!empty($warnings)): ?>
        <div class="recipe-warning" role="alert">
            <strong>Custo parcial: revise as unidades dos ingredientes.</strong>
            <ul>
                <?php foreach ($warnings as $warning): ?>
                    <li><?= htmlspecialchars((string)$warning, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</section>

<section class="panel">
    <h3>Simulador de custo</h3>
    <form class="sim-form" method="get" action="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id']), ENT_QUOTES, 'UTF-8'); ?>">
        <label>
            Quantidade produzida
            <input type="number" step="0.001" min="0.001" name="qty" value="<?= htmlspecialchars((string)$producedQty, ENT_QUOTES, 'UTF-8'); ?>">
        </label>
        <button class="btn primary" type="submit">Simular</button>
        <?php if ($isSimulationAdjusted): ?>
            <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)$recipe['id']), ENT_QUOTES, 'UTF-8'); ?>">Resetar simulação</a>
        <?php endif; ?>
    </form>

    <div class="detail-yield-highlight">
        <span><?= $isSimulationAdjusted ? 'Rendimento simulado' : 'Rendimento da receita'; ?></span>
        <strong><?= number_format($displayYieldQty, 3, ',', '.'); ?> <?= htmlspecialchars((string)($recipe['yield_unit'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
    </div>

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
                        <td><?= $item['cost_unit'] === null ? '—' : 'R$ ' . number_format((float)$item['cost_unit'], 4, ',', '.'); ?></td>
                        <td><?= $item['cost_total'] === null ? '—' : 'R$ ' . number_format((float)$item['cost_total'], 2, ',', '.'); ?></td>
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

    <div class="simulation-totals">
        <div class="simulation-total-card">
            <div>
                <span>Peso total</span>
                <small>
                    Ingredientes usados para <?= number_format((float)$producedQty, 3, ',', '.'); ?> <?= htmlspecialchars((string)($recipe['yield_unit'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>.
                    <?= $simulationWeightIgnored > 0 ? ' ' . (int)$simulationWeightIgnored . ' item(ns) em un/l/ml não entram no peso.' : ''; ?>
                </small>
            </div>
            <div>
                <strong><?= number_format($simulationWeightKg, 3, ',', '.'); ?> kg</strong>
                <small><?= number_format($simulationWeightKg * 1000, 0, ',', '.'); ?> g</small>
            </div>
        </div>
        <div class="simulation-total-card">
            <div>
                <span>Valor total</span>
                <small><?= !empty($warnings) ? 'Itens com unidade incompatível não entram no valor.' : 'Soma do custo total dos ingredientes.'; ?></small>
            </div>
            <div>
                <strong>R$ <?= number_format((float)$totalCost, 2, ',', '.'); ?></strong>
                <small>Custo da quantidade simulada</small>
            </div>
        </div>
    </div>
</section>
