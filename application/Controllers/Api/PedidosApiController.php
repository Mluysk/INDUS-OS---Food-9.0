<?php
namespace App\Controllers\Api;

use App\Core\Controller;
use App\Services\PedidosService;
use App\Services\ConfigService;
use App\Services\ExpedicaoService;

class PedidosApiController extends Controller
{
    private PedidosService $pedidos;
    private ConfigService $config;
    private ExpedicaoService $expedicao;

    private const STATUS_ATUALIZAVEIS = ['novo', 'aprovacao', 'producao', 'separacao', 'expedicao', 'faturado', 'cancelado'];

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        PedidosService $pedidos,
        ConfigService $config,
        ExpedicaoService $expedicao
    ) {
        parent::__construct($container, $auth, $audit);
        $this->pedidos = $pedidos;
        $this->config = $config;
        $this->expedicao = $expedicao;
    }

    public function index(): void
    {
        $decoded = $this->ensureJwt();
        $clienteId = (int)($decoded['cliente_id'] ?? $decoded['sub'] ?? 0);
        if ($clienteId === 0) {
            $this->json(['message' => 'Cliente não associado ao token'], 422);
            return;
        }
        $pedidos = $this->pedidos->listarPorCliente($clienteId);
        $confirmacoes = $this->expedicao->confirmacoesCliente($pedidos);
        foreach ($pedidos as &$pedido) $pedido['data_confirmacao_cliente'] = $confirmacoes[(int)($pedido['id'] ?? 0)] ?? null;
        unset($pedido);
        $this->json(['pedidos' => $pedidos]);
    }

    public function store(): void
    {
        $decoded = $this->ensureJwt();
        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        $clienteId = (int)($decoded['cliente_id'] ?? $decoded['sub'] ?? 0);
        if ($clienteId === 0) {
            $this->json(['message' => 'Cliente não associado ao token'], 422);
            return;
        }
        $pedido = [
            'codigo' => 'P-00000',
            'total' => $payload['total'] ?? 0,
            'observacoes' => $payload['observacoes'] ?? null,
        ];
        $itens = $payload['itens'] ?? [];
        $baixa = filter_var($this->config->get('PEDIDO_BAIXA_NO_CRIAR', 'false'), FILTER_VALIDATE_BOOLEAN);
        $resultado = $this->pedidos->criarPedidoCliente($pedido, $itens, $clienteId, (int)($decoded['sub'] ?? $clienteId), $baixa);
        $pedidoId = (int)($resultado['pedido_id'] ?? 0);
        $portalCodigo = $pedidoId > 0 ? sprintf('P-%05d', $pedidoId) : 'P-00000';
        $pedidoCriado = $resultado['pedido'] ?? null;

        if ($pedidoId > 0) {
            $pedidosRepo = $this->container->get(\App\Repositories\PedidosRepository::class);
            $pedidosRepo->update($pedidoId, ['codigo' => $portalCodigo]);
            if (is_array($pedidoCriado)) {
                $pedidoCriado['codigo'] = $portalCodigo;
            }
        }
        $status = $pedidoCriado['status'] ?? 'aprovacao';

        $this->json([
            'message' => 'Pedido registrado com sucesso.',
            'pedido_id' => $resultado['pedido_id'] ?? null,
            'codigo' => $portalCodigo,
            'status' => $status,
            'status_label' => $pedidoCriado['status_label'] ?? $this->pedidos->statusLabel($status),
            'created_at' => $pedidoCriado['created_at'] ?? null,
            'total' => $pedidoCriado['total'] ?? null,
            'pedido' => $pedidoCriado,
            'backorder' => $resultado['backorder'] ?? [],
        ], 201);
    }

    public function show($id): void
    {
        $decoded = $this->ensureJwt();
        $clienteId = (int)($decoded['cliente_id'] ?? $decoded['sub'] ?? 0);
        $detalhe = $this->pedidos->detalhar((int)$id);
        if (!$detalhe['pedido'] || (int)$detalhe['pedido']['cliente_id'] !== $clienteId) {
            $this->json(['message' => 'Pedido não encontrado'], 404);
            return;
        }
        $pedido = $detalhe['pedido'];
        $pedido['status_label'] = $pedido['status_label'] ?? $this->pedidos->statusLabel($pedido['status'] ?? null);
        $detalhe['pedido'] = $pedido;
        $db = $this->container->get(\App\Repositories\PedidosRepository::class)->getConnection();
        $romaneioStmt = $db->prepare('SELECT id, romaneio, data_separacao, data_expedicao, volumes, peso_total, transportadora, motorista_nome, responsavel_expedicao_nome FROM expedicoes WHERE pedido_id = :pedido_id ORDER BY id DESC LIMIT 1');
        $romaneioStmt->execute(['pedido_id' => (int)$id]);
        $detalhe['romaneio'] = $romaneioStmt->fetch() ?: null;

        $historicoStmt = $db->prepare("SELECT acao, detalhes, created_at FROM audit_logs WHERE entidade = 'pedidos' AND entidade_id = :pedido_id AND acao IN ('create', 'update', 'status', 'expedir', 'receber') ORDER BY id DESC");
        $historicoStmt->execute(['pedido_id' => (int)$id]);
        $detalhe['historico'] = array_map(static function (array $registro): array {
            $dados = json_decode((string)($registro['detalhes'] ?? ''), true);
            $dados = is_array($dados) ? $dados : [];
            return [
                'acao' => (string)($registro['acao'] ?? ''),
                'created_at' => $registro['created_at'] ?? null,
                'motivo' => $dados['observacao'] ?? $dados['justificativa'] ?? $dados['motivo_label'] ?? $dados['motivo'] ?? null,
                'origem' => $dados['origem'] ?? null,
                'mudancas' => is_array($dados['mudancas'] ?? null) ? $dados['mudancas'] : [],
            ];
        }, $historicoStmt->fetchAll());
        $this->json($detalhe);
    }

    public function nota($id): void
    {
        $decoded = $this->ensureJwt();
        $pedidoId = (int)$id;
        $clienteId = (int)($decoded['cliente_id'] ?? $decoded['sub'] ?? 0);
        $detalhe = $this->pedidos->detalharCompleto($pedidoId);
        $pedido = $detalhe['pedido'] ?? null;

        if ($pedidoId <= 0 || !$pedido || $clienteId <= 0 || (int)($pedido['cliente_id'] ?? 0) !== $clienteId) {
            $this->json(['message' => 'Pedido não encontrado.'], 404);
            return;
        }

        $pixDefaultType = strtolower((string)$this->config->get('NOTE_PIX_KEY_TYPE', 'cnpj'));
        $notaPixEnabled = $this->config->get('NOTE_PIX_ENABLED', '0') === '1';
        $notaPixKey = trim((string)$this->config->get('NOTE_PIX_KEY', ''));
        $notaPixType = in_array($pixDefaultType, ['cnpj', 'cpf', 'email', 'telefone'], true) ? $pixDefaultType : 'cnpj';

        $this->audit->log((int)($decoded['sub'] ?? $clienteId), 'documento', 'pedidos', $pedidoId, [
            'documento' => 'nota_pedido_portal',
            'codigo' => $pedido['codigo'] ?? null,
            'origem' => 'portal',
        ]);

        $this->render('pedidos/nota', [
            'pedido' => $pedido,
            'itens' => $detalhe['itens'] ?? [],
            'notaPixEnabled' => $notaPixEnabled,
            'notaPixKey' => $notaPixKey,
            'notaPixType' => $notaPixType,
        ], [
            'title' => 'Nota de pedido',
            'layout' => 'layouts/print',
        ]);
    }

    public function romaneio($id): void
    {
        $decoded = $this->ensureJwt();
        $pedidoId = (int)$id;
        $clienteId = (int)($decoded['cliente_id'] ?? $decoded['sub'] ?? 0);
        $detalhePedido = $this->pedidos->detalhar($pedidoId);
        $pedido = $detalhePedido['pedido'] ?? null;
        if ($pedidoId <= 0 || !$pedido || $clienteId <= 0 || (int)($pedido['cliente_id'] ?? 0) !== $clienteId) {
            $this->json(['message' => 'Pedido não encontrado.'], 404);
            return;
        }
        $expedicoes = $this->container->get(\App\Repositories\ExpedicoesRepository::class)->all(['pedido_id' => $pedidoId]);
        if (!$expedicoes) {
            $this->json(['message' => 'O romaneio deste pedido ainda não foi gerado.'], 404);
            return;
        }
        usort($expedicoes, static fn(array $a, array $b): int => (int)($b['id'] ?? 0) <=> (int)($a['id'] ?? 0));
        $detalhe = $this->container->get(\App\Services\ExpedicaoService::class)->detalheConcluido((int)$expedicoes[0]['id']);
        $this->audit->log((int)($decoded['sub'] ?? $clienteId), 'documento', 'pedidos', $pedidoId, [
            'documento' => 'romaneio_portal', 'romaneio' => $detalhe['expedicao']['romaneio'] ?? null, 'origem' => 'portal',
        ]);
        $this->render('expedicao/romaneio_print', $detalhe, [
            'title' => 'Romaneio ' . ($detalhe['expedicao']['romaneio'] ?? ''), 'layout' => 'layouts/print',
        ]);
    }

    public function update($id): void
    {
        $decoded = $this->ensureJwt();
        $pedidoId = (int)$id;
        $clienteId = (int)($decoded['cliente_id'] ?? $decoded['sub'] ?? 0);
        $detalhe = $this->pedidos->detalhar($pedidoId);
        $pedido = $detalhe['pedido'] ?? null;

        if (!$pedido || $clienteId <= 0 || (int)($pedido['cliente_id'] ?? 0) !== $clienteId) {
            $this->json(['message' => 'Pedido não encontrado.'], 404);
            return;
        }
        if (!in_array(strtolower((string)($pedido['status'] ?? '')), ['novo', 'aprovacao'], true)) {
            $this->json(['message' => 'Este pedido não pode mais ser editado porque já está em processamento.'], 422);
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        $atuais = [];
        foreach (($detalhe['itens'] ?? []) as $item) {
            $produtoId = (int)($item['produto_id'] ?? 0);
            if ($produtoId > 0) {
                $atuais[$produtoId] = $item;
            }
        }
        $produtosRepo = $this->container->get(\App\Repositories\ProdutosRepository::class);
        $itens = [];
        $incluidos = [];
        foreach (($payload['itens'] ?? []) as $item) {
            $produtoId = (int)($item['produto_id'] ?? 0);
            $quantidade = (float)($item['quantidade'] ?? 0);
            if ($produtoId <= 0 || $quantidade <= 0 || isset($incluidos[$produtoId])) {
                continue;
            }
            $produto = $produtosRepo->find($produtoId);
            if (!$produto
                || (int)($produto['is_ativo'] ?? 1) !== 1
                || (int)($produto['mostrar_portal'] ?? 1) !== 1
                || strtoupper((string)($produto['tipo'] ?? 'PA')) !== 'PA') {
                continue;
            }
            $precoUnitario = isset($atuais[$produtoId])
                ? (float)($atuais[$produtoId]['preco_unit'] ?? 0)
                : (float)($produto['preco'] ?? 0);
            $itens[] = [
                'produto_id' => $produtoId,
                'quantidade' => $quantidade,
                'preco_unit' => $precoUnitario,
            ];
            $incluidos[$produtoId] = true;
        }
        if (!$itens) {
            $this->json(['message' => 'Mantenha pelo menos um item com quantidade válida.'], 422);
            return;
        }

        try {
            $resultado = $this->pedidos->atualizarPedido(
                $pedidoId,
                $clienteId,
                $itens,
                $payload['observacoes'] ?? ($pedido['observacoes'] ?? null),
                (int)($decoded['sub'] ?? $clienteId),
                $pedido['forma_pagto'] ?? null
            );
        } catch (\InvalidArgumentException $exception) {
            $this->json(['message' => $exception->getMessage()], 422);
            return;
        }

        $this->json(['message' => 'Pedido atualizado com sucesso.', 'pedido' => $resultado['pedido'] ?? $resultado]);
    }

    public function updateStatus($id): void
    {
        $decoded = $this->ensureJwt();
        $pedidoId = (int) $id;
        $clienteId = (int)($decoded['cliente_id'] ?? $decoded['sub'] ?? 0);

        if ($pedidoId <= 0) {
            $this->json(['message' => 'Pedido inválido.'], 422);
            return;
        }

        if ($clienteId === 0) {
            $this->json(['message' => 'Cliente não associado ao token'], 422);
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        $status = isset($payload['status']) ? strtolower(trim((string) $payload['status'])) : '';

        if ($status === '' || !in_array($status, self::STATUS_ATUALIZAVEIS, true)) {
            $this->json(['message' => 'Status selecionado não é permitido.'], 422);
            return;
        }

        $detalhe = $this->pedidos->detalhar($pedidoId);
        $pedido = $detalhe['pedido'] ?? null;

        if (!$pedido || (int)($pedido['cliente_id'] ?? 0) !== $clienteId) {
            $this->json(['message' => 'Pedido não encontrado'], 404);
            return;
        }

        try {
            $this->pedidos->atualizarStatus($pedidoId, $status, (int)($decoded['sub'] ?? $clienteId));
        } catch (\InvalidArgumentException $exception) {
            $this->json(['message' => $exception->getMessage()], 422);
            return;
        }

        $this->json([
            'message' => 'Status atualizado com sucesso.',
            'status' => $status,
            'status_label' => $this->formatStatusLabel($status),
        ]);
    }

    public function confirmReceipt($id): void
    {
        $decoded = $this->ensureJwt();
        $pedidoId = (int)$id;
        $clienteId = (int)($decoded['cliente_id'] ?? $decoded['sub'] ?? 0);
        if ($pedidoId <= 0 || $clienteId <= 0) {
            $this->json(['message' => 'Pedido inválido.'], 422);
            return;
        }
        $detalhe = $this->pedidos->detalhar($pedidoId);
        $pedido = $detalhe['pedido'] ?? null;
        if (!$pedido || (int)($pedido['cliente_id'] ?? 0) !== $clienteId) {
            $this->json(['message' => 'Pedido não encontrado.'], 404);
            return;
        }
        try {
            $resultado = $this->expedicao->confirmarRecebimentoPortal($pedidoId, (int)($decoded['sub'] ?? $clienteId));
            $this->json($resultado + ['message' => 'Recebimento confirmado com sucesso.']);
        } catch (\InvalidArgumentException $exception) {
            $this->json(['message' => $exception->getMessage()], 422);
        } catch (\Throwable $exception) {
            $this->json(['message' => 'Não foi possível confirmar o recebimento.'], 500);
        }
    }

    private function ensureJwt(): array
    {
        $token = $this->extractBearerToken();
        if (!$token) {
            $this->json(['message' => 'Token ausente'], 401);
            exit;
        }

        $decoded = $this->auth->validateJwt($token);
        if (!$decoded) {
            $this->json(['message' => 'Token inválido ou expirado'], 401);
            exit;
        }

        return $decoded;
    }

    private function formatStatusLabel(string $status): string
    {
        return $this->pedidos->statusLabel($status);
    }
}
