<?php
namespace App\Core;

use App\Services\AuthService;
use App\Services\AuditService;
use App\Services\ConfigService;

abstract class Controller
{
    protected Container $container;
    protected AuthService $auth;
    protected AuditService $audit;
    private ?array $inputCache = null;

    public function __construct(Container $container, AuthService $auth, AuditService $audit)
    {
        $this->container = $container;
        $this->auth = $auth;
        $this->audit = $audit;
    }

    protected function view(string $path, array $data = []): void
    {
        extract($data);
        include __DIR__.'/../views/'.$path.'.php';
    }

    protected function buildSharedViewData(): array
    {
        $config = $this->container->getConfig();

        $appName = $config['name'] ?? 'IndusOS Food';
        $appTagline = 'Sistema de Controle Industrial (SCI)';
        $support = [
            'email' => 'suporte@indusos.com.br',
            'telefone' => '(11) 3000-0000',
            'whatsapp' => '+55 11 99999-9999',
            'horario' => 'Segunda a sexta, 8h às 18h',
            'mensagem' => 'Fale conosco sempre que precisar ajustar volumes, lotes ou agendar coletas.',
        ];
        $nota = [
            'empresa' => 'IndusOS Food',
            'documento' => 'CNPJ 00.000.000/0000-00',
            'endereco' => 'Rua Exemplo, 123 - Bairro - Cidade/UF',
            'contato' => '(11) 3000-0000',
            'email' => 'contato@indusos.com.br',
            'site' => 'indusos.com.br',
            'logo' => 'uploads/branding/logodark.png',
            'pix_enabled' => false,
            'pix_key' => '',
        ];
        $theme = [
            'mode' => 'light',
            'primary_hex' => '#5b616f',
            'primary_name' => 'Grafite Suave',
            'logo_light' => 'uploads/branding/logodark.png',
            'logo_dark' => 'uploads/branding/logowhite.png',
            'logo_compact' => 'uploads/branding/logosmalldark.png',
            'logo_compact_light' => 'uploads/branding/logosmalldark.png',
            'logo_compact_dark' => 'uploads/branding/logosmallwhite.png',
            'logo_footer' => 'uploads/branding/logo.png',
            'title_colorize' => '0',
            'logo_filter' => '0',
            'title_size' => '26',
            'title_offset_x' => '0',
            'title_offset_y' => '0',
            'logo_light_size' => '57',
            'logo_dark_size' => '57',
            'logo_compact_light_size' => '76',
            'logo_compact_dark_size' => '76',
            'logo_light_offset_x' => '0',
            'logo_light_offset_y' => '0',
            'logo_dark_offset_x' => '0',
            'logo_dark_offset_y' => '0',
            'logo_compact_light_offset_x' => '0',
            'logo_compact_light_offset_y' => '0',
            'logo_compact_dark_offset_x' => '0',
            'logo_compact_dark_offset_y' => '0',
            'portal_login_logo_light' => 'uploads/branding/logodark.png',
            'portal_login_logo_dark' => 'uploads/branding/logowhite.png',
            'portal_login_logo_light_size' => '60',
            'portal_login_logo_dark_size' => '60',
            'portal_login_logo_light_offset_x' => '0',
            'portal_login_logo_light_offset_y' => '0',
            'portal_login_logo_dark_offset_x' => '0',
            'portal_login_logo_dark_offset_y' => '0',
            'portal_login_logo_filter' => '0',
            'notification_popup_seconds' => '3',
        ];
        $quickActionDefaults = [
            [
                'key' => 'clientes',
                'label' => 'Clientes',
                'description' => 'Cadastre e acompanhe contas e acessos.',
                'href' => 'clientes',
                'icon' => 'users',
                'shortcut' => 'F1',
                'enabled' => '1',
            ],
            [
                'key' => 'produtos',
                'label' => 'Produtos',
                'description' => 'Atualize catálogo, BOM e fichas técnicas.',
                'href' => 'produtos',
                'icon' => 'package',
                'shortcut' => 'F2',
                'enabled' => '1',
            ],
            [
                'key' => 'pedidos',
                'label' => 'Pedidos',
                'description' => 'Gerencie vendas e aprovações do portal.',
                'href' => 'pedidos',
                'icon' => 'file-text',
                'shortcut' => 'F3',
                'enabled' => '1',
            ],
            [
                'key' => 'producao',
                'label' => 'Ordens de Produção',
                'description' => 'Planeje, inicie e finalize lotes de fabricação.',
                'href' => 'producao/op',
                'icon' => 'factory',
                'shortcut' => 'F4',
                'enabled' => '1',
            ],
            [
                'key' => 'estoque',
                'label' => 'Estoque',
                'description' => 'Confira saldos, reservas e validade de lotes.',
                'href' => 'estoque/posicao',
                'icon' => 'boxes',
                'shortcut' => 'F5',
                'enabled' => '1',
            ],
            [
                'key' => 'expedicao',
                'label' => 'Expedição',
                'description' => 'Monte picking FEFO e gere romaneios.',
                'href' => 'expedicao',
                'icon' => 'truck',
                'shortcut' => 'F6',
                'enabled' => '1',
            ],
            [
                'key' => 'financeiro',
                'label' => 'Financeiro',
                'description' => 'Controle contas a pagar e receber.',
                'href' => 'financeiro/titulos',
                'icon' => 'wallet',
                'shortcut' => 'F7',
                'enabled' => '1',
            ],
        ];
        $quickActions = $quickActionDefaults;

        try {
            /** @var ConfigService $configService */
            $configService = $this->container->get(ConfigService::class);
            $appName = $configService->get('THEME_APP_TITLE', $appName) ?: $appName;
            $appTagline = $configService->get('THEME_APP_TAGLINE', $appTagline) ?: $appTagline;
            $theme['mode'] = strtolower($configService->get('THEME_MODE', $theme['mode']) ?: $theme['mode']);
            $theme['primary_hex'] = $configService->get('THEME_PRIMARY_HEX', $theme['primary_hex']) ?: $theme['primary_hex'];
            $theme['primary_name'] = $configService->get('THEME_PRIMARY_NAME', $theme['primary_name']) ?: $theme['primary_name'];
            $notificationSeconds = $configService->get('NOTIFICATION_POPUP_SECONDS', $theme['notification_popup_seconds']) ?: $theme['notification_popup_seconds'];
            $notificationSeconds = is_numeric($notificationSeconds) ? (int)$notificationSeconds : 3;
            $notificationSeconds = max(1, min(30, $notificationSeconds));
            $theme['notification_popup_seconds'] = (string)$notificationSeconds;
            $theme['logo_light'] = $configService->get('THEME_LOGO_LIGHT', $theme['logo_light']) ?: $theme['logo_light'];
            $theme['logo_dark'] = $configService->get('THEME_LOGO_DARK', $theme['logo_dark']) ?: $theme['logo_dark'];
            $compactFallback = $configService->get('THEME_LOGO_COMPACT', $theme['logo_compact']) ?: $theme['logo_compact'];
            $theme['logo_compact_light'] = $configService->get('THEME_LOGO_COMPACT_LIGHT', $compactFallback) ?: $compactFallback;
            $theme['logo_compact_dark'] = $configService->get('THEME_LOGO_COMPACT_DARK', $theme['logo_compact_dark']) ?: $theme['logo_compact_dark'];
            $theme['logo_compact'] = $theme['logo_compact_light'];
            $theme['logo_footer'] = $configService->get('THEME_LOGO_FOOTER', $theme['logo_footer']) ?: $theme['logo_footer'];
            $theme['portal_login_logo_light'] = $configService->get('PORTAL_LOGIN_LOGO_LIGHT', $theme['logo_light']) ?: $theme['logo_light'];
            $theme['portal_login_logo_dark'] = $configService->get('PORTAL_LOGIN_LOGO_DARK', $theme['logo_dark']) ?: $theme['logo_dark'];
            $colorize = $configService->get('THEME_TITLE_COLORIZE', $theme['title_colorize']) ?: $theme['title_colorize'];
            $theme['title_colorize'] = $colorize === '1' ? '1' : '0';
            $logoFilter = $configService->get('THEME_LOGO_FILTER', $theme['logo_filter']) ?: $theme['logo_filter'];
            $theme['logo_filter'] = $logoFilter === '1' ? '1' : '0';
            $portalLoginLogoFilter = $configService->get('PORTAL_LOGIN_LOGO_FILTER', $theme['portal_login_logo_filter']) ?: $theme['portal_login_logo_filter'];
            $theme['portal_login_logo_filter'] = $portalLoginLogoFilter === '1' ? '1' : '0';
            $titleSize = $configService->get('THEME_TITLE_SIZE', $theme['title_size']) ?: $theme['title_size'];
            if (!is_numeric($titleSize)) {
                $titleSize = $theme['title_size'];
            }
            $sizeInt = (int) $titleSize;
            if ($sizeInt < 16) {
                $sizeInt = 16;
            } elseif ($sizeInt > 64) {
                $sizeInt = 64;
            }
            $theme['title_size'] = (string) $sizeInt;

            $titleOffsetX = $configService->get('THEME_TITLE_OFFSET_X', $theme['title_offset_x']) ?: $theme['title_offset_x'];
            $titleOffsetY = $configService->get('THEME_TITLE_OFFSET_Y', $theme['title_offset_y']) ?: $theme['title_offset_y'];
            $offsetX = is_numeric($titleOffsetX) ? (int)$titleOffsetX : 0;
            $offsetY = is_numeric($titleOffsetY) ? (int)$titleOffsetY : 0;
            $theme['title_offset_x'] = (string)max(-80, min(80, $offsetX));
            $theme['title_offset_y'] = (string)max(-80, min(80, $offsetY));

            $sizeKeys = [
                'logo_light_size' => ['key' => 'THEME_LOGO_LIGHT_SIZE', 'min' => 24, 'max' => 140],
                'logo_dark_size' => ['key' => 'THEME_LOGO_DARK_SIZE', 'min' => 24, 'max' => 140],
                'logo_compact_light_size' => ['key' => 'THEME_LOGO_COMPACT_LIGHT_SIZE', 'min' => 16, 'max' => 120],
                'logo_compact_dark_size' => ['key' => 'THEME_LOGO_COMPACT_DARK_SIZE', 'min' => 16, 'max' => 120],
                'portal_login_logo_light_size' => ['key' => 'PORTAL_LOGIN_LOGO_LIGHT_SIZE', 'min' => 24, 'max' => 180],
                'portal_login_logo_dark_size' => ['key' => 'PORTAL_LOGIN_LOGO_DARK_SIZE', 'min' => 24, 'max' => 180],
            ];

            foreach ($sizeKeys as $themeKey => $settings) {
                $raw = $configService->get($settings['key'], $theme[$themeKey]) ?: $theme[$themeKey];
                $val = is_numeric($raw) ? (int)$raw : (int)$theme[$themeKey];
                $val = max($settings['min'], min($settings['max'], $val));
                $theme[$themeKey] = (string)$val;
            }

            $offsetKeys = [
                'logo_light_offset_x' => 'THEME_LOGO_LIGHT_OFFSET_X',
                'logo_light_offset_y' => 'THEME_LOGO_LIGHT_OFFSET_Y',
                'logo_dark_offset_x' => 'THEME_LOGO_DARK_OFFSET_X',
                'logo_dark_offset_y' => 'THEME_LOGO_DARK_OFFSET_Y',
                'logo_compact_light_offset_x' => 'THEME_LOGO_COMPACT_LIGHT_OFFSET_X',
                'logo_compact_light_offset_y' => 'THEME_LOGO_COMPACT_LIGHT_OFFSET_Y',
                'logo_compact_dark_offset_x' => 'THEME_LOGO_COMPACT_DARK_OFFSET_X',
                'logo_compact_dark_offset_y' => 'THEME_LOGO_COMPACT_DARK_OFFSET_Y',
                'portal_login_logo_light_offset_x' => 'PORTAL_LOGIN_LOGO_LIGHT_OFFSET_X',
                'portal_login_logo_light_offset_y' => 'PORTAL_LOGIN_LOGO_LIGHT_OFFSET_Y',
                'portal_login_logo_dark_offset_x' => 'PORTAL_LOGIN_LOGO_DARK_OFFSET_X',
                'portal_login_logo_dark_offset_y' => 'PORTAL_LOGIN_LOGO_DARK_OFFSET_Y',
            ];

            foreach ($offsetKeys as $themeKey => $configKey) {
                $raw = $configService->get($configKey, $theme[$themeKey]) ?: $theme[$themeKey];
                $minOffset = strpos($configKey, 'PORTAL_LOGIN_LOGO_') === 0 ? -200 : -80;
                $maxOffset = strpos($configKey, 'PORTAL_LOGIN_LOGO_') === 0 ? 200 : 80;
                $val = is_numeric($raw) ? (int)$raw : 0;
                $val = max($minOffset, min($maxOffset, $val));
                $theme[$themeKey] = (string)$val;
            }

            $support['email'] = $configService->get('PORTAL_SUPPORT_EMAIL', $support['email']) ?: $support['email'];
            $support['telefone'] = $configService->get('PORTAL_SUPPORT_PHONE', $support['telefone']) ?: $support['telefone'];
            $support['whatsapp'] = $configService->get('PORTAL_SUPPORT_WHATSAPP', $support['whatsapp']) ?: $support['whatsapp'];
            $support['horario'] = $configService->get('PORTAL_SUPPORT_HOURS', $support['horario']) ?: $support['horario'];
            $support['mensagem'] = $configService->get('PORTAL_SUPPORT_MESSAGE', $support['mensagem']) ?: $support['mensagem'];

            $nota['empresa'] = $configService->get('NOTE_FABRICA_NOME', $nota['empresa']) ?: $nota['empresa'];
            $nota['documento'] = $configService->get('NOTE_FABRICA_DOC', $nota['documento']) ?: $nota['documento'];
            $nota['endereco'] = $configService->get('NOTE_FABRICA_ENDERECO', $nota['endereco']) ?: $nota['endereco'];
            $nota['contato'] = $configService->get('NOTE_FABRICA_CONTATO', $nota['contato']) ?: $nota['contato'];
            $nota['email'] = $configService->get('NOTE_FABRICA_EMAIL', $nota['email']) ?: $nota['email'];
            $nota['site'] = $configService->get('NOTE_FABRICA_SITE', $nota['site']) ?: $nota['site'];
            $notaLogo = $configService->get('NOTE_LOGO', $theme['logo_light'] ?? $nota['logo']) ?: ($theme['logo_light'] ?? $nota['logo']);
            $nota['logo'] = $notaLogo;
            $pixEnabled = $configService->get('NOTE_PIX_ENABLED', '0') === '1';
            $pixKey = trim((string)$configService->get('NOTE_PIX_KEY', ''));
            $pixType = strtolower((string)$configService->get('NOTE_PIX_KEY_TYPE', 'cnpj'));
            $nota['pix_enabled'] = $pixEnabled;
            $nota['pix_key'] = $pixKey;
            $nota['pix_type'] = in_array($pixType, ['cnpj','cpf','email','telefone'], true) ? $pixType : 'cnpj';

            $rawQuickActions = $configService->get('DASH_QUICK_ACTIONS', '');
            $decodedQuickActions = json_decode((string)$rawQuickActions, true);
            if (is_array($decodedQuickActions)) {
                $quickActions = array_map(static function (array $action) use ($decodedQuickActions) {
                    $state = $decodedQuickActions[$action['key']] ?? [];
                    $action['enabled'] = isset($state['enabled']) ? (string)$state['enabled'] : $action['enabled'];
                    $action['shortcut'] = isset($state['shortcut']) ? (string)$state['shortcut'] : $action['shortcut'];
                    return $action;
                }, $quickActionDefaults);
            }
        } catch (\Throwable $e) {
            // Ignore configuration lookup errors when the database é inacessível.
        }

        $basePath = rtrim($_SERVER['APP_BASE_PATH'] ?? '', '/');
        $urlBuilder = function (string $path = '') use ($basePath): string {
            if (preg_match('#^https?://#i', $path)) {
                return $path;
            }
            $trimmed = '/' . ltrim($path, '/');
            $prefix = $basePath === '' ? '' : $basePath;
            return $prefix . ($path === '' ? '/' : $trimmed);
        };
        $assetBuilder = function (string $path) use ($basePath): string {
            if (preg_match('#^https?://#i', $path)) {
                return $path;
            }

            $normalized = str_replace('\\', '/', trim($path));
            if (preg_match('#(?:^|/)public/(.+)$#i', $normalized, $matches)) {
                $normalized = $matches[1];
            } elseif (preg_match('#(?:^|/)(uploads/.+)$#i', $normalized, $matches)) {
                $normalized = $matches[1];
            }
            $normalized = ltrim($normalized, '/');
            if (stripos($normalized, 'public/') === 0) {
                $normalized = substr($normalized, 7);
            }

            $trimmed = '/' . ltrim($normalized, '/');
            $prefix = $basePath === '' ? '' : $basePath;
            return $prefix . $trimmed;
        };

        $shortcutMap = [];
        $quickActions = array_values(array_filter($quickActions, static fn(array $action): bool => ($action['enabled'] ?? '1') === '1'));
        foreach ($quickActions as $action) {
            if (($action['enabled'] ?? '1') !== '1') {
                continue;
            }
            $shortcut = strtoupper((string)($action['shortcut'] ?? ''));
            if ($shortcut === '') {
                continue;
            }
            $shortcutMap[$shortcut] = $urlBuilder($action['href'] ?? '');
        }

        return [
            'appName' => $appName,
            'appTagline' => $appTagline,
            'theme' => $theme,
            'basePath' => $basePath,
            'url' => $urlBuilder,
            'asset' => $assetBuilder,
            'support' => $support,
            'nota' => $nota,
            'quickActions' => $quickActions,
            'shortcutMap' => $shortcutMap,
        ];
    }

    protected function render(string $view, array $data = [], array $options = []): void
    {
        $shared = $this->buildSharedViewData();
        $flash = $this->pullFlash();
        $loggedUser = $this->auth->user();

        $data = array_merge($shared, [
            'title' => $options['title'] ?? '',
            'pageSubtitle' => $options['subtitle'] ?? '',
            'pageActions' => $options['pageActions'] ?? [],
            'breadcrumbs' => $options['breadcrumbs'] ?? [],
            'activeMenu' => $options['activeMenu'] ?? null,
            'user' => $loggedUser,
            'flash' => $flash,
        ], $data);

        extract($data);

        ob_start();
        include __DIR__.'/../views/'.$view.'.php';
        $content = ob_get_clean();

        // Garante que variáveis internas da view não sobrescrevam o usuário autenticado no layout.
        $user = $loggedUser;

        $layout = $options['layout'] ?? 'layouts/app';
        include __DIR__.'/../views/'.$layout.'.php';
    }

    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function redirect(string $url): void
    {
        if (!preg_match('#^https?://#i', $url)) {
            $basePath = rtrim($_SERVER['APP_BASE_PATH'] ?? '', '/');
            $prefix = $basePath === '' ? '' : $basePath;
            if ($url === '' || $url === '/') {
                $url = $prefix ?: '/';
            } elseif ($url[0] === '/') {
                $url = $prefix . $url;
            } else {
                $url = $prefix . '/' . ltrim($url, '/');
            }
        }

        header('Location: '.$url);
        exit;
    }

    protected function setFlash(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['flash_'.$type] = $message;
    }

    protected function pullFlash(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $flash = [
            'success' => $_SESSION['flash_success'] ?? null,
            'error' => $_SESSION['flash_error'] ?? null,
        ];
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        return $flash;
    }

    protected function wantsJson(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (str_starts_with($uri, '/api/')) {
            return true;
        }

        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (stripos($accept, 'application/json') !== false) {
            return true;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            return true;
        }

        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return strcasecmp($requestedWith, 'XMLHttpRequest') === 0;
    }

    protected function getAuthorizationHeader(): string
    {
        $candidates = [
            $_SERVER['HTTP_AUTHORIZATION'] ?? '',
            $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '',
            $_SERVER['Authorization'] ?? '',
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return $candidate;
            }
        }

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            foreach (['Authorization', 'authorization'] as $key) {
                if (!empty($headers[$key])) {
                    return $headers[$key];
                }
            }
        }

        return '';
    }

    protected function extractBearerToken(?string $header = null): ?string
    {
        $value = $header ?? $this->getAuthorizationHeader();
        if (!is_string($value) || $value === '') {
            return null;
        }

        if (preg_match('/Bearer\s+(\S+)/i', $value, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Safely fetch a request value from JSON or form data.
     */
    protected function input(?string $key = null, $default = null)
    {
        $data = $this->gatherInput();
        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    private function gatherInput(): array
    {
        if ($this->inputCache !== null) {
            return $this->inputCache;
        }

        $json = json_decode(file_get_contents('php://input'), true);
        if (!is_array($json)) {
            $json = [];
        }

        // Merge GET first so body values can intentionally override them when present.
        $data = array_merge($_GET, $_POST, $json);
        $this->inputCache = $data;

        return $data;
    }
}
