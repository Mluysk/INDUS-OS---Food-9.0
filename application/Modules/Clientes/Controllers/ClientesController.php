<?php
namespace App\Modules\Clientes\Controllers;

use App\Core\Controller;
use App\Services\ClientesService;
use App\Services\ConfigService;

class ClientesController extends Controller
{
    private ClientesService $clientes;
    private ConfigService $config;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        ClientesService $clientes,
        ConfigService $config
    ) {
        parent::__construct($container, $auth, $audit);
        $this->clientes = $clientes;
        $this->config = $config;
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro']);
        $clientes = $this->clientes->all();
        $pageSizeRaw = $this->config->get('LIST_PAGE_SIZE_OPTIONS', '15,20,30,40,50,100') ?: '15,20,30,40,50,100';
        $pageSizeOptions = array_values(array_map(
            fn (int $size): int => min(500, $size),
            array_filter(array_map('intval', preg_split('/[,\s;]+/', $pageSizeRaw)), fn (int $size): bool => $size > 0)
        ));
        if (!$pageSizeOptions) {
            $pageSizeOptions = [15, 20, 30, 40, 50, 100];
        }
        $pageSizeDefault = max(1, min(500, (int)($this->config->get('LIST_PAGE_SIZE_DEFAULT_CLIENTES', $this->config->get('LIST_PAGE_SIZE_DEFAULT', '15')) ?: '15')));
        $pageSizeOptions = array_values(array_unique(array_merge($pageSizeOptions, [$pageSizeDefault])));
        sort($pageSizeOptions);

        if ($this->wantsJson()) {
            $this->json($clientes);
            return;
        }

        $this->render('clientes/index_with_patch', [
            'clientes' => $clientes,
            'listPageSizeOptions' => $pageSizeOptions,
            'listPageSizeDefault' => $pageSizeDefault,
        ], [
            'title' => 'Clientes',
            'activeMenu' => 'clientes',
            'breadcrumbs' => ['Cadastros', 'Clientes'],
        ]);
    }

    public function store(): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        try {
            $id = $this->clientes->create($data, $this->auth->user()['id']);
            if ($this->wantsJson()) {
                $this->json(['id' => $id], 201);
            } else {
                $this->setFlash('success', 'Cliente cadastrado com sucesso.');
                $this->redirect('/clientes');
            }
        } catch (\InvalidArgumentException $e) {
            if ($this->wantsJson()) {
                $this->json(['message' => $e->getMessage()], 422);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->redirect('/clientes');
            }
        }
    }

    public function update($id): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        try {
            $this->clientes->update((int)$id, $data, $this->auth->user()['id']);
            if ($this->wantsJson()) {
                $this->json(['message' => 'Atualizado']);
            } else {
                $this->setFlash('success', 'Cliente atualizado com sucesso.');
                $this->redirect('/clientes');
            }
        } catch (\InvalidArgumentException $e) {
            if ($this->wantsJson()) {
                $this->json(['message' => $e->getMessage()], 422);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->redirect('/clientes');
            }
        }
    }

    public function show($id): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro']);
        $cliente = $this->clientes->find((int) $id);

        if (!$cliente) {
            if ($this->wantsJson()) {
                $this->json(['message' => 'Cliente não encontrado'], 404);
            } else {
                $this->setFlash('error', 'Cliente não encontrado.');
                $this->redirect('/clientes');
            }
            return;
        }

        if ($this->wantsJson()) {
            $this->json($cliente);
            return;
        }

        $this->render('clientes/detail', [
            'cliente' => $cliente,
        ], [
            'title' => 'Detalhes do cliente',
            'activeMenu' => 'clientes',
            'breadcrumbs' => ['Cadastros', 'Clientes', 'Detalhes'],
        ]);
    }

    public function delete($id): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        $clienteId = (int) $id;

        try {
            $this->clientes->delete($clienteId, $this->auth->user()['id']);
            if ($this->wantsJson()) {
                $this->json(['message' => 'Cliente removido']);
            } else {
                $this->setFlash('success', 'Cliente removido com sucesso.');
                $this->redirect('/clientes');
            }
        } catch (\InvalidArgumentException $e) {
            if ($this->wantsJson()) {
                $this->json(['message' => $e->getMessage()], 422);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->redirect('/clientes');
            }
        }
    }

    public function printList(): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro']);
        $clientes = $this->clientes->all();
        $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'documento', 'clientes', null, [
            'documento' => 'lista_clientes',
            'quantidade' => count($clientes),
        ]);

        $this->render('clientes/print', [
            'clientes' => $clientes,
        ], [
            'title' => 'Clientes cadastrados',
            'layout' => 'layouts/print',
        ]);
    }
}
