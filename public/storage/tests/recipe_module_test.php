<?php

declare(strict_types=1);

use App\Services\RecipeService;
use App\Services\ConfigService;

$bootstrap = require __DIR__ . '/../../application/bootstrap.php';
$container = $bootstrap['container'];

$pdo = $container->get(PDO::class);
/** @var RecipeService $recipes */
$recipes = $container->get(RecipeService::class);
/** @var ConfigService $config */
$config = $container->get(ConfigService::class);

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

echo "Testando conversão de unidades...\n";
assertTrue(abs(convertQty(1, 'kg', 'g') - 1000) < 0.0001, 'Falha conversão KG->G');
assertTrue(abs(convertQty(250, 'g', 'kg') - 0.25) < 0.0001, 'Falha conversão G->KG');
assertTrue(abs(convertQty(1, 'l', 'ml') - 1000) < 0.0001, 'Falha conversão L->ML');
assertTrue(abs(convertQty(750, 'ml', 'l') - 0.75) < 0.0001, 'Falha conversão ML->L');
assertTrue(abs(convertQty(2, 'un', 'un') - 2) < 0.0001, 'Falha conversão UN');

echo "Conversões ok.\n";

$created = [
    'products' => [],
    'stocks' => [],
    'recipes' => [],
    'recipe_items' => [],
    'runs' => [],
    'orders' => [],
    'order_items' => [],
    'movements' => [],
];

$originalAllowNegative = $config->get('ALLOW_NEGATIVE_STOCK', '0');

try {
    $sku1 = 'MP-' . strtoupper(bin2hex(random_bytes(3)));
    $sku2 = 'MP-' . strtoupper(bin2hex(random_bytes(3)));

    $stmt = $pdo->prepare('INSERT INTO produtos (sku, nome, tipo, categoria, unidade, preco, created_at) VALUES (:sku, :nome, :tipo, :categoria, :unidade, :preco, NOW())');
    $stmt->execute([
        'sku' => $sku1,
        'nome' => 'Ingrediente A',
        'tipo' => 'MP',
        'categoria' => 'Teste',
        'unidade' => 'kg',
        'preco' => 0,
    ]);
    $produtoA = (int)$pdo->lastInsertId();
    $created['products'][] = $produtoA;

    $stmt->execute([
        'sku' => $sku2,
        'nome' => 'Ingrediente B',
        'tipo' => 'MP',
        'categoria' => 'Teste',
        'unidade' => 'kg',
        'preco' => 0,
    ]);
    $produtoB = (int)$pdo->lastInsertId();
    $created['products'][] = $produtoB;

    $stmtStock = $pdo->prepare('INSERT INTO estoques (produto_id, lote, validade, localizacao, quantidade, reservado, custo_medio) VALUES (:produto, :lote, :validade, NULL, :quantidade, 0, :custo)');
    $stmtStock->execute([
        'produto' => $produtoA,
        'lote' => 'L1',
        'validade' => null,
        'quantidade' => 50,
        'custo' => 12.5,
    ]);
    $created['stocks'][] = (int)$pdo->lastInsertId();

    $stmtStock->execute([
        'produto' => $produtoB,
        'lote' => 'L2',
        'validade' => null,
        'quantidade' => 20,
        'custo' => 9.0,
    ]);
    $created['stocks'][] = (int)$pdo->lastInsertId();

    $recipeId = $recipes->createRecipe([
        'name' => 'Receita Teste',
        'sku' => 'RC-' . strtoupper(bin2hex(random_bytes(2))),
        'yield_qty' => 10,
        'yield_unit' => 'kg',
        'active' => 1,
        'items' => [
            ['stock_product_id' => $produtoA, 'qty' => 3, 'unit' => 'kg', 'loss_percent' => 0],
        ],
    ]);
    $created['recipes'][] = $recipeId;

    $runId = $recipes->runRecipeManual($recipeId, 10, 25.0, 1);
    $created['runs'][] = $runId;
    echo "Execução manual com saldo suficiente ok.\n";

    $config->set('ALLOW_NEGATIVE_STOCK', '0');
    $failed = false;
    try {
        $recipes->runRecipeManual($recipeId, 999, 0, 1);
    } catch (Throwable $e) {
        $failed = true;
    }
    assertTrue($failed, 'Era esperado bloquear execução sem saldo suficiente');
    echo "Bloqueio sem saldo ok.\n";

    $recipeRollbackId = $recipes->createRecipe([
        'name' => 'Receita Rollback',
        'sku' => 'RC-' . strtoupper(bin2hex(random_bytes(2))),
        'yield_qty' => 1,
        'yield_unit' => 'kg',
        'active' => 1,
        'items' => [
            ['stock_product_id' => $produtoA, 'qty' => 1, 'unit' => 'kg', 'loss_percent' => 0],
            ['stock_product_id' => $produtoB, 'qty' => 1, 'unit' => 'invalid', 'loss_percent' => 0],
        ],
    ]);
    $created['recipes'][] = $recipeRollbackId;

    $beforeQtyStmt = $pdo->prepare('SELECT quantidade FROM estoques WHERE produto_id = :produto LIMIT 1');
    $beforeQtyStmt->execute(['produto' => $produtoA]);
    $beforeQty = (float)$beforeQtyStmt->fetchColumn();

    try {
        $recipes->runRecipeManual($recipeRollbackId, 1, 0, 1);
    } catch (Throwable $e) {
        $afterQtyStmt = $pdo->prepare('SELECT quantidade FROM estoques WHERE produto_id = :produto LIMIT 1');
        $afterQtyStmt->execute(['produto' => $produtoA]);
        $afterQty = (float)$afterQtyStmt->fetchColumn();
        assertTrue(abs($beforeQty - $afterQty) < 0.0001, 'Rollback não restaurou estoque');
        echo "Rollback ok.\n";
    }

    $orderStmt = $pdo->prepare('INSERT INTO pedidos (cliente_id, codigo, status, total, created_at) VALUES (1, :codigo, "faturado", 0, NOW())');
    $orderStmt->execute(['codigo' => 'PED-' . strtoupper(bin2hex(random_bytes(2)))]);
    $pedidoId = (int)$pdo->lastInsertId();
    $created['orders'][] = $pedidoId;

    $pdo->prepare('UPDATE produtos SET recipe_id = :recipe WHERE id = :id')
        ->execute(['recipe' => $recipeId, 'id' => $produtoA]);

    $itemStmt = $pdo->prepare('INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unit, desconto) VALUES (:pedido, :produto, 2, 20, 0)');
    $itemStmt->execute(['pedido' => $pedidoId, 'produto' => $produtoA]);
    $orderItemId = (int)$pdo->lastInsertId();
    $created['order_items'][] = $orderItemId;

    $runSaleId = $recipes->runRecipeFromSale($orderItemId, 1);
    $created['runs'][] = $runSaleId;
    $runSaleId2 = $recipes->runRecipeFromSale($orderItemId, 1);
    assertTrue($runSaleId === $runSaleId2, 'Idempotência por venda falhou');
    echo "Idempotência ok.\n";

    echo "Testes concluídos com sucesso.\n";
} finally {
    $config->set('ALLOW_NEGATIVE_STOCK', $originalAllowNegative ?? '0');

    if (!empty($created['runs'])) {
        $placeholders = implode(',', array_fill(0, count($created['runs']), '?'));
        $pdo->prepare("DELETE FROM recipe_run_items WHERE run_id IN ({$placeholders})")->execute($created['runs']);
        $pdo->prepare("DELETE FROM recipe_runs WHERE id IN ({$placeholders})")->execute($created['runs']);
        $pdo->prepare("DELETE FROM movimentos_estoque WHERE ref_tipo = 'recipe_run' AND ref_id IN ({$placeholders})")->execute($created['runs']);
    }

    if (!empty($created['recipes'])) {
        $placeholders = implode(',', array_fill(0, count($created['recipes']), '?'));
        $pdo->prepare("DELETE FROM recipe_items WHERE recipe_id IN ({$placeholders})")->execute($created['recipes']);
        $pdo->prepare("DELETE FROM recipes WHERE id IN ({$placeholders})")->execute($created['recipes']);
    }

    if (!empty($created['order_items'])) {
        $placeholders = implode(',', array_fill(0, count($created['order_items']), '?'));
        $pdo->prepare("DELETE FROM pedido_itens WHERE id IN ({$placeholders})")->execute($created['order_items']);
    }

    if (!empty($created['orders'])) {
        $placeholders = implode(',', array_fill(0, count($created['orders']), '?'));
        $pdo->prepare("DELETE FROM pedidos WHERE id IN ({$placeholders})")->execute($created['orders']);
    }

    if (!empty($created['stocks'])) {
        $placeholders = implode(',', array_fill(0, count($created['stocks']), '?'));
        $pdo->prepare("DELETE FROM estoques WHERE id IN ({$placeholders})")->execute($created['stocks']);
    }

    if (!empty($created['products'])) {
        $placeholders = implode(',', array_fill(0, count($created['products']), '?'));
        $pdo->prepare("DELETE FROM produtos WHERE id IN ({$placeholders})")->execute($created['products']);
    }
}
