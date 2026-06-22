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
$visual = !empty($visual);
$sharedVisual = !empty($sharedVisual);
$shareUrl = '';
if ($visual) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    $query = $_GET;
    $query['modo'] = 'visual';
    $query['compartilhar'] = '1';
    unset($query['share']);
    $shareUrl = ($host !== '' ? $scheme . '://' . $host : '') . $path . '?' . http_build_query($query);
}

$now = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('d/m/Y H:i');
$logo = $asset($nota['logo'] ?? ($theme['logo_light'] ?? 'img/logo.png'));

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
    <?php if ($visual): ?>
        <style>
            .visual-catalog { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
            .visual-product { overflow: hidden; border: 1px solid rgba(15, 23, 42, 0.10); border-radius: 18px; background: #fff; box-shadow: 0 18px 44px -34px rgba(15, 23, 42, 0.45); break-inside: avoid; }
            .visual-product__photo { display: grid; place-items: center; aspect-ratio: 4 / 3; background: color-mix(in srgb, var(--brand) 8%, #fff); border-bottom: 1px solid rgba(15, 23, 42, 0.08); }
            .visual-product__photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .visual-product__empty { width: 100%; height: 100%; display: grid; place-items: center; background: color-mix(in srgb, var(--brand) 8%, #fff); color: var(--brand); }
            .visual-product__empty-mark { width: 62px; height: 62px; border-radius: 18px; display: grid; place-items: center; background: rgba(255, 255, 255, 0.82); }
            .visual-product__empty-mark svg { width: 30px; height: 30px; stroke: currentColor; }
            .visual-product__body { display: grid; gap: 10px; padding: 14px; }
            .visual-product__top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
            .visual-product h2 { margin: 0; color: #0f172a; font-size: 1rem; line-height: 1.2; }
            .visual-product__sku { flex: 0 0 auto; padding: 5px 8px; border-radius: 999px; background: color-mix(in srgb, var(--brand) 12%, #fff); color: var(--brand); font-size: 0.72rem; font-weight: 900; }
            .visual-product__meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
            .visual-product__meta span { min-width: 0; padding: 8px; border-radius: 10px; background: rgba(15, 23, 42, 0.035); color: #475569; font-size: 0.78rem; font-weight: 750; }
            .visual-product__price { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: 2px; padding-top: 10px; border-top: 1px solid rgba(15, 23, 42, 0.08); color: #0f172a; font-weight: 900; }
            .visual-product__price strong { color: var(--brand); font-size: 1.12rem; }
            .visual-share-button {
                position: fixed;
                right: 22px;
                bottom: 22px;
                z-index: 40;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 9px;
                min-height: 50px;
                padding: 0 18px;
                border: 0;
                border-radius: 999px;
                background: var(--brand);
                color: #fff;
                font-weight: 900;
                cursor: pointer;
                box-shadow: 0 20px 42px -22px rgba(15, 23, 42, 0.65);
            }
            .visual-share-button svg {
                width: 20px;
                height: 20px;
                stroke: currentColor;
            }
            .visual-share-button__status {
                position: fixed;
                right: 22px;
                bottom: 82px;
                z-index: 39;
                display: none;
                max-width: min(320px, calc(100vw - 44px));
                padding: 10px 14px;
                border-radius: 14px;
                background: #0f172a;
                color: #fff;
                font-size: 0.86rem;
                font-weight: 800;
                box-shadow: 0 20px 42px -28px rgba(15, 23, 42, 0.72);
            }
            .visual-share-button__status.is-visible { display: block; }
            @media (max-width: 720px) {
                .visual-catalog { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
                .visual-product { border-radius: 14px; }
                .visual-product__body { padding: 10px; gap: 8px; }
                .visual-product h2 { font-size: 0.88rem; }
                .visual-product__meta { grid-template-columns: 1fr; gap: 6px; }
                .visual-product__meta span { font-size: 0.68rem; padding: 6px; }
                .visual-share-button {
                    right: 14px;
                    bottom: 14px;
                    width: 54px;
                    height: 54px;
                    min-height: 54px;
                    padding: 0;
                    border-radius: 50%;
                }
                .visual-share-button span { display: none; }
                .visual-share-button svg { width: 24px; height: 24px; }
                .visual-share-button__status {
                    right: 14px;
                    bottom: 76px;
                }
            }
            @media print {
                .visual-share-button,
                .visual-share-button__status {
                    display: none !important;
                }
                .visual-catalog { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
                .visual-product { box-shadow: none; border-radius: 12px; }
                .visual-product__body { padding: 10px; gap: 8px; }
                .visual-product h2 { font-size: 0.86rem; }
                .visual-product__meta span { font-size: 0.68rem; padding: 6px; }
            }
        </style>
        <section class="visual-catalog">
            <?php foreach ($produtos as $produto): ?>
                <?php
                    $imagePath = trim((string)($produto['imagem_path'] ?? ''));
                    if ($imagePath === '') {
                        $imagePath = trim((string)($produto['imagem_pronto_path'] ?? ''));
                    }
                    $imageUrl = $imagePath !== '' ? $asset($imagePath) : '';
                ?>
                <article class="visual-product">
                    <div class="visual-product__photo">
                        <?php if ($imageUrl !== ''): ?>
                            <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($produto['nome'] ?? 'Produto', ENT_QUOTES, 'UTF-8'); ?>">
                        <?php else: ?>
                            <span class="visual-product__empty" aria-label="Produto sem imagem cadastrada">
                                <span class="visual-product__empty-mark" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                        <circle cx="9" cy="9" r="2"></circle>
                                        <path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21"></path>
                                    </svg>
                                </span>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="visual-product__body">
                        <div class="visual-product__top">
                            <h2><?= htmlspecialchars($produto['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                            <span class="visual-product__sku"><?= htmlspecialchars($produto['sku'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="visual-product__meta">
                            <span><?= htmlspecialchars($produto['categoria'] ?? 'Sem categoria', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span><?= htmlspecialchars($produto['unidade'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span>Peso <?= htmlspecialchars($formatWeight($produto['peso_liquido'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></span>
                            <span><?= (int)($produto['shelf_life_dias'] ?? 0); ?> dias</span>
                        </div>
                        <div class="visual-product__price">
                            <span>Preco</span>
                            <strong><?= $formatPrice((float)($produto['preco'] ?? 0)); ?></strong>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
        <button type="button" class="visual-share-button" data-share-url="<?= htmlspecialchars($shareUrl, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Compartilhar catálogo visual">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <path d="m8.59 13.51 6.83 3.98"></path>
                <path d="m15.41 6.51-6.82 3.98"></path>
            </svg>
            <span>Compartilhar link</span>
        </button>
        <div class="visual-share-button__status" role="status" aria-live="polite"></div>
        <script>
            (() => {
                const button = document.querySelector('.visual-share-button');
                const status = document.querySelector('.visual-share-button__status');
                if (!button) return;
                const showStatus = (message) => {
                    if (!status) return;
                    status.textContent = message;
                    status.classList.add('is-visible');
                    window.clearTimeout(status._timer);
                    status._timer = window.setTimeout(() => status.classList.remove('is-visible'), 2600);
                };
                const copyLink = async (url) => {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(url);
                        return true;
                    }
                    const input = document.createElement('input');
                    input.value = url;
                    input.setAttribute('readonly', 'readonly');
                    input.style.position = 'fixed';
                    input.style.left = '-9999px';
                    document.body.appendChild(input);
                    input.select();
                    const ok = document.execCommand('copy');
                    input.remove();
                    return ok;
                };
                button.addEventListener('click', async () => {
                    const url = button.dataset.shareUrl || window.location.href;
                    const title = document.title || 'Catálogo visual de produtos';
                    try {
                        if (navigator.share) {
                            await navigator.share({ title, text: 'Confira o catálogo visual de produtos.', url });
                            return;
                        }
                        await copyLink(url);
                        showStatus('Link do catálogo copiado.');
                    } catch (error) {
                        if (error && error.name === 'AbortError') return;
                        showStatus('Nao foi possivel compartilhar. Copie o link da pagina.');
                    }
                });
            })();
        </script>
    <?php else: ?>
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
    <?php endif; ?>
<?php else: ?>
    <div class="no-data">Nenhum produto encontrado para os filtros selecionados.</div>
<?php endif; ?>
