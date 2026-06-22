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

    #pedido-edit-modal .pedido-edit-desconto-mode label.is-active {
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
    var basePedidos = <?= json_encode(rtrim($url('pedidos'), '/'), JSON_UNESCAPED_SLASHES); ?>;
    var lastPedidoId = '';
    var loadingId = '';

    function parseSmart(value) {
        var raw = String(value == null ? '' : value).trim();
        if (!raw) return 0;
        if (raw.indexOf(',') >= 0) return Number(raw.replace(/\./g, '').replace(',', '.')) || 0;
        return Number(raw) || 0;
    }

    function money(value) {
        return 'R$ ' + Number(value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function syncMode(card) {
        var radios = card.querySelectorAll('input[name="desconto_tipo"]');
        radios.forEach(function (radio) {
            var label = radio.closest('label');
            if (label) label.classList.toggle('is-active', radio.checked);
        });
        var checked = card.querySelector('input[name="desconto_tipo"]:checked');
        var wrap = card.querySelector('.pedido-edit-discount-input-wrap');
        if (wrap) wrap.dataset.prefix = checked && checked.value === 'percentual' ? '%' : 'R$';
    }

    function ensureCard() {
        var modal = document.getElementById('pedido-edit-modal');
        if (!modal) return null;
        var form = modal.querySelector('#pedido-edit-form');
        var summary = modal.querySelector('.pedido-edit-summary');
        var totalCard = summary ? summary.querySelector('.item-total') : null;
        if (!form || !summary || !totalCard) return null;

        var card = summary.querySelector('.pedido-edit-desconto-card');
        if (!card) {
            card = document.createElement('div');
            card.className = 'pedido-edit-desconto-card';
            card.innerHTML = '' +
                '<div class="pedido-edit-desconto-title"><i data-lucide="badge-percent"></i><span>Desconto</span></div>' +
                '<div class="pedido-edit-desconto-grid">' +
                    '<div class="pedido-edit-desconto-mode" aria-label="Tipo de desconto">' +
                        '<label class="is-active" title="Desconto em reais"><input type="radio" name="desconto_tipo" value="valor" checked><span>R$</span></label>' +
                        '<label title="Desconto em porcentagem"><input type="radio" name="desconto_tipo" value="percentual"><span>%</span></label>' +
                    '</div>' +
                    '<div class="pedido-edit-desconto-value">' +
                        '<label for="pedido-edit-desconto-valor">Valor do desconto</label>' +
                        '<span class="pedido-edit-discount-input-wrap" data-prefix="R$">' +
                            '<input type="number" min="0" step="0.01" name="desconto_valor" id="pedido-edit-desconto-valor" value="0" inputmode="decimal">' +
                        '</span>' +
                    '</div>' +
                '</div>';
            summary.classList.add('has-discount');
            summary.insertBefore(card, totalCard);
            card.addEventListener('input', updateTotal);
            card.addEventListener('change', function () {
                syncMode(card);
                updateTotal();
            });
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons({ elements: card.querySelectorAll('[data-lucide]') });
            }
        }
        syncMode(card);
        return card;
    }

    function itemsTotal(modal) {
        var total = 0;
        modal.querySelectorAll('#pedido-edit-grid .item-row[data-row]').forEach(function (row) {
            total += parseSmart(row.querySelector('input[name="item_quantidade[]"]') && row.querySelector('input[name="item_quantidade[]"]').value) *
                parseSmart(row.querySelector('input[name="item_preco[]"]') && row.querySelector('input[name="item_preco[]"]').value);
        });
        return total;
    }

    function updateTotal() {
        var modal = document.getElementById('pedido-edit-modal');
        if (!modal) return;
        var card = ensureCard();
        var totalEl = modal.querySelector('#pedido-edit-total');
        if (!card || !totalEl) return;

        var base = itemsTotal(modal);
        var freteAtivo = !!(modal.querySelector('#pedido-edit-frete-ativo') && modal.querySelector('#pedido-edit-frete-ativo').checked);
        if (freteAtivo) base += parseSmart(modal.querySelector('#pedido-edit-frete-valor') && modal.querySelector('#pedido-edit-frete-valor').value);

        var tipo = (card.querySelector('input[name="desconto_tipo"]:checked') || {}).value || 'valor';
        var valor = parseSmart(card.querySelector('#pedido-edit-desconto-valor') && card.querySelector('#pedido-edit-desconto-valor').value);
        var desconto = tipo === 'percentual' ? base * Math.min(Math.max(valor, 0), 100) / 100 : Math.min(Math.max(valor, 0), base);
        totalEl.textContent = money(Math.max(0, base - desconto));
    }

    function loadDiscount(pedidoId) {
        if (!pedidoId || pedidoId === loadingId) return;
        loadingId = pedidoId;
        fetch(basePedidos + '/' + pedidoId + '/editar', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) { return response.ok ? response.json() : Promise.reject(response); })
            .then(function (payload) {
                var card = ensureCard();
                if (!card) return;
                var tipo = payload && payload.pedido && payload.pedido.desconto_tipo === 'percentual' ? 'percentual' : 'valor';
                var valorRadio = card.querySelector('input[name="desconto_tipo"][value="' + tipo + '"]');
                var input = card.querySelector('#pedido-edit-desconto-valor');
                var valor = tipo === 'percentual'
                    ? Number(payload && payload.pedido && payload.pedido.desconto_valor_original ? payload.pedido.desconto_valor_original : 0)
                    : Number(payload && payload.pedido && payload.pedido.desconto ? payload.pedido.desconto : 0);
                if (valorRadio) valorRadio.checked = true;
                if (input) input.value = valor.toFixed(2);
                syncMode(card);
                updateTotal();
            })
            .catch(updateTotal)
            .finally(function () { loadingId = ''; });
    }

    function tick() {
        var modal = document.getElementById('pedido-edit-modal');
        if (!modal) return;
        ensureCard();
        updateTotal();
        var pedidoInput = modal.querySelector('input[name="pedido_id"]');
        var pedidoId = pedidoInput ? pedidoInput.value : '';
        if (pedidoId && pedidoId !== lastPedidoId) {
            lastPedidoId = pedidoId;
            loadDiscount(pedidoId);
        }
    }

    function run() {
        tick();
        document.addEventListener('input', function (event) {
            if (event.target && event.target.closest && event.target.closest('#pedido-edit-modal')) updateTotal();
        });
        document.addEventListener('change', function (event) {
            if (event.target && event.target.closest && event.target.closest('#pedido-edit-modal')) updateTotal();
        });
        setInterval(tick, 500);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
