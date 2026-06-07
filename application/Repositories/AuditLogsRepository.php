<?php
namespace App\Repositories;

class AuditLogsRepository extends BaseRepository
{
    protected string $table = 'audit_logs';

    public function ultimasModificacoes(string $entidade, array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn (int $id): bool => $id > 0)));
        if (!$ids) {
            return [];
        }

        $placeholders = [];
        $params = ['entidade' => $entidade];
        foreach ($ids as $idx => $id) {
            $key = 'id' . $idx;
            $placeholders[] = ':' . $key;
            $params[$key] = $id;
        }

        $stmt = $this->db->prepare(
            "SELECT entidade_id, MAX(created_at) AS updated_at
             FROM {$this->table}
             WHERE entidade = :entidade
               AND acao IN ('update', 'status')
               AND entidade_id IN (" . implode(',', $placeholders) . ")
             GROUP BY entidade_id"
        );
        $stmt->execute($params);

        $map = [];
        foreach ($stmt->fetchAll() as $row) {
            $map[(int)($row['entidade_id'] ?? 0)] = $row['updated_at'] ?? null;
        }

        return $map;
    }
}
