<?php
namespace App\Modules\Portal\Controllers;

use App\Core\Controller;
use App\Services\ConfigService;
use App\Services\PortalService;
use App\Services\ProdutosService;

class PortalController extends Controller
{
    private PortalService $portal;
    private ProdutosService $produtos;
    private ConfigService $config;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        PortalService $portal,
        ProdutosService $produtos,
        ConfigService $config
    ) {
        parent::__construct($container, $auth, $audit);
        $this->portal = $portal;
        $this->produtos = $produtos;
        $this->config = $config;
    }

    public function index(): void
    {
        $produtos = array_values(array_filter(
            $this->produtos->all(['mostrar_portal' => 1]),
            static fn(array $produto): bool => ($produto['tipo'] ?? 'PA') === 'PA'
        ));
        $this->render('portal/index_mobile_patch', [
            'catalogo' => $produtos,
            'portalGroupOptions' => $this->portalGroupOptions(),
            'portalClientGroups' => json_decode((string)($this->config->get('PORTAL_CLIENT_GROUPS', '[]') ?? '[]'), true) ?: [],
            'portalCatalogPageSize' => max(1, min(9999, (int)$this->config->get('PORTAL_CATALOG_PAGE_SIZE', '15'))),
        ], [
            'layout' => 'layouts/portal',
            'title' => 'Portal do Cliente',
        ]);
    }

    public function registro(): void
    {
        $this->render('portal/register_email_patch', ['portalGroupOptions' => $this->portalGroupOptions()], [
            'layout' => 'layouts/portal',
            'title' => 'Criar conta',
        ]);
    }

    public function cadastro(): void
    {
        $payload = $this->wantsJson() ? (json_decode(file_get_contents('php://input'), true) ?: []) : ($_POST ?: []);
        try {
            $resultado = $this->portal->registrarCliente($payload);
        } catch (\InvalidArgumentException $e) {
            if ($this->wantsJson()) {
                $this->json(['message' => $e->getMessage()], 422);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->redirect('/portal/registro');
            }
            return;
        } catch (\Throwable $e) {
            if ($this->wantsJson()) {
                $this->json(['message' => 'Não foi possível concluir o cadastro.'], 500);
            } else {
                $this->setFlash('error', 'Não foi possível concluir o cadastro.');
                $this->redirect('/portal/registro');
            }
            return;
        }

        if ($this->wantsJson()) {
            $this->json([
                'message' => 'Cadastro realizado com sucesso!',
                'cliente_id' => $resultado['cliente_id'],
                'usuario_id' => $resultado['usuario_id'],
            ], 201);
            return;
        }

        $this->setFlash('success', 'Cadastro realizado com sucesso! Faça login para acompanhar seus pedidos.');
        $this->redirect('/portal');
    }

    /**
     * @return array<int>
     */
    private function portalProductIds(): array
    {
        $raw = trim((string)($this->config->get('PORTAL_PRODUCT_IDS', '') ?? ''));
        $globalIds = $raw === '' ? [] : array_values(array_unique(array_filter(array_map(
            static fn(string $value): int => (int)trim($value),
            explode(',', $raw)
        ), static fn(int $id): bool => $id > 0)));

        $clienteId = (int)($this->auth->user()['cliente_id'] ?? ($_SESSION['cliente_id'] ?? 0));
        if ($clienteId <= 0) return $globalIds;
        $groups = json_decode((string)($this->config->get('PORTAL_CLIENT_GROUPS', '[]') ?? '[]'), true);
        if (!is_array($groups)) return $globalIds;
        $groupIds = [];
        $matched = false;
        foreach ($groups as $group) {
            if (!is_array($group)) continue;
            $clients = array_map('intval', is_array($group['client_ids'] ?? null) ? $group['client_ids'] : []);
            if (!in_array($clienteId, $clients, true)) continue;
            $matched = true;
            $groupIds = array_merge($groupIds, array_map('intval', is_array($group['product_ids'] ?? null) ? $group['product_ids'] : []));
        }
        if (!$matched) return $globalIds;
        $groupIds = array_values(array_unique(array_filter($groupIds, static fn(int $id): bool => $id > 0)));
        return $groupIds ?: [0];
    }

    private function portalGroupOptions(): array
    {
        $groups = json_decode((string)($this->config->get('PORTAL_CLIENT_GROUPS', '[]') ?? '[]'), true);
        $options = [];
        foreach (is_array($groups) ? $groups : [] as $group) {
            if (!is_array($group)) continue;
            $id = trim((string)($group['id'] ?? ''));
            $name = trim((string)($group['name'] ?? ''));
            if ($id !== '' && $name !== '') $options[] = ['id' => $id, 'name' => $name, 'product_ids' => array_values(array_map('intval', is_array($group['product_ids'] ?? null) ? $group['product_ids'] : [])), 'created_by_client' => (int)($group['created_by_client'] ?? 0)];
        }
        return $options;
    }
}
