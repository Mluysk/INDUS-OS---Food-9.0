<?php
namespace App\Repositories;

class RecipeRunsRepository extends BaseRepository
{
    protected string $table = 'recipe_runs';

    public function listByRecipe(int $recipeId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE recipe_id = :recipe ORDER BY created_at DESC, id DESC");
        $stmt->execute(['recipe' => $recipeId]);
        return $stmt->fetchAll();
    }

    public function findBySource(string $source, int $sourceId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE source = :source AND source_id = :source_id LIMIT 1");
        $stmt->execute(['source' => $source, 'source_id' => $sourceId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
