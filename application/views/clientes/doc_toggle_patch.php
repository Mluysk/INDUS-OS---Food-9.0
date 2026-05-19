<script>
(function () {
  const initDocumentoToggle = () => {
    const form = document.querySelector('.cliente-form');
    if (!form || form.dataset.docTogglePatch === '1') return;
    form.dataset.docTogglePatch = '1';

    const hiddenField = form.querySelector('input[name="documento_tipo"]');
    const documentoInput = form.querySelector('#documento');
    const cnpjButton = form.querySelector('#btn-cnpj');
    const cpfButton = form.querySelector('#btn-cpf');
    const labelText = form.querySelector('[data-doc-label-text]');
    const labelIcon = form.querySelector('[data-doc-label-icon]');
    const ieField = form.querySelector('.ie-input-group');
    const ieInput = ieField ? ieField.querySelector('input[name="ie"]') : null;
    const hint = form.querySelector('.cnpj-autofill-hint');
    const feedback = form.querySelector('.cnpj-autofill-feedback');

    const onlyDigits = (value) => String(value || '').replace(/\D+/g, '');

    const formatCpf = (value) => {
      const digits = onlyDigits(value).slice(0, 11);
      if (digits.length <= 3) return digits;
      if (digits.length <= 6) return `${digits.slice(0, 3)}.${digits.slice(3)}`;
      if (digits.length <= 9) return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6)}`;
      return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9)}`;
    };

    const formatCnpj = (value) => {
      const digits = onlyDigits(value).slice(0, 14);
      if (digits.length <= 2) return digits;
      if (digits.length <= 5) return `${digits.slice(0, 2)}.${digits.slice(2)}`;
      if (digits.length <= 8) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5)}`;
      if (digits.length <= 12) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8)}`;
      return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8, 12)}-${digits.slice(12)}`;
    };

    const renderIcons = () => {
      if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons({ elements: form.querySelectorAll('[data-lucide]') });
      }
    };

    const setDocumentoTipo = (type, clearValue = true) => {
      const normalizedType = type === 'cpf' ? 'cpf' : 'cnpj';
      const isCpf = normalizedType === 'cpf';

      if (hiddenField) hiddenField.value = normalizedType;

      if (cnpjButton) {
        cnpjButton.classList.toggle('active', !isCpf);
        cnpjButton.setAttribute('aria-pressed', !isCpf ? 'true' : 'false');
      }

      if (cpfButton) {
        cpfButton.classList.toggle('active', isCpf);
        cpfButton.setAttribute('aria-pressed', isCpf ? 'true' : 'false');
      }

      if (labelText) labelText.textContent = isCpf ? 'CPF' : 'CNPJ';
      if (labelIcon) labelIcon.setAttribute('data-lucide', isCpf ? 'id-card' : 'building-2');

      if (documentoInput) {
        documentoInput.placeholder = isCpf ? '000.000.000-00' : '00.000.000/0000-00';
        documentoInput.maxLength = isCpf ? 14 : 18;
        if (clearValue) {
          documentoInput.value = '';
        } else {
          documentoInput.value = isCpf ? formatCpf(documentoInput.value) : formatCnpj(documentoInput.value);
        }
      }

      if (ieField) ieField.hidden = isCpf;
      if (ieInput) {
        ieInput.disabled = isCpf;
        if (isCpf) ieInput.value = '';
      }
      if (hint) hint.hidden = isCpf;
      if (feedback && isCpf) feedback.textContent = '';

      renderIcons();
    };

    const handleDocumentoClick = (event) => {
      const button = event.target.closest('#btn-cnpj, #btn-cpf');
      if (!button || !form.contains(button)) return;
      event.preventDefault();
      event.stopImmediatePropagation();
      setDocumentoTipo(button.id === 'btn-cpf' ? 'cpf' : 'cnpj');
    };

    document.addEventListener('click', handleDocumentoClick, true);

    if (documentoInput) {
      documentoInput.addEventListener('input', () => {
        const type = hiddenField && hiddenField.value === 'cpf' ? 'cpf' : 'cnpj';
        documentoInput.value = type === 'cpf' ? formatCpf(documentoInput.value) : formatCnpj(documentoInput.value);
      });
    }

    window.setClienteDocumentoType = (type, clearValue = false) => setDocumentoTipo(type, clearValue);
    window.formatClienteDocumento = (value, type) => (type === 'cpf' ? formatCpf(value) : formatCnpj(value));

    setDocumentoTipo(hiddenField && hiddenField.value === 'cpf' ? 'cpf' : 'cnpj', false);
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDocumentoToggle, { once: true });
  } else {
    initDocumentoToggle();
  }
})();
</script>
