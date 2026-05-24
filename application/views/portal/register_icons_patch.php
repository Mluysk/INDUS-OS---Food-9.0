<?php
include __DIR__.'/register_branch_patch.php';
?>
<style>
    html body .auth-panel label.portal-icon-label {
        display: flex;
        align-items: center;
        gap: 0.38rem;
    }

    html body .document-toggle > span.portal-icon-label,
    html body .portal-branch-switch .portal-branch-text {
        display: inline-flex;
        align-items: center;
        gap: 0.38rem;
    }

    html body .portal-form-icon {
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        color: var(--primary, #dc2626);
        stroke-width: 1.9;
    }

    html body .document-toggle > span.portal-icon-label {
        font-weight: 800;
        color: var(--text, #111827);
    }

    html body .portal-branch-switch .portal-form-icon {
        color: var(--primary, #dc2626);
    }
</style>
<script>
(function () {
    const iconMap = {
        'registro-email': 'mail',
        'registro-telefone': 'phone',
        'registro-cnpj': 'building-2',
        'registro-cpf': 'id-card',
        'registro-razao': 'landmark',
        'registro-fantasia': 'store',
        'registro-cep': 'map-pin',
        'registro-endereco': 'route',
        'registro-numero': 'hash',
        'registro-bairro': 'map',
        'registro-cidade': 'building',
        'registro-estado': 'flag',
        'registro-filial': 'git-branch',
        'registro-senha': 'lock-keyhole',
        'registro-confirma': 'shield-check'
    };

    const ensureLucide = (callback) => {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            callback();
            return;
        }
        const existing = document.querySelector('script[data-portal-lucide]');
        if (existing) {
            existing.addEventListener('load', callback, { once: true });
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/lucide@0.371.0/dist/umd/lucide.min.js';
        script.defer = true;
        script.dataset.portalLucide = '1';
        script.addEventListener('load', callback, { once: true });
        document.head.appendChild(script);
    };

    const addIconToLabel = (label, icon) => {
        if (!label || label.dataset.iconReady === '1') return;
        label.dataset.iconReady = '1';
        label.classList.add('portal-icon-label');
        const span = document.createElement('span');
        span.className = 'portal-form-icon';
        span.setAttribute('data-lucide', icon);
        span.setAttribute('aria-hidden', 'true');
        label.prepend(span);
    };

    const decorateLabels = () => {
        Object.entries(iconMap).forEach(([id, icon]) => {
            addIconToLabel(document.querySelector(`label[for="${id}"]`), icon);
        });

        const documentLabel = document.querySelector('.document-toggle > span:not(.portal-form-icon)');
        addIconToLabel(documentLabel, 'file-badge-2');

        const branchSwitch = document.querySelector('.portal-branch-switch');
        if (branchSwitch && branchSwitch.dataset.textWrapped !== '1') {
            branchSwitch.dataset.textWrapped = '1';
            const textNode = Array.from(branchSwitch.childNodes).find((node) => node.nodeType === Node.TEXT_NODE && node.textContent.trim());
            const textSpan = Array.from(branchSwitch.children).find((child) => child.tagName === 'SPAN' && child.textContent.trim() === 'Tem filial?');
            const target = textSpan || textNode;
            if (target) {
                const wrapper = document.createElement('span');
                wrapper.className = 'portal-branch-text portal-icon-label';
                wrapper.innerHTML = '<span class="portal-form-icon" data-lucide="store"></span><span>Tem filial?</span>';
                target.replaceWith(wrapper);
            }
        }

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    };

    const start = () => ensureLucide(() => {
        decorateLabels();
        window.setTimeout(decorateLabels, 120);
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }
})();
</script>
