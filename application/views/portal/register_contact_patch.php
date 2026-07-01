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
        gap: 0.5rem;
        flex-wrap: nowrap;
        margin: 0.42rem 0 0.82rem;
        min-height: 64px;
        padding: 6px 10px;
        border: 1px solid color-mix(in srgb, var(--primary, #dc2626) 10%, var(--portal-card-border, rgba(15, 23, 42, 0.08)));
        border-radius: 16px;
        background: color-mix(in srgb, var(--primary, #dc2626) 3%, #ffffff);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
        color: var(--portal-muted, rgba(71, 85, 105, 0.82));
        font-weight: 750;
    }

    html[data-theme="dark"] body .portal-contact-type {
        background: color-mix(in srgb, var(--primary, #dc2626) 10%, rgba(15, 23, 42, 0.86));
        border-color: color-mix(in srgb, var(--primary, #dc2626) 18%, rgba(148, 163, 184, 0.16));
        color: #cbd5e1;
        box-shadow: inset 0 1px 0 rgba(148, 163, 184, 0.08);
    }

    html body .portal-contact-type__title {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        flex: 0 0 auto;
        min-height: 46px;
        padding: 0 8px;
        color: var(--text, #111827);
        font-size: 0.82rem;
        font-weight: 850;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    html body .portal-contact-type__title .portal-form-icon {
        width: 15px;
        height: 15px;
        color: var(--text-muted, #64748b);
    }

    html body .portal-contact-type label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.42rem;
        flex: 0 0 auto;
        min-height: 48px;
        margin: 0;
        padding: 0.35rem 0.9rem;
        border: 1px solid color-mix(in srgb, var(--primary, #dc2626) 10%, rgba(148, 163, 184, 0.26));
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.76);
        color: var(--text, #111827);
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 800;
        box-shadow: none;
        transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
    }

    html[data-theme="dark"] body .portal-contact-type label {
        background: rgba(15, 23, 42, 0.78);
        border-color: rgba(148, 163, 184, 0.2);
        color: #e2e8f0;
    }

    html body .portal-contact-type label:has(input[type="radio"]:checked) {
        border-color: color-mix(in srgb, var(--primary, #dc2626) 42%, transparent);
        background: var(--primary, #dc2626);
        color: #fff;
        box-shadow: 0 10px 22px -18px color-mix(in srgb, var(--primary, #dc2626) 76%, transparent);
    }

    html body .portal-contact-type label:hover {
        transform: none;
    }

    html body .portal-contact-type input[type="radio"] {
        width: 13px;
        height: 13px;
        accent-color: var(--primary, #dc2626);
    }

    html body .portal-contact-type label:has(input[type="radio"]:checked) input[type="radio"] {
        accent-color: #fff;
    }

    @media (max-width: 640px) {
        html body .portal-contact-type {
            align-items: center;
            gap: 4px;
            min-height: 54px;
            padding: 5px 6px;
        }

        html body .portal-contact-type__title {
            width: auto;
            min-width: 0;
            min-height: 40px;
            padding: 0 4px;
            font-size: 0.66rem;
        }

        html body .portal-contact-type label {
            flex: 0 0 auto;
            min-height: 40px;
            padding: 0.22rem 0.5rem;
            font-size: 0.75rem;
        }
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
            <input type="tel" id="registro-whatsapp" name="whatsapp" placeholder="(11) 99999-0000" required>
        `;
        typeGroup.insertAdjacentElement('afterend', whatsappField);
        maskPhoneInput(whatsappField.querySelector('#registro-whatsapp'));

        const addressGrid = form.querySelector('.address-grid');
        if (addressGrid) {
            const moveBeforeAddress = [
                document.getElementById('registro-email')?.closest('.field-group'),
                contatoInput.closest('.field-group'),
                typeGroup,
                whatsappField
            ].filter(Boolean);
            [...new Set(moveBeforeAddress)].forEach((element) => {
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
