<style>
    #pedido-form .pedido-cliente-search-field {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        min-width: 0;
    }

    #pedido-form .pedido-cliente-search-field select[name="cliente_id"] {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        pointer-events: none;
    }

    #pedido-form .cliente-search-wrap {
        position: relative;
        display: block;
        width: 100%;
    }

    #pedido-form #pedido-cliente-search {
        width: 100%;
        min-height: 46px;
        font-weight: 700;
    }

    #pedido-form .cliente-suggestions {
        position: absolute;
        z-index: 35;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        max-height: 320px;
        overflow: auto;
        margin: 0;
        padding: 0.35rem;
        list-style: none;
        border: 1px solid var(--border-subtle, #e2e8f0);
        border-radius: 12px;
        background: var(--surface-elevated, #fff);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.14);
    }

    #pedido-form .cliente-suggestions[hidden] {
        display: none !important;
    }

    #pedido-form .cliente-suggestions button {
        width: 100%;
        border: 0;
        border-radius: 9px;
        padding: 0.62rem 0.75rem;
        background: transparent;
        text-align: left;
        cursor: pointer;
        color: var(--text-strong, #0f172a);
        font-weight: 800;
    }

    #pedido-form .cliente-suggestions button:hover,
    #pedido-form .cliente-suggestions button:focus {
        outline: none;
        background: var(--surface-muted, #f1f5f9);
    }

    #pedido-form .cliente-suggestion-meta {
        display: block;
        margin-top: 0.15rem;
        color: var(--text-muted, #64748b);
        font-size: 0.78rem;
        font-weight: 650;
    }

    #pedido-form .pedido-cliente-error {
        display: none;
        color: var(--danger, #dc2626);
        font-size: 0.78rem;
        font-weight: 750;
    }

    #pedido-form .pedido-cliente-search-field.is-invalid .pedido-cliente-error {
        display: block;
    }

    #pedido-form .pedido-cliente-search-field.is-invalid #pedido-cliente-search {
        border-color: var(--danger, #dc2626);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
</style>

<script>
(function () {
    const clientes = <?= json_encode($clientes ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();

    const cleanDoc = (value) => String(value || '').replace(/\D/g, '');

    const clientName = (cliente) => {
        return cliente.nome_fantasia || cliente.razao_social || cliente.nome || `Cliente ${cliente.id || ''}`.trim();
    };

    const clientMeta = (cliente) => {
        const parts = [];
        const doc = cliente.cnpj || cliente.cpf || cliente.documento || '';
        if (doc) parts.push(doc);
        if (cliente.email) parts.push(cliente.email);
        if (cliente.cidade || cliente.estado) parts.push([cliente.cidade, cliente.estado].filter(Boolean).join(' / '));
        if (cliente.telefone || cliente.whatsapp) parts.push(cliente.whatsapp || cliente.telefone);
        return parts.filter(Boolean).join(' - ');
    };

    const run = () => {
        const form = document.getElementById('pedido-form');
        const select = form ? form.querySelector('select[name="cliente_id"]') : null;
        const label = select ? select.closest('label') : null;
        if (!form || !select || !label || label.dataset.clientSearchReady === '1') return;

        label.dataset.clientSearchReady = '1';
        label.classList.add('pedido-cliente-search-field');
        select.required = false;

        const placeholder = select.querySelector('option[value=""]')?.textContent?.trim() || 'Selecione...';
        const wrap = document.createElement('span');
        wrap.className = 'cliente-search-wrap';
        wrap.innerHTML = `
            <input type="text" id="pedido-cliente-search" autocomplete="off" placeholder="${placeholder}" aria-autocomplete="list" required>
            <ul class="cliente-suggestions" id="pedido-cliente-suggestions" hidden></ul>
            <span class="pedido-cliente-error">Selecione um cliente cadastrado.</span>
        `;
        select.insertAdjacentElement('afterend', wrap);

        const input = wrap.querySelector('#pedido-cliente-search');
        const suggestions = wrap.querySelector('#pedido-cliente-suggestions');

        const options = clientes
            .map((cliente) => ({
                id: String(cliente.id || ''),
                label: clientName(cliente),
                meta: clientMeta(cliente),
                search: normalize([
                    cliente.id,
                    cliente.nome_fantasia,
                    cliente.razao_social,
                    cliente.nome,
                    cliente.email,
                    cliente.cidade,
                    cliente.estado,
                    cliente.telefone,
                    cliente.whatsapp,
                    cliente.cnpj,
                    cliente.cpf,
                    cliente.documento,
                    cleanDoc(cliente.cnpj),
                    cleanDoc(cliente.cpf),
                    cleanDoc(cliente.documento),
                    clientName(cliente),
                    clientMeta(cliente)
                ].filter(Boolean).join(' ')),
            }))
            .filter((cliente) => cliente.id)
            .sort((a, b) => Number(b.id || 0) - Number(a.id || 0));

        const clearSelection = () => {
            select.value = '';
            label.classList.remove('is-invalid');
        };

        const closeSuggestions = () => {
            suggestions.hidden = true;
        };

        const chooseCliente = (cliente) => {
            select.value = cliente.id;
            input.value = cliente.label;
            input.dataset.selectedCliente = cliente.id;
            label.classList.remove('is-invalid');
            select.dispatchEvent(new Event('change', { bubbles: true }));
            closeSuggestions();
        };

        const syncFromSelect = () => {
            const selected = options.find((cliente) => cliente.id === String(select.value || ''));
            if (selected && input.value !== selected.label) {
                input.value = selected.label;
                input.dataset.selectedCliente = selected.id;
            }
        };

        const renderSuggestions = () => {
            if (document.activeElement !== input) {
                closeSuggestions();
                return;
            }

            const typed = input.value;
            const term = normalize(`${typed} ${cleanDoc(typed)}`);
            const matches = options
                .filter((cliente) => !term || cliente.search.includes(term))
                .slice(0, term ? 40 : 20);

            suggestions.innerHTML = '';
            if (!matches.length) {
                suggestions.innerHTML = '<li><button type="button" disabled>Nenhum cliente encontrado</button></li>';
                suggestions.hidden = false;
                return;
            }

            matches.forEach((cliente) => {
                const item = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.innerHTML = `${cliente.label}<span class="cliente-suggestion-meta">${cliente.meta || 'Cliente cadastrado'}</span>`;
                button.addEventListener('mousedown', (event) => event.preventDefault());
                button.addEventListener('click', () => chooseCliente(cliente));
                item.appendChild(button);
                suggestions.appendChild(item);
            });
            suggestions.hidden = false;
        };

        input.addEventListener('input', () => {
            if (input.dataset.selectedCliente && input.value !== options.find((cliente) => cliente.id === input.dataset.selectedCliente)?.label) {
                delete input.dataset.selectedCliente;
            }
            clearSelection();
            renderSuggestions();
        });

        input.addEventListener('focus', renderSuggestions);
        input.addEventListener('blur', () => window.setTimeout(closeSuggestions, 130));
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                const first = suggestions.querySelector('button:not([disabled])');
                if (first) {
                    event.preventDefault();
                    first.click();
                }
            }
            if (event.key === 'Escape') {
                closeSuggestions();
                input.blur();
            }
        });

        select.addEventListener('change', syncFromSelect);
        document.addEventListener('click', (event) => {
            if (!label.contains(event.target)) closeSuggestions();
        });

        form.addEventListener('submit', (event) => {
            if (select.value) return;
            event.preventDefault();
            label.classList.add('is-invalid');
            input.focus();
            renderSuggestions();
        }, true);

        syncFromSelect();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();
</script>
