<section class="panel">
    <h2>Demonstração simplificada</h2>
    <p class="panel-description">Resumo financeiro com base nos títulos cadastrados.</p>
    <div class="cards" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <article class="card">
            <h2>Receitas</h2>
            <strong>R$ <?= number_format((float)$dre['receitas'], 2, ',', '.'); ?></strong>
        </article>
        <article class="card">
            <h2>Despesas</h2>
            <strong>R$ <?= number_format((float)$dre['despesas'], 2, ',', '.'); ?></strong>
        </article>
        <article class="card">
            <h2>Resultado</h2>
            <strong>R$ <?= number_format((float)$dre['resultado'], 2, ',', '.'); ?></strong>
        </article>
    </div>
    <div class="form-actions">
        <a class="button button-outline" href="<?= htmlspecialchars($url('relatorios/pedidos/pdf'), ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Pedidos (PDF)</a>
        <a class="button button-outline" href="<?= htmlspecialchars($url('relatorios/estoque/xlsx'), ENT_QUOTES, 'UTF-8'); ?>">Estoque (XLSX)</a>
    </div>
</section>
