<?php
namespace App\Services;

use App\Repositories\RecipesRepository;
use App\Repositories\RecipeItemsRepository;
use App\Repositories\RecipeRunsRepository;
use App\Repositories\RecipeRunItemsRepository;
use App\Repositories\PedidoItensRepository;
use App\Repositories\EstoquesRepository;
use App\Repositories\MovimentosEstoqueRepository;
use App\Repositories\ProdutosRepository;

/**
 * Assumptions:
 * - Uses produtos.unidade as the default stock unit.
 * - Uses estoques.custo_medio as the primary unit cost; falls back to latest movimentos_estoque.custo_unit.
 * - Consumes stock by FEFO using estoques.validade when available.
 * - Orders are considered confirmed for recipe execution when status changes to "faturado".
 */
class RecipeService
{
    private RecipesRepository $recipes;
    private RecipeItemsRepository $items;
    private RecipeRunsRepository $runs;
    private RecipeRunItemsRepository $runItems;
    private PedidoItensRepository $pedidoItens;
    private EstoquesRepository $estoques;
    private MovimentosEstoqueRepository $movimentos;
    private ProdutosRepository $produtos;
    private ConfigService $config;
    private const RECIPE_SKU_PREFIX = 'A-';

    public function __construct(
        RecipesRepository $recipes,
        RecipeItemsRepository $items,
        RecipeRunsRepository $runs,
        RecipeRunItemsRepository $runItems,
        PedidoItensRepository $pedidoItens,
        EstoquesRepository $estoques,
        MovimentosEstoqueRepository $movimentos,
        ProdutosRepository $produtos,
        ConfigService $config
    ) {
        $this->recipes = $recipes;
        $this->items = $items;
        $this->runs = $runs;
        $this->runItems = $runItems;
        $this->pedidoItens = $pedidoItens;
        $this->estoques = $estoques;
        $this->movimentos = $movimentos;
        $this->produtos = $produtos;
        $this->config = $config;
    }

    public function listRecipes(array $filters = []): array
    {
        $normalized = [
            'term' => isset($filters['term']) ? trim((string)$filters['term']) : null,
            'active' => isset($filters['active']) && in_array((string)$filters['active'], ['0', '1'], true) ? (string)$filters['active'] : null,
        ];
        $normalized = array_filter($normalized, static fn($v) => $v !== null && $v !== '');
        return $this->recipes->search($normalized);
    }

    public function getNextSku(): string
    {
        return $this->nextRecipeSku();
    }

    public function getRecipe(int $id): ?array
    {
        $recipe = $this->recipes->find($id);
        if (!$recipe) {
            return null;
        }
        $recipe['items'] = $this->items->listByRecipe($id);
        $produtoPaId = (int)($recipe['produto_pa_id'] ?? 0);
        $produtoPa = $produtoPaId > 0 ? $this->produtos->find($produtoPaId) : null;
        $recipe['produto_pa_peso_liquido'] = $produtoPa['peso_liquido'] ?? null;
        return $recipe;
    }

    public function createRecipe(array $data): int
    {
        $items = $data['items'] ?? [];
        unset($data['items']);
        $payload = $this->sanitizeRecipePayload($data);
        $this->assertRecipeSkuAvailable($payload['sku'] ?? '');
        $id = $this->recipes->create($payload);
        $this->syncProdutoAcabado($id, $payload);
        $this->persistItems($id, $items);
        return $id;
    }

    public function updateRecipe(int $id, array $data): bool
    {
        $items = $data['items'] ?? [];
        unset($data['items']);
        $data['id'] = $id;
        $payload = $this->sanitizeRecipePayload($data, true);
        $this->assertRecipeSkuAvailable($payload['sku'] ?? '', $id);
        $updated = $this->recipes->update($id, $payload);
        $this->syncProdutoAcabado($id, $payload);
        $this->items->deleteByRecipe($id);
        $this->persistItems($id, $items);
        return $updated;
    }

    public function runRecipeManual(int $recipeId, float $producedQty, float $saleUnitPrice, int $userId): int
    {
        return $this->runRecipe($recipeId, $producedQty, $saleUnitPrice, null, 'MANUAL', null, $userId);
    }

    public function runRecipeFromMapaEntrada(int $mapaEntradaId, int $produtoId, float $producedQty, int $userId): ?int
    {
        if ($mapaEntradaId <= 0 || $produtoId <= 0 || $producedQty <= 0) {
            return null;
        }

        $existing = $this->runs->findBySource('ADJUST', $mapaEntradaId);
        if ($existing) {
            return (int)$existing['id'];
        }

        $produto = $this->produtos->find($produtoId);
        if (!$produto) {
            return null;
        }

        $recipeId = (int)($produto['recipe_id'] ?? 0);
        if ($recipeId > 0) {
            $linkedRecipe = $this->recipes->find($recipeId);
            if (!$linkedRecipe || (int)($linkedRecipe['active'] ?? 1) !== 1) {
                $recipeId = 0;
            }
        }

        if ($recipeId <= 0) {
            $activeRecipe = $this->recipes->findActiveByProdutoPaId($produtoId);
            $recipeId = $activeRecipe ? (int)($activeRecipe['id'] ?? 0) : 0;
        }

        if ($recipeId <= 0) {
            return null;
        }

        $recipe = $this->getRecipe($recipeId);
        if (!$recipe || empty($recipe['items'])) {
            throw new \InvalidArgumentException('Entrada bloqueada: a receita vinculada esta em revisao e ainda nao possui insumos cadastrados. Complete a receita antes de consumir MP automaticamente no mapa.');
        }

        return $this->runRecipe($recipeId, $producedQty, 0.0, 0.0, 'ADJUST', $mapaEntradaId, $userId);
    }

    public function runRecipeFromSale(int $orderItemId, int $userId): ?int
    {
        $existing = $this->runs->findBySource('SALE', $orderItemId);
        if ($existing) {
            return (int)$existing['id'];
        }

        $orderItem = $this->pedidoItens->findWithProduto($orderItemId);
        if (!$orderItem) {
            return null;
        }

        $recipeId = (int)($orderItem['recipe_id'] ?? 0);
        if ($recipeId <= 0) {
            return null;
        }

        $producedQty = (float)($orderItem['quantidade'] ?? 0);
        $saleUnitPrice = (float)($orderItem['preco_unit'] ?? 0);
        $discount = (float)($orderItem['desconto'] ?? 0);
        $saleTotalOverride = max(0.0, ($producedQty * $saleUnitPrice) - $discount);

        return $this->runRecipe($recipeId, $producedQty, $saleUnitPrice, $saleTotalOverride, 'SALE', $orderItemId, $userId);
    }

    public function listRuns(int $recipeId): array
    {
        return $this->runs->listByRecipe($recipeId);
    }

    public function listRunItems(int $runId): array
    {
        return $this->runItems->listByRun($runId);
    }

    public function simulateCost(int $recipeId, float $producedQty): array
    {
        $recipe = $this->recipes->find($recipeId);
        if (!$recipe) {
            throw new \InvalidArgumentException('Receita não encontrada.');
        }

        if ($producedQty <= 0) {
            throw new \InvalidArgumentException('Quantidade produzida inválida.');
        }

        $yieldQty = (float)($recipe['yield_qty'] ?? 0);
        if ($yieldQty <= 0) {
            throw new \InvalidArgumentException('Rendimento da receita inválido.');
        }

        $factor = $producedQty / $yieldQty;
        $items = $this->items->listByRecipe($recipeId);
        $summary = [];
        $total = 0.0;
        $warnings = [];

        foreach ($items as $item) {
            $qtyBase = (float)($item['qty'] ?? 0);
            $lossPercent = (float)($item['loss_percent'] ?? 0);
            $qtyUsed = $qtyBase * $factor;
            if ($lossPercent > 0) {
                $qtyUsed *= (1 + ($lossPercent / 100));
            }

            $recipeUnit = (string)($item['unit'] ?? '');
            $productUnit = (string)($item['product_unit'] ?? '');
            $displayQty = $qtyUsed;
            $displayUnit = $recipeUnit;
            $costUnit = null;
            $costTotal = null;
            $conversionError = null;

            try {
                $displayQty = convertQty($qtyUsed, $recipeUnit, $productUnit);
                $displayUnit = $productUnit;
                $costUnit = $this->getCurrentCostUnit((int)$item['stock_product_id']);
                $costTotal = $displayQty * $costUnit;
                $total += $costTotal;
            } catch (\InvalidArgumentException $e) {
                $conversionError = $e->getMessage();
                $warnings[] = sprintf(
                    '%s: unidade da receita (%s) incompativel com a unidade do produto (%s).',
                    (string)($item['nome'] ?? $item['sku'] ?? 'Ingrediente'),
                    $recipeUnit !== '' ? $recipeUnit : '-',
                    $productUnit !== '' ? $productUnit : '-'
                );
            }

            $summary[] = [
                'stock_product_id' => (int)$item['stock_product_id'],
                'sku' => $item['sku'] ?? null,
                'nome' => $item['nome'] ?? null,
                'qty_used' => $displayQty,
                'unit' => $displayUnit,
                'cost_unit' => $costUnit,
                'cost_total' => $costTotal,
                'loss_percent' => $lossPercent,
                'conversion_error' => $conversionError,
            ];
        }

        return [
            'recipe' => $recipe,
            'produced_qty' => $producedQty,
            'items' => $summary,
            'total_cost' => $total,
            'warnings' => $warnings,
        ];
    }

    private function runRecipe(int $recipeId, float $producedQty, float $saleUnitPrice, ?float $saleTotalOverride, string $source, ?int $sourceId, ?int $userId): int
    {
        if ($producedQty <= 0) {
            throw new \InvalidArgumentException('Quantidade produzida deve ser maior que zero.');
        }

        $recipe = $this->recipes->find($recipeId);
        if (!$recipe) {
            throw new \InvalidArgumentException('Receita não encontrada.');
        }

        $yieldQty = (float)($recipe['yield_qty'] ?? 0);
        if ($yieldQty <= 0) {
            throw new \InvalidArgumentException('Rendimento da receita inválido.');
        }

        $items = $this->items->listByRecipe($recipeId);
        if (!$items) {
            throw new \InvalidArgumentException('Cadastre itens na receita antes de executá-la.');
        }

        $allowNegative = $this->config->get('ALLOW_NEGATIVE_STOCK', '0') === '1';
        $factor = $producedQty / $yieldQty;
        $conn = $this->recipes->getConnection();

        $startedTransaction = !$conn->inTransaction();
        if ($startedTransaction) {
            $conn->beginTransaction();
        }
        try {
            $runId = $this->runs->create([
                'recipe_id' => $recipeId,
                'produced_qty' => $producedQty,
                'total_cost' => 0,
                'sale_unit_price' => $saleUnitPrice,
                'sale_total' => 0,
                'profit_total' => 0,
                'margin_percent' => 0,
                'source' => $source,
                'source_id' => $sourceId,
                'created_by' => $userId,
            ]);

            $totalCost = 0.0;

            foreach ($items as $item) {
                $qtyBase = (float)($item['qty'] ?? 0);
                $lossPercent = (float)($item['loss_percent'] ?? 0);
                $qtyUsed = $qtyBase * $factor;
                if ($lossPercent > 0) {
                    $qtyUsed *= (1 + ($lossPercent / 100));
                }

                $productUnit = (string)($item['product_unit'] ?? '');
                $convertedQty = convertQty($qtyUsed, (string)$item['unit'], $productUnit);
                if ($convertedQty <= 0) {
                    continue;
                }

                $costUnit = $this->getCurrentCostUnit((int)$item['stock_product_id']);
                $costTotal = $convertedQty * $costUnit;

                $this->consumeStock(
                    (int)$item['stock_product_id'],
                    $convertedQty,
                    $costUnit,
                    $runId,
                    $userId,
                    $allowNegative
                );

                $this->runItems->create([
                    'run_id' => $runId,
                    'stock_product_id' => (int)$item['stock_product_id'],
                    'qty_used' => $convertedQty,
                    'cost_unit_at_time' => $costUnit,
                    'cost_total' => $costTotal,
                ]);

                $totalCost += $costTotal;
            }

            $saleTotal = $saleTotalOverride ?? ($producedQty * $saleUnitPrice);
            $profit = $saleTotal - $totalCost;
            $margin = $saleTotal > 0 ? ($profit / $saleTotal) * 100 : 0;

            $this->runs->update($runId, [
                'total_cost' => $totalCost,
                'sale_total' => $saleTotal,
                'profit_total' => $profit,
                'margin_percent' => $margin,
            ]);

            if ($startedTransaction) {
                $conn->commit();
            }

            return $runId;
        } catch (\Throwable $e) {
            if ($startedTransaction && $conn->inTransaction()) {
                $conn->rollBack();
            }
            throw $e;
        }
    }

    private function sanitizeRecipePayload(array $data, bool $isUpdate = false): array
    {
        $produtoPaId = (int)($data['produto_pa_id'] ?? 0);
        if ($produtoPaId <= 0) {
            throw new \InvalidArgumentException('Selecione um Produto P.A para a receita.');
        }

        $produtoPa = $this->produtos->find($produtoPaId);
        if (!$produtoPa || strtoupper((string)($produtoPa['tipo'] ?? '')) !== 'PA') {
            throw new \InvalidArgumentException('Produto P.A inválido para associação da receita.');
        }

        $name = trim((string)($produtoPa['nome'] ?? ''));
        if ($name === '') {
            throw new \InvalidArgumentException('Produto P.A sem nome válido.');
        }

        $skuRaw = trim((string)($produtoPa['sku'] ?? ''));
        if ($skuRaw === '') {
            $skuRaw = $this->nextRecipeSku();
        }

        $yieldQty = (float)($data['yield_qty'] ?? 0);
        if ($yieldQty <= 0) {
            throw new \InvalidArgumentException('Informe o rendimento da receita.');
        }

        $yieldUnit = trim((string)($data['yield_unit'] ?? ''));
        if ($yieldUnit === '') {
            throw new \InvalidArgumentException('Informe a unidade do rendimento.');
        }

        $payload = [
            'name' => $name,
            'sku' => $skuRaw,
            'yield_qty' => $yieldQty,
            'yield_unit' => $yieldUnit,
            'active' => isset($data['active']) ? (int)(bool)$data['active'] : 1,
            'produto_pa_id' => $produtoPaId,
        ];

        if ($isUpdate) {
            $payload['updated_at'] = date('Y-m-d H:i:s');
        }

        if ((int)$payload['active'] === 1) {
            $existingActive = $this->recipes->findActiveByProdutoPaId($produtoPaId, $isUpdate ? (int)($data['id'] ?? 0) : null);
            if ($existingActive) {
                throw new \InvalidArgumentException('Já existe uma receita ativa para este Produto P.A.');
            }
        }

        return $payload;
    }

    private function syncProdutoAcabado(int $recipeId, array $payload): void
    {
        $produtoPaId = (int)($payload['produto_pa_id'] ?? 0);
        if ($produtoPaId <= 0) {
            return;
        }

        $produtoSelecionado = $this->produtos->find($produtoPaId);
        if (!$produtoSelecionado) {
            return;
        }

        $produtoAtualDaReceita = $this->produtos->findByRecipeId($recipeId);
        if ($produtoAtualDaReceita && (int)$produtoAtualDaReceita['id'] !== $produtoPaId) {
            $this->produtos->update((int)$produtoAtualDaReceita['id'], ['recipe_id' => null]);
        }

        $this->produtos->update($produtoPaId, ['recipe_id' => $recipeId]);
    }

    private function assertRecipeSkuAvailable(string $sku, ?int $recipeId = null): void
    {
        if ($sku === '') {
            return;
        }

        $produto = $this->produtos->findBySku($sku);
        if ($produto) {
            $produtoRecipeId = isset($produto['recipe_id']) ? (int)$produto['recipe_id'] : 0;
            if ($produtoRecipeId !== 0 && $recipeId !== null && $produtoRecipeId === $recipeId) {
                return;
            }

            if ($produtoRecipeId !== 0 && $recipeId === null) {
                throw new \InvalidArgumentException('Já existe um produto acabado cadastrado com este SKU.');
            }

            if ($produtoRecipeId !== 0 && $recipeId !== null && $produtoRecipeId !== $recipeId) {
                throw new \InvalidArgumentException('Já existe um produto acabado cadastrado com este SKU.');
            }
        }
    }

    private function nextRecipeSku(): string
    {
        $digitsRaw = $this->config->get('SKU_DIGITS', '3');
        $digits = (int)($digitsRaw ?: 3);
        if ($digits < 2) {
            $digits = 2;
        } elseif ($digits > 8) {
            $digits = 8;
        }

        $sequence = $this->recipes->nextSequence(self::RECIPE_SKU_PREFIX, $digits);

        return self::RECIPE_SKU_PREFIX.$sequence;
    }

    private function persistItems(int $recipeId, array $items): void
    {
        $order = 1;
        foreach ($items as $item) {
            $productId = (int)($item['stock_product_id'] ?? 0);
            $qty = (float)($item['qty'] ?? 0);
            $unit = trim((string)($item['unit'] ?? ''));
            $loss = isset($item['loss_percent']) ? (float)$item['loss_percent'] : 0;

            if ($productId <= 0 || $qty <= 0 || $unit === '') {
                continue;
            }

            $this->items->create([
                'recipe_id' => $recipeId,
                'stock_product_id' => $productId,
                'qty' => $qty,
                'unit' => $unit,
                'loss_percent' => $loss,
                'sort_order' => $order,
            ]);
            $order++;
        }
    }

    private function getCurrentCostUnit(int $produtoId): float
    {
        $conn = $this->estoques->getConnection();
        $stmt = $conn->prepare('SELECT quantidade, custo_medio FROM estoques WHERE produto_id = :produto');
        $stmt->execute(['produto' => $produtoId]);
        $rows = $stmt->fetchAll();
        $totalQty = 0.0;
        $totalCost = 0.0;
        foreach ($rows as $row) {
            $qty = (float)($row['quantidade'] ?? 0);
            $cost = (float)($row['custo_medio'] ?? 0);
            if ($qty > 0 && $cost > 0) {
                $totalQty += $qty;
                $totalCost += ($qty * $cost);
            }
        }
        if ($totalQty > 0) {
            return $totalCost / $totalQty;
        }

        $stmt = $conn->prepare('SELECT custo_unit FROM movimentos_estoque WHERE produto_id = :produto AND custo_unit > 0 ORDER BY id DESC LIMIT 1');
        $stmt->execute(['produto' => $produtoId]);
        $last = $stmt->fetchColumn();
        return $last !== false ? (float)$last : 0.0;
    }

    private function consumeStock(int $produtoId, float $qtyNeeded, float $costUnit, int $runId, ?int $userId, bool $allowNegative): void
    {
        $conn = $this->estoques->getConnection();
        $stmt = $conn->prepare('SELECT * FROM estoques WHERE produto_id = :produto ORDER BY validade ASC, id ASC FOR UPDATE');
        $stmt->execute(['produto' => $produtoId]);
        $lotes = $stmt->fetchAll();

        $totalBalance = 0.0;
        foreach ($lotes as $lote) {
            $totalBalance += (float)($lote['quantidade'] ?? 0);
        }

        if (!$allowNegative && $totalBalance < $qtyNeeded) {
            throw new \InvalidArgumentException('Estoque insuficiente para executar a receita.');
        }

        $remaining = $qtyNeeded;
        foreach ($lotes as $lote) {
            if ($remaining <= 0) {
                break;
            }
            $available = (float)($lote['quantidade'] ?? 0);
            if ($available <= 0) {
                continue;
            }
            $consume = min($available, $remaining);

            $conn->prepare('UPDATE estoques SET quantidade = quantidade - :qtd WHERE id = :id')
                ->execute([
                    'qtd' => $consume,
                    'id' => $lote['id'],
                ]);

            $this->movimentos->create([
                'produto_id' => $produtoId,
                'tipo' => 'saida',
                'status' => 'efetivado',
                'quantidade' => $consume,
                'custo_unit' => $costUnit,
                'lote' => $lote['lote'] ?? null,
                'validade' => $lote['validade'] ?? null,
                'ref_tipo' => 'recipe_run',
                'ref_id' => $runId,
                'usuario_id' => $userId,
            ]);

            $remaining -= $consume;
        }

        if ($remaining > 0) {
            if (!$allowNegative) {
                throw new \InvalidArgumentException('Estoque insuficiente para executar a receita.');
            }

            $fallback = $conn->prepare('SELECT * FROM estoques WHERE produto_id = :produto AND lote IS NULL AND localizacao IS NULL LIMIT 1 FOR UPDATE');
            $fallback->execute(['produto' => $produtoId]);
            $row = $fallback->fetch();

            if (!$row) {
                $conn->prepare('INSERT INTO estoques (produto_id, lote, validade, localizacao, quantidade, reservado, custo_medio) VALUES (:produto, NULL, NULL, NULL, 0, 0, :custo)')
                    ->execute([
                        'produto' => $produtoId,
                        'custo' => $costUnit,
                    ]);
                $rowId = (int)$conn->lastInsertId();
            } else {
                $rowId = (int)$row['id'];
            }

            $conn->prepare('UPDATE estoques SET quantidade = quantidade - :qtd WHERE id = :id')
                ->execute([
                    'qtd' => $remaining,
                    'id' => $rowId,
                ]);

            $this->movimentos->create([
                'produto_id' => $produtoId,
                'tipo' => 'saida',
                'status' => 'efetivado',
                'quantidade' => $remaining,
                'custo_unit' => $costUnit,
                'lote' => null,
                'validade' => null,
                'ref_tipo' => 'recipe_run',
                'ref_id' => $runId,
                'usuario_id' => $userId,
            ]);
        }
    }
}
