<?php
namespace App\Repositories;

class PedidosRepository extends BaseRepository
{
    protected string $table = 'pedidos';
    private bool $formaPagamentoChecked = false;
    private bool $freteValorChecked = false;
    private bool $descontoChecked = false;

    public function find(int|string $id): ?array
    {
        $this->ensurePedidoExtraColumns();
        return parent::find($id);
    }

    public function create(array $data): int
    {
        $this->ensurePedidoExtraColumns();
        return parent::create($data);
    }

    public function update(int|string $id, array $data): bool
    {
        $this->ensurePedidoExtraColumns();
        return parent::update($id, $data);
    }

    public function all(array $criteria = []): array
    {
        $this->ensurePedidoExtraColumns();
        if ($criteria) {
            return parent::all($criteria);
        }

        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC, id DESC");
        return $stmt->fetchAll();
    }

    public function findByCodigo(string $codigo): ?array
    {
        $this->ensurePedidoExtraColumns();
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE codigo = :codigo");
        $stmt->execute(['codigo' => $codigo]);
        $pedido = $stmt->fetch();
        return $pedido ?: null;
    }

    public function listarPorCliente(int $clienteId): array
    {
        $this->ensurePedidoExtraColumns();
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE cliente_id = :cliente ORDER BY created_at DESC, id DESC");
        $stmt->execute(['cliente' => $clienteId]);
        return $stmt->fetchAll();
    }

    public function findWithCliente(int $id): ?array
    {
        $this->ensurePedidoExtraColumns();
        $sql = "SELECT p.*, c.nome_fantasia AS cliente_nome_fantasia, c.razao_social AS cliente_razao_social, c.cnpj AS cliente_cnpj,
                       c.cpf AS cliente_cpf, c.email AS cliente_email, c.telefone AS cliente_telefone,
                       c.cep AS cliente_cep, c.endereco AS cliente_endereco, c.numero AS cliente_numero,
                       c.bairro AS cliente_bairro, c.cidade AS cliente_cidade, c.estado AS cliente_estado
                FROM {$this->table} p
                LEFT JOIN clientes c ON c.id = p.cliente_id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $pedido = $stmt->fetch();
        return $pedido ?: null;
    }

    private function ensurePedidoExtraColumns(): void
    {
        $this->ensureFormaPagamentoColumn();
        $this->ensureFreteValorColumn();
        $this->ensureDescontoColumn();
    }

    private function ensureFormaPagamentoColumn(): void
    {
        if ($this->formaPagamentoChecked) {
            return;
        }

        try {
            $this->db->query("SELECT forma_pagto FROM {$this->table} LIMIT 1");
        } catch (\PDOException $exception) {
            if ($exception->getCode() !== '42S22') {
                throw $exception;
            }
            $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN forma_pagto VARCHAR(40) DEFAULT 'PIX' AFTER observacoes");
        }

        $this->formaPagamentoChecked = true;
    }

    private function ensureFreteValorColumn(): void
    {
        if ($this->freteValorChecked) {
            return;
        }

        try {
            $this->db->query("SELECT frete_valor FROM {$this->table} LIMIT 1");
        } catch (\PDOException $exception) {
            if ($exception->getCode() !== '42S22') {
                throw $exception;
            }
            $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN frete_valor DECIMAL(14,2) DEFAULT 0 AFTER total");
        }

        $this->freteValorChecked = true;
    }

    private function ensureDescontoColumn(): void
    {
        if ($this->descontoChecked) {
            return;
        }

        try {
            $this->db->query("SELECT desconto FROM {$this->table} LIMIT 1");
        } catch (\PDOException $exception) {
            if ($exception->getCode() !== '42S22') {
                throw $exception;
            }
            $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN desconto DECIMAL(14,2) DEFAULT 0 AFTER frete_valor");
        }

        $this->descontoChecked = true;
    }
}
