<?php
namespace App\Services;

use App\Repositories\UsuariosRepository;
use App\Repositories\ClientesRepository;
use App\Libraries\Jwt;

class AuthService
{
    private UsuariosRepository $usuarios;
    private ClientesRepository $clientes;
    private array $config;
    private ?array $currentUser = null;

    public function __construct(UsuariosRepository $usuarios, ClientesRepository $clientes)
    {
        $this->usuarios = $usuarios;
        $this->clientes = $clientes;
        $this->config = require __DIR__.'/../config/app.php';
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function attempt(string $login, string $senha): bool
    {
        $usuario = $this->usuarios->findByEmailOrDocumento($login);
        if (!$usuario) {
            return false;
        }

        $hash = $usuario['senha_hash'] ?? '';
        $valid = false;
        if ($hash) {
            if (function_exists('password_verify') && password_get_info($hash)['algo'] !== 0) {
                $valid = password_verify($senha, $hash);
            } else {
                $valid = hash_equals($hash, $senha);
            }
        }

        if (!$valid) {
            return false;
        }
        if (!($usuario['is_ativo'] ?? 1)) {
            return false;
        }
        $usuario = $this->hydrateCliente($usuario);
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_role'] = $usuario['papel'];
        $_SESSION['cliente_id'] = $usuario['cliente_id'] ?? null;
        $this->currentUser = $usuario;
        return true;
    }

    public function check(): bool
    {
        if ($this->currentUser) {
            return true;
        }
        if (!empty($_SESSION['user_id'])) {
            $usuario = $this->usuarios->find((int)$_SESSION['user_id']);
            if ($usuario) {
                $this->currentUser = $this->hydrateCliente($usuario);
            }
        }
        return (bool)$this->currentUser;
    }

    public function user(): ?array
    {
        $this->check();
        return $this->currentUser;
    }

    public function id(): ?int
    {
        $this->check();
        return isset($this->currentUser['id']) ? (int) $this->currentUser['id'] : null;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->currentUser = null;
    }

    public function ensureRole(array $roles): void
    {
        if (!$this->check()) {
            if ($this->wantsJson()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Autenticação necessária']);
            } else {
                $_SESSION['flash_error'] = 'Por favor, faça login para continuar.';
                header('Location: /login');
            }
            exit;
        }

        $role = (string)($this->currentUser['papel'] ?? '');
        if ($role === 'admin') {
            return;
        }

        $moduleAccess = $this->moduleAccessForCurrentRequest($role);
        if ($moduleAccess !== null) {
            if ($moduleAccess) {
                return;
            }
        } elseif (in_array($role, $roles, true)) {
            return;
        }

        if ($this->wantsJson()) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Permissões insuficientes']);
        } else {
            http_response_code(403);
            header('Content-Type: text/html; charset=utf-8');
            echo '<h1>Acesso negado</h1><p>Você não possui permissão para acessar este recurso.</p>';
        }
        exit;
    }

    private function moduleAccessForCurrentRequest(string $role): ?bool
    {
        $module = $this->currentModuleKey();
        if ($module === null) {
            return null;
        }

        try {
            $conn = $this->usuarios->getConnection();
            $stmt = $conn->prepare("SELECT valor FROM configuracoes WHERE chave = 'RBAC_MODULES' ORDER BY updated_at DESC LIMIT 1");
            $stmt->execute();
            $raw = $stmt->fetchColumn();
        } catch (\Throwable $exception) {
            return null;
        }

        if (!is_string($raw) || trim($raw) === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || !array_key_exists($role, $decoded) || !is_array($decoded[$role])) {
            return null;
        }

        return in_array($module, array_map('strval', $decoded[$role]), true);
    }

    private function currentModuleKey(): ?string
    {
        $path = strtok((string)($_SERVER['REQUEST_URI'] ?? ''), '?') ?: '/';
        $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptDir = rtrim(dirname($scriptName), '/');
        $basePath = rtrim((string)($_SERVER['APP_BASE_PATH'] ?? ''), '/');

        if ($scriptDir !== '' && $scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir)) ?: '/';
        }

        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        if (str_starts_with($path, '/index.php')) {
            $path = substr($path, strlen('/index.php')) ?: '/';
        }

        $segment = trim($path, '/');
        $segment = $segment === '' ? 'dashboard' : explode('/', $segment)[0];

        $aliases = [
            'metas' => 'meta',
            'meta' => 'meta',
            'producao' => 'producao',
            'financeiro' => 'financeiro',
            'config' => 'config',
            'auditoria' => 'auditoria',
            'relatorios' => 'relatorios',
            'clientes' => 'clientes',
            'produtos' => 'produtos',
            'pedidos' => 'pedidos',
            'mapa' => 'mapa',
            'desempenho' => 'desempenho',
            'estoque' => 'estoque',
            'expedicao' => 'expedicao',
            'receitas' => 'receitas',
            'dashboard' => 'dashboard',
            'portal' => 'portal',
        ];

        return $aliases[$segment] ?? null;
    }

    private function wantsJson(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (str_starts_with($uri, '/api/')) {
            return true;
        }

        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (stripos($accept, 'application/json') !== false) {
            return true;
        }

        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return strcasecmp($requestedWith, 'XMLHttpRequest') === 0;
    }

    public function generateJwt(array $usuario, int $ttl = 3600): string
    {
        $payload = [
            'sub' => $usuario['id'],
            'email' => $usuario['email'],
            'role' => $usuario['papel'],
            'cliente_id' => $usuario['cliente_id'] ?? null,
            'exp' => time() + $ttl,
        ];
        return Jwt::encode($payload, $this->config['jwt_secret'], 'HS256');
    }

    public function validateJwt(string $token): ?array
    {
        $decoded = Jwt::decode($token, $this->config['jwt_secret'], 'HS256');
        if (!$decoded) {
            return null;
        }

        $subject = isset($decoded['sub']) ? (int) $decoded['sub'] : null;
        if ($subject && empty($decoded['cliente_id'])) {
            $usuario = $this->usuarios->find($subject);
            if ($usuario && ($usuario['papel'] ?? null) === 'cliente') {
                $cliente = $this->clientes->findByUsuarioId((int) $usuario['id']);
                if ($cliente) {
                    $decoded['cliente_id'] = (int) $cliente['id'];
                }
            }
            if ($usuario && empty($decoded['email']) && !empty($usuario['email'])) {
                $decoded['email'] = $usuario['email'];
            }
        }

        return $decoded;
    }

    private function hydrateCliente(array $usuario): array
    {
        if ($usuario['papel'] === 'cliente') {
            $cliente = $this->clientes->findByUsuarioId((int)$usuario['id']);
            if ($cliente) {
                $usuario['cliente_id'] = $cliente['id'];
            }
        }
        return $usuario;
    }
}
