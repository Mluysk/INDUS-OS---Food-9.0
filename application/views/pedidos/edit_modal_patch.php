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
                        <p class="panel-description">Digite o produto para acrescentar itens; código SKU, peso, valor e subtotal acompanham automaticamente.</p>
                    </div>
                </div>
                <div class="pedido-edit-item-entry">
                    <label class="pedido-edit-field pedido-edit-code-field">
                        Código
                        <span class="pedido-edit-code-preview" id="pedido-edit-produto-codigo">-</span>
                    </label>
                    <label class="pedido-edit-field pedido-edit-produto-search-wrap">
                        Produto
                        <input type="text" id="pedido-edit-produto-search" autocomplete="off" placeholder="Produto..." aria-autocomplete="list" data-search-clear>
                        <ul class="pedido-edit-produto-suggestions" id="pedido-edit-produto-suggestions" hidden></ul>
                    </label>
                    <label class="pedido-edit-field">
                        Quantidade
                        <input type="number" min="0" step="0.01" id="pedido-edit-item-quantidade" value="1">
                    </label>
                    <label class="pedido-edit-field">
                        Peso
                        <span class="pedido-edit-code-preview" id="pedido-edit-produto-peso">-</span>
                    </label>
                    <label class="pedido-edit-field">
                        Valor
                        <input type="number" min="0" step="0.01" id="pedido-edit-item-valor" value="0">
                    </label>
                    <label class="pedido-edit-field">
                        Subtotal
                        <span class="pedido-edit-subtotal-preview" id="pedido-edit-item-subtotal">R$ 0,00</span>
                    </label>
                    <button type="button" class="button pedido-edit-add-item" id="pedido-edit-add-search-item">Adicionar item</button>
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
    .pedido-edit-item-entry { display: grid; grid-template-columns: minmax(112px, .55fr) minmax(260px, 1.55fr) minmax(110px, .55fr) minmax(110px, .55fr) minmax(120px, .62fr) minmax(120px, .62fr) auto; gap: 8px; align-items: end; }
    .pedido-edit-field { display: grid; gap: 6px; min-width: 0; color: var(--text-muted, #64748b); font-weight: 800; }
    .pedido-edit-field input { width: 100%; min-height: 46px; border: 1px solid var(--border-subtle, #e2e8f0); border-radius: 12px; padding: 0 .85rem; background: var(--surface-elevated, #fff); color: var(--text-strong, #0f172a); font: inherit; font-weight: 700; transition: border-color .15s ease, box-shadow .15s ease; }
    .pedido-edit-field input:focus { outline: 0; border-color: var(--primary, var(--accent, #dc2626)); box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 220, 38, 38), .14); }
    .pedido-edit-produto-search-wrap { position: relative; }
    .pedido-edit-produto-search-wrap input.has-search-clear-button { padding-right: 42px !important; }
    .pedido-edit-produto-search-wrap .search-clear-button { top: auto; bottom: 11px; right: 12px; width: 24px; height: 24px; min-width: 24px; min-height: 24px; transform: none; display: grid; place-items: center; line-height: 1; }
    .pedido-edit-produto-search-wrap .search-clear-button:hover, .pedido-edit-produto-search-wrap .search-clear-button:focus-visible { transform: none; }
    .pedido-edit-produto-search-wrap .search-clear-button svg { display: block; width: 12px; height: 12px; margin: 0; }
    .pedido-edit-produto-suggestions { position: absolute; z-index: 20; top: calc(100% + 6px); left: 0; right: 0; max-height: 260px; overflow-y: auto; margin: 0; padding: 6px; list-style: none; border: 1px solid var(--border, #e5e7eb); border-radius: 12px; background: var(--surface-elevated, #fff); box-shadow: 0 16px 36px rgba(15, 23, 42, .14); }
    .pedido-edit-produto-suggestions[hidden] { display: none; }
    .pedido-edit-produto-suggestions button { width: 100%; border: 0; border-radius: 10px; padding: 9px 10px; background: transparent; color: var(--text, #0f172a); text-align: left; font-weight: 800; cursor: pointer; }
    .pedido-edit-produto-suggestions button:hover, .pedido-edit-produto-suggestions button:focus { background: var(--surface-muted, #f1f5f9); }
    .pedido-edit-suggestion-meta { display: block; margin-top: 3px; color: var(--text-muted, #64748b); font-size: .78rem; font-weight: 700; }
    .pedido-edit-code-preview, .pedido-edit-subtotal-preview { display: flex; align-items: center; min-height: 46px; padding: 0 .85rem; border: 1px solid var(--border-subtle, #e2e8f0); border-radius: 12px; background: var(--surface-elevated, #fff); color: var(--text-strong, #0f172a); font-weight: 850; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .pedido-edit-code-preview { justify-content: center; background: var(--surface-muted, #f8fafc); }
    .pedido-edit-add-item { min-height: 46px; white-space: nowrap; }
    .pedido-edit-grid { display: grid; gap: 8px; }
    .pedido-edit-grid .item-row { display: grid; grid-template-columns: minmax(104px, 0.5fr) minmax(250px, 1.55fr) minmax(110px, 0.55fr) minmax(110px, 0.55fr) minmax(120px, 0.62fr) minmax(120px, 0.62fr) 44px; gap: 8px; align-items: center; }
    .pedido-edit-grid .item-row--head { color: var(--text-muted, #64748b); font-size: 0.78rem; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; }
    .pedido-edit-grid select, .pedido-edit-grid input { width: 100%; }
    .pedido-edit-grid .remove-item { width: 32px; height: 32px; min-width: 32px; min-height: 32px; padding: 0; justify-self: center; display: inline-grid; place-items: center; border: 0; border-radius: 8px; background: transparent; color: var(--danger, #ef1f2d); box-shadow: none; transition: background .16s ease, color .16s ease, transform .16s ease; }
    .pedido-trash-icon { position: relative; display: block; width: 16px; height: 16px; color: currentColor; }
    .pedido-trash-icon::before { content: ''; position: absolute; left: 4px; top: 6px; width: 8px; height: 8px; border: 2px solid currentColor; border-top: 0; border-radius: 0 0 1.8px 1.8px; background: linear-gradient(90deg, transparent 0 30%, currentColor 30% 40%, transparent 40% 60%, currentColor 60% 70%, transparent 70% 100%) center 1px / 100% 6px no-repeat; }
    .pedido-trash-icon::after { content: ''; position: absolute; left: 2px; top: 4px; width: 12px; height: 2px; border-radius: 999px; background: currentColor; transition: transform .18s ease; }
    .pedido-trash-icon__handle { position: absolute; left: 5.5px; top: 1px; width: 5px; height: 4px; border: 2px solid currentColor; border-bottom: 0; border-radius: 3px 3px 0 0; transition: transform .18s ease; }
    .remove-item:hover, .remove-item:focus-visible { background: color-mix(in srgb, var(--danger, #ef1f2d) 10%, #fff); color: var(--danger, #ef1f2d); transform: none; outline: 0; }
    .remove-item:hover .pedido-trash-icon::after, .remove-item:focus-visible .pedido-trash-icon::after, .remove-item:hover .pedido-trash-icon__handle, .remove-item:focus-visible .pedido-trash-icon__handle { transform: rotate(-18deg) translate(-1px, -3px); transform-origin: 13px 5px; }
    .pedido-edit-code, .pedido-edit-product-name, .pedido-edit-weight, .pedido-edit-subtotal { display: flex; align-items: center; min-height: 42px; padding: 0.55rem 0.7rem; border: 1px solid var(--border-subtle, #e2e8f0); border-radius: 10px; background: var(--surface-elevated, #fff); color: var(--text-strong, #0f172a); font-weight: 800; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .pedido-edit-product-name { overflow: hidden; text-overflow: ellipsis; }
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
    @media (max-width: 980px) { .pedido-edit-item-entry, .pedido-edit-grid .item-row { grid-template-columns: repeat(2, minmax(180px, 1fr)); padding: 10px 0; border-bottom: 1px solid var(--border, #e5e7eb); } .pedido-edit-grid .item-row--head { display: none; } }
    @media (max-width: 760px) { .pedido-edit-summary, .pedido-edit-frete-row { grid-template-columns: 1fr; } }
    @media (max-width: 640px) { .pedido-edit-modal { padding: 12px; } .pedido-edit-dialog { padding: 16px; } .pedido-edit-item-entry, .pedido-edit-grid .item-row { grid-template-columns: 1fr; } }
</style>

<script>
    (() => {
        const produtos = <?= json_encode($produtos ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
        const basePedidos = <?= json_encode($url('pedidos'), JSON_UNESCAPED_SLASHES); ?>.replace(/\/$/, '');
        const modal = document.getElementById('pedido-edit-modal');
        const form = document.getElementById('pedido-edit-form');
        const grid = document.getElementById('pedido-edit-grid');
        const search = document.getElementById('pedido-edit-produto-search');
        const suggestions = document.getElementById('pedido-edit-produto-suggestions');
        const codePreview = document.getElementById('pedido-edit-produto-codigo');
        const weightPreview = document.getElementById('pedido-edit-produto-peso');
        const qtyEntry = document.getElementById('pedido-edit-item-quantidade');
        const priceEntry = document.getElementById('pedido-edit-item-valor');
        const subtotalPreview = document.getElementById('pedido-edit-item-subtotal');
        const addButton = document.getElementById('pedido-edit-add-search-item');
        const totalEl = document.getElementById('pedido-edit-total');
        const freightToggle = document.getElementById('pedido-edit-frete-ativo');
        const freightInput = document.getElementById('pedido-edit-frete-valor');
        const feedback = modal?.querySelector('.pedido-edit-feedback');
        const headRow = grid?.querySelector('.item-row--head');
        const pagamentosValidos = ['PIX','Dinheiro','Cartão de crédito','Cartão de débito','Boleto','TED','Transferência bancária','Depósito bancário','Cheque','Vale alimentação','Vale refeição','A prazo','Outros'];

        if (!modal || !form || !grid || !search || !suggestions || !codePreview || !weightPreview || !qtyEntry || !priceEntry || !subtotalPreview || !addButton || !totalEl || !freightToggle || !freightInput) return;

        const parseNumber = (value) => {
            const raw = String(value ?? '').trim();
            if (!raw) return 0;
            const cleaned = raw.replace(/[^\d,.-]/g, '');
            const hasComma = cleaned.includes(',');
            const hasDot = cleaned.includes('.');
            let normalized = cleaned;
            if (hasComma && hasDot) {
                normalized = cleaned.lastIndexOf(',') > cleaned.lastIndexOf('.')
                    ? cleaned.replace(/\./g, '').replace(',', '.')
                    : cleaned.replace(/,/g, '');
            } else if (hasComma) {
                normalized = cleaned.replace(',', '.');
            }
            return Number(normalized) || 0;
        };
        const formatDecimalValue = (value) => parseNumber(value).toFixed(2);
        const formatCurrency = (value) => 'R$ ' + Number(value || 0).toFixed(2).replace('.', ',');
        const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        const escapeHtml = (value = '') => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
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
        const productLabel = (produto) => String(produto?.nome || 'Produto');
        const selectedProduto = (row) => productById(row.querySelector('input[name="item_produto[]"]')?.value);
        let selectedProduct = null;
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
            const name = row.querySelector('.pedido-edit-product-name');
            if (name) {
                name.textContent = productLabel(produto);
                name.title = productLabel(produto);
            }
            if (options.updatePrice) {
                row.querySelector('input[name="item_preco[]"]').value = formatDecimalValue(productPrice(produto));
            }
        };
        const updateEntrySubtotal = () => {
            subtotalPreview.textContent = formatCurrency(parseNumber(qtyEntry.value) * parseNumber(priceEntry.value));
        };
        const clearEntryProduct = () => {
            selectedProduct = null;
            codePreview.textContent = '-';
            weightPreview.textContent = '-';
            updateEntrySubtotal();
        };
        const closeSuggestions = () => {
            suggestions.hidden = true;
        };
        const chooseProduct = (produto) => {
            selectedProduct = produto;
            search.value = productLabel(produto);
            codePreview.textContent = productSku(produto);
            weightPreview.textContent = formatWeight(productWeight(produto));
            priceEntry.value = formatDecimalValue(productPrice(produto));
            updateEntrySubtotal();
            closeSuggestions();
        };
        const renderSuggestions = () => {
            const term = normalize(search.value);
            const matches = produtos.filter((produto) => normalize(`${produto.nome || ''} ${produto.sku || ''}`).includes(term));
            suggestions.innerHTML = '';
            if (!matches.length) {
                suggestions.innerHTML = '<li><button type="button" disabled>Nenhum produto encontrado</button></li>';
                suggestions.hidden = false;
                selectedProduct = null;
                codePreview.textContent = '-';
                weightPreview.textContent = '-';
                return;
            }
            matches.forEach((produto) => {
                const item = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.innerHTML = `${escapeHtml(productLabel(produto))}<span class="pedido-edit-suggestion-meta">${escapeHtml(productSku(produto))} - ${escapeHtml(formatWeight(productWeight(produto)))} - ${escapeHtml(formatCurrency(productPrice(produto)))}</span>`;
                button.addEventListener('mousedown', (event) => event.preventDefault());
                button.addEventListener('click', () => chooseProduct(produto));
                item.appendChild(button);
                suggestions.appendChild(item);
            });
            suggestions.hidden = false;
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
        const buildRow = (item = {}) => {
            const row = document.createElement('div');
            row.className = 'item-row';
            row.dataset.row = '1';
            const produto = productById(item.produto_id ?? '');
            const produtoNome = productLabel(produto);
            row.innerHTML = `
                <span class="pedido-edit-code">${escapeHtml(productSku(produto))}</span>
                <span class="pedido-edit-product-name" title="${escapeHtml(produtoNome)}">${escapeHtml(produtoNome)}</span>
                <input type="number" min="0" step="0.01" name="item_quantidade[]" value="${item.quantidade ?? 1}">
                <span class="pedido-edit-weight">${escapeHtml(formatWeight(productWeight(produto)))}</span>
                <input type="number" min="0" step="0.01" name="item_preco[]" value="${formatDecimalValue(item.preco_unit ?? productPrice(produto))}">
                <span class="pedido-edit-subtotal">R$ 0,00</span>
                <button type="button" class="button button-ghost remove-item" aria-label="Remover produto"><span class="pedido-trash-icon" aria-hidden="true"><span class="pedido-trash-icon__handle"></span></span></button>
                <input type="hidden" name="item_produto[]" value="${escapeHtml(item.produto_id ?? '')}" required>
            `;
            row.querySelectorAll('input').forEach((input) => input.addEventListener('input', computeTotal));
            row.querySelector('.remove-item').addEventListener('click', () => { row.remove(); computeTotal(); });
            return row;
        };
        const resetEntry = () => {
            selectedProduct = null;
            search.value = '';
            qtyEntry.value = '1';
            priceEntry.value = '0';
            codePreview.textContent = '-';
            weightPreview.textContent = '-';
            updateEntrySubtotal();
            closeSuggestions();
        };
        const addSearchItem = () => {
            if (!selectedProduct) {
                search.focus();
                renderSuggestions();
                return;
            }
            const quantity = parseNumber(qtyEntry.value);
            if (quantity <= 0) {
                qtyEntry.focus();
                return;
            }
            grid.appendChild(buildRow({
                produto_id: selectedProduct.id,
                quantidade: quantity,
                preco_unit: formatDecimalValue(priceEntry.value),
            }));
            resetEntry();
            computeTotal();
        };
        const resetRows = () => grid.querySelectorAll('.item-row[data-row]').forEach((row) => row.remove());
        const renderPedido = (payload) => {
            const pedido = payload.pedido || {};
            const itens = payload.itens || [];
            const freteValor = Number(pedido.frete_valor || 0);
            form.querySelector('input[name="pedido_id"]').value = pedido.id || '';
            form.querySelector('select[name="cliente_id"]').value = pedido.cliente_id || '';
            const pagamento = pagamentosValidos.includes(pedido.forma_pagto) ? pedido.forma_pagto : 'PIX';
            const pagamentoSelect = form.querySelector('select[name="forma_pagto"]');
            pagamentoSelect.value = pagamento;
            pagamentoSelect.dispatchEvent(new Event('change', { bubbles: true }));
            form.querySelector('textarea[name="observacoes"]').value = pedido.observacoes || '';
            freightToggle.checked = freteValor > 0;
            freightInput.value = freteValor.toFixed(2);
            resetRows();
            itens.forEach((item) => grid.appendChild(buildRow(item)));
            resetEntry();
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
        search.addEventListener('input', () => {
            selectedProduct = null;
            codePreview.textContent = '-';
            weightPreview.textContent = '-';
            if (!search.value.trim()) {
                priceEntry.value = '0';
            }
            updateEntrySubtotal();
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
            if (!modal.contains(event.target)) {
                closeSuggestions();
            }
        });
        qtyEntry.addEventListener('input', updateEntrySubtotal);
        priceEntry.addEventListener('input', updateEntrySubtotal);
        addButton.addEventListener('click', addSearchItem);
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
