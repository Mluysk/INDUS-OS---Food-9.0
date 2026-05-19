<style>
    #panel-lista-pedidos > div[style*="overflow-x"] {
        overflow-x: visible !important;
    }

    #panel-lista-pedidos .data-table {
        table-layout: fixed !important;
        width: 100% !important;
        min-width: 0 !important;
    }

    #panel-lista-pedidos .data-table thead th {
        padding: 14px 10px !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.06em !important;
    }

    #panel-lista-pedidos .data-table tbody td {
        padding: 14px 10px !important;
        font-size: 0.88rem !important;
        line-height: 1.22 !important;
    }

    #panel-lista-pedidos .data-table th:nth-child(1),
    #panel-lista-pedidos .data-table td:nth-child(1) {
        width: 44px !important;
    }

    #panel-lista-pedidos .data-table th:nth-child(2),
    #panel-lista-pedidos .data-table td:nth-child(2) {
        width: 72px !important;
    }

    #panel-lista-pedidos .data-table th:nth-child(3),
    #panel-lista-pedidos .data-table td:nth-child(3) {
        width: 145px !important;
        overflow-wrap: anywhere !important;
    }

    #panel-lista-pedidos .data-table th:nth-child(4),
    #panel-lista-pedidos .data-table td:nth-child(4) {
        width: 104px !important;
    }

    #panel-lista-pedidos .data-table th:nth-child(5),
    #panel-lista-pedidos .data-table td:nth-child(5) {
        width: 104px !important;
        white-space: nowrap !important;
    }

    #panel-lista-pedidos .data-table th:nth-child(6),
    #panel-lista-pedidos .data-table td:nth-child(6) {
        width: 160px !important;
        white-space: nowrap !important;
    }

    #panel-lista-pedidos .pedido-created-at {
        display: inline-flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 3px !important;
        white-space: nowrap !important;
        line-height: 1.05 !important;
    }

    #panel-lista-pedidos .pedido-created-date {
        font-size: 1em !important;
        font-weight: 500 !important;
    }

    #panel-lista-pedidos .pedido-created-time {
        font-size: 0.74em !important;
        color: var(--text-muted, #64748b) !important;
        font-weight: 600 !important;
    }

    #panel-lista-pedidos .data-table th:nth-child(7),
    #panel-lista-pedidos .data-table td:nth-child(7) {
        width: 280px !important;
    }

    #panel-lista-pedidos .pedido-actions {
        gap: 8px !important;
        max-width: 280px !important;
    }

    #panel-lista-pedidos .pedido-action-buttons {
        gap: 8px !important;
        max-width: 100% !important;
    }

    #panel-lista-pedidos .pedido-action-buttons .button {
        min-width: 86px !important;
        min-height: 38px !important;
        padding: 0 12px !important;
        border-radius: 12px !important;
        font-size: 0.82rem !important;
    }

    #panel-lista-pedidos .pedido-action-buttons .pedido-icon-btn,
    #panel-lista-pedidos .pedido-action-buttons button.pedido-icon-btn,
    #panel-lista-pedidos .pedido-action-buttons a.pedido-icon-btn,
    #panel-lista-pedidos .pedido-edit-btn.pedido-icon-btn,
    #panel-lista-pedidos .nota-print-btn.pedido-icon-btn,
    #panel-lista-pedidos .pedido-delete-link.pedido-icon-btn {
        box-sizing: border-box !important;
        display: inline-flex !important;
        flex: 0 0 39px !important;
        align-items: center !important;
        justify-content: center !important;
        width: 39px !important;
        min-width: 39px !important;
        max-width: 39px !important;
        height: 39px !important;
        min-height: 39px !important;
        max-height: 39px !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 12px !important;
        line-height: 1 !important;
    }

    #panel-lista-pedidos .pedido-icon-btn svg {
        width: 16px !important;
        height: 16px !important;
    }

    #panel-lista-pedidos .pix-inline {
        gap: 5px !important;
        max-width: 100% !important;
        padding-left: 1px !important;
    }

    #panel-lista-pedidos .pix-toggle {
        gap: 5px !important;
        font-size: 0.78rem !important;
    }

    #panel-lista-pedidos .pix-toggle input {
        width: 13px !important;
        height: 13px !important;
    }

    #panel-lista-pedidos .pix-key {
        width: 152px !important;
        height: 26px !important;
        padding: 2px 6px !important;
        font-size: 0.78rem !important;
    }

    @media (max-width: 1180px) {
        #panel-lista-pedidos > div[style*="overflow-x"] {
            overflow-x: auto !important;
        }

        #panel-lista-pedidos .data-table {
            min-width: 960px !important;
        }
    }
</style>

<script>
(function () {
    const panel = document.getElementById('panel-lista-pedidos');
    if (!panel) {
        return;
    }

    panel.querySelectorAll('.data-table tbody tr').forEach((row) => {
        const cell = row.children[5];
        if (!cell || cell.querySelector('.pedido-created-at')) {
            return;
        }

        const text = (cell.textContent || '').trim();
        const match = text.match(/^(\d{4}-\d{2}-\d{2})\s+(\d{2}:\d{2}(?::\d{2})?)$/);
        if (!match) {
            return;
        }

        cell.innerHTML = `<span class="pedido-created-at"><span class="pedido-created-date">${match[1]}</span><span class="pedido-created-time">${match[2]}</span></span>`;
    });
})();
</script>
