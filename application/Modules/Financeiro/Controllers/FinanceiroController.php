<?php
namespace App\Modules\Financeiro\Controllers;

use App\Core\Controller;
use App\Services\ConfigService;
use App\Services\FinanceiroService;

class FinanceiroController extends Controller
{
    private FinanceiroService $financeiro;
    private ConfigService $config;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        FinanceiroService $financeiro,
        ConfigService $config
    ) {
        parent::__construct($container, $auth, $audit);
        $this->financeiro = $financeiro;
        $this->config = $config;
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro']);
        $entradas = $this->financeiro->listarLancamentos('entrada');
        $saidas = $this->financeiro->listarLancamentos('saida');
        $pageSizeAvailable = [15, 20, 30, 40, 50, 100];
        $pageSizeRaw = $this->config->get('LIST_PAGE_SIZE_OPTIONS', '15,20,30,40,50,100') ?: '15,20,30,40,50,100';
        $pageSizeOptions = array_values(array_intersect($pageSizeAvailable, array_map('intval', explode(',', $pageSizeRaw))));
        if (!$pageSizeOptions) {
            $pageSizeOptions = $pageSizeAvailable;
        }
        $pageSizeDefault = (int)($this->config->get('LIST_PAGE_SIZE_DEFAULT', '15') ?: '15');
        if (!in_array($pageSizeDefault, $pageSizeOptions, true)) {
            $pageSizeDefault = $pageSizeOptions[0];
        }

        if ($this->wantsJson()) {
            $this->json([
                'entradas' => $entradas,
                'saidas' => $saidas,
            ]);
            return;
        }

        $this->render('financeiro/index', [
            'entradas' => $entradas,
            'saidas' => $saidas,
            'listPageSizeOptions' => $pageSizeOptions,
            'listPageSizeDefault' => $pageSizeDefault,
        ], [
            'title' => 'Financeiro',
            'activeMenu' => 'financeiro',
            'breadcrumbs' => ['Financeiro', 'Entradas e Saídas'],
        ]);
    }


    public function storeLancamento(): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro']);
        $dados = $this->input();
        $tipo = (string)($dados['tipo'] ?? '');

        try {
            if ($tipo === 'entrada') {
                $this->financeiro->criarLancamentoEntrada($dados, (int)$this->auth->user()['id']);
                $mensagem = 'Entrada registrada com sucesso.';
            } elseif ($tipo === 'saida') {
                $this->financeiro->criarLancamentoSaida($dados, (int)$this->auth->user()['id']);
                $mensagem = 'Saída registrada com sucesso.';
            } else {
                throw new \InvalidArgumentException('Tipo de lançamento inválido.');
            }
        } catch (\Throwable $exception) {
            if ($this->wantsJson()) {
                $this->json(['message' => $exception->getMessage()], 422);
                return;
            }

            $this->setFlash('error', $exception->getMessage());
            $this->redirect('/financeiro/titulos#' . ($tipo === 'saida' ? 'saida' : 'entrada'));
            return;
        }

        if ($this->wantsJson()) {
            $this->json(['message' => $mensagem], 201);
            return;
        }

        $this->setFlash('success', $mensagem);
        $this->redirect('/financeiro/titulos#' . $tipo);
    }


    public function marcarPago($id): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro']);

        try {
            $this->financeiro->marcarLancamentoPago((int)$id, (int)$this->auth->user()['id']);
        } catch (\Throwable $exception) {
            if ($this->wantsJson()) {
                $this->json(['message' => $exception->getMessage()], 422);
                return;
            }

            $this->setFlash('error', $exception->getMessage());
            $this->redirect('/financeiro/titulos');
            return;
        }

        if ($this->wantsJson()) {
            $this->json(['message' => 'Lançamento marcado como pago.']);
            return;
        }

        $this->setFlash('success', 'Lançamento marcado como pago.');
        $this->redirect('/financeiro/titulos#' . ((string)($_POST['tipo'] ?? '') === 'saida' ? 'saida-lista' : 'entrada-lista'));
    }

    public function baixa($id): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro']);
        $dados = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $valor = (float)($dados['valor'] ?? 0);
        $this->financeiro->baixarTitulo((int)$id, $valor, $this->auth->user()['id']);
        if ($this->wantsJson()) {
            $this->json(['message' => 'Baixa registrada']);
        } else {
            $this->setFlash('success', 'Baixa registrada com sucesso.');
            $this->redirect('/financeiro/titulos');
        }
    }
}
