<?php
if (!isset($pedidos) || !is_array($pedidos)) {
    return;
}

$pedidosRepoForPortalCode = null;
foreach ($pedidos as $pedidoIndex => $pedidoItem) {
    $pedidoId = (int)($pedidoItem['id'] ?? 0);
    $codigoAtual = strtoupper(trim((string)($pedidoItem['codigo'] ?? '')));

    if ($pedidoId <= 0 || !str_starts_with($codigoAtual, 'PED')) {
        continue;
    }

    $novoCodigoPortal = sprintf('P-%05d', $pedidoId);
    $pedidos[$pedidoIndex]['codigo'] = $novoCodigoPortal;

    try {
        $pedidosRepoForPortalCode ??= $this->container->get(\App\Repositories\PedidosRepository::class);
        $pedidosRepoForPortalCode->update($pedidoId, ['codigo' => $novoCodigoPortal]);
    } catch (\Throwable $exception) {
        // Mantem a lista ajustada mesmo se o banco nao puder ser atualizado neste carregamento.
    }
}
