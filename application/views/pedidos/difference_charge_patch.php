<style>
    #pedido-form .pedido-difference-charge {
        display: flex;
        align-items: end;
        gap: 0.75rem;
        margin: 0.45rem 0 0.85rem;
        padding: 0.65rem 0.8rem;
        border: 1px solid color-mix(in srgb, var(--primary, #9f1239) 18%, var(--border, #dbe2ea));
        border-radius: 12px;
        background: color-mix(in srgb, var(--primary, #9f1239) 4%, var(--surface, #fff));
    }

    #pedido-form .pedido-difference-toggle {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        min-height: 42px;
        color: #1f2937;
        font-weight: 800;
        white-space: nowrap;
        cursor: pointer;
    }

    #pedido-form .pedido-difference-toggle input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    #pedido-form .pedido-difference-track {
        position: relative;
        width: 42px;
        height: 23px;
        flex: 0 0 auto;
        border: 1px solid #cbd5e1;
        border-radius: 999px;
        background: #e5e7eb;
        transition: 160ms ease;
    }

    #pedido-form .pedido-difference-track::after {
        content: "";
        position: absolute;
        top: 3px;
        left: 3px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 4px rgb(15 23 42 / 20%);
        transition: 160ms ease;
    }

    #pedido-form .pedido-difference-toggle input:checked + .pedido-difference-track {
        border-color: var(--primary, #9f1239);
        background: var(--primary, #9f1239);
    }

    #pedido-form .pedido-difference-toggle input:checked + .pedido-difference-track::after {
        transform: translateX(19px);
    }

    #pedido-form .pedido-difference-fields {
        display: grid;
        grid-template-columns: minmax(190px, 1fr) 132px;
        gap: 0.65rem;
        flex: 1 1 auto;
        max-width: 520px;
    }

    #pedido-form .pedido-difference-fields[hidden] {
        display: none;
    }

    #pedido-form .pedido-difference-field {
        display: grid;
        gap: 0.28rem;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 800;
    }

    #pedido-form .pedido-difference-field input {
        width: 100%;
        min-height: 42px;
        padding: 0.6rem 0.75rem;
        border: 1px solid #d8dee8;
        border-radius: 10px;
        background: var(--surface, #fff);
        color: var(--text, #111827);
        font: inherit;
        font-size: 0.92rem;
        outline: none;
    }

    #pedido-form .pedido-difference-field input:focus {
        border-color: var(--primary, #9f1239);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary, #9f1239) 14%, transparent);
    }

    #pedido-form .pedido-difference-row {
        background: color-mix(in srgb, var(--primary, #9f1239) 5%, var(--surface, #fff));
    }

    #pedido-form .pedido-difference-row .pedido-product-code {
        color: var(--primary, #9f1239);
        font-weight: 900;
    }

    #pedido-form .pedido-difference-row .pedido-line-input[readonly] {
        background: transparent;
        cursor: default;
    }

    @media (max-width: 760px) {
        #pedido-form .pedido-difference-charge {
            align-items: stretch;
            flex-direction: column;
        }

        #pedido-form .pedido-difference-fields {
            grid-template-columns: 1fr;
            max-width: none;
        }
    }
</style>

<script>
(function () {
    const money = (value) => 'R$ ' + Number(value || 0).toFixed(2).replace('.', ',');

    const run = () => {
        const form = document.getElementById('pedido-form');
        const entry = form?.querySelector('.pedido-item-entry');
        const body = form?.querySelector('#pedido-products-body');
        if (!form || !entry || !body || form.querySelector('[data-difference-charge-control]')) {
            return;
        }

        const control = document.createElement('div');
        control.className = 'pedido-difference-charge';
        control.dataset.differenceChargeControl = '1';
        control.innerHTML = `
            <label class="pedido-difference-toggle">
                <input type="checkbox" name="cobranca_diferenca_ativa" value="1" id="pedido-difference-active">
                <span class="pedido-difference-track" aria-hidden="true"></span>
                <span>Cobrança de diferença</span>
            </label>
            <div class="pedido-difference-fields" hidden>
                <label class="pedido-difference-field">
                    Descrição
                    <input type="text" name="cobranca_diferenca_descricao" id="pedido-difference-description" maxlength="160" placeholder="Ex.: Ajuste de valor" disabled>
                </label>
                <label class="pedido-difference-field">
                    Valor
                    <input type="number" name="cobranca_diferenca_valor" id="pedido-difference-value" min="0.01" step="0.01" value="0" inputmode="decimal" disabled>
                </label>
            </div>
        `;
        entry.insertAdjacentElement('afterend', control);

        const toggle = control.querySelector('#pedido-difference-active');
        const fields = control.querySelector('.pedido-difference-fields');
        const description = control.querySelector('#pedido-difference-description');
        const value = control.querySelector('#pedido-difference-value');
        let syncing = false;

        const removeCharge = () => {
            body.querySelector('[data-difference-charge]')?.remove();
        };

        const createChargeRow = () => {
            const row = document.createElement('div');
            row.className = 'pedido-products-row pedido-difference-row';
            row.dataset.differenceCharge = '1';
            row.innerHTML = `
                <span class="pedido-product-code">00</span>
                <span class="pedido-product-name"></span>
                <input type="number" class="pedido-line-input pedido-line-qty" value="1" readonly tabindex="-1">
                <span class="pedido-line-weight">-</span>
                <input type="number" class="pedido-line-input pedido-line-price" value="0" readonly tabindex="-1">
                <span class="pedido-line-subtotal">R$ 0,00</span>
                <button type="button" class="button button-ghost remove-item" aria-label="Remover cobrança">×</button>
            `;
            row.querySelector('.remove-item').addEventListener('click', () => {
                toggle.checked = false;
                sync();
            });
            return row;
        };

        const notifyTotals = (row) => {
            const price = row?.querySelector('.pedido-line-price');
            if (price) {
                price.dispatchEvent(new Event('input', { bubbles: true }));
            } else {
                form.dispatchEvent(new Event('input', { bubbles: true }));
            }
        };

        const sync = () => {
            if (syncing) return;
            syncing = true;
            const active = toggle.checked;
            fields.hidden = !active;
            description.disabled = !active;
            value.disabled = !active;
            description.required = active;
            value.required = active;

            if (!active) {
                removeCharge();
                notifyTotals(null);
                syncing = false;
                return;
            }

            let row = body.querySelector('[data-difference-charge]');
            if (!row) {
                row = createChargeRow();
                body.appendChild(row);
            } else if (row !== body.lastElementChild) {
                body.appendChild(row);
            }

            const amount = Math.max(0, Number(value.value) || 0);
            const label = description.value.trim() || 'Cobrança de diferença';
            row.dataset.subtotal = String(amount);
            row.querySelector('.pedido-product-name').textContent = label;
            row.querySelector('.pedido-product-name').title = label;
            row.querySelector('.pedido-line-price').value = amount.toFixed(2);
            row.querySelector('.pedido-line-subtotal').textContent = money(amount);
            notifyTotals(row);
            syncing = false;
        };

        toggle.addEventListener('change', sync);
        description.addEventListener('input', sync);
        value.addEventListener('input', sync);
        new MutationObserver(() => {
            if (toggle.checked && body.querySelector('[data-difference-charge]') !== body.lastElementChild) {
                sync();
            }
        }).observe(body, { childList: true });

        sync();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
