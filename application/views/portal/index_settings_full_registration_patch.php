<?php
include __DIR__.'/index_settings_sections_patch.php';
?>
<style>
    html body .portal-settings-doc-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.35rem 0;
        color: var(--text-muted, #64748b);
        font-weight: 850;
    }

    html body .portal-settings-doc-toggle__buttons,
    html body .portal-settings-contact-type {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        flex-wrap: wrap;
    }

    html body .portal-settings-doc-btn {
        border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.08));
        border-radius: 999px;
        min-height: 36px;
        padding: 0.45rem 0.9rem;
        background: color-mix(in srgb, var(--portal-surface, #fff) 86%, transparent);
        color: var(--text-muted, #64748b);
        font: inherit;
        font-weight: 900;
        cursor: pointer;
    }

    html body .portal-settings-doc-btn.is-active {
        border-color: var(--primary, #dc2626);
        background: var(--primary, #dc2626);
        color: #fff;
    }

    html body .portal-settings-cnpj-hint {
        margin: -0.25rem 0 0.15rem;
        color: var(--text-muted, #64748b);
        font-size: 0.86rem;
        font-weight: 750;
    }

    html body .portal-settings-cnpj-hint::before {
        content: '*';
        color: var(--primary, #dc2626);
        margin-right: 0.35rem;
    }

    html body .portal-settings-contact-type {
        gap: 0.85rem;
        color: var(--text-muted, #64748b);
        font-weight: 780;
    }

    html body .portal-settings-contact-type label {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        cursor: pointer;
    }

    html body .portal-settings-contact-type input {
        accent-color: var(--primary, #dc2626);
    }

    html body .portal-settings-branch-row {
        display: grid;
        grid-template-columns: max-content minmax(220px, 1fr);
        align-items: center;
        gap: 0.75rem;
        padding: 0.9rem;
        border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.08));
        border-radius: 18px;
        background: color-mix(in srgb, var(--portal-surface, #fff) 82%, transparent);
    }

    html body .portal-settings-branch-switch {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        font-weight: 900;
        color: var(--text, #0f172a);
        cursor: pointer;
    }

    html body .portal-settings-branch-switch input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    html body .portal-settings-branch-pill {
        width: 72px;
        height: 34px;
        border-radius: 999px;
        position: relative;
        display: inline-flex;
        align-items: center;
        padding: 0 10px;
        background: #d1d5db;
        color: #475569;
        font-size: 0.76rem;
        font-weight: 950;
    }

    html body .portal-settings-branch-pill::before {
        content: 'Não';
    }

    html body .portal-settings-branch-pill::after {
        content: '';
        position: absolute;
        top: 4px;
        left: 4px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 6px 14px rgba(15, 23, 42, 0.2);
        transition: transform 0.18s ease;
    }

    html body .portal-settings-branch-switch input:checked + .portal-settings-branch-pill {
        background: var(--primary, #dc2626);
        color: #fff;
    }

    html body .portal-settings-branch-switch input:checked + .portal-settings-branch-pill::before {
        content: 'Sim';
    }

    html body .portal-settings-branch-switch input:checked + .portal-settings-branch-pill::after {
        transform: translateX(38px);
    }

    html body .portal-settings-branch-name[hidden],
    html body .portal-settings-doc-field[hidden] {
        display: none !important;
    }

    html body .portal-settings-branch-name {
        min-width: 0;
    }

    @media (max-width: 640px) {
        html body .portal-settings-branch-row {
            grid-template-columns: 1fr;
        }
    }

    html body .portal-settings-uf-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    html body .portal-settings-uf-wrap input {
        padding-right: 4.1rem;
        text-transform: uppercase;
    }

    html body .portal-settings-state-flag {
        position: absolute;
        right: 0.68rem;
        width: 40px;
        height: 26px;
        border-radius: 7px;
        object-fit: cover;
        border: 1px solid rgba(15, 23, 42, 0.14);
        box-shadow: 0 8px 18px -12px rgba(15, 23, 42, 0.65);
        background: rgba(255, 255, 255, 0.88);
        display: none;
    }

    html body .portal-settings-state-flag.is-visible {
        display: block;
    }
</style>
<script>
(function () {
    const onlyDigits = (value) => String(value || '').replace(/\D+/g, '');
    const escapeHtml = (value) => String(value || '').replace(/[&<>"]/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;'
    }[char]));

    const stateFlagUrls = {
        AC: 'https://upload.wikimedia.org/wikipedia/commons/4/4c/Bandeira_do_Acre.svg', AL: 'https://upload.wikimedia.org/wikipedia/commons/8/88/Bandeira_de_Alagoas.svg', AP: 'https://upload.wikimedia.org/wikipedia/commons/0/0c/Bandeira_do_Amap%C3%A1.svg', AM: 'https://upload.wikimedia.org/wikipedia/commons/6/6b/Bandeira_do_Amazonas.svg', BA: 'https://upload.wikimedia.org/wikipedia/commons/2/28/Bandeira_da_Bahia.svg', CE: 'https://upload.wikimedia.org/wikipedia/commons/2/2e/Bandeira_do_Cear%C3%A1.svg', DF: 'https://upload.wikimedia.org/wikipedia/commons/3/3c/Bandeira_do_Distrito_Federal_%28Brasil%29.svg', ES: 'https://upload.wikimedia.org/wikipedia/commons/4/43/Bandeira_do_Esp%C3%ADrito_Santo.svg', GO: 'https://upload.wikimedia.org/wikipedia/commons/b/be/Flag_of_Goi%C3%A1s.svg', MA: 'https://upload.wikimedia.org/wikipedia/commons/4/45/Bandeira_do_Maranh%C3%A3o.svg', MT: 'https://upload.wikimedia.org/wikipedia/commons/0/0b/Bandeira_de_Mato_Grosso.svg', MS: 'https://upload.wikimedia.org/wikipedia/commons/6/64/Bandeira_de_Mato_Grosso_do_Sul.svg', MG: 'https://upload.wikimedia.org/wikipedia/commons/f/f4/Bandeira_de_Minas_Gerais.svg', PA: 'https://upload.wikimedia.org/wikipedia/commons/0/02/Bandeira_do_Par%C3%A1.svg', PB: 'https://upload.wikimedia.org/wikipedia/commons/b/bb/Bandeira_da_Para%C3%ADba.svg', PR: 'https://upload.wikimedia.org/wikipedia/commons/9/93/Bandeira_do_Paran%C3%A1.svg', PE: 'https://upload.wikimedia.org/wikipedia/commons/5/59/Bandeira_de_Pernambuco.svg', PI: 'https://upload.wikimedia.org/wikipedia/commons/3/33/Bandeira_do_Piau%C3%AD.svg', RJ: 'https://upload.wikimedia.org/wikipedia/commons/7/73/Bandeira_do_estado_do_Rio_de_Janeiro.svg', RN: 'https://upload.wikimedia.org/wikipedia/commons/3/30/Bandeira_do_Rio_Grande_do_Norte.svg', RS: 'https://upload.wikimedia.org/wikipedia/commons/6/63/Bandeira_do_Rio_Grande_do_Sul.svg', RO: 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Bandeira_de_Rond%C3%B4nia.svg', RR: 'https://upload.wikimedia.org/wikipedia/commons/9/98/Bandeira_de_Roraima.svg', SC: 'https://upload.wikimedia.org/wikipedia/commons/1/1a/Bandeira_de_Santa_Catarina.svg', SP: 'https://upload.wikimedia.org/wikipedia/commons/2/2b/Bandeira_do_estado_de_S%C3%A3o_Paulo.svg', SE: 'https://upload.wikimedia.org/wikipedia/commons/b/be/Bandeira_de_Sergipe.svg', TO: 'https://upload.wikimedia.org/wikipedia/commons/f/ff/Bandeira_do_Tocantins.svg'
    };

    const readCliente = () => {
        try { return JSON.parse(localStorage.getItem('portal-cliente') || '{}') || {}; } catch (error) { return {}; }
    };

    const first = (...values) => values.map((value) => String(value || '').trim()).find(Boolean) || '';
    const value = (root, selector) => root.querySelector(selector)?.value.trim() || '';
    const checked = (root, selector) => Boolean(root.querySelector(selector)?.checked);
    const radioValue = (root, name) => root.querySelector(`input[name="${name}"]:checked`)?.value || '';

    const writeCliente = (cliente) => {
        localStorage.setItem('portal-cliente', JSON.stringify(cliente || {}));
        const name = cliente.nome_fantasia || cliente.nome_loja || cliente.razao_social || cliente.nome || 'Cliente';
        document.querySelectorAll('[data-cliente-badge]').forEach((item) => { item.textContent = name; });
        const title = document.querySelector('[data-cliente-nome]');
        if (title && title.dataset.portalGreetingManaged !== '1') title.textContent = name;
        window.dispatchEvent(new StorageEvent('storage', { key: 'portal-cliente' }));
    };

    const formatCpf = (input) => onlyDigits(input).slice(0, 11)
        .replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    const formatCnpj = (input) => onlyDigits(input).slice(0, 14)
        .replace(/(\d{2})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1/$2').replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    const formatCep = (input) => {
        const digits = onlyDigits(input).slice(0, 8);
        return digits.length === 8 ? `${digits.slice(0, 5)}-${digits.slice(5)}` : digits;
    };
    const formatPhone = (input) => {
        const digits = onlyDigits(input).slice(0, 11);
        if (digits.length > 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
        if (digits.length > 6) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
        if (digits.length > 2) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
        return digits;
    };

    const fullSettingsMarkup = () => {
        const c = readCliente();
        const doc = first(c.documento, c.cnpj, c.cpf);
        const docType = onlyDigits(first(c.cpf)).length === 11 && onlyDigits(first(c.cnpj)).length !== 14 ? 'cpf' : 'cnpj';
        const hasBranch = Boolean(first(c.nome_filial, c.filial_nome, c.im));
        return `
            <section class="portal-panel" id="portal-tab-configuracao" data-portal-tab-panel="configuracao" hidden>
                <div class="portal-settings-tab-card">
                    <div class="portal-settings-tab-card__head">
                        <div>
                            <h3>Configuração</h3>
                            <p>Atualize seu cadastro completo e personalize como sua loja aparece no portal.</p>
                        </div>
                    </div>
                    <div class="portal-settings-subtabs" role="tablist" aria-label="Configurações do portal">
                        <button type="button" class="portal-settings-subtab is-active" data-settings-section-trigger="cadastro" aria-selected="true">Cadastro</button>
                        <button type="button" class="portal-settings-subtab" data-settings-section-trigger="personalizacao" aria-selected="false">Personalização</button>
                    </div>
                    <form class="portal-settings-form" data-portal-settings-full-form>
                        <div class="portal-settings-section" data-settings-section="cadastro">
                            <h4 class="portal-settings-section-title">Dados do responsável</h4>
                            <div class="portal-settings-grid">
                                <div class="portal-settings-field"><label for="portal-full-nome">Nome completo</label><input id="portal-full-nome" type="text" value="${escapeHtml(first(c.nome, c.responsavel))}" placeholder="Nome completo"></div>
                                <div class="portal-settings-field"><label for="portal-full-email">E-mail</label><input id="portal-full-email" type="email" value="${escapeHtml(c.email || '')}" placeholder="email@empresa.com"></div>
                                <div class="portal-settings-field"><label for="portal-full-telefone">Contato</label><input id="portal-full-telefone" type="tel" value="${escapeHtml(first(c.telefone, c.contato))}" placeholder="(11) 99999-0000"></div>
                                <div class="portal-settings-field"><label for="portal-full-whatsapp">WhatsApp</label><input id="portal-full-whatsapp" type="tel" value="${escapeHtml(c.whatsapp || '')}" placeholder="(11) 99999-0000"></div>
                            </div>
                            <div class="portal-settings-contact-type">
                                <strong>Tipo do contato</strong>
                                <label><input type="radio" name="portal_full_contato_tipo" value="celular" ${first(c.contato_tipo, 'celular') !== 'telefone' ? 'checked' : ''}> Celular</label>
                                <label><input type="radio" name="portal_full_contato_tipo" value="telefone" ${c.contato_tipo === 'telefone' ? 'checked' : ''}> Telefone</label>
                            </div>
                            <div class="portal-settings-doc-toggle">
                                <span>Documento</span>
                                <div class="portal-settings-doc-toggle__buttons">
                                    <button type="button" class="portal-settings-doc-btn ${docType === 'cnpj' ? 'is-active' : ''}" data-settings-doc="cnpj">CNPJ</button>
                                    <button type="button" class="portal-settings-doc-btn ${docType === 'cpf' ? 'is-active' : ''}" data-settings-doc="cpf">CPF</button>
                                </div>
                            </div>
                            <div class="portal-settings-grid">
                                <div class="portal-settings-field portal-settings-doc-field" data-settings-doc-field="cnpj" ${docType === 'cpf' ? 'hidden' : ''}><label for="portal-full-cnpj">CNPJ</label><input id="portal-full-cnpj" type="text" value="${escapeHtml(formatCnpj(first(c.cnpj, doc)))}" placeholder="00.000.000/0000-00"></div>
                                <div class="portal-settings-field portal-settings-doc-field" data-settings-doc-field="cpf" ${docType === 'cnpj' ? 'hidden' : ''}><label for="portal-full-cpf">CPF</label><input id="portal-full-cpf" type="text" value="${escapeHtml(formatCpf(first(c.cpf, doc)))}" placeholder="000.000.000-00"></div>
                                <div class="portal-settings-field"><label for="portal-full-razao">Razão social / Nome fiscal</label><input id="portal-full-razao" type="text" value="${escapeHtml(first(c.razao_social, c.nome))}" placeholder="Use para emissão fiscal"></div>
                                <div class="portal-settings-field"><label for="portal-full-fantasia">Nome da loja</label><input id="portal-full-fantasia" type="text" value="${escapeHtml(first(c.nome_loja, c.nome_fantasia, c.razao_social, c.nome))}" placeholder="Nome que aparece no portal"></div>
                            </div>
                            <p class="portal-settings-cnpj-hint" data-settings-cnpj-hint>Ao informar o CNPJ, os dados cadastrais disponíveis serão preenchidos automaticamente.</p>
                            <div class="portal-settings-branch-row">
                                <label class="portal-settings-branch-switch"><input type="checkbox" id="portal-full-has-branch" ${hasBranch ? 'checked' : ''}><span class="portal-settings-branch-pill" aria-hidden="true"></span><span>Tem filial?</span></label>
                                <div class="portal-settings-field portal-settings-branch-name" ${hasBranch ? '' : 'hidden'}><label for="portal-full-filial">Nome da filial</label><input id="portal-full-filial" type="text" value="${escapeHtml(first(c.nome_filial, c.filial_nome, c.im))}" placeholder="Ex.: Filial Centro"></div>
                            </div>
                            <h4 class="portal-settings-section-title">Endereço</h4>
                            <div class="portal-settings-grid">
                                <div class="portal-settings-field"><label for="portal-full-cep">CEP</label><input id="portal-full-cep" type="text" value="${escapeHtml(formatCep(c.cep || ''))}" placeholder="00000-000"></div>
                                <div class="portal-settings-field"><label for="portal-full-endereco">Endereço</label><input id="portal-full-endereco" type="text" value="${escapeHtml(c.endereco || '')}" placeholder="Rua, avenida"></div>
                                <div class="portal-settings-field"><label for="portal-full-numero">Número</label><input id="portal-full-numero" type="text" value="${escapeHtml(c.numero || '')}" placeholder="123"></div>
                                <div class="portal-settings-field"><label for="portal-full-bairro">Bairro</label><input id="portal-full-bairro" type="text" value="${escapeHtml(c.bairro || '')}" placeholder="Centro"></div>
                                <div class="portal-settings-field"><label for="portal-full-cidade">Cidade</label><input id="portal-full-cidade" type="text" value="${escapeHtml(c.cidade || '')}" placeholder="São Paulo"></div>
                                <div class="portal-settings-field"><label for="portal-full-estado">Estado (UF)</label><span class="portal-settings-uf-wrap"><input id="portal-full-estado" type="text" value="${escapeHtml(c.estado || '')}" maxlength="2" placeholder="SP"><img class="portal-settings-state-flag" alt=""></span></div>
                            </div>
                            <h4 class="portal-settings-section-title">Senha</h4>
                            <div class="portal-settings-grid">
                                <div class="portal-settings-field"><label for="portal-full-senha">Nova senha</label><input id="portal-full-senha" type="password" minlength="8" placeholder="Opcional"></div>
                                <div class="portal-settings-field"><label for="portal-full-confirma">Confirme a senha</label><input id="portal-full-confirma" type="password" minlength="8" placeholder="Opcional"></div>
                            </div>
                        </div>
                        <div class="portal-settings-section" data-settings-section="personalizacao" hidden>
                            <h4 class="portal-settings-section-title">Personalização</h4>
                            <div class="portal-settings-grid">
                                <div class="portal-settings-field"><label for="portal-full-store">Nome da loja</label><input id="portal-full-store" type="text" value="${escapeHtml(first(c.nome_loja, c.nome_fantasia, c.razao_social, c.nome))}" placeholder="Nome da loja"></div>
                            </div>
                            <div class="portal-settings-option"><div><strong>Tema do portal</strong><small>Alternar entre modo claro e noturno.</small></div><button type="button" class="portal-btn portal-btn--ghost" data-settings-theme>Alternar tema</button></div>
                        </div>
                        <div class="portal-settings-feedback" data-settings-feedback aria-live="polite"></div>
                        <div class="portal-settings-actions"><button type="button" class="portal-btn portal-btn--ghost" data-settings-back="novo-pedido">Voltar</button><button type="submit" class="portal-btn portal-btn--primary">Atualizar cadastro</button></div>
                    </form>
                </div>
            </section>
        `;
    };

    const setSection = (section) => {
        document.querySelectorAll('[data-settings-section]').forEach((panel) => { panel.hidden = panel.dataset.settingsSection !== section; });
        document.querySelectorAll('[data-settings-section-trigger]').forEach((button) => {
            const active = button.dataset.settingsSectionTrigger === section;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    };

    const setDoc = (doc) => {
        document.querySelectorAll('[data-settings-doc]').forEach((button) => button.classList.toggle('is-active', button.dataset.settingsDoc === doc));
        document.querySelectorAll('[data-settings-doc-field]').forEach((field) => { field.hidden = field.dataset.settingsDocField !== doc; });
    };

    const setInput = (id, val, overwrite = true) => {
        const input = document.getElementById(id);
        const normalized = String(val || '').trim();
        if (!input || !normalized) return;
        if (!overwrite && input.value.trim()) return;
        input.value = normalized;
        input.dispatchEvent(new Event('input', { bubbles: true }));
    };

    const syncFlag = () => {
        const input = document.getElementById('portal-full-estado');
        const flag = document.querySelector('.portal-settings-state-flag');
        if (!input || !flag) return;
        const uf = input.value.replace(/[^A-Za-z]/g, '').toUpperCase().slice(0, 2);
        input.value = uf;
        const url = stateFlagUrls[uf];
        flag.classList.toggle('is-visible', Boolean(url));
        if (url) {
            flag.src = url;
            flag.alt = `Bandeira oficial do estado ${uf}`;
        }
    };

    const fillFromCep = async () => {
        const cep = onlyDigits(document.getElementById('portal-full-cep')?.value || '');
        if (cep.length !== 8) return;
        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await response.json();
            if (!response.ok || data.erro) return;
            setInput('portal-full-endereco', data.logradouro || '', false);
            setInput('portal-full-bairro', data.bairro || '', false);
            setInput('portal-full-cidade', data.localidade || '', false);
            setInput('portal-full-estado', data.uf || '', false);
            syncFlag();
        } catch (error) {}
    };

    const fillFromCnpj = async () => {
        const cnpj = onlyDigits(document.getElementById('portal-full-cnpj')?.value || '');
        const feedback = document.querySelector('[data-settings-feedback]');
        if (cnpj.length !== 14) return;
        if (feedback) feedback.textContent = 'Consultando CNPJ...';
        try {
            const response = await fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`);
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Não foi possível consultar este CNPJ.');
            const phone = first(data.ddd_telefone_1 && data.telefone ? `${data.ddd_telefone_1}${data.telefone}` : '', data.telefone);
            setInput('portal-full-razao', first(data.razao_social, data.nome));
            setInput('portal-full-fantasia', first(data.nome_fantasia, data.razao_social, data.nome));
            setInput('portal-full-store', first(data.nome_fantasia, data.razao_social, data.nome));
            setInput('portal-full-email', data.email || '', false);
            setInput('portal-full-telefone', formatPhone(phone), false);
            setInput('portal-full-cep', formatCep(data.cep || ''));
            setInput('portal-full-endereco', first(data.logradouro, data.descricao_logradouro, data.endereco));
            setInput('portal-full-numero', first(data.numero, data.numero_endereco));
            setInput('portal-full-bairro', data.bairro || '');
            setInput('portal-full-cidade', first(data.municipio, data.cidade));
            setInput('portal-full-estado', data.uf || '');
            syncFlag();
            if (feedback) feedback.textContent = 'Dados preenchidos pelo CNPJ.';
        } catch (error) {
            if (feedback) feedback.textContent = error.message || 'Não foi possível consultar o CNPJ.';
        }
    };

    const bindFullForm = () => {
        const form = document.querySelector('[data-portal-settings-full-form]');
        if (!form || form.dataset.fullBound === '1') return;
        form.dataset.fullBound = '1';
        document.querySelectorAll('[data-settings-section-trigger]').forEach((button) => button.addEventListener('click', () => setSection(button.dataset.settingsSectionTrigger || 'cadastro')));
        document.querySelectorAll('[data-settings-doc]').forEach((button) => button.addEventListener('click', () => setDoc(button.dataset.settingsDoc || 'cnpj')));
        form.querySelector('[data-settings-theme]')?.addEventListener('click', () => document.querySelector('.portal-theme-button')?.click());
        form.querySelector('[data-settings-back]')?.addEventListener('click', () => {
            document.querySelectorAll('[data-portal-tab-panel]').forEach((panel) => { panel.hidden = panel.dataset.portalTabPanel !== 'novo-pedido'; });
        });
        form.querySelector('#portal-full-has-branch')?.addEventListener('change', (event) => {
            const area = form.querySelector('.portal-settings-branch-name');
            area.hidden = !event.currentTarget.checked;
            if (!event.currentTarget.checked) form.querySelector('#portal-full-filial').value = '';
        });
        ['portal-full-telefone', 'portal-full-whatsapp'].forEach((id) => form.querySelector(`#${id}`)?.addEventListener('input', (event) => { event.currentTarget.value = formatPhone(event.currentTarget.value); }));
        form.querySelector('#portal-full-cnpj')?.addEventListener('input', (event) => { event.currentTarget.value = formatCnpj(event.currentTarget.value); if (onlyDigits(event.currentTarget.value).length === 14) fillFromCnpj(); });
        form.querySelector('#portal-full-cnpj')?.addEventListener('blur', fillFromCnpj);
        form.querySelector('#portal-full-cpf')?.addEventListener('input', (event) => { event.currentTarget.value = formatCpf(event.currentTarget.value); });
        form.querySelector('#portal-full-cep')?.addEventListener('input', (event) => { event.currentTarget.value = formatCep(event.currentTarget.value); });
        form.querySelector('#portal-full-cep')?.addEventListener('blur', fillFromCep);
        form.querySelector('#portal-full-estado')?.addEventListener('input', syncFlag);
        syncFlag();
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const senha = value(form, '#portal-full-senha');
            const confirma = value(form, '#portal-full-confirma');
            const feedback = form.querySelector('[data-settings-feedback]');
            if (senha || confirma) {
                if (senha.length < 8 || senha !== confirma) {
                    if (feedback) feedback.textContent = 'Confira a senha: mínimo 8 caracteres e confirmação igual.';
                    return;
                }
            }
            const cliente = readCliente();
            const docType = form.querySelector('[data-settings-doc].is-active')?.dataset.settingsDoc || 'cnpj';
            cliente.nome = value(form, '#portal-full-nome');
            cliente.email = value(form, '#portal-full-email');
            cliente.telefone = value(form, '#portal-full-telefone');
            cliente.contato = cliente.telefone;
            cliente.contato_tipo = radioValue(form, 'portal_full_contato_tipo') || 'celular';
            cliente.whatsapp = value(form, '#portal-full-whatsapp');
            cliente.cnpj = docType === 'cnpj' ? value(form, '#portal-full-cnpj') : '';
            cliente.cpf = docType === 'cpf' ? value(form, '#portal-full-cpf') : '';
            cliente.documento = cliente.cnpj || cliente.cpf;
            cliente.razao_social = value(form, '#portal-full-razao') || cliente.nome;
            cliente.nome_fantasia = value(form, '#portal-full-fantasia') || cliente.razao_social;
            cliente.nome_loja = value(form, '#portal-full-store') || cliente.nome_fantasia;
            cliente.nome_filial = checked(form, '#portal-full-has-branch') ? value(form, '#portal-full-filial') : '';
            cliente.filial_nome = cliente.nome_filial;
            cliente.cep = value(form, '#portal-full-cep');
            cliente.endereco = value(form, '#portal-full-endereco');
            cliente.numero = value(form, '#portal-full-numero');
            cliente.bairro = value(form, '#portal-full-bairro');
            cliente.cidade = value(form, '#portal-full-cidade');
            cliente.estado = value(form, '#portal-full-estado').toUpperCase();
            if (senha) cliente.senha_atualizada = true;
            writeCliente(cliente);
            if (feedback) feedback.textContent = 'Cadastro atualizado.';
            window.setTimeout(() => { if (feedback) feedback.textContent = ''; }, 3900);
        });
    };

    const apply = () => {
        const panels = document.querySelector('.portal-app__panels');
        if (!panels) return;
        document.getElementById('portal-tab-configuracao')?.remove();
        panels.insertAdjacentHTML('beforeend', fullSettingsMarkup());
        bindFullForm();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { apply(); window.setTimeout(apply, 360); }, { once: true });
    } else {
        apply();
        window.setTimeout(apply, 360);
    }
})();
</script>
