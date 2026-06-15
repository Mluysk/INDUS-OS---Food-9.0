<?php
/** @var array $pedido */
/** @var array $itens */
$cliente = $pedido['cliente'] ?? [];
if (!$cliente && isset($pedido['cliente_nome_fantasia'])) {
    $cliente = [
        'nome_fantasia' => $pedido['cliente_nome_fantasia'],
        'razao_social' => $pedido['cliente_razao_social'] ?? null,
        'cnpj' => $pedido['cliente_cnpj'] ?? null,
        'cpf' => $pedido['cliente_cpf'] ?? null,
        'email' => $pedido['cliente_email'] ?? null,
        'telefone' => $pedido['cliente_telefone'] ?? null,
        'cep' => $pedido['cliente_cep'] ?? null,
        'endereco' => $pedido['cliente_endereco'] ?? null,
        'numero' => $pedido['cliente_numero'] ?? null,
        'bairro' => $pedido['cliente_bairro'] ?? null,
        'cidade' => $pedido['cliente_cidade'] ?? null,
        'estado' => $pedido['cliente_estado'] ?? null,
    ];
}
$formatWeight = static function ($value): string {
    $numeric = (float)($value ?? 0);
    if ($numeric <= 0) {
        return '-';
    }
    if ($numeric < 1) {
        return number_format($numeric * 1000, 0, ',', '.') . ' g';
    }
    return number_format($numeric, 3, ',', '.') . ' kg';
};
$formatEndereco = static function (array $cliente): string {
    $logradouro = trim((string)($cliente['endereco'] ?? ''));
    $numero = trim((string)($cliente['numero'] ?? ''));
    $bairro = trim((string)($cliente['bairro'] ?? ''));
    $cidade = trim((string)($cliente['cidade'] ?? ''));
    $estado = trim((string)($cliente['estado'] ?? ''));

    $linha = $logradouro;
    if ($numero !== '') {
        $linha .= ($linha !== '' ? ', ' : '') . $numero;
    }

    $complementos = array_values(array_filter([$bairro, trim($cidade . ($estado !== '' ? '/' . $estado : ''))], static fn($item) => $item !== '' && $item !== '/'));
    if ($complementos) {
        $linha .= ($linha !== '' ? ' - ' : '') . implode(' - ', $complementos);
    }

    return trim($linha);
};
$clienteNome = $cliente['nome_fantasia'] ?? ($cliente['razao_social'] ?? 'Cliente');
$clienteDoc = $cliente['cnpj'] ?? ($cliente['cpf'] ?? '');
$clienteCep = trim((string)($cliente['cep'] ?? ''));
$clienteContato = trim(($cliente['telefone'] ?? '').' '.($cliente['email'] ?? ''));
$clienteEndereco = $formatEndereco($cliente);
$notaDados = $nota ?? [];
$notaLogo = $asset($notaDados['logo'] ?? ($theme['logo_light'] ?? 'img/logo.png'));
$pixEnabled = isset($notaPixEnabled) ? (bool)$notaPixEnabled : (bool)($notaDados['pix_enabled'] ?? false);
$pixKey = trim($notaPixKey ?? ($notaDados['pix_key'] ?? ''));
$pixType = isset($notaPixType) ? (string)$notaPixType : (string)($notaDados['pix_type'] ?? 'cnpj');
$pixLabels = [
    'cnpj' => 'CNPJ',
    'cpf' => 'CPF',
    'email' => 'E-mail',
    'telefone' => 'Celular',
];
$pixLabel = $pixLabels[strtolower($pixType)] ?? '';
$pedidoCodigo = $pedido['codigo'] ?? ('#'.$pedido['id']);
$itensSubtotal = array_reduce($itens, static function($carry, $item){
    $q = (float)($item['quantidade'] ?? 0);
    $p = (float)($item['preco_unit'] ?? 0);
    return $carry + ($q * $p);
}, 0.0);
$freteValor = max(0.0, (float)($pedido['frete_valor'] ?? 0));
$cobrancaDiferencaValor = max(0.0, (float)($pedido['cobranca_diferenca_valor'] ?? 0));
$cobrancaDiferencaDescricao = trim((string)($pedido['cobranca_diferenca_descricao'] ?? '')) ?: 'Cobrança de diferença';
$totalBase = $itensSubtotal + $freteValor + $cobrancaDiferencaValor;
$total = isset($pedido['total']) ? (float)$pedido['total'] : $totalBase;
if ($total < $totalBase) {
    $total = $totalBase;
}
$desconto = max(0.0, (float)($pedido['desconto'] ?? 0));
$totalFinal = max(0.0, $total - $desconto);
$notaCidade = 'SAO PAULO';
if (!empty($notaDados['cidade'])) {
    $notaCidade = $notaDados['cidade'];
} elseif (!empty($notaDados['endereco']) && preg_match('/-\s*([^\-\/]+)\s*\/\s*([A-Za-z]{2})/u', $notaDados['endereco'], $matches)) {
    $notaCidade = $matches[1];
} elseif (!empty($cliente['cidade'])) {
    $notaCidade = $cliente['cidade'];
}
$pixPayload = '';
if ($pixEnabled && $pixKey !== '') {
    $txid = preg_replace('/[^A-Za-z0-9]/', '', (string)$pedidoCodigo) ?: 'INDUSOS';
    $pixPayload = gerar_payload_pix(
        $pixKey,
        $totalFinal,
        $notaDados['empresa'] ?? 'INDUSOS FOOD',
        $notaCidade,
        $txid,
        $pedidoCodigo
    );
}
$pixQrUrl = $pixPayload !== '' ? 'https://quickchart.io/qr?size=200&margin=2&text=' . urlencode($pixPayload) : '';
?>
<div class="nota-shell">
    <div class="nota-head">
        <div class="nota-head__col">
            <div class="nota-logo">
                <img src="<?= htmlspecialchars($notaLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="Logo">
            </div>
            <div class="nota-empresa">
                <h1><?= htmlspecialchars($notaDados['empresa'] ?? 'IndusOS Food', ENT_QUOTES, 'UTF-8'); ?></h1>
                <p><?= htmlspecialchars($notaDados['documento'] ?? 'CNPJ 00.000.000/0000-00', ENT_QUOTES, 'UTF-8'); ?></p>
                <p><?= htmlspecialchars($notaDados['endereco'] ?? 'Rua Exemplo, 123 - Cidade/UF', ENT_QUOTES, 'UTF-8'); ?></p>
                <p><?= htmlspecialchars($notaDados['contato'] ?? '(11) 3000-0000', ENT_QUOTES, 'UTF-8'); ?></p>
                <p><?= htmlspecialchars($notaDados['email'] ?? 'contato@indusos.com.br', ENT_QUOTES, 'UTF-8'); ?><?php if (!empty($notaDados['site'])): ?> · <?= htmlspecialchars($notaDados['site'], ENT_QUOTES, 'UTF-8'); ?><?php endif; ?></p>
            </div>
        </div>
        <div class="nota-head__col nota-head__info">
            <div class="nota-head__box">
                <div class="row"><span class="label">Telefone:</span><span class="value"><?= htmlspecialchars($notaDados['contato'] ?? '(11) 3000-0000', ENT_QUOTES, 'UTF-8'); ?></span></div>
                <div class="row"><span class="label">Documento:</span><span class="value"><?= htmlspecialchars($pedidoCodigo, ENT_QUOTES, 'UTF-8'); ?></span></div>
                <div class="row"><span class="label">Emissão:</span><span class="value"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($pedido['created_at'] ?? date('Y-m-d H:i:s'))), ENT_QUOTES, 'UTF-8'); ?></span></div>
                <div class="row"><span class="label">Status:</span><span class="value"><?= htmlspecialchars($pedido['status_label'] ?? $pedido['status'] ?? 'novo', ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
        </div>
    </div>

    <div class="nota-block">
        <div class="nota-block__col">
            <h3>Cliente</h3>
            <p><strong><?= htmlspecialchars($clienteNome, ENT_QUOTES, 'UTF-8'); ?></strong></p>
            <p><?= htmlspecialchars($clienteDoc ?: '-', ENT_QUOTES, 'UTF-8'); ?></p>
            <p><span class="label">CEP:</span> <?= htmlspecialchars($clienteCep ?: '-', ENT_QUOTES, 'UTF-8'); ?></p>
            <p><?= htmlspecialchars($clienteEndereco ?: 'Endereço não informado', ENT_QUOTES, 'UTF-8'); ?></p>
            <p><?= htmlspecialchars($clienteContato ?: 'Contato não informado', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="nota-block__col">
            <h3>Pedido</h3>
            <p><span class="label">Código:</span> <?= htmlspecialchars($pedidoCodigo, ENT_QUOTES, 'UTF-8'); ?></p>
            <p><span class="label">Vendedor:</span> <?= htmlspecialchars($pedido['vendedor'] ?? 'Administrador', ENT_QUOTES, 'UTF-8'); ?></p>
            <p><span class="label">Pagamento:</span> <?= htmlspecialchars($pedido['forma_pagto'] ?? 'A combinar', ENT_QUOTES, 'UTF-8'); ?></p>
            <p><span class="label">Validade da proposta:</span> <?= htmlspecialchars($pedido['validade'] ?? date('d/m/Y', strtotime('+7 days')), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>

    <div class="nota-items">
        <table class="nota-table">
            <thead>
            <tr>
                <th class="code-col">Código</th>
                <th class="product-col left">Produto</th>
                <th class="qty-col">Quantidade</th>
                <th class="weight-col">Peso</th>
                <th class="money-col">Valor</th>
                <th class="money-col">Subtotal</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($itens): ?>
                <?php foreach ($itens as $item): $q = (float)($item['quantidade'] ?? 0); $p = (float)($item['preco_unit'] ?? 0); ?>
                    <tr>
                        <td class="code-col"><strong><?= htmlspecialchars($item['sku'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong></td>
                        <td class="left product-col">
                            <strong><?= htmlspecialchars($item['nome'] ?? $item['produto_nome'] ?? $item['produto_id'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                            <?php if (!empty($item['descricao'])): ?><div class="muted"><?= htmlspecialchars($item['descricao'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
                        </td>
                        <td class="qty-col"><?= number_format($q, 2, ',', '.'); ?></td>
                        <td class="weight-col"><?= htmlspecialchars($formatWeight($item['peso_liquido'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="money-col">R$ <?= number_format($p, 2, ',', '.'); ?></td>
                        <td class="money-col">R$ <?= number_format($q * $p, 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($cobrancaDiferencaValor > 0): ?>
                    <tr class="nota-cobranca-row">
                        <td class="code-col"><strong>00</strong></td>
                        <td class="left product-col"><strong><?= htmlspecialchars($cobrancaDiferencaDescricao, ENT_QUOTES, 'UTF-8'); ?></strong></td>
                        <td class="qty-col">1,00</td>
                        <td class="weight-col">-</td>
                        <td class="money-col">R$ <?= number_format($cobrancaDiferencaValor, 2, ',', '.'); ?></td>
                        <td class="money-col">R$ <?= number_format($cobrancaDiferencaValor, 2, ',', '.'); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ($freteValor > 0): ?>
                    <tr class="nota-frete-row">
                        <td class="code-col"><strong>FRETE</strong></td>
                        <td class="left product-col"><strong>Frete</strong></td>
                        <td class="qty-col">1,00</td>
                        <td class="weight-col">-</td>
                        <td class="money-col">R$ <?= number_format($freteValor, 2, ',', '.'); ?></td>
                        <td class="money-col">R$ <?= number_format($freteValor, 2, ',', '.'); ?></td>
                    </tr>
                <?php endif; ?>
            <?php else: ?>
                <tr><td colspan="6" class="no-data">Nenhum item informado.</td></tr>
                <?php if ($freteValor > 0): ?>
                    <tr class="nota-frete-row"><td class="code-col"><strong>FRETE</strong></td><td class="left product-col"><strong>Frete</strong></td><td class="qty-col">1,00</td><td class="weight-col">-</td><td class="money-col">R$ <?= number_format($freteValor, 2, ',', '.'); ?></td><td class="money-col">R$ <?= number_format($freteValor, 2, ',', '.'); ?></td></tr>
                <?php endif; ?>
                <?php if ($cobrancaDiferencaValor > 0): ?>
                    <tr class="nota-cobranca-row"><td class="code-col"><strong>00</strong></td><td class="left product-col"><strong><?= htmlspecialchars($cobrancaDiferencaDescricao, ENT_QUOTES, 'UTF-8'); ?></strong></td><td class="qty-col">1,00</td><td class="weight-col">-</td><td class="money-col">R$ <?= number_format($cobrancaDiferencaValor, 2, ',', '.'); ?></td><td class="money-col">R$ <?= number_format($cobrancaDiferencaValor, 2, ',', '.'); ?></td></tr>
                <?php endif; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="nota-observacoes">
        <div class="nota-observacoes__col">
            <h4>Observações</h4>
            <p><?= nl2br(htmlspecialchars($pedido['observacoes'] ?? '-', ENT_QUOTES, 'UTF-8')); ?></p>
            <h4>Forma de pagamento</h4>
            <p><?= htmlspecialchars($pedido['forma_pagto'] ?? 'PIX / Transferência', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="nota-observacoes__col total-box">
            <div class="total-row"><span>Subtotal produtos</span><span>R$ <?= number_format($itensSubtotal, 2, ',', '.'); ?></span></div>
            <?php if ($freteValor > 0): ?>
                <div class="total-row"><span>Frete</span><span>R$ <?= number_format($freteValor, 2, ',', '.'); ?></span></div>
            <?php endif; ?>
            <?php if ($cobrancaDiferencaValor > 0): ?>
                <div class="total-row"><span><?= htmlspecialchars($cobrancaDiferencaDescricao, ENT_QUOTES, 'UTF-8'); ?></span><span>R$ <?= number_format($cobrancaDiferencaValor, 2, ',', '.'); ?></span></div>
            <?php endif; ?>
            <?php if ($desconto > 0): ?>
                <div class="total-row"><span>Desconto</span><span>- R$ <?= number_format($desconto, 2, ',', '.'); ?></span></div>
            <?php endif; ?>
            <div class="total-row total-row__grand"><span>Total</span><span>R$ <?= number_format($totalFinal, 2, ',', '.'); ?></span></div>
        </div>
    </div>

    <?php if ($pixEnabled): ?>
        <div class="pix-box">
            <div class="pix-left">
                <div class="pix-heading">
                    <div class="pix-brand-title">
                        <img class="pix-brand-logo" src="<?= htmlspecialchars($asset('uploads/branding/pix-logo.png'), ENT_QUOTES, 'UTF-8'); ?>" alt="PIX powered by Banco Central">
                        <span class="pix-eyebrow">Pagamento via PIX</span>
                    </div>
                    <h3>Chave <?= $pixLabel ? '(' . htmlspecialchars($pixLabel, ENT_QUOTES, 'UTF-8') . ')' : ''; ?></h3>
                    <p class="pix-key"><?= htmlspecialchars($pixKey !== '' ? $pixKey : 'Chave não informada', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="pix-note">Use o QR ou a chave para pagar <strong>R$ <?= number_format($totalFinal, 2, ',', '.'); ?></strong>.</p>
                </div>
            </div>
            <div class="pix-right">
                <?php if ($pixQrUrl): ?>
                    <img src="<?= htmlspecialchars($pixQrUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="QR Code PIX">
                <?php else: ?>
                    <div class="pix-qr-placeholder">Informe uma chave PIX válida para gerar o QR.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    :root { --nota-border:#0f172a; --nota-text:#0f172a; --nota-muted:#475569; }
    body { color: var(--nota-text); }
    .print-container { width: 100%; max-width: none; }
    .nota-shell { width: 100%; font-family: 'Inter', Arial, sans-serif; font-size: 12px; color: var(--nota-text); }
    .nota-head { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(260px, 0.9fr); border: 2px solid var(--nota-border); padding: 10px; gap: 10px; }
    .nota-head__col { display: flex; gap: 10px; min-width: 0; }
    .nota-head__info { align-items: stretch; justify-content: flex-end; }
    .nota-head__box { border: 1px solid var(--nota-border); padding: 8px 10px; width: 100%; }
    .nota-head__box .row { display:flex; justify-content: space-between; gap: 12px; font-weight: 600; font-size:12px; }
    .nota-head__box .label { color: var(--nota-muted); }
    .nota-logo img { height: 48px; object-fit: contain; }
    .nota-empresa h1 { margin:0; font-size: 16px; letter-spacing: 0.02em; }
    .nota-empresa p { margin:2px 0; }
    .nota-block { display:grid; grid-template-columns:1fr 1fr; border:2px solid var(--nota-border); border-top:none; padding:10px; gap:10px; }
    .nota-block h3 { margin:0 0 4px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.04em; }
    .nota-block .label { color: var(--nota-muted); font-weight:600; }
    .nota-block p { margin: 2px 0; }
    .nota-items { border:2px solid var(--nota-border); border-top:none; }
    .nota-table { width:100%; border-collapse: collapse; table-layout: fixed; }
    .nota-table th, .nota-table td { border:1px solid var(--nota-border); padding:7px 8px; text-align:center; vertical-align: middle; }
    .nota-table th { background:#f8fafc; font-size:11px; }
    .nota-table td.left, .nota-table th.left { text-align:left; }
    .nota-table .muted { color: var(--nota-muted); font-size:11px; }
    .nota-frete-row td { background:#fff7ed; font-weight:800; }
    .nota-cobranca-row td { background:#fef2f2; font-weight:800; }
    .code-col { width: 12%; }
    .product-col { width: 36%; }
    .qty-col { width: 12%; }
    .weight-col { width: 12%; }
    .money-col { width: 14%; white-space: nowrap; }
    .nota-observacoes { display:grid; grid-template-columns: minmax(0, 1.45fr) minmax(250px, 0.75fr); border:2px solid var(--nota-border); border-top:none; padding:10px; gap:10px; }
    .nota-observacoes h4 { margin:0 0 4px; font-size:12px; }
    .nota-observacoes p { margin:0 0 6px; }
    .total-box { border:1px solid var(--nota-border); padding:8px; display:flex; flex-direction:column; gap:6px; }
    .total-row { display:flex; justify-content: space-between; gap: 16px; font-weight:600; }
    .total-row__grand { font-size:14px; border-top:1px solid var(--nota-border); padding-top:6px; }
    .pix-box { border:2px solid var(--nota-border); border-top:none; padding:12px 10px; display:grid; grid-template-columns:minmax(0, 1fr) 150px; align-items:center; gap:12px; }
    .pix-brand-title { display:flex; flex-direction:column; align-items:flex-start; gap:3px; margin-bottom:4px; }
    .pix-brand-logo { width:133px; height:auto; flex:0 0 auto; }
    .pix-left .pix-eyebrow { font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color: var(--nota-muted); margin:0; }
    .pix-left h3 { margin:2px 0 4px; font-size:14px; }
    .pix-key { font-weight:700; margin:2px 0; word-break:break-all; }
    .pix-note { margin:0; color: var(--nota-muted); }
    .pix-right { border:1px solid var(--nota-border); border-radius:8px; padding:6px; display:flex; align-items:center; justify-content:center; min-height:140px; }
    .pix-right img { width:100%; height:100%; object-fit:contain; }
    .pix-qr-placeholder { text-align:center; color: var(--nota-muted); font-size:11px; }
    .no-data { text-align:center; color: var(--nota-muted); }
    @page { margin: 8mm; }
    @media print {
        body { margin:0; padding:0; }
        .nota-shell { font-size:11px; }
        .nota-head, .nota-block, .nota-items, .nota-observacoes, .pix-box { border-width:1px; }
        .nota-table th, .nota-table td { padding:5px 6px; }
        .pix-box { grid-template-columns:minmax(0, 1fr) 130px; }
        .pix-brand-logo { width:116px; }
        .pix-right { min-height:120px; }
    }
</style>
