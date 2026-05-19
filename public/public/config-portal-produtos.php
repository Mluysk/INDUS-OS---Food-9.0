<?php
declare(strict_types=1);

$bootstrap = require __DIR__.'/../application/bootstrap.php';

use App\Services\AuthService;
use App\Services\ConfigService;
use App\Services\ProdutosService;

/** @var AuthService $auth */
$auth = $bootstrap['container']->get(AuthService::class);
$auth->ensureRole(['admin']);

/** @var ConfigService $config */
$config = $bootstrap['container']->get(ConfigService::class);
/** @var ProdutosService $produtosService */
$produtosService = $bootstrap['container']->get(ProdutosService::class);

$basePath = rtrim($_SERVER['APP_BASE_PATH'] ?? '', '/');
$url = static function (string $path = '') use ($basePath): string {
    $path = ltrim($path, '/');
    return ($basePath === '' ? '' : $basePath).($path === '' ? '/' : '/'.$path);
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['portal_products'] ?? [];
    if (!is_array($ids)) {
        $ids = [];
    }

    $cleanIds = array_values(array_unique(array_filter(array_map(static fn($id): int => (int)$id, $ids), static fn(int $id): bool => $id > 0)));
    $config->set('PORTAL_PRODUCT_IDS', implode(',', $cleanIds));
    $_SESSION['flash_success'] = 'Produtos do portal atualizados com sucesso.';
    header('Location: '.$url('config-portal-produtos.php'));
    exit;
}

$selectedRaw = (string)$config->get('PORTAL_PRODUCT_IDS', '');
$selectedIds = array_values(array_filter(array_map(static fn($id): int => (int)trim($id), explode(',', $selectedRaw)), static fn(int $id): bool => $id > 0));
$selectedMap = array_flip($selectedIds);

$products = array_values(array_filter($produtosService->all(['tipo' => 'PA']), static fn(array $produto): bool => strtoupper((string)($produto['tipo'] ?? 'PA')) === 'PA'));
usort($products, static function (array $a, array $b): int {
    return strcasecmp((string)($a['nome'] ?? ''), (string)($b['nome'] ?? ''));
});

$categories = [];
foreach ($products as $product) {
    $category = trim((string)($product['categoria'] ?? ''));
    if ($category !== '') {
        $categories[$category] = $category;
    }
}
sort($categories);
$user = $auth->user() ?? [];
$initials = '';
if (!empty($user['nome'])) {
    $parts = preg_split('/\s+/', trim((string)$user['nome']));
    $initials = strtoupper(substr($parts[0] ?? '', 0, 1).(isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Produtos do portal do cliente</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #df2429;
            --primary-dark: #c91f24;
            --bg: #f5f7fb;
            --surface: #ffffff;
            --surface-alt: #f8fafc;
            --border: rgba(15, 23, 42, 0.1);
            --text: #0f172a;
            --muted: #64748b;
            --shadow: 0 28px 70px -42px rgba(15, 23, 42, 0.55);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text);
            background: radial-gradient(circle at top left, rgba(223, 36, 41, 0.08), transparent 32%), var(--bg);
        }
        .wrap { width: min(1180px, calc(100% - 40px)); margin: 0 auto; padding: 28px 0 46px; }
        .topbar, .card { background: rgba(255, 255, 255, 0.94); border: 1px solid var(--border); box-shadow: var(--shadow); }
        .topbar { min-height: 76px; border-radius: 22px; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 22px; margin-bottom: 34px; }
        .crumbs { display: flex; align-items: center; gap: 10px; color: var(--muted); font-size: 0.95rem; }
        .crumbs strong { color: var(--text); }
        .top-actions { display: flex; align-items: center; gap: 12px; }
        .btn, .icon-btn { border: 0; border-radius: 16px; cursor: pointer; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 46px; padding: 0 18px; }
        .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 18px 32px -22px var(--primary); }
        .btn-light { background: #f1f5f9; color: var(--text); }
        .avatar { width: 48px; height: 48px; border-radius: 50%; background: #fee2e2; color: var(--primary-dark); display: grid; place-items: center; font-weight: 800; }
        .hero { display: flex; align-items: end; justify-content: space-between; gap: 18px; margin-bottom: 22px; }
        .title { display: flex; align-items: center; gap: 14px; margin: 0; font-size: clamp(2rem, 3vw, 3rem); letter-spacing: 0; }
        .title-icon { width: 50px; height: 50px; border-radius: 16px; background: var(--primary); color: #fff; display: grid; place-items: center; box-shadow: 0 18px 32px -20px var(--primary); }
        .subtitle { margin: 8px 0 0; color: var(--muted); font-size: 1.05rem; }
        .status-pill { background: #fff; border: 1px solid var(--border); border-radius: 999px; padding: 12px 18px; font-weight: 800; color: var(--muted); white-space: nowrap; }
        .card { border-radius: 24px; padding: 28px; }
        .flash { border: 1px solid rgba(22, 163, 74, 0.28); background: #dcfce7; color: #047857; border-radius: 16px; padding: 16px 18px; margin-bottom: 18px; font-weight: 800; }
        .toolbar { display: grid; grid-template-columns: minmax(220px, 1fr) minmax(180px, 280px) auto auto; gap: 12px; align-items: end; margin-bottom: 18px; }
        label { display: grid; gap: 8px; color: var(--muted); font-weight: 800; }
        input[type="search"], select { width: 100%; min-height: 52px; border: 1px solid rgba(148, 163, 184, 0.28); border-radius: 16px; padding: 0 16px; font: inherit; font-weight: 700; background: var(--surface-alt); color: var(--text); outline: none; }
        input[type="search"]:focus, select:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(223, 36, 41, 0.1); }
        .hint { background: #f8fafc; border: 1px solid var(--border); border-radius: 18px; padding: 16px 18px; color: var(--muted); font-weight: 700; margin-bottom: 18px; }
        .products { border: 1px solid var(--border); border-radius: 20px; overflow: hidden; max-height: 560px; overflow-y: auto; background: #fff; }
        .product-row { display: grid; grid-template-columns: 48px 132px minmax(200px, 1fr) 180px 110px; gap: 14px; align-items: center; padding: 14px 18px; border-bottom: 1px solid rgba(15, 23, 42, 0.07); }
        .product-row:last-child { border-bottom: 0; }
        .product-row:hover { background: #f8fafc; }
        .product-row input { width: 22px; height: 22px; accent-color: var(--primary); }
        .sku, .category, .price { color: var(--muted); font-weight: 800; }
        .name { font-weight: 900; }
        .empty { padding: 26px; color: var(--muted); font-weight: 800; text-align: center; }
        .footer-actions { position: sticky; bottom: 0; background: linear-gradient(180deg, rgba(255,255,255,0.72), #fff 36%); padding-top: 18px; margin-top: 18px; display: flex; align-items: center; justify-content: space-between; gap: 14px; }
        .counter { color: var(--muted); font-weight: 800; }
        @media (max-width: 820px) {
            .wrap { width: min(100% - 24px, 1180px); }
            .topbar, .hero, .toolbar, .footer-actions { grid-template-columns: 1fr; flex-direction: column; align-items: stretch; }
            .product-row { grid-template-columns: 42px 1fr; gap: 8px 12px; }
            .sku, .category, .price { grid-column: 2; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="topbar">
            <div class="crumbs"><span>Início</span><span>/</span><span>Configurações</span><span>/</span><strong>Portal do cliente</strong></div>
            <div class="top-actions">
                <a class="btn btn-light" href="<?= htmlspecialchars($url('config'), ENT_QUOTES, 'UTF-8'); ?>">Voltar</a>
                <div class="avatar" aria-label="Usuário logado"><?= htmlspecialchars($initials ?: 'A', ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        </div>

        <section class="hero">
            <div>
                <h1 class="title"><span class="title-icon" data-lucide="shopping-bag"></span>Produtos do portal</h1>
                <p class="subtitle">Escolha quais produtos aparecem para o cliente no portal. Se nada for marcado, todos os produtos PA continuam visíveis.</p>
            </div>
            <div class="status-pill" id="statusText"></div>
        </section>

        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="flash"><?= htmlspecialchars((string)$_SESSION['flash_success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['flash_success']); ?></div>
        <?php endif; ?>

        <form class="card" method="post" id="portalProductsForm">
            <div class="toolbar">
                <label>Buscar produto
                    <input type="search" id="searchInput" placeholder="Digite nome, SKU ou categoria">
                </label>
                <label>Categoria
                    <select id="categoryFilter">
                        <option value="">Todas</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button class="btn btn-light" type="button" id="selectVisible">Selecionar visíveis</button>
                <button class="btn btn-light" type="button" id="clearSelection">Limpar</button>
            </div>

            <div class="hint">Exemplo: para mostrar somente produtos da Alexandria, filtre pela categoria ou nome e deixe marcado apenas o grupo desejado antes de salvar.</div>

            <div class="products" id="productsList">
                <?php if (!$products): ?>
                    <div class="empty">Nenhum produto PA cadastrado.</div>
                <?php endif; ?>
                <?php foreach ($products as $product): ?>
                    <?php
                        $id = (int)($product['id'] ?? 0);
                        $name = (string)($product['nome'] ?? 'Produto sem nome');
                        $sku = (string)($product['sku'] ?? '-');
                        $category = (string)($product['categoria'] ?? 'Sem categoria');
                        $price = number_format((float)($product['preco'] ?? 0), 2, ',', '.');
                        $search = mb_strtolower($sku.' '.$name.' '.$category, 'UTF-8');
                    ?>
                    <label class="product-row" data-row data-search="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" data-category="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="checkbox" name="portal_products[]" value="<?= $id; ?>" <?= isset($selectedMap[$id]) ? 'checked' : ''; ?>>
                        <span class="sku"><?= htmlspecialchars($sku, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="name"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="category"><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="price">R$ <?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="footer-actions">
                <div class="counter" id="counterText"></div>
                <button class="btn btn-primary" type="submit">Salvar produtos do portal</button>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/lucide@0.371.0/dist/umd/lucide.min.js"></script>
    <script>
        (() => {
            const rows = Array.from(document.querySelectorAll('[data-row]'));
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const counterText = document.getElementById('counterText');
            const statusText = document.getElementById('statusText');
            const selectVisible = document.getElementById('selectVisible');
            const clearSelection = document.getElementById('clearSelection');
            const normalize = (value) => String(value || '').toLocaleLowerCase('pt-BR').normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            const refresh = () => {
                const term = normalize(searchInput.value);
                const category = categoryFilter.value;
                let visible = 0;
                rows.forEach((row) => {
                    const matchesTerm = normalize(row.dataset.search).includes(term);
                    const matchesCategory = !category || row.dataset.category === category;
                    const show = matchesTerm && matchesCategory;
                    row.style.display = show ? '' : 'none';
                    if (show) visible += 1;
                });
                const selected = rows.filter((row) => row.querySelector('input').checked).length;
                counterText.textContent = `${selected} produto${selected === 1 ? '' : 's'} selecionado${selected === 1 ? '' : 's'} | ${visible} visível${visible === 1 ? '' : 'eis'}`;
                statusText.textContent = selected > 0 ? `${selected} no portal` : 'Portal mostra todos';
            };

            searchInput.addEventListener('input', refresh);
            categoryFilter.addEventListener('change', refresh);
            rows.forEach((row) => row.querySelector('input').addEventListener('change', refresh));
            selectVisible.addEventListener('click', () => {
                rows.forEach((row) => {
                    if (row.style.display !== 'none') row.querySelector('input').checked = true;
                });
                refresh();
            });
            clearSelection.addEventListener('click', () => {
                rows.forEach((row) => row.querySelector('input').checked = false);
                refresh();
            });

            refresh();
            if (window.lucide) lucide.createIcons();
        })();
    </script>
</body>
</html>
