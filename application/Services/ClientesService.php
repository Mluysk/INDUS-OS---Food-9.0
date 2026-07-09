<?php
namespace App\Services;

use App\Repositories\ClientesRepository;

class ClientesService
{
    private ClientesRepository $clientes;
    private AuditService $audit;
    private ?array $clientesColumns = null;

    public function __construct(ClientesRepository $clientes, AuditService $audit)
    {
        $this->clientes = $clientes;
        $this->audit = $audit;
    }

    public function create(array $data, int $usuarioId): int
    {
        $payload = $this->preparePayload($data, false);
        $this->assertUniqueFields($payload, null);
        $id = $this->clientes->create($payload);
        $this->audit->log($usuarioId, 'create', 'clientes', $id, $payload);
        return $id;
    }

    public function update(int $id, array $data, int $usuarioId): bool
    {
        $atual = $this->clientes->find($id);
        if (!$atual) {
            throw new \InvalidArgumentException('Cliente não encontrado.');
        }

        $payload = $this->preparePayload($data, true);

        if (!$payload) {
            throw new \InvalidArgumentException('Nenhuma alteração informada.');
        }

        $this->assertUniqueFields($payload, $id);

        try {
            $result = $this->clientes->update($id, $payload);
        } catch (\PDOException $exception) {
            if ((string)$exception->getCode() === '23000') {
                throw new \InvalidArgumentException('Já existe outro cliente com este CNPJ, CPF ou e-mail.');
            }
            throw $exception;
        }

        $this->audit->log($usuarioId, 'update', 'clientes', $id, $this->audit->withChanges($atual, $payload));
        return $result;
    }

    public function all(): array
    {
        return $this->clientes->all();
    }

    public function find(int $id): ?array
    {
        return $this->clientes->find($id);
    }

    public function delete(int $id, int $usuarioId): bool
    {
        $cliente = $this->clientes->find($id);
        if (!$cliente) {
            throw new \InvalidArgumentException('Cliente não encontrado.');
        }

        $db = $this->clientes->getConnection();
        $db->beginTransaction();

        try {
            $this->deleteClientDependencies($db, $id);
            $result = $this->clientes->delete($id);

            $usuarioVinculado = (int)($cliente['usuario_id'] ?? 0);
            if ($usuarioVinculado > 0) {
                $this->deleteIfPossible($db, 'usuarios', 'id = :id', ['id' => $usuarioVinculado]);
            }

            $db->commit();
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw new \InvalidArgumentException('Não foi possível excluir o cliente agora.');
        }

        $this->audit->log($usuarioId, 'delete', 'clientes', $id, $cliente);
        return $result;
    }

    private function preparePayload(array $data, bool $isUpdate): array
    {
        $documentType = $data['documento_tipo'] ?? null;
        unset($data['documento_tipo']);

        $cnpj = $this->cleanDocument($data['cnpj'] ?? null);
        $cpf = $this->cleanDocument($data['cpf'] ?? null);
        $documento = $this->cleanDocument($data['documento'] ?? null);
        unset($data['documento']);

        if ($documento !== null) {
            if ($documentType === 'cpf') {
                $cpf = $documento;
                $cnpj = null;
            } else {
                $cnpj = $documento;
                $cpf = null;
            }
        }

        if ($documentType === 'cpf') {
            $cnpj = null;
            if ($cpf === null && !$isUpdate) {
                throw new \InvalidArgumentException('Informe um CPF.');
            }
        } else {
            $cpf = null;
            if ($cnpj === null && !$isUpdate) {
                throw new \InvalidArgumentException('Informe um CNPJ.');
            }
        }

        if ($cpf !== null && !valida_cpf($cpf)) {
            throw new \InvalidArgumentException('CPF inválido');
        }

        if ($cnpj !== null && !valida_cnpj($cnpj)) {
            throw new \InvalidArgumentException('CNPJ inválido');
        }

        $payload = [];
        $allowed = [
            'razao_social',
            'nome_fantasia',
            'ie',
            'email',
            'telefone',
            'contato_tipo',
            'whatsapp',
            'cep',
            'endereco',
            'numero',
            'bairro',
            'cidade',
            'estado',
        ];
        $allowed = array_values(array_filter($allowed, fn (string $field): bool => $this->hasClienteColumn($field)));

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $this->cleanString($data[$field]);
            }
        }

        if (!$isUpdate) {
            $requiredOnCreate = [
                'razao_social' => 'Nome / Razão social',
                'nome_fantasia' => 'Nome fantasia',
                'email' => 'E-mail',
                'telefone' => 'Contato',
                'contato_tipo' => 'Tipo do contato',
                'whatsapp' => 'WhatsApp',
                'cep' => 'CEP',
                'endereco' => 'Endereço',
                'numero' => 'Número',
                'bairro' => 'Bairro',
                'cidade' => 'Cidade',
                'estado' => 'Estado (UF)',
            ];
            $requiredOnCreate = array_filter(
                $requiredOnCreate,
                fn (string $label, string $field): bool => $this->hasClienteColumn($field),
                ARRAY_FILTER_USE_BOTH
            );

            $missing = [];
            foreach ($requiredOnCreate as $field => $label) {
                if (!array_key_exists($field, $payload) || $payload[$field] === null || $payload[$field] === '') {
                    $missing[] = $label;
                }
            }

            if ($missing) {
                throw new \InvalidArgumentException('Preencha todos os campos obrigatórios: ' . implode(', ', $missing) . '.');
            }
        }

        if (array_key_exists('cep', $payload) && $payload['cep'] !== null) {
            $cepDigits = preg_replace('/\D+/', '', $payload['cep']);
            if ($cepDigits === '') {
                $payload['cep'] = null;
            } elseif (strlen($cepDigits) === 8) {
                $payload['cep'] = substr($cepDigits, 0, 5) . '-' . substr($cepDigits, 5);
            } else {
                throw new \InvalidArgumentException('Informe um CEP com 8 dígitos.');
            }
        }

        if (array_key_exists('estado', $payload) && $payload['estado'] !== null) {
            $payload['estado'] = strtoupper($payload['estado']);
            if ($payload['estado'] !== null && $payload['estado'] !== '' && strlen($payload['estado']) !== 2) {
                throw new \InvalidArgumentException('Informe o estado (UF) com 2 letras.');
            }
        }

        if ($this->hasClienteColumn('contato_tipo') && array_key_exists('contato_tipo', $payload) && $payload['contato_tipo'] !== null) {
            $payload['contato_tipo'] = strtolower($payload['contato_tipo']);
            if (!in_array($payload['contato_tipo'], ['celular', 'telefone'], true)) {
                throw new \InvalidArgumentException('Selecione um tipo de contato válido (celular ou telefone).');
            }
        }

        if ($this->hasClienteColumn('whatsapp')) {
            $whatsappValue = $payload['whatsapp'] ?? null;
            if ($whatsappValue === null || trim((string) $whatsappValue) === '') {
                if (!$isUpdate || array_key_exists('whatsapp', $data)) {
                    throw new \InvalidArgumentException('Informe o WhatsApp do cliente.');
                }
            }
        }

        if ($cnpj !== null || ($isUpdate && $documentType === 'cnpj')) {
            $payload['cnpj'] = $cnpj;
        } elseif (!$isUpdate && !array_key_exists('cnpj', $payload)) {
            $payload['cnpj'] = null;
        }

        if ($cpf !== null || ($isUpdate && $documentType === 'cpf')) {
            $payload['cpf'] = $cpf;
        } elseif (!$isUpdate && !array_key_exists('cpf', $payload)) {
            $payload['cpf'] = null;
        }

        if ($isUpdate && $documentType === 'cnpj') {
            $payload['cpf'] = null;
        }
        if ($isUpdate && $documentType === 'cpf') {
            $payload['cnpj'] = null;
        }

        return $payload;
    }

    private function assertUniqueFields(array $payload, ?int $ignoreId): void
    {
        if (!empty($payload['email'])) {
            $cliente = $ignoreId === null
                ? $this->clientes->findByEmail((string)$payload['email'])
                : $this->clientes->findByEmailExcept((string)$payload['email'], $ignoreId);
            if ($cliente) {
                throw new \InvalidArgumentException('Já existe outro cliente com este e-mail.');
            }
        }

        foreach (['cnpj', 'cpf'] as $field) {
            if (empty($payload[$field])) {
                continue;
            }
            $cliente = $ignoreId === null
                ? $this->clientes->findByDocumento((string)$payload[$field])
                : $this->clientes->findByDocumentoExcept((string)$payload[$field], $ignoreId);
            if ($cliente) {
                $label = strtoupper($field);
                throw new \InvalidArgumentException("Já existe outro cliente com este {$label}.");
            }
        }
    }

    private function deleteClientDependencies(\PDO $db, int $clienteId): void
    {
        $pedidoIds = [];
        try {
            $stmt = $db->prepare('SELECT id FROM pedidos WHERE cliente_id = :cliente');
            $stmt->execute(['cliente' => $clienteId]);
            $pedidoIds = array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: []);
        } catch (\PDOException $exception) {
            $this->ignoreMissingTableOrColumn($exception);
        }

        foreach ($pedidoIds as $pedidoId) {
            $this->deleteIfPossible($db, 'ordens_producao', 'pedido_id = :pedido', ['pedido' => $pedidoId]);
            $this->deleteIfPossible($db, 'movimentos_estoque', 'ref_tipo = :tipo AND ref_id = :pedido', [
                'tipo' => 'pedido',
                'pedido' => $pedidoId,
            ]);
            $this->deleteIfPossible($db, 'pedido_itens', 'pedido_id = :pedido', ['pedido' => $pedidoId]);
            $this->deleteIfPossible($db, 'pedidos', 'id = :pedido', ['pedido' => $pedidoId]);
        }
    }

    private function deleteIfPossible(\PDO $db, string $table, string $where, array $params): void
    {
        try {
            $stmt = $db->prepare("DELETE FROM {$table} WHERE {$where}");
            $stmt->execute($params);
        } catch (\PDOException $exception) {
            $this->ignoreMissingTableOrColumn($exception);
        }
    }

    private function ignoreMissingTableOrColumn(\PDOException $exception): void
    {
        if (!in_array((string)$exception->getCode(), ['42S02', '42S22'], true)) {
            throw $exception;
        }
    }

    private function hasClienteColumn(string $column): bool
    {
        return in_array($column, $this->getClienteColumns(), true);
    }

    private function getClienteColumns(): array
    {
        if ($this->clientesColumns !== null) {
            return $this->clientesColumns;
        }

        try {
            $stmt = $this->clientes->getConnection()->query('SHOW COLUMNS FROM clientes');
            $columns = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
            $this->clientesColumns = array_values(array_filter(array_map(
                static fn (array $row): string => (string)($row['Field'] ?? ''),
                $columns
            )));
        } catch (\Throwable $e) {
            $this->clientesColumns = [
                'id',
                'usuario_id',
                'razao_social',
                'nome_fantasia',
                'cnpj',
                'cpf',
                'ie',
                'im',
                'email',
                'telefone',
                'contato_tipo',
                'whatsapp',
                'cep',
                'endereco',
                'numero',
                'bairro',
                'cidade',
                'estado',
                'created_at',
            ];
        }

        return $this->clientesColumns;
    }

    private function cleanString($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            return $trimmed === '' ? null : $trimmed;
        }

        return $value;
    }

    private function cleanDocument($value): ?string
    {
        $clean = $this->cleanString($value);
        if ($clean === null) {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $clean);
        return $digits === '' ? null : $digits;
    }
}
