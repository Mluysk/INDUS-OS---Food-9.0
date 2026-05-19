<style>
    #pedido-form .pedido-order-summary {
        display: flex;
        justify-content: space-between;
        align-items: stretch;
        gap: 16px;
        margin-top: 0.85rem;
        flex-wrap: wrap;
    }

    #pedido-form .pedido-frete-card,
    #pedido-form .pedido-total-card {
        border: 1px solid var(--border-subtle, #e2e8f0);
        border-radius: 16px;
        background: var(--surface-elevated, #fff);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.07);
    }

    #pedido-form .pedido-frete-card {
        display: grid;
        grid-template-columns: minmax(190px, auto) minmax(170px, 220px);
        gap: 14px;
        align-items: center;
        padding: 14px 16px;
    }

    #pedido-form .pedido-frete-switch {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        color: var(--text-strong, #0f172a);
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
    }

    #pedido-form .pedido-frete-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        border-radius: 12px;
        background: color-mix(in srgb, var(--primary, #dc2626) 12%, #fff);
        color: var(--primary, #dc2626);
    }

    #pedido-form .pedido-frete-icon svg {
        width: 20px;
        height: 20px;
    }

    #pedido-form .pedido-frete-switch input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    #pedido-form .pedido-frete-slider {
        position: relative;
        width: 68px;
        height: 32px;
        flex: 0 0 68px;
        border-radius: 999px;
        background: var(--border-muted, #cbd5e1);
        color: #fff;
        transition: background 0.18s ease;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.08);
    }

    #pedido-form .pedido-frete-slider::before {
        content: "Não";
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.76rem;
        font-weight: 900;
        line-height: 1;
        opacity: 0.9;
    }

    #pedido-form .pedido-frete-slider::after {
        content: "";
        position: absolute;
        width: 24px;
        height: 24px;
        top: 4px;
        right: 4px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 2px 7px rgba(15, 23, 42, 0.22);
        transition: transform 0.18s ease, right 0.18s ease;
    }

    #pedido-form .pedido-frete-switch input:checked + .pedido-frete-slider {
        background: var(--primary, #22c55e);
    }

    #pedido-form .pedido-frete-switch input:checked + .pedido-frete-slider::before {
        content: "Sim";
    }

    #pedido-form .pedido-frete-switch input:checked + .pedido-frete-slider::after {
        right: 4px;
        transform: translateX(0);
    }

    #pedido-form .pedido-frete-switch input:not(:checked) + .pedido-frete-slider::after {
        right: 40px;
    }

    #pedido-form .pedido-frete-value {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin: 0;
        color: var(--text-muted, #64748b);
        font-size: 0.88rem;
        font-weight: 800;
    }

    #pedido-form .pedido-frete-value input {
        width: 100%;
        min-height: 42px;
        border-radius: 12px;
        font-weight: 800;
    }

    #pedido-form .pedido-frete-value input:disabled {
        opacity: 0.58;
        cursor: not-allowed;
    }

    #pedido-form .pedido-total-card.item-total {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 14px;
        margin-top: 0;
        min-width: min(100%, 310px);
        padding: 16px 20px;
        background: linear-gradient(135deg, #fff 0%, #fff7f7 100%);
        border-color: rgba(220, 38, 38, 0.22);
    }

    #pedido-form .pedido-total-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: var(--primary, #dc2626);
        color: #fff;
        box-shadow: 0 12px 24px rgba(220, 38, 38, 0.22);
    }

    #pedido-form .pedido-total-icon svg {
        width: 24px;
        height: 24px;
    }

    #pedido-form .pedido-total-text {
        display: grid;
        gap: 2px;
    }

    #pedido-form .pedido-total-label {
        color: var(--text-muted, #64748b);
        font-size: 0.82rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    #pedido-form .pedido-total-card #total-display {
        color: var(--text-strong, #0f172a);
        font-size: 1.55rem;
        line-height: 1;
        font-weight: 950;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    @media (max-width: 760px) {
        #pedido-form .pedido-order-summary,
        #pedido-form .pedido-frete-card,
        #pedido-form .pedido-total-card.item-total {
            width: 100%;
        }

        #pedido-form .pedido-frete-card {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
(function () {
    const money = (value) => 'R$ ' + Number(value || 0).toFixed(2).replace('.', ',');

    const run = () => {
        const form = document.getElementById('pedido-form');
        const totalCard = form ? form.querySelector('.item-total') : null;
        const productsBody = form ? form.querySelector('#pedido-products-body') : null;
        const totalDisplay = form ? form.querySelector('#total-display') : null;

        if (!form || !totalCard || !productsBody || !totalDisplay || totalCard.dataset.freightReady === '1') return;
        totalCard.dataset.freightReady = '1';

        const summary = document.createElement('div');
        summary.className = 'pedido-order-summary';
        totalCard.parentNode.insertBefore(summary, totalCard);

        const freightCard = document.createElement('div');
        freightCard.className = 'pedido-frete-card';
        freightCard.innerHTML = `
            <label class="pedido-frete-switch">
                <span class="pedido-frete-icon" aria-hidden="true"><i data-lucide="truck"></i></span>
                <input type="checkbox" id="pedido-frete-ativo" name="frete_ativo" value="1">
                <span class="pedido-frete-slider" aria-hidden="true"></span>
                <span>Ativar frete</span>
            </label>
            <label class="pedido-frete-value">
                Valor do frete
                <input type="number" min="0" step="0.01" name="frete_valor" id="pedido-frete-valor" value="0" disabled>
            </label>
        `;

        summary.appendChild(freightCard);
        summary.appendChild(totalCard);

        totalCard.classList.add('pedido-total-card');
        totalCard.innerHTML = `
            <span class="pedido-total-icon" aria-hidden="true"><i data-lucide="circle-dollar-sign"></i></span>
            <span class="pedido-total-text">
                <span class="pedido-total-label">Valor total</span>
                <strong id="total-display">${totalDisplay.textContent || 'R$ 0,00'}</strong>
            </span>
        `;

        const newTotalDisplay = totalCard.querySelector('#total-display');
        const freightToggle = form.querySelector('#pedido-frete-ativo');
        const freightInput = form.querySelector('#pedido-frete-valor');

        const calculateItemsTotal = () => {
            let total = 0;
            productsBody.querySelectorAll('.pedido-products-row').forEach((row) => {
                const qty = Number(row.querySelector('.pedido-line-qty')?.value || 0);
                const price = Number(row.querySelector('.pedido-line-price')?.value || 0);
                total += qty * price;
            });
            return total;
        };

        const updateTotalWithFreight = () => {
            const freightEnabled = Boolean(freightToggle.checked);
            const freightValue = freightEnabled ? Math.max(0, Number(freightInput.value || 0)) : 0;
            freightInput.disabled = !freightEnabled;
            newTotalDisplay.textContent = money(calculateItemsTotal() + freightValue);
        };

        freightToggle.addEventListener('change', () => {
            if (!freightToggle.checked) {
                freightInput.value = '0';
            } else {
                freightInput.focus();
            }
            updateTotalWithFreight();
        });
        freightInput.addEventListener('input', updateTotalWithFreight);

        productsBody.addEventListener('input', () => window.setTimeout(updateTotalWithFreight, 0));
        productsBody.addEventListener('click', () => window.setTimeout(updateTotalWithFreight, 0));

        const observer = new MutationObserver(() => window.setTimeout(updateTotalWithFreight, 0));
        observer.observe(productsBody, { childList: true, subtree: true });

        form.addEventListener('submit', () => updateTotalWithFreight(), true);

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons({ elements: summary.querySelectorAll('[data-lucide]') });
        }

        updateTotalWithFreight();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
