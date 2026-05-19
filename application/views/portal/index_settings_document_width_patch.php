<?php
include __DIR__.'/index_settings_documents_first_patch.php';
?>
<style>
    html body .portal-settings-document-card .portal-settings-grid {
        grid-template-columns: minmax(236px, 0.44fr) minmax(0, 0.99fr) minmax(0, 0.99fr) !important;
        gap: 1rem !important;
        align-items: stretch !important;
    }

    html body .portal-settings-document-card [data-settings-doc-field] input {
        font-variant-numeric: tabular-nums;
    }

    html body .portal-settings-info-row {
        display: grid !important;
        grid-template-columns: calc(43ch + 22px) minmax(206px, 206px) minmax(250px, 0.72fr) !important;
        gap: 1rem !important;
        align-items: stretch !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 0 1rem !important;
        overflow: visible !important;
    }

    html body .portal-settings-address-row {
        display: grid !important;
        grid-template-columns: minmax(128px, 0.34fr) minmax(0, 1.6fr) minmax(12ch, 12ch) minmax(0, 1fr) !important;
        gap: 1rem !important;
        align-items: stretch !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 0 1rem !important;
    }

    html body .portal-settings-address-row .portal-settings-field {
        min-width: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
    }

    html body .portal-settings-address-row #portal-full-numero {
        text-align: left !important;
        font-variant-numeric: tabular-nums;
    }

    html body .portal-settings-info-row .portal-settings-field,
    html body .portal-settings-info-row .portal-settings-contact-type {
        min-width: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        position: relative !important;
        transform: none !important;
        z-index: 1 !important;
    }

    html body .portal-settings-info-row .portal-settings-field input {
        min-width: 0 !important;
        width: 100% !important;
    }

    html body #portal-full-email {
        text-transform: none !important;
    }

    html body .portal-settings-info-row .portal-settings-contact-type {
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        gap: 0.75rem !important;
        height: 100% !important;
    }

    html body .portal-settings-personalization-row {
        display: grid !important;
        grid-template-columns: minmax(0, 1.25fr) minmax(0, 1.25fr) minmax(230px, 0.8fr) !important;
        gap: 1rem !important;
        align-items: stretch !important;
        width: 100% !important;
        margin-bottom: 1rem !important;
    }

    html body .portal-settings-personalization-row .portal-settings-field,
    html body .portal-settings-personalization-row .portal-settings-branch-row {
        min-width: 0 !important;
        width: 100% !important;
        margin: 0 !important;
    }

    html body .portal-settings-personalization-row .portal-settings-branch-row {
        align-content: center !important;
        padding: 0.9rem 1rem !important;
    }

    html body .portal-settings-first-line,
    html body .portal-settings-top-grid,
    html body .portal-settings-contact-line,
    html body .portal-settings-name-email-row,
    html body .portal-settings-contact-row {
        display: contents !important;
    }

    html body .portal-settings-whatsapp-row {
        width: min(520px, 100%) !important;
        max-width: 100% !important;
        margin: 0 0 1rem !important;
    }

    html body .portal-settings-hidden-name-field {
        display: none !important;
    }

    html body .portal-app__header {
        position: relative !important;
    }

    html body .portal-app__tag {
        width: min(100%, calc(100vw - 610px)) !important;
        max-width: 860px !important;
    }

    html body .portal-app__header-links {
        position: absolute !important;
        top: calc(0.36rem + 22px) !important;
        right: 1.25rem !important;
        z-index: 5 !important;
        margin: 0 !important;
    }

    @media (max-width: 1180px) {
        html body .portal-settings-info-row,
        html body .portal-settings-personalization-row,
        html body .portal-settings-address-row {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 980px) {
        html body .portal-settings-document-card .portal-settings-grid {
            grid-template-columns: 1fr !important;
        }

        html body .portal-app__tag {
            width: 100% !important;
            max-width: 100% !important;
        }

        html body .portal-app__header-links {
            position: static !important;
            margin-top: 0.75rem !important;
        }
    }

    @media (max-width: 700px) {
        html body .portal-settings-info-row,
        html body .portal-settings-personalization-row,
        html body .portal-settings-address-row {
            grid-template-columns: 1fr !important;
        }
    }
</style>
<script>
(function () {
    const onlyDigits = (value) => String(value || '').replace(/\D+/g, '');
    const formatPhone = (value) => {
        const digits = onlyDigits(value).slice(0, 11);
        if (digits.length > 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
        if (digits.length > 6) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
        if (digits.length > 2) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
        return digits;
    };
    const formatCpf = (value) => onlyDigits(value).slice(0, 11)
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    const formatCnpj = (value) => onlyDigits(value).slice(0, 14)
        .replace(/(\d{2})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1/$2')
        .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    const formatCep = (value) => {
        const digits = onlyDigits(value).slice(0, 8);
        return digits.length > 5 ? `${digits.slice(0, 5)}-${digits.slice(5)}` : digits;
    };

    const bindMask = (id, formatter) => {
        const input = document.getElementById(id);
        if (!input || input.dataset.pasteMaskReady === '1') return;
        input.dataset.pasteMaskReady = '1';
        const apply = () => {
            const next = formatter(input.value);
            if (input.value !== next) input.value = next;
        };
        ['input', 'change', 'blur'].forEach((eventName) => input.addEventListener(eventName, apply));
        input.addEventListener('paste', () => window.setTimeout(apply, 0));
        apply();
    };

    const restoreEmailCase = (input, value, start, end) => {
        window.setTimeout(() => {
            if (input.value !== value) {
                input.value = value;
                if (Number.isFinite(start) && Number.isFinite(end)) {
                    input.setSelectionRange(start, end);
                }
            }
        }, 0);
    };

    const prepareEmailField = () => {
        const email = document.getElementById('portal-full-email');
        if (!email) return;
        email.setAttribute('type', 'text');
        email.setAttribute('inputmode', 'email');
        email.setAttribute('autocomplete', 'email');
        email.setAttribute('autocapitalize', 'none');
        email.style.textTransform = 'none';
        if (email.dataset.caseReady === '1') return;
        email.dataset.caseReady = '1';
        email.addEventListener('beforeinput', (event) => {
            if (!event.data || event.inputType !== 'insertText') return;
            const start = email.selectionStart ?? email.value.length;
            const end = email.selectionEnd ?? start;
            const next = email.value.slice(0, start) + event.data + email.value.slice(end);
            restoreEmailCase(email, next, start + event.data.length, start + event.data.length);
        });
        email.addEventListener('paste', (event) => {
            const pasted = event.clipboardData?.getData('text');
            if (typeof pasted !== 'string') return;
            const start = email.selectionStart ?? email.value.length;
            const end = email.selectionEnd ?? start;
            const next = email.value.slice(0, start) + pasted + email.value.slice(end);
            restoreEmailCase(email, next, start + pasted.length, start + pasted.length);
        });
    };

    const bindPasteMasks = () => {
        bindMask('portal-full-telefone', formatPhone);
        bindMask('portal-full-whatsapp', formatPhone);
        bindMask('portal-full-cnpj', formatCnpj);
        bindMask('portal-full-cpf', formatCpf);
        bindMask('portal-full-cep', formatCep);
        prepareEmailField();
    };

    const unwrapRows = (section) => {
        section.querySelectorAll('.portal-settings-first-line, .portal-settings-top-grid, .portal-settings-contact-line, .portal-settings-name-email-row, .portal-settings-contact-row').forEach((row) => {
            if (row.classList.contains('portal-settings-info-row')) return;
            while (row.firstChild) {
                row.parentNode.insertBefore(row.firstChild, row);
            }
            row.remove();
        });
    };

    const fixInfoRow = () => {
        const nameField = document.getElementById('portal-full-nome')?.closest('.portal-settings-field');
        const emailField = document.getElementById('portal-full-email')?.closest('.portal-settings-field');
        const contactField = document.getElementById('portal-full-telefone')?.closest('.portal-settings-field');
        const whatsappField = document.getElementById('portal-full-whatsapp')?.closest('.portal-settings-field');
        const contactType = document.querySelector('.portal-settings-contact-type');
        const section = emailField?.closest('[data-settings-section="cadastro"]');
        const title = section?.querySelector('.portal-settings-section-title');
        if (!section || !title || !emailField || !contactField || !whatsappField || !contactType) return;

        unwrapRows(section);

        if (nameField) {
            nameField.classList.add('portal-settings-hidden-name-field');
            section.appendChild(nameField);
        }

        let row = section.querySelector('.portal-settings-info-row');
        if (!row) {
            row = document.createElement('div');
            row.className = 'portal-settings-info-row';
            title.insertAdjacentElement('afterend', row);
        }

        row.appendChild(emailField);
        row.appendChild(contactField);
        row.appendChild(contactType);
        whatsappField.classList.add('portal-settings-whatsapp-row');
        row.insertAdjacentElement('afterend', whatsappField);
    };

    const fixAddressRow = () => {
        const cepField = document.getElementById('portal-full-cep')?.closest('.portal-settings-field');
        const addressField = document.getElementById('portal-full-endereco')?.closest('.portal-settings-field');
        const numberField = document.getElementById('portal-full-numero')?.closest('.portal-settings-field');
        const districtField = document.getElementById('portal-full-bairro')?.closest('.portal-settings-field');
        const section = cepField?.closest('[data-settings-section="cadastro"]');
        const addressTitle = Array.from(section?.querySelectorAll('.portal-settings-section-title') || []).find((item) => item.textContent.includes('Endereço'));
        if (!section || !addressTitle || !cepField || !addressField || !numberField || !districtField) return;

        let row = section.querySelector('.portal-settings-address-row');
        if (!row) {
            row = document.createElement('div');
            row.className = 'portal-settings-address-row';
            addressTitle.insertAdjacentElement('afterend', row);
        }

        row.appendChild(cepField);
        row.appendChild(addressField);
        row.appendChild(numberField);
        row.appendChild(districtField);
    };

    const moveBranchToPersonalization = () => {
        const section = document.querySelector('[data-settings-section="personalizacao"]');
        const store = document.getElementById('portal-full-store')?.closest('.portal-settings-field');
        const branch = document.getElementById('portal-full-branch')?.closest('.portal-settings-field');
        const branchRow = document.querySelector('.portal-settings-branch-row');
        const title = section?.querySelector('.portal-settings-section-title');
        if (!section || !title || !store || !branch || !branchRow) return;

        let row = section.querySelector('.portal-settings-personalization-row');
        if (!row) {
            row = document.createElement('div');
            row.className = 'portal-settings-personalization-row';
            title.insertAdjacentElement('afterend', row);
        }

        row.appendChild(store);
        row.appendChild(branch);
        row.appendChild(branchRow);
    };

    const applyCadastroLabels = () => {
        const title = document.querySelector('[data-settings-section="cadastro"] .portal-settings-section-title');
        if (title) {
            title.childNodes.forEach((node) => {
                if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
                    node.textContent = ' Informação';
                }
            });
        }

        const fantasiaLabel = document.querySelector('label[for="portal-full-fantasia"]');
        if (fantasiaLabel) {
            fantasiaLabel.textContent = 'Nome fantasia';
        }

        const fantasiaInput = document.getElementById('portal-full-fantasia');
        if (fantasiaInput && fantasiaInput.placeholder === 'Nome que aparece no portal') {
            fantasiaInput.placeholder = 'Nome fantasia';
        }

        const storeLabel = document.querySelector('label[for="portal-full-store"]');
        if (storeLabel) {
            storeLabel.textContent = 'Nome da loja';
        }
    };

    const boot = () => {
        fixInfoRow();
        fixAddressRow();
        moveBranchToPersonalization();
        applyCadastroLabels();
        bindPasteMasks();
        window.setTimeout(() => { fixInfoRow(); fixAddressRow(); moveBranchToPersonalization(); applyCadastroLabels(); bindPasteMasks(); }, 500);
        window.setTimeout(() => { fixInfoRow(); fixAddressRow(); moveBranchToPersonalization(); applyCadastroLabels(); bindPasteMasks(); }, 1500);
        window.setTimeout(() => { fixInfoRow(); fixAddressRow(); moveBranchToPersonalization(); applyCadastroLabels(); bindPasteMasks(); }, 2300);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
</script>
