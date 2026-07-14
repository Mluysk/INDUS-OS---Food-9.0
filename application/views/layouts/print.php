<?php
$documentTitle = trim(($title ?? '') . ' · ' . ($appName ?? 'IndusOS Food'), ' ·');
$hasCustomShareButton = str_contains((string)($content ?? ''), 'visual-share-button');
$reportLogoTintEnabled = !empty($sidebarLogo['filter_enabled']);
$reportLogoFilterMatrix = (string)($sidebarLogo['filter_matrix'] ?? '');
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
            --form-logo-size: 53px;
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

        .print-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 18px;
            min-width: 0;
            padding: 8px 0;
        }

        .print-brand > div {
            min-width: 0;
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
            height: var(--form-logo-size) !important;
            width: auto;
            max-width: 100%;
            display: block;
            flex: 0 0 auto;
            object-fit: contain;
            object-position: left center;
            align-self: flex-start;
            transform-origin: left center;
        }

        body.report-logo-filter-enabled .print-header img {
            filter: url(#reportLogoTintFilter);
            -webkit-filter: url(#reportLogoTintFilter);
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

        .report-share-button {
            position: fixed;
            right: 22px;
            bottom: 22px;
            z-index: 40;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 50px;
            padding: 0 18px;
            border: 0;
            border-radius: 999px;
            background: var(--brand);
            color: #fff;
            font: inherit;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 20px 42px -22px rgba(15, 23, 42, 0.65);
        }

        .report-share-button svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
        }

        .report-share-status {
            position: fixed;
            right: 22px;
            bottom: 82px;
            z-index: 39;
            display: none;
            max-width: min(320px, calc(100vw - 44px));
            padding: 10px 14px;
            border-radius: 14px;
            background: #0f172a;
            color: #fff;
            font-size: 0.86rem;
            font-weight: 800;
            box-shadow: 0 20px 42px -28px rgba(15, 23, 42, 0.72);
        }

        .report-share-status.is-visible {
            display: block;
        }

        @media (max-width: 720px) {
            .report-share-button {
                right: 14px;
                bottom: 14px;
                width: 54px;
                height: 54px;
                min-height: 54px;
                padding: 0;
                border-radius: 50%;
            }

            .report-share-button span {
                display: none;
            }

            .report-share-button svg {
                width: 24px;
                height: 24px;
            }

            .report-share-status {
                right: 14px;
                bottom: 76px;
            }
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

            .print-brand {
                gap: 10px;
                padding: 4px 0;
            }

            .print-button {
                display: none;
            }

            .report-share-button,
            .report-share-status {
                display: none !important;
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
<body class="<?= $reportLogoTintEnabled ? 'report-logo-filter-enabled' : ''; ?>">
    <?php if ($reportLogoTintEnabled && $reportLogoFilterMatrix !== ''): ?>
        <svg xmlns="http://www.w3.org/2000/svg" width="0" height="0" style="position:absolute;visibility:hidden" aria-hidden="true">
            <defs>
                <filter id="reportLogoTintFilter" color-interpolation-filters="sRGB">
                    <feColorMatrix type="matrix" values="<?= htmlspecialchars($reportLogoFilterMatrix, ENT_QUOTES, 'UTF-8'); ?>"></feColorMatrix>
                </filter>
            </defs>
        </svg>
    <?php endif; ?>
    <div class="print-container">
        <?= $content; ?>
    </div>
    <?php if (!$hasCustomShareButton): ?>
        <button type="button" class="report-share-button" aria-label="Compartilhar relatório">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <path d="m8.59 13.51 6.83 3.98"></path>
                <path d="m15.41 6.51-6.82 3.98"></path>
            </svg>
            <span>Compartilhar link</span>
        </button>
        <div class="report-share-status" role="status" aria-live="polite"></div>
    <?php endif; ?>
    <script>
        (() => {
            const button = document.querySelector('.report-share-button');
            const status = document.querySelector('.report-share-status');
            if (!button) {
                return;
            }

            const showStatus = (message) => {
                if (!status) {
                    return;
                }
                status.textContent = message;
                status.classList.add('is-visible');
                window.clearTimeout(status._timer);
                status._timer = window.setTimeout(() => status.classList.remove('is-visible'), 2600);
            };

            const copyLink = async (url) => {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(url);
                    return true;
                }

                const input = document.createElement('input');
                input.value = url;
                input.setAttribute('readonly', 'readonly');
                input.style.position = 'fixed';
                input.style.left = '-9999px';
                document.body.appendChild(input);
                input.select();
                const copied = document.execCommand('copy');
                input.remove();
                return copied;
            };

            button.addEventListener('click', async () => {
                const url = window.location.href;
                const title = document.title || 'Relatório';
                try {
                    if (navigator.share) {
                        await navigator.share({
                            title,
                            text: 'Confira este relatório.',
                            url,
                        });
                        return;
                    }

                    const copied = await copyLink(url);
                    showStatus(copied ? 'Link do relatório copiado.' : 'Não foi possível copiar o link.');
                } catch (error) {
                    if (error && error.name === 'AbortError') {
                        return;
                    }
                    showStatus('Não foi possível compartilhar. Copie o link da página.');
                }
            });
        })();
    </script>
</body>
</html>
