<?php
$formatPrice = function (?float $value): string {
    return 'R$ ' . number_format((float)($value ?? 0), 2, ',', '.');
};
$formatWeight = static function ($value): string {
    $numeric = (float)($value ?? 0);
    if ($numeric <= 0) {
        return '-';
    }
    if ($numeric < 1) {
        return number_format($numeric * 1000, 0, ',', '.') . ' g';
    }
    return number_format($numeric, 3, ',', '.') . ' kg';
};
$simplified = !empty($simplified);

$now = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('d/m/Y H:i');
$logo = $asset($theme['mode'] === 'dark' ? ($theme['logo_dark'] ?? 'img/logodark.png') : ($theme['logo_light'] ?? 'img/logo.png'));

$filterPills = [];
if (!empty($filters['term'])) {
    $filterPills[] = 'Busca: "' . htmlspecialchars($filters['term'], ENT_QUOTES, 'UTF-8') . '"';
}
if (!empty($filters['categoria'])) {
    $categoriasFiltro = is_array($filters['categoria']) ? $filters['categoria'] : [$filters['categoria']];
    $categoriasFiltro = array_values(array_filter(array_map('trim', $categoriasFiltro), static fn($item) => $item !== ''));
    if ($categoriasFiltro) {
        $filterPills[] = 'Categoria: ' . htmlspecialchars(implode(', ', $categoriasFiltro), ENT_QUOTES, 'UTF-8');
    }
}
if (!empty($filters['tipo'])) {
    $tipoLabel = $filters['tipo'] === 'MP' ? 'Matéria-prima' : 'Produto acabado';
    $filterPills[] = 'Tipo: ' . htmlspecialchars($tipoLabel, ENT_QUOTES, 'UTF-8');
}
?>
<header class="print-header">
    <div class="print-brand">
        <img src="<?= htmlspecialchars($logo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($appName ?? 'IndusOS Food', ENT_QUOTES, 'UTF-8'); ?>">
        <div>
            <h1><?= $simplified ? 'Catálogo simplificado de produtos' : 'Catálogo de produtos'; ?></h1>
            <p class="print-meta">Atualizado em <?= htmlspecialchars($now, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php if ($filterPills): ?>
                <p class="print-meta">Filtros: <?= implode(' · ', $filterPills); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <button class="print-button" type="button" onclick="window.print()">Imprimir</button>
</header>

<?php if (!empty($produtos)): ?>
    <table>
        <thead>
            <tr>
                <?php if (!$simplified): ?>
                    <th>ID</th>
                <?php endif; ?>
                <th>SKU</th>
                <th>Nome</th>
                <th>Categoria</th>
                <?php if (!$simplified): ?>
                    <th>Tipo</th>
                <?php endif; ?>
                <th>Unidade</th>
                <?php if ($simplified): ?>
                    <th>Peso líquido</th>
                <?php endif; ?>
                <th>Preço</th>
                <?php if (!$simplified): ?>
                    <th>Validade ( dias )</th>
                    <th>Criado em</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($produtos as $produto): ?>
            <tr>
                <?php if (!$simplified): ?>
                    <td><?= (int) $produto['id']; ?></td>
                <?php endif; ?>
                <td><?= htmlspecialchars($produto['sku'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($produto['categoria'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                <?php if (!$simplified): ?>
                    <td><?= $produto['tipo'] === 'MP' ? 'Matéria-prima' : 'Produto acabado'; ?></td>
                <?php endif; ?>
                <td><?= htmlspecialchars($produto['unidade'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                <?php if ($simplified): ?>
                    <td><?= htmlspecialchars($formatWeight($produto['peso_liquido'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></td>
                <?php endif; ?>
                <td><?= $formatPrice((float)($produto['preco'] ?? 0)); ?></td>
                <?php if (!$simplified): ?>
                    <td><?= (int)($produto['shelf_life_dias'] ?? 0); ?> dias</td>
                    <td><?= htmlspecialchars($produto['created_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="no-data">Nenhum produto encontrado para os filtros selecionados.</div>
<?php endif; ?>
