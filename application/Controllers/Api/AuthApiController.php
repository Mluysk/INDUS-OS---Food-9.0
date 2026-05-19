<?php
namespace App\Controllers\Api;

use App\Core\Controller;
use App\Repositories\UsuariosRepository;
use App\Repositories\ClientesRepository;

class AuthApiController extends Controller
{
    private UsuariosRepository $usuarios;
    private ClientesRepository $clientes;

    public function __construct(
        \App\Core\Container $container,
        \App\Services\AuthService $auth,
        \App\Services\AuditService $audit,
        UsuariosRepository $usuarios,
        ClientesRepository $clientes
    ) {
        parent::__construct($container, $auth, $audit);
        $this->usuarios = $usuarios;
        $this->clientes = $clientes;
    }

    public function token(): void
    {
        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        $login = $payload['login'] ?? '';
        $senha = $payload['senha'] ?? '';
        $usuario = $this->usuarios->findByEmailOrDocumento($login);
        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            $this->json(['message' => 'Usuário ou senha incorretos.'], 401);
            return;
        }
        if ($usuario['papel'] !== 'cliente') {
            $this->json(['message' => 'Acesso restrito ao cliente'], 403);
            return;
        }
        $cliente = $this->clientes->findByUsuarioId((int)$usuario['id']);
        if ($cliente) {
            $usuario['cliente_id'] = $cliente['id'];
        }
        $token = $this->auth->generateJwt($usuario, 3600);

        $clientePayload = null;
        if ($cliente) {
            $nomeFilial = trim((string)($cliente['im'] ?? ''));
            $clientePayload = [
                'id' => (int) $cliente['id'],
                'razao_social' => $cliente['razao_social'] ?? null,
                'nome_fantasia' => $cliente['nome_fantasia'] ?? ($cliente['razao_social'] ?? ($usuario['nome'] ?? null)),
                'nome_loja' => $cliente['nome_fantasia'] ?? ($cliente['razao_social'] ?? ($usuario['nome'] ?? null)),
                'nome_filial' => $nomeFilial !== '' ? $nomeFilial : null,
                'email' => $cliente['email'] ?? ($usuario['email'] ?? null),
                'telefone' => $cliente['telefone'] ?? null,
                'cep' => $cliente['cep'] ?? null,
                'cidade' => $cliente['cidade'] ?? null,
                'estado' => $cliente['estado'] ?? null,
            ];
        }

        $this->audit->log($usuario['id'], 'jwt_emitido', 'usuarios', $usuario['id'], []);

        $this->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'usuario' => [
                'id' => (int) $usuario['id'],
                'nome' => $usuario['nome'] ?? null,
                'email' => $usuario['email'] ?? null,
            ],
            'cliente' => $clientePayload,
        ]);
    }
}
