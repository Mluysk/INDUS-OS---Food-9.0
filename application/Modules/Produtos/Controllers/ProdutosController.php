<?php
namespace App\Modules\Produtos\Controllers;

use App\Core\Controller;
use App\Services\ConfigService;
use App\Services\EstoqueService;
use App\Services\ProdutosService;
use App\Repositories\AuditLogsRepository;
use App\Repositories\ProdutosLancamentosRepository;

class ProdutosController extends Controller
{
    private ProdutosService $produtos;
    private ConfigService $config;
    private ProdutosLancamentosRepository $lancamentos;
    private EstoqueService $estoque;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        ProdutosService $produtos,
        ConfigService $config,
        ProdutosLancamentosRepository $lancamentos,
        EstoqueService $estoque
    ) {
        parent::__construct($container, $auth, $audit);
        $this->produtos = $produtos;
        $this->config = $config;
        $this->lancamentos = $lancamentos;
        $this->estoque = $estoque;
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $filters = [
            'term' => $_GET['q'] ?? null,
            'categoria' => $_GET['categoria'] ?? null,
            'tipo' => $_GET['tipo'] ?? null,
            'status' => $_GET['status'] ?? 'all',
            'sort' => $_GET['sort'] ?? null,
            'dir' => $_GET['dir'] ?? null,
            'include_inactive' => true,
        ];
        $produtos = $this->produtos->all($filters);
        $produtosLancamento = $this->produtos->all([
            'sort' => 'nome',
            'dir' => 'asc',
        ]);
        $lancamentos = $this->lancamentos->listarRecentes(13);
        $produtoIdsVisiveis = array_values(array_unique(array_merge(array_column($produtos, 'id'), array_column($produtosLancamento, 'id'))));
        $historicoLancamentos = $this->lancamentos->listarPorProdutos($produtoIdsVisiveis);
        $lancamentosPorProduto = $this->lancamentos->ultimosPorProduto(array_column($produtosLancamento, 'id'));
        $produtoAtualizacoes = $this->container->get(AuditLogsRepository::class)->ultimasModificacoes('produtos', $produtoIdsVisiveis);
        $categorias = $this->produtos->categorias();
        $metricUnitsRaw = $this->config->get('METRIC_UNITS', 'kg, un, cx') ?: 'kg, un, cx';
        $unidades = array_values(array_filter(array_map('trim', explode(',', $metricUnitsRaw)), static fn($u) => $u !== ''));
        if (empty($unidades)) {
            $unidades = ['kg', 'un', 'cx'];
        }
        $pageSizeRaw = $this->config->get('LIST_PAGE_SIZE_OPTIONS', '15,20,30,40,50,100') ?: '15,20,30,40,50,100';
        $pageSizeOptions = array_values(array_map(
            fn (int $size): int => min(500, $size),
            array_filter(array_map('intval', preg_split('/[,\s;]+/', $pageSizeRaw)), fn (int $size): bool => $size > 0)
        ));
        if (!$pageSizeOptions) {
            $pageSizeOptions = [15, 20, 30, 40, 50, 100];
        }
        $pageSizeDefault = max(1, min(500, (int)($this->config->get('LIST_PAGE_SIZE_DEFAULT_PRODUTOS', $this->config->get('LIST_PAGE_SIZE_DEFAULT', '15')) ?: '15')));
        $pageSizeOptions = array_values(array_unique(array_merge($pageSizeOptions, [$pageSizeDefault])));
        sort($pageSizeOptions);

        if ($this->wantsJson()) {
            $this->json(['data' => $produtos, 'filters' => $filters, 'unidades' => $unidades]);
            return;
        }

        $this->render('produtos/index_with_patch', [
            'produtos' => $produtos,
            'produtosLancamento' => $produtosLancamento,
            'lancamentos' => $lancamentos,
            'historicoLancamentos' => $historicoLancamentos,
            'lancamentosPorProduto' => $lancamentosPorProduto,
            'produtoAtualizacoes' => $produtoAtualizacoes,
            'categorias' => $categorias,
            'filters' => $filters,
            'unidades' => $unidades,
            'skuDigits' => $this->produtos->getSkuDigits(),
            'nextSkuPa' => $this->produtos->nextSkuForTipo('PA'),
            'nextSkuMp' => $this->produtos->nextSkuForTipo('MP'),
            'listPageSizeOptions' => $pageSizeOptions,
            'listPageSizeDefault' => $pageSizeDefault,
        ], [
            'title' => 'Produtos',
            'activeMenu' => 'produtos',
            'breadcrumbs' => ['Cadastros', 'Produtos'],
        ]);
    }

    public function store(): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        if (!empty($data['lancamento_produto'])) {
            $this->salvarLancamento($data);
            return;
        }

        $componentesRaw = $data['componentes'] ?? [];
        if (is_string($componentesRaw) && trim($componentesRaw) !== '') {
            $decoded = json_decode($componentesRaw, true);
            $componentes = is_array($decoded) ? $decoded : [];
        } elseif (is_array($componentesRaw)) {
            $componentes = $componentesRaw;
        } else {
            $componentes = [];
        }
        unset($data['componentes']);
        try {
            $this->aplicarRemocaoImagens($data);
            $imagemPath = $this->salvarImagemProduto('imagem_produto');
            if ($imagemPath !== null) {
                $data['imagem_path'] = $imagemPath;
            }
            $imagemEmbalagemPath = $this->salvarImagemProduto('imagem_embalagem');
            if ($imagemEmbalagemPath !== null) {
                $data['imagem_embalagem_path'] = $imagemEmbalagemPath;
            }
            $imagemProntoPath = $this->salvarImagemProduto('imagem_pronto');
            if ($imagemProntoPath !== null) {
                $data['imagem_pronto_path'] = $imagemProntoPath;
            }
            $id = $this->produtos->create($data, $componentes, $this->auth->user()['id']);
            $this->salvarRecebimentoInicial($data, $id);
            $this->registrarRecebimentoMpNoEstoque($data, $id);
        } catch (\InvalidArgumentException $e) {
            if ($this->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->redirect('/produtos');
            }
            return;
        }

        if ($this->wantsJson()) {
            $this->json([
                'id' => $id,
                'message' => 'Produto cadastrado com sucesso.',
                'next_sku_pa' => $this->produtos->nextSkuForTipo('PA'),
                'next_sku_mp' => $this->produtos->nextSkuForTipo('MP'),
            ], 201);
        } else {
            $this->setFlash('success', 'Produto cadastrado com sucesso.');
            $this->redirect('/produtos');
        }
    }

    private function salvarLancamento(array $data): void
    {
        $produtoId = (int)($data['lancamento_produto'] ?? 0);
        if ($produtoId <= 0) {
            $this->setFlash('error', 'Selecione um produto válido para o lançamento.');
            $this->redirect('/produtos?tab=lancamento');
            return;
        }

        $payload = $this->montarLancamentoPayload($data, $produtoId);

        try {
            $this->lancamentos->criar($payload);
            if (!empty($payload['preco']) && $payload['preco'] > 0) {
                try {
                    $this->produtos->atualizarPreco($produtoId, (float)$payload['preco'], $this->auth->user()['id']);
                } catch (\Throwable $e) {
                    $this->setFlash('error', 'Lançamento salvo, mas não foi possível atualizar o preço do produto.');
                }
            }
        } catch (\InvalidArgumentException $e) {
            if ($this->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->redirect('/produtos?tab=lancamento');
            }
            return;
        }

        if ($this->wantsJson()) {
            $this->json(['message' => 'Lançamento salvo com sucesso.'], 201);
        } else {
            $this->setFlash('success', 'Lançamento salvo com sucesso.');
            $this->redirect('/produtos?tab=lancamento');
        }
    }

    private function salvarRecebimentoInicial(array $data, int $produtoId): void
    {
        $payload = $this->montarLancamentoPayload($data, $produtoId, 'recebimento_');
        $hasData = array_filter($payload, static fn($value, $key) => $key !== 'produto_id' && $key !== 'usuario_id', ARRAY_FILTER_USE_BOTH);
        if (!$hasData) {
            return;
        }

        try {
            $this->lancamentos->criar($payload);
        } catch (\InvalidArgumentException $e) {
            $this->setFlash('error', 'Produto cadastrado, mas não foi possível salvar o recebimento inicial.');
        }
    }

    private function registrarRecebimentoMpNoEstoque(array $data, int $produtoId): void
    {
        $tipo = strtoupper(trim((string)($data['tipo'] ?? '')));
        if ($tipo !== 'MP') {
            return;
        }

        $quantidade = isset($data['recebimento_quantidade'])
            ? (float)str_replace(',', '.', (string)$data['recebimento_quantidade'])
            : 0;
        if ($quantidade <= 0) {
            return;
        }

        $this->estoque->registrarMovimento([
            'produto_id' => $produtoId,
            'tipo' => 'entrada',
            'status' => 'efetivado',
            'quantidade' => $quantidade,
            'lote' => $this->sanitizeText($data['recebimento_lote'] ?? null),
            'validade' => $this->normalizeDate($data['recebimento_data_vencimento'] ?? null),
            'custo_unit' => isset($data['recebimento_preco']) ? (float)$data['recebimento_preco'] : 0,
            'usuario_id' => $this->auth->user()['id'],
        ], $this->auth->user()['id']);
    }

    private function montarLancamentoPayload(array $data, int $produtoId, string $prefix = 'lancamento_'): array
    {
        return [
            'produto_id' => $produtoId,
            'preco' => isset($data[$prefix.'preco']) ? (float)$data[$prefix.'preco'] : 0,
            'fornecedor' => $this->sanitizeText($data[$prefix.'fornecedor'] ?? null),
            'data_entrega' => $this->normalizeDate($data[$prefix.'data_entrega'] ?? null),
            'lote' => $this->sanitizeText($data[$prefix.'lote'] ?? null),
            'data_fabricacao' => $this->normalizeDate($data[$prefix.'data_fabricacao'] ?? null),
            'data_vencimento' => $this->normalizeDate($data[$prefix.'data_vencimento'] ?? null),
            'usuario_id' => $this->auth->user()['id'],
        ];
    }

    private function normalizeDate(?string $value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        $date = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$date) {
            return null;
        }

        return $date->format('Y-m-d');
    }

    private function sanitizeText(?string $value): ?string
    {
        $value = trim((string)$value);
        return $value === '' ? null : $value;
    }

    public function ficha($id): void
    {
        $this->auth->ensureRole(['admin','gestor','producao']);
        $ficha = $this->produtos->fichaTecnica((int)$id);
        $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'documento', 'produtos', (int)$id, [
            'documento' => 'ficha_tecnica',
            'produto' => $ficha['produto']['nome'] ?? null,
            'sku' => $ficha['produto']['sku'] ?? null,
        ]);

        if ($this->wantsJson()) {
            $this->json($ficha);
            return;
        }

        $this->render('produtos/ficha', [
            'ficha' => $ficha,
        ], [
            'title' => 'Ficha técnica',
            'activeMenu' => 'produtos',
            'breadcrumbs' => ['Produtos', 'Ficha técnica'],
        ]);
    }

    public function printList(): void
    {
        $printMode = strtolower(trim((string)($_GET['modo'] ?? '')));
        $simplified = $printMode === 'simplificado';
        $visual = $printMode === 'visual';
        $sharedVisual = $visual && in_array(strtolower(trim((string)($_GET['compartilhar'] ?? $_GET['share'] ?? ''))), ['1', 'sim', 'true'], true);

        if (!$sharedVisual) {
            $this->auth->ensureRole(['admin','gestor','producao']);
        }

        $filters = [
            'term' => $_GET['q'] ?? null,
            'categoria' => $_GET['categoria'] ?? null,
            'tipo' => $_GET['tipo'] ?? null,
            'sort' => $_GET['sort'] ?? null,
            'dir' => $_GET['dir'] ?? null,
            'include_inactive' => false,
        ];
        $produtos = $this->produtos->all($filters);
        $usuarioId = (int)($this->auth->user()['id'] ?? 0);
        if ($usuarioId > 0) {
            $this->audit->log($usuarioId, 'documento', 'produtos', null, [
                'documento' => $visual ? 'catalogo_produtos_visual' : ($simplified ? 'catalogo_produtos_simplificado' : 'catalogo_produtos'),
                'quantidade' => count($produtos),
                'filtros' => $filters,
                'modo' => $visual ? 'visual' : ($simplified ? 'simplificado' : 'completo'),
                'compartilhado' => $sharedVisual,
            ]);
        }

        $this->render('produtos/print', [
            'produtos' => $produtos,
            'filters' => $filters,
            'simplified' => $simplified,
            'visual' => $visual,
            'sharedVisual' => $sharedVisual,
        ], [
            'title' => 'Catálogo de produtos',
            'layout' => 'layouts/print',
        ]);
    }

    public function update($id): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        try {
            $this->aplicarRemocaoImagens($data);
            $imagemPath = $this->salvarImagemProduto('imagem_produto');
            if ($imagemPath !== null) {
                $data['imagem_path'] = $imagemPath;
            }
            $imagemEmbalagemPath = $this->salvarImagemProduto('imagem_embalagem');
            if ($imagemEmbalagemPath !== null) {
                $data['imagem_embalagem_path'] = $imagemEmbalagemPath;
            }
            $imagemProntoPath = $this->salvarImagemProduto('imagem_pronto');
            if ($imagemProntoPath !== null) {
                $data['imagem_pronto_path'] = $imagemProntoPath;
            }
            $this->produtos->update((int)$id, $data, $this->auth->user()['id']);
        } catch (\InvalidArgumentException $e) {
            if ($this->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->redirect('/produtos');
            }
            return;
        }

        if ($this->wantsJson()) {
            $this->json(['message' => 'Produto atualizado com sucesso.']);
        } else {
            $this->setFlash('success', 'Produto atualizado com sucesso.');
            $this->redirect('/produtos');
        }
    }

    public function deactivate($id): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        try {
            $this->produtos->deactivate((int)$id, (int)$this->auth->user()['id']);
            $this->setFlash('success', 'Produto desativado com sucesso.');
        } catch (\InvalidArgumentException $e) {
            $this->setFlash('error', $e->getMessage());
        }
        $this->redirect('/produtos?tab=lista');
    }

    public function activate($id): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        try {
            $this->produtos->activate((int)$id, (int)$this->auth->user()['id']);
            $this->setFlash('success', 'Produto ativado com sucesso.');
        } catch (\InvalidArgumentException $e) {
            $this->setFlash('error', $e->getMessage());
        }
        $this->redirect('/produtos?tab=lista');
    }

    public function delete($id): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        try {
            $this->produtos->delete((int)$id, (int)$this->auth->user()['id']);
            $message = 'Produto e seus registros vinculados foram excluidos permanentemente.';
        } catch (\InvalidArgumentException $e) {
            if ($this->wantsJson()) {
                $this->json(['error' => $e->getMessage()], 422);
                return;
            }
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/produtos?tab=lista');
            return;
        }

        if ($this->wantsJson()) {
            $this->json([
                'message' => $message,
                'next_sku_pa' => $this->produtos->nextSkuForTipo('PA'),
                'next_sku_mp' => $this->produtos->nextSkuForTipo('MP'),
            ]);
            return;
        }
        $this->setFlash('success', $message);
        $this->redirect('/produtos?tab=lista');
    }

    private function salvarImagemProduto(string $field): ?string
    {
        if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
            return null;
        }

        $file = $_FILES[$field];
        $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($error !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('Nao foi possivel enviar a imagem do produto.');
        }

        $tmpName = (string)($file['tmp_name'] ?? '');
        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            throw new \InvalidArgumentException('Imagem do produto invalida.');
        }

        if ((int)($file['size'] ?? 0) > 4 * 1024 * 1024) {
            throw new \InvalidArgumentException('A imagem do produto deve ter no maximo 4 MB.');
        }

        $mime = mime_content_type($tmpName) ?: '';
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];
        if (!isset($extensions[$mime])) {
            throw new \InvalidArgumentException('Envie uma imagem JPG, PNG, WEBP ou GIF.');
        }

        $uploadDir = dirname(__DIR__, 4) . '/public/uploads/produtos';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new \InvalidArgumentException('Nao foi possivel preparar a pasta de imagens dos produtos.');
        }

        $filename = 'produto-' . date('YmdHis') . '-' . bin2hex(random_bytes(5)) . '.' . $extensions[$mime];
        $destination = $uploadDir . '/' . $filename;
        if (!move_uploaded_file($tmpName, $destination)) {
            throw new \InvalidArgumentException('Nao foi possivel salvar a imagem do produto.');
        }

        return 'uploads/produtos/' . $filename;
    }

    private function aplicarRemocaoImagens(array &$data): void
    {
        $map = [
            'remove_imagem_path' => 'imagem_path',
            'remove_imagem_embalagem_path' => 'imagem_embalagem_path',
            'remove_imagem_pronto_path' => 'imagem_pronto_path',
        ];

        foreach ($map as $flag => $field) {
            if (($data[$flag] ?? '0') === '1') {
                $data[$field] = '';
            }
            unset($data[$flag]);
        }
    }
}
