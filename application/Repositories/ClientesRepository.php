<?php
namespace App\Repositories;

class ClientesRepository extends BaseRepository
{
    protected string $table = 'clientes';
    private bool $portalCustomizationColumnsReady = false;

    public function ensurePortalCustomizationColumns(): void
    {
        if ($this->portalCustomizationColumnsReady) {
            return;
        }

        $columns = [
            'portal_palette_hex' => "ALTER TABLE {$this->table} ADD COLUMN portal_palette_hex VARCHAR(7) NULL AFTER estado",
            'portal_palette_name' => "ALTER TABLE {$this->table} ADD COLUMN portal_palette_name VARCHAR(80) NULL AFTER portal_palette_hex",
            'portal_cart_hex' => "ALTER TABLE {$this->table} ADD COLUMN portal_cart_hex VARCHAR(7) NULL AFTER portal_palette_name",
        ];

        foreach ($columns as $column => $sql) {
            $stmt = $this->db->prepare("SHOW COLUMNS FROM {$this->table} LIKE :column");
            $stmt->execute(['column' => $column]);
            if (!$stmt->fetch()) {
                $this->db->exec($sql);
            }
        }

        $this->portalCustomizationColumnsReady = true;
    }

    public function findByDocumento(string $documento): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE cpf = :doc OR cnpj = :doc";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['doc' => $documento]);
        $cliente = $stmt->fetch();
        return $cliente ?: null;
    }

    public function findByDocumentoExcept(string $documento, int $ignoreId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE (cpf = :doc OR cnpj = :doc) AND id <> :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['doc' => $documento, 'id' => $ignoreId]);
        $cliente = $stmt->fetch();
        return $cliente ?: null;
    }

    public function findByUsuarioId(int $usuarioId): ?array
    {
        $this->ensurePortalCustomizationColumns();
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE usuario_id = :usuario");
        $stmt->execute(['usuario' => $usuarioId]);
        $cliente = $stmt->fetch();
        return $cliente ?: null;
    }

    public function updatePortalProfile(int $id, array $data): bool
    {
        $this->ensurePortalCustomizationColumns();
        return $this->update($id, $data);
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $cliente = $stmt->fetch();
        return $cliente ?: null;
    }

    public function findByEmailExcept(string $email, int $ignoreId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email AND id <> :id");
        $stmt->execute(['email' => $email, 'id' => $ignoreId]);
        $cliente = $stmt->fetch();
        return $cliente ?: null;
    }

    public function releaseInactivePortalIdentity(string $documento, string $email): int
    {
        $documento = preg_replace('/\D+/', '', $documento) ?? '';
        $email = strtolower(trim($email));
        if ($documento === '' && $email === '') {
            return 0;
        }

        $conditions = [];
        $params = [];
        if ($documento !== '') {
            $conditions[] = '(c.cpf = :doc OR c.cnpj = :doc)';
            $params['doc'] = $documento;
        }
        if ($email !== '') {
            $conditions[] = 'LOWER(c.email) = :email';
            $params['email'] = $email;
        }

        $stmt = $this->db->prepare("SELECT c.id
            FROM {$this->table} c
            LEFT JOIN usuarios u ON u.id = c.usuario_id
            WHERE (" . implode(' OR ', $conditions) . ")
              AND (c.usuario_id IS NULL OR COALESCE(u.is_ativo, 1) = 0)");
        $stmt->execute($params);
        $ids = array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: []);
        if (!$ids) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $update = $this->db->prepare("UPDATE {$this->table}
            SET usuario_id = NULL,
                razao_social = NULL,
                nome_fantasia = NULL,
                cnpj = NULL,
                cpf = NULL,
                ie = NULL,
                im = NULL,
                email = NULL,
                telefone = NULL,
                whatsapp = NULL,
                cep = NULL,
                endereco = NULL,
                numero = NULL,
                bairro = NULL,
                cidade = NULL,
                estado = NULL
            WHERE id IN ({$placeholders})");
        $update->execute($ids);

        return count($ids);
    }
}
