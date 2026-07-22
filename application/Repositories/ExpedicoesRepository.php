<?php
namespace App\Repositories;

class ExpedicoesRepository extends BaseRepository
{
    protected string $table = 'expedicoes';
    private bool $assinaturasChecked = false;

    public function find(int|string $id): ?array
    {
        $this->ensureAssinaturas();
        return parent::find($id);
    }

    public function all(array $criteria = []): array
    {
        $this->ensureAssinaturas();
        return parent::all($criteria);
    }

    public function create(array $data): int
    {
        $this->ensureAssinaturas();
        return parent::create($data);
    }

    public function maiorSequencialRomaneio(): int
    {
        $this->ensureAssinaturas();
        $stmt = $this->db->query("SELECT COALESCE(MAX(CAST(SUBSTRING(romaneio, 5) AS UNSIGNED)), 0) FROM {$this->table} WHERE romaneio REGEXP '^ROM-[0-9]+$'");
        return (int)$stmt->fetchColumn();
    }

    private function ensureAssinaturas(): void
    {
        if ($this->assinaturasChecked) return;
        $columns = $this->db->query("SHOW COLUMNS FROM {$this->table}")->fetchAll();
        $names = array_map(static fn(array $column): string => strtolower((string)($column['Field'] ?? $column['field'] ?? '')), $columns);
        $adds = [];
        if (!in_array('motorista_nome', $names, true)) $adds[] = 'ADD COLUMN motorista_nome VARCHAR(120) NULL';
        if (!in_array('assinatura_motorista', $names, true)) $adds[] = 'ADD COLUMN assinatura_motorista LONGTEXT NULL';
        if (!in_array('responsavel_expedicao_nome', $names, true)) $adds[] = 'ADD COLUMN responsavel_expedicao_nome VARCHAR(120) NULL';
        if (!in_array('assinatura_responsavel', $names, true)) $adds[] = 'ADD COLUMN assinatura_responsavel LONGTEXT NULL';
        if (!in_array('recebedor_nome', $names, true)) $adds[] = 'ADD COLUMN recebedor_nome VARCHAR(120) NULL';
        if (!in_array('assinatura_recebedor', $names, true)) $adds[] = 'ADD COLUMN assinatura_recebedor LONGTEXT NULL';
        if (!in_array('data_recebimento', $names, true)) $adds[] = 'ADD COLUMN data_recebimento DATETIME NULL';
        if (!in_array('data_confirmacao_cliente', $names, true)) $adds[] = 'ADD COLUMN data_confirmacao_cliente DATETIME NULL';
        if (!in_array('observacao_recebimento', $names, true)) $adds[] = 'ADD COLUMN observacao_recebimento VARCHAR(500) NULL';
        if (!in_array('tipo_entrega', $names, true)) $adds[] = "ADD COLUMN tipo_entrega VARCHAR(20) NOT NULL DEFAULT 'propria'";
        if ($adds) $this->db->exec("ALTER TABLE {$this->table} " . implode(', ', $adds));
        $this->assinaturasChecked = true;
    }
}
