<?php
namespace App\Repositories;

class RecipesRepository extends BaseRepository
{
    protected string $table = 'recipes';

    public function nextSequence(string $prefix, int $digits): string
    {
        $start = strlen($prefix) + 1;
        $stmt = $this->db->prepare("SELECT MAX(CAST(SUBSTRING(sku, {$start}) AS UNSIGNED)) AS max_num FROM {$this->table} WHERE sku LIKE :like");
        $stmt->execute(['like' => $prefix.'%']);
        $max = (int)($stmt->fetchColumn() ?: 0);

        return str_pad((string)($max + 1), $digits, '0', STR_PAD_LEFT);
    }

    public function findActiveByProdutoPaId(int $produtoPaId, ?int $excludeRecipeId = null): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE produto_pa_id = :produto AND active = 1";
        $params = ['produto' => $produtoPaId];

        if ($excludeRecipeId !== null) {
            $sql .= ' AND id <> :exclude';
            $params['exclude'] = $excludeRecipeId;
        }

        $sql .= ' LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function deactivateLinkedToProduct(int $produtoId, ?int $recipeId = null): int
    {
        $conditions = ['produto_pa_id = :produto'];
        $params = ['produto' => $produtoId];
        if ($recipeId !== null && $recipeId > 0) {
            $conditions[] = 'id = :recipe';
            $params['recipe'] = $recipeId;
        }

        $sql = "UPDATE {$this->table} SET active = 0 WHERE active = 1 AND (" . implode(' OR ', $conditions) . ')';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    public function search(array $filters = []): array
    {
        $sql = "SELECT r.*, p.sku AS produto_pa_sku, p.nome AS produto_pa_nome,
                       COALESCE(ri.item_count, 0) AS item_count
                FROM {$this->table} r
                LEFT JOIN produtos p ON p.id = r.produto_pa_id
                LEFT JOIN (
                    SELECT recipe_id, COUNT(*) AS item_count
                    FROM recipe_items
                    GROUP BY recipe_id
                ) ri ON ri.recipe_id = r.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['term'])) {
            $sql .= " AND (r.name LIKE :term OR r.sku LIKE :term OR p.nome LIKE :term OR p.sku LIKE :term)";
            $params['term'] = '%' . $filters['term'] . '%';
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $sql .= " AND r.active = :active";
            $params['active'] = (int)$filters['active'];
        }

        $sql .= " ORDER BY r.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
