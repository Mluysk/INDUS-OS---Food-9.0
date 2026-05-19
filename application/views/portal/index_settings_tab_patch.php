<?php
include __DIR__.'/index_icons_refined_patch.php';
?>
<style>
    html body #portal-settings-modal {
        display: none !important;
    }

    html body .portal-settings-tab-card {
        border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.08));
        border-radius: 24px;
        background: var(--portal-card-bg, #fff);
        box-shadow: 0 24px 62px -42px rgba(15, 23, 42, 0.5);
        padding: 1.55rem;
    }

    html body .portal-settings-tab-card__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    html body .portal-settings-tab-card__head h3 {
        margin: 0;
        color: var(--text, #0f172a);
        font-size: 1.45rem;
        font-weight: 950;
    }

    html body .portal-settings-tab-card__head p {
        margin: 0.28rem 0 0;
        color: var(--text-muted, #64748b);
        font-weight: 650;
    }

    html body .portal-settings-tab-card .portal-settings-option {
        margin-top: 0.25rem;
    }
</style>
<script>
(function () {
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
        window.dispatchEvent(new StorageEvent('storage', { key: 'portal-cliente' }));
    };

    const fieldValue = (root, selector) => root.querySelector(selector)?.value.trim() || '';

    const settingsForm = () => {
        const cliente = readCliente();
        return `
            <section class="portal-panel" id="portal-tab-configuracao" data-portal-tab-panel="configuracao" hidden>
                <div class="portal-settings-tab-card">
                    <div class="portal-settings-tab-card__head">
                        <div>
                            <h3>Configuração</h3>
                            <p>Personalize o nome exibido no portal e ajuste seus dados de cadastro.</p>
                        </div>
                    </div>
                    <form class="portal-settings-form" data-portal-settings-tab-form>
                        <div class="portal-settings-grid">
                            <div class="portal-settings-field">
                                <label for="portal-tab-store">Nome da loja</label>
                                <input id="portal-tab-store" type="text" value="${escapeHtml(cliente.nome_loja || cliente.nome_fantasia || cliente.razao_social || cliente.nome || '')}" placeholder="Nome da loja">
                            </div>
                            <div class="portal-settings-field">
                                <label for="portal-tab-branch">Nome da filial</label>
                                <input id="portal-tab-branch" type="text" value="${escapeHtml(cliente.nome_filial || cliente.filial_nome || cliente.im || '')}" placeholder="Nome da filial">
                            </div>
                        </div>
                        <button type="button" class="portal-btn portal-btn--ghost" data-portal-tab-edit-register>Editar próprio cadastro</button>
                        <div class="portal-settings-edit-area" data-portal-tab-edit-area>
                            <div class="portal-settings-field">
                                <label for="portal-tab-email">E-mail</label>
                                <input id="portal-tab-email" type="email" value="${escapeHtml(cliente.email || '')}" placeholder="email@empresa.com">
                            </div>
                            <div class="portal-settings-field">
                                <label for="portal-tab-contact">Contato</label>
                                <input id="portal-tab-contact" type="text" value="${escapeHtml(cliente.telefone || cliente.contato || '')}" placeholder="Contato">
                            </div>
                            <div class="portal-settings-field">
                                <label for="portal-tab-whatsapp">WhatsApp</label>
                                <input id="portal-tab-whatsapp" type="text" value="${escapeHtml(cliente.whatsapp || '')}" placeholder="WhatsApp">
                            </div>
                            <div class="portal-settings-field">
                                <label for="portal-tab-city">Cidade</label>
                                <input id="portal-tab-city" type="text" value="${escapeHtml(cliente.cidade || '')}" placeholder="Cidade">
                            </div>
                        </div>
                        <div class="portal-settings-option">
                            <div>
                                <strong>Tema do portal</strong>
                                <small>Alternar entre modo claro e noturno.</small>
                            </div>
                            <button type="button" class="portal-btn portal-btn--ghost" data-portal-tab-theme>Alternar tema</button>
                        </div>
                        <div class="portal-settings-feedback" data-portal-tab-feedback aria-live="polite"></div>
                        <div class="portal-settings-actions">
                            <button type="button" class="portal-btn portal-btn--ghost" data-portal-tab-go="novo-pedido">Voltar</button>
                            <button type="submit" class="portal-btn portal-btn--primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </section>
        `;
    };

    const setActivePortalPanel = (target) => {
        const panels = Array.from(document.querySelectorAll('[data-portal-tab-panel]'));
        const buttons = Array.from(document.querySelectorAll('[data-portal-tab-trigger]'));
        panels.forEach((panel) => {
            if (panel.dataset.portalTabPanel === target) {
                panel.removeAttribute('hidden');
            } else {
                panel.setAttribute('hidden', '');
            }
        });
        buttons.forEach((button) => {
            const active = button.dataset.portalTabTrigger === target;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        if (target === 'pedidos') {
            document.querySelector('[data-portal-tab-trigger="pedidos"]')?.click();
        }
    };

    const bindSettingsForm = () => {
        const form = document.querySelector('[data-portal-settings-tab-form]');
        if (!form || form.dataset.bound === '1') return;
        form.dataset.bound = '1';
        form.querySelector('[data-portal-tab-edit-register]')?.addEventListener('click', () => {
            form.querySelector('[data-portal-tab-edit-area]')?.classList.toggle('is-open');
        });
        form.querySelector('[data-portal-tab-theme]')?.addEventListener('click', () => {
            document.querySelector('.portal-theme-button')?.click();
        });
        form.querySelector('[data-portal-tab-go]')?.addEventListener('click', (event) => {
            setActivePortalPanel(event.currentTarget.dataset.portalTabGo || 'novo-pedido');
        });
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const cliente = readCliente();
            cliente.nome_loja = fieldValue(form, '#portal-tab-store');
            cliente.nome_fantasia = cliente.nome_loja;
            cliente.nome_filial = fieldValue(form, '#portal-tab-branch');
            cliente.filial_nome = cliente.nome_filial;
            cliente.email = fieldValue(form, '#portal-tab-email') || cliente.email || '';
            cliente.telefone = fieldValue(form, '#portal-tab-contact') || cliente.telefone || cliente.contato || '';
            cliente.contato = cliente.telefone;
            cliente.whatsapp = fieldValue(form, '#portal-tab-whatsapp') || cliente.whatsapp || '';
            cliente.cidade = fieldValue(form, '#portal-tab-city') || cliente.cidade || '';
            writeCliente(cliente);
            const feedback = form.querySelector('[data-portal-tab-feedback]');
            if (feedback) feedback.textContent = 'Configurações salvas.';
            window.setTimeout(() => {
                if (feedback) feedback.textContent = '';
            }, 3900);
        });
    };

    const prepareSettingsTab = () => {
        const panels = document.querySelector('.portal-app__panels');
        if (!panels) return;
        const oldPanel = document.getElementById('portal-tab-configuracao');
        if (oldPanel) oldPanel.remove();
        panels.insertAdjacentHTML('beforeend', settingsForm());
        bindSettingsForm();

        const oldButton = document.querySelector('.portal-app__header-links [data-portal-settings]');
        if (!oldButton) return;
        const button = oldButton.cloneNode(true);
        button.dataset.portalJump = 'configuracao';
        button.dataset.portalSettings = 'true';
        button.setAttribute('aria-label', 'Configuração');
        button.title = 'Configuração';
        oldButton.replaceWith(button);
        button.addEventListener('click', () => setActivePortalPanel('configuracao'));
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            prepareSettingsTab();
            window.setTimeout(prepareSettingsTab, 180);
        }, { once: true });
    } else {
        prepareSettingsTab();
        window.setTimeout(prepareSettingsTab, 180);
    }
})();
</script>
