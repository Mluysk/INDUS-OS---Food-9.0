<style>
    .pedidos-top-tabs .pedidos-tab,
    .pedidos-tabs .pedidos-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .pedidos-top-tabs .pedidos-tab [data-lucide],
    .pedidos-tabs .pedidos-tab [data-lucide] {
        width: 18px;
        height: 18px;
        flex: 0 0 auto;
    }
</style>

<script>
(function () {
    const addIcon = (button, icon) => {
        if (!button || button.dataset.tabIconApplied === '1') {
            return;
        }
        button.dataset.tabIconApplied = '1';
        const text = button.textContent.trim();
        button.innerHTML = `<span data-lucide="${icon}" aria-hidden="true"></span><span>${text}</span>`;
    };

    const run = () => {
        addIcon(document.querySelector('[data-tab-target="novo-pedido"]'), 'circle-plus');
        addIcon(document.querySelector('[data-tab-target="lista-pedidos"]'), 'list');
        if (window.lucide) {
            window.lucide.createIcons({ elements: document.querySelectorAll('.pedidos-tab [data-lucide]') });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();
</script>
