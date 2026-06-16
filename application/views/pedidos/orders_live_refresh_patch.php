<script>
(function () {
    const basePedidos = <?= json_encode($url('pedidos'), JSON_UNESCAPED_SLASHES); ?>.replace(/\/$/, '');
    const defaultPixEnabled = <?= $notePixEnabled ? 'true' : 'false'; ?>;
    const defaultPixKey = <?= json_encode($notePixKey ?? '', JSON_UNESCAPED_SLASHES); ?>;

    const normalize = (value) => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLocaleLowerCase('pt-BR');

    const currentRows = () => Array.from(document.querySelectorAll('.pedido-list-table tbody tr[data-pedido-row]'));

    const applyPedidoSearch = () => {
        const input = document.querySelector('[data-pedido-list-search]');
        const noResults = document.querySelector('[data-pedido-no-results]');
        const rows = currentRows();
        const term = normalize(input?.value || '');
        let visible = 0;

        rows.forEach((row) => {
            const match = !term || normalize(row.dataset.pedidoSearch || '').includes(term);
            row.hidden = !match;
            if (match) {
                visible += 1;
            }
        });

        if (noResults) {
            noResults.hidden = visible > 0 || rows.length === 0;
        }
    };

    const getRowId = (row) => Number(row?.getAttribute('data-sort-id') || row?.children?.[0]?.textContent || 0);

    const addDeleteButton = (row) => {
        const group = row.querySelector('.pedido-action-buttons');
        const edit = row.querySelector('.pedido-edit-btn');
        if (!group || !edit || group.querySelector('.pedido-delete-link')) {
            return;
        }

        const pedidoId = getRowId(row);
        if (!pedidoId) {
            return;
        }

        const href = new URL(window.location.href);
        href.searchParams.set('delete_pedido', String(pedidoId));

        const link = document.createElement('a');
        link.href = href.toString();
        link.className = 'button pedido-icon-btn pedido-icon-btn--danger pedido-delete-link';
        link.dataset.tooltip = 'Excluir';
        link.setAttribute('aria-label', 'Excluir');
        link.setAttribute('title', 'Excluir');
        link.innerHTML = '<span data-lucide="trash-2" aria-hidden="true"></span>';
        link.addEventListener('click', (event) => {
            if (!window.confirm('Tem certeza que deseja excluir este pedido?')) {
                event.preventDefault();
            }
        });
        group.appendChild(link);
    };

    const enhancePedidoRow = (row) => {
        addDeleteButton(row);
        if (window.lucide) {
            window.lucide.createIcons({ elements: row.querySelectorAll('[data-lucide]') });
        }
    };

    const refreshPedidoList = async () => {
        const tableBody = document.querySelector('.pedido-list-table tbody');
        if (!tableBody) {
            return;
        }

        const response = await fetch(window.location.pathname + window.location.search, {
            credentials: 'same-origin',
            headers: { 'Accept': 'text/html' },
        });
        if (!response.ok) {
            return;
        }

        const html = await response.text();
        const parsed = new DOMParser().parseFromString(html, 'text/html');
        const freshBody = parsed.querySelector('.pedido-list-table tbody');
        if (!freshBody) {
            return;
        }

        const knownIds = new Set(currentRows().map((row) => String(getRowId(row))).filter((id) => id !== '0'));
        const freshRows = Array.from(freshBody.querySelectorAll('tr[data-pedido-row]'));
        const firstCurrentRow = tableBody.querySelector('tr[data-pedido-row], tr[data-pedido-empty], tr[data-pedido-no-results]');
        let inserted = 0;

        freshRows.forEach((freshRow) => {
            const id = String(getRowId(freshRow));
            if (!id || id === '0' || knownIds.has(id)) {
                return;
            }
            const imported = document.importNode(freshRow, true);
            enhancePedidoRow(imported);
            tableBody.insertBefore(imported, firstCurrentRow);
            knownIds.add(id);
            inserted += 1;
        });

        if (inserted > 0) {
            tableBody.querySelector('[data-pedido-empty]')?.remove();
        }
        applyPedidoSearch();
    };

    const markPedidoApproved = (form) => {
        const row = form.closest('tr[data-pedido-row]');
        if (!row) {
            return;
        }

        const badge = row.querySelector('.badge');
        if (badge) {
            badge.classList.remove('badge-danger', 'badge-warning', 'badge-success');
            badge.classList.add('badge-warning');
            badge.textContent = 'producao';
        }

        form.remove();
        row.dataset.pedidoSearch = `${row.dataset.pedidoSearch || ''} producao`;
        applyPedidoSearch();
    };

    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const action = form.action || '';
        if (!/\/pedidos\/\d+\/aprovar(?:\/|$|\?)/i.test(action)) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        if (!window.confirm('Deseja aprovar este pedido?')) {
            return;
        }
        if (form.dataset.approving === '1') {
            return;
        }

        form.dataset.approving = '1';
        const buttons = Array.from(form.querySelectorAll('button, input[type="submit"]'));
        buttons.forEach((button) => {
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
        });

        try {
            const response = await fetch(action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const payload = await response.json().catch(() => ({}));
            if (!response.ok || payload.success === false || payload.error) {
                throw new Error(payload.message || payload.error || 'Nao foi possivel aprovar o pedido.');
            }

            markPedidoApproved(form);
            window.appLiveSaved?.({
                message: payload.message || 'Pedido aprovado com sucesso.',
                source: 'pedidos',
                form,
                action,
                method: 'POST',
                payload,
            });
        } catch (error) {
            window.showAppToast?.(error?.message || 'Nao foi possivel aprovar o pedido.', 'error');
        } finally {
            buttons.forEach((button) => {
                button.disabled = false;
                button.removeAttribute('aria-busy');
            });
            delete form.dataset.approving;
        }
    }, true);

    document.addEventListener('click', (event) => {
        const button = event.target instanceof Element ? event.target.closest('.nota-print-btn') : null;
        if (!button) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        const row = button.closest('tr');
        const checkbox = row?.querySelector('.pix-checkbox');
        const keyInput = row?.querySelector('.pix-key');
        const pedidoId = button.dataset.pedido;
        if (!pedidoId) {
            return;
        }

        const params = new URLSearchParams();
        const enabled = checkbox ? checkbox.checked : defaultPixEnabled;
        const key = keyInput ? keyInput.value.trim() : defaultPixKey;
        params.set('pix', enabled ? '1' : '0');
        if (enabled && key) {
            params.set('pix_key', key);
        }

        window.open(`${basePedidos}/${pedidoId}/nota?${params.toString()}`, '_blank', 'noopener');
    }, true);

    window.addEventListener('app:live-saved', (event) => {
        const detail = event.detail || {};
        const form = detail.form;
        if (!(form instanceof HTMLFormElement) || form.id !== 'pedido-form') {
            return;
        }

        refreshPedidoList();
    });

    document.querySelector('[data-pedido-list-search]')?.addEventListener('input', applyPedidoSearch);
})();
</script>
