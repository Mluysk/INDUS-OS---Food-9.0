<section class="panel">
    <h2>Picking do pedido #<?= htmlspecialchars($picking['pedido']['id'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></h2>
    <?php if (!empty($picking['pedido'])): ?>
        <p class="panel-description">Cliente: <?= htmlspecialchars($picking['pedido']['cliente_id'] ?? '-', ENT_QUOTES, 'UTF-8'); ?> • Status: <?= htmlspecialchars($picking['pedido']['status'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço unitário</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($picking['itens'] ?? [] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['produto_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= number_format((float)$item['quantidade'], 4, ',', '.'); ?></td>
                        <td>R$ <?= number_format((float)$item['preco_unit'], 2, ',', '.'); ?></td>
                        <td>R$ <?= number_format((float)$item['preco_unit'] * (float)$item['quantidade'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="form-actions">
        <a class="button button-outline" href="<?= htmlspecialchars($url('expedicao'), ENT_QUOTES, 'UTF-8'); ?>">Voltar à fila</a>
    </div>
</section>
