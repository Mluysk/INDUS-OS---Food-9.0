<?php
include __DIR__.'/index_original.php';
include __DIR__.'/title_color_live_patch.php';
include __DIR__.'/audit_tab_patch.php';
?>
<style>
    html body [data-tab-panel="users"].active .user-subpanel {
        display: none !important;
    }

    html body [data-tab-panel="users"].active .user-subpanel.active {
        display: block !important;
    }

    html body [data-tab-panel="users"].active .user-subtab {
        pointer-events: auto !important;
        position: relative !important;
        z-index: 3 !important;
    }

    html body [data-user-panel="hierarchy"].active .hierarchy-form,
    html body [data-user-panel="hierarchy"].active .role-grid,
    html body [data-user-panel="hierarchy"].active .role-card {
        pointer-events: auto !important;
    }

    .config-export-card {
        margin-top: 18px;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        background: linear-gradient(135deg, var(--surface-muted), var(--surface));
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .config-export-card h3 {
        margin: 0;
        font-size: 1.05rem;
        color: var(--text);
    }

    .config-export-card p {
        margin: 4px 0 0;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .config-export-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(260px, 1fr));
        gap: 14px;
    }

    .config-export-group {
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px;
        background: var(--surface);
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-width: 0;
    }

    .config-export-group header {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        color: var(--text);
    }

    .config-export-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .config-export-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 44px;
        border-radius: 999px;
        padding: 11px 16px;
        border: 1px solid var(--border);
        background: var(--surface-muted);
        color: var(--text);
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease;
    }

    .config-export-button:hover {
        transform: translateY(-1px);
        border-color: var(--primary);
        background: var(--primary-soft);
        color: var(--primary-dark);
        text-decoration: none;
    }

    .config-export-button.primary {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .config-export-button.primary:hover {
        background: var(--primary-dark);
        color: #fff;
    }

    .config-import-line {
        display: grid;
        grid-template-columns: minmax(160px, 1fr) auto;
        gap: 10px;
        align-items: center;
    }

    .config-import-line input[type="file"] {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 10px;
        background: var(--surface-muted);
        color: var(--text);
        font-weight: 700;
        min-width: 0;
    }

    @media (max-width: 860px) {
        .config-export-grid {
            grid-template-columns: 1fr;
        }

        .config-import-line {
            grid-template-columns: 1fr;
        }
    }
</style>
<script>
(function () {
    const validUserTabs = new Set(['create', 'hierarchy']);

    const activateMainUsersTab = () => {
        document.querySelectorAll('.tab-button').forEach((button) => {
            const active = button.dataset.tab === 'users';
            button.classList.toggle('active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        document.querySelectorAll('.tab-panel').forEach((panel) => {
            panel.classList.toggle('active', panel.dataset.tabPanel === 'users');
        });

        document.querySelectorAll('input[name="active_tab"]').forEach((input) => {
            input.value = 'users';
        });

        localStorage.setItem('configTab', 'users');
    };

    const activateUserSubtab = (key) => {
        const target = validUserTabs.has(key) ? key : 'create';
        document.querySelectorAll('.user-subtab').forEach((button) => {
            const active = button.dataset.userTab === target;
            button.classList.toggle('active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        document.querySelectorAll('.user-subpanel').forEach((panel) => {
            panel.classList.toggle('active', panel.dataset.userPanel === target);
        });

        localStorage.setItem('configUserTab', target);
    };

    const basePathForApp = () => {
        const path = window.location.pathname || '/';
        const configIndex = path.indexOf('/config');
        if (configIndex > -1) {
            return path.slice(0, configIndex);
        }
        const indexPhp = path.indexOf('/index.php');
        if (indexPhp > -1) {
            return path.slice(0, indexPhp);
        }
        return '';
    };

    const buildUrl = (path) => `${basePathForApp().replace(/\/$/, '')}/${String(path || '').replace(/^\//, '')}`;

    const routeHierarchySave = (form) => {
        form.setAttribute('action', buildUrl('/config/hierarchy/save'));
        form.setAttribute('method', 'post');
    };

    const submitImport = (input, action) => {
        if (!input || !input.files || !input.files.length) {
            alert('Selecione uma planilha antes de importar.');
            return;
        }

        const form = document.createElement('form');
        form.method = 'post';
        form.enctype = 'multipart/form-data';
        form.action = action;
        form.style.display = 'none';
        input.name = 'arquivo';
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    };

    const addExportPanel = () => {
        if (document.querySelector('[data-config-export-panel]')) return;
        const backupPanel = document.querySelector('[data-tab-panel="backup"]');
        if (!backupPanel) return;

        const card = document.createElement('section');
        card.className = 'config-export-card';
        card.dataset.configExportPanel = '1';
        card.innerHTML = `
            <div>
                <h3>Exportação</h3>
                <p>Exporte clientes e produtos para Excel ou importe planilhas para cadastro rápido. Para importar, use CSV/TXT ou XLS simples com cabeçalho.</p>
            </div>
            <div class="config-export-grid">
                <div class="config-export-group">
                    <header><span data-lucide="users"></span><span>Clientes</span></header>
                    <div class="config-export-actions">
                        <a class="config-export-button primary" href="${buildUrl('/config/export/clientes')}">
                            <span data-lucide="file-spreadsheet"></span>
                            <span>Exportar XLS</span>
                        </a>
                    </div>
                    <div class="config-import-line">
                        <input type="file" data-import-file="clientes" accept=".csv,.txt,.xls,.xlsx,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        <button type="button" class="config-export-button" data-import-action="${buildUrl('/config/import/clientes')}" data-import-source="clientes">
                            <span data-lucide="upload"></span>
                            <span>Importar clientes</span>
                        </button>
                    </div>
                </div>
                <div class="config-export-group">
                    <header><span data-lucide="package"></span><span>Produtos</span></header>
                    <div class="config-export-actions">
                        <a class="config-export-button primary" href="${buildUrl('/config/export/produtos')}">
                            <span data-lucide="file-spreadsheet"></span>
                            <span>Exportar XLS</span>
                        </a>
                    </div>
                    <div class="config-import-line">
                        <input type="file" data-import-file="produtos" accept=".csv,.txt,.xls,.xlsx,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        <button type="button" class="config-export-button" data-import-action="${buildUrl('/config/import/produtos')}" data-import-source="produtos">
                            <span data-lucide="upload"></span>
                            <span>Importar produtos</span>
                        </button>
                    </div>
                </div>
            </div>
        `;

        const footer = backupPanel.querySelector('.form-footer');
        if (footer) {
            footer.before(card);
        } else {
            backupPanel.appendChild(card);
        }

        card.querySelectorAll('[data-import-action]').forEach((button) => {
            button.addEventListener('click', () => {
                const source = button.getAttribute('data-import-source');
                const input = card.querySelector(`[data-import-file="${source}"]`);
                submitImport(input, button.getAttribute('data-import-action'));
            });
        });

        if (window.lucide) {
            window.lucide.createIcons({ elements: card.querySelectorAll('[data-lucide]') });
        }
    };

    const cleanLocalThemePreview = () => {
        [
            'themePreviewActive',
            'themePreviewHex',
            'themePreviewName',
            'themePreviewLogoFilter',
            'themePreviewTitleSize',
            'themePreviewTitleOffsetX',
            'themePreviewTitleOffsetY'
        ].forEach((key) => localStorage.removeItem(key));
    };

    const normalizeHex = (value) => {
        let hex = String(value || '').trim();
        if (!hex) return null;
        if (!hex.startsWith('#')) hex = `#${hex}`;
        hex = hex.replace(/[^#0-9a-fA-F]/g, '').toUpperCase();
        return /^#[0-9A-F]{6}$/.test(hex) ? hex : null;
    };

    const shadeHex = (hex, percent) => {
        const normal = normalizeHex(hex) || '#5B616F';
        const raw = normal.slice(1);
        const amount = percent / 100;
        const channel = (pair) => {
            const base = parseInt(pair, 16);
            const value = percent >= 0 ? base + (255 - base) * amount : base + base * amount;
            return Math.max(0, Math.min(255, Math.round(value))).toString(16).padStart(2, '0');
        };
        return `#${channel(raw.slice(0, 2))}${channel(raw.slice(2, 4))}${channel(raw.slice(4, 6))}`.toLowerCase();
    };

    const applyDatabaseTheme = () => {
        const primary = normalizeHex(<?= json_encode($themePrimaryHex ?? '#5B616F'); ?>) || '#5B616F';
        const databaseMode = String(<?= json_encode($themeMode ?? 'light'); ?> || 'light').toLowerCase() === 'dark' ? 'dark' : 'light';
        const storedMode = String(localStorage.getItem('theme') || '').toLowerCase();
        const mode = ['light', 'dark'].includes(storedMode) ? storedMode : databaseMode;
        const titleSize = parseInt(<?= json_encode($titleSize ?? '26'); ?>, 10) || 26;
        const titleX = parseInt(<?= json_encode($titleOffsetX ?? '0'); ?>, 10) || 0;
        const titleY = parseInt(<?= json_encode($titleOffsetY ?? '0'); ?>, 10) || 0;
        const root = document.documentElement.style;

        cleanLocalThemePreview();
        localStorage.setItem('theme', mode);

        root.setProperty('--primary', primary.toLowerCase());
        root.setProperty('--primary-dark', shadeHex(primary, -25));
        root.setProperty('--primary-soft', shadeHex(primary, 45));
        root.setProperty('--brand-title-size', `${Math.max(16, Math.min(64, titleSize))}px`);
        root.setProperty('--brand-title-x', `${Math.max(-80, Math.min(80, titleX)) + 33}px`);
        root.setProperty('--brand-title-y', `${Math.max(-80, Math.min(80, titleY))}px`);

        document.body.classList.toggle('theme-dark', mode === 'dark');
        document.body.classList.toggle('theme-light', mode !== 'dark');
    };

    const boot = () => {
        applyDatabaseTheme();
        addExportPanel();

        document.querySelectorAll('.user-subtab').forEach((button) => {
            if (button.dataset.hierarchyTabFixReady === '1') return;
            button.dataset.hierarchyTabFixReady = '1';
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                activateMainUsersTab();
                activateUserSubtab(button.dataset.userTab || 'create');
            }, true);
        });

        const stored = localStorage.getItem('configUserTab');
        if (stored && !validUserTabs.has(stored)) {
            localStorage.removeItem('configUserTab');
        }

        const activeButton = document.querySelector('.user-subtab.active');
        const activePanel = document.querySelector('.user-subpanel.active');
        if (!activeButton || !activePanel) {
            activateUserSubtab(validUserTabs.has(stored || '') ? stored : 'create');
        }

        document.querySelectorAll('.hierarchy-form').forEach((form) => {
            routeHierarchySave(form);
            if (form.dataset.hierarchySubmitReady === '1') return;
            form.dataset.hierarchySubmitReady = '1';
            form.addEventListener('submit', () => {
                routeHierarchySave(form);
                localStorage.setItem('configTab', 'users');
                localStorage.setItem('configUserTab', 'hierarchy');
                form.querySelectorAll('input[name="active_tab"]').forEach((input) => {
                    input.value = 'users';
                });
            });
        });

        document.querySelectorAll('form[data-tab-panel="appearance"], form [name="active_tab"][value="appearance"]').forEach((node) => {
            const form = node.matches('form') ? node : node.closest('form');
            if (!form || form.dataset.themeDbSubmitReady === '1') return;
            form.dataset.themeDbSubmitReady = '1';
            form.addEventListener('submit', cleanLocalThemePreview);
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
</script>
