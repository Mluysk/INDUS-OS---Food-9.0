<?php
include __DIR__.'/index_settings_neutral_cards_patch.php';
?>
<style>
    html body .portal-settings-contact-row {
        display: grid !important;
        grid-template-columns: minmax(240px, 1fr) minmax(220px, 0.8fr) !important;
        gap: 0.85rem !important;
        align-items: stretch !important;
        max-width: 100% !important;
    }

    html body .portal-settings-contact-row .portal-settings-contact-type {
        margin: 0 !important;
        height: 100%;
        align-content: center;
    }

    html body .portal-settings-whatsapp-row {
        max-width: min(520px, 100%) !important;
    }

    @media (max-width: 760px) {
        html body .portal-settings-contact-row {
            grid-template-columns: 1fr !important;
        }

        html body .portal-settings-whatsapp-row {
            max-width: 100% !important;
        }
    }
</style>
<script>
(function () {
    const arrangeContact = () => {
        const contato = document.getElementById('portal-full-telefone')?.closest('.portal-settings-field');
        const whatsapp = document.getElementById('portal-full-whatsapp')?.closest('.portal-settings-field');
        const tipo = document.querySelector('.portal-settings-contact-type');
        const section = contato?.closest('[data-settings-section="cadastro"]');
        if (!contato || !whatsapp || !tipo || !section || section.dataset.contactLayoutReady === '1') return;
        section.dataset.contactLayoutReady = '1';

        const row = document.createElement('div');
        row.className = 'portal-settings-contact-row';
        contato.insertAdjacentElement('beforebegin', row);
        row.appendChild(contato);
        row.appendChild(tipo);
        whatsapp.classList.add('portal-settings-whatsapp-row');
        row.insertAdjacentElement('afterend', whatsapp);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            arrangeContact();
            window.setTimeout(arrangeContact, 450);
            window.setTimeout(arrangeContact, 900);
        }, { once: true });
    } else {
        arrangeContact();
        window.setTimeout(arrangeContact, 450);
        window.setTimeout(arrangeContact, 900);
    }
})();
</script>
