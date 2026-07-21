<?php
$esc = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
$dateBr = static fn($value): string => ($time = strtotime((string)$value)) ? date('d/m/Y H:i', $time) : '—';
$cliente = trim((string)($pedido['cliente_nome_fantasia'] ?? '')) ?: trim((string)($pedido['cliente_razao_social'] ?? ''));
?>
<style>
.rom-print{border:1px solid var(--border);padding:24px;border-radius:12px}.rom-print__head{display:flex;justify-content:space-between;gap:24px;padding-bottom:18px;border-bottom:2px solid var(--brand)}.rom-print__head h2{margin:0 0 5px;font-size:25px}.rom-print__head p{margin:0;color:var(--muted)}.rom-print__number{text-align:right}.rom-print__number small{display:block;color:var(--muted)}.rom-print__number strong{font-size:20px}.rom-print__grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:20px 0}.rom-print__field{padding:12px;border:1px solid var(--border);border-radius:8px}.rom-print__field small{display:block;color:var(--muted);margin-bottom:4px}.rom-print table{width:100%;border-collapse:collapse;font-size:13px}.rom-print th,.rom-print td{padding:10px 8px;border:1px solid var(--border);text-align:left}.rom-print th{background:#f1f5f9;font-size:11px;text-transform:uppercase}.rom-print td:not(:first-child),.rom-print th:not(:first-child){text-align:center}.rom-print__signatures{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:28px}.rom-print__signature{padding:14px;border:1px solid var(--border);border-radius:10px;text-align:center}.rom-print__signature h3{margin:0 0 3px;font-size:15px}.rom-print__signature p{margin:0;color:var(--muted)}.rom-print__signature img{display:block;width:100%;height:130px;object-fit:contain;margin:10px 0 5px;border-bottom:1px solid #64748b}.rom-print__missing{height:130px;display:grid;place-items:center;color:var(--muted);border-bottom:1px solid #64748b}@media(max-width:720px){.rom-print__grid{grid-template-columns:1fr 1fr}.rom-print__signatures{grid-template-columns:1fr}}@media print{body{padding:0}.rom-print{border:0;padding:0}.report-toolbar{display:none!important}}
</style>
<section class="rom-print">
    <header class="rom-print__head"><div><h2>Romaneio de expedição</h2><p>Comprovante original de separação e entrega à transportadora.</p></div><div class="rom-print__number"><small>Número do romaneio</small><strong><?= $esc($expedicao['romaneio'] ?? '—'); ?></strong></div></header>
    <div class="rom-print__grid">
        <div class="rom-print__field"><small>Pedido</small><strong><?= $esc($pedido['codigo'] ?? '#'.($pedido['id'] ?? 0)); ?></strong></div>
        <div class="rom-print__field"><small>Cliente</small><strong><?= $esc($cliente ?: 'Não identificado'); ?></strong></div>
        <div class="rom-print__field"><small>Separação</small><strong><?= $esc($dateBr($expedicao['data_separacao'] ?? '')); ?></strong></div>
        <div class="rom-print__field"><small>Expedição</small><strong><?= $esc($dateBr($expedicao['data_expedicao'] ?? '')); ?></strong></div><div class="rom-print__field"><small>Tipo de entrega</small><strong><?= strtolower((string)($expedicao['tipo_entrega']??'propria'))==='terceiros'?'Transportadora terceirizada':'Entrega própria'; ?></strong></div>
        <div class="rom-print__field"><small>Volumes</small><strong><?= (int)($expedicao['volumes'] ?? 0); ?></strong></div>
        <div class="rom-print__field"><small>Peso total</small><strong><?= number_format((float)($expedicao['peso_total'] ?? 0), 3, ',', '.'); ?> kg</strong></div>
        <div class="rom-print__field"><small>Transportadora</small><strong><?= $esc($expedicao['transportadora'] ?: 'Não informada'); ?></strong></div>
        <div class="rom-print__field"><small>Status</small><strong>Expedido</strong></div>
    </div>
    <table><thead><tr><th>Produto</th><th>SKU</th><th>Quantidade</th><th>Peso unitário</th><th>Peso total</th></tr></thead><tbody>
    <?php foreach ($itens as $item): $quantity=(float)($item['quantidade']??0); $weight=(float)($item['peso_liquido']??0); ?>
        <tr><td><strong><?= $esc($item['nome'] ?? $item['produto_nome'] ?? 'Produto'); ?></strong></td><td><?= $esc($item['sku'] ?? '—'); ?></td><td><?= number_format($quantity, 4, ',', '.'); ?> <?= $esc($item['unidade'] ?? ''); ?></td><td><?= number_format($weight, 3, ',', '.'); ?> kg</td><td><?= number_format($quantity*$weight, 3, ',', '.'); ?> kg</td></tr>
    <?php endforeach; ?>
    </tbody></table>
    <div class="rom-print__signatures">
        <article class="rom-print__signature"><h3>Motorista</h3><p><?= $esc($expedicao['motorista_nome'] ?: 'Não informado'); ?></p><?php if (!empty($expedicao['assinatura_motorista'])): ?><img src="<?= $esc($expedicao['assinatura_motorista']); ?>" alt="Assinatura original do motorista"><?php else: ?><div class="rom-print__missing">Assinatura não registrada</div><?php endif; ?><small>Assinatura do motorista</small></article>
        <article class="rom-print__signature"><h3>Responsável pela expedição</h3><p><?= $esc($expedicao['responsavel_expedicao_nome'] ?: 'Não informado'); ?></p><?php if (!empty($expedicao['assinatura_responsavel'])): ?><img src="<?= $esc($expedicao['assinatura_responsavel']); ?>" alt="Assinatura original do responsável"><?php else: ?><div class="rom-print__missing">Assinatura não registrada</div><?php endif; ?><small>Assinatura do responsável</small></article>
    </div>
</section>
