<?php
include __DIR__.'/index_settings_contact_layout_patch.php';
?>
<style>
    html body .portal-settings-whatsapp-row {
        width: 100% !important;
        max-width: 100% !important;
        margin-top: 0.85rem !important;
        grid-column: 1 / -1 !important;
        display: grid !important;
    }

    html body .portal-settings-whatsapp-row input {
        width: 100% !important;
    }
</style>
<script>
(function () {
    const forceWhatsappBelow = () => {
        const row = document.querySelector('.portal-settings-contact-row');
        const whatsapp = document.getElementById('portal-full-whatsapp')?.closest('.portal-settings-field');
        if (!row || !whatsapp) return;
        whatsapp.classList.add('portal-settings-whatsapp-row');
        if (whatsapp.previousElementSibling !== row) {
            row.insertAdjacentElement('afterend', whatsapp);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            forceWhatsappBelow();
            window.setTimeout(forceWhatsappBelow, 500);
            window.setTimeout(forceWhatsappBelow, 1000);
        }, { once: true });
    } else {
        forceWhatsappBelow();
        window.setTimeout(forceWhatsappBelow, 500);
        window.setTimeout(forceWhatsappBelow, 1000);
    }
})();
</script>
