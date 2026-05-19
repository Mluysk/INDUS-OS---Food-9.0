<style>
    .cliente-tab-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .cliente-tab-button [data-lucide] {
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
        addIcon(document.querySelector('[data-cliente-tab="novo"]'), 'circle-plus');
        addIcon(document.querySelector('[data-cliente-tab="lista"]'), 'list');
        if (window.lucide) {
            window.lucide.createIcons({ elements: document.querySelectorAll('.cliente-tab-button [data-lucide]') });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();
</script>
