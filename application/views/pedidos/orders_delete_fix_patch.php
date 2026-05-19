<?php
if (!isset($_GET['delete_pedido'])) {
    return;
}

$pedidoIdToDelete = (int)$_GET['delete_pedido'];
$loggedRole = strtolower((string)($user['papel'] ?? $user['role'] ?? $_SESSION['user_role'] ?? ''));

if (!in_array($loggedRole, ['admin', 'administrator', 'gestor', 'financeiro', 'expedicao'], true)) {
    $this->setFlash('error', 'Você não tem permissão para excluir pedidos.');
    $this->redirect('/pedidos');
}

if ($pedidoIdToDelete <= 0) {
    $this->setFlash('error', 'Pedido inválido para exclusão.');
    $this->redirect('/pedidos');
}

$db = null;
try {
    $pedidosRepo = $this->container->get(\App\Repositories\PedidosRepository::class);
    $pedidoAtual = $pedidosRepo->find($pedidoIdToDelete);

    if (!$pedidoAtual) {
        $this->setFlash('error', 'Pedido não encontrado.');
        $this->redirect('/pedidos');
    }

    $db = $pedidosRepo->getConnection();
    $db->beginTransaction();

    $deleteIfTableExists = static function (\PDO $db, string $table, string $where, array $params) use ($pedidoIdToDelete): void {
        try {
            $stmt = $db->prepare("DELETE FROM {$table} WHERE {$where}");
            $stmt->execute($params);
        } catch (\PDOException $exception) {
            $code = (string)$exception->getCode();
            if (!in_array($code, ['42S02', '42S22'], true)) {
                throw $exception;
            }
        }
    };

    $deleteIfTableExists($db, 'ordens_producao', 'pedido_id = :pedido', ['pedido' => $pedidoIdToDelete]);
    $deleteIfTableExists($db, 'movimentos_estoque', 'ref_tipo = :tipo AND ref_id = :pedido', [
        'tipo' => 'pedido',
        'pedido' => $pedidoIdToDelete,
    ]);
    $deleteIfTableExists($db, 'pedido_itens', 'pedido_id = :pedido', ['pedido' => $pedidoIdToDelete]);
    $deleteIfTableExists($db, 'pedidos', 'id = :pedido', ['pedido' => $pedidoIdToDelete]);

    $db->commit();

    $this->audit->log((int)($user['id'] ?? 0), 'delete', 'pedidos', $pedidoIdToDelete, $pedidoAtual);
    $this->setFlash('success', 'Pedido excluído com sucesso.');
} catch (\Throwable $exception) {
    if ($db instanceof \PDO && $db->inTransaction()) {
        $db->rollBack();
    }
    $this->setFlash('error', 'Não foi possível excluir o pedido agora.');
}

$this->redirect('/pedidos');
