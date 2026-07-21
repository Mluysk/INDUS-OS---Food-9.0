<?php
namespace App\Controllers\Api;

use App\Core\Controller;
use App\Services\PedidosService;
use App\Services\ConfigService;

class PedidosApiController extends Controller
{
    private PedidosService $pedidos;
    private ConfigService $config;

    private const STATUS_ATUALIZAVEIS = ['novo', 'aprovacao', 'producao', 'separacao', 'expedicao', 'faturado', 'cancelado'];

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        PedidosService $pedidos,
        ConfigService $config
    ) {
        parent::__construct($container, $auth, $audit);
        $this->pedidos = $pedidos;
        $this->config = $config;
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

        $historicoStmt = $db->prepare("SELECT acao, detalhes, created_at FROM audit_logs WHERE entidade = 'pedidos' AND entidade_id = :pedido_id AND acao IN ('create', 'update', 'status', 'expedir') ORDER BY id DESC");
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
