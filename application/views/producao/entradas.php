<?php $today = date('Y-m-d'); ?>
<style>
.panel { background: var(--surface, #fff); border-radius: 14px; padding: 18px 18px 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 16px; }
.panel-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.panel h3 { margin: 4px 0; font-size: 1.05rem; }
.eyebrow { text-transform: uppercase; letter-spacing: .08em; font-size: .68rem; margin: 0; color: var(--muted, #6b7280); }
.muted { color: var(--muted, #6b7280); font-size: .85rem; }
.btn-primary { background: var(--primary, #4b5563); color: #fff; border: none; border-radius: 12px; padding: 10px 16px; font-weight: 700; cursor: pointer; box-shadow: 0 8px 18px rgba(0,0,0,0.1); transition: transform .15s ease, box-shadow .15s ease; text-decoration: none; display: inline-flex; gap: 8px; align-items: center; }
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 12px 22px rgba(0,0,0,0.12); }
.table-wrap { overflow-x: auto; }
.op-table { width: 100%; border-collapse: collapse; margin-top: 14px; font-size: .9rem; }
.op-table th, .op-table td { border: 1px solid rgba(148,163,184,0.4); padding: 8px 10px; text-align: center; }
.op-table th { background: rgba(15,23,42,0.08); text-transform: uppercase; font-size: .72rem; letter-spacing: .08em; color: #1f2937; }
.op-table td:first-child, .op-table th:first-child { text-align: left; }
.op-table .prod-name { font-weight: 700; }
.op-table .prod-sku { font-size: .85rem; color: var(--muted, #6b7280); font-weight: 600; }
.op-table .prod-label { display: inline-flex; gap: 8px; align-items: baseline; flex-wrap: nowrap; }
.op-table .qty { font-weight: 700; }
.op-table .stock-alert { background: rgba(239,68,68,0.12); color: #991b1b; }
.op-table .input-prod { width: 140px; padding: 8px 38px 8px 10px; border: 1px solid var(--border, #e5e7eb); border-radius: 999px; background: var(--input, #f8fafc); color: inherit; text-align: center; font-weight: 700; }
.op-form { display: flex; align-items: center; justify-content: center; }
.op-input-wrap { position: relative; display: inline-flex; align-items: center; }
.op-submit {
    position: absolute;
    right: 6px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    border: none;
    background: #0ea5e9;
    color: #fff;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 12px rgba(14,165,233,0.2);
}
.op-submit:hover { filter: brightness(0.95); }
</style>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Entrada diária</p>
            <h3>Produção do dia</h3>
            <p class="muted">Registre o produzido do dia para lançar a entrada no mapa diário e no mapa completo.</p>
        </div>
        <a class="btn-primary" href="/producao/op">Voltar para produção</a>
    </div>

    <div class="table-wrap">
        <table class="op-table">
            <thead>
                <tr>
                    <th>Produtos</th>
                    <th>Estoque</th>
                    <th>Produção do dia (<?= date('d/m/Y', strtotime($today)); ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($planejamento)): ?>
                    <?php foreach ($planejamento as $produto): ?>
                        <?php $estoqueVal = (float)($produto['estoque'] ?? 0); ?>
                        <tr>
                            <td>
                                <span class="prod-label">
                                    <span class="prod-sku"><?= htmlspecialchars($produto['sku'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="prod-name"><?= htmlspecialchars($produto['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                </span>
                            </td>
                            <td class="qty <?= $estoqueVal <= 0 ? 'stock-alert' : ''; ?>"><?= number_format($estoqueVal, 2, ',', '.'); ?></td>
                            <td>
                                <form class="op-form" action="/mapa/entrada" method="post">
                                    <input type="hidden" name="produto_id" value="<?= (int)($produto['id'] ?? 0); ?>">
                                    <input type="hidden" name="data_referencia" value="<?= htmlspecialchars($today, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="tipo" value="estoque">
                                    <span class="op-input-wrap">
                                        <input class="input-prod" type="number" step="0.01" min="0" name="quantidade" placeholder="0">
                                        <button type="submit" class="op-submit" aria-label="Lançar produção">+</button>
                                    </span>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="muted">Nenhum produto acabado cadastrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
