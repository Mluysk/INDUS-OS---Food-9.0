<?php
include __DIR__.'/index_menu_cart_patch.php';
?>
<style>
    html body .portal-settings-modal {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: grid;
        place-items: center;
        padding: 1.25rem;
    }

    html body .portal-settings-modal[hidden] {
        display: none !important;
    }

    html body .portal-settings-modal__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.46);
        backdrop-filter: blur(7px);
    }

    html body .portal-settings-modal__dialog {
        position: relative;
        width: min(440px, 100%);
        border-radius: 24px;
        border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.08));
        background: var(--portal-card-bg, #fff);
        box-shadow: 0 28px 70px -38px rgba(15, 23, 42, 0.55);
        padding: 1.35rem;
    }

    html body .portal-settings-modal__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    html body .portal-settings-modal__title {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        margin: 0;
        font-size: 1.18rem;
        font-weight: 900;
        color: var(--text, #0f172a);
    }

    html body .portal-settings-modal__icon {
        width: 2.25rem;
        height: 2.25rem;
        display: inline-grid;
        place-items: center;
        border-radius: 14px;
        background: color-mix(in srgb, var(--primary, #dc2626) 12%, #ffffff);
        color: var(--primary, #dc2626);
    }

    html body .portal-settings-modal__icon svg {
        width: 1.15rem;
        height: 1.15rem;
        fill: currentColor;
    }

    html body .portal-settings-modal__close {
        width: 2.45rem;
        height: 2.45rem;
        display: inline-grid;
        place-items: center;
        border: 0;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.14);
        color: var(--text, #0f172a);
        cursor: pointer;
    }

    html body .portal-settings-modal__close svg {
        width: 1.05rem;
        height: 1.05rem;
        fill: currentColor;
    }

    html body .portal-settings-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.08));
        border-radius: 18px;
        background: color-mix(in srgb, var(--portal-surface, #fff) 78%, transparent);
    }

    html body .portal-settings-option strong {
        display: block;
        font-size: 0.98rem;
        color: var(--text, #0f172a);
    }

    html body .portal-settings-option small {
        display: block;
        margin-top: 0.2rem;
        color: var(--text-muted, #64748b);
        font-weight: 650;
    }
</style>
<script>
(function () {
    const gearIcon = `
        <span class="portal-app__link-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M10.98 2.25h2.04a1.4 1.4 0 0 1 1.36 1.06l.34 1.35c.43.16.84.34 1.23.56l1.22-.72a1.4 1.4 0 0 1 1.71.22l1.44 1.44a1.4 1.4 0 0 1 .22 1.71l-.72 1.22c.22.39.4.8.56 1.23l1.35.34a1.4 1.4 0 0 1 1.06 1.36v2.04a1.4 1.4 0 0 1-1.06 1.36l-1.35.34c-.16.43-.34.84-.56 1.23l.72 1.22a1.4 1.4 0 0 1-.22 1.71l-1.44 1.44a1.4 1.4 0 0 1-1.71.22l-1.22-.72c-.39.22-.8.4-1.23.56l-.34 1.35a1.4 1.4 0 0 1-1.36 1.06h-2.04a1.4 1.4 0 0 1-1.36-1.06l-.34-1.35a7.36 7.36 0 0 1-1.23-.56l-1.22.72a1.4 1.4 0 0 1-1.71-.22l-1.44-1.44a1.4 1.4 0 0 1-.22-1.71l.72-1.22a7.36 7.36 0 0 1-.56-1.23l-1.35-.34a1.4 1.4 0 0 1-1.06-1.36v-2.04a1.4 1.4 0 0 1 1.06-1.36l1.35-.34c.16-.43.34-.84.56-1.23l-.72-1.22a1.4 1.4 0 0 1 .22-1.71l1.44-1.44a1.4 1.4 0 0 1 1.71-.22l1.22.72c.39-.22.8-.4 1.23-.56l.34-1.35a1.4 1.4 0 0 1 1.36-1.06Zm1.02 6.5a3.25 3.25 0 1 0 0 6.5 3.25 3.25 0 0 0 0-6.5Z" />
            </svg>
        </span>`;

    const closeIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6.22 5.81a.8.8 0 0 1 1.14 0L12 10.45l4.64-4.64a.8.8 0 1 1 1.13 1.13L13.13 11.6l4.64 4.64a.8.8 0 1 1-1.13 1.13L12 12.73l-4.64 4.64a.8.8 0 1 1-1.14-1.13l4.65-4.64-4.65-4.65a.8.8 0 0 1 0-1.14Z" />
        </svg>`;

    const ensureSettingsModal = () => {
        let modal = document.getElementById('portal-settings-modal');
        if (modal) return modal;
        modal = document.createElement('div');
        modal.id = 'portal-settings-modal';
        modal.className = 'portal-settings-modal';
        modal.hidden = true;
        modal.innerHTML = `
            <div class="portal-settings-modal__backdrop" data-portal-settings-close></div>
            <div class="portal-settings-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="portal-settings-title">
                <div class="portal-settings-modal__header">
                    <h3 class="portal-settings-modal__title" id="portal-settings-title">
                        <span class="portal-settings-modal__icon" aria-hidden="true">${gearIcon.replace('portal-app__link-icon', '')}</span>
                        Configuração
                    </h3>
                    <button type="button" class="portal-settings-modal__close" aria-label="Fechar" data-portal-settings-close>${closeIcon}</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.querySelectorAll('[data-portal-settings-close]').forEach((button) => {
            button.addEventListener('click', () => {
                modal.hidden = true;
                modal.setAttribute('aria-hidden', 'true');
            });
        });
        return modal;
    };

    const openSettings = () => {
        const modal = ensureSettingsModal();
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        modal.querySelector('[data-portal-settings-close]')?.focus();
    };

    const run = () => {
        const menu = document.querySelector('.portal-app__header-links');
        if (!menu) return;
        let button = menu.querySelector('[data-portal-settings]');
        if (!button) {
            button = document.createElement('button');
            button.type = 'button';
            button.className = 'portal-app__link';
            button.dataset.portalSettings = 'true';
            button.innerHTML = `${gearIcon}<span class="portal-app__link-text">Configuração</span>`;
            button.addEventListener('click', openSettings);
        }
        menu.appendChild(button);
        ensureSettingsModal();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
