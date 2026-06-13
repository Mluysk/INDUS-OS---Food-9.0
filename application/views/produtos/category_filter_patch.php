<?php
$selectedCategoriasRaw = $filters['categoria'] ?? [];
if (!is_array($selectedCategoriasRaw)) {
    $selectedCategoriasRaw = trim((string)$selectedCategoriasRaw) === '' ? [] : explode(',', (string)$selectedCategoriasRaw);
}
$selectedCategorias = array_values(array_unique(array_filter(array_map('trim', $selectedCategoriasRaw), static fn($item) => $item !== '')));
?>
<style>
    .category-multi-filter { position: relative; display: flex; flex-direction: column; gap: 6px; }
    .category-multi-filter select[name="categoria"] { display: none; }
    .category-multi-button {
        width: 100%;
        min-height: 46px;
        border: 1px solid var(--border-muted, #d8e0ec);
        border-radius: 14px;
        background: var(--surface, #fff);
        color: var(--text, #0f172a);
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
        outline: none;
    }
    .category-multi-button:hover,
    .category-multi-filter.is-open .category-multi-button {
        border-color: var(--primary, #dc2626);
        box-shadow: 0 10px 28px rgba(220, 38, 38, 0.10);
    }
    .category-multi-button::after { content: "v"; font-size: 0.78rem; color: var(--text-muted, #64748b); }
    .category-multi-filter.is-open .category-multi-button::after { content: "^"; }
    .category-multi-menu {
        position: absolute;
        z-index: 40;
        top: calc(100% + 8px);
        left: 0;
        width: min(360px, 92vw);
        background: var(--surface-elevated, #fff);
        border: 1px solid var(--border-muted, #d8e0ec);
        border-radius: 16px;
        padding: 8px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.14);
        display: none;
        overflow: hidden;
    }
    .category-multi-filter.is-open .category-multi-menu { display: grid; gap: 6px; }
    .category-multi-options {
        display: grid;
        gap: 4px;
        max-height: 218px;
        overflow-y: auto;
        padding-right: 3px;
    }
    .category-multi-options::-webkit-scrollbar { width: 8px; }
    .category-multi-options::-webkit-scrollbar-thumb { background: var(--border-muted, #cbd5e1); border-radius: 999px; }
    .category-multi-menu label.category-multi-option,
    .filter-grid .category-multi-menu label.category-multi-option {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        width: 100%;
        min-height: 42px;
        padding: 9px 12px;
        margin: 0;
        border-radius: 10px;
        font-size: 0.74rem;
        font-weight: 700;
        cursor: pointer;
        color: var(--text, #0f172a);
        background: transparent;
        text-align: left;
    }
    .category-multi-menu label.category-multi-option:hover { background: var(--surface-alt, #f4f7fb); }
    .category-multi-option span {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .category-multi-option input {
        flex: 0 0 auto;
        width: 18px;
        height: 18px;
        margin: 0;
        accent-color: var(--primary, #dc2626);
        order: 2;
    }
    .category-multi-actions {
        position: sticky;
        bottom: 0;
        display: flex;
        gap: 8px;
        padding-top: 10px;
        border-top: 1px solid var(--border-muted, #e5e7eb);
        background: var(--surface-elevated, #fff);
        box-shadow: 0 -10px 18px rgba(255, 255, 255, 0.9);
    }
    .category-multi-actions button { border: 0; border-radius: 10px; padding: 9px 12px; font-weight: 800; cursor: pointer; }
    .category-multi-apply { background: var(--primary, #dc2626); color: #fff; }
    .category-multi-clear { background: var(--surface-alt, #eef2f7); color: var(--text, #0f172a); }
</style>
<script>
(function () {
    const categorias = <?= json_encode(array_values($categorias ?? []), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    const selectedFromServer = <?= json_encode($selectedCategorias, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    const form = document.getElementById('catalog-filters');
    const table = document.getElementById('catalog-table');
    const tbody = table ? table.querySelector('tbody') : null;
    const resultMeta = document.querySelector('.catalog-meta');
    const printLinks = Array.from(document.querySelectorAll('.print-link'));
    const nativeSelect = form ? form.querySelector('select[name="categoria"]') : null;

    if (!form || !tbody || !nativeSelect || nativeSelect.dataset.multiReady === '1') return;
    nativeSelect.dataset.multiReady = '1';

    const selected = new Set(selectedFromServer.length ? selectedFromServer : (nativeSelect.value ? [nativeSelect.value] : []));
    const endpoint = form.dataset.endpoint || '';
    const printBase = form.dataset.print || '';
    const actionBase = endpoint.replace(/[?#].*$/, '').replace(/\/$/, '');
    const sortLinks = Array.from(document.querySelectorAll('.sort-link'));
    const state = {
        sort: sortLinks.find((link) => link.dataset.active)?.dataset.active || 'created_at',
        dir: sortLinks.find((link) => link.dataset.direction)?.dataset.direction || 'desc'
    };

    const shell = document.createElement('div');
    shell.className = 'category-multi-filter';
    nativeSelect.parentNode.insertBefore(shell, nativeSelect);
    shell.appendChild(nativeSelect);

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'category-multi-button';
    shell.appendChild(button);

    const menu = document.createElement('div');
    menu.className = 'category-multi-menu';
    shell.appendChild(menu);

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));
    const selectedList = () => Array.from(selected).filter(Boolean);
    const labelText = () => {
        const items = selectedList();
        if (items.length === 0) return 'Todas';
        if (items.length === 1) return items[0];
        return 'Seleção personalizada';
    };

    const syncButton = () => {
        button.textContent = labelText();
        nativeSelect.value = selectedList()[0] || '';
    };

    const buildQuery = (extra = {}) => {
        const params = new URLSearchParams();
        const q = form.querySelector('input[name="q"]')?.value.trim() || '';
        const tipo = form.querySelector('select[name="tipo"]')?.value || '';
        if (q) params.set('q', q);
        selectedList().forEach((categoria) => params.append('categoria[]', categoria));
        if (tipo) params.set('tipo', tipo);
        if (extra.sort) params.set('sort', extra.sort);
        if (extra.dir) params.set('dir', extra.dir);
        return params.toString();
    };

    const updatePrintLink = () => {
        if (!printLinks.length || !printBase) return;
        printLinks.forEach((printLink) => {
            const params = new URLSearchParams(buildQuery({}));
            if (printLink.dataset.printMode === 'simplificado') {
                params.set('modo', 'simplificado');
            }
            const qs = params.toString();
            printLink.href = printBase + (qs ? '?' + qs : '');
        });
    };

    const formatPrice = (value) => Number(value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const serializeProduct = (obj) => encodeURIComponent(JSON.stringify(obj));

    const renderRows = (items) => {
        if (!items || items.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9">${escapeHtml(table.dataset.empty || 'Nenhum produto encontrado para os filtros selecionados.')}</td></tr>`;
            return;
        }

        tbody.innerHTML = items.map((item) => {
            const badgeClass = item.tipo === 'PA' ? 'badge-success' : 'badge-warning';
            const active = Number(item.is_ativo ?? 1) === 1;
            const itemId = Number(item.id || 0);
            const actionPath = (suffix) => `${actionBase}/${itemId}/${suffix}`;
            window.productInfoMap = window.productInfoMap || {};
            if (!window.productInfoMap[itemId]) {
                window.productInfoMap[itemId] = {
                    id: itemId,
                    sku: item.sku || '',
                    nome: item.nome || '',
                    created_at: item.created_at || '',
                    updated_at: '',
                    prices: (window.historyPorProduto && window.historyPorProduto[itemId] ? window.historyPorProduto[itemId] : []).slice(0, 6),
                };
            }
            return `
                <tr>
                    <td>${itemId}</td>
                    <td>${escapeHtml(item.sku || '')}</td>
                    <td>${escapeHtml(item.nome || '')}<span class="product-status ${active ? 'is-active' : ''}">${active ? 'Ativo' : 'Inativo'}</span></td>
                    <td>${escapeHtml(item.categoria || '—')}</td>
                    <td><span class="badge ${badgeClass}">${escapeHtml(item.tipo || '')}</span></td>
                    <td>${escapeHtml(item.unidade || '')}</td>
                    <td>R$ ${formatPrice(item.preco)}</td>
                    <td>${Number(item.shelf_life_dias || 0)} dias</td>
                    <td>
                        <div class="product-actions">
                            <button type="button" class="product-icon-btn product-icon-btn--info js-product-info" data-product-id="${itemId}" data-tooltip="Informações" aria-label="Ver informações do produto"><span data-lucide="info" aria-hidden="true"></span></button>
                            <button type="button" class="product-icon-btn js-edit-product" data-product="${serializeProduct(item)}" data-tooltip="Editar" aria-label="Editar produto"><span data-lucide="pencil" aria-hidden="true"></span></button>
                            <form method="post" action="${escapeHtml(actionPath(active ? 'desativar' : 'ativar'))}" ${active ? `onsubmit="return confirm('Desativar este produto? Ele deixara de aparecer em novos pedidos e relatorios.');"` : ''}>
                                <button type="submit" class="product-status-switch ${active ? 'is-active' : ''}" title="${active ? 'Desativar produto' : 'Ativar produto'}" aria-label="${active ? 'Desativar produto' : 'Ativar produto'}">
                                    <span class="product-status-switch__icon"><span data-lucide="${active ? 'power' : 'power-off'}" aria-hidden="true"></span></span>
                                    <span>${active ? 'Ativo' : 'Inativo'}</span>
                                </button>
                            </form>
                            <form method="post" action="${escapeHtml(actionPath('excluir'))}" onsubmit="return confirm('Excluir este produto? Se houver historico, ele sera arquivado/desativado.');"><button type="submit" class="product-icon-btn product-icon-btn--danger" data-tooltip="Excluir" aria-label="Excluir produto"><span data-lucide="trash-2" aria-hidden="true"></span></button></form>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
        if (window.lucide) {
            window.lucide.createIcons({ elements: tbody.querySelectorAll('[data-lucide]') });
        }
    };

    const updateResultsLabel = (count) => {
        if (!resultMeta) return;
        resultMeta.textContent = `Resultados: ${count} ${count === 1 ? 'item' : 'itens'}`;
    };

    const refreshSortIndicators = () => {
        sortLinks.forEach((link) => {
            const col = link.dataset.sort;
            const indicator = link.querySelector('.sort-indicator');
            const isActive = state.sort === col;
            link.dataset.next = isActive && state.dir === 'asc' ? 'desc' : 'asc';
            link.dataset.active = state.sort;
            link.dataset.direction = state.dir;
            if (indicator) {
                indicator.textContent = isActive ? (state.dir === 'asc' ? '▲' : '▼') : '⇅';
                indicator.classList.toggle('active', isActive);
            }
        });
    };

    const fetchCatalog = () => {
        const qs = buildQuery({ sort: state.sort, dir: state.dir });
        const url = endpoint + (qs ? '?' + qs : '');
        form.classList.add('is-loading');
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then((res) => res.json())
            .then((payload) => {
                const items = payload.data || [];
                renderRows(items);
                updateResultsLabel(items.length);
                refreshSortIndicators();
                updatePrintLink();
            })
            .finally(() => form.classList.remove('is-loading'));
    };

    const renderMenu = () => {
        const options = categorias.map((categoria) => `
            <label class="category-multi-option">
                <span>${escapeHtml(categoria)}</span>
                <input type="checkbox" value="${escapeHtml(categoria)}" ${selected.has(categoria) ? 'checked' : ''}>
            </label>
        `).join('');

        menu.innerHTML = `
            <div class="category-multi-options">
                ${options || '<div class="category-multi-option">Nenhuma categoria cadastrada</div>'}
            </div>
            <div class="category-multi-actions">
                <button type="button" class="category-multi-apply">Aplicar</button>
                <button type="button" class="category-multi-clear">Limpar</button>
            </div>
        `;
    };

    button.addEventListener('click', () => shell.classList.toggle('is-open'));
    document.addEventListener('click', (event) => {
        if (!shell.contains(event.target)) shell.classList.remove('is-open');
    });

    menu.addEventListener('change', (event) => {
        const input = event.target.closest('input[type="checkbox"]');
        if (!input) return;
        if (input.checked) selected.add(input.value);
        else selected.delete(input.value);
        syncButton();
    });

    menu.addEventListener('click', (event) => {
        const apply = event.target.closest('.category-multi-apply');
        const clear = event.target.closest('.category-multi-clear');
        if (!apply && !clear) return;
        event.preventDefault();
        if (clear) {
            selected.clear();
            renderMenu();
            syncButton();
        }
        shell.classList.remove('is-open');
        fetchCatalog();
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        event.stopImmediatePropagation();
        fetchCatalog();
    }, true);

    sortLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopImmediatePropagation();
            state.sort = link.dataset.sort || 'created_at';
            state.dir = link.dataset.next || 'asc';
            fetchCatalog();
        }, true);
    });

    form.querySelector('select[name="tipo"]')?.addEventListener('change', updatePrintLink);
    form.querySelector('input[name="q"]')?.addEventListener('input', updatePrintLink);

    renderMenu();
    syncButton();
    updatePrintLink();
})();
</script>
