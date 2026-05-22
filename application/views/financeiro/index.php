<?php
$entradas = $entradas ?? [];
$saidas = $saidas ?? [];
$hoje = date('Y-m-d');

if (!function_exists('financeiro_format_date')) {
    function financeiro_format_date(?string $date): string
    {
        if (!$date) {
            return '-';
        }

        $timestamp = strtotime($date);
        return $timestamp ? date('d/m/Y', $timestamp) : $date;
    }
}
?>

<style>
    .finance-tabs,
    .finance-card-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
    .finance-tab-panel > .panel.finance-list-active > .finance-card-tabs { position: relative !important; top: -68px !important; margin-bottom: -45px !important; transform: none !important; }
    .finance-tab-button,
    .finance-card-tab-button { display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--border, #e5e7eb); border-radius: 999px; padding: 8px 14px; font-weight: 700; background: var(--surface-alt, #f8fafc); color: var(--text, #111827); cursor: pointer; }
    .finance-tab-button .icon,
    .finance-card-tab-button .icon { width: 16px; height: 16px; }
    .finance-tab-button.active,
    .finance-card-tab-button.active { background: var(--primary, #4b5563); color: #fff; border-color: transparent; }
    .finance-card-header .finance-card-tabs { justify-content: flex-end; margin-bottom: 0; margin-left: auto; }
    .finance-tab-panel,
    .finance-card-panel { display: none; }
    .finance-tab-panel.active,
    .finance-card-panel.active { display: block; }
    .finance-card-header { display: flex; justify-content: space-between; gap: 18px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 12px; }
    .finance-card-header h2 { margin-bottom: 4px; }
    .finance-lancamento-heading { margin-bottom: 26px; }
    .finance-lancamento-heading h3 { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .finance-lancamento-heading .icon { width: 20px; height: 20px; color: var(--primary, #4b5563); }
    .finance-lancamento-heading .finance-form-note { margin: 0; }
    .finance-table-wrap { overflow-x: auto; }
    .finance-form-note { margin-top: 0; }
    .finance-list-toolbar { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin: 12px 0 16px; }
    .finance-list-toolbar label { display: flex; flex-direction: column; gap: 4px; margin: 0; min-width: 150px; font-size: 0.82rem; font-weight: 700; color: var(--muted, #64748b); }
    .finance-list-toolbar select { min-height: 38px; border: 1px solid var(--border, #e5e7eb); border-radius: 10px; padding: 7px 10px; background: var(--surface, #fff); color: var(--text, #111827); font-weight: 700; }
    .finance-status-filter-group { position: relative; min-width: 210px; }
    .finance-status-filter-title { display: block; margin-bottom: 4px; font-size: 0.82rem; font-weight: 700; color: var(--muted, #64748b); }
    .finance-status-filter-button { display: inline-flex; min-height: 38px; width: 100%; align-items: center; justify-content: space-between; gap: 8px; border: 1px solid var(--border, #e5e7eb); border-radius: 10px; padding: 7px 10px; background: var(--surface, #fff); color: var(--text, #111827); font-weight: 800; cursor: pointer; }
    .finance-status-filter-button .icon { width: 16px; height: 16px; flex: 0 0 16px; }
    .finance-status-filter-current { margin-left: auto; }
    .finance-status-filter-chevron { font-size: 0.8rem; opacity: 0.72; }
    .finance-status-filter-menu { position: absolute; z-index: 9999; top: calc(100% + 6px); left: 0; min-width: 230px; border: 1px solid var(--border, #e5e7eb); border-radius: 14px; padding: 8px; background: var(--surface, #fff); box-shadow: 0 18px 40px rgba(15, 23, 42, 0.16); }
    .finance-status-filter-option { display: flex; width: 100%; align-items: center; justify-content: space-between; gap: 10px; border: 0; border-radius: 10px; padding: 8px 10px; background: transparent; color: inherit; font-weight: 800; text-align: left; cursor: pointer; }
    .finance-status-filter-option:hover,
    .finance-status-filter-option.active { background: rgba(86, 97, 255, 0.08); }
    .finance-status-neutral { background: rgba(100, 116, 139, 0.14); color: #475569; border: 1px solid rgba(100, 116, 139, 0.25); }
    .finance-list-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 5px; }
    .finance-list-heading > div:first-child { transform: translateY(-12px); }
    .finance-list-heading h3 { margin-bottom: 4px; }
    .finance-list-side { display: flex; flex-direction: column; align-items: flex-end; gap: 9px; }
    .finance-list-actions { display: flex; align-items: center; justify-content: flex-end; gap: 7px; flex-wrap: wrap; transform: translateY(-15px); }
    .finance-list-action-button { display: inline-flex; align-items: center; gap: 6px; border: 1px solid color-mix(in srgb, var(--primary, #4f46e5) 20%, var(--border, #e5e7eb)); border-radius: 999px; padding: 6px 10px; background: var(--surface, #fff); color: var(--text, #111827); font-size: 0.74rem; font-weight: 850; cursor: pointer; box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06); }
    .finance-list-action-button:hover { border-color: var(--primary, #4f46e5); color: var(--primary, #4f46e5); background: color-mix(in srgb, var(--primary, #4f46e5) 6%, var(--surface, #fff)); }
    .finance-list-action-button .icon { width: 14px; height: 14px; }
    .finance-list-search { position: relative; display: block; width: min(100%, 390px); margin-top: 3px; transform: translateY(6px); }
    .finance-list-search .icon { position: absolute; left: 12px; top: 50%; width: 16px; height: 16px; color: var(--muted, #64748b); transform: translateY(-50%); pointer-events: none; }
    .finance-list-search input { width: 100%; min-height: 38px; border: 1px solid var(--border, #e5e7eb); border-radius: 999px; padding: 8px 12px 8px 36px; background: var(--surface, #fff); color: var(--text, #111827); font-size: 0.82rem; font-weight: 800; box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06); }
    .finance-list-search input:focus { outline: 0; border-color: var(--primary, #4f46e5); box-shadow: var(--primary-glow, 0 0 0 3px rgba(79, 70, 229, 0.20)); }
    .finance-list-title { display: inline-flex; align-items: center; gap: 8px; }
    .finance-list-title-icon { position: relative; display: inline-grid; place-items: center; width: 24px; height: 22px; color: var(--primary, #4f46e5); flex: 0 0 24px; }
    .finance-list-title-icon .icon { position: absolute; stroke-width: 2.6; }
    .finance-list-title-icon .icon:first-child { width: 20px; height: 20px; left: 0; top: 1px; }
    .finance-list-title-icon .icon:last-child { width: 13px; height: 13px; right: 0; bottom: 0; padding: 1px; border-radius: 999px; background: var(--surface, #fff); }
    .finance-list-pagination { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-top: 14px; color: var(--muted, #64748b); font-size: 0.82rem; font-weight: 800; }
    .finance-list-page-size { display: inline-flex; align-items: center; gap: 8px; }
    .finance-list-page-size select { min-height: 34px; border: 1px solid var(--border, #e5e7eb); border-radius: 999px; padding: 6px 28px 6px 10px; background: var(--surface, #fff); color: var(--text, #111827); font-weight: 850; }
    .finance-list-page-buttons { display: inline-flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .finance-list-page-button { min-width: 34px; min-height: 34px; border: 1px solid var(--border, #e5e7eb); border-radius: 999px; padding: 6px 10px; background: var(--surface, #fff); color: var(--text, #111827); font-weight: 850; cursor: pointer; }
    .finance-list-page-input { width: 58px; min-height: 34px; border: 1px solid var(--border, #e5e7eb); border-radius: 999px; padding: 6px 10px; background: var(--surface, #fff); color: var(--text, #111827); font-weight: 850; text-align: center; }
    .finance-list-page-button:hover { border-color: var(--primary, #4f46e5); color: var(--primary, #4f46e5); }
    .finance-list-page-button.active { border-color: var(--primary, #4f46e5); background: var(--primary, #4f46e5); color: #fff; }
    .finance-list-page-ellipsis { min-width: 22px; text-align: center; color: var(--muted, #64748b); }
    .finance-export-group { position: relative; display: inline-flex; }
    .finance-export-menu { position: absolute; z-index: 50; top: calc(100% + 8px); right: 0; display: grid; gap: 6px; min-width: 132px; border: 1px solid var(--border, #e5e7eb); border-radius: 14px; padding: 8px; background: var(--surface, #fff); box-shadow: 0 18px 40px rgba(15, 23, 42, 0.16); }
    .finance-export-menu[hidden] { display: none; }
    .finance-print-area { display: none; }
    @media print {
        body.finance-print-mode > *:not(.finance-print-area) { display: none !important; }
        body.finance-print-mode .finance-print-area { display: block !important; padding: 24px; color: #111827; font-family: Arial, sans-serif; }
        .finance-print-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; margin-bottom: 18px; border-bottom: 3px solid #4f46e5; padding-bottom: 14px; }
        .finance-print-kicker { margin: 0 0 5px; color: #4f46e5; font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .finance-print-area h1 { margin: 0; font-size: 24px; }
        .finance-print-period { color: #111827; font-size: 15px; font-weight: 800; text-align: right; }
        .finance-print-muted { margin-top: 4px; color: #64748b; font-size: 12px; font-weight: 700; }
        .finance-print-area table { width: 100%; border-collapse: collapse; }
        .finance-print-area th,
        .finance-print-area td { border: 1px solid #d1d5db; padding: 8px; text-align: left; font-size: 13px; }
        .finance-print-area th { background: #eef2ff; color: #312e81; }
        .finance-print-footer { display: flex; justify-content: flex-end; margin-top: 16px; }
        .finance-print-total { min-width: 240px; border: 2px solid #4f46e5; border-radius: 14px; padding: 12px 14px; text-align: right; background: #eef2ff; }
        .finance-print-total span { display: block; color: #64748b; font-size: 11px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
        .finance-print-total strong { display: block; margin-top: 4px; color: #312e81; font-size: 22px; }
    }
    .finance-status-summary { display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-wrap: wrap; min-height: 34px; align-self: flex-end; order: 2; transform: translateY(-6px); }
    .finance-period-card { position: relative; overflow: hidden; order: 1; min-width: 430px; border: 1px solid color-mix(in srgb, var(--primary, #4f46e5) 24%, var(--border, #e5e7eb)); border-radius: 20px; padding: 12px 14px; background: linear-gradient(135deg, color-mix(in srgb, var(--primary, #4f46e5) 10%, var(--surface, #fff)), var(--surface, #fff) 58%, color-mix(in srgb, var(--primary, #4f46e5) 6%, var(--surface-alt, #f8fafc))); box-shadow: 0 18px 38px rgba(15, 23, 42, 0.10); }
    .finance-period-card::before { content: ''; position: absolute; inset: 0 auto 0 0; width: 5px; background: var(--primary, #4f46e5); }
    .finance-period-card.is-hidden { display: none; }
    .finance-period-card[data-finance-period-card="resumo"] { transform: translateY(-12px); }
    .finance-card-header .finance-period-card { flex: 0 1 440px; margin-left: auto; }
    .finance-period-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 8px; }
    .finance-period-label { display: inline-flex; align-items: center; gap: 6px; color: var(--muted, #64748b); font-size: 0.72rem; font-weight: 900; letter-spacing: 0.05em; text-transform: uppercase; }
    .finance-period-label .icon { width: 15px; height: 15px; }
    .finance-period-total { color: var(--primary, #4f46e5); font-size: 1.65rem; font-weight: 900; white-space: nowrap; }
    .finance-period-controls { display: flex; justify-content: flex-end; gap: 8px; flex-wrap: wrap; }
    .finance-period-controls select,
    .finance-period-controls input { min-height: 32px; border: 1px solid color-mix(in srgb, var(--primary, #4f46e5) 18%, var(--border, #e5e7eb)); border-radius: 999px; padding: 5px 10px; background: var(--surface, #fff); color: var(--text, #111827); font-size: 0.78rem; font-weight: 800; box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05); }
    .finance-period-controls input[type=number] { width: 92px; }
    .finance-period-footer { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; margin-top: 9px; }
    .finance-period-focus { color: var(--primary, #4f46e5); font-size: 1.32rem; font-weight: 900; letter-spacing: 0.04em; line-height: 1; text-transform: uppercase; white-space: nowrap; }
    .finance-period-caption { display: block; color: var(--muted, #64748b); font-size: 0.74rem; font-weight: 700; text-align: right; }
    @media (max-width: 900px) {
        .finance-list-heading { align-items: stretch; flex-direction: column; }
        .finance-list-side { align-items: stretch; }
        .finance-period-card { min-width: 0; width: 100%; }
        .finance-status-summary { justify-content: flex-start; }
    }
    .finance-status-pill { display: inline-flex; align-items: center; gap: 7px; border-radius: 999px; padding: 5px 8px 5px 10px; font-size: 0.7rem; font-weight: 900; letter-spacing: 0.03em; text-transform: uppercase; white-space: nowrap; }
    .finance-status-pill-count { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 22px; border-radius: 999px; padding: 0 6px; font-size: 0.72rem; font-weight: 900; background: rgba(255,255,255,0.7); }
    .finance-status-filter-group.is-heading { display: inline-flex; min-width: 0; }
    .finance-status-filter-heading { min-height: auto; width: auto; border: 0; border-radius: 0; padding: 0; background: transparent; color: inherit; font: inherit; font-weight: 700; }
    .finance-status-filter-heading .icon { width: 18px; height: 18px; padding: 2px; border-radius: 8px; background: rgba(0,0,0,0.06); opacity: 0.72; }
    .finance-status-filter-heading.is-active .icon,
    .finance-status-filter-heading:hover .icon { background: rgba(86, 97, 255, 0.12); opacity: 1; }
    .finance-status-filter-heading .finance-status-filter-count { margin-left: 0; padding: 2px 6px; font-size: 0.68rem; letter-spacing: 0; text-transform: none; }
    .finance-status-filter-group.is-heading .finance-status-filter-menu { position: fixed; left: auto; top: auto; text-transform: none; }
    .finance-status-filter-option.active::after { content: '✓'; font-weight: 900; color: #4f46e5; }
    .finance-sort-link { display: inline-flex; align-items: center; gap: 6px; border: 0; padding: 0; background: transparent; color: inherit; font: inherit; font-weight: 700; cursor: pointer; }
    .finance-sort-link:hover .finance-sort-indicator { opacity: 1; }
    .finance-sort-indicator { font-size: 0.85rem; opacity: 0.65; padding: 2px 5px; border-radius: 8px; background: rgba(0,0,0,0.06); color: inherit; transition: all 0.2s ease; }
    .finance-sort-indicator.active { background: rgba(86, 97, 255, 0.12); color: inherit; opacity: 1; }
    .finance-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .finance-status-danger { background: rgba(220, 38, 38, 0.16); color: #b91c1c; border: 1px solid rgba(220, 38, 38, 0.28); }
    .finance-status-warning { background: rgba(250, 204, 21, 0.22); color: #92400e; border: 1px solid rgba(250, 204, 21, 0.45); }
    .finance-status-success { background: rgba(16, 185, 129, 0.16); color: #047857; border: 1px solid rgba(16, 185, 129, 0.28); }
    .finance-status-paid { background: rgba(37, 99, 235, 0.16); color: #1d4ed8; border: 1px solid rgba(37, 99, 235, 0.28); }
    .finance-paid-form { margin: 0; }
    .finance-paid-button { border: 1px solid rgba(16, 185, 129, 0.35); background: rgba(16, 185, 129, 0.12); color: #047857; border-radius: 999px; padding: 6px 12px; font-weight: 800; cursor: pointer; }
    .finance-paid-button:hover { background: rgba(16, 185, 129, 0.2); }
    .finance-paid-button:disabled,
    .finance-paid-button.is-paid { border-color: rgba(148, 163, 184, 0.35); background: rgba(148, 163, 184, 0.18); color: #64748b; cursor: default; opacity: 1; }
    .finance-lancamento-form { display: flex; align-items: flex-end; gap: 16px; flex-wrap: nowrap; }
    .finance-card-panel[data-finance-card-panel$="-lancamento"] .finance-lancamento-form { transform: translateY(16px); }
    .finance-lancamento-form label { margin: 0; }
    .finance-lancamento-form .finance-field-label { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px; font-weight: 800; line-height: 1.2; }
    .finance-lancamento-form .finance-field-label .icon { width: 15px; height: 15px; color: var(--primary, #4b5563); stroke-width: 2.4; }
    .finance-lancamento-form .finance-field-date,
    .finance-lancamento-form .finance-field-validity { flex: 0 0 150px; }
    .finance-lancamento-form .finance-field-title { flex: 0 0 110px; }
    .finance-lancamento-form .finance-field-company { flex: 1 1 320px; min-width: 280px; }
    .finance-lancamento-form .finance-field-value { flex: 0 0 170px; }
    .finance-lancamento-form .finance-field-date input,
    .finance-lancamento-form .finance-field-validity input,
    .finance-lancamento-form .finance-field-title input,
    .finance-lancamento-form .finance-field-company input,
    .finance-lancamento-form .finance-field-value input { width: 100%; max-width: 100%; }
    .finance-lancamento-form .form-actions { flex: 0 0 auto; margin: 0; }
    @media (max-width: 1180px) {
        .finance-lancamento-form { flex-wrap: wrap; }
    }
    .finance-table-wrap .data-table { font-size: 0.82rem; }
    .finance-table-wrap .data-table th,
    .finance-table-wrap .data-table td { padding: 0.45rem 0.55rem; line-height: 1.2; white-space: nowrap; }
    .finance-table-wrap .data-table th:nth-child(3),
    .finance-table-wrap .data-table td:nth-child(3) { white-space: normal; min-width: 180px; max-width: 280px; }
    .finance-table-wrap .finance-status-badge { padding: 3px 8px; font-size: 0.68rem; }
    .finance-table-wrap .finance-paid-button { padding: 4px 10px; font-size: 0.76rem; }
    .finance-summary-grid { display: block; margin-top: 16px; }
    .finance-summary-metrics { display: grid; grid-template-columns: repeat(3, minmax(180px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .finance-summary-card,
    .finance-chart-panel { border: 1px solid var(--border, #e5e7eb); border-radius: 18px; padding: 14px; background: var(--surface, #fff); box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08); }
    .finance-summary-card { display: grid; gap: 8px; }
    .finance-summary-card span { color: var(--muted, #64748b); font-size: 0.72rem; font-weight: 900; letter-spacing: 0.05em; text-transform: uppercase; }
    .finance-summary-card strong { color: var(--text, #111827); font-size: 1.52rem; line-height: 1; }
    .finance-summary-card small { color: var(--muted, #64748b); font-size: 0.78rem; font-weight: 700; }
    .finance-summary-card.is-entrada strong { color: #047857; }
    .finance-summary-card.is-saida strong { color: #b91c1c; }
    .finance-summary-card.is-saldo strong.is-negative { color: #b91c1c; }
    .finance-summary-card.is-saldo strong.is-positive { color: #047857; }
    .finance-summary-charts { display: grid; grid-template-columns: repeat(2, minmax(280px, 1fr)); gap: 16px; min-width: 0; }
    .finance-summary-panel { display: none; }
    .finance-summary-panel.active { display: block; }
    .finance-performance-compare-card { position: relative; overflow: hidden; margin-bottom: 16px; border: 1px solid rgba(var(--primary-rgb, 79, 70, 229), 0.24); border-radius: 18px; padding: 18px 20px; background: linear-gradient(135deg, rgba(var(--primary-rgb, 79, 70, 229), 0.09), var(--surface, #fff) 48%, rgba(148, 163, 184, 0.06)); box-shadow: var(--primary-shadow, 0 14px 34px rgba(15, 23, 42, 0.08)); }
    .finance-performance-compare-card::before { content: ''; position: absolute; inset: 0 auto 0 0; width: 7px; background: linear-gradient(180deg, var(--primary, #4f46e5), var(--primary-dark, #3730a3)); }
    .finance-performance-top { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 18px; padding-left: 8px; }
    .finance-performance-type { display: inline-flex; align-items: center; gap: 8px; color: var(--primary-dark, #475569); font-size: 1rem; font-weight: 900; letter-spacing: 0.08em; text-transform: uppercase; }
    .finance-performance-type .icon { width: 20px; height: 20px; }
    .finance-performance-type select { border: 0; padding: 0 18px 0 0; background: transparent; color: inherit; font: inherit; letter-spacing: inherit; text-transform: uppercase; cursor: pointer; }
    .finance-performance-controls { display: grid; grid-template-columns: minmax(240px, 1fr) auto minmax(240px, 1fr); gap: 20px; align-items: end; }
    .finance-performance-side { display: grid; grid-template-columns: minmax(120px, 0.82fr) minmax(170px, 1fr); gap: 12px; }
    .finance-performance-field { display: grid; gap: 5px; }
    .finance-performance-field span { color: var(--muted, #64748b); font-size: 0.72rem; font-weight: 900; letter-spacing: 0.05em; text-transform: uppercase; }
    .finance-performance-field select,
    .finance-performance-field input { min-height: 46px; width: 100%; border: 1px solid rgba(var(--primary-rgb, 79, 70, 229), 0.18); border-radius: 999px; padding: 8px 16px; background: rgba(255,255,255,0.92); color: var(--text, #111827); font-size: 0.94rem; font-weight: 900; box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06); }
    .finance-performance-field select:focus,
    .finance-performance-field input:focus { outline: 0; border-color: var(--primary, #4f46e5); box-shadow: var(--primary-glow, 0 0 0 3px rgba(79, 70, 229, 0.20)); }
    .finance-performance-x { color: var(--primary, #4f46e5); font-size: 2.6rem; font-weight: 950; line-height: 1; align-self: center; text-shadow: 0 10px 22px rgba(var(--primary-rgb, 79, 70, 229), 0.18); }
    .finance-performance-footer { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-top: 18px; padding-left: 8px; }
    .finance-performance-focus { color: var(--primary, #4f46e5); font-size: 1.55rem; font-weight: 950; letter-spacing: 0.04em; text-transform: uppercase; }
    .finance-performance-caption { color: #64748b; font-size: 0.95rem; font-weight: 850; text-align: right; }
    .finance-performance-kpis { display: grid; grid-template-columns: repeat(3, minmax(180px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .finance-performance-charts { display: grid; grid-template-columns: repeat(2, minmax(280px, 1fr)); gap: 16px; }
    .finance-chart-panel { position: relative; min-width: 0; }
    .finance-chart-panel.has-chart-zoom { padding-bottom: 48px; }
    .finance-chart-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
    .finance-chart-header h3 { margin: 0 0 4px; }
    .finance-chart-header p { margin: 0; color: var(--muted, #64748b); font-size: 0.84rem; font-weight: 650; }
    .finance-chart-tools { display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex-wrap: wrap; }
    .finance-chart-legend { display: inline-flex; align-items: center; gap: 10px; flex-wrap: wrap; color: var(--muted, #64748b); font-size: 0.76rem; font-weight: 850; }
    .finance-chart-legend span { display: inline-flex; align-items: center; gap: 5px; }
    .finance-chart-legend span::before { content: ''; width: 10px; height: 10px; border-radius: 999px; background: var(--legend-color); }
    .finance-chart-actions { position: absolute; right: 14px; bottom: 12px; z-index: 12; display: inline-flex; align-items: center; gap: 5px; border: 1px solid rgba(100, 116, 139, 0.18); border-radius: 999px; padding: 3px; background: rgba(248,250,252,0.94); box-shadow: 0 10px 24px rgba(15, 23, 42, 0.10); }
    .finance-chart-action { display: inline-grid; place-items: center; width: 26px; height: 26px; border: 0; border-radius: 999px; background: transparent; color: #64748b; cursor: pointer; transition: background .15s ease, color .15s ease, transform .15s ease; }
    .finance-chart-action:hover,
    .finance-chart-action:focus-visible { outline: 0; background: rgba(var(--primary-rgb, 79, 70, 229), 0.11); color: var(--primary, #4f46e5); }
    .finance-chart-action:active { transform: scale(0.94); }
    .finance-chart-action .icon { width: 14px; height: 14px; stroke-width: 2.7; }
    .finance-chart-canvas { display: block; width: 100%; height: 260px; }
    .finance-chart-canvas.is-pie { height: 250px; }
    .finance-chart-tooltip { position: absolute; z-index: 20; display: none; min-width: 168px; border: 1px solid rgba(15, 23, 42, 0.10); border-radius: 12px; padding: 9px 10px; background: rgba(255,255,255,0.96); color: var(--text, #111827); box-shadow: 0 18px 38px rgba(15, 23, 42, 0.16); pointer-events: none; }
    .finance-chart-tooltip.active { display: grid; gap: 3px; }
    .finance-chart-tooltip strong { color: var(--text, #111827); font-size: 0.82rem; }
    .finance-chart-tooltip span { color: var(--muted, #64748b); font-size: 0.75rem; font-weight: 800; }
    .finance-summary-note { margin-top: 12px; color: var(--muted, #64748b); font-size: 0.8rem; font-weight: 700; text-align: right; }
    @media (max-width: 1180px) {
        .finance-summary-grid,
        .finance-summary-charts,
        .finance-performance-kpis,
        .finance-performance-charts,
        .finance-performance-controls,
        .finance-performance-side { grid-template-columns: 1fr; }
        .finance-performance-x { text-align: center; }
        .finance-performance-footer { align-items: flex-start; flex-direction: column; }
        .finance-performance-caption { text-align: left; }
    }
    .finance-live-message { display: none; margin-bottom: 14px; border-radius: 14px; padding: 12px 14px; font-weight: 700; }
    .finance-live-message.active { display: block; }
    .finance-live-message.success { background: rgba(16, 185, 129, 0.14); color: #047857; border: 1px solid rgba(16, 185, 129, 0.25); }
    .finance-live-message.error { background: rgba(220, 38, 38, 0.14); color: #b91c1c; border: 1px solid rgba(220, 38, 38, 0.25); }

</style>

<div id="finance-live-message" class="finance-live-message" role="status" aria-live="polite"></div>

<div class="finance-tabs" role="tablist" aria-label="Lançamentos financeiros">
    <button type="button" class="finance-tab-button active" id="tab-entrada" data-finance-tab="entrada" aria-selected="true"><span class="icon" data-lucide="arrow-down-circle"></span><span>Entrada</span></button>
    <button type="button" class="finance-tab-button" id="tab-saida" data-finance-tab="saida" aria-selected="false"><span class="icon" data-lucide="arrow-up-circle"></span><span>Saída</span></button>
    <button type="button" class="finance-tab-button" id="tab-resumo" data-finance-tab="resumo" aria-selected="false"><span class="icon" data-lucide="pie-chart"></span><span>Resumo</span></button>
</div>

<section class="finance-tab-panel active" data-finance-panel="entrada" aria-labelledby="tab-entrada">
    <section class="panel">
        <div class="finance-card-header">
            <div>
                <h2>Entrada</h2>
                <p class="panel-description finance-form-note">Cadastre títulos de entrada e acompanhe a lista dentro do mesmo card.</p>
            </div>
            <div class="finance-period-card is-hidden" data-finance-period-card="entrada">
                <div class="finance-period-top">
                    <span class="finance-period-label"><span class="icon" data-lucide="calendar-clock"></span><span>Valor lançado</span></span>
                    <strong class="finance-period-total" data-finance-period-total="entrada">R$ 0,00</strong>
                </div>
                <div class="finance-period-controls" aria-label="Filtro de valor lançado de entrada">
                    <select data-finance-period-select="entrada" aria-label="Período do valor de entrada">
                        <option value="diario">Diário</option>
                        <option value="semanal">Semanal</option>
                        <option value="mensal" selected>Mensal</option>
                        <option value="anual">Anual</option>
                    </select>
                    <input type="date" data-finance-period-input="entrada" data-period-input="diario" value="<?= htmlspecialchars($hoje, ENT_QUOTES, 'UTF-8'); ?>" hidden>
                    <input type="week" data-finance-period-input="entrada" data-period-input="semanal" hidden>
                    <input type="month" data-finance-period-input="entrada" data-period-input="mensal" value="<?= htmlspecialchars(date('Y-m'), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="number" data-finance-period-input="entrada" data-period-input="anual" min="2000" max="2100" value="<?= htmlspecialchars(date('Y'), ENT_QUOTES, 'UTF-8'); ?>" hidden>
                </div>
                <div class="finance-period-footer">
                    <strong class="finance-period-focus" data-finance-period-focus="entrada"><?= htmlspecialchars(strtoupper(strftime('%B/%y')), ENT_QUOTES, 'UTF-8'); ?></strong>
                    <small class="finance-period-caption" data-finance-period-caption="entrada"></small>
                </div>
            </div>
        </div>

        <div class="finance-card-tabs" role="tablist" aria-label="Entrada">
            <button type="button" class="finance-card-tab-button active" id="tab-entrada-lancamento" data-finance-subtab="entrada" data-finance-subpanel="lancamento" aria-selected="true"><span class="icon" data-lucide="plus-circle"></span><span>Lançamento</span></button>
            <button type="button" class="finance-card-tab-button" id="tab-entrada-lista" data-finance-subtab="entrada" data-finance-subpanel="lista" aria-selected="false"><span class="icon" data-lucide="list-checks"></span><span>Lista de entrada</span></button>
        </div>

        <div class="finance-card-panel active" data-finance-card-panel="entrada-lancamento" aria-labelledby="tab-entrada-lancamento">
            <div class="finance-lancamento-heading">
                <h3><span class="icon" data-lucide="file-plus-2"></span><span>Novo lançamento de entrada</span></h3>
                <p class="panel-description finance-form-note">Informe data, Nº do título, empresa, valor e validade do lançamento.</p>
            </div>
            <form method="post" action="<?= htmlspecialchars($url('financeiro/lancamentos'), ENT_QUOTES, 'UTF-8'); ?>" class="form-grid finance-lancamento-form" data-finance-live-form>
                <input type="hidden" name="tipo" value="entrada">
                <label class="finance-field-date">
                    <span class="finance-field-label"><span class="icon" data-lucide="calendar-days"></span><span>Data</span></span>
                    <input type="date" name="data_lancamento" value="<?= htmlspecialchars($hoje, ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>
                <label class="finance-field-title">
                    <span class="finance-field-label"><span class="icon" data-lucide="hash"></span><span>Nº do título</span></span>
                    <input type="text" name="numero_titulo" required maxlength="8" inputmode="numeric" pattern="[0-9]{1,8}" placeholder="00000001">
                </label>
                <label class="finance-field-company">
                    <span class="finance-field-label"><span class="icon" data-lucide="building-2"></span><span>Nome da empresa</span></span>
                    <input type="text" name="nome_empresa" required placeholder="Empresa responsável">
                </label>
                <label class="finance-field-value">
                    <span class="finance-field-label"><span class="icon" data-lucide="circle-dollar-sign"></span><span>Valor</span></span>
                    <input type="number" step="0.01" min="0.01" max="9999999999.99" name="valor" required placeholder="0,00">
                </label>
                <label class="finance-field-validity">
                    <span class="finance-field-label"><span class="icon" data-lucide="calendar-check"></span><span>Validade</span></span>
                    <input type="date" name="validade" required>
                </label>
                <div class="form-actions">
                    <button class="button button-primary" type="submit">Salvar entrada</button>
                </div>
            </form>
        </div>

        <div class="finance-card-panel" data-finance-card-panel="entrada-lista" aria-labelledby="tab-entrada-lista">
            <div class="finance-list-heading">
                <div>
                    <h3 class="finance-list-title"><span class="finance-list-title-icon" aria-hidden="true"><span class="icon" data-lucide="list-checks"></span><span class="icon" data-lucide="arrow-down-circle"></span></span><span>Lista de entrada</span></h3>
                    <p class="panel-description">Entradas cadastradas no módulo financeiro.</p>
                    <label class="finance-list-search">
                        <span class="icon" data-lucide="search"></span>
                        <input type="search" data-finance-list-search="entrada" placeholder="Buscar por titulo, data, empresa, valor ou validade" aria-label="Buscar na lista de entrada">
                    </label>
                </div>
                <div class="finance-list-side">
                    <div class="finance-list-actions" aria-label="Ações da lista de entradas">
                        <button type="button" class="finance-list-action-button" data-finance-list-action="print" data-finance-list-type="entrada"><span class="icon" data-lucide="printer"></span><span>Imprimir</span></button>
                        <div class="finance-export-group" data-finance-export-group>
                            <button type="button" class="finance-list-action-button" data-finance-export-trigger aria-haspopup="true" aria-expanded="false"><span class="icon" data-lucide="download"></span><span>Exportar</span></button>
                            <div class="finance-export-menu" data-finance-export-menu hidden>
                                <button type="button" class="finance-list-action-button" data-finance-list-action="csv" data-finance-list-type="entrada"><span class="icon" data-lucide="file-text"></span><span>CSV</span></button>
                                <button type="button" class="finance-list-action-button" data-finance-list-action="txt" data-finance-list-type="entrada"><span class="icon" data-lucide="file-text"></span><span>TXT</span></button>
                                <button type="button" class="finance-list-action-button" data-finance-list-action="xls" data-finance-list-type="entrada"><span class="icon" data-lucide="file-text"></span><span>XLS</span></button>
                            </div>
                        </div>
                    </div>
                    <div class="finance-status-summary" data-finance-status-summary="entrada" aria-label="Resumo de status das entradas"></div>
                </div>
            </div>
            <div class="finance-table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><button type="button" class="finance-sort-link" data-finance-sort-button="entrada" data-finance-sort-field="data" aria-label="Ordenar entradas por data" title="Clique para ordenar a data"><span class="finance-sort-label">Data</span><span class="finance-sort-indicator" aria-hidden="true">⇅</span></button></th>
                            <th><button type="button" class="finance-sort-link" data-finance-sort-button="entrada" data-finance-sort-field="numero_titulo" aria-label="Ordenar entradas por número do título" title="Clique para ordenar o Nº do título"><span class="finance-sort-label">Nº do título</span><span class="finance-sort-indicator" aria-hidden="true">⇅</span></button></th>
                            <th><button type="button" class="finance-sort-link" data-finance-sort-button="entrada" data-finance-sort-field="nome_empresa" aria-label="Ordenar entradas por empresa" title="Clique para ordenar a empresa"><span class="finance-sort-label">Empresa</span><span class="finance-sort-indicator" aria-hidden="true">⇅</span></button></th>
                            <th><button type="button" class="finance-sort-link" data-finance-sort-button="entrada" data-finance-sort-field="valor" aria-label="Ordenar entradas por valor" title="Clique para ordenar o valor"><span class="finance-sort-label">Valor</span><span class="finance-sort-indicator" aria-hidden="true">⇅</span></button></th>
                            <th><button type="button" class="finance-sort-link" data-finance-sort-button="entrada" data-finance-sort-field="validade" aria-label="Ordenar entradas por validade" title="Clique para ordenar a validade"><span class="finance-sort-label">Validade</span><span class="finance-sort-indicator" aria-hidden="true">⇅</span></button></th>
                            <th>
                                <div class="finance-status-filter-group is-heading" data-finance-status-filter="entrada" data-value="">
                                    <button type="button" class="finance-status-filter-button finance-status-filter-heading" data-finance-status-trigger aria-haspopup="listbox" aria-expanded="false" aria-label="Selecionar um ou mais status de entrada">
                                        <span>Status</span>
                                        <span class="icon" data-lucide="filter"></span>
                                        <span data-finance-status-current class="finance-status-filter-current finance-status-filter-count finance-status-badge finance-status-neutral" hidden>Todos</span>
                                    </button>
                                    <div class="finance-status-filter-menu" data-finance-status-menu role="listbox" aria-multiselectable="true" hidden>
                                        <button type="button" class="finance-status-filter-option active" data-finance-status-option value="" data-label="Todos" data-status-class="finance-status-neutral"><span class="finance-status-badge finance-status-neutral">Todos</span></button>
                                        <button type="button" class="finance-status-filter-option" data-finance-status-option value="em_dia" data-label="Em dia" data-status-class="finance-status-success"><span class="finance-status-badge finance-status-success">Em dia</span></button>
                                        <button type="button" class="finance-status-filter-option" data-finance-status-option value="a_vencer" data-label="A vencer" data-status-class="finance-status-warning"><span class="finance-status-badge finance-status-warning">A vencer</span></button>
                                        <button type="button" class="finance-status-filter-option" data-finance-status-option value="vencido" data-label="Vencido" data-status-class="finance-status-danger"><span class="finance-status-badge finance-status-danger">Vencido</span></button>
                                        <button type="button" class="finance-status-filter-option" data-finance-status-option value="pago" data-label="Pago" data-status-class="finance-status-paid"><span class="finance-status-badge finance-status-paid">Pago</span></button>
                                    </div>
                                </div>
                            </th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody data-finance-tbody="entrada">
                        <?php if ($entradas): ?>
                            <?php foreach ($entradas as $entrada): ?>
                                <tr>
                                    <td><?= htmlspecialchars(financeiro_format_date($entrada['data_lancamento'] ?? null), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($entrada['numero_titulo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($entrada['nome_empresa'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>R$ <?= number_format((float)($entrada['valor'] ?? 0), 2, ',', '.'); ?></td>
                                    <td><?= htmlspecialchars(financeiro_format_date($entrada['validade'] ?? null), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="finance-status-badge <?= htmlspecialchars($entrada['status_vencimento_class'] ?? 'finance-status-success', ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($entrada['status_vencimento_label'] ?? 'Em dia', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td>
                                        <?php if (($entrada['status_vencimento'] ?? '') === 'pago'): ?>
                                            <button type="button" class="finance-paid-button is-paid" disabled>Pago</button>
                                        <?php else: ?>
                                            <form method="post" action="<?= htmlspecialchars($url('financeiro/lancamentos/'.((int)$entrada['id']).'/pago'), ENT_QUOTES, 'UTF-8'); ?>" class="finance-paid-form" data-finance-paid-form>
                                                <input type="hidden" name="tipo" value="entrada">
                                                <button type="submit" class="finance-paid-button">Pago</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">Nenhuma entrada cadastrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="finance-list-pagination" data-finance-pagination="entrada" aria-label="Paginação da lista de entrada"></div>
        </div>
    </section>
</section>

<section class="finance-tab-panel" data-finance-panel="saida" aria-labelledby="tab-saida">
    <section class="panel">
        <div class="finance-card-header">
            <div>
                <h2>Saída</h2>
                <p class="panel-description finance-form-note">Cadastre saídas e consulte a lista dentro do mesmo card.</p>
            </div>
            <div class="finance-period-card is-hidden" data-finance-period-card="saida">
                <div class="finance-period-top">
                    <span class="finance-period-label"><span class="icon" data-lucide="calendar-clock"></span><span>Valor lançado</span></span>
                    <strong class="finance-period-total" data-finance-period-total="saida">R$ 0,00</strong>
                </div>
                <div class="finance-period-controls" aria-label="Filtro de valor lançado de saída">
                    <select data-finance-period-select="saida" aria-label="Período do valor de saída">
                        <option value="diario">Diário</option>
                        <option value="semanal">Semanal</option>
                        <option value="mensal" selected>Mensal</option>
                        <option value="anual">Anual</option>
                    </select>
                    <input type="date" data-finance-period-input="saida" data-period-input="diario" value="<?= htmlspecialchars($hoje, ENT_QUOTES, 'UTF-8'); ?>" hidden>
                    <input type="week" data-finance-period-input="saida" data-period-input="semanal" hidden>
                    <input type="month" data-finance-period-input="saida" data-period-input="mensal" value="<?= htmlspecialchars(date('Y-m'), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="number" data-finance-period-input="saida" data-period-input="anual" min="2000" max="2100" value="<?= htmlspecialchars(date('Y'), ENT_QUOTES, 'UTF-8'); ?>" hidden>
                </div>
                <div class="finance-period-footer">
                    <strong class="finance-period-focus" data-finance-period-focus="saida"><?= htmlspecialchars(strtoupper(strftime('%B/%y')), ENT_QUOTES, 'UTF-8'); ?></strong>
                    <small class="finance-period-caption" data-finance-period-caption="saida"></small>
                </div>
            </div>
        </div>

        <div class="finance-card-tabs" role="tablist" aria-label="Saída">
            <button type="button" class="finance-card-tab-button active" id="tab-saida-lancamento" data-finance-subtab="saida" data-finance-subpanel="lancamento" aria-selected="true"><span class="icon" data-lucide="plus-circle"></span><span>Lançamento</span></button>
            <button type="button" class="finance-card-tab-button" id="tab-saida-lista" data-finance-subtab="saida" data-finance-subpanel="lista" aria-selected="false"><span class="icon" data-lucide="list-checks"></span><span>Lista de saída</span></button>
        </div>

        <div class="finance-card-panel active" data-finance-card-panel="saida-lancamento" aria-labelledby="tab-saida-lancamento">
            <div class="finance-lancamento-heading">
                <h3><span class="icon" data-lucide="file-plus-2"></span><span>Novo lançamento de saída</span></h3>
                <p class="panel-description finance-form-note">Informe data, Nº do título, empresa e valor do lançamento.</p>
            </div>
            <form method="post" action="<?= htmlspecialchars($url('financeiro/lancamentos'), ENT_QUOTES, 'UTF-8'); ?>" class="form-grid finance-lancamento-form" data-finance-live-form>
                <input type="hidden" name="tipo" value="saida">
                <label class="finance-field-date">
                    <span class="finance-field-label"><span class="icon" data-lucide="calendar-days"></span><span>Data</span></span>
                    <input type="date" name="data_entrada" value="<?= htmlspecialchars($hoje, ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>
                <label class="finance-field-title">
                    <span class="finance-field-label"><span class="icon" data-lucide="hash"></span><span>Nº do título</span></span>
                    <input type="text" name="numero_titulo" required maxlength="8" inputmode="numeric" pattern="[0-9]{1,8}" placeholder="00000001">
                </label>
                <label class="finance-field-company">
                    <span class="finance-field-label"><span class="icon" data-lucide="building-2"></span><span>Nome da empresa</span></span>
                    <input type="text" name="nome_empresa" required placeholder="Empresa responsável">
                </label>
                <label class="finance-field-value">
                    <span class="finance-field-label"><span class="icon" data-lucide="circle-dollar-sign"></span><span>Valor</span></span>
                    <input type="number" step="0.01" min="0.01" max="9999999999.99" name="valor" required placeholder="0,00">
                </label>
                <label class="finance-field-validity">
                    <span class="finance-field-label"><span class="icon" data-lucide="calendar-check"></span><span>Validade</span></span>
                    <input type="date" name="validade" required>
                </label>
                <input type="hidden" name="data_lancamento" value="<?= htmlspecialchars($hoje, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="form-actions">
                    <button class="button button-primary" type="submit">Salvar saída</button>
                </div>
            </form>
        </div>

        <div class="finance-card-panel" data-finance-card-panel="saida-lista" aria-labelledby="tab-saida-lista">
            <div class="finance-list-heading">
                <div>
                    <h3 class="finance-list-title"><span class="finance-list-title-icon" aria-hidden="true"><span class="icon" data-lucide="list-checks"></span><span class="icon" data-lucide="arrow-up-circle"></span></span><span>Lista de saída</span></h3>
                    <p class="panel-description">Saídas cadastradas no módulo financeiro.</p>
                    <label class="finance-list-search">
                        <span class="icon" data-lucide="search"></span>
                        <input type="search" data-finance-list-search="saida" placeholder="Buscar por titulo, data, empresa, valor ou validade" aria-label="Buscar na lista de saida">
                    </label>
                </div>
                <div class="finance-list-side">
                    <div class="finance-list-actions" aria-label="Ações da lista de saídas">
                        <button type="button" class="finance-list-action-button" data-finance-list-action="print" data-finance-list-type="saida"><span class="icon" data-lucide="printer"></span><span>Imprimir</span></button>
                        <div class="finance-export-group" data-finance-export-group>
                            <button type="button" class="finance-list-action-button" data-finance-export-trigger aria-haspopup="true" aria-expanded="false"><span class="icon" data-lucide="download"></span><span>Exportar</span></button>
                            <div class="finance-export-menu" data-finance-export-menu hidden>
                                <button type="button" class="finance-list-action-button" data-finance-list-action="csv" data-finance-list-type="saida"><span class="icon" data-lucide="file-text"></span><span>CSV</span></button>
                                <button type="button" class="finance-list-action-button" data-finance-list-action="txt" data-finance-list-type="saida"><span class="icon" data-lucide="file-text"></span><span>TXT</span></button>
                                <button type="button" class="finance-list-action-button" data-finance-list-action="xls" data-finance-list-type="saida"><span class="icon" data-lucide="file-text"></span><span>XLS</span></button>
                            </div>
                        </div>
                    </div>
                    <div class="finance-status-summary" data-finance-status-summary="saida" aria-label="Resumo de status das saídas"></div>
                </div>
            </div>
            <div class="finance-table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><button type="button" class="finance-sort-link" data-finance-sort-button="saida" data-finance-sort-field="data" aria-label="Ordenar saídas por data" title="Clique para ordenar a data"><span class="finance-sort-label">Data</span><span class="finance-sort-indicator" aria-hidden="true">⇅</span></button></th>
                            <th><button type="button" class="finance-sort-link" data-finance-sort-button="saida" data-finance-sort-field="numero_titulo" aria-label="Ordenar saídas por número do título" title="Clique para ordenar o Nº do título"><span class="finance-sort-label">Nº do título</span><span class="finance-sort-indicator" aria-hidden="true">⇅</span></button></th>
                            <th><button type="button" class="finance-sort-link" data-finance-sort-button="saida" data-finance-sort-field="nome_empresa" aria-label="Ordenar saídas por empresa" title="Clique para ordenar a empresa"><span class="finance-sort-label">Empresa</span><span class="finance-sort-indicator" aria-hidden="true">⇅</span></button></th>
                            <th><button type="button" class="finance-sort-link" data-finance-sort-button="saida" data-finance-sort-field="valor" aria-label="Ordenar saídas por valor" title="Clique para ordenar o valor"><span class="finance-sort-label">Valor</span><span class="finance-sort-indicator" aria-hidden="true">⇅</span></button></th>
                            <th><button type="button" class="finance-sort-link" data-finance-sort-button="saida" data-finance-sort-field="validade" aria-label="Ordenar saídas por validade" title="Clique para ordenar a validade"><span class="finance-sort-label">Validade</span><span class="finance-sort-indicator" aria-hidden="true">⇅</span></button></th>
                            <th>
                                <div class="finance-status-filter-group is-heading" data-finance-status-filter="saida" data-value="">
                                    <button type="button" class="finance-status-filter-button finance-status-filter-heading" data-finance-status-trigger aria-haspopup="listbox" aria-expanded="false" aria-label="Selecionar um ou mais status de saída">
                                        <span>Status</span>
                                        <span class="icon" data-lucide="filter"></span>
                                        <span data-finance-status-current class="finance-status-filter-current finance-status-filter-count finance-status-badge finance-status-neutral" hidden>Todos</span>
                                    </button>
                                    <div class="finance-status-filter-menu" data-finance-status-menu role="listbox" aria-multiselectable="true" hidden>
                                        <button type="button" class="finance-status-filter-option active" data-finance-status-option value="" data-label="Todos" data-status-class="finance-status-neutral"><span class="finance-status-badge finance-status-neutral">Todos</span></button>
                                        <button type="button" class="finance-status-filter-option" data-finance-status-option value="em_dia" data-label="Em dia" data-status-class="finance-status-success"><span class="finance-status-badge finance-status-success">Em dia</span></button>
                                        <button type="button" class="finance-status-filter-option" data-finance-status-option value="a_vencer" data-label="A vencer" data-status-class="finance-status-warning"><span class="finance-status-badge finance-status-warning">A vencer</span></button>
                                        <button type="button" class="finance-status-filter-option" data-finance-status-option value="vencido" data-label="Vencido" data-status-class="finance-status-danger"><span class="finance-status-badge finance-status-danger">Vencido</span></button>
                                        <button type="button" class="finance-status-filter-option" data-finance-status-option value="pago" data-label="Pago" data-status-class="finance-status-paid"><span class="finance-status-badge finance-status-paid">Pago</span></button>
                                    </div>
                                </div>
                            </th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody data-finance-tbody="saida">
                        <?php if ($saidas): ?>
                            <?php foreach ($saidas as $saida): ?>
                                <tr>
                                    <td><?= htmlspecialchars(financeiro_format_date($saida['data_entrada'] ?? null), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($saida['numero_titulo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($saida['nome_empresa'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>R$ <?= number_format((float)($saida['valor'] ?? 0), 2, ',', '.'); ?></td>
                                    <td><?= htmlspecialchars(financeiro_format_date($saida['validade'] ?? null), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="finance-status-badge <?= htmlspecialchars($saida['status_vencimento_class'] ?? 'finance-status-success', ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($saida['status_vencimento_label'] ?? 'Em dia', ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td>
                                        <?php if (($saida['status_vencimento'] ?? '') === 'pago'): ?>
                                            <button type="button" class="finance-paid-button is-paid" disabled>Pago</button>
                                        <?php else: ?>
                                            <form method="post" action="<?= htmlspecialchars($url('financeiro/lancamentos/'.((int)$saida['id']).'/pago'), ENT_QUOTES, 'UTF-8'); ?>" class="finance-paid-form" data-finance-paid-form>
                                                <input type="hidden" name="tipo" value="saida">
                                                <button type="submit" class="finance-paid-button">Pago</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">Nenhuma saída cadastrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="finance-list-pagination" data-finance-pagination="saida" aria-label="Paginação da lista de saída"></div>
        </div>
    </section>
</section>

<section class="finance-tab-panel" data-finance-panel="resumo" aria-labelledby="tab-resumo">
    <section class="panel">
        <div class="finance-card-header">
            <div>
                <h2>Resumo</h2>
                <p class="panel-description finance-form-note">Compare entradas, saidas e saldo por dia, semana, mes ou ano.</p>
            </div>
            <div class="finance-period-card" data-finance-period-card="resumo">
                <div class="finance-period-top">
                    <span class="finance-period-label"><span class="icon" data-lucide="scale"></span><span>Entrada - saida</span></span>
                    <strong class="finance-period-total" data-finance-summary-total="saldo">R$ 0,00</strong>
                </div>
                <div class="finance-period-controls" aria-label="Filtro do resumo financeiro">
                    <select data-finance-period-select="resumo" aria-label="Periodo do resumo financeiro">
                        <option value="diario">Diario</option>
                        <option value="semanal">Semanal</option>
                        <option value="mensal" selected>Mensal</option>
                        <option value="anual">Anual</option>
                    </select>
                    <input type="date" data-finance-period-input="resumo" data-period-input="diario" value="<?= htmlspecialchars($hoje, ENT_QUOTES, 'UTF-8'); ?>" hidden>
                    <input type="week" data-finance-period-input="resumo" data-period-input="semanal" hidden>
                    <input type="month" data-finance-period-input="resumo" data-period-input="mensal" value="<?= htmlspecialchars(date('Y-m'), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="number" data-finance-period-input="resumo" data-period-input="anual" min="2000" max="2100" value="<?= htmlspecialchars(date('Y'), ENT_QUOTES, 'UTF-8'); ?>" hidden>
                </div>
                <div class="finance-period-footer">
                    <strong class="finance-period-focus" data-finance-period-focus="resumo"><?= htmlspecialchars(strtoupper(strftime('%B/%y')), ENT_QUOTES, 'UTF-8'); ?></strong>
                    <small class="finance-period-caption" data-finance-period-caption="resumo"></small>
                </div>
            </div>
            <div class="finance-card-tabs" role="tablist" aria-label="Resumo financeiro">
                <button type="button" class="finance-card-tab-button active" id="tab-resumo-geral" data-finance-summary-tab="geral" aria-selected="true"><span class="icon" data-lucide="layout-dashboard"></span><span>Visão geral</span></button>
                <button type="button" class="finance-card-tab-button" id="tab-resumo-desempenho" data-finance-summary-tab="desempenho" aria-selected="false"><span class="icon" data-lucide="activity"></span><span>Comparativo de desempenho</span></button>
            </div>
        </div>

        <div class="finance-summary-panel active" data-finance-summary-panel="geral" aria-labelledby="tab-resumo-geral">
        <div class="finance-summary-grid">
            <div class="finance-summary-metrics" aria-label="Indicadores do resumo financeiro">
                <div class="finance-summary-card is-entrada">
                    <span>Entrada</span>
                    <strong data-finance-summary-total="entrada">R$ 0,00</strong>
                    <small data-finance-summary-count="entrada">0 lancamentos</small>
                </div>
                <div class="finance-summary-card is-saida">
                    <span>Saida</span>
                    <strong data-finance-summary-total="saida">R$ 0,00</strong>
                    <small data-finance-summary-count="saida">0 lancamentos</small>
                </div>
                <div class="finance-summary-card is-saldo">
                    <span>Resultado</span>
                    <strong data-finance-summary-result>R$ 0,00</strong>
                    <small data-finance-summary-status>Sem movimento no periodo.</small>
                </div>
            </div>
            <div class="finance-summary-charts">
                <div class="finance-chart-panel has-chart-zoom">
                    <div class="finance-chart-header">
                        <div>
                            <h3>Comparativo</h3>
                            <p data-finance-wave-caption>Entradas e saidas no periodo.</p>
                        </div>
                        <div class="finance-chart-tools">
                            <div class="finance-chart-legend" aria-label="Legenda do grafico de ondas">
                                <span style="--legend-color:#10b981;">Entrada</span>
                                <span style="--legend-color:#ef4444;">Saida</span>
                            </div>
                            <div class="finance-chart-actions" aria-label="Zoom do grafico comparativo">
                                <button type="button" class="finance-chart-action" data-finance-chart-zoom="wave" data-finance-chart-action="out" title="Reduzir grafico" aria-label="Reduzir grafico"><span class="icon" data-lucide="minus"></span></button>
                                <button type="button" class="finance-chart-action" data-finance-chart-zoom="wave" data-finance-chart-action="reset" title="Restaurar zoom" aria-label="Restaurar zoom"><span class="icon" data-lucide="search"></span></button>
                                <button type="button" class="finance-chart-action" data-finance-chart-zoom="wave" data-finance-chart-action="in" title="Expandir grafico" aria-label="Expandir grafico"><span class="icon" data-lucide="plus"></span></button>
                            </div>
                        </div>
                    </div>
                    <canvas class="finance-chart-canvas" data-finance-wave-chart width="720" height="260" aria-label="Grafico de ondas comparando entrada e saida"></canvas>
                    <div class="finance-chart-tooltip" data-finance-wave-tooltip role="status" aria-live="polite"></div>
                </div>
                <div class="finance-chart-panel">
                    <div class="finance-chart-header">
                        <div>
                            <h3>Participacao</h3>
                            <p data-finance-pie-caption>Percentual entre entrada e saida.</p>
                        </div>
                    </div>
                    <canvas class="finance-chart-canvas is-pie" data-finance-pie-chart width="320" height="250" aria-label="Grafico de pizza percentual"></canvas>
                    <div class="finance-summary-note" data-finance-pie-note>0% entrada / 0% saida</div>
                </div>
            </div>
        </div>
        </div>

        <div class="finance-summary-panel" data-finance-summary-panel="desempenho" aria-labelledby="tab-resumo-desempenho">
            <div class="finance-performance-compare-card">
                <div class="finance-performance-top">
                    <label class="finance-performance-type">
                        <span class="icon" data-lucide="scale"></span>
                        <select data-finance-performance-type aria-label="Tipo do comparativo">
                            <option value="entrada">Entrada</option>
                            <option value="saida">Saida</option>
                        </select>
                    </label>
                    <strong class="finance-performance-caption" data-finance-performance-mode-caption>Compare periodos do mesmo tipo financeiro.</strong>
                </div>

                <div class="finance-performance-controls">
                    <div class="finance-performance-side" data-finance-compare-side="left">
                        <label class="finance-performance-field">
                            <span>Periodo A</span>
                            <select data-finance-compare-period="left" aria-label="Periodo A do comparativo">
                                <option value="diario">Diario</option>
                                <option value="semanal">Semanal</option>
                                <option value="mensal" selected>Mensal</option>
                                <option value="anual">Anual</option>
                            </select>
                        </label>
                        <label class="finance-performance-field">
                            <span>Data A</span>
                            <input type="date" data-finance-compare-input="left" data-period-input="diario" value="<?= htmlspecialchars($hoje, ENT_QUOTES, 'UTF-8'); ?>" hidden>
                            <input type="week" data-finance-compare-input="left" data-period-input="semanal" hidden>
                            <input type="month" data-finance-compare-input="left" data-period-input="mensal" value="<?= htmlspecialchars(date('Y-m'), ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="number" data-finance-compare-input="left" data-period-input="anual" min="2000" max="2100" value="<?= htmlspecialchars(date('Y'), ENT_QUOTES, 'UTF-8'); ?>" hidden>
                        </label>
                    </div>

                    <span class="finance-performance-x" aria-hidden="true">X</span>

                    <div class="finance-performance-side" data-finance-compare-side="right">
                        <label class="finance-performance-field">
                            <span>Periodo B</span>
                            <select data-finance-compare-period="right" aria-label="Periodo B do comparativo">
                                <option value="diario">Diario</option>
                                <option value="semanal">Semanal</option>
                                <option value="mensal" selected>Mensal</option>
                                <option value="anual">Anual</option>
                            </select>
                        </label>
                        <label class="finance-performance-field">
                            <span>Data B</span>
                            <input type="date" data-finance-compare-input="right" data-period-input="diario" value="<?= htmlspecialchars($hoje, ENT_QUOTES, 'UTF-8'); ?>" hidden>
                            <input type="week" data-finance-compare-input="right" data-period-input="semanal" hidden>
                            <input type="month" data-finance-compare-input="right" data-period-input="mensal" value="<?= htmlspecialchars(date('Y-m'), ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="number" data-finance-compare-input="right" data-period-input="anual" min="2000" max="2100" value="<?= htmlspecialchars(date('Y'), ENT_QUOTES, 'UTF-8'); ?>" hidden>
                        </label>
                    </div>
                </div>

                <div class="finance-performance-footer">
                    <strong class="finance-performance-focus" data-finance-performance-focus>MAIO/26 X MAIO/26</strong>
                    <span class="finance-performance-caption" data-finance-performance-caption>Desempenho mensal - 2026</span>
                </div>
            </div>

            <div class="finance-performance-kpis" aria-label="Indicadores de desempenho financeiro">
                <div class="finance-summary-card is-entrada">
                    <span data-finance-performance-label="left">Periodo A</span>
                    <strong data-finance-performance-total="left">R$ 0,00</strong>
                    <small data-finance-performance-detail="left">0 lancamentos</small>
                </div>
                <div class="finance-summary-card is-saida">
                    <span data-finance-performance-label="right">Periodo B</span>
                    <strong data-finance-performance-total="right">R$ 0,00</strong>
                    <small data-finance-performance-detail="right">0 lancamentos</small>
                </div>
                <div class="finance-summary-card is-saldo">
                    <span>Diferenca</span>
                    <strong data-finance-performance-result>R$ 0,00</strong>
                    <small data-finance-performance-status>Sem variacao entre os periodos.</small>
                </div>
            </div>

            <div class="finance-performance-charts">
                <div class="finance-chart-panel has-chart-zoom">
                    <div class="finance-chart-header">
                        <div>
                            <h3>Comparacao dos periodos</h3>
                            <p data-finance-performance-chart-caption>Valor total de cada recorte.</p>
                        </div>
                        <div class="finance-chart-tools">
                            <div class="finance-chart-legend" aria-label="Legenda do comparativo">
                                <span style="--legend-color:#10b981;" data-finance-performance-legend="left">Periodo A</span>
                                <span style="--legend-color:#ef4444;" data-finance-performance-legend="right">Periodo B</span>
                            </div>
                            <div class="finance-chart-actions" aria-label="Zoom do grafico de periodos">
                                <button type="button" class="finance-chart-action" data-finance-chart-zoom="performance" data-finance-chart-action="out" title="Reduzir grafico" aria-label="Reduzir grafico"><span class="icon" data-lucide="minus"></span></button>
                                <button type="button" class="finance-chart-action" data-finance-chart-zoom="performance" data-finance-chart-action="reset" title="Restaurar zoom" aria-label="Restaurar zoom"><span class="icon" data-lucide="search"></span></button>
                                <button type="button" class="finance-chart-action" data-finance-chart-zoom="performance" data-finance-chart-action="in" title="Expandir grafico" aria-label="Expandir grafico"><span class="icon" data-lucide="plus"></span></button>
                            </div>
                        </div>
                    </div>
                    <canvas class="finance-chart-canvas" data-finance-performance-chart="comparativo" width="520" height="260" aria-label="Grafico comparando dois periodos"></canvas>
                </div>
                <div class="finance-chart-panel">
                    <div class="finance-chart-header">
                        <div>
                            <h3>Participacao</h3>
                            <p data-finance-performance-pie-caption>Percentual de cada periodo no total comparado.</p>
                        </div>
                    </div>
                    <canvas class="finance-chart-canvas is-pie" data-finance-performance-chart="participacao" width="320" height="250" aria-label="Grafico percentual da comparacao"></canvas>
                    <div class="finance-summary-note" data-finance-performance-pie-note>0% periodo A / 0% periodo B</div>
                </div>
            </div>
        </div>
    </section>
</section>

<script>
    (function() {
        const buttons = document.querySelectorAll('[data-finance-tab]');
        const panels = document.querySelectorAll('[data-finance-panel]');
        const subButtons = document.querySelectorAll('[data-finance-subtab]');
        const subPanels = document.querySelectorAll('[data-finance-card-panel]');
        const summaryButtons = document.querySelectorAll('[data-finance-summary-tab]');
        const summaryPanels = document.querySelectorAll('[data-finance-summary-panel]');
        const messageBox = document.getElementById('finance-live-message');
        const listUrl = <?= json_encode($url('financeiro/titulos')); ?>;
        const lancamentosBaseUrl = <?= json_encode($url('financeiro/lancamentos')); ?>;
        const todayValue = <?= json_encode($hoje); ?>;
        const configuredPageSizeOptions = <?= json_encode(array_values(array_map('intval', $listPageSizeOptions ?? [15, 20, 30, 40, 50, 100]))); ?>;
        const configuredPageSizeDefaults = <?= json_encode($listPageSizeDefaults ?? ['entrada' => 15, 'saida' => 15]); ?>;
        const financeRows = {
            entrada: <?= json_encode(array_values($entradas), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>,
            saida: <?= json_encode(array_values($saidas), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>
        };
        const financeSortState = {
            entrada: { field: '', direction: '' },
            saida: { field: '', direction: '' }
        };
        const financePageSizeOptions = configuredPageSizeOptions.length ? configuredPageSizeOptions : [15, 20, 30, 40, 50, 100];
        const financePageSizeDefault = (tipo) => {
            const configured = Number(configuredPageSizeDefaults[tipo] || financePageSizeOptions[0] || 15);
            return Math.min(500, Math.max(1, configured));
        };
        const financePaginationState = {
            entrada: { page: 1, perPage: financePageSizeDefault('entrada') },
            saida: { page: 1, perPage: financePageSizeDefault('saida') }
        };
        const emptyMessages = {
            entrada: 'Nenhuma entrada cadastrada.',
            saida: 'Nenhuma saída cadastrada.'
        };
        const filteredEmptyMessages = {
            entrada: 'Nenhuma entrada encontrada com os filtros selecionados.',
            saida: 'Nenhuma saída encontrada com os filtros selecionados.'
        };
        const statusSummaryItems = [
            { key: 'vencido', label: 'Vencido', className: 'finance-status-danger' },
            { key: 'a_vencer', label: 'A vencer', className: 'finance-status-warning' },
            { key: 'em_dia', label: 'Em dia', className: 'finance-status-success' },
            { key: 'pago', label: 'Pago', className: 'finance-status-paid' }
        ];
        const periodLabels = {
            diario: 'Diário',
            semanal: 'Semanal',
            mensal: 'Mensal',
            anual: 'Anual'
        };

        const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#039;',
            '"': '&quot;'
        }[char]));

        const formatDate = (date) => {
            if (!date) {
                return '-';
            }
            const parts = String(date).split('-');
            if (parts.length !== 3) {
                return escapeHtml(date);
            }
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        };

        const formatCurrency = (value) => 'R$ ' + Number(value || 0).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        const showMessage = (message, type = 'success') => {
            if (!messageBox) {
                return;
            }
            messageBox.textContent = message;
            messageBox.className = `finance-live-message active ${type}`;
            window.clearTimeout(showMessage.timeout);
            showMessage.timeout = window.setTimeout(() => {
                messageBox.className = 'finance-live-message';
                messageBox.textContent = '';
            }, 4500);
        };

        const removeLeakedCodeText = () => {
            const suspiciousFragments = ['downloadFile(', 'printFinanceList', 'const handleFinanceListAction', 'window.setTimeout(() =>', 'const htmlParts'];
            const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
                acceptNode(node) {
                    const parent = node.parentElement;
                    if (!parent || ['SCRIPT', 'STYLE'].includes(parent.tagName)) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    const text = String(node.textContent || '').trim();
                    if (!text || text.length < 80) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    return suspiciousFragments.some((fragment) => text.includes(fragment))
                        ? NodeFilter.FILTER_ACCEPT
                        : NodeFilter.FILTER_REJECT;
                }
            });
            const leakedNodes = [];
            while (walker.nextNode()) {
                leakedNodes.push(walker.currentNode);
            }
            leakedNodes.forEach((node) => node.remove());
        };

        function activateSubtab(type, panel, updateHash) {
            const target = type + '-' + panel;

            subButtons.forEach((button) => {
                if (button.dataset.financeSubtab !== type) {
                    return;
                }

                const active = button.dataset.financeSubpanel === panel;
                button.classList.toggle('active', active);
                button.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            subPanels.forEach((subPanel) => {
                if (!subPanel.dataset.financeCardPanel.startsWith(type + '-')) {
                    return;
                }

                subPanel.classList.toggle('active', subPanel.dataset.financeCardPanel === target);
            });

            document.querySelector(`[data-finance-period-card="${type}"]`)?.classList.toggle('is-hidden', panel !== 'lista');
            document.querySelector(`[data-finance-panel="${type}"] > .panel`)?.classList.toggle('finance-list-active', panel === 'lista');

            if (updateHash && window.location.hash !== '#' + target) {
                history.replaceState(null, '', '#' + target);
            }
        }

        function activeSummaryPanel() {
            return document.querySelector('[data-finance-summary-panel].active')?.dataset.financeSummaryPanel || 'geral';
        }

        function renderActiveSummaryPanel() {
            if (activeSummaryPanel() === 'desempenho') {
                renderPerformanceDashboard();
                return;
            }
            renderSummaryDashboard();
        }

        function activateSummaryPanel(panel, updateHash) {
            const target = panel === 'desempenho' ? 'desempenho' : 'geral';
            summaryButtons.forEach((button) => {
                const active = button.dataset.financeSummaryTab === target;
                button.classList.toggle('active', active);
                button.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            summaryPanels.forEach((summaryPanel) => {
                summaryPanel.classList.toggle('active', summaryPanel.dataset.financeSummaryPanel === target);
            });
            document.querySelector('[data-finance-period-card="resumo"]')?.classList.toggle('is-hidden', target !== 'geral');
            renderActiveSummaryPanel();

            if (updateHash) {
                const hash = target === 'desempenho' ? 'resumo-desempenho' : 'resumo';
                if (window.location.hash !== '#' + hash) {
                    history.replaceState(null, '', '#' + hash);
                }
            }
        }

        function activate(tab, subpanel, updateHash) {
            removeLeakedCodeText();
            buttons.forEach((button) => {
                const active = button.dataset.financeTab === tab;
                button.classList.toggle('active', active);
                button.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            panels.forEach((panel) => {
                panel.classList.toggle('active', panel.dataset.financePanel === tab);
            });

            if (tab === 'resumo') {
                activateSummaryPanel(subpanel || activeSummaryPanel(), updateHash);
                return;
            }

            activateSubtab(tab, subpanel || 'lancamento', false);

            if (updateHash) {
                const hash = subpanel && subpanel !== 'lancamento' ? tab + '-' + subpanel : tab;
                if (window.location.hash !== '#' + hash) {
                    history.replaceState(null, '', '#' + hash);
                }
            }
        }

        const paidAction = (item) => `${lancamentosBaseUrl}/${encodeURIComponent(item.id)}/pago`;

        const statusBadge = (item) => `<span class="finance-status-badge ${escapeHtml(item.status_vencimento_class || 'finance-status-success')}">${escapeHtml(item.status_vencimento_label || 'Em dia')}</span>`;

        const paidButton = (item, tipo) => {
            if ((item.status_vencimento || '') === 'pago') {
                return '<button type="button" class="finance-paid-button is-paid" disabled>Pago</button>';
            }
            return `<form method="post" action="${paidAction(item)}" class="finance-paid-form" data-finance-paid-form>
                <input type="hidden" name="tipo" value="${tipo}">
                <button type="submit" class="finance-paid-button">Pago</button>
            </form>`;
        };

        const getFilterState = (tipo) => {
            const statusValues = (document.querySelector(`[data-finance-status-filter="${tipo}"]`)?.dataset.value || '')
                .split(',')
                .map((value) => value.trim())
                .filter(Boolean);
            const sort = financeSortState[tipo] || { field: '', direction: '' };
            const search = (document.querySelector(`[data-finance-list-search="${tipo}"]`)?.value || '').trim();

            return {
                statusValues,
                search,
                sortField: sort.field || '',
                sortDirection: sort.direction || ''
            };
        };

        const refreshSortIndicators = (tipo) => {
            const sort = financeSortState[tipo] || { field: '', direction: '' };
            document.querySelectorAll(`[data-finance-sort-button="${tipo}"]`).forEach((button) => {
                const isActive = sort.field === button.dataset.financeSortField && Boolean(sort.direction);
                const indicator = button.querySelector('.finance-sort-indicator');
                button.dataset.direction = isActive ? sort.direction : '';
                button.setAttribute('aria-sort', isActive ? (sort.direction === 'asc' ? 'ascending' : 'descending') : 'none');

                if (indicator) {
                    indicator.textContent = isActive ? (sort.direction === 'asc' ? '▲' : '▼') : '⇅';
                    indicator.classList.toggle('active', isActive);
                }
            });
        };

        const numberFromTitle = (value) => {
            const digits = String(value || '').replace(/\D/g, '');
            return digits ? Number(digits) : 0;
        };

        const numberFromValue = (value) => {
            const raw = String(value ?? '').replace(/R\$|\s/g, '');
            const normalized = raw.includes(',')
                ? raw.replace(/\./g, '').replace(',', '.')
                : raw;
            const parsed = Number(normalized);
            return Number.isFinite(parsed) ? parsed : 0;
        };

        const dateValue = (value) => {
            if (!value) {
                return 0;
            }
            const timestamp = Date.parse(`${value}T00:00:00`);
            return Number.isFinite(timestamp) ? timestamp : 0;
        };

        const isoWeekValue = (dateString) => {
            if (!dateString) {
                return '';
            }
            const date = new Date(`${dateString}T00:00:00`);
            if (Number.isNaN(date.getTime())) {
                return '';
            }
            const utcDate = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
            const day = utcDate.getUTCDay() || 7;
            utcDate.setUTCDate(utcDate.getUTCDate() + 4 - day);
            const yearStart = new Date(Date.UTC(utcDate.getUTCFullYear(), 0, 1));
            const week = Math.ceil((((utcDate - yearStart) / 86400000) + 1) / 7);
            return `${utcDate.getUTCFullYear()}-W${String(week).padStart(2, '0')}`;
        };

        const itemDate = (item, tipo) => tipo === 'saida' ? item.data_entrada : item.data_lancamento;

        const sortValue = (item, field, tipo) => {
            if (field === 'valor') {
                return numberFromValue(item.valor);
            }
            if (field === 'numero_titulo') {
                return numberFromTitle(item.numero_titulo);
            }
            if (field === 'data') {
                return dateValue(tipo === 'saida' ? item.data_entrada : item.data_lancamento);
            }
            if (field === 'validade') {
                return dateValue(item.validade);
            }
            if (field === 'nome_empresa') {
                return String(item.nome_empresa || '').toLocaleLowerCase('pt-BR');
            }
            return String(item[field] || '').toLocaleLowerCase('pt-BR');
        };

        const normalizeSearchText = (value) => String(value ?? '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLocaleLowerCase('pt-BR');

        const financeSearchText = (item, tipo) => [
            item.numero_titulo || '',
            formatDate(itemDate(item, tipo)),
            itemDate(item, tipo) || '',
            item.nome_empresa || '',
            formatCurrency(item.valor),
            String(item.valor ?? ''),
            formatDate(item.validade),
            item.validade || ''
        ].map(normalizeSearchText).join(' ');

        const getPeriodState = (tipo) => {
            const select = document.querySelector(`[data-finance-period-select="${tipo}"]`);
            const period = select?.value || 'mensal';
            const input = document.querySelector(`[data-finance-period-input="${tipo}"][data-period-input="${period}"]`);
            const fallbackValue = period === 'anual' ? todayValue.slice(0, 4) : (period === 'mensal' ? todayValue.slice(0, 7) : todayValue);
            return {
                period,
                value: input?.value || fallbackValue
            };
        };

        const periodRows = (tipo) => {
            const periodState = getPeriodState(tipo);
            return (financeRows[tipo] || []).filter((item) => matchesPeriod(item, tipo, periodState.period, periodState.value));
        };

        const addDays = (date, days) => {
            const next = new Date(date);
            next.setDate(next.getDate() + days);
            return next;
        };

        const isoDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        const startOfIsoWeek = (weekValue) => {
            const match = String(weekValue || '').match(/^(\d{4})-W(\d{2})$/);
            if (!match) {
                return new Date(`${todayValue}T00:00:00`);
            }
            const year = Number(match[1]);
            const week = Number(match[2]);
            const simple = new Date(year, 0, 1 + (week - 1) * 7);
            const day = simple.getDay() || 7;
            return addDays(simple, 1 - day);
        };

        const summarySeriesBuckets = (period, value) => {
            const buckets = [];
            if (period === 'diario') {
                const selected = new Date(`${value || todayValue}T00:00:00`);
                for (let index = 6; index >= 0; index -= 1) {
                    const date = addDays(selected, -index);
                    buckets.push({
                        key: isoDate(date),
                        label: date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' }),
                        tooltipLabel: date.toLocaleDateString('pt-BR')
                    });
                }
                return buckets;
            }
            if (period === 'semanal') {
                const start = startOfIsoWeek(value);
                for (let index = 0; index < 7; index += 1) {
                    const date = addDays(start, index);
                    buckets.push({
                        key: isoDate(date),
                        label: date.toLocaleDateString('pt-BR', { weekday: 'short' }).replace('.', ''),
                        tooltipLabel: date.toLocaleDateString('pt-BR', { weekday: 'long', day: '2-digit', month: '2-digit', year: 'numeric' })
                    });
                }
                return buckets;
            }
            if (period === 'mensal') {
                const base = monthYearDate(value) || new Date();
                const days = new Date(base.getFullYear(), base.getMonth() + 1, 0).getDate();
                for (let day = 1; day <= days; day += 1) {
                    const date = new Date(base.getFullYear(), base.getMonth(), day);
                    buckets.push({
                        key: isoDate(date),
                        label: String(day).padStart(2, '0'),
                        tooltipLabel: date.toLocaleDateString('pt-BR')
                    });
                }
                return buckets;
            }
            const year = Number(value || new Date().getFullYear());
            for (let month = 0; month < 12; month += 1) {
                const date = new Date(year, month, 1);
                buckets.push({
                    key: `${year}-${String(month + 1).padStart(2, '0')}`,
                    label: date.toLocaleDateString('pt-BR', { month: 'short' }).replace('.', ''),
                    tooltipLabel: date.toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' })
                });
            }
            return buckets;
        };

        const summaryRows = (tipo, period, value) => (financeRows[tipo] || [])
            .filter((item) => matchesPeriod(item, tipo, period, value));

        const sumRows = (rows) => rows.reduce((acc, item) => acc + numberFromValue(item.valor), 0);

        const setCanvasSize = (canvas) => {
            const rect = canvas.getBoundingClientRect();
            const ratio = window.devicePixelRatio || 1;
            const width = Math.max(1, Math.round(rect.width * ratio));
            const height = Math.max(1, Math.round(rect.height * ratio));
            if (canvas.width !== width || canvas.height !== height) {
                canvas.width = width;
                canvas.height = height;
            }
            const ctx = canvas.getContext('2d');
            ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
            return { ctx, width: rect.width, height: rect.height };
        };

        const chartZoomLevels = [0.75, 1, 1.25, 1.5, 2];
        const chartZoomState = { wave: 1, performance: 1 };
        const getChartZoom = (key) => chartZoomState[key] || 1;
        const getChartAxisMax = (maxValue, zoomKey) => Math.max(1, maxValue / getChartZoom(zoomKey));
        const valueToY = (value, axisMax, padding, chartHeight) => {
            const ratio = Math.min(1, Math.max(0, value / axisMax));
            return padding.top + chartHeight - ratio * chartHeight;
        };
        const drawValueGrid = (ctx, width, padding, chartHeight, axisMax) => {
            ctx.strokeStyle = 'rgba(100,116,139,0.18)';
            ctx.lineWidth = 1;
            ctx.fillStyle = '#64748b';
            ctx.font = '700 11px Arial, sans-serif';
            ctx.textAlign = 'left';
            ctx.textBaseline = 'middle';
            for (let index = 0; index <= 4; index += 1) {
                const value = axisMax - (axisMax / 4) * index;
                const y = padding.top + (chartHeight / 4) * index;
                ctx.beginPath();
                ctx.moveTo(padding.left, y);
                ctx.lineTo(width - padding.right, y);
                ctx.stroke();
                ctx.fillText(formatCurrency(value), 8, y);
            }
            ctx.textBaseline = 'alphabetic';
        };

        const bindWaveTooltip = (canvas, points) => {
            const tooltip = document.querySelector('[data-finance-wave-tooltip]');
            const panel = canvas.closest('.finance-chart-panel');
            if (!tooltip || !panel) {
                return;
            }
            canvas.dataset.tooltipBound = canvas.dataset.tooltipBound || '0';
            canvas._financeWavePoints = points;

            if (canvas.dataset.tooltipBound === '1') {
                return;
            }

            canvas.dataset.tooltipBound = '1';
            canvas.addEventListener('mousemove', (event) => {
                const rect = canvas.getBoundingClientRect();
                const x = event.clientX - rect.left;
                const y = event.clientY - rect.top;
                const activePoints = canvas._financeWavePoints || [];
                const nearest = activePoints.reduce((best, point) => {
                    const distance = Math.hypot(point.x - x, point.y - y);
                    return distance < best.distance ? { point, distance } : best;
                }, { point: null, distance: Number.POSITIVE_INFINITY });

                if (!nearest.point || nearest.distance > 12) {
                    tooltip.classList.remove('active');
                    return;
                }

                const point = nearest.point;
                tooltip.innerHTML = `<strong>${point.typeLabel} - ${point.tooltipLabel}</strong>
                    <span>Valor: ${formatCurrency(point.value)}</span>
                    <span>Valor liquido: ${formatCurrency(point.netValue)}</span>
                    <span>Media do periodo: ${formatCurrency(point.average)}</span>`;
                tooltip.classList.add('active');
                const panelRect = panel.getBoundingClientRect();
                const maxLeft = Math.max(8, panelRect.width - tooltip.offsetWidth - 8);
                const tooltipLeft = Math.min(Math.max(8, event.clientX - panelRect.left + 12), maxLeft);
                const tooltipTop = Math.max(8, event.clientY - panelRect.top - tooltip.offsetHeight - 12);
                tooltip.style.left = `${tooltipLeft}px`;
                tooltip.style.top = `${tooltipTop}px`;
            });

            canvas.addEventListener('mouseleave', () => {
                tooltip.classList.remove('active');
            });
        };

        const drawWaveChart = (series) => {
            const canvas = document.querySelector('[data-finance-wave-chart]');
            if (!canvas) {
                return;
            }
            const { ctx, width, height } = setCanvasSize(canvas);
            ctx.clearRect(0, 0, width, height);
            const padding = { top: 18, right: 18, bottom: 34, left: 54 };
            const chartWidth = width - padding.left - padding.right;
            const chartHeight = height - padding.top - padding.bottom;
            const maxValue = Math.max(1, ...series.map((item) => Math.max(item.entrada, item.saida)));
            const axisMax = getChartAxisMax(maxValue, 'wave');
            const averages = {
                entrada: series.reduce((acc, item) => acc + item.entrada, 0) / Math.max(1, series.length),
                saida: series.reduce((acc, item) => acc + item.saida, 0) / Math.max(1, series.length)
            };
            const hoverPoints = [];

            drawValueGrid(ctx, width, padding, chartHeight, axisMax);

            const point = (item, index, field) => {
                const x = padding.left + (series.length <= 1 ? chartWidth / 2 : (chartWidth / (series.length - 1)) * index);
                const y = valueToY(item[field], axisMax, padding, chartHeight);
                return { x, y };
            };

            const drawLine = (field, color) => {
                ctx.beginPath();
                series.forEach((item, index) => {
                    const current = point(item, index, field);
                    if (index === 0) {
                        ctx.moveTo(current.x, current.y);
                        return;
                    }
                    const previous = point(series[index - 1], index - 1, field);
                    const midX = (previous.x + current.x) / 2;
                    ctx.bezierCurveTo(midX, previous.y, midX, current.y, current.x, current.y);
                });
                ctx.strokeStyle = color;
                ctx.lineWidth = 3;
                ctx.stroke();

                series.forEach((item, index) => {
                    const current = point(item, index, field);
                    hoverPoints.push({
                        x: current.x,
                        y: current.y,
                        label: item.label,
                        tooltipLabel: item.tooltipLabel || item.label,
                        typeLabel: field === 'entrada' ? 'Entrada' : 'Saida',
                        value: item[field],
                        netValue: item.entrada - item.saida,
                        average: averages[field]
                    });
                    ctx.beginPath();
                    ctx.arc(current.x, current.y, 3.5, 0, Math.PI * 2);
                    ctx.fillStyle = color;
                    ctx.fill();
                });
            };

            drawLine('entrada', '#10b981');
            drawLine('saida', '#ef4444');

            ctx.fillStyle = '#64748b';
            ctx.font = '700 11px Arial, sans-serif';
            ctx.textAlign = 'center';
            const labelStep = Math.max(1, Math.ceil(series.length / 8));
            series.forEach((item, index) => {
                if (index % labelStep !== 0 && index !== series.length - 1) {
                    return;
                }
                const x = padding.left + (series.length <= 1 ? chartWidth / 2 : (chartWidth / (series.length - 1)) * index);
                ctx.fillText(item.label, x, height - 12);
            });
            bindWaveTooltip(canvas, hoverPoints);
        };

        const drawPieChart = (entradaTotal, saidaTotal) => {
            const canvas = document.querySelector('[data-finance-pie-chart]');
            if (!canvas) {
                return;
            }
            const { ctx, width, height } = setCanvasSize(canvas);
            ctx.clearRect(0, 0, width, height);
            const total = entradaTotal + saidaTotal;
            const radius = Math.max(54, Math.min(width, height) / 2 - 22);
            const centerX = width / 2;
            const centerY = height / 2;

            if (total <= 0) {
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
                ctx.strokeStyle = 'rgba(100,116,139,0.25)';
                ctx.lineWidth = 24;
                ctx.stroke();
                ctx.fillStyle = '#64748b';
                ctx.font = '800 14px Arial, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Sem dados', centerX, centerY + 5);
                return;
            }

            const entradaAngle = (entradaTotal / total) * Math.PI * 2;
            const drawSlice = (start, end, color) => {
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, start, end);
                ctx.closePath();
                ctx.fillStyle = color;
                ctx.fill();
            };

            drawSlice(-Math.PI / 2, -Math.PI / 2 + entradaAngle, '#10b981');
            drawSlice(-Math.PI / 2 + entradaAngle, Math.PI * 1.5, '#ef4444');
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius * 0.58, 0, Math.PI * 2);
            ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--surface') || '#fff';
            ctx.fill();

            const entradaPercent = Math.round((entradaTotal / total) * 100);
            ctx.fillStyle = '#111827';
            ctx.font = '900 24px Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(`${entradaPercent}%`, centerX, centerY - 2);
            ctx.fillStyle = '#64748b';
            ctx.font = '800 11px Arial, sans-serif';
            ctx.fillText('entrada', centerX, centerY + 18);
        };

        const renderSummaryDashboard = () => {
            const { period, value } = getPeriodState('resumo');
            const inputs = Array.from(document.querySelectorAll('[data-finance-period-input="resumo"]'));
            inputs.forEach((input) => {
                input.hidden = input.dataset.periodInput !== period;
            });

            const entradasResumo = summaryRows('entrada', period, value);
            const saidasResumo = summaryRows('saida', period, value);
            const entradaTotal = sumRows(entradasResumo);
            const saidaTotal = sumRows(saidasResumo);
            const saldo = entradaTotal - saidaTotal;
            const totalMovimento = entradaTotal + saidaTotal;
            const entradaPercent = totalMovimento > 0 ? Math.round((entradaTotal / totalMovimento) * 100) : 0;
            const saidaPercent = totalMovimento > 0 ? 100 - entradaPercent : 0;

            document.querySelectorAll('[data-finance-summary-total="entrada"]').forEach((target) => { target.textContent = formatCurrency(entradaTotal); });
            document.querySelectorAll('[data-finance-summary-total="saida"]').forEach((target) => { target.textContent = formatCurrency(saidaTotal); });
            document.querySelectorAll('[data-finance-summary-total="saldo"]').forEach((target) => { target.textContent = formatCurrency(saldo); });
            document.querySelector('[data-finance-summary-count="entrada"]').textContent = `${entradasResumo.length} ${entradasResumo.length === 1 ? 'lancamento' : 'lancamentos'}`;
            document.querySelector('[data-finance-summary-count="saida"]').textContent = `${saidasResumo.length} ${saidasResumo.length === 1 ? 'lancamento' : 'lancamentos'}`;

            const result = document.querySelector('[data-finance-summary-result]');
            if (result) {
                result.textContent = formatCurrency(saldo);
                result.classList.toggle('is-positive', saldo >= 0);
                result.classList.toggle('is-negative', saldo < 0);
            }

            const status = document.querySelector('[data-finance-summary-status]');
            if (status) {
                status.textContent = saldo > 0
                    ? 'Entradas acima das saidas no periodo.'
                    : (saldo < 0 ? 'Saidas acima das entradas no periodo.' : 'Entradas e saidas equilibradas no periodo.');
            }

            const focus = document.querySelector('[data-finance-period-focus="resumo"]');
            const caption = document.querySelector('[data-finance-period-caption="resumo"]');
            if (focus) {
                focus.textContent = periodFocusLabel(period, value);
            }
            if (caption) {
                caption.textContent = `${periodLabels[period] || 'Periodo'} - ${periodDisplayLabel(period, value)}`;
            }

            const buckets = summarySeriesBuckets(period, value);
            const series = buckets.map((bucket) => {
                const entrada = (financeRows.entrada || [])
                    .filter((item) => {
                        const date = itemDate(item, 'entrada');
                        return period === 'anual' ? String(date).startsWith(bucket.key) : date === bucket.key;
                    })
                    .reduce((acc, item) => acc + numberFromValue(item.valor), 0);
                const saida = (financeRows.saida || [])
                    .filter((item) => {
                        const date = itemDate(item, 'saida');
                        return period === 'anual' ? String(date).startsWith(bucket.key) : date === bucket.key;
                    })
                    .reduce((acc, item) => acc + numberFromValue(item.valor), 0);
                return { ...bucket, entrada, saida };
            });

            const waveCaption = document.querySelector('[data-finance-wave-caption]');
            if (waveCaption) {
                waveCaption.textContent = period === 'diario'
                    ? 'Ultimos 7 dias ate a data selecionada.'
                    : `Comparacao ${String(periodLabels[period] || 'do periodo').toLocaleLowerCase('pt-BR')}.`;
            }
            const pieNote = document.querySelector('[data-finance-pie-note]');
            if (pieNote) {
                pieNote.textContent = `${entradaPercent}% entrada / ${saidaPercent}% saida`;
            }
            const pieCaption = document.querySelector('[data-finance-pie-caption]');
            if (pieCaption) {
                pieCaption.textContent = `${formatCurrency(entradaTotal)} entrada e ${formatCurrency(saidaTotal)} saida.`;
            }

            drawWaveChart(series);
            drawPieChart(entradaTotal, saidaTotal);
        };

        const getCompareState = (side) => {
            const period = document.querySelector(`[data-finance-compare-period="${side}"]`)?.value || 'mensal';
            const inputs = Array.from(document.querySelectorAll(`[data-finance-compare-input="${side}"]`));
            inputs.forEach((input) => {
                input.hidden = input.dataset.periodInput !== period;
            });
            const input = inputs.find((item) => item.dataset.periodInput === period);
            return { period, value: input?.value || '' };
        };

        const compareRows = (tipo, state) => (financeRows[tipo] || [])
            .filter((item) => matchesPeriod(item, tipo, state.period, state.value));

        const drawPerformanceBarChart = (left, right) => {
            const canvas = document.querySelector('[data-finance-performance-chart="comparativo"]');
            if (!canvas) {
                return;
            }
            const { ctx, width, height } = setCanvasSize(canvas);
            ctx.clearRect(0, 0, width, height);
            const padding = { top: 18, right: 18, bottom: 34, left: 54 };
            const chartWidth = width - padding.left - padding.right;
            const chartHeight = height - padding.top - padding.bottom;
            const maxValue = Math.max(1, left.total, right.total);
            const axisMax = getChartAxisMax(maxValue, 'performance');
            const bars = [
                { ...left, color: '#10b981' },
                { ...right, color: '#ef4444' }
            ];

            drawValueGrid(ctx, width, padding, chartHeight, axisMax);

            const barWidth = Math.min(130, chartWidth / 4);
            bars.forEach((bar, index) => {
                const x = padding.left + chartWidth * (index === 0 ? 0.28 : 0.72) - barWidth / 2;
                const barHeight = Math.min(chartHeight, (bar.total / axisMax) * chartHeight);
                const y = padding.top + chartHeight - barHeight;
                ctx.fillStyle = bar.color;
                ctx.fillRect(x, y, barWidth, Math.max(4, barHeight));

                ctx.fillStyle = '#111827';
                ctx.font = '900 13px Arial, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(formatCurrency(bar.total), x + barWidth / 2, Math.max(15, y - 8));
                ctx.fillStyle = '#64748b';
                ctx.font = '800 12px Arial, sans-serif';
                ctx.fillText(bar.shortLabel, x + barWidth / 2, height - 12);
            });
        };

        const drawPerformancePieChart = (leftTotal, rightTotal) => {
            const canvas = document.querySelector('[data-finance-performance-chart="participacao"]');
            if (!canvas) {
                return;
            }
            const { ctx, width, height } = setCanvasSize(canvas);
            ctx.clearRect(0, 0, width, height);
            const total = leftTotal + rightTotal;
            const radius = Math.max(54, Math.min(width, height) / 2 - 22);
            const centerX = width / 2;
            const centerY = height / 2;

            if (total <= 0) {
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
                ctx.strokeStyle = 'rgba(100,116,139,0.25)';
                ctx.lineWidth = 24;
                ctx.stroke();
                ctx.fillStyle = '#64748b';
                ctx.font = '800 14px Arial, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Sem dados', centerX, centerY + 5);
                return;
            }

            const leftAngle = (leftTotal / total) * Math.PI * 2;
            const drawSlice = (start, end, color) => {
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, start, end);
                ctx.closePath();
                ctx.fillStyle = color;
                ctx.fill();
            };
            drawSlice(-Math.PI / 2, -Math.PI / 2 + leftAngle, '#10b981');
            drawSlice(-Math.PI / 2 + leftAngle, Math.PI * 1.5, '#ef4444');
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius * 0.58, 0, Math.PI * 2);
            ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--surface') || '#fff';
            ctx.fill();

            const percent = Math.round((leftTotal / total) * 100);
            ctx.fillStyle = '#111827';
            ctx.font = '900 24px Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(`${percent}%`, centerX, centerY - 2);
            ctx.fillStyle = '#64748b';
            ctx.font = '800 11px Arial, sans-serif';
            ctx.fillText('periodo A', centerX, centerY + 18);
        };

        const renderPerformanceDashboard = () => {
            const tipo = document.querySelector('[data-finance-performance-type]')?.value === 'saida' ? 'saida' : 'entrada';
            const leftState = getCompareState('left');
            const rightState = getCompareState('right');
            const leftRows = compareRows(tipo, leftState);
            const rightRows = compareRows(tipo, rightState);
            const leftTotal = sumRows(leftRows);
            const rightTotal = sumRows(rightRows);
            const difference = leftTotal - rightTotal;
            const variation = rightTotal > 0 ? (difference / rightTotal) * 100 : (leftTotal > 0 ? 100 : 0);
            const typeLabel = tipo === 'saida' ? 'Saida' : 'Entrada';
            const leftLabel = periodDisplayLabel(leftState.period, leftState.value);
            const rightLabel = periodDisplayLabel(rightState.period, rightState.value);
            const leftShort = periodFocusLabel(leftState.period, leftState.value);
            const rightShort = periodFocusLabel(rightState.period, rightState.value);
            const leftPercent = leftTotal + rightTotal > 0 ? Math.round((leftTotal / (leftTotal + rightTotal)) * 100) : 0;
            const rightPercent = leftTotal + rightTotal > 0 ? 100 - leftPercent : 0;

            const leftTarget = document.querySelector('[data-finance-performance-total="left"]');
            const rightTarget = document.querySelector('[data-finance-performance-total="right"]');
            const resultTarget = document.querySelector('[data-finance-performance-result]');
            const leftDetail = document.querySelector('[data-finance-performance-detail="left"]');
            const rightDetail = document.querySelector('[data-finance-performance-detail="right"]');
            const status = document.querySelector('[data-finance-performance-status]');
            const leftTitle = document.querySelector('[data-finance-performance-label="left"]');
            const rightTitle = document.querySelector('[data-finance-performance-label="right"]');
            const focus = document.querySelector('[data-finance-period-focus="resumo"]');
            const caption = document.querySelector('[data-finance-period-caption="resumo"]');
            const performanceFocus = document.querySelector('[data-finance-performance-focus]');
            const performanceCaption = document.querySelector('[data-finance-performance-caption]');
            const modeCaption = document.querySelector('[data-finance-performance-mode-caption]');
            const chartCaption = document.querySelector('[data-finance-performance-chart-caption]');
            const pieCaption = document.querySelector('[data-finance-performance-pie-caption]');
            const pieNote = document.querySelector('[data-finance-performance-pie-note]');
            const leftLegend = document.querySelector('[data-finance-performance-legend="left"]');
            const rightLegend = document.querySelector('[data-finance-performance-legend="right"]');

            if (leftTarget) {
                leftTarget.textContent = formatCurrency(leftTotal);
            }
            if (rightTarget) {
                rightTarget.textContent = formatCurrency(rightTotal);
            }
            document.querySelectorAll('[data-finance-summary-total="saldo"]').forEach((target) => { target.textContent = formatCurrency(difference); });
            if (resultTarget) {
                resultTarget.textContent = formatCurrency(difference);
                resultTarget.classList.toggle('is-positive', difference >= 0);
                resultTarget.classList.toggle('is-negative', difference < 0);
            }
            if (leftTitle) {
                leftTitle.textContent = `${typeLabel} - ${leftShort}`;
            }
            if (rightTitle) {
                rightTitle.textContent = `${typeLabel} - ${rightShort}`;
            }
            if (leftDetail) {
                leftDetail.textContent = `${leftRows.length} ${leftRows.length === 1 ? 'lancamento' : 'lancamentos'} - ${leftLabel}`;
            }
            if (rightDetail) {
                rightDetail.textContent = `${rightRows.length} ${rightRows.length === 1 ? 'lancamento' : 'lancamentos'} - ${rightLabel}`;
            }
            if (status) {
                status.textContent = difference > 0
                    ? `${leftShort} ficou ${Math.abs(variation).toLocaleString('pt-BR', { maximumFractionDigits: 1 })}% acima de ${rightShort}.`
                    : (difference < 0 ? `${leftShort} ficou ${Math.abs(variation).toLocaleString('pt-BR', { maximumFractionDigits: 1 })}% abaixo de ${rightShort}.` : 'Sem variacao entre os periodos.');
            }
            if (focus) {
                focus.textContent = `${leftShort} X ${rightShort}`;
            }
            if (caption) {
                caption.textContent = `${typeLabel} - ${periodLabels[leftState.period] || 'Periodo'} x ${periodLabels[rightState.period] || 'Periodo'}`;
            }
            if (performanceFocus) {
                performanceFocus.textContent = `${leftShort} X ${rightShort}`;
            }
            if (performanceCaption) {
                performanceCaption.textContent = `${typeLabel} - ${periodLabels[leftState.period] || 'Periodo'} x ${periodLabels[rightState.period] || 'Periodo'}`;
            }
            if (modeCaption) {
                modeCaption.textContent = `Comparando ${String(typeLabel).toLocaleLowerCase('pt-BR')} entre dois periodos.`;
            }
            if (chartCaption) {
                chartCaption.textContent = `${leftLabel} contra ${rightLabel}.`;
            }
            if (pieCaption) {
                pieCaption.textContent = `${formatCurrency(leftTotal)} em ${leftShort} e ${formatCurrency(rightTotal)} em ${rightShort}.`;
            }
            if (pieNote) {
                pieNote.textContent = `${leftPercent}% ${leftShort} / ${rightPercent}% ${rightShort}`;
            }
            if (leftLegend) {
                leftLegend.textContent = leftShort;
            }
            if (rightLegend) {
                rightLegend.textContent = rightShort;
            }

            drawPerformanceBarChart(
                { total: leftTotal, shortLabel: leftShort },
                { total: rightTotal, shortLabel: rightShort }
            );
            drawPerformancePieChart(leftTotal, rightTotal);
        };

        const renderChartZoomTarget = (key) => {
            if (key === 'performance') {
                renderPerformanceDashboard();
                return;
            }
            renderSummaryDashboard();
        };

        const updateChartZoom = (key, action) => {
            const currentIndex = chartZoomLevels.indexOf(getChartZoom(key));
            const safeIndex = currentIndex >= 0 ? currentIndex : 1;
            let nextIndex = safeIndex;
            if (action === 'in') {
                nextIndex = Math.min(chartZoomLevels.length - 1, safeIndex + 1);
            } else if (action === 'out') {
                nextIndex = Math.max(0, safeIndex - 1);
            } else {
                nextIndex = 1;
            }
            chartZoomState[key] = chartZoomLevels[nextIndex];
            renderChartZoomTarget(key);
        };

        const applyFinanceFilters = (tipo) => {
            const state = getFilterState(tipo);
            const rows = periodRows(tipo);
            const statusRows = state.statusValues.length
                ? rows.filter((item) => state.statusValues.includes(item.status_vencimento || ''))
                : rows;
            const searchNeedle = normalizeSearchText(state.search);
            const filteredRows = searchNeedle
                ? statusRows.filter((item) => financeSearchText(item, tipo).includes(searchNeedle))
                : statusRows;

            if (!state.sortField || !state.sortDirection) {
                return filteredRows;
            }

            return [...filteredRows].sort((left, right) => {
                const leftValue = sortValue(left, state.sortField, tipo);
                const rightValue = sortValue(right, state.sortField, tipo);
                const result = typeof leftValue === 'string' || typeof rightValue === 'string'
                    ? String(leftValue).localeCompare(String(rightValue), 'pt-BR', { numeric: true, sensitivity: 'base' })
                    : (leftValue === rightValue ? Number(left.id || 0) - Number(right.id || 0) : leftValue - rightValue);
                return state.sortDirection === 'desc' ? -result : result;
            });
        };

        const resetFinancePage = (tipo) => {
            if (financePaginationState[tipo]) {
                financePaginationState[tipo].page = 1;
            }
        };

        const pageWindow = (currentPage, totalPages) => {
            const pages = [];
            const addPage = (page) => {
                if (page >= 1 && page <= totalPages && !pages.includes(page)) {
                    pages.push(page);
                }
            };
            addPage(1);
            for (let page = currentPage - 2; page <= currentPage + 2; page += 1) {
                addPage(page);
            }
            addPage(totalPages);
            pages.sort((left, right) => left - right);
            return pages.reduce((acc, page, index) => {
                if (index > 0 && page - pages[index - 1] > 1) {
                    acc.push('...');
                }
                acc.push(page);
                return acc;
            }, []);
        };

        const paginateRows = (tipo, rows) => {
            const state = financePaginationState[tipo] || { page: 1, perPage: financePageSizeDefault(tipo) };
            const perPage = Math.min(500, Math.max(1, Number(state.perPage) || financePageSizeDefault(tipo)));
            const totalPages = Math.max(1, Math.ceil(rows.length / perPage));
            const page = Math.min(Math.max(1, Number(state.page) || 1), totalPages);
            state.page = page;
            state.perPage = perPage;
            const start = (page - 1) * perPage;
            return {
                page,
                perPage,
                totalPages,
                totalRows: rows.length,
                rows: rows.slice(start, start + perPage)
            };
        };

        const renderPagination = (tipo, pagination) => {
            const target = document.querySelector(`[data-finance-pagination="${tipo}"]`);
            if (!target) {
                return;
            }
            const start = pagination.totalRows ? (pagination.page - 1) * pagination.perPage + 1 : 0;
            const end = Math.min(pagination.totalRows, pagination.page * pagination.perPage);
            const pageButtons = pageWindow(pagination.page, pagination.totalPages).map((page) => {
                if (page === '...') {
                    return '<span class="finance-list-page-ellipsis">...</span>';
                }
                const active = page === pagination.page;
                return `<button type="button" class="finance-list-page-button ${active ? 'active' : ''}" data-finance-page="${tipo}" data-page="${page}" aria-current="${active ? 'page' : 'false'}">${page}</button>`;
            }).join('');
            const pageSizeChoices = Array.from(new Set([...financePageSizeOptions, pagination.perPage])).sort((left, right) => left - right);
            const options = pageSizeChoices.map((size) => `<option value="${size}" ${size === pagination.perPage ? 'selected' : ''}>${size}</option>`).join('');
            target.innerHTML = `<span>${start}-${end} de ${pagination.totalRows}</span>
                <div class="finance-list-page-buttons">${pageButtons}<label>Ir para <input class="finance-list-page-input" type="number" min="1" max="${pagination.totalPages}" value="${pagination.page}" data-finance-page-input="${tipo}"></label></div>
                <label class="finance-list-page-size"><span>Mostrar</span><select data-finance-page-size="${tipo}" aria-label="Quantidade por página">${options}</select><input class="finance-list-page-input" type="number" min="1" max="500" value="${pagination.perPage}" data-finance-page-size-manual="${tipo}" aria-label="Quantidade manual por página"><span>por página</span></label>`;
        };

        const emptyRow = (tipo) => {
            const hasRows = (financeRows[tipo] || []).length > 0;
            return `<tr><td colspan="7">${hasRows ? filteredEmptyMessages[tipo] : emptyMessages[tipo]}</td></tr>`;
        };

        const renderEntradas = (rows) => {
            const tbody = document.querySelector('[data-finance-tbody="entrada"]');
            if (!tbody) {
                return;
            }
            if (!rows.length) {
                tbody.innerHTML = emptyRow('entrada');
                return;
            }
            tbody.innerHTML = rows.map((entrada) => `<tr>
                <td>${formatDate(entrada.data_lancamento)}</td>
                <td>${escapeHtml(entrada.numero_titulo || '-')}</td>
                <td>${escapeHtml(entrada.nome_empresa || '-')}</td>
                <td>${formatCurrency(entrada.valor)}</td>
                <td>${formatDate(entrada.validade)}</td>
                <td>${statusBadge(entrada)}</td>
                <td>${paidButton(entrada, 'entrada')}</td>
            </tr>`).join('');
        };

        const renderSaidas = (rows) => {
            const tbody = document.querySelector('[data-finance-tbody="saida"]');
            if (!tbody) {
                return;
            }
            if (!rows.length) {
                tbody.innerHTML = emptyRow('saida');
                return;
            }
            tbody.innerHTML = rows.map((saida) => `<tr>
                <td>${formatDate(saida.data_entrada)}</td>
                <td>${escapeHtml(saida.numero_titulo || '-')}</td>
                <td>${escapeHtml(saida.nome_empresa || '-')}</td>
                <td>${formatCurrency(saida.valor)}</td>
                <td>${formatDate(saida.validade)}</td>
                <td>${statusBadge(saida)}</td>
                <td>${paidButton(saida, 'saida')}</td>
            </tr>`).join('');
        };

        const exportHeaders = ['Data', 'Nº do título', 'Empresa', 'Valor', 'Validade', 'Status'];

        const exportRow = (item, tipo) => [
            formatDate(tipo === 'saida' ? item.data_entrada : item.data_lancamento),
            item.numero_titulo || '-',
            item.nome_empresa || '-',
            formatCurrency(item.valor),
            formatDate(item.validade),
            item.status_vencimento_label || 'Em dia'
        ];

        const exportFilename = (tipo, extension) => `financeiro-${tipo}-${new Date().toISOString().slice(0, 10)}.${extension}`;

        const downloadFile = (content, filename, mimeType) => {
            const blob = new Blob([content], { type: mimeType });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            URL.revokeObjectURL(link.href);
            link.remove();
        };

        const delimitedValue = (value, delimiter) => {
            const text = String(value ?? '');
            return `"${text.replace(/"/g, '""')}"`;
        };

        const exportDelimited = (tipo, delimiter, extension, mimeType) => {
            const rows = applyFinanceFilters(tipo);
            const content = [exportHeaders, ...rows.map((item) => exportRow(item, tipo))]
                .map((row) => row.map((value) => delimitedValue(value, delimiter)).join(delimiter))
                .join('\n');
            downloadFile('\ufeff' + content, exportFilename(tipo, extension), mimeType);
        };

        const appendCell = (row, tagName, value) => {
            const cell = document.createElement(tagName);
            cell.textContent = String(value ?? '');
            row.appendChild(cell);
        };

        const exportXls = (tipo) => {
            const rows = applyFinanceFilters(tipo);
            const table = document.createElement('table');
            table.setAttribute('border', '1');
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            exportHeaders.forEach((header) => appendCell(headerRow, 'th', header));
            thead.appendChild(headerRow);
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            if (rows.length) {
                rows.forEach((item) => {
                    const row = document.createElement('tr');
                    exportRow(item, tipo).forEach((value) => appendCell(row, 'td', value));
                    tbody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = exportHeaders.length;
                cell.textContent = 'Nenhum lançamento encontrado.';
                row.appendChild(cell);
                tbody.appendChild(row);
            }
            table.appendChild(tbody);

            const exportDoc = document.implementation.createHTMLDocument('');
            const meta = exportDoc.createElement('meta');
            meta.setAttribute('charset', 'UTF-8');
            exportDoc.head.appendChild(meta);
            const style = exportDoc.createElement('style');
            style.textContent = 'table{border-collapse:collapse;}th{background:#eef2ff;font-weight:bold;}td,th{border:1px solid #d1d5db;padding:6px;}';
            exportDoc.head.appendChild(style);
            exportDoc.body.appendChild(exportDoc.importNode(table, true));

            downloadFile('\ufeff<!doctype html>' + exportDoc.documentElement.outerHTML, exportFilename(tipo, 'xls'), 'application/vnd.ms-excel;charset=utf-8');
        };

        const printPeriodDetails = (tipo) => {
            const { period, value } = getPeriodState(tipo);
            const label = periodLabels[period] || 'Período';
            return `${label} • ${periodDisplayLabel(period, value)}`;
        };

        const printFinanceList = (tipo) => {
            const rows = applyFinanceFilters(tipo);
            const title = tipo === 'saida' ? 'Lista de saída' : 'Lista de entrada';
            const printedTotal = rows.reduce((acc, item) => acc + numberFromValue(item.valor), 0);
            document.querySelector('.finance-print-area')?.remove();

            const printArea = document.createElement('section');
            printArea.className = 'finance-print-area';

            const header = document.createElement('header');
            header.className = 'finance-print-header';
            const headerText = document.createElement('div');
            const kicker = document.createElement('p');
            kicker.className = 'finance-print-kicker';
            kicker.textContent = 'Relatório financeiro';
            const heading = document.createElement('h1');
            heading.textContent = title;
            headerText.appendChild(kicker);
            headerText.appendChild(heading);

            const headerMeta = document.createElement('div');
            headerMeta.className = 'finance-print-period';
            headerMeta.textContent = printPeriodDetails(tipo);
            const generatedAt = document.createElement('div');
            generatedAt.className = 'finance-print-muted';
            generatedAt.textContent = `Impresso em ${new Date().toLocaleDateString('pt-BR')} • ${rows.length} ${rows.length === 1 ? 'lançamento' : 'lançamentos'}`;
            headerMeta.appendChild(generatedAt);
            header.appendChild(headerText);
            header.appendChild(headerMeta);
            printArea.appendChild(header);

            const table = document.createElement('table');
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            exportHeaders.forEach((header) => appendCell(headerRow, 'th', header));
            thead.appendChild(headerRow);
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            if (rows.length) {
                rows.forEach((item) => {
                    const row = document.createElement('tr');
                    exportRow(item, tipo).forEach((value) => appendCell(row, 'td', value));
                    tbody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = exportHeaders.length;
                cell.textContent = 'Nenhum lançamento encontrado.';
                row.appendChild(cell);
                tbody.appendChild(row);
            }
            table.appendChild(tbody);
            printArea.appendChild(table);

            const footer = document.createElement('footer');
            footer.className = 'finance-print-footer';
            const totalBox = document.createElement('div');
            totalBox.className = 'finance-print-total';
            const totalLabel = document.createElement('span');
            totalLabel.textContent = 'Total impresso';
            const totalValue = document.createElement('strong');
            totalValue.textContent = formatCurrency(printedTotal);
            totalBox.appendChild(totalLabel);
            totalBox.appendChild(totalValue);
            footer.appendChild(totalBox);
            printArea.appendChild(footer);
            document.body.appendChild(printArea);

            document.body.classList.add('finance-print-mode');
            window.print();
            window.setTimeout(() => {
                document.body.classList.remove('finance-print-mode');
                printArea.remove();
            }, 300);
        };

        const handleFinanceListAction = (tipo, action) => {
            if (action === 'print') {
                printFinanceList(tipo);
                return;
            }
            if (action === 'csv') {
                exportDelimited(tipo, ';', 'csv', 'text/csv;charset=utf-8');
                return;
            }
            if (action === 'txt') {
                exportDelimited(tipo, '\t', 'txt', 'text/plain;charset=utf-8');
                return;
            }
            if (action === 'xls') {
                exportXls(tipo);
            }
        };

        const monthYearDate = (value) => {
            const [year, month] = String(value || '').split('-');
            const date = new Date(Number(year), Number(month || 1) - 1, 1);
            return Number.isNaN(date.getTime()) ? null : date;
        };

        const periodDisplayLabel = (period, value) => {
            if (!value) {
                return 'período não informado';
            }
            if (period === 'diario') {
                return formatDate(value);
            }
            if (period === 'semanal') {
                return value.replace('-W', ' • semana ');
            }
            if (period === 'mensal') {
                const date = monthYearDate(value);
                return date ? date.toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' }) : value;
            }
            return String(value);
        };

        const periodFocusLabel = (period, value) => {
            if (!value) {
                return 'PERÍODO';
            }
            if (period === 'mensal') {
                const date = monthYearDate(value);
                return date ? date.toLocaleDateString('pt-BR', { month: 'long', year: '2-digit' }).replace(' de ', '/').toLocaleUpperCase('pt-BR') : String(value).toLocaleUpperCase('pt-BR');
            }
            if (period === 'diario') {
                const parts = String(value).split('-');
                return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0].slice(-2)}` : String(value).toLocaleUpperCase('pt-BR');
            }
            if (period === 'semanal') {
                const match = String(value).match(/^(\d{4})-W(\d{2})$/);
                return match ? `SEMANA ${match[2]}` : String(value).toLocaleUpperCase('pt-BR');
            }
            return `ANO ${String(value)}`;
        };

        const matchesPeriod = (item, tipo, period, value) => {
            const date = itemDate(item, tipo);
            if (!date || !value) {
                return false;
            }
            if (period === 'diario') {
                return date === value;
            }
            if (period === 'semanal') {
                return isoWeekValue(date) === value;
            }
            if (period === 'mensal') {
                return String(date).startsWith(value);
            }
            if (period === 'anual') {
                return String(date).startsWith(`${value}-`);
            }
            return false;
        };

        const renderPeriodSummary = (tipo) => {
            const select = document.querySelector(`[data-finance-period-select="${tipo}"]`);
            const total = document.querySelector(`[data-finance-period-total="${tipo}"]`);
            const caption = document.querySelector(`[data-finance-period-caption="${tipo}"]`);
            const focus = document.querySelector(`[data-finance-period-focus="${tipo}"]`);
            if (!select || !total) {
                return;
            }
            const period = select.value || 'mensal';
            const inputs = Array.from(document.querySelectorAll(`[data-finance-period-input="${tipo}"]`));
            inputs.forEach((input) => {
                input.hidden = input.dataset.periodInput !== period;
            });
            const input = inputs.find((item) => item.dataset.periodInput === period);
            const value = input?.value || '';
            const rows = periodRows(tipo);
            const sum = rows.reduce((acc, item) => acc + numberFromValue(item.valor), 0);
            const plural = rows.length === 1 ? 'lançamento' : 'lançamentos';
            total.textContent = formatCurrency(sum);
            if (focus) {
                focus.textContent = periodFocusLabel(period, value);
            }
            if (caption) {
                caption.textContent = `${periodLabels[period] || 'Período'} • ${periodDisplayLabel(period, value)} • ${rows.length} ${plural}`;
            }
        };

        const renderStatusSummary = (tipo) => {
            const target = document.querySelector(`[data-finance-status-summary="${tipo}"]`);
            if (!target) {
                return;
            }
            const counts = periodRows(tipo).reduce((acc, item) => {
                const key = item.status_vencimento || 'em_dia';
                acc[key] = (acc[key] || 0) + 1;
                return acc;
            }, {});

            target.innerHTML = statusSummaryItems.map((status) => `<span class="finance-status-pill ${status.className}">
                <span>${status.label}</span>
                <span class="finance-status-pill-count">${Number(counts[status.key] || 0)}</span>
            </span>`).join('');
        };

        const renderFinanceList = (tipo) => {
            if (tipo === 'resumo') {
                renderSummaryDashboard();
                return;
            }
            const rows = applyFinanceFilters(tipo);
            const pagination = paginateRows(tipo, rows);
            refreshSortIndicators(tipo);
            renderPeriodSummary(tipo);
            renderStatusSummary(tipo);
            if (tipo === 'saida') {
                renderSaidas(pagination.rows);
                renderPagination(tipo, pagination);
                return;
            }
            renderEntradas(pagination.rows);
            renderPagination(tipo, pagination);
        };

        const refreshLancamentos = async () => {
            const response = await fetch(listUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!response.ok) {
                throw new Error('Não foi possível atualizar a lista ao vivo.');
            }
            const data = await response.json();
            financeRows.entrada = data.entradas || [];
            financeRows.saida = data.saidas || [];
            renderFinanceList('entrada');
            renderFinanceList('saida');
            renderSummaryDashboard();
            renderPerformanceDashboard();
        };

        const submitLive = async (form) => {
            const submitButton = form.querySelector('button[type="submit"]');
            const formData = new FormData(form);
            const tipo = formData.get('tipo') === 'saida' ? 'saida' : 'entrada';
            const isPayment = form.matches('[data-finance-paid-form]');

            if (submitButton) {
                submitButton.disabled = true;
            }

            try {
                const response = await fetch(form.action, {
                    method: form.method || 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(payload.message || 'Não foi possível salvar a alteração.');
                }

                await refreshLancamentos();
                if (!isPayment) {
                    form.reset();
                    form.querySelectorAll('input[name="data_lancamento"], input[name="data_entrada"]').forEach((input) => {
                        input.value = <?= json_encode($hoje); ?>;
                    });
                    showMessage(payload.message || 'Lançamento salvo ao vivo, sem recarregar a página.');
                    return;
                }

                activate(tipo, 'lista', true);
                showMessage(payload.message || 'Pagamento atualizado ao vivo.');
            } catch (error) {
                showMessage(error.message || 'Não foi possível salvar a alteração.', 'error');
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        };

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }
            if (!form.matches('[data-finance-live-form], [data-finance-paid-form]')) {
                return;
            }
            event.preventDefault();
            submitLive(form);
        });

        buttons.forEach((button) => {
            button.addEventListener('click', () => activate(button.dataset.financeTab, 'lancamento', true));
        });

        subButtons.forEach((button) => {
            button.addEventListener('click', () => activate(button.dataset.financeSubtab, button.dataset.financeSubpanel, true));
        });

        summaryButtons.forEach((button) => {
            button.addEventListener('click', () => activateSummaryPanel(button.dataset.financeSummaryTab, true));
        });

        document.querySelector('[data-finance-performance-type]')?.addEventListener('change', renderPerformanceDashboard);
        document.querySelectorAll('[data-finance-compare-period]').forEach((select) => {
            select.addEventListener('change', renderPerformanceDashboard);
        });
        document.querySelectorAll('[data-finance-compare-input]').forEach((input) => {
            if (input.dataset.periodInput === 'semanal') {
                input.value = isoWeekValue(todayValue);
            }
            input.addEventListener('change', renderPerformanceDashboard);
        });
        document.querySelectorAll('[data-finance-list-search]').forEach((input) => {
            input.addEventListener('input', () => {
                resetFinancePage(input.dataset.financeListSearch);
                renderFinanceList(input.dataset.financeListSearch);
            });
        });
        document.querySelectorAll('[data-finance-chart-zoom]').forEach((button) => {
            button.addEventListener('click', () => {
                updateChartZoom(button.dataset.financeChartZoom, button.dataset.financeChartAction);
            });
        });

        const closeExportMenus = (except = null) => {
            document.querySelectorAll('[data-finance-export-group]').forEach((group) => {
                if (group === except) {
                    return;
                }
                group.querySelector('[data-finance-export-menu]')?.setAttribute('hidden', 'hidden');
                group.querySelector('[data-finance-export-trigger]')?.setAttribute('aria-expanded', 'false');
            });
        };

        document.querySelectorAll('[data-finance-export-trigger]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.stopPropagation();
                const group = button.closest('[data-finance-export-group]');
                const menu = group?.querySelector('[data-finance-export-menu]');
                const isOpen = menu && !menu.hasAttribute('hidden');
                closeExportMenus(group);
                if (menu) {
                    menu.toggleAttribute('hidden', Boolean(isOpen));
                    button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                }
            });
        });

        document.querySelectorAll('[data-finance-list-action]').forEach((button) => {
            button.addEventListener('click', () => {
                closeExportMenus();
                handleFinanceListAction(button.dataset.financeListType, button.dataset.financeListAction);
            });
        });

        document.addEventListener('click', (event) => {
            if (!(event.target instanceof Element) || !event.target.closest('[data-finance-export-group]')) {
                closeExportMenus();
            }
        });

        document.querySelectorAll('[data-finance-period-select]').forEach((select) => {
            const tipo = select.dataset.financePeriodSelect;
            document.querySelector(`[data-finance-period-input="${tipo}"][data-period-input="semanal"]`)?.setAttribute('value', isoWeekValue(todayValue));
            select.addEventListener('change', () => {
                resetFinancePage(tipo);
                renderFinanceList(tipo);
            });
        });

        document.querySelectorAll('[data-finance-period-input]').forEach((input) => {
            input.addEventListener('change', () => {
                resetFinancePage(input.dataset.financePeriodInput);
                renderFinanceList(input.dataset.financePeriodInput);
            });
        });

        const positionStatusMenu = (filter) => {
            const trigger = filter.querySelector('[data-finance-status-trigger]');
            const menu = filter.querySelector('[data-finance-status-menu]');
            if (!trigger || !menu) {
                return;
            }

            const rect = trigger.getBoundingClientRect();
            const gap = 8;
            const menuWidth = Math.max(230, rect.width);
            const maxLeft = Math.max(8, window.innerWidth - menuWidth - 8);
            menu.style.minWidth = `${menuWidth}px`;
            menu.style.left = `${Math.min(Math.max(8, rect.left), maxLeft)}px`;
            menu.style.top = `${rect.bottom + gap}px`;
        };

        const closeStatusMenus = (except = null) => {
            document.querySelectorAll('[data-finance-status-filter]').forEach((filter) => {
                if (filter === except) {
                    return;
                }
                const menu = filter.querySelector('[data-finance-status-menu]');
                menu?.setAttribute('hidden', 'hidden');
                if (menu) {
                    menu.style.left = '';
                    menu.style.top = '';
                    menu.style.minWidth = '';
                }
                filter.querySelector('[data-finance-status-trigger]')?.setAttribute('aria-expanded', 'false');
            });
        };

        document.querySelectorAll('[data-finance-status-filter]').forEach((filter) => {
            const trigger = filter.querySelector('[data-finance-status-trigger]');
            const menu = filter.querySelector('[data-finance-status-menu]');
            const currentLabel = filter.querySelector('[data-finance-status-current]');

            const updateStatusSummary = () => {
                const selected = Array.from(filter.querySelectorAll('[data-finance-status-option].active'))
                    .filter((option) => option.getAttribute('value'));
                const values = selected.map((option) => option.getAttribute('value') || '');
                filter.dataset.value = values.join(',');

                if (currentLabel) {
                    currentLabel.hidden = values.length === 0;
                    if (values.length === 0) {
                        currentLabel.textContent = 'Todos';
                        currentLabel.className = 'finance-status-filter-current finance-status-filter-count finance-status-badge finance-status-neutral';
                    } else if (values.length === 1) {
                        currentLabel.textContent = selected[0].dataset.label || '1 status';
                        currentLabel.className = `finance-status-filter-current finance-status-filter-count finance-status-badge ${selected[0].dataset.statusClass || 'finance-status-neutral'}`;
                    } else {
                        currentLabel.textContent = `${values.length} status`;
                        currentLabel.className = 'finance-status-filter-current finance-status-filter-count finance-status-badge finance-status-neutral';
                    }
                }

                trigger?.classList.toggle('is-active', values.length > 0);
                filter.querySelector('[data-finance-status-option][value=""]')?.classList.toggle('active', values.length === 0);
            };

            trigger?.addEventListener('click', () => {
                const isOpen = !menu?.hasAttribute('hidden');
                closeStatusMenus(filter);
                if (!menu) {
                    return;
                }
                menu.toggleAttribute('hidden', isOpen);
                if (!isOpen) {
                    positionStatusMenu(filter);
                }
                trigger.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            });

            filter.querySelectorAll('[data-finance-status-option]').forEach((option) => {
                option.addEventListener('click', () => {
                    const value = option.getAttribute('value') || '';
                    if (!value) {
                        filter.querySelectorAll('[data-finance-status-option]').forEach((item) => item.classList.toggle('active', item === option));
                    } else {
                        option.classList.toggle('active');
                        filter.querySelector('[data-finance-status-option][value=""]')?.classList.remove('active');
                    }

                    updateStatusSummary();
                    resetFinancePage(filter.dataset.financeStatusFilter);
                    renderFinanceList(filter.dataset.financeStatusFilter);
                });
            });

            updateStatusSummary();
        });

        document.addEventListener('click', (event) => {
            if (!(event.target instanceof Element) || !event.target.closest('[data-finance-status-filter]')) {
                closeStatusMenus();
            }
        });

        window.addEventListener('resize', () => {
            closeStatusMenus();
            if (document.querySelector('[data-finance-panel="resumo"]')?.classList.contains('active')) {
                renderActiveSummaryPanel();
            }
        });
        window.addEventListener('scroll', () => closeStatusMenus(), true);

        document.querySelectorAll('[data-finance-sort-button]').forEach((button) => {
            button.addEventListener('click', () => {
                const tipo = button.dataset.financeSortButton;
                const field = button.dataset.financeSortField;
                const current = financeSortState[tipo] || { field: '', direction: '' };
                const nextDirection = current.field === field && current.direction === 'asc' ? 'desc' : 'asc';
                financeSortState[tipo] = { field, direction: nextDirection };
                resetFinancePage(tipo);
                renderFinanceList(tipo);
            });
        });

        document.addEventListener('click', (event) => {
            const button = event.target instanceof Element ? event.target.closest('[data-finance-page]') : null;
            if (!button) {
                return;
            }
            const tipo = button.dataset.financePage;
            const page = Number(button.dataset.page || 1);
            if (financePaginationState[tipo]) {
                financePaginationState[tipo].page = page;
                renderFinanceList(tipo);
            }
        });

        document.addEventListener('change', (event) => {
            const select = event.target instanceof Element ? event.target.closest('[data-finance-page-size]') : null;
            const input = event.target instanceof Element ? event.target.closest('[data-finance-page-input]') : null;
            if (select) {
                const tipo = select.dataset.financePageSize;
                if (!financePaginationState[tipo]) {
                    return;
                }
                financePaginationState[tipo].perPage = Number(select.value) || financePageSizeDefault(tipo);
                resetFinancePage(tipo);
                renderFinanceList(tipo);
                return;
            }
            const sizeInput = event.target instanceof Element ? event.target.closest('[data-finance-page-size-manual]') : null;
            if (sizeInput) {
                const tipo = sizeInput.dataset.financePageSizeManual;
                if (financePaginationState[tipo]) {
                    financePaginationState[tipo].perPage = Math.min(500, Math.max(1, Number(sizeInput.value) || financePageSizeDefault(tipo)));
                    resetFinancePage(tipo);
                    renderFinanceList(tipo);
                }
                return;
            }
            if (input) {
                const tipo = input.dataset.financePageInput;
                if (financePaginationState[tipo]) {
                    financePaginationState[tipo].page = Number(input.value) || 1;
                    renderFinanceList(tipo);
                }
            }
        });

        document.addEventListener('keydown', (event) => {
            const input = event.target instanceof Element ? event.target.closest('[data-finance-page-input]') : null;
            const sizeInput = event.target instanceof Element ? event.target.closest('[data-finance-page-size-manual]') : null;
            if (event.key !== 'Enter' || (!input && !sizeInput)) {
                return;
            }
            event.preventDefault();
            if (sizeInput) {
                const tipo = sizeInput.dataset.financePageSizeManual;
                if (financePaginationState[tipo]) {
                    financePaginationState[tipo].perPage = Math.min(500, Math.max(1, Number(sizeInput.value) || financePageSizeDefault(tipo)));
                    resetFinancePage(tipo);
                    renderFinanceList(tipo);
                }
                return;
            }
            const tipo = input.dataset.financePageInput;
            if (financePaginationState[tipo]) {
                financePaginationState[tipo].page = Number(input.value) || 1;
                renderFinanceList(tipo);
            }
        });


        renderFinanceList('entrada');
        renderFinanceList('saida');
        renderSummaryDashboard();
        renderPerformanceDashboard();
        removeLeakedCodeText();

        const initial = window.location.hash.replace('#', '');
        if (initial === 'resumo') {
            activate('resumo', 'geral', false);
        } else if (initial === 'resumo-desempenho') {
            activate('resumo', 'desempenho', false);
        } else if (initial === 'saida' || initial === 'saida-lancamento') {
            activate('saida', 'lancamento', false);
        } else if (initial === 'saida-lista') {
            activate('saida', 'lista', false);
        } else if (initial === 'entrada-lista') {
            activate('entrada', 'lista', false);
        } else {
            activate('entrada', 'lancamento', false);
        }
    })();
</script>
