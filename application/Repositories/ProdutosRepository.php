<?php
namespace App\Repositories;

class ProdutosRepository extends BaseRepository
{
    protected string $table = 'produtos';
    private ?bool $hasActiveColumn = null;

    public function find(int|string $id): ?array
    {
        $row = parent::find($id);
        return $row ? $this->normalizarLinha($row) : null;
    }

    public function findBySku(string $sku): ?array
    {
        $normalizedSku = $this->normalizarSku($sku);
        if (preg_match('/^(A-|MP-)0*(\d+)$/', $normalizedSku, $matches)) {
            $prefix = $matches[1];
            $number = (int)$matches[2];
            $start = strlen($prefix) + 1;
            $stmt = $this->db->prepare(
                "SELECT * FROM {$this->table} WHERE sku = :sku OR (sku LIKE :prefix_like AND CAST(SUBSTRING(sku, {$start}) AS UNSIGNED) = :number) LIMIT 1"
            );
            $stmt->execute([
                'sku' => $normalizedSku,
                'prefix_like' => $prefix.'%',
                'number' => $number,
            ]);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE sku = :sku LIMIT 1");
            $stmt->execute(['sku' => $normalizedSku]);
        }

        $produto = $stmt->fetch();
        return $produto ? $this->normalizarLinha($produto) : null;
    }

    public function findByRecipeId(int $recipeId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE recipe_id = :recipe_id");
        $stmt->execute(['recipe_id' => $recipeId]);
        $produto = $stmt->fetch();
        return $produto ? $this->normalizarLinha($produto) : null;
    }

    /**
     * Lista produtos com filtros opcionais de busca, categoria e tipo.
     */
    public function search(array $filters = []): array
    {
        $hasActiveColumn = $this->ensureActiveColumn();
        $this->normalizarCategoriasExistentes();

        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($hasActiveColumn && empty($filters['include_inactive'])) {
            $sql .= " AND COALESCE(is_ativo, 1) = 1";
        }

        if (!empty($filters['term'])) {
            $sql .= " AND (sku LIKE :term OR nome LIKE :term OR categoria LIKE :term)";
            $params['term'] = '%' . $filters['term'] . '%';
        }

        if (!empty($filters['categoria'])) {
            if (is_array($filters['categoria'])) {
                $placeholders = [];
                foreach (array_values($filters['categoria']) as $index => $categoria) {
                    $key = 'categoria_' . $index;
                    $placeholders[] = ':' . $key;
                    $params[$key] = $this->normalizarCategoria($categoria) ?? $categoria;
                }
                if ($placeholders) {
                    $sql .= " AND categoria IN (" . implode(', ', $placeholders) . ")";
                }
            } else {
                $sql .= " AND categoria = :categoria";
                $params['categoria'] = $this->normalizarCategoria($filters['categoria']) ?? $filters['categoria'];
            }
        }

        if (!empty($filters['tipo'])) {
            $sql .= " AND tipo = :tipo";
            $params['tipo'] = $filters['tipo'];
        }

        $sortable = [
            'id' => 'id',
            'sku' => 'sku',
            'nome' => 'nome',
            'categoria' => 'categoria',
            'tipo' => 'tipo',
            'unidade' => 'unidade',
            'preco' => 'preco',
            'shelf_life_dias' => 'shelf_life_dias',
            'created_at' => 'created_at',
        ];

        $column = $sortable[$filters['sort'] ?? 'created_at'] ?? 'created_at';

        $dir = $filters['dir'] ?? 'desc';
        $direction = in_array($dir, ['asc', 'desc'], true) ? $dir : 'desc';

        $sql .= " ORDER BY {$column} {$direction}, nome ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return array_map(fn($row) => $this->normalizarLinha($row), $stmt->fetchAll());
    }

    public function categorias(): array
    {
        $hasActiveColumn = $this->ensureActiveColumn();
        $this->normalizarCategoriasExistentes();

        $activeWhere = $hasActiveColumn ? 'COALESCE(is_ativo, 1) = 1 AND ' : '';
        $stmt = $this->db->query("SELECT DISTINCT categoria FROM {$this->table} WHERE {$activeWhere}categoria IS NOT NULL AND categoria <> '' ORDER BY categoria ASC");
        return array_values(array_filter(array_map(fn($categoria) => $this->normalizarCategoria($categoria), array_column($stmt->fetchAll(), 'categoria'))));
    }

    public function deactivate(int $id): bool
    {
        if (!$this->ensureActiveColumn()) {
            throw new \InvalidArgumentException('Atualize o banco para habilitar a opcao de desativar produtos.');
        }
        return $this->update($id, ['is_ativo' => 0]);
    }

    public function activate(int $id): bool
    {
        if (!$this->ensureActiveColumn()) {
            throw new \InvalidArgumentException('Atualize o banco para habilitar a opcao de ativar produtos.');
        }
        return $this->update($id, ['is_ativo' => 1]);
    }

    public function deleteOrDeactivate(int $id): string
    {
        $this->ensureActiveColumn();
        try {
            if (parent::delete($id)) {
                return 'deleted';
            }
        } catch (\Throwable $exception) {
            // Produtos com historico em pedidos/estoque ficam arquivados para preservar os registros.
        }

        $this->deactivate($id);
        return 'deactivated';
    }

    public function nextSequence(string $prefix, int $digits): string
    {
        $start = strlen($prefix) + 1;
        $stmt = $this->db->prepare("SELECT MAX(CAST(SUBSTRING(sku, {$start}) AS UNSIGNED)) AS max_num FROM {$this->table} WHERE sku LIKE :like");
        $stmt->execute(['like' => $prefix.'%']);
        $max = (int)($stmt->fetchColumn() ?: 0);

        return str_pad((string)($max + 1), $digits, '0', STR_PAD_LEFT);
    }

    private function normalizarLinha(array $row): array
    {
        if (isset($row['sku'])) {
            $row['sku'] = $this->normalizarSku((string)$row['sku']);
        }

        if (array_key_exists('categoria', $row)) {
            $row['categoria'] = $this->normalizarCategoria($row['categoria']);
        }

        return $row;
    }

    private function ensureActiveColumn(): bool
    {
        if ($this->hasActiveColumn !== null) {
            return $this->hasActiveColumn;
        }

        try {
            $stmt = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'is_ativo'");
            if (!$stmt || !$stmt->fetch()) {
                $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN is_ativo TINYINT(1) NOT NULL DEFAULT 1 AFTER tabela_nutricional");
            }
            $this->hasActiveColumn = true;
        } catch (\Throwable $exception) {
            // Mantem compatibilidade com bancos sem permissao de alteracao automatica.
            $this->hasActiveColumn = false;
        }

        return $this->hasActiveColumn;
    }

    private function normalizarCategoriasExistentes(): void
    {
        try {
            $stmt = $this->db->query("SELECT id, categoria FROM {$this->table} WHERE categoria IS NOT NULL AND categoria <> ''");
            $update = $this->db->prepare("UPDATE {$this->table} SET categoria = :categoria WHERE id = :id");
            foreach ($stmt->fetchAll() as $row) {
                $categoriaAtual = (string)($row['categoria'] ?? '');
                $categoriaNormalizada = $this->normalizarCategoria($categoriaAtual);
                if ($categoriaNormalizada !== null && $categoriaNormalizada !== $categoriaAtual) {
                    $update->execute([
                        'categoria' => $categoriaNormalizada,
                        'id' => (int)$row['id'],
                    ]);
                }
            }
        } catch (\Throwable $exception) {
            // Mantem a listagem funcionando mesmo se a normalizacao automatica falhar.
        }
    }

    private function normalizarCategoria(mixed $value): ?string
    {
        $categoria = trim((string)($value ?? ''));
        if ($categoria === '') {
            return null;
        }

        if (function_exists('mb_strtoupper')) {
            return mb_strtoupper($categoria, 'UTF-8');
        }

        return strtoupper($categoria);
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
