<?php
include __DIR__.'/index_settings_top_fields_patch.php';
?>
<style>
    html body .portal-settings-first-line {
        display: grid !important;
        grid-template-columns: minmax(260px, 1.25fr) minmax(260px, 1.25fr) minmax(210px, 0.9fr) minmax(220px, 0.78fr) !important;
        gap: 0.9rem !important;
        width: 100% !important;
        max-width: 100% !important;
        margin-bottom: 1rem !important;
        align-items: stretch !important;
    }

    html body .portal-settings-first-line .portal-settings-field,
    html body .portal-settings-first-line .portal-settings-contact-type {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        margin: 0 !important;
    }

    html body .portal-settings-first-line .portal-settings-contact-type {
        display: flex !important;
        align-items: center !important;
        align-content: center !important;
        height: 100% !important;
    }

    html body .portal-settings-top-grid,
    html body .portal-settings-contact-line {
        display: contents !important;
    }

    @media (max-width: 1180px) {
        html body .portal-settings-first-line {
            grid-template-columns: repeat(2, minmax(260px, 1fr)) !important;
        }
    }

    @media (max-width: 700px) {
        html body .portal-settings-first-line {
            grid-template-columns: 1fr !important;
        }
    }
</style>
<script>
(function () {
    const unwrap = (section) => {
        ['.portal-settings-top-grid', '.portal-settings-contact-line', '.portal-settings-name-email-row', '.portal-settings-contact-row'].forEach((selector) => {
            section.querySelectorAll(selector).forEach((row) => {
                while (row.firstChild) row.parentNode.insertBefore(row.firstChild, row);
                row.remove();
            });
        });
    };

    const arrangeFirstLine = () => {
        const nameField = document.getElementById('portal-full-nome')?.closest('.portal-settings-field');
        const emailField = document.getElementById('portal-full-email')?.closest('.portal-settings-field');
        const contactField = document.getElementById('portal-full-telefone')?.closest('.portal-settings-field');
        const whatsappField = document.getElementById('portal-full-whatsapp')?.closest('.portal-settings-field');
        const contactType = document.querySelector('.portal-settings-contact-type');
        const section = nameField?.closest('[data-settings-section="cadastro"]');
        if (!section || !nameField || !emailField || !contactField || !whatsappField || !contactType) return;

        unwrap(section);
        section.dataset.nameEmailWideReady = '';
        section.dataset.contactLayoutReady = '';

        let firstLine = section.querySelector('.portal-settings-first-line');
        if (!firstLine) {
            firstLine = document.createElement('div');
            firstLine.className = 'portal-settings-first-line';
            nameField.insertAdjacentElement('beforebegin', firstLine);
        }
        firstLine.appendChild(nameField);
        firstLine.appendChild(emailField);
        firstLine.appendChild(contactField);
        firstLine.appendChild(contactType);

        whatsappField.classList.add('portal-settings-whatsapp-row');
        firstLine.insertAdjacentElement('afterend', whatsappField);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            arrangeFirstLine();
            window.setTimeout(arrangeFirstLine, 700);
            window.setTimeout(arrangeFirstLine, 1300);
        }, { once: true });
    } else {
        arrangeFirstLine();
        window.setTimeout(arrangeFirstLine, 700);
        window.setTimeout(arrangeFirstLine, 1300);
    }
})();
</script>
