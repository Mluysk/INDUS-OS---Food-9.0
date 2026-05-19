<?php
namespace App\Repositories;

class FinanceiroLancamentosRepository extends BaseRepository
{
    protected string $table = 'financeiro_lancamentos';

    public function porTipo(string $tipo): array
    {
        $this->garantirEstruturaTabela();
        return $this->buscarPorTipo($tipo);
    }

    public function create(array $data): int
    {
        $this->garantirEstruturaTabela();
        return parent::create($data);
    }

    public function find(int|string $id): ?array
    {
        $this->garantirEstruturaTabela();
        return parent::find($id);
    }

    public function marcarPago(int $id): bool
    {
        $this->garantirEstruturaTabela();
        return $this->update($id, ['status' => 'pago']);
    }

    private function buscarPorTipo(string $tipo): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE tipo = :tipo ORDER BY created_at DESC, id DESC");
        $stmt->execute(['tipo' => $tipo]);
        return $stmt->fetchAll();
    }

    private function garantirEstruturaTabela(): void
    {
        $this->criarTabelaSeNaoExistir();

        if (!$this->colunaExiste('status')) {
            $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'aberto' AFTER data_entrada");
        }
    }

    private function criarTabelaSeNaoExistir(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->table} (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            tipo ENUM('entrada','saida') NOT NULL,
            numero_titulo VARCHAR(60) NOT NULL,
            nome_empresa VARCHAR(180) NOT NULL,
            validade DATE NULL,
            valor DECIMAL(14,2) NULL,
            data_lancamento DATE NOT NULL,
            data_entrada DATE NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'aberto',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_financeiro_lancamentos_tipo (tipo),
            INDEX idx_financeiro_lancamentos_numero (numero_titulo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    private function colunaExiste(string $coluna): bool
    {
        $stmt = $this->db->prepare("SHOW COLUMNS FROM {$this->table} LIKE :coluna");
        $stmt->execute(['coluna' => $coluna]);
        return (bool)$stmt->fetch();
    }
}
