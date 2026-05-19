<?php
namespace App\Repositories;

class ProdutoComponentesRepository extends BaseRepository
{
    protected string $table = 'produto_componentes';

    public function listarComponentes(int $produtoId): array
    {
        $stmt = $this->db->prepare("SELECT pc.*, p.sku AS componente_sku, p.nome AS componente_nome, p.unidade AS componente_unidade FROM {$this->table} pc JOIN produtos p ON p.id = pc.componente_id WHERE pc.produto_id = :produto");
        $stmt->execute(['produto' => $produtoId]);
        return array_map(fn($row) => $this->normalizarLinha($row), $stmt->fetchAll());
    }

    private function normalizarLinha(array $row): array
    {
        if (isset($row['componente_sku'])) {
            $row['componente_sku'] = $this->normalizarSku((string)$row['componente_sku']);
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
