<style>
    .cliente-tabs {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 16px;
        flex-wrap: wrap;
    }

    .cliente-tab-button {
        appearance: none;
        height: 34px;
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border, #e5e7eb);
        background: var(--surface-alt, #f8fafc);
        color: var(--text, #111827);
        border-radius: 999px;
        padding: 0 14px;
        font-weight: 700;
        font-size: inherit;
        line-height: 1;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .cliente-tab-button.active {
        border-color: var(--primary, #dc2626);
        background: var(--primary, #dc2626);
        color: #fff;
        box-shadow: 0 10px 22px rgba(var(--brand-rgb, 220, 38, 38), 0.2);
    }

    .cliente-tab-panel[hidden] {
        display: none !important;
    }

    @media (max-width: 720px) {
        .cliente-tabs {
            display: flex;
        }

        .cliente-tab-button {
            flex: 0 0 auto;
            text-align: center;
        }
    }
</style>

<script>
(function () {
    const run = () => {
        const panels = Array.from(document.querySelectorAll('section.panel'));
        const createPanel = panels.find((panel) => panel.querySelector('.cliente-form'));
        const listPanel = panels.find((panel) => panel.querySelector('.clientes-table'));

        if (!createPanel || !listPanel || document.querySelector('.cliente-tabs')) {
            return;
        }

        createPanel.classList.add('cliente-tab-panel');
        listPanel.classList.add('cliente-tab-panel');
        createPanel.dataset.clientePanel = 'novo';
        listPanel.dataset.clientePanel = 'lista';

        const tabs = document.createElement('div');
        tabs.className = 'cliente-tabs';
        tabs.setAttribute('role', 'tablist');
        tabs.setAttribute('aria-label', 'Clientes');
        tabs.innerHTML = `
            <button type="button" class="cliente-tab-button active" data-cliente-tab="novo" aria-selected="true">Novo cliente</button>
            <button type="button" class="cliente-tab-button" data-cliente-tab="lista" aria-selected="false">Lista de clientes</button>
        `;

        createPanel.parentNode.insertBefore(tabs, createPanel);

        const buttons = Array.from(tabs.querySelectorAll('[data-cliente-tab]'));
        const showTab = (key) => {
            buttons.forEach((button) => {
                const active = button.dataset.clienteTab === key;
                button.classList.toggle('active', active);
                button.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            createPanel.hidden = key !== 'novo';
            listPanel.hidden = key !== 'lista';
            localStorage.setItem('clienteTab', key);
        };

        buttons.forEach((button) => {
            button.addEventListener('click', () => showTab(button.dataset.clienteTab || 'novo'));
        });

        showTab(localStorage.getItem('clienteTab') === 'lista' ? 'lista' : 'novo');
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();

(function () {
    const cleanLeakedCode = () => {
        const markers = [
            'printWindow.document.body.appendChild(printableDialog)',
            'validateRequiredFields',
            'js-cliente-detalhe',
            'Não foi possível carregar os detalhes deste cliente agora'
        ];
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        const leaked = [];

        while (walker.nextNode()) {
            const node = walker.currentNode;
            const text = node.nodeValue || '';
            if (markers.some((marker) => text.includes(marker))) {
                leaked.push(node);
            }
        }

        leaked.forEach((node) => node.remove());
    };

    const formatDocument = (value, type) => {
        const digits = String(value || '').replace(/\D+/g, '');
        if (type === 'cpf' && digits.length === 11) {
            return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9)}`;
        }
        if (type === 'cnpj' && digits.length === 14) {
            return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8, 12)}-${digits.slice(12)}`;
        }
        return value || '-';
    };

    const openDetails = (button) => {
        const modal = document.querySelector('[data-modal]');
        if (!modal) {
            return;
        }

        let data = {};
        const payload = button.getAttribute('data-cliente');
        if (payload) {
            try {
                data = JSON.parse(payload) || {};
            } catch (_) {
                data = {};
            }
        }

        const fields = modal.querySelectorAll('[data-field]');
        const subtitle = modal.querySelector('[data-modal-subtitle]');
        const enderecoCompleto = [data.endereco, data.numero].filter(Boolean).join(', ');

        fields.forEach((field) => {
            const key = field.dataset.field;
            let value = data[key];
            if (key === 'cnpj') {
                value = formatDocument(value, 'cnpj');
            } else if (key === 'cpf') {
                value = formatDocument(value, 'cpf');
            } else if (key === 'endereco') {
                value = enderecoCompleto || '-';
            } else if (key === 'contato_tipo') {
                const tipo = String(value || '').toLowerCase();
                value = tipo === 'celular' ? 'Celular' : (tipo === 'telefone' ? 'Telefone' : '-');
            }
            field.textContent = value ? String(value) : '-';
        });

        if (subtitle) {
            subtitle.textContent = data.email ? `Contato: ${data.email}` : '';
        }

        modal.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';

        if (window.lucide) {
            window.lucide.createIcons({ elements: modal.querySelectorAll('[data-lucide]') });
        }
    };

    const installDetails = () => {
        cleanLeakedCode();

        document.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof Element)) {
                return;
            }

            const detailButton = target.closest('.js-cliente-detalhe');
            if (detailButton) {
                event.preventDefault();
                event.stopPropagation();
                openDetails(detailButton);
                return;
            }

            const closeButton = target.closest('[data-modal-close]');
            if (closeButton) {
                const modal = closeButton.closest('[data-modal]') || document.querySelector('[data-modal]');
                if (modal) {
                    modal.setAttribute('hidden', 'hidden');
                    document.body.style.overflow = '';
                }
            }
        }, true);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', installDetails);
    } else {
        installDetails();
    }
})();
</script>
