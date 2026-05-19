<script>
(function () {
    const modal = document.querySelector('[data-modal]');
    const form = document.querySelector('.cliente-form');
    if (!modal || !form) {
        return;
    }

    const baseUrl = (modal.dataset.baseUrl || '/clientes').replace(/\/$/, '');
    const editButton = modal.querySelector('[data-modal-edit]');
    const printButton = modal.querySelector('[data-modal-print]');
    const deleteButton = modal.querySelector('[data-modal-delete]');
    const editCancel = form.querySelector('[data-edit-cancel]');
    const submitButton = form.querySelector('button[type="submit"]');
    const defaultSubmitText = submitButton ? submitButton.textContent : 'Salvar cliente';
    let currentData = null;

    const getPayloadFromButton = (button) => {
        const payload = button ? button.getAttribute('data-cliente') : '';
        if (!payload) {
            return null;
        }
        try {
            return JSON.parse(payload);
        } catch (_) {
            return null;
        }
    };

    const closeModal = () => {
        modal.setAttribute('hidden', 'hidden');
        document.body.style.overflow = '';
    };

    const formatDocument = (value, type) => {
        if (window.formatClienteDocumento) {
            return window.formatClienteDocumento(value, type);
        }
        return String(value || '');
    };

    const fillForm = (data) => {
        const docType = data.cpf ? 'cpf' : 'cnpj';
        if (window.setClienteDocumentoType) {
            window.setClienteDocumentoType(docType, false);
        } else {
            const button = form.querySelector(docType === 'cpf' ? '#btn-cpf' : '#btn-cnpj');
            button?.click();
        }

        const docInput = form.querySelector('#documento');
        if (docInput) {
            docInput.value = formatDocument(data[docType] || '', docType);
        }

        const names = ['razao_social', 'nome_fantasia', 'ie', 'email', 'telefone', 'whatsapp', 'cep', 'endereco', 'numero', 'bairro', 'cidade', 'estado'];
        names.forEach((name) => {
            const input = form.querySelector(`[name="${name}"]`);
            if (input) {
                input.value = data[name] || '';
            }
        });

        const contatoTipo = String(data.contato_tipo || 'celular').toLowerCase();
        form.querySelectorAll('[name="contato_tipo"]').forEach((option) => {
            option.checked = option.value === contatoTipo;
        });

        form.dataset.editId = String(data.id || '');
        if (submitButton) {
            submitButton.textContent = 'Atualizar cliente';
        }
        if (editCancel) {
            editCancel.hidden = false;
        }
        if (window.updateClienteUfFlag) {
            window.updateClienteUfFlag();
        }
    };

    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof Element)) {
            return;
        }
        const detailButton = target.closest('.js-cliente-detalhe');
        if (!detailButton) {
            return;
        }
        const data = getPayloadFromButton(detailButton);
        if (data) {
            currentData = data;
            modal.dataset.clienteId = String(data.id || '');
        }
    }, true);

    editButton?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        const data = currentData || (modal.dataset.clienteId ? { id: modal.dataset.clienteId } : null);
        if (!data || !data.id) {
            alert('Não foi possível carregar este cliente para edição.');
            return;
        }

        fillForm(data);
        closeModal();
        document.querySelector('[data-cliente-tab="novo"]')?.click();
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, true);

    printButton?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        const dialog = modal.querySelector('.cliente-modal__dialog');
        if (!dialog) {
            return;
        }

        const printableDialog = dialog.cloneNode(true);
        printableDialog.querySelector('.cliente-modal__close')?.remove();
        printableDialog.querySelector('.cliente-modal__footer')?.remove();

        const printWindow = window.open('', '_blank', 'width=900,height=700');
        if (!printWindow) {
            alert('Não foi possível abrir a janela de impressão.');
            return;
        }

        printWindow.document.open();
        printWindow.document.write(`<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><title>Detalhes do cliente</title><style>body{font-family:Arial,sans-serif;margin:24px;color:#0f172a}.cliente-modal__dialog{width:100%;max-width:100%;border:0;box-shadow:none;background:#fff}.cliente-modal__header{margin-bottom:14px}.cliente-modal__header h3{margin:0 0 6px;font-size:28px}.cliente-modal__subtitle{margin:0;color:#475569}.cliente-modal__content{overflow:visible}.cliente-modal__content dl{display:grid;grid-template-columns:repeat(2,minmax(220px,1fr));gap:14px 24px;margin:0}.cliente-modal__content dt{font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:4px}.cliente-modal__content dd{margin:0;font-size:20px;font-weight:700;color:#0f172a;word-break:break-word}@media print{body{margin:0}}</style></head><body></body></html>`);
        printWindow.document.body.appendChild(printableDialog);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }, true);

    deleteButton?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        const id = currentData?.id || modal.dataset.clienteId;
        if (!id) {
            alert('Não foi possível identificar este cliente.');
            return;
        }
        if (!window.confirm('Tem certeza que deseja excluir este cliente?')) {
            return;
        }

        deleteButton.disabled = true;
        fetch(`${baseUrl}/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then(() => window.location.reload())
            .catch(() => alert('Não foi possível excluir o cliente agora.'))
            .finally(() => {
                deleteButton.disabled = false;
            });
    }, true);

    editCancel?.addEventListener('click', () => {
        form.removeAttribute('data-edit-id');
        if (submitButton) {
            submitButton.textContent = defaultSubmitText;
        }
    });
})();
</script>
