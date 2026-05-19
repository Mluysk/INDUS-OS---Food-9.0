<?php
namespace App\Services;

use App\Repositories\UsuariosRepository;
use App\Repositories\ClientesRepository;

class PortalService
{
    private UsuariosRepository $usuarios;
    private ClientesRepository $clientes;
    private AuditService $audit;

    public function __construct(UsuariosRepository $usuarios, ClientesRepository $clientes, AuditService $audit)
    {
        $this->usuarios = $usuarios;
        $this->clientes = $clientes;
        $this->audit = $audit;
    }

    public function registrarCliente(array $dados): array
    {
        $nome = trim($dados['nome'] ?? '');
        $email = strtolower(trim($dados['email'] ?? ''));
        $senha = $dados['senha'] ?? '';
        $confirmacao = $dados['confirmacao'] ?? '';
        $telefone = trim($dados['telefone'] ?? '');
        $contatoTipo = ($dados['contato_tipo'] ?? 'celular') === 'telefone' ? 'telefone' : 'celular';
        $whatsapp = trim($dados['whatsapp'] ?? '');
        $cep = preg_replace('/\D+/', '', $dados['cep'] ?? '');
        $endereco = trim($dados['endereco'] ?? '');
        $numero = trim($dados['numero'] ?? '');
        $bairro = trim($dados['bairro'] ?? '');
        $cidade = trim($dados['cidade'] ?? '');
        $estado = strtoupper(trim($dados['estado'] ?? ''));
        $razaoSocial = trim($dados['razao_social'] ?? $nome);
        $nomeFantasia = trim($dados['nome_fantasia'] ?? ($dados['nome_loja'] ?? $nome));
        $temFilial = filter_var($dados['tem_filial'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $nomeFilial = $temFilial ? trim($dados['nome_filial'] ?? '') : '';
        $cnpj = $dados['cnpj'] ?? '';
        $cpf = $dados['cpf'] ?? '';

        if ($nome === '') {
            throw new \InvalidArgumentException('Informe o nome completo.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('E-mail inválido.');
        }
        if ($senha === '' || strlen($senha) < 8) {
            throw new \InvalidArgumentException('Defina uma senha com pelo menos 8 caracteres.');
        }
        if ($senha !== $confirmacao) {
            throw new \InvalidArgumentException('As senhas informadas não conferem.');
        }
        if ($temFilial && $nomeFilial === '') {
            throw new \InvalidArgumentException('Informe o nome da filial.');
        }

        if ($nomeFantasia === '') {
            $nomeFantasia = $nome;
        }
        if ($razaoSocial === '') {
            $razaoSocial = $nomeFantasia;
        }

        $documento = '';
        if ($cnpj) {
            $documento = somente_numeros($cnpj);
            if (!valida_cnpj($documento)) {
                throw new \InvalidArgumentException('CNPJ inválido.');
            }
            $cnpj = $documento;
            $cpf = null;
        } elseif ($cpf) {
            $documento = somente_numeros($cpf);
            if (!valida_cpf($documento)) {
                throw new \InvalidArgumentException('CPF inválido.');
            }
            $cpf = $documento;
            $cnpj = null;
        } else {
            throw new \InvalidArgumentException('Informe um CPF ou CNPJ válido.');
        }

        if ($this->usuarios->findByEmail($email)) {
            throw new \InvalidArgumentException('E-mail já cadastrado.');
        }
        if ($documento && $this->usuarios->findByDocumento($documento)) {
            throw new \InvalidArgumentException('Documento já cadastrado.');
        }
        $releasedUsers = $this->usuarios->releaseInactiveIdentity($documento, $email);
        $releasedClients = $this->clientes->releaseInactivePortalIdentity($documento, $email);
        if ($this->clientes->findByEmail($email)) {
            throw new \InvalidArgumentException('Cliente já cadastrado com este e-mail.');
        }
        if ($documento && $this->clientes->findByDocumento($documento)) {
            throw new \InvalidArgumentException('Cliente já cadastrado com este documento.');
        }

        if ($cep === '' || strlen($cep) !== 8) {
            throw new \InvalidArgumentException('Informe um CEP válido com 8 dígitos.');
        }

        if ($endereco === '' || $numero === '' || $bairro === '' || $cidade === '' || strlen($estado) !== 2) {
            throw new \InvalidArgumentException('Preencha todos os campos de endereço.');
        }

        $cepFormatado = substr($cep, 0, 5) . '-' . substr($cep, 5);

        $hash = password_hash($senha, PASSWORD_ARGON2ID);

        $pdo = $this->usuarios->getConnection();
        $pdo->beginTransaction();
        try {
            $usuarioId = $this->usuarios->create([
                'nome' => $nome,
                'email' => $email,
                'cpf' => $cpf,
                'cnpj' => $cnpj,
                'senha_hash' => $hash,
                'papel' => 'cliente',
                'is_ativo' => 1,
            ]);

            $clienteId = $this->clientes->create([
                'usuario_id' => $usuarioId,
                'razao_social' => $razaoSocial,
                'nome_fantasia' => $nomeFantasia,
                'cnpj' => $cnpj,
                'cpf' => $cpf,
                'email' => $email,
                'telefone' => $telefone,
                'contato_tipo' => $contatoTipo,
                'whatsapp' => $whatsapp,
                'cep' => $cepFormatado,
                'endereco' => $endereco,
                'numero' => $numero,
                'bairro' => $bairro,
                'cidade' => $cidade,
                'estado' => $estado,
                'im' => $nomeFilial ?: null,
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        $this->audit->log($usuarioId, 'create', 'usuarios', $usuarioId, ['papel' => 'cliente']);
        $this->audit->log($usuarioId, 'create', 'clientes', $clienteId, [
            'email' => $email,
            'cadastros_removidos_liberados' => [
                'usuarios' => $releasedUsers,
                'clientes' => $releasedClients,
            ],
        ]);

        return [
            'usuario_id' => $usuarioId,
            'cliente_id' => $clienteId,
        ];
    }
}
