<script>
(function () {
    const form = document.querySelector('.cliente-form');
    const modal = document.querySelector('[data-modal]');
    if (!form || !modal) {
        return;
    }

    const baseUrl = (modal.dataset.baseUrl || '/clientes').replace(/\/$/, '');
    const submitButton = form.querySelector('button[type="submit"]');
    const editCancel = form.querySelector('[data-edit-cancel]');

    const requiredNames = [
        ['razao_social', 'Nome / Razão social'],
        ['nome_fantasia', 'Nome fantasia'],
        ['documento', 'CNPJ/CPF'],
        ['email', 'E-mail'],
        ['telefone', 'Contato'],
        ['contato_tipo', 'Tipo do contato'],
        ['whatsapp', 'WhatsApp'],
        ['cep', 'CEP'],
        ['endereco', 'Endereço'],
        ['numero', 'Número'],
        ['bairro', 'Bairro'],
        ['cidade', 'Cidade'],
        ['estado', 'Estado (UF)'],
    ];

    const validate = () => {
        const missing = [];
        let first = null;

        requiredNames.forEach(([name, label]) => {
            const fields = Array.from(form.querySelectorAll(`[name="${name}"]`));
            if (!fields.length) {
                return;
            }

            const isRadio = fields[0].type === 'radio';
            const empty = isRadio
                ? !fields.some((field) => field.checked)
                : String(fields[0].value || '').trim() === '';

            if (empty) {
                missing.push(label);
                first ||= fields[0];
            }
        });

        if (missing.length) {
            alert(`Preencha os campos obrigatórios: ${missing.join(', ')}.`);
            first?.focus?.();
            return false;
        }
        return true;
    };

    form.addEventListener('submit', (event) => {
        const editId = form.dataset.editId;
        if (!editId) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        if (!validate()) {
            return;
        }

        const payload = Object.fromEntries(new FormData(form).entries());
        if (submitButton) {
            submitButton.disabled = true;
        }

        fetch(`${baseUrl}/${editId}`, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        })
            .then((response) => response.ok ? response.json() : response.json().then((data) => Promise.reject(data)).catch(() => Promise.reject({})))
            .then(() => {
                window.appLiveSaved?.({ message: 'Cliente atualizado com sucesso.', source: 'clientes' });
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
            })
            .catch((error) => {
                alert(error && error.message ? error.message : 'Não foi possível atualizar o cliente agora.');
            })
            .finally(() => {
                if (submitButton) {
                    submitButton.disabled = false;
                }
            });
    }, true);

    editCancel?.addEventListener('click', () => {
        form.removeAttribute('data-edit-id');
    });
})();
</script>
