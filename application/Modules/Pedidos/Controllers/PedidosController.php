<?php
namespace App\Modules\Pedidos\Controllers;

use App\Core\Controller;
use App\Services\PedidosService;
use App\Services\ConfigService;
use App\Services\ClientesService;
use App\Services\ProdutosService;
use InvalidArgumentException;

class PedidosController extends Controller
{
    private PedidosService $pedidos;
    private ConfigService $config;
    private ClientesService $clientes;
    private ProdutosService $produtos;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        PedidosService $pedidos,
        ConfigService $config,
        ClientesService $clientes,
        ProdutosService $produtos
    ) {
        parent::__construct($container, $auth, $audit);
        $this->pedidos = $pedidos;
        $this->config = $config;
        $this->clientes = $clientes;
        $this->produtos = $produtos;
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro','expedicao']);
        $lista = $this->pedidos->listar();
        $clientes = $this->clientes->all();
        $produtos = $this->produtos->all();
        $mapClientes = [];
        foreach ($clientes as $cliente) {
            $mapClientes[(int)($cliente['id'] ?? 0)] = $cliente;
        }

        $notePixEnabled = $this->config->get('NOTE_PIX_ENABLED', '0') === '1';
        $notePixKey = trim((string)$this->config->get('NOTE_PIX_KEY', ''));
        $saidaData = isset($_GET['saida_data']) ? (string)$_GET['saida_data'] : date('Y-m-d');
        $saidaLojas = $_GET['saida_lojas'] ?? [];
        if (!is_array($saidaLojas)) {
            $saidaLojas = [$saidaLojas];
        }
        $saidaLojas = array_values(array_filter(array_map('intval', $saidaLojas), static fn (int $id): bool => $id > 0));
        $saidaLojasDisponiveis = $this->pedidos->lojasComSaidaDoDia($saidaData);
        $lojasDisponiveisIds = array_map(static fn (array $loja): int => (int)($loja['id'] ?? 0), $saidaLojasDisponiveis);
        if ($saidaLojas && $lojasDisponiveisIds) {
            $saidaLojas = array_values(array_intersect($saidaLojas, $lojasDisponiveisIds));
        }
        $saidaDia = $this->pedidos->gerarSaidaDoDia($saidaData, $saidaLojas);

        if ($this->wantsJson()) {
            $this->json($lista);
            return;
        }

        $this->render('pedidos/index_with_patch', [
            'pedidos' => $lista,
            'clientes' => $clientes,
            'produtos' => $produtos,
            'clienteMap' => $mapClientes,
            'notePixEnabled' => $notePixEnabled,
            'notePixKey' => $notePixKey,
            'saidaDia' => $saidaDia,
            'saidaLojasDisponiveis' => $saidaLojasDisponiveis,
        ], [
            'title' => 'Pedidos',
            'activeMenu' => 'pedidos',
            'breadcrumbs' => ['Vendas', 'Pedidos'],
        ]);
    }

    public function store(): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro']);
        $payload = json_decode(file_get_contents('php://input'), true);
        $data = $payload ?: $_POST;

        $clienteId = (int)($data['cliente_id'] ?? 0);
        $itens = $this->normalizarItens($data);
        if ($clienteId <= 0 || empty($itens)) {
            if ($this->wantsJson()) {
                $this->json(['message' => 'Cliente e itens são obrigatórios'], 422);
            } else {
                $this->setFlash('error', 'Informe um cliente válido e pelo menos um item.');
                $this->redirect('/pedidos');
            }
            return;
        }

        $subtotal = $this->calcularTotal($itens);
        $freteValor = $this->normalizarFrete($data);
        try {
            $cobrancaDiferenca = $this->normalizarCobrancaDiferenca($data);
        } catch (InvalidArgumentException $exception) {
            if ($this->wantsJson()) {
                $this->json(['message' => $exception->getMessage()], 422);
            } else {
                $this->setFlash('error', $exception->getMessage());
                $this->redirect('/pedidos');
            }
            return;
        }
        $desconto = $this->normalizarDesconto($data, $subtotal + $freteValor + $cobrancaDiferenca['valor']);
        $pedido = [
            'observacoes' => $data['observacoes'] ?? null,
            'forma_pagto' => $this->normalizarPagamento($data['forma_pagto'] ?? 'PIX'),
            'frete_valor' => $freteValor,
            'desconto' => $desconto,
            'cobranca_diferenca_valor' => $cobrancaDiferenca['valor'],
            'cobranca_diferenca_descricao' => $cobrancaDiferenca['descricao'],
            'total' => $subtotal + $freteValor + $cobrancaDiferenca['valor'],
        ];
        $baixaNoCriar = filter_var($this->config->get('PEDIDO_BAIXA_NO_CRIAR', 'false'), FILTER_VALIDATE_BOOLEAN);
        $resultado = $this->pedidos->criarPedidoCliente($pedido, $itens, $clienteId, $this->auth->user()['id'], $baixaNoCriar);

        if ($this->wantsJson()) {
            $this->json($resultado, 201);
            return;
        }

        $mensagem = 'Pedido criado com ID '.$resultado['pedido_id'];
        if (!empty($resultado['backorder'])) {
            $mensagem .= ' (backorder gerado)';
        }
        $this->setFlash('success', $mensagem);
        $this->redirect('/pedidos');
    }

    public function edit(string $id): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro','expedicao']);
        $pedidoId = (int)$id;
        if ($pedidoId <= 0) {
            $this->json(['message' => 'Pedido inválido'], 422);
            return;
        }

        $detalhe = $this->pedidos->detalharCompleto($pedidoId);
        if (empty($detalhe['pedido'])) {
            $this->json(['message' => 'Pedido não encontrado'], 404);
            return;
        }

        $this->json($detalhe);
    }

    public function update(string $id): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro']);
        $pedidoId = (int)$id;
        $payload = json_decode(file_get_contents('php://input'), true);
        $data = $payload ?: $_POST;
        $clienteId = (int)($data['cliente_id'] ?? 0);
        $itens = $this->normalizarItens($data);
        $freteValor = array_key_exists('frete_ativo', $data) || array_key_exists('frete_valor', $data)
            ? $this->normalizarFrete($data)
            : null;
        $desconto = array_key_exists('desconto_valor', $data) || array_key_exists('desconto_tipo', $data)
            ? $this->normalizarDesconto($data, $this->calcularTotal($itens) + (float)($freteValor ?? 0))
            : null;

        try {
            $resultado = $this->pedidos->atualizarPedido(
                $pedidoId,
                $clienteId,
                $itens,
                $data['observacoes'] ?? null,
                $this->auth->user()['id'],
                $this->normalizarPagamento($data['forma_pagto'] ?? 'PIX'),
                $freteValor,
                $desconto
            );
        } catch (InvalidArgumentException $exception) {
            if ($this->wantsJson()) {
                $this->json(['message' => $exception->getMessage()], 422);
            } else {
                $this->setFlash('error', $exception->getMessage());
                $this->redirect('/pedidos');
            }
            return;
        }

        if ($this->wantsJson()) {
            $this->json($resultado);
            return;
        }

        $this->setFlash('success', 'Pedido atualizado com sucesso.');
        $this->redirect('/pedidos');
    }

    public function approve(string $id): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro']);

        $pedidoId = (int)$id;
        if ($pedidoId <= 0) {
            if ($this->wantsJson()) {
                $this->json(['message' => 'Pedido inválido'], 422);
            } else {
                $this->setFlash('error', 'Pedido inválido.');
                $this->redirect('/pedidos');
            }
            return;
        }

        try {
            $this->pedidos->atualizarStatus($pedidoId, 'producao', $this->auth->user()['id']);
        } catch (InvalidArgumentException $exception) {
            if ($this->wantsJson()) {
                $this->json(['message' => $exception->getMessage()], 422);
            } else {
                $this->setFlash('error', $exception->getMessage());
                $this->redirect('/pedidos');
            }
            return;
        }

        if ($this->wantsJson()) {
            $this->json(['message' => 'Pedido aprovado com sucesso']);
            return;
        }

        $this->setFlash('success', 'Pedido aprovado com sucesso.');
        $this->redirect('/pedidos');
    }

    public function nota(string $id): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro','expedicao']);
        $pedidoId = (int)$id;
        if ($pedidoId <= 0) {
            $this->setFlash('error', 'Pedido inválido para nota.');
            $this->redirect('/pedidos');
            return;
        }

        $detalhe = $this->pedidos->detalharCompleto($pedidoId);
        if (!$detalhe['pedido']) {
            $this->setFlash('error', 'Pedido não encontrado.');
            $this->redirect('/pedidos');
            return;
        }

        $pixParam = null;
        if (isset($_GET['pix'])) {
            $pixParam = $_GET['pix'] === '1';
        }
        $pixKey = isset($_GET['pix_key']) ? trim((string)$_GET['pix_key']) : '';
        $pixDefaultEnabled = $this->config->get('NOTE_PIX_ENABLED', '0') === '1';
        $pixDefaultKey = trim((string)$this->config->get('NOTE_PIX_KEY', ''));
        $pixDefaultType = strtolower((string)$this->config->get('NOTE_PIX_KEY_TYPE', 'cnpj'));

        $notaPixEnabled = $pixParam !== null ? $pixParam : $pixDefaultEnabled;
        $notaPixKey = $pixKey !== '' ? $pixKey : $pixDefaultKey;
        $notaPixType = in_array($pixDefaultType, ['cnpj','cpf','email','telefone'], true) ? $pixDefaultType : 'cnpj';
        $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'documento', 'pedidos', $pedidoId, [
            'documento' => 'nota_pedido',
            'codigo' => $detalhe['pedido']['codigo'] ?? null,
            'cliente' => $detalhe['pedido']['cliente_nome'] ?? $detalhe['pedido']['cliente'] ?? null,
            'total' => $detalhe['pedido']['total'] ?? null,
            'pix_na_nota' => $notaPixEnabled,
        ]);

        $this->render('pedidos/nota', [
            'pedido' => $detalhe['pedido'],
            'itens' => $detalhe['itens'],
            'notaPixEnabled' => $notaPixEnabled,
            'notaPixKey' => $notaPixKey,
            'notaPixType' => $notaPixType,
        ], [
            'title' => 'Nota de pedido',
            'layout' => 'layouts/print',
        ]);
    }

    private function normalizarItens(array $data): array
    {
        $itensRaw = $data['itens'] ?? [];
        if (is_string($itensRaw)) {
            $decoded = json_decode($itensRaw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        if (!empty($data['item_produto']) && is_array($data['item_produto'])) {
            $produtos = $data['item_produto'];
            $quantidades = $data['item_quantidade'] ?? [];
            $precos = $data['item_preco'] ?? [];
            $itens = [];
            foreach ($produtos as $idx => $produtoId) {
                $produto = (int)$produtoId;
                $quantidade = isset($quantidades[$idx]) ? (float)$quantidades[$idx] : 0.0;
                $preco = isset($precos[$idx]) ? (float)$precos[$idx] : 0.0;
                if ($produto > 0 && $quantidade > 0) {
                    $itens[] = [
                        'produto_id' => $produto,
                        'quantidade' => $quantidade,
                        'preco_unit' => $preco,
                    ];
                }
            }
            return $itens;
        }

        return is_array($itensRaw) ? $itensRaw : [];
    }

    private function calcularTotal(array $itens): float
    {
        return array_reduce($itens, static function ($carry, $item) {
            $qtd = (float)($item['quantidade'] ?? 0);
            $preco = (float)($item['preco_unit'] ?? 0);
            return $carry + ($qtd * $preco);
        }, 0.0);
    }

    private function normalizarFrete(array $data): float
    {
        $ativo = filter_var($data['frete_ativo'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (!$ativo) {
            return 0.0;
        }

        $valor = str_replace(',', '.', (string)($data['frete_valor'] ?? '0'));
        return max(0.0, (float)$valor);
    }

    private function normalizarCobrancaDiferenca(array $data): array
    {
        $ativo = filter_var($data['cobranca_diferenca_ativa'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (!$ativo) {
            return ['valor' => 0.0, 'descricao' => null];
        }

        $valor = max(0.0, (float)str_replace(',', '.', (string)($data['cobranca_diferenca_valor'] ?? '0')));
        $descricao = trim((string)($data['cobranca_diferenca_descricao'] ?? ''));
        if ($valor <= 0 || $descricao === '') {
            throw new InvalidArgumentException('Informe a descrição e o valor da cobrança de diferença.');
        }

        return [
            'valor' => $valor,
            'descricao' => mb_substr($descricao, 0, 160),
        ];
    }

    private function normalizarDesconto(array $data, float $base): float
    {
        $tipo = (string)($data['desconto_tipo'] ?? 'valor');
        $valor = max(0.0, (float)str_replace(',', '.', (string)($data['desconto_valor'] ?? '0')));
        if ($valor <= 0 || $base <= 0) {
            return 0.0;
        }
        $desconto = $tipo === 'percentual' ? ($base * min($valor, 100.0) / 100.0) : $valor;
        return min($base, $desconto);
    }

    private function normalizarPagamento(?string $pagamento): string
    {
        $pagamento = trim((string)$pagamento);
        $permitidos = ['PIX','Dinheiro','Cartão de crédito','Cartão de débito','Boleto','TED','Transferência bancária','Depósito bancário','Cheque','Vale alimentação','Vale refeição','A prazo','Outros'];
        return in_array($pagamento, $permitidos, true) ? $pagamento : 'PIX';
    }
}
