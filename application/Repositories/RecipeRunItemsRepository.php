<?php
namespace App\Repositories;

class RecipeRunItemsRepository extends BaseRepository
{
    protected string $table = 'recipe_run_items';

    public function listByRun(int $runId): array
    {
        $stmt = $this->db->prepare("SELECT rri.*, p.sku, p.nome, p.unidade AS product_unit FROM {$this->table} rri JOIN produtos p ON p.id = rri.stock_product_id WHERE rri.run_id = :run ORDER BY rri.id ASC");
        $stmt->execute(['run' => $runId]);
        return $stmt->fetchAll();
    }
}
