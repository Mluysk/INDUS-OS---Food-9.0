<?php
namespace App\Repositories;

class ProdutosRepository extends BaseRepository
{
    protected string $table = 'produtos';
    private ?bool $hasActiveColumn = null;
    private ?bool $hasImageColumn = null;
    private bool $deletedSkuHistoryReady = false;

    public function create(array $data): int
    {
        if (!$this->ensureImageColumn()) {
            unset($data['imagem_path']);
            unset($data['imagem_pronto_path']);
        }
        return parent::create($data);
    }

    public function update(int|string $id, array $data): bool
    {
        if (!$this->ensureImageColumn()) {
            unset($data['imagem_path']);
            unset($data['imagem_pronto_path']);
        }
        return parent::update($id, $data);
    }

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
        $this->ensureImageColumn();
        $this->normalizarCategoriasExistentes();

        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        $status = strtolower(trim((string)($filters['status'] ?? '')));
        if ($hasActiveColumn && $status === 'inactive') {
            $sql .= " AND COALESCE(is_ativo, 1) = 0";
        } elseif ($hasActiveColumn && ($status === 'active' || empty($filters['include_inactive']))) {
            $sql .= " AND COALESCE(is_ativo, 1) = 1";
        }

        if (!empty($filters['term'])) {
            $sql .= " AND (CAST(id AS CHAR) LIKE :term OR sku LIKE :term OR nome LIKE :term OR categoria LIKE :term)";
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

    public function deletePermanently(int $id): bool
    {
        $produto = $this->find($id);
        if (!$produto) {
            return false;
        }
        $this->ensureDeletedSkuHistoryTable();

        $dependencies = [
            ['table' => 'produto_componentes', 'column' => 'produto_id'],
            ['table' => 'produto_componentes', 'column' => 'componente_id'],
            ['table' => 'produtos_lancamentos', 'column' => 'produto_id'],
            ['table' => 'recipe_items', 'column' => 'stock_product_id'],
            ['table' => 'recipe_run_items', 'column' => 'stock_product_id'],
            ['table' => 'estoques', 'column' => 'produto_id'],
            ['table' => 'movimentos_estoque', 'column' => 'produto_id'],
            ['table' => 'pedido_itens', 'column' => 'produto_id'],
            ['table' => 'ordens_producao', 'column' => 'produto_id'],
            ['table' => 'mapa_entradas', 'column' => 'produto_id'],
        ];

        $this->db->beginTransaction();
        try {
            $history = $this->db->prepare(
                'INSERT INTO produtos_skus_excluidos (sku, produto_id_original, deleted_at)
                 VALUES (:sku, :produto_id, CURRENT_TIMESTAMP)
                 ON DUPLICATE KEY UPDATE produto_id_original = VALUES(produto_id_original), deleted_at = CURRENT_TIMESTAMP'
            );
            $history->execute([
                'sku' => (string)($produto['sku'] ?? ''),
                'produto_id' => $id,
            ]);

            if ($this->tableHasColumn('recipes', 'produto_pa_id')) {
                $stmt = $this->db->prepare('UPDATE recipes SET produto_pa_id = NULL WHERE produto_pa_id = :id');
                $stmt->execute(['id' => $id]);
            }

            foreach ($dependencies as $dependency) {
                if (!$this->tableHasColumn($dependency['table'], $dependency['column'])) {
                    continue;
                }
                $stmt = $this->db->prepare(
                    sprintf('DELETE FROM `%s` WHERE `%s` = :id', $dependency['table'], $dependency['column'])
                );
                $stmt->execute(['id' => $id]);
            }

            $deleted = parent::delete($id);
            $this->db->commit();
            return $deleted;
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw new \InvalidArgumentException('Nao foi possivel excluir permanentemente o produto e seus vinculos.');
        }
    }

    public function nextSequence(string $prefix, int $digits, bool $reuseAvailable = false): string
    {
        $start = strlen($prefix) + 1;
        if ($reuseAvailable) {
            $stmt = $this->db->prepare(
                "SELECT CAST(SUBSTRING(sku, {$start}) AS UNSIGNED) AS sequence_num
                 FROM {$this->table}
                 WHERE sku LIKE :like
                 ORDER BY sequence_num ASC"
            );
            $stmt->execute(['like' => $prefix.'%']);
            $expected = 1;
            foreach ($stmt->fetchAll() as $row) {
                $current = (int)($row['sequence_num'] ?? 0);
                if ($current < $expected) {
                    continue;
                }
                if ($current > $expected) {
                    break;
                }
                $expected++;
            }

            return str_pad((string)$expected, $digits, '0', STR_PAD_LEFT);
        }

        $this->ensureDeletedSkuHistoryTable();
        $stmt = $this->db->prepare(
            "SELECT MAX(sequence_num) FROM (
                SELECT CAST(SUBSTRING(sku, {$start}) AS UNSIGNED) AS sequence_num
                FROM {$this->table} WHERE sku LIKE :active_like
                UNION ALL
                SELECT CAST(SUBSTRING(sku, {$start}) AS UNSIGNED) AS sequence_num
                FROM produtos_skus_excluidos WHERE sku LIKE :deleted_like
            ) sku_history"
        );
        $stmt->execute([
            'active_like' => $prefix.'%',
            'deleted_like' => $prefix.'%',
        ]);
        $max = (int)($stmt->fetchColumn() ?: 0);

        return str_pad((string)($max + 1), $digits, '0', STR_PAD_LEFT);
    }

    private function tableHasColumn(string $table, string $column): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column'
        );
        $stmt->execute(['table' => $table, 'column' => $column]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function ensureDeletedSkuHistoryTable(): void
    {
        if ($this->deletedSkuHistoryReady) {
            return;
        }

        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS produtos_skus_excluidos (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                sku VARCHAR(50) NOT NULL UNIQUE,
                produto_id_original INT NULL,
                deleted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
        $this->deletedSkuHistoryReady = true;
    }

    private function normalizarLinha(array $row): array
    {
        $this->ensureImageColumn();

        if (isset($row['sku'])) {
            $row['sku'] = $this->normalizarSku((string)$row['sku']);
        }

        if (array_key_exists('categoria', $row)) {
            $row['categoria'] = $this->normalizarCategoria($row['categoria']);
        }

        return $row;
    }

    private function ensureImageColumn(): bool
    {
        if ($this->hasImageColumn !== null) {
            return $this->hasImageColumn;
        }

        try {
            $stmt = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'imagem_path'");
            if (!$stmt || !$stmt->fetch()) {
                $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN imagem_path VARCHAR(255) NULL AFTER tabela_nutricional");
            }
            $stmt = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'imagem_pronto_path'");
            if (!$stmt || !$stmt->fetch()) {
                $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN imagem_pronto_path VARCHAR(255) NULL AFTER imagem_path");
            }
            $this->hasImageColumn = true;
        } catch (\Throwable $exception) {
            $this->hasImageColumn = false;
        }

        return $this->hasImageColumn;
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
