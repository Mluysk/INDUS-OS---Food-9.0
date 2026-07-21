<?php
namespace App\Services;

use App\Repositories\ExpedicoesRepository;
use App\Repositories\PedidosRepository;
use App\Repositories\PedidoItensRepository;

class ExpedicaoService
{
    private ExpedicoesRepository $expedicoes;
    private PedidosRepository $pedidos;
    private PedidoItensRepository $itens;
    private EstoqueService $estoque;
    private AuditService $audit;
    private ConfigService $config;

    public function __construct(
        ExpedicoesRepository $expedicoes,
        PedidosRepository $pedidos,
        PedidoItensRepository $itens,
        EstoqueService $estoque,
        AuditService $audit,
        ConfigService $config
    ) {
        $this->expedicoes = $expedicoes;
        $this->pedidos = $pedidos;
        $this->itens = $itens;
        $this->estoque = $estoque;
        $this->audit = $audit;
        $this->config = $config;
    }

    public function proximoRomaneio(): string
    {
        $digits = max(2, min(10, (int)($this->config->get('ROMANEIO_DIGITS', '4') ?: 4)));
        return 'ROM-' . str_pad((string)($this->expedicoes->maiorSequencialRomaneio() + 1), $digits, '0', STR_PAD_LEFT);
    }

    public function gerarPicking(int $pedidoId, int $usuarioId = 0): array
    {
        $pedido = $this->pedidos->find($pedidoId);
        if (!$pedido) {
            throw new \InvalidArgumentException('Pedido não encontrado.');
        }
        $statusAtual = strtolower((string)($pedido['status'] ?? ''));
        if (!in_array($statusAtual, ['producao', 'separando', 'separacao'], true)) {
            throw new \InvalidArgumentException('Somente pedidos aprovados podem iniciar o picking.');
        }
        if ($statusAtual === 'producao') {
            $this->pedidos->update($pedidoId, ['status' => 'separando']);
            $this->audit->log($usuarioId, 'status', 'pedidos', $pedidoId, $this->audit->withChanges($pedido, ['status' => 'separando', 'origem' => 'abertura_picking']));
            $pedido['status'] = 'separando';
        }
        $itens = $this->itens->listarPorPedido($pedidoId);
        return array_merge(['pedido' => $pedido, 'itens' => $itens], $this->resumoConferencia($itens));
    }

    public function conferirItemPicking(int $pedidoId, int $itemId, bool|string $conferido, int $usuarioId, string $observacao = '', string $motivo = '', ?float $novoValor = null): array
    {
        $pedido = $this->pedidos->find($pedidoId);
        if (!$pedido) {
            throw new \InvalidArgumentException('Pedido não encontrado.');
        }
        $statusAtual = strtolower((string)($pedido['status'] ?? ''));
        if (!in_array($statusAtual, ['producao', 'separando', 'separacao'], true)) {
            throw new \InvalidArgumentException('Este pedido não permite mais alterações no Picking.');
        }
        $estado = is_string($conferido) ? strtolower($conferido) : ($conferido ? 'ok' : 'pendente');
        if ($estado === 'x' && trim($observacao) === '') throw new \InvalidArgumentException('Informe a observação da inconformidade.');
        if ($estado === 'x' && !in_array($motivo,['falta_produto','erro'],true)) throw new \InvalidArgumentException('Selecione o motivo da inconformidade.');
        if ($estado === 'x' && $motivo === 'falta_produto' && ($novoValor === null || $novoValor < 0)) throw new \InvalidArgumentException('Informe o novo valor ajustado.');
        if (!$this->itens->atualizarStatusPicking($itemId, $pedidoId, $estado, $observacao, $motivo, $novoValor)) {
            throw new \InvalidArgumentException('Produto não encontrado neste pedido.');
        }

        $itens = $this->itens->listarPorPedido($pedidoId);
        $resumo = $this->resumoConferencia($itens);
        $novoStatus = $resumo['todos_conferidos'] ? 'separacao' : 'separando';
        if ($novoStatus !== $statusAtual) {
            $this->pedidos->update($pedidoId, ['status' => $novoStatus]);
            $this->audit->log($usuarioId, 'status', 'pedidos', $pedidoId, $this->audit->withChanges($pedido, [
                'status' => $novoStatus,
                'origem' => 'picking_conferencia',
            ]));
        }

        return array_merge($resumo, [
            'status' => $novoStatus,
            'status_label' => $resumo['todos_conferidos'] ? 'Separado' : 'Separando',
        ]);
    }

    public function enviarParaRevisao(int $pedidoId, int $usuarioId): void
    {
        $pedido = $this->pedidos->find($pedidoId);
        if (!$pedido) throw new \InvalidArgumentException('Pedido não encontrado.');
        $itens = $this->itens->listarPorPedido($pedidoId);
        $inconformidades = array_values(array_map(static fn(array $item): array => [
            'produto_id' => (int)($item['produto_id'] ?? 0),
            'sku' => (string)($item['sku'] ?? ''),
            'produto' => (string)($item['nome'] ?? ('Produto #'.($item['produto_id'] ?? 0))),
            'quantidade' => (float)($item['quantidade'] ?? 0),
            'observacao' => (string)($item['picking_observacao'] ?? 'Sem observação'),
            'motivo' => (string)($item['picking_motivo'] ?? 'erro'),
            'novo_valor' => $item['picking_novo_valor'] ?? null,
            'marcacao' => 'X',
        ], array_filter($itens, static fn(array $item): bool => ($item['picking_status'] ?? '') === 'x')));
        if (!$inconformidades) throw new \InvalidArgumentException('O pedido não possui inconformidades para revisão.');
        $this->pedidos->update($pedidoId, ['status'=>'revisao']);
        $this->audit->log($usuarioId,'status','pedidos',$pedidoId,$this->audit->withChanges($pedido,[
            'status'=>'revisao',
            'origem'=>'expedicao_picking_inconforme',
            'pedido_id'=>$pedidoId,
            'total_inconformidades'=>count($inconformidades),
            'produtos_inconformes'=>$inconformidades,
        ]));
    }

    public function registrarPendenciaInformacao(int $pedidoId, string $tipo, string $observacao, int $usuarioId): void
    {
        $pedido = $this->pedidos->find($pedidoId);
        if (!$pedido) throw new \InvalidArgumentException('Pedido não encontrado.');
        $status = strtolower((string)($pedido['status'] ?? ''));
        if (!in_array($status, ['producao','separando','separacao'], true)) {
            throw new \InvalidArgumentException('Este pedido não permite registrar pendências de informação.');
        }
        $tipos = [
            'frete' => 'Frete ou transportadora',
            'entrega' => 'Endereço ou dados de entrega',
            'documentacao' => 'Documentação',
            'pagamento' => 'Pagamento ou cobrança',
            'outro' => 'Outra informação',
        ];
        if (!isset($tipos[$tipo])) throw new \InvalidArgumentException('Selecione o tipo de informação pendente.');
        $observacao = trim($observacao);
        if ($observacao === '') throw new \InvalidArgumentException('Descreva qual informação está faltando.');
        if (mb_strlen($observacao) > 500) throw new \InvalidArgumentException('A descrição deve ter no máximo 500 caracteres.');

        $this->pedidos->update($pedidoId, ['status' => 'revisao']);
        $this->audit->log($usuarioId, 'status', 'pedidos', $pedidoId, $this->audit->withChanges($pedido, [
            'status' => 'revisao',
            'origem' => 'picking_pendencia_informacao',
            'tipo_pendencia' => $tipo,
            'tipo_pendencia_label' => $tipos[$tipo],
            'observacao_pendencia' => $observacao,
        ]));
    }

    private function resumoConferencia(array $itens): array
    {
        $total = count($itens);
        $conferidos = count(array_filter($itens, static fn(array $item): bool => ($item['picking_status'] ?? ((int)($item['picking_conferido'] ?? 0)===1?'ok':'pendente')) === 'ok'));
        $divergentes = count(array_filter($itens, static fn(array $item): bool => ($item['picking_status'] ?? '') === 'x'));
        return [
            'total_itens' => $total,
            'itens_conferidos' => $conferidos,
            'itens_divergentes' => $divergentes,
            'todos_conferidos' => $total > 0 && $conferidos === $total,
            'progresso_percentual' => $total > 0 ? round(($conferidos / $total) * 100, 1) : 0,
        ];
    }

    public function registrarExpedicao(int $pedidoId, array $dados, int $usuarioId, bool $converteReservas): int
    {
        $pedido = $this->pedidos->find($pedidoId);
        if (!$pedido) {
            throw new \InvalidArgumentException('Pedido não encontrado.');
        }
        if (strtolower((string)($pedido['status'] ?? '')) !== 'separacao') {
            throw new \InvalidArgumentException('Somente pedidos em separação podem gerar romaneio.');
        }
        if ($this->expedicoes->all(['pedido_id' => $pedidoId])) {
            throw new \InvalidArgumentException('Este pedido já possui uma expedição registrada.');
        }
        if ($converteReservas) {
            foreach ($this->itens->listarPorPedido($pedidoId) as $item) {
                $this->estoque->reservarFefo($item['produto_id'], (float)$item['quantidade'], 'pedido', $pedidoId, $usuarioId, true);
            }
        }
        $payload = [
            'pedido_id' => $pedidoId,
            'data_separacao' => $dados['data_separacao'] ?? date('Y-m-d H:i:s'),
            'data_expedicao' => date('Y-m-d H:i:s'),
            'volumes' => max(1, (int)($dados['volumes'] ?? 1)),
            'peso_total' => max(0, (float)($dados['peso_total'] ?? 0)),
            'transportadora' => mb_substr(trim((string)($dados['transportadora'] ?? '')), 0, 120),
            'romaneio' => $this->proximoRomaneio(),
            'motorista_nome' => mb_substr(trim((string)($dados['motorista_nome'] ?? '')), 0, 120),
            'assinatura_motorista' => $this->validarAssinatura((string)($dados['assinatura_motorista'] ?? '')),
            'responsavel_expedicao_nome' => mb_substr(trim((string)($dados['responsavel_expedicao_nome'] ?? '')), 0, 120),
            'assinatura_responsavel' => $this->validarAssinatura((string)($dados['assinatura_responsavel'] ?? '')),
        ];
        if ($payload['motorista_nome'] === '' || $payload['responsavel_expedicao_nome'] === '') {
            throw new \InvalidArgumentException('Informe o motorista e o responsável pela expedição.');
        }
        if ($payload['assinatura_motorista'] === '' || $payload['assinatura_responsavel'] === '') {
            throw new \InvalidArgumentException('As assinaturas do motorista e do responsável são obrigatórias.');
        }
        $id = $this->expedicoes->create($payload);
        $this->pedidos->update($pedidoId, ['status' => 'expedicao']);
        $this->audit->log($usuarioId, 'expedir', 'pedidos', $pedidoId, $payload);
        return $id;
    }

    private function validarAssinatura(string $assinatura): string
    {
        $assinatura = trim($assinatura);
        if ($assinatura === '') return '';
        if (!preg_match('#^data:image/png;base64,[A-Za-z0-9+/=]+$#', $assinatura) || strlen($assinatura) > 700000) {
            throw new \InvalidArgumentException('Assinatura digital inválida. Tente assinar novamente.');
        }
        return $assinatura;
    }

    public function listarAguardandoRecebimento(): array
    {
        $aguardando = [];
        foreach ($this->expedicoes->all() as $expedicao) {
            if (!empty($expedicao['data_recebimento'])) continue;
            $pedido = $this->pedidos->findWithCliente((int)($expedicao['pedido_id'] ?? 0));
            if (!$pedido || !in_array(strtolower((string)($pedido['status'] ?? '')), ['expedicao', 'faturado'], true)) continue;
            $expedicao['pedido'] = $pedido;
            $aguardando[] = $expedicao;
        }
        usort($aguardando, static fn(array $a, array $b): int => strcmp((string)($b['data_expedicao'] ?? ''), (string)($a['data_expedicao'] ?? '')));
        return $aguardando;
    }

    public function confirmarRecebimento(int $expedicaoId, array $dados, int $usuarioId): void
    {
        $expedicao = $this->expedicoes->find($expedicaoId);
        if (!$expedicao) throw new \InvalidArgumentException('Expedição não encontrada.');
        if (!empty($expedicao['data_recebimento'])) throw new \InvalidArgumentException('O recebimento desta expedição já foi confirmado.');
        $pedido = $this->pedidos->find((int)($expedicao['pedido_id'] ?? 0));
        if (!$pedido) throw new \InvalidArgumentException('Pedido relacionado não encontrado.');
        $recebedor = mb_substr(trim((string)($dados['recebedor_nome'] ?? '')), 0, 120);
        $assinatura = $this->validarAssinatura((string)($dados['assinatura_recebedor'] ?? ''));
        if ($recebedor === '' || $assinatura === '') throw new \InvalidArgumentException('Informe o responsável e faça a assinatura do recebimento.');
        $payload = [
            'recebedor_nome' => $recebedor,
            'assinatura_recebedor' => $assinatura,
            'data_recebimento' => date('Y-m-d H:i:s'),
            'observacao_recebimento' => mb_substr(trim((string)($dados['observacao_recebimento'] ?? '')), 0, 500),
        ];
        $this->expedicoes->update($expedicaoId, $payload);
        $this->pedidos->update((int)$pedido['id'], ['status' => 'entregue']);
        $this->audit->log($usuarioId, 'receber', 'pedidos', (int)$pedido['id'], $payload + ['expedicao_id' => $expedicaoId, 'status' => 'entregue']);
    }

    public function detalheConcluido(int $expedicaoId): array
    {
        $expedicao = $this->expedicoes->find($expedicaoId);
        if (!$expedicao) throw new \InvalidArgumentException('Expedição não encontrada.');
        $pedido = $this->pedidos->findWithCliente((int)($expedicao['pedido_id'] ?? 0));
        if (!$pedido) throw new \InvalidArgumentException('Pedido relacionado não encontrado.');
        return ['expedicao' => $expedicao, 'pedido' => $pedido, 'itens' => $this->itens->listarPorPedido((int)$pedido['id'])];
    }

    public function listarFila(): array
    {
        $fila = [];
        foreach ($this->pedidos->all() as $pedido) {
            if (in_array(strtolower((string)($pedido['status'] ?? '')), ['producao', 'separando', 'separacao'], true)) {
                $pedido = $this->pedidos->findWithCliente((int)$pedido['id']) ?: $pedido;
                $pedido['itens'] = $this->itens->listarPorPedido($pedido['id']);
                $fila[] = $pedido;
            }
        }
        return $fila;
    }

    public function listarDisponiveisRomaneio(): array
    {
        $pedidos = [];
        foreach ($this->pedidos->all() as $pedido) {
            if (strtolower((string)($pedido['status'] ?? '')) !== 'separacao') continue;
            $pedido = $this->pedidos->findWithCliente((int)$pedido['id']) ?: $pedido;
            $pedido['itens'] = $this->itens->listarPorPedido((int)$pedido['id']);
            if (!$this->resumoConferencia($pedido['itens'])['todos_conferidos']) continue;
            $pedidos[] = $pedido;
        }
        return $pedidos;
    }

    public function listarConcluidos(): array
    {
        $concluidos = [];
        foreach ($this->expedicoes->all() as $expedicao) {
            $pedido = $this->pedidos->findWithCliente((int)($expedicao['pedido_id'] ?? 0));
            if (!$pedido || !in_array(strtolower((string)($pedido['status'] ?? '')), ['expedicao', 'faturado', 'entregue'], true)) continue;
            $expedicao['pedido'] = $pedido;
            $expedicao['itens'] = $this->itens->listarPorPedido((int)$pedido['id']);
            $concluidos[] = $expedicao;
        }
        usort($concluidos, static fn(array $a, array $b): int => strcmp((string)($b['data_expedicao'] ?? ''), (string)($a['data_expedicao'] ?? '')));
        return $concluidos;
    }
}
