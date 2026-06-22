<style>
    #pedido-edit-modal .pedido-edit-desconto-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
        padding: 16px;
    }

    #pedido-edit-modal .pedido-edit-desconto-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
    }

    #pedido-edit-modal .pedido-edit-desconto-title i {
        color: var(--accent, var(--primary, #dc2626));
    }

    #pedido-edit-modal .pedido-edit-desconto-grid {
        display: grid;
        grid-template-columns: auto minmax(150px, 1fr);
        gap: 14px;
        align-items: end;
    }

    #pedido-edit-modal .pedido-edit-desconto-mode {
        display: inline-flex;
        height: 42px;
        padding: 4px;
        border-radius: 999px;
        background: #e5e7eb;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, .08);
    }

    #pedido-edit-modal .pedido-edit-desconto-mode label {
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

    #pedido-edit-modal .pedido-edit-desconto-mode input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    #pedido-edit-modal .pedido-edit-desconto-mode label:has(input:checked) {
        background: var(--accent, var(--primary, #dc2626));
        color: #fff;
        box-shadow: 0 8px 18px rgba(220, 38, 38, .2);
    }

    #pedido-edit-modal .pedido-edit-desconto-value label {
        display: block;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 6px;
    }

    #pedido-edit-modal .pedido-edit-discount-input-wrap {
        position: relative;
        display: block;
    }

    #pedido-edit-modal .pedido-edit-discount-input-wrap::before {
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

    #pedido-edit-modal .pedido-edit-discount-input-wrap input {
        width: 100%;
        min-height: 46px;
        border: 1px solid #dbe3ee;
        border-radius: 14px;
        padding: 0 14px 0 42px;
        font-weight: 800;
        color: #0f172a;
        background: #f8fafc;
    }

    #pedido-edit-modal .pedido-edit-summary.has-discount {
        grid-template-columns: minmax(230px, 1fr) minmax(230px, .9fr) minmax(220px, 320px);
    }

    @media (max-width: 980px) {
        #pedido-edit-modal .pedido-edit-summary.has-discount {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
(function () {
    const basePedidos = <?= json_encode(rtrim($url('pedidos'), '/'), JSON_UNESCAPED_SLASHES); ?>;
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

    const setupEditDiscount = () => {
        const modal = document.getElementById('pedido-edit-modal');
        const summary = modal ? modal.querySelector('.pedido-edit-summary') : null;
        const totalCard = summary ? summary.querySelector('.item-total') : null;
        if (!modal || !summary || !totalCard || summary.querySelector('.pedido-edit-desconto-card')) return;

        const card = document.createElement('div');
        card.className = 'pedido-edit-desconto-card';
        card.innerHTML = `
            <div class="pedido-edit-desconto-title"><i data-lucide="badge-percent"></i><span>Desconto</span></div>
            <div class="pedido-edit-desconto-grid">
                <div class="pedido-edit-desconto-mode" aria-label="Tipo de desconto">
                    <label title="Desconto em reais">
                        <input type="radio" name="desconto_tipo" value="valor" checked>
                        <span>R$</span>
                    </label>
                    <label title="Desconto em porcentagem">
                        <input type="radio" name="desconto_tipo" value="percentual">
                        <span>%</span>
                    </label>
                </div>
                <div class="pedido-edit-desconto-value">
                    <label for="pedido-edit-desconto-valor">Valor do desconto</label>
                    <span class="pedido-edit-discount-input-wrap" data-prefix="R$">
                        <input type="number" min="0" step="0.01" name="desconto_valor" id="pedido-edit-desconto-valor" value="0" inputmode="decimal">
                    </span>
                </div>
            </div>
        `;
        summary.classList.add('has-discount');
        summary.insertBefore(card, totalCard);
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons({ elements: card.querySelectorAll('[data-lucide]') });
        }
    };

    const getItemsTotal = () => {
        const grid = document.querySelector('#pedido-edit-grid');
        let total = 0;
        grid?.querySelectorAll('.item-row[data-row]').forEach((row) => {
            total += parseSmart(row.querySelector('input[name="item_quantidade[]"]')?.value) * parseSmart(row.querySelector('input[name="item_preco[]"]')?.value);
        });
        return total;
    };

    const updateEditDiscountTotal = () => {
        const modal = document.getElementById('pedido-edit-modal');
        const totalEl = modal ? modal.querySelector('#pedido-edit-total') : null;
        if (!modal || !totalEl) return;

        const products = getItemsTotal();
        const freteAtivo = Boolean(modal.querySelector('#pedido-edit-frete-ativo')?.checked);
        const frete = freteAtivo ? parseSmart(modal.querySelector('#pedido-edit-frete-valor')?.value) : 0;
        const base = products + frete;
        const tipo = modal.querySelector('input[name="desconto_tipo"]:checked')?.value || 'valor';
        const descontoInput = modal.querySelector('#pedido-edit-desconto-valor');
        const wrap = modal.querySelector('.pedido-edit-discount-input-wrap');
        const valor = parseSmart(descontoInput?.value);
        const desconto = tipo === 'percentual' ? base * Math.min(Math.max(valor, 0), 100) / 100 : Math.min(Math.max(valor, 0), base);
        if (wrap) wrap.dataset.prefix = tipo === 'percentual' ? '%' : 'R$';
        totalEl.textContent = money(Math.max(0, base - desconto));
    };

    const loadPedidoDiscount = (pedidoId) => {
        if (!pedidoId) return;
        fetch(`${basePedidos}/${pedidoId}/editar`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then((payload) => {
                const input = document.querySelector('#pedido-edit-desconto-valor');
                const tipo = payload?.pedido?.desconto_tipo === 'percentual' ? 'percentual' : 'valor';
                const valor = tipo === 'percentual'
                    ? Number(payload?.pedido?.desconto_valor_original || 0)
                    : Number(payload?.pedido?.desconto || 0);
                const radio = document.querySelector(`#pedido-edit-modal input[name="desconto_tipo"][value="${tipo}"]`);
                if (radio) radio.checked = true;
                if (input) input.value = valor.toFixed(2);
                updateEditDiscountTotal();
            })
            .catch(() => updateEditDiscountTotal());
    };

    const refresh = () => {
        scheduled = false;
        setupEditDiscount();
        updateEditDiscountTotal();
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
        document.addEventListener('click', (event) => {
            const button = event.target instanceof Element ? event.target.closest('.pedido-edit-btn') : null;
            window.setTimeout(() => {
                refresh();
                if (button) loadPedidoDiscount(button.dataset.pedido);
            }, 220);
        });
        const grid = document.querySelector('#pedido-edit-grid');
        if (grid) new MutationObserver(schedule).observe(grid, { childList: true });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
