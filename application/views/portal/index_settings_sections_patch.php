<?php
include __DIR__.'/index_settings_tab_patch.php';
?>
<style>
    html body .portal-settings-subtabs {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.35rem;
        border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.08));
        border-radius: 999px;
        background: color-mix(in srgb, var(--portal-surface, #fff) 84%, transparent);
        margin-bottom: 1rem;
    }

    html body .portal-settings-subtab {
        min-height: 42px;
        border: 0;
        border-radius: 999px;
        padding: 0.65rem 1.15rem;
        background: transparent;
        color: var(--text-muted, #64748b);
        font: inherit;
        font-weight: 900;
        cursor: pointer;
    }

    html body .portal-settings-subtab.is-active {
        background: var(--primary, #dc2626);
        color: #fff;
        box-shadow: 0 14px 32px -22px var(--primary, #dc2626);
    }

    html body .portal-settings-section[hidden] {
        display: none !important;
    }

    html body .portal-settings-section {
        display: grid;
        gap: 0.85rem;
    }

    html body .portal-settings-section-title {
        margin: 0 0 0.2rem;
        color: var(--text, #0f172a);
        font-size: 1.05rem;
        font-weight: 950;
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

    const value = (root, selector) => root.querySelector(selector)?.value.trim() || '';

    const settingsMarkup = () => {
        const cliente = readCliente();
        return `
            <section class="portal-panel" id="portal-tab-configuracao" data-portal-tab-panel="configuracao" hidden>
                <div class="portal-settings-tab-card">
                    <div class="portal-settings-tab-card__head">
                        <div>
                            <h3>Configuração</h3>
                            <p>Atualize seu cadastro e personalize como sua loja aparece no portal.</p>
                        </div>
                    </div>
                    <div class="portal-settings-subtabs" role="tablist" aria-label="Configurações do portal">
                        <button type="button" class="portal-settings-subtab is-active" data-settings-section-trigger="cadastro" aria-selected="true">Cadastro</button>
                        <button type="button" class="portal-settings-subtab" data-settings-section-trigger="personalizacao" aria-selected="false">Personalização</button>
                    </div>
                    <form class="portal-settings-form" data-portal-settings-sections-form>
                        <div class="portal-settings-section" data-settings-section="cadastro">
                            <h4 class="portal-settings-section-title">Cadastro</h4>
                            <div class="portal-settings-grid">
                                <div class="portal-settings-field">
                                    <label for="portal-settings-name">Nome / Razão social</label>
                                    <input id="portal-settings-name" type="text" value="${escapeHtml(cliente.razao_social || cliente.nome || '')}" placeholder="Nome / Razão social">
                                </div>
                                <div class="portal-settings-field">
                                    <label for="portal-settings-doc">CNPJ / CPF</label>
                                    <input id="portal-settings-doc" type="text" value="${escapeHtml(cliente.documento || cliente.cnpj || cliente.cpf || '')}" placeholder="Documento">
                                </div>
                                <div class="portal-settings-field">
                                    <label for="portal-settings-email-main">E-mail</label>
                                    <input id="portal-settings-email-main" type="email" value="${escapeHtml(cliente.email || '')}" placeholder="email@empresa.com">
                                </div>
                                <div class="portal-settings-field">
                                    <label for="portal-settings-contact-main">Contato</label>
                                    <input id="portal-settings-contact-main" type="text" value="${escapeHtml(cliente.telefone || cliente.contato || '')}" placeholder="Contato">
                                </div>
                                <div class="portal-settings-field">
                                    <label for="portal-settings-whatsapp-main">WhatsApp</label>
                                    <input id="portal-settings-whatsapp-main" type="text" value="${escapeHtml(cliente.whatsapp || '')}" placeholder="WhatsApp">
                                </div>
                                <div class="portal-settings-field">
                                    <label for="portal-settings-city-main">Cidade</label>
                                    <input id="portal-settings-city-main" type="text" value="${escapeHtml(cliente.cidade || '')}" placeholder="Cidade">
                                </div>
                            </div>
                        </div>
                        <div class="portal-settings-section" data-settings-section="personalizacao" hidden>
                            <h4 class="portal-settings-section-title">Personalização</h4>
                            <div class="portal-settings-grid">
                                <div class="portal-settings-field">
                                    <label for="portal-settings-store-main">Nome da loja</label>
                                    <input id="portal-settings-store-main" type="text" value="${escapeHtml(cliente.nome_loja || cliente.nome_fantasia || cliente.razao_social || cliente.nome || '')}" placeholder="Nome da loja">
                                </div>
                                <div class="portal-settings-field">
                                    <label for="portal-settings-branch-main">Nome da filial</label>
                                    <input id="portal-settings-branch-main" type="text" value="${escapeHtml(cliente.nome_filial || cliente.filial_nome || cliente.im || '')}" placeholder="Nome da filial">
                                </div>
                            </div>
                            <div class="portal-settings-option">
                                <div>
                                    <strong>Tema do portal</strong>
                                    <small>Alternar entre modo claro e noturno.</small>
                                </div>
                                <button type="button" class="portal-btn portal-btn--ghost" data-settings-theme>Alternar tema</button>
                            </div>
                        </div>
                        <div class="portal-settings-feedback" data-settings-feedback aria-live="polite"></div>
                        <div class="portal-settings-actions">
                            <button type="button" class="portal-btn portal-btn--ghost" data-settings-back="novo-pedido">Voltar</button>
                            <button type="submit" class="portal-btn portal-btn--primary">Atualizar cadastro</button>
                        </div>
                    </form>
                </div>
            </section>
        `;
    };

    const setSettingsSection = (section) => {
        document.querySelectorAll('[data-settings-section]').forEach((panel) => {
            panel.hidden = panel.dataset.settingsSection !== section;
        });
        document.querySelectorAll('[data-settings-section-trigger]').forEach((button) => {
            const active = button.dataset.settingsSectionTrigger === section;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    };

    const setPortalPanel = (target) => {
        document.querySelectorAll('[data-portal-tab-panel]').forEach((panel) => {
            panel.hidden = panel.dataset.portalTabPanel !== target;
        });
    };

    const bind = () => {
        const form = document.querySelector('[data-portal-settings-sections-form]');
        if (!form || form.dataset.sectionsBound === '1') return;
        form.dataset.sectionsBound = '1';
        document.querySelectorAll('[data-settings-section-trigger]').forEach((button) => {
            button.addEventListener('click', () => setSettingsSection(button.dataset.settingsSectionTrigger || 'cadastro'));
        });
        form.querySelector('[data-settings-theme]')?.addEventListener('click', () => {
            document.querySelector('.portal-theme-button')?.click();
        });
        form.querySelector('[data-settings-back]')?.addEventListener('click', (event) => {
            setPortalPanel(event.currentTarget.dataset.settingsBack || 'novo-pedido');
        });
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const cliente = readCliente();
            cliente.razao_social = value(form, '#portal-settings-name') || cliente.razao_social || cliente.nome || '';
            cliente.nome = cliente.razao_social;
            cliente.documento = value(form, '#portal-settings-doc') || cliente.documento || cliente.cnpj || cliente.cpf || '';
            cliente.email = value(form, '#portal-settings-email-main') || cliente.email || '';
            cliente.telefone = value(form, '#portal-settings-contact-main') || cliente.telefone || cliente.contato || '';
            cliente.contato = cliente.telefone;
            cliente.whatsapp = value(form, '#portal-settings-whatsapp-main') || cliente.whatsapp || '';
            cliente.cidade = value(form, '#portal-settings-city-main') || cliente.cidade || '';
            cliente.nome_loja = value(form, '#portal-settings-store-main') || cliente.nome_loja || cliente.nome_fantasia || cliente.razao_social || '';
            cliente.nome_fantasia = cliente.nome_loja;
            cliente.nome_filial = value(form, '#portal-settings-branch-main') || cliente.nome_filial || cliente.filial_nome || '';
            cliente.filial_nome = cliente.nome_filial;
            writeCliente(cliente);
            const feedback = form.querySelector('[data-settings-feedback]');
            if (feedback) feedback.textContent = 'Cadastro atualizado.';
            window.setTimeout(() => {
                if (feedback) feedback.textContent = '';
            }, 3900);
        });
    };

    const apply = () => {
        const panels = document.querySelector('.portal-app__panels');
        if (!panels) return;
        document.getElementById('portal-tab-configuracao')?.remove();
        panels.insertAdjacentHTML('beforeend', settingsMarkup());
        bind();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            apply();
            window.setTimeout(apply, 220);
        }, { once: true });
    } else {
        apply();
        window.setTimeout(apply, 220);
    }
})();
</script>
