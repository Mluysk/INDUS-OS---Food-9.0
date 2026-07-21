<?php
namespace App\Repositories;

class PedidosRepository extends BaseRepository
{
    protected string $table = 'pedidos';
    private bool $formaPagamentoChecked = false;
    private bool $freteValorChecked = false;
    private bool $descontoChecked = false;
    private bool $cobrancaDiferencaChecked = false;
    private bool $statusSeparandoChecked = false;

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

    public function relatorioSaidaPorDia(string $data, array $lojas = []): array
    {
        $this->ensurePedidoExtraColumns();
        $lojas = array_values(array_filter(array_map('intval', $lojas), static fn (int $id): bool => $id > 0));
        $params = ['data' => $data];
        $lojaWhere = '';
        if ($lojas) {
            $placeholders = [];
            foreach ($lojas as $idx => $lojaId) {
                $key = 'loja' . $idx;
                $placeholders[] = ':' . $key;
                $params[$key] = $lojaId;
            }
            $lojaWhere = ' AND ped.cliente_id IN (' . implode(', ', $placeholders) . ')';
        }

        $stmt = $this->db->prepare("
            SELECT
                pi.produto_id,
                p.sku,
                p.nome,
                p.unidade,
                p.peso_liquido,
                COUNT(DISTINCT ped.id) AS pedidos,
                COALESCE(SUM(pi.quantidade), 0) AS quantidade,
                COALESCE(SUM(pi.quantidade * pi.preco_unit), 0) AS valor_total,
                COALESCE(SUM(pi.quantidade * COALESCE(p.peso_liquido, 0)), 0) AS peso_total
            FROM pedido_itens pi
            INNER JOIN pedidos ped ON ped.id = pi.pedido_id
            INNER JOIN produtos p ON p.id = pi.produto_id
            WHERE DATE(ped.created_at) = :data
              AND COALESCE(ped.status, '') <> 'cancelado'
              {$lojaWhere}
            GROUP BY pi.produto_id, p.sku, p.nome, p.unidade, p.peso_liquido
            ORDER BY p.nome ASC, p.sku ASC
        ");
        $stmt->execute($params);
        $itens = $stmt->fetchAll();

        $resumo = $this->db->prepare("
            SELECT
                COUNT(DISTINCT ped.id) AS pedidos,
                COUNT(DISTINCT CASE WHEN ped.codigo LIKE 'P-%' THEN ped.id END) AS pedidos_portal,
                COUNT(DISTINCT CASE WHEN ped.codigo LIKE 'P-%' THEN NULL ELSE ped.id END) AS pedidos_internos,
                COALESCE(SUM(pi.quantidade), 0) AS quantidade,
                COALESCE(SUM(pi.quantidade * pi.preco_unit), 0) AS valor_total,
                COALESCE(SUM(pi.quantidade * COALESCE(p.peso_liquido, 0)), 0) AS peso_total
            FROM pedidos ped
            INNER JOIN pedido_itens pi ON pi.pedido_id = ped.id
            INNER JOIN produtos p ON p.id = pi.produto_id
            WHERE DATE(ped.created_at) = :data
              AND COALESCE(ped.status, '') <> 'cancelado'
              {$lojaWhere}
        ");
        $resumo->execute($params);

        return [
            'itens' => $itens,
            'resumo' => $resumo->fetch() ?: [],
        ];
    }

    public function lojasComSaidaPorDia(string $data): array
    {
        $this->ensurePedidoExtraColumns();
        $stmt = $this->db->prepare("
            SELECT DISTINCT
                c.id,
                c.nome_fantasia,
                c.razao_social,
                c.cnpj,
                c.cpf
            FROM pedidos ped
            INNER JOIN pedido_itens pi ON pi.pedido_id = ped.id
            INNER JOIN clientes c ON c.id = ped.cliente_id
            WHERE DATE(ped.created_at) = :data
              AND COALESCE(ped.status, '') <> 'cancelado'
            ORDER BY COALESCE(NULLIF(c.nome_fantasia, ''), c.razao_social, c.id) ASC
        ");
        $stmt->execute(['data' => $data]);
        return $stmt->fetchAll();
    }

    private function ensurePedidoExtraColumns(): void
    {
        $this->ensureStatusSeparando();
        $this->ensureFormaPagamentoColumn();
        $this->ensureFreteValorColumn();
        $this->ensureDescontoColumn();
        $this->ensureCobrancaDiferencaColumns();
    }

    private function ensureStatusSeparando(): void
    {
        if ($this->statusSeparandoChecked) {
            return;
        }

        $column = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'status'")->fetch();
        $type = strtolower((string)($column['Type'] ?? $column['type'] ?? ''));
        if ($type !== '' && (!str_contains($type, "'separando'") || !str_contains($type, "'revisao'") || !str_contains($type, "'entregue'"))) {
            $this->db->exec("ALTER TABLE {$this->table} MODIFY COLUMN status ENUM('novo','aprovacao','producao','separando','separacao','revisao','expedicao','faturado','entregue','cancelado') NOT NULL DEFAULT 'novo'");
        }

        $this->statusSeparandoChecked = true;
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

        try {
            $this->db->query("SELECT desconto_tipo FROM {$this->table} LIMIT 1");
        } catch (\PDOException $exception) {
            if ($exception->getCode() !== '42S22') {
                throw $exception;
            }
            $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN desconto_tipo VARCHAR(20) NOT NULL DEFAULT 'valor' AFTER desconto");
        }

        try {
            $this->db->query("SELECT desconto_valor_original FROM {$this->table} LIMIT 1");
        } catch (\PDOException $exception) {
            if ($exception->getCode() !== '42S22') {
                throw $exception;
            }
            $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN desconto_valor_original DECIMAL(14,4) NOT NULL DEFAULT 0 AFTER desconto_tipo");
        }

        $this->descontoChecked = true;
    }

    private function ensureCobrancaDiferencaColumns(): void
    {
        if ($this->cobrancaDiferencaChecked) {
            return;
        }

        try {
            $this->db->query("SELECT cobranca_diferenca_valor FROM {$this->table} LIMIT 1");
        } catch (\PDOException $exception) {
            if ($exception->getCode() !== '42S22') {
                throw $exception;
            }
            $this->db->exec("ALTER TABLE {$this->table}
                ADD COLUMN cobranca_diferenca_valor DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER desconto,
                ADD COLUMN cobranca_diferenca_descricao VARCHAR(160) NULL AFTER cobranca_diferenca_valor");
        }

        $this->cobrancaDiferencaChecked = true;
    }
}
