<section class="panel">
    <h2>Ficha técnica</h2>
    <?php if ($ficha && isset($ficha['produto'])): ?>
        <p class="panel-description">Detalhes completos do produto e seus componentes.</p>
        <div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
            <label>
                SKU
                <input type="text" value="<?= htmlspecialchars($ficha['produto']['sku'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </label>
            <label>
                Nome
                <input type="text" value="<?= htmlspecialchars($ficha['produto']['nome'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </label>
            <label>
                Tipo
                <input type="text" value="<?= htmlspecialchars($ficha['produto']['tipo'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </label>
            <label>
                Unidade
                <input type="text" value="<?= htmlspecialchars($ficha['produto']['unidade'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </label>
        </div>
        <section class="panel" style="padding: 0; box-shadow: none;">
            <h3 style="padding: 0 20px;">Componentes</h3>
            <div style="overflow-x: auto; padding: 0 20px 20px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Componente</th>
                            <th>Quantidade</th>
                            <th>Unidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ficha['componentes'] as $componente): ?>
                            <tr>
                                <td><?= htmlspecialchars($componente['componente_sku'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($componente['componente_nome'] ?? (string)$componente['componente_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= number_format((float)$componente['quantidade'], 4, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($componente['componente_unidade'] ?? $componente['unidade'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php else: ?>
        <p class="panel-description">Produto não encontrado ou sem componentes cadastrados.</p>
    <?php endif; ?>
</section>
