<?php
$menuItems = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => 'dashboard', 'icon' => 'layout-dashboard', 'roles' => ['admin','gestor','producao','expedicao','financeiro']],
    ['key' => 'clientes', 'label' => 'Clientes', 'href' => 'clientes', 'icon' => 'users', 'roles' => ['admin','gestor','cliente']],
    ['key' => 'produtos', 'label' => 'Produtos', 'href' => 'produtos', 'icon' => 'package', 'roles' => ['admin','gestor']],
    ['key' => 'receitas', 'label' => 'Receitas', 'href' => 'receitas', 'icon' => 'book-open', 'roles' => ['admin','gestor','producao']],
    ['key' => 'pedidos', 'label' => 'Pedidos', 'href' => 'pedidos', 'icon' => 'shopping-cart', 'roles' => ['admin','gestor','expedicao','financeiro']],
    ['key' => 'mapa', 'label' => 'Mapa', 'href' => 'mapa', 'icon' => 'map', 'roles' => ['admin','gestor','producao','expedicao']],
    ['key' => 'desempenho', 'label' => 'Desempenho', 'href' => 'desempenho', 'icon' => 'activity', 'roles' => ['admin','gestor','producao','expedicao']],
    ['key' => 'metas', 'label' => 'Metas', 'href' => 'metas', 'icon' => 'target', 'roles' => ['admin','gestor','producao','expedicao']],
    ['key' => 'producao', 'label' => 'Produção', 'href' => 'producao/op', 'icon' => 'factory', 'roles' => ['admin','gestor','producao']],
    ['key' => 'estoque', 'label' => 'Estoque', 'href' => 'estoque/posicao', 'icon' => 'boxes', 'roles' => ['admin','gestor','expedicao']],
    ['key' => 'expedicao', 'label' => 'Expedição', 'href' => 'expedicao', 'icon' => 'truck', 'roles' => ['admin','gestor','expedicao']],
    ['key' => 'financeiro', 'label' => 'Financeiro', 'href' => 'financeiro/titulos', 'icon' => 'wallet', 'roles' => ['admin','gestor','financeiro']],
    ['key' => 'relatorios', 'label' => 'Relatórios', 'href' => 'relatorios', 'icon' => 'bar-chart-3', 'roles' => ['admin','gestor']],
    ['key' => 'auditoria', 'label' => 'Auditoria', 'href' => 'auditoria', 'icon' => 'history', 'roles' => ['admin']],
    ['key' => 'config', 'label' => 'Configurações', 'href' => 'config', 'icon' => 'settings', 'roles' => ['admin']],
];

$role = $user['papel'] ?? 'cliente';
$activeKey = $activeMenu ?? '';
$basePathPrefix = rtrim($basePath ?? '', '/');
$segmentMap = [
    'dashboard' => 'dashboard',
    'clientes' => 'clientes',
    'produtos' => 'produtos',
    'receitas' => 'receitas',
    'pedidos' => 'pedidos',
    'mapa' => 'mapa',
    'desempenho' => 'desempenho',
    'metas' => 'metas',
    'producao' => 'producao',
    'estoque' => 'estoque',
    'expedicao' => 'expedicao',
    'financeiro' => 'financeiro',
    'relatorios' => 'relatorios',
    'auditoria' => 'auditoria',
    'config' => 'config',
];

// Usa o script atual para determinar o menu ativo e mantém a seleção após recarregar.
$currentScript = basename($_SERVER['PHP_SELF'] ?? '');
$scriptKey = strtolower(pathinfo($currentScript, PATHINFO_FILENAME));
if ($activeKey === '' && $scriptKey && $scriptKey !== 'index') {
    $activeKey = $segmentMap[$scriptKey] ?? $scriptKey;
}

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if ($basePathPrefix !== '' && strpos($requestPath, $basePathPrefix) === 0) {
    $requestPath = substr($requestPath, strlen($basePathPrefix));
}
$requestPath = trim($requestPath, '/');
if ($activeKey === '') {
    $segment = $requestPath === '' ? 'dashboard' : explode('/', $requestPath)[0];
    $activeKey = $segmentMap[strtolower($segment)] ?? '';
}
$activeMenuItem = null;
foreach ($menuItems as $item) {
    if (($item['key'] ?? '') === $activeKey) {
        $activeMenuItem = $item;
        break;
    }
}
$breadcrumbTrail = $breadcrumbs ?? [];
if (!is_array($breadcrumbTrail)) {
    $breadcrumbTrail = [$breadcrumbTrail];
}
$breadcrumbTrail = array_values(array_filter($breadcrumbTrail, static fn($crumb) => $crumb !== null && $crumb !== ''));
if (!$breadcrumbTrail) {
    if (!empty($title)) {
        $breadcrumbTrail = ['Início', $title];
    } else {
        $breadcrumbTrail = ['Início'];
    }
} elseif (strcasecmp((string)$breadcrumbTrail[0], 'Início') !== 0) {
    array_unshift($breadcrumbTrail, 'Início');
}
$initials = '';
if (!empty($user['nome'])) {
    $parts = preg_split('/\s+/', trim($user['nome']));
    $initials = strtoupper(substr($parts[0], 0, 1).(isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}

$themeMode = strtolower($theme['mode'] ?? 'light');
$primaryHex = $theme['primary_hex'] ?? '#5b616f';
$primaryDark = $primaryHex;
if (!function_exists('shade_hex')) {
    function shade_hex(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            return '#5b616f';
        }
        $values = str_split($hex, 2);
        $result = '#';
        foreach ($values as $pair) {
            $base = hexdec($pair);
            if ($percent >= 0) {
                $calc = $base + (255 - $base) * ($percent / 100);
            } else {
                $calc = $base + ($base * ($percent / 100));
            }
            $calc = max(0, min(255, $calc));
            $result .= str_pad(dechex((int)round($calc)), 2, '0', STR_PAD_LEFT);
        }
        return strtoupper($result);
    }
}
if (!function_exists('hex_to_rgb_channels')) {
    function hex_to_rgb_channels(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            $hex = '5B616F';
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
if (!function_exists('app_logo_matrix_values')) {
    function app_logo_matrix_values(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            $hex = '5B616F';
        }
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        $row = static fn(float $factor): array => [
            sprintf('%.6f', 0.2126 * $factor),
            sprintf('%.6f', 0.7152 * $factor),
            sprintf('%.6f', 0.0722 * $factor),
            '0',
            '0',
        ];
        $values = array_merge($row($r), $row($g), $row($b), ['0', '0', '0', '1', '0']);
        return implode(' ', $values);
    }
}
$logoTintHex = $primaryHex;
$logoMatrixValues = app_logo_matrix_values($logoTintHex);
$primaryDark = shade_hex($primaryHex, -25);
$primarySoft = shade_hex($primaryHex, 45);
$primaryRgb = implode(', ', hex_to_rgb_channels($primaryHex));
$logoLight = htmlspecialchars($asset($theme['logo_light'] ?? 'img/logo.png'), ENT_QUOTES, 'UTF-8');
$logoDark = htmlspecialchars($asset($theme['logo_dark'] ?? 'img/logodark.png'), ENT_QUOTES, 'UTF-8');
$logoCompactLight = htmlspecialchars($asset($theme['logo_compact_light'] ?? ($theme['logo_compact'] ?? 'img/logosmall.png')), ENT_QUOTES, 'UTF-8');
$logoCompactDark = htmlspecialchars($asset($theme['logo_compact_dark'] ?? ($theme['logo_compact_light'] ?? 'img/logosmall.png')), ENT_QUOTES, 'UTF-8');
$footerLogoPath = $theme['logo_footer'] ?? 'img/rodape/logo.png';
$footerLogo = htmlspecialchars($asset($footerLogoPath), ENT_QUOTES, 'UTF-8');
$currentYear = date('Y');
$tagline = $appTagline ?? 'Sistema de Controle Industrial (SCI)';
$titleColorize = ($theme['title_colorize'] ?? '0') === '1';
$logoTintEnabled = ($theme['logo_filter'] ?? '0') === '1';
$logoSizing = [
    'light' => [
        'height' => (int)($theme['logo_light_size'] ?? 50),
        'x' => (int)($theme['logo_light_offset_x'] ?? 0),
        'y' => (int)($theme['logo_light_offset_y'] ?? 0),
    ],
    'dark' => [
        'height' => (int)($theme['logo_dark_size'] ?? 50),
        'x' => (int)($theme['logo_dark_offset_x'] ?? 0),
        'y' => (int)($theme['logo_dark_offset_y'] ?? 0),
    ],
    'compact-light' => [
        'height' => (int)($theme['logo_compact_light_size'] ?? 40),
        'x' => (int)($theme['logo_compact_light_offset_x'] ?? 0),
        'y' => (int)($theme['logo_compact_light_offset_y'] ?? 0),
    ],
    'compact-dark' => [
        'height' => (int)($theme['logo_compact_dark_size'] ?? 40),
        'x' => (int)($theme['logo_compact_dark_offset_x'] ?? 0),
        'y' => (int)($theme['logo_compact_dark_offset_y'] ?? 0),
    ],
];
$titleSizePx = isset($theme['title_size']) ? (int)$theme['title_size'] : 24;
if ($titleSizePx < 16) {
    $titleSizePx = 16;
} elseif ($titleSizePx > 64) {
    $titleSizePx = 64;
}
$brandTitleSize = $titleSizePx.'px';
$brandTitleOffsetX = (int)($theme['title_offset_x'] ?? 0);
$brandTitleOffsetY = (int)($theme['title_offset_y'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?><?= $title ? ' — '.htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : ''; ?></title>
    <script>
        (function(){
            try {
                var active = localStorage.getItem('themePreviewActive') === '1';
                var hex = active ? localStorage.getItem('themePreviewHex') : '';
                if (!hex) return;
                hex = String(hex).trim();
                if (!hex) return;
                if (hex.charAt(0) !== '#') hex = '#' + hex;
                hex = hex.replace(/[^#0-9a-fA-F]/g, '').toUpperCase();
                if (/^#[0-9A-F]{3}$/.test(hex)) {
                    hex = '#' + hex.charAt(1) + hex.charAt(1) + hex.charAt(2) + hex.charAt(2) + hex.charAt(3) + hex.charAt(3);
                }
                if (!/^#[0-9A-F]{6}$/.test(hex)) return;
                var shade = function(value, percent) {
                    var raw = value.slice(1);
                    var amount = percent / 100;
                    var channel = function(pair) {
                        var base = parseInt(pair, 16);
                        var next = percent >= 0 ? base + (255 - base) * amount : base + base * amount;
                        return Math.max(0, Math.min(255, Math.round(next))).toString(16).padStart(2, '0');
                    };
                    return ('#' + channel(raw.slice(0,2)) + channel(raw.slice(2,4)) + channel(raw.slice(4,6))).toLowerCase();
                };
                var r = parseInt(hex.slice(1, 3), 16);
                var g = parseInt(hex.slice(3, 5), 16);
                var b = parseInt(hex.slice(5, 7), 16);
                var root = document.documentElement.style;
                root.setProperty('--primary', hex.toLowerCase());
                root.setProperty('--primary-dark', shade(hex, -25));
                root.setProperty('--primary-soft', shade(hex, 45));
                root.setProperty('--primary-rgb', r + ', ' + g + ', ' + b);
                root.setProperty('--primary-shadow', '0 18px 34px -24px rgba(' + r + ', ' + g + ', ' + b + ', 0.62)');
                root.setProperty('--primary-shadow-strong', '0 22px 42px -26px rgba(' + r + ', ' + g + ', ' + b + ', 0.72)');
                root.setProperty('--primary-glow', '0 0 0 3px rgba(' + r + ', ' + g + ', ' + b + ', 0.20)');
            } catch (error) {}
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
        :root {
            --logo-light-height: <?= (int)$logoSizing['light']['height']; ?>px;
            --logo-dark-height: <?= (int)$logoSizing['dark']['height']; ?>px;
            --logo-compact-light-height: <?= (int)$logoSizing['compact-light']['height']; ?>px;
            --logo-compact-dark-height: <?= (int)$logoSizing['compact-dark']['height']; ?>px;
            --logo-light-x: <?= (int)$logoSizing['light']['x']; ?>px;
            --logo-light-y: <?= (int)$logoSizing['light']['y']; ?>px;
            --logo-dark-x: <?= (int)$logoSizing['dark']['x']; ?>px;
            --logo-dark-y: <?= (int)$logoSizing['dark']['y']; ?>px;
            --logo-compact-light-x: <?= (int)$logoSizing['compact-light']['x']; ?>px;
            --logo-compact-light-y: <?= (int)$logoSizing['compact-light']['y']; ?>px;
            --logo-compact-dark-x: <?= (int)$logoSizing['compact-dark']['x']; ?>px;
            --logo-compact-dark-y: <?= (int)$logoSizing['compact-dark']['y']; ?>px;
            --brand-title-x: <?= $brandTitleOffsetX; ?>px;
            --brand-title-y: <?= $brandTitleOffsetY; ?>px;
        }
        :root {
            --sidebar-width: 265px;
            --sidebar-width-collapsed: 110px;
            --bg: #f5f7fb;
            --sidebar-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(243, 245, 250, 0.94) 100%);
            --mobile-sidebar-width: 82vw;
            --sidebar-text: #0f172a;
            --sidebar-muted: rgba(71, 85, 105, 0.85);
            --sidebar-search-bg: rgba(241, 245, 249, 0.9);
            --sidebar-search-border: rgba(148, 163, 184, 0.35);
            --sidebar-search-text: #1f2937;
            --surface: #ffffff;
            --surface-alt: #f8fafc;
            --surface-muted: rgba(249, 250, 251, 0.8);
            --border: rgba(15, 23, 42, 0.08);
            --text: #0f172a;
            --text-muted: rgba(71, 85, 105, 0.82);
            --primary: <?= htmlspecialchars(strtolower($primaryHex), ENT_QUOTES, 'UTF-8'); ?>;
            --primary-dark: <?= htmlspecialchars(strtolower($primaryDark), ENT_QUOTES, 'UTF-8'); ?>;
            --primary-soft: <?= htmlspecialchars(strtolower($primarySoft), ENT_QUOTES, 'UTF-8'); ?>;
            --primary-rgb: <?= htmlspecialchars($primaryRgb, ENT_QUOTES, 'UTF-8'); ?>;
            --primary-shadow: 0 18px 34px -24px rgba(var(--primary-rgb), 0.62);
            --primary-shadow-strong: 0 22px 42px -26px rgba(var(--primary-rgb), 0.72);
            --primary-glow: 0 0 0 3px rgba(var(--primary-rgb), 0.20);
            --shadow-soft: 0 28px 60px -40px rgba(15, 23, 42, 0.55);
            --shadow-card: 0 24px 48px -38px rgba(100, 116, 139, 0.4);
            --pill-bg: rgba(91, 97, 111, 0.12);
            --pill-text: inherit;
            --transition: all 0.25s ease;
            --radius-lg: 20px;
            --brand-title-size: <?= htmlspecialchars($brandTitleSize, ENT_QUOTES, 'UTF-8'); ?>;
            --density-scale: 1;
        }
        body.theme-dark {
            --bg: #0b1324;
            --sidebar-bg: linear-gradient(180deg, rgba(14, 22, 40, 0.95) 0%, rgba(10, 16, 32, 0.96) 120%);
            --sidebar-text: #e2e8f0;
            --sidebar-muted: rgba(148, 163, 184, 0.82);
            --sidebar-search-bg: rgba(30, 41, 59, 0.88);
            --sidebar-search-border: rgba(71, 85, 105, 0.4);
            --sidebar-search-text: #e2e8f0;
            --surface: rgba(22, 32, 48, 0.92);
            --surface-alt: rgba(15, 23, 42, 0.82);
            --surface-muted: rgba(30, 41, 59, 0.7);
            --border: rgba(148, 163, 184, 0.18);
            --text: #e2e8f0;
            --text-muted: rgba(203, 213, 225, 0.75);
            --shadow-soft: 0 28px 60px -40px rgba(3, 7, 18, 0.75);
            --shadow-card: 0 24px 48px -34px rgba(15, 23, 42, 0.75);
            --pill-bg: rgba(148, 163, 184, 0.14);
            --pill-text: #f8fafc;
        }
        * {
            box-sizing: border-box;
        }
        html {
            width: 100%;
            overflow-x: hidden;
        }
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            transition: var(--transition);
            width: 100%;
            overflow-x: hidden;
        }
        a {
            color: inherit;
            text-decoration: none;
        }
        img, video {
            max-width: 100%;
            height: auto;
        }
        main {
            width: 100%;
            min-width: 0;
        }
        .card table {
            width: 100%;
            border-collapse: collapse;
            word-wrap: break-word;
            table-layout: auto;
        }
        @media (max-width: 768px) {
            .card table {
                display: block;
                overflow-x: auto;
            }
            .card table thead,
            .card table tbody {
                display: table;
                width: 100%;
            }
        }
        .layout {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(91, 97, 111, 0.04));
            width: 100%;
            overflow-x: hidden;
            padding-left: var(--sidebar-width);
            position: relative;
        }
        .layout.sidebar-collapsed {
            padding-left: var(--sidebar-width-collapsed);
        }
        body.theme-dark .layout {
            background: linear-gradient(135deg, rgba(9, 14, 28, 0.88), rgba(22, 30, 48, 0.94));
        }
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            padding: 28px 22px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            border-right: 1px solid var(--border);
            box-shadow: inset -1px 0 0 rgba(15, 23, 42, 0.06);
            transition: width 0.25s ease, padding 0.25s ease;
            overflow-y: auto;
            overflow-x: visible;
        }
        .sidebar.collapsed {
            width: var(--sidebar-width-collapsed);
            padding: 28px 16px;
        }
        .brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: relative;
        }
        .brand-identity {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .brand-logos {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-logo-img {
            max-height: 50px;
            width: auto;
            transition: opacity 0.2s ease;
            filter: var(--logo-filter, none);
            transition: opacity 0.2s ease, filter 0.3s ease;
        }
        .brand-logo-img[data-logo-variant="light"] {
            max-height: var(--logo-light-height, 50px);
            transform: translate(var(--logo-light-x, 0px), var(--logo-light-y, 0px));
        }
        .brand-logo-img[data-logo-variant="dark"] {
            max-height: var(--logo-dark-height, 50px);
            transform: translate(var(--logo-dark-x, 0px), var(--logo-dark-y, 0px));
        }
        .brand-logo-expanded {
            display: block;
        }
        .brand-logo-dark {
            display: none;
        }
        body.theme-dark .brand-logo-light {
            display: none;
        }
        body.theme-dark .brand-logo-dark {
            display: block;
        }
        .sidebar:not(.collapsed) .brand-logo-compact {
            display: none !important;
        }
        .brand-logo-compact {
            display: none;
            max-height: 40px;
        }
        .brand-logo-img[data-logo-variant="compact-light"] {
            max-height: var(--logo-compact-light-height, 40px);
            transform: translate(var(--logo-compact-light-x, 0px), var(--logo-compact-light-y, 0px));
        }
        .brand-logo-img[data-logo-variant="compact-dark"] {
            max-height: var(--logo-compact-dark-height, 40px);
            transform: translate(var(--logo-compact-dark-x, 0px), var(--logo-compact-dark-y, 0px));
        }
        .brand-title {
            font-size: var(--brand-title-size, 1.5rem);
            font-weight: 700;
            letter-spacing: -0.01em;
            color: var(--sidebar-text);
            display: inline-flex;
            transform: translate(var(--brand-title-x, 0px), var(--brand-title-y, 0px));
        }
        .brand-title.colorized {
            color: var(--primary);
        }
        .brand-tagline {
            font-size: 0.55rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--sidebar-muted);
            white-space: nowrap;
            margin-top: -4px;
        }
        .sidebar.collapsed .brand-title,
        .sidebar.collapsed .brand-tagline,
        .sidebar.collapsed .sidebar-search,
        .sidebar.collapsed .item-label {
            display: none !important;
        }
        .sidebar.collapsed .brand-logo-expanded {
            display: none !important;
        }
        .sidebar.collapsed .brand-logo-compact {
            display: block !important;
        }
        .sidebar.collapsed .brand-logo-compact.brand-logo-dark {
            display: none !important;
        }
        /* Filtro desativado + tema escuro: usar logo pequena escura no menu recolhido */
        body.theme-dark:not(.logo-filter-enabled) .sidebar.collapsed .brand-logo-compact.brand-logo-dark {
            display: block !important;
        }
        body.theme-dark:not(.logo-filter-enabled) .sidebar.collapsed .brand-logo-compact.brand-logo-light {
            display: none !important;
        }
        body.logo-filter-enabled.theme-light .brand-logo-dark,
        body.logo-filter-enabled.theme-light .sidebar.collapsed .brand-logo-compact.brand-logo-dark {
            display: block !important;
        }
        body.logo-filter-enabled.theme-light .brand-logo-light,
        body.logo-filter-enabled.theme-light .sidebar.collapsed .brand-logo-compact.brand-logo-light {
            display: none !important;
        }
        body.logo-filter-enabled.theme-dark .brand-logo-dark,
        body.logo-filter-enabled.theme-dark .sidebar.collapsed .brand-logo-compact.brand-logo-dark {
            display: block !important;
        }
        body.logo-filter-enabled.theme-dark .brand-logo-light,
        body.logo-filter-enabled.theme-dark .sidebar.collapsed .brand-logo-compact.brand-logo-light {
            display: none !important;
        }
        body.logo-filter-enabled.theme-light .sidebar:not(.collapsed) .brand-logo-compact {
            display: none !important;
        }
        body.logo-filter-enabled.theme-light .sidebar.collapsed .brand-logo-expanded {
            display: none !important;
        }
        body.logo-filter-enabled.theme-dark .sidebar:not(.collapsed) .brand-logo-compact {
            display: none !important;
        }
        @media (min-width: 901px) {
            .sidebar.collapsed .brand-logo-expanded,
            body.logo-filter-enabled .sidebar.collapsed .brand-logo-expanded {
                display: none !important;
            }
            .sidebar.collapsed .brand-logo-compact.brand-logo-light,
            body:not(.logo-filter-enabled) .sidebar.collapsed .brand-logo-compact.brand-logo-light {
                display: block !important;
            }
            .sidebar.collapsed .brand-logo-compact.brand-logo-dark,
            body:not(.logo-filter-enabled) .sidebar.collapsed .brand-logo-compact.brand-logo-dark {
                display: none !important;
            }
            /* Filtro desligado: usar apenas a logo pequena clara */
            body:not(.logo-filter-enabled) .sidebar.collapsed .brand-logo-compact.brand-logo-light {
                display: block !important;
            }
            body:not(.logo-filter-enabled) .sidebar.collapsed .brand-logo-compact.brand-logo-dark {
                display: none !important;
            }
            /* Filtro ligado: usar apenas a logo pequena escura */
            body.logo-filter-enabled .sidebar.collapsed .brand-logo-compact.brand-logo-dark {
                display: block !important;
            }
            body.logo-filter-enabled .sidebar.collapsed .brand-logo-compact.brand-logo-light {
                display: none !important;
            }
        }
        .sidebar.collapsed .brand-logos {
            justify-content: center;
        }
        .sidebar.collapsed .nav-scroll {
            padding-right: 14px;
            margin-right: -10px;
        }
        .sidebar-search {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 14px;
            padding: 10px 14px;
            background: var(--sidebar-search-bg);
            border: 1px solid var(--sidebar-search-border);
        }
        .sidebar-search input {
            background: transparent;
            border: none;
            outline: none;
            color: var(--sidebar-search-text);
            flex: 1;
            font-size: 0.95rem;
        }
        .sidebar-search .icon {
            width: 18px;
            height: 18px;
            color: var(--sidebar-muted);
        }
        .sidebar-search input::placeholder {
            color: rgba(148, 163, 184, 0.7);
        }
        .sidebar-body {
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex: 1 1 auto;
            min-height: 0;
        }
        .nav-scroll {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 0;
            margin-right: 0;
            scrollbar-width: thin;
        }
        @media (min-width: 901px) {
            .nav-scroll {
                padding-right: 20px;
                margin-right: -20px;
            }
        }
        .nav-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .nav-scroll::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.45);
            border-radius: 8px;
        }
        body.theme-dark .nav-scroll::-webkit-scrollbar-thumb {
            background: rgba(71, 85, 105, 0.55);
        }
        .nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 14px;
            color: var(--sidebar-muted);
            font-weight: 600;
            font-size: 0.9rem;
            transition: var(--transition);
            position: relative;
        }
        .nav-item .nav-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 12px;
            background: transparent;
            line-height: 1;
        }
        body.theme-dark .nav-item .nav-icon {
            background: transparent;
        }
        .nav-item .nav-icon svg {
            width: 20px;
            height: 20px;
        }
        .sidebar.collapsed .nav-item .nav-icon {
            width: 28px;
            height: 28px;
            padding: 0;
        }
        .sidebar.collapsed .nav-item .nav-icon svg {
            width: 18px;
            height: 18px;
        }
        .nav-item:hover {
            color: var(--sidebar-text);
            background: rgba(148, 163, 184, 0.14);
            transform: translateX(4px);
        }
        body.theme-dark .nav-item:hover {
            color: #f8fafc;
        }
        .nav-item.active {
            color: #fff;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            box-shadow: var(--primary-shadow);
        }
        .sidebar-footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            color: var(--sidebar-muted);
            padding-top: 20px;
            border-top: 1px solid rgba(148, 163, 184, 0.2);
        }
        .collapse-toggle {
            border: none;
            background: rgba(148, 163, 184, 0.16);
            color: var(--sidebar-text);
            border-radius: 14px;
            padding: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            flex-shrink: 0;
            box-shadow: 0 12px 30px -18px rgba(15, 23, 42, 0.35);
            position: fixed;
            top: 60px;
            left: calc(var(--sidebar-width) - 33px);
            z-index: 999;
            width: 40px;
            height: 40px;
        }
        .sidebar.collapsed .collapse-toggle {
            left: calc(var(--sidebar-width-collapsed) - 33px);
            top: 60px;
        }
        .collapse-toggle:hover {
            background: rgba(148, 163, 184, 0.24);
        }
        .collapse-toggle .icon {
            width: 22px;
            height: 22px;
        }
        .mobile-menu-toggle,
        .sidebar-backdrop {
            display: none;
        }
        .mobile-menu-toggle {
            position: fixed;
            top: 16px;
            left: -8px;
            z-index: 1200;
            padding: 12px 14px;
            border-radius: 14px;
            background: var(--primary);
            color: #fff;
            border: none;
            box-shadow: 0 16px 32px -20px rgba(15, 23, 42, 0.4);
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: left 0.28s ease, background 0.28s ease;
        }
        .mobile-menu-toggle .icon {
            width: 20px;
            height: 20px;
        }
        .mobile-menu-toggle .icon-close { display: none; }
        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 998;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .sidebar-backdrop.active {
            display: block;
            opacity: 1;
        }
        .content {
            flex: 1;
            padding: 36px 48px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            min-width: 0;
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            margin-left: auto;
        }
        .footer {
            margin-top: auto;
            padding: 32px 0 42px;
        }
        .footer-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            text-align: center;
        }
        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .footer-logo img {
            width: 247px;
            height: 76px;
            max-width: 100%;
            object-fit: contain;
            filter: grayscale(1) brightness(0.85);
            opacity: 0.95;
        }
        .footer-copy {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.02em;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--surface);
            border-radius: 18px;
            padding: 16px 24px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-card);
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .logout-form {
            margin: 0;
        }
        .topbar-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: var(--surface-alt);
            padding: 10px 18px;
            font-weight: 600;
            color: var(--text);
            cursor: pointer;
            transition: var(--transition);
        }
        .topbar-button.logout-button {
            background: var(--surface);
        }
        .topbar-button.logout-button span[data-lucide] {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .topbar-button .theme-icon {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .topbar-button .theme-icon > span,
        .topbar-button .theme-icon > svg {
            width: 100%;
            height: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .topbar-button .theme-icon-dark {
            display: none;
        }
        body.theme-dark .topbar-button .theme-icon-dark {
            display: inline-flex;
        }
        body.theme-dark .topbar-button .theme-icon-light {
            display: none;
        }
        .topbar-button:hover {
            background: var(--primary-soft);
            color: var(--primary-dark);
            border-color: transparent;
        }
        .topbar-button.logout-button:hover {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border-color: transparent;
        }
        body.theme-dark .topbar-button:hover {
            color: #0f172a;
        }
        body.theme-dark .topbar-button.logout-button:hover {
            color: #0f172a;
        }
        .breadcrumbs {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            color: var(--text-muted);
        }
        .breadcrumbs span:last-child {
            color: var(--text);
            font-weight: 600;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            margin-top: 8px;
        }
        .page-title {
            margin: 0;
            font-size: 2rem;
            letter-spacing: -0.01em;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .page-title .title-icon {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: var(--primary);
            color: #fff;
            padding: 6px;
            box-sizing: border-box;
        }
        .page-title .title-icon svg {
            width: 18px;
            height: 18px;
        }
        .page-subtitle {
            margin: 6px 0 0;
            color: var(--text-muted);
            font-size: 1rem;
        }
        .page-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border-radius: 14px;
            border: 1px solid transparent;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            background: var(--surface-alt);
            color: var(--text);
        }
        .btn span[data-lucide] {
            width: 18px;
            height: 18px;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: var(--primary-shadow-strong);
        }
        .btn-primary:hover {
            filter: brightness(1.05);
        }
        .btn-outline {
            background: transparent;
            border-color: var(--border);
        }
        .alerts {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .alert {
            padding: 14px 18px;
            border-radius: 14px;
            font-weight: 600;
            border: 1px solid transparent;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.12);
            color: #047857;
            border-color: rgba(34, 197, 94, 0.28);
        }
        .alert-error {
            background: rgba(248, 113, 113, 0.16);
            color: #b91c1c;
            border-color: rgba(248, 113, 113, 0.32);
        }
        body.theme-dark .alert-success {
            color: #34d399;
            border-color: rgba(34, 197, 94, 0.24);
        }
        body.theme-dark .alert-error {
            color: #fca5a5;
            border-color: rgba(248, 113, 113, 0.28);
        }
        .page-content {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }
        .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-soft);
        }
        .panel h2 {
            margin-top: 0;
            margin-bottom: 6px;
            font-size: 1.4rem;
        }
        .panel-description {
            margin: 0 0 18px;
            color: var(--text-muted);
        }
        .form-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
        .filter-grid {
            display: grid;
            gap: 14px 16px;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            margin-bottom: 18px;
            align-items: end;
        }
        .filter-grid label {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-weight: 600;
            color: var(--text-muted);
        }
        .filter-grid input,
        .filter-grid select {
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 12px 14px;
            background: var(--surface-alt);
            color: var(--text);
            font-size: 0.95rem;
        }
        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .form-grid label {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-weight: 600;
            color: var(--text-muted);
        }
        .form-grid input,
        .form-grid select,
        .form-grid textarea {
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 12px 14px;
            background: var(--surface-alt);
            color: var(--text);
            font-size: 0.95rem;
        }
        textarea {
            resize: vertical;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 4px;
        }
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 12px;
            border: 1px solid transparent;
            padding: 10px 18px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        .button-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: var(--primary-shadow);
        }
        .tab-button.active,
        .clientes-tab.is-active,
        .clientes-tab.active,
        .pedidos-tab.is-active,
        .pedidos-tab.active,
        .appearance-subtab.active,
        .user-subtab.active,
        .portal-tab.active,
        .portal-tab.is-active,
        .portal-menu button.active,
        .portal-menu button.is-active,
        .pill.active,
        .pill.is-active {
            box-shadow: var(--primary-shadow) !important;
        }
        .button-outline {
            background: transparent;
            border-color: var(--border);
            color: var(--text);
        }
        .button-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--surface);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border);
        }
        .data-table th,
        .data-table td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        .data-table th {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            background: var(--surface-alt);
        }
        .data-table tbody tr:hover {
            background: rgba(91, 97, 111, 0.08);
        }
        body.theme-dark .data-table tbody tr:hover {
            background: rgba(148, 163, 184, 0.08);
        }
        .panel-header.row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .catalog-table {
            background: var(--surface-alt);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px;
            box-shadow: var(--shadow-soft);
        }
        .catalog-meta {
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 10px;
        }
        .cards {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 22px 24px;
            box-shadow: var(--shadow-card);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .card h2 {
            margin: 0;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
        }
        .card strong {
            font-size: 2.2rem;
            letter-spacing: -0.03em;
        }
        .split {
            display: grid;
            gap: 24px;
        }
        @media (min-width: 1100px) {
            .split {
                grid-template-columns: minmax(0, 1.8fr) minmax(0, 1fr);
            }
        }
        .quick-actions,
        .timeline {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 26px;
            box-shadow: var(--shadow-soft);
        }
        .quick-actions h3,
        .timeline h3 {
            margin: 0 0 12px;
            font-size: 1.2rem;
        }
        .quick-actions .panel-description {
            color: var(--text-muted);
            margin-bottom: 20px;
        }
        .action-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
        .action-button {
            display: flex;
            flex-direction: column;
            gap: 10px;
            border-radius: 16px;
            padding: 18px 20px;
            border: 1px solid var(--border);
            background: var(--surface-alt);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
            color: var(--text);
            transition: var(--transition);
            position: relative;
        }
        .action-button:hover {
            transform: translateY(-2px);
            border-color: var(--primary);
            box-shadow: var(--primary-shadow);
        }
        .action-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: rgba(91, 97, 111, 0.18);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .action-icon [data-lucide] {
            width: 22px;
            height: 22px;
        }
        body.theme-dark .action-icon {
            background: rgba(148, 163, 184, 0.18);
            color: var(--primary-soft);
        }
        .shortcut {
            position: absolute;
            top: 16px;
            right: 16px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary);
            background: var(--pill-bg);
            padding: 4px 10px;
            border-radius: 999px;
            letter-spacing: 0.08em;
        }
        .action-button span:last-child {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .timeline ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .timeline li {
            background: var(--surface-alt);
            border-radius: 14px;
            padding: 18px;
            border: 1px solid transparent;
            transition: var(--transition);
        }
        .timeline li:hover {
            border-color: var(--primary);
        }
        .timeline strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1rem;
        }
        .user-badge {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            max-width: min(100%, 360px);
            background: var(--surface-alt);
            border-radius: 999px;
            padding: 10px 16px;
            border: 1px solid var(--border);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }
        .user-badge > div:last-child {
            min-width: 0;
        }
        .user-badge strong,
        .user-badge small {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .user-avatar {
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }
        body.theme-dark .user-avatar {
            color: #0f172a;
        }
        @media (max-width: 1400px) {
            .content {
                padding: 32px 36px;
            }
        }
        @media (max-width: 1260px) {
            .content {
                padding: 28px 24px;
            }
            .sidebar {
                width: 240px;
            }
        }
        @media (max-width: 1100px) {
            .layout {
                flex-direction: column;
                padding-left: 0;
            }
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                flex-direction: row;
                align-items: center;
                gap: 16px;
                padding: 20px;
            }
            .sidebar.collapsed {
                width: 100%;
            }
            .nav-list {
                flex-direction: column;
                overflow-x: hidden;
            }
            .content {
                padding: 28px 24px;
            }
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        @media (max-width: 900px) {
            .layout {
                padding-left: 0;
            }
            .sidebar .sidebar-footer {
                display: none;
            }
            .sidebar .brand,
            .sidebar .brand-identity,
            .sidebar .brand-logos,
            .sidebar .sidebar-body,
            .sidebar .nav-scroll,
            .sidebar .nav-list,
            .sidebar .sidebar-footer {
                width: 100%;
                max-width: none;
                margin: 0;
                padding: 0;
            }
            .sidebar .brand,
            .sidebar .brand-identity,
            .sidebar .brand-logos {
                align-items: center;
                justify-content: center;
                text-align: center;
                gap: 10px;
            }
            .sidebar .brand-title,
            .sidebar .brand-tagline {
                text-align: center;
                width: 100%;
            }
            .nav-list { align-items: flex-start; }
            .nav-list .nav-item { justify-content: flex-start; width: 100%; }
            .sidebar-backdrop {
                display: block;
                opacity: 0;
                pointer-events: none;
            }
            .sidebar-backdrop.active {
                opacity: 1;
                pointer-events: auto;
            }
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
                height: 100dvh;
                max-height: 100dvh;
                width: var(--mobile-sidebar-width, 82vw);
                max-width: 85vw;
                border-radius: 0 18px 18px 0;
                box-shadow: 18px 0 40px -28px rgba(15, 23, 42, 0.6);
                padding: calc(24px + env(safe-area-inset-top, 0px)) 18px calc(30px + env(safe-area-inset-bottom, 0px));
                z-index: 999;
                overflow-y: auto;
                overflow-x: hidden;
                overscroll-behavior: contain;
                background: var(--surface);
                transition: transform 0.28s ease;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar.collapsed {
                width: var(--mobile-sidebar-width, 82vw);
            }
            .nav-list {
                flex-direction: column;
                overflow-x: hidden;
            }
            .sidebar.collapsed {
                width: var(--mobile-sidebar-width, 82vw);
                padding: calc(24px + env(safe-area-inset-top, 0px)) 24px calc(34px + env(safe-area-inset-bottom, 0px));
            }
            .sidebar.collapsed .brand-title,
            .sidebar.collapsed .brand-tagline,
            .sidebar.collapsed .sidebar-search,
            .sidebar.collapsed .item-label,
            .sidebar.collapsed .brand-logo-expanded {
                display: block !important;
            }
            .sidebar.collapsed .brand-logo-compact {
                display: none !important;
            }
            /* Garante que o conteúdo interno do menu mobile comece no eixo X = 0 sem animações laterais */
            .sidebar .brand,
            .sidebar .sidebar-body,
            .sidebar .nav-scroll,
            .sidebar .nav-list,
            .sidebar .sidebar-search {
                margin-left: 0;
                padding-left: 0;
                transform: none;
            }
            .nav-list .nav-item,
            .nav-list .nav-item:hover {
                transform: none;
            }
            .brand {
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 10px;
                margin-bottom: 10px;
                text-align: center;
            }
            .brand-identity {
                gap: 6px;
                align-items: center;
                justify-content: center;
            }
            .brand-logos {
                justify-content: center;
            }
            .sidebar-body {
                margin-top: 8px;
                gap: 12px;
                align-items: center;
                display: flex;
                flex-direction: column;
                width: 100%;
            }
            .sidebar-search {
                width: 100%;
                max-width: 360px;
                margin: 0 0 10px 0;
            }
            .nav-scroll {
                margin-top: 12px;
                width: 100%;
            }
            .nav-list {
                align-items: flex-start;
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding-bottom: 120px;
            }
            .nav-list .nav-item {
                padding: 20px 22px;
                border-radius: 16px;
                gap: 18px;
                justify-content: flex-start;
                text-align: left;
                font-size: 1.25rem;
            }
            .nav-list .nav-item .item-label {
                text-align: left;
            }
            .nav-list .nav-item .nav-icon {
                width: 35px;
                height: 35px;
            }
        }
        @media (max-width: 640px) {
            .topbar {
                gap: 12px;
            }
            .topbar-right {
                width: 100%;
                flex-wrap: wrap;
                justify-content: flex-start;
                gap: 10px;
            }
            .topbar-right .topbar-button,
            .topbar-right .logout-form {
                flex: 1 1 48%;
            }
            .topbar-right .logout-form {
                display: flex;
            }
            .topbar-right .logout-form .topbar-button {
                width: 100%;
                justify-content: center;
            }
            .topbar-right .topbar-button {
                justify-content: center;
                min-width: 0;
            }
            .user-badge {
                width: 100%;
                justify-content: flex-start;
            }
        }
        body.logo-filter-enabled.theme-light .sidebar.collapsed .brand-logo-expanded.brand-logo-dark,
        body.logo-filter-enabled.theme-light .sidebar.collapsed .brand-logo-expanded.brand-logo-light,
        body.theme-dark .sidebar.collapsed .brand-logo-expanded.brand-logo-dark,
        body:not(.theme-dark) .sidebar.collapsed .brand-logo-expanded.brand-logo-light {
            display: none !important;
        }
        @media (max-width: 900px) {
            .collapse-toggle {
                display: none;
            }
            .mobile-menu-toggle {
                display: inline-flex;
                left: -9px;
                right: auto;
                top: 16px;
                padding: 12px 14px;
                border-radius: 12px;
                box-shadow: 0 16px 32px -22px rgba(15, 23, 42, 0.55);
                transition: left 0.28s ease, background 0.28s ease;
            }
            body.mobile-menu-open .mobile-menu-toggle {
                left: auto;
                right: 12px;
                background: rgba(255, 255, 255, 0.92);
                color: var(--primary-text, #0f172a);
            }
            body.mobile-menu-open .mobile-menu-toggle .icon-menu { display: none; }
            body.mobile-menu-open .mobile-menu-toggle .icon-close { display: inline-block; }
            .content {
                padding: 20px 18px;
            }
            body.mobile-menu-open {
                overflow: hidden;
            }
        }
    </style>
</head>
<body class="<?= $themeMode === 'dark' ? 'theme-dark' : 'theme-light'; ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="0" height="0" style="position: absolute; visibility: hidden;">
        <defs>
            <filter id="logoTintFilter" color-interpolation-filters="sRGB">
                <feColorMatrix id="logoTintMatrix" type="matrix" values="<?= htmlspecialchars($logoMatrixValues, ENT_QUOTES, 'UTF-8'); ?>"></feColorMatrix>
            </filter>
        </defs>
    </svg>
<button class="mobile-menu-toggle" type="button" id="mobileMenuToggle" aria-label="Abrir menu">
    <span class="icon icon-menu" data-lucide="menu"></span>
    <span class="icon icon-close" data-lucide="x"></span>
</button>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<div class="layout">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-identity">
                <div class="brand-logos">
                    <img class="brand-logo-img brand-logo-expanded brand-logo-light logo-tintable" src="<?= $logoLight; ?>" alt="Logo IndusOS Food" data-logo-variant="light">
                    <img class="brand-logo-img brand-logo-expanded brand-logo-dark logo-tintable" src="<?= $logoDark; ?>" alt="Logo IndusOS Food (tema escuro)" data-logo-variant="dark">
                    <img class="brand-logo-img brand-logo-compact brand-logo-light logo-tintable" src="<?= $logoCompactLight; ?>" alt="Logo compacto tema claro" data-logo-variant="compact-light">
                    <img class="brand-logo-img brand-logo-compact brand-logo-dark logo-tintable" src="<?= $logoCompactDark; ?>" alt="Logo compacto tema escuro" data-logo-variant="compact-dark">
                </div>
                <span class="brand-title<?= $titleColorize ? ' colorized' : ''; ?>"><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="brand-tagline"><?= htmlspecialchars($tagline, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <button class="collapse-toggle" type="button" id="collapseSidebar" title="Recolher menu" aria-label="Alternar menu">
                <span class="icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3.5" y="3.5" width="17" height="17" rx="3.2"></rect>
                        <line x1="11.5" y1="3.5" x2="11.5" y2="20.5"></line>
                    </svg>
                </span>
            </button>
        </div>
        <div class="sidebar-body">
            <div class="sidebar-search">
                <span class="icon" data-lucide="search"></span>
                <input type="search" id="menuSearch" placeholder="Buscar módulos" aria-label="Buscar módulos">
            </div>
            <div class="nav-scroll" role="presentation">
                <ul class="nav-list" id="menuList">
                    <?php foreach ($menuItems as $item): ?>
                        <?php if (in_array($role, $item['roles'], true)): ?>
                            <li>
                                <a class="nav-item <?= $activeKey === $item['key'] ? 'active' : ''; ?>" href="<?= htmlspecialchars($url($item['href']), ENT_QUOTES, 'UTF-8'); ?>">
                                    <span class="nav-icon" data-lucide="<?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>"></span>
                                    <span class="item-label"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="sidebar-footer">
            <span class="nav-icon" data-lucide="keyboard"></span>
            <span class="item-label">Use F1–F7 para atalhos</span>
        </div>
    </aside>
    <main class="content" id="app" role="main" tabindex="-1">
        <header class="topbar">
            <div class="breadcrumbs">
                <?php foreach ($breadcrumbTrail as $crumb): ?>
                    <span><?= htmlspecialchars($crumb, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endforeach; ?>
            </div>
            <div class="topbar-right">
                <button class="topbar-button" type="button" id="toggleTheme" aria-label="Alternar tema">
                    <span class="theme-icon theme-icon-light"><span data-lucide="moon"></span></span>
                    <span class="theme-icon theme-icon-dark"><span data-lucide="sun"></span></span>
                    <span>Alternar tema</span>
                </button>
                <form class="logout-form" method="post" action="<?= htmlspecialchars($url('logout'), ENT_QUOTES, 'UTF-8'); ?>">
                    <button class="topbar-button logout-button" type="submit">
                        <span data-lucide="log-out"></span>
                        <span>Sair</span>
                    </button>
                </form>
                <div class="user-badge">
                    <div class="user-avatar"><?= $initials ?: 'U'; ?></div>
                    <div>
                        <strong><?= htmlspecialchars($user['nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8'); ?></strong><br>
                        <small style="color: var(--text-muted); text-transform: capitalize;"><?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?></small>
                    </div>
                </div>
            </div>
        </header>

        <?php if (($flash['success'] ?? null) || ($flash['error'] ?? null)): ?>
            <div class="alerts" role="alert">
                <?php if ($flash['success'] ?? null): ?>
                    <div class="alert alert-success">✅ <?= htmlspecialchars($flash['success'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <?php if ($flash['error'] ?? null): ?>
                    <div class="alert alert-error">⚠️ <?= htmlspecialchars($flash['error'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($title || !empty($pageSubtitle) || !empty($pageActions)): ?>
            <div class="page-header">
                <div>
                    <?php if ($title): ?>
                        <h1 class="page-title">
                            <?php if (!empty($activeMenuItem['icon'])): ?>
                                <span class="title-icon" data-lucide="<?= htmlspecialchars($activeMenuItem['icon'], ENT_QUOTES, 'UTF-8'); ?>"></span>
                            <?php endif; ?>
                            <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
                        </h1>
                    <?php endif; ?>
                    <?php if (!empty($pageSubtitle)): ?>
                        <p class="page-subtitle"><?= htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </div>
                <?php if (!empty($pageActions)): ?>
                    <div class="page-actions">
                        <?php foreach ($pageActions as $action): ?>
                            <?php
                                $actionLabel = htmlspecialchars($action['label'] ?? 'Ação', ENT_QUOTES, 'UTF-8');
                                $actionHref = $action['href'] ?? '#';
                                $actionIcon = $action['icon'] ?? null;
                                $actionTarget = $action['target'] ?? null;
                                $variant = strtolower($action['variant'] ?? 'outline');
                                $classes = ['btn'];
                                if ($variant === 'primary') {
                                    $classes[] = 'btn-primary';
                                } elseif ($variant === 'outline') {
                                    $classes[] = 'btn-outline';
                                }
                                $actionUrl = htmlspecialchars($url($actionHref), ENT_QUOTES, 'UTF-8');
                            ?>
                            <a class="<?= implode(' ', $classes); ?>" href="<?= $actionUrl; ?>"<?= $actionTarget ? ' target="'.htmlspecialchars($actionTarget, ENT_QUOTES, 'UTF-8').'"' : ''; ?>>
                                <?php if ($actionIcon): ?>
                                    <span data-lucide="<?= htmlspecialchars($actionIcon, ENT_QUOTES, 'UTF-8'); ?>"></span>
                                <?php endif; ?>
                                <span><?= $actionLabel; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="page-content">
            <?= $content; ?>
        </div>
        <footer class="footer" role="contentinfo">
            <div class="footer-inner">
                <div class="footer-logo">
                    <img src="<?= $footerLogo; ?>" alt="Logotipo do rodapé IndusOS" loading="lazy">
                </div>
                <p class="footer-copy">
                    INDUS•OS © <?= $currentYear; ?> • Todos os direitos reservados
                    <br>
                    <a href="https://mystic-designer.com.br/" target="_blank" rel="noopener noreferrer">
                        <span>Created by </span><span style="color: var(--primary); font-weight: 700;">Mystic Designer®</span>
                    </a>
                </p>
            </div>
        </footer>
    </main>
</div>
<script src="https://unpkg.com/lucide@0.371.0/dist/umd/lucide.min.js"></script>
<script>
    const safeStorage = {
        get(key) {
            try {
                return window.localStorage.getItem(key);
            } catch (error) {
                return null;
            }
        },
        set(key, value) {
            try {
                window.localStorage.setItem(key, value);
            } catch (error) {
                // ignore storage errors (private mode, etc.)
            }
        },
        remove(key) {
            try {
                window.localStorage.removeItem(key);
            } catch (error) {
                // ignore removal errors
            }
        },
    };

    const configuredTheme = (<?= json_encode($themeMode); ?> || 'light').toLowerCase();
    const storedThemeRaw = safeStorage.get('theme') || '';
    const storedTheme = storedThemeRaw.toLowerCase();
    const initialTheme = ['light', 'dark'].includes(storedTheme) ? storedTheme : configuredTheme;
    const savedTitleSize = <?= json_encode($brandTitleSize); ?>;

    const normaliseHex = (value) => {
        if (!value) {
            return null;
        }
        let hex = String(value).trim();
        if (hex === '') {
            return null;
        }
        if (!hex.startsWith('#')) {
            hex = `#${hex}`;
        }
        hex = hex.replace(/[^#0-9a-fA-F]/g, '').toUpperCase();
        if (hex.length === 4) {
            hex = `#${hex[1]}${hex[1]}${hex[2]}${hex[2]}${hex[3]}${hex[3]}`;
        }
        if (hex.length !== 7) {
            return null;
        }
        return hex;
    };

    const clampChannel = (value) => Math.max(0, Math.min(255, value));

    const hexToRgb = (hex) => {
        const normalised = normaliseHex(hex) || '#5B616F';
        const raw = normalised.slice(1);
        return {
            r: parseInt(raw.slice(0, 2), 16),
            g: parseInt(raw.slice(2, 4), 16),
            b: parseInt(raw.slice(4, 6), 16),
        };
    };

    /**
     * Calcula o estoque diário a partir de um saldo anterior, entradas e saídas.
     * Produção é apenas registrada e não interfere no cálculo do estoque.
     *
     * @param {Object} params
     * @param {number} [params.estoqueAnterior=0]
     * @param {number} [params.entrada=0]
     * @param {number} [params.saida=0]
     * @param {number} [params.producao=0]
     * @returns {{producao:number, entrada:number, saida:number, estoqueFinal:number}}
     */
    window.calculateDailyStock = (params = {}) => {
        const toNumber = (value) => {
            const parsed = parseFloat(value);
            return Number.isFinite(parsed) ? parsed : 0;
        };

        const producao = toNumber(params.producao);
        const entrada = toNumber(params.entrada);
        const saida = toNumber(params.saida);
        const estoqueAnterior = toNumber(params.estoqueAnterior);
        const estoqueFinal = estoqueAnterior + entrada - saida;

        return {
            producao,
            entrada,
            saida,
            estoqueFinal,
        };
    };

    const shadeHex = (hex, percent) => {
        const normalised = normaliseHex(hex);
        if (!normalised) {
            return '#5b616f';
        }
        const raw = normalised.slice(1);
        const amount = percent / 100;
        const adjust = (channel) => {
            if (percent >= 0) {
                return clampChannel(channel + (255 - channel) * amount);
            }
            return clampChannel(channel + channel * amount);
        };
        const r = adjust(parseInt(raw.slice(0, 2), 16));
        const g = adjust(parseInt(raw.slice(2, 4), 16));
        const b = adjust(parseInt(raw.slice(4, 6), 16));
        const toHex = (channel) => channel.toString(16).padStart(2, '0');
        return `#${toHex(Math.round(r))}${toHex(Math.round(g))}${toHex(Math.round(b))}`.toLowerCase();
    };

    const setPrimaryCss = (hex) => {
        const primary = normaliseHex(hex) || '#5B616F';
        const rgb = hexToRgb(primary);
        const root = document.documentElement.style;
        root.setProperty('--primary', primary.toLowerCase());
        root.setProperty('--primary-dark', shadeHex(primary, -25));
        root.setProperty('--primary-soft', shadeHex(primary, 45));
        root.setProperty('--primary-rgb', `${rgb.r}, ${rgb.g}, ${rgb.b}`);
        root.setProperty('--primary-shadow', `0 18px 34px -24px rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.62)`);
        root.setProperty('--primary-shadow-strong', `0 22px 42px -26px rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.72)`);
        root.setProperty('--primary-glow', `0 0 0 3px rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.20)`);
    };

    let logoPrimaryHex = <?= json_encode(strtolower($primaryHex)); ?>;
    const savedPrimaryHex = normaliseHex(logoPrimaryHex) || '#5B616F';
    logoPrimaryHex = savedPrimaryHex;

    let logoTintEnabled = <?= $logoTintEnabled ? 'true' : 'false'; ?>;

    const previewActive = safeStorage.get('themePreviewActive') === '1';
    const previewHex = safeStorage.get('themePreviewHex');
    const previewLogoFilter = safeStorage.get('themePreviewLogoFilter');
    const previewTitleSize = safeStorage.get('themePreviewTitleSize');

    const clampTitleSize = (value) => {
        const parsed = parseInt(value, 10);
        if (Number.isNaN(parsed)) {
            return null;
        }
        if (parsed < 16) { return 16; }
        if (parsed > 64) { return 64; }
        return parsed;
    };

    const applyTitleSize = (value) => {
        const clamped = clampTitleSize(value);
        if (clamped === null) {
            return;
        }
        document.documentElement.style.setProperty('--brand-title-size', `${clamped}px`);
    };

    applyTitleSize(savedTitleSize);
    if (previewTitleSize) {
        applyTitleSize(previewTitleSize);
    }

    if (previewActive) {
        const normalisedPreview = normaliseHex(previewHex);
        if (normalisedPreview) {
            logoPrimaryHex = normalisedPreview.toLowerCase();
        }
        if (previewLogoFilter !== null) {
            logoTintEnabled = previewLogoFilter === '1';
        }
    }

    setPrimaryCss(logoPrimaryHex);

    const applyTheme = (mode) => {
        const normalised = mode === 'dark' ? 'dark' : 'light';
        document.body.classList.toggle('theme-dark', normalised === 'dark');
        document.body.classList.toggle('theme-light', normalised !== 'dark');
        safeStorage.set('theme', normalised);
    };
    applyTheme(initialTheme);

    const toRgb = (hex) => {
        const value = hex.replace(/[^0-9a-f]/gi, '');
        if (value.length === 3) {
            const [r, g, b] = value.split('');
            return {
                r: parseInt(r + r, 16),
                g: parseInt(g + g, 16),
                b: parseInt(b + b, 16),
            };
        }
        if (value.length !== 6) {
            return { r: 91, g: 97, b: 111 };
        }
        return {
            r: parseInt(value.slice(0, 2), 16),
            g: parseInt(value.slice(2, 4), 16),
            b: parseInt(value.slice(4, 6), 16),
        };
    };

    const logoMatrixNode = document.getElementById('logoTintMatrix');

    const buildLogoMatrix = (hex) => {
        const normalised = normaliseHex(hex) || '#5B616F';
        const { r, g, b } = toRgb(normalised);
        const rNorm = r / 255;
        const gNorm = g / 255;
        const bNorm = b / 255;
        const row = (factor) => [
            (0.2126 * factor).toFixed(6),
            (0.7152 * factor).toFixed(6),
            (0.0722 * factor).toFixed(6),
            '0',
            '0',
        ];
        const rRow = row(rNorm);
        const gRow = row(gNorm);
        const bRow = row(bNorm);
        return [...rRow, ...gRow, ...bRow, '0', '0', '0', '1', '0'].join(' ');
    };

    const applyLogoTint = () => {
        const logos = document.querySelectorAll('.logo-tintable');
        if (!logos.length) {
            return;
        }
        if (!logoTintEnabled || !logoMatrixNode) {
            document.body.classList.remove('logo-filter-enabled');
            logos.forEach((img) => {
                img.style.removeProperty('filter');
                img.style.removeProperty('-webkit-filter');
            });
            return;
        }
        const tintSource = logoPrimaryHex;
        const matrix = buildLogoMatrix(tintSource);
        logoMatrixNode.setAttribute('values', matrix);
        document.body.classList.add('logo-filter-enabled');
        logos.forEach((img) => {
            img.style.filter = 'url(#logoTintFilter)';
            img.style.webkitFilter = 'url(#logoTintFilter)';
        });
    };

    applyLogoTint();

    const toggleThemeButton = document.getElementById('toggleTheme');
    if (toggleThemeButton) {
        toggleThemeButton.addEventListener('click', () => {
            const next = document.body.classList.contains('theme-dark') ? 'light' : 'dark';
            applyTheme(next);
            applyLogoTint();
        });
    }

    const sidebar = document.getElementById('sidebar');
    const layout = document.querySelector('.layout');
    const collapse = document.getElementById('collapseSidebar');
    if (collapse) {
        collapse.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            if (layout) {
                layout.classList.toggle('sidebar-collapsed');
            }
        });
    }

    const mobileToggle = document.getElementById('mobileMenuToggle');
    const mobileBackdrop = document.getElementById('sidebarBackdrop');
    let sidebarWasCollapsed = sidebar.classList.contains('collapsed');

    const ensureMobileExpanded = () => {
        if (window.innerWidth <= 900) {
            sidebar.classList.remove('collapsed');
            if (layout) layout.classList.remove('sidebar-collapsed');
        }
    };

    ensureMobileExpanded();

    const openMobileMenu = () => {
        sidebarWasCollapsed = sidebar.classList.contains('collapsed');
        sidebar.classList.remove('collapsed');
        if (layout) layout.classList.remove('sidebar-collapsed');
        sidebar.classList.add('mobile-open');
        document.body.classList.add('mobile-menu-open');
        if (mobileBackdrop) mobileBackdrop.classList.add('active');
    };

    const closeMobileMenu = () => {
        sidebar.classList.remove('mobile-open');
        document.body.classList.remove('mobile-menu-open');
        if (mobileBackdrop) mobileBackdrop.classList.remove('active');
        if (sidebarWasCollapsed && window.innerWidth > 900) {
            sidebar.classList.add('collapsed');
            if (layout) layout.classList.add('sidebar-collapsed');
        }
    };

    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            if (document.body.classList.contains('mobile-menu-open')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }
    if (mobileBackdrop) {
        mobileBackdrop.addEventListener('click', closeMobileMenu);
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth > 900) {
            closeMobileMenu();
        } else {
            ensureMobileExpanded();
        }
    });

    const scrollActiveMenuItem = () => {
        const activeItem = document.querySelector('#menuList .nav-item.active');
        if (!activeItem) return;
        activeItem.scrollIntoView({ block: 'nearest', inline: 'nearest' });
    };

    window.addEventListener('load', scrollActiveMenuItem);
    scrollActiveMenuItem();

    const search = document.getElementById('menuSearch');
    const items = document.querySelectorAll('#menuList li');
    if (search) {
        search.addEventListener('input', () => {
            const term = search.value.toLowerCase();
            items.forEach((li) => {
                const label = li.textContent.toLowerCase();
                li.style.display = label.includes(term) ? '' : 'none';
            });
        });
    }

    const shortcuts = <?= json_encode($shortcutMap ?? []); ?>;

    const applyGlobalZoom = () => {
        const width = window.innerWidth;
        const height = window.innerHeight;
        const shouldZoom = width <= 1366 && height <= 768;
        document.documentElement.style.zoom = shouldZoom ? '0.87' : '';
    };

    applyGlobalZoom();
    window.addEventListener('resize', applyGlobalZoom);

    document.addEventListener('keydown', (event) => {
        if (event.target && ['INPUT', 'TEXTAREA'].includes(event.target.tagName)) {
            return;
        }
        const key = event.key.toUpperCase();
        if (shortcuts[key]) {
            event.preventDefault();
            window.location.href = shortcuts[key];
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const card = document.getElementById('card-acoes-rapidas');
        if (!card) return;

        const update = () => {
            const checked = document.querySelectorAll('.acao-rapida-checkbox:checked').length;
            if (checked === 0) {
                card.style.display = 'none';
                card.setAttribute('aria-hidden', 'true');
            } else {
                card.style.display = '';
                card.setAttribute('aria-hidden', 'false');
            }
        };

        document.addEventListener('change', (event) => {
            if (event.target && event.target.classList.contains('acao-rapida-checkbox')) {
                update();
            }
        });

        update();
    });

    const bindConfirmations = () => {
        document.querySelectorAll('#app form[data-confirm]').forEach((form) => {
            if (form.dataset.boundConfirm === '1') return;
            form.dataset.boundConfirm = '1';
            form.addEventListener('submit', (event) => {
                const message = form.getAttribute('data-confirm');
                if (!confirm(message)) {
                    event.preventDefault();
                }
            });
        });
    };

    bindConfirmations();
    if (window.lucide) {
        lucide.createIcons();
    }
</script>
</body>
</html>
