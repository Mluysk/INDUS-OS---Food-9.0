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
$footerLogoPath = $theme['logo_footer'] ?? 'img/rodape/logo.png';
$footerLogo = htmlspecialchars($asset($footerLogoPath), ENT_QUOTES, 'UTF-8');
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
        :root { color-scheme: light dark; font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; --bg: #f5f5f5; --text: #1f2933; --muted: #52606d; --primary: #2563eb; --primary-contrast: #fff; --card: #ffffffd9; --border: rgba(15,23,42,0.08); }
        [data-theme="dark"] { --bg: #0f172a; --text: #e2e8f0; --muted: #94a3b8; --primary: #3b82f6; --card: rgba(15,23,42,0.8); --border: rgba(148,163,184,0.16); }
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
        .topbar { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; }
        .theme-toggle { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.95rem; color: var(--muted); }
        .theme-toggle input { width: auto; }
        footer { text-align: center; padding: 2.5rem 1.5rem 3.5rem; color: var(--muted); font-size: 0.92rem; }
        .footer-brand { display: flex; flex-direction: column; align-items: center; gap: 12px; margin-bottom: 1rem; }
        .footer-brand img { max-height: 65px; width: auto; display: block; }
        .portal-app { width: min(100% - 28px, 1580px) !important; max-width: 1580px !important; margin-left: auto !important; margin-right: auto !important; }
        .portal-app__panels, .portal-panel, .portal-catalog-card, .portal-cart-card { min-width: 0; }
        .portal-catalog-tools { display: grid; grid-template-columns: minmax(180px, 1fr) minmax(140px, 220px); gap: 0.75rem; margin: 0.9rem 0 1rem; align-items: end; }
        .portal-catalog-tools__field { display: flex; flex-direction: column; gap: 0.28rem; }
        .portal-catalog-tools label { margin: 0; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; color: var(--muted); }
        .portal-catalog-tools input, .portal-catalog-tools select { min-height: 42px; padding: 0.62rem 0.75rem; font-size: 0.9rem; border-radius: 12px; }
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
    </style>
</head>
<body>
    <header class="topbar"><div><strong><?= htmlspecialchars($appName ?? 'IndusOS Food', ENT_QUOTES, 'UTF-8'); ?></strong></div><label class="theme-toggle"><input type="checkbox" id="toggle-theme">Tema escuro</label></header>
    <?php if (!empty($flash['success'])): ?><div class="container"><div class="alert alert-success"><?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div></div><?php endif; ?>
    <?php if (!empty($flash['error'])): ?><div class="container"><div class="alert alert-error"><?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div></div><?php endif; ?>
    <?= $content ?? '' ?>
    <footer role="contentinfo"><div class="footer-brand"><img src="<?= $footerLogo; ?>" alt="Logotipo rodapé IndusOS" loading="lazy"></div><div>INDUS•OS © <?= $currentYear; ?> • Todos os direitos reservados<br><a href="https://mystic-designer.com.br/" target="_blank" rel="noopener noreferrer"><span>Created by </span><span style="color: var(--primary); font-weight: 700;">Mystic Designer®</span></a></div></footer>
    <script>
        const storedTheme = localStorage.getItem('portal-theme');
        const root = document.documentElement;
        const toggle = document.getElementById('toggle-theme');
        if (storedTheme === 'dark') { root.setAttribute('data-theme', 'dark'); toggle.checked = true; }
        const applyGlobalZoom = () => { const width = window.innerWidth; const height = window.innerHeight; const shouldZoom = width >= 900 && width <= 1366 && height <= 768; document.documentElement.style.zoom = shouldZoom ? '0.87' : ''; };
        applyGlobalZoom();
        window.addEventListener('resize', applyGlobalZoom);
        toggle?.addEventListener('change', () => { const mode = toggle.checked ? 'dark' : 'light'; root.setAttribute('data-theme', mode); localStorage.setItem('portal-theme', mode); });

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
            const resetQtyFields = () => { document.querySelectorAll('input[data-qtd]').forEach((input) => { input.min = '0'; input.placeholder = '0'; if (input.dataset.portalQtyTouched !== '1') input.value = '0'; }); };
            document.addEventListener('input', (event) => { const input = event.target; if (input instanceof HTMLInputElement && input.matches('input[data-qtd]')) input.dataset.portalQtyTouched = '1'; });
            document.addEventListener('click', (event) => { const button = event.target instanceof Element ? event.target.closest('button[data-add]') : null; if (!button) return; const productId = button.getAttribute('data-add'); const card = button.closest('.portal-product'); const input = card ? card.querySelector(`input[data-qtd="${productId}"]`) : null; if (!(input instanceof HTMLInputElement)) return; const quantity = parseFloat(String(input.value || '0').replace(',', '.')); if (!Number.isFinite(quantity) || quantity <= 0) { event.preventDefault(); event.stopImmediatePropagation(); input.value = '0'; input.dataset.portalQtyTouched = '0'; input.focus(); return; } window.setTimeout(() => { input.value = '0'; input.dataset.portalQtyTouched = '0'; }, 0); }, true);
            document.addEventListener('click', (event) => { const button = event.target instanceof Element ? event.target.closest('#btn-finalizar') : null; if (button) window.setTimeout(resetQtyFields, 1200); });
            const observer = new MutationObserver(resetQtyFields);
            const start = () => { resetQtyFields(); observer.observe(document.body, { childList: true, subtree: true }); };
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
                tools.innerHTML = `<div class="portal-catalog-tools__field"><label for="portal-product-search">Buscar produto</label><input id="portal-product-search" type="search" placeholder="Nome, SKU ou unidade"></div><div class="portal-catalog-tools__field"><label for="portal-product-filter">Filtro</label><select id="portal-product-filter"><option value="">Todos</option></select></div>`;
                catalog.parentNode.insertBefore(tools, catalog);
                const empty = document.createElement('p');
                empty.className = 'portal-catalog-empty-filter';
                empty.textContent = 'Nenhum produto encontrado com esse filtro.';
                catalog.parentNode.insertBefore(empty, catalog.nextSibling);
                const search = tools.querySelector('#portal-product-search');
                const filter = tools.querySelector('#portal-product-filter');
                const updateFilterOptions = () => { const selected = filter.value; const types = new Map(); catalog.querySelectorAll('.portal-product .badge').forEach((badge) => { const label = badge.textContent.trim(); if (label) types.set(normalize(label), label); }); filter.innerHTML = '<option value="">Todos</option>'; Array.from(types.entries()).sort((a, b) => a[1].localeCompare(b[1], 'pt-BR')).forEach(([value, label]) => { const option = document.createElement('option'); option.value = value; option.textContent = label; filter.appendChild(option); }); filter.value = Array.from(types.keys()).includes(selected) ? selected : ''; };
                const applyFilter = () => { const term = normalize(search.value); const type = filter.value; let visible = 0; catalog.querySelectorAll('.portal-product').forEach((card) => { const badge = card.querySelector('.badge'); const matchesText = !term || normalize(card.textContent).includes(term); const matchesType = !type || normalize(badge ? badge.textContent : '') === type; const show = matchesText && matchesType; card.style.display = show ? '' : 'none'; if (show) visible += 1; }); empty.classList.toggle('is-visible', visible === 0); };
                search.addEventListener('input', applyFilter);
                filter.addEventListener('change', applyFilter);
                new MutationObserver(() => { updateFilterOptions(); applyFilter(); }).observe(catalog, { childList: true, subtree: true });
                updateFilterOptions();
                applyFilter();
            };
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', installCatalogTools); else installCatalogTools();
        })();
    </script>
</body>
</html>
