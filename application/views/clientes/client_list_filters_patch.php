<script>
(function () {
    const initClientesListFilters = () => {
        const tbody = document.querySelector('[data-clientes-tbody]');
        const paginationTarget = document.querySelector('[data-list-pagination="clientes"]');
        const searchInput = document.querySelector('[data-clientes-search]');
        const citySelect = document.querySelector('[data-clientes-city-filter]');
        const ufSelect = document.querySelector('[data-clientes-uf-filter]');
        const sortButtons = Array.from(document.querySelectorAll('[data-clientes-sort-field]'));

        if (!tbody || !paginationTarget || tbody.dataset.clientesFilterPatch === '1') {
            return;
        }

        tbody.dataset.clientesFilterPatch = '1';

        const pageSizeOptions = <?= json_encode($listPageSizeOptions ?? [15, 20, 30, 40, 50, 100], JSON_UNESCAPED_UNICODE); ?>;
        const state = {
            page: 1,
            perPage: Number(<?= json_encode((int) ($listPageSizeDefault ?? 15)); ?>) || 15,
            sortField: '',
            sortDirection: ''
        };

        const columnIndex = {
            id: 0,
            nome: 1,
            cnpj: 2,
            cpf: 3,
            contato: 4,
            cidade: 5
        };

        const normalize = (value) => String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLocaleLowerCase('pt-BR')
            .trim();

        const getRows = () => Array.from(tbody.querySelectorAll('tr[data-cliente-row]'));

        const uppercaseUfValues = () => {
            ufSelect?.querySelectorAll('option').forEach((option) => {
                if (option.value !== '') {
                    option.value = option.value.trim().toUpperCase();
                    option.textContent = option.textContent.trim().toUpperCase();
                }
            });

            getRows().forEach((row) => {
                row.dataset.clienteUf = String(row.dataset.clienteUf || '').trim().toUpperCase();
                row.querySelectorAll('.cliente-uf').forEach((element) => {
                    element.textContent = String(element.textContent || '').trim().toUpperCase();
                });
            });

            document.querySelectorAll('input[name="estado"], [data-field="estado"]').forEach((element) => {
                if (element instanceof HTMLInputElement) {
                    element.value = element.value.trim().toUpperCase();
                    return;
                }
                element.textContent = String(element.textContent || '').trim().toUpperCase();
            });
        };

        const getCellText = (row, field) => {
            const index = columnIndex[field];
            return index === undefined ? '' : (row.children[index]?.textContent || '');
        };

        const getSortValue = (row, field, type) => {
            const datasetKey = `sort${field.charAt(0).toUpperCase()}${field.slice(1)}`;
            const rawValue = row.dataset[datasetKey] || getCellText(row, field);

            if (type === 'number') {
                const number = Number(String(rawValue).replace(/[^\d,-]/g, '').replace(',', '.'));
                return Number.isFinite(number) ? number : 0;
            }

            return normalize(rawValue);
        };

        const updateSortButtons = () => {
            sortButtons.forEach((button) => {
                const indicator = button.querySelector('span');
                const active = button.dataset.clientesSortField === state.sortField && state.sortDirection;

                button.classList.toggle('active', Boolean(active));
                button.setAttribute('aria-sort', active ? (state.sortDirection === 'asc' ? 'ascending' : 'descending') : 'none');

                if (indicator) {
                    indicator.textContent = active ? (state.sortDirection === 'asc' ? '\u25B2' : '\u25BC') : '\u21C5';
                    indicator.classList.toggle('active', Boolean(active));
                }
            });
        };

        const rowMatchesFilters = (row) => {
            const general = normalize(searchInput?.value || '');
            const city = normalize(citySelect?.value || '');
            const uf = String(ufSelect?.value || '').trim().toUpperCase();
            const rowSearch = normalize(row.dataset.clienteSearch || row.textContent || '');
            const rowCity = normalize(row.dataset.clienteCity || getCellText(row, 'cidade'));
            const rowUf = String(row.dataset.clienteUf || '').trim().toUpperCase();

            return (!general || rowSearch.includes(general))
                && (!city || rowCity === city || rowCity.includes(city))
                && (!uf || rowUf === uf);
        };

        const sortedRows = (rows) => {
            if (!state.sortField || !state.sortDirection) {
                return rows;
            }

            const button = sortButtons.find((item) => item.dataset.clientesSortField === state.sortField);
            const type = button?.dataset.clientesSortType || 'text';
            const direction = state.sortDirection === 'asc' ? 1 : -1;

            return [...rows].sort((first, second) => {
                const firstValue = getSortValue(first, state.sortField, type);
                const secondValue = getSortValue(second, state.sortField, type);

                if (type === 'number') {
                    return (firstValue - secondValue) * direction;
                }

                return String(firstValue).localeCompare(String(secondValue), 'pt-BR', { numeric: true }) * direction;
            });
        };

        const ensureEmptyRow = () => {
            let emptyRow = tbody.querySelector('[data-clientes-filter-empty]');

            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.dataset.clientesFilterEmpty = '1';
                emptyRow.innerHTML = '<td colspan="7" class="clientes-filter-empty">Nenhum cliente encontrado.</td>';
                tbody.appendChild(emptyRow);
            }

            return emptyRow;
        };

        const renderPagination = () => {
            uppercaseUfValues();
            const allRows = getRows();
            const staticEmptyRows = Array.from(tbody.querySelectorAll('tr:not([data-cliente-row]):not([data-clientes-filter-empty])'));
            const visibleRows = sortedRows(allRows.filter(rowMatchesFilters));
            const hiddenRows = allRows.filter((row) => !visibleRows.includes(row));

            visibleRows.forEach((row) => tbody.appendChild(row));
            hiddenRows.forEach((row) => tbody.appendChild(row));
            staticEmptyRows.forEach((row) => {
                row.hidden = allRows.length > 0;
            });

            const totalRows = visibleRows.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / state.perPage));
            state.page = Math.min(Math.max(1, state.page), totalPages);

            const startIndex = (state.page - 1) * state.perPage;
            const endIndex = startIndex + state.perPage;
            allRows.forEach((row) => {
                row.hidden = true;
            });
            visibleRows.slice(startIndex, endIndex).forEach((row) => {
                row.hidden = false;
            });

            const emptyRow = ensureEmptyRow();
            emptyRow.hidden = totalRows > 0;

            const start = totalRows ? startIndex + 1 : 0;
            const end = Math.min(endIndex, totalRows);
            const options = pageSizeOptions
                .map((option) => `<option value="${option}" ${Number(option) === state.perPage ? 'selected' : ''}>${option}</option>`)
                .join('');
            const buttons = Array.from({ length: totalPages }, (_, index) => {
                const page = index + 1;
                return `<button type="button" class="list-page-button ${page === state.page ? 'active' : ''}" data-client-page="${page}">${page}</button>`;
            }).join('');

            paginationTarget.innerHTML = `<span>${start}-${end} de ${totalRows}</span>
                <div class="list-pagination-pages">${buttons}<label>Ir para <input class="list-page-input" type="number" min="1" max="${totalPages}" value="${state.page}" data-client-page-input></label></div>
                <label class="list-pagination-size"><span>Mostrar</span><select class="list-page-size-select" data-client-page-size>${options}</select><input class="list-page-input" type="number" min="1" max="500" value="${state.perPage}" data-client-page-size-manual aria-label="Quantidade manual por página"><span>por página</span></label>`;

            updateSortButtons();
        };

        const queueRender = () => {
            window.setTimeout(renderPagination, 0);
        };

        [searchInput].forEach((input) => {
            input?.addEventListener('input', () => {
                state.page = 1;
                queueRender();
            });
        });

        citySelect?.addEventListener('input', () => {
            state.page = 1;
            queueRender();
        });

        [citySelect, ufSelect].forEach((select) => {
            select?.addEventListener('change', () => {
                state.page = 1;
                queueRender();
            });
        });

        paginationTarget.addEventListener('click', (event) => {
            const button = event.target.closest('[data-client-page]');
            if (!button) {
                return;
            }

            state.page = Number(button.dataset.clientPage) || 1;
            renderPagination();
        });

        paginationTarget.addEventListener('change', (event) => {
            const pageSize = event.target.closest('[data-client-page-size]');
            if (pageSize) {
                state.perPage = Math.max(1, Number(pageSize.value) || state.perPage);
                state.page = 1;
                renderPagination();
                return;
            }

            const manualSize = event.target.closest('[data-client-page-size-manual]');
            if (manualSize) {
                state.perPage = Math.min(500, Math.max(1, Number(manualSize.value) || state.perPage));
                state.page = 1;
                renderPagination();
            }
        });

        paginationTarget.addEventListener('keydown', (event) => {
            const input = event.target.closest('[data-client-page-input]');
            if (!input || event.key !== 'Enter') {
                return;
            }

            event.preventDefault();
            state.page = Number(input.value) || 1;
            renderPagination();
        });

        sortButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopImmediatePropagation();

                const field = button.dataset.clientesSortField || '';
                if (state.sortField === field) {
                    state.sortDirection = state.sortDirection === 'asc' ? 'desc' : (state.sortDirection === 'desc' ? '' : 'asc');
                } else {
                    state.sortField = field;
                    state.sortDirection = 'asc';
                }

                if (!state.sortDirection) {
                    state.sortField = '';
                }

                state.page = 1;
                renderPagination();
            }, true);
        });

        renderPagination();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initClientesListFilters);
    } else {
        window.setTimeout(initClientesListFilters, 0);
    }
})();
</script>
