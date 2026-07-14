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
$logo = $asset($sidebarLogo['path'] ?? ($theme['logo_light'] ?? 'img/logo.png'));

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

<style>
    .print-brand img {
        width: auto !important;
        height: var(--form-logo-size, 96px) !important;
        min-width: 0;
        max-width: none;
        object-fit: contain;
        object-position: left center;
        transform: none;
        transform-origin: left center;
        margin: 6px 0;
        padding: 0;
        box-sizing: border-box;
    }
    .print-brand img.is-square-form-logo {
        width: var(--form-logo-size, 96px) !important;
        min-width: var(--form-logo-size, 96px);
        max-width: var(--form-logo-size, 96px);
        margin-top: var(--square-form-logo-margin, 47px);
        margin-bottom: var(--square-form-logo-margin, 47px);
        transform: scale(1.85);
        transform-origin: left center;
    }
</style>
<script>
    (() => {
        const logo = document.querySelector('.print-brand img');
        if (!logo) return;
        const classifyLogo = () => {
            const width = Number(logo.naturalWidth || 0);
            const height = Number(logo.naturalHeight || 0);
            if (!width || !height) return;
            const ratio = width / height;
            const isSquare = ratio >= 0.8 && ratio <= 1.25;
            logo.classList.toggle('is-square-form-logo', isSquare);
            if (isSquare) {
                const scaleOverflow = logo.offsetHeight * ((1.85 - 1) / 2);
                logo.style.setProperty('--square-form-logo-margin', `${6 + scaleOverflow}px`);
            } else {
                logo.style.removeProperty('--square-form-logo-margin');
            }
        };
        if (logo.complete) classifyLogo();
        else logo.addEventListener('load', classifyLogo, { once: true });
    })();
</script>

<?php if ($visual): ?>
    <style>
        .visual-catalog-filters {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) minmax(190px, 280px) auto;
            align-items: end;
            gap: 12px;
            margin: 0 0 20px;
            padding: 16px;
            border: 1px solid rgba(15, 23, 42, 0.10);
            border-radius: 14px;
            background: rgba(248, 250, 252, 0.82);
        }
        .visual-catalog-filters__field {
            display: grid;
            gap: 6px;
            min-width: 0;
        }
        .visual-catalog-filters__field label {
            color: #475569;
            font-size: 0.78rem;
            font-weight: 850;
        }
        .visual-catalog-filters__control {
            position: relative;
            min-width: 0;
        }
        .visual-catalog-filters__control svg {
            position: absolute;
            top: 50%;
            left: 13px;
            width: 18px;
            height: 18px;
            transform: translateY(-50%);
            stroke: #64748b;
            pointer-events: none;
        }
        .visual-catalog-filters input,
        .visual-catalog-filters select {
            width: 100%;
            height: 44px;
            border: 1px solid #d9dde4;
            border-radius: 10px;
            background: #fff;
            color: #0f172a;
            font: inherit;
            font-size: 0.9rem;
            outline: none;
        }
        .visual-catalog-filters input {
            padding: 0 13px 0 41px;
        }
        .visual-catalog-filters select {
            padding: 0 38px 0 13px;
            cursor: pointer;
        }
        .visual-catalog-filters input:focus,
        .visual-catalog-filters select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--brand) 14%, transparent);
        }
        .visual-catalog-filters__actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .visual-catalog-filters__submit,
        .visual-catalog-filters__clear {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 44px;
            border-radius: 10px;
            padding: 0 16px;
            font: inherit;
            font-size: 0.86rem;
            font-weight: 850;
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;
        }
        .visual-catalog-filters__submit {
            border: 1px solid var(--brand);
            background: var(--brand);
            color: #fff;
        }
        .visual-catalog-filters__clear {
            border: 1px solid #d9dde4;
            background: #fff;
            color: #475569;
        }
        .visual-catalog-live-status {
            margin: -8px 0 16px;
            color: #64748b;
            font-size: 0.84rem;
            font-weight: 750;
        }
        @media (max-width: 720px) {
            .visual-catalog-filters {
                grid-template-columns: 1fr;
                padding: 12px;
            }
            .visual-catalog-filters__actions > * {
                flex: 1 1 0;
            }
        }
        @media print {
            .visual-catalog-filters {
                display: none !important;
            }
            .visual-catalog-live-status {
                display: none !important;
            }
        }
    </style>
    <?php
        $clearVisualQuery = ['modo' => 'visual'];
        if ($sharedVisual) {
            $clearVisualQuery['compartilhar'] = '1';
        }
        $clearVisualUrl = (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '') . '?' . http_build_query($clearVisualQuery);
    ?>
    <form class="visual-catalog-filters" method="get" data-visual-live-filter>
        <input type="hidden" name="modo" value="visual">
        <?php if ($sharedVisual): ?>
            <input type="hidden" name="compartilhar" value="1">
        <?php endif; ?>
        <div class="visual-catalog-filters__field">
            <label for="visual-product-search">Buscar produto por nome</label>
            <div class="visual-catalog-filters__control">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.8-3.8"></path>
                </svg>
                <input id="visual-product-search" type="search" name="q" value="<?= htmlspecialchars((string)($filters['term'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Digite o nome do produto" autocomplete="off">
            </div>
        </div>
        <div class="visual-catalog-filters__field">
            <label for="visual-product-category">Categoria</label>
            <select id="visual-product-category" name="categoria">
                <option value="">Todas as categorias</option>
                <?php foreach (($categorias ?? []) as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8'); ?>" <?= (($filters['categoria'] ?? '') === $categoria) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="visual-catalog-filters__actions">
            <?php if (!empty($filters['term']) || !empty($filters['categoria'])): ?>
                <a class="visual-catalog-filters__clear" href="<?= htmlspecialchars($clearVisualUrl, ENT_QUOTES, 'UTF-8'); ?>">Limpar</a>
            <?php endif; ?>
        </div>
    </form>
    <div class="visual-catalog-live-status" data-visual-live-status aria-live="polite"></div>
<?php endif; ?>

<?php if (!empty($produtos)): ?>
    <?php if ($visual): ?>
        <style>
            .visual-catalog { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
            .visual-product { overflow: hidden; border: 1px solid rgba(15, 23, 42, 0.10); border-radius: 18px; background: #fff; box-shadow: 0 18px 44px -34px rgba(15, 23, 42, 0.45); break-inside: avoid; }
            .visual-product.is-hidden { display: none; }
            .visual-product__photo { position: relative; display: grid; place-items: center; aspect-ratio: 4 / 3; overflow: hidden; background: color-mix(in srgb, var(--brand) 8%, #fff); border-bottom: 1px solid rgba(15, 23, 42, 0.08); }
            .visual-product__photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .visual-product__zoom {
                position: absolute;
                inset: 0;
                display: grid;
                place-items: center;
                border: 0;
                padding: 0;
                background: rgba(15, 23, 42, 0.02);
                color: #fff;
                cursor: zoom-in;
                opacity: 0;
                transition: opacity 0.18s ease, background 0.18s ease;
            }
            .visual-product__photo:hover .visual-product__zoom,
            .visual-product__zoom:focus-visible {
                opacity: 1;
                background: rgba(15, 23, 42, 0.07);
            }
            .visual-product__zoom-mark {
                display: inline-grid;
                place-items: center;
                width: 48px;
                height: 48px;
                border: 1px solid rgba(255, 255, 255, 0.52);
                border-radius: 50%;
                background: color-mix(in srgb, var(--brand) 48%, transparent);
                backdrop-filter: blur(3px);
                box-shadow: 0 14px 28px -22px rgba(15, 23, 42, 0.58);
            }
            .visual-product__zoom svg { width: 25px; height: 25px; stroke: currentColor; }
            .visual-product__photo-toggle {
                position: absolute;
                top: 50%;
                right: -18px;
                z-index: 2;
                display: inline-grid;
                place-items: center start;
                width: 48px;
                height: 48px;
                min-width: 48px;
                min-height: 48px;
                transform: translateY(-50%);
                border: 1px solid rgba(255, 255, 255, 0.52);
                border-radius: 50%;
                padding: 0 0 0 12px;
                background: color-mix(in srgb, var(--brand) 48%, transparent);
                backdrop-filter: blur(3px);
                color: #fff;
                cursor: pointer;
                box-shadow: 0 14px 28px -22px rgba(15, 23, 42, 0.58);
            }
            .visual-product__photo-toggle:hover,
            .visual-product__photo-toggle:focus-visible {
                transform: translateY(-50%);
                background: color-mix(in srgb, var(--brand) 48%, transparent);
            }
            .visual-product__photo-toggle svg { width: 14px; height: 14px; stroke: currentColor; }
            .visual-product__photo-caption {
                position: absolute;
                left: 10px;
                bottom: 10px;
                z-index: 2;
                max-width: calc(100% - 20px);
                border-radius: 999px;
                padding: 5px 9px;
                background: rgba(15, 23, 42, 0.68);
                color: #fff;
                font-size: 0.68rem;
                font-weight: 850;
            }
            .visual-image-lightbox {
                position: fixed;
                inset: 0;
                z-index: 100;
                display: none;
                align-items: center;
                justify-content: center;
                padding: clamp(18px, 4vw, 46px);
                background: rgba(15, 23, 42, 0.78);
                backdrop-filter: blur(8px);
            }
            .visual-image-lightbox.is-open { display: flex; }
            .visual-image-lightbox__dialog {
                position: relative;
                display: grid;
                gap: 12px;
                width: min(980px, 100%);
            }
            .visual-image-lightbox img {
                width: 100%;
                max-height: min(76vh, 760px);
                object-fit: contain;
                border-radius: 18px;
                background: #fff;
                box-shadow: 0 28px 70px -24px rgba(0, 0, 0, 0.78);
            }
            .visual-image-lightbox__caption {
                justify-self: center;
                padding: 7px 13px;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.92);
                color: #0f172a;
                font-weight: 850;
            }
            .visual-image-lightbox__close {
                position: absolute;
                top: 14px;
                right: 14px;
                z-index: 2;
                display: inline-grid;
                place-items: center;
                width: 38px;
                height: 38px;
                border: 1px solid rgba(255, 255, 255, 0.72);
                border-radius: 50%;
                padding: 0;
                background: var(--brand);
                color: #fff;
                cursor: pointer;
                box-shadow: 0 16px 30px -20px rgba(15, 23, 42, 0.82);
            }
            .visual-image-lightbox__close svg { width: 18px; height: 18px; stroke: currentColor; }
            .visual-image-lightbox__nav {
                position: absolute;
                top: 50%;
                z-index: 3;
                display: inline-grid;
                place-items: center;
                width: 62px;
                height: 62px;
                transform: translateY(-50%);
                border: 1px solid rgba(255, 255, 255, 0.62);
                border-radius: 50%;
                padding: 0;
                background: color-mix(in srgb, var(--brand) 58%, transparent);
                backdrop-filter: blur(6px);
                color: #fff;
                cursor: pointer;
                box-shadow: 0 18px 36px -24px rgba(15, 23, 42, 0.72);
            }
            .visual-image-lightbox__nav--prev { left: 16px; }
            .visual-image-lightbox__nav--next { right: 16px; }
            .visual-image-lightbox__nav:hover,
            .visual-image-lightbox__nav:focus-visible { transform: translateY(-50%); }
            .visual-image-lightbox__nav[hidden] { display: none; }
            .visual-image-lightbox__nav svg { width: 19px; height: 19px; stroke: currentColor; }
            body.visual-image-lightbox-open { overflow: hidden; }
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
            @media (hover: none) {
                .visual-product__zoom {
                    opacity: 1;
                    inset: auto 10px 10px auto;
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    background: transparent;
                }
                .visual-product__zoom-mark {
                    width: 36px;
                    height: 36px;
                }
                .visual-product__zoom svg { width: 19px; height: 19px; }
            }
            @media (max-width: 640px) {
                .visual-image-lightbox__nav {
                    width: 52px;
                    height: 52px;
                }
                .visual-image-lightbox__nav--prev { left: 10px; }
                .visual-image-lightbox__nav--next { right: 10px; }
            }
            @media print {
                .visual-share-button,
                .visual-share-button__status,
                .visual-product__zoom,
                .visual-product__photo-toggle,
                .visual-product__photo-caption,
                .visual-image-lightbox {
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
                    $productImages = [];
                    $primaryImagePath = trim((string)($produto['imagem_path'] ?? ''));
                    $secondaryImagePath = trim((string)($produto['imagem_pronto_path'] ?? ''));
                    if ($primaryImagePath !== '') {
                        $productImages[] = ['url' => $asset($primaryImagePath), 'label' => 'Principal'];
                    }
                    if ($secondaryImagePath !== '' && $secondaryImagePath !== $primaryImagePath) {
                        $productImages[] = ['url' => $asset($secondaryImagePath), 'label' => 'Secundária'];
                    }
                    $firstProductImage = $productImages[0] ?? null;
                    $productImagesJson = htmlspecialchars(
                        json_encode($productImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    $productName = (string)($produto['nome'] ?? 'Produto');
                ?>
                <article class="visual-product" data-product-name="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8'); ?>" data-product-category="<?= htmlspecialchars((string)($produto['categoria'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="visual-product__photo"<?= $productImages ? ' data-visual-gallery="' . $productImagesJson . '" data-gallery-index="0"' : ''; ?>>
                        <?php if ($firstProductImage): ?>
                            <img src="<?= htmlspecialchars($firstProductImage['url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($productName . ' - ' . $firstProductImage['label'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="button" class="visual-product__zoom" data-visual-gallery-zoom aria-label="Ampliar imagem de <?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="visual-product__zoom-mark" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="10.5" cy="10.5" r="6.5"></circle>
                                        <path d="m16 16 4.5 4.5"></path>
                                        <path d="M10.5 7.6v5.8"></path>
                                        <path d="M7.6 10.5h5.8"></path>
                                    </svg>
                                </span>
                            </button>
                            <span class="visual-product__photo-caption"><?= htmlspecialchars($firstProductImage['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php if (count($productImages) > 1): ?>
                                <button type="button" class="visual-product__photo-toggle" data-visual-gallery-next aria-label="Ver outra foto de <?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8'); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.35" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="m9 18 6-6-6-6"></path>
                                    </svg>
                                </button>
                            <?php endif; ?>
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
        <div class="visual-image-lightbox" id="visual-image-lightbox" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Imagem ampliada do produto">
            <div class="visual-image-lightbox__dialog">
                <button type="button" class="visual-image-lightbox__close" data-visual-lightbox-close aria-label="Fechar imagem ampliada">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.4" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                <button type="button" class="visual-image-lightbox__nav visual-image-lightbox__nav--prev" data-visual-lightbox-prev aria-label="Ver imagem anterior">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                </button>
                <img src="" alt="">
                <button type="button" class="visual-image-lightbox__nav visual-image-lightbox__nav--next" data-visual-lightbox-next aria-label="Ver próxima imagem">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>
                <span class="visual-image-lightbox__caption"></span>
            </div>
        </div>
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
                const liveForm = document.querySelector('[data-visual-live-filter]');
                const liveSearch = liveForm?.querySelector('input[name="q"]');
                const liveCategory = liveForm?.querySelector('select[name="categoria"]');
                const liveStatus = document.querySelector('[data-visual-live-status]');
                const productCards = Array.from(document.querySelectorAll('.visual-product'));

                const normalizeFilterText = (value) => String(value || '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toUpperCase()
                    .trim();

                const updateLiveUrl = () => {
                    const url = new URL(window.location.href);
                    const query = liveSearch?.value.trim() || '';
                    const category = liveCategory?.value || '';
                    url.searchParams.set('modo', 'visual');
                    if (query !== '') {
                        url.searchParams.set('q', query);
                    } else {
                        url.searchParams.delete('q');
                    }
                    if (category !== '') {
                        url.searchParams.set('categoria', category);
                    } else {
                        url.searchParams.delete('categoria');
                    }
                    window.history.replaceState({}, '', url.toString());
                };

                const applyLiveFilters = () => {
                    if (!liveForm) return;
                    const query = normalizeFilterText(liveSearch?.value || '');
                    const category = normalizeFilterText(liveCategory?.value || '');
                    let visibleCount = 0;

                    productCards.forEach((card) => {
                        const name = normalizeFilterText(card.dataset.productName || '');
                        const cardCategory = normalizeFilterText(card.dataset.productCategory || '');
                        const matchesName = query === '' || name.includes(query);
                        const matchesCategory = category === '' || cardCategory === category;
                        const isVisible = matchesName && matchesCategory;
                        card.classList.toggle('is-hidden', !isVisible);
                        if (isVisible) visibleCount += 1;
                    });

                    if (liveStatus) {
                        liveStatus.textContent = visibleCount === 1
                            ? '1 produto encontrado'
                            : `${visibleCount} produtos encontrados`;
                    }
                    updateLiveUrl();
                };

                if (liveForm) {
                    liveForm.addEventListener('submit', (event) => {
                        event.preventDefault();
                        applyLiveFilters();
                    });
                    liveSearch?.addEventListener('input', applyLiveFilters);
                    liveCategory?.addEventListener('change', applyLiveFilters);
                    applyLiveFilters();
                }

                const lightbox = document.getElementById('visual-image-lightbox');
                const lightboxImage = lightbox?.querySelector('img');
                const lightboxCaption = lightbox?.querySelector('.visual-image-lightbox__caption');
                const closeButton = lightbox?.querySelector('[data-visual-lightbox-close]');
                const previousButton = lightbox?.querySelector('[data-visual-lightbox-prev]');
                const nextButton = lightbox?.querySelector('[data-visual-lightbox-next]');
                let lightboxGallery = [];
                let lightboxIndex = 0;
                let lastTrigger = null;

                if (!lightbox || !lightboxImage) return;

                const readGallery = (media) => {
                    try {
                        const images = JSON.parse(media?.dataset.visualGallery || '[]');
                        return Array.isArray(images) ? images.filter((image) => image?.url) : [];
                    } catch (error) {
                        return [];
                    }
                };

                const updateLightbox = () => {
                    const image = lightboxGallery[lightboxIndex] || {};
                    lightboxImage.src = image.url || '';
                    lightboxImage.alt = image.label || 'Imagem ampliada do produto';
                    if (lightboxCaption) {
                        lightboxCaption.textContent = image.label || 'Imagem do produto';
                    }
                    const hasNavigation = lightboxGallery.length > 1;
                    previousButton?.toggleAttribute('hidden', !hasNavigation);
                    nextButton?.toggleAttribute('hidden', !hasNavigation);
                };

                const moveLightbox = (direction) => {
                    if (lightboxGallery.length < 2) return;
                    lightboxIndex = (lightboxIndex + direction + lightboxGallery.length) % lightboxGallery.length;
                    updateLightbox();
                };

                const closeLightbox = () => {
                    lightbox.classList.remove('is-open');
                    lightbox.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('visual-image-lightbox-open');
                    lightboxImage.src = '';
                    lightboxGallery = [];
                    lightboxIndex = 0;
                    lastTrigger?.focus();
                };

                document.querySelectorAll('[data-visual-gallery-next]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const media = button.closest('.visual-product__photo');
                        const images = readGallery(media);
                        if (!media || images.length < 2) return;
                        const nextIndex = (Number(media.dataset.galleryIndex || 0) + 1) % images.length;
                        const image = media.querySelector('img');
                        const caption = media.querySelector('.visual-product__photo-caption');
                        media.dataset.galleryIndex = String(nextIndex);
                        if (image) {
                            image.src = images[nextIndex].url;
                            image.alt = images[nextIndex].label || 'Produto';
                        }
                        if (caption) {
                            caption.textContent = images[nextIndex].label || 'Imagem';
                        }
                    });
                });

                document.querySelectorAll('[data-visual-gallery-zoom]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const media = button.closest('.visual-product__photo');
                        const images = readGallery(media);
                        if (!media || !images.length) return;
                        lastTrigger = button;
                        lightboxGallery = images;
                        lightboxIndex = Math.min(Math.max(Number(media.dataset.galleryIndex || 0), 0), images.length - 1);
                        updateLightbox();
                        lightbox.classList.add('is-open');
                        lightbox.setAttribute('aria-hidden', 'false');
                        document.body.classList.add('visual-image-lightbox-open');
                        closeButton?.focus();
                    });
                });

                closeButton?.addEventListener('click', closeLightbox);
                previousButton?.addEventListener('click', () => moveLightbox(-1));
                nextButton?.addEventListener('click', () => moveLightbox(1));
                lightbox.addEventListener('click', (event) => {
                    if (event.target === lightbox) closeLightbox();
                });
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && lightbox.classList.contains('is-open')) {
                        closeLightbox();
                    }
                    if (event.key === 'ArrowLeft' && lightbox.classList.contains('is-open')) {
                        moveLightbox(-1);
                    }
                    if (event.key === 'ArrowRight' && lightbox.classList.contains('is-open')) {
                        moveLightbox(1);
                    }
                });
            })();
        </script>
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
