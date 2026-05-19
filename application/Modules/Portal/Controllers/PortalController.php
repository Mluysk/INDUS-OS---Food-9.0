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
        $allowedIds = $this->portalProductIds();
        $produtos = array_values(array_filter(
            $this->produtos->all(),
            static function (array $produto) use ($allowedIds): bool {
                if (($produto['tipo'] ?? 'PA') !== 'PA') {
                    return false;
                }
                if (!$allowedIds) {
                    return true;
                }
                return in_array((int)($produto['id'] ?? 0), $allowedIds, true);
            }
        ));
        $this->render('portal/index_whatsapp_beside_contact_type_patch', [
            'catalogo' => $produtos,
        ], [
            'layout' => 'layouts/portal',
            'title' => 'Portal do Cliente',
        ]);
    }

    public function registro(): void
    {
        $this->render('portal/register_email_patch', [], [
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
        if ($raw === '') {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            static fn(string $value): int => (int)trim($value),
            explode(',', $raw)
        ), static fn(int $id): bool => $id > 0)));
    }
}
