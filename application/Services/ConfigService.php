<?php
namespace App\Services;

use App\Repositories\ConfiguracoesRepository;

class ConfigService
{
    private ConfiguracoesRepository $configuracoes;

    /**
     * Configuracoes visuais e atalhos sao pessoais por usuario logado.
     * Logos do sistema principal continuam globais, pois representam a marca
     * do ADM para todas as contas.
     * O restante continua global para preservar regras de producao, estoque,
     * nota, portal e demais parametros operacionais.
     */
    private const USER_SCOPED_PREFIXES = [
        'THEME_',
    ];

    private const USER_SCOPED_KEYS = [
        'DASH_QUICK_ACTIONS',
    ];

    private const GLOBAL_THEME_PREFIXES = [
        'THEME_LOGO_',
    ];

    public function __construct(ConfiguracoesRepository $configuracoes)
    {
        $this->configuracoes = $configuracoes;
    }

    public function get(string $chave, ?string $default = null): ?string
    {
        if ($this->isGlobalThemeKey($chave)) {
            $adminKey = $this->adminScopedKey($chave);
            if ($adminKey !== null) {
                $adminValue = $this->configuracoes->get($adminKey, null);
                if ($adminValue !== null) {
                    return $adminValue;
                }
            }
        }

        if (($this->isLoginRequest() || $this->isPublicPortalRequest()) && $this->isThemeKey($chave)) {
            $adminKey = $this->adminScopedKey($chave);
            if ($adminKey !== null) {
                $adminValue = $this->configuracoes->get($adminKey, null);
                if ($adminValue !== null) {
                    return $adminValue;
                }
            }
        }

        if ($this->isUserScoped($chave)) {
            $userKey = $this->userScopedKey($chave);
            if ($userKey !== null) {
                $userValue = $this->configuracoes->get($userKey, null);
                if ($userValue !== null) {
                    return $userValue;
                }

                $personalDefault = $this->personalThemeDefault($chave);
                if ($personalDefault !== null) {
                    return $personalDefault;
                }
            }
        }

        return $this->configuracoes->get($chave, $default);
    }

    private function personalThemeDefault(string $chave): ?string
    {
        return match ($chave) {
            'THEME_PRIMARY_HEX' => '#DC2626',
            'THEME_PRIMARY_NAME' => 'Vermelho Energia',
            default => null,
        };
    }

    public function set(string $chave, string $valor): void
    {
        if ($this->shouldPreservePortalProductSave($chave)) {
            return;
        }
        if ($this->shouldPreserveVisualToggle($chave, $valor)) {
            return;
        }

        $targetKey = $this->userScopedKey($chave) ?? $chave;
        $conn = $this->configuracoes->getConnection();

        $update = $conn->prepare('UPDATE configuracoes SET valor = :valor WHERE chave = :chave');
        $update->execute(['chave' => $targetKey, 'valor' => $valor]);

        if ($update->rowCount() > 0) {
            $this->clearLegacyAdminScopedValue($chave);
            return;
        }

        $insert = $conn->prepare('INSERT INTO configuracoes (chave, valor) VALUES (:chave, :valor)');
        try {
            $insert->execute(['chave' => $targetKey, 'valor' => $valor]);
        } catch (\PDOException $exception) {
            $retry = $conn->prepare('UPDATE configuracoes SET valor = :valor WHERE chave = :chave');
            $retry->execute(['chave' => $targetKey, 'valor' => $valor]);
        }

        $this->clearLegacyAdminScopedValue($chave);
    }

    public function getConnection(): \PDO
    {
        return $this->configuracoes->getConnection();
    }

    private function shouldPreservePortalProductSave(string $chave): bool
    {
        $activeTab = (string)($_POST['active_tab'] ?? '');
        if ($activeTab !== 'portal-products') {
            return false;
        }

        return $chave !== 'PORTAL_PRODUCT_IDS';
    }

    private function shouldPreserveVisualToggle(string $chave, string $valor): bool
    {
        $visualToggles = ['THEME_TITLE_COLORIZE', 'THEME_LOGO_FILTER'];
        if (!in_array($chave, $visualToggles, true) || $valor !== '0') {
            return false;
        }

        $activeTab = (string)($_POST['active_tab'] ?? '');
        if ($activeTab === 'appearance') {
            return false;
        }

        if (array_key_exists($chave, $_POST)) {
            return false;
        }

        if (isset($_POST['config']) && is_array($_POST['config']) && array_key_exists($chave, $_POST['config'])) {
            return false;
        }

        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
    }

    private function isUserScoped(string $chave): bool
    {
        if ($this->isLoginRequest()) {
            return false;
        }

        if ($this->isGlobalThemeKey($chave)) {
            return false;
        }

        if (in_array($chave, self::USER_SCOPED_KEYS, true)) {
            return true;
        }

        return $this->isThemeKey($chave);
    }

    private function isThemeKey(string $chave): bool
    {
        foreach (self::USER_SCOPED_PREFIXES as $prefix) {
            if (str_starts_with($chave, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function isGlobalThemeKey(string $chave): bool
    {
        foreach (self::GLOBAL_THEME_PREFIXES as $prefix) {
            if (str_starts_with($chave, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function userScopedKey(string $chave): ?string
    {
        if (!$this->isUserScoped($chave)) {
            return null;
        }

        $userId = $this->currentUserId();
        if ($userId <= 0) {
            return null;
        }

        return 'USER_'.$userId.'_'.$chave;
    }

    private function adminScopedKey(string $chave): ?string
    {
        $adminId = $this->adminUserId();
        if ($adminId <= 0) {
            return null;
        }

        return 'USER_'.$adminId.'_'.$chave;
    }

    private function clearLegacyAdminScopedValue(string $chave): void
    {
        if (!$this->isGlobalThemeKey($chave)) {
            return;
        }

        $adminKey = $this->adminScopedKey($chave);
        if ($adminKey === null) {
            return;
        }

        $delete = $this->configuracoes->getConnection()->prepare('DELETE FROM configuracoes WHERE chave = :chave');
        $delete->execute(['chave' => $adminKey]);
    }

    private function currentUserId(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int)($_SESSION['user_id'] ?? 0);
    }

    private function adminUserId(): int
    {
        try {
            $conn = $this->configuracoes->getConnection();
            $stmt = $conn->query("SELECT id FROM usuarios WHERE papel = 'admin' AND COALESCE(is_ativo, 1) = 1 ORDER BY id ASC LIMIT 1");
            return (int)($stmt->fetchColumn() ?: 0);
        } catch (\Throwable $exception) {
            return 0;
        }
    }

    private function isLoginRequest(): bool
    {
        return in_array($this->currentRequestPath(), ['/login', '/logout'], true);
    }

    private function isPublicPortalRequest(): bool
    {
        $uri = $this->currentRequestPath();
        if ($uri === '/portal' || $uri === '/portal/registro') {
            return true;
        }

        if ($uri !== '/produtos/print') {
            return false;
        }

        $mode = strtolower(trim((string)($_GET['modo'] ?? '')));
        $shared = strtolower(trim((string)($_GET['compartilhar'] ?? $_GET['share'] ?? '')));

        return $mode === 'visual' && in_array($shared, ['1', 'sim', 'true'], true);
    }

    private function currentRequestPath(): string
    {
        $uri = strtok((string)($_SERVER['REQUEST_URI'] ?? ''), '?') ?: '';
        $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptDir = rtrim(dirname($scriptName), '/');
        $basePath = $scriptDir === '/' ? '' : $scriptDir;

        if ($basePath !== '' && str_ends_with($basePath, '/public')) {
            $basePath = substr($basePath, 0, -strlen('/public'));
        }

        if ($basePath !== '' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath)) ?: '/';
        }

        if (str_starts_with($uri, '/index.php')) {
            $uri = substr($uri, strlen('/index.php')) ?: '/';
        }

        $uri = '/' . trim($uri, '/');
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }
}
