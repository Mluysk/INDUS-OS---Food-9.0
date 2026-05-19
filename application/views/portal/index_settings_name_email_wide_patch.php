<?php
include __DIR__.'/index_settings_whatsapp_below_patch.php';
?>
<style>
    html body .portal-settings-name-email-row {
        display: grid !important;
        grid-template-columns: minmax(320px, 1fr) minmax(320px, 1fr) !important;
        gap: 1rem !important;
        width: 100% !important;
        max-width: 100% !important;
        margin-bottom: 0.85rem !important;
    }

    html body .portal-settings-name-email-row .portal-settings-field {
        width: 100% !important;
    }

    html body .portal-settings-name-email-row input {
        width: 100% !important;
    }

    @media (max-width: 820px) {
        html body .portal-settings-name-email-row {
            grid-template-columns: 1fr !important;
        }
    }
</style>
<script>
(function () {
    const arrangeNameEmail = () => {
        const nameField = document.getElementById('portal-full-nome')?.closest('.portal-settings-field');
        const emailField = document.getElementById('portal-full-email')?.closest('.portal-settings-field');
        const section = nameField?.closest('[data-settings-section="cadastro"]');
        if (!nameField || !emailField || !section || section.dataset.nameEmailWideReady === '1') return;
        section.dataset.nameEmailWideReady = '1';
        const row = document.createElement('div');
        row.className = 'portal-settings-name-email-row';
        nameField.insertAdjacentElement('beforebegin', row);
        row.appendChild(nameField);
        row.appendChild(emailField);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            arrangeNameEmail();
            window.setTimeout(arrangeNameEmail, 500);
            window.setTimeout(arrangeNameEmail, 1000);
        }, { once: true });
    } else {
        arrangeNameEmail();
        window.setTimeout(arrangeNameEmail, 500);
        window.setTimeout(arrangeNameEmail, 1000);
    }
})();
</script>
