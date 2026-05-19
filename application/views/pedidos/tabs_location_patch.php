<style>
    .pedidos-top-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin: -0.25rem 0 1rem;
    }

    .pedidos-top-tabs .pedidos-tab {
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 999px;
        padding: 8px 18px;
        min-height: 42px;
        color: var(--text, #111827);
        background: var(--surface-alt, #f8fafc);
        font-weight: 700;
        box-shadow: none;
    }

    .pedidos-top-tabs .pedidos-tab.is-active {
        color: #fff;
        background: var(--primary, #dc2626);
        border-color: transparent;
        box-shadow: none;
    }

    .pedidos-tabs-card > .pedidos-tabs-header {
        display: none !important;
    }

    .pedidos-tabs-card {
        margin-top: 0;
    }

    .pedidos-tab-panel h3:first-child {
        margin-top: 0;
    }

    @media (max-width: 720px) {
        .pedidos-top-tabs {
            margin-top: 0;
        }

        .pedidos-top-tabs .pedidos-tab {
            flex: 1 1 auto;
            padding-inline: 12px;
        }
    }
</style>

<script>
    (() => {
        const tabs = document.querySelector('.pedidos-tabs');
        const panel = document.querySelector('.pedidos-tabs-card');
        if (!tabs || !panel || tabs.classList.contains('pedidos-top-tabs')) {
            return;
        }

        tabs.classList.add('pedidos-top-tabs');
        panel.parentNode.insertBefore(tabs, panel);
    })();
</script>
