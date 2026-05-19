<?php
$logo = $asset($theme['mode'] === 'dark' ? ($theme['logo_dark'] ?? 'img/logodark.png') : ($theme['logo_light'] ?? 'img/logo.png'));
$now = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('d/m/Y H:i');
$today = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');
$datas = $relatorioOp['datas'] ?? [];
$linhas = $relatorioOp['linhas'] ?? [];
?>
<style>
.print-header { align-items: flex-start; }
.print-header h1 { margin: 0 0 6px; }
.op-table { width: 100%; border-collapse: collapse; margin-top: 18px; font-size: 0.9rem; }
.op-table th, .op-table td { border: 1px solid rgba(148,163,184,0.5); padding: 8px 10px; text-align: center; }
.op-table th { background: rgba(15,23,42,0.12); text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.08em; color: #1f2937; }
.op-table thead .subhead th { background: rgba(15,23,42,0.06); font-size: 0.7rem; }
.op-table td:first-child, .op-table th:first-child { text-align: left; }
.op-table .prod-name { font-weight: 700; }
.op-table .prod-sku { display: block; font-size: 0.75rem; color: #64748b; }
.op-table .qty { font-weight: 700; }
.op-table .empty { color: #64748b; font-style: italic; text-align: center; }
.print-meta { color: #64748b; margin: 0; }
.op-table .is-today { background: rgba(14, 165, 233, 0.15); }
.op-table .stock-alert { background: rgba(239,68,68,0.14); color: #991b1b; }
</style>

<header class="print-header">
    <div class="print-brand">
        <img src="<?= htmlspecialchars($logo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($appName ?? 'IndusOS Food', ENT_QUOTES, 'UTF-8'); ?>">
        <div>
            <h1>(OO) - Ordem de Produção</h1>
            <p class="print-meta">Gerado em <?= htmlspecialchars($now, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>
    <button class="print-button" type="button" onclick="window.print()">Imprimir</button>
</header>

<?php if (!empty($datas)): ?>
    <table class="op-table">
        <thead>
            <tr>
                <th rowspan="2">Produtos</th>
                <th rowspan="2">Estoque</th>
                <?php foreach ($datas as $data): ?>
                    <th colspan="1" class="<?= $data === $today ? 'is-today' : ''; ?>"><?= date('d/m/Y', strtotime($data)); ?></th>
                <?php endforeach; ?>
            </tr>
            <tr class="subhead">
                <?php foreach ($datas as $data): ?>
                    <th class="<?= $data === $today ? 'is-today' : ''; ?>">Produção</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($linhas)): ?>
                <?php foreach ($linhas as $linha): ?>
                    <tr>
                        <td>
                            <span class="prod-name"><?= htmlspecialchars($linha['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="prod-sku"><?= htmlspecialchars($linha['sku'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        </td>
                        <?php $estoqueVal = (float)($linha['estoque'] ?? 0); ?>
                        <td class="qty <?= $estoqueVal <= 0 ? 'stock-alert' : ''; ?>"><?= number_format($estoqueVal, 2, ',', '.'); ?></td>
                        <?php foreach ($datas as $data): ?>
                            <td class="qty <?= $data === $today ? 'is-today' : ''; ?>"><?= number_format((float)($linha['datas'][$data] ?? 0), 2, ',', '.'); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= 2 + count($datas); ?>" class="empty">Nenhuma produção planejada encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="empty">Nenhuma data disponível para o relatório.</p>
<?php endif; ?>
