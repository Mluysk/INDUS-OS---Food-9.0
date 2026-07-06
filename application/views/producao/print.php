<div class="print-header">
    <div>
        <h1>Planejamento de produção</h1>
        <p>Pedidos internos – emissão em <?= htmlspecialchars($emitidoEm ?? date('d/m/Y H:i'), ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <div>
        <?php $reportLogo = $sidebarLogo['path'] ?? ($theme['logo_light'] ?? 'img/logo.png'); ?>
        <?php if ($reportLogo !== ''): ?>
            <img src="<?= htmlspecialchars(($asset ?? fn($p) => $p)($reportLogo), ENT_QUOTES, 'UTF-8'); ?>" alt="Logo" />
        <?php endif; ?>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Produto</th>
            <th>Unidade</th>
            <th>Estoque atual</th>
            <th>Produzir</th>
            <th>Distribuição (próximos 5 dias)</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['sku'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($item['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($item['unidade'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= number_format((float)($item['estoque'] ?? 0), 2, ',', '.'); ?></td>
                    <td><strong><?= number_format((float)($item['quantidade_produzir'] ?? 0), 2, ',', '.'); ?></strong></td>
                    <td class="volumes">
                        <?php if (!empty($item['volumes'])): ?>
                            <?php foreach ($item['volumes'] as $vol): ?>
                                <div class="volume-row">
                                    <span class="vol-date"><?= date('d/m/Y', strtotime($vol['data'] ?? date('Y-m-d'))); ?></span>
                                    <span class="vol-qty"><?= number_format((float)($vol['quantidade'] ?? 0), 2, ',', '.'); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="no-data">Nenhum volume selecionado para produção.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<button class="print-button" onclick="window.print();">Imprimir agora</button>
