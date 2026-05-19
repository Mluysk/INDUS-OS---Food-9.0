<section class="panel">
    <h2>Gerar romaneio</h2>
    <p class="panel-description">Informe o pedido para gerar o romaneio e atualizar o status.</p>
    <form method="post" action="<?= htmlspecialchars($url('expedicao/romaneio'), ENT_QUOTES, 'UTF-8'); ?>" class="form-grid">
        <label>
            Pedido ID
            <input type="number" name="pedido_id" required>
        </label>
        <label>
            Volumes
            <input type="number" name="volumes" value="1" min="1">
        </label>
        <label>
            Peso total (kg)
            <input type="number" step="0.001" name="peso_total">
        </label>
        <label>
            Transportadora
            <input type="text" name="transportadora">
        </label>
        <label>
            Romaneio
            <input type="text" name="romaneio" required>
        </label>
        <div class="form-actions" style="grid-column: 1 / -1;">
            <button class="button button-primary" type="submit">Registrar expedição</button>
        </div>
    </form>
</section>

<section class="panel">
    <h2>Pedidos em fila</h2>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Itens</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($fila): ?>
                    <?php foreach ($fila as $pedido): ?>
                        <tr>
                            <td><?= (int)$pedido['id']; ?></td>
                            <td><?= htmlspecialchars($pedido['codigo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge badge-warning"><?= htmlspecialchars($pedido['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td>R$ <?= number_format((float)($pedido['total'] ?? 0), 2, ',', '.'); ?></td>
                            <td><?= count($pedido['itens'] ?? []); ?></td>
                            <td><a class="button button-outline" href="<?= htmlspecialchars($url('expedicao/picking/'.(int)($pedido['id'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">Picking</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Nenhum pedido aguardando expedição.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
