<?php
namespace App\Modules\Relatorios\Controllers;

use App\Core\Controller;
use App\Services\RelatoriosService;

class RelatoriosController extends Controller
{
    private RelatoriosService $relatorios;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        RelatoriosService $relatorios
    ) {
        parent::__construct($container, $auth, $audit);
        $this->relatorios = $relatorios;
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin','gestor']);

        $links = [
            [
                'title' => 'Pedidos (PDF)',
                'description' => 'Gere o relatório consolidado de pedidos em PDF para compartilhar com a diretoria ou clientes.',
                'href' => 'relatorios/pedidos/pdf',
                'icon' => 'file-text',
                'target' => '_blank',
            ],
            [
                'title' => 'Estoque (XLSX)',
                'description' => 'Exporte a posição de estoque com validade para tratamento em planilhas ou BI.',
                'href' => 'relatorios/estoque/xlsx',
                'icon' => 'table',
                'target' => '_blank',
            ],
            [
                'title' => 'DRE Simplificada',
                'description' => 'Visualize receitas, despesas e resultado do período diretamente no painel.',
                'href' => 'relatorios/dre',
                'icon' => 'pie-chart',
                'target' => null,
            ],
        ];

        if ($this->wantsJson()) {
            $this->json($links);
            return;
        }

        $this->render('relatorios/index', [
            'links' => $links,
        ], [
            'title' => 'Relatórios',
            'activeMenu' => 'relatorios',
            'breadcrumbs' => ['Relatórios'],
        ]);
    }

    public function pedidosPdf(): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        $arquivo = $this->relatorios->gerarPedidosPdf();
        $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'documento', 'relatorios', null, [
            'documento' => 'relatorio_pedidos',
            'arquivo' => $arquivo['filename'] ?? null,
            'formato' => 'pdf',
        ]);

        if ($this->wantsJson()) {
            $this->json([
                'filename' => $arquivo['filename'],
                'mime' => $arquivo['mime'],
                'content' => base64_encode($arquivo['content']),
            ]);
            return;
        }

        header('Content-Type: ' . $arquivo['mime']);
        header('Content-Disposition: attachment; filename="' . $arquivo['filename'] . '"');
        echo $arquivo['content'];
        exit;
    }

    public function estoqueXlsx(): void
    {
        $this->auth->ensureRole(['admin','gestor']);
        $arquivo = $this->relatorios->gerarEstoqueXlsx();
        $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'export', 'estoque', null, [
            'arquivo' => $arquivo['filename'] ?? null,
            'formato' => 'xlsx',
        ]);

        if ($this->wantsJson()) {
            $this->json([
                'filename' => $arquivo['filename'],
                'mime' => $arquivo['mime'],
                'content' => base64_encode($arquivo['content']),
            ]);
            return;
        }

        header('Content-Type: ' . $arquivo['mime']);
        header('Content-Disposition: attachment; filename="' . $arquivo['filename'] . '"');
        echo $arquivo['content'];
        exit;
    }

    public function dre(): void
    {
        $this->auth->ensureRole(['admin','gestor','financeiro']);
        $dre = $this->relatorios->gerarDreSimples();

        if ($this->wantsJson()) {
            $this->json($dre);
            return;
        }

        $this->render('relatorios/dre', [
            'dre' => $dre,
        ], [
            'title' => 'Relatórios',
            'activeMenu' => 'relatorios',
            'breadcrumbs' => ['Relatórios', 'DRE'],
        ]);
    }
}
