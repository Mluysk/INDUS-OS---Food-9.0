<?php
$datas = $mapa['datas'] ?? [];
$linhas = $mapa['linhas'] ?? [];
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
        .mapa .tab-bar { display: inline-flex; gap: 8px; background: var(--surface); border: 1px solid var(--border); padding: 6px; border-radius: 12px; box-shadow: var(--shadow-soft); }
        .mapa .tab-btn { border: 1px solid var(--primary) !important; background: var(--primary) !important; color: #fff !important; padding: 10px 14px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: all 0.15s ease; box-shadow: none; }
        .mapa .tab-btn.active { background: var(--primary) !important; color: #fff !important; border-color: var(--primary) !important; box-shadow: none; }
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
        .mapa .swatch { width: 10px; height: 10px; border-radius: 50%; display: inline-block; box-shadow: 0 0 0 1px rgba(0,0,0,0.08); flex-shrink: 0; }
        .mapa .swatch.saida { background: #fda4af; box-shadow: 0 0 0 1px rgba(225, 29, 72, 0.24); }
        .mapa .swatch.entrada { background: #bae6fd; box-shadow: 0 0 0 1px rgba(14, 165, 233, 0.24); }
        .mapa .swatch.producao { background: #e9d5ff; box-shadow: 0 0 0 1px rgba(168, 85, 247, 0.24); }
        .mapa .swatch.saldo { width: 10px; height: 10px; border-radius: 50% !important; background: #bbf7d0; box-shadow: 0 0 0 1px rgba(22, 163, 74, 0.18); }
        .mapa .pill.saldo .swatch { box-shadow: 0 0 0 1px rgba(22, 163, 74, 0.18); border-radius: 50%; }
        .mapa .pill.saldo { background: var(--surface-alt); border: 1px solid var(--border); color: var(--text); box-shadow: var(--shadow-soft); }
        .mapa .grid-card { background: var(--surface); border: 1px solid var(--border); border-radius: 18px; box-shadow: var(--shadow-soft); overflow: hidden; }
        .mapa table { width: 100%; border-collapse: separate; border-spacing: 14px 14px; min-width: 760px; }
        .mapa th, .mapa td { padding: 0; text-align: center; background: transparent; border: none; box-shadow: none; }
        .mapa thead th { background: transparent; font-size: 0.86rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
        .mapa .col-produto { text-align: left; width: 230px; position: sticky; left: 0; background: linear-gradient(90deg, var(--surface) 90%, rgba(255,255,255,0)); z-index: 2; padding-right: 8px; }
        .mapa .dia-header { display: flex; align-items: center; justify-content: center; gap: 10px; }
        .mapa .dia-header .dia-header-date { display: grid; gap: 2px; }
        .mapa .dia-header .dia-header-date .dia-header-label { font-weight: 800; color: var(--text); letter-spacing: -0.01em; }
        .mapa .dia-header .dia-header-date .dia-header-week { color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; }
        .mapa .dia-header .dia-header-meta { display: inline-flex; align-items: center; gap: 8px; }
        .mapa .dia-header .dia-header-media { padding: 4px 10px; border-radius: 12px; background: linear-gradient(135deg, rgba(11,116,255,0.08), rgba(11,116,255,0.16)); color: #0b74ff; font-weight: 800; font-size: 0.9rem; border: 1px solid rgba(11,116,255,0.22); box-shadow: 0 6px 14px rgba(11,116,255,0.12); }
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
        .mapa .dia-header .feriado-toggle { padding: 4px 8px; font-size: 0.78rem; }
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
        .mapa .compact-year-nav { display: inline-flex; align-items: center; gap: 10px; }
        .mapa .compact-year-pill { display: inline-flex; align-items: center; gap: 10px; padding: 8px 12px; background: var(--surface-alt); border: 1px solid var(--border); border-radius: 12px; box-shadow: var(--shadow-soft); font-weight: 800; color: var(--text); }
        .mapa .compact-year-pill .year-label { min-width: 64px; text-align: center; letter-spacing: 0.02em; }
        .mapa .compact-actions { display: flex; gap: 8px; align-items: center; }
        .mapa .compact-table { margin-top: 12px; overflow-x: auto; }
        .mapa .compact-grid table { width: 100%; border-collapse: collapse; min-width: 900px; }
        .mapa .compact-grid th, .mapa .compact-grid td { border: 1px solid var(--border); padding: 8px 10px; background: var(--surface); }
        .mapa .compact-grid th { position: sticky; top: 0; background: linear-gradient(180deg, var(--surface), var(--surface-alt)); z-index: 2; }
        .mapa .compact-grid th.hoje { background: linear-gradient(180deg, rgba(11,116,255,0.08), rgba(11,116,255,0.02)); border-color: rgba(11,116,255,0.3); box-shadow: 0 6px 14px rgba(11,116,255,0.12); }
        .mapa .compact-prod { width: 220px; position: sticky; left: 0; background: linear-gradient(90deg, var(--surface), var(--surface-alt)); z-index: 3; }
        .mapa .compact-prod-info { display: grid; gap: 2px; }
        .mapa .compact-th { display: grid; gap: 2px; text-align: center; font-weight: 700; color: var(--text); }
        .mapa .compact-th small { color: var(--text-muted); font-size: 0.78rem; }
        .mapa .compact-th .compact-media { color: #0b74ff; font-weight: 800; font-size: 0.78rem; }
        .mapa .compact-cell { display: grid; gap: 6px; }
        .mapa .compact-cell.hoje { background: linear-gradient(180deg, rgba(11,116,255,0.05), rgba(11,116,255,0.02)); border-radius: 10px; padding: 4px; }
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
        .mapa .table-wrap { overflow-x: auto; }
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
        .mapa .hint { color: var(--text-muted); font-size: 0.9rem; margin: 0; }
        .mapa .form-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 14px; box-shadow: var(--shadow-card); display: grid; grid-template-columns: minmax(200px, 1.3fr) minmax(120px, 0.75fr) minmax(120px, 0.75fr) minmax(180px, 1fr) minmax(190px, 1fr) auto; gap: 6px; align-items: end; }
        .mapa .form-card .form-row-full { grid-column: 1 / -1; }
        .mapa .form-card .form-row-actions { grid-column: 1 / -1; }
        .mapa label { display: block; font-weight: 800; color: var(--text); margin-bottom: 6px; font-size: 1rem; }
        .mapa input[type="text"], .mapa input[type="number"], .mapa input[type="date"], .mapa select, .mapa textarea { width: 100%; border: 1px solid var(--border); background: var(--surface-alt); color: var(--text); border-radius: 10px; padding: 9px 10px; box-sizing: border-box; }
        .mapa .date-field input[type="date"] { width: calc(64% + 42px); }
        .mapa .date-field input[type="date"]::-webkit-calendar-picker-indicator { transform: scale(1.26); transform-origin: center; }
        .mapa .quantity-field { transform: translateX(-6px); }
        .mapa .quantity-field input[type="number"] { width: calc(67% + 12px); transform: translateX(0); }
        .mapa .registro-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; }
        .mapa .registro-meta { display: flex; flex-direction: column; }
        .mapa .actions { display: flex; justify-content: flex-end; align-items: center; }
        .mapa .hint-inline { margin-top: 4px; }
        .mapa .hint { display: inline-flex; align-items: center; gap: 14px; flex-wrap: nowrap; }
        .mapa .hint p { margin: 0; }
        .mapa .hint-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0; box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.02); }
        .mapa .hint-dot--producao { background: #d8b4fe; box-shadow: 0 0 0 4px rgba(216, 180, 254, 0.35); }
        .mapa .hint-dot--entrada { background: #93c5fd; box-shadow: 0 0 0 4px rgba(147, 197, 253, 0.35); }
        .mapa .hint-item { display: inline-flex; align-items: center; gap: 8px; }
        .mapa .hint-item--entrada { margin-left: 39px; }
        .mapa .hint-text { display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
        .mapa .hint-text .hint-title { font-weight: 600; }
        .mapa .weekly-average,
        .mapa .weekly-average-label { font-weight: 700; color: var(--primary); }
        .mapa .weekly-average-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; background: var(--primary); border: 1px solid transparent; color: #fff; font-weight: 800; box-shadow: none; }
        .mapa .weekly-average-btn .weekly-average-label,
        .mapa .weekly-average-btn .weekly-average { color: #fff; }
        .mapa .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: none; border-radius: 12px; padding: 10px 14px; font-weight: 700; cursor: pointer; text-decoration: none; }
        .mapa .btn.primary { background: var(--primary); color: #fff; box-shadow: var(--shadow-soft); }
        .mapa .btn.secondary { background: var(--surface-alt); color: var(--text); border: 1px solid var(--border); }
        .mapa .choice { display: flex; gap: 8px; align-items: center; }
        .mapa .choice label { margin: 0; font-size: 0.86rem; }
        .mapa .choice .choice-entrada { margin-left: 358px; }
        .mapa .choice input[type="radio"] { margin-right: 6px; accent-color: var(--primary); }
        .mapa .consume-receita { margin-left: 12px; font-size: 0.85rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px; }
        .mapa .consume-receita input { accent-color: var(--primary); }
        .mapa .nav-wrap { display: grid; grid-template-columns: 1fr; gap: 12px; align-items: stretch; }
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
                <span class="pill saldo"><span class="swatch saldo"></span> Estoque</span>
            </div>
        </div>
    </div>

    <div class="tab-bar">
        <a class="tab-btn<?= $isFull ? '' : ' active'; ?>" data-map-tab="resumo" href="<?= htmlspecialchars($url('/mapa'), ENT_QUOTES, 'UTF-8'); ?>">Mapa diário</a>
        <a class="tab-btn<?= $isFull ? ' active' : ''; ?>" data-map-tab="completo" href="<?= htmlspecialchars($url('/mapa?full=1#completo'), ENT_QUOTES, 'UTF-8'); ?>">Mapa completo</a>
    </div>

    <div class="map-pane" data-map-pane="resumo"<?= $isFull ? ' hidden' : ''; ?>>
        <div class="grid-card" style="padding: 14px 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 10px; align-items: center;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:46px; height:46px; border-radius:14px; background: linear-gradient(135deg, rgba(225,29,72,0.16), rgba(225,29,72,0.08)); display:grid; place-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="0.8"/></svg>
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

    <form class="form-card" action="/mapa/entrada" method="post">
        <input type="hidden" name="full" value="<?= $isFull ? '1' : '0'; ?>">
        <div>
            <label for="produto_id">Produto acabado</label>
            <select id="produto_id" name="produto_id" required>
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
        <div>
            <label for="observacao">Observação</label>
            <input type="text" id="observacao" name="observacao" placeholder="Lote, turno, responsável...">
        </div>
        <div class="registro-row form-row-full">
            <div class="registro-meta">
                <label>Tipo de registro</label>
                <div class="choice">
                    <label><input type="radio" name="tipo" value="planejado" checked> Produção</label>
                    <label class="choice-entrada"><input type="radio" name="tipo" value="estoque"> Entrada</label>
                    <label class="consume-receita"><input type="checkbox" name="consumir_receita" value="1"> Consumir receita (baixar MP)</label>
                </div>
                <div class="hint hint-inline">
                    <span class="hint-item">
                        <span class="hint-dot hint-dot--producao" aria-hidden="true"></span>
                        <span class="hint-text">
                            <span class="hint-title">Produção é apenas planejamento</span>
                            <span class="hint-desc">e não muda o estoque.</span>
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

    <div class="grid-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="col-produto">Produto</th>
                        <?php foreach ($visibleDatas as $dia => $info): $isHoje = !empty($info['is_hoje']); ?>
                            <th>
                                    <div class="dia-header<?= $isHoje ? ' hoje' : ''; ?>" data-dia-header="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>">
                                        <div class="dia-header-date">
                                            <span class="dia-header-label"><?= htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <small class="dia-header-week"><?= htmlspecialchars($info['semana'], ENT_QUOTES, 'UTF-8'); ?></small>
                                        </div>
                                        <div class="dia-header-meta">
                                            <span class="dia-header-media" data-dia-media="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>" data-media-base="<?= number_format((float)($info['media_conclusao'] ?? 0), 1, '.', ''); ?>">Média diária <?= number_format((float)($info['media_conclusao'] ?? 0), 1, ',', '.'); ?>%</span>
                                            <label class="feriado-toggle" title="Marcar este dia como feriado">
                                                <input type="checkbox" data-feriado="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8'); ?>"> Feriado
                                            </label>
                                        </div>
                                    </div>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$linhas): ?>
                        <tr>
                            <td class="col-produto" colspan="<?= 1 + count($visibleDatas); ?>" style="text-align:center; padding: 28px 12px;">
                                Nenhum produto acabado cadastrado ainda.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($linhas as $linha): ?>
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
                        <tr>
                            <td class="col-produto">
                                <div class="produto-card">
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
                                <td>
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
                                                    <input type="hidden" name="consumir_receita" value="0">
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
                                                    <input type="hidden" name="consumir_receita" value="0">
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
                    <h3><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="#0b74ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Mapa completo</h3>
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
                    <button class="nav-btn" type="button" id="refresh-full">Recarregar</button>
                </div>
            </div>
            <div class="compact-status">
                <div style="font-weight:700; color:var(--text); display:flex; align-items:center; gap:8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="0.8"/></svg>
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
                <div class="status-bar" aria-hidden="true">
                    <span class="seg-neg" data-status-seg="negativos" style="width:0%"></span>
                    <span class="seg-zero" data-status-seg="zerados" style="width:0%"></span>
                    <span class="seg-baixo" data-status-seg="baixos" style="width:0%"></span>
                    <span class="seg-ok" data-status-seg="ok" style="width:100%"></span>
                </div>
            </div>
            <?php
            $compactDatas = $datas;
            $compactLinhas = $linhas;
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
                            . '</div><small>' . htmlspecialchars($info['semana'] ?? '', ENT_QUOTES, 'UTF-8') . '</small><small class="compact-media">Conclusão '
                            . $mediaDia . '%</small></div></th>';
                    }
                    ?>
                    <div class="compact-grid">
                        <table>
                            <thead>
                                <tr><?= $compactHeader; ?></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($compactLinhas as $linha): ?>
                                    <?php
                                    $produtoSku = htmlspecialchars($linha['sku'] ?? '—', ENT_QUOTES, 'UTF-8');
                                    $produtoNome = htmlspecialchars($linha['nome'] ?? '', ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <tr>
                                        <td class="compact-prod"><div class="compact-prod-info"><div class="sku"><?= $produtoSku; ?></div><div class="nome"><?= $produtoNome; ?></div></div></td>
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
                                            <td>
                                                <div class="compact-cell<?= !empty($compactDatas[$dia]['is_hoje']) ? ' hoje' : ''; ?>">
                                                    <div class="badge-row">
                                                        <span class="compact-badge saida"><span class="label">Saída</span><span class="value"><?= $fmtInt($saida); ?></span></span>
                                                        <span class="compact-badge producao"><span class="label">Produção</span><span class="value"><?= $fmtInt($producao); ?></span></span>
                                                        <span class="compact-badge entrada"><span class="label">Entrada</span><span class="value"><?= $fmtInt($entrada); ?></span></span>
                                                        <span class="compact-badge percent"><span class="label">Conclusão</span><span class="value"><?= $conclusao; ?>%</span></span>
                                                        <span class="compact-badge <?= $estoqueClass; ?>"><span class="label">Estoque</span><span class="value"><?= $fmtInt($estoque); ?></span></span>
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
            const refreshFullBtn = document.getElementById('refresh-full');
            const tabButtons = Array.from(document.querySelectorAll('[data-map-tab]'));
            const panes = Array.from(document.querySelectorAll('[data-map-pane]'));
            let activePane = fullMode || window.location.hash === '#completo' ? 'completo' : 'resumo';
            let fullRendered = false;
            let compactYear = (mapaState.base || mapaState.hoje || new Date().toISOString().slice(0, 10)).slice(0, 4);
            const yearLabel = document.getElementById('compact-year-label');
            const updateYearLabel = () => { if (yearLabel) yearLabel.textContent = compactYear; };

            const fmtInt = (num) => new Intl.NumberFormat('pt-BR', {minimumFractionDigits: 0, maximumFractionDigits: 0}).format(Number(num || 0));
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
                    }, 140);
                };
                document.querySelectorAll('.map-pane[data-map-pane="resumo"] .table-wrap, .map-pane[data-map-pane="resumo"] .nav-dias').forEach((el) => {
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
                if ((params.full ?? fullMode) === true) qs.set('full', '1');
                const resp = await fetch(`${mapaUrl}${qs.toString() ? '?' + qs.toString() : ''}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const payload = await resp.json();
                if (!resp.ok) throw new Error(payload.message || 'Erro ao navegar');
                mapaState = payload;
                preferHoje(mapaState);
                fullMode = !!(mapaState.full);
                windowSize = Math.max(1, visibleDates().length);
                if (params.full || !compactYear) {
                    const refDate = mapaState.janela_inicio || mapaState.base || mapaState.hoje || new Date().toISOString().slice(0, 10);
                    compactYear = String(refDate).slice(0, 4);
                }
                renderAll();
                applyFullMode();
            };

            const renderRange = () => {
                const dates = visibleDates();
                const label = document.getElementById('janela-range');
                if (!label || !dates.length) return;
                const weeklyValues = dates.map((dia) => Number(mapaState?.datas?.[dia]?.media_conclusao ?? 0)).filter((val) => !Number.isNaN(val));
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
                    `<span class="nav-avg" data-nav-media="${dia}" data-media-base="${Number(info.media_conclusao || 0).toFixed(1)}">${feriados[dia] ? 'Feriado' : `Média ${media}%`}</span>` +
                `</div>`;
            };

            const headerCellMarkup = (dia, info) => {
                const media = Number(info.media_conclusao ?? 0).toFixed(1).replace('.', ',');
                return `<th><div class="dia-header${info.is_hoje ? ' hoje' : ''}" data-dia-header="${dia}">` +
                    `<div class="dia-header-date"><span class="dia-header-label">${info.label || fmtDate(dia)}</span>` +
                    `<small class="dia-header-week">${info.semana || ''}</small></div>` +
                    `<div class="dia-header-meta">` +
                        `<span class="dia-header-media" data-dia-media="${dia}" data-media-base="${Number(info.media_conclusao || 0).toFixed(1)}">${feriados[dia] ? 'Feriado' : `Média diária ${media}%`}</span>` +
                        `<label class="feriado-toggle" title="Marcar este dia como feriado"><input type="checkbox" data-feriado="${dia}"> Feriado</label>` +
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
                return `<td><div class="dia-card${info.is_hoje ? ' hoje' : ''}" data-dia="${dia}" data-produto="${linha.id}">` +
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
                                `<input type="hidden" name="tipo" value="planejado">
                                                    <input type="hidden" name="consumir_receita" value="0">` +
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
                                `<input type="hidden" name="tipo" value="estoque">
                                                    <input type="hidden" name="consumir_receita" value="0">` +
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

            const renderTabela = () => {
                const datas = visibleDates();
                const nav = document.getElementById('nav-dias');
                const headRow = document.querySelector('.mapa thead tr');
                const tbody = document.querySelector('.mapa tbody');
                if (!nav || !headRow || !tbody) return;

                nav.innerHTML = datas.map((dia) => navChipMarkup(dia, mapaState.datas[dia])).join('');
                headRow.innerHTML = '<th class="col-produto">Produto</th>' + datas.map((dia) => headerCellMarkup(dia, mapaState.datas[dia])).join('');

                const linhasHtml = (mapaState.linhas || []).map((linha) => {
                    const cells = datas.map((dia) => cellMarkup(linha, dia, mapaState.datas[dia] || {})).join('');
                    const lastDay = datas[datas.length - 1];
                    const saldo = Number(linha?.dias?.[lastDay]?.estoque_final ?? linha.estoque || 0);
                    let saldoClass = 'ok';
                    if (saldo < 0) saldoClass = 'negativo';
                    else if (saldo === 0) saldoClass = 'zerado';
                    else if (saldo >= lowMin && saldo <= lowMax) saldoClass = 'baixo';
                    return `<tr data-produto="${linha.id}"><td class="col-produto"><div class="produto-card"><div class="sku">${linha.sku || '—'}</div><div class="nome">${linha.nome || ''}</div><div class="saldo ${saldoClass}"><span>Saldo:</span><strong>${fmtInt(saldo)}</strong></div></div></td>${cells}</tr>`;
                }).join('');

                tbody.innerHTML = linhasHtml || `<tr><td class="col-produto" colspan="${1 + datas.length}" style="text-align:center; padding: 28px 12px;">Nenhum produto acabado cadastrado ainda.</td></tr>`;
            };

            const renderCompact = (state) => {
                const wrap = document.getElementById('compact-table');
                if (!wrap) return;
                const data = state || mapaState;
                const datas = Object.keys(data.datas || {}).sort().filter((dia) => `${dia}`.startsWith(`${compactYear}-`));
                const linhas = data.linhas || [];
                if (!datas.length || !linhas.length) {
                    wrap.innerHTML = `<div class="compact-empty">Nenhum registro para ${compactYear}. Ajuste o ano ou adicione lançamentos.</div>`;
                    return;
                }
                const header = ['<th class="compact-prod">Produto</th>'];
                datas.forEach((dia) => {
                    const info = data.datas[dia] || {};
                    const media = Number(info.media_conclusao || 0).toFixed(1).replace('.', ',');
                    const hojeCls = info.is_hoje ? ' hoje' : '';
                    header.push(`<th class="${hojeCls}"><div class="compact-th${hojeCls}"><div>${info.label || fmtDate(dia)}</div><small>${info.semana || ''}</small><small class="compact-media">Conclusão ${media}%</small></div></th>`);
                });

                const badge = (cls, label, valor, opts = {}) => {
                    const isPercent = opts.percent === true;
                    const display = isPercent ? `${Number(valor || 0).toFixed(1).replace('.', ',')}%` : fmtInt(valor);
                    return `<span class="compact-badge ${cls}"><span class="label">${label}</span><span class="value">${display}</span></span>`;
                };

                const rows = linhas.map((linha) => {
                    const cells = datas.map((dia) => {
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
                        return `<td><div class="compact-cell${hojeCls}">`
                            + `<div class="badge-row">`
                            + badge('saida', 'Saída', saida)
                            + badge('producao', 'Produção', producao)
                            + badge('entrada', 'Entrada', entrada)
                            + badge('percent', 'Conclusão', conclusao, {percent: true})
                            + badge(estoqueBadgeClass, 'Estoque', estoque)
                            + `</div>`
                            + `</div></td>`;
                    }).join('');
                    return `<tr><td class="compact-prod"><div class="compact-prod-info"><div class="sku">${linha.sku || '—'}</div><div class="nome">${linha.nome || ''}</div></div></td>${cells}</tr>`;
                });

                wrap.innerHTML = `<div class="compact-grid"><table><thead><tr>${header.join('')}</tr></thead><tbody>${rows.join('')}</tbody></table></div>`;
                fullRendered = true;
                if (yearLabel) yearLabel.textContent = compactYear;
                const hojeTh = wrap.querySelector('.compact-grid th.hoje');
                if (hojeTh) {
                    requestAnimationFrame(() => centerInScroll(wrap, hojeTh, 'auto'));
                }
            };

            const applyStatus = () => {
                const status = mapaState.status || {};
                const totalProdutos = (mapaState.linhas || []).length || 1;
                const neg = Number(status.negativos || 0);
                const zero = Number(status.zerados || 0);
                const baixo = Number(status.baixos || 0);
                const ok = Math.max(totalProdutos - (neg + zero + baixo), 0);
                document.querySelectorAll('.status-value').forEach((el) => {
                    const key = el.dataset.status;
                    if (!key) return;
                    el.textContent = fmtInt(status[key] || 0);
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
            };

            const applyNavMedias = () => {
                const datas = mapaState.datas || {};
                Object.entries(datas).forEach(([dia, info]) => {
                    const target = document.querySelector(`.nav-avg[data-nav-media="${dia}"]`);
                    const header = document.querySelector(`.dia-header-media[data-dia-media="${dia}"]`);
                    const media = Number(info.media_conclusao || 0);
                    if (target) {
                        target.dataset.mediaBase = media.toFixed(1);
                        if (!feriados[dia]) {
                            target.textContent = `Média ${media.toFixed(1).replace('.', ',')}%`;
                        }
                    }
                    if (header) {
                        header.dataset.mediaBase = media.toFixed(1);
                        if (!feriados[dia]) {
                            header.textContent = `Média diária ${media.toFixed(1).replace('.', ',')}%`;
                        }
                    }
                });
                Object.keys(feriados).forEach(renderFeriado);
            };

            const renderFeriado = (dia) => {
                const isFeriado = !!feriados[dia];
                const check = document.querySelector(`input[data-feriado="${dia}"]`);
                const nav = document.querySelector(`.nav-chip[data-dia-nav="${dia}"]`);
                const navAvg = document.querySelector(`.nav-avg[data-nav-media="${dia}"]`);
                const header = document.querySelector(`.dia-header-media[data-dia-media="${dia}"]`);
                const progress = document.querySelector(`.dia-card[data-dia="${dia}"] .progress-value`);
                const baseMedia = Number(navAvg?.dataset.mediaBase || header?.dataset.mediaBase || 0);
                if (check) check.checked = isFeriado;
                if (nav) nav.classList.toggle('feriado', isFeriado);
                if (navAvg) navAvg.textContent = isFeriado ? 'Feriado' : `Média ${baseMedia.toFixed(1).replace('.', ',')}%`;
                if (header) header.textContent = isFeriado ? 'Feriado' : `Média diária ${baseMedia.toFixed(1).replace('.', ',')}%`;
                if (progress) {
                    const original = progress.dataset.progressBase || progress.textContent;
                    progress.dataset.progressBase = original;
                    progress.textContent = isFeriado ? '—' : original;
                }
            };

            const bindFeriados = () => {
                document.querySelectorAll('input[data-feriado]').forEach((input) => {
                    const dia = input.dataset.feriado;
                    input.checked = !!feriados[dia];
                    input.addEventListener('change', () => {
                        if (input.checked) {
                            feriados[dia] = true;
                        } else {
                            delete feriados[dia];
                        }
                        localStorage.setItem('mapaFeriados', JSON.stringify(feriados));
                        renderFeriado(dia);
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
                    consumirReceita: !!form.querySelector('input[name="consumir_receita"]')?.checked,
                };
            };

            const restoreBulkFormState = (state) => {
                if (!state) return;
                const form = document.querySelector('.mapa .form-card');
                if (!form) return;
                const produto = form.querySelector('#produto_id');
                const data = form.querySelector('#data_referencia');
                const quantidade = form.querySelector('#quantidade');
                const obs = form.querySelector('#observacao');
                const tipos = form.querySelectorAll('input[name="tipo"]');
                const consumirReceita = form.querySelector('input[name="consumir_receita"]');
                if (produto) produto.value = state.produto;
                if (data) data.value = state.data;
                if (quantidade) quantidade.value = state.quantidade;
                if (obs) obs.value = state.observacao;
                if (tipos?.length) {
                    tipos.forEach((t) => {
                        t.checked = t.value === state.tipo;
                    });
                }
                if (consumirReceita) {
                    consumirReceita.checked = !!state.consumirReceita;
                }
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
                    const data = new FormData(form);
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const bulkSnapshot = form.classList.contains('form-card') ? captureBulkFormState() : null;
                    const scrollSnapshot = captureScrollState();
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = '...';
                    }
                    fetch(form.action, {
                        method: 'POST',
                        body: data,
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    }).then(async (resp) => {
                        const payload = await resp.json().catch(() => ({}));
                        if (!resp.ok) {
                            throw new Error(payload.message || 'Erro ao salvar');
                        }
                        if (payload.mapa) {
                            mapaState = payload.mapa;
                            fullMode = !!(mapaState.full);
                            renderAll();
                            applyFullMode();
                            restoreScrollState(scrollSnapshot);
                        } else {
                            fetchMapa({ start: mapaState.janela_inicio || mapaState.base || mapaState.hoje, full: fullMode }).catch(() => {});
                        }
                        if (bulkSnapshot) restoreBulkFormState(bulkSnapshot);
                        showToast(payload.message || 'Salvo');
                    }).catch((err) => {
                        showToast(err.message || 'Erro ao registrar', 'error');
                    }).finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = '+';
                        }
                    });
                });
            };

            const bindForms = () => {
                document.querySelectorAll('.mini-form').forEach(handleSubmit);
                const bulkForm = document.querySelector('.mapa .form-card');
                if (bulkForm) handleSubmit(bulkForm);
            };

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
                wrap.scrollTo({left: Math.max(0, target.offsetLeft), behavior});
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
                const align = () => {
                    if (navChip) {
                        navChip.classList.add('ativo');
                    }
                    if (navChip) {
                        const navRow = navChip.closest('.nav-dias');
                        if (navRow) {
                            alignStartInScroll(navRow, navChip, 'smooth');
                        } else {
                            navChip.scrollIntoView({behavior: 'smooth', inline: 'start', block: 'nearest'});
                        }
                    } else if (firstNav) {
                        firstNav.scrollIntoView({behavior: 'smooth', inline: 'start', block: 'nearest'});
                    }
                    if (targetCard) {
                        const wrap = targetCard.closest('.table-wrap');
                        if (wrap) {
                            alignStartInScroll(wrap, targetCard, 'smooth');
                        } else {
                            targetCard.scrollIntoView({behavior: 'smooth', inline: 'start', block: 'nearest'});
                        }
                        targetCard.classList.add('hoje');
                    }
                };
                requestAnimationFrame(align);
            };

            const renderAll = () => {
                renderRange();
                renderTabela();
                applyStatus();
                applyNavMedias();
                bindFeriados();
                bindForms();
                focusToday();
                applyFullMode();
                if (activePane === 'completo' || fullRendered) {
                    renderCompact();
                }
            };

            const switchPane = (pane) => {
                activePane = pane;
                tabButtons.forEach((btn) => btn.classList.toggle('active', btn.dataset.mapTab === pane));
                panes.forEach((paneEl) => {
                    const match = paneEl.dataset.mapPane === pane;
                    paneEl.hidden = !match;
                });
                if (pane === 'completo' && !mapaState.full) {
                    fetchMapa({ start: mapaState.janela_inicio || mapaState.base || mapaState.hoje, full: true })
                        .then(() => renderCompact())
                        .catch((err) => showToast(err.message || 'Erro ao carregar mapa completo', 'error'));
                } else if (pane === 'completo') {
                    renderCompact();
                }
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
                updateYearLabel();
                try {
                    await fetchMapa({ start: `${compactYear}-01-01`, full: true });
                    renderCompact();
                } catch (err) {
                    showToast(err.message || 'Erro ao carregar ano', 'error');
                }
            };

            if (refreshFullBtn) {
                refreshFullBtn.addEventListener('click', () => {
                    switchPane('completo');
                    fetchMapa({ start: `${compactYear}-01-01`, full: true }).catch(() => {});
                });
            }

            document.getElementById('year-prev')?.addEventListener('click', () => changeYear(-1));
            document.getElementById('year-next')?.addEventListener('click', () => changeYear(1));

            tabButtons.forEach((btn) => {
                btn.addEventListener('click', (ev) => {
                    const target = btn.dataset.mapTab;
                    if (target !== 'resumo') {
                        return;
                    }
                    ev.preventDefault();
                    switchPane(target);
                });
            });

            updateYearLabel();
            installScrollOptimizer();
            renderAll();
            switchPane(activePane);
        })();
    </script>
</section>
