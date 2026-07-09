<?php
$recipe = $recipe ?? [];
$runs = $runs ?? [];
?>
<style>
.panel { background: var(--surface, #fff); border-radius: 14px; padding: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); margin-bottom: 16px; }
.data-table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 0.9rem; }
.data-table th, .data-table td { border: 1px solid rgba(148,163,184,0.4); padding: 8px 10px; text-align: left; }
.data-table th { text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.08em; background: rgba(15,23,42,0.08); }
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; text-decoration: none; }
.btn.primary { background: var(--primary, #4b5563); color: #fff; }
.btn.ghost { background: transparent; border: 1px solid var(--border, #e5e7eb); color: inherit; }
</style>

<section class="panel">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
        <div>
            <h2>Execuções da receita</h2>
            <p class="panel-description"><?= htmlspecialchars($recipe['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div style="display:flex; gap:8px;">
            <a class="btn ghost" href="<?= htmlspecialchars($url('receitas/' . (int)($recipe['id'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">Voltar</a>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Origem</th>
                <th>Qtd produzida</th>
                <th>Custo total</th>
                <th>Venda total</th>
                <th>Lucro</th>
                <th>Margem</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($runs)): ?>
                <?php foreach ($runs as $run): ?>
                    <tr>
                        <td><?= (int)$run['id']; ?></td>
                        <td><?= htmlspecialchars($run['source'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= number_format((float)$run['produced_qty'], 3, ',', '.'); ?></td>
                        <td>R$ <?= number_format((float)$run['total_cost'], 2, ',', '.'); ?></td>
                        <td>R$ <?= number_format((float)$run['sale_total'], 2, ',', '.'); ?></td>
                        <td>R$ <?= number_format((float)$run['profit_total'], 2, ',', '.'); ?></td>
                        <td><?= number_format((float)$run['margin_percent'], 2, ',', '.'); ?>%</td>
                        <td><?= htmlspecialchars($run['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Nenhuma execução registrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
