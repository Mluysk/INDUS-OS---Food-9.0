<?php
namespace App\Services;

use App\Repositories\PedidosRepository;
use App\Repositories\PedidoItensRepository;
use App\Repositories\ProdutosRepository;
use App\Repositories\OrdensProducaoRepository;
use App\Repositories\ClientesRepository;
use InvalidArgumentException;

class PedidosService
{
    private const STATUS_VALIDOS = ['novo', 'aprovacao', 'producao', 'separacao', 'expedicao', 'faturado', 'cancelado'];
    private const STATUS_LABELS = [
        'novo' => 'Recebido',
        'aprovacao' => 'Em aprovação',
        'producao' => 'Aprovado',
        'separacao' => 'Em separação',
        'expedicao' => 'Em transporte',
        'faturado' => 'Pedido recebido',
        'cancelado' => 'Cancelado',
    ];

    private PedidosRepository $pedidos;
    private PedidoItensRepository $itens;
    private ProdutosRepository $produtos;
    private EstoqueService $estoque;
    private OrdensProducaoRepository $opRepository;
    private AuditService $audit;
    private ClientesRepository $clientes;
    private RecipeService $recipes;

    public function __construct(
        PedidosRepository $pedidos,
        PedidoItensRepository $itens,
        ProdutosRepository $produtos,
        EstoqueService $estoque,
        OrdensProducaoRepository $opRepository,
        AuditService $audit,
        ClientesRepository $clientes,
        RecipeService $recipes
    ) {
        $this->pedidos = $pedidos;
        $this->itens = $itens;
        $this->produtos = $produtos;
        $this->estoque = $estoque;
        $this->opRepository = $opRepository;
        $this->audit = $audit;
        $this->clientes = $clientes;
        $this->recipes = $recipes;
    }

    public function criarPedidoCliente(array $pedido, array $itens, int $clienteId, int $usuarioId, bool $baixaNoCriar): array
    {
        $pedido['cliente_id'] = $clienteId;
        $pedido['status'] = 'aprovacao';
        $pedidoId = $this->pedidos->create($pedido);
        $backorder = [];
        foreach ($itens as $item) {
            $item['pedido_id'] = $pedidoId;
            $itemId = $this->itens->create($item);
            $produto = $this->produtos->find($item['produto_id']);
            if ($produto && $produto['tipo'] === 'PA') {
                $reserva = $this->estoque->reservarFefo(
                    $item['produto_id'],
                    (float)$item['quantidade'],
                    'pedido',
                    $pedidoId,
                    $usuarioId,
                    $baixaNoCriar
                );
                if ($baixaNoCriar && !empty($produto['recipe_id'])) {
                    $this->recipes->runRecipeFromSale($itemId, $usuarioId);
                }
                if ($reserva['faltante'] > 0) {
                    $backorder[] = [
                        'produto_id' => $item['produto_id'],
                        'faltante' => $reserva['faltante'],
                    ];
                }
            }
        }
        if ($backorder) {
            foreach ($backorder as $faltante) {
                $this->opRepository->create([
                    'pedido_id' => $pedidoId,
                    'produto_id' => $faltante['produto_id'],
                    'quantidade' => $faltante['faltante'],
                    'etapa' => 'planejada',
                ]);
            }
        }
        $pedidoCriado = $this->pedidos->find($pedidoId);
        if ($pedidoCriado) {
            $pedidoCriado['status_label'] = $this->statusLabel($pedidoCriado['status'] ?? null);
        }
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $auditPedido = $pedido;
        $auditPedido['dispositivo'] = $this->detectarDispositivo($userAgent);
        $auditPedido['user_agent'] = $userAgent;
        $auditPedido['localidade'] = $_SERVER['REMOTE_ADDR'] ?? 'Não registrada';
        $this->audit->log($usuarioId, 'create', 'pedidos', $pedidoId, $auditPedido);
        return [
            'pedido_id' => $pedidoId,
            'pedido' => $pedidoCriado,
            'backorder' => $backorder,
        ];
    }

    public function atualizarPedido(int $pedidoId, int $clienteId, array $itens, ?string $observacoes, int $usuarioId, ?string $formaPagto = null, ?float $freteValor = null, ?float $desconto = null): array
    {
        $pedidoAtual = $this->pedidos->find($pedidoId);
        if ($pedidoId <= 0 || !$pedidoAtual) {
            throw new InvalidArgumentException('Pedido não encontrado.');
        }
        if ($clienteId <= 0) {
            throw new InvalidArgumentException('Cliente é obrigatório.');
        }
        if (empty($itens)) {
            throw new InvalidArgumentException('Informe pelo menos um item para o pedido.');
        }

        $freteFinal = $freteValor ?? (float)($pedidoAtual['frete_valor'] ?? 0);
        $total = $this->calcularTotal($itens) + $freteFinal;
        $descontoFinal = $desconto ?? (float)($pedidoAtual['desconto'] ?? 0);
        $descontoFinal = min(max(0.0, $descontoFinal), $total);
        $db = $this->pedidos->getConnection();
        $db->beginTransaction();

        try {
            $payload = [
                'cliente_id' => $clienteId,
                'observacoes' => $observacoes,
                'frete_valor' => $freteFinal,
                'desconto' => $descontoFinal,
                'total' => $total,
            ];
            if ($formaPagto !== null) {
                $payload['forma_pagto'] = $formaPagto;
            }
            $this->pedidos->update($pedidoId, $payload);
            $this->itens->deleteByPedido($pedidoId);
            foreach ($itens as $item) {
                $this->itens->create([
                    'pedido_id' => $pedidoId,
                    'produto_id' => (int)$item['produto_id'],
                    'quantidade' => (float)$item['quantidade'],
                    'preco_unit' => (float)$item['preco_unit'],
                ]);
            }
            $db->commit();
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }

        $this->audit->log($usuarioId, 'update', 'pedidos', $pedidoId, [
            'cliente_id' => $clienteId,
            'observacoes' => $observacoes,
            'forma_pagto' => $formaPagto,
            'frete_valor' => $freteFinal,
            'desconto' => $descontoFinal,
            'total' => $total,
            'itens' => $itens,
        ]);

        return $this->detalharCompleto($pedidoId);
    }

    public function atualizarStatus(int $pedidoId, string $status, int $usuarioId): void
    {
        $statusNormalizado = strtolower(trim($status));
        if (!in_array($statusNormalizado, self::STATUS_VALIDOS, true)) {
            throw new InvalidArgumentException('Status de pedido inválido.');
        }
        $pedidoAtual = $this->pedidos->find($pedidoId);
        $statusAnterior = $pedidoAtual['status'] ?? null;

        $this->pedidos->update($pedidoId, ['status' => $statusNormalizado]);
        $this->audit->log($usuarioId, 'status', 'pedidos', $pedidoId, ['status' => $statusNormalizado]);

        if ($statusNormalizado === 'faturado') {
            try {
                $itens = $this->itens->listarPorPedido($pedidoId);
                foreach ($itens as $item) {
                    $this->recipes->runRecipeFromSale((int)$item['id'], $usuarioId);
                }
            } catch (\Throwable $e) {
                if ($statusAnterior !== null) {
                    $this->pedidos->update($pedidoId, ['status' => $statusAnterior]);
                }
                throw $e;
            }
        }
    }

    public function listar(): array
    {
        return $this->pedidos->all();
    }

    public function detalhar(int $id): array
    {
        $pedido = $this->pedidos->find($id);
        if ($pedido) {
            $pedido['status_label'] = $this->statusLabel($pedido['status'] ?? null);
        }

        return [
            'pedido' => $pedido,
            'itens' => $this->itens->listarPorPedido($id),
        ];
    }

    public function detalharCompleto(int $id): array
    {
        $pedido = $this->pedidos->findWithCliente($id) ?? $this->pedidos->find($id);
        $itens = $this->itens->listarPorPedido($id);

        if ($pedido) {
            $pedido['status_label'] = $this->statusLabel($pedido['status'] ?? null);
            $freteValor = (float)($pedido['frete_valor'] ?? 0);
            if (!isset($pedido['total']) || $pedido['total'] === null) {
                $pedido['total'] = $this->calcularTotal($itens) + $freteValor;
            }
            if (!isset($pedido['desconto'])) {
                $pedido['desconto'] = 0;
            }
            if (!isset($pedido['cliente']) && isset($pedido['cliente_nome_fantasia'])) {
                $pedido['cliente'] = [
                    'nome_fantasia' => $pedido['cliente_nome_fantasia'],
                    'razao_social' => $pedido['cliente_razao_social'] ?? null,
                    'cnpj' => $pedido['cliente_cnpj'] ?? null,
                    'cpf' => $pedido['cliente_cpf'] ?? null,
                    'email' => $pedido['cliente_email'] ?? null,
                    'telefone' => $pedido['cliente_telefone'] ?? null,
                    'cep' => $pedido['cliente_cep'] ?? null,
                    'endereco' => $pedido['cliente_endereco'] ?? null,
                    'numero' => $pedido['cliente_numero'] ?? null,
                    'bairro' => $pedido['cliente_bairro'] ?? null,
                    'cidade' => $pedido['cliente_cidade'] ?? null,
                    'estado' => $pedido['cliente_estado'] ?? null,
                ];
            }
            if (!isset($pedido['cliente']) && isset($pedido['cliente_id'])) {
                $cliente = $this->clientes->find((int)$pedido['cliente_id']);
                if ($cliente) {
                    $pedido['cliente'] = $cliente;
                }
            }
        }

        return [
            'pedido' => $pedido,
            'itens' => $itens,
        ];
    }

    public function listarPorCliente(int $clienteId): array
    {
        $pedidos = $this->pedidos->listarPorCliente($clienteId);
        $resultado = [];
        foreach ($pedidos as $pedido) {
            $pedidoId = (int)($pedido['id'] ?? 0);
            $itens = $pedidoId > 0 ? $this->itens->listarPorPedido($pedidoId) : [];
            $pedido['itens'] = $itens;
            $pedido['status_label'] = $this->statusLabel($pedido['status'] ?? null);
            if (!isset($pedido['total']) || $pedido['total'] === null) {
                $pedido['total'] = $this->calcularTotal($itens) + (float)($pedido['frete_valor'] ?? 0);
            }
            if (!isset($pedido['desconto'])) {
                $pedido['desconto'] = 0;
            }
            $resultado[] = $pedido;
        }
        return $resultado;
    }

    public function statusLabel(?string $status): string
    {
        $normalizado = strtolower(trim((string)$status));
        if ($normalizado === '') {
            return self::STATUS_LABELS['novo'];
        }

        return self::STATUS_LABELS[$normalizado] ?? ucfirst($normalizado);
    }

    private function calcularTotal(array $itens): float
    {
        return array_reduce($itens, static function ($carry, $item) {
            $quantidade = (float)($item['quantidade'] ?? 0);
            $preco = (float)($item['preco_unit'] ?? 0);
            return $carry + ($quantidade * $preco);
        }, 0.0);
    }

    private function detectarDispositivo(string $userAgent): string
    {
        $agent = strtolower(trim($userAgent));
        if ($agent === '') {
            return 'Web';
        }

        $mobileTokens = [
            'android',
            'iphone',
            'ipad',
            'ipod',
            'mobile',
            'windows phone',
            'blackberry',
            'opera mini',
            'tablet',
        ];

        foreach ($mobileTokens as $token) {
            if (strpos($agent, $token) !== false) {
                return 'Mobile';
            }
        }

        return 'Web';
    }
}
