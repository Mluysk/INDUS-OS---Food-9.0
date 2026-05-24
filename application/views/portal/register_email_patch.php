<?php
include __DIR__.'/register_contact_patch.php';
?>
<script>
(function () {
    const onlyDigits = (value) => String(value || '').replace(/\D+/g, '');
    let lastEmailCnpj = '';

    const firstValue = (...values) => {
        for (const value of values) {
            const normalized = String(value || '').trim();
            if (normalized) return normalized;
        }
        return '';
    };

    const findEmail = (data) => firstValue(
        data.email,
        data.correio_eletronico,
        data.email_empresa,
        data.email_estabelecimento,
        data.estabelecimento && data.estabelecimento.email,
        data.estabelecimento && data.estabelecimento.correio_eletronico
    );

    const formatPhone = (value) => {
        const digits = onlyDigits(value).slice(0, 11);
        if (digits.length === 11) return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
        if (digits.length === 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
        return digits;
    };

    const findPhone = (data) => firstValue(
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

    const setFieldValue = (id, value) => {
        const input = document.getElementById(id);
        if (!input || !value) return;
        input.value = value;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const syncContactType = (phone) => {
        const digits = onlyDigits(phone);
        const type = digits.length === 11 ? 'celular' : 'telefone';
        const option = document.querySelector(`input[name="contato_tipo"][value="${type}"]`);
        if (option) {
            option.checked = true;
            option.dispatchEvent(new Event('change', { bubbles: true }));
        }
    };

    const fillContactFromCnpj = async (cnpj) => {
        if (cnpj.length !== 14 || cnpj === lastEmailCnpj) return;
        lastEmailCnpj = cnpj;
        try {
            const response = await fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`);
            if (!response.ok) return;
            const data = await response.json();
            const email = findEmail(data);
            const phone = formatPhone(findPhone(data));
            setFieldValue('registro-email', email);
            if (phone) {
                setFieldValue('registro-telefone', phone);
                syncContactType(phone);
            }
        } catch (error) {
            lastEmailCnpj = '';
        }
    };

    const mountEmailPatch = () => {
        const cnpjInput = document.getElementById('registro-cnpj');
        if (!cnpjInput || cnpjInput.dataset.emailPatchReady === '1') return;
        cnpjInput.dataset.emailPatchReady = '1';

        const handle = () => {
            const cnpj = onlyDigits(cnpjInput.value);
            if (cnpj.length < 14) lastEmailCnpj = '';
            if (cnpj.length === 14) fillContactFromCnpj(cnpj);
        };

        cnpjInput.addEventListener('input', handle);
        cnpjInput.addEventListener('blur', handle);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mountEmailPatch, { once: true });
    } else {
        mountEmailPatch();
    }
})();
</script>
