<?php
$basePath = rtrim($_SERVER['APP_BASE_PATH'] ?? '', '/');
$asset = static function (string $path) use ($basePath): string {
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $normalized = ltrim($path, '/');
    if (stripos($normalized, 'public/') === 0) {
        $normalized = substr($normalized, 7);
    }

    $prefix = $basePath === '' ? '' : $basePath;
    return $prefix.'/'.ltrim($normalized, '/');
};
$footerLogoPath = $theme['logo_footer'] ?? 'uploads/branding/logo.png';
$footerLogo = htmlspecialchars($asset($footerLogoPath), ENT_QUOTES, 'UTF-8');
$headerLogoDark = htmlspecialchars($asset($theme['logo_dark'] ?? 'img/logodark.png'), ENT_QUOTES, 'UTF-8');
$headerLogoDarkHeight = max(32, min(72, (int) ($theme['logo_dark_size'] ?? 57)));
$portalPrimaryHex = htmlspecialchars($theme['primary_hex'] ?? '#2563EB', ENT_QUOTES, 'UTF-8');
$currentYear = date('Y');
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(($title ?? 'Portal do Cliente').' · '.($appName ?? 'IndusOS Food'), ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { color-scheme: light dark; font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; --bg: #f5f5f5; --text: #1f2933; --muted: #52606d; --primary: <?= $portalPrimaryHex; ?>; --portal-system-primary: <?= $portalPrimaryHex; ?>; --primary-contrast: #fff; --card: #ffffffd9; --border: rgba(15,23,42,0.08); }
        [data-theme="dark"] { --bg: #0f172a; --text: #e2e8f0; --muted: #94a3b8; --primary: var(--portal-system-primary, <?= $portalPrimaryHex; ?>); --card: rgba(15,23,42,0.8); --border: rgba(148,163,184,0.16); }
        body { margin: 0; background: var(--bg); color: var(--text); }
        *, *::before, *::after { box-sizing: border-box; }
        img, video { max-width: 100%; height: auto; }
        main { width: 100%; min-width: 0; }
        .card table { width: 100%; border-collapse: collapse; word-wrap: break-word; }
        @media (max-width: 768px) { .card table { display: block; overflow-x: auto; } .card table thead, .card table tbody { display: table; width: 100%; } }
        .hero { padding: 4rem 1.5rem 3rem; text-align: center; }
        .hero h1 { font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 0.5rem; }
        .hero p { color: var(--muted); max-width: 640px; margin: 0 auto; font-size: 1.05rem; }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem 4rem; }
        .grid { display: grid; gap: 1.5rem; }
        @media (min-width: 960px) { .grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 1.75rem; box-shadow: 0 20px 40px -24px rgba(15,23,42,0.35); backdrop-filter: blur(8px); }
        label { display: block; font-weight: 600; margin-bottom: 0.35rem; }
        input, select, textarea { width: 100%; border-radius: 12px; border: 1px solid var(--border); padding: 0.75rem 0.9rem; font-size: 1rem; background: rgba(255,255,255,0.92); color: var(--text); }
        [data-theme="dark"] input, [data-theme="dark"] textarea, [data-theme="dark"] select { background: rgba(15,23,42,0.8); }
        button { border: none; border-radius: 12px; padding: 0.8rem 1.4rem; font-weight: 600; cursor: pointer; background: linear-gradient(135deg, var(--primary), #1d4ed8); color: var(--primary-contrast); transition: transform 0.15s ease, box-shadow 0.15s ease; }
        button:hover { transform: translateY(-1px); box-shadow: 0 12px 30px -16px rgba(37,99,235,0.8); }
        .catalogo { display: grid; gap: 1.5rem; }
        @media (min-width: 768px) { .catalogo { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); } }
        .produto { border: 1px solid var(--border); border-radius: 18px; padding: 1.25rem; background: var(--card); display: flex; flex-direction: column; gap: 0.5rem; }
        .produto h3 { margin: 0; font-size: 1.2rem; }
        .badge { display: inline-flex; padding: 0.25rem 0.7rem; font-size: 0.75rem; border-radius: 999px; background: rgba(37,99,235,0.12); color: var(--primary); font-weight: 600; }
        .alert { border-radius: 14px; padding: 0.9rem 1rem; margin-bottom: 1rem; font-weight: 600; }
        .alert-success { background: rgba(22, 163, 74, 0.15); color: #15803d; }
        .alert-error { background: rgba(220, 38, 38, 0.18); color: #b91c1c; }
        .topbar { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; background: var(--primary); color: var(--primary-contrast); box-shadow: 0 14px 32px -28px color-mix(in srgb, var(--primary, #2563eb) 80%, transparent); }
        .topbar strong { color: var(--primary-contrast); }
        .portal-header-brand { display: flex; align-items: center; min-width: 0; height: 64px; }
        .portal-header-logo {
            display: block;
            width: auto;
            max-width: min(280px, 42vw);
            object-fit: contain;
            object-position: left center;
        }
        .portal-header-logo--dark { height: <?= $headerLogoDarkHeight; ?>px; max-height: 100%; }
        .portal-header-brand__fallback { color: var(--primary-contrast); font-weight: 800; }
        @media (max-width: 720px) {
            .portal-header-brand { height: 48px; max-width: calc(100vw - 110px); }
            .portal-header-logo { max-width: 100%; max-height: 48px; }
        }
        .theme-toggle { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.95rem; color: var(--muted); }
        .theme-toggle input { width: auto; }
        .portal-theme-switch {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            color: var(--primary-contrast, #fff);
            font-size: 0.9rem;
            font-weight: 800;
            line-height: 1;
            cursor: pointer;
            user-select: none;
        }
        .portal-theme-switch input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }
        .portal-theme-switch__track {
            position: relative;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            width: 64px;
            height: 34px;
            padding: 3px;
            border: 2px solid rgba(255, 255, 255, 0.9);
            border-radius: 999px;
            background: color-mix(in srgb, var(--primary, #2563eb) 72%, #0f172a);
            box-sizing: border-box;
            transition: background 180ms ease, border-color 180ms ease;
        }
        .portal-theme-switch__icon {
            position: relative;
            z-index: 1;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 15px;
            line-height: 1;
            transition: color 180ms ease;
        }
        .portal-theme-switch__icon:first-child {
            transform: translateX(-2px);
        }
        .portal-theme-switch__thumb {
            position: absolute;
            z-index: 0;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 5px 14px -7px rgba(15, 23, 42, 0.8);
            transition: transform 180ms ease;
        }
        .portal-theme-switch input:checked + .portal-theme-switch__track .portal-theme-switch__thumb {
            transform: translateX(30px);
        }
        .portal-theme-switch input:not(:checked) + .portal-theme-switch__track .portal-theme-switch__icon:first-child,
        .portal-theme-switch input:checked + .portal-theme-switch__track .portal-theme-switch__icon:nth-child(2) {
            color: var(--primary, #2563eb);
        }
        .portal-theme-switch input:focus-visible + .portal-theme-switch__track {
            outline: 3px solid color-mix(in srgb, #fff 46%, transparent);
            outline-offset: 3px;
        }
        footer { text-align: center; padding: 2.5rem 1.5rem 3.5rem; color: var(--muted); font-size: 0.92rem; }
        .footer-brand { display: flex; flex-direction: column; align-items: center; gap: 12px; margin-bottom: 1rem; }
        .footer-brand img { max-height: 65px; width: auto; display: block; }
        .portal-app { width: min(100% - 28px, 1580px) !important; max-width: 1580px !important; margin-left: auto !important; margin-right: auto !important; }
        .portal-app__panels, .portal-panel, .portal-catalog-card, .portal-cart-card { min-width: 0; }
        .portal-catalog-tools { display: grid; grid-template-columns: minmax(180px, 1fr) minmax(140px, 220px) minmax(150px, 230px); gap: 0.85rem; margin: 0.9rem 0 1rem; align-items: end; }
        .portal-catalog-tools__field { display: flex; flex-direction: column; gap: 0.35rem; }
        .portal-catalog-tools label { display: inline-flex; align-items: center; gap: 0.42rem; margin: 0; font-size: 0.72rem; font-weight: 900; letter-spacing: 0.05em; text-transform: uppercase; color: var(--primary); }
        .portal-catalog-tools__icon { width: 16px; height: 16px; display: inline-flex; align-items: center; justify-content: center; color: var(--primary); }
        .portal-catalog-tools__icon svg { width: 16px; height: 16px; stroke: currentColor; }
        .portal-catalog-tools__control { position: relative; display: block; }
        .portal-catalog-tools__control-icon { position: absolute; left: 0.86rem; top: 50%; width: 18px; height: 18px; transform: translateY(-50%); color: var(--primary); pointer-events: none; }
        .portal-catalog-tools__control-icon svg { width: 18px; height: 18px; stroke: currentColor; }
        .portal-catalog-tools input, .portal-catalog-tools select { min-height: 48px; padding: 0.72rem 0.9rem 0.72rem 2.75rem; font-size: 0.94rem; font-weight: 750; border-radius: 16px; border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 32%, var(--border, rgba(15,23,42,0.08))); background: linear-gradient(135deg, color-mix(in srgb, var(--primary, #2563eb) 8%, #fff), rgba(255,255,255,0.96)); color: #1e293b !important; -webkit-text-fill-color: #1e293b; caret-color: var(--primary, #2563eb); box-shadow: 0 18px 34px -30px color-mix(in srgb, var(--primary, #2563eb) 55%, transparent); }
        .portal-catalog-tools input::placeholder { color: #94a3b8 !important; -webkit-text-fill-color: #94a3b8; opacity: 1; }
        .portal-catalog-tools select option { background: #ffffff; color: #1e293b; }
        html[data-theme="dark"] body .portal-catalog-tools input,
        html[data-theme="dark"] body .portal-catalog-tools select { background: #fffaf7; border-color: color-mix(in srgb, var(--primary, #2563eb) 38%, #cbd5e1); color: #172033 !important; -webkit-text-fill-color: #172033; }
        html[data-theme="dark"] body .portal-catalog-tools input::placeholder { color: #7c8799 !important; -webkit-text-fill-color: #7c8799; }
        input[type="search"]::-webkit-search-decoration, input[type="search"]::-webkit-search-cancel-button, input[type="search"]::-webkit-search-results-button, input[type="search"]::-webkit-search-results-decoration { -webkit-appearance: none; appearance: none; display: none; }
        .portal-catalog-tools .search-clear-host input,
        .search-clear-host input[type="search"],
        .search-clear-host input.has-search-clear-button { padding-right: 42px !important; }
        .search-clear-host { position: relative !important; }
        .search-clear-button { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); display: inline-grid; place-items: center; width: 24px; height: 24px; min-width: 24px; min-height: 24px; padding: 0; border: 0; border-radius: 50%; background: color-mix(in srgb, var(--primary, #2563eb) 10%, transparent); color: var(--primary, #2563eb); cursor: pointer; opacity: 0; pointer-events: none; transition: opacity 0.15s ease, background 0.15s ease, color 0.15s ease; z-index: 5; box-shadow: none; }
        .search-clear-button.is-visible { opacity: 1; pointer-events: auto; }
        .search-clear-button:hover, .search-clear-button:focus-visible { background: var(--primary, #2563eb); color: #fff; outline: 0; transform: translateY(-50%); box-shadow: none; }
        .search-clear-button svg { width: 13px; height: 13px; stroke: currentColor; }
        .portal-catalog-tools select { appearance: none; padding-right: 2.35rem; cursor: pointer; }
        .portal-catalog-tools__select::after { content: ''; position: absolute; right: 0.92rem; top: 50%; width: 9px; height: 9px; border-right: 2px solid var(--primary); border-bottom: 2px solid var(--primary); transform: translateY(-64%) rotate(45deg); pointer-events: none; }
        .portal-catalog-tools input:focus, .portal-catalog-tools select:focus { outline: 3px solid color-mix(in srgb, var(--primary, #2563eb) 18%, transparent); border-color: var(--primary); box-shadow: 0 20px 42px -28px color-mix(in srgb, var(--primary, #2563eb) 70%, transparent); }
        .portal-catalog-empty-filter { display: none; margin: 0.5rem 0 0; padding: 0.8rem 0.95rem; border: 1px dashed var(--border); border-radius: 12px; color: var(--muted); font-size: 0.9rem; font-weight: 600; }
        .portal-catalog-empty-filter.is-visible { display: block; }
        .portal-catalog { grid-template-columns: repeat(auto-fill, minmax(178px, 1fr)) !important; gap: 0.95rem !important; }
        .portal-product { min-width: 0; border-radius: 14px !important; padding: 0.9rem !important; gap: 0.38rem !important; font-size: 0.74rem; }
        .portal-product__header { gap: 0.45rem !important; }
        .portal-product h3 { font-size: 0.86rem !important; line-height: 1.18 !important; margin: 0 !important; }
        .portal-product small, .portal-product__meta, .portal-product__label { font-size: 0.66rem !important; line-height: 1.18 !important; }
        .portal-product .badge { padding: 0.18rem 0.48rem !important; font-size: 0.62rem !important; }
        .portal-product__price { font-size: 0.92rem !important; line-height: 1.1 !important; }
        .portal-product__footer, .portal-product__controls, .portal-product__input-group { gap: 0.42rem !important; }
        .portal-product input[data-qtd] { min-height: 38px !important; padding: 0.48rem 0.55rem !important; font-size: 0.86rem !important; border-radius: 10px !important; }
        .portal-product button[data-add] { width: 42px !important; height: 42px !important; min-width: 42px !important; min-height: 42px !important; flex: 0 0 42px !important; display: inline-grid !important; grid-template-columns: 1fr !important; place-items: center !important; position: relative !important; overflow: visible !important; padding: 0 !important; border-radius: 999px !important; font-size: 0 !important; line-height: 0 !important; background: linear-gradient(135deg, var(--primary), #1d4ed8) !important; box-shadow: 0 16px 26px -18px rgba(37, 99, 235, 0.9) !important; }
        .portal-product button[data-add] .portal-add-cart-icon { width: 20px; height: 20px; color: #fff; }
        .portal-product button[data-add] .portal-add-cart-plus { position: absolute; top: -5px; right: -5px; width: 18px; height: 18px; display: grid; place-items: center; border-radius: 50%; background: #fff; color: var(--primary); border: 2px solid var(--primary); font-size: 0.82rem; line-height: 1; font-weight: 900; }
        .portal-product button[data-add]:hover { transform: translateY(-1px) scale(1.04) !important; box-shadow: 0 20px 34px -18px rgba(37, 99, 235, 0.95) !important; }
        .portal-cart-table { table-layout: fixed; }
        .portal-cart-table th:nth-child(1), .portal-cart-table tbody td:nth-child(1) { width: 31%; }
        .portal-cart-table th:nth-child(2), .portal-cart-table tbody td:nth-child(2) { width: 19%; transform: translateX(9px); }
        .portal-cart-table th:nth-child(3), .portal-cart-table tbody td:nth-child(3) { width: 21%; }
        .portal-cart-table th:nth-child(4), .portal-cart-table tbody td:nth-child(4) { width: 24%; }
        .portal-cart-table tfoot td:first-child { width: 48%; }
        .portal-cart-table tfoot td:last-child { text-align: right; white-space: nowrap; }
        #portal-cart-total { display: block; transform: translateX(-24px); }
        .portal-cart-table .portal-cart-remove-cell { width: 44px; min-width: 44px; text-align: right; }
        .portal-cart-remove { width: 34px; height: 34px; min-width: 34px; min-height: 34px; display: inline-grid; place-items: center; padding: 0; border-radius: 999px; background: rgba(239, 68, 68, 0.12); color: #dc2626; font-size: 0; line-height: 0; box-shadow: none; }
        .portal-cart-remove svg { width: 12.6px; height: 12.6px; stroke: currentColor; }
        .portal-cart-remove:hover { transform: translateY(-1px) scale(1.03); background: rgba(239, 68, 68, 0.18); box-shadow: 0 12px 24px -18px rgba(220, 38, 38, 0.85); }
        @media (max-width: 720px) { .portal-app { width: min(100% - 18px, 100%) !important; } .portal-catalog-tools { grid-template-columns: 1fr; } .portal-catalog { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)) !important; } }
        @media (min-width: 1024px) { .portal-app__grid { align-items: start; grid-template-columns: minmax(0, 1fr) minmax(375px, 425px) !important; } .portal-cart-stack { position: sticky; top: 1.25rem; align-self: start; display: flex; flex-direction: column; max-height: calc(100vh - 2.5rem); min-height: 0; } .portal-cart-card { display: flex; flex-direction: column; max-height: calc(100vh - 7rem); min-height: 0; overflow: hidden; font-size: 0.88rem; line-height: 1.3; padding: 1.15rem !important; } .portal-cart-card h3 { font-size: 1.08rem; line-height: 1.2; margin: 0 0 0.6rem; } .portal-cart-table { display: flex; flex: 1 1 auto; flex-direction: column; min-height: 0; width: 100%; } .portal-cart-table thead, .portal-cart-table tfoot { display: table; width: 100%; table-layout: fixed; flex: 0 0 auto; } .portal-cart-table th { font-size: 0.68rem; letter-spacing: 0.04em; padding: 0.45rem 0.35rem !important; } .portal-cart-table td { font-size: 0.82rem; line-height: 1.25; padding: 0.52rem 0.35rem !important; } .portal-cart-table tbody td:first-child { font-size: 0.78rem; font-weight: 600; } .portal-cart-table tfoot td { font-size: 0.9rem; font-weight: 700; } .portal-cart-empty { font-size: 0.82rem !important; } .portal-cart-table tbody { display: block; flex: 1 1 auto; max-height: min(52vh, 460px); min-height: 0; overflow-y: auto; overscroll-behavior: contain; padding-right: 4px; scrollbar-width: thin; } .portal-cart-table tbody tr { display: table; width: 100%; table-layout: fixed; } .portal-cart-table th, .portal-cart-table td { vertical-align: middle; } .portal-cart-actions { flex: 0 0 auto; } .portal-cart-actions .portal-btn, #btn-finalizar { font-size: 0.88rem; padding: 0.72rem 1rem; } }
        @media (min-width: 1400px) { .portal-app__grid { grid-template-columns: minmax(0, 1fr) minmax(388px, 438px) !important; } .portal-catalog { grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)) !important; } }
        html body .topbar { background: var(--primary, #2563eb) !important; color: var(--primary-contrast, #fff) !important; border-bottom: none !important; }
        html body .topbar strong { color: var(--primary-contrast, #fff) !important; }
    </style>
</head>
<body>
    <header class="topbar"><div class="portal-header-brand"><img class="portal-header-logo portal-header-logo--dark" src="<?= $headerLogoDark; ?>" alt="<?= htmlspecialchars($appName ?? 'IndusOS Food', ENT_QUOTES, 'UTF-8'); ?>"><strong class="portal-header-brand__fallback" hidden><?= htmlspecialchars($appName ?? 'IndusOS Food', ENT_QUOTES, 'UTF-8'); ?></strong></div><label class="theme-toggle portal-theme-switch" for="toggle-theme"><span>Alternar tema</span><input type="checkbox" id="toggle-theme" aria-label="Alternar entre tema claro e escuro"><span class="portal-theme-switch__track" aria-hidden="true"><span class="portal-theme-switch__icon">&#9788;</span><span class="portal-theme-switch__icon">&#9790;</span><span class="portal-theme-switch__thumb"></span></span></label></header>
    <?php if (!empty($flash['success'])): ?><div class="container"><div class="alert alert-success"><?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div></div><?php endif; ?>
    <?php if (!empty($flash['error'])): ?><div class="container"><div class="alert alert-error"><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div></div><?php endif; ?>
    <?= $content ?? '' ?>
    <footer role="contentinfo"><div class="footer-brand"><img src="<?= $footerLogo; ?>" alt="Logotipo rodapé IndusOS" loading="lazy"></div><div>INDUS•OS © <?= $currentYear; ?> • Todos os direitos reservados<br><a href="https://mystic-designer.com.br/" target="_blank" rel="noopener noreferrer" style="color: inherit; text-decoration: none;"><span>Created by </span><span style="color: var(--primary); font-weight: 700;">Mystic Designer®</span></a></div></footer>
    <script>
        const storedTheme = localStorage.getItem('portal-theme');
        const root = document.documentElement;
        const toggle = document.getElementById('toggle-theme');
        if (storedTheme === 'dark') { root.setAttribute('data-theme', 'dark'); toggle.checked = true; }
        const applyGlobalZoom = () => { const width = window.innerWidth; const height = window.innerHeight; const shouldZoom = width >= 900 && width <= 1366 && height <= 768; document.documentElement.style.zoom = shouldZoom ? '0.87' : ''; };
        applyGlobalZoom();
        window.addEventListener('resize', applyGlobalZoom);
        toggle?.addEventListener('change', () => { const mode = toggle.checked ? 'dark' : 'light'; root.setAttribute('data-theme', mode); localStorage.setItem('portal-theme', mode); });

        const installSearchClearButtons = (rootNode = document) => {
            const selector = 'input[type="search"], input[id*="search" i], input[name="q"], .portal-catalog-tools input';
            rootNode.querySelectorAll?.(selector).forEach((input) => {
                if (!(input instanceof HTMLInputElement) || input.dataset.searchClearReady === '1') return;
                if (input.type === 'hidden' || input.type === 'password' || input.readOnly || input.disabled) return;
                const placeholder = String(input.getAttribute('placeholder') || '').toLowerCase();
                const id = String(input.id || '').toLowerCase();
                const name = String(input.name || '').toLowerCase();
                const isSearch = input.type === 'search'
                    || id.includes('search')
                    || id.includes('busca')
                    || name === 'q'
                    || placeholder.includes('buscar')
                    || placeholder.includes('produto');
                if (!isSearch || !input.parentElement) return;
                input.dataset.searchClearReady = '1';
                input.classList.add('has-search-clear-button');
                const host = input.parentElement;
                host.classList.add('search-clear-host');
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'search-clear-button';
                button.setAttribute('aria-label', 'Limpar busca');
                button.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>';
                const sync = () => button.classList.toggle('is-visible', input.value.trim() !== '');
                button.addEventListener('click', () => {
                    input.value = '';
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    input.focus();
                    sync();
                });
                input.addEventListener('input', sync);
                input.addEventListener('change', sync);
                host.appendChild(button);
                sync();
            });
        };

        installSearchClearButtons();
        new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node instanceof Element) installSearchClearButtons(node);
                });
            });
        }).observe(document.body, { childList: true, subtree: true });

        (() => {
            const style = document.createElement('style');
            style.textContent = `
                .portal-cart-table {
                    table-layout: fixed !important;
                }
                .portal-cart-table th:nth-child(1),
                .portal-cart-table tbody td:nth-child(1) {
                    width: 31% !important;
                }
                .portal-cart-table th:nth-child(2),
                .portal-cart-table tbody td:nth-child(2) {
                    width: 19% !important;
                    padding-left: calc(0.35rem + 9px) !important;
                    transform: none !important;
                }
                .portal-cart-table th:nth-child(3),
                .portal-cart-table tbody td:nth-child(3) {
                    width: 21% !important;
                }
                .portal-cart-table th:nth-child(4),
                .portal-cart-table tbody td:nth-child(4) {
                    width: 24% !important;
                }
                .portal-cart-table .portal-cart-remove-cell {
                    width: 44px !important;
                    min-width: 44px !important;
                    padding-left: 0 !important;
                    padding-right: 0.5px !important;
                    text-align: right !important;
                }
                .portal-cart-remove {
                    width: 34px !important;
                    height: 34px !important;
                    min-width: 34px !important;
                    min-height: 34px !important;
                    margin-left: auto !important;
                    margin-right: 0 !important;
                }
                .portal-cart-remove svg {
                    width: 12.6px !important;
                    height: 12.6px !important;
                }
                .portal-cart-table tfoot td:first-child {
                    width: 48% !important;
                }
                .portal-cart-table tfoot td:last-child {
                    padding-right: 3.2rem !important;
                    text-align: right !important;
                    white-space: nowrap !important;
                    overflow: visible !important;
                }
                #portal-cart-total {
                    display: block !important;
                    transform: translateX(-24px) !important;
                    max-width: calc(100% - 3.2rem) !important;
                    white-space: nowrap !important;
                }
            `;
            document.head.appendChild(style);
        })();

        (() => {
            const cartIconMarkup = `<span class="portal-add-cart-plus" aria-hidden="true">+</span><svg class="portal-add-cart-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h8.42a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>`;
            const decorateCartButtons = () => { document.querySelectorAll('.portal-product button[data-add]').forEach((button) => { if (button.dataset.iconReady === '1') return; button.dataset.iconReady = '1'; button.setAttribute('aria-label', 'Adicionar ao carrinho'); button.setAttribute('title', 'Adicionar ao carrinho'); button.innerHTML = cartIconMarkup; }); };
            const start = () => { decorateCartButtons(); new MutationObserver(decorateCartButtons).observe(document.body, { childList: true, subtree: true }); };
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start); else start();
        })();

        (() => {
            const removeIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>';
            const getCart = () => { try { return Array.isArray(cart) ? cart : null; } catch (error) { return null; } };
            const getUpdater = () => { try { return typeof atualizarCarrinho === 'function' ? atualizarCarrinho : null; } catch (error) { return null; } };
            const ensureRemoveHeader = () => {
                const table = document.querySelector('.portal-cart-table');
                const headRow = table?.querySelector('thead tr');
                if (!headRow || headRow.querySelector('[data-cart-remove-head]')) return;
                const th = document.createElement('th');
                th.className = 'portal-cart-remove-cell';
                th.setAttribute('data-cart-remove-head', '1');
                th.setAttribute('aria-label', 'Remover');
                th.textContent = '';
                headRow.appendChild(th);
            };
            const decorateCartRows = () => {
                const cartData = getCart();
                const lines = document.getElementById('portal-cart-lines');
                if (!cartData || !lines) return;
                ensureRemoveHeader();
                lines.querySelectorAll('tr').forEach((row, index) => {
                    if (row.querySelector('.portal-cart-empty')) return;
                    let cell = row.querySelector('.portal-cart-remove-cell');
                    if (!cell) {
                        cell = document.createElement('td');
                        cell.className = 'portal-cart-remove-cell';
                        row.appendChild(cell);
                    }
                    const existing = cell.querySelector('[data-cart-remove]');
                    if (existing && existing.getAttribute('data-cart-remove') === String(index)) return;
                    cell.innerHTML = `<button type="button" class="portal-cart-remove" data-cart-remove="${index}" aria-label="Remover produto" title="Remover produto">${removeIcon}</button>`;
                });
            };
            document.addEventListener('click', (event) => {
                const button = event.target instanceof Element ? event.target.closest('[data-cart-remove]') : null;
                if (!button) return;
                event.preventDefault();
                event.stopPropagation();
                const cartData = getCart();
                const updater = getUpdater();
                const index = Number(button.getAttribute('data-cart-remove'));
                if (!cartData || !updater || !Number.isInteger(index) || index < 0 || index >= cartData.length) return;
                cartData.splice(index, 1);
                updater();
                window.setTimeout(decorateCartRows, 0);
            });
            const start = () => {
                decorateCartRows();
                const lines = document.getElementById('portal-cart-lines');
                if (lines) new MutationObserver(decorateCartRows).observe(lines, { childList: true });
            };
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start); else start();
        })();

        (() => {
            const prepareQtyFields = () => { document.querySelectorAll('input[data-qtd]').forEach((input) => { input.min = '0'; input.placeholder = '0'; if (input.value === '') input.value = '0'; }); };
            document.addEventListener('click', (event) => { const button = event.target instanceof Element ? event.target.closest('button[data-add]') : null; if (!button) return; const productId = button.getAttribute('data-add'); const card = button.closest('.portal-product'); const input = card ? card.querySelector(`input[data-qtd="${productId}"]`) : null; if (!(input instanceof HTMLInputElement)) return; const quantity = parseFloat(String(input.value || '').replace(',', '.')); if (!Number.isFinite(quantity) || quantity <= 0) { event.preventDefault(); event.stopImmediatePropagation(); input.value = '0'; input.dispatchEvent(new Event('input', { bubbles: true })); input.focus(); } }, true);
            const observer = new MutationObserver(prepareQtyFields);
            const start = () => { prepareQtyFields(); observer.observe(document.body, { childList: true, subtree: true }); };
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start); else start();
        })();

        (() => {
            const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
            const installCatalogTools = () => {
                const catalog = document.getElementById('portal-catalog');
                if (!catalog || catalog.dataset.filterToolsReady === '1') return;
                catalog.dataset.filterToolsReady = '1';
                const tools = document.createElement('div');
                tools.className = 'portal-catalog-tools';
                tools.innerHTML = `<div class="portal-catalog-tools__field"><label for="portal-product-search"><span class="portal-catalog-tools__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="7"></circle></svg></span><span>Buscar produto</span></label><span class="portal-catalog-tools__control"><span class="portal-catalog-tools__control-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="7"></circle></svg></span><input id="portal-product-search" type="search" placeholder="Nome, SKU ou unidade"></span></div><div class="portal-catalog-tools__field"><label for="portal-product-filter"><span class="portal-catalog-tools__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg></span><span>Categoria</span></label><span class="portal-catalog-tools__control portal-catalog-tools__select"><span class="portal-catalog-tools__control-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9"></path><path d="M14 17H5"></path><circle cx="17" cy="17" r="3"></circle><circle cx="7" cy="7" r="3"></circle></svg></span><select id="portal-product-filter"><option value="">Todos</option></select></span></div><div class="portal-catalog-tools__field"><label for="portal-product-sort"><span class="portal-catalog-tools__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10"></path><path d="M7 12h7"></path><path d="M7 17h4"></path><path d="M17 14v6"></path><path d="m14 17 3 3 3-3"></path></svg></span><span>Ordenar</span></label><span class="portal-catalog-tools__control portal-catalog-tools__select"><span class="portal-catalog-tools__control-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg></span><select id="portal-product-sort"><option value="name-asc">Nome A-Z</option><option value="name-desc">Nome Z-A</option><option value="price-asc">Menor preço</option><option value="price-desc">Maior preço</option><option value="validity-desc">Maior validade</option><option value="validity-asc">Menor validade</option><option value="sku-asc">SKU A-Z</option></select></span></div>`;
                catalog.parentNode.insertBefore(tools, catalog);
                const empty = document.createElement('p');
                empty.className = 'portal-catalog-empty-filter';
                empty.textContent = 'Nenhum produto encontrado com esse filtro.';
                catalog.parentNode.insertBefore(empty, catalog.nextSibling);
                const search = tools.querySelector('#portal-product-search');
                const filter = tools.querySelector('#portal-product-filter');
                const sort = tools.querySelector('#portal-product-sort');
                const availableProducts = () => {
                    const cliente = state?.cliente || {};
                    const groupIds = Array.isArray(cliente.grupos_produtos_ids) && cliente.grupos_produtos_ids.length
                        ? cliente.grupos_produtos_ids.map(String)
                        : [String(cliente.grupo_produtos_id || '')].filter(Boolean);
                    const matchedGroups = portalClientGroups.filter((item) => groupIds.includes(String(item?.id || '')));
                    const allowed = Array.isArray(cliente.grupo_produtos_product_ids)
                        ? new Set(cliente.grupo_produtos_product_ids.map(Number).filter((id) => id > 0))
                        : (matchedGroups.length
                            ? new Set(matchedGroups.flatMap((group) => (Array.isArray(group.product_ids) ? group.product_ids : []).map(Number).filter((id) => id > 0)))
                            : null);
                    return catalogoCompleto.filter((product) => !allowed || allowed.has(Number(product.id)));
                };
                const updateFilterOptions = () => {
                    const selected = filter.value;
                    const categories = new Map();
                    availableProducts().forEach((product) => {
                        const label = String(product.categoria || '').trim();
                        if (label) categories.set(normalize(label), label);
                    });
                    filter.innerHTML = '<option value="">Todos</option>';
                    Array.from(categories.entries()).sort((a, b) => a[1].localeCompare(b[1], 'pt-BR')).forEach(([value, label]) => {
                        const option = document.createElement('option');
                        option.value = value;
                        option.textContent = label;
                        filter.appendChild(option);
                    });
                    filter.value = Array.from(categories.keys()).includes(selected) ? selected : '';
                };
                const applyFilter = () => {
                    const term = normalize(search.value);
                    const category = filter.value;
                    const [key, direction] = (sort.value || 'name-asc').split('-');
                    const multiplier = direction === 'desc' ? -1 : 1;
                    const filtered = availableProducts().filter((product) => {
                        const searchable = normalize(`${product.nome || ''} ${product.sku || ''} ${product.unidade || ''} ${product.categoria || ''}`);
                        return (!term || searchable.includes(term))
                            && (!category || normalize(product.categoria || '') === category);
                    });
                    filtered.sort((a, b) => {
                        if (key === 'price' || key === 'validity') {
                            const field = key === 'price' ? 'preco' : 'shelf_life_dias';
                            return ((Number(a[field] || 0) - Number(b[field] || 0)) * multiplier)
                                || normalize(a.nome).localeCompare(normalize(b.nome), 'pt-BR');
                        }
                        const field = key === 'sku' ? 'sku' : 'nome';
                        return normalize(a[field]).localeCompare(normalize(b[field]), 'pt-BR') * multiplier;
                    });
                    catalogo.splice(0, catalogo.length, ...filtered);
                    catalogPage = 1;
                    desenharCatalogo(true);
                    empty.classList.toggle('is-visible', filtered.length === 0);
                };
                search.addEventListener('input', applyFilter);
                filter.addEventListener('change', applyFilter);
                sort.addEventListener('change', applyFilter);
                window.addEventListener('portal:cliente-atualizado', () => { updateFilterOptions(); applyFilter(); });
                new MutationObserver(() => {
                    if (document.body.getAttribute('data-portal-state') === 'app') {
                        updateFilterOptions();
                        applyFilter();
                    }
                }).observe(document.body, { attributes: true, attributeFilter: ['data-portal-state'] });
                updateFilterOptions();
                applyFilter();
            };
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', installCatalogTools); else installCatalogTools();
        })();
    </script>
</body>
</html>
