<?php
namespace App\Repositories;

use App\Core\Model;
use PDOException;

abstract class BaseRepository extends Model
{
    protected string $table;

    public function getConnection(): \PDO
    {
        return $this->db;
    }

    public function find(int|string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(array $criteria = []): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($criteria) {
            $conditions = [];
            foreach ($criteria as $field => $value) {
                $conditions[] = "{$field} = :{$field}";
            }
            $sql .= ' WHERE '.implode(' AND ', $conditions);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($criteria);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $fields = array_keys($data);
        $columns = implode(',', $fields);
        $placeholders = implode(',', array_map(fn($f) => ':' . $f, $fields));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");

        try {
            $stmt->execute($data);
        } catch (PDOException $e) {
            $this->handlePdoException($e);
        }

        return (int)$this->db->lastInsertId();
    }

    public function update(int|string $id, array $data): bool
    {
        $fields = [];
        foreach ($data as $field => $value) {
            $fields[] = "{$field} = :{$field}";
        }
        $data['id'] = $id;
        $sql = "UPDATE {$this->table} SET ".implode(',', $fields)." WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        try {
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->handlePdoException($e);
        }

        return false;
    }

    public function delete(int|string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");

        try {
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            $this->handlePdoException($e);
        }

        return false;
    }

    protected function handlePdoException(PDOException $e): void
    {
        if ($e->getCode() === '23000') {
            throw new \InvalidArgumentException('Não foi possível salvar os dados: verifique campos únicos e tente novamente.');
        }

        throw $e;
    }
}
