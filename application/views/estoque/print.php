<?php
$logo = $asset($nota['logo'] ?? ($theme['logo_light'] ?? 'img/logo.png'));
$now = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('d/m/Y H:i');
$faixa = $faixaBaixa ?? ['min' => 4, 'max' => 8];

$filterChips = [];
if (!empty($filters['q'])) {
    $filterChips[] = 'Busca: "' . htmlspecialchars($filters['q'], ENT_QUOTES, 'UTF-8') . '"';
}
if (!empty($filters['tipo'])) {
    $filterChips[] = $filters['tipo'] === 'MP' ? 'Tipo: Matéria-prima' : 'Tipo: Produto acabado';
}
if (!empty($filters['status'])) {
    $map = ['NEG' => 'Negativo', 'ZERO' => 'Sem estoque', 'LOW' => 'Estoque baixo', 'OK' => 'Normal'];
    $label = $map[$filters['status']] ?? $filters['status'];
    $filterChips[] = 'Status: ' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
}

$badgeClass = function (string $statusKey) {
    return match ($statusKey) {
        'NEG' => 'pill pill-neg',
        'ZERO' => 'pill pill-zero',
        'LOW' => 'pill pill-low',
        default => 'pill pill-ok',
    };
};
?>
<style>
.print-header { align-items: flex-start; }
.pill { padding: 6px 12px; border-radius: 12px; font-weight: 700; display: inline-flex; min-width: 90px; justify-content: center; border: 1px solid rgba(0,0,0,0.06); }
.pill-ok { background: rgba(34,197,94,0.14); color: #166534; border-color: rgba(34,197,94,0.28); }
.pill-neg { background: rgba(225,29,72,0.12); color: #991b1b; border-color: rgba(225,29,72,0.28); }
.pill-zero { background: rgba(250,204,21,0.14); color: #854d0e; border-color: rgba(250,204,21,0.32); }
.pill-low { background: rgba(249,115,22,0.14); color: #9a3412; border-color: rgba(249,115,22,0.32); }
.status-row { display: flex; gap: 10px; flex-wrap: wrap; margin: 0 0 1rem; }
.status-chip { border-radius: 12px; padding: 10px 14px; border: 1px solid rgba(0,0,0,0.05); min-width: 160px; }
.status-chip small { display: block; text-transform: uppercase; letter-spacing: 0.06em; color: #52606d; }
.status-chip strong { font-size: 1.1rem; }
.status-neg { background: rgba(225,29,72,0.1); border-color: rgba(225,29,72,0.32); color: #991b1b; }
.status-zero { background: rgba(250,204,21,0.12); border-color: rgba(250,204,21,0.32); color: #854d0e; }
.status-low { background: rgba(249,115,22,0.12); border-color: rgba(249,115,22,0.32); color: #9a3412; }
.filter-chips { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 6px; }
.filter-chips .chip { background: rgba(15,23,42,0.06); border-radius: 999px; padding: 6px 10px; font-size: 0.88rem; color: #374151; }
</style>

<header class="print-header">
    <div class="print-brand">
        <img src="<?= htmlspecialchars($logo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($appName ?? 'IndusOS Food', ENT_QUOTES, 'UTF-8'); ?>">
        <div>
            <h1>Posição de estoque</h1>
            <p class="print-meta">Atualizado em <?= htmlspecialchars($now, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php if ($filterChips): ?>
                <div class="filter-chips">
                    <?php foreach ($filterChips as $chip): ?>
                        <span class="chip"><?= $chip; ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <button class="print-button" type="button" onclick="window.print()">Imprimir</button>
</header>

<div class="status-row">
    <div class="status-chip status-neg"><small>Negativo</small><strong><?= (int)($status['negativos'] ?? 0); ?></strong></div>
    <div class="status-chip status-zero"><small>Sem estoque</small><strong><?= (int)($status['zerados'] ?? 0); ?></strong></div>
    <div class="status-chip status-low"><small>Estoque baixo (<?= (int)$faixa['min']; ?>–<?= (int)$faixa['max']; ?>)</small><strong><?= (int)($status['baixos'] ?? 0); ?></strong></div>
</div>

<?php if (!empty($posicao)): ?>
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Produto</th>
                <th>Tipo</th>
                <th>Quantidade</th>
                <th>Planejado (mapa)</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($posicao as $linha): ?>
            <?php $statusKey = $linha['status_key'] ?? 'OK'; ?>
            <tr>
                <td><?= htmlspecialchars($linha['sku'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($linha['nome'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= ($linha['tipo'] ?? 'PA') === 'PA' ? 'PA' : 'MP'; ?></td>
                <td><span class="<?= $badgeClass($statusKey); ?>"><?= number_format((float)($linha['saldo'] ?? 0), 0, ',', '.'); ?></span></td>
                <td><?= number_format((float)($linha['planejado_mapa'] ?? 0), 0, ',', '.'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="no-data">Nenhum item encontrado com os filtros aplicados.</div>
<?php endif; ?>
