<?php
namespace App\Repositories;

class RecipeItemsRepository extends BaseRepository
{
    protected string $table = 'recipe_items';

    public function listByRecipe(int $recipeId): array
    {
        $stmt = $this->db->prepare("SELECT ri.*, p.sku, p.nome, p.unidade AS product_unit FROM {$this->table} ri JOIN produtos p ON p.id = ri.stock_product_id WHERE ri.recipe_id = :recipe ORDER BY ri.sort_order ASC, ri.id ASC");
        $stmt->execute(['recipe' => $recipeId]);
        return $stmt->fetchAll();
    }

    public function deleteByRecipe(int $recipeId): void
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE recipe_id = :recipe");
        $stmt->execute(['recipe' => $recipeId]);
    }
}
