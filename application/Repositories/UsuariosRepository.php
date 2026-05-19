<?php
namespace App\Repositories;

use PDOException;

class UsuariosRepository extends BaseRepository
{
    protected string $table = 'usuarios';

    /**
     * Lista usuários ativos com dados básicos ordenados por nome.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(array $criteria = []): array
    {
        $sql = "SELECT id, nome, email, papel, cpf, cnpj FROM {$this->table}";

        $conditions = [];
        if ($criteria) {
            foreach ($criteria as $field => $value) {
                $conditions[] = "{$field} = :{$field}";
            }
        }

        if (!array_key_exists('is_ativo', $criteria)) {
            $conditions[] = 'COALESCE(is_ativo, 1) = 1';
        }

        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY nome ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($criteria);

        return $stmt->fetchAll() ?: [];
    }

    public function findByEmailOrDocumento(string $login): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :login OR cpf = :login OR cnpj = :login";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['login' => $login]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email AND COALESCE(is_ativo, 1) = 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByEmailExcept(string $email, int $ignoreId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email AND id <> :id AND COALESCE(is_ativo, 1) = 1");
        $stmt->execute(['email' => $email, 'id' => $ignoreId]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByDocumento(string $documento): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE (cpf = :doc OR cnpj = :doc) AND COALESCE(is_ativo, 1) = 1");
        $stmt->execute(['doc' => $documento]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function releaseInactiveIdentity(string $documento, string $email): int
    {
        $documento = preg_replace('/\D+/', '', $documento) ?? '';
        $email = strtolower(trim($email));
        if ($documento === '' && $email === '') {
            return 0;
        }

        $conditions = [];
        $params = [];
        if ($documento !== '') {
            $conditions[] = '(cpf = :doc OR cnpj = :doc)';
            $params['doc'] = $documento;
        }
        if ($email !== '') {
            $conditions[] = 'LOWER(email) = :email';
            $params['email'] = $email;
        }

        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE (" . implode(' OR ', $conditions) . ") AND COALESCE(is_ativo, 1) = 0");
        $stmt->execute($params);
        $ids = array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: []);
        foreach ($ids as $id) {
            $this->softDeleteById($id);
        }

        return count($ids);
    }

    public function deleteById(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");

        try {
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }

            return $this->softDeleteById($id);
        }
    }

    private function softDeleteById(int $id): bool
    {
        $token = bin2hex(random_bytes(6));
        $email = sprintf('deleted-user-%d-%s@local.invalid', $id, $token);
        $password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("UPDATE {$this->table}
            SET nome = :nome,
                email = :email,
                cpf = NULL,
                cnpj = NULL,
                senha_hash = :senha_hash,
                is_ativo = 0
            WHERE id = :id");

        return $stmt->execute([
            'id' => $id,
            'nome' => 'Usuário removido #'.$id,
            'email' => $email,
            'senha_hash' => $password,
        ]);
    }
}
