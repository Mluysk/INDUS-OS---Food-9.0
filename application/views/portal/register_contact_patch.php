<?php
include __DIR__.'/register_icons_patch.php';
?>
<style>
    html body .portal-cnpj-auto-hint {
        display: flex;
        align-items: center;
        gap: 0.48rem;
        margin: 0.45rem 0 0;
        color: var(--portal-muted, rgba(71, 85, 105, 0.82));
        font-size: 0.86rem;
        font-weight: 750;
    }

    html body .portal-cnpj-auto-hint::before {
        content: '✦';
        color: var(--primary, #dc2626);
        font-size: 1.05rem;
        line-height: 1;
    }

    html body .portal-contact-type {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        flex-wrap: wrap;
        margin: 0.5rem 0 1rem;
        color: var(--portal-muted, rgba(71, 85, 105, 0.82));
        font-weight: 750;
    }

    html body .portal-contact-type__title {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        color: var(--text, #111827);
        font-size: 0.88rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    html body .portal-contact-type__title .portal-form-icon {
        width: 17px;
        height: 17px;
        color: var(--text-muted, #64748b);
    }

    html body .portal-contact-type label {
        display: inline-flex;
        align-items: center;
        gap: 0.38rem;
        margin: 0;
        cursor: pointer;
        font-size: 0.9rem;
    }

    html body .portal-contact-type input[type="radio"] {
        width: 16px;
        height: 16px;
        accent-color: var(--primary, #dc2626);
    }

    html body .portal-whatsapp-field {
        margin-bottom: 1rem;
    }
</style>
<script>
(function () {
    const onlyDigits = (value) => String(value || '').replace(/\D+/g, '');

    const formatPhoneValue = (value) => {
        const digits = onlyDigits(value).slice(0, 11);
        if (digits.length > 10) {
            return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
        }
        if (digits.length > 6) {
            return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
        }
        if (digits.length > 2) {
            return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
        }
        return digits;
    };

    const maskPhoneInput = (input) => {
        if (!input || input.dataset.phoneMaskReady === '1') return;
        input.dataset.phoneMaskReady = '1';
        input.maxLength = 15;
        input.addEventListener('input', () => {
            const cursorAtEnd = input.selectionStart === input.value.length;
            input.value = formatPhoneValue(input.value);
            if (cursorAtEnd) {
                input.setSelectionRange(input.value.length, input.value.length);
            }
        });
    };

    const mountCnpjHint = () => {
        const cnpjInput = document.getElementById('registro-cnpj');
        if (!cnpjInput || cnpjInput.dataset.autoHintReady === '1') return;
        cnpjInput.dataset.autoHintReady = '1';
        const hint = document.createElement('p');
        hint.className = 'portal-cnpj-auto-hint';
        hint.textContent = 'Ao informar o CNPJ, os dados cadastrais disponíveis serão preenchidos automaticamente.';
        const feedback = cnpjInput.parentElement?.querySelector('.portal-cnpj-feedback');
        (feedback || cnpjInput).insertAdjacentElement('afterend', hint);
    };

    const mountContactFields = () => {
        const contatoInput = document.getElementById('registro-telefone');
        const form = document.getElementById('portal-register-form');
        if (!contatoInput || !form || contatoInput.dataset.contactPatchReady === '1') return;
        contatoInput.dataset.contactPatchReady = '1';

        const contatoLabel = document.querySelector('label[for="registro-telefone"]');
        if (contatoLabel) {
            const icon = contatoLabel.querySelector('.portal-form-icon')?.outerHTML || '';
            contatoLabel.innerHTML = `${icon}<span>Contato</span>`;
        }
        contatoInput.placeholder = '(11) 99999-0000';
        maskPhoneInput(contatoInput);

        const typeGroup = document.createElement('div');
        typeGroup.className = 'portal-contact-type';
        typeGroup.innerHTML = `
            <span class="portal-contact-type__title"><span class="portal-form-icon" data-lucide="phone-call"></span><span>Tipo do contato</span></span>
            <label><input type="radio" name="contato_tipo" value="celular" checked> Celular</label>
            <label><input type="radio" name="contato_tipo" value="telefone"> Telefone</label>
        `;
        contatoInput.insertAdjacentElement('afterend', typeGroup);

        const whatsappField = document.createElement('div');
        whatsappField.className = 'field-group portal-whatsapp-field';
        whatsappField.innerHTML = `
            <label for="registro-whatsapp" class="portal-icon-label"><span class="portal-form-icon" data-lucide="message-circle"></span><span>WhatsApp</span></label>
            <input type="tel" id="registro-whatsapp" name="whatsapp" placeholder="(11) 99999-0000">
        `;
        typeGroup.insertAdjacentElement('afterend', whatsappField);
        maskPhoneInput(whatsappField.querySelector('#registro-whatsapp'));

        const addressGrid = form.querySelector('.address-grid');
        if (addressGrid) {
            const moveBeforeAddress = [
                document.querySelector('label[for="registro-email"]'),
                document.getElementById('registro-email'),
                contatoLabel,
                contatoInput,
                typeGroup,
                whatsappField
            ].filter(Boolean);
            moveBeforeAddress.forEach((element) => {
                addressGrid.parentNode.insertBefore(element, addressGrid);
            });
        }

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    };

    const mount = () => {
        mountCnpjHint();
        mountContactFields();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mount, { once: true });
    } else {
        mount();
    }
})();
</script>
