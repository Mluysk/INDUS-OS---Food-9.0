<?php
namespace App\Modules\Receitas\Controllers;

use App\Core\Controller;
use App\Repositories\ProdutosRepository;
use App\Services\ConfigService;
use App\Services\RecipeService;

class ReceitasController extends Controller
{
    private RecipeService $recipes;
    private ProdutosRepository $produtos;
    private ConfigService $config;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        RecipeService $recipes,
        ProdutosRepository $produtos,
        ConfigService $config
    ) {
        parent::__construct($container, $auth, $audit);
        $this->recipes = $recipes;
        $this->produtos = $produtos;
        $this->config = $config;
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $filters = [
            'term' => $_GET['q'] ?? null,
            'active' => $_GET['active'] ?? null,
        ];
        $recipes = $this->recipes->listRecipes($filters);
        $paRecipeRows = $this->buildPARecipeRows([]);

        $this->render('receitas/list', [
            'recipes' => $recipes,
            'paRecipeRows' => $paRecipeRows,
            'filters' => $filters,
        ], [
            'title' => 'Receitas',
            'activeMenu' => 'receitas',
            'breadcrumbs' => ['Cadastros', 'Receitas'],
        ]);
    }

    public function create(): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $this->render('receitas/form', [
            'recipe' => null,
            'items' => [],
            'nextSku' => $this->recipes->getNextSku(),
            'products' => $this->listProducts(),
            'paProducts' => $this->listPAProducts(),
            'selectedPaId' => max(0, (int)($_GET['produto_pa_id'] ?? 0)),
            'units' => $this->units(),
        ], [
            'title' => 'Nova receita',
            'activeMenu' => 'receitas',
            'breadcrumbs' => ['Cadastros', 'Receitas', 'Nova'],
        ]);
    }

    public function store(): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $data = $_POST;
        $data['items'] = $this->parseItems($data);

        try {
            $id = $this->recipes->createRecipe($data);
        } catch (\InvalidArgumentException $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/receitas/nova');
            return;
        }

        $this->setFlash('success', 'Receita cadastrada com sucesso.');
        $this->redirect('/receitas/' . $id);
    }

    public function show($id): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $recipe = $this->recipes->getRecipe((int)$id);
        if (!$recipe) {
            $this->setFlash('error', 'Receita não encontrada.');
            $this->redirect('/receitas');
            return;
        }

        $producedQty = isset($_GET['qty']) ? (float)$_GET['qty'] : (float)($recipe['yield_qty'] ?? 1);
        $producedQty = $producedQty > 0 ? $producedQty : (float)($recipe['yield_qty'] ?? 1);
        $simulation = $this->recipes->simulateCost((int)$id, $producedQty);

        $this->render('receitas/view', [
            'recipe' => $recipe,
            'simulation' => $simulation,
        ], [
            'title' => 'Receita',
            'activeMenu' => 'receitas',
            'breadcrumbs' => ['Cadastros', 'Receitas', $recipe['name'] ?? 'Detalhe'],
        ]);
    }

    public function edit($id): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $recipe = $this->recipes->getRecipe((int)$id);
        if (!$recipe) {
            $this->setFlash('error', 'Receita não encontrada.');
            $this->redirect('/receitas');
            return;
        }

        $this->render('receitas/form', [
            'recipe' => $recipe,
            'items' => $recipe['items'] ?? [],
            'nextSku' => $recipe['sku'] ?? $this->recipes->getNextSku(),
            'products' => $this->listProducts(),
            'paProducts' => $this->listPAProducts(),
            'units' => $this->units(),
        ], [
            'title' => 'Editar receita',
            'activeMenu' => 'receitas',
            'breadcrumbs' => ['Cadastros', 'Receitas', 'Editar'],
        ]);
    }

    public function update($id): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $data = $_POST;
        $data['items'] = $this->parseItems($data);

        try {
            $this->recipes->updateRecipe((int)$id, $data);
        } catch (\InvalidArgumentException $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/receitas/' . (int)$id . '/editar');
            return;
        }

        $this->setFlash('success', 'Receita atualizada com sucesso.');
        $this->redirect('/receitas/' . (int)$id);
    }

    public function runs($id): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $recipe = $this->recipes->getRecipe((int)$id);
        if (!$recipe) {
            $this->setFlash('error', 'Receita não encontrada.');
            $this->redirect('/receitas');
            return;
        }

        $runs = $this->recipes->listRuns((int)$id);

        $this->render('receitas/runs', [
            'recipe' => $recipe,
            'runs' => $runs,
        ], [
            'title' => 'Execuções da receita',
            'activeMenu' => 'receitas',
            'breadcrumbs' => ['Cadastros', 'Receitas', 'Execuções'],
        ]);
    }

    public function runManualForm($id): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $recipe = $this->recipes->getRecipe((int)$id);
        if (!$recipe) {
            $this->setFlash('error', 'Receita não encontrada.');
            $this->redirect('/receitas');
            return;
        }

        $this->render('receitas/run_manual', [
            'recipe' => $recipe,
        ], [
            'title' => 'Executar receita',
            'activeMenu' => 'receitas',
            'breadcrumbs' => ['Cadastros', 'Receitas', 'Executar'],
        ]);
    }

    public function runManual($id): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $recipe = $this->recipes->getRecipe((int)$id);
        if (!$recipe) {
            $this->setFlash('error', 'Receita não encontrada.');
            $this->redirect('/receitas');
            return;
        }

        $producedQty = (float)($_POST['produced_qty'] ?? 0);
        $saleUnitPrice = (float)($_POST['sale_unit_price'] ?? 0);

        try {
            $runId = $this->recipes->runRecipeManual((int)$id, $producedQty, $saleUnitPrice, (int)$this->auth->user()['id']);
        } catch (\Throwable $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/receitas/' . (int)$id . '/executar');
            return;
        }

        $this->setFlash('success', 'Receita executada. Execução #' . $runId);
        $this->redirect('/receitas/' . (int)$id . '/runs');
    }

    private function parseItems(array $data): array
    {
        $productIds = $data['item_product_id'] ?? [];
        $quantities = $data['item_qty'] ?? [];
        $units = $data['item_unit'] ?? [];
        $losses = $data['item_loss'] ?? [];

        $items = [];
        foreach ($productIds as $index => $productId) {
            $pid = (int)$productId;
            $qty = isset($quantities[$index]) ? (float)$quantities[$index] : 0.0;
            $unit = isset($units[$index]) ? trim((string)$units[$index]) : '';
            $loss = isset($losses[$index]) ? (float)$losses[$index] : 0.0;
            if ($pid > 0 && $qty > 0 && $unit !== '') {
                $items[] = [
                    'stock_product_id' => $pid,
                    'qty' => $qty,
                    'unit' => $unit,
                    'loss_percent' => $loss,
                ];
            }
        }

        return $items;
    }

    private function listProducts(): array
    {
        return $this->produtos->search(['sort' => 'nome', 'dir' => 'asc']);
    }


    private function listPAProducts(): array
    {
        return $this->produtos->search([
            'tipo' => 'PA',
            'sort' => 'nome',
            'dir' => 'asc',
        ]);
    }

    private function buildPARecipeRows(array $filters = []): array
    {
        $paFilters = [
            'tipo' => 'PA',
            'sort' => 'nome',
            'dir' => 'asc',
        ];
        if (!empty($filters['term'])) {
            $paFilters['term'] = $filters['term'];
        }

        $paProducts = $this->produtos->search($paFilters);
        $recipes = $this->recipes->listRecipes([]);
        $recipesByProduct = [];
        foreach ($recipes as $recipe) {
            $produtoPaId = (int)($recipe['produto_pa_id'] ?? 0);
            if ($produtoPaId > 0 && !isset($recipesByProduct[$produtoPaId])) {
                $recipesByProduct[$produtoPaId] = $recipe;
            }
        }

        $activeFilter = (string)($filters['active'] ?? '');
        $rows = [];
        foreach ($paProducts as $product) {
            $productId = (int)($product['id'] ?? 0);
            $recipe = $recipesByProduct[$productId] ?? null;
            $status = 'none';
            $statusLabel = 'Cadastrar receita';

            if ($recipe) {
                if ((int)($recipe['active'] ?? 1) !== 1) {
                    $status = 'inactive';
                    $statusLabel = 'Inativa';
                } elseif ((int)($recipe['item_count'] ?? 0) <= 0) {
                    $status = 'review';
                    $statusLabel = 'Revisao';
                } else {
                    $status = 'active';
                    $statusLabel = 'Ativa';
                }
            }

            if ($activeFilter === '1' && !in_array($status, ['active', 'review'], true)) {
                continue;
            }
            if ($activeFilter === '0' && $status !== 'inactive') {
                continue;
            }
            if ($activeFilter === 'none' && $status !== 'none') {
                continue;
            }
            if ($activeFilter === 'review' && $status !== 'review') {
                continue;
            }

            $rows[] = [
                'product' => $product,
                'recipe' => $recipe,
                'status' => $status,
                'status_label' => $statusLabel,
            ];
        }

        return $rows;
    }

    private function units(): array
    {
        $raw = $this->config->get('METRIC_UNITS', 'kg, g, l, ml, un') ?: 'kg, g, l, ml, un';
        $units = array_values(array_filter(array_map('trim', explode(',', $raw)), static fn($u) => $u !== ''));
        $defaults = ['kg', 'g', 'l', 'ml', 'un'];
        foreach ($defaults as $unit) {
            if (!in_array($unit, $units, true)) {
                $units[] = $unit;
            }
        }

        return $units;
    }

}
