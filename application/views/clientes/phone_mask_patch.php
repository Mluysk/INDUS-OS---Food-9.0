<script>
(() => {
    const form = document.querySelector('.cliente-form');
    if (!form || form.dataset.phoneMaskReady === '1') return;
    form.dataset.phoneMaskReady = '1';

    const contactInput = form.querySelector('input[name="telefone"]');
    const whatsappInput = form.querySelector('input[name="whatsapp"]');
    const typeInputs = Array.from(form.querySelectorAll('input[name="contato_tipo"]'));

    const digitsOnly = (value, limit) => String(value || '').replace(/\D+/g, '').slice(0, limit);

    const formatPhone = (value, mobile) => {
        const digits = digitsOnly(value, mobile ? 11 : 10);
        if (!digits) return '';
        if (digits.length < 3) return `(${digits}`;

        const ddd = digits.slice(0, 2);
        const number = digits.slice(2);
        if (!number) return `(${ddd})`;

        const firstBlockLength = mobile && number.length > 8 ? 5 : 4;
        const firstBlock = number.slice(0, firstBlockLength);
        const secondBlock = number.slice(firstBlockLength, firstBlockLength + 4);
        return `(${ddd}) ${firstBlock}${secondBlock ? `-${secondBlock}` : ''}`;
    };

    const isMobileContact = () => (
        form.querySelector('input[name="contato_tipo"]:checked')?.value || 'celular'
    ) === 'celular';

    const applyContactMask = () => {
        if (!contactInput) return;
        const digits = digitsOnly(contactInput.value, 11);
        const detectedType = digits.length === 11 ? 'celular' : (digits.length === 10 ? 'telefone' : '');
        const mobile = detectedType ? detectedType === 'celular' : isMobileContact();
        contactInput.value = formatPhone(digits, mobile);
        contactInput.maxLength = 15;
        contactInput.inputMode = 'numeric';
        contactInput.placeholder = mobile ? '(00) 00000-0000' : '(00) 0000-0000';
        if (detectedType) {
            const option = typeInputs.find((input) => input.value === detectedType);
            if (option && !option.checked) {
                option.checked = true;
                option.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    };

    const applyWhatsappMask = () => {
        if (!whatsappInput) return;
        whatsappInput.value = formatPhone(whatsappInput.value, true);
        whatsappInput.maxLength = 15;
        whatsappInput.inputMode = 'numeric';
        whatsappInput.placeholder = '(00) 00000-0000';
    };

    contactInput?.addEventListener('input', applyContactMask);
    contactInput?.addEventListener('paste', () => window.setTimeout(applyContactMask, 0));
    contactInput?.addEventListener('focus', applyContactMask);
    whatsappInput?.addEventListener('input', applyWhatsappMask);
    whatsappInput?.addEventListener('paste', () => window.setTimeout(applyWhatsappMask, 0));
    whatsappInput?.addEventListener('focus', applyWhatsappMask);
    typeInputs.forEach((input) => input.addEventListener('change', applyContactMask));

    form.addEventListener('reset', () => window.setTimeout(() => {
        applyContactMask();
        applyWhatsappMask();
    }, 0));

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.js-cliente-detalhe, [data-modal-edit]')) return;
        window.setTimeout(() => {
            applyContactMask();
            applyWhatsappMask();
        }, 0);
    });

    applyContactMask();
    applyWhatsappMask();
})();
</script>
