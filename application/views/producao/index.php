<style>
.panel { background: var(--surface, #fff); border-radius: 14px; padding: 18px 18px 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 16px; }
.panel-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.panel h3 { margin: 4px 0; font-size: 1.05rem; }
.eyebrow { text-transform: uppercase; letter-spacing: .08em; font-size: .68rem; margin: 0; color: var(--muted, #6b7280); }
.muted { color: var(--muted, #6b7280); font-size: .85rem; }
.legend { display: flex; gap: 12px; align-items: center; font-size: .82rem; color: var(--muted, #6b7280); }
.legend .dot { width: 12px; height: 12px; border-radius: 999px; display: inline-block; margin-right: 6px; }
.table-wrap { overflow-x: auto; }
@media (max-width: 900px) {
    .panel-header { align-items: flex-start; }
}
.btn-primary { background: var(--primary, #4b5563); color: #fff; border: none; border-radius: 12px; padding: 10px 16px; font-weight: 700; cursor: pointer; box-shadow: 0 8px 18px rgba(0,0,0,0.1); transition: transform .15s ease, box-shadow .15s ease; text-decoration: none; display: inline-flex; gap: 8px; align-items: center; }
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 12px 22px rgba(0,0,0,0.12); }
.op-table { width: 100%; border-collapse: collapse; margin-top: 14px; font-size: .9rem; }
.op-table th, .op-table td { border: 1px solid rgba(148,163,184,0.4); padding: 8px 10px; text-align: center; }
.op-table th { background: rgba(15,23,42,0.08); text-transform: uppercase; font-size: .72rem; letter-spacing: .08em; color: #1f2937; }
.op-table thead .subhead th { background: rgba(15,23,42,0.04); font-size: .7rem; }
.op-table td:first-child, .op-table th:first-child { text-align: left; }
.op-table .prod-name { font-weight: 700; }
.op-table .prod-sku { font-size: .85rem; color: var(--muted, #6b7280); font-weight: 600; }
.op-table .prod-label { display: inline-flex; gap: 8px; align-items: baseline; flex-wrap: nowrap; }
.op-table .qty { font-weight: 700; }
.op-table .empty { color: var(--muted, #6b7280); font-style: italic; }
.op-table .is-today { background: rgba(14, 165, 233, 0.12); }
.op-table .stock-alert { background: rgba(239,68,68,0.12); color: #991b1b; }
.op-date-select { display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: .7rem; text-transform: none; }
.op-date-select input { width: 14px; height: 14px; }
.op-actions { display: flex; gap: 10px; align-items: center; }
</style>

<?php $today = date('Y-m-d'); ?>
<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Relatório</p>
            <h3>Ordem de produção</h3>
            <p class="muted">Resumo das quantidades planejadas no mapa diário e completo para os próximos dias.</p>
        </div>
        <div class="op-actions">
            <a class="btn-primary" href="/producao/entradas">Entrada diária</a>
            <button class="btn-primary" form="op-date-form" type="submit">Imprimir relatório</button>
        </div>
    </div>

    <?php if (!empty($relatorioOp['datas'])): ?>
        <form id="op-date-form" action="/producao/relatorio-op" method="get" target="_blank">
        <div class="table-wrap">
            <table class="op-table">
                <thead>
                    <tr>
                        <th rowspan="2">Produtos</th>
                        <th rowspan="2">Estoque</th>
                        <?php foreach ($relatorioOp['datas'] as $data): ?>
                            <?php $isToday = $data === $today; ?>
                            <th colspan="1" class="<?= $isToday ? 'is-today' : ''; ?>">
                                <label class="op-date-select">
                                    <input type="checkbox" name="datas[]" value="<?= htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); ?>" <?= in_array($data, $datasSelecionadas ?? [], true) ? 'checked' : ''; ?>>
                                    <?= date('d/m/Y', strtotime($data)); ?>
                                </label>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                    <tr class="subhead">
                        <?php foreach ($relatorioOp['datas'] as $data): ?>
                            <th class="<?= $data === $today ? 'is-today' : ''; ?>">Produção</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($relatorioOp['linhas'])): ?>
                        <?php foreach ($relatorioOp['linhas'] as $linha): ?>
                            <tr>
                                <td>
                                    <span class="prod-label">
                                        <span class="prod-sku"><?= htmlspecialchars($linha['sku'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span class="prod-name"><?= htmlspecialchars($linha['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </span>
                                </td>
                                <?php $estoqueVal = (float)($linha['estoque'] ?? 0); ?>
                                <td class="qty <?= $estoqueVal <= 0 ? 'stock-alert' : ''; ?>"><?= number_format($estoqueVal, 2, ',', '.'); ?></td>
                                <?php foreach ($relatorioOp['datas'] as $data): ?>
                                    <td class="qty <?= $data === $today ? 'is-today' : ''; ?>"><?= number_format((float)($linha['datas'][$data] ?? 0), 2, ',', '.'); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= 2 + count($relatorioOp['datas']); ?>" class="empty">Nenhuma produção planejada encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </form>
    <?php else: ?>
        <p class="muted">Nenhuma data disponível para o relatório.</p>
    <?php endif; ?>
</section>
