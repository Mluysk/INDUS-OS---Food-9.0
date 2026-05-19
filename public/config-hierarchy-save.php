<?php
declare(strict_types=1);

$bootstrap = require __DIR__.'/../application/bootstrap.php';

use App\Services\AuthService;
use App\Services\ConfigService;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$basePath = rtrim($_SERVER['APP_BASE_PATH'] ?? '', '/');
if ($basePath === '') {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $scriptDir = rtrim(dirname($scriptName), '/');
    $basePath = $scriptDir === '/' ? '' : $scriptDir;
    if (str_ends_with($basePath, '/public')) {
        $basePath = substr($basePath, 0, -strlen('/public'));
    }
}

$redirect = ($basePath === '' ? '' : $basePath).'/config?tab=users';

try {
    /** @var AuthService $auth */
    $auth = $bootstrap['container']->get(AuthService::class);
    $user = $auth->user();

    if (!$user || ($user['papel'] ?? '') !== 'admin') {
        $_SESSION['flash_error'] = 'Acesso negado.';
        header('Location: '.$redirect);
        exit;
    }

    $modules = [
        'dashboard',
        'clientes',
        'produtos',
        'pedidos',
        'producao',
        'estoque',
        'expedicao',
        'financeiro',
        'relatorios',
        'mapa',
        'desempenho',
        'meta',
        'receitas',
        'config',
        'portal',
    ];

    $defaults = [
        'admin' => $modules,
        'gestor' => ['dashboard', 'clientes', 'produtos', 'pedidos', 'receitas', 'producao', 'estoque', 'expedicao', 'financeiro', 'relatorios', 'mapa', 'desempenho', 'meta', 'config'],
        'producao' => ['dashboard', 'receitas', 'producao', 'mapa'],
        'expedicao' => ['dashboard', 'expedicao', 'pedidos', 'estoque'],
        'financeiro' => ['dashboard', 'financeiro', 'relatorios'],
        'qualidade' => ['dashboard', 'producao', 'estoque', 'mapa', 'desempenho', 'relatorios'],
    ];

    $payload = $_POST['hierarquia'] ?? [];
    if (!is_array($payload)) {
        $payload = [];
    }

    $result = [];
    foreach ($defaults as $role => $fallback) {
        if ($role === 'admin') {
            $result[$role] = $modules;
            continue;
        }

        $selected = $payload[$role] ?? [];
        if (!is_array($selected)) {
            $selected = [];
        }

        $choices = array_values(array_intersect($modules, array_map('strval', $selected)));
        $result[$role] = $choices ?: $fallback;
    }

    /** @var ConfigService $config */
    $config = $bootstrap['container']->get(ConfigService::class);
    $conn = $config->getConnection();
    $stmt = $conn->prepare('INSERT INTO configuracoes (chave, valor) VALUES (:chave, :valor) ON DUPLICATE KEY UPDATE valor = VALUES(valor)');
    $stmt->execute([
        'chave' => 'RBAC_MODULES',
        'valor' => json_encode($result, JSON_UNESCAPED_SLASHES),
    ]);

    $_SESSION['flash_success'] = 'Hierarquia salva com sucesso.';
} catch (Throwable $exception) {
    $_SESSION['flash_error'] = 'Não foi possível salvar a hierarquia agora: '.$exception->getMessage();
}

header('Location: '.$redirect);
exit;
