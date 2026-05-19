<?php
include __DIR__.'/index_settings_document_width_patch.php';
?>
<style>
    html body .topbar .portal-user-pill,
    html body .topbar .portal-client-pill,
    html body .topbar .portal-profile-pill,
    html body .topbar .portal-app__client,
    html body .topbar [data-cliente-badge],
    html body .portal-app__profile,
    html body .portal-app__client,
    html body [data-cliente-badge] {
        width: max-content !important;
        min-width: max-content !important;
        max-width: min(calc(100vw - 2rem), 46ch) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 0.62rem !important;
        white-space: nowrap !important;
        overflow: visible !important;
        box-sizing: border-box !important;
        flex: 0 0 auto !important;
    }

    html body .portal-app__profile strong,
    html body .portal-app__profile b,
    html body .portal-app__client strong,
    html body .portal-app__client b,
    html body [data-cliente-badge] strong,
    html body [data-cliente-badge] b {
        max-width: none !important;
        min-width: 0 !important;
        width: auto !important;
        white-space: nowrap !important;
        overflow: visible !important;
        text-overflow: clip !important;
    }

    html body .portal-app__profile small,
    html body .portal-app__client small,
    html body [data-cliente-badge] small {
        white-space: nowrap !important;
        overflow: visible !important;
        text-overflow: clip !important;
    }

    html body .portal-app__profile [class*="avatar"],
    html body .portal-app__client [class*="avatar"],
    html body [data-cliente-badge] [class*="avatar"],
    html body .portal-app__profile [class*="initial"],
    html body .portal-app__client [class*="initial"],
    html body [data-cliente-badge] [class*="initial"] {
        width: 42px !important;
        height: 42px !important;
        min-width: 42px !important;
        min-height: 42px !important;
        max-width: 42px !important;
        max-height: 42px !important;
        aspect-ratio: 1 / 1 !important;
        flex: 0 0 42px !important;
        display: inline-grid !important;
        place-items: center !important;
        border-radius: 999px !important;
        line-height: 1 !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }

    html body .portal-app__profile > span:first-child:not(:last-child),
    html body .portal-app__client > span:first-child:not(:last-child),
    html body [data-cliente-badge] > span:first-child:not(:last-child) {
        width: 42px !important;
        height: 42px !important;
        min-width: 42px !important;
        min-height: 42px !important;
        max-width: 42px !important;
        max-height: 42px !important;
        aspect-ratio: 1 / 1 !important;
        flex: 0 0 42px !important;
        display: inline-grid !important;
        place-items: center !important;
        border-radius: 999px !important;
        line-height: 1 !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }

    html body #portal-full-cep,
    html body .portal-settings-field:has(#portal-full-cep) input {
        width: 14ch !important;
        min-width: 14ch !important;
        max-width: 14ch !important;
        font-variant-numeric: tabular-nums !important;
    }

    html body .portal-settings-field:has(#portal-full-cep) {
        width: calc(14ch + 2rem) !important;
        min-width: calc(14ch + 2rem) !important;
        max-width: calc(14ch + 2rem) !important;
        flex: 0 0 calc(14ch + 2rem) !important;
    }

    html body .portal-settings-field:has(#portal-full-cidade) {
        width: 64% !important;
        max-width: 64% !important;
        min-width: min(64%, 260px) !important;
        flex: 0 0 64% !important;
    }

    html body .portal-settings-field:has(#portal-full-cidade) input,
    html body #portal-full-cidade {
        width: 100% !important;
        max-width: 100% !important;
    }

    html body .portal-settings-address-row {
        grid-template-columns: calc(14ch + 2rem) minmax(0, 1.6fr) minmax(12ch, 12ch) minmax(0, 1fr) !important;
    }

    @media (max-width: 640px) {
        html body .topbar .portal-user-pill,
        html body .topbar .portal-client-pill,
        html body .topbar .portal-profile-pill,
        html body .topbar .portal-app__client,
        html body .topbar [data-cliente-badge],
        html body .portal-app__profile,
        html body .portal-app__client,
        html body [data-cliente-badge] {
            max-width: calc(100vw - 1.25rem) !important;
        }

        html body .portal-settings-field:has(#portal-full-cidade) {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            flex-basis: 100% !important;
        }
    }
</style>
