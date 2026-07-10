<?php
$datas = $mapa['datas'] ?? [];
$linhas = $mapa['linhas'] ?? [];
$status = $mapa['status'] ?? ['negativos' => 0, 'zerados' => 0, 'baixos' => 0];
$lowMin = (int)($mapa['low_min'] ?? 4);
$lowMax = (int)($mapa['low_max'] ?? 8);
$baseAno = (int)substr((string)($mapa['base'] ?? date('Y-m-d')), 0, 4);
$janelaInicio = $mapa['janela_inicio'] ?? ($mapa['base'] ?? date('Y-m-d'));
$minAno = 2025;
$maxAno = 2100;
$prevAno = max($minAno, $baseAno - 1);
$nextAno = min($maxAno, $baseAno + 1);
$canPrev = $baseAno > $minAno;
$canNext = $baseAno < $maxAno;

$fmtInt = static function ($num) {
    return number_format((float)$num, 0, ',', '.');
};
?>
<section class="mapa mapa-completo">
    <style>
        .mapa { display: flex; flex-direction: column; gap: 14px; }
        .mapa .hero { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 14px 16px; box-shadow: var(--shadow-card); display: grid; grid-template-columns: 1fr auto; gap: 10px; align-items: center; }
        .mapa .hero h2 { margin: 0 0 4px; font-size: 1.04rem; letter-spacing: -0.02em; }
        .mapa .hero p { margin: 0; color: var(--text-muted); font-size: 0.9rem; }
        .mapa .legend-block { display: flex; flex-direction: column; gap: 8px; align-items: flex-end; }
        .mapa .legend { display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }
        .mapa .year-nav { display: inline-flex; align-items: center; gap: 10px; background: var(--surface-alt); border: 1px solid var(--border); padding: 8px 10px; border-radius: 12px; box-shadow: var(--shadow-soft); }
        .mapa .year-btn { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 30px; border-radius: 10px; border: 1px solid var(--border); background: linear-gradient(135deg, rgba(11,116,255,0.12), rgba(11,116,255,0.04)); color: #0b74ff; font-weight: 800; text-decoration: none; transition: transform 0.15s ease, box-shadow 0.15s ease; }
        .mapa .year-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 16px rgba(11,116,255,0.12); }
        .mapa .year-btn.disabled { opacity: 0.35; pointer-events: none; box-shadow: none; }
        .mapa .year-label { font-weight: 800; letter-spacing: 0.08em; color: var(--text); display: inline-flex; align-items: center; gap: 6px; }
        .mapa .pill { display: inline-flex; align-items: center; gap: 8px; padding: 6px 10px; border-radius: 999px; background: var(--surface-alt); border: 1px solid var(--border); color: var(--text); font-size: 0.86rem; box-shadow: var(--shadow-soft); }
        .mapa .swatch { width: 10px; height: 10px; border-radius: 50%; display: inline-block; box-shadow: 0 0 0 1px rgba(0,0,0,0.08); flex-shrink: 0; }
        .mapa .swatch.saida { background: #fda4af; box-shadow: 0 0 0 1px rgba(225, 29, 72, 0.24); }
        .mapa .swatch.entrada { background: #bae6fd; box-shadow: 0 0 0 1px rgba(14, 165, 233, 0.24); }
        .mapa .swatch.producao { background: #e9d5ff; box-shadow: 0 0 0 1px rgba(168, 85, 247, 0.24); }
        .mapa .swatch.estoque-swatch { background: #bbf7d0; box-shadow: 0 0 0 1px rgba(22, 163, 74, 0.18); }
        .mapa .tab-bar { display: inline-flex; gap: 12px; width: fit-content; background: var(--surface); border: 1px solid var(--border); padding: 6px; border-radius: 999px; box-shadow: var(--shadow-soft); }
        .mapa .tab-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0 !important; background: transparent !important; color: var(--text-muted) !important; padding: 10px 20px; border-radius: 999px; font-weight: 600; cursor: pointer; transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease; text-decoration: none; box-shadow: none; }
        .mapa .tab-btn svg { width: 17px; height: 17px; flex: 0 0 17px; }
        .mapa .tab-btn:not(.active):hover { background: var(--surface-alt) !important; color: var(--text) !important; }
        .mapa .tab-btn.active { background: var(--primary) !important; color: #fff !important; box-shadow: 0 6px 20px -12px var(--primary); }
        .mapa .status-chip { background: var(--surface-alt); border-radius: 12px; padding: 9px 12px; min-width: 150px; border: 1px solid var(--border); box-shadow: var(--shadow-soft); }
        .mapa .status-chip .status-label { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--text); }
        .mapa .status-chip .status-value { font-weight: 800; font-size: 1.1rem; color: var(--text); }
        .mapa .status-chip.negativo { background: rgba(225,29,72,0.12); border-color: rgba(225,29,72,0.28); color: #991b1b; }
        .mapa .status-chip.negativo .status-label, .mapa .status-chip.negativo .status-value { color: #991b1b; }
        .mapa .status-chip.zerado { background: rgba(250,204,21,0.14); border-color: rgba(250,204,21,0.3); color: #854d0e; }
        .mapa .status-chip.zerado .status-label, .mapa .status-chip.zerado .status-value { color: #854d0e; }
        .mapa .status-chip.baixo { background: rgba(249,115,22,0.14); border-color: rgba(249,115,22,0.32); color: #9a3412; }
        .mapa .status-chip.baixo .status-label, .mapa .status-chip.baixo .status-value { color: #9a3412; }
        .mapa .status-bar { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); height: 6px; border-radius: 999px; overflow: hidden; border: 1px solid var(--border); background: var(--surface-alt); }
        .mapa .status-bar span { display: block; height: 100%; }
        .mapa .seg-neg { background: linear-gradient(135deg, rgba(225,29,72,0.4), rgba(225,29,72,0.2)); }
        .mapa .seg-zero { background: linear-gradient(135deg, rgba(250,204,21,0.45), rgba(250,204,21,0.22)); }
        .mapa .seg-baixo { background: linear-gradient(135deg, rgba(249,115,22,0.4), rgba(249,115,22,0.2)); }
        .mapa .seg-ok { background: linear-gradient(135deg, rgba(59,130,246,0.3), rgba(59,130,246,0.12)); }
        .mapa .compact-card { background: var(--surface); border: 1px solid var(--border); border-radius: 18px; box-shadow: var(--shadow-soft); padding: 14px; display: flex; flex-direction: column; gap: 12px; }
        .mapa .compact-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .mapa .compact-header h3 { margin: 0; display: inline-flex; align-items: center; gap: 8px; }
        .mapa .compact-status { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; padding: 10px; border-radius: 12px; background: var(--surface-alt); border: 1px solid var(--border); }
        .mapa .year-pill { display: inline-flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 999px; border: 1px solid var(--border); background: linear-gradient(135deg, rgba(11,116,255,0.08), rgba(11,116,255,0.02)); color: #0b74ff; font-weight: 800; letter-spacing: 0.06em; box-shadow: var(--shadow-soft); }
        .mapa .compact-table { overflow-x: auto; }
        .mapa .compact-grid table { width: 100%; border-collapse: separate; border-spacing: 10px 10px; min-width: 880px; }
        .mapa .compact-grid th, .mapa .compact-grid td { background: transparent; padding: 0; text-align: center; }
        .mapa .compact-prod { width: 230px; text-align: left; position: sticky; left: 0; background: #fff !important; z-index: 3; box-shadow: 12px 0 14px rgba(15, 23, 42, 0.08); }
        .mapa .produto-card { background: #fff !important; border: 1px solid var(--border); border-radius: 16px; padding: 12px; box-shadow: var(--shadow-soft); display: grid; gap: 6px; }
        .mapa .produto-card .sku { font-weight: 800; letter-spacing: 0.02em; color: var(--text); font-size: 0.9rem; }
        .mapa .produto-card .nome { color: var(--text-muted); font-size: 0.86rem; margin-top: -2px; line-height: 1.3; }
        .mapa .saldo { display: inline-flex; align-items: center; gap: 6px; padding: 4px 9px; border-radius: 10px; margin-top: 2px; font-size: 0.86rem; border: 1px solid transparent; }
        .mapa .saldo strong { font-weight: 800; }
        .mapa .saldo.ok { background: #dcfce7; border-color: #86efac; color: #166534; }
        .mapa .saldo.negativo { background: #fee2e2; border-color: #fca5a5; color: #991b1b; }
        .mapa .saldo.zerado { background: #fef9c3; border-color: #fde68a; color: #854d0e; }
        .mapa .saldo.baixo { background: #ffedd5; border-color: #fdba74; color: #9a3412; }
        .mapa .compact-th { background: var(--surface-alt); border: 1px solid var(--border); border-radius: 12px; padding: 12px; box-shadow: var(--shadow-soft); display: grid; gap: 8px; position: relative; }
        .mapa .compact-title-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .mapa .date-block { display: flex; flex-direction: column; gap: 2px; }
        .mapa .date-label { font-weight: 800; letter-spacing: 0.02em; color: var(--text); }
        .mapa .weekday { color: var(--text-muted); font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; font-size: 0.82rem; }
        .mapa .compact-media { font-weight: 800; color: #0b74ff; white-space: nowrap; }
        .mapa .compact-title-actions { display: inline-flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }
        .mapa .feriado-toggle { display: inline-flex; align-items: center; gap: 6px; padding: 4px 8px; border-radius: 10px; background: var(--surface); border: 1px solid var(--border); color: var(--text-muted); font-weight: 700; font-size: 0.8rem; box-shadow: var(--shadow-soft); }
        .mapa .feriado-toggle input { width: 16px; height: 16px; accent-color: #f97316; }
        .mapa .compact-th.hoje { border-color: rgba(11,116,255,0.3); box-shadow: 0 10px 24px rgba(11,116,255,0.12); background: linear-gradient(180deg, rgba(11,116,255,0.05), rgba(11,116,255,0.02)); }
        .mapa .compact-cell { background: var(--surface-alt); border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow-soft); padding: 10px; }
        .mapa .compact-cell.hoje { border-color: rgba(11,116,255,0.3); box-shadow: 0 10px 24px rgba(11,116,255,0.12); }
        .mapa .badge-row { display: grid; grid-template-columns: repeat(5, minmax(120px, 1fr)); gap: 8px; align-items: stretch; }
        .mapa .compact-badge { border-radius: 14px; padding: 12px 10px; display: flex; flex-direction: column; justify-content: center; gap: 2px; font-weight: 800; font-size: 0.9rem; border: 1px solid transparent; box-shadow: 0 10px 22px rgba(0,0,0,0.05); text-align: center; background: linear-gradient(180deg, #ffffff, rgba(255,255,255,0.88)); }
        .mapa .compact-badge .label { color: var(--text-muted); font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .mapa .compact-badge .value { font-size: 1.05rem; }
        .mapa .compact-badge.saida { border-color: rgba(225,29,72,0.25); background: linear-gradient(135deg, rgba(255,241,244,0.98), rgba(252,232,236,0.9)); color: #b4234d; }
        .mapa .compact-badge.producao { border-color: rgba(168,85,247,0.25); background: linear-gradient(135deg, rgba(245,238,255,0.98), rgba(238,228,255,0.9)); color: #7a2cb8; }
        .mapa .compact-badge.entrada { border-color: rgba(14,165,233,0.25); background: linear-gradient(135deg, rgba(232,244,255,0.98), rgba(219,239,255,0.9)); color: #0f70c0; }
        .mapa .compact-badge.percent { border-color: rgba(11,116,255,0.25); background: linear-gradient(135deg, rgba(232,242,255,0.98), rgba(219,234,255,0.9)); color: #0b74ff; }
        .mapa .compact-badge.estoque { border-color: rgba(22,163,74,0.24); background: linear-gradient(135deg, rgba(231,250,238,0.98), rgba(219,244,229,0.9)); color: #166534; }
        .mapa .compact-badge.estoque.negativo { background: linear-gradient(135deg, rgba(225,29,72,0.16), rgba(225,29,72,0.08)); border-color: rgba(225,29,72,0.28); color: #991b1b; }
        .mapa .compact-badge.estoque.zerado { background: linear-gradient(135deg, rgba(250,204,21,0.18), rgba(250,204,21,0.1)); border-color: rgba(250,204,21,0.3); color: #854d0e; }
        .mapa .compact-badge.estoque.baixo { background: linear-gradient(135deg, rgba(249,115,22,0.18), rgba(249,115,22,0.08)); border-color: rgba(249,115,22,0.32); color: #9a3412; }
        .mapa .compact-forms { margin-top: 8px; display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 6px; }
        .mapa .mini-form.compacto { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 6px 8px; display: grid; grid-template-columns: auto 1fr auto; gap: 6px; align-items: center; box-shadow: inset 0 1px 0 rgba(255,255,255,0.35); }
        .mapa .mini-form.compacto label { margin: 0; font-size: 0.78rem; font-weight: 800; letter-spacing: 0.03em; color: var(--text); text-transform: uppercase; white-space: nowrap; }
        .mapa .mini-form.compacto input { width: 100%; padding: 4px 6px; border-radius: 9px; border: 1px solid var(--border); background: #f8fbff; color: var(--text); font-weight: 700; text-align: right; min-width: 60px; font-size: 0.85rem; }
        .mapa .mini-form.compacto button { border: none; background: #0b74ff; color: #fff; width: 26px; height: 26px; border-radius: 9px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 9px rgba(11,116,255,0.2); display: inline-flex; align-items: center; justify-content: center; font-size: 0.92rem; }
        .mapa .mini-form.compacto .controls { display: contents; }
        .mapa .empty { padding: 18px; border-radius: 14px; background: var(--surface); border: 1px solid var(--border); box-shadow: var(--shadow-soft); text-align: center; color: var(--text-muted); }
        @media (max-width: 960px) {
            .mapa .badge-row { grid-template-columns: repeat(2, minmax(0,1fr)); }
            .mapa .compact-grid table { min-width: 720px; }
        }

        @media (max-width: 768px) {
            .mapa .hero { grid-template-columns: 1fr; }
            .mapa .legend-block { align-items: flex-start; }
            .mapa .legend { justify-content: flex-start; }
            .mapa .compact-header { flex-direction: column; align-items: flex-start; }
            .mapa .compact-status { width: 100%; justify-content: flex-start; }
            .mapa .year-nav { width: 100%; justify-content: space-between; }
            .mapa .status-chip { min-width: 130px; }
            .mapa .badge-row { grid-template-columns: repeat(1, minmax(0,1fr)); }
            .mapa .compact-grid table { min-width: 640px; }
        }

        @media (max-width: 540px) {
            .mapa .legend { gap: 4px; }
            .mapa .tab-bar { width: 100%; justify-content: space-between; }
            .mapa .tab-btn { flex: 1; text-align: center; padding: 10px; }
            .mapa .compact-status { flex-direction: column; align-items: flex-start; }
            .mapa .year-nav { flex-wrap: wrap; gap: 6px; }
            .mapa .status-chip { min-width: auto; width: 100%; }
            .mapa .compact-grid table { min-width: 520px; }
        }
    </style>

    <div class="hero">
        <div>
            <h2>Mapa completo de produção, saídas e estoque</h2>
            <p>Visualização anual/geral colorida como o mapa diário. Utilize a aba para voltar ao resumo.</p>
        </div>
        <div class="legend-block">
            <div class="legend">
                <span class="pill"><span class="swatch saida"></span> Saídas</span>
                <span class="pill"><span class="swatch producao"></span> Produção</span>
                <span class="pill"><span class="swatch entrada"></span> Entrada</span>
                <span class="pill"><span class="swatch estoque-swatch"></span> Estoque</span>
            </div>
        </div>
    </div>

    <div class="tab-bar">
        <a class="tab-btn" href="<?= htmlspecialchars($url('/mapa'), ENT_QUOTES, 'UTF-8'); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></svg><span>Mapa diário</span></a>
        <a class="tab-btn active" href="<?= htmlspecialchars($url('/mapa?full=1#completo'), ENT_QUOTES, 'UTF-8'); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg><span>Mapa completo</span></a>
    </div>

    <div class="compact-card">
        <div class="compact-header">
            <div>
                <h3><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="#0b74ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Mapa completo</h3>
                <p class="hint">Período carregado para <?= htmlspecialchars($baseAno, ENT_QUOTES, 'UTF-8'); ?>. Navegue nas datas com a barra inferior ou use o filtro de ano na URL.</p>
            </div>
            <div class="compact-status">
                <div style="font-weight:700; color:var(--text); display:flex; align-items:center; gap:8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="0.8"/></svg>
                    Status do estoque
                </div>
                <div class="year-nav" aria-label="Navegar anos do mapa">
                    <a class="year-btn<?= $canPrev ? '' : ' disabled'; ?>" href="<?= $canPrev ? htmlspecialchars($url('/mapa?full=1&start=' . $prevAno . '-01-01#completo'), ENT_QUOTES, 'UTF-8') : '#'; ?>" aria-label="Ano anterior" aria-disabled="<?= $canPrev ? 'false' : 'true'; ?>">&larr;</a>
                    <div class="year-label"><?= htmlspecialchars((string)$baseAno, ENT_QUOTES, 'UTF-8'); ?></div>
                    <a class="year-btn<?= $canNext ? '' : ' disabled'; ?>" href="<?= $canNext ? htmlspecialchars($url('/mapa?full=1&start=' . $nextAno . '-01-01#completo'), ENT_QUOTES, 'UTF-8') : '#'; ?>" aria-label="Próximo ano" aria-disabled="<?= $canNext ? 'false' : 'true'; ?>">&rarr;</a>
                </div>
                <div class="status-chip negativo">
                    <div class="status-label">Negativo</div>
                    <div class="status-value" data-status="negativos"><?= (int)($status['negativos'] ?? 0); ?></div>
                </div>
                <div class="status-chip zerado">
                    <div class="status-label">Sem estoque</div>
                    <div class="status-value" data-status="zerados"><?= (int)($status['zerados'] ?? 0); ?></div>
                </div>
                <div class="status-chip baixo">
                    <div class="status-label">Estoque baixo</div>
                    <div class="status-value" data-status="baixos"><?= (int)($status['baixos'] ?? 0); ?></div>
                </div>
                <div class="status-bar" aria-hidden="true">
                    <?php
                    $totalStatus = max(1, (int)($status['negativos'] ?? 0) + (int)($status['zerados'] ?? 0) + (int)($status['baixos'] ?? 0));
                    $negPerc = ((int)($status['negativos'] ?? 0) / $totalStatus) * 100;
                    $zeroPerc = ((int)($status['zerados'] ?? 0) / $totalStatus) * 100;
                    $baixoPerc = ((int)($status['baixos'] ?? 0) / $totalStatus) * 100;
                    $okPerc = max(0, 100 - $negPerc - $zeroPerc - $baixoPerc);
                    ?>
                    <span class="seg-neg" style="width: <?= number_format($negPerc, 2, '.', ''); ?>%"></span>
                    <span class="seg-zero" style="width: <?= number_format($zeroPerc, 2, '.', ''); ?>%"></span>
                    <span class="seg-baixo" style="width: <?= number_format($baixoPerc, 2, '.', ''); ?>%"></span>
                    <span class="seg-ok" style="width: <?= number_format($okPerc, 2, '.', ''); ?>%"></span>
                </div>
            </div>
        </div>

        <div class="compact-table" aria-live="polite">
            <?php if (!empty($datas) && !empty($linhas)): ?>
                <?php
                $header = '<th class="compact-prod">Produto</th>';
                foreach (array_keys($datas) as $dia) {
                    $info = $datas[$dia];
                    $mediaValor = (float)($info['media_conclusao'] ?? 0);
                    $mediaDia = number_format($mediaValor, 1, ',', '');
                    $mediaDataAttr = number_format($mediaValor, 1, '.', '');
                    $hojeCls = !empty($info['is_hoje']) ? ' hoje' : '';
                    $header .= '<th class="' . $hojeCls . '"><div class="compact-th' . $hojeCls . '"><div class="compact-title-row">'
                        . '<div class="date-block">'
                        . '<div class="date-label">' . htmlspecialchars($info['label'] ?? $dia, ENT_QUOTES, 'UTF-8') . '</div>'
                        . '<div class="weekday">' . htmlspecialchars($info['semana'] ?? '', ENT_QUOTES, 'UTF-8') . '</div>'
                        . '</div>'
                        . '<div class="compact-title-actions">'
                        . '<span class="compact-media" data-media-dia="' . htmlspecialchars($dia, ENT_QUOTES, 'UTF-8') . '" data-media-base="' . $mediaDataAttr . '">Conclusão ' . $mediaDia . '%</span>'
                        . '<label class="feriado-toggle" title="Marcar este dia como feriado"><input type="checkbox" data-feriado-dia="' . htmlspecialchars($dia, ENT_QUOTES, 'UTF-8') . '"> Feriado</label>'
                        . '</div>'
                        . '</div></div></th>';
                }
                ?>
                <div class="compact-grid">
                    <table>
                        <thead>
                            <tr><?= $header; ?></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($linhas as $linha): ?>
                                <?php
                                $produtoSku = htmlspecialchars($linha['sku'] ?? '—', ENT_QUOTES, 'UTF-8');
                                $produtoNome = htmlspecialchars($linha['nome'] ?? '', ENT_QUOTES, 'UTF-8');
                                $produtoId = (int)($linha['id'] ?? 0);
                                $ultimoDia = array_key_last($datas);
                                $saldo = (float)($linha['dias'][$ultimoDia]['estoque_final'] ?? $linha['estoque'] ?? 0);
                                ?>
                                <tr>
                                    <?php
                                    $saldoClass = 'ok';
                                    if ($saldo < 0) {
                                        $saldoClass = 'negativo';
                                    } elseif ($saldo === 0.0) {
                                        $saldoClass = 'zerado';
                                    } elseif ($saldo >= $lowMin && $saldo <= $lowMax) {
                                        $saldoClass = 'baixo';
                                    }
                                    ?>
                                    <td class="compact-prod">
                                        <div class="produto-card">
                                            <div class="sku"><?= $produtoSku; ?></div>
                                            <div class="nome"><?= $produtoNome; ?></div>
                                            <div class="saldo <?= $saldoClass; ?>"><span>Saldo:</span><strong><?= $fmtInt($saldo); ?></strong></div>
                                        </div>
                                    </td>
                                    <?php foreach (array_keys($datas) as $dia): ?>
                                        <?php
                                        $celula = $linha['dias'][$dia] ?? [];
                                        $saida = (float)($celula['saida'] ?? 0);
                                        $producao = (float)($celula['entrada_planejada'] ?? 0);
                                        $entrada = (float)($celula['entrada_estoque'] ?? 0);
                                        $estoque = (float)($celula['estoque_final'] ?? 0);
                                        $conclusao = number_format((float)($celula['producao_percentual'] ?? 0), 1, ',', '');
                                        $producaoInput = number_format($producao, 2, '.', '');
                                        $entradaInput = number_format($entrada, 2, '.', '');
                                        $estoqueClass = 'estoque';
                                        if ($estoque < 0) {
                                            $estoqueClass .= ' negativo';
                                        } elseif ($estoque === 0.0) {
                                            $estoqueClass .= ' zerado';
                                        } elseif ($estoque >= $lowMin && $estoque <= $lowMax) {
                                            $estoqueClass .= ' baixo';
                                        }
                                        ?>
                                        <td>
                                            <div class="compact-cell<?= !empty($datas[$dia]['is_hoje']) ? ' hoje' : ''; ?>">
                                                <div class="badge-row">
                                                    <span class="compact-badge saida" data-compact-prod="<?= $produtoId; ?>" data-compact-dia="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" data-compact-metric="saida"><span class="label">Saída</span><span class="value"><?= $fmtInt($saida); ?></span></span>
                                                    <span class="compact-badge producao" data-compact-prod="<?= $produtoId; ?>" data-compact-dia="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" data-compact-metric="producao"><span class="label">Produção</span><span class="value"><?= $fmtInt($producao); ?></span></span>
                                                    <span class="compact-badge entrada" data-compact-prod="<?= $produtoId; ?>" data-compact-dia="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" data-compact-metric="entrada"><span class="label">Entrada</span><span class="value"><?= $fmtInt($entrada); ?></span></span>
                                                    <span class="compact-badge percent" data-compact-prod="<?= $produtoId; ?>" data-compact-dia="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" data-compact-metric="percent"><span class="label">Conclusão</span><span class="value"><?= $conclusao; ?>%</span></span>
                                                    <span class="compact-badge <?= $estoqueClass; ?>" data-compact-prod="<?= $produtoId; ?>" data-compact-dia="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" data-compact-metric="estoque"><span class="label">Estoque</span><span class="value"><?= $fmtInt($estoque); ?></span></span>
                                                </div>
                                                <div class="compact-forms">
                                                    <form action="<?= htmlspecialchars($url('/mapa/entrada'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mini-form compacto">
                                                        <label for="prod-<?= $produtoId; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">Produção</label>
                                                        <div class="controls">
                                                            <input type="hidden" name="tipo" value="planejado">
                                                            <input type="hidden" name="produto_id" value="<?= $produtoId; ?>">
                                                            <input type="hidden" name="data_referencia" value="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <input type="hidden" name="janela_inicio" value="<?= htmlspecialchars($janelaInicio, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <input type="hidden" name="full" value="1">
                                                            <input id="prod-<?= $produtoId; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" type="number" step="0.01" min="0" name="quantidade" value="<?= $producaoInput; ?>" placeholder="0">
                                                            <button type="submit" title="Registrar produção">+</button>
                                                        </div>
                                                    </form>
                                                    <form action="<?= htmlspecialchars($url('/mapa/entrada'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mini-form compacto">
                                                        <label for="ent-<?= $produtoId; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">Entrada</label>
                                                        <div class="controls">
                                                            <input type="hidden" name="tipo" value="estoque">
                                                            <input type="hidden" name="produto_id" value="<?= $produtoId; ?>">
                                                            <input type="hidden" name="data_referencia" value="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <input type="hidden" name="janela_inicio" value="<?= htmlspecialchars($janelaInicio, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <input type="hidden" name="full" value="1">
                                                            <input id="ent-<?= $produtoId; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" type="number" step="0.01" min="0" name="quantidade" value="<?= $entradaInput; ?>" placeholder="0">
                                                            <button type="submit" title="Adicionar entrada">+</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty">Nenhum registro encontrado no mapa completo. Lance produção ou entradas para este período.</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        (function () {
            const feriados = JSON.parse(localStorage.getItem('mapaFeriados') || '{}');

            function applyDia(dia) {
                const checked = !!feriados[dia];
                document.querySelectorAll(`input[data-feriado-dia="${dia}"]`).forEach((el) => {
                    el.checked = checked;
                });
                document.querySelectorAll(`[data-media-dia="${dia}"]`).forEach((el) => {
                    const base = Number(el.dataset.mediaBase || 0);
                    const formatted = base.toLocaleString('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
                    el.textContent = checked ? 'Feriado' : `Conclusão ${formatted}%`;
                });
            }

            document.querySelectorAll('input[data-feriado-dia]').forEach((input) => {
                const dia = input.dataset.feriadoDia;
                applyDia(dia);
                input.addEventListener('change', () => {
                    if (input.checked) {
                        feriados[dia] = true;
                    } else {
                        delete feriados[dia];
                    }
                    localStorage.setItem('mapaFeriados', JSON.stringify(feriados));
                    applyDia(dia);
                });
            });

            const centerInScroll = (wrap, target, behavior = 'auto') => {
                if (!wrap || !target) return;
                const targetLeft = target.offsetLeft;
                const targetWidth = target.offsetWidth;
                const centerOffset = (wrap.clientWidth - targetWidth) / 2;
                const left = Math.max(0, targetLeft - centerOffset);
                wrap.scrollTo({left, behavior});
            };

            // Após carregar todas as colunas do ano, posiciona a grade no dia atual
            const hojeTh = document.querySelector('.compact-grid th.hoje');
            const container = document.querySelector('.compact-table');
            if (hojeTh && container) {
                requestAnimationFrame(() => centerInScroll(container, hojeTh, 'auto'));
            }

            const showToast = (msg, type = 'info') => {
                let holder = document.querySelector('.mapa-toast');
                if (!holder) {
                    holder = document.createElement('div');
                    holder.className = 'mapa-toast';
                    holder.style.position = 'fixed';
                    holder.style.top = '16px';
                    holder.style.right = '16px';
                    holder.style.zIndex = '1200';
                    document.body.appendChild(holder);
                }
                const toast = document.createElement('div');
                toast.textContent = msg;
                toast.style.background = type === 'error' ? '#fee2e2' : '#ecfeff';
                toast.style.color = type === 'error' ? '#991b1b' : '#0f172a';
                toast.style.border = '1px solid rgba(0,0,0,0.08)';
                toast.style.boxShadow = '0 10px 30px rgba(0,0,0,0.12)';
                toast.style.padding = '10px 14px';
                toast.style.borderRadius = '12px';
                toast.style.marginTop = '8px';
                toast.style.fontWeight = '700';
                holder.appendChild(toast);
                setTimeout(() => toast.remove(), 2200);
            };

            const fmtInt = (value) => Number(value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

            const updateStatusCounts = (status = {}) => {
                const neg = Number(status.negativos || 0);
                const zero = Number(status.zerados || 0);
                const baixo = Number(status.baixos || 0);
                const total = Math.max(1, neg + zero + baixo);
                document.querySelectorAll('[data-status="negativos"]').forEach((el) => (el.textContent = neg));
                document.querySelectorAll('[data-status="zerados"]').forEach((el) => (el.textContent = zero));
                document.querySelectorAll('[data-status="baixos"]').forEach((el) => (el.textContent = baixo));
                const bar = document.querySelector('.status-bar');
                if (bar) {
                    const segs = {
                        neg: ((neg / total) * 100).toFixed(2),
                        zero: ((zero / total) * 100).toFixed(2),
                        baixo: ((baixo / total) * 100).toFixed(2),
                    };
                    const ok = Math.max(0, 100 - (Number(segs.neg) + Number(segs.zero) + Number(segs.baixo))).toFixed(2);
                    const spans = bar.querySelectorAll('span');
                    if (spans[0]) spans[0].style.width = `${segs.neg}%`;
                    if (spans[1]) spans[1].style.width = `${segs.zero}%`;
                    if (spans[2]) spans[2].style.width = `${segs.baixo}%`;
                    if (spans[3]) spans[3].style.width = `${ok}%`;
                }
            };

            const updateFromPayload = (mapaPayload) => {
                if (!mapaPayload || !mapaPayload.datas || !mapaPayload.linhas) return;
                updateStatusCounts(mapaPayload.status || {});

                Object.entries(mapaPayload.datas).forEach(([dia, info]) => {
                    const mediaEl = document.querySelector(`[data-media-dia="${dia}"]`);
                    if (mediaEl) {
                        const mediaVal = Number(info.media_conclusao || 0);
                        mediaEl.dataset.mediaBase = mediaVal.toFixed(1);
                        mediaEl.textContent = info.feriado ? 'Feriado' : `Conclusão ${mediaVal.toLocaleString('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%`;
                    }
                    const fer = document.querySelector(`[data-feriado-dia="${dia}"]`);
                    if (fer) fer.checked = !!info.feriado;
                });

                mapaPayload.linhas.forEach((linha) => {
                    const pid = linha.id || linha.produto_id;
                    const dias = linha.dias || {};
                    Object.entries(dias).forEach(([dia, cel]) => {
                        const selector = `[data-compact-prod="${pid}"][data-compact-dia="${dia}"]`;
                        document.querySelectorAll(selector).forEach((chip) => {
                            const metric = chip.dataset.compactMetric;
                            let val = 0;
                            if (metric === 'saida') val = cel.saida || 0;
                            if (metric === 'producao') val = cel.entrada_planejada || 0;
                            if (metric === 'entrada') val = cel.entrada_estoque || 0;
                            if (metric === 'estoque') val = cel.estoque_final || 0;
                            if (metric === 'percent') val = cel.producao_percentual || 0;
                            const valueEl = chip.querySelector('.value');
                            if (valueEl) {
                                if (metric === 'percent') {
                                    valueEl.textContent = `${Number(val || 0).toLocaleString('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%`;
                                } else {
                                    valueEl.textContent = fmtInt(val);
                                }
                            }
                            if (metric === 'estoque') {
                                const lowMin = Number(mapaPayload.low_min || 0);
                                const lowMax = Number(mapaPayload.low_max || 0);
                                let cls = 'compact-badge estoque';
                                if (val < 0) cls += ' negativo';
                                else if (val === 0) cls += ' zerado';
                                else if (val >= lowMin && val <= lowMax) cls += ' baixo';
                                chip.className = cls;
                            }
                        });
                    });
                });
            };

            document.querySelectorAll('.mini-form.compacto').forEach((form) => {
                form.addEventListener('submit', (ev) => {
                    ev.preventDefault();
                    const data = new FormData(form);
                    const submit = form.querySelector('button[type="submit"]');
                    if (submit) {
                        submit.disabled = true;
                        submit.textContent = '...';
                    }
                    fetch(form.action, {
                        method: 'POST',
                        body: data,
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    }).then(async (resp) => {
                        const payload = await resp.json().catch(() => ({}));
                        if (!resp.ok) throw new Error(payload.message || 'Erro ao salvar');
                        if (payload.mapa) {
                            updateFromPayload(payload.mapa);
                        }
                        showToast(payload.message || 'Salvo');
                    }).catch((err) => {
                        showToast(err.message || 'Erro ao salvar', 'error');
                    }).finally(() => {
                        if (submit) {
                            submit.disabled = false;
                            submit.textContent = '+';
                        }
                    });
                });
            });

        })();
    </script>
</section>
