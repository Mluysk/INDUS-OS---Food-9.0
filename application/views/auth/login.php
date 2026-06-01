<?php
$theme = $theme ?? [];
$old = $old ?? [];
$themeMode = strtolower($theme['mode'] ?? 'light');
$primaryHex = $theme['primary_hex'] ?? '#5b616f';
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
$primaryHover = $primaryHex;
if (preg_match('/^#([0-9a-f]{6})$/i', $primaryHex, $match)) {
    $hex = $match[1];
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $factor = 0.88; // slightly darker for hover state
    $primaryHover = sprintf('#%02x%02x%02x', max(0, min(255, (int) ($r * $factor))), max(0, min(255, (int) ($g * $factor))), max(0, min(255, (int) ($b * $factor))));
}
$assetHelper = isset($asset) && is_callable($asset) ? $asset : static fn (string $path): string => $path;
$urlHelper = isset($url) && is_callable($url) ? $url : static fn (string $path = ''): string => ($path === '' ? '/' : $path);
$logoLightPath = $theme['logo_light'] ?? '';
$logoDarkPath = $theme['logo_dark'] ?? $logoLightPath;
$logoTintEnabled = ($theme['logo_filter'] ?? '0') === '1';
$logoLight = $logoLightPath ? $assetHelper($logoLightPath) : '';
$logoDark = $logoDarkPath ? $assetHelper($logoDarkPath) : '';
$logoMatrixValues = app_logo_matrix_values($primaryHex);
$logoLightSize = max(24, min(140, (int)($theme['logo_light_size'] ?? 52)));
$logoDarkSize = max(24, min(140, (int)($theme['logo_dark_size'] ?? $logoLightSize)));
$logoLightOffsetX = (int)round(max(-80, min(80, (int)($theme['logo_light_offset_x'] ?? 0))) * 0.5);
$logoLightOffsetY = (int)round(max(-80, min(80, (int)($theme['logo_light_offset_y'] ?? 0))) * 0.5);
$logoDarkOffsetX = (int)round(max(-80, min(80, (int)($theme['logo_dark_offset_x'] ?? 0))) * 0.5);
$logoDarkOffsetY = (int)round(max(-80, min(80, (int)($theme['logo_dark_offset_y'] ?? 0))) * 0.5);
$titleSize = isset($theme['title_size']) && is_numeric($theme['title_size']) ? (int) $theme['title_size'] : 24;
if ($titleSize < 16) {
    $titleSize = 16;
} elseif ($titleSize > 64) {
    $titleSize = 64;
}
$brandTitleStyle = 'font-size: '.$titleSize.'px;';
if (($theme['title_colorize'] ?? '0') === '1') {
    $brandTitleStyle .= ' color: '.htmlspecialchars($primaryHex, ENT_QUOTES, 'UTF-8').';';
}
$paletteName = $theme['primary_name'] ?? '';
$paletteHex = strtoupper($primaryHex);
$brandTagline = $appTagline ?? 'Sistema de Controle Industrial (SCI)';
$loginSubtitle = 'Faça login para acessar o painel';
$initialThemeAttr = $themeMode === 'dark' ? 'dark' : 'light';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="<?= $logoTintEnabled ? 'logo-filter-enabled' : ''; ?>" data-theme="<?= htmlspecialchars($initialThemeAttr, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?> &mdash; Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --bg: #f3f4f6;
            --bg-accent: rgba(15, 23, 42, 0.06);
            --card: #ffffff;
            --text: #1f2937;
            --text-muted: #4b5563;
            --primary: <?= htmlspecialchars(strtolower($primaryHex), ENT_QUOTES, 'UTF-8'); ?>;
            --primary-hover: <?= htmlspecialchars(strtolower($primaryHover), ENT_QUOTES, 'UTF-8'); ?>;
            --error: #dc2626;
            --success: #047857;
        }
        [data-theme="dark"] {
            color-scheme: dark;
            --bg: #0f172a;
            --bg-accent: rgba(148, 163, 184, 0.1);
            --card: rgba(15, 23, 42, 0.78);
            --text: #e5e7eb;
            --text-muted: #94a3b8;
            --primary: <?= htmlspecialchars(strtolower($primaryHex), ENT_QUOTES, 'UTF-8'); ?>;
            --primary-hover: <?= htmlspecialchars(strtolower($primaryHover), ENT_QUOTES, 'UTF-8'); ?>;
            --error: #f87171;
            --success: #34d399;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: radial-gradient(120% 120% at 50% 0%, var(--bg-accent) 0%, transparent 65%), var(--bg);
            color: var(--text);
            transition: background 0.3s ease, color 0.3s ease;
            padding: 24px;
        }
        .login-card {
            background: var(--card);
            border-radius: 20px;
            padding: 36px 32px 32px;
            width: min(420px, 96vw);
            box-shadow: 0 30px 60px -35px rgba(15, 23, 42, 0.55);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }
        .brand {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }
        .brand-identity {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .brand-logo {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .brand-logo img {
            height: var(--login-logo-size, 52px);
            width: auto;
            max-width: 100%;
            transform: translate(var(--login-logo-x, 0), var(--login-logo-y, 0));
            transition: opacity 0.2s ease;
            filter: none;
        }
        .brand-logo img.logo-tintable {
            transition: opacity 0.2s ease, filter 0.28s ease;
        }
        .brand-logo img.logo-dark {
            display: none;
        }
        [data-theme="dark"] .brand-logo img.logo-light {
            display: none;
        }
        [data-theme="dark"] .brand-logo img.logo-dark {
            display: block;
        }
        .brand-logo .logo-fallback {
            height: 52px;
            width: 52px;
            border-radius: 14px;
            background: var(--primary);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 600;
            font-size: 1.125rem;
        }
        .brand-text h1 {
            margin: 0;
            font-weight: 600;
            <?= $brandTitleStyle ?>
        }
        .brand-text p {
            margin: 0;
        }
        .brand-text p.login-subtitle {
            margin-top: 4px;
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .brand-text p.brand-tagline {
            margin-top: 6px;
            color: var(--text-muted);
            font-size: 0.8rem;
            opacity: 0.85;
        }
        .palette-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.18);
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: 10px;
        }
        .palette-tag .swatch {
            height: 16px;
            width: 16px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
        }
        .theme-toggle {
            border: none;
            background: rgba(148, 163, 184, 0.16);
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 6px 12px;
            transition: background 0.2s ease, color 0.2s ease;
        }
        .theme-toggle:hover {
            background: rgba(148, 163, 184, 0.28);
            color: var(--text);
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.45);
            background: rgba(255, 255, 255, 0.02);
            color: inherit;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(91, 97, 111, 0.18);
        }
        .login-input-shell {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .login-input-shell input {
            padding-left: 46px;
        }
        .login-input-shell--password input {
            padding-right: 48px;
        }
        .login-input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            color: var(--primary);
            transform: translateY(-50%);
            pointer-events: none;
            opacity: 0.9;
        }
        .login-input-icon svg,
        .login-password-toggle svg {
            display: block;
            width: 100%;
            height: 100%;
        }
        .login-password-toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            padding: 0;
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.14);
            color: var(--primary);
            cursor: pointer;
            box-shadow: none;
            transform: translateY(-50%) !important;
            transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease;
        }
        .login-password-toggle:hover,
        .login-password-toggle:focus-visible {
            outline: none;
            background: rgba(148, 163, 184, 0.22);
            border-color: var(--primary);
            box-shadow: none;
            transform: translateY(-50%) !important;
        }
        .login-password-toggle__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 17px;
            width: 17px;
            height: 17px;
            color: currentColor;
            pointer-events: none;
        }
        .login-password-toggle__icon--hide {
            display: none !important;
        }
        .login-password-toggle.is-visible .login-password-toggle__icon--show {
            display: none !important;
        }
        .login-password-toggle.is-visible .login-password-toggle__icon--hide {
            display: inline-flex !important;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px 16px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            background: var(--primary);
            color: #fff;
            transition: background 0.2s ease, transform 0.2s ease;
        }
        .btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }
        .alert {
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 0.95rem;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .alert-error {
            background: rgba(220, 38, 38, 0.12);
            color: var(--error);
        }
        .alert-success {
            background: rgba(4, 120, 87, 0.12);
            color: var(--success);
        }
        .actions {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }
        a {
            color: var(--primary);
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .logo-filter-enabled .logo-tintable {
            filter: url(#loginLogoTintFilter);
            -webkit-filter: url(#loginLogoTintFilter);
        }
        html.logo-filter-enabled[data-theme="light"] .brand-logo img.logo-dark {
            display: block !important;
        }
        html.logo-filter-enabled[data-theme="light"] .brand-logo img.logo-light {
            display: none !important;
        }
        @media (min-width: 521px) {
            .brand-logo img {
                transform: translateX(-4px);
            }
        }
        @media (max-width: 520px) {
            .login-card {
                padding: 28px 24px;
            }
            .brand {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .brand-identity,
            .brand-logo,
            .brand-text {
                align-items: center;
                width: 100%;
            }
            .brand-logo {
                justify-content: center;
            }
            .brand-logo img {
                transform: none;
            }
            .theme-toggle {
                align-self: flex-end;
            }
            .palette-tag {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php if ($logoTintEnabled): ?>
    <svg xmlns="http://www.w3.org/2000/svg" width="0" height="0" style="position:absolute;visibility:hidden;">
        <defs>
            <filter id="loginLogoTintFilter" color-interpolation-filters="sRGB">
                <feColorMatrix type="matrix" values="<?= htmlspecialchars($logoMatrixValues, ENT_QUOTES, 'UTF-8'); ?>"></feColorMatrix>
            </filter>
        </defs>
    </svg>
    <?php endif; ?>
    <div class="login-card">
        <div class="brand">
            <div class="brand-identity">
                <div class="brand-logo">
                    <?php if ($logoLight || $logoDark): ?>
                        <?php if ($logoLight): ?>
                            <img src="<?= htmlspecialchars($logoLight, ENT_QUOTES, 'UTF-8'); ?>" alt="Logo" class="logo-light logo-tintable" style="--login-logo-size: <?= $logoLightSize; ?>px; --login-logo-x: <?= $logoLightOffsetX; ?>px; --login-logo-y: <?= $logoLightOffsetY; ?>px;">
                        <?php endif; ?>
                        <?php if ($logoDark): ?>
                            <img src="<?= htmlspecialchars($logoDark, ENT_QUOTES, 'UTF-8'); ?>" alt="Logo" class="logo-dark logo-tintable" style="--login-logo-size: <?= $logoDarkSize; ?>px; --login-logo-x: <?= $logoDarkOffsetX; ?>px; --login-logo-y: <?= $logoDarkOffsetY; ?>px;">
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="logo-fallback"><?= htmlspecialchars(mb_substr($appName, 0, 2, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </div>
                <div class="brand-text">
                    <h1><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="login-subtitle"><?= htmlspecialchars($loginSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php if (!empty($brandTagline) && $brandTagline !== $loginSubtitle): ?>
                        <p class="brand-tagline"><?= htmlspecialchars($brandTagline, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($paletteName)): ?>
                        <span class="palette-tag">
                            <span class="swatch" style="background: <?= htmlspecialchars($primaryHex, ENT_QUOTES, 'UTF-8'); ?>;"></span>
                            <?= htmlspecialchars($paletteName, ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars($paletteHex, ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <button type="button" class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
                <span id="themeIcon" aria-hidden="true">🌞</span>
                <span id="themeLabel">Modo claro</span>
            </button>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Ops!</strong> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($urlHelper('login'), ENT_QUOTES, 'UTF-8'); ?>">
            <div style="margin-bottom: 18px;">
                <label for="login">E-mail, CPF ou CNPJ</label>
                <div class="login-input-shell">
                    <span class="login-input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.05" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input type="text" name="login" id="login" autocomplete="username" value="<?= htmlspecialchars($old['login'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <label for="senha">Senha</label>
                <div class="login-input-shell login-input-shell--password">
                    <span class="login-input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.05" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="11" width="16" height="10" rx="2"></rect>
                            <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                        </svg>
                    </span>
                    <input type="password" name="senha" id="senha" autocomplete="current-password" required>
                    <button type="button" class="login-password-toggle" data-password-toggle aria-label="Mostrar senha" aria-pressed="false">
                        <span class="login-password-toggle__icon login-password-toggle__icon--show" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.05" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </span>
                        <span class="login-password-toggle__icon login-password-toggle__icon--hide" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.05" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c6.5 0 10 7 10 7a13.16 13.16 0 0 1-2.12 2.95"></path>
                                <path d="M6.61 6.61A13.53 13.53 0 0 0 2 12s3.5 7 10 7a10.63 10.63 0 0 0 5.39-1.61"></path>
                                <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88"></path>
                                <path d="M3 3l18 18"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
            <button class="btn" type="submit">Entrar</button>
        </form>

        <div class="actions">
            <span>Esqueceu a senha? <a href="#" onclick="alert('Entre em contato com o suporte para recuperar sua senha.'); return false;">Solicitar suporte</a></span>
            <a href="<?= htmlspecialchars($urlHelper('/'), ENT_QUOTES, 'UTF-8'); ?>">Voltar ao site</a>
        </div>
    </div>

    <script>
        const root = document.documentElement;
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        const storedTheme = localStorage.getItem('theme');
        const defaultTheme = '<?= $initialThemeAttr === 'dark' ? 'dark' : 'light'; ?>';
        const toggle = document.getElementById('themeToggle');
        const icon = document.getElementById('themeIcon');
        const label = document.getElementById('themeLabel');

        function applyTheme(theme) {
            const resolved = theme === 'dark' ? 'dark' : 'light';
            root.setAttribute('data-theme', resolved);
            if (resolved === 'dark') {
                icon.textContent = '🌙';
                label.textContent = 'Modo escuro';
            } else {
                icon.textContent = '🌞';
                label.textContent = 'Modo claro';
            }
        }

        let initialTheme = storedTheme;
        if (!initialTheme) {
            initialTheme = defaultTheme || (prefersDark ? 'dark' : 'light');
        }
        applyTheme(initialTheme);

        const applyGlobalZoom = () => {
            const width = window.innerWidth;
            const height = window.innerHeight;
            const shouldZoom = width <= 1366 && height <= 768;
            document.documentElement.style.zoom = shouldZoom ? '0.87' : '';
        };

        applyGlobalZoom();
        window.addEventListener('resize', applyGlobalZoom);

        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            const shell = button.closest('.login-input-shell');
            const input = shell?.querySelector('input[type="password"], input[type="text"]');
            if (!input) return;
            button.addEventListener('click', () => {
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                button.classList.toggle('is-visible', !visible);
                button.setAttribute('aria-pressed', String(!visible));
                button.setAttribute('aria-label', visible ? 'Mostrar senha' : 'Ocultar senha');
                input.focus();
            });
        });

        toggle.addEventListener('click', () => {
            const current = root.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', next);
            applyTheme(next);
        });
    </script>
</body>
</html>
