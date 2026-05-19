<?php
namespace App\Services;

use App\Repositories\ExpedicoesRepository;
use App\Repositories\PedidosRepository;
use App\Repositories\PedidoItensRepository;

class ExpedicaoService
{
    private ExpedicoesRepository $expedicoes;
    private PedidosRepository $pedidos;
    private PedidoItensRepository $itens;
    private EstoqueService $estoque;
    private AuditService $audit;

    public function __construct(
        ExpedicoesRepository $expedicoes,
        PedidosRepository $pedidos,
        PedidoItensRepository $itens,
        EstoqueService $estoque,
        AuditService $audit
    ) {
        $this->expedicoes = $expedicoes;
        $this->pedidos = $pedidos;
        $this->itens = $itens;
        $this->estoque = $estoque;
        $this->audit = $audit;
    }

    public function gerarPicking(int $pedidoId): array
    {
        $pedido = $this->pedidos->find($pedidoId);
        $itens = $this->itens->listarPorPedido($pedidoId);
        return ['pedido' => $pedido, 'itens' => $itens];
    }

    public function registrarExpedicao(int $pedidoId, array $dados, int $usuarioId, bool $converteReservas): int
    {
        if ($converteReservas) {
            foreach ($this->itens->listarPorPedido($pedidoId) as $item) {
                $this->estoque->reservarFefo($item['produto_id'], (float)$item['quantidade'], 'pedido', $pedidoId, $usuarioId, true);
            }
        }
        $dados['pedido_id'] = $pedidoId;
        $id = $this->expedicoes->create($dados);
        $this->pedidos->update($pedidoId, ['status' => 'expedicao']);
        $this->audit->log($usuarioId, 'expedir', 'pedidos', $pedidoId, $dados);
        return $id;
    }

    public function listarFila(): array
    {
        $fila = [];
        foreach ($this->pedidos->all() as $pedido) {
            if (in_array($pedido['status'], ['separacao', 'expedicao', 'aprovacao', 'producao'], true)) {
                $pedido['itens'] = $this->itens->listarPorPedido($pedido['id']);
                $fila[] = $pedido;
            }
        }
        return $fila;
    }
}
