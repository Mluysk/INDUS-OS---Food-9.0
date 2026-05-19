<?php
namespace App\Services;

use App\Libraries\SimplePdf;
use App\Libraries\SimpleXlsx;
use App\Repositories\PedidosRepository;
use App\Repositories\FinanceiroTitulosRepository;
use App\Repositories\EstoquesRepository;

class RelatoriosService
{
    private PedidosRepository $pedidos;
    private FinanceiroTitulosRepository $titulos;
    private EstoquesRepository $estoques;

    public function __construct(PedidosRepository $pedidos, FinanceiroTitulosRepository $titulos, EstoquesRepository $estoques)
    {
        $this->pedidos = $pedidos;
        $this->titulos = $titulos;
        $this->estoques = $estoques;
    }

    public function gerarPedidosPdf(): array
    {
        $dados = $this->pedidos->all();
        $html = '<h1>Pedidos</h1><table border="1" width="100%"><tr><th>Código</th><th>Status</th><th>Total</th></tr>';
        foreach ($dados as $pedido) {
            $html .= sprintf('<tr><td>%s</td><td>%s</td><td>%.2f</td></tr>', $pedido['codigo'], $pedido['status'], $pedido['total']);
        }
        $html .= '</table>';

        $pdf = new SimplePdf();
        $content = $pdf->renderFromHtml($html);

        return [
            'filename' => 'relatorio-pedidos-' . date('Ymd_His') . '.pdf',
            'mime' => 'application/pdf',
            'content' => $content,
        ];
    }

    public function gerarEstoqueXlsx(): array
    {
        $writer = new SimpleXlsx('Estoque');
        $writer->setHeader(['SKU', 'Quantidade', 'Validade']);
        $stmt = $this->estoques->getConnection()->query('SELECT e.*, p.sku FROM estoques e JOIN produtos p ON p.id = e.produto_id');
        foreach ($stmt->fetchAll() as $linha) {
            $writer->addRow([$linha['sku'], $linha['quantidade'], $linha['validade']]);
        }

        return [
            'filename' => 'relatorio-estoque-' . date('Ymd_His') . '.xlsx',
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'content' => $writer->toString(),
        ];
    }

    public function gerarDreSimples(): array
    {
        $receitas = 0;
        $despesas = 0;
        foreach ($this->titulos->all() as $titulo) {
            if ($titulo['tipo'] === 'receber') {
                $receitas += $titulo['valor'];
            } else {
                $despesas += $titulo['valor'];
            }
        }
        return [
            'receitas' => $receitas,
            'despesas' => $despesas,
            'resultado' => $receitas - $despesas,
        ];
    }
}
