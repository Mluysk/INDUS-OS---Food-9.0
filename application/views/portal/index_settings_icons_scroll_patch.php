<?php
include __DIR__.'/index_settings_full_registration_patch.php';
?>
<style>
    html,
    body {
        overflow-x: hidden !important;
    }

    html body .portal-app,
    html body .portal-app__panels,
    html body .portal-panel,
    html body .portal-settings-tab-card,
    html body .portal-settings-form,
    html body .portal-settings-section,
    html body .portal-settings-grid,
    html body .portal-settings-field,
    html body .portal-settings-branch-row,
    html body .portal-settings-option {
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }

    html body .portal-settings-tab-card {
        overflow-x: hidden !important;
    }

    html body .portal-settings-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(230px, 100%), 1fr)) !important;
    }

    html body .portal-settings-field input,
    html body .portal-settings-uf-wrap,
    html body .portal-settings-actions,
    html body .portal-settings-subtabs {
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }

    html body .portal-settings-subtabs,
    html body .portal-settings-actions,
    html body .portal-settings-doc-toggle {
        flex-wrap: wrap !important;
    }

    html body .portal-settings-label-icon,
    html body .portal-settings-section-icon,
    html body .portal-settings-subtab-icon {
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 18px;
        color: var(--primary, #2563eb);
    }

    html body .portal-settings-label-icon svg,
    html body .portal-settings-section-icon svg,
    html body .portal-settings-subtab-icon svg {
        width: 18px;
        height: 18px;
        display: block;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    html body .portal-settings-field label,
    html body .portal-settings-section-title,
    html body .portal-settings-subtab,
    html body .portal-settings-doc-toggle > span,
    html body .portal-settings-contact-type > strong {
        display: inline-flex !important;
        align-items: center;
        gap: 0.42rem;
    }

    html body .portal-settings-subtab.is-active .portal-settings-subtab-icon {
        color: currentColor;
    }
</style>
<script>
(function () {
    const icons = {
        user: '<svg viewBox="0 0 24 24"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>',
        mail: '<svg viewBox="0 0 24 24"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>',
        phone: '<svg viewBox="0 0 24 24"><path d="M22 16.92v2.1a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 3.3 2 2 0 0 1 4.11 1h2.1a2 2 0 0 1 2 1.72c.12.91.33 1.8.62 2.65a2 2 0 0 1-.45 2.11L7.5 8.36a16 16 0 0 0 6.14 6.14l.88-.88a2 2 0 0 1 2.11-.45c.85.29 1.74.5 2.65.62A2 2 0 0 1 22 16.92Z"/></svg>',
        message: '<svg viewBox="0 0 24 24"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5A8.5 8.5 0 0 1 21 11v.5Z"/></svg>',
        file: '<svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h5"/></svg>',
        building: '<svg viewBox="0 0 24 24"><path d="M3 21h18"/><path d="M5 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"/><path d="M9 7h1"/><path d="M14 7h1"/><path d="M9 11h1"/><path d="M14 11h1"/><path d="M9 15h1"/><path d="M14 15h1"/></svg>',
        store: '<svg viewBox="0 0 24 24"><path d="M4 10h16"/><path d="M5 10 6.5 4h11L19 10"/><path d="M6 10v10h12V10"/><path d="M9 20v-6h6v6"/></svg>',
        map: '<svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
        home: '<svg viewBox="0 0 24 24"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/></svg>',
        hash: '<svg viewBox="0 0 24 24"><path d="M4 9h16"/><path d="M4 15h16"/><path d="M10 3 8 21"/><path d="m16 3-2 18"/></svg>',
        lock: '<svg viewBox="0 0 24 24"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>',
        palette: '<svg viewBox="0 0 24 24"><path d="M12 22a10 10 0 1 1 10-10 3 3 0 0 1-3 3h-1.5a2 2 0 0 0-1.7 3.05A2.6 2.6 0 0 1 13.6 22H12Z"/><circle cx="7.5" cy="10.5" r=".5"/><circle cx="10.5" cy="7.5" r=".5"/><circle cx="14.5" cy="7.5" r=".5"/><circle cx="16.5" cy="10.5" r=".5"/></svg>',
        id: '<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M5.8 17a3.4 3.4 0 0 1 6.4 0"/><path d="M14 9h4"/><path d="M14 13h4"/></svg>'
    };

    const fieldIcons = {
        'portal-full-nome': 'user',
        'portal-full-email': 'mail',
        'portal-full-telefone': 'phone',
        'portal-full-whatsapp': 'message',
        'portal-full-cnpj': 'file',
        'portal-full-cpf': 'id',
        'portal-full-razao': 'building',
        'portal-full-fantasia': 'store',
        'portal-full-filial': 'store',
        'portal-full-cep': 'map',
        'portal-full-endereco': 'home',
        'portal-full-numero': 'hash',
        'portal-full-bairro': 'map',
        'portal-full-cidade': 'map',
        'portal-full-estado': 'map',
        'portal-full-senha': 'lock',
        'portal-full-confirma': 'lock',
        'portal-full-store': 'store',
        'portal-full-branch': 'store'
    };

    const iconSpan = (name, className = 'portal-settings-label-icon') => `<span class="${className}" aria-hidden="true">${icons[name] || icons.file}</span>`;

    const addIconToLabel = (label, name) => {
        if (!label || label.dataset.iconReady === '1') return;
        label.dataset.iconReady = '1';
        label.insertAdjacentHTML('afterbegin', iconSpan(name));
    };

    const applyIcons = () => {
        Object.entries(fieldIcons).forEach(([id, icon]) => {
            addIconToLabel(document.querySelector(`label[for="${id}"]`), icon);
        });
        document.querySelectorAll('[data-settings-section-trigger]').forEach((button) => {
            if (button.dataset.iconReady === '1') return;
            button.dataset.iconReady = '1';
            const icon = button.dataset.settingsSectionTrigger === 'personalizacao' ? 'palette' : 'id';
            button.insertAdjacentHTML('afterbegin', iconSpan(icon, 'portal-settings-subtab-icon'));
        });
        document.querySelectorAll('.portal-settings-section-title').forEach((title) => {
            if (title.dataset.iconReady === '1') return;
            title.dataset.iconReady = '1';
            const text = title.textContent.toLowerCase();
            const icon = text.includes('endereço') ? 'map' : text.includes('senha') ? 'lock' : text.includes('personal') ? 'palette' : 'id';
            title.insertAdjacentHTML('afterbegin', iconSpan(icon, 'portal-settings-section-icon'));
        });
        addIconToLabel(document.querySelector('.portal-settings-doc-toggle > span'), 'file');
        addIconToLabel(document.querySelector('.portal-settings-contact-type > strong'), 'phone');
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            applyIcons();
            window.setTimeout(applyIcons, 420);
            window.setTimeout(applyIcons, 900);
        }, { once: true });
    } else {
        applyIcons();
        window.setTimeout(applyIcons, 420);
        window.setTimeout(applyIcons, 900);
    }
})();
</script>
