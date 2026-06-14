<style>
    .product-weight-field {
        position: relative;
        display: block;
        width: 100%;
    }

    .product-weight-field input[name="peso_liquido"] {
        width: 100%;
        padding-right: 92px;
    }

    .product-weight-preview {
        position: absolute;
        top: 50%;
        right: 30px;
        transform: translateY(-50%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        max-width: 58px;
        padding: 0.2rem 0.45rem;
        border-radius: 999px;
        background: var(--surface-muted, #f8fafc);
        color: var(--text-muted, #64748b);
        font-size: 0.72rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        line-height: 1;
        white-space: nowrap;
        pointer-events: none;
        z-index: 1;
    }

    .product-weight-preview:empty {
        display: none;
    }
</style>

<script>
(function () {
    const formatWeight = (value) => {
        const numeric = Number(String(value || '').replace(',', '.'));
        if (!numeric) {
            return '';
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

            const field = document.createElement('span');
            field.className = 'product-weight-field';
            input.parentNode.insertBefore(field, input);
            field.appendChild(input);

            const preview = document.createElement('span');
            preview.className = 'product-weight-preview';
            preview.setAttribute('aria-hidden', 'true');
            field.appendChild(preview);

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
