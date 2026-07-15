<?php
$datas = $mapa['datas'] ?? [];
$linhas = $mapa['linhas'] ?? [];
$initialDailyLimit = 3;
$initialDailyLines = array_slice($linhas, 0, $initialDailyLimit);
$hoje = $mapa['hoje'] ?? date('Y-m-d');
$base = $mapa['base'] ?? $hoje;
if (isset($datas[$hoje])) {
    $base = $hoje;
}
$janelaInicio = $mapa['janela_inicio'] ?? $base;
$janelaFim = $mapa['janela_fim'] ?? $base;
$isFull = !empty($mapa['full']);
$visibleDatas = $datas;
$janelaVisInicio = array_key_first($visibleDatas) ?? $janelaInicio;
$janelaVisFim = array_key_last($visibleDatas) ?? $janelaFim;
$windowSize = max(1, count($visibleDatas));
if (!isset($datas[$base])) {
    if (isset($datas[$hoje])) {
        $base = $hoje;
    } elseif (!empty($datas)) {
        $base = array_key_first($datas);
    }
}
$lowMin = (int)($mapa['low_min'] ?? 4);
$lowMax = (int)($mapa['low_max'] ?? 8);
$meses = $mapa['meses'] ?? [];
$anos = $mapa['anos'] ?? [];
$prevRef = date('Y-m-d', strtotime($janelaInicio.' -'.$windowSize.' days'));
$nextRef = date('Y-m-d', strtotime($janelaInicio.' +'.$windowSize.' days'));
$baseMedias = [];
foreach ($datas as $diaKey => $info) {
    $baseMedias[$diaKey] = (float)($info['media_conclusao'] ?? 0);
}
?>
<section class="mapa">
    
    <style>
        .mapa { display: flex; flex-direction: column; gap: 14px; }
        .mapa .hero { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 14px 16px; box-shadow: var(--shadow-card); display: grid; grid-template-columns: 1fr auto; gap: 10px; align-items: center; }
        .mapa .hero h2 { margin: 0 0 4px; font-size: 1.02rem; letter-spacing: -0.02em; }
        .mapa .hero p { margin: 0; color: var(--text-muted); font-size: 0.9rem; }
        .mapa .legend-block { display: flex; flex-direction: column; gap: 8px; align-items: flex-end; }
        .mapa .legend { display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }
        .mapa .tab-bar { display: inline-flex; gap: 12px; width: fit-content; background: var(--surface); border: 1px solid var(--border); padding: 6px; border-radius: 999px; box-shadow: var(--shadow-soft); }
        .mapa .tab-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0 !important; background: transparent !important; color: var(--text-muted) !important; padding: 10px 20px; border-radius: 999px; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease; box-shadow: none; }
        .mapa .tab-btn svg { width: 17px; height: 17px; flex: 0 0 17px; }
        .mapa .tab-btn:not(.active):hover { background: var(--surface-alt) !important; color: var(--text) !important; }
        .mapa .tab-btn.active { background: var(--primary) !important; color: #fff !important; box-shadow: 0 6px 20px -12px var(--primary); }
        .mapa .map-pane[hidden] { display: none !important; }
        .mapa .pill { display: inline-flex; align-items: center; gap: 8px; padding: 6px 10px; border-radius: 999px; background: var(--surface-alt); border: 1px solid var(--border); color: var(--text); font-size: 0.86rem; box-shadow: var(--shadow-soft); }
        .mapa .status-chip { background: var(--surface-alt); border-radius: 12px; padding: 9px 12px; min-width: 150px; border: 1px solid var(--border); box-shadow: var(--shadow-soft); }
        .mapa .status-chip .status-label { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--text); }
        .mapa .status-chip .status-value { font-weight: 800; font-size: 1.1rem; color: var(--text); }
        .mapa .status-chip.negativo { background: rgba(225,29,72,0.12); border-color: rgba(225,29,72,0.28); color: #991b1b; }
        .mapa .status-chip.negativo .status-label, .mapa .status-chip.negativo .status-value { color: #991b1b; }
        .mapa .status-chip.zerado { background: rgba(250,204,21,0.14); border-color: rgba(250,204,21,0.3); color: #854d0e; }
        .mapa .status-chip.zerado .status-label, .mapa .status-chip.zerado .status-value { color: #854d0e; }
        .mapa .status-chip.baixo { background: rgba(249,115,22,0.14); border-color: rgba(249,115,22,0.32); color: #9a3412; }
        .mapa .status-chip.baixo .status-label, .mapa .status-chip.baixo .status-value { color: #9a3412; }
        .mapa .status-chip.positivo { background: rgba(34,197,94,0.14); border-color: rgba(34,197,94,0.3); color: #166534; }
        .mapa .status-chip.positivo .status-label, .mapa .status-chip.positivo .status-value { color: #166534; }
        .mapa .map-pane[data-map-pane="resumo"] .estoque-status-card { margin-bottom: 6px; }
        .mapa .estoque-status-icon { width: 46px; height: 46px; display: grid; place-items: center; flex: 0 0 46px; border-radius: 14px; background: color-mix(in srgb, var(--primary) 14%, transparent); color: var(--primary); }
        .mapa .swatch { width: 10px; height: 10px; border-radius: 50%; display: inline-block; box-shadow: 0 0 0 1px rgba(0,0,0,0.08); flex-shrink: 0; }
        .mapa .swatch.saida { background: #fda4af; box-shadow: 0 0 0 1px rgba(225, 29, 72, 0.24); }
        .mapa .swatch.entrada { background: #bae6fd; box-shadow: 0 0 0 1px rgba(14, 165, 233, 0.24); }
        .mapa .swatch.producao { background: #e9d5ff; box-shadow: 0 0 0 1px rgba(168, 85, 247, 0.24); }
        .mapa .swatch.estoque-swatch { width: 10px; height: 10px; border-radius: 50%; background: #bbf7d0; box-shadow: 0 0 0 1px rgba(22, 163, 74, 0.18); }
        .mapa .grid-card { background: var(--surface); border: 1px solid var(--border); border-radius: 18px; box-shadow: var(--shadow-soft); overflow: hidden; }
        .mapa table { width: 100%; border-collapse: separate; border-spacing: 14px 14px; min-width: 760px; }
        .mapa th, .mapa td { padding: 0; text-align: center; background: transparent; border: none; box-shadow: none; }
        .mapa thead th { background: transparent; font-size: 0.86rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
        .mapa .col-produto { text-align: left; width: 230px; position: sticky; left: 0; background: linear-gradient(90deg, var(--surface) 90%, rgba(255,255,255,0)); z-index: 2; padding-right: 8px; }
        .mapa .map-pane[data-map-pane="resumo"] thead th { position: sticky; top: 0; z-index: 8; background: var(--surface); }
        .mapa .map-pane[data-map-pane="resumo"] thead th.col-produto { left: 0; z-index: 12; padding-right: 0; text-align: center !important; vertical-align: middle; background: #fff !important; background-image: none !important; }
        .mapa .dia-header { display: grid; align-items: center; justify-items: center; gap: 6px; width: 100%; padding: 8px; border: 1px solid transparent; border-radius: 14px; box-sizing: border-box; }
        .mapa .dia-header.hoje { background: linear-gradient(180deg, rgba(11,116,255,0.08), rgba(11,116,255,0.02)); border-color: rgba(11,116,255,0.3); box-shadow: 0 6px 14px rgba(11,116,255,0.12); }
        .mapa .dia-header.hoje .dia-header-label, .mapa .dia-header.hoje .dia-header-week { color: #0b74ff; }
        .mapa .dia-header.hoje .dia-header-media { background: linear-gradient(135deg, rgba(11,116,255,0.14), rgba(11,116,255,0.22)); border-color: rgba(11,116,255,0.36); box-shadow: 0 7px 18px rgba(11,116,255,0.2); }
        .mapa .dia-header .dia-header-date { display: grid; gap: 2px; justify-items: center; }
        .mapa .dia-header .dia-header-date .dia-header-label { font-weight: 800; color: var(--text); letter-spacing: -0.01em; }
        .mapa .dia-header .dia-header-date .dia-header-week { color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; }
        .mapa .dia-header .dia-header-meta { display: grid; justify-items: center; gap: 7px; width: 100%; }
        .mapa .dia-header .dia-header-media { padding: 4px 10px; border-radius: 12px; background: linear-gradient(135deg, rgba(11,116,255,0.08), rgba(11,116,255,0.16)); color: #0b74ff; font-weight: 800; font-size: 0.9rem; border: 1px solid rgba(11,116,255,0.22); box-shadow: 0 6px 14px rgba(11,116,255,0.12); }
        .mapa .dia-header .feriado-toggle { width: 100%; box-sizing: border-box; justify-content: flex-start; padding: 9px 14px; border-radius: 12px; }
        .mapa .produto-card { background: #fff !important; border: 1px solid var(--border); border-radius: 16px; padding: 12px; box-shadow: var(--shadow-soft); display: grid; gap: 6px; }
        .mapa .sku { font-weight: 800; letter-spacing: 0.02em; color: var(--text); font-size: 0.9rem; }
        .mapa .nome { color: var(--text-muted); font-size: 0.86rem; margin-top: -2px; line-height: 1.3; }
        .mapa .saldo { display: inline-flex; align-items: center; gap: 6px; padding: 4px 9px; border-radius: 10px; margin-top: 2px; font-size: 0.86rem; border: 1px solid transparent; }
        .mapa .saldo strong { font-weight: 800; }
        .mapa .saldo.ok { background: linear-gradient(135deg, rgba(231,250,238,0.98), rgba(219,244,229,0.9)); border-color: rgba(22,163,74,0.24); color: #166534; }
        .mapa .saldo.negativo { background: linear-gradient(135deg, rgba(225,29,72,0.16), rgba(225,29,72,0.08)); border-color: rgba(225,29,72,0.28); color: #991b1b; }
        .mapa .saldo.zerado { background: linear-gradient(135deg, rgba(250,204,21,0.18), rgba(250,204,21,0.1)); border-color: rgba(250,204,21,0.3); color: #854d0e; }
        .mapa .saldo.baixo { background: linear-gradient(135deg, rgba(249,115,22,0.18), rgba(249,115,22,0.08)); border-color: rgba(249,115,22,0.32); color: #9a3412; }
        .mapa .dia-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 11px; box-shadow: var(--shadow-soft); display: grid; gap: 8px; min-width: 418px; }
        .map-scroll-hint { position: fixed; z-index: 9999; width: min(420px, calc(100vw - 24px)); padding: 13px 42px 13px 14px; border: 1px solid rgba(11,116,255,0.28); border-radius: 16px; background: color-mix(in srgb, var(--surface, #fff) 96%, #eaf3ff); color: var(--text, #172033); box-shadow: 0 18px 46px rgba(15,23,42,0.22); animation: mapHintIn 0.18s ease-out; }
        .map-scroll-hint.is-closing { opacity: 0; transform: translateY(4px); transition: opacity 0.16s ease, transform 0.16s ease; }
        .map-scroll-hint__close { position: absolute; top: 8px; right: 8px; width: 27px; height: 27px; display: grid; place-items: center; padding: 0; border: 0; border-radius: 50%; background: rgba(11,116,255,0.1); color: #0b74ff; cursor: pointer; }
        .map-scroll-hint__close svg { width: 14px; height: 14px; }
        .map-scroll-hint__title { display: flex; align-items: center; gap: 8px; margin-bottom: 9px; color: #0b74ff; font-size: 0.9rem; font-weight: 850; }
        .map-scroll-hint__title svg { width: 19px; height: 19px; flex: 0 0 auto; }
        .map-scroll-hint__tips { display: grid; grid-template-columns: 1fr 1fr; gap: 9px; }
        .map-scroll-hint__tip { display: grid; grid-template-columns: 32px 1fr; gap: 8px; align-items: center; padding: 8px; border-radius: 12px; background: var(--surface-alt, #f7f9fc); border: 1px solid var(--border, #e2e8f0); }
        .map-scroll-hint__tip > svg { width: 27px; height: 27px; color: #0b74ff; }
        .map-scroll-hint__tip strong { display: block; margin-bottom: 2px; font-size: 0.78rem; }
        .map-scroll-hint__tip span { display: block; color: var(--text-muted, #64748b); font-size: 0.72rem; line-height: 1.3; }
        .map-scroll-hint kbd { display: inline-flex; padding: 1px 5px; border: 1px solid var(--border, #cbd5e1); border-bottom-width: 2px; border-radius: 5px; background: var(--surface, #fff); color: var(--text, #172033); font: 700 0.68rem/1.3 inherit; }
        @keyframes mapHintIn { from { opacity: 0; transform: translateY(7px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @media (max-width: 620px) { .map-scroll-hint__tips { grid-template-columns: 1fr; } }
        .mapa th.dia-col-hoje, .mapa td.dia-col-hoje { width: 418px; min-width: 418px; max-width: 418px; }
        .mapa td.dia-col-hoje .dia-card { width: 418px; min-width: 418px; max-width: 418px; box-sizing: border-box; }
        .mapa .dia-head { display: flex; justify-content: space-between; align-items: baseline; gap: 6px; border-bottom: 1px dashed var(--border); padding-bottom: 4px; }
        .mapa .dia-head-right { display: inline-flex; align-items: center; gap: 10px; justify-content: flex-end; }
        .mapa .dia-head-right .dia-progress { text-align: right; }
        .mapa .dia-date { font-weight: 800; font-size: 0.9rem; color: var(--text); letter-spacing: 0.01em; }
        .mapa .dia-week { color: var(--text-muted); font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; }
        .mapa .dia-progress { display: grid; gap: 2px; text-align: right; }
        .mapa .dia-progress .progress-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); }
        .mapa .dia-progress .progress-value { font-weight: 800; color: #0b74ff; font-size: 0.82rem; }
        .mapa .dia-progress .progress-hint { font-size: 0.7rem; color: var(--text-muted); }
        .mapa .feriado-toggle { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border-radius: 12px; background: var(--surface-alt); border: 1px solid var(--border); color: var(--text-muted); font-weight: 700; font-size: 0.82rem; box-shadow: var(--shadow-soft); }
        .mapa .feriado-toggle input { width: 16px; height: 16px; accent-color: #f97316; }
        .mapa .feriado-toggle { flex-wrap: wrap; }
        .mapa .feriado-reason { flex: 1 0 100%; width: 100%; margin-top: 4px; padding: 7px 9px; border: 1px solid var(--border); border-radius: 9px; background: var(--surface); color: var(--text); font: inherit; font-size: 0.76rem; }
        .mapa .feriado-reason[hidden] { display: none !important; }
        .mapa .dia-header .feriado-toggle { padding: 9px 14px; font-size: 0.78rem; }
        .mapa .metrics-row { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; }
        .mapa .metric-chip { border-radius: 12px; padding: 10px 12px; min-height: 54px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; font-weight: 800; border: 1px solid transparent; box-shadow: inset 0 1px 0 rgba(255,255,255,0.35), 0 4px 14px rgba(0,0,0,0.04); text-align: center; }
        .mapa .metric-chip .label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; width: 100%; line-height: 1.1; }
        .mapa .metric-chip .value { font-size: 0.98rem; width: 100%; line-height: 1.1; }
        .mapa .metric-chip.saida { background: linear-gradient(135deg, #ffe0e3, #f9b4c0); border-color: rgba(199, 35, 72, 0.38); color: #b42318; }
        .mapa .metric-chip.producao { background: linear-gradient(135deg, #f4e5ff, #d8c2ff); border-color: rgba(109, 40, 217, 0.38); color: #6b21a8; }
        .mapa .metric-chip.entrada { background: linear-gradient(135deg, #e3f3ff, #bfe5ff); border-color: rgba(14, 116, 227, 0.36); color: #0f4f71; }
        .mapa .metric-chip.estoque { background: linear-gradient(135deg, #dffce9, #b4f0cc); border-color: rgba(22, 163, 74, 0.34); color: #166534; }
        .mapa .metric-chip.estoque.negativo { background: linear-gradient(135deg, #ffe0e3, #f9b4c0); color: #9b1c1c; border-color: rgba(225, 29, 72, 0.32); }
        .mapa .metric-chip.estoque.zerado { background: linear-gradient(135deg, #fff7d6, #fde68a); color: #854d0e; border-color: rgba(234, 179, 8, 0.36); box-shadow: 0 10px 24px rgba(234,179,8,0.12); }
        .mapa .metric-chip.estoque.baixo { background: linear-gradient(135deg, #ffe8d5, #fdba74); color: #9a3412; border-color: rgba(249, 115, 22, 0.32); box-shadow: 0 10px 24px rgba(249,115,22,0.12); }
        .mapa .forms-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .mapa .mini-form { background: var(--surface-alt); border: 1px solid var(--border); border-radius: 14px; padding: 6px 8px; display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 6px; box-shadow: inset 0 1px 0 rgba(255,255,255,0.5); }
        .mapa .mini-form label { font-size: 0.8rem; font-weight: 800; color: var(--text); margin: 0; text-transform: uppercase; letter-spacing: 0.02em; white-space: nowrap; text-align: center; }
        .mapa .mini-form .controls { display: contents; }
        .mapa .mini-form { font-size: 0.9rem; }
        .mapa .mini-form input { width: 100%; min-width: 70px; padding: 5px 7px; border-radius: 12px; border: 1px solid var(--border); background: #f8fbff; color: var(--text); font-weight: 700; text-align: right; }
        .mapa .mini-form button { border: none; background: #0b74ff; color: #fff; width: 29px; height: 29px; border-radius: 11px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 10px rgba(11,116,255,0.18); display: inline-flex; align-items: center; justify-content: center; font-size: 0.96rem; }
        .mapa .compact-card { padding: 14px; }
        .mapa .compact-header { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 12px; padding-bottom: 10px; border-bottom: 1px solid var(--border); }
        .mapa .compact-header h3 { margin: 0; font-size: 1rem; display: flex; align-items: center; gap: 8px; }
        .mapa .compact-title-icon { width: 36px; height: 36px; display: inline-grid; place-items: center; flex: 0 0 36px; border-radius: 11px; background: color-mix(in srgb, var(--primary) 14%, transparent); color: var(--primary); }
        .mapa .compact-title-icon svg { width: 21px; height: 21px; }
        .mapa .compact-status-icon { width: 36px; height: 36px; display: inline-grid; place-items: center; flex: 0 0 36px; border-radius: 11px; background: color-mix(in srgb, var(--primary) 14%, transparent); color: var(--primary); }
        .mapa .compact-status-icon svg { width: 21px; height: 21px; }
        .mapa .compact-year-nav { display: inline-flex; align-items: center; gap: 6px; }
        .mapa .compact-year-pill { min-height: 34px; box-sizing: border-box; display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; background: var(--surface-alt); border: 1px solid var(--border); border-radius: 11px; box-shadow: var(--shadow-soft); font-weight: 800; color: var(--text); }
        .mapa .compact-year-pill .year-label { min-width: 52px; text-align: center; letter-spacing: 0.02em; }
        .mapa .compact-actions { display: flex; gap: 6px; align-items: center; }
        .mapa .compact-actions .nav-btn { width: 34px; height: 34px; border-radius: 11px; }
        .mapa .compact-actions .nav-btn svg { width: 17px; height: 17px; }
        .mapa .compact-table { margin-top: 12px; overflow-x: auto; }
        .mapa .compact-grid table { width: 100%; border-collapse: collapse; min-width: 900px; }
        .mapa .compact-grid th, .mapa .compact-grid td { border: 0; padding: 7px; background: transparent; }
        .mapa .compact-grid th { position: sticky; top: 0; background: transparent; z-index: 2; }
        .mapa .compact-grid th.hoje { background: transparent; box-shadow: none; }
        .mapa .compact-prod { width: 230px; position: sticky; left: 0; padding: 0 8px 0 0 !important; vertical-align: middle; text-align: left; background: linear-gradient(90deg, var(--surface) 90%, rgba(255,255,255,0)); z-index: 10; }
        .mapa .compact-prod-info { display: grid; gap: 2px; }
        .mapa .compact-grid th.compact-prod, .mapa .compact-grid td.compact-prod { background: #fff !important; }
        .mapa .compact-grid thead th.compact-prod { top: 0; left: 0; z-index: 14; padding-right: 0 !important; text-align: center !important; vertical-align: middle; background-image: none !important; }
        .mapa .compact-grid tbody .compact-prod .produto-card { margin: 0; background-color: #fff !important; background-image: none !important; }
        .mapa .compact-th { display: grid; gap: 6px; justify-items: center; width: 100%; padding: 8px; border: 1px solid transparent; border-radius: 14px; box-sizing: border-box; text-align: center; font-weight: 700; color: var(--text); }
        .mapa .compact-th.hoje { background: linear-gradient(180deg, rgba(11,116,255,0.08), rgba(11,116,255,0.02)); border-color: rgba(11,116,255,0.3); box-shadow: 0 6px 14px rgba(11,116,255,0.12); }
        .mapa .compact-th.hoje > div, .mapa .compact-th.hoje > small { color: #0b74ff; }
        .mapa .compact-th.hoje .compact-media { background: linear-gradient(135deg, rgba(11,116,255,0.14), rgba(11,116,255,0.22)); border-color: rgba(11,116,255,0.36); box-shadow: 0 7px 18px rgba(11,116,255,0.2); }
        .mapa .compact-th .feriado-toggle { width: 100%; box-sizing: border-box; justify-content: flex-start; padding: 9px 14px; border-radius: 12px; }
        .mapa .compact-th small { color: var(--text-muted); font-size: 0.78rem; }
        .mapa .compact-th .compact-media { display: inline-flex; align-items: center; justify-content: center; justify-self: center; padding: 4px 10px; border-radius: 12px; background: linear-gradient(135deg, rgba(11,116,255,0.08), rgba(11,116,255,0.16)); color: #0b74ff; font-weight: 800; font-size: 0.82rem; border: 1px solid rgba(11,116,255,0.22); box-shadow: 0 6px 14px rgba(11,116,255,0.12); white-space: nowrap; }
        .mapa .compact-cell { display: grid; gap: 8px; min-width: 360px; padding: 10px; border: 1px solid var(--border); border-radius: 14px; background: var(--surface); box-shadow: var(--shadow-soft); }
        .mapa .compact-cell.hoje { background: linear-gradient(180deg, rgba(11,116,255,0.05), rgba(11,116,255,0.02)); border-color: rgba(11,116,255,0.28); box-shadow: 0 10px 24px rgba(11,116,255,0.12); }
        .mapa .compact-status { margin-top: 12px; padding: 10px 12px; border-radius: 14px; background: var(--surface-alt); border: 1px solid var(--border); box-shadow: var(--shadow-soft); display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .mapa .compact-status .status-chip { min-width: 140px; }
        .mapa .badge-row { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 6px; }
        .mapa .compact-badge { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 10px; border-radius: 12px; font-weight: 800; font-size: 0.82rem; letter-spacing: -0.01em; border: 1px solid transparent; box-shadow: inset 0 1px 0 rgba(255,255,255,0.5), 0 8px 18px rgba(0,0,0,0.06); }
        .mapa .compact-badge .label { font-weight: 700; font-size: 0.78rem; opacity: 0.85; }
        .mapa .compact-badge.saida { background: linear-gradient(135deg, #fee2e2, #fecdd3); color: #9f1239; border-color: rgba(225,29,72,0.28); }
        .mapa .compact-badge.producao { background: linear-gradient(135deg, #f3e8ff, #e9d5ff); color: #6b21a8; border-color: rgba(147,51,234,0.22); }
        .mapa .compact-badge.entrada { background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0ea5e9; border-color: rgba(14,165,233,0.24); }
        .mapa .compact-badge.estoque { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #166534; border-color: rgba(22,163,74,0.24); }
        .mapa .compact-badge.estoque.negativo { background: linear-gradient(135deg, #fee2e2, #fecdd3); color: #991b1b; border-color: rgba(225,29,72,0.32); }
        .mapa .compact-badge.estoque.zerado { background: linear-gradient(135deg, #fef9c3, #fef08a); color: #854d0e; border-color: rgba(245,158,11,0.32); }
        .mapa .compact-badge.estoque.baixo { background: linear-gradient(135deg, #ffedd5, #fed7aa); color: #9a3412; border-color: rgba(249,115,22,0.3); }
        .mapa .compact-badge.percent { background: linear-gradient(135deg, #e0ecff, #d0e2ff); color: #0b74ff; border-color: rgba(11,116,255,0.26); }
        .mapa .compact-empty { padding: 28px; text-align: center; color: var(--text-muted); border: 1px dashed var(--border); border-radius: 12px; background: var(--surface); }
        .mapa .status-bar { width: 100%; height: 12px; border-radius: 999px; overflow: hidden; background: var(--surface-alt); border: 1px solid var(--border); box-shadow: inset 0 1px 0 rgba(255,255,255,0.4); display: flex; }
        .mapa .status-bar span { display: block; height: 100%; transition: width 0.2s ease; }
        .mapa .status-bar .seg-neg { background: linear-gradient(90deg, #fecdd3, #fda4af); }
        .mapa .status-bar .seg-zero { background: linear-gradient(90deg, #fef08a, #fde68a); }
        .mapa .status-bar .seg-baixo { background: linear-gradient(90deg, #fed7aa, #fdba74); }
        .mapa .status-bar .seg-ok { background: linear-gradient(90deg, #bbf7d0, #86efac); }
        .mapa .status-bar-panel { width: 100%; display: grid; gap: 7px; }
        .mapa .status-percentages { display: flex; justify-content: flex-end; align-items: center; gap: 12px; flex-wrap: wrap; }
        .mapa .status-percentage { display: inline-flex; align-items: center; gap: 5px; color: var(--text-muted); font-size: 0.76rem; font-weight: 700; white-space: nowrap; }
        .mapa .status-percentage strong { color: var(--text); font-size: 0.78rem; }
        .mapa .status-percentage-dot { width: 9px; height: 9px; flex: 0 0 9px; border-radius: 50%; box-shadow: 0 0 0 3px rgba(0,0,0,0.025); }
        .mapa .status-percentage-dot.negativo { background: #fda4af; }
        .mapa .status-percentage-dot.zerado { background: #fde68a; }
        .mapa .status-percentage-dot.baixo { background: #fdba74; }
        .mapa .status-percentage-dot.ok { background: #86efac; }
        .mapa .table-wrap { overflow-x: auto; }
        .mapa tbody tr { content-visibility: auto; contain-intrinsic-size: 170px; }
        .mapa .map-pane[data-map-pane="completo"] .compact-table {
            contain: layout paint;
            overscroll-behavior: contain auto;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: auto;
            scrollbar-gutter: stable;
        }
        .mapa .map-pane[data-map-pane="completo"],
        .mapa .map-pane[data-map-pane="completo"] * {
            box-shadow: none !important;
        }
        .mapa .map-pane[data-map-pane="resumo"],
        .mapa .map-pane[data-map-pane="resumo"] * {
            box-shadow: none !important;
        }
        .mapa .map-pane[data-map-pane="completo"] tbody tr {
            content-visibility: auto;
            contain-intrinsic-size: 170px;
        }
        .mapa .map-pane[data-map-pane="completo"] .compact-day-card {
            contain: layout paint style;
        }
        .mapa .map-pane[data-map-pane="resumo"] .table-wrap,
        .mapa .map-pane[data-map-pane="resumo"] .nav-dias {
            contain: layout paint;
            overscroll-behavior-x: contain;
            overscroll-behavior-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        .mapa .map-pane[data-map-pane="resumo"] tbody tr,
        .mapa .map-pane[data-map-pane="resumo"] .dia-card,
        .mapa .map-pane[data-map-pane="resumo"] .produto-card,
        .mapa .map-pane[data-map-pane="resumo"] .metric-chip,
        .mapa .map-pane[data-map-pane="resumo"] .mini-form,
        .mapa .map-pane[data-map-pane="resumo"] .nav-chip {
            backface-visibility: hidden;
        }
        .mapa.is-scrolling .map-pane[data-map-pane="resumo"] .dia-card,
        .mapa.is-scrolling .map-pane[data-map-pane="resumo"] .produto-card,
        .mapa.is-scrolling .map-pane[data-map-pane="resumo"] .metric-chip,
        .mapa.is-scrolling .map-pane[data-map-pane="resumo"] .mini-form,
        .mapa.is-scrolling .map-pane[data-map-pane="resumo"] .nav-chip {
            transition: none !important;
            box-shadow: none !important;
        }
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .dia-card,
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .produto-card,
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .metric-chip,
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .mini-form,
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .compact-th {
            transition: none !important;
            box-shadow: none !important;
        }
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .compact-grid tbody,
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .compact-grid thead {
            pointer-events: none;
        }
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .compact-day-card,
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .compact-th {
            background-image: none !important;
            background-color: var(--surface) !important;
        }
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .metric-chip.saida { background: #f9b4c0 !important; }
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .metric-chip.producao { background: #d8c2ff !important; }
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .metric-chip.entrada { background: #bfe5ff !important; }
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .metric-chip.estoque { background: #b4f0cc !important; }
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .metric-chip.estoque.negativo { background: #f9b4c0 !important; }
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .metric-chip.estoque.zerado { background: #fde68a !important; }
        .mapa.is-scrolling .map-pane[data-map-pane="completo"] .metric-chip.estoque.baixo { background: #fdba74 !important; }
        .mapa .hint { color: var(--text-muted); font-size: 0.9rem; margin: 0; }
        .mapa .form-card { position: relative; z-index: 30; overflow: visible; background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 14px; box-shadow: var(--shadow-card); display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(0, 0.75fr) minmax(0, 0.75fr) minmax(0, 2.2fr); gap: 6px; align-items: end; }
        .mapa .form-card-title { grid-column: 1 / 4; display: flex; align-items: center; gap: 9px; margin: 0 0 4px; color: var(--text); font-size: 1.05rem; font-weight: 850; letter-spacing: -0.01em; transform: translateY(-16px); }
        .mapa .quick-entry-icon { width: 36px; height: 36px; display: inline-grid; place-items: center; flex: 0 0 36px; border-radius: 11px; background: color-mix(in srgb, var(--primary) 14%, transparent); color: var(--primary); }
        .mapa .quick-entry-icon svg { width: 21px; height: 21px; }
        .mapa .form-card .form-row-full { grid-column: 1 / -1; }
        .mapa .form-card .form-row-actions { grid-column: 1 / -1; }
        .mapa label { display: block; font-weight: 800; color: var(--text); margin-bottom: 6px; font-size: 1rem; }
        .mapa input[type="text"], .mapa input[type="number"], .mapa input[type="date"], .mapa select, .mapa textarea { width: 100%; border: 1px solid var(--border); background: var(--surface-alt); color: var(--text); border-radius: 10px; padding: 9px 10px; box-sizing: border-box; }
        .mapa .map-product-search { position: relative; }
        .mapa .map-product-search select { display: none; }
        .mapa .produto-search-input { padding-left: 38px !important; padding-right: 42px !important; }
        .mapa .produto-search-input:focus { outline: none; border-color: var(--primary); box-shadow: var(--primary-glow); }
        .mapa .produto-search-icon { position: absolute; z-index: 3; left: 12px; bottom: 10px; width: 17px; height: 17px; color: var(--text-muted); pointer-events: none; }
        .mapa .map-product-search > .search-clear-button { top: auto; right: 9px; bottom: 7px; transform: none; }
        .mapa .produto-suggestions { position: absolute; z-index: 1000; top: calc(100% + 6px); left: 0; right: 0; max-height: 260px; overflow: auto; margin: 0; padding: 6px; list-style: none; border: 1px solid var(--border); border-radius: 12px; background: var(--surface); box-shadow: 0 18px 38px rgba(15, 23, 42, 0.22); }
        .mapa .produto-suggestions[hidden] { display: none; }
        .mapa .map-product-search:focus-within .produto-suggestions { display: block !important; }
        .mapa .produto-suggestions li[hidden] { display: none !important; }
        .mapa .produto-suggestions button { width: 100%; border: 0; border-radius: 9px; padding: 9px 10px; background: transparent; color: var(--text); text-align: left; font-weight: 700; cursor: pointer; }
        .mapa .produto-suggestions button:hover, .mapa .produto-suggestions button:focus { outline: none; background: var(--surface-alt); }
        .mapa .produto-suggestion-sku { display: block; margin-top: 2px; color: var(--text-muted); font-size: 0.78rem; font-weight: 600; }
        .mapa .produto-suggestions-empty { padding: 9px 10px; color: var(--text-muted); font-size: 0.86rem; }
        .mapa .date-field input[type="date"] { width: 100%; }
        .mapa .date-field input[type="date"]::-webkit-calendar-picker-indicator { transform: scale(1.26); transform-origin: center; }
        .mapa .form-card > .quantity-field { margin-left: 0 !important; transform: none !important; }
        .mapa .quantity-field > label { transform: none !important; }
        .mapa .quantity-field input[type="number"] { width: 100%; transform: none !important; }
        .mapa .form-card > .map-product-search { grid-column: 1; }
        .mapa .form-card > .date-field { grid-column: 2; }
        .mapa .form-card > .quantity-field { grid-column: 3; }
        @media (min-width: 961px) {
            .mapa .form-card > .map-product-search,
            .mapa .form-card > .date-field,
            .mapa .form-card > .quantity-field { transform: translateY(-23px) !important; }
        }
        .mapa .form-card .registro-row { grid-column: 4; grid-row: 1 / 3; position: relative; align-self: stretch; min-width: 0; padding: 0 0 44px 24px; }
        .mapa .registro-meta { display: grid; grid-template-columns: max-content minmax(0, 1fr); grid-template-rows: auto auto; column-gap: 24px; row-gap: 12px; min-width: 0; width: calc(100% + 124px); transform: translateX(-124px); }
        .mapa .registro-meta > label { grid-column: 1; grid-row: 1; align-self: center; margin-bottom: 0; }
        .mapa .registro-meta > .choice { grid-column: 2; grid-row: 1; align-self: center; transform: translateY(9px); }
        .mapa .registro-meta > .hint { grid-column: 1 / -1; grid-row: 2; align-self: start; flex-direction: row; align-items: flex-start; gap: 22px; transform: translate(124px, 9px); }
        .mapa .registro-meta > .hint .hint-item:first-child { flex: 0 0 305px; align-items: flex-start; transform: translateX(12px); }
        .mapa .registro-meta > .hint .hint-item:first-child .hint-dot { margin-top: 4px; }
        .mapa .registro-meta > .hint .hint-item--entrada { flex: 0 0 220px; }
        .mapa .registro-meta > .hint .hint-item--entrada .hint-dot { transform: translateY(-16px); }
        .mapa .registro-meta > .hint .hint-item--entrada .hint-text { transform: translateY(-7px); }
        @media (min-width: 621px) { .mapa .registro-meta > .hint .hint-item--entrada { transform: translate(-99px, 9px); } }
        .mapa .registro-meta .hint-text { display: block; white-space: normal; line-height: 1.15; }
        .mapa .registro-meta .hint-desc { display: block; }
        .mapa .actions { position: absolute; right: 0; bottom: 0; display: flex; justify-content: flex-end; align-items: center; }
        .mapa .hint-inline { margin-top: 4px; }
        .mapa .hint { display: inline-flex; align-items: center; gap: 14px; flex-wrap: nowrap; }
        .mapa .hint p { margin: 0; }
        .mapa .hint-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0; box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.02); }
        .mapa .hint-dot--producao { background: #d8b4fe; box-shadow: 0 0 0 4px rgba(216, 180, 254, 0.35); }
        .mapa .hint-dot--entrada { background: #93c5fd; box-shadow: 0 0 0 4px rgba(147, 197, 253, 0.35); }
        .mapa .hint-item { display: inline-flex; align-items: center; gap: 8px; }
        .mapa .hint-item--entrada { margin-left: 0; }
        .mapa .hint-text { display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
        .mapa .hint-text .hint-title { font-weight: 600; }
        .mapa .weekly-average,
        .mapa .weekly-average-label { font-weight: 700; color: var(--primary); }
        .mapa .weekly-average-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; background: var(--primary); border: 1px solid transparent; color: #fff; font-weight: 800; box-shadow: none; }
        .mapa .weekly-average-btn .weekly-average-label,
        .mapa .weekly-average-btn .weekly-average { color: #fff; }
        .mapa .weekly-title-icon { width: 36px; height: 36px; display: inline-grid; place-items: center; flex: 0 0 36px; border-radius: 11px; background: color-mix(in srgb, var(--primary) 14%, transparent); color: var(--primary); }
        .mapa .weekly-title-icon svg { width: 21px; height: 21px; }
        .mapa .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: none; border-radius: 12px; padding: 10px 14px; font-weight: 700; cursor: pointer; text-decoration: none; }
        .mapa .btn.primary { background: var(--primary); color: #fff; box-shadow: var(--shadow-soft); }
        .mapa .btn.secondary { background: var(--surface-alt); color: var(--text); border: 1px solid var(--border); }
        .mapa .choice { display: flex; flex-direction: row; gap: 28px; align-items: center; flex-wrap: wrap; }
        .mapa .choice label { display: flex; align-items: center; gap: 8px; margin: 0; font-size: 0.86rem; cursor: pointer; transition: color 0.16s ease; }
        .mapa .choice .choice-entrada { margin-left: 0; }
        @media (min-width: 961px) { .mapa .choice .choice-entrada { transform: translateX(75px); } }
        @media (min-width: 961px) { .mapa .choice input[type="radio"][value="estoque"] { transform: translateX(-6px); } }
        .mapa .choice input[type="radio"] { appearance: none; -webkit-appearance: none; position: relative; width: 46px; height: 28px; flex: 0 0 46px; margin: 0; border: 1px solid var(--border); border-radius: 999px; background: color-mix(in srgb, var(--text-muted) 18%, var(--surface)); cursor: pointer; transition: background 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease; }
        .mapa .choice input[type="radio"]::before { content: ''; position: absolute; top: 3px; left: 3px; width: 20px; height: 20px; border-radius: 50%; background: #fff; box-shadow: 0 1px 4px rgba(15,23,42,0.28); transition: transform 0.18s ease; }
        .mapa .choice input[type="radio"]:checked::before { transform: translateX(18px); }
        .mapa .choice input[type="radio"][value="planejado"]:checked { background: #a855f7; border-color: #9333ea; box-shadow: 0 0 0 3px rgba(168,85,247,0.16); }
        .mapa .choice input[type="radio"][value="estoque"]:checked { background: #3b82f6; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(59,130,246,0.16); }
        .mapa .choice label:has(input[value="planejado"]:checked) { color: #7e22ce; }
        .mapa .choice label:has(input[value="estoque"]:checked) { color: #1d4ed8; }
        .mapa .choice input[type="radio"]:focus-visible { outline: 2px solid currentColor; outline-offset: 3px; }
        .mapa .nav-wrap { display: grid; grid-template-columns: 1fr; gap: 12px; align-items: stretch; }
        .mapa .map-pane[data-map-pane="resumo"] .nav-wrap { margin-bottom: 6px; }
        .mapa .nav-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 14px; box-shadow: var(--shadow-card); }
        .mapa .nav-dias-row { display: flex; gap: 10px; align-items: center; justify-content: space-between; flex-wrap: wrap; }
        .mapa .nav-btn { width: 42px; height: 42px; border-radius: 14px; border: 1px solid var(--border); background: var(--surface-alt); color: var(--text); display: grid; place-items: center; cursor: pointer; box-shadow: var(--shadow-soft); }
        .mapa .nav-btn.primary { width: auto; padding: 0 14px; font-weight: 800; background: linear-gradient(135deg, rgba(11,116,255,0.14), rgba(11,116,255,0.08)); color: #0b74ff; border-color: rgba(11,116,255,0.2); }
        .mapa .nav-btn:disabled { opacity: 0.55; cursor: not-allowed; }
        .mapa .nav-btn:hover { background: var(--surface-hover); }
        .mapa .nav-dias { display: flex; gap: 12px; overflow-x: auto; padding: 6px 4px; scroll-snap-type: x mandatory; }
        .mapa .nav-chip { scroll-snap-align: start; display: grid; gap: 4px; padding: 12px 12px 10px; border-radius: 14px; border: 1px solid var(--border); background: linear-gradient(180deg, #f8fbff, #f6f8fc); min-width: 148px; box-shadow: var(--shadow-soft); transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease; position: relative; }
        .mapa .nav-chip .nav-date { font-weight: 800; letter-spacing: -0.01em; font-size: 0.98rem; color: var(--text); }
        .mapa .nav-chip .nav-week { font-size: 0.78rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; }
        .mapa .nav-chip .nav-avg { font-weight: 800; font-size: 0.86rem; color: #0b74ff; }
        .mapa .nav-chip.ativo { border-color: rgba(11, 116, 255, 0.35); box-shadow: 0 10px 25px rgba(11,116,255,0.16); background: linear-gradient(135deg, rgba(11,116,255,0.08), rgba(11,116,255,0.02)); }
        .mapa .nav-chip.feriado { background: linear-gradient(135deg, #fff1e6, #ffe6d5); border-color: rgba(249,115,22,0.3); }
        .mapa .nav-chip.feriado .nav-date,
        .mapa .nav-chip.feriado .nav-week,
        .mapa .nav-chip.feriado .nav-avg { color: #c2410c; }
        .mapa .nav-chip:not(.ativo):not(.feriado) { background: linear-gradient(135deg, #fafafa, #f5f5f5); border-color: #e5e7eb; }
        .mapa .nav-chip:not(.ativo):not(.feriado) .nav-date { color: #374151; }
        .mapa .nav-chip:not(.ativo):not(.feriado) .nav-week,
        .mapa .nav-chip:not(.ativo):not(.feriado) .nav-avg { color: #4b5563; }
        .mapa .nav-chip:hover { transform: translateY(-2px); }
        .mapa .dia-card.hoje { border-color: rgba(11,116,255,0.24); box-shadow: 0 10px 24px rgba(11,116,255,0.12); background: linear-gradient(180deg, rgba(11,116,255,0.05), rgba(11,116,255,0.02)); }
        .mapa .dia-head.hoje .dia-date { color: #0b74ff; }
        .mapa .dia-head.hoje .dia-week { color: rgba(11,116,255,0.8); }
        body.theme-dark .mapa .tab-bar,
        body.theme-dark .mapa .grid-card,
        body.theme-dark .mapa .nav-card,
        body.theme-dark .mapa .form-card,
        body.theme-dark .mapa .dia-card,
        body.theme-dark .mapa .mini-form,
        body.theme-dark .mapa .compact-status {
            background: rgba(15, 23, 42, 0.72);
            border-color: rgba(148, 163, 184, 0.22);
            box-shadow: 0 18px 38px -30px rgba(0, 0, 0, 0.85);
        }
        body.theme-dark .mapa .produto-card,
        body.theme-dark .mapa .nav-chip {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.96), rgba(15, 23, 42, 0.92)) !important;
            border-color: rgba(148, 163, 184, 0.24);
            box-shadow: 0 16px 34px -28px rgba(0, 0, 0, 0.9);
        }
        body.theme-dark .mapa .nav-chip.ativo,
        body.theme-dark .mapa .dia-card.hoje {
            background: linear-gradient(180deg, rgba(var(--primary-rgb), 0.18), rgba(15, 23, 42, 0.92)) !important;
            border-color: rgba(var(--primary-rgb), 0.38);
            box-shadow: 0 16px 34px -28px rgba(var(--primary-rgb), 0.45);
        }
        body.theme-dark .mapa .nav-chip.feriado {
            background: linear-gradient(180deg, rgba(249, 115, 22, 0.18), rgba(15, 23, 42, 0.92)) !important;
            border-color: rgba(249, 115, 22, 0.34);
        }
        body.theme-dark .mapa input[type="text"],
        body.theme-dark .mapa input[type="number"],
        body.theme-dark .mapa input[type="date"],
        body.theme-dark .mapa select,
        body.theme-dark .mapa textarea,
        body.theme-dark .mapa .mini-form input {
            background: rgba(15, 23, 42, 0.88);
            border-color: rgba(148, 163, 184, 0.26);
            color: var(--text);
            box-shadow: none;
        }
        body.theme-dark .mapa .nav-btn,
        body.theme-dark .mapa .feriado-toggle,
        body.theme-dark .mapa .pill,
        body.theme-dark .mapa .compact-year-pill {
            background: rgba(30, 41, 59, 0.82);
            border-color: rgba(148, 163, 184, 0.22);
            color: var(--text);
            box-shadow: none;
        }
        body.theme-dark .mapa .dia-header .dia-header-media,
        body.theme-dark .mapa .nav-chip .nav-avg,
        body.theme-dark .mapa .compact-th .compact-media,
        body.theme-dark .mapa .dia-progress .progress-value {
            color: #93c5fd;
        }
        body.theme-dark .mapa .sku,
        body.theme-dark .mapa .nome,
        body.theme-dark .mapa .nav-chip .nav-date,
        body.theme-dark .mapa .nav-chip .nav-week {
            color: var(--text);
        }
        body.theme-dark .mapa .nome,
        body.theme-dark .mapa .nav-chip .nav-week {
            color: var(--text-muted);
        }
        @media (max-width: 1200px) {
            .mapa table { min-width: 720px; }
            .mapa .dia-card { min-width: 374px; }
            .mapa .nav-dias { gap: 8px; }
            .mapa .nav-chip { min-width: 120px; padding: 10px 10px 8px; }
            .mapa .nav-chip .nav-date { font-size: 0.9rem; }
            .mapa .nav-chip .nav-week { font-size: 0.72rem; }
            .mapa .nav-chip .nav-avg { font-size: 0.78rem; }
        }
        @media (max-width: 960px) {
            .mapa .hero { grid-template-columns: 1fr; }
            .mapa .legend-block { align-items: flex-start; }
            .mapa .legend { justify-content: flex-start; }
            .mapa .col-produto { position: sticky; left: 0; }
            .mapa .forms-row { grid-template-columns: 1fr; }
            .mapa .metrics-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .mapa .form-card { grid-template-columns: 1fr; }
            .mapa .form-card-title,
            .mapa .form-card > .map-product-search,
            .mapa .form-card > .date-field,
            .mapa .form-card > .quantity-field,
            .mapa .form-card .registro-row { grid-column: 1; grid-row: auto; }
            .mapa .form-card > .quantity-field { margin-left: 0 !important; }
            .mapa .form-card .registro-row { padding: 0; }
            .mapa .registro-meta { width: 100%; transform: none; }
            .mapa .registro-meta > .choice { transform: none; }
            .mapa .registro-meta > .hint { transform: none; }
            .mapa .form-card .actions { position: static; margin-top: 12px; }
        }
        @media (max-width: 620px) {
            .mapa .registro-meta > .hint { flex-direction: column; }
            .mapa .registro-meta > .hint .hint-item:first-child,
            .mapa .registro-meta > .hint .hint-item--entrada { flex-basis: auto; }
        }
    </style>

    <?php $status = $mapa['status'] ?? ['negativos' => 0, 'zerados' => 0, 'baixos' => 0]; ?>
    <div class="hero">
        <div>
            <h2>Mapa diário de produção, saídas e metas</h2>
            <p class="hint">Navegue por qualquer semana (anterior ou futura) para analisar produção e saídas. Estoque do dia = estoque anterior + entrada − saída; produção é neutra para o saldo e alimenta a conclusão de produção.</p>
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
        <a class="tab-btn<?= $isFull ? '' : ' active'; ?>" data-map-tab="resumo" href="<?= htmlspecialchars($url('/mapa'), ENT_QUOTES, 'UTF-8'); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></svg><span>Mapa diário</span></a>
        <a class="tab-btn<?= $isFull ? ' active' : ''; ?>" data-map-tab="completo" href="<?= htmlspecialchars($url('/mapa?full=1#completo'), ENT_QUOTES, 'UTF-8'); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg><span>Mapa completo</span></a>
    </div>

    <div class="map-pane" data-map-pane="resumo"<?= $isFull ? ' hidden' : ''; ?>>
        <div class="grid-card estoque-status-card" style="padding: 14px 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 10px; align-items: center;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div class="estoque-status-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/></svg>
                </div>
                <div>
                    <div style="font-weight:700; color:var(--text);">Status do estoque</div>
                    <div style="color:var(--text-muted); font-size:0.92rem;">Acompanhe produtos em falta, negativos ou com estoque baixo.</div>
                </div>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-start; flex-wrap:wrap;">
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
            </div>
        </div>

        <div class="nav-wrap">
            <div class="nav-card">
                <?php
                    $mediaSemanalTotal = 0;
                    $mediaSemanalCount = 0;
                    foreach ($visibleDatas as $info) {
                        if (isset($info['media_conclusao'])) {
                            $mediaSemanalTotal += (float)$info['media_conclusao'];
                            $mediaSemanalCount++;
                        }
                    }
                    $mediaSemanal = $mediaSemanalCount ? ($mediaSemanalTotal / $mediaSemanalCount) : 0;
                ?>
                <div class="nav-dias-row" style="margin-bottom:8px;">
                    <div style="font-weight:800; color:var(--text); display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <span class="weekly-title-icon" title="Média de 7 dias" aria-label="Média de 7 dias"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="3"/><path d="M7 2v4m10-4v4M3 9h18M7 17v-2m3 2v-4m3 4v-3m3 3v-6m3 6v-5"/></svg></span>
                    <span>Média diária & Semanal</span>
                    <small id="janela-range" style="color:var(--text-muted); font-weight:600;">Janela <?= htmlspecialchars(date('d/m', strtotime($janelaVisInicio)), ENT_QUOTES, 'UTF-8'); ?> – <?= htmlspecialchars(date('d/m', strtotime($janelaVisFim)), ENT_QUOTES, 'UTF-8'); ?> • <span class="weekly-average-btn"><span class="weekly-average-label">Média semanal:</span> <span class="weekly-average"><?= number_format($mediaSemanal, 1, ',', '.'); ?>%</span></span></small>
                </div>
            </div>
            <div class="nav-dias-row">
                <div class="nav-dias" id="nav-dias">
                    <?php foreach ($visibleDatas as $dia => $info): $isHoje = !empty($info['is_hoje']); ?>
                        <div class="nav-chip<?= $isHoje ? ' ativo' : ''; ?>" data-dia-nav="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">
                            <span class="nav-date"><?= htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="nav-week"><?= htmlspecialchars($info['semana'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="nav-avg" data-nav-media="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" data-media-base="<?= number_format((float)($info['media_conclusao'] ?? 0), 1, '.', ''); ?>">Média <?= number_format((float)($info['media_conclusao'] ?? 0), 1, ',', '.'); ?>%</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <form class="form-card" action="<?= htmlspecialchars($url('/mapa/entrada'), ENT_QUOTES, 'UTF-8'); ?>" method="post">
        <h3 class="form-card-title"><span class="quick-entry-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m13 2-9 12h8l-1 8 9-12h-8z"/><path d="M18 3v4m-2-2h4"/></svg></span><span>Lançamento rápido</span></h3>
        <input type="hidden" name="full" value="<?= $isFull ? '1' : '0'; ?>">
        <div class="map-product-search">
            <label for="produto_search">Produto acabado</label>
            <input class="produto-search-input" id="produto_search" type="text" autocomplete="off" placeholder="Buscar produto..." aria-label="Buscar produto acabado" aria-autocomplete="list" aria-controls="produto_suggestions" required>
            <svg class="produto-search-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
            <ul class="produto-suggestions" id="produto_suggestions" role="listbox" hidden>
                <?php foreach ($linhas as $linha): ?>
                    <?php
                        $produtoId = (string)($linha['id'] ?? '');
                        $produtoSku = trim((string)($linha['sku'] ?? ''));
                        $produtoNome = trim((string)($linha['nome'] ?? ''));
                        $produtoBusca = trim($produtoSku.' '.$produtoNome);
                    ?>
                    <li data-product-search="<?= htmlspecialchars($produtoBusca, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="button" role="option" data-product-id="<?= htmlspecialchars($produtoId, ENT_QUOTES, 'UTF-8'); ?>" data-product-label="<?= htmlspecialchars($produtoBusca, ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($produtoNome ?: $produtoBusca, ENT_QUOTES, 'UTF-8'); ?>
                            <span class="produto-suggestion-sku"><?= htmlspecialchars($produtoSku, ENT_QUOTES, 'UTF-8'); ?></span>
                        </button>
                    </li>
                <?php endforeach; ?>
                <li class="produto-suggestions-empty" data-product-empty hidden>Nenhum produto encontrado.</li>
            </ul>
            <select id="produto_id" name="produto_id" aria-hidden="true" tabindex="-1">
                <option value="">Selecione</option>
                <?php foreach ($linhas as $linha): ?>
                    <option value="<?= htmlspecialchars($linha['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars(($linha['sku'] ?? '').' — '.($linha['nome'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="date-field">
            <label for="data_referencia">Dia</label>
            <input type="date" id="data_referencia" name="data_referencia" value="<?= date('Y-m-d'); ?>" required>
        </div>
        <div class="quantity-field">
            <label for="quantidade">Quantidade</label>
            <input type="number" step="0.01" min="0" id="quantidade" name="quantidade" placeholder="Ex.: 120" required>
        </div>
        <div class="registro-row form-row-full">
            <div class="registro-meta">
                <label>Tipo de registro:</label>
                <div class="choice">
                    <label><input type="radio" name="tipo" value="planejado" checked> Produção</label>
                    <label class="choice-entrada"><input type="radio" name="tipo" value="estoque"> Entrada</label>
                </div>
                <div class="hint hint-inline">
                    <span class="hint-item">
                        <span class="hint-dot hint-dot--producao" aria-hidden="true"></span>
                        <span class="hint-text">
                            <span class="hint-title">Produção é apenas</span>
                            <span class="hint-desc">planejamento e não<br>muda o estoque.</span>
                        </span>
                    </span>
                    <span class="hint-item hint-item--entrada">
                        <span class="hint-dot hint-dot--entrada" aria-hidden="true"></span>
                        <span class="hint-text">
                            <span class="hint-title">Entradas de estoque</span>
                            <span class="hint-desc">atualizam o saldo real.</span>
                        </span>
                    </span>
                </div>
            </div>
            <div class="actions">
                <button class="btn primary" type="submit">Registrar</button>
            </div>
        </div>
    </form>

    <script>
        (() => {
            const form = document.currentScript.previousElementSibling;
            const search = form?.querySelector('#produto_search');
            const select = form?.querySelector('#produto_id');
            const list = form?.querySelector('#produto_suggestions');
            if (!form || !search || !select || !list) return;
            search.dataset.bound = '1';
            const items = Array.from(list.querySelectorAll('li[data-product-search]'));
            const empty = list.querySelector('[data-product-empty]');
            const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
            const open = () => { list.hidden = false; };
            const filter = () => {
                const term = normalize(search.value.trim());
                let visible = 0;
                items.forEach((item) => {
                    const match = !term || normalize(item.dataset.productSearch).includes(term);
                    item.hidden = !match;
                    if (match) visible++;
                });
                if (empty) empty.hidden = visible !== 0;
                open();
            };
            const choose = (button) => {
                select.value = button.dataset.productId || '';
                search.value = button.dataset.productLabel || button.textContent.trim();
                search.setCustomValidity('');
                list.hidden = true;
                search.blur();
            };
            items.forEach((item) => item.querySelector('button')?.addEventListener('mousedown', (event) => {
                event.preventDefault();
                choose(event.currentTarget);
            }));
            search.addEventListener('focus', filter);
            search.addEventListener('click', filter);
            search.addEventListener('input', () => { select.value = ''; search.setCustomValidity(''); filter(); });
            search.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') { list.hidden = true; search.blur(); }
                if (event.key === 'Enter') {
                    const first = items.find((item) => !item.hidden)?.querySelector('button');
                    if (first) { event.preventDefault(); choose(first); }
                }
            });
            form.querySelector('.map-product-search')?.addEventListener('focusout', (event) => {
                if (!event.currentTarget.contains(event.relatedTarget)) list.hidden = true;
            });
            document.addEventListener('mousedown', (event) => {
                if (!event.target.closest('.map-product-search')) list.hidden = true;
            });
            form.addEventListener('submit', (event) => {
                if (select.value) return;
                event.preventDefault();
                event.stopImmediatePropagation();
                search.setCustomValidity('Selecione um produto da lista.');
                search.reportValidity();
                filter();
            }, true);
        })();
    </script>

    <div class="grid-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="col-produto">Produto</th>
                        <?php foreach ($visibleDatas as $dia => $info): $isHoje = !empty($info['is_hoje']); ?>
                            <th class="<?= $isHoje ? 'dia-col-hoje' : ''; ?>">
                                    <div class="dia-header<?= $isHoje ? ' hoje' : ''; ?>" data-dia-header="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">
                                        <div class="dia-header-date">
                                            <span class="dia-header-label"><?= htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <small class="dia-header-week"><?= htmlspecialchars($info['semana'], ENT_QUOTES, 'UTF-8'); ?></small>
                                        </div>
                                        <div class="dia-header-meta">
                                            <span class="dia-header-media" data-dia-media="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" data-media-base="<?= number_format((float)($info['media_conclusao'] ?? 0), 1, '.', ''); ?>">Conclusão <?= number_format((float)($info['media_conclusao'] ?? 0), 1, ',', '.'); ?>%</span>
                                            <label class="feriado-toggle" title="Marcar este dia como sem produção">
                                                <input type="checkbox" data-feriado="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>"> Sem produção
                                            </label>
                                        </div>
                                    </div>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody data-initial-count="<?= count($initialDailyLines); ?>">
                    <?php if (!$linhas): ?>
                        <tr>
                            <td class="col-produto" colspan="<?= 1 + count($visibleDatas); ?>" style="text-align:center; padding: 28px 12px;">
                                Nenhum produto acabado cadastrado ainda.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($initialDailyLines as $linha): ?>
                        <?php
                            $ultimoDia = array_key_last($visibleDatas);
                            $saldo = (float)($linha['dias'][$ultimoDia]['estoque_final'] ?? $linha['estoque'] ?? 0);
                            $saldoClass = 'ok';
                            if ($saldo < 0) {
                                $saldoClass = 'negativo';
                            } elseif ($saldo === 0.0) {
                                $saldoClass = 'zerado';
                            } elseif ($saldo >= $lowMin && $saldo <= $lowMax) {
                                $saldoClass = 'baixo';
                            }
                        ?>
                        <tr data-produto="<?= (int)($linha['id'] ?? 0); ?>">
                            <td class="col-produto">
                                <div class="produto-card" data-product-card="<?= (int)($linha['id'] ?? 0); ?>">
                                    <div class="sku"><?= htmlspecialchars($linha['sku'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="nome"><?= htmlspecialchars($linha['nome'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="saldo <?= $saldoClass; ?>">
                                        <span>Saldo:</span>
                                        <strong><?= number_format($saldo, 0, ',', '.'); ?></strong>
                                    </div>
                                </div>
                            </td>
                    <?php foreach ($visibleDatas as $dia => $info):
                                $isHoje = !empty($info['is_hoje']);
                                $celula = $linha['dias'][$dia] ?? ['saida' => 0, 'entrada_planejada' => 0, 'entrada_estoque' => 0, 'estoque_inicial' => 0, 'estoque_final' => 0, 'sobra' => 0, 'producao_percentual' => 0];
                                $saida = (float)$celula['saida'];
                                $entradaPlanejada = (float)$celula['entrada_planejada'];
                                $entradaEstoque = (float)$celula['entrada_estoque'];
                                $entradaTotal = (float)($celula['entrada_total'] ?? $entradaEstoque);
                                $entradaPlanejadaFmt = $entradaPlanejada > 0 ? htmlspecialchars(number_format($entradaPlanejada, 2, '.', ''), ENT_QUOTES, 'UTF-8') : '';
                                $entradaEstoqueFmt = $entradaEstoque > 0 ? htmlspecialchars(number_format($entradaEstoque, 2, '.', ''), ENT_QUOTES, 'UTF-8') : '';
                                $estoqueFinal = (float)($celula['estoque_final'] ?? 0);
                                $conclusao = (float)($celula['producao_percentual'] ?? 0);
                                $conclusaoFmt = number_format($conclusao, 1, ',', '.');
                                $estoqueClass = '';
                                if ($estoqueFinal < 0) {
                                    $estoqueClass = 'negativo';
                                } elseif ($estoqueFinal === 0.0) {
                                    $estoqueClass = 'zerado';
                                } elseif ($estoqueFinal >= $lowMin && $estoqueFinal <= $lowMax) {
                                    $estoqueClass = 'baixo';
                                }
                            ?>
                                <td class="<?= $isHoje ? 'dia-col-hoje' : ''; ?>">
                                    <div class="dia-card<?= $isHoje ? ' hoje' : ''; ?>" data-dia="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" data-produto="<?= (int)$linha['id']; ?>">
                                        <div class="dia-head<?= $isHoje ? ' hoje' : ''; ?>">
                                            <div>
                                                <span class="dia-date"><?= htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                <span class="dia-week"><?= htmlspecialchars($info['semana'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                            <div class="dia-head-right">
                                                <div class="dia-progress">
                                                    <span class="progress-label">Conclusão de produção</span>
                                                    <span class="progress-value" data-progress-base="<?= $conclusaoFmt; ?>%">
                                                        <?= $conclusaoFmt; ?>%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="metrics-row">
                                            <div class="metric-chip saida">
                                                <span class="label">Saída</span>
                                                <span class="value"><?= $saida > 0 ? number_format($saida, 0, ',', '.') : '0'; ?></span>
                                            </div>
                                            <div class="metric-chip producao">
                                                <span class="label">Produção</span>
                                                <span class="value"><?= $entradaPlanejada > 0 ? number_format($entradaPlanejada, 0, ',', '.') : '0'; ?></span>
                                            </div>
                                            <div class="metric-chip entrada">
                                                <span class="label">Entrada</span>
                                                <span class="value"><?= $entradaEstoque > 0 ? number_format($entradaEstoque, 0, ',', '.') : '0'; ?></span>
                                            </div>
                                            <div class="metric-chip estoque <?= $estoqueClass; ?>">
                                                <span class="label">Estoque</span>
                                                <span class="value"><?= number_format($estoqueFinal, 0, ',', '.'); ?></span>
                                            </div>
                                        </div>
                                        <div class="forms-row">
                                            <form action="<?= htmlspecialchars($url('/mapa/entrada'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mini-form">
                                                <label for="producao-<?= (int)$linha['id']; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">Produção</label>
                                                <div class="controls">
                                                    <input type="hidden" name="tipo" value="planejado">
                                                    <input type="hidden" name="produto_id" value="<?= (int)$linha['id']; ?>">
                                                    <input type="hidden" name="data_referencia" value="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <input type="hidden" name="janela_inicio" value="<?= htmlspecialchars($janelaInicio, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <input type="hidden" name="full" value="<?= $isFull ? '1' : '0'; ?>">
                                                    <input id="producao-<?= (int)$linha['id']; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" type="number" step="0.01" min="0" name="quantidade" value="<?= $entradaPlanejadaFmt; ?>" placeholder="0">
                                                    <button type="submit" title="Registrar produção">+</button>
                                                </div>
                                            </form>
                                            <form action="<?= htmlspecialchars($url('/mapa/entrada'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mini-form">
                                                <label for="entrada-<?= (int)$linha['id']; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">Entrada</label>
                                                <div class="controls">
                                                    <input type="hidden" name="tipo" value="estoque">
                                                    <input type="hidden" name="produto_id" value="<?= (int)$linha['id']; ?>">
                                                    <input type="hidden" name="data_referencia" value="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <input type="hidden" name="janela_inicio" value="<?= htmlspecialchars($janelaInicio, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <input type="hidden" name="full" value="<?= $isFull ? '1' : '0'; ?>">
                                                    <input id="entrada-<?= (int)$linha['id']; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" type="number" step="0.01" min="0" name="quantidade" value="<?= $entradaEstoqueFmt; ?>" placeholder="0">
                                                    <button type="submit" title="Adicionar estoque">+</button>
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
    </div>
    </div>

    <div class="map-pane" data-map-pane="completo"<?= $isFull ? '' : ' hidden'; ?>>
        <div class="grid-card compact-card">
            <div class="compact-header">
                <div>
                    <h3><span class="compact-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></span>Mapa completo</h3>
                    <p class="hint">Grade anual com saídas, produção planejada, entradas e estoque coloridos como no mapa diário. O dia atual fica marcado e você pode navegar livremente entre anos.</p>
                </div>
                <div class="compact-actions">
                    <div class="compact-year-nav">
                        <button class="nav-btn" type="button" id="year-prev" aria-label="Ano anterior">◀</button>
                        <div class="compact-year-pill">
                            <span class="year-label" id="compact-year-label">—</span>
                        </div>
                        <button class="nav-btn" type="button" id="year-next" aria-label="Próximo ano">▶</button>
                    </div>
                    <div class="compact-year-nav">
                        <button class="nav-btn" type="button" id="month-prev" aria-label="Mês anterior">◀</button>
                        <div class="compact-year-pill"><span class="year-label" id="compact-month-label">—</span></div>
                        <button class="nav-btn" type="button" id="month-next" aria-label="Próximo mês">▶</button>
                    </div>
                    <button class="nav-btn" type="button" id="refresh-full" aria-label="Recarregar mapa completo" title="Recarregar mapa completo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 4v5h5"/><path d="M4 13a8.1 8.1 0 0 0 15.5 2M20 20v-5h-5"/></svg></button>
                </div>
            </div>
            <div class="compact-status">
                <div style="font-weight:700; color:var(--text); display:flex; align-items:center; gap:8px;">
                    <span class="compact-status-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/></svg></span>
                    Status do estoque
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
                <div class="status-chip positivo">
                    <div class="status-label">Positivo</div>
                    <div class="status-value" data-status="ok"><?= max(count($linhas) - (int)($status['negativos'] ?? 0) - (int)($status['zerados'] ?? 0) - (int)($status['baixos'] ?? 0), 0); ?></div>
                </div>
                <div class="status-bar-panel">
                    <div class="status-percentages" aria-label="Percentuais do status do estoque">
                        <span class="status-percentage"><i class="status-percentage-dot negativo"></i>Negativo <strong data-status-pct="negativos">0,0%</strong></span>
                        <span class="status-percentage"><i class="status-percentage-dot zerado"></i>Sem estoque <strong data-status-pct="zerados">0,0%</strong></span>
                        <span class="status-percentage"><i class="status-percentage-dot baixo"></i>Estoque baixo <strong data-status-pct="baixos">0,0%</strong></span>
                        <span class="status-percentage"><i class="status-percentage-dot ok"></i>Normal <strong data-status-pct="ok">100,0%</strong></span>
                    </div>
                    <div class="status-bar" aria-hidden="true">
                        <span class="seg-neg" data-status-seg="negativos" style="width:0%"></span>
                        <span class="seg-zero" data-status-seg="zerados" style="width:0%"></span>
                        <span class="seg-baixo" data-status-seg="baixos" style="width:0%"></span>
                        <span class="seg-ok" data-status-seg="ok" style="width:100%"></span>
                    </div>
                </div>
            </div>
            <?php
            $compactInitialMonth = substr($hoje, 0, 7);
            $compactDatas = array_filter($datas, static fn($dia) => str_starts_with((string)$dia, $compactInitialMonth), ARRAY_FILTER_USE_KEY);
            $compactLinhas = $linhas;
            $compactInitialLinhas = array_slice($compactLinhas, 0, 2);
            $compactLowMin = $lowMin;
            $compactLowMax = $lowMax;
            ?>
            <div class="compact-table" id="compact-table" aria-live="polite">
                <?php if (!empty($compactDatas) && !empty($compactLinhas)): ?>
                    <?php
                    $fmtInt = static function ($num) {
                        return number_format((float)$num, 0, ',', '.');
                    };
                    $compactHeader = '<th class="compact-prod">Produto</th>';
                    foreach (array_keys($compactDatas) as $dia) {
                        $info = $compactDatas[$dia];
                        $mediaDia = number_format((float)($info['media_conclusao'] ?? 0), 1, ',', '');
                        $hojeCls = !empty($info['is_hoje']) ? ' hoje' : '';
                        $compactHeader .= '<th class="' . $hojeCls . '"><div class="compact-th' . $hojeCls . '"><div>'
                            . htmlspecialchars($info['label'] ?? $dia, ENT_QUOTES, 'UTF-8')
                            . '</div><small>' . htmlspecialchars($info['semana'] ?? '', ENT_QUOTES, 'UTF-8') . '</small><span class="compact-media" data-compact-media="' . htmlspecialchars($dia, ENT_QUOTES, 'UTF-8') . '">Conclusão '
                            . $mediaDia . '%</span><label class="feriado-toggle"><input type="checkbox" data-feriado="' . htmlspecialchars($dia, ENT_QUOTES, 'UTF-8') . '"> Sem produção</label></div></th>';
                    }
                    ?>
                    <div class="compact-grid">
                        <table>
                            <thead>
                                <tr><?= $compactHeader; ?></tr>
                            </thead>
                            <tbody data-compact-initial-count="<?= count($compactInitialLinhas); ?>">
                                <?php foreach ($compactInitialLinhas as $linha): ?>
                                    <?php
                                    $produtoSku = htmlspecialchars($linha['sku'] ?? '—', ENT_QUOTES, 'UTF-8');
                                    $produtoNome = htmlspecialchars($linha['nome'] ?? '', ENT_QUOTES, 'UTF-8');
                                    $compactLastDay = array_key_last($compactDatas);
                                    $compactSaldo = (float)($linha['dias'][$compactLastDay]['estoque_final'] ?? $linha['estoque'] ?? 0);
                                    $compactSaldoClass = $compactSaldo < 0 ? 'negativo' : ($compactSaldo === 0.0 ? 'zerado' : (($compactSaldo >= $compactLowMin && $compactSaldo <= $compactLowMax) ? 'baixo' : 'ok'));
                                    ?>
                                    <tr>
                                        <td class="compact-prod"><div class="produto-card compact-product-card" data-product-card="<?= (int)($linha['id'] ?? 0); ?>"><div class="sku"><?= $produtoSku; ?></div><div class="nome"><?= $produtoNome; ?></div><div class="saldo <?= $compactSaldoClass; ?>"><span>Saldo:</span><strong><?= $fmtInt($compactSaldo); ?></strong></div></div></td>
                                        <?php foreach (array_keys($compactDatas) as $dia): ?>
                                            <?php
                                            $celula = $linha['dias'][$dia] ?? [];
                                            $saida = (float)($celula['saida'] ?? 0);
                                            $producao = (float)($celula['entrada_planejada'] ?? 0);
                                            $entrada = (float)($celula['entrada_estoque'] ?? 0);
                                            $estoque = (float)($celula['estoque_final'] ?? 0);
                                            $conclusao = number_format((float)($celula['producao_percentual'] ?? 0), 1, ',', '');
                                            $estoqueClass = 'estoque';
                                            if ($estoque < 0) {
                                                $estoqueClass .= ' negativo';
                                            } elseif ($estoque === 0.0) {
                                                $estoqueClass .= ' zerado';
                                            } elseif ($estoque >= $compactLowMin && $estoque <= $compactLowMax) {
                                                $estoqueClass .= ' baixo';
                                            }
                                            ?>
                                            <?php $compactHoje = !empty($compactDatas[$dia]['is_hoje']); ?>
                                            <td>
                                                <div class="dia-card compact-day-card<?= $compactHoje ? ' hoje' : ''; ?>" data-dia="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" data-produto="<?= (int)$linha['id']; ?>">
                                                    <div class="dia-head<?= $compactHoje ? ' hoje' : ''; ?>">
                                                        <div>
                                                            <span class="dia-date"><?= htmlspecialchars($compactDatas[$dia]['label'] ?? $dia, ENT_QUOTES, 'UTF-8'); ?></span>
                                                            <span class="dia-week"><?= htmlspecialchars($compactDatas[$dia]['semana'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                        </div>
                                                        <div class="dia-progress">
                                                            <span class="progress-label">Conclusão de produção</span>
                                                            <span class="progress-value"><?= $conclusao; ?>%</span>
                                                        </div>
                                                    </div>
                                                    <div class="metrics-row">
                                                        <div class="metric-chip saida"><span class="label">Saída</span><span class="value"><?= $fmtInt($saida); ?></span></div>
                                                        <div class="metric-chip producao"><span class="label">Produção</span><span class="value"><?= $fmtInt($producao); ?></span></div>
                                                        <div class="metric-chip entrada"><span class="label">Entrada</span><span class="value"><?= $fmtInt($entrada); ?></span></div>
                                                        <div class="metric-chip <?= $estoqueClass; ?>"><span class="label">Estoque</span><span class="value"><?= $fmtInt($estoque); ?></span></div>
                                                    </div>
                                                    <div class="forms-row">
                                                        <form action="<?= htmlspecialchars($url('/mapa/entrada'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mini-form">
                                                            <label for="compact-producao-<?= (int)$linha['id']; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">Produção</label>
                                                            <div class="controls">
                                                                <input type="hidden" name="tipo" value="planejado"><input type="hidden" name="produto_id" value="<?= (int)$linha['id']; ?>"><input type="hidden" name="data_referencia" value="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="janela_inicio" value="<?= htmlspecialchars($compactInitialMonth . '-01', ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="month" value="<?= htmlspecialchars($compactInitialMonth, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="full" value="1">
                                                                <input id="compact-producao-<?= (int)$linha['id']; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" type="number" step="0.01" min="0" name="quantidade" value="<?= $producao ? number_format($producao, 2, '.', '') : ''; ?>" placeholder="0"><button type="submit" title="Registrar produção">+</button>
                                                            </div>
                                                        </form>
                                                        <form action="<?= htmlspecialchars($url('/mapa/entrada'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mini-form">
                                                            <label for="compact-entrada-<?= (int)$linha['id']; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">Entrada</label>
                                                            <div class="controls">
                                                                <input type="hidden" name="tipo" value="estoque"><input type="hidden" name="produto_id" value="<?= (int)$linha['id']; ?>"><input type="hidden" name="data_referencia" value="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="janela_inicio" value="<?= htmlspecialchars($compactInitialMonth . '-01', ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="month" value="<?= htmlspecialchars($compactInitialMonth, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="full" value="1">
                                                                <input id="compact-entrada-<?= (int)$linha['id']; ?>-<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" type="number" step="0.01" min="0" name="quantidade" value="<?= $entrada ? number_format($entrada, 2, '.', '') : ''; ?>" placeholder="0"><button type="submit" title="Adicionar estoque">+</button>
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
                    <div class="compact-empty">Nenhum registro encontrado. Clique em "Mapa completo" para carregar ou adicione lançamentos.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const mapaUrl = '<?= htmlspecialchars($url('/mapa'), ENT_QUOTES, 'UTF-8'); ?>';
            const semProducaoUrl = '<?= htmlspecialchars($url('/mapa/sem-producao'), ENT_QUOTES, 'UTF-8'); ?>';
            let mapaState = <?= json_encode($mapa, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
            const preferHoje = (state) => {
                if (state && state.hoje && state.datas && state.datas[state.hoje]) {
                    state.base = state.hoje;
                }
            };
            preferHoje(mapaState);
            let windowSize = 7;
            let fullMode = !!(mapaState.full);
            const feriados = JSON.parse(localStorage.getItem('mapaFeriados') || '{}');
            Object.entries(mapaState.datas || {}).forEach(([dia, info]) => {
                if (info?.sem_producao) feriados[dia] = { active: true, reason: info.sem_producao_motivo || 'outro' };
                else delete feriados[dia];
            });
            const feriadoReasons = [
                ['folga', 'Folga'],
                ['feriado', 'Feriado nacional, estadual ou municipal'],
                ['parada_programada', 'Parada programada'],
                ['manutencao', 'Manutenção preventiva ou corretiva'],
                ['falta_materia_prima', 'Falta de matéria-prima'],
                ['falta_energia', 'Falta de energia ou utilidades'],
                ['problema_equipamento', 'Problema em máquina ou equipamento'],
                ['equipe_indisponivel', 'Equipe indisponível'],
                ['sem_demanda', 'Sem demanda de produção'],
                ['clima_logistica', 'Clima ou problema logístico'],
                ['qualidade', 'Bloqueio de qualidade'],
                ['outro', 'Outro motivo'],
            ];
            const feriadoAtivo = (dia) => feriados[dia] === true || (!!feriados[dia] && feriados[dia].active !== false);
            const feriadoMotivo = (dia) => typeof feriados[dia] === 'object' ? (feriados[dia].reason || 'feriado') : 'feriado';
            const persistirSemProducao = (dia) => {
                const body = new URLSearchParams({
                    data_referencia: dia,
                    ativo: feriadoAtivo(dia) ? '1' : '0',
                    motivo: feriadoMotivo(dia),
                });
                return fetch(semProducaoUrl, {
                    method: 'POST',
                    body,
                    cache: 'no-store',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                }).then(async (response) => {
                    const payload = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(payload.message || 'Erro ao salvar dia sem produção');
                    compactMonthCache.delete(dia.slice(0, 7));
                    return payload;
                });
            };
            const refreshFullBtn = document.getElementById('refresh-full');
            const tabButtons = Array.from(document.querySelectorAll('[data-map-tab]'));
            const panes = Array.from(document.querySelectorAll('[data-map-pane]'));
            let activePane = fullMode || window.location.hash === '#completo' ? 'completo' : 'resumo';
            let fullRendered = false;
            let compactRenderToken = 0;
            let compactRowsObserver = null;
            let dailyRenderToken = 0;
            let dailyRowsObserver = null;
            let dailyScrollHandler = null;
            const compactMonthCache = new Map();
            const compactMonthCacheLimit = 3;
            const rememberCompactMonth = (month, payload) => {
                compactMonthCache.delete(month);
                compactMonthCache.set(month, payload);
                while (compactMonthCache.size > compactMonthCacheLimit) {
                    compactMonthCache.delete(compactMonthCache.keys().next().value);
                }
                return payload;
            };
            const cachedCompactMonth = (month) => {
                if (!compactMonthCache.has(month)) return null;
                return rememberCompactMonth(month, compactMonthCache.get(month));
            };
            let initialDailyMarkup = true;
            let compactYear = (mapaState.base || mapaState.hoje || new Date().toISOString().slice(0, 10)).slice(0, 4);
            let compactMonth = (mapaState.hoje || mapaState.base || new Date().toISOString().slice(0, 10)).slice(0, 7);
            const yearLabel = document.getElementById('compact-year-label');
            const monthLabel = document.getElementById('compact-month-label');
            const updateYearLabel = () => {
                if (yearLabel) yearLabel.textContent = compactYear;
                if (monthLabel) {
                    const [year, month] = compactMonth.split('-').map(Number);
                    monthLabel.textContent = new Intl.DateTimeFormat('pt-BR', { month: 'short' }).format(new Date(year, month - 1, 1)).replace('.', '').toUpperCase();
                }
            };

            const integerFormatter = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            const decimalFormatter = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
            const fmtInt = (num) => integerFormatter.format(Number(num || 0));
            const fmtDate = (dia) => {
                const [y, m, d] = dia.split('-');
                return `${d}/${m}`;
            };
            const addDays = (dia, delta) => {
                const dt = new Date(`${dia}T00:00:00`);
                dt.setDate(dt.getDate() + delta);
                return dt.toISOString().slice(0, 10);
            };
            const sortedDates = () => Object.keys(mapaState.datas || {}).sort();
            const visibleDates = () => {
                return sortedDates();
            };
            windowSize = Math.max(1, visibleDates().length);

            const installScrollOptimizer = () => {
                const root = document.querySelector('.mapa');
                if (!root) return;
                let timeoutId = 0;
                let ticking = false;
                const markScrolling = () => {
                    if (!ticking) {
                        ticking = true;
                        requestAnimationFrame(() => {
                            root.classList.add('is-scrolling');
                            ticking = false;
                        });
                    }
                    window.clearTimeout(timeoutId);
                    timeoutId = window.setTimeout(() => {
                        root.classList.remove('is-scrolling');
                    }, 220);
                };
                document.querySelectorAll('.map-pane[data-map-pane="resumo"] .table-wrap, .map-pane[data-map-pane="resumo"] .nav-dias, .map-pane[data-map-pane="completo"] .compact-table').forEach((el) => {
                    el.addEventListener('scroll', markScrolling, { passive: true });
                });
            };

            const showToast = (msg, type = 'success') => {
                let container = document.querySelector('.mapa-toast');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'mapa-toast';
                    container.style.position = 'fixed';
                    container.style.top = '16px';
                    container.style.right = '16px';
                    container.style.zIndex = '1500';
                    document.body.appendChild(container);
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
                container.appendChild(toast);
                setTimeout(() => toast.remove(), 2200);
            };

            const applyFullMode = () => {
                document.querySelectorAll('[data-nav-direction]').forEach((btn) => {
                    btn.disabled = fullMode;
                });
                document.querySelectorAll('input[name="full"]').forEach((input) => {
                    input.value = fullMode ? '1' : '0';
                });
                updateYearLabel();
            };

            const fetchMapa = async (params = {}) => {
                const qs = new URLSearchParams();
                if (params.start) qs.set('start', params.start);
                if (params.ref) qs.set('ref', params.ref);
                if ((params.full ?? fullMode) === true) {
                    qs.set('full', '1');
                    qs.set('month', params.month || compactMonth);
                }
                qs.set('_live', `${Date.now()}-${Math.random().toString(16).slice(2)}`);
                const resp = await fetch(`${mapaUrl}${qs.toString() ? '?' + qs.toString() : ''}`, {
                    method: 'GET',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache',
                    },
                });
                const payload = await resp.json();
                if (!resp.ok) throw new Error(payload.message || 'Erro ao navegar');
                mapaState = payload;
                Object.entries(payload.datas || {}).forEach(([dia, info]) => {
                    if (info?.sem_producao) feriados[dia] = { active: true, reason: info.sem_producao_motivo || 'outro' };
                    else delete feriados[dia];
                });
                localStorage.setItem('mapaFeriados', JSON.stringify(feriados));
                if (payload.full) rememberCompactMonth(params.month || compactMonth, payload);
                preferHoje(mapaState);
                fullMode = !!(mapaState.full);
                windowSize = Math.max(1, visibleDates().length);
                if (params.full || !compactYear) {
                    const refDate = mapaState.janela_inicio || mapaState.base || mapaState.hoje || new Date().toISOString().slice(0, 10);
                    compactYear = String(refDate).slice(0, 4);
                }
                if (params.render !== false) {
                    renderAll();
                    applyFullMode();
                }
            };

            const renderRange = () => {
                const dates = visibleDates();
                const label = document.getElementById('janela-range');
                if (!label || !dates.length) return;
                const weeklyValues = dates.filter((dia) => !feriadoAtivo(dia)).map((dia) => Number(mapaState?.datas?.[dia]?.media_conclusao ?? 0)).filter((val) => !Number.isNaN(val));
                const weeklyAvg = weeklyValues.length ? (weeklyValues.reduce((sum, val) => sum + val, 0) / weeklyValues.length) : 0;
                const weeklyText = weeklyAvg.toFixed(1).replace('.', ',');
                label.innerHTML = `Janela ${fmtDate(dates[0])} – ${fmtDate(dates[dates.length - 1])} • <span class="weekly-average-btn"><span class="weekly-average-label">Média semanal:</span> <span class="weekly-average">${weeklyText}%</span></span>`;
            };

            const navChipMarkup = (dia, info) => {
                const media = Number(info.media_conclusao ?? 0).toFixed(1).replace('.', ',');
                const isHoje = info.is_hoje ? ' ativo' : '';
                const fer = feriados[dia] ? ' feriado' : '';
                return `<div class="nav-chip${isHoje}${fer}" data-dia-nav="${dia}">` +
                    `<span class="nav-date">${info.label || fmtDate(dia)}</span>` +
                    `<span class="nav-week">${info.semana || ''}</span>` +
                    `<span class="nav-avg" data-nav-media="${dia}" data-media-base="${Number(info.media_conclusao || 0).toFixed(1)}">${feriadoAtivo(dia) ? 'Sem produção' : `Média ${media}%`}</span>` +
                `</div>`;
            };

            const headerCellMarkup = (dia, info) => {
                const media = Number(info.media_conclusao ?? 0).toFixed(1).replace('.', ',');
                return `<th class="${info.is_hoje ? 'dia-col-hoje' : ''}"><div class="dia-header${info.is_hoje ? ' hoje' : ''}" data-dia-header="${dia}">` +
                    `<div class="dia-header-date"><span class="dia-header-label">${info.label || fmtDate(dia)}</span>` +
                    `<small class="dia-header-week">${info.semana || ''}</small></div>` +
                    `<div class="dia-header-meta">` +
                        `<span class="dia-header-media" data-dia-media="${dia}" data-media-base="${Number(info.media_conclusao || 0).toFixed(1)}">${feriadoAtivo(dia) ? 'Sem produção' : `Conclusão ${media}%`}</span>` +
                        `<label class="feriado-toggle" title="Marcar este dia como sem produção"><input type="checkbox" data-feriado="${dia}"> Sem produção</label>` +
                    `</div>` +
                `</div></th>`;
            };

            const cellMarkup = (linha, dia, info) => {
                const celula = linha.dias?.[dia] ?? {};
                const saida = Number(celula.saida || 0);
                const entradaPlanejada = Number(celula.entrada_planejada || 0);
                const entradaEstoque = Number(celula.entrada_estoque || 0);
                const estoqueFinal = Number(celula.estoque_final || 0);
                const conclusao = Number(celula.producao_percentual || 0).toFixed(1);
                let estoqueClass = '';
                const lowMin = Number(mapaState.low_min || 0);
                const lowMax = Number(mapaState.low_max || 0);
                if (estoqueFinal < 0) {
                    estoqueClass = 'negativo';
                } else if (estoqueFinal === 0) {
                    estoqueClass = 'zerado';
                } else if (estoqueFinal >= lowMin && estoqueFinal <= lowMax) {
                    estoqueClass = 'baixo';
                }
                const janelaInicio = mapaState.janela_inicio || dia;
                return `<td class="${info.is_hoje ? 'dia-col-hoje' : ''}"><div class="dia-card${info.is_hoje ? ' hoje' : ''}" data-dia="${dia}" data-produto="${linha.id}">` +
                    `<div class="dia-head${info.is_hoje ? ' hoje' : ''}">` +
                        `<div><span class="dia-date">${info.label || fmtDate(dia)}</span><span class="dia-week">${info.semana || ''}</span></div>` +
                        `<div class="dia-head-right"><div class="dia-progress"><span class="progress-label">Conclusão de produção</span><span class="progress-value" data-progress-base="${conclusao.replace('.', ',')}%">${feriados[dia] ? '—' : `${conclusao.replace('.', ',')}%`}</span></div></div>` +
                    `</div>` +
                    `<div class="metrics-row">` +
                        `<div class="metric-chip saida"><span class="label">Saída</span><span class="value">${fmtInt(saida)}</span></div>` +
                        `<div class="metric-chip producao"><span class="label">Produção</span><span class="value">${fmtInt(entradaPlanejada)}</span></div>` +
                        `<div class="metric-chip entrada"><span class="label">Entrada</span><span class="value">${fmtInt(entradaEstoque)}</span></div>` +
                        `<div class="metric-chip estoque ${estoqueClass}"><span class="label">Estoque</span><span class="value">${fmtInt(estoqueFinal)}</span></div>` +
                    `</div>` +
                    `<div class="forms-row">` +
                        `<form action="<?= htmlspecialchars($url('/mapa/entrada'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mini-form">` +
                            `<label for="producao-${linha.id}-${dia}">Produção</label>` +
                            `<div class="controls">` +
                                `<input type="hidden" name="tipo" value="planejado">` +
                                `<input type="hidden" name="produto_id" value="${linha.id}">` +
                                `<input type="hidden" name="data_referencia" value="${dia}">` +
                                `<input type="hidden" name="janela_inicio" value="${janelaInicio}">` +
                                `<input type="hidden" name="full" value="${fullMode ? '1' : '0'}">` +
                                `<input id="producao-${linha.id}-${dia}" type="number" step="0.01" min="0" name="quantidade" value="${entradaPlanejada ? entradaPlanejada.toFixed(2) : ''}" placeholder="0">` +
                                `<button type="submit" title="Registrar produção">+</button>` +
                            `</div>` +
                        `</form>` +
                        `<form action="<?= htmlspecialchars($url('/mapa/entrada'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mini-form">` +
                            `<label for="entrada-${linha.id}-${dia}">Entrada</label>` +
                            `<div class="controls">` +
                                `<input type="hidden" name="tipo" value="estoque">` +
                                `<input type="hidden" name="produto_id" value="${linha.id}">` +
                                `<input type="hidden" name="data_referencia" value="${dia}">` +
                                `<input type="hidden" name="janela_inicio" value="${janelaInicio}">` +
                                `<input type="hidden" name="full" value="${fullMode ? '1' : '0'}">` +
                                `<input id="entrada-${linha.id}-${dia}" type="number" step="0.01" min="0" name="quantidade" value="${entradaEstoque ? entradaEstoque.toFixed(2) : ''}" placeholder="0">` +
                                `<button type="submit" title="Adicionar estoque">+</button>` +
                            `</div>` +
                        `</form>` +
                    `</div>` +
                `</div></td>`;
            };

            const rowMarkup = (linha, datas) => {
                const lowMin = Number(mapaState.low_min || 0);
                const lowMax = Number(mapaState.low_max || 0);
                const cells = datas.map((dia) => cellMarkup(linha, dia, mapaState.datas[dia] || {})).join('');
                const lastDay = datas[datas.length - 1];
                const saldo = Number(linha?.dias?.[lastDay]?.estoque_final ?? linha.estoque ?? 0);
                let saldoClass = 'ok';
                if (saldo < 0) saldoClass = 'negativo';
                else if (saldo === 0) saldoClass = 'zerado';
                else if (saldo >= lowMin && saldo <= lowMax) saldoClass = 'baixo';
                return `<tr data-produto="${linha.id}"><td class="col-produto"><div class="produto-card" data-product-card="${linha.id}"><div class="sku">${linha.sku || '—'}</div><div class="nome">${linha.nome || ''}</div><div class="saldo ${saldoClass}"><span>Saldo:</span><strong>${fmtInt(saldo)}</strong></div></div></td>${cells}</tr>`;
            };

            const renderTabela = () => {
                const datas = visibleDates();
                const nav = document.getElementById('nav-dias');
                const headRow = document.querySelector('.mapa thead tr');
                const tbody = document.querySelector('.mapa tbody');
                if (!nav || !headRow || !tbody) return;

                nav.innerHTML = datas.map((dia) => navChipMarkup(dia, mapaState.datas[dia])).join('');
                headRow.innerHTML = '<th class="col-produto">Produto</th>' + datas.map((dia) => headerCellMarkup(dia, mapaState.datas[dia])).join('');

                const lines = mapaState.linhas || [];
                const initialCount = Math.min(3, lines.length);
                const linhasHtml = lines.slice(0, initialCount).map((linha) => rowMarkup(linha, datas)).join('');

                tbody.innerHTML = linhasHtml || `<tr><td class="col-produto" colspan="${1 + datas.length}" style="text-align:center; padding: 28px 12px;">Nenhum produto acabado cadastrado ainda.</td></tr>`;
                tbody.dataset.initialCount = String(initialCount);
                window.setTimeout(loadRemainingDailyRows, 0);
            };

            const loadRemainingDailyRows = () => {
                if (activePane !== 'resumo') return;
                const tbody = document.querySelector('.mapa .map-pane[data-map-pane="resumo"] tbody');
                const lines = mapaState.linhas || [];
                const dates = visibleDates();
                if (!tbody || !dates.length) return;
                let index = Number(tbody.dataset.initialCount || tbody.querySelectorAll('tr[data-produto]').length || 0);
                dailyRowsObserver?.disconnect();
                dailyRowsObserver = null;
                if (dailyScrollHandler) {
                    window.removeEventListener('scroll', dailyScrollHandler);
                    dailyScrollHandler = null;
                }
                const token = ++dailyRenderToken;
                tbody.querySelector('[data-daily-row-sentinel]')?.remove();
                if (index >= lines.length) return;
                const sentinel = document.createElement('tr');
                sentinel.dataset.dailyRowSentinel = '1';
                sentinel.innerHTML = `<td colspan="${dates.length + 1}" style="height:1px;padding:0;border:0;"></td>`;
                tbody.appendChild(sentinel);
                const appendBatch = () => {
                    if (token !== dailyRenderToken || activePane !== 'resumo' || !sentinel.isConnected || index >= lines.length) return;
                    if (document.querySelector('.mapa')?.classList.contains('is-scrolling')) {
                        window.setTimeout(appendBatch, 120);
                        return;
                    }
                    const end = Math.min(index + 3, lines.length);
                    const addedCount = end - index;
                    sentinel.insertAdjacentHTML('beforebegin', lines.slice(index, end).map((line) => rowMarkup(line, dates)).join(''));
                    index = end;
                    tbody.dataset.initialCount = String(index);
                    const addedRows = [];
                    let previousRow = sentinel.previousElementSibling;
                    for (let i = 0; i < addedCount && previousRow; i += 1) {
                        addedRows.unshift(previousRow);
                        previousRow = previousRow.previousElementSibling;
                    }
                    addedRows.forEach((row) => {
                        row.querySelectorAll('form:not([data-mapa-submit-bound="1"])').forEach(handleSubmit);
                    });
                    if (index >= lines.length) {
                        dailyRowsObserver?.disconnect();
                        dailyRowsObserver = null;
                        if (dailyScrollHandler) {
                            window.removeEventListener('scroll', dailyScrollHandler);
                            dailyScrollHandler = null;
                        }
                        sentinel.remove();
                    } else {
                        window.requestAnimationFrame(() => {
                            if (sentinel.isConnected && sentinel.getBoundingClientRect().top < window.innerHeight + 700) appendBatch();
                        });
                    }
                };
                if ('IntersectionObserver' in window) {
                    dailyRowsObserver = new IntersectionObserver((entries) => {
                        if (entries.some((entry) => entry.isIntersecting)) appendBatch();
                    }, { root: null, rootMargin: '1400px 0px', threshold: 0 });
                    dailyRowsObserver.observe(sentinel);
                    let dailyScrollTicking = false;
                    dailyScrollHandler = () => {
                        if (dailyScrollTicking || token !== dailyRenderToken || !sentinel.isConnected) return;
                        dailyScrollTicking = true;
                        window.requestAnimationFrame(() => {
                            dailyScrollTicking = false;
                            if (sentinel.isConnected && sentinel.getBoundingClientRect().top < window.innerHeight + 1400) {
                                appendBatch();
                            }
                        });
                    };
                    window.addEventListener('scroll', dailyScrollHandler, { passive: true });
                    window.setTimeout(dailyScrollHandler, 0);
                } else {
                    const appendFallbackRows = () => {
                        appendBatch();
                        if (token === dailyRenderToken && index < lines.length) window.setTimeout(appendFallbackRows, 64);
                    };
                    window.setTimeout(appendFallbackRows, 0);
                }
            };

            const forceLiveCellUpdate = (produtoId, dia) => {
                const linha = (mapaState.linhas || []).find((item) => String(item.id) === String(produtoId));
                const celula = linha?.dias?.[dia];
                const paneRoot = document.querySelector(`.map-pane[data-map-pane="${activePane}"]`);
                const card = paneRoot?.querySelector(`.dia-card[data-produto="${produtoId}"][data-dia="${dia}"]`);
                if (!linha || !celula || !card) return;
                const values = {
                    saida: Number(celula.saida || 0),
                    producao: Number(celula.entrada_planejada || 0),
                    entrada: Number(celula.entrada_estoque || 0),
                    estoque: Number(celula.estoque_final || 0),
                };
                Object.entries(values).forEach(([key, value]) => {
                    const target = card.querySelector(`.metric-chip.${key} .value`);
                    if (target) target.textContent = fmtInt(value);
                });
                const progress = card.querySelector('.progress-value');
                if (progress) {
                    const value = `${Number(celula.producao_percentual || 0).toFixed(1).replace('.', ',')}%`;
                    progress.dataset.progressBase = value;
                    progress.textContent = feriados[dia] ? '—' : value;
                }
                const productionInput = card.querySelector('form input[name="tipo"][value="planejado"]')?.closest('form')?.querySelector('input[name="quantidade"]');
                const stockInput = card.querySelector('form input[name="tipo"][value="estoque"]')?.closest('form')?.querySelector('input[name="quantidade"]');
                if (productionInput) productionInput.value = values.producao ? values.producao.toFixed(2) : '';
                if (stockInput) stockInput.value = values.entrada ? values.entrada.toFixed(2) : '';
                const dates = visibleDates();
                const lastDay = dates[dates.length - 1] || dia;
                const balance = Number(linha.dias?.[lastDay]?.estoque_final ?? linha.estoque ?? 0);
                const balanceTarget = paneRoot?.querySelector(`[data-product-card="${produtoId}"] .saldo strong`);
                if (balanceTarget) balanceTarget.textContent = fmtInt(balance);
                applyStatus();
                applyNavMedias();
            };

            const applyOptimisticCellUpdate = (produtoId, dia, tipo, quantidade) => {
                const linha = (mapaState.linhas || []).find((item) => String(item.id) === String(produtoId));
                const celula = linha?.dias?.[dia];
                const paneRoot = document.querySelector(`.map-pane[data-map-pane="${activePane}"]`);
                const card = paneRoot?.querySelector(`.dia-card[data-produto="${produtoId}"][data-dia="${dia}"]`);
                if (!linha || !celula || !card) return;
                const value = Number(quantidade || 0);
                if (tipo === 'planejado') {
                    celula.entrada_planejada = value;
                } else {
                    const previousEntry = Number(celula.entrada_estoque || 0);
                    const difference = value - previousEntry;
                    celula.entrada_estoque = value;
                    celula.estoque_final = Number(celula.estoque_final || 0) + difference;
                    celula.estoque_pos_saida = Number(celula.estoque_pos_saida || 0) + difference;
                    celula.sobra = Number(celula.sobra || 0) + difference;
                    const dates = visibleDates();
                    const targetIndex = dates.indexOf(dia);
                    dates.slice(Math.max(0, targetIndex + 1)).forEach((nextDay) => {
                        const nextCell = linha.dias?.[nextDay];
                        if (!nextCell) return;
                        nextCell.estoque_inicial = Number(nextCell.estoque_inicial || 0) + difference;
                        nextCell.estoque_final = Number(nextCell.estoque_final || 0) + difference;
                        nextCell.estoque_pos_saida = Number(nextCell.estoque_pos_saida || 0) + difference;
                        nextCell.sobra = Number(nextCell.sobra || 0) + difference;
                        const nextStock = paneRoot?.querySelector(`.dia-card[data-produto="${produtoId}"][data-dia="${nextDay}"] .metric-chip.estoque .value`);
                        if (nextStock) nextStock.textContent = fmtInt(nextCell.estoque_final);
                    });
                }
                const planned = Number(celula.entrada_planejada || 0);
                const entry = Number(celula.entrada_estoque || 0);
                celula.producao_percentual = planned > 0 ? (entry / planned) * 100 : 0;
                const productionTarget = card.querySelector('.metric-chip.producao .value');
                const entryTarget = card.querySelector('.metric-chip.entrada .value');
                const stockTarget = card.querySelector('.metric-chip.estoque .value');
                const progressTarget = card.querySelector('.progress-value');
                if (productionTarget) productionTarget.textContent = fmtInt(planned);
                if (entryTarget) entryTarget.textContent = fmtInt(entry);
                if (stockTarget) stockTarget.textContent = fmtInt(celula.estoque_final);
                const dates = visibleDates();
                if (progressTarget) progressTarget.textContent = `${celula.producao_percentual.toFixed(1).replace('.', ',')}%`;
                const dayTotals = (mapaState.linhas || []).reduce((totals, item) => {
                    totals.planned += Number(item.dias?.[dia]?.entrada_planejada || 0);
                    totals.entry += Number(item.dias?.[dia]?.entrada_estoque || 0);
                    return totals;
                }, { planned: 0, entry: 0 });
                mapaState.datas[dia].media_conclusao = dayTotals.planned > 0 ? (dayTotals.entry / dayTotals.planned) * 100 : 0;
                applyNavMedias();
                const lastDay = dates[dates.length - 1] || dia;
                const balanceValue = Number(linha.dias?.[lastDay]?.estoque_final ?? celula.estoque_final ?? 0);
                const balanceBadge = paneRoot?.querySelector(`[data-product-card="${produtoId}"] .saldo`);
                const balanceTarget = balanceBadge?.querySelector('strong');
                if (balanceTarget) balanceTarget.textContent = fmtInt(balanceValue);
                if (balanceBadge) {
                    balanceBadge.classList.remove('negativo', 'zerado', 'baixo', 'ok');
                    const lowMin = Number(mapaState.low_min || 0);
                    const lowMax = Number(mapaState.low_max || 0);
                    balanceBadge.classList.add(balanceValue < 0 ? 'negativo' : balanceValue === 0 ? 'zerado' : (balanceValue >= lowMin && balanceValue <= lowMax ? 'baixo' : 'ok'));
                }
                const statusDay = mapaState.hoje || dia;
                const liveStatus = { negativos: 0, zerados: 0, baixos: 0 };
                const statusLowMin = Number(mapaState.low_min || 0);
                const statusLowMax = Number(mapaState.low_max || 0);
                (mapaState.linhas || []).forEach((item) => {
                    const stock = Number(item.dias?.[statusDay]?.estoque_final ?? item.estoque ?? 0);
                    if (stock < 0) liveStatus.negativos++;
                    else if (stock === 0) liveStatus.zerados++;
                    else if (stock >= statusLowMin && stock <= statusLowMax) liveStatus.baixos++;
                });
                mapaState.status = liveStatus;
                applyStatus();
            };

            const renderCompact = (state) => {
                const wrap = document.getElementById('compact-table');
                if (!wrap) return;
                const data = state || mapaState;
                const datas = Object.keys(data.datas || {}).sort().filter((dia) => `${dia}`.startsWith(`${compactMonth}-`));
                const linhas = data.linhas || [];
                if (!datas.length || !linhas.length) {
                    wrap.innerHTML = `<div class="compact-empty">Nenhum registro para ${compactMonth}. Ajuste o mês ou adicione lançamentos.</div>`;
                    return;
                }
                const header = ['<th class="compact-prod">Produto</th>'];
                datas.forEach((dia) => {
                    const info = data.datas[dia] || {};
                    const media = Number(info.media_conclusao || 0).toFixed(1).replace('.', ',');
                    const hojeCls = info.is_hoje ? ' hoje' : '';
                    header.push(`<th class="${hojeCls}"><div class="compact-th${hojeCls}"><div>${info.label || fmtDate(dia)}</div><small>${info.semana || ''}</small><span class="compact-media" data-compact-media="${dia}">Conclusão ${media}%</span><label class="feriado-toggle"><input type="checkbox" data-feriado="${dia}"> Sem produção</label></div></th>`);
                });

                const badge = (cls, label, valor, opts = {}) => {
                    const isPercent = opts.percent === true;
                    const display = isPercent ? `${Number(valor || 0).toFixed(1).replace('.', ',')}%` : fmtInt(valor);
                    return `<span class="compact-badge ${cls}"><span class="label">${label}</span><span class="value">${display}</span></span>`;
                };

                const rowMarkupCompact = (linha) => {
                    const cells = datas.map((dia) => {
                        const info = data.datas?.[dia] || {};
                        const celula = linha.dias?.[dia] || {};
                        const saida = Number(celula.saida || 0);
                        const entrada = Number(celula.entrada_estoque || 0);
                        const producao = Number(celula.entrada_planejada || 0);
                        const conclusao = Number(celula.producao_percentual || 0);
                        const estoque = Number(celula.estoque_final || 0);
                        const lowMin = Number(data.low_min || 0);
                        const lowMax = Number(data.low_max || 0);
                        let estoqueClass = 'ok';
                        if (estoque < 0) estoqueClass = 'negativo';
                        else if (estoque === 0) estoqueClass = 'zerado';
                        else if (estoque >= lowMin && estoque <= lowMax) estoqueClass = 'baixo';
                        const estoqueBadgeClass = `estoque ${estoqueClass !== 'ok' ? estoqueClass : ''}`.trim();
                        const hojeCls = info.is_hoje ? ' hoje' : '';
                        return `<td><div class="dia-card compact-day-card${hojeCls}" data-dia="${dia}" data-produto="${linha.id}">`
                            + `<div class="dia-head${hojeCls}"><div><span class="dia-date">${info.label || fmtDate(dia)}</span> <span class="dia-week">${info.semana || ''}</span></div>`
                            + `<div class="dia-progress"><span class="progress-label">Conclusão de produção</span><span class="progress-value">${Number(conclusao || 0).toFixed(1).replace('.', ',')}%</span></div></div>`
                            + `<div class="metrics-row">`
                            + `<div class="metric-chip saida"><span class="label">Saída</span><span class="value">${fmtInt(saida)}</span></div>`
                            + `<div class="metric-chip producao"><span class="label">Produção</span><span class="value">${fmtInt(producao)}</span></div>`
                            + `<div class="metric-chip entrada"><span class="label">Entrada</span><span class="value">${fmtInt(entrada)}</span></div>`
                            + `<div class="metric-chip ${estoqueBadgeClass}"><span class="label">Estoque</span><span class="value">${fmtInt(estoque)}</span></div>`
                            + `</div>`
                            + `<div class="forms-row">`
                            + `<form action="<?= htmlspecialchars($url('/mapa/entrada'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mini-form">`
                            + `<label for="compact-producao-${linha.id}-${dia}">Produção</label><div class="controls">`
                            + `<input type="hidden" name="tipo" value="planejado"><input type="hidden" name="produto_id" value="${linha.id}"><input type="hidden" name="data_referencia" value="${dia}"><input type="hidden" name="janela_inicio" value="${compactMonth}-01"><input type="hidden" name="month" value="${compactMonth}"><input type="hidden" name="full" value="1">`
                            + `<input id="compact-producao-${linha.id}-${dia}" type="number" step="0.01" min="0" name="quantidade" value="${producao ? producao.toFixed(2) : ''}" placeholder="0"><button type="submit" title="Registrar produção">+</button></div></form>`
                            + `<form action="<?= htmlspecialchars($url('/mapa/entrada'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mini-form">`
                            + `<label for="compact-entrada-${linha.id}-${dia}">Entrada</label><div class="controls">`
                            + `<input type="hidden" name="tipo" value="estoque"><input type="hidden" name="produto_id" value="${linha.id}"><input type="hidden" name="data_referencia" value="${dia}"><input type="hidden" name="janela_inicio" value="${compactMonth}-01"><input type="hidden" name="month" value="${compactMonth}"><input type="hidden" name="full" value="1">`
                            + `<input id="compact-entrada-${linha.id}-${dia}" type="number" step="0.01" min="0" name="quantidade" value="${entrada ? entrada.toFixed(2) : ''}" placeholder="0"><button type="submit" title="Adicionar estoque">+</button></div></form>`
                            + `</div></div></td>`;
                    }).join('');
                    const lastDay = datas[datas.length - 1];
                    const balance = Number(linha.dias?.[lastDay]?.estoque_final ?? linha.estoque ?? 0);
                    const lowMin = Number(data.low_min || 0);
                    const lowMax = Number(data.low_max || 0);
                    const balanceClass = balance < 0 ? 'negativo' : balance === 0 ? 'zerado' : (balance >= lowMin && balance <= lowMax ? 'baixo' : 'ok');
                    return `<tr><td class="compact-prod"><div class="produto-card compact-product-card" data-product-card="${linha.id}"><div class="sku">${linha.sku || '—'}</div><div class="nome">${linha.nome || ''}</div><div class="saldo ${balanceClass}"><span>Saldo:</span><strong>${fmtInt(balance)}</strong></div></div></td>${cells}</tr>`;
                };

                const token = ++compactRenderToken;
                compactRowsObserver?.disconnect();
                compactRowsObserver = null;
                const initialCount = Math.min(2, linhas.length);
                const hasRemainingRows = initialCount < linhas.length;
                const sentinelMarkup = hasRemainingRows
                    ? `<tr data-compact-row-sentinel><td colspan="${datas.length + 1}" style="height:1px;padding:0;border:0;"></td></tr>`
                    : '';
                wrap.innerHTML = `<div class="compact-grid"><table><thead><tr>${header.join('')}</tr></thead><tbody>${linhas.slice(0, initialCount).map(rowMarkupCompact).join('')}${sentinelMarkup}</tbody></table></div>`;
                fullRendered = true;
                if (yearLabel) yearLabel.textContent = compactYear;
                const hojeTh = wrap.querySelector('.compact-grid th.hoje');
                if (hojeTh) {
                    centerInScroll(wrap, hojeTh, 'auto');
                }
                bindFeriados();
                const tbody = wrap.querySelector('.compact-grid tbody');
                tbody?.querySelectorAll('form.mini-form').forEach(handleSubmit);
                let index = initialCount;
                const sentinel = tbody?.querySelector('[data-compact-row-sentinel]');
                const appendCompactRows = () => {
                    if (token !== compactRenderToken || !tbody?.isConnected || index >= linhas.length) return;
                    if (document.querySelector('.mapa')?.classList.contains('is-scrolling')) {
                        window.setTimeout(appendCompactRows, 120);
                        return;
                    }
                    const end = Math.min(index + 2, linhas.length);
                    const addedCount = end - index;
                    if (sentinel?.isConnected) {
                        sentinel.insertAdjacentHTML('beforebegin', linhas.slice(index, end).map(rowMarkupCompact).join(''));
                    }
                    const addedRows = [];
                    let previousRow = sentinel?.previousElementSibling;
                    for (let i = 0; i < addedCount && previousRow; i += 1) {
                        addedRows.unshift(previousRow);
                        previousRow = previousRow.previousElementSibling;
                    }
                    addedRows.forEach((row) => {
                        row.querySelectorAll('form.mini-form:not([data-mapa-submit-bound="1"])').forEach(handleSubmit);
                    });
                    index = end;
                    if (index >= linhas.length) {
                        compactRowsObserver?.disconnect();
                        compactRowsObserver = null;
                        sentinel?.remove();
                    } else {
                        window.requestAnimationFrame(() => {
                            if (sentinel?.isConnected && sentinel.getBoundingClientRect().top < window.innerHeight + 700) {
                                appendCompactRows();
                            }
                        });
                    }
                };
                if (sentinel && index < linhas.length) {
                    if ('IntersectionObserver' in window) {
                        compactRowsObserver = new IntersectionObserver((entries) => {
                            if (entries.some((entry) => entry.isIntersecting)) appendCompactRows();
                        }, { root: null, rootMargin: '700px 0px', threshold: 0 });
                        compactRowsObserver.observe(sentinel);
                    } else {
                        const appendFallbackRows = () => {
                            appendCompactRows();
                            if (token === compactRenderToken && index < linhas.length) {
                                window.setTimeout(appendFallbackRows, 64);
                            }
                        };
                        window.setTimeout(appendFallbackRows, 0);
                    }
                }
            };

            const applyStatus = () => {
                const status = mapaState.status || {};
                const totalProdutos = (mapaState.linhas || []).length;
                const neg = Number(status.negativos || 0);
                const zero = Number(status.zerados || 0);
                const baixo = Number(status.baixos || 0);
                const ok = Math.max(totalProdutos - (neg + zero + baixo), 0);
                document.querySelectorAll('.status-value').forEach((el) => {
                    const key = el.dataset.status;
                    if (!key) return;
                    el.textContent = fmtInt(key === 'ok' ? ok : (status[key] || 0));
                });
                const segs = {
                    negativos: neg,
                    zerados: zero,
                    baixos: baixo,
                    ok,
                };
                const total = Object.values(segs).reduce((sum, v) => sum + v, 0) || 1;
                document.querySelectorAll('[data-status-seg]').forEach((el) => {
                    const key = el.dataset.statusSeg;
                    if (!key) return;
                    const pct = Math.max(0, Math.min(100, (segs[key] || 0) / total * 100));
                    el.style.width = `${pct}%`;
                });
                document.querySelectorAll('[data-status-pct]').forEach((el) => {
                    const key = el.dataset.statusPct;
                    if (!key) return;
                    const pct = Math.max(0, Math.min(100, (segs[key] || 0) / total * 100));
                    el.textContent = `${decimalFormatter.format(pct)}%`;
                });
            };

            const applyNavMedias = () => {
                const datas = mapaState.datas || {};
                Object.entries(datas).forEach(([dia, info]) => {
                    const target = document.querySelector(`.nav-avg[data-nav-media="${dia}"]`);
                    const headers = document.querySelectorAll(`.dia-header-media[data-dia-media="${dia}"], .compact-media[data-compact-media="${dia}"]`);
                    const media = Number(info.media_conclusao || 0);
                    if (target) {
                        target.dataset.mediaBase = media.toFixed(1);
                        if (!feriados[dia]) {
                            target.textContent = `Média ${media.toFixed(1).replace('.', ',')}%`;
                        }
                    }
                    headers.forEach((header) => {
                        header.dataset.mediaBase = media.toFixed(1);
                        if (!feriados[dia]) {
                            header.textContent = `Conclusão ${media.toFixed(1).replace('.', ',')}%`;
                        }
                    });
                });
                Object.keys(feriados).forEach(renderFeriado);
            };

            const renderFeriado = (dia) => {
                const isFeriado = feriadoAtivo(dia);
                const checks = document.querySelectorAll(`input[data-feriado="${dia}"]`);
                const reasons = document.querySelectorAll(`select[data-feriado-reason="${dia}"]`);
                const nav = document.querySelector(`.nav-chip[data-dia-nav="${dia}"]`);
                const navAvg = document.querySelector(`.nav-avg[data-nav-media="${dia}"]`);
                const headers = document.querySelectorAll(`.dia-header-media[data-dia-media="${dia}"], .compact-media[data-compact-media="${dia}"]`);
                const progressItems = document.querySelectorAll(`.dia-card[data-dia="${dia}"] .progress-value`);
                const baseMedia = Number(navAvg?.dataset.mediaBase || headers[0]?.dataset.mediaBase || 0);
                checks.forEach((check) => { check.checked = isFeriado; });
                reasons.forEach((select) => { select.hidden = !isFeriado; select.value = feriadoMotivo(dia); });
                if (nav) nav.classList.toggle('feriado', isFeriado);
                if (navAvg) navAvg.textContent = isFeriado ? 'Sem produção' : `Média ${baseMedia.toFixed(1).replace('.', ',')}%`;
                headers.forEach((header) => { header.textContent = isFeriado ? 'Sem produção' : `Conclusão ${baseMedia.toFixed(1).replace('.', ',')}%`; });
                progressItems.forEach((progress) => {
                    const original = progress.dataset.progressBase || progress.textContent;
                    progress.dataset.progressBase = original;
                    progress.textContent = isFeriado ? '—' : original;
                });
                const productiveDates = visibleDates().filter((date) => !feriadoAtivo(date));
                const productiveValues = productiveDates.map((date) => Number(mapaState.datas?.[date]?.media_conclusao ?? 0)).filter((value) => !Number.isNaN(value));
                const productiveAverage = productiveValues.length ? productiveValues.reduce((sum, value) => sum + value, 0) / productiveValues.length : 0;
                document.querySelectorAll('.weekly-average').forEach((target) => {
                    target.textContent = `${productiveAverage.toFixed(1).replace('.', ',')}%`;
                });
            };

            const bindFeriados = () => {
                document.querySelectorAll('input[data-feriado]').forEach((input) => {
                    const dia = input.dataset.feriado;
                    const label = input.closest('.feriado-toggle');
                    let reasonSelect = label?.querySelector(`select[data-feriado-reason="${dia}"]`);
                    if (label && !reasonSelect) {
                        reasonSelect = document.createElement('select');
                        reasonSelect.className = 'feriado-reason';
                        reasonSelect.dataset.feriadoReason = dia;
                        reasonSelect.setAttribute('aria-label', 'Motivo sem produção');
                        reasonSelect.innerHTML = feriadoReasons.map(([value, text]) => `<option value="${value}">${text}</option>`).join('');
                        label.appendChild(reasonSelect);
                    }
                    input.checked = feriadoAtivo(dia);
                    if (reasonSelect) {
                        reasonSelect.value = feriadoMotivo(dia);
                        reasonSelect.hidden = !input.checked;
                        if (reasonSelect.dataset.feriadoBound !== '1') {
                            reasonSelect.dataset.feriadoBound = '1';
                            reasonSelect.addEventListener('change', () => {
                                feriados[dia] = { active: true, reason: reasonSelect.value };
                                localStorage.setItem('mapaFeriados', JSON.stringify(feriados));
                                renderFeriado(dia);
                                persistirSemProducao(dia).catch((error) => showToast(error.message, 'error'));
                            });
                        }
                    }
                    if (input.dataset.feriadoBound === '1') return;
                    input.dataset.feriadoBound = '1';
                    input.addEventListener('change', () => {
                        if (input.checked) {
                            feriados[dia] = { active: true, reason: reasonSelect?.value || feriadoMotivo(dia) };
                        } else {
                            delete feriados[dia];
                        }
                        localStorage.setItem('mapaFeriados', JSON.stringify(feriados));
                        renderFeriado(dia);
                        persistirSemProducao(dia).catch((error) => showToast(error.message, 'error'));
                    });
                });
                Object.keys(feriados).forEach(renderFeriado);
            };

            const captureBulkFormState = () => {
                const form = document.querySelector('.mapa .form-card');
                if (!form) return null;
                return {
                    produto: form.querySelector('#produto_id')?.value || '',
                    data: form.querySelector('#data_referencia')?.value || '',
                    quantidade: form.querySelector('#quantidade')?.value || '',
                    observacao: form.querySelector('#observacao')?.value || '',
                    tipo: form.querySelector('input[name="tipo"]:checked')?.value || 'planejado',
                };
            };

            const clearBulkForm = () => {
                const form = document.querySelector('.mapa .form-card');
                if (!form) return;
                const produto = form.querySelector('#produto_id');
                const search = form.querySelector('#produto_search');
                const data = form.querySelector('#data_referencia');
                const quantidade = form.querySelector('#quantidade');
                const obs = form.querySelector('#observacao');
                const tipos = form.querySelectorAll('input[name="tipo"]');
                if (produto) {
                    produto.value = '';
                    produto.dispatchEvent(new Event('change'));
                }
                if (search) { search.value = ''; search.setCustomValidity(''); search.dispatchEvent(new Event('change', { bubbles: true })); }
                if (data) data.value = new Date().toISOString().slice(0, 10);
                if (quantidade) quantidade.value = '';
                if (obs) obs.value = '';
                if (tipos?.length) {
                    tipos.forEach((t) => { t.checked = t.value === 'planejado'; });
                }
                form.querySelector('input[name="consumir_receita"]')?.removeAttribute('checked');
                const consume = form.querySelector('input[name="consumir_receita"]');
                if (consume) consume.checked = false;
            };

            const bindProductSearch = () => {
                const form = document.querySelector('.mapa .form-card');
                const select = form?.querySelector('#produto_id');
                const search = form?.querySelector('#produto_search');
                const suggestions = form?.querySelector('#produto_suggestions');
                if (!select || !search || !suggestions || search.dataset.bound === '1') return;
                search.dataset.bound = '1';
                const products = Array.from(select.options).filter((option) => option.value).map((option) => {
                    const parts = option.textContent.trim().split(/\s+[—-]\s+/);
                    return { id: option.value, label: option.textContent.trim(), sku: parts[0] || '', name: parts.slice(1).join(' — ') || option.textContent.trim() };
                });
                const escapeProductText = (value) => String(value || '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
                const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                const close = () => { suggestions.hidden = true; };
                const choose = (product) => {
                    select.value = product.id;
                    search.value = product.label;
                    search.setCustomValidity('');
                    close();
                };
                const render = () => {
                    const term = normalize(search.value.trim());
                    const matches = products.filter((product) => !term || normalize(`${product.sku} ${product.name}`).includes(term)).slice(0, 12);
                    suggestions.innerHTML = matches.length
                        ? matches.map((product) => `<li><button type="button" role="option" data-product-id="${escapeProductText(product.id)}">${escapeProductText(product.name)}<span class="produto-suggestion-sku">${escapeProductText(product.sku)}</span></button></li>`).join('')
                        : '<li class="produto-suggestions-empty">Nenhum produto encontrado.</li>';
                    suggestions.hidden = false;
                    suggestions.querySelectorAll('[data-product-id]').forEach((button) => {
                        button.addEventListener('click', () => choose(products.find((product) => product.id === button.dataset.productId)));
                    });
                };
                search.addEventListener('focus', render);
                search.addEventListener('click', render);
                search.addEventListener('input', () => { select.value = ''; search.setCustomValidity(''); render(); });
                search.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') close();
                    if (event.key === 'Enter' && !suggestions.hidden) {
                        const first = suggestions.querySelector('[data-product-id]');
                        if (first) { event.preventDefault(); first.click(); }
                    }
                });
                select.addEventListener('change', () => {
                    const selected = products.find((product) => product.id === select.value);
                    search.value = selected ? selected.label : '';
                });
                form.addEventListener('submit', (event) => {
                    if (select.value) return;
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    search.setCustomValidity('Selecione um produto da lista.');
                    search.reportValidity();
                }, true);
                document.addEventListener('click', (event) => {
                    if (!event.target.closest('.map-product-search')) close();
                });
                select.dispatchEvent(new Event('change'));
            };

            const captureScrollState = () => {
                return {
                    table: document.querySelector('.table-wrap')?.scrollLeft || 0,
                    nav: document.querySelector('.nav-row')?.scrollLeft || 0,
                    activeDia: document.querySelector('.nav-chip.ativo')?.dataset?.diaNav || null,
                };
            };

            const restoreScrollState = (state) => {
                if (!state) return;
                const table = document.querySelector('.table-wrap');
                const nav = document.querySelector('.nav-row');
                if (table) table.scrollLeft = state.table || 0;
                if (nav) nav.scrollLeft = state.nav || 0;
                if (state.activeDia) {
                    const chip = document.querySelector(`.nav-chip[data-dia-nav="${state.activeDia}"]`);
                    if (chip) chip.classList.add('ativo');
                }
            };

            const handleSubmit = (form) => {
                if (form.dataset.mapaSubmitBound === '1') return;
                form.dataset.mapaSubmitBound = '1';
                form.addEventListener('submit', (ev) => {
                    ev.preventDefault();
                    const paneSnapshot = activePane;
                    const windowSnapshot = mapaState.janela_inicio || mapaState.base || mapaState.hoje || new Date().toISOString().slice(0, 10);
                    const data = new FormData(form);
                    data.set('full', paneSnapshot === 'completo' ? '1' : '0');
                    data.set('live_quick', '1');
                    const submittedProduct = String(data.get('produto_id') || '');
                    const submittedDay = String(data.get('data_referencia') || '');
                    const submittedMonth = submittedDay.slice(0, 7);
                    const submittedType = String(data.get('tipo') || 'planejado');
                    const submittedQuantity = Number(data.get('quantidade') || 0);
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const submitLabel = submitBtn?.textContent || '+';
                    const bulkSnapshot = form.classList.contains('form-card') ? captureBulkFormState() : null;
                    const scrollSnapshot = captureScrollState();
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.setAttribute('aria-busy', 'true');
                    }
                    applyOptimisticCellUpdate(submittedProduct, submittedDay, submittedType, submittedQuantity);
                    if (submittedMonth) compactMonthCache.delete(submittedMonth);
                    fetch(form.action, {
                        method: 'POST',
                        body: data,
                        cache: 'no-store',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    }).then(async (resp) => {
                        const payload = await resp.json().catch(() => ({}));
                        if (!resp.ok) {
                            throw new Error(payload.message || 'Erro ao salvar');
                        }
                        activePane = paneSnapshot;
                        const syncInBackground = () => fetchMapa({ start: windowSnapshot, full: paneSnapshot === 'completo', render: false }).then(() => {
                                forceLiveCellUpdate(submittedProduct, submittedDay);
                                tabButtons.forEach((btn) => btn.classList.toggle('active', btn.dataset.mapTab === paneSnapshot));
                                panes.forEach((paneEl) => { paneEl.hidden = paneEl.dataset.mapPane !== paneSnapshot; });
                                syncLiveUrl(paneSnapshot);
                                if (paneSnapshot === 'completo') restoreScrollState(scrollSnapshot);
                                if (submittedMonth && paneSnapshot === 'resumo') {
                                    prefetchCompactMonth(submittedMonth, true).catch(() => {});
                                }
                            }).catch((err) => showToast(err.message || 'Erro ao sincronizar o mapa', 'error'));
                        window.setTimeout(syncInBackground, 250);
                        if (bulkSnapshot) clearBulkForm();
                        showToast(payload.message || 'Salvo');
                    }).catch((err) => {
                        showToast(err.message || 'Erro ao registrar', 'error');
                    }).finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.removeAttribute('aria-busy');
                            submitBtn.textContent = submitLabel;
                        }
                    });
                });
            };

            const bindForms = () => {
                document.querySelectorAll('.mini-form').forEach(handleSubmit);
                const bulkForm = document.querySelector('.mapa .form-card');
                if (bulkForm) handleSubmit(bulkForm);
                bindProductSearch();
            };

            document.addEventListener('pointerdown', (event) => {
                const button = event.target.closest('.mapa form button[type="submit"]');
                const form = button?.closest('form');
                if (!form || activePane !== 'resumo') return;
                const data = new FormData(form);
                applyOptimisticCellUpdate(
                    String(data.get('produto_id') || ''),
                    String(data.get('data_referencia') || ''),
                    String(data.get('tipo') || 'planejado'),
                    Number(data.get('quantidade') || 0)
                );
            }, true);

            const scrollHintShown = { resumo: false, completo: false };
            let scrollHintTimer = 0;
            const closeScrollHint = () => {
                const hint = document.querySelector('.map-scroll-hint');
                if (!hint) return;
                window.clearTimeout(scrollHintTimer);
                hint.classList.add('is-closing');
                window.setTimeout(() => hint.remove(), 170);
            };
            const showScrollHint = (card, pane = 'resumo') => {
                if (scrollHintShown[pane] || !card) return;
                scrollHintShown[pane] = true;
                const hint = document.createElement('aside');
                hint.className = 'map-scroll-hint';
                hint.setAttribute('role', 'status');
                hint.innerHTML = `
                    <button class="map-scroll-hint__close" type="button" aria-label="Fechar dica"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
                    <div class="map-scroll-hint__title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01"/></svg>Como navegar pelo Mapa</div>
                    <div class="map-scroll-hint__tips">
                        <div class="map-scroll-hint__tip"><svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="3" width="14" height="25" rx="7"/><path d="M16 3v8m0-5h.01M5 16h4m14 0h4M3 16l3-3m-3 3 3 3m23-3-3-3m3 3-3 3"/></svg><div><strong>Mouse</strong><span><kbd>Shift</kbd> + roda: horizontal<br>Roda normal: vertical</span></div></div>
                        <div class="map-scroll-hint__tip"><svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="4" y="5" width="24" height="22" rx="4"/><path d="M11 18c1-5 2-8 4-8 1.5 0 1.5 2 1.5 4 1-2 2-3 3-2 1 .5.5 2 .5 3 1-1 2-1 2.5 0 1 2-1 7-4 8M8 23h16"/><path d="M2 16h4m20 0h4M2 16l2-2m-2 2 2 2m26-2-2-2m2 2-2 2"/></svg><div><strong>Notebook</strong><span>Dois dedos para os lados: horizontal<br>Para cima/baixo: vertical</span></div></div>
                    </div>`;
                document.body.appendChild(hint);
                const rect = card.getBoundingClientRect();
                const hintRect = hint.getBoundingClientRect();
                const left = Math.max(12, Math.min(window.innerWidth - hintRect.width - 12, rect.left + (rect.width - hintRect.width) / 2));
                const above = rect.top - hintRect.height - 10;
                hint.style.left = `${left}px`;
                hint.style.top = `${above >= 12 ? above : Math.min(window.innerHeight - hintRect.height - 12, rect.bottom + 10)}px`;
                hint.querySelector('.map-scroll-hint__close')?.addEventListener('click', closeScrollHint);
                scrollHintTimer = window.setTimeout(closeScrollHint, 6000);
            };
            document.querySelector('.mapa .table-wrap')?.addEventListener('pointerover', (event) => {
                if (event.pointerType && event.pointerType !== 'mouse') return;
                const card = event.target.closest('.dia-card');
                if (card) showScrollHint(card, 'resumo');
            }, { passive: true });
            document.getElementById('compact-table')?.addEventListener('pointerover', (event) => {
                if (event.pointerType && event.pointerType !== 'mouse') return;
                const card = event.target.closest('.dia-card');
                if (card) showScrollHint(card, 'completo');
            }, { passive: true });

            const centerInScroll = (wrap, target, behavior = 'smooth') => {
                if (!wrap || !target) return;
                const targetLeft = target.offsetLeft;
                const targetWidth = target.offsetWidth;
                const centerOffset = (wrap.clientWidth - targetWidth) / 2;
                const left = Math.max(0, targetLeft - centerOffset);
                wrap.scrollTo({left, behavior});
            };

            const alignStartInScroll = (wrap, target, behavior = 'smooth') => {
                if (!wrap || !target) return;
                const stickyProduct = wrap.classList.contains('table-wrap') ? wrap.querySelector('.col-produto') : null;
                const stickyInset = stickyProduct ? stickyProduct.getBoundingClientRect().width + 14 : 0;
                wrap.scrollTo({left: Math.max(0, target.offsetLeft - stickyInset), behavior});
            };

            const focusToday = () => {
                const datas = visibleDates();
                const todayKey = mapaState.hoje && datas.includes(mapaState.hoje) ? mapaState.hoje : (mapaState.base || mapaState.hoje || datas[0]);
                if (!todayKey) return;
                const firstKey = datas[0];
                const navChip = document.querySelector(`.nav-chip[data-dia-nav="${todayKey}"]`);
                const firstNav = firstKey ? document.querySelector(`.nav-chip[data-dia-nav="${firstKey}"]`) : null;
                const targetCard = document.querySelector(`.dia-card[data-dia="${todayKey}"]`);
                const firstCard = firstKey ? document.querySelector(`.dia-card[data-dia="${firstKey}"]`) : null;
                const targetCell = targetCard?.closest('td');
                const firstCell = firstCard?.closest('td');
                const align = () => {
                    if (navChip) {
                        navChip.classList.add('ativo');
                    }
                    if (navChip) {
                        const navRow = navChip.closest('.nav-dias');
                        if (navRow) {
                            alignStartInScroll(navRow, navChip, 'auto');
                        } else {
                            navChip.scrollIntoView({behavior: 'auto', inline: 'start', block: 'nearest'});
                        }
                    } else if (firstNav) {
                        firstNav.scrollIntoView({behavior: 'auto', inline: 'start', block: 'nearest'});
                    }
                    if (targetCell) {
                        const wrap = targetCell.closest('.table-wrap');
                        if (wrap) {
                            alignStartInScroll(wrap, targetCell, 'auto');
                        } else {
                            targetCell.scrollIntoView({behavior: 'auto', inline: 'start', block: 'nearest'});
                        }
                        targetCard.classList.add('hoje');
                    } else if (firstCell) {
                        const wrap = firstCell.closest('.table-wrap');
                        if (wrap) alignStartInScroll(wrap, firstCell, 'auto');
                    }
                };
                align();
            };

            const renderAll = () => {
                if (activePane === 'resumo') {
                    renderRange();
                    if (initialDailyMarkup) {
                        initialDailyMarkup = false;
                    } else {
                        renderTabela();
                    }
                    applyNavMedias();
                    bindFeriados();
                    bindForms();
                    focusToday();
                } else {
                    renderCompact();
                }
                applyStatus();
                applyFullMode();
            };

            const setLiveBusy = (busy) => {
                const root = document.querySelector('.mapa');
                if (root) root.setAttribute('aria-busy', busy ? 'true' : 'false');
                tabButtons.forEach((button) => button.classList.toggle('is-loading', busy));
            };

            const syncLiveUrl = (pane) => {
                const url = new URL(window.location.href);
                url.searchParams.delete('full');
                url.searchParams.delete('start');
                url.searchParams.delete('ref');
                url.hash = pane === 'completo' ? 'completo' : '';
                window.history.replaceState({ mapaPane: pane }, '', `${url.pathname}${url.search}${url.hash}`);
            };

            const switchPane = async (pane) => {
                activePane = pane;
                tabButtons.forEach((btn) => btn.classList.toggle('active', btn.dataset.mapTab === pane));
                panes.forEach((paneEl) => {
                    const match = paneEl.dataset.mapPane === pane;
                    paneEl.hidden = !match;
                });
                syncLiveUrl(pane);
                setLiveBusy(true);
                try {
                    if (pane === 'completo' && !fullMode) {
                        mapaState = await prefetchCompactMonth(compactMonth);
                        fullMode = true;
                        renderAll();
                        applyFullMode();
                    } else if (pane === 'completo') {
                        if (!fullRendered) renderCompact();
                    } else if (fullMode) {
                        const start = mapaState.hoje || new Date().toISOString().slice(0, 10);
                        await fetchMapa({ start, full: false });
                    }
                } catch (err) {
                    showToast(err.message || 'Erro ao atualizar o mapa', 'error');
                } finally {
                    setLiveBusy(false);
                }
            };

            const prefetchCompactMonth = async (month = compactMonth, force = false) => {
                const cached = !force ? cachedCompactMonth(month) : null;
                if (cached) return cached;
                const qs = new URLSearchParams({ full: '1', month, start: `${month}-01`, _live: `${Date.now()}-${Math.random().toString(16).slice(2)}` });
                const response = await fetch(`${mapaUrl}?${qs}`, {
                    method: 'GET',
                    cache: 'no-store',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Cache-Control': 'no-cache' },
                });
                const payload = await response.json();
                if (!response.ok) throw new Error(payload.message || 'Erro ao carregar mapa completo');
                Object.entries(payload.datas || {}).forEach(([dia, info]) => {
                    if (info?.sem_producao) feriados[dia] = { active: true, reason: info.sem_producao_motivo || 'outro' };
                    else delete feriados[dia];
                });
                localStorage.setItem('mapaFeriados', JSON.stringify(feriados));
                return rememberCompactMonth(month, payload);
            };

            const loadWindow = async (direction) => {
                const datas = visibleDates();
                if (!datas.length) return;
                const delta = direction === 'prev' ? -windowSize : 1;
                const targetStart = direction === 'prev' ? addDays(datas[0], delta) : addDays(datas[datas.length - 1], delta);
                try {
                    await fetchMapa({ start: targetStart });
                } catch (err) {
                    showToast(err.message || 'Erro ao navegar', 'error');
                }
            };

            document.querySelectorAll('[data-nav-direction]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const dir = btn.dataset.navDirection;
                    if (!dir) return;
                    loadWindow(dir);
                });
            });

            const changeYear = async (delta) => {
                const baseYear = Number(compactYear || (mapaState.base || mapaState.hoje || '').slice(0, 4) || new Date().getFullYear());
                compactYear = String(baseYear + delta);
                compactMonth = `${compactYear}-${compactMonth.slice(5, 7)}`;
                updateYearLabel();
                try {
                    mapaState = await prefetchCompactMonth(compactMonth);
                    fullMode = true;
                    renderAll();
                    applyFullMode();
                } catch (err) {
                    showToast(err.message || 'Erro ao carregar ano', 'error');
                }
            };

            const changeMonth = async (delta) => {
                const [year, month] = compactMonth.split('-').map(Number);
                const target = new Date(year, month - 1 + delta, 1);
                const targetYear = String(target.getFullYear());
                compactMonth = `${targetYear}-${String(target.getMonth() + 1).padStart(2, '0')}`;
                compactYear = targetYear;
                updateYearLabel();
                try {
                    mapaState = await prefetchCompactMonth(compactMonth);
                    fullMode = true;
                    renderAll();
                    applyFullMode();
                } catch (err) {
                    showToast(err.message || 'Erro ao carregar mês', 'error');
                }
            };

            if (refreshFullBtn) {
                refreshFullBtn.addEventListener('click', async () => {
                    const wasFull = fullMode;
                    await switchPane('completo');
                    if (wasFull) {
                        setLiveBusy(true);
                        try {
                            await fetchMapa({ start: `${compactYear}-01-01`, full: true });
                        } catch (err) {
                            showToast(err.message || 'Erro ao atualizar mapa completo', 'error');
                        } finally {
                            setLiveBusy(false);
                        }
                    }
                });
            }

            document.getElementById('year-prev')?.addEventListener('click', () => changeYear(-1));
            document.getElementById('year-next')?.addEventListener('click', () => changeYear(1));
            document.getElementById('month-prev')?.addEventListener('click', () => changeMonth(-1));
            document.getElementById('month-next')?.addEventListener('click', () => changeMonth(1));

            tabButtons.forEach((btn) => {
                btn.addEventListener('click', (ev) => {
                    const target = btn.dataset.mapTab;
                    ev.preventDefault();
                    if (target) switchPane(target);
                });
            });

            // A busca precisa funcionar mesmo que outra rotina visual do mapa falhe.
            bindProductSearch();
            // Os lançamentos também precisam estar ativos antes da renderização pesada.
            bindForms();
            updateYearLabel();
            installScrollOptimizer();
            renderAll();
            loadRemainingDailyRows();
            switchPane(activePane);
            if (fullMode) {
                rememberCompactMonth(compactMonth, mapaState);
            } else {
                window.setTimeout(() => prefetchCompactMonth(compactMonth).catch(() => {}), 0);
            }
        })();
    </script>
</section>
