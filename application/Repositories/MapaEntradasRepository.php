<?php
namespace App\Repositories;

class MapaEntradasRepository extends BaseRepository
{
    protected string $table = 'mapa_entradas';
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

            $ddl = "CREATE TABLE IF NOT EXISTS mapa_entradas (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT NOT NULL,
  data_referencia DATE NOT NULL,
  tipo ENUM('planejado','estoque') NOT NULL DEFAULT 'planejado',
  quantidade DECIMAL(14,4) NOT NULL DEFAULT 0,
  observacao VARCHAR(180),
  usuario_id INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_produto_dia_tipo (produto_id, data_referencia, tipo),
  FOREIGN KEY (produto_id) REFERENCES produtos(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            $this->db->exec($ddl);
            $this->checked = true;
            return;
        }

        // Ensure legacy installs gain the new columns/indexes
        $this->ensureTipoColumn();
        $this->ensureUniqueIndex();
        $this->checked = true;
    }

    private function ensureTipoColumn(): void
    {
        $stmt = $this->db->prepare('SHOW COLUMNS FROM '.$this->table.' LIKE "tipo"');
        $stmt->execute();
        $hasTipo = (bool)$stmt->fetch();

        if (!$hasTipo) {
            $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN tipo ENUM('planejado','estoque') NOT NULL DEFAULT 'planejado' AFTER data_referencia");
        }
    }

    private function ensureUniqueIndex(): void
    {
        $expected = 'produto_id,data_referencia,tipo';

        // Detect all indexes related to produto/data/tipo
        $stmt = $this->db->prepare(
            "SELECT index_name, GROUP_CONCAT(column_name ORDER BY seq_in_index SEPARATOR ',') AS cols "
            ."FROM information_schema.statistics "
            ."WHERE table_schema = DATABASE() AND table_name = :table "
            ."AND index_name IN ('uniq_produto_dia_tipo','uniq_produto_dia') "
            .'GROUP BY index_name'
        );
        $stmt->execute(['table' => $this->table]);
        $indexes = $stmt->fetchAll();

        $hasExpected = false;
        foreach ($indexes as $idx) {
            $cols = strtolower((string)($idx['cols'] ?? ''));
            if ($cols === $expected && strtolower((string)$idx['index_name']) === 'uniq_produto_dia_tipo') {
                $hasExpected = true;
                continue;
            }

            // Drop any legacy/incorrect index shapes
            try {
                $this->db->exec('ALTER TABLE '.$this->table.' DROP INDEX '.$idx['index_name']);
            } catch (\Throwable $e) {
                // ignore if missing
            }
        }

        if (!$hasExpected) {
            // Remove stray legacy index if it still exists under a different name
            try {
                $this->db->exec('ALTER TABLE '.$this->table.' DROP INDEX uniq_produto_dia');
            } catch (\Throwable $e) {
                // ignore
            }

            $this->db->exec('ALTER TABLE '.$this->table.' ADD UNIQUE KEY uniq_produto_dia_tipo (produto_id, data_referencia, tipo)');
        }
    }

    public function porJanela(string $inicio, string $fim, ?string $tipo = null): array
    {
        $this->ensureTableExists();

        $whereTipo = '';
        $params = ['inicio' => $inicio, 'fim' => $fim];
        if ($tipo) {
            $whereTipo = ' AND tipo = :tipo';
            $params['tipo'] = $tipo;
        }

        $stmt = $this->db->prepare(
            'SELECT produto_id, data_referencia, tipo, quantidade '
            .'FROM mapa_entradas '
            .'WHERE data_referencia BETWEEN :inicio AND :fim'.$whereTipo
        );
        $stmt->execute($params);
        $mapa = [];
        foreach ($stmt->fetchAll() as $row) {
            $prodId = (int)($row['produto_id'] ?? 0);
            $dia = $row['data_referencia'];
            $tipoRow = strtolower((string)($row['tipo'] ?? 'planejado'));
            $mapa[$tipoRow][$prodId][$dia] = (float)($row['quantidade'] ?? 0);
        }
        return $mapa;
    }

    public function limitesDatas(): array
    {
        $this->ensureTableExists();

        $stmt = $this->db->prepare('SELECT MIN(data_referencia) as min_data, MAX(data_referencia) as max_data FROM '.$this->table);
        $stmt->execute();
        $row = $stmt->fetch() ?: [];

        $min = $row['min_data'] ?? null;
        $max = $row['max_data'] ?? null;

        return [$min ?: null, $max ?: null];
    }

    public function planejadoFuturo(): array
    {
        $this->ensureTableExists();

        $stmt = $this->db->prepare(
            'SELECT produto_id, SUM(quantidade) as total '
            .'FROM mapa_entradas '
            ."WHERE data_referencia >= :hoje AND tipo = 'planejado' "
            .'GROUP BY produto_id'
        );
        $stmt->execute(['hoje' => date('Y-m-d')]);

        $planejado = [];
        foreach ($stmt->fetchAll() as $row) {
            $planejado[(int)($row['produto_id'] ?? 0)] = (float)($row['total'] ?? 0);
        }

        return $planejado;
    }

    public function planejadoDetalhado(string $inicio, string $fim): array
    {
        $this->ensureTableExists();

        $stmt = $this->db->prepare(
            'SELECT me.produto_id, me.data_referencia, me.quantidade, '
            .'p.sku, p.nome, p.unidade '
            .'FROM '.$this->table.' me '
            .'JOIN produtos p ON p.id = me.produto_id '
            ."WHERE me.tipo = 'planejado' AND me.data_referencia BETWEEN :inicio AND :fim "
            .'ORDER BY p.nome ASC, me.data_referencia ASC'
        );

        $stmt->execute([
            'inicio' => $inicio,
            'fim' => $fim,
        ]);

        return $stmt->fetchAll();
    }

    public function gravarEntradaManual(array $payload): int
    {
        $this->ensureTableExists();
        $this->ensureTipoColumn();
        $this->ensureUniqueIndex();

        $tipo = strtolower(trim((string)($payload['tipo'] ?? 'planejado')));
        $tipo = in_array($tipo, ['planejado', 'estoque'], true) ? $tipo : 'planejado';

        $quantidadeRaw = $payload['quantidade'] ?? null;
        if ($quantidadeRaw === null || $quantidadeRaw === '') {
            throw new \InvalidArgumentException('Informe a quantidade para registrar.');
        }

        $data = [
            'produto_id' => (int)($payload['produto_id'] ?? 0),
            'data_referencia' => $payload['data_referencia'] ?? date('Y-m-d'),
            'tipo' => $tipo,
            'quantidade' => (float)$quantidadeRaw,
            'observacao' => trim((string)($payload['observacao'] ?? '')),
            'usuario_id' => (int)($payload['usuario_id'] ?? 0),
        ];

        $existsStmt = $this->db->prepare(
            'SELECT id FROM '.$this->table.' WHERE produto_id = :produto_id AND data_referencia = :data_referencia AND tipo = :tipo'
        );
        $existsStmt->execute([
            'produto_id' => $data['produto_id'],
            'data_referencia' => $data['data_referencia'],
            'tipo' => $data['tipo'],
        ]);
        $existingId = (int)($existsStmt->fetchColumn() ?: 0);

        if ($existingId > 0) {
            $update = $this->db->prepare(
                'UPDATE '.$this->table.' SET quantidade = :quantidade, observacao = :observacao, usuario_id = :usuario_id '
                .'WHERE id = :id'
            );
            $update->execute([
                'quantidade' => $data['quantidade'],
                'observacao' => $data['observacao'],
                'usuario_id' => $data['usuario_id'],
                'id' => $existingId,
            ]);

            return $existingId;
        }

        $insert = $this->db->prepare(
            'INSERT INTO '.$this->table.' (produto_id, data_referencia, tipo, quantidade, observacao, usuario_id) '
            .'VALUES (:produto_id, :data_referencia, :tipo, :quantidade, :observacao, :usuario_id)'
        );
        $insert->execute($data);

        return (int)$this->db->lastInsertId();
    }
}
