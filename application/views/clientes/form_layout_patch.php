<style>
    .cliente-form .name-row,
    .cliente-form .contact-row {
        grid-column: 1 / -1;
        display: grid;
        gap: 1rem;
        align-items: end;
    }

    .cliente-form .document-row {
        grid-column: 1 / -1;
        display: grid;
        gap: 1rem;
        align-items: start;
    }

    .cliente-form .name-row {
        grid-template-columns: repeat(2, minmax(260px, 1fr));
    }

    .cliente-form .document-row {
        grid-template-columns: minmax(220px, 1fr) minmax(160px, 0.7fr);
    }

    .cliente-form .contact-row {
        grid-template-columns: minmax(180px, 1fr) minmax(210px, auto) minmax(180px, 1fr) minmax(220px, 1.2fr);
    }

    .cliente-form .address-grid {
        grid-template-columns: minmax(130px, 0.75fr) minmax(390px, 2.3fr) minmax(100px, 0.55fr) minmax(180px, 1fr) minmax(210px, 1.1fr) minmax(96px, 0.48fr);
        align-items: end;
    }

    .cliente-form .address-field-city {
        grid-column: 1 / span 2;
        grid-row: 2;
        min-width: 0;
    }

    .cliente-form .address-field-state {
        grid-column: 3;
        grid-row: 2;
        min-width: 0;
    }

    .cliente-form .address-field-city input[name="cidade"] {
        width: 100%;
    }

    .cliente-form .address-field-street {
        min-width: 0;
    }

    .cliente-form .address-field-street input[name="endereco"] {
        width: 100%;
    }

    .cliente-form .address-field-number {
        min-width: 0;
    }

    .cliente-form .name-row label,
    .cliente-form .contact-input-group,
    .cliente-form .whatsapp-input-group,
    .cliente-form .email-input-group,
    .cliente-form .ie-input-group {
        width: 100%;
        min-width: 0;
        max-width: none;
        margin: 0;
    }

    .cliente-form .ie-input-group {
        align-self: start;
    }

    .cliente-form .contact-type-group {
        width: 100%;
        min-width: 190px;
        align-self: end;
    }

    .cliente-form .contact-type-toggle {
        min-height: 54px;
        align-items: center;
    }

    .cliente-form .ie-input-group[hidden] {
        display: none !important;
    }

    .cnpj-autofill-hint {
        display: flex;
        align-items: flex-start;
        gap: 0.4rem;
        margin: 0.42rem 0 0;
        font-size: 0.78rem;
        line-height: 1.35;
        color: var(--text-muted);
    }

    .cnpj-autofill-hint .icon {
        width: 0.9rem;
        height: 0.9rem;
        flex: 0 0 auto;
        margin-top: 0.08rem;
        color: var(--primary);
    }

    .cnpj-autofill-hint[hidden] {
        display: none !important;
    }

    .cnpj-autofill-feedback {
        grid-column: 1 / -1;
        margin: -0.35rem 0 0;
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .cnpj-autofill-feedback[data-state="success"] {
        color: var(--success-strong, #047857);
    }

    .cnpj-autofill-feedback[data-state="error"] {
        color: var(--danger-strong, #dc2626);
    }

    @media (max-width: 1180px) {
        .cliente-form .contact-row {
            grid-template-columns: repeat(2, minmax(220px, 1fr));
        }

        .cliente-form .address-grid {
            grid-template-columns: minmax(130px, 0.7fr) minmax(320px, 1.7fr) minmax(110px, 0.6fr);
        }

        .cliente-form .address-field-city {
            grid-column: 1 / span 2;
            grid-row: auto;
        }

        .cliente-form .address-field-state {
            grid-column: 3;
            grid-row: auto;
        }
    }

    @media (max-width: 720px) {
        .cliente-form .name-row,
        .cliente-form .document-row,
        .cliente-form .contact-row,
        .cliente-form .address-grid {
            grid-template-columns: 1fr;
        }

        .cliente-form .address-field-city,
        .cliente-form .address-field-state {
            grid-column: auto;
            grid-row: auto;
        }

        .cliente-form .contact-type-group {
            min-width: 0;
        }
    }
</style>

<script>
(function () {
    const removeLeakedClienteCode = () => {
        const markers = [
            'printWindow.document.body.appendChild(printableDialog)',
            'validateRequiredFields',
            'js-cliente-detalhe',
            'Não foi possível carregar os detalhes deste cliente agora'
        ];
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        const leakedNodes = [];

        while (walker.nextNode()) {
            const node = walker.currentNode;
            const value = node.nodeValue || '';
            if (markers.some((marker) => value.includes(marker))) {
                leakedNodes.push(node);
            }
        }

        leakedNodes.forEach((node) => node.remove());
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', removeLeakedClienteCode);
    } else {
        removeLeakedClienteCode();
    }
})();

(function () {
    const form = document.querySelector('.cliente-form');
    if (!form) {
        return;
    }

    const razaoInput = form.querySelector('input[name="razao_social"]');
    const fantasiaInput = form.querySelector('input[name="nome_fantasia"]');
    const razaoLabel = razaoInput ? razaoInput.closest('label') : null;
    const fantasiaLabel = fantasiaInput ? fantasiaInput.closest('label') : null;
    const docInput = form.querySelector('#documento');
    const originalDocLabel = docInput ? docInput.closest('label') : null;
    const emailInput = form.querySelector('input[name="email"]');
    const emailLabel = emailInput ? emailInput.closest('label') : null;
    const contactRow = form.querySelector('.contact-row');
    const hiddenDocType = form.querySelector('input[name="documento_tipo"]');
    const cnpjButton = form.querySelector('#btn-cnpj');
    const cpfButton = form.querySelector('#btn-cpf');

    const addFieldClass = (name, className) => {
        const input = form.querySelector(`[name="${name}"]`);
        const label = input ? input.closest('label') : null;
        if (label) {
            label.classList.add(className);
        }
    };

    addFieldClass('cep', 'address-field-cep');
    addFieldClass('endereco', 'address-field-street');
    addFieldClass('numero', 'address-field-number');
    addFieldClass('bairro', 'address-field-neighborhood');
    addFieldClass('cidade', 'address-field-city');
    addFieldClass('estado', 'address-field-state');

    if (razaoLabel && fantasiaLabel && !form.querySelector('.name-row')) {
        const nameRow = document.createElement('div');
        nameRow.className = 'name-row';
        razaoLabel.parentNode.insertBefore(nameRow, razaoLabel);
        nameRow.appendChild(razaoLabel);
        nameRow.appendChild(fantasiaLabel);
    }

    if (originalDocLabel && !form.querySelector('.document-row')) {
        const documentRow = document.createElement('div');
        documentRow.className = 'document-row';
        originalDocLabel.parentNode.insertBefore(documentRow, originalDocLabel);
        documentRow.appendChild(originalDocLabel);

        const ieLabel = document.createElement('label');
        ieLabel.className = 'ie-input-group';
        ieLabel.innerHTML = '<span class="field-label-with-icon"><span class="icon" data-lucide="badge-check"></span><span>IE</span></span><input type="text" name="ie" placeholder="Inscrição estadual" autocomplete="off">';
        documentRow.appendChild(ieLabel);
    }

    const docLabel = docInput ? docInput.closest('label') : null;
    const hint = document.createElement('span');
    hint.className = 'cnpj-autofill-hint';
    hint.innerHTML = '<span class="icon" data-lucide="sparkles"></span><span>Ao informar o CNPJ, os dados cadastrais disponíveis serão preenchidos automaticamente.</span>';
    if (docLabel && !docLabel.querySelector('.cnpj-autofill-hint')) {
        docLabel.appendChild(hint);
    }

    if (emailLabel && contactRow && !emailLabel.classList.contains('email-input-group')) {
        emailLabel.classList.add('email-input-group');
        contactRow.appendChild(emailLabel);
    }

    const ieField = form.querySelector('.ie-input-group');
    const ieInput = ieField ? ieField.querySelector('input[name="ie"]') : null;

    const feedback = document.createElement('p');
    feedback.className = 'cnpj-autofill-feedback';
    feedback.setAttribute('aria-live', 'polite');
    if (form.querySelector('.document-row')) {
        form.querySelector('.document-row').insertAdjacentElement('afterend', feedback);
    }

    const field = (name) => form.querySelector(`[name="${name}"]`);
    const onlyDigits = (value) => String(value || '').replace(/\D+/g, '');
    const isCnpjMode = () => {
        if (hiddenDocType) {
            return hiddenDocType.value !== 'cpf';
        }
        return !(cpfButton && cpfButton.classList.contains('active'));
    };

    const setFeedback = (message, state = 'muted') => {
        feedback.textContent = message || '';
        feedback.dataset.state = state;
    };

    const updateHintVisibility = () => {
        hint.hidden = !isCnpjMode();
    };

    const fillIfEmpty = (name, value) => {
        const input = field(name);
        if (!input || value === undefined || value === null || String(value).trim() === '') {
            return;
        }
        if (String(input.value || '').trim() === '') {
            input.value = String(value).trim();
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    };

    const firstValue = (...values) => {
        for (const value of values.flat()) {
            if (value === undefined || value === null) {
                continue;
            }
            const text = String(value).trim();
            if (text !== '') {
                return text;
            }
        }
        return '';
    };

    const formatPhone = (value) => {
        const digits = onlyDigits(value);
        if (digits.length === 11) {
            return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
        }
        if (digits.length === 10) {
            return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
        }
        return String(value || '').trim();
    };

    const ieFromItem = (item) => {
        if (!item || typeof item !== 'object') {
            return '';
        }
        return firstValue(
            item.inscricao_estadual,
            item.inscricaoEstadual,
            item.inscricao_estadual_numero,
            item.inscricao,
            item.numero,
            item.number,
            item.ie
        );
    };

    const findStateRegistration = (data) => {
        const direct = firstValue(
            data.inscricao_estadual,
            data.inscricaoEstadual,
            data.inscricao_estadual_numero,
            data.inscricao,
            data.ie,
            data.estabelecimento && data.estabelecimento.inscricao_estadual,
            data.estabelecimento && data.estabelecimento.inscricaoEstadual,
            data.estabelecimento && data.estabelecimento.ie
        );
        if (direct) {
            return direct;
        }

        const uf = String(data.uf || '').toUpperCase();
        const lists = [
            data.inscricoes_estaduais,
            data.inscricoesEstaduais,
            data.estabelecimento && data.estabelecimento.inscricoes_estaduais,
            data.estabelecimento && data.estabelecimento.inscricoesEstaduais,
        ].filter(Array.isArray);

        for (const list of lists) {
            const match = list.find((item) => {
                const itemUf = String((item && (item.estado || item.uf)) || '').toUpperCase();
                const inactive = ['false', 'baixada', 'inativo', 'inativa'].includes(String((item && (item.ativo ?? item.situacao)) || '').toLowerCase());
                return !inactive && (!uf || !itemUf || itemUf === uf);
            }) || list[0];
            const value = ieFromItem(match);
            if (value) {
                return value;
            }
        }

        return '';
    };

    const findEmail = (data) => {
        const direct = firstValue(
            data.email,
            data.correio_eletronico,
            data.correioEletronico,
            data.email_principal,
            data.emailPrincipal,
            data.estabelecimento && data.estabelecimento.email,
            data.estabelecimento && data.estabelecimento.correio_eletronico,
            data.estabelecimento && data.estabelecimento.correioEletronico
        );
        if (direct) {
            return direct;
        }

        const lists = [
            data.emails,
            data.email_list,
            data.emailList,
            data.estabelecimento && data.estabelecimento.emails,
        ].filter(Array.isArray);

        for (const list of lists) {
            const value = firstValue(...list.map((item) => {
                if (item && typeof item === 'object') {
                    return firstValue(item.email, item.valor, item.value, item.endereco);
                }
                return item;
            }));
            if (value) {
                return value;
            }
        }

        return '';
    };

    const formatCep = (cep) => {
        const digits = onlyDigits(cep).slice(0, 8);
        return digits.length === 8 ? `${digits.slice(0, 5)}-${digits.slice(5)}` : digits;
    };

    const fillFromCnpj = (data) => {
        const primaryPhone = firstValue(data.ddd_telefone_1, data.telefone, data.phone);
        const secondaryPhone = firstValue(data.whatsapp, data.celular, data.ddd_telefone_2, data.phone2);
        const phone = formatPhone(primaryPhone || secondaryPhone);
        const email = findEmail(data);
        const fantasy = data.nome_fantasia || data.razao_social || '';

        fillIfEmpty('razao_social', data.razao_social);
        fillIfEmpty('nome_fantasia', fantasy);
        fillIfEmpty('ie', findStateRegistration(data));
        fillIfEmpty('email', email);
        fillIfEmpty('telefone', phone);
        fillIfEmpty('cep', formatCep(data.cep));
        fillIfEmpty('endereco', data.logradouro || data.descricao_tipo_de_logradouro);
        fillIfEmpty('numero', data.numero);
        fillIfEmpty('bairro', data.bairro);
        fillIfEmpty('cidade', data.municipio);
        fillIfEmpty('estado', data.uf);

        if (window.updateClienteUfFlag) {
            window.updateClienteUfFlag();
        }
    };

    let lastLoadedCnpj = '';
    let abortController = null;

    const loadCnpjData = () => {
        if (!docInput || !isCnpjMode()) {
            return;
        }

        const cnpj = onlyDigits(docInput.value);
        if (cnpj.length === 0) {
            setFeedback('');
            return;
        }
        if (cnpj.length !== 14) {
            setFeedback('Digite um CNPJ com 14 dígitos para buscar os dados automaticamente.', 'muted');
            return;
        }
        if (cnpj === lastLoadedCnpj) {
            return;
        }

        lastLoadedCnpj = cnpj;
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();
        setFeedback('Consultando dados do CNPJ...', 'muted');

        fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`, { signal: abortController.signal })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then((data) => {
                fillFromCnpj(data || {});
                setFeedback('Dados do CNPJ preenchidos automaticamente.', 'success');
            })
            .catch((error) => {
                if (error && error.name === 'AbortError') {
                    return;
                }
                setFeedback('Não foi possível consultar esse CNPJ agora. Preencha os dados manualmente.', 'error');
            });
    };

    const updateIeVisibility = () => {
        const isCnpj = isCnpjMode();
        if (ieField) {
            ieField.hidden = !isCnpj;
        }
        if (ieInput) {
            ieInput.disabled = !isCnpj;
            if (!isCnpj) {
                ieInput.value = '';
            }
        }
        if (!isCnpj) {
            setFeedback('');
        }
        updateHintVisibility();
    };

    [cnpjButton, cpfButton].forEach((button) => {
        if (!button) {
            return;
        }
        button.addEventListener('click', () => window.setTimeout(updateIeVisibility, 0));
    });

    if (docInput) {
        docInput.addEventListener('blur', loadCnpjData);
        docInput.addEventListener('input', () => {
            if (isCnpjMode() && onlyDigits(docInput.value).length < 14) {
                lastLoadedCnpj = '';
                setFeedback('');
            }
        });
    }

    form.addEventListener('reset', () => {
        window.setTimeout(() => {
            lastLoadedCnpj = '';
            setFeedback('');
            updateIeVisibility();
        }, 0);
    });

    updateIeVisibility();

    if (window.lucide) {
        window.lucide.createIcons({ elements: form.querySelectorAll('[data-lucide]') });
    }
})();
</script>
