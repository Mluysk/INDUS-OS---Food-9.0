<?php
include __DIR__.'/index_state_field_width_patch.php';
?>
<style>
    html body [data-settings-section="cadastro"],
    html body [data-settings-section="cadastro"] .portal-settings-card,
    html body [data-settings-section="cadastro"] .portal-settings-content {
        overflow: visible !important;
    }

    html body .portal-settings-info-row {
        display: flex !important;
        flex-wrap: nowrap !important;
        gap: 1rem !important;
        align-items: stretch !important;
        justify-content: flex-start !important;
        width: 100% !important;
        max-width: none !important;
        min-width: calc((43ch + 22px) + 206px + 160px + 190px + 3rem) !important;
        overflow: visible !important;
    }

    html body .portal-settings-info-row .portal-settings-field:has(#portal-full-email) {
        flex: 0 0 calc(43ch + 22px) !important;
        width: calc(43ch + 22px) !important;
        min-width: calc(43ch + 22px) !important;
        max-width: calc(43ch + 22px) !important;
    }

    html body .portal-settings-info-row .portal-settings-field:has(#portal-full-telefone) {
        flex: 0 0 206px !important;
        width: 206px !important;
        min-width: 206px !important;
        max-width: 206px !important;
    }

    html body .portal-settings-info-row .portal-settings-contact-type {
        flex: 0 0 160px !important;
        width: 160px !important;
        min-width: 160px !important;
        max-width: 160px !important;
        padding-left: 0.72rem !important;
        padding-right: 0.72rem !important;
    }

    html body .portal-settings-info-row .portal-settings-contact-type label,
    html body .portal-settings-info-row .portal-settings-contact-type span {
        white-space: nowrap !important;
    }

    html body .portal-settings-info-row .portal-settings-whatsapp-row,
    html body .portal-settings-info-row .portal-settings-field:has(#portal-full-whatsapp) {
        flex: 0 0 190px !important;
        width: 190px !important;
        min-width: 190px !important;
        max-width: 190px !important;
        margin: 0 !important;
    }

    html body .portal-settings-info-row #portal-full-whatsapp {
        width: 100% !important;
        max-width: 100% !important;
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }

    @media (max-width: 700px) {
        html body .portal-settings-info-row {
            display: grid !important;
            grid-template-columns: 1fr !important;
            min-width: 0 !important;
        }

        html body .portal-settings-info-row .portal-settings-field:has(#portal-full-email),
        html body .portal-settings-info-row .portal-settings-field:has(#portal-full-telefone),
        html body .portal-settings-info-row .portal-settings-contact-type,
        html body .portal-settings-info-row .portal-settings-whatsapp-row,
        html body .portal-settings-info-row .portal-settings-field:has(#portal-full-whatsapp) {
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            flex-basis: auto !important;
        }
    }
</style>
<script>
(function () {
    const moveWhatsappBesideContactType = () => {
        const row = document.querySelector('.portal-settings-info-row');
        const emailField = document.getElementById('portal-full-email')?.closest('.portal-settings-field');
        const contactField = document.getElementById('portal-full-telefone')?.closest('.portal-settings-field');
        const contactType = document.querySelector('.portal-settings-contact-type');
        const whatsappField = document.getElementById('portal-full-whatsapp')?.closest('.portal-settings-field');
        if (!row || !emailField || !contactField || !contactType || !whatsappField) return;

        row.style.display = 'flex';
        row.style.flexWrap = 'nowrap';
        row.style.gap = '1rem';
        row.style.alignItems = 'stretch';
        row.style.justifyContent = 'flex-start';
        row.style.maxWidth = 'none';
        row.style.overflow = 'visible';

        if (emailField.parentElement !== row) row.appendChild(emailField);
        if (contactField.parentElement !== row) row.appendChild(contactField);
        if (contactType.parentElement !== row) row.appendChild(contactType);
        if (whatsappField.previousElementSibling !== contactType) contactType.insertAdjacentElement('afterend', whatsappField);

        emailField.style.flex = '0 0 calc(43ch + 22px)';
        emailField.style.width = 'calc(43ch + 22px)';
        emailField.style.minWidth = 'calc(43ch + 22px)';
        emailField.style.maxWidth = 'calc(43ch + 22px)';

        contactField.style.flex = '0 0 206px';
        contactField.style.width = '206px';
        contactField.style.minWidth = '206px';
        contactField.style.maxWidth = '206px';

        contactType.style.flex = '0 0 160px';
        contactType.style.width = '160px';
        contactType.style.minWidth = '160px';
        contactType.style.maxWidth = '160px';

        whatsappField.classList.add('portal-settings-whatsapp-row');
        whatsappField.style.flex = '0 0 190px';
        whatsappField.style.width = '190px';
        whatsappField.style.minWidth = '190px';
        whatsappField.style.maxWidth = '190px';
        whatsappField.style.margin = '0';
    };

    const boot = () => {
        moveWhatsappBesideContactType();
        [250, 650, 1200, 1700, 2450, 3100, 4300].forEach((delay) => {
            window.setTimeout(moveWhatsappBesideContactType, delay);
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
</script>
