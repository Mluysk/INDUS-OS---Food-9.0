<?php
namespace App\Services;

use App\Repositories\OrdensProducaoRepository;
use App\Repositories\ProdutosRepository;
use App\Repositories\ProdutoComponentesRepository;
use App\Repositories\MapaEntradasRepository;

class ProducaoService
{
    private OrdensProducaoRepository $ordens;
    private ProdutosRepository $produtos;
    private ProdutoComponentesRepository $componentes;
    private MapaEntradasRepository $mapaEntradas;
    private EstoqueService $estoque;
    private AuditService $audit;
    private \PDO $db;

    public function __construct(
        OrdensProducaoRepository $ordens,
        ProdutosRepository $produtos,
        ProdutoComponentesRepository $componentes,
        EstoqueService $estoque,
        AuditService $audit,
        MapaEntradasRepository $mapaEntradas
    ) {
        $this->ordens = $ordens;
        $this->produtos = $produtos;
        $this->componentes = $componentes;
        $this->estoque = $estoque;
        $this->audit = $audit;
        $this->mapaEntradas = $mapaEntradas;
        $this->db = $produtos->getConnection();
    }

    public function iniciar(int $opId, int $usuarioId): void
    {
        $op = $this->ordens->find($opId);
        if (!$op) {
            throw new \RuntimeException('OP não encontrada');
        }
        $produto = $this->produtos->find($op['produto_id']);
        $componentes = $this->componentes->listarComponentes($op['produto_id']);
        foreach ($componentes as $componente) {
            $necessario = ($componente['quantidade'] * $op['quantidade']) / max(1, $op['rendimento_percent'] ?: 100);
            $this->estoque->reservarFefo((int)$componente['componente_id'], (float)$necessario, 'op', $opId, $usuarioId, true);
        }
        $this->ordens->update($opId, [
            'etapa' => 'em_andamento',
            'inicio_real' => date('Y-m-d H:i:s'),
        ]);
        $this->audit->log($usuarioId, 'iniciar', 'ordens_producao', $opId, []);
    }

    public function finalizar(int $opId, string $lote, ?string $validade, int $usuarioId): void
    {
        $op = $this->ordens->find($opId);
        if (!$op) {
            throw new \RuntimeException('OP não encontrada');
        }
        $this->estoque->registrarMovimento([
            'produto_id' => $op['produto_id'],
            'tipo' => 'entrada',
            'status' => 'efetivado',
            'quantidade' => $op['quantidade'],
            'lote' => $lote,
            'validade' => $validade,
            'ref_tipo' => 'op',
            'ref_id' => $opId,
            'usuario_id' => $usuarioId,
        ], $usuarioId);
        $this->ordens->update($opId, [
            'etapa' => 'finalizada',
            'lote' => $lote,
            'validade' => $validade,
            'fim_real' => date('Y-m-d H:i:s'),
        ]);
        $this->audit->log($usuarioId, 'finalizar', 'ordens_producao', $opId, ['lote' => $lote]);
    }

    public function criar(array $dados, int $usuarioId): int
    {
        $id = $this->ordens->create($dados);
        $this->audit->log($usuarioId, 'create', 'ordens_producao', $id, $dados);
        return $id;
    }

    public function listar(): array
    {
        $sql = "SELECT op.*, p.nome AS produto_nome, p.sku, p.unidade,
                       COALESCE(op.inicio_previsto, op.created_at) AS data_op
                FROM ordens_producao op
                JOIN produtos p ON p.id = op.produto_id
                ORDER BY op.created_at DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function planejamento(): array
    {
        $sql = "SELECT p.id, p.sku, p.nome, p.unidade, p.shelf_life_dias, COALESCE(SUM(e.quantidade),0) AS estoque,
                       COALESCE(SUM(e.reservado),0) AS reservado,
                       MIN(e.validade) AS validade_proxima,
                       (SELECT MAX(m.created_at) FROM movimentos_estoque m WHERE m.produto_id = p.id) AS atualizado
                FROM produtos p
                LEFT JOIN estoques e ON e.produto_id = p.id
                WHERE p.tipo = 'PA'
                GROUP BY p.id, p.sku, p.nome, p.unidade, p.shelf_life_dias
                ORDER BY p.nome ASC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function planejadoMapa(string $inicio, string $fim): array
    {
        $rows = $this->mapaEntradas->planejadoDetalhado($inicio, $fim);
        $agrupado = [];

        foreach ($rows as $row) {
            $pid = (int)($row['produto_id'] ?? 0);
            if (!isset($agrupado[$pid])) {
                $agrupado[$pid] = [
                    'produto_id' => $pid,
                    'sku' => $row['sku'] ?? '',
                    'nome' => $row['nome'] ?? '',
                    'unidade' => $row['unidade'] ?? '',
                    'dias' => [],
                ];
            }

            $agrupado[$pid]['dias'][] = [
                'data' => $row['data_referencia'],
                'quantidade' => (float)($row['quantidade'] ?? 0),
            ];
        }

        return array_values($agrupado);
    }
}
