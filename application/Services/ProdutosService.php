<?php
namespace App\Services;

use App\Repositories\ProdutosRepository;
use App\Repositories\ProdutoComponentesRepository;
use App\Services\ConfigService;

class ProdutosService
{
    private ProdutosRepository $produtos;
    private ProdutoComponentesRepository $componentes;
    private AuditService $audit;
    private ConfigService $config;

    public function __construct(ProdutosRepository $produtos, ProdutoComponentesRepository $componentes, AuditService $audit, ConfigService $config)
    {
        $this->produtos = $produtos;
        $this->componentes = $componentes;
        $this->audit = $audit;
        $this->config = $config;
    }

    public function create(array $data, array $componentes, int $usuarioId): int
    {
        $payload = $this->sanitizeProdutoPayload($data);

        if (!empty($payload['sku'])) {
            $existing = $this->produtos->findBySku($payload['sku']);
            if ($existing) {
                throw new \InvalidArgumentException('Já existe um produto cadastrado com este SKU.');
            }
        }

        $id = $this->produtos->create($payload);
        foreach ($componentes as $componente) {
            $componente['produto_id'] = $id;
            $this->componentes->create($componente);
        }
        $this->audit->log($usuarioId, 'create', 'produtos', $id, $payload);
        return $id;
    }

    public function update(int $id, array $data, int $usuarioId): bool
    {
        $payload = $this->sanitizeProdutoPayload($data, $id);
        if (!empty($payload['sku'])) {
            $existing = $this->produtos->findBySku($payload['sku']);
            if ($existing && (int)$existing['id'] !== $id) {
                throw new \InvalidArgumentException('Já existe um produto cadastrado com este SKU.');
            }
        }
        $updated = $this->produtos->update($id, $payload);
        $this->audit->log($usuarioId, 'update', 'produtos', $id, $payload);
        return $updated;
    }

    public function atualizarPreco(int $id, float $preco, int $usuarioId): bool
    {
        $produto = $this->produtos->find($id);
        if (!$produto) {
            throw new \InvalidArgumentException('Produto não encontrado.');
        }

        $payload = ['preco' => $preco];
        $updated = $this->produtos->update($id, $payload);
        $this->audit->log($usuarioId, 'update', 'produtos', $id, $payload);
        return $updated;
    }

    public function all(array $filters = []): array
    {
        $normalized = [
            'term' => isset($filters['term']) ? trim((string)$filters['term']) : null,
            'categoria' => $this->normalizeCategoriaFilter($filters['categoria'] ?? null),
            'tipo' => isset($filters['tipo']) ? trim((string)$filters['tipo']) : null,
            'sort' => isset($filters['sort']) ? trim((string)$filters['sort']) : null,
            'dir' => isset($filters['dir']) ? strtolower(trim((string)$filters['dir'])) : null,
            'status' => isset($filters['status']) ? strtolower(trim((string)$filters['status'])) : null,
            'include_inactive' => !empty($filters['include_inactive']),
        ];

        $normalized = array_filter($normalized, static function ($value) {
            if (is_array($value)) {
                return $value !== [];
            }
            return $value !== null && $value !== '';
        });

        return $this->produtos->search($normalized);
    }

    public function deactivate(int $id, int $usuarioId): bool
    {
        $produto = $this->produtos->find($id);
        if (!$produto) {
            throw new \InvalidArgumentException('Produto nao encontrado.');
        }

        $updated = $this->produtos->deactivate($id);
        $this->audit->log($usuarioId, 'status', 'produtos', $id, [
            'sku' => $produto['sku'] ?? null,
            'nome' => $produto['nome'] ?? null,
            'is_ativo' => 0,
            'status' => 'desativado',
        ]);
        return $updated;
    }

    public function activate(int $id, int $usuarioId): bool
    {
        $produto = $this->produtos->find($id);
        if (!$produto) {
            throw new \InvalidArgumentException('Produto nao encontrado.');
        }

        $updated = $this->produtos->activate($id);
        $this->audit->log($usuarioId, 'status', 'produtos', $id, [
            'sku' => $produto['sku'] ?? null,
            'nome' => $produto['nome'] ?? null,
            'is_ativo' => 1,
            'status' => 'ativado',
        ]);
        return $updated;
    }

    public function delete(int $id, int $usuarioId): string
    {
        $produto = $this->produtos->find($id);
        if (!$produto) {
            throw new \InvalidArgumentException('Produto nao encontrado.');
        }

        $deleted = $this->produtos->deletePermanently($id);
        if (!$deleted) {
            throw new \InvalidArgumentException('Nao foi possivel excluir permanentemente o produto.');
        }
        $this->audit->log($usuarioId, 'delete', 'produtos', $id, [
            'sku' => $produto['sku'] ?? null,
            'nome' => $produto['nome'] ?? null,
            'resultado' => 'removido_permanentemente',
        ]);
        return 'deleted';
    }

    public function categorias(): array
    {
        return $this->produtos->categorias();
    }

    public function fichaTecnica(int $id): array
    {
        $produto = $this->produtos->find($id);
        $componentes = $this->componentes->listarComponentes($id);
        return ['produto' => $produto, 'componentes' => $componentes];
    }

    public function calcularConsumoReceita(int $produtoId, float $quantidade = 1): array
    {
        if ($quantidade <= 0) {
            throw new \InvalidArgumentException('A quantidade deve ser maior que zero.');
        }

        $componentes = $this->componentes->listarComponentes($produtoId);
        $consumo = [];

        foreach ($componentes as $comp) {
            $consumo[] = [
                'componente_id' => $comp['componente_id'] ?? null,
                'sku' => $this->normalizarSku((string)($comp['componente_sku'] ?? $comp['sku'] ?? '')),
                'nome' => $comp['nome'] ?? $comp['componente_nome'] ?? null,
                'unidade' => $comp['unidade'] ?? $comp['componente_unidade'] ?? null,
                'quantidade_por_lote' => (float)($comp['quantidade'] ?? 0),
                'quantidade_total' => (float)($comp['quantidade'] ?? 0) * $quantidade,
            ];
        }

        return $consumo;
    }

    public function nextSkuForTipo(string $tipo): string
    {
        $tipoNorm = strtoupper($tipo) === 'MP' ? 'MP' : 'PA';
        $digits = $this->skuDigits();
        $prefix = $tipoNorm === 'MP' ? 'MP-' : 'A-';
        $sequence = $this->produtos->nextSequence($prefix, $digits, $this->reuseDeletedSku());

        return $prefix.$sequence;
    }

    public function getSkuDigits(): int
    {
        return $this->skuDigits();
    }

    private function normalizeCategoriaFilter(mixed $value): array|string|null
    {
        if (is_array($value)) {
            $items = $value;
        } else {
            $raw = trim((string)($value ?? ''));
            if ($raw === '') {
                return null;
            }
            $items = strpos($raw, ',') !== false ? explode(',', $raw) : [$raw];
        }

        $items = array_values(array_unique(array_filter(array_map(fn($item) => $this->normalizarCategoria($item), $items), static fn($item) => $item !== null && $item !== '')));

        if (count($items) === 0) {
            return null;
        }
        if (count($items) === 1) {
            return $items[0];
        }

        return $items;
    }

    private function sanitizeProdutoPayload(array $data, ?int $id = null): array
    {
        $tipo = strtoupper(trim($data['tipo'] ?? 'PA')) === 'MP' ? 'MP' : 'PA';
        $digits = $this->skuDigits();
        $prefix = $tipo === 'MP' ? 'MP-' : 'A-';

        $existing = $id !== null ? $this->produtos->find($id) : null;
        $skuRaw = trim((string)($data['sku'] ?? ''));
        $tipoAnterior = strtoupper(trim((string)($existing['tipo'] ?? '')));
        if ($existing && $tipoAnterior !== '' && $tipoAnterior !== $tipo) {
            $skuRaw = $this->produtos->nextSequence($prefix, $digits, $this->reuseDeletedSku());
        } elseif ($skuRaw === '' && $existing) {
            $skuRaw = $existing['sku'] ?? '';
        }
        if ($skuRaw === '') {
            $sequence = $this->produtos->nextSequence($prefix, $digits, $this->reuseDeletedSku());
            $skuRaw = $sequence;
        }

        $nome = trim((string)($data['nome'] ?? ''));
        if ($nome === '') {
            throw new \InvalidArgumentException('Informe o nome do produto.');
        }

        $payload = [
            'sku' => $this->aplicarPrefixoSku($skuRaw, $tipo, $digits, $prefix),
            'nome' => $nome,
            'tipo' => $tipo,
            'categoria' => $this->normalizarCategoria($data['categoria'] ?? null),
            'recipe_id' => isset($data['recipe_id']) && (int)$data['recipe_id'] > 0 ? (int)$data['recipe_id'] : null,
            'unidade' => trim((string)($data['unidade'] ?? '')),
            'peso_liquido' => isset($data['peso_liquido']) ? (float)$data['peso_liquido'] : null,
            'shelf_life_dias' => isset($data['shelf_life_dias']) ? (int)$data['shelf_life_dias'] : 0,
            'preco' => isset($data['preco']) ? (float)$data['preco'] : 0,
            'alergicos' => isset($data['alergicos']) ? trim((string)$data['alergicos']) : null,
        ];

        if (array_key_exists('imagem_path', $data)) {
            $imagemPath = trim((string)($data['imagem_path'] ?? ''));
            $payload['imagem_path'] = $imagemPath !== '' ? $imagemPath : null;
        }
        foreach (['imagem_pronto_path'] as $imageField) {
            if (array_key_exists($imageField, $data)) {
                $imagePath = trim((string)($data[$imageField] ?? ''));
                $payload[$imageField] = $imagePath !== '' ? $imagePath : null;
            }
        }

        if ($payload['unidade'] === '') {
            throw new \InvalidArgumentException('Informe a unidade de medida.');
        }

        if (isset($data['tabela_nutricional'])) {
            $payload['tabela_nutricional'] = $this->normalizaJson($data['tabela_nutricional']);
        }

        return $payload;
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

    private function aplicarPrefixoSku(string $sku, string $tipo, int $digits, string $prefix): string
    {
        $clean = strtoupper(preg_replace('/^(A-|MP-)/i', '', $sku));
        $clean = preg_replace('/[^A-Z0-9_-]/', '', $clean ?? '');

        if ($clean === '') {
            throw new \InvalidArgumentException('Informe o SKU do produto.');
        }

        if (preg_match('/^[0-9]+$/', $clean)) {
            $clean = str_pad((string)((int)$clean), $digits, '0', STR_PAD_LEFT);
        }

        return $prefix . $clean;
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
        $digitsRaw = $this->config->get('SKU_DIGITS', '3');
        $digits = (int)($digitsRaw ?: 3);
        if ($digits < 2) {
            $digits = 2;
        } elseif ($digits > 8) {
            $digits = 8;
        }

        return $digits;
    }

    private function reuseDeletedSku(): bool
    {
        return $this->config->get('PRODUCT_REUSE_DELETED_SKU', '0') === '1';
    }

    private function normalizaJson(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return null;
            }

            json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $trimmed;
            }

            return json_encode($trimmed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return null;
    }
}
