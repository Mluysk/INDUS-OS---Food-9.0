<?php
include __DIR__.'/index_settings_icons_scroll_patch.php';
?>
<style>
    html body .portal-settings-section {
        padding: 1rem !important;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 12%, var(--portal-card-border, rgba(15, 23, 42, 0.08))) !important;
        border-radius: 20px !important;
        background:
            linear-gradient(135deg,
                color-mix(in srgb, var(--primary, #2563eb) 5%, transparent),
                color-mix(in srgb, var(--portal-surface, #fff) 88%, transparent)
            ) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.42);
    }

    html body .portal-settings-section-title {
        width: 100%;
        margin: 0 0 0.35rem !important;
        padding: 0.45rem 0.55rem;
        border-radius: 14px;
        background: color-mix(in srgb, var(--primary, #2563eb) 7%, transparent);
    }

    html body .portal-settings-grid {
        padding: 0.15rem;
    }

    html body .portal-settings-branch-row,
    html body .portal-settings-option,
    html body .portal-settings-doc-toggle,
    html body .portal-settings-contact-type {
        padding: 0.85rem !important;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 10%, var(--portal-card-border, rgba(15, 23, 42, 0.08))) !important;
        border-radius: 18px !important;
        background: color-mix(in srgb, var(--primary, #2563eb) 4%, var(--portal-surface, #fff)) !important;
    }

    html body .portal-settings-field {
        padding: 0.72rem;
        border: 1px solid color-mix(in srgb, var(--primary, #2563eb) 8%, var(--portal-card-border, rgba(15, 23, 42, 0.08)));
        border-radius: 16px;
        background: color-mix(in srgb, var(--portal-surface, #fff) 91%, var(--primary, #2563eb));
    }

    html body .portal-settings-field input {
        background: color-mix(in srgb, #ffffff 72%, var(--portal-surface, #fff)) !important;
    }

    [data-theme="dark"] body .portal-settings-section {
        border-color: color-mix(in srgb, var(--primary, #60a5fa) 18%, rgba(148, 163, 184, 0.16)) !important;
        background:
            linear-gradient(135deg,
                color-mix(in srgb, var(--primary, #60a5fa) 9%, transparent),
                color-mix(in srgb, var(--portal-surface, rgba(30, 41, 59, 0.76)) 92%, transparent)
            ) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
    }

    [data-theme="dark"] body .portal-settings-section-title,
    [data-theme="dark"] body .portal-settings-branch-row,
    [data-theme="dark"] body .portal-settings-option,
    [data-theme="dark"] body .portal-settings-doc-toggle,
    [data-theme="dark"] body .portal-settings-contact-type,
    [data-theme="dark"] body .portal-settings-field {
        background: color-mix(in srgb, var(--primary, #60a5fa) 7%, rgba(15, 23, 42, 0.55)) !important;
        border-color: color-mix(in srgb, var(--primary, #60a5fa) 16%, rgba(148, 163, 184, 0.18)) !important;
    }

    [data-theme="dark"] body .portal-settings-field input {
        background: rgba(15, 23, 42, 0.72) !important;
    }
</style>
