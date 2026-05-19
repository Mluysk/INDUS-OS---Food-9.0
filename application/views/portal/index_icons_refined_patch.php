<?php
include __DIR__.'/index_settings_profile_patch.php';
?>
<style>
    html body .portal-app__header-links [data-portal-jump="suporte"] .portal-app__link-icon,
    html body .portal-app__header-links [data-portal-settings] .portal-app__link-icon {
        width: 22px !important;
        height: 22px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        flex: 0 0 22px;
    }

    html body .portal-app__header-links [data-portal-jump="suporte"] .portal-app__link-icon svg,
    html body .portal-app__header-links [data-portal-settings] .portal-app__link-icon svg {
        width: 22px !important;
        height: 22px !important;
        display: block;
        fill: none !important;
        stroke: currentColor !important;
        stroke-width: 2.1;
        stroke-linecap: round;
        stroke-linejoin: round;
        shape-rendering: geometricPrecision;
    }

    html body .portal-app__header-links [data-portal-settings] {
        color: var(--primary, #2563eb);
    }
</style>
<script>
(function () {
    const supportIcon = `
        <span class="portal-app__link-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M4 12a8 8 0 0 1 16 0" />
                <path d="M4 12v3.2a2 2 0 0 0 2 2h1.2a1 1 0 0 0 1-1V12a1 1 0 0 0-1-1H6a2 2 0 0 0-2 1Z" />
                <path d="M20 12v3.2a2 2 0 0 1-2 2h-1.2a1 1 0 0 1-1-1V12a1 1 0 0 1 1-1H18a2 2 0 0 1 2 1Z" />
                <path d="M18 17.2c0 2-1.7 3.3-4.1 3.3H12" />
            </svg>
        </span>`;

    const settingsIcon = `
        <span class="portal-app__link-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3.15" />
                <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2.05 2.05 0 0 1-2.9 2.9l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 1.56V21a2.05 2.05 0 0 1-4.1 0v-.04A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.87.34l-.06.06a2.05 2.05 0 0 1-2.9-2.9l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.56-1H3a2.05 2.05 0 0 1 0-4.1h.04A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2.05 2.05 0 0 1 2.9-2.9l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.56V3a2.05 2.05 0 0 1 4.1 0v.04A1.7 1.7 0 0 0 15 4.6a1.7 1.7 0 0 0 1.87-.34l.06-.06a2.05 2.05 0 0 1 2.9 2.9l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.56 1H21a2.05 2.05 0 0 1 0 4.1h-.04A1.7 1.7 0 0 0 19.4 15Z" />
            </svg>
        </span>`;

    const replaceIcon = (button, icon) => {
        if (!button) return;
        const current = button.querySelector('.portal-app__link-icon');
        if (current) {
            current.outerHTML = icon;
        } else {
            button.insertAdjacentHTML('afterbegin', icon);
        }
    };

    const run = () => {
        replaceIcon(document.querySelector('.portal-app__header-links [data-portal-jump="suporte"]'), supportIcon);
        replaceIcon(document.querySelector('.portal-app__header-links [data-portal-settings]'), settingsIcon);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            run();
            window.setTimeout(run, 160);
            window.setTimeout(run, 360);
        }, { once: true });
    } else {
        run();
        window.setTimeout(run, 160);
        window.setTimeout(run, 360);
    }
})();
</script>
