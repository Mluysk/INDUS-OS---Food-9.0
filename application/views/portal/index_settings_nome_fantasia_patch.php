<?php
include __DIR__.'/index_settings_document_width_patch.php';
?>
<script>
(function () {
    const applyNomeFantasiaLabel = () => {
        const fantasiaLabel = document.querySelector('label[for="portal-full-fantasia"]');
        if (fantasiaLabel) {
            fantasiaLabel.textContent = 'Nome fantasia';
        }

        const fantasiaInput = document.getElementById('portal-full-fantasia');
        if (fantasiaInput && fantasiaInput.placeholder === 'Nome que aparece no portal') {
            fantasiaInput.placeholder = 'Nome fantasia';
        }

        const storeLabel = document.querySelector('label[for="portal-full-store"]');
        if (storeLabel) {
            storeLabel.textContent = 'Nome da loja';
        }

        const storeInput = document.getElementById('portal-full-store');
        if (storeInput && storeInput.placeholder === 'Nome fantasia') {
            storeInput.placeholder = 'Nome da loja';
        }
    };

    const boot = () => {
        applyNomeFantasiaLabel();
        window.setTimeout(applyNomeFantasiaLabel, 120);
        window.setTimeout(applyNomeFantasiaLabel, 420);
        const root = document.querySelector('.portal-app__panels') || document.body;
        if (!root || root.dataset.nomeFantasiaObserver === '1') return;
        root.dataset.nomeFantasiaObserver = '1';
        new MutationObserver(applyNomeFantasiaLabel).observe(root, { childList: true, subtree: true });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
</script>
