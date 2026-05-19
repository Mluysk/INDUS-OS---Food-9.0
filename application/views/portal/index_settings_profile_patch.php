<?php
include __DIR__.'/index_header_lift_patch.php';
?>
<style>
    html body .portal-app__header-links [data-portal-settings] {
        width: 48px;
        min-width: 48px;
        height: 48px;
        padding: 0 !important;
        display: inline-grid !important;
        place-items: center;
        border-radius: 999px;
    }

    html body .portal-app__header-links [data-portal-settings] .portal-app__link-text {
        display: none !important;
    }

    html body .portal-app__header-links [data-portal-settings] .portal-app__link-icon {
        width: 22px;
        height: 22px;
        display: inline-flex;
    }

    html body .portal-app__header-links [data-portal-settings] .portal-app__link-icon svg {
        width: 22px;
        height: 22px;
        display: block;
        fill: currentColor;
        shape-rendering: geometricPrecision;
    }

    html body .portal-settings-modal__dialog {
        width: min(560px, 100%) !important;
    }

    html body .portal-settings-form {
        display: grid;
        gap: 0.85rem;
    }

    html body .portal-settings-field {
        display: grid;
        gap: 0.38rem;
    }

    html body .portal-settings-field label {
        color: var(--text-muted, #64748b);
        font-size: 0.88rem;
        font-weight: 850;
    }

    html body .portal-settings-field input {
        width: 100%;
        min-height: 48px;
        border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.1));
        border-radius: 14px;
        background: color-mix(in srgb, var(--portal-surface, #fff) 92%, transparent);
        color: var(--text, #0f172a);
        font: inherit;
        font-weight: 750;
        padding: 0.78rem 0.95rem;
        outline: none;
    }

    html body .portal-settings-field input:focus {
        border-color: color-mix(in srgb, var(--primary, #2563eb) 58%, transparent);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary, #2563eb) 12%, transparent);
    }

    html body .portal-settings-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }

    html body .portal-settings-edit-area {
        display: none;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
        padding-top: 0.15rem;
    }

    html body .portal-settings-edit-area.is-open {
        display: grid;
    }

    html body .portal-settings-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.65rem;
        flex-wrap: wrap;
        padding-top: 0.25rem;
    }

    html body .portal-settings-feedback {
        min-height: 1.2rem;
        color: #047857;
        font-size: 0.88rem;
        font-weight: 850;
    }

    @media (max-width: 640px) {
        html body .portal-settings-grid,
        html body .portal-settings-edit-area {
            grid-template-columns: 1fr;
        }
    }
</style>
<script>
(function () {
    const gearOnlyIcon = `
        <span class="portal-app__link-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 8.35a3.65 3.65 0 1 0 0 7.3 3.65 3.65 0 0 0 0-7.3Zm-1.02-6.1h2.04c.66 0 1.24.45 1.4 1.09l.31 1.22c.37.14.73.29 1.08.49l1.11-.65c.57-.34 1.29-.25 1.76.22l1.44 1.44c.47.47.56 1.19.22 1.76l-.65 1.11c.2.35.36.71.49 1.08l1.22.31c.64.16 1.09.74 1.09 1.4v2.04c0 .66-.45 1.24-1.09 1.4l-1.22.31c-.13.37-.29.73-.49 1.08l.65 1.11c.34.57.25 1.29-.22 1.76l-1.44 1.44c-.47.47-1.19.56-1.76.22l-1.11-.65c-.35.2-.71.36-1.08.49l-.31 1.22c-.16.64-.74 1.09-1.4 1.09h-2.04c-.66 0-1.24-.45-1.4-1.09l-.31-1.22a7.9 7.9 0 0 1-1.08-.49l-1.11.65c-.57.34-1.29.25-1.76-.22l-1.44-1.44a1.44 1.44 0 0 1-.22-1.76l.65-1.11a7.9 7.9 0 0 1-.49-1.08l-1.22-.31a1.44 1.44 0 0 1-1.09-1.4v-2.04c0-.66.45-1.24 1.09-1.4l1.22-.31c.13-.37.29-.73.49-1.08l-.65-1.11a1.44 1.44 0 0 1 .22-1.76l1.44-1.44c.47-.47 1.19-.56 1.76-.22l1.11.65c.35-.2.71-.36 1.08-.49l.31-1.22c.16-.64.74-1.09 1.4-1.09Z" />
            </svg>
        </span>`;

    const escapeHtml = (value) => String(value || '').replace(/[&<>"]/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;'
    }[char]));

    const readCliente = () => {
        try {
            return JSON.parse(localStorage.getItem('portal-cliente') || '{}') || {};
        } catch (error) {
            return {};
        }
    };

    const writeCliente = (cliente) => {
        localStorage.setItem('portal-cliente', JSON.stringify(cliente || {}));
        const name = cliente.nome_fantasia || cliente.nome_loja || cliente.razao_social || cliente.nome || 'Cliente';
        const badge = document.querySelector('[data-cliente-badge]');
        const title = document.querySelector('[data-cliente-nome]');
        if (badge) badge.textContent = name;
        if (title) title.textContent = name;
    };

    const fieldValue = (selector) => document.querySelector(selector)?.value.trim() || '';

    const buildSettingsBody = () => {
        const cliente = readCliente();
        return `
            <form class="portal-settings-form" data-portal-profile-form>
                <div class="portal-settings-grid">
                    <div class="portal-settings-field">
                        <label for="portal-settings-store">Nome da loja</label>
                        <input id="portal-settings-store" type="text" value="${escapeHtml(cliente.nome_loja || cliente.nome_fantasia || cliente.razao_social || cliente.nome || '')}" placeholder="Nome da loja">
                    </div>
                    <div class="portal-settings-field">
                        <label for="portal-settings-branch">Nome da filial</label>
                        <input id="portal-settings-branch" type="text" value="${escapeHtml(cliente.nome_filial || cliente.filial_nome || cliente.im || '')}" placeholder="Nome da filial">
                    </div>
                </div>
                <button type="button" class="portal-btn portal-btn--ghost" data-portal-edit-register>Editar próprio cadastro</button>
                <div class="portal-settings-edit-area" data-portal-edit-area>
                    <div class="portal-settings-field">
                        <label for="portal-settings-email">E-mail</label>
                        <input id="portal-settings-email" type="email" value="${escapeHtml(cliente.email || '')}" placeholder="email@empresa.com">
                    </div>
                    <div class="portal-settings-field">
                        <label for="portal-settings-contact">Contato</label>
                        <input id="portal-settings-contact" type="text" value="${escapeHtml(cliente.telefone || cliente.contato || '')}" placeholder="Contato">
                    </div>
                    <div class="portal-settings-field">
                        <label for="portal-settings-whatsapp">WhatsApp</label>
                        <input id="portal-settings-whatsapp" type="text" value="${escapeHtml(cliente.whatsapp || '')}" placeholder="WhatsApp">
                    </div>
                    <div class="portal-settings-field">
                        <label for="portal-settings-city">Cidade</label>
                        <input id="portal-settings-city" type="text" value="${escapeHtml(cliente.cidade || '')}" placeholder="Cidade">
                    </div>
                </div>
                <div class="portal-settings-option">
                    <div>
                        <strong>Tema do portal</strong>
                        <small>Alternar entre modo claro e noturno.</small>
                    </div>
                    <button type="button" class="portal-btn portal-btn--ghost" data-portal-settings-theme>Alternar tema</button>
                </div>
                <div class="portal-settings-feedback" data-portal-settings-feedback aria-live="polite"></div>
                <div class="portal-settings-actions">
                    <button type="button" class="portal-btn portal-btn--ghost" data-portal-settings-close>Cancelar</button>
                    <button type="submit" class="portal-btn portal-btn--primary">Salvar</button>
                </div>
            </form>
        `;
    };

    const replaceSettingsButton = () => {
        const menu = document.querySelector('.portal-app__header-links');
        const oldButton = menu?.querySelector('[data-portal-settings]');
        if (!menu || !oldButton) return null;
        const button = oldButton.cloneNode(false);
        button.type = 'button';
        button.className = oldButton.className;
        button.dataset.portalSettings = 'true';
        button.setAttribute('aria-label', 'Configuração');
        button.title = 'Configuração';
        button.innerHTML = `${gearOnlyIcon}<span class="portal-app__link-text">Configuração</span>`;
        oldButton.replaceWith(button);
        return button;
    };

    const wireModal = () => {
        const modal = document.getElementById('portal-settings-modal');
        if (!modal) return;
        const dialog = modal.querySelector('.portal-settings-modal__dialog');
        if (!dialog || modal.dataset.profileReady === '1') return;
        modal.dataset.profileReady = '1';

        const openModal = () => {
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
            const option = dialog.querySelector('.portal-settings-option');
            const form = dialog.querySelector('[data-portal-profile-form]');
            if (!form) {
                option?.insertAdjacentHTML('beforebegin', buildSettingsBody());
                option?.remove();
            } else {
                form.outerHTML = buildSettingsBody();
            }
            bindForm(modal);
            modal.querySelector('#portal-settings-store')?.focus();
        };

        const settingsButton = replaceSettingsButton();
        settingsButton?.addEventListener('click', openModal);
    };

    const bindForm = (modal) => {
        modal.querySelectorAll('[data-portal-settings-close]').forEach((item) => {
            item.addEventListener('click', () => {
                modal.hidden = true;
                modal.setAttribute('aria-hidden', 'true');
            });
        });
        modal.querySelector('[data-portal-settings-theme]')?.addEventListener('click', () => {
            document.querySelector('.portal-theme-button')?.click();
        });
        modal.querySelector('[data-portal-edit-register]')?.addEventListener('click', () => {
            modal.querySelector('[data-portal-edit-area]')?.classList.toggle('is-open');
        });
        modal.querySelector('[data-portal-profile-form]')?.addEventListener('submit', (event) => {
            event.preventDefault();
            const cliente = readCliente();
            cliente.nome_loja = fieldValue('#portal-settings-store');
            cliente.nome_fantasia = cliente.nome_loja;
            cliente.nome_filial = fieldValue('#portal-settings-branch');
            cliente.filial_nome = cliente.nome_filial;
            cliente.email = fieldValue('#portal-settings-email') || cliente.email || '';
            cliente.telefone = fieldValue('#portal-settings-contact') || cliente.telefone || cliente.contato || '';
            cliente.contato = cliente.telefone;
            cliente.whatsapp = fieldValue('#portal-settings-whatsapp') || cliente.whatsapp || '';
            cliente.cidade = fieldValue('#portal-settings-city') || cliente.cidade || '';
            writeCliente(cliente);
            const feedback = modal.querySelector('[data-portal-settings-feedback]');
            if (feedback) feedback.textContent = 'Configurações salvas.';
            window.setTimeout(() => {
                if (feedback) feedback.textContent = '';
            }, 3900);
        });
    };

    const run = () => {
        window.setTimeout(wireModal, 0);
        window.setTimeout(wireModal, 120);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
