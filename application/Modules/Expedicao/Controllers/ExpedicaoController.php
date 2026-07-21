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
        try {
            $picking = $this->expedicao->gerarPicking((int)$pedidoId, (int)($this->auth->user()['id'] ?? 0));
        } catch (\InvalidArgumentException $exception) {
            if ($this->wantsJson()) {
                $this->json(['message' => $exception->getMessage()], 422);
            } else {
                $this->setFlash('error', $exception->getMessage());
                $this->redirect('/expedicao?tab=fila');
            }
            return;
        }
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

    public function conferirItem($pedidoId, $itemId): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        $dados = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $conferido = isset($dados['status']) ? (string)$dados['status'] : filter_var($dados['conferido'] ?? false, FILTER_VALIDATE_BOOLEAN);
        try {
            $resultado = $this->expedicao->conferirItemPicking(
                (int)$pedidoId,
                (int)$itemId,
                $conferido,
                (int)($this->auth->user()['id'] ?? 0),
                (string)($dados['observacao'] ?? ''),
                (string)($dados['motivo'] ?? ''),
                isset($dados['novo_valor']) ? (float)$dados['novo_valor'] : null
            );
            $this->json($resultado);
        } catch (\InvalidArgumentException $exception) {
            $this->json(['message' => $exception->getMessage()], 422);
        }
    }

    public function revisar($pedidoId): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        try {$this->expedicao->enviarParaRevisao((int)$pedidoId,(int)($this->auth->user()['id']??0));$this->json(['message'=>'Pedido enviado para revisão.']);}
        catch(\InvalidArgumentException $exception){$this->json(['message'=>$exception->getMessage()],422);}
    }

    public function registrarPendencia($pedidoId): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        $dados = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        try {
            $this->expedicao->registrarPendenciaInformacao(
                (int)$pedidoId,
                (string)($dados['tipo'] ?? ''),
                (string)($dados['observacao'] ?? ''),
                (int)($this->auth->user()['id'] ?? 0)
            );
            $this->json(['message' => 'Pendência de informação registrada e enviada para revisão.']);
        } catch (\InvalidArgumentException $exception) {
            $this->json(['message' => $exception->getMessage()], 422);
        }
    }

    public function romaneio(): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        $dados = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $usuarioResponsavel = $this->auth->user() ?? [];
        $dados['responsavel_expedicao_nome'] = trim((string)($usuarioResponsavel['nome'] ?? $usuarioResponsavel['email'] ?? 'Usuario do sistema'));
        $pedidoId = (int)$dados['pedido_id'];
        $converte = filter_var($this->config->get('PEDIDO_BAIXA_NO_CRIAR', 'false'), FILTER_VALIDATE_BOOLEAN) === false;
        if ($pedidoId <= 0) {
            if ($this->wantsJson()) {
                $this->json(['message' => 'Pedido inválido'], 422);
            } else {
                $this->setFlash('error', 'Informe um pedido válido.');
                $this->redirect('/expedicao?tab=romaneio');
            }
            return;
        }
        try {
            $id = $this->expedicao->registrarExpedicao($pedidoId, $dados, $this->auth->user()['id'], $converte);
        } catch (\InvalidArgumentException $exception) {
            if ($this->wantsJson()) {
                $this->json(['message' => $exception->getMessage()], 422);
            } else {
                $this->setFlash('error', $exception->getMessage());
                $this->redirect('/expedicao?tab=romaneio');
            }
            return;
        }
        if ($this->wantsJson()) {
            $this->json(['id' => $id], 201);
        } else {
            $this->setFlash('success', 'Romaneio gerado com sucesso.');
            $this->redirect('/expedicao?tab=concluidos&expedicao_registrada=1');
        }
    }

    public function recebimento($expedicaoId): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        $dados = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        try {
            $this->expedicao->confirmarRecebimento((int)$expedicaoId, $dados, (int)($this->auth->user()['id'] ?? 0));
        } catch (\InvalidArgumentException $exception) {
            if ($this->wantsJson()) {$this->json(['message'=>$exception->getMessage()],422);return;}
            $this->setFlash('error', $exception->getMessage());
            $this->redirect('/expedicao?tab=recebimento');
            return;
        }
        if ($this->wantsJson()) {$this->json(['message'=>'Recebimento confirmado.','status'=>'entregue']);return;}
        $this->setFlash('success', 'Recebimento confirmado. O pedido agora está como Entregue.');
        $this->redirect('/expedicao?tab=recebimento');
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        $fila = $this->expedicao->listarFila();
        $disponiveisRomaneio = $this->expedicao->listarDisponiveisRomaneio();
        $concluidos = $this->expedicao->listarConcluidos();
        $aguardandoRecebimento = $this->expedicao->listarAguardandoRecebimento();
        $proximoRomaneio = $this->expedicao->proximoRomaneio();
        $usuarioAtual = $this->auth->user() ?? [];
        $responsavelExpedicaoNome = trim((string)($usuarioAtual['nome'] ?? $usuarioAtual['email'] ?? 'Usuário do sistema'));

        if ($this->wantsJson()) {
            $this->json(['fila' => $fila, 'disponiveis_romaneio' => $disponiveisRomaneio, 'aguardando_recebimento' => $aguardandoRecebimento, 'concluidos' => $concluidos]);
            return;
        }

        $this->render('expedicao/index', [
            'fila' => $fila,
            'disponiveisRomaneio' => $disponiveisRomaneio,
            'concluidos' => $concluidos,
            'aguardandoRecebimento' => $aguardandoRecebimento,
            'proximoRomaneio' => $proximoRomaneio,
            'responsavelExpedicaoNome' => $responsavelExpedicaoNome,
        ], [
            'title' => 'Expedição',
            'activeMenu' => 'expedicao',
            'breadcrumbs' => ['Expedição', 'Fila'],
        ]);
    }

    public function detalhe($expedicaoId): void
    {
        $this->auth->ensureRole(['admin','gestor','expedicao']);
        try {
            $detalhe = $this->expedicao->detalheConcluido((int)$expedicaoId);
        } catch (\InvalidArgumentException $exception) {
            $this->setFlash('error', $exception->getMessage());
            $this->redirect('/expedicao?tab=concluidos');
            return;
        }
        $this->render('expedicao/detalhe', $detalhe, ['title'=>'Detalhes da expedição','activeMenu'=>'expedicao','breadcrumbs'=>['Expedição','Detalhes']]);
    }
}
