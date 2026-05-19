<style>
    #panel-lista-pedidos .pix-inline .pix-key {
        display: none !important;
    }

    #panel-lista-pedidos .pix-inline {
        padding-left: 2px !important;
    }

    #panel-lista-pedidos .pix-toggle {
        font-weight: 600 !important;
    }
</style>

<script>
(function () {
    const defaultPixKey = <?= json_encode($notePixKey ?? '', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

    document.querySelectorAll('#panel-lista-pedidos .pix-key').forEach((input) => {
        input.value = defaultPixKey;
        input.setAttribute('hidden', 'hidden');
        input.setAttribute('aria-hidden', 'true');
        input.tabIndex = -1;
    });

    document.querySelectorAll('#panel-lista-pedidos .nota-print-btn').forEach((button) => {
        if (button.dataset.pixConfigPatched === '1') {
            return;
        }
        button.dataset.pixConfigPatched = '1';
        button.addEventListener('click', () => {
            const row = button.closest('tr');
            const keyInput = row ? row.querySelector('.pix-key') : null;
            if (keyInput) {
                keyInput.value = defaultPixKey;
            }
        }, true);
    });
})();
</script>
