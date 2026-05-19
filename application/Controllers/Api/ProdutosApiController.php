<?php
namespace App\Controllers\Api;

use App\Core\Controller;
use App\Services\ProdutosService;

class ProdutosApiController extends Controller
{
    private ProdutosService $produtos;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        ProdutosService $produtos
    ) {
        parent::__construct($container, $auth, $audit);
        $this->produtos = $produtos;
    }

    public function index(): void
    {
        $this->ensureJwt();
        $this->json($this->produtos->all());
    }

    private function ensureJwt(): array
    {
        $token = $this->extractBearerToken();
        if (!$token) {
            $this->json(['message' => 'Token ausente'], 401);
            exit;
        }

        $decoded = $this->auth->validateJwt($token);
        if (!$decoded) {
            $this->json(['message' => 'Token inválido ou expirado'], 401);
            exit;
        }

        return $decoded;
    }
}
