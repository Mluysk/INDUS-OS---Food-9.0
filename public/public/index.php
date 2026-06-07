<?php
declare(strict_types=1);

$bootstrap = require __DIR__.'/../application/bootstrap.php';
$router = $bootstrap['router'];

use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Clientes\Controllers\ClientesController;
use App\Modules\Produtos\Controllers\ProdutosController;
use App\Modules\Producao\Controllers\ProducaoController;
use App\Modules\Estoque\Controllers\EstoqueController;
use App\Modules\Expedicao\Controllers\ExpedicaoController;
use App\Modules\Financeiro\Controllers\FinanceiroController;
use App\Modules\Relatorios\Controllers\RelatoriosController;
use App\Modules\Config\Controllers\ConfigController;
use App\Modules\Pedidos\Controllers\PedidosController;
use App\Modules\Receitas\Controllers\ReceitasController;
use App\Modules\Mapa\Controllers\MapaController;
use App\Modules\Desempenho\Controllers\DesempenhoController;
use App\Modules\Meta\Controllers\MetaController;
use App\Modules\Portal\Controllers\PortalController;
use App\Controllers\Api\AuthApiController;
use App\Controllers\Api\ProdutosApiController;
use App\Controllers\Api\PedidosApiController;
use App\Controllers\DashboardController;
use App\Controllers\Cli\BackupController;

// Painel rotas
$router->add('GET', '/', [DashboardController::class, 'index']);
$router->add('GET', '/dashboard', [DashboardController::class, 'index']);
$router->add('GET', '/login', [AuthController::class, 'form']);
$router->add('POST', '/login', [AuthController::class, 'login']);
$router->add('POST', '/logout', [AuthController::class, 'logout']);

// Portal do cliente
$router->add('GET', '/portal', [PortalController::class, 'index']);
$router->add('GET', '/portal/registro', [PortalController::class, 'registro']);
$router->add('POST', '/portal/cadastro', [PortalController::class, 'cadastro']);

$router->add('GET', '/clientes', [ClientesController::class, 'index']);
$router->add('POST', '/clientes', [ClientesController::class, 'store']);
$router->add('GET', '/clientes/imprimir', [ClientesController::class, 'printList']);
$router->add('GET', '/clientes/{id}', [ClientesController::class, 'show']);
$router->add('PUT', '/clientes/{id}', [ClientesController::class, 'update']);
$router->add('DELETE', '/clientes/{id}', [ClientesController::class, 'delete']);

$router->add('GET', '/pedidos', [PedidosController::class, 'index']);
$router->add('POST', '/pedidos', [PedidosController::class, 'store']);
$router->add('GET', '/pedidos/{id}/editar', [PedidosController::class, 'edit']);
$router->add('POST', '/pedidos/{id}/update', [PedidosController::class, 'update']);
$router->add('POST', '/pedidos/{id}/aprovar', [PedidosController::class, 'approve']);
$router->add('GET', '/pedidos/{id}/nota', [PedidosController::class, 'nota']);

$router->add('GET', '/mapa', [MapaController::class, 'index']);
$router->add('POST', '/mapa/entrada', [MapaController::class, 'registrarEntrada']);

$router->add('GET', '/desempenho', [DesempenhoController::class, 'index']);
$router->add('GET', '/meta', [DesempenhoController::class, 'index']);
$router->add('GET', '/metas', [MetaController::class, 'index']);

$router->add('GET', '/produtos', [ProdutosController::class, 'index']);
$router->add('POST', '/produtos', [ProdutosController::class, 'store']);
$router->add('POST', '/produtos/{id}/update', [ProdutosController::class, 'update']);
$router->add('GET', '/produtos/{id}/ficha', [ProdutosController::class, 'ficha']);
$router->add('GET', '/produtos/print', [ProdutosController::class, 'printList']);

$router->add('GET', '/receitas', [ReceitasController::class, 'index']);
$router->add('GET', '/receitas/nova', [ReceitasController::class, 'create']);
$router->add('POST', '/receitas', [ReceitasController::class, 'store']);
$router->add('GET', '/receitas/{id}', [ReceitasController::class, 'show']);
$router->add('GET', '/receitas/{id}/editar', [ReceitasController::class, 'edit']);
$router->add('POST', '/receitas/{id}/update', [ReceitasController::class, 'update']);
$router->add('GET', '/receitas/{id}/runs', [ReceitasController::class, 'runs']);
$router->add('GET', '/receitas/{id}/executar', [ReceitasController::class, 'runManualForm']);
$router->add('POST', '/receitas/{id}/executar', [ReceitasController::class, 'runManual']);

$router->add('GET', '/producao/op', [ProducaoController::class, 'index']);
$router->add('POST', '/producao/op', [ProducaoController::class, 'store']);
$router->add('PATCH', '/producao/op/{id}/iniciar', [ProducaoController::class, 'iniciar']);
$router->add('PATCH', '/producao/op/{id}/finalizar', [ProducaoController::class, 'finalizar']);
$router->add('POST', '/producao/op/{id}/iniciar', [ProducaoController::class, 'iniciar']);
$router->add('POST', '/producao/op/{id}/finalizar', [ProducaoController::class, 'finalizar']);
$router->add('POST', '/producao/print', [ProducaoController::class, 'imprimir']);
$router->add('GET', '/producao/relatorio-op', [ProducaoController::class, 'relatorioOp']);
$router->add('GET', '/producao/entradas', [ProducaoController::class, 'entradas']);

$router->add('GET', '/estoque/posicao', [EstoqueController::class, 'posicao']);
$router->add('GET', '/estoque/posicao/print', [EstoqueController::class, 'imprimir']);
$router->add('POST', '/estoque/movimento', [EstoqueController::class, 'movimentar']);

$router->add('GET', '/expedicao', [ExpedicaoController::class, 'index']);
$router->add('GET', '/expedicao/picking/{pedidoId}', [ExpedicaoController::class, 'picking']);
$router->add('POST', '/expedicao/romaneio', [ExpedicaoController::class, 'romaneio']);

$router->add('GET', '/financeiro/titulos', [FinanceiroController::class, 'index']);
$router->add('PATCH', '/financeiro/titulos/{id}/baixa', [FinanceiroController::class, 'baixa']);
$router->add('POST', '/financeiro/titulos/{id}/baixa', [FinanceiroController::class, 'baixa']);

$router->add('GET', '/relatorios', [RelatoriosController::class, 'index']);
$router->add('GET', '/relatorios/pedidos/pdf', [RelatoriosController::class, 'pedidosPdf']);
$router->add('GET', '/relatorios/estoque/xlsx', [RelatoriosController::class, 'estoqueXlsx']);
$router->add('GET', '/relatorios/dre', [RelatoriosController::class, 'dre']);

$router->add('GET', '/config', [ConfigController::class, 'index']);
$router->add('POST', '/config', [ConfigController::class, 'save']);
$router->add('POST', '/config/save', [ConfigController::class, 'save']);
$router->add('POST', '/config/users/create', [ConfigController::class, 'createUser']);
$router->add('POST', '/config/users/update', [ConfigController::class, 'updateUser']);
$router->add('POST', '/config/users/delete', [ConfigController::class, 'deleteUser']);
$router->add('POST', '/config/hierarchy/save', [ConfigController::class, 'saveHierarchy']);

// API rotas JWT
$router->add('POST', '/api/auth/token', [AuthApiController::class, 'token']);
$router->add('GET', '/api/auth/cliente', [AuthApiController::class, 'cliente']);
$router->add('PATCH', '/api/auth/cliente', [AuthApiController::class, 'updateCliente']);
$router->add('GET', '/api/produtos', [ProdutosApiController::class, 'index']);
$router->add('GET', '/api/pedidos', [PedidosApiController::class, 'index']);
$router->add('POST', '/api/pedidos', [PedidosApiController::class, 'store']);
$router->add('GET', '/api/pedidos/{id}', [PedidosApiController::class, 'show']);
$router->add('PATCH', '/api/pedidos/{id}/status', [PedidosApiController::class, 'updateStatus']);

// CLI
if (php_sapi_name() === 'cli') {
    $router->add('GET', '/cli/backup:run', [BackupController::class, 'run']);
    $path = $argv[1] ?? '/';
    $router->dispatch('GET', $path);
    return;
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

// compute the project base path so the app works from subdirectories (e.g. XAMPP/htdocs)
$basePath = $scriptDir;
if ($basePath !== '' && str_ends_with($basePath, '/public')) {
    $basePath = substr($basePath, 0, -strlen('/public'));
}
$basePath = rtrim($basePath, '/');

// allow explicit override via environment (useful on hospedagem compartilhada como Hostinger)
$envBase = trim((string) (getenv('APP_BASE_PATH') ?: ''));
if ($envBase !== '') {
    $envBase = '/'.ltrim($envBase, '/');
    $envBase = $envBase === '/' ? '' : rtrim($envBase, '/');
    $basePath = $envBase;
}

if ($scriptDir !== '' && str_starts_with($requestUri, $scriptDir)) {
    $requestUri = substr($requestUri, strlen($scriptDir)) ?: '/';
}

if ($basePath !== '' && str_starts_with($requestUri, $basePath)) {
    $requestUri = substr($requestUri, strlen($basePath)) ?: '/';
}

$_SERVER['APP_BASE_PATH'] = $basePath;
$normalizedRequest = strtok($requestUri ?: '/', '?') ?: '/';

ob_start();
$router->dispatch($_SERVER['REQUEST_METHOD'], $requestUri ?: '/');
$response = ob_get_clean();

$moduleOptions = [
    'dashboard' => ['label' => 'Dashboard', 'href' => 'dashboard', 'icon' => 'layout-dashboard'],
    'clientes' => ['label' => 'Clientes', 'href' => 'clientes', 'icon' => 'users'],
    'produtos' => ['label' => 'Produtos', 'href' => 'produtos', 'icon' => 'package'],
    'receitas' => ['label' => 'Receitas', 'href' => 'receitas', 'icon' => 'book-open'],
    'pedidos' => ['label' => 'Pedidos', 'href' => 'pedidos', 'icon' => 'shopping-cart'],
    'mapa' => ['label' => 'Mapa', 'href' => 'mapa', 'icon' => 'map'],
    'desempenho' => ['label' => 'Desempenho', 'href' => 'desempenho', 'icon' => 'activity'],
    'meta' => ['label' => 'Metas', 'href' => 'metas', 'icon' => 'target'],
    'producao' => ['label' => 'Producao', 'href' => 'producao/op', 'icon' => 'factory'],
    'estoque' => ['label' => 'Estoque', 'href' => 'estoque/posicao', 'icon' => 'boxes'],
    'expedicao' => ['label' => 'Expedicao', 'href' => 'expedicao', 'icon' => 'truck'],
    'financeiro' => ['label' => 'Financeiro', 'href' => 'financeiro/titulos', 'icon' => 'wallet'],
    'relatorios' => ['label' => 'Relatorios', 'href' => 'relatorios', 'icon' => 'bar-chart-3'],
    'config' => ['label' => 'Configuracoes', 'href' => 'config', 'icon' => 'settings'],
    'portal' => ['label' => 'Portal do cliente', 'href' => 'portal', 'icon' => 'globe'],
];
$defaultHierarchy = [
    'admin' => array_keys($moduleOptions),
    'gestor' => ['dashboard', 'clientes', 'produtos', 'pedidos', 'receitas', 'producao', 'estoque', 'expedicao', 'financeiro', 'relatorios', 'mapa', 'desempenho', 'meta', 'config'],
    'producao' => ['dashboard', 'receitas', 'producao', 'mapa'],
    'expedicao' => ['dashboard', 'expedicao', 'pedidos', 'estoque'],
    'financeiro' => ['dashboard', 'financeiro', 'relatorios'],
    'qualidade' => ['dashboard', 'producao', 'estoque', 'mapa', 'desempenho', 'relatorios'],
];
$activeRole = '';
if (session_status() === PHP_SESSION_ACTIVE) {
    $activeRole = (string)($_SESSION['user_role'] ?? '');
}
$allowedModules = $defaultHierarchy[$activeRole] ?? [];
try {
    if ($activeRole !== '') {
        $configService = $bootstrap['container']->get(\App\Services\ConfigService::class);
        $decodedHierarchy = json_decode((string)$configService->get('RBAC_MODULES', ''), true);
        if (is_array($decodedHierarchy) && isset($decodedHierarchy[$activeRole]) && is_array($decodedHierarchy[$activeRole])) {
            $allowedModules = array_values(array_intersect(array_keys($moduleOptions), array_map('strval', $decodedHierarchy[$activeRole])));
        }
        if ($activeRole === 'admin') {
            $allowedModules = array_keys($moduleOptions);
        }
    }
} catch (\Throwable $exception) {
    // Mantem a hierarquia padrao se a configuracao nao puder ser lida.
}

$menuHierarchyPayload = [
    'basePath' => $basePath,
    'allowed' => $allowedModules,
    'modules' => $moduleOptions,
];
$menuHierarchyScript = '<script>window.__INDUS_MENU_HIERARCHY__ = '.json_encode($menuHierarchyPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).';</script>';
$menuHierarchyScript .= <<<'HTML'
<script>
    (() => {
        const config = window.__INDUS_MENU_HIERARCHY__;
        if (!config || !Array.isArray(config.allowed)) return;
        const menu = document.getElementById('menuList');
        if (!menu) return;

        const allowed = new Set(config.allowed);
        const modules = config.modules || {};
        const trimRightSlash = (value) => value.endsWith('/') ? value.slice(0, -1) : value;
        const trimLeftSlash = (value) => value.startsWith('/') ? value.slice(1) : value;
        const basePath = trimRightSlash(String(config.basePath || ''));
        const segmentMap = { metas: 'meta', meta: 'meta', producao: 'producao' };
        const hrefFor = (path) => `${basePath}/${trimLeftSlash(String(path || ''))}`.replaceAll('//', '/');
        const keyFromHref = (href) => {
            try {
                const url = new URL(href, window.location.origin);
                let path = url.pathname;
                if (basePath && path.startsWith(basePath)) {
                    path = path.slice(basePath.length) || '/';
                }
                const cleanPath = trimLeftSlash(path);
                const segment = cleanPath.split('/')[0] || 'dashboard';
                return segmentMap[segment] || segment;
            } catch (_) {
                return '';
            }
        };
        const currentKey = keyFromHref(window.location.href);
        const existing = new Set();

        menu.querySelectorAll('li').forEach((item) => {
            const link = item.querySelector('a[href]');
            const key = link ? keyFromHref(link.getAttribute('href')) : '';
            if (key) existing.add(key);
            item.style.display = key && allowed.has(key) ? '' : 'none';
        });

        config.allowed.forEach((key) => {
            if (existing.has(key) || !modules[key]) return;
            const module = modules[key];
            const li = document.createElement('li');
            const active = key === currentKey ? ' active' : '';
            li.innerHTML = `<a class="nav-item${active}" href="${hrefFor(module.href)}"><span class="nav-icon" data-lucide="${module.icon}"></span><span class="item-label">${module.label}</span></a>`;
            menu.appendChild(li);
        });

        menu.querySelectorAll('.nav-item').forEach((link) => {
            const key = keyFromHref(link.getAttribute('href'));
            const active = key !== '' && key === currentKey;
            link.classList.toggle('active', active);
            if (active) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });

        if (window.lucide) {
            window.lucide.createIcons({ elements: menu.querySelectorAll('[data-lucide]') });
        }
    })();
</script>
HTML;

$autoDismissAlerts = <<<'HTML'
<style>
    .alerts[data-auto-dismiss="1"] {
        transition: opacity 0.28s ease, transform 0.28s ease, max-height 0.28s ease, margin 0.28s ease;
        overflow: hidden;
    }

    .alerts[data-auto-dismiss="1"].is-dismissing {
        opacity: 0;
        transform: translateY(-6px);
        max-height: 0;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        pointer-events: none;
    }
</style>
<script>
    (() => {
        const dismissAlerts = () => {
            document.querySelectorAll('.alerts:not([data-auto-dismiss])').forEach((alerts) => {
                alerts.dataset.autoDismiss = '1';
                window.setTimeout(() => {
                    alerts.classList.add('is-dismissing');
                    window.setTimeout(() => alerts.remove(), 320);
                }, 3900);
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', dismissAlerts);
        } else {
            dismissAlerts();
        }
    })();
</script>
HTML;

if (is_string($response) && stripos($response, '</body>') !== false) {
    $response = preg_replace('/<\/body>/i', $menuHierarchyScript.$autoDismissAlerts.'</body>', $response, 1);
}

echo $response;
