<style>
    #pedido-form .pedido-item-builder {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        gap: 1.15rem;
        margin-top: 0.35rem;
    }

    #pedido-form .pedido-item-title strong,
    #pedido-form .pedido-products-list h4 {
        display: block;
        margin: 0;
        color: var(--text-strong, #0f172a);
        font-size: 1.05rem;
    }

    #pedido-form .pedido-item-title .panel-description {
        margin: 0.1rem 0 0;
    }

    #pedido-form .pedido-item-entry {
        display: grid;
        grid-template-columns: minmax(96px, 0.42fr) minmax(250px, 1.45fr) minmax(110px, 0.52fr) minmax(110px, 0.5fr) minmax(130px, 0.62fr) minmax(140px, 0.65fr) minmax(145px, auto);
        gap: 0.75rem;
        align-items: end;
    }

    #pedido-form .pedido-field {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        min-width: 0;
        margin: 0;
        color: var(--text-muted, #64748b);
        font-weight: 700;
    }

    #pedido-form .pedido-field input {
        width: 100%;
        min-height: 46px;
    }

    #pedido-form .pedido-subtotal-preview,
    #pedido-form .pedido-readonly-value {
        display: flex;
        align-items: center;
        min-height: 46px;
        padding: 0.65rem 0.9rem;
        border: 1px solid var(--border-subtle, #e2e8f0);
        border-radius: 12px;
        background: var(--surface-muted, #f8fafc);
        color: var(--text-strong, #0f172a);
        font-weight: 800;
        white-space: nowrap;
    }

    #pedido-form .pedido-readonly-value {
        color: var(--text-muted, #64748b);
        font-weight: 750;
    }

    #pedido-form .pedido-code-field {
        max-width: 118px;
    }

    #pedido-form #pedido-produto-codigo {
        justify-content: center;
        padding-inline: 0.55rem;
        font-variant-numeric: tabular-nums;
    }

    #pedido-form .pedido-add-item {
        min-height: 46px;
        align-self: end;
        white-space: nowrap;
    }

    #pedido-form .produto-search-wrap {
        position: relative;
        outline: 0 !important;
        box-shadow: none !important;
        border-color: transparent;
    }

    #pedido-form .pedido-field:has(.produto-search-wrap):focus,
    #pedido-form .pedido-field:has(.produto-search-wrap):focus-within,
    #pedido-form .produto-search-wrap:focus,
    #pedido-form .produto-search-wrap:focus-within {
        outline: 0 !important;
        box-shadow: none !important;
        border-color: transparent !important;
    }

    #pedido-form #pedido-produto-search {
        outline: 0;
    }

    #pedido-form #pedido-produto-search:focus {
        outline: 0 !important;
        border-color: var(--primary, #dc2626);
        box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 220, 38, 38), 0.14);
    }

    #pedido-form .produto-search-wrap .search-clear-button {
        top: calc(1.2em + 0.35rem + 23px);
        right: 12px;
        width: 24px;
        height: 24px;
        min-width: 24px;
        min-height: 24px;
        transform: translateY(-50%);
        display: grid;
        place-items: center;
        line-height: 1;
    }

    #pedido-form .produto-search-wrap .search-clear-button svg {
        display: block;
        width: 12px;
        height: 12px;
        margin: 0;
    }

    #pedido-form .produto-suggestions {
        position: absolute;
        z-index: 30;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        max-height: 260px;
        overflow: auto;
        margin: 0;
        padding: 0.35rem;
        list-style: none;
        border: 1px solid var(--border-subtle, #e2e8f0);
        border-radius: 12px;
        background: var(--surface-elevated, #fff);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.14);
    }

    #pedido-form .produto-suggestions[hidden] {
        display: none !important;
    }

    #pedido-form .produto-suggestions button {
        width: 100%;
        border: 0;
        border-radius: 9px;
        padding: 0.62rem 0.75rem;
        background: transparent;
        text-align: left;
        cursor: pointer;
        color: var(--text-strong, #0f172a);
        font-weight: 700;
    }

    #pedido-form .produto-suggestions button:hover,
    #pedido-form .produto-suggestions button:focus {
        outline: none;
        background: var(--surface-muted, #f1f5f9);
    }

    #pedido-form .produto-suggestion-meta {
        display: block;
        margin-top: 0.15rem;
        color: var(--text-muted, #64748b);
        font-size: 0.78rem;
        font-weight: 600;
    }

    #pedido-form .pedido-products-list {
        padding: 0.95rem 0 0;
    }

    #pedido-form .pedido-products-table {
        display: grid;
        gap: 0;
        margin-top: 0.75rem;
        overflow: hidden;
        border: 1px solid var(--border-subtle, #e2e8f0);
        border-radius: 14px;
        background: var(--surface-elevated, #fff);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    #pedido-form .pedido-products-head,
    #pedido-form .pedido-products-row {
        display: grid;
        grid-template-columns: minmax(92px, 0.42fr) minmax(240px, 1.55fr) minmax(120px, 0.52fr) minmax(108px, 0.48fr) minmax(125px, 0.58fr) minmax(130px, 0.6fr) 52px;
        gap: 0.7rem;
        align-items: center;
    }

    #pedido-form .pedido-products-head {
        min-height: 46px;
        padding: 0 1rem;
        background: var(--surface-muted, #f8fafc);
        color: var(--text-muted, #64748b);
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        border-bottom: 1px solid var(--border-subtle, #e2e8f0);
    }

    #pedido-form #pedido-products-body {
        display: grid;
    }

    #pedido-form .pedido-products-row {
        min-height: 58px;
        padding: 0.55rem 1rem;
        color: var(--text-strong, #0f172a);
        font-weight: 700;
        border-bottom: 1px solid rgba(226, 232, 240, 0.72);
    }

    #pedido-form .pedido-products-row:last-child {
        border-bottom: 0;
    }

    #pedido-form .pedido-products-row:hover {
        background: rgba(248, 250, 252, 0.75);
    }

    #pedido-form .pedido-product-code,
    #pedido-form .pedido-line-weight {
        color: var(--text-muted, #64748b);
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    #pedido-form .pedido-product-name {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #pedido-form .pedido-line-input {
        width: 100%;
        max-width: 132px;
        min-height: 38px;
        padding: 0.45rem 0.65rem;
        border: 1px solid var(--border-subtle, #e2e8f0);
        border-radius: 10px;
        background: var(--surface-muted, #f8fafc);
        color: var(--text-strong, #0f172a);
        font-weight: 800;
    }

    #pedido-form .pedido-line-input:focus {
        outline: none;
        border-color: var(--primary, #dc2626);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(var(--brand-rgb, 220, 38, 38), 0.12);
    }

    #pedido-form .pedido-line-subtotal {
        font-weight: 900;
        color: var(--text-strong, #0f172a);
    }

    #pedido-form .pedido-products-row .remove-item {
        width: 32px;
        height: 32px;
        min-height: 32px;
        padding: 0;
        justify-self: center;
        border-radius: 10px;
    }

    #pedido-form .pedido-empty-list {
        margin: 0.85rem 0 0;
        color: var(--text-muted, #64748b);
        font-size: 0.92rem;
    }

    #pedido-form .item-total {
        justify-content: flex-start;
        gap: 0.3rem;
        margin-top: 0.65rem;
        padding: 0.75rem 1rem;
        width: fit-content;
        border-radius: 12px;
        background: var(--surface-muted, #f8fafc);
    }

    #pedido-form .form-actions {
        justify-content: flex-end;
    }

    @media (max-width: 1180px) {
        #pedido-form .pedido-item-entry {
            grid-template-columns: repeat(3, minmax(160px, 1fr));
        }
    }

    @media (max-width: 980px) {
        #pedido-form .pedido-item-entry,
        #pedido-form .pedido-products-head,
        #pedido-form .pedido-products-row {
            grid-template-columns: 1fr 1fr;
        }

        #pedido-form .pedido-add-item,
        #pedido-form .pedido-products-row .remove-item {
            justify-self: start;
        }
    }

    @media (max-width: 640px) {
        #pedido-form .pedido-item-entry,
        #pedido-form .pedido-products-head,
        #pedido-form .pedido-products-row {
            grid-template-columns: 1fr;
        }

        #pedido-form .pedido-code-field {
            max-width: none;
        }

        #pedido-form .pedido-products-head {
            display: none;
        }

        #pedido-form .pedido-products-row {
            gap: 0.45rem;
        }
    }
</style>

<script>
(function () {
    const produtos = <?= json_encode($produtos ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    const money = (value) => 'R$ ' + Number(value || 0).toFixed(2).replace('.', ',');
    const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    const formatWeight = (value) => {
        const numeric = Number(value || 0);
        if (!numeric) {
            return '-';
        }
        if (numeric < 1) {
            return `${(numeric * 1000).toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 })} g`;
        }
        return `${numeric.toLocaleString('pt-BR', { minimumFractionDigits: 3, maximumFractionDigits: 3 })} kg`;
    };

    const run = () => {
        const form = document.getElementById('pedido-form');
        const oldTable = form ? form.querySelector('.item-table') : null;
        if (!form || !oldTable || oldTable.dataset.searchPatch === '1') {
            return;
        }

        oldTable.dataset.searchPatch = '1';
        oldTable.className = 'pedido-item-builder';
        oldTable.innerHTML = `
            <div class="pedido-item-title">
                <strong>Itens do pedido</strong>
                <p class="panel-description">Use os produtos já cadastrados e informe quantidade e valor unitário.</p>
            </div>
            <div class="pedido-item-entry">
                <label class="pedido-field pedido-code-field">
                    Código
                    <span class="pedido-readonly-value" id="pedido-produto-codigo">-</span>
                </label>
                <label class="pedido-field produto-search-wrap">
                    Produtos
                    <input type="text" id="pedido-produto-search" autocomplete="off" placeholder="Produto..." aria-autocomplete="list">
                    <ul class="produto-suggestions" id="pedido-produto-suggestions" hidden></ul>
                </label>
                <label class="pedido-field">
                    Quantidade
                    <input type="number" min="0" step="0.01" id="pedido-item-quantidade" value="1">
                </label>
                <label class="pedido-field">
                    Peso
                    <span class="pedido-readonly-value" id="pedido-produto-peso">-</span>
                </label>
                <label class="pedido-field">
                    Valor
                    <input type="number" min="0" step="0.01" id="pedido-item-valor" value="0">
                </label>
                <label class="pedido-field">
                    Subtotal
                    <span class="pedido-subtotal-preview" id="pedido-item-subtotal">R$ 0,00</span>
                </label>
                <button type="button" class="button pedido-add-item" id="pedido-add-search-item">Adicionar item</button>
            </div>
            <div class="pedido-products-list">
                <h4>Lista de Produtos</h4>
                <div class="pedido-products-table">
                    <div class="pedido-products-head">
                        <span>Código</span>
                        <span>Produtos</span>
                        <span>Quantidade</span>
                        <span>Peso</span>
                        <span>Valor</span>
                        <span>Subtotal</span>
                        <span></span>
                    </div>
                    <div id="pedido-products-body"></div>
                </div>
                <p class="pedido-empty-list" id="pedido-empty-list">Nenhum produto adicionado.</p>
            </div>
            <div class="item-total">
                <span>Total</span>
                <strong id="total-display">R$ 0,00</strong>
            </div>
        `;

        const search = document.getElementById('pedido-produto-search');
        const suggestions = document.getElementById('pedido-produto-suggestions');
        const codeEl = document.getElementById('pedido-produto-codigo');
        const weightEl = document.getElementById('pedido-produto-peso');
        const qtyInput = document.getElementById('pedido-item-quantidade');
        const priceInput = document.getElementById('pedido-item-valor');
        const subtotalEl = document.getElementById('pedido-item-subtotal');
        const addButton = document.getElementById('pedido-add-search-item');
        const body = document.getElementById('pedido-products-body');
        const empty = document.getElementById('pedido-empty-list');
        const totalEl = oldTable.querySelector('#total-display');
        let selectedProduct = null;

        const productLabel = (product) => product.nome || 'Produto';
        const productPrice = (product) => Number(product.preco ?? product.preco_venda ?? product.valor ?? 0);
        const productWeight = (product) => Number(product.peso_liquido ?? product.peso ?? product.peso_bruto ?? 0);

        const updateEntrySubtotal = () => {
            const subtotal = (Number(qtyInput.value) || 0) * (Number(priceInput.value) || 0);
            subtotalEl.textContent = money(subtotal);
        };

        const updateRow = (row) => {
            const qty = Number(row.querySelector('.pedido-line-qty')?.value) || 0;
            const price = Number(row.querySelector('.pedido-line-price')?.value) || 0;
            const subtotal = qty * price;
            row.dataset.subtotal = String(subtotal);
            row.querySelector('.pedido-line-subtotal').textContent = money(subtotal);
            const hiddenQuantity = row.querySelector('input[name="item_quantidade[]"]');
            const hiddenPrice = row.querySelector('input[name="item_preco[]"]');
            if (hiddenQuantity) {
                hiddenQuantity.value = String(qty);
            }
            if (hiddenPrice) {
                hiddenPrice.value = String(price);
            }
        };

        const updateTotal = () => {
            let total = 0;
            body.querySelectorAll('.pedido-products-row').forEach((row) => {
                updateRow(row);
                total += Number(row.dataset.subtotal || 0);
            });
            totalEl.textContent = money(total);
            empty.hidden = body.querySelectorAll('.pedido-products-row').length > 0;
        };

        const closeSuggestions = () => {
            suggestions.hidden = true;
        };

        const searchHasFocus = () => document.activeElement === search;

        const clearProductInfo = () => {
            codeEl.textContent = '-';
            weightEl.textContent = '-';
        };

        const chooseProduct = (product) => {
            selectedProduct = product;
            search.value = productLabel(product);
            codeEl.textContent = product.sku || '-';
            weightEl.textContent = formatWeight(productWeight(product));
            priceInput.value = productPrice(product).toFixed(2);
            updateEntrySubtotal();
            closeSuggestions();
        };

        const renderSuggestions = () => {
            if (!searchHasFocus()) {
                closeSuggestions();
                return;
            }
            const term = normalize(search.value);
            const matches = produtos
                .filter((product) => normalize(`${product.nome || ''} ${product.sku || ''}`).includes(term));

            suggestions.innerHTML = '';
            if (!matches.length) {
                suggestions.innerHTML = '<li><button type="button" disabled>Nenhum produto encontrado</button></li>';
                suggestions.hidden = false;
                selectedProduct = null;
                clearProductInfo();
                return;
            }

            matches.forEach((product) => {
                const item = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.innerHTML = `${product.nome || 'Produto'}<span class="produto-suggestion-meta">${product.sku || 'Sem SKU'} - ${formatWeight(productWeight(product))} - ${money(productPrice(product))}</span>`;
                button.addEventListener('mousedown', (event) => event.preventDefault());
                button.addEventListener('click', () => chooseProduct(product));
                item.appendChild(button);
                suggestions.appendChild(item);
            });
            suggestions.hidden = false;
        };

        const addItem = () => {
            if (!selectedProduct) {
                search.focus();
                renderSuggestions();
                return;
            }

            const quantity = Number(qtyInput.value) || 0;
            const price = Number(priceInput.value) || 0;
            const sku = selectedProduct.sku || '-';
            const weight = formatWeight(productWeight(selectedProduct));
            if (quantity <= 0) {
                qtyInput.focus();
                return;
            }

            const subtotal = quantity * price;
            const row = document.createElement('div');
            row.className = 'pedido-products-row';
            row.dataset.subtotal = String(subtotal);
            row.innerHTML = `
                <span class="pedido-product-code">${sku}</span>
                <span class="pedido-product-name" title="${selectedProduct.nome || 'Produto'}">${selectedProduct.nome || 'Produto'}</span>
                <input type="number" min="0" step="0.01" class="pedido-line-input pedido-line-qty" value="${quantity}">
                <span class="pedido-line-weight">${weight}</span>
                <input type="number" min="0" step="0.01" class="pedido-line-input pedido-line-price" value="${price.toFixed(2)}">
                <span class="pedido-line-subtotal">${money(subtotal)}</span>
                <button type="button" class="button button-ghost remove-item" aria-label="Remover item">×</button>
                <input type="hidden" name="item_produto[]" value="${selectedProduct.id}">
                <input type="hidden" name="item_quantidade[]" value="${quantity}">
                <input type="hidden" name="item_preco[]" value="${price}">
            `;
            row.querySelectorAll('.pedido-line-input').forEach((input) => {
                input.addEventListener('input', updateTotal);
            });
            row.querySelector('.remove-item').addEventListener('click', () => {
                row.remove();
                updateTotal();
            });

            body.appendChild(row);
            selectedProduct = null;
            search.value = '';
            qtyInput.value = '1';
            priceInput.value = '0';
            clearProductInfo();
            closeSuggestions();
            search.blur();
            updateEntrySubtotal();
            updateTotal();
        };

        search.addEventListener('input', () => {
            selectedProduct = null;
            clearProductInfo();
            renderSuggestions();
        });
        search.addEventListener('focus', renderSuggestions);
        search.addEventListener('blur', () => window.setTimeout(closeSuggestions, 120));
        search.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                const first = suggestions.querySelector('button:not([disabled])');
                first?.click();
            }
            if (event.key === 'Escape') {
                closeSuggestions();
                search.blur();
            }
        });
        document.addEventListener('click', (event) => {
            if (!oldTable.contains(event.target)) {
                closeSuggestions();
            }
        });

        qtyInput.addEventListener('input', updateEntrySubtotal);
        priceInput.addEventListener('input', updateEntrySubtotal);
        addButton.addEventListener('click', addItem);

        form.addEventListener('submit', (event) => {
            updateTotal();
            if (!body.querySelector('.pedido-products-row:not([data-difference-charge])')) {
                event.preventDefault();
                search.focus();
                renderSuggestions();
            }
        });

        window.addEventListener('app:live-saved', (event) => {
            if (event.detail?.form !== form) {
                return;
            }

            form.reset();
            selectedProduct = null;
            body.innerHTML = '';
            search.value = '';
            qtyInput.value = '1';
            priceInput.value = '0';

            const clientSearch = form.querySelector('#pedido-cliente-search');
            if (clientSearch) {
                clientSearch.value = '';
                delete clientSearch.dataset.selectedCliente;
            }

            const clientSelect = form.querySelector('select[name="cliente_id"]');
            if (clientSelect) {
                clientSelect.value = '';
            }

            const payment = form.querySelector('select[name="forma_pagto"]');
            if (payment) {
                payment.value = 'PIX';
            }

            clearProductInfo();
            closeSuggestions();
            updateEntrySubtotal();
            updateTotal();

            form.querySelectorAll('input, select, textarea').forEach((field) => {
                field.dispatchEvent(new Event('input', { bubbles: true }));
                field.dispatchEvent(new Event('change', { bubbles: true }));
            });
            form.dispatchEvent(new CustomEvent('pedido:reset-after-create', { bubbles: true }));
        });

        clearProductInfo();
        updateEntrySubtotal();
        updateTotal();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();
</script>
