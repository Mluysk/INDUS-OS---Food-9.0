<?php
namespace App\Services;

use App\Repositories\FinanceiroLancamentosRepository;
use App\Repositories\FinanceiroTitulosRepository;

class FinanceiroService
{
    private FinanceiroTitulosRepository $titulos;
    private FinanceiroLancamentosRepository $lancamentos;
    private ConfigService $config;
    private AuditService $audit;

    public function __construct(
        FinanceiroTitulosRepository $titulos,
        FinanceiroLancamentosRepository $lancamentos,
        ConfigService $config,
        AuditService $audit
    ) {
        $this->titulos = $titulos;
        $this->lancamentos = $lancamentos;
        $this->config = $config;
        $this->audit = $audit;
    }

    public function criarTitulo(array $dados, int $usuarioId): int
    {
        $id = $this->titulos->create($dados);
        $this->audit->log($usuarioId, 'create', 'financeiro_titulos', $id, $dados);
        return $id;
    }

    public function baixarTitulo(int $id, float $valorPago, int $usuarioId): void
    {
        $titulo = $this->titulos->find($id);
        if (!$titulo) {
            throw new \RuntimeException('Título não encontrado');
        }
        $novoPago = ($titulo['valor_pago'] ?? 0) + $valorPago;
        $status = $novoPago >= $titulo['valor'] ? 'pago' : 'parcial';
        $this->titulos->update($id, [
            'valor_pago' => $novoPago,
            'status' => $status,
        ]);
        $this->audit->log($usuarioId, 'baixa', 'financeiro_titulos', $id, ['valor_pago' => $novoPago]);
    }

    public function criarLancamentoEntrada(array $dados, int $usuarioId): int
    {
        $payload = $this->normalizarLancamento($dados, 'entrada');
        $id = $this->lancamentos->create($payload);
        $this->audit->log($usuarioId, 'create', 'financeiro_lancamentos', $id, $payload);
        return $id;
    }

    public function criarLancamentoSaida(array $dados, int $usuarioId): int
    {
        $payload = $this->normalizarLancamento($dados, 'saida');
        $id = $this->lancamentos->create($payload);
        $this->audit->log($usuarioId, 'create', 'financeiro_lancamentos', $id, $payload);
        return $id;
    }

    public function listarLancamentos(string $tipo): array
    {
        return array_map(fn(array $lancamento): array => $this->aplicarStatusVencimento($lancamento), $this->lancamentos->porTipo($tipo));
    }

    public function marcarLancamentoPago(int $id, int $usuarioId): void
    {
        $lancamento = $this->lancamentos->find($id);
        if (!$lancamento) {
            throw new \RuntimeException('Lançamento não encontrado');
        }

        $this->lancamentos->marcarPago($id);
        $this->audit->log($usuarioId, 'pago', 'financeiro_lancamentos', $id, ['status' => 'pago']);
    }

    private function aplicarStatusVencimento(array $lancamento): array
    {
        if (($lancamento['status'] ?? 'aberto') === 'pago') {
            $lancamento['status_vencimento'] = 'pago';
            $lancamento['status_vencimento_label'] = 'Pago';
            $lancamento['status_vencimento_class'] = 'finance-status-paid';
            return $lancamento;
        }

        $dataBase = $lancamento['validade'] ?? null;

        $status = $this->calcularStatusVencimento($dataBase);
        $lancamento['status_vencimento'] = $status['key'];
        $lancamento['status_vencimento_label'] = $status['label'];
        $lancamento['status_vencimento_class'] = $status['class'];
        return $lancamento;
    }

    private function calcularStatusVencimento(?string $data): array
    {
        $vencimento = $this->normalizarData($data);
        if ($vencimento === null) {
            return ['key' => 'sem_data', 'label' => 'Sem data', 'class' => 'finance-status-warning'];
        }

        $hoje = new \DateTimeImmutable('today');
        $dataVencimento = new \DateTimeImmutable($vencimento);
        if ($dataVencimento < $hoje) {
            return ['key' => 'vencido', 'label' => 'Vencido', 'class' => 'finance-status-danger'];
        }

        $diasAviso = (int)($this->config->get('FINANCEIRO_ALERTA_VENCIMENTO_DIAS', '7') ?: '7');
        $diasAviso = max(0, min(365, $diasAviso));
        $limiteAviso = $hoje->modify('+' . $diasAviso . ' days');
        if ($dataVencimento <= $limiteAviso) {
            return ['key' => 'a_vencer', 'label' => 'A vencer', 'class' => 'finance-status-warning'];
        }

        return ['key' => 'em_dia', 'label' => 'Em dia', 'class' => 'finance-status-success'];
    }

    private function normalizarLancamento(array $dados, string $tipo): array
    {
        $numeroTitulo = trim((string)($dados['numero_titulo'] ?? ''));
        $nomeEmpresa = trim((string)($dados['nome_empresa'] ?? ''));

        if ($numeroTitulo === '') {
            throw new \InvalidArgumentException('Informe o Nº do título.');
        }

        if (!preg_match('/^\d{1,8}$/', $numeroTitulo)) {
            throw new \InvalidArgumentException('Informe o Nº do título com até 8 dígitos.');
        }

        if ($nomeEmpresa === '') {
            throw new \InvalidArgumentException('Informe o nome da empresa.');
        }

        $payload = [
            'tipo' => $tipo,
            'numero_titulo' => $numeroTitulo,
            'nome_empresa' => $nomeEmpresa,
            'validade' => $this->normalizarData($dados['validade'] ?? null),
            'valor' => isset($dados['valor']) && $dados['valor'] !== '' ? (float)$dados['valor'] : null,
            'data_lancamento' => $this->normalizarData($dados['data_lancamento'] ?? null) ?: date('Y-m-d'),
            'data_entrada' => $this->normalizarData($dados['data_entrada'] ?? null),
            'status' => 'aberto',
        ];

        if ($payload['valor'] !== null && $payload['valor'] > 9999999999.99) {
            throw new \InvalidArgumentException('Informe o valor com até 10 dígitos.');
        }

        if ($tipo === 'entrada') {
            if ($payload['validade'] === null) {
                throw new \InvalidArgumentException('Informe a validade da entrada.');
            }
            if ($payload['valor'] === null || $payload['valor'] <= 0) {
                throw new \InvalidArgumentException('Informe um valor válido para a entrada.');
            }
            $payload['data_entrada'] = null;
        }

        if ($tipo === 'saida') {
            if ($payload['validade'] === null) {
                throw new \InvalidArgumentException('Informe a validade da saída.');
            }
            if ($payload['valor'] === null || $payload['valor'] <= 0) {
                throw new \InvalidArgumentException('Informe um valor válido para a saída.');
            }
            if ($payload['data_entrada'] === null) {
                throw new \InvalidArgumentException('Informe a data da saída.');
            }
        }

        return $payload;
    }

    private function normalizarData(mixed $valor): ?string
    {
        $data = trim((string)($valor ?? ''));
        if ($data === '') {
            return null;
        }

        $dt = \DateTimeImmutable::createFromFormat('Y-m-d', $data);
        return $dt ? $dt->format('Y-m-d') : null;
    }

    public function listar(array $filtros = []): array
    {
        return $this->titulos->all($filtros);
    }
}
