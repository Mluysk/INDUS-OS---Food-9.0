<?php
namespace App\Modules\Meta\Controllers;

use App\Core\Controller;

class MetaController extends Controller
{
    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit
    ) {
        parent::__construct($container, $auth, $audit);
    }

    public function index(): void
    {
        $this->auth->ensureRole(['admin', 'gestor', 'producao', 'expedicao']);

        $this->render('meta/index', [], [
            'title' => 'Metas',
            'pageSubtitle' => 'Defina e acompanhe metas de produção e operação.',
            'breadcrumbs' => ['Planejamento', 'Metas'],
            'activeMenu' => 'metas',
        ]);
    }
}
