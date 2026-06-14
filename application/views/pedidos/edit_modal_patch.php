<div id="pedido-edit-modal" class="pedido-edit-modal hidden" aria-hidden="true">
    <div class="pedido-edit-backdrop" data-edit-close></div>
    <div class="pedido-edit-dialog" role="dialog" aria-modal="true" aria-labelledby="pedido-edit-title">
        <div class="pedido-edit-head">
            <div>
                <p class="muted" style="margin:0; font-size:0.85rem;">Editar pedido</p>
                <h3 id="pedido-edit-title">Atualize os dados do pedido</h3>
            </div>
            <button type="button" class="button ghost" data-edit-close aria-label="Fechar">x</button>
        </div>

        <form id="pedido-edit-form" class="form-grid">
            <input type="hidden" name="pedido_id" value="">
            <label>
                Cliente
                <select name="cliente_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach (($clientes ?? []) as $cliente): ?>
                        <option value="<?= (int)$cliente['id']; ?>"><?= htmlspecialchars($cliente['nome_fantasia'] ?? $cliente['razao_social'] ?? 'Cliente '.$cliente['id'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="pedido-payment-field">
                Opção de pagamento
                <select name="forma_pagto">
                    <option value="PIX">PIX</option>
                    <option value="Dinheiro">Dinheiro</option>
                    <option value="Cartão de crédito">Cartão de crédito</option>
                    <option value="Cartão de débito">Cartão de débito</option>
                    <option value="Boleto">Boleto</option>
                    <option value="TED">TED</option>
                    <option value="Transferência bancária">Transferência bancária</option>
                    <option value="Depósito bancário">Depósito bancário</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Vale alimentação">Vale alimentação</option>
                    <option value="Vale refeição">Vale refeição</option>
                    <option value="A prazo">A prazo</option>
                    <option value="Outros">Outros</option>
                </select>
            </label>
            <label>
                Observações
                <textarea name="observacoes" rows="2" placeholder="Instruções internas, datas ou referências"></textarea>
            </label>

            <div class="pedido-edit-items">
                <div class="item-table__header">
                    <div>
                        <strong>Itens do pedido</strong>
                        <p class="panel-description">Ao trocar o produto, código SKU, peso, valor e subtotal acompanham automaticamente.</p>
                    </div>
                    <button type="button" class="button" id="pedido-edit-add-item">Adicionar item</button>
                </div>
                <div class="pedido-edit-grid" id="pedido-edit-grid">
                    <div class="item-row item-row--head">
                        <span>Código</span>
                        <span>Produto</span>
                        <span>Quantidade</span>
                        <span>Peso</span>
                        <span>Valor</span>
                        <span>Subtotal</span>
                        <span></span>
                    </div>
                </div>
                <div class="pedido-edit-summary">
                    <div class="pedido-edit-frete-card">
                        <div class="pedido-edit-frete-title"><i data-lucide="truck"></i><span>Frete</span></div>
                        <div class="pedido-edit-frete-row">
                            <label class="pedido-edit-frete-toggle" title="Ativar frete">
                                <input type="checkbox" name="frete_ativo" value="1" id="pedido-edit-frete-ativo">
                                <span class="pedido-edit-frete-track"></span>
                            </label>
                            <div class="pedido-edit-frete-value">
                                <label for="pedido-edit-frete-valor">Valor do frete</label>
                                <input type="number" min="0" step="0.01" name="frete_valor" id="pedido-edit-frete-valor" value="0.00" inputmode="decimal">
                            </div>
                        </div>
                    </div>
                    <div class="item-total">
                        <span>Valor total</span>
                        <strong id="pedido-edit-total">R$ 0,00</strong>
                    </div>
                </div>
            </div>

            <p class="pedido-edit-feedback" aria-live="polite"></p>
            <div class="form-actions pedido-edit-actions">
                <button type="submit" class="button button-primary">Salvar alterações</button>
                <button type="button" class="button ghost" data-edit-close>Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
    .pedido-edit-modal.hidden { display: none; }
    .pedido-edit-modal { position: fixed; inset: 0; z-index: 1300; display: grid; place-items: center; padding: 24px; }
    .pedido-edit-backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.42); backdrop-filter: blur(2px); }
    .pedido-edit-dialog { position: relative; width: min(1180px, 96vw); max-height: 90vh; overflow-y: auto; padding: 22px; border-radius: 18px; background: var(--surface-elevated, #fff); color: var(--text-primary, #0f172a); box-shadow: 0 22px 70px rgba(15, 23, 42, 0.22); }
    .pedido-edit-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 16px; }
    .pedido-edit-head h3 { margin: 4px 0 0; }
    .pedido-edit-items { grid-column: 1 / -1; display: grid; gap: 12px; padding: 16px; border: 1px solid var(--border, #e5e7eb); border-radius: 14px; background: var(--surface-alt, #f8fafc); }
    .pedido-edit-grid { display: grid; gap: 8px; }
    .pedido-edit-grid .item-row { display: grid; grid-template-columns: minmax(104px, 0.5fr) minmax(250px, 1.55fr) minmax(110px, 0.55fr) minmax(110px, 0.55fr) minmax(120px, 0.62fr) minmax(120px, 0.62fr) 44px; gap: 8px; align-items: center; }
    .pedido-edit-grid .item-row--head { color: var(--text-muted, #64748b); font-size: 0.78rem; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; }
    .pedido-edit-grid select, .pedido-edit-grid input { width: 100%; }
    .pedido-edit-code, .pedido-edit-weight, .pedido-edit-subtotal { display: flex; align-items: center; min-height: 42px; padding: 0.55rem 0.7rem; border: 1px solid var(--border-subtle, #e2e8f0); border-radius: 10px; background: var(--surface-elevated, #fff); color: var(--text-strong, #0f172a); font-weight: 800; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .pedido-edit-code { justify-content: center; background: var(--surface-muted, #f8fafc); }
    .pedido-edit-weight { color: var(--text-muted, #64748b); }
    .pedido-edit-summary { display: grid; grid-template-columns: minmax(260px, 1fr) minmax(220px, 320px); gap: 16px; align-items: stretch; margin-top: 12px; }
    .pedido-edit-frete-card, .pedido-edit-summary .item-total { border: 1px solid #e2e8f0; border-radius: 16px; background: #fff; box-shadow: 0 14px 30px rgba(15, 23, 42, .06); }
    .pedido-edit-frete-card { padding: 16px; }
    .pedido-edit-frete-title { display: flex; align-items: center; gap: 10px; font-weight: 800; color: #0f172a; margin-bottom: 12px; }
    .pedido-edit-frete-title i { color: var(--accent, #dc2626); }
    .pedido-edit-frete-row { display: grid; grid-template-columns: auto minmax(160px, 1fr); gap: 14px; align-items: end; }
    .pedido-edit-frete-value label { display: block; font-weight: 700; color: #64748b; margin-bottom: 6px; }
    .pedido-edit-frete-value input { width: 100%; min-height: 46px; border: 1px solid #dbe3ee; border-radius: 14px; padding: 0 14px; font-weight: 800; color: #0f172a; background: #f8fafc; }
    .pedido-edit-frete-toggle { display: inline-flex; align-items: center; min-width: 86px; height: 42px; border-radius: 999px; padding: 4px; background: #e5e7eb; cursor: pointer; user-select: none; box-shadow: inset 0 1px 2px rgba(15, 23, 42, .08); }
    .pedido-edit-frete-toggle input { position: absolute; opacity: 0; pointer-events: none; }
    .pedido-edit-frete-track { position: relative; display: flex; align-items: center; width: 100%; height: 100%; border-radius: inherit; padding: 0 12px; color: #64748b; font-size: 13px; font-weight: 900; transition: background .2s ease, color .2s ease; }
    .pedido-edit-frete-track::after { content: ''; position: absolute; top: 4px; left: 4px; width: 26px; height: 26px; border-radius: 50%; background: #fff; box-shadow: 0 2px 8px rgba(15, 23, 42, .18); transition: transform .2s ease; }
    .pedido-edit-frete-track::before { content: 'Não'; padding-left: 30px; }
    .pedido-edit-frete-toggle input:checked + .pedido-edit-frete-track { background: var(--accent, #dc2626); color: #fff; }
    .pedido-edit-frete-toggle input:checked + .pedido-edit-frete-track::after { transform: translateX(42px); }
    .pedido-edit-frete-toggle input:checked + .pedido-edit-frete-track::before { content: 'Sim'; padding-left: 6px; }
    .pedido-edit-summary .item-total { margin-top: 0; padding: 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .pedido-edit-summary .item-total span { display: flex; align-items: center; gap: 8px; font-weight: 800; color: #64748b; }
    .pedido-edit-summary .item-total span::before { content: '$'; display: grid; place-items: center; width: 30px; height: 30px; border-radius: 10px; background: rgba(220, 38, 38, .1); color: var(--accent, #dc2626); font-weight: 900; }
    .pedido-edit-summary .item-total strong { font-size: 24px; color: #0f172a; }
    .pedido-edit-feedback { grid-column: 1 / -1; min-height: 20px; margin: 0; color: var(--text-muted, #64748b); font-weight: 600; }
    .pedido-edit-feedback[data-state="error"] { color: var(--danger-strong, #dc2626); }
    .pedido-edit-feedback[data-state="success"] { color: var(--success-strong, #047857); }
    .pedido-edit-actions { grid-column: 1 / -1; gap: 10px; }
    @media (max-width: 980px) { .pedido-edit-grid .item-row { grid-template-columns: repeat(2, minmax(180px, 1fr)); padding: 10px 0; border-bottom: 1px solid var(--border, #e5e7eb); } .pedido-edit-grid .item-row--head { display: none; } }
    @media (max-width: 760px) { .pedido-edit-summary, .pedido-edit-frete-row { grid-template-columns: 1fr; } }
    @media (max-width: 640px) { .pedido-edit-modal { padding: 12px; } .pedido-edit-dialog { padding: 16px; } .pedido-edit-grid .item-row { grid-template-columns: 1fr; } }
</style>

<script>
    (() => {
        const produtos = <?= json_encode($produtos ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
        const basePedidos = <?= json_encode($url('pedidos'), JSON_UNESCAPED_SLASHES); ?>.replace(/\/$/, '');
        const modal = document.getElementById('pedido-edit-modal');
        const form = document.getElementById('pedido-edit-form');
        const grid = document.getElementById('pedido-edit-grid');
        const addButton = document.getElementById('pedido-edit-add-item');
        const totalEl = document.getElementById('pedido-edit-total');
        const freightToggle = document.getElementById('pedido-edit-frete-ativo');
        const freightInput = document.getElementById('pedido-edit-frete-valor');
        const feedback = modal?.querySelector('.pedido-edit-feedback');
        const headRow = grid?.querySelector('.item-row--head');
        const pagamentosValidos = ['PIX','Dinheiro','Cartão de crédito','Cartão de débito','Boleto','TED','Transferência bancária','Depósito bancário','Cheque','Vale alimentação','Vale refeição','A prazo','Outros'];

        if (!modal || !form || !grid || !addButton || !totalEl || !freightToggle || !freightInput) return;

        const parseNumber = (value) => Number(String(value || '0').replace(/\./g, '').replace(',', '.')) || 0;
        const formatCurrency = (value) => 'R$ ' + Number(value || 0).toFixed(2).replace('.', ',');
        const formatWeight = (value) => {
            const numeric = Number(value || 0);
            if (!numeric) return '-';
            if (numeric < 1) return `${(numeric * 1000).toLocaleString('pt-BR', { maximumFractionDigits: 0 })} g`;
            return `${numeric.toLocaleString('pt-BR', { minimumFractionDigits: 3, maximumFractionDigits: 3 })} kg`;
        };
        const productById = (id) => produtos.find((produto) => String(produto.id ?? '') === String(id ?? '')) || null;
        const productPrice = (produto) => Number(produto?.preco ?? produto?.preco_venda ?? produto?.valor ?? 0);
        const productWeight = (produto) => Number(produto?.peso_liquido ?? produto?.peso ?? produto?.peso_bruto ?? 0);
        const productSku = (produto) => String(produto?.sku || '-');
        const selectedProduto = (row) => productById(row.querySelector('select[name="item_produto[]"]')?.value);
        const setFeedback = (message, state = '') => {
            if (!feedback) return;
            feedback.textContent = message || '';
            feedback.dataset.state = state;
        };
        const openModal = () => { modal.classList.remove('hidden'); modal.setAttribute('aria-hidden', 'false'); if (window.lucide) window.lucide.createIcons(); };
        const closeModal = () => { modal.classList.add('hidden'); modal.setAttribute('aria-hidden', 'true'); setFeedback(''); };
        const syncFreight = () => {
            const enabled = freightToggle.checked;
            freightInput.disabled = !enabled;
            freightInput.style.opacity = enabled ? '1' : '.55';
            if (!enabled) freightInput.value = '0.00';
        };
        const syncRowProductFields = (row, options = {}) => {
            const produto = selectedProduto(row);
            row.querySelector('.pedido-edit-code').textContent = productSku(produto);
            row.querySelector('.pedido-edit-weight').textContent = formatWeight(productWeight(produto));
            if (options.updatePrice) {
                row.querySelector('input[name="item_preco[]"]').value = productPrice(produto).toFixed(2);
            }
        };
        const computeTotal = () => {
            let total = 0;
            grid.querySelectorAll('.item-row[data-row]').forEach((row) => {
                syncRowProductFields(row);
                const qty = parseNumber(row.querySelector('input[name="item_quantidade[]"]').value);
                const price = parseNumber(row.querySelector('input[name="item_preco[]"]').value);
                const subtotal = qty * price;
                row.querySelector('.pedido-edit-subtotal').textContent = formatCurrency(subtotal);
                total += subtotal;
            });
            syncFreight();
            total += freightToggle.checked ? parseNumber(freightInput.value) : 0;
            totalEl.textContent = formatCurrency(total);
        };
        const productOptions = (selectedId = '') => produtos.map((produto) => {
            const id = String(produto.id ?? '');
            const selected = id === String(selectedId) ? 'selected' : '';
            const label = produto.nome ?? 'Produto';
            return `<option value="${id}" ${selected}>${label}</option>`;
        }).join('');
        const buildRow = (item = {}) => {
            const row = document.createElement('div');
            row.className = 'item-row';
            row.dataset.row = '1';
            const produto = productById(item.produto_id ?? '');
            row.innerHTML = `
                <span class="pedido-edit-code">${productSku(produto)}</span>
                <select name="item_produto[]" required>
                    <option value="">Produto...</option>
                    ${productOptions(item.produto_id ?? '')}
                </select>
                <input type="number" min="0" step="0.01" name="item_quantidade[]" value="${item.quantidade ?? 1}">
                <span class="pedido-edit-weight">${formatWeight(productWeight(produto))}</span>
                <input type="number" min="0" step="0.01" name="item_preco[]" value="${item.preco_unit ?? productPrice(produto)}">
                <span class="pedido-edit-subtotal">R$ 0,00</span>
                <button type="button" class="button button-ghost remove-item" aria-label="Remover">x</button>
            `;
            row.querySelector('select').addEventListener('change', () => {
                syncRowProductFields(row, { updatePrice: true });
                computeTotal();
            });
            row.querySelectorAll('input').forEach((input) => input.addEventListener('input', computeTotal));
            row.querySelector('.remove-item').addEventListener('click', () => { row.remove(); computeTotal(); });
            return row;
        };
        const resetRows = () => grid.querySelectorAll('.item-row[data-row]').forEach((row) => row.remove());
        const renderPedido = (payload) => {
            const pedido = payload.pedido || {};
            const itens = payload.itens || [];
            const freteValor = Number(pedido.frete_valor || 0);
            form.querySelector('input[name="pedido_id"]').value = pedido.id || '';
            form.querySelector('select[name="cliente_id"]').value = pedido.cliente_id || '';
            const pagamento = pagamentosValidos.includes(pedido.forma_pagto) ? pedido.forma_pagto : 'PIX';
            form.querySelector('select[name="forma_pagto"]').value = pagamento;
            form.querySelector('textarea[name="observacoes"]').value = pedido.observacoes || '';
            freightToggle.checked = freteValor > 0;
            freightInput.value = freteValor.toFixed(2);
            resetRows();
            itens.forEach((item) => grid.appendChild(buildRow(item)));
            if (!itens.length && headRow) grid.appendChild(buildRow());
            computeTotal();
        };
        const loadPedido = (pedidoId) => {
            setFeedback('Carregando pedido...');
            fetch(`${basePedidos}/${pedidoId}/editar`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then((response) => response.ok ? response.json() : response.json().then((body) => Promise.reject(body)))
                .then((payload) => { renderPedido(payload); setFeedback(''); openModal(); })
                .catch((error) => { setFeedback(error?.message || 'Não foi possível carregar o pedido.', 'error'); openModal(); });
        };

        document.addEventListener('click', (event) => {
            const button = event.target.closest('.pedido-edit-btn');
            if (!button) return;
            event.preventDefault();
            event.stopImmediatePropagation();
            loadPedido(button.dataset.pedido);
        }, true);

        modal.querySelectorAll('[data-edit-close]').forEach((button) => button.addEventListener('click', closeModal));
        addButton.addEventListener('click', () => { grid.appendChild(buildRow()); computeTotal(); });
        freightToggle.addEventListener('change', computeTotal);
        freightInput.addEventListener('input', computeTotal);

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const pedidoId = form.querySelector('input[name="pedido_id"]').value;
            if (!pedidoId) { setFeedback('Pedido inválido para edição.', 'error'); return; }
            syncFreight();
            setFeedback('Salvando alterações...');
            fetch(`${basePedidos}/${pedidoId}/update`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            })
                .then((response) => response.ok ? response.json() : response.json().then((body) => Promise.reject(body)))
                .then(() => {
                    setFeedback('Pedido atualizado com sucesso.', 'success');
                    window.appLiveSaved?.({ message: 'Pedido atualizado com sucesso.', source: 'pedidos' });
                    closeModal();
                })
                .catch((error) => setFeedback(error?.message || 'Não foi possível salvar o pedido.', 'error'));
        });
    })();
</script>
