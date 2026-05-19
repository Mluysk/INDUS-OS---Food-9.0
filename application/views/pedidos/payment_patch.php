<script>
(function () {
    const paymentOptions = [
        ['PIX', 'PIX'],
        ['Dinheiro', 'Dinheiro'],
        ['Cartão de crédito', 'Cartão de crédito'],
        ['Cartão de débito', 'Cartão de débito'],
        ['Boleto', 'Boleto'],
        ['TED', 'TED'],
        ['Transferência bancária', 'Transferência bancária'],
        ['Depósito bancário', 'Depósito bancário'],
        ['Cheque', 'Cheque'],
        ['Vale alimentação', 'Vale alimentação'],
        ['Vale refeição', 'Vale refeição'],
        ['A prazo', 'A prazo'],
        ['Outros', 'Outros'],
    ];

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
        if (!form || form.querySelector('select[name="forma_pagto"]')) {
            return null;
        }
        const cliente = form.querySelector('select[name="cliente_id"]')?.closest('label');
        if (!cliente) {
            return null;
        }
        const field = buildPaymentLabel(id);
        cliente.insertAdjacentElement('afterend', field);
        return field.querySelector('select');
    };

    const ensureCreatePayment = () => {
        const select = insertAfterCliente(document.getElementById('pedido-form'), 'pedido-forma-pagto');
        if (select) {
            select.value = 'PIX';
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
                })
                .catch(() => {
                    select.value = 'PIX';
                });
        }, 150);
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
