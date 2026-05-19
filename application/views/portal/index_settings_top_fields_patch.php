<?php
include __DIR__.'/index_settings_name_email_wide_patch.php';
?>
<style>
    html body .portal-settings-top-grid {
        display: grid !important;
        grid-template-columns: minmax(380px, 1.15fr) minmax(380px, 1.15fr) !important;
        gap: 1.05rem !important;
        width: 100% !important;
        max-width: 100% !important;
        margin-bottom: 1rem !important;
        align-items: stretch !important;
    }

    html body .portal-settings-contact-line {
        display: grid !important;
        grid-template-columns: minmax(360px, 1fr) minmax(280px, 0.72fr) !important;
        gap: 1.05rem !important;
        width: 100% !important;
        max-width: 100% !important;
        margin-bottom: 1rem !important;
        align-items: stretch !important;
    }

    html body .portal-settings-top-grid .portal-settings-field,
    html body .portal-settings-contact-line .portal-settings-field,
    html body .portal-settings-contact-line .portal-settings-contact-type {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        margin: 0 !important;
    }

    html body .portal-settings-contact-line .portal-settings-contact-type {
        display: flex !important;
        align-items: center !important;
        align-content: center !important;
        height: 100% !important;
    }

    html body .portal-settings-whatsapp-row {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 0 1rem !important;
    }

    @media (max-width: 900px) {
        html body .portal-settings-top-grid,
        html body .portal-settings-contact-line {
            grid-template-columns: 1fr !important;
        }
    }
</style>
<script>
(function () {
    const unwrapLegacyRows = (section) => {
        ['.portal-settings-name-email-row', '.portal-settings-contact-row'].forEach((selector) => {
            section.querySelectorAll(selector).forEach((row) => {
                while (row.firstChild) {
                    row.parentNode.insertBefore(row.firstChild, row);
                }
                row.remove();
            });
        });
    };

    const organizeTopFields = () => {
        const nameField = document.getElementById('portal-full-nome')?.closest('.portal-settings-field');
        const emailField = document.getElementById('portal-full-email')?.closest('.portal-settings-field');
        const contactField = document.getElementById('portal-full-telefone')?.closest('.portal-settings-field');
        const whatsappField = document.getElementById('portal-full-whatsapp')?.closest('.portal-settings-field');
        const contactType = document.querySelector('.portal-settings-contact-type');
        const section = nameField?.closest('[data-settings-section="cadastro"]');
        if (!section || !nameField || !emailField || !contactField || !whatsappField || !contactType) return;

        unwrapLegacyRows(section);
        section.dataset.nameEmailWideReady = '';
        section.dataset.contactLayoutReady = '';

        let topRow = section.querySelector('.portal-settings-top-grid');
        if (!topRow) {
            topRow = document.createElement('div');
            topRow.className = 'portal-settings-top-grid';
            nameField.insertAdjacentElement('beforebegin', topRow);
        }
        topRow.appendChild(nameField);
        topRow.appendChild(emailField);

        let contactRow = section.querySelector('.portal-settings-contact-line');
        if (!contactRow) {
            contactRow = document.createElement('div');
            contactRow.className = 'portal-settings-contact-line';
            topRow.insertAdjacentElement('afterend', contactRow);
        }
        contactRow.appendChild(contactField);
        contactRow.appendChild(contactType);

        whatsappField.classList.add('portal-settings-whatsapp-row');
        contactRow.insertAdjacentElement('afterend', whatsappField);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            organizeTopFields();
            window.setTimeout(organizeTopFields, 650);
            window.setTimeout(organizeTopFields, 1200);
        }, { once: true });
    } else {
        organizeTopFields();
        window.setTimeout(organizeTopFields, 650);
        window.setTimeout(organizeTopFields, 1200);
    }
})();
</script>
