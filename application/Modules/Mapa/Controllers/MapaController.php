<?php
namespace App\Modules\Mapa\Controllers;

use App\Core\Controller;
use App\Services\MapaService;

class MapaController extends Controller
{
    private MapaService $mapa;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        MapaService $mapa
    ) {
        parent::__construct($container, $auth, $audit);
        $this->mapa = $mapa;
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin', 'gestor', 'producao', 'expedicao']);

        $ref = isset($_GET['ref']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$_GET['ref'])
            ? (string)$_GET['ref']
            : null;
        $inicio = isset($_GET['start']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$_GET['start'])
            ? (string)$_GET['start']
            : null;

        $full = isset($_GET['full']) && (string)$_GET['full'] === '1';

        $mapa = $this->mapa->gerar($ref, $inicio, $full);

        if ($this->wantsJson()) {
            $this->json($mapa);
            return;
        }

        $view = $full ? 'mapa/mapa-completo' : 'mapa/index';

        $this->render($view, [
            'mapa' => $mapa,
            'mapaFull' => $full,
        ], [
            'title' => 'Mapa de produção',
            'pageSubtitle' => 'Visualize entradas previstas e saídas por produto, dia a dia.',
            'breadcrumbs' => ['Planejamento', 'Mapa'],
            'activeMenu' => 'mapa',
        ]);
    }

    public function registrarEntrada(): void
    {
        $this->auth->ensureRole(['admin', 'gestor', 'producao']);

        $payload = $this->input();
        $tipo = $payload['tipo'] ?? 'planejado';
        $full = isset($payload['full']) && (string)$payload['full'] === '1';

        $user = $this->auth->user();
        $userId = (int) ($user['id'] ?? 0);

        try {
            $this->mapa->registrarEntradaManual($payload, $userId);
            $auditAction = $tipo === 'estoque' ? 'create' : 'planejamento';
            $auditEntity = $tipo === 'estoque' ? 'estoque' : 'producao';
            $auditDetails = $payload;
            $auditDetails['origem'] = 'mapa';
            $auditDetails['registro'] = $tipo === 'estoque' ? 'entrada_estoque' : 'planejamento_producao';
            $this->audit->log($userId, $auditAction, $auditEntity, null, $auditDetails);
            if ($this->wantsJson()) {
                $start = isset($payload['janela_inicio']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$payload['janela_inicio'])
                    ? (string)$payload['janela_inicio']
                    : null;
                $refDay = isset($payload['data_referencia']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$payload['data_referencia'])
                    ? (string)$payload['data_referencia']
                    : null;
                $mapa = $this->mapa->gerar($refDay, $start, $full);
                $this->json([
                    'message' => $tipo === 'estoque' ? 'Entrada de estoque registrada' : 'Produção registrada para o mapa',
                    'mapa' => $mapa,
                ]);
                return;
            }
            $this->setFlash('success', $tipo === 'estoque' ? 'Entrada de estoque registrada.' : 'Produção planejada registrada no mapa.');
        } catch (\Throwable $e) {
            if ($this->wantsJson()) {
                $this->json(['message' => $e->getMessage()], 422);
                return;
            }
            $this->setFlash('error', $e->getMessage());
        }

        $this->redirect($full ? '/mapa?full=1' : '/mapa');
    }
}
