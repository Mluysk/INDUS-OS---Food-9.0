<style>
    .product-weight-preview {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        margin-top: 0.35rem;
        padding: 0.32rem 0.58rem;
        border-radius: 999px;
        background: var(--surface-muted, #f8fafc);
        color: var(--text-muted, #64748b);
        font-size: 0.78rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
    }
</style>

<script>
(function () {
    const formatWeight = (value) => {
        const numeric = Number(String(value || '').replace(',', '.'));
        if (!numeric) {
            return 'Peso não informado';
        }
        if (numeric < 1) {
            return `${(numeric * 1000).toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 })} g`;
        }
        return `${numeric.toLocaleString('pt-BR', { minimumFractionDigits: 3, maximumFractionDigits: 3 })} kg`;
    };

    const applyWeightPreview = () => {
        document.querySelectorAll('input[name="peso_liquido"]').forEach((input) => {
            if (input.dataset.weightPreviewReady === '1') {
                return;
            }
            input.dataset.weightPreviewReady = '1';
            input.setAttribute('step', '0.001');
            input.setAttribute('placeholder', 'Ex.: 0,300 ou 1,000');

            const preview = document.createElement('span');
            preview.className = 'product-weight-preview';
            input.insertAdjacentElement('afterend', preview);

            const sync = () => {
                preview.textContent = formatWeight(input.value);
            };

            input.addEventListener('input', sync);
            input.addEventListener('blur', () => {
                const numeric = Number(String(input.value || '').replace(',', '.'));
                if (numeric) {
                    input.value = numeric.toFixed(3);
                }
                sync();
            });
            sync();
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyWeightPreview);
    } else {
        applyWeightPreview();
    }

    document.addEventListener('click', (event) => {
        if (event.target instanceof Element && event.target.closest('.js-edit-product')) {
            window.setTimeout(applyWeightPreview, 0);
        }
    });
})();
</script>
