<style>
    #pedido-form .pedido-desconto-card {
        border: 1px solid var(--border-subtle, #e2e8f0);
        border-radius: 16px;
        background: var(--surface-elevated, #fff);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.07);
        padding: 16px;
        min-width: min(100%, 330px);
        flex: 1 1 330px;
    }

    #pedido-form .pedido-desconto-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
    }

    #pedido-form .pedido-desconto-title i {
        color: var(--accent, var(--primary, #dc2626));
    }

    #pedido-form .pedido-desconto-grid {
        display: grid;
        grid-template-columns: auto minmax(150px, 1fr);
        gap: 14px;
        align-items: end;
    }

    #pedido-form .pedido-desconto-mode {
        display: inline-flex;
        height: 42px;
        padding: 4px;
        border-radius: 999px;
        background: #e5e7eb;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, .08);
    }

    #pedido-form .pedido-desconto-mode label {
        position: relative;
        display: grid;
        place-items: center;
        min-width: 44px;
        height: 34px;
        margin: 0;
        border-radius: 999px;
        cursor: pointer;
        color: #64748b;
        font-weight: 900;
    }

    #pedido-form .pedido-desconto-mode input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    #pedido-form .pedido-desconto-mode label:has(input:checked) {
        background: var(--accent, var(--primary, #dc2626));
        color: #fff;
        box-shadow: 0 8px 18px rgba(220, 38, 38, .2);
    }

    #pedido-form .pedido-desconto-value label {
        display: block;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 6px;
    }

    #pedido-form .pedido-discount-input-wrap {
        position: relative;
        display: block;
    }

    #pedido-form .pedido-discount-input-wrap::before {
        content: attr(data-prefix);
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #0f172a;
        font-weight: 900;
        z-index: 1;
        pointer-events: none;
    }

    #pedido-form .pedido-discount-input-wrap input {
        width: 100%;
        min-height: 46px;
        border: 1px solid #dbe3ee;
        border-radius: 14px;
        padding: 0 14px 0 42px;
        font-weight: 800;
        color: #0f172a;
        background: #f8fafc;
    }

    @media (max-width: 760px) {
        #pedido-form .pedido-desconto-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
(function () {
    let scheduled = false;

    const parseSmart = (value) => {
        const raw = String(value ?? '').trim();
        if (!raw) return 0;
        if (raw.includes(',')) return Number(raw.replace(/\./g, '').replace(',', '.')) || 0;
        return Number(raw) || 0;
    };

    const money = (value) => 'R$ ' + Number(value || 0).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    const setupDiscountCard = () => {
        const form = document.getElementById('pedido-form');
        const summary = form ? form.querySelector('.pedido-order-summary') : null;
        const totalCard = form ? form.querySelector('.pedido-total-card') : null;
        if (!form || !summary || !totalCard || form.querySelector('.pedido-desconto-card')) return;

        const card = document.createElement('div');
        card.className = 'pedido-desconto-card';
        card.innerHTML = `
            <div class="pedido-desconto-title"><i data-lucide="badge-percent"></i><span>Desconto</span></div>
            <div class="pedido-desconto-grid">
                <div class="pedido-desconto-mode" aria-label="Tipo de desconto">
                    <label title="Desconto em reais">
                        <input type="radio" name="desconto_tipo" value="valor" checked>
                        <span>R$</span>
                    </label>
                    <label title="Desconto em porcentagem">
                        <input type="radio" name="desconto_tipo" value="percentual">
                        <span>%</span>
                    </label>
                </div>
                <div class="pedido-desconto-value">
                    <label for="pedido-desconto-valor">Valor do desconto</label>
                    <span class="pedido-discount-input-wrap" data-prefix="R$">
                        <input type="number" min="0" step="0.01" name="desconto_valor" id="pedido-desconto-valor" value="0" inputmode="decimal">
                    </span>
                </div>
            </div>
        `;
        summary.insertBefore(card, totalCard);

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons({ elements: card.querySelectorAll('[data-lucide]') });
        }
    };

    const calculateProducts = (body) => {
        let total = 0;
        body.querySelectorAll('.pedido-products-row').forEach((row) => {
            const qty = parseSmart(row.querySelector('.pedido-line-qty')?.value);
            const price = parseSmart(row.querySelector('.pedido-line-price')?.value);
            total += qty * price;
        });
        return total;
    };

    const updateDiscountTotal = () => {
        const form = document.getElementById('pedido-form');
        const body = form ? form.querySelector('#pedido-products-body') : null;
        const totalEl = form ? form.querySelector('#total-display') : null;
        if (!form || !body || !totalEl) return;

        const products = calculateProducts(body);
        const freteAtivo = Boolean(form.querySelector('#pedido-frete-ativo')?.checked);
        const frete = freteAtivo ? parseSmart(form.querySelector('#pedido-frete-valor')?.value) : 0;
        const tipo = form.querySelector('input[name="desconto_tipo"]:checked')?.value || 'valor';
        const input = form.querySelector('#pedido-desconto-valor');
        const wrap = form.querySelector('.pedido-discount-input-wrap');
        const valor = parseSmart(input?.value);
        const base = products + frete;
        const desconto = tipo === 'percentual'
            ? base * Math.min(Math.max(valor, 0), 100) / 100
            : Math.min(Math.max(valor, 0), base);

        if (wrap) wrap.dataset.prefix = tipo === 'percentual' ? '%' : 'R$';
        totalEl.textContent = money(Math.max(0, base - desconto));
    };

    const refresh = () => {
        scheduled = false;
        setupDiscountCard();
        updateDiscountTotal();
    };

    const schedule = () => {
        if (scheduled) return;
        scheduled = true;
        window.requestAnimationFrame(refresh);
    };

    const run = () => {
        refresh();
        document.addEventListener('input', schedule);
        document.addEventListener('change', schedule);
        document.addEventListener('click', () => window.setTimeout(schedule, 40));
        const body = document.querySelector('#pedido-products-body');
        if (body) new MutationObserver(schedule).observe(body, { childList: true });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
