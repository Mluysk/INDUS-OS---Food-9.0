<style>
    .clientes-table {
        table-layout: fixed;
        width: 100%;
    }

    .clientes-table thead th {
        padding: 14px 10px !important;
        font-size: calc(0.72rem - 1px) !important;
        line-height: 1.15 !important;
        letter-spacing: 0.06em !important;
        font-weight: 800 !important;
        color: var(--text-muted, #64748b) !important;
        white-space: nowrap;
    }

    .clientes-table tbody td {
        padding: 14px 10px !important;
        font-size: calc(0.88rem - 1px) !important;
        line-height: 1.22 !important;
        vertical-align: middle !important;
    }

    .clientes-table th:nth-child(1),
    .clientes-table td:nth-child(1) {
        width: 58px;
        text-align: center;
    }

    .clientes-table th:nth-child(2),
    .clientes-table td:nth-child(2) {
        width: 36%;
        overflow-wrap: anywhere;
    }

    .clientes-table th:nth-child(3),
    .clientes-table td:nth-child(3),
    .clientes-table th:nth-child(4),
    .clientes-table td:nth-child(4) {
        width: 14%;
        overflow-wrap: anywhere;
    }

    .clientes-table th:nth-child(5),
    .clientes-table td:nth-child(5) {
        width: 13%;
        white-space: nowrap;
    }

    .clientes-table th:nth-child(6),
    .clientes-table td:nth-child(6) {
        width: 15%;
        overflow-wrap: anywhere;
    }

    .clientes-table th:nth-child(7),
    .clientes-table td:nth-child(7) {
        width: 112px;
    }

    .clientes-table .table-action {
        font-size: calc(0.82rem - 1px) !important;
        line-height: 1 !important;
        font-weight: 700 !important;
    }

    .clientes-table .table-action .icon {
        color: var(--primary, #4f46e5) !important;
        stroke: var(--primary, #4f46e5) !important;
        fill: none !important;
    }

    .clientes-table .table-action:hover,
    .clientes-table .table-action:focus-visible {
        border-color: var(--primary, #4f46e5) !important;
        background: var(--primary, #4f46e5) !important;
        color: #fff !important;
        box-shadow: 0 8px 18px rgba(var(--primary-rgb, 79, 70, 229), 0.28) !important;
        outline: 0;
    }

    .clientes-table .table-action:hover .icon,
    .clientes-table .table-action:focus-visible .icon {
        color: currentColor !important;
        stroke: currentColor !important;
        fill: none !important;
    }
</style>
