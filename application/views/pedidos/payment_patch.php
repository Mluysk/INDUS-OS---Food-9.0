<script>
(function () {
    const pixLogoUrl = <?= json_encode($asset('uploads/branding/pix-logo.png'), JSON_UNESCAPED_SLASHES); ?>;
    const paymentOptions = [
        ['PIX', 'PIX', 'pix'],
        ['Dinheiro', 'Dinheiro', 'banknote'],
        ['Cartão de crédito', 'Cartão de crédito', 'card'],
        ['Cartão de débito', 'Cartão de débito', 'card'],
        ['Boleto', 'Boleto', 'file'],
        ['TED', 'TED', 'bank'],
        ['Transferência bancária', 'Transferência bancária', 'transfer'],
        ['Depósito bancário', 'Depósito bancário', 'bank'],
        ['Cheque', 'Cheque', 'receipt'],
        ['Vale alimentação', 'Vale alimentação', 'utensils'],
        ['Vale refeição', 'Vale refeição', 'utensils'],
        ['A prazo', 'A prazo', 'calendar'],
        ['Outros', 'Outros', 'more'],
    ];
    const paymentOptionMap = new Map(paymentOptions.map(([value, text, icon]) => [value, { text, icon }]));

    const iconSvg = (name) => {
        const paths = {
            banknote: '<rect x="3" y="6" width="18" height="12" rx="2"></rect><circle cx="12" cy="12" r="2.2"></circle><path d="M7 10v4M17 10v4"></path>',
            card: '<rect x="3" y="5" width="18" height="14" rx="2.5"></rect><path d="M3 10h18M7 15h4"></path>',
            file: '<path d="M7 3h7l4 4v14H7z"></path><path d="M14 3v5h5M9 13h6M9 17h4"></path>',
            bank: '<path d="M4 10h16L12 5z"></path><path d="M6 10v8M10 10v8M14 10v8M18 10v8M4 19h16"></path>',
            transfer: '<path d="M7 7h13M17 4l3 3-3 3M17 17H4M7 14l-3 3 3 3"></path>',
            receipt: '<path d="M7 3h10v18l-2-1.2L13 21l-2-1.2L9 21l-2-1.2z"></path><path d="M9 8h6M9 12h6M9 16h4"></path>',
            utensils: '<path d="M7 3v8M5 3v4a2 2 0 0 0 4 0V3M7 11v10M16 3v18M16 3c2 1.4 3 3.3 3 5.5 0 1.8-.8 3.2-3 4"></path>',
            calendar: '<rect x="4" y="5" width="16" height="16" rx="2"></rect><path d="M8 3v4M16 3v4M4 10h16M8 14h3M13 14h3M8 18h3"></path>',
            more: '<circle cx="6" cy="12" r="1.5"></circle><circle cx="12" cy="12" r="1.5"></circle><circle cx="18" cy="12" r="1.5"></circle>',
        };
        return `<svg class="payment-option-icon" viewBox="0 0 24 24" aria-hidden="true">${paths[name] || paths.more}</svg>`;
    };

    const iconMarkup = (value) => {
        const option = paymentOptionMap.get(value);
        if (option?.icon === 'pix') {
            return `<img class="payment-option-pix" src="${pixLogoUrl}" alt="">`;
        }
        return iconSvg(option?.icon || 'more');
    };

    const optionLabel = (value) => paymentOptionMap.get(value)?.text || value || 'PIX';

    const syncPaymentSelect = (select) => {
        const custom = select?.nextElementSibling;
        if (!select || !custom?.classList.contains('payment-select-ui')) {
            return;
        }
        const button = custom.querySelector('.payment-select-button');
        if (button) {
            button.innerHTML = `${iconMarkup(select.value)}<span>${optionLabel(select.value)}</span>`;
        }
        custom.querySelectorAll('[data-payment-value]').forEach((item) => {
            item.classList.toggle('is-selected', item.dataset.paymentValue === select.value);
        });
    };

    const enhancePaymentSelect = (select) => {
        if (!select || select.dataset.paymentEnhanced === '1') {
            syncPaymentSelect(select);
            return select;
        }
        select.dataset.paymentEnhanced = '1';
        select.classList.add('payment-native-select');
        const custom = document.createElement('div');
        custom.className = 'payment-select-ui';
        custom.innerHTML = `
            <button type="button" class="payment-select-button" aria-haspopup="listbox" aria-expanded="false"></button>
            <div class="payment-select-menu" role="listbox" hidden>
                ${paymentOptions.map(([value, text]) => `
                    <button type="button" class="payment-select-option" role="option" data-payment-value="${value}">
                        ${iconMarkup(value)}<span>${text}</span>
                    </button>
                `).join('')}
            </div>
        `;
        select.insertAdjacentElement('afterend', custom);
        const trigger = custom.querySelector('.payment-select-button');
        const menu = custom.querySelector('.payment-select-menu');
        trigger.addEventListener('click', () => {
            const open = menu.hidden;
            document.querySelectorAll('.payment-select-menu:not([hidden])').forEach((otherMenu) => {
                if (otherMenu !== menu) {
                    otherMenu.hidden = true;
                    otherMenu.previousElementSibling?.setAttribute('aria-expanded', 'false');
                }
            });
            menu.hidden = !open;
            trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        custom.querySelectorAll('[data-payment-value]').forEach((item) => {
            item.addEventListener('click', () => {
                select.value = item.dataset.paymentValue;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
            });
        });
        select.addEventListener('change', () => syncPaymentSelect(select));
        syncPaymentSelect(select);
        return select;
    };

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.payment-select-ui')) {
            document.querySelectorAll('.payment-select-menu:not([hidden])').forEach((menu) => {
                menu.hidden = true;
                menu.previousElementSibling?.setAttribute('aria-expanded', 'false');
            });
        }
    });

    const buildPaymentLabel = (id) => {
        const label = document.createElement('label');
        label.className = 'pedido-payment-field';
        label.innerHTML = `
            Opção de pagamento
            <select name="forma_pagto" id="${id}">
                ${paymentOptions.map(([value, text]) => `<option value="${value}">${text}</option>`).join('')}
            </select>
        `;
        return label;
    };

    const insertAfterCliente = (form, id) => {
        if (!form) {
            return null;
        }
        const existing = form.querySelector('select[name="forma_pagto"]');
        if (existing) {
            return enhancePaymentSelect(existing);
        }
        const cliente = form.querySelector('select[name="cliente_id"]')?.closest('label');
        if (!cliente) {
            return null;
        }
        const field = buildPaymentLabel(id);
        cliente.insertAdjacentElement('afterend', field);
        return enhancePaymentSelect(field.querySelector('select'));
    };

    const ensureCreatePayment = () => {
        const select = insertAfterCliente(document.getElementById('pedido-form'), 'pedido-forma-pagto');
        if (select) {
            select.value = 'PIX';
            syncPaymentSelect(select);
        }
    };

    const ensureEditPayment = () => {
        return insertAfterCliente(document.getElementById('pedido-edit-form'), 'pedido-edit-forma-pagto');
    };

    const basePedidos = <?= json_encode($url('pedidos'), JSON_UNESCAPED_SLASHES); ?>.replace(/\/$/, '');

    document.addEventListener('click', (event) => {
        const button = event.target.closest('.pedido-edit-btn');
        if (!button) {
            return;
        }
        const select = ensureEditPayment();
        const pedidoId = button.dataset.pedido;
        if (!pedidoId || !select) {
            return;
        }
        window.setTimeout(() => {
            fetch(`${basePedidos}/${pedidoId}/editar`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then((response) => response.ok ? response.json() : null)
                .then((payload) => {
                    const pagamento = payload?.pedido?.forma_pagto || 'PIX';
                    if (Array.from(select.options).some((option) => option.value === pagamento)) {
                        select.value = pagamento;
                    } else {
                        select.value = 'PIX';
                    }
                    syncPaymentSelect(select);
                })
                .catch(() => {
                    select.value = 'PIX';
                    syncPaymentSelect(select);
                });
        }, 150);
        window.setTimeout(() => {
            document.querySelectorAll('select[name="forma_pagto"]').forEach(syncPaymentSelect);
        }, 350);
    }, true);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            ensureCreatePayment();
            ensureEditPayment();
        });
    } else {
        ensureCreatePayment();
        ensureEditPayment();
    }
})();
</script>

<style>
    #pedido-form {
        grid-template-columns: minmax(280px, 1.15fr) minmax(220px, 0.85fr) minmax(420px, 1.75fr);
        align-items: start;
    }

    #pedido-form label:has(select[name="cliente_id"]) {
        min-width: 280px;
    }

    #pedido-form .pedido-payment-field,
    #pedido-edit-form .pedido-payment-field {
        min-width: 0;
    }

    .payment-native-select {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    .payment-select-ui {
        position: relative;
        margin-top: 6px;
    }

    .payment-select-button,
    .payment-select-option {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        color: var(--text, #0f172a);
        font: inherit;
        text-align: left;
    }

    .payment-select-button {
        min-height: 52px;
        padding: 0 16px;
        border: 1px solid var(--border, #d8dee8);
        border-radius: 14px;
        background: var(--surface, #fff);
        justify-content: flex-start;
    }

    .payment-select-button::after {
        content: "";
        width: 8px;
        height: 8px;
        margin-left: auto;
        border-right: 2px solid currentColor;
        border-bottom: 2px solid currentColor;
        transform: rotate(45deg) translateY(-2px);
        opacity: 0.75;
    }

    .payment-select-menu {
        position: absolute;
        z-index: 70;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        max-height: 320px;
        overflow: auto;
        padding: 6px;
        border: 1px solid var(--border, #d8dee8);
        border-radius: 14px;
        background: var(--surface, #fff);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.16);
    }

    .payment-select-option {
        min-height: 38px;
        padding: 8px 10px;
        border: 0;
        border-radius: 10px;
        background: transparent;
        cursor: pointer;
    }

    .payment-select-option:hover,
    .payment-select-option.is-selected {
        background: rgba(15, 23, 42, 0.07);
    }

    .payment-option-icon {
        width: 18px;
        height: 18px;
        color: currentColor;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
        flex: 0 0 18px;
    }

    .payment-option-pix {
        width: 19px;
        height: 19px;
        object-fit: cover;
        object-position: left center;
        flex: 0 0 19px;
    }

    #pedido-form label:has(textarea[name="observacoes"]) {
        min-width: 420px;
    }

    #pedido-form textarea[name="observacoes"] {
        width: 100%;
        min-height: 56px;
        resize: vertical;
    }

    @media (max-width: 1180px) {
        #pedido-form {
            grid-template-columns: repeat(2, minmax(240px, 1fr));
        }

        #pedido-form label:has(textarea[name="observacoes"]) {
            grid-column: 1 / -1;
            min-width: 0;
        }
    }

    @media (max-width: 720px) {
        #pedido-form {
            grid-template-columns: 1fr;
        }

        #pedido-form label:has(select[name="cliente_id"]),
        #pedido-form label:has(textarea[name="observacoes"]) {
            min-width: 0;
        }
    }
</style>
