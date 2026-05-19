<?php
namespace App\Repositories;

class ProdutosLancamentosRepository extends BaseRepository
{
    protected string $table = 'produtos_lancamentos';
    private bool $checked = false;

    private function ensureTableExists(): void
    {
        if ($this->checked) {
            return;
        }

        try {
            $this->db->query('SELECT 1 FROM '.$this->table.' LIMIT 1');
        } catch (\PDOException $e) {
            if ($e->getCode() !== '42S02') {
                throw $e;
            }

            $ddl = "CREATE TABLE IF NOT EXISTS produtos_lancamentos (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT NOT NULL,
  preco DECIMAL(12,2) DEFAULT 0,
  fornecedor VARCHAR(120),
  data_entrega DATE,
  lote VARCHAR(60),
  data_fabricacao DATE,
  data_vencimento DATE,
  usuario_id INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (produto_id) REFERENCES produtos(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            $this->db->exec($ddl);
            $this->checked = true;
            return;
        }

        $this->checked = true;
    }

    public function criar(array $payload): int
    {
        $this->ensureTableExists();
        return $this->create($payload);
    }

    public function listarRecentes(int $limit = 50): array
    {
        $this->ensureTableExists();

        $stmt = $this->db->prepare(
            'SELECT pl.*, p.nome '
            .'FROM '.$this->table.' pl '
            .'JOIN produtos p ON p.id = pl.produto_id '
            .'ORDER BY pl.created_at DESC, pl.id DESC '
            .'LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function ultimosPorProduto(array $produtoIds): array
    {
        $this->ensureTableExists();

        $produtoIds = array_values(array_unique(array_filter(array_map('intval', $produtoIds))));
        if (!$produtoIds) {
            return [];
        }

        $placeholders = [];
        $params = [];
        foreach ($produtoIds as $idx => $id) {
            $key = 'p'.$idx;
            $placeholders[] = ':'.$key;
            $params[$key] = $id;
        }

        $sql = 'SELECT pl.* '
            .'FROM '.$this->table.' pl '
            .'JOIN ('
                .'SELECT produto_id, MAX(id) AS max_id '
                .'FROM '.$this->table.' '
                .'WHERE produto_id IN ('.implode(',', $placeholders).') '
                .'GROUP BY produto_id'
            .') latest ON latest.max_id = pl.id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $map = [];
        foreach ($rows as $row) {
            $map[(int)($row['produto_id'] ?? 0)] = $row;
        }

        return $map;
    }

    public function listarPorProdutos(array $produtoIds): array
    {
        $this->ensureTableExists();

        $produtoIds = array_values(array_unique(array_filter(array_map('intval', $produtoIds))));
        if (!$produtoIds) {
            return [];
        }

        $placeholders = [];
        $params = [];
        foreach ($produtoIds as $idx => $id) {
            $key = 'p'.$idx;
            $placeholders[] = ':'.$key;
            $params[$key] = $id;
        }

        $stmt = $this->db->prepare(
            'SELECT pl.*, p.nome '
            .'FROM '.$this->table.' pl '
            .'JOIN produtos p ON p.id = pl.produto_id '
            .'WHERE pl.produto_id IN ('.implode(',', $placeholders).') '
            .'ORDER BY pl.created_at DESC, pl.id DESC'
        );
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
