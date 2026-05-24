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
            $clientePayload = $this->clientePayload($cliente, $usuario);
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

    public function cliente(): void
    {
        $decoded = $this->ensureJwt();
        $clienteId = (int)($decoded['cliente_id'] ?? 0);
        $usuarioId = (int)($decoded['sub'] ?? 0);
        if ($clienteId <= 0 || $usuarioId <= 0) {
            $this->json(['message' => 'Cliente nÃ£o associado ao token'], 422);
            return;
        }

        $cliente = $this->clientes->find($clienteId);
        $usuario = $this->usuarios->find($usuarioId) ?: [];
        if (!$cliente || (int)($cliente['usuario_id'] ?? 0) !== $usuarioId) {
            $this->json(['message' => 'Cliente nÃ£o encontrado'], 404);
            return;
        }

        $this->json(['cliente' => $this->clientePayload($cliente, $usuario)]);
    }

    public function updateCliente(): void
    {
        $decoded = $this->ensureJwt();
        $clienteId = (int)($decoded['cliente_id'] ?? 0);
        $usuarioId = (int)($decoded['sub'] ?? 0);
        if ($clienteId <= 0 || $usuarioId <= 0) {
            $this->json(['message' => 'Cliente nÃ£o associado ao token'], 422);
            return;
        }

        $cliente = $this->clientes->find($clienteId);
        if (!$cliente || (int)($cliente['usuario_id'] ?? 0) !== $usuarioId) {
            $this->json(['message' => 'Cliente nÃ£o encontrado'], 404);
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        $hex = static fn($value): ?string => preg_match('/^#?[0-9a-f]{6}$/i', (string)$value)
            ? '#'.strtoupper(ltrim((string)$value, '#'))
            : null;
        $clean = static fn($value, int $max = 160): ?string => trim((string)$value) !== ''
            ? substr(trim((string)$value), 0, $max)
            : null;

        $data = [
            'razao_social' => $clean($payload['razao_social'] ?? $cliente['razao_social'] ?? null),
            'nome_fantasia' => $clean($payload['nome_fantasia'] ?? $payload['nome_loja'] ?? $cliente['nome_fantasia'] ?? null),
            'email' => $clean($payload['email'] ?? $cliente['email'] ?? null),
            'telefone' => $clean($payload['telefone'] ?? $cliente['telefone'] ?? null, 30),
            'contato_tipo' => (($payload['contato_tipo'] ?? 'celular') === 'telefone') ? 'telefone' : 'celular',
            'whatsapp' => $clean($payload['whatsapp'] ?? $cliente['whatsapp'] ?? null, 30),
            'cep' => $clean($payload['cep'] ?? $cliente['cep'] ?? null, 9),
            'endereco' => $clean($payload['endereco'] ?? $cliente['endereco'] ?? null),
            'numero' => $clean($payload['numero'] ?? $cliente['numero'] ?? null, 20),
            'bairro' => $clean($payload['bairro'] ?? $cliente['bairro'] ?? null, 120),
            'cidade' => $clean($payload['cidade'] ?? $cliente['cidade'] ?? null, 120),
            'estado' => strtoupper((string)($clean($payload['estado'] ?? $cliente['estado'] ?? null, 2) ?? '')),
            'im' => $clean($payload['nome_filial'] ?? $payload['filial_nome'] ?? $cliente['im'] ?? null, 30),
            'portal_palette_hex' => $hex($payload['portal_palette_hex'] ?? null),
            'portal_palette_name' => $clean($payload['portal_palette_name'] ?? null, 80),
            'portal_cart_hex' => $hex($payload['portal_cart_hex'] ?? null),
        ];

        $this->clientes->updatePortalProfile($clienteId, $data);
        $clienteAtualizado = $this->clientes->find($clienteId) ?: $cliente;
        $usuario = $this->usuarios->find($usuarioId) ?: [];

        $this->json([
            'message' => 'Cadastro atualizado.',
            'cliente' => $this->clientePayload($clienteAtualizado, $usuario),
        ]);
    }

    private function clientePayload(array $cliente, array $usuario = []): array
    {
        $nomeFilial = trim((string)($cliente['im'] ?? ''));
        return [
            'id' => (int) $cliente['id'],
            'razao_social' => $cliente['razao_social'] ?? null,
            'nome_fantasia' => $cliente['nome_fantasia'] ?? ($cliente['razao_social'] ?? ($usuario['nome'] ?? null)),
            'nome_loja' => $cliente['nome_fantasia'] ?? ($cliente['razao_social'] ?? ($usuario['nome'] ?? null)),
            'nome_filial' => $nomeFilial !== '' ? $nomeFilial : null,
            'filial_nome' => $nomeFilial !== '' ? $nomeFilial : null,
            'email' => $cliente['email'] ?? ($usuario['email'] ?? null),
            'telefone' => $cliente['telefone'] ?? null,
            'contato' => $cliente['telefone'] ?? null,
            'contato_tipo' => $cliente['contato_tipo'] ?? 'celular',
            'whatsapp' => $cliente['whatsapp'] ?? null,
            'cep' => $cliente['cep'] ?? null,
            'endereco' => $cliente['endereco'] ?? null,
            'numero' => $cliente['numero'] ?? null,
            'bairro' => $cliente['bairro'] ?? null,
            'cidade' => $cliente['cidade'] ?? null,
            'estado' => $cliente['estado'] ?? null,
            'portal_palette_hex' => $cliente['portal_palette_hex'] ?? null,
            'portal_palette_name' => $cliente['portal_palette_name'] ?? null,
            'portal_cart_hex' => $cliente['portal_cart_hex'] ?? null,
        ];
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
            $this->json(['message' => 'Token invÃ¡lido ou expirado'], 401);
            exit;
        }

        return $decoded;
    }
}
