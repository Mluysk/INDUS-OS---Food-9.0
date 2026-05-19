<style>
    #pedido-form .pedido-frete-switch {
        gap: 11px;
    }

    #pedido-form .pedido-frete-slider {
        width: 74px;
        height: 34px;
        flex: 0 0 74px;
        border-radius: 999px;
        overflow: hidden;
        background: #cbd5e1;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.08), 0 6px 14px rgba(15, 23, 42, 0.08);
    }

    #pedido-form .pedido-frete-slider::before {
        left: 34px;
        right: auto;
        font-size: 0.72rem;
        letter-spacing: 0;
        color: #fff;
        z-index: 1;
        transition: left 0.18s ease;
    }

    #pedido-form .pedido-frete-slider::after {
        width: 28px;
        height: 28px;
        top: 3px;
        left: 3px;
        right: auto;
        border-radius: 50%;
        transform: translateX(0);
        z-index: 2;
    }

    #pedido-form .pedido-frete-switch input:checked + .pedido-frete-slider {
        background: var(--primary, #22c55e);
    }

    #pedido-form .pedido-frete-switch input:checked + .pedido-frete-slider::before {
        left: 12px;
        content: "Sim";
    }

    #pedido-form .pedido-frete-switch input:checked + .pedido-frete-slider::after {
        left: 43px;
        right: auto;
        transform: translateX(0);
    }

    #pedido-form .pedido-frete-switch input:not(:checked) + .pedido-frete-slider::before {
        content: "Não";
        left: 34px;
    }

    #pedido-form .pedido-frete-switch input:not(:checked) + .pedido-frete-slider::after {
        left: 3px;
        right: auto;
    }
</style>
