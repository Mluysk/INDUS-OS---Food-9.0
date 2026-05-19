<?php
namespace App\Repositories;

class PedidoItensRepository extends BaseRepository
{
    protected string $table = 'pedido_itens';

    public function listarPorPedido(int $pedidoId): array
    {
        $stmt = $this->db->prepare("SELECT pi.*, p.nome, p.sku, p.unidade, p.peso_liquido, p.recipe_id FROM {$this->table} pi JOIN produtos p ON p.id = pi.produto_id WHERE pi.pedido_id = :pedido");
        $stmt->execute(['pedido' => $pedidoId]);
        return array_map(fn($row) => $this->normalizarLinha($row), $stmt->fetchAll());
    }

    public function findWithProduto(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT pi.*, p.nome, p.sku, p.unidade, p.peso_liquido, p.recipe_id FROM {$this->table} pi JOIN produtos p ON p.id = pi.produto_id WHERE pi.id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->normalizarLinha($row) : null;
    }

    public function deleteByPedido(int $pedidoId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE pedido_id = :pedido");
        return $stmt->execute(['pedido' => $pedidoId]);
    }

    private function normalizarLinha(array $row): array
    {
        if (isset($row['sku'])) {
            $row['sku'] = $this->normalizarSku((string)$row['sku']);
        }

        return $row;
    }

    private function normalizarSku(string $sku): string
    {
        $sku = strtoupper(trim($sku));
        if (preg_match('/^(A-|MP-)0*(\d+)$/', $sku, $matches)) {
            return $matches[1] . str_pad((string)((int)$matches[2]), $this->skuDigits(), '0', STR_PAD_LEFT);
        }

        return $sku;
    }

    private function skuDigits(): int
    {
        try {
            $stmt = $this->db->prepare("SELECT valor FROM configuracoes WHERE chave = 'SKU_DIGITS' LIMIT 1");
            $stmt->execute();
            $digits = (int)($stmt->fetchColumn() ?: 3);
        } catch (\Throwable $exception) {
            $digits = 3;
        }

        if ($digits < 2) {
            return 2;
        }
        if ($digits > 8) {
            return 8;
        }

        return $digits;
    }
}
