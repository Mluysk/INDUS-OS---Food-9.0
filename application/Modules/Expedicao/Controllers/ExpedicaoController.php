<?php
namespace App\Modules\Expedicao\Controllers;

use App\Core\Controller;
use App\Services\ExpedicaoService;
use App\Services\ConfigService;

class ExpedicaoController extends Controller
{
    private ExpedicaoService $expedicao;
    private ConfigService $config;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        ExpedicaoService $expedicao,
        ConfigService $config
    ) {
        parent::__construct($container, $auth, $audit);
        $this->expedicao = $expedicao;
        $this->config = $config;
    }

    public function picking($pedidoId): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        $picking = $this->expedicao->gerarPicking((int)$pedidoId);
        $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'documento', 'expedicao', (int)$pedidoId, [
            'documento' => 'picking',
            'pedido_id' => (int)$pedidoId,
        ]);

        if ($this->wantsJson()) {
            $this->json($picking);
            return;
        }

        $this->render('expedicao/picking', [
            'picking' => $picking,
        ], [
            'title' => 'Picking e conferência',
            'activeMenu' => 'expedicao',
            'breadcrumbs' => ['Expedição', 'Picking'],
        ]);
    }

    public function romaneio(): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        $dados = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $pedidoId = (int)$dados['pedido_id'];
        $converte = filter_var($this->config->get('PEDIDO_BAIXA_NO_CRIAR', 'false'), FILTER_VALIDATE_BOOLEAN) === false;
        if ($pedidoId <= 0) {
            if ($this->wantsJson()) {
                $this->json(['message' => 'Pedido inválido'], 422);
            } else {
                $this->setFlash('error', 'Informe um pedido válido.');
                $this->redirect('/expedicao');
            }
            return;
        }
        $id = $this->expedicao->registrarExpedicao($pedidoId, $dados, $this->auth->user()['id'], $converte);
        if ($this->wantsJson()) {
            $this->json(['id' => $id], 201);
        } else {
            $this->setFlash('success', 'Romaneio gerado com sucesso.');
            $this->redirect('/expedicao');
        }
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        $fila = $this->expedicao->listarFila();

        if ($this->wantsJson()) {
            $this->json($fila);
            return;
        }

        $this->render('expedicao/index', [
            'fila' => $fila,
        ], [
            'title' => 'Expedição',
            'activeMenu' => 'expedicao',
            'breadcrumbs' => ['Expedição', 'Fila'],
        ]);
    }
}
