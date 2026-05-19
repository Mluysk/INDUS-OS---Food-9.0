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

    const fillEmailFromCnpj = async (cnpj) => {
        if (cnpj.length !== 14 || cnpj === lastEmailCnpj) return;
        lastEmailCnpj = cnpj;
        try {
            const response = await fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`);
            if (!response.ok) return;
            const data = await response.json();
            const email = findEmail(data);
            const emailInput = document.getElementById('registro-email');
            if (emailInput && email) {
                emailInput.value = email;
                emailInput.dispatchEvent(new Event('input', { bubbles: true }));
                emailInput.dispatchEvent(new Event('change', { bubbles: true }));
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
            if (cnpj.length === 14) fillEmailFromCnpj(cnpj);
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
