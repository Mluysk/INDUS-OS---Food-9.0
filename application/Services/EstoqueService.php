<?php
namespace App\Services;

use App\Repositories\EstoquesRepository;
use App\Repositories\MovimentosEstoqueRepository;
use App\Repositories\ConfiguracoesRepository;

class EstoqueService
{
    private EstoquesRepository $estoques;
    private MovimentosEstoqueRepository $movimentos;
    private ConfiguracoesRepository $configuracoes;
    private AuditService $audit;

    public function __construct(EstoquesRepository $estoques, MovimentosEstoqueRepository $movimentos, ConfiguracoesRepository $configuracoes, AuditService $audit)
    {
        $this->estoques = $estoques;
        $this->movimentos = $movimentos;
        $this->configuracoes = $configuracoes;
        $this->audit = $audit;
    }

    public function registrarMovimento(array $dados, int $usuarioId): int
    {
        $id = $this->movimentos->create($dados);
        $this->audit->log($usuarioId, 'movimento', 'estoque', $id, $dados);
        $conn = $this->estoques->getConnection();
        $stmt = $conn->prepare('SELECT * FROM estoques WHERE produto_id = :produto AND lote <=> :lote AND IFNULL(localizacao, "") = IFNULL(:localizacao, "")');
        $stmt->execute([
            'produto' => $dados['produto_id'],
            'lote' => $dados['lote'] ?? null,
            'localizacao' => $dados['destino'] ?? $dados['origem'] ?? null,
        ]);
        $estoque = $stmt->fetch();
        if (!$estoque) {
            $insert = $conn->prepare('INSERT INTO estoques (produto_id, lote, validade, localizacao, quantidade, reservado, custo_medio) VALUES (:produto_id, :lote, :validade, :localizacao, 0, 0, :custo)');
            $insert->execute([
                'produto_id' => $dados['produto_id'],
                'lote' => $dados['lote'] ?? null,
                'validade' => $dados['validade'] ?? null,
                'localizacao' => $dados['destino'] ?? $dados['origem'] ?? null,
                'custo' => $dados['custo_unit'] ?? 0,
            ]);
            $estoqueId = (int)$conn->lastInsertId();
        } else {
            $estoqueId = $estoque['id'];
        }

        $quantidade = (float)$dados['quantidade'];
        if ($dados['tipo'] === 'entrada') {
            $conn->prepare('UPDATE estoques SET quantidade = quantidade + :qtd, validade = IFNULL(:validade, validade), custo_medio = :custo WHERE id = :id')
                ->execute([
                    'qtd' => $quantidade,
                    'validade' => $dados['validade'] ?? null,
                    'custo' => $dados['custo_unit'] ?? 0,
                    'id' => $estoqueId,
                ]);
        } elseif ($dados['tipo'] === 'saida' && ($dados['status'] ?? 'efetivado') === 'efetivado') {
            // Permite saldos negativos quando há saída sem estoque disponível
            $conn->prepare('UPDATE estoques SET quantidade = quantidade - :qtd WHERE id = :id')
                ->execute(['qtd' => $quantidade, 'id' => $estoqueId]);
        } elseif (($dados['status'] ?? '') === 'reservado') {
            $conn->prepare('UPDATE estoques SET reservado = reservado + :qtd WHERE id = :id')
                ->execute(['qtd' => $quantidade, 'id' => $estoqueId]);
        }
        return $id;
    }

    public function getConnection(): \PDO
    {
        return $this->estoques->getConnection();
    }

    public function reservarFefo(int $produtoId, float $quantidade, string $refTipo, int $refId, int $usuarioId, bool $efetivar): array
    {
        $lotes = $this->estoques->findLotesFefo($produtoId);
        $reservado = [];
        foreach ($lotes as $lote) {
            if ($quantidade <= 0) {
                break;
            }
            $disponivel = $efetivar ? $lote['quantidade'] : ($lote['quantidade'] - $lote['reservado']);
            if ($disponivel <= 0) {
                continue;
            }
            $consumo = min($disponivel, $quantidade);
            $status = $efetivar ? 'efetivado' : 'reservado';
            $this->registrarMovimento([
                'produto_id' => $produtoId,
                'tipo' => 'saida',
                'status' => $status,
                'quantidade' => $consumo,
                'lote' => $lote['lote'],
                'validade' => $lote['validade'],
                'ref_tipo' => $refTipo,
                'ref_id' => $refId,
                'usuario_id' => $usuarioId,
            ], $usuarioId);
            $reservado[] = ['lote' => $lote['lote'], 'quantidade' => $consumo];
            $quantidade -= $consumo;
        }
        return ['reservas' => $reservado, 'faltante' => max(0, $quantidade)];
    }

    public function alertasValidade(int $dias): array
    {
        $stmt = $this->estoques->getConnection()->prepare("SELECT e.*, p.nome, p.sku, DATEDIFF(e.validade, CURDATE()) AS dias_para_vencer FROM estoques e JOIN produtos p ON p.id = e.produto_id WHERE e.quantidade > 0 AND e.validade IS NOT NULL AND e.validade <= DATE_ADD(CURDATE(), INTERVAL :dias DAY)");
        $stmt->execute(['dias' => $dias]);
        return $stmt->fetchAll();
    }
}
