<?php
namespace App\Repositories;

class EstoquesRepository extends BaseRepository
{
    protected string $table = 'estoques';

    public function findLotesFefo(int $produtoId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE produto_id = :produto AND quantidade > 0 ORDER BY validade ASC, id ASC");
        $stmt->execute(['produto' => $produtoId]);
        return $stmt->fetchAll();
    }
}
