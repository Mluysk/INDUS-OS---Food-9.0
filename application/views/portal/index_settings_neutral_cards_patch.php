<?php
include __DIR__.'/index_settings_icons_scroll_patch.php';
?>
<style>
    html body .portal-settings-section {
        padding: 1rem !important;
        border: 1px solid rgba(15, 23, 42, 0.075) !important;
        border-radius: 20px !important;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.88), rgba(248, 250, 252, 0.78)) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.58);
    }

    html body .portal-settings-section-title {
        width: 100%;
        margin: 0 0 0.35rem !important;
        padding: 0.45rem 0.55rem;
        border-radius: 14px;
        background: rgba(248, 250, 252, 0.92) !important;
        border: 1px solid rgba(15, 23, 42, 0.045);
    }

    html body .portal-settings-grid {
        padding: 0.15rem;
    }

    html body .portal-settings-branch-row,
    html body .portal-settings-option,
    html body .portal-settings-doc-toggle,
    html body .portal-settings-contact-type {
        padding: 0.85rem !important;
        border: 1px solid rgba(15, 23, 42, 0.065) !important;
        border-radius: 18px !important;
        background: rgba(250, 251, 253, 0.86) !important;
    }

    html body .portal-settings-field {
        padding: 0.72rem;
        border: 1px solid rgba(15, 23, 42, 0.055) !important;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.72) !important;
    }

    html body .portal-settings-field input {
        background: rgba(255, 255, 255, 0.92) !important;
    }

    [data-theme="dark"] body .portal-settings-section {
        border-color: rgba(148, 163, 184, 0.16) !important;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.64), rgba(15, 23, 42, 0.54)) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.055);
    }

    [data-theme="dark"] body .portal-settings-section-title,
    [data-theme="dark"] body .portal-settings-branch-row,
    [data-theme="dark"] body .portal-settings-option,
    [data-theme="dark"] body .portal-settings-doc-toggle,
    [data-theme="dark"] body .portal-settings-contact-type,
    [data-theme="dark"] body .portal-settings-field {
        background: rgba(30, 41, 59, 0.56) !important;
        border-color: rgba(148, 163, 184, 0.14) !important;
    }

    [data-theme="dark"] body .portal-settings-field input {
        background: rgba(15, 23, 42, 0.72) !important;
    }
</style>
