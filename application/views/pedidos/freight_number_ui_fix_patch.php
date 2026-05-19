<style>
    #pedido-form .pedido-frete-card.pedido-frete-popup-style {
        display: block;
        padding: 16px;
    }

    #pedido-form .pedido-frete-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
    }

    #pedido-form .pedido-frete-title i {
        color: var(--accent, var(--primary, #dc2626));
    }

    #pedido-form .pedido-frete-row {
        display: grid;
        grid-template-columns: auto minmax(160px, 1fr);
        gap: 14px;
        align-items: end;
    }

    #pedido-form .pedido-frete-popup-toggle {
        display: inline-flex;
        align-items: center;
        min-width: 86px;
        height: 42px;
        border-radius: 999px;
        padding: 4px;
        background: #e5e7eb;
        cursor: pointer;
        user-select: none;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, .08);
    }

    #pedido-form .pedido-frete-popup-toggle input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    #pedido-form .pedido-frete-popup-track {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        height: 100%;
        border-radius: inherit;
        padding: 0 12px;
        color: #64748b;
        font-size: 13px;
        font-weight: 900;
        transition: background .2s ease, color .2s ease;
    }

    #pedido-form .pedido-frete-popup-track::after {
        content: '';
        position: absolute;
        top: 4px;
        left: 4px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .18);
        transition: transform .2s ease;
    }

    #pedido-form .pedido-frete-popup-track::before {
        content: 'Não';
        padding-left: 30px;
    }

    #pedido-form .pedido-frete-popup-toggle input:checked + .pedido-frete-popup-track {
        background: var(--accent, var(--primary, #dc2626));
        color: #fff;
    }

    #pedido-form .pedido-frete-popup-toggle input:checked + .pedido-frete-popup-track::after {
        transform: translateX(42px);
    }

    #pedido-form .pedido-frete-popup-toggle input:checked + .pedido-frete-popup-track::before {
        content: 'Sim';
        padding-left: 6px;
    }

    #pedido-form .pedido-frete-popup-value label {
        display: block;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 6px;
    }

    #pedido-form .pedido-money-input,
    #pedido-edit-modal .pedido-money-input {
        position: relative;
        display: block;
    }

    #pedido-form .pedido-money-input::before,
    #pedido-edit-modal .pedido-money-input::before {
        content: 'R$';
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #0f172a;
        font-weight: 900;
        pointer-events: none;
        z-index: 1;
    }

    #pedido-form .pedido-frete-popup-value input,
    #pedido-edit-modal .pedido-edit-frete-value input {
        width: 100%;
        min-height: 46px;
        border: 1px solid #dbe3ee;
        border-radius: 14px;
        padding: 0 14px 0 42px;
        font-weight: 800;
        color: #0f172a;
        background: #f8fafc;
    }

    #pedido-edit-modal .pedido-edit-summary .item-total.pedido-total-card {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 14px;
        margin-top: 0;
        min-width: min(100%, 310px);
        padding: 16px 20px;
        background: linear-gradient(135deg, #fff 0%, #fff7f7 100%);
        border: 1px solid rgba(220, 38, 38, 0.22);
        border-radius: 16px;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.07);
    }

    #pedido-edit-modal .pedido-total-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: var(--primary, #dc2626);
        color: #fff;
        box-shadow: 0 12px 24px rgba(220, 38, 38, 0.22);
        flex: 0 0 44px;
    }

    #pedido-edit-modal .pedido-total-icon svg {
        width: 24px;
        height: 24px;
    }

    #pedido-edit-modal .pedido-total-text {
        display: grid;
        gap: 2px;
    }

    #pedido-edit-modal .pedido-total-label {
        color: var(--text-muted, #64748b);
        font-size: 0.82rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    #pedido-edit-modal .pedido-total-card #pedido-edit-total {
        color: var(--text-strong, #0f172a);
        font-size: 1.55rem;
        line-height: 1;
        font-weight: 950;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    #pedido-edit-modal .pedido-edit-summary .item-total.pedido-total-card span::before,
    #pedido-edit-modal .pedido-edit-summary .item-total.pedido-total-card .pedido-total-text::before,
    #pedido-edit-modal .pedido-edit-summary .item-total.pedido-total-card .pedido-total-label::before {
        content: none !important;
        display: none !important;
    }

    @media (max-width: 760px) {
        #pedido-form .pedido-frete-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
(function () {
    let recalcQueued = false;

    const parseSmart = (value) => {
        const raw = String(value ?? '').trim();
        if (!raw) return 0;
        if (raw.includes(',')) {
            return Number(raw.replace(/\./g, '').replace(',', '.')) || 0;
        }
        return Number(raw) || 0;
    };

    const money = (value) => 'R$ ' + Number(value || 0).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    const setText = (element, value) => {
        if (element && element.textContent !== value) {
            element.textContent = value;
        }
    };

    const normalizeNumberInput = (input) => {
        if (!input) return 0;
        const value = parseSmart(input.value);
        if (String(input.value || '').includes(',')) {
            const normalized = value.toFixed(2);
            if (input.value !== normalized) input.value = normalized;
        }
        return value;
    };

    const setDisabled = (input, disabled, emptyValue) => {
        if (!input) return;
        if (input.disabled !== disabled) input.disabled = disabled;
        const opacity = disabled ? '.55' : '1';
        if (input.style.opacity !== opacity) input.style.opacity = opacity;
        if (disabled && input.value !== emptyValue) input.value = emptyValue;
    };

    const applyMoneyPrefix = () => {
        ['#pedido-frete-valor', '#pedido-edit-frete-valor'].forEach((selector) => {
            const input = document.querySelector(selector);
            if (!input || input.parentElement?.classList.contains('pedido-money-input')) return;
            const wrapper = document.createElement('span');
            wrapper.className = 'pedido-money-input';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);
        });
    };

    const setupNovoPedidoFreteVisual = () => {
        const form = document.getElementById('pedido-form');
        const card = form ? form.querySelector('.pedido-frete-card') : null;
        if (!form || !card || card.dataset.popupVisualReady === '1') return;

        const oldToggle = form.querySelector('#pedido-frete-ativo');
        const oldValue = form.querySelector('#pedido-frete-valor');
        const checked = Boolean(oldToggle?.checked);
        const value = oldValue?.value || '0';

        card.dataset.popupVisualReady = '1';
        card.classList.add('pedido-frete-popup-style');
        card.innerHTML = `
            <div class="pedido-frete-title"><i data-lucide="truck"></i><span>Frete</span></div>
            <div class="pedido-frete-row">
                <label class="pedido-frete-popup-toggle" title="Ativar frete">
                    <input type="checkbox" id="pedido-frete-ativo" name="frete_ativo" value="1" ${checked ? 'checked' : ''}>
                    <span class="pedido-frete-popup-track"></span>
                </label>
                <div class="pedido-frete-popup-value">
                    <label for="pedido-frete-valor">Valor do frete</label>
                    <span class="pedido-money-input"><input type="number" min="0" step="0.01" name="frete_valor" id="pedido-frete-valor" value="${value}" inputmode="decimal"></span>
                </div>
            </div>
        `;

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons({ elements: card.querySelectorAll('[data-lucide]') });
        }
    };

    const setupEditTotalVisual = () => {
        const card = document.querySelector('#pedido-edit-modal .pedido-edit-summary .item-total');
        const total = card ? card.querySelector('#pedido-edit-total') : null;
        if (!card || !total || card.dataset.totalVisualReady === '1') return;
        const current = total.textContent || 'R$ 0,00';
        card.dataset.totalVisualReady = '1';
        card.classList.add('pedido-total-card');
        card.innerHTML = `
            <span class="pedido-total-icon" aria-hidden="true"><i data-lucide="circle-dollar-sign"></i></span>
            <span class="pedido-total-text">
                <span class="pedido-total-label">Valor total</span>
                <strong id="pedido-edit-total">${current}</strong>
            </span>
        `;
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons({ elements: card.querySelectorAll('[data-lucide]') });
        }
    };

    const recalcNovoPedido = () => {
        const form = document.getElementById('pedido-form');
        const body = form ? form.querySelector('#pedido-products-body') : null;
        const totalEl = form ? form.querySelector('#total-display') : null;
        if (!form || !body || !totalEl) return;

        let total = 0;
        body.querySelectorAll('.pedido-products-row').forEach((row) => {
            const qtyInput = row.querySelector('.pedido-line-qty');
            const priceInput = row.querySelector('.pedido-line-price');
            const qty = normalizeNumberInput(qtyInput);
            const price = normalizeNumberInput(priceInput);
            const subtotal = qty * price;
            const subtotalEl = row.querySelector('.pedido-line-subtotal');
            const hiddenQty = row.querySelector('input[name="item_quantidade[]"]');
            const hiddenPrice = row.querySelector('input[name="item_preco[]"]');
            row.dataset.subtotal = String(subtotal);
            setText(subtotalEl, money(subtotal));
            if (hiddenQty && hiddenQty.value !== String(qty)) hiddenQty.value = String(qty);
            if (hiddenPrice && hiddenPrice.value !== String(price)) hiddenPrice.value = String(price);
            total += subtotal;
        });

        const freteToggle = form.querySelector('#pedido-frete-ativo');
        const freteInput = form.querySelector('#pedido-frete-valor');
        const freteAtivo = Boolean(freteToggle?.checked);
        const freteValor = freteAtivo ? normalizeNumberInput(freteInput) : 0;
        setDisabled(freteInput, !freteAtivo, '0');
        setText(totalEl, money(total + freteValor));
    };

    const recalcEditPedido = () => {
        const modal = document.getElementById('pedido-edit-modal');
        const grid = modal ? modal.querySelector('#pedido-edit-grid') : null;
        const totalEl = modal ? modal.querySelector('#pedido-edit-total') : null;
        if (!modal || !grid || !totalEl) return;

        let total = 0;
        grid.querySelectorAll('.item-row[data-row]').forEach((row) => {
            const qty = normalizeNumberInput(row.querySelector('input[name="item_quantidade[]"]'));
            const price = normalizeNumberInput(row.querySelector('input[name="item_preco[]"]'));
            const subtotal = qty * price;
            const subtotalEl = row.querySelector('.pedido-edit-subtotal');
            setText(subtotalEl, money(subtotal));
            total += subtotal;
        });

        const freteToggle = modal.querySelector('#pedido-edit-frete-ativo');
        const freteInput = modal.querySelector('#pedido-edit-frete-valor');
        const freteAtivo = Boolean(freteToggle?.checked);
        const freteValor = freteAtivo ? normalizeNumberInput(freteInput) : 0;
        setDisabled(freteInput, !freteAtivo, '0.00');
        setText(totalEl, money(total + freteValor));
    };

    const recalcAll = () => {
        recalcQueued = false;
        setupNovoPedidoFreteVisual();
        setupEditTotalVisual();
        applyMoneyPrefix();
        recalcNovoPedido();
        recalcEditPedido();
    };

    const scheduleRecalc = () => {
        if (recalcQueued) return;
        recalcQueued = true;
        window.requestAnimationFrame(recalcAll);
    };

    const run = () => {
        recalcAll();
        document.addEventListener('input', scheduleRecalc);
        document.addEventListener('change', scheduleRecalc);
        document.addEventListener('click', () => window.setTimeout(scheduleRecalc, 40));

        const pedidoBody = document.querySelector('#pedido-products-body');
        if (pedidoBody) {
            new MutationObserver(scheduleRecalc).observe(pedidoBody, { childList: true });
        }
        const editGrid = document.querySelector('#pedido-edit-grid');
        if (editGrid) {
            new MutationObserver(scheduleRecalc).observe(editGrid, { childList: true });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
