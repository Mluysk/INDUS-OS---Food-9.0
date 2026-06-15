<?php
$documentTitle = trim(($title ?? '') . ' · ' . ($appName ?? 'IndusOS Food'), ' ·');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($documentTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        :root {
            --brand: <?= htmlspecialchars($theme['primary_hex'] ?? '#5b616f', ENT_QUOTES, 'UTF-8'); ?>;
            --text: #1f2933;
            --muted: #52606d;
            --border: #d9dde4;
            --surface: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--surface);
            color: var(--text);
            padding: 1rem;
        }

        .print-container {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            background: #fff;
        }

        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.75rem;
        }

        .print-header h1 {
            margin: 0;
            font-size: 1.75rem;
            line-height: 1.2;
        }

        .print-header p {
            margin: 0.35rem 0 0;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .print-header img {
            height: 58px;
            display: block;
        }

        .print-meta {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .print-button {
            appearance: none;
            border: none;
            background: var(--brand);
            color: #fff;
            padding: 0.55rem 1.25rem;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.18);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.92rem;
            margin-bottom: 2rem;
        }

        th, td {
            text-align: left;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            padding: 0.65rem 0.75rem;
        }

        th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            background: rgba(15, 23, 42, 0.03);
        }

        tbody tr:nth-child(even) {
            background: rgba(15, 23, 42, 0.03);
        }

        .no-data {
            text-align: center;
            padding: 2rem 0;
            color: var(--muted);
            font-style: italic;
        }

        .nota-shell {
            font-size: 10.8px !important;
        }

        .nota-head {
            padding: 7px !important;
            gap: 7px !important;
        }

        .nota-logo img {
            height: 42px !important;
        }

        .nota-empresa h1 {
            font-size: 14px !important;
        }

        .nota-empresa p,
        .nota-block p,
        .nota-observacoes p {
            margin-top: 1px !important;
            margin-bottom: 1px !important;
        }

        .nota-head__box {
            padding: 6px 8px !important;
        }

        .nota-head__box .row {
            font-size: 10.8px !important;
            line-height: 1.18 !important;
        }

        .nota-block {
            padding: 7px !important;
            gap: 7px !important;
        }

        .nota-block h3 {
            margin-bottom: 2px !important;
            font-size: 11.5px !important;
        }

        .nota-table {
            margin-bottom: 0 !important;
        }

        .nota-table th,
        .nota-table td {
            padding: 3px 5px !important;
            font-size: 10.9px !important;
            line-height: 1.12 !important;
        }

        .nota-table th {
            font-size: 10.2px !important;
            letter-spacing: 0.05em !important;
        }

        .nota-table .product-col strong {
            font-size: 10.9px !important;
            line-height: 1.12 !important;
        }

        .nota-table .muted {
            font-size: 10px !important;
            line-height: 1.08 !important;
        }

        .nota-observacoes {
            padding: 7px !important;
            gap: 7px !important;
        }

        .nota-observacoes h4 {
            margin-bottom: 2px !important;
            font-size: 10.6px !important;
        }

        .total-box {
            padding: 6px !important;
            gap: 4px !important;
        }

        .total-row__grand {
            font-size: 12.4px !important;
            padding-top: 4px !important;
        }

        .pix-box {
            min-height: 142px !important;
            padding: 8px 8px 8px 20px !important;
            gap: 14px !important;
            grid-template-columns: minmax(200px, 1fr) minmax(270px, 0.9fr) 124px !important;
        }

        .pix-brand-logo {
            width: 161px !important;
            max-width: 90% !important;
            transform: none !important;
        }

        .pix-payment-info {
            padding: 8px 0 3px !important;
            text-align: right !important;
        }

        .pix-payment-info .pix-eyebrow {
            font-size: 11px !important;
            margin: 0 0 9px !important;
            transform: translateY(9px) !important;
        }

        .pix-payment-info h3 {
            margin: 0 0 7px !important;
        }

        .pix-payment-info h3,
        .pix-payment-info .pix-key {
            font-size: 14px !important;
            margin-top: 0 !important;
            transform: translateY(9px) !important;
        }

        .pix-payment-info .pix-note {
            font-size: 11px !important;
            margin-top: auto !important;
            transform: translateY(-12px) !important;
        }

        .pix-right {
            width: 124px !important;
            height: 124px !important;
            min-height: 0 !important;
            padding: 5px !important;
        }

        @page {
            margin: 8mm;
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .print-button {
                display: none;
            }

            .print-container {
                width: 100%;
                max-width: none;
                box-shadow: none;
            }

            .nota-shell {
                font-size: 10px !important;
            }

            .nota-table th,
            .nota-table td {
                padding: 2px 4px !important;
                font-size: 10.2px !important;
                line-height: 1.08 !important;
            }

            .nota-table .product-col strong {
                font-size: 10.2px !important;
            }

            .nota-table th {
                font-size: 9.5px !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <?= $content; ?>
    </div>
    <script>
        window.addEventListener('load', () => {
            if (window.matchMedia('print').matches) {
                return;
            }
        });
    </script>
</body>
</html>
