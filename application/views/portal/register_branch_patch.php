<?php
include __DIR__.'/register.php';
?>
<style>
    html body .portal-store-helper,
    html body .portal-cnpj-feedback {
        margin: 2.6px 0 0.65rem;
        color: var(--portal-muted, rgba(71, 85, 105, 0.82));
        font-size: 0.86rem;
    }

    html body .portal-cnpj-feedback {
        margin: 0.35rem 0 0;
        display: none;
        font-weight: 700;
    }

    html body .portal-store-helper {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 0.62rem;
        align-items: start;
        padding: 0.74rem 0.82rem;
        border: 1px solid color-mix(in srgb, var(--primary, #dc2626) 22%, transparent);
        border-radius: 14px;
        background: color-mix(in srgb, var(--primary, #dc2626) 8%, #fff);
        box-shadow: 0 10px 24px -20px color-mix(in srgb, var(--primary, #dc2626) 55%, transparent);
        line-height: 1.35;
    }

    html body .portal-store-helper::before {
        content: '?';
        width: 22px;
        height: 22px;
        border-radius: 999px;
        display: inline-grid;
        place-items: center;
        background: color-mix(in srgb, var(--primary, #dc2626) 14%, #fff);
        color: var(--primary, #dc2626);
        font-size: 0.76rem;
        font-weight: 900;
        line-height: 1;
    }

    html body .portal-store-helper strong {
        color: var(--text, #111827);
        font-weight: 900;
    }

    html body .portal-cnpj-feedback[data-state] {
        display: block;
    }

    html body .portal-cnpj-feedback[data-state="loading"] {
        color: var(--portal-muted, rgba(71, 85, 105, 0.82));
    }

    html body .portal-cnpj-feedback[data-state="success"] {
        color: #15803d;
    }

    html body .portal-cnpj-feedback[data-state="error"] {
        color: #b91c1c;
    }

    html body .portal-uf-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    html body .portal-uf-wrap input {
        width: 100%;
        padding-right: 4.35rem;
        text-transform: uppercase;
    }

    html body .portal-state-flag {
        position: absolute;
        right: 0.78rem;
        width: 42px;
        height: 28px;
        border-radius: 7px;
        object-fit: cover;
        border: 1px solid rgba(15, 23, 42, 0.14);
        box-shadow: 0 8px 18px -12px rgba(15, 23, 42, 0.65);
        background: rgba(255, 255, 255, 0.88);
        display: none;
    }

    html body .portal-state-flag.is-visible {
        display: block;
    }

    html body .portal-branch-card {
        display: grid;
        gap: 0.8rem;
        margin: 0.35rem 0 1rem;
        padding: 0.95rem;
        border: 1px solid var(--portal-card-border, rgba(15, 23, 42, 0.08));
        border-radius: 18px;
        background: rgba(148, 163, 184, 0.08);
    }

    html body .portal-branch-switch {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        margin: 0;
        cursor: pointer;
        user-select: none;
        font-weight: 800;
        color: var(--text, #111827);
    }

    html body .portal-branch-switch input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    html body .portal-branch-pill {
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
        font-weight: 900;
        transition: background 0.18s ease, color 0.18s ease;
    }

    html body .portal-branch-pill::before {
        content: 'Não';
    }

    html body .portal-branch-pill::after {
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

    html body .portal-branch-switch input:checked + .portal-branch-pill {
        justify-content: flex-start;
        background: var(--primary, #dc2626);
        color: #fff;
    }

    html body .portal-branch-switch input:checked + .portal-branch-pill::before {
        content: 'Sim';
    }

    html body .portal-branch-switch input:checked + .portal-branch-pill::after {
        transform: translateX(38px);
    }

    html body .portal-branch-field {
        margin: 0;
    }

    html body .portal-branch-field.is-hidden {
        display: none;
    }
</style>
<script>
(function () {
    const onlyDigits = (value) => String(value || '').replace(/\D+/g, '');
    let lastCnpjLookup = '';

    const stateFlagUrls = {
        AC: 'https://upload.wikimedia.org/wikipedia/commons/4/4c/Bandeira_do_Acre.svg',
        AL: 'https://upload.wikimedia.org/wikipedia/commons/8/88/Bandeira_de_Alagoas.svg',
        AP: 'https://upload.wikimedia.org/wikipedia/commons/0/0c/Bandeira_do_Amap%C3%A1.svg',
        AM: 'https://upload.wikimedia.org/wikipedia/commons/6/6b/Bandeira_do_Amazonas.svg',
        BA: 'https://upload.wikimedia.org/wikipedia/commons/2/28/Bandeira_da_Bahia.svg',
        CE: 'https://upload.wikimedia.org/wikipedia/commons/2/2e/Bandeira_do_Cear%C3%A1.svg',
        DF: 'https://upload.wikimedia.org/wikipedia/commons/3/3c/Bandeira_do_Distrito_Federal_%28Brasil%29.svg',
        ES: 'https://upload.wikimedia.org/wikipedia/commons/4/43/Bandeira_do_Esp%C3%ADrito_Santo.svg',
        GO: 'https://upload.wikimedia.org/wikipedia/commons/b/be/Flag_of_Goi%C3%A1s.svg',
        MA: 'https://upload.wikimedia.org/wikipedia/commons/4/45/Bandeira_do_Maranh%C3%A3o.svg',
        MT: 'https://upload.wikimedia.org/wikipedia/commons/0/0b/Bandeira_de_Mato_Grosso.svg',
        MS: 'https://upload.wikimedia.org/wikipedia/commons/6/64/Bandeira_de_Mato_Grosso_do_Sul.svg',
        MG: 'https://upload.wikimedia.org/wikipedia/commons/f/f4/Bandeira_de_Minas_Gerais.svg',
        PA: 'https://upload.wikimedia.org/wikipedia/commons/0/02/Bandeira_do_Par%C3%A1.svg',
        PB: 'https://upload.wikimedia.org/wikipedia/commons/b/bb/Bandeira_da_Para%C3%ADba.svg',
        PR: 'https://upload.wikimedia.org/wikipedia/commons/9/93/Bandeira_do_Paran%C3%A1.svg',
        PE: 'https://upload.wikimedia.org/wikipedia/commons/5/59/Bandeira_de_Pernambuco.svg',
        PI: 'https://upload.wikimedia.org/wikipedia/commons/3/33/Bandeira_do_Piau%C3%AD.svg',
        RJ: 'https://upload.wikimedia.org/wikipedia/commons/7/73/Bandeira_do_estado_do_Rio_de_Janeiro.svg',
        RN: 'https://upload.wikimedia.org/wikipedia/commons/3/30/Bandeira_do_Rio_Grande_do_Norte.svg',
        RS: 'https://upload.wikimedia.org/wikipedia/commons/6/63/Bandeira_do_Rio_Grande_do_Sul.svg',
        RO: 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Bandeira_de_Rond%C3%B4nia.svg',
        RR: 'https://upload.wikimedia.org/wikipedia/commons/9/98/Bandeira_de_Roraima.svg',
        SC: 'https://upload.wikimedia.org/wikipedia/commons/1/1a/Bandeira_de_Santa_Catarina.svg',
        SP: 'https://upload.wikimedia.org/wikipedia/commons/2/2b/Bandeira_do_estado_de_S%C3%A3o_Paulo.svg',
        SE: 'https://upload.wikimedia.org/wikipedia/commons/b/be/Bandeira_de_Sergipe.svg',
        TO: 'https://upload.wikimedia.org/wikipedia/commons/f/ff/Bandeira_do_Tocantins.svg'
    };

    const setFeedback = (element, message, state = '') => {
        if (!element) return;
        element.textContent = message;
        if (state) {
            element.dataset.state = state;
        } else {
            delete element.dataset.state;
        }
    };

    const setFieldValue = (id, value, overwrite = true) => {
        const input = document.getElementById(id);
        if (!input || value === undefined || value === null) return;
        const normalized = String(value).trim();
        if (!normalized) return;
        if (!overwrite && String(input.value || '').trim() !== '') return;
        input.value = normalized;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const firstValue = (...values) => {
        for (const value of values) {
            const normalized = String(value || '').trim();
            if (normalized) return normalized;
        }
        return '';
    };

    const formatCep = (value) => {
        const digits = onlyDigits(value).slice(0, 8);
        if (digits.length !== 8) return digits;
        return `${digits.slice(0, 5)}-${digits.slice(5)}`;
    };

    const formatCpf = (value) => {
        const digits = onlyDigits(value).slice(0, 11);
        return digits
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    };

    const formatCnpj = (value) => {
        const digits = onlyDigits(value).slice(0, 14);
        return digits
            .replace(/(\d{2})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1/$2')
            .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    };

    const formatPhone = (ddd, number) => {
        const digits = onlyDigits(`${ddd || ''}${number || ''}`);
        if (!digits) return '';
        if (digits.length === 11) return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
        if (digits.length === 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
        return digits;
    };

    const findCnpjEmail = (data) => firstValue(
        data.email,
        data.correio_eletronico,
        data.email_empresa,
        data.email_estabelecimento,
        data.estabelecimento && data.estabelecimento.email,
        data.estabelecimento && data.estabelecimento.correio_eletronico
    );

    const findCnpjPhone = (data) => firstValue(
        data.ddd_telefone_1,
        data.telefone_1,
        data.telefone,
        data.celular,
        data.whatsapp,
        data.estabelecimento && data.estabelecimento.ddd_telefone_1,
        data.estabelecimento && data.estabelecimento.telefone_1,
        data.estabelecimento && data.estabelecimento.telefone,
        data.estabelecimento && data.estabelecimento.celular
    );

    const fetchCnpjData = async (cnpj) => {
        const response = await fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`);
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || 'Não foi possível consultar este CNPJ.');
        }
        return data;
    };

    const fillFromCnpj = async (cnpj, feedback) => {
        if (cnpj.length !== 14) {
            setFeedback(feedback, 'Informe um CNPJ com 14 dígitos para preencher automaticamente.', 'error');
            return;
        }
        if (lastCnpjLookup === cnpj) return;
        lastCnpjLookup = cnpj;
        setFeedback(feedback, 'Consultando CNPJ...', 'loading');

        try {
            const data = await fetchCnpjData(cnpj);
            const razao = data.razao_social || data.nome || '';
            const fantasia = data.nome_fantasia || razao;
            const telefone = formatPhone('', findCnpjPhone(data));
            const endereco = firstValue(data.logradouro, data.descricao_logradouro, data.nome_logradouro, data.endereco);
            const numero = firstValue(data.numero, data.numero_endereco, data.numero_logradouro);
            const bairro = firstValue(data.bairro, data.nome_bairro);
            const cidade = firstValue(data.municipio, data.cidade, data.nome_municipio);

            setFieldValue('registro-razao', razao);
            setFieldValue('registro-fantasia', fantasia);
            setFieldValue('registro-email', findCnpjEmail(data), false);
            setFieldValue('registro-telefone', telefone, false);
            setFieldValue('registro-cep', formatCep(data.cep || ''));
            setFieldValue('registro-endereco', endereco);
            setFieldValue('registro-numero', numero);
            setFieldValue('registro-bairro', bairro);
            setFieldValue('registro-cidade', cidade);
            setFieldValue('registro-estado', data.uf || '');
            setFeedback(feedback, 'Dados cadastrais preenchidos automaticamente pelo CNPJ.', 'success');
        } catch (error) {
            lastCnpjLookup = '';
            setFeedback(feedback, error.message || 'Não foi possível consultar este CNPJ agora.', 'error');
        }
    };

    const mountBranchFields = () => {
        const storeInput = document.getElementById('registro-fantasia');
        const form = document.getElementById('portal-register-form');
        if (!storeInput || !form || storeInput.dataset.branchPatchReady === '1') return;
        storeInput.dataset.branchPatchReady = '1';

        const storeLabel = document.querySelector('label[for="registro-fantasia"]');
        if (storeLabel) {
            storeLabel.textContent = 'Nome da loja';
        }
        storeInput.placeholder = 'Nome que aparece no portal';

        const helper = document.createElement('p');
        helper.className = 'portal-store-helper';
        helper.innerHTML = '<span><strong>Nome da loja como vai aparecer na saudação do portal.</strong> Use um nome curto para ficar bonito na saudação, na pílula do usuário e nos pedidos. Se essa conta for uma filial, marque <strong>É uma filial</strong> e informe qual filial.</span>';
        storeInput.insertAdjacentElement('afterend', helper);

        const wrapper = document.createElement('div');
        wrapper.className = 'portal-branch-card';
        wrapper.innerHTML = `
            <label class="portal-branch-switch" for="registro-tem-filial">
                <input type="checkbox" id="registro-tem-filial" name="tem_filial" value="1">
                <span class="portal-branch-pill" aria-hidden="true"></span>
                <span>É uma filial</span>
            </label>
            <div class="portal-branch-field is-hidden" data-portal-branch-field>
                <label for="registro-filial">Nome da filial</label>
                <input type="text" id="registro-filial" name="nome_filial" placeholder="Ex.: Filial Centro" disabled>
            </div>
        `;
        helper.insertAdjacentElement('afterend', wrapper);

        const checkbox = wrapper.querySelector('#registro-tem-filial');
        const branchField = wrapper.querySelector('[data-portal-branch-field]');
        const branchInput = wrapper.querySelector('#registro-filial');

        const syncBranch = () => {
            const active = Boolean(checkbox.checked);
            branchField.classList.toggle('is-hidden', !active);
            branchInput.disabled = !active;
            if (!active) {
                branchInput.value = '';
            }
        };

        checkbox.addEventListener('change', syncBranch);
        form.addEventListener('reset', () => setTimeout(syncBranch, 0));
        syncBranch();
    };

    const mountDocumentMasks = () => {
        const cnpjInput = document.getElementById('registro-cnpj');
        const cpfInput = document.getElementById('registro-cpf');
        if (cnpjInput && cnpjInput.dataset.maskReady !== '1') {
            cnpjInput.dataset.maskReady = '1';
            cnpjInput.maxLength = 18;
            cnpjInput.addEventListener('input', () => {
                const cursorAtEnd = cnpjInput.selectionStart === cnpjInput.value.length;
                cnpjInput.value = formatCnpj(cnpjInput.value);
                if (cursorAtEnd) cnpjInput.setSelectionRange(cnpjInput.value.length, cnpjInput.value.length);
            });
        }
        if (cpfInput && cpfInput.dataset.maskReady !== '1') {
            cpfInput.dataset.maskReady = '1';
            cpfInput.maxLength = 14;
            cpfInput.addEventListener('input', () => {
                const cursorAtEnd = cpfInput.selectionStart === cpfInput.value.length;
                cpfInput.value = formatCpf(cpfInput.value);
                if (cursorAtEnd) cpfInput.setSelectionRange(cpfInput.value.length, cpfInput.value.length);
            });
        }
    };

    const mountStateFlag = () => {
        const estadoInput = document.getElementById('registro-estado');
        if (!estadoInput || estadoInput.dataset.flagReady === '1') return;
        estadoInput.dataset.flagReady = '1';

        const wrapper = document.createElement('span');
        wrapper.className = 'portal-uf-wrap';
        estadoInput.parentNode.insertBefore(wrapper, estadoInput);
        wrapper.appendChild(estadoInput);

        const flag = document.createElement('img');
        flag.className = 'portal-state-flag';
        flag.alt = '';
        flag.loading = 'lazy';
        wrapper.appendChild(flag);

        const syncFlag = () => {
            const uf = String(estadoInput.value || '').replace(/[^A-Za-z]/g, '').toUpperCase().slice(0, 2);
            if (estadoInput.value !== uf) {
                estadoInput.value = uf;
            }
            const url = stateFlagUrls[uf];
            flag.classList.toggle('is-visible', Boolean(url));
            if (url && flag.src !== url) {
                flag.src = url;
                flag.alt = `Bandeira oficial do estado ${uf}`;
            }
        };

        flag.addEventListener('error', () => flag.classList.remove('is-visible'));
        estadoInput.addEventListener('input', syncFlag);
        estadoInput.addEventListener('change', syncFlag);
        syncFlag();
    };

    const mountCnpjLookup = () => {
        const cnpjInput = document.getElementById('registro-cnpj');
        if (!cnpjInput || cnpjInput.dataset.cnpjLookupReady === '1') return;
        cnpjInput.dataset.cnpjLookupReady = '1';

        const feedback = document.createElement('p');
        feedback.className = 'portal-cnpj-feedback';
        feedback.setAttribute('role', 'status');
        feedback.setAttribute('aria-live', 'polite');
        cnpjInput.insertAdjacentElement('afterend', feedback);

        cnpjInput.addEventListener('input', () => {
            const digits = onlyDigits(cnpjInput.value);
            if (digits.length < 14) {
                lastCnpjLookup = '';
                setFeedback(feedback, '');
            }
            if (digits.length === 14) {
                fillFromCnpj(digits, feedback);
            }
        });

        cnpjInput.addEventListener('blur', () => {
            const digits = onlyDigits(cnpjInput.value);
            if (digits.length === 14) {
                fillFromCnpj(digits, feedback);
            }
        });
    };

    const mount = () => {
        mountBranchFields();
        mountDocumentMasks();
        mountStateFlag();
        mountCnpjLookup();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mount, { once: true });
    } else {
        mount();
    }
})();
</script>
