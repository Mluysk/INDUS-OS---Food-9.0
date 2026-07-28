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
        root.setProperty('--brand-title-x', `${Math.max(-80, Math.min(80, titleX))}px`);
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
<style>
.portal-settings-tabs{display:inline-flex;gap:6px;margin:0 0 16px;padding:4px;border:1px solid var(--border);border-radius:12px;background:var(--surface-muted)}.portal-settings-tab{display:inline-flex;align-items:center;gap:7px;min-height:38px;padding:8px 13px;border:0;border-radius:9px;background:transparent;color:var(--text-muted);font-weight:800;cursor:pointer}.portal-settings-tab svg{width:16px;height:16px}.portal-settings-tab.active{background:var(--primary);color:#fff}.portal-settings-pane[hidden]{display:none!important}.portal-groups-head{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:14px}.portal-groups-head h3{display:flex;align-items:center;gap:8px;margin:0}.portal-groups-head h3 svg{width:21px;height:21px;color:var(--primary)}.portal-group-list{display:grid;gap:14px}.portal-group-card{padding:16px;border:1px solid var(--border);border-radius:15px;background:var(--surface-alt)}.portal-group-card__head{display:grid;grid-template-columns:minmax(220px,1fr) auto;gap:10px;align-items:end;margin-bottom:14px}.portal-group-card__head label{display:grid;gap:6px;font-weight:800;color:var(--text-muted)}.portal-group-card__head input{min-height:44px;border:1px solid var(--border);border-radius:11px;padding:10px 12px;background:var(--surface);color:var(--text)}.portal-group-remove{display:grid;place-items:center;width:38px;height:38px;border:0;border-radius:10px;background:#fff1f2;color:#dc2626;cursor:pointer}.portal-group-remove svg{width:15px;height:15px}.portal-group-columns{display:grid;grid-template-columns:1fr 1fr;gap:14px}.portal-group-selector{padding:13px;border:1px solid var(--border);border-radius:12px;background:var(--surface)}.portal-group-selector h4{margin:0 0 4px}.portal-group-selector>p{margin:0 0 10px;color:var(--text-muted);font-size:.8rem}.portal-group-options{display:grid;gap:6px;max-height:230px;overflow:auto;padding-right:4px}.portal-group-option{display:flex;align-items:center;gap:9px;padding:8px 9px;border:1px solid var(--border);border-radius:9px;font-weight:700;cursor:pointer}.portal-group-option input{width:17px;height:17px;accent-color:var(--primary)}.portal-group-empty{padding:18px;border:1px dashed var(--border);border-radius:13px;text-align:center;color:var(--text-muted)}@media(max-width:760px){.portal-group-columns{grid-template-columns:1fr}.portal-groups-head{align-items:flex-start;flex-direction:column}}
.portal-group-product-search{position:relative;margin:0 0 10px}.portal-group-product-search>svg{position:absolute;left:12px;top:50%;width:16px;height:16px;transform:translateY(-50%);color:var(--text-muted);pointer-events:none}.portal-group-product-search input{width:100%;min-height:42px;padding:9px 36px 9px 38px;border:1px solid var(--border);border-radius:10px;background:var(--surface-alt);color:var(--text)}.portal-group-product-search:focus-within>svg{color:var(--primary)}.portal-group-product-search__clear{position:absolute;right:7px;top:50%;display:grid;place-items:center;width:28px;height:28px;padding:0;border:0;border-radius:8px;transform:translateY(-50%);background:transparent;color:var(--text-muted);cursor:pointer}.portal-group-product-search__clear svg{width:14px;height:14px}.portal-group-option[hidden]{display:none!important}.portal-group-search-empty{padding:12px;text-align:center;color:var(--text-muted);font-size:.82rem}
.portal-groups-head__actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}.portal-groups-head__actions button{display:inline-flex;align-items:center;gap:7px}.portal-groups-head__actions svg{width:16px;height:16px}
.portal-groups-head>div:first-child{display:grid;gap:7px}.portal-groups-head h3{min-height:26px;line-height:1.3!important}.portal-groups-head .card-subtitle{position:static!important;margin:0!important;line-height:1.45!important}
.portal-group-row{display:grid;grid-template-columns:minmax(220px,1fr) auto auto;align-items:center;gap:12px;padding:13px 15px;border:1px solid var(--border);border-radius:13px;background:var(--surface)}.portal-group-row__name{display:flex;align-items:center;gap:10px;min-width:0}.portal-group-row__name>svg{width:19px;height:19px;flex:0 0 19px;color:var(--primary)}.portal-group-row__name strong{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.portal-group-row__name small{display:block;margin-top:3px;color:var(--text-muted)}.portal-group-row__stats{display:flex;align-items:center;gap:7px;flex-wrap:wrap}.portal-group-row__stat{padding:5px 9px;border-radius:999px;background:var(--surface-muted);color:var(--text-muted);font-size:.76rem;font-weight:800}.portal-group-row__actions{display:flex;align-items:center;gap:5px}.portal-group-row__action{display:grid;place-items:center;width:34px;height:34px;padding:0;border:0;border-radius:10px;background:var(--surface-muted);color:var(--text);cursor:pointer}.portal-group-row__action.danger{background:#fff1f2;color:#dc2626}.portal-group-row__action svg{width:14px;height:14px}.portal-group-editor-actions{display:flex;justify-content:flex-end;margin-top:12px}.portal-group-editor-actions button{display:inline-flex;align-items:center;gap:7px}@media(max-width:700px){.portal-group-row{grid-template-columns:1fr auto}.portal-group-row__stats{grid-column:1/-1;grid-row:2}.portal-group-row__actions{grid-column:2;grid-row:1}}
</style>
<script>
(() => {
const bootPortalGroups=()=>{
 const panel=document.querySelector('[data-appearance-panel="portal"]');const form=panel?.closest('form');if(!panel||!form||panel.dataset.groupsReady==='1')return;panel.dataset.groupsReady='1';
 const clients=<?= json_encode(array_values($clientsList ?? []), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
 const products=<?= json_encode(array_values($portalGroupProducts ?? []), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
 let groups=<?= json_encode(array_values($portalClientGroups ?? []), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;let editingIndex=-1;
 const esc=(v)=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
 const existing=Array.from(panel.children);const visual=document.createElement('div');visual.className='portal-settings-pane';visual.dataset.portalSettingsPane='visual';existing.forEach(node=>visual.appendChild(node));panel.appendChild(visual);
 const groupsPane=document.createElement('div');groupsPane.className='portal-settings-pane';groupsPane.dataset.portalSettingsPane='groups';groupsPane.dataset.autosaveSkip='';groupsPane.hidden=true;groupsPane.innerHTML='<div class="portal-groups-head"><div><h3><span data-lucide="users-round"></span>Grupos de clientes</h3><p class="card-subtitle">Monte os grupos de produtos. No cadastro, o cliente escolhe um grupo e passa a aparecer automaticamente aqui.</p></div><div class="portal-groups-head__actions"><button type="button" class="btn ghost" data-save-portal-groups><span data-lucide="save"></span> Salvar grupos</button><button type="button" class="btn-primary" data-add-portal-group><span data-lucide="plus"></span> Novo grupo</button></div></div><div class="portal-group-list"></div>';panel.appendChild(groupsPane);
 const tabs=document.createElement('div');tabs.className='portal-settings-tabs';tabs.innerHTML='<button type="button" class="portal-settings-tab active" data-portal-settings-tab="visual"><span data-lucide="settings-2"></span><span>Aparência e suporte</span></button><button type="button" class="portal-settings-tab" data-portal-settings-tab="groups"><span data-lucide="users-round"></span><span>Grupos de clientes</span></button>';panel.prepend(tabs);
 tabs.insertAdjacentHTML('beforeend','<button type="button" class="portal-settings-tab" data-portal-settings-tab="catalog"><span data-lucide="layout-grid"></span><span>Catálogo</span></button>');
 const catalogPane=document.createElement('div');catalogPane.className='portal-settings-pane';catalogPane.dataset.portalSettingsPane='catalog';catalogPane.hidden=true;catalogPane.innerHTML='<div class="support-card"><h3><span data-lucide="layout-grid"></span> Paginação do catálogo</h3><p class="card-subtitle">Defina quantos produtos aparecem por página no Portal do Cliente. O cliente ainda poderá alterar a quantidade durante a navegação.</p><div class="field"><label for="portal-catalog-page-size">Produtos por página</label><input id="portal-catalog-page-size" type="number" min="1" max="9999" name="config[PORTAL_CATALOG_PAGE_SIZE]" value="<?= (int)($themeDefaults['PORTAL_CATALOG_PAGE_SIZE'] ?? 15); ?>" data-portal-catalog-page-size style="width:calc(4ch + 42px);min-width:calc(4ch + 42px)"><p class="field-hint">Valor permitido entre 1 e 9999 produtos.</p></div><button type="button" class="btn-primary" data-save-portal-catalog><span data-lucide="save"></span> Salvar catálogo</button></div>';panel.appendChild(catalogPane);
 const hidden=document.createElement('input');hidden.type='hidden';hidden.name='config[PORTAL_CLIENT_GROUPS]';hidden.dataset.autosaveSkip='';form.appendChild(hidden);const list=groupsPane.querySelector('.portal-group-list');
 const normalize=()=>{groups=groups.filter(g=>g&&typeof g==='object').map((g,i)=>({id:String(g.id||`grupo_${Date.now()}_${i}`),name:String(g.name||''),client_ids:Array.isArray(g.client_ids)?g.client_ids.map(Number):[],product_ids:Array.isArray(g.product_ids)?g.product_ids.map(Number):[],created_by_client:Math.max(0,Number(g.created_by_client)||0)}));hidden.value=JSON.stringify(groups);};
 const options=(items,selected,type)=>items.map(item=>{const id=Number(item.id);const label=type==='client'?(item.nome_fantasia||item.razao_social||item.email||`Cliente #${id}`):`${item.nome||`Produto #${id}`}${item.sku?` · ${item.sku}`:''}${item.categoria?` · ${item.categoria}`:''}`;return `<label class="portal-group-option"><input type="checkbox" value="${id}" ${selected.includes(id)?'checked':''}><span>${esc(label)}</span></label>`;}).join('')||'<div class="portal-group-empty">Nenhum cadastro disponível.</div>';
 const editor=(g,i)=>`<article class="portal-group-card" data-group-index="${i}"><div class="portal-group-card__head"><label>Nome do grupo<input value="${esc(g.name)}" placeholder="Ex.: Atacado, Varejo ou Rede Sul" data-group-name></label><button type="button" class="portal-group-remove" data-remove-group aria-label="Excluir grupo" title="Excluir grupo"><span data-lucide="trash-2"></span></button></div><div class="portal-group-columns"><section class="portal-group-selector"><h4>Clientes que escolheram o grupo</h4><p>A lista é atualizada quando o cliente conclui o cadastro.</p><div class="portal-group-options" data-group-clients>${options(clients,g.client_ids,'client')}</div></section><section class="portal-group-selector"><h4>Produtos permitidos</h4><p>Somente os produtos marcados ficarão disponíveis.</p><div class="portal-group-product-search"><span data-lucide="search"></span><input type="search" data-product-group-search placeholder="Buscar por produto, SKU ou categoria" autocomplete="off"><button type="button" class="portal-group-product-search__clear" data-clear-product-search aria-label="Limpar busca"><span data-lucide="x"></span></button></div><div class="portal-group-options" data-group-products>${options(products,g.product_ids,'product')}</div><div class="portal-group-search-empty" data-product-search-empty hidden>Nenhum produto encontrado.</div></section></div><div class="portal-group-editor-actions"><button type="button" class="btn ghost" data-close-group-editor><span data-lucide="chevron-up"></span> Recolher</button></div></article>`;
 const row=(g,i)=>`<article class="portal-group-row" data-group-row="${i}"><div class="portal-group-row__name"><span data-lucide="users-round"></span><div><strong>${esc(g.name||'Grupo sem nome')}</strong><small>Grupo de produtos do Portal</small></div></div><div class="portal-group-row__stats"><span class="portal-group-row__stat">${g.client_ids.length} cliente${g.client_ids.length===1?'':'s'}</span><span class="portal-group-row__stat">${g.product_ids.length} produto${g.product_ids.length===1?'':'s'}</span></div><div class="portal-group-row__actions"><button type="button" class="portal-group-row__action" data-edit-group="${i}" title="Editar grupo" aria-label="Editar grupo"><span data-lucide="pencil"></span></button><button type="button" class="portal-group-row__action danger" data-remove-group-row="${i}" title="Excluir grupo" aria-label="Excluir grupo"><span data-lucide="trash-2"></span></button></div></article>`;
 const render=()=>{normalize();list.innerHTML=groups.length?groups.map((g,i)=>editingIndex===i?editor(g,i):row(g,i)).join(''):'<div class="portal-group-empty">Crie o primeiro grupo de produtos para disponibilizá-lo no cadastro do Portal.</div>';if(window.lucide)window.lucide.createIcons({elements:panel.querySelectorAll('[data-lucide]')});};
 const syncCard=(card)=>{const i=Number(card.dataset.groupIndex);if(!groups[i])return;groups[i].name=card.querySelector('[data-group-name]').value.trim();groups[i].client_ids=Array.from(card.querySelectorAll('[data-group-clients] input:checked')).map(x=>Number(x.value));groups[i].product_ids=Array.from(card.querySelectorAll('[data-group-products] input:checked')).map(x=>Number(x.value));normalize();};
 tabs.addEventListener('click',e=>{const b=e.target.closest('[data-portal-settings-tab]');if(!b)return;const key=b.dataset.portalSettingsTab;tabs.querySelectorAll('button').forEach(x=>x.classList.toggle('active',x===b));panel.querySelectorAll('[data-portal-settings-pane]').forEach(x=>x.hidden=x.dataset.portalSettingsPane!==key);});
 groupsPane.querySelector('[data-add-portal-group]').addEventListener('click',()=>{groups.push({id:`grupo_${Date.now()}`,name:'',client_ids:[],product_ids:[]});editingIndex=groups.length-1;render();list.querySelector('[data-group-name]')?.focus();});
 groupsPane.querySelector('[data-save-portal-groups]').addEventListener('click',()=>{list.querySelectorAll('.portal-group-card[data-group-index]').forEach(syncCard);normalize();localStorage.setItem('configTab','appearance');localStorage.setItem('configAppearanceTab','portal');form.requestSubmit();});
 catalogPane.querySelector('[data-save-portal-catalog]').addEventListener('click',()=>{const input=catalogPane.querySelector('[data-portal-catalog-page-size]');input.value=String(Math.min(9999,Math.max(1,Number(input.value)||15)));localStorage.setItem('configTab','appearance');localStorage.setItem('configAppearanceTab','portal');form.requestSubmit();});
 list.addEventListener('input',e=>{const card=e.target.closest('[data-group-index]');if(card)syncCard(card);});list.addEventListener('change',e=>{const card=e.target.closest('[data-group-index]');if(card)syncCard(card);});list.addEventListener('click',e=>{const b=e.target.closest('[data-remove-group]');if(!b)return;const card=b.closest('[data-group-index]');if(!confirm('Excluir este grupo de clientes?'))return;groups.splice(Number(card.dataset.groupIndex),1);editingIndex=-1;render();form.requestSubmit();});
 list.addEventListener('input',e=>{if(!e.target.matches('[data-product-group-search]'))return;const section=e.target.closest('.portal-group-selector');const term=e.target.value.trim().toLocaleLowerCase('pt-BR');let visible=0;section.querySelectorAll('[data-group-products] .portal-group-option').forEach(option=>{const show=!term||option.textContent.toLocaleLowerCase('pt-BR').includes(term);option.hidden=!show;if(show)visible++;});section.querySelector('[data-product-search-empty]').hidden=visible!==0;});
 list.addEventListener('click',e=>{const clear=e.target.closest('[data-clear-product-search]');if(!clear)return;const input=clear.closest('.portal-group-product-search').querySelector('[data-product-group-search]');input.value='';input.dispatchEvent(new Event('input',{bubbles:true}));input.focus();});
 list.addEventListener('click',e=>{const edit=e.target.closest('[data-edit-group]');if(edit){editingIndex=Number(edit.dataset.editGroup);render();list.querySelector('[data-group-name]')?.focus();return;}const close=e.target.closest('[data-close-group-editor]');if(close){const card=close.closest('[data-group-index]');syncCard(card);editingIndex=-1;render();return;}const remove=e.target.closest('[data-remove-group-row]');if(remove){if(!confirm('Excluir este grupo de clientes?'))return;groups.splice(Number(remove.dataset.removeGroupRow),1);editingIndex=-1;render();form.requestSubmit();}});
 form.addEventListener('submit',()=>{list.querySelectorAll('.portal-group-card[data-group-index]').forEach(syncCard);normalize();});render();
};if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',bootPortalGroups,{once:true});else bootPortalGroups();
})();
</script>
<style>
    .tab-button.config-portal-main-tab{display:inline-flex;align-items:center;gap:8px}.tab-button.config-portal-main-tab svg{width:17px;height:17px}
    .appearance-subtab[data-appearance-tab="portal"]{display:none!important}
    .config-portal-main-heading{display:none;align-items:flex-start;gap:12px;margin-bottom:18px}.config-portal-main-heading svg{width:26px;height:26px;flex:0 0 26px;color:var(--primary)}.config-portal-main-heading h2{margin:0 0 5px}.config-portal-main-heading p{margin:0;color:var(--text-muted)}
    form[data-tab-panel="appearance"].config-portal-main-mode>.appearance-heading,form[data-tab-panel="appearance"].config-portal-main-mode>.appearance-subtabs{display:none!important}
    form[data-tab-panel="appearance"].config-portal-main-mode .config-portal-main-heading{display:flex}
</style>
<script>
(() => {
    const installPortalMainTab=()=>{
        const nav=document.querySelector('.config-tabs');
        const appearanceButton=nav?.querySelector('[data-tab="appearance"]');
        const appearanceForm=document.querySelector('form[data-tab-panel="appearance"]');
        const portalSubtab=appearanceForm?.querySelector('[data-appearance-tab="portal"]');
        const portalPanel=appearanceForm?.querySelector('[data-appearance-panel="portal"]');
        if(!nav||!appearanceButton||!appearanceForm||!portalSubtab||!portalPanel||nav.querySelector('[data-portal-main-tab]'))return;
        const button=document.createElement('button');
        button.type='button';button.className='tab-button tab-button--with-icon config-portal-main-tab';button.dataset.portalMainTab='1';button.setAttribute('aria-selected','false');
        button.innerHTML='<span data-lucide="globe-2" aria-hidden="true"></span><span>Portal</span>';
        appearanceButton.after(button);
        const heading=document.createElement('div');heading.className='config-portal-main-heading';heading.innerHTML='<span data-lucide="globe-2" aria-hidden="true"></span><div><h2>Configurações do Portal</h2><p>Personalize o acesso dos clientes, a identidade do login e os canais de suporte.</p></div>';
        portalPanel.before(heading);
        const openPortal=()=>{
            appearanceButton.click();portalSubtab.click();
            nav.querySelectorAll('.tab-button').forEach((item)=>{const active=item===button;item.classList.toggle('active',active);item.setAttribute('aria-selected',active?'true':'false');});
            appearanceForm.classList.add('config-portal-main-mode');
            localStorage.setItem('configPortalMainTab','1');localStorage.setItem('configTab','appearance');localStorage.setItem('configAppearanceTab','portal');
        };
        button.addEventListener('click',openPortal);
        nav.querySelectorAll('.tab-button:not([data-portal-main-tab])').forEach((item)=>item.addEventListener('click',()=>{
            button.classList.remove('active');button.setAttribute('aria-selected','false');
            appearanceForm.classList.remove('config-portal-main-mode');localStorage.removeItem('configPortalMainTab');
        }));
        appearanceForm.addEventListener('submit',()=>{if(appearanceForm.classList.contains('config-portal-main-mode'))localStorage.setItem('configPortalMainTab','1');});
        if(localStorage.getItem('configPortalMainTab')==='1')openPortal();
        if(window.lucide)window.lucide.createIcons({elements:[...button.querySelectorAll('[data-lucide]'),...heading.querySelectorAll('[data-lucide]')]});
    };
    if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',installPortalMainTab,{once:true});else installPortalMainTab();
})();
</script>
<script>
(() => {
    const installUserCreateSubmit = () => {
        const form = document.querySelector('.user-form');
        if (!form || form.dataset.createAjaxReady === '1') return;
        form.dataset.createAjaxReady = '1';
        form.addEventListener('submit', async (event) => {
            const createAction = form.dataset.createAction || '';
            if (!createAction || form.action.replace(/\/$/, '') !== new URL(createAction, location.href).href.replace(/\/$/, '')) return;
            event.preventDefault();
            const submit = form.querySelector('#user-submit');
            if (submit) { submit.disabled = true; submit.textContent = 'Salvando...'; }
            try {
                const response = await fetch(createAction, {method:'POST',body:new FormData(form),headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(payload.message || 'Não foi possível cadastrar o usuário.');
                form.reset();
                ['#user-id','#user-name','#user-email','#user-documento','#user-password'].forEach((selector) => { const field=form.querySelector(selector); if(field) field.value=''; });
                const role = form.querySelector('#user-role');
                if (role?.options.length) role.selectedIndex = 0;
                const cancel = form.querySelector('#user-cancel');
                if (cancel) cancel.style.display = 'none';
                form.setAttribute('action', createAction);
                window.showAppToast?.(payload.message || 'Usuário cadastrado com sucesso.', 'success');
            } catch (error) {
                window.showAppToast?.(error.message || 'Não foi possível cadastrar o usuário.', 'error');
            } finally {
                if (submit) { submit.disabled = false; submit.textContent = 'Criar usuário'; }
            }
        }, true);
    };
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',installUserCreateSubmit,{once:true}); else installUserCreateSubmit();
})();
</script>
<script>
(() => {
    const installUserCreateIcons = () => {
        const form = document.querySelector('.user-form');
        if (!form || form.dataset.createIconsReady === '1') return;
        form.dataset.createIconsReady = '1';
        const title = form.querySelector('h2');
        if (title) { title.classList.add('user-create-title-with-icon'); title.insertAdjacentHTML('afterbegin','<span data-lucide="user-plus" aria-hidden="true"></span>'); }
        const fields = {'user-name':'user-round','user-email':'mail','user-documento':'contact','user-role':'shield-check','user-password':'key-round'};
        Object.entries(fields).forEach(([id, icon]) => {
            const label = form.querySelector(`label[for="${id}"]`);
            if (!label) return;
            label.classList.add('user-create-label-with-icon');
            label.insertAdjacentHTML('afterbegin', `<span data-lucide="${icon}" aria-hidden="true"></span>`);
        });
        const submit = form.querySelector('#user-submit');
        if (submit) submit.insertAdjacentHTML('afterbegin','<span data-lucide="user-plus" aria-hidden="true"></span>');
        if (window.lucide) window.lucide.createIcons({elements:form.querySelectorAll('[data-lucide]')});
    };
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',installUserCreateIcons,{once:true}); else installUserCreateIcons();
})();
</script>
<script>
(() => {
    const installUserPasswordToggle = () => {
        const button = document.querySelector('[data-user-password-toggle]');
        const input = document.querySelector('#user-password');
        if (!button || !input || button.dataset.ready === '1') return;
        button.dataset.ready = '1';
        button.addEventListener('click', () => {
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            button.setAttribute('aria-pressed', visible ? 'false' : 'true');
            button.setAttribute('aria-label', visible ? 'Mostrar senha' : 'Ocultar senha');
            const icon = button.querySelector('[data-user-password-icon]');
            if (icon) {
                icon.setAttribute('data-lucide', visible ? 'eye' : 'eye-off');
                if (window.lucide) window.lucide.createIcons({elements:[icon]});
            }
            input.focus({preventScroll:true});
        });
    };
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',installUserPasswordToggle,{once:true}); else installUserPasswordToggle();
})();
</script>
<script>
(() => {
    const shouldClearCreatedUser = new URLSearchParams(window.location.search).get('user_created') === '1';
    const clearCreatedUserForm = () => {
        if (!shouldClearCreatedUser) return;
        const params = new URLSearchParams(window.location.search);
        const form = document.querySelector('.user-form');
        if (form) {
            form.reset();
            form.setAttribute('action', form.dataset.createAction || form.getAttribute('action'));
            const fields = ['#user-id','#user-name','#user-email','#user-documento','#user-password'];
            fields.forEach((selector) => { const field=form.querySelector(selector); if(field) field.value=''; });
            const role = form.querySelector('#user-role');
            if (role?.options.length) role.selectedIndex = 0;
            const submit = form.querySelector('#user-submit');
            if (submit) submit.textContent = 'Criar usuário';
            const cancel = form.querySelector('#user-cancel');
            if (cancel) cancel.style.display = 'none';
        }
        if (params.has('user_created')) {
            params.delete('user_created');
            const query = params.toString();
            history.replaceState(null, '', window.location.pathname + (query ? '?' + query : '') + window.location.hash);
        }
    };
    const clearRepeatedly = () => {
        clearCreatedUserForm();
        window.setTimeout(clearCreatedUserForm, 80);
        window.setTimeout(clearCreatedUserForm, 350);
    };
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',clearRepeatedly,{once:true}); else clearRepeatedly();
    window.addEventListener('pageshow', clearRepeatedly, {once:true});
})();
</script>
<script>
(() => {
    const promotePeopleTabs = () => {
        const root = document.querySelector('[data-tab-panel="users"]');
        const nav = root?.querySelector('.user-subtabs');
        const createPanel = root?.querySelector('[data-user-panel="create"]');
        const hierarchyPanel = root?.querySelector('[data-user-panel="hierarchy"]');
        const hierarchyButton = nav?.querySelector('[data-user-tab="hierarchy"]');
        const listCard = createPanel?.querySelector('.user-list-card');
        const usersTable = listCard?.querySelector('[data-people-panel="users"]');
        const clientsTable = listCard?.querySelector('[data-people-panel="clients"]');
        if (!root || !nav || !createPanel || !hierarchyPanel || !hierarchyButton || !listCard || !usersTable || !clientsTable || root.dataset.peoplePromoted === '1') return;
        root.dataset.peoplePromoted = '1';

        listCard.querySelector('.config-people-tabs')?.remove();
        usersTable.hidden = false;
        clientsTable.hidden = false;

        const makeButton = (key, label, icon) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'user-subtab';
            button.dataset.userTab = key;
            button.setAttribute('aria-selected', 'false');
            button.innerHTML = `<span data-lucide="${icon}" aria-hidden="true"></span><span>${label}</span>`;
            return button;
        };
        const usersButton = makeButton('users-list', 'Usuários', 'users');
        const clientsButton = makeButton('clients-list', 'Clientes', 'contact');
        hierarchyButton.before(usersButton, clientsButton);
        hierarchyButton.remove();

        const usersPanel = document.createElement('div');
        usersPanel.className = 'user-subpanel';
        usersPanel.dataset.userPanel = 'users-list';
        const usersCard = document.createElement('div');
        usersCard.className = 'card user-list-card';
        usersCard.innerHTML = '<header class="list-header"><div><h3 class="config-section-title"><span data-lucide="users-round" aria-hidden="true"></span><span>Usuários cadastrados</span></h3><p class="card-subtitle">Gerencie os acessos internos e suas hierarquias.</p></div></header>';
        usersCard.appendChild(usersTable);
        const hierarchyCard = hierarchyPanel.querySelector(':scope > .card');
        const hierarchyTitle = hierarchyCard?.querySelector('.hierarchy-header h2');
        if (hierarchyTitle && !hierarchyTitle.querySelector('[data-lucide]')) {
            hierarchyTitle.classList.add('config-section-title');
            hierarchyTitle.insertAdjacentHTML('afterbegin','<span data-lucide="network" aria-hidden="true"></span>');
        }
        const innerTabs = document.createElement('div');
        innerTabs.className = 'config-people-tabs config-user-inner-tabs';
        innerTabs.setAttribute('role', 'tablist');
        innerTabs.innerHTML = '<button type="button" class="config-people-tab is-active" data-user-inner-tab="list" aria-selected="true"><span data-lucide="users"></span><span>Usuários</span></button><button type="button" class="config-people-tab" data-user-inner-tab="hierarchy" aria-selected="false"><span data-lucide="network"></span><span>Hierarquia</span></button>';
        usersCard.classList.add('config-user-inner-panel');
        usersCard.dataset.userInnerPanel = 'list';
        if (hierarchyCard) {
            hierarchyCard.classList.add('config-user-inner-panel');
            hierarchyCard.dataset.userInnerPanel = 'hierarchy';
            hierarchyCard.hidden = true;
        }
        usersPanel.appendChild(innerTabs);
        usersPanel.appendChild(usersCard);
        if (hierarchyCard) usersPanel.appendChild(hierarchyCard);

        const clientsPanel = document.createElement('div');
        clientsPanel.className = 'user-subpanel';
        clientsPanel.dataset.userPanel = 'clients-list';
        const clientsCard = document.createElement('div');
        clientsCard.className = 'card user-list-card config-clients-portal-card';
        clientsCard.innerHTML = '<header class="list-header"><div><h3 class="config-section-title"><span data-lucide="contact-round" aria-hidden="true"></span><span>Clientes do Portal</span></h3><p class="card-subtitle">Gerencie separadamente os clientes que possuem acesso ao Portal.</p></div></header>';
        clientsCard.appendChild(clientsTable);
        clientsPanel.appendChild(clientsCard);
        hierarchyPanel.before(usersPanel, clientsPanel);
        hierarchyPanel.remove();
        listCard.remove();

        const activateInner = (key) => {
            innerTabs.querySelectorAll('[data-user-inner-tab]').forEach((button) => { const active=button.dataset.userInnerTab===key; button.classList.toggle('is-active',active); button.setAttribute('aria-selected',active?'true':'false'); });
            usersPanel.querySelectorAll(':scope > [data-user-inner-panel]').forEach((panel) => { panel.hidden=panel.dataset.userInnerPanel!==key; });
            localStorage.setItem('configUserInnerTab', key);
        };
        innerTabs.addEventListener('click', (event) => { const button=event.target.closest('[data-user-inner-tab]'); if(button) activateInner(button.dataset.userInnerTab); });
        activateInner(localStorage.getItem('configUserInnerTab') === 'hierarchy' ? 'hierarchy' : 'list');
        hierarchyCard?.querySelector('form')?.addEventListener('submit', () => {
            localStorage.setItem('configUserTab', 'users-list');
            localStorage.setItem('configPeopleTab', 'users');
            localStorage.setItem('configUserInnerTab', 'hierarchy');
        });

        const activate = (key) => {
            nav.querySelectorAll('.user-subtab').forEach((button) => {
                const active = button.dataset.userTab === key;
                button.classList.toggle('active', active);
                button.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            root.querySelectorAll(':scope > .user-subpanel').forEach((panel) => panel.classList.toggle('active', panel.dataset.userPanel === key));
            localStorage.setItem('configUserTab', key);
            localStorage.setItem('configPeopleTab', key === 'clients-list' ? 'clients' : 'users');
        };
        usersPanel.addEventListener('click', (event) => {
            const editButton = event.target.closest('.user-edit');
            if (!editButton) return;
            localStorage.setItem('configPeopleTab', 'users');
            localStorage.setItem('configUserTab', 'create');
            activate('create');
            window.setTimeout(() => {
                const form = root.querySelector('.user-form');
                form?.scrollIntoView({behavior:'smooth', block:'start'});
            }, 0);
        });
        [usersButton, clientsButton].forEach((button) => button.addEventListener('click', (event) => { event.preventDefault(); event.stopPropagation(); activate(button.dataset.userTab); }, true));
        const previousTopTab = localStorage.getItem('configUserTab');
        const requestedPeopleTab = new URLSearchParams(window.location.search).get('people');
        if (requestedPeopleTab === 'clients') {
            localStorage.setItem('configUserTab', 'clients-list');
            localStorage.setItem('configPeopleTab', 'clients');
        }
        const preferred = requestedPeopleTab === 'clients' || localStorage.getItem('configPeopleTab') === 'clients' ? 'clients-list' : (previousTopTab === 'hierarchy' ? 'users-list' : null);
        if (preferred) activate(preferred);
        if (previousTopTab === 'hierarchy') activateInner('hierarchy');
        if (window.lucide) window.lucide.createIcons({elements:root.querySelectorAll('[data-lucide]')});
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => window.setTimeout(promotePeopleTabs, 0), {once:true}); else window.setTimeout(promotePeopleTabs, 0);
})();
</script>
<style>
    .user-password-action-row{display:grid;grid-template-columns:minmax(0,64%) auto;align-items:end;gap:9px}
    .user-password-control{position:relative}.user-password-control input{width:100%;padding-right:48px!important}
    .user-password-toggle{position:absolute;right:7px;bottom:7px;display:grid;place-items:center;width:34px;height:34px;border:0;border-radius:9px;background:transparent;color:var(--text-muted);cursor:pointer}
    .user-password-toggle:hover,.user-password-toggle:focus-visible{background:var(--surface-muted);color:var(--primary);outline:0}
    .user-password-toggle svg{width:18px;height:18px}.user-password-submit-actions{display:flex;align-items:center;gap:9px;min-height:46px}
    .user-create-title-with-icon,.user-create-label-with-icon{display:inline-flex;align-items:center;gap:8px}
    .user-form > .card-subtitle{position:relative;top:10px;margin:-6px 0 37px}
    .user-permissions-note{display:flex;align-items:center;gap:12px;margin:-8px 0 30px;padding:12px 14px;border:1px solid color-mix(in srgb,var(--primary) 22%,var(--border));border-radius:13px;background:color-mix(in srgb,var(--primary) 6%,var(--surface));color:var(--text-muted)}
    .user-permissions-note__icon{width:22px;height:22px;flex:0 0 22px;color:var(--primary)}
    .user-permissions-note>div{display:grid;gap:2px}.user-permissions-note strong{color:var(--text);font-size:.84rem}.user-permissions-note span{font-size:.78rem;line-height:1.35}.user-permissions-note b{color:var(--primary)}
    .user-form .field{gap:9px}
    .user-form > .field-grid + .field-grid,.user-form > .field-grid + .user-password-action-row{margin-top:18px}
    .user-create-title-with-icon svg{width:27px;height:27px;color:var(--primary);stroke-width:2.2}
    .user-create-label-with-icon svg{width:17px;height:17px;color:var(--primary);stroke-width:2.1}
    #user-submit{display:inline-flex;align-items:center;justify-content:center;gap:8px}#user-submit svg{width:17px;height:17px}
    @media(max-width:680px){.user-password-action-row{grid-template-columns:1fr}.user-password-submit-actions{justify-content:flex-end}}
    .config-people-tabs { display:inline-flex; align-items:center; gap:5px; padding:4px; border:1px solid var(--border); border-radius:12px; background:var(--surface-muted); }
    [data-tab-panel="users"]>.user-subtabs .user-subtab{display:inline-flex;align-items:center;justify-content:center;gap:8px}
    [data-tab-panel="users"]>.user-subtabs .user-subtab svg{width:17px;height:17px;flex:0 0 17px;stroke-width:2.2}
    .config-people-tab { display:inline-flex; align-items:center; gap:7px; min-height:36px; border:0; border-radius:9px; padding:7px 12px; background:transparent; color:var(--text-muted); font-weight:800; cursor:pointer; }
    .config-people-tab svg { width:16px; height:16px; }
    .config-people-tab.is-active { background:var(--primary); color:#fff; box-shadow:0 8px 18px color-mix(in srgb,var(--primary) 24%,transparent); }
    .config-user-inner-tabs { width:max-content; margin:0 0 14px; }
    .config-user-inner-panel[hidden] { display:none!important; }
    .config-people-panel[hidden] { display:none!important; }
    .config-clients-portal-card { margin-top:0!important; padding-top:18px!important; }
    .config-clients-portal-card > .list-header { margin-top:0!important; padding-top:0!important; }
    .config-section-title { display:flex; align-items:center; gap:9px; }
    .config-section-title > svg { width:21px; height:21px; flex:0 0 21px; color:var(--primary); stroke-width:2.25; }
    .hierarchy-header > div:first-child { display:grid; gap:8px; }
    .hierarchy-header .config-section-title { margin:0!important; min-height:25px; font-size:1.08rem; line-height:1.3; color:var(--text); }
    .hierarchy-header .config-section-title + .card-subtitle { margin:0!important; position:static!important; line-height:1.45; }
    .config-client-open { box-sizing:border-box; display:inline-flex!important; align-items:center; justify-content:center; width:34px; height:34px; min-width:34px; padding:0!important; border:0!important; border-radius:10px!important; background:var(--surface-muted,#f3f4f6)!important; color:var(--text,#020617)!important; text-decoration:none; }
    .config-client-open:hover { box-shadow:0 7px 18px rgba(15,23,42,.12); transform:translateY(-1px); }
    .config-client-open svg { width:14px; height:14px; stroke-width:2.25; }
    .config-client-actions { display:inline-flex; flex-wrap:nowrap; align-items:center; justify-content:center; gap:5px; white-space:nowrap; }
    .config-client-actions form { margin:0; }
    .config-client-actions .product-icon-btn { box-sizing:border-box; position:relative; display:inline-flex; flex:0 0 34px; align-items:center; justify-content:center; width:34px; min-width:34px; max-width:34px; height:34px; min-height:34px; max-height:34px; padding:0; border:0; border-radius:10px; background:var(--surface-muted,#f3f4f6); color:var(--text,#020617); cursor:pointer; text-decoration:none; }
    .config-client-actions .product-icon-btn svg { width:14px; height:14px; stroke-width:2.25; }
    .config-client-actions .product-icon-btn--danger { color:#dc2626; background:#fff1f2; }
    .config-client-actions .product-icon-btn::after { content:attr(data-tooltip); position:absolute; left:50%; bottom:calc(100% + 8px); transform:translateX(-50%) translateY(4px); padding:7px 10px; border-radius:8px; background:#111827; color:#fff; font-size:.72rem; font-weight:700; line-height:1; white-space:nowrap; opacity:0; pointer-events:none; transition:opacity .16s ease,transform .16s ease; z-index:40; }
    .config-client-actions .product-icon-btn:hover::after,.config-client-actions .product-icon-btn:focus-visible::after { opacity:1; transform:translateX(-50%) translateY(0); }
    .config-client-access-off { background:var(--surface-muted)!important; color:var(--text-muted)!important; }
    .config-user-row-actions { display:inline-flex; align-items:center; justify-content:center; gap:7px; }
    .config-user-row-actions form { margin:0; }
    .config-user-action { box-sizing:border-box; display:inline-flex!important; align-items:center; justify-content:center; width:34px; min-width:34px; height:34px; min-height:34px; padding:0!important; border:0!important; border-radius:10px!important; cursor:pointer; transition:transform .15s ease,box-shadow .15s ease,background .15s ease; }
    .config-user-action svg { width:14px; height:14px; stroke-width:2.25; }
    .config-user-action--edit { background:var(--surface-muted,#f3f4f6)!important; color:var(--text,#020617)!important; }
    .config-user-action--delete { background:#fff1f2!important; color:#dc2626!important; }
    .config-user-action:hover { transform:translateY(-1px); box-shadow:0 7px 18px rgba(15,23,42,.12); }
    .config-user-action--delete:hover { background:#ffe4e6!important; box-shadow:0 7px 18px rgba(220,38,38,.14); }
    @media(max-width:680px){.user-list-card .list-header{align-items:flex-start;flex-direction:column}.config-people-tabs{width:100%}.config-people-tab{flex:1;justify-content:center}}
</style>
<script>
(() => {
    const clients = <?= json_encode(array_values($clientsList ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    const bootPeopleLists = () => {
        const card = document.querySelector('[data-user-panel="create"] .user-list-card');
        const header = card?.querySelector('.list-header');
        const usersTable = card?.querySelector(':scope > .table-responsive');
        if (!card || !header || !usersTable || card.dataset.peopleTabsReady === '1') return;
        card.dataset.peopleTabsReady = '1';

        const title = header.querySelector('h3');
        const subtitle = header.querySelector('.card-subtitle');
        if (title) title.textContent = 'Cadastros de acesso';
        if (subtitle) subtitle.textContent = 'Consulte separadamente os usuários internos e os clientes com acesso ao Portal.';

        const tabs = document.createElement('div');
        tabs.className = 'config-people-tabs';
        tabs.setAttribute('role', 'tablist');
        tabs.innerHTML = '<button type="button" class="config-people-tab is-active" data-people-tab="users" aria-selected="true"><span data-lucide="shield-user"></span><span>Usuários</span></button><button type="button" class="config-people-tab" data-people-tab="clients" aria-selected="false"><span data-lucide="users"></span><span>Clientes</span></button>';
        header.appendChild(tabs);

        usersTable.classList.add('config-people-panel');
        usersTable.dataset.peoplePanel = 'users';
        usersTable.querySelectorAll('tbody tr').forEach((row) => {
            if (row.querySelector('.user-edit[data-papel="cliente"]')) row.remove();
        });
        usersTable.querySelectorAll('tbody tr').forEach((row) => {
            const actionsCell = row.querySelector('.actions-col');
            const editButton = row.querySelector('.user-edit');
            const deleteForm = row.querySelector('.user-delete-form');
            const deleteButton = deleteForm?.querySelector('button[type="submit"]');
            if (!actionsCell || (!editButton && !deleteForm)) return;
            let actionsWrap = actionsCell.querySelector('.config-user-row-actions');
            if (!actionsWrap) {
                actionsWrap = document.createElement('span');
                actionsWrap.className = 'config-user-row-actions config-client-actions product-actions';
                if (editButton) actionsWrap.appendChild(editButton);
                if (deleteForm) actionsWrap.appendChild(deleteForm);
                actionsCell.appendChild(actionsWrap);
            }
            if (editButton) {
                editButton.className = 'product-icon-btn user-edit';
                editButton.innerHTML = '<span data-lucide="pencil" aria-hidden="true"></span>';
                editButton.setAttribute('data-tooltip','Editar');
                editButton.removeAttribute('title');
                editButton.setAttribute('aria-label','Editar usuário');
            }
            if (deleteButton) {
                deleteButton.className = 'product-icon-btn product-icon-btn--danger';
                deleteButton.innerHTML = '<span data-lucide="trash-2" aria-hidden="true"></span>';
                deleteButton.setAttribute('data-tooltip','Excluir permanentemente');
                deleteButton.removeAttribute('title');
                deleteButton.setAttribute('aria-label','Excluir usuário permanentemente');
            }
        });
        const clientsPanel = document.createElement('div');
        clientsPanel.className = 'table-responsive config-people-panel';
        clientsPanel.dataset.peoplePanel = 'clients';
        clientsPanel.hidden = true;
        const rows = clients.length ? clients.map((client) => {
            const name = String(client.nome_fantasia || client.razao_social || `Cliente #${client.id || 0}`).trim();
            const documentValue = String(client.cnpj || client.cpf || '—').trim();
            const href = `${location.pathname.replace(/\/config\/?$/, '')}/clientes?cliente_id=${encodeURIComponent(client.id || 0)}`;
            const hasPortalAccess = Number(client.usuario_id || 0) > 0;
            const activeAccess = hasPortalAccess && Number(client.usuario_ativo ?? 1) === 1;
            const accessLabel = activeAccess ? 'Cliente' : 'Cliente inativo';
            const accessClass = activeAccess ? 'role-chip' : 'role-chip config-client-access-off';
            const editHref = `${location.pathname.replace(/\/config\/?$/, '')}/config/clientes/${encodeURIComponent(client.id || 0)}/editar`;
            const viewHref = `${editHref}?modo=visualizar`;
            const deleteHref = `${location.pathname.replace(/\/config\/?$/, '')}/config/clientes/${encodeURIComponent(client.id || 0)}/excluir`;
            return `<tr><td>${escapeHtml(name)}</td><td>${escapeHtml(documentValue)}</td><td>${escapeHtml(client.email || '—')}</td><td>${escapeHtml(client.telefone || '—')}</td><td><span class="${accessClass}">${escapeHtml(accessLabel)}</span></td><td class="actions-col"><span class="config-client-actions product-actions"><a class="product-icon-btn" href="${escapeHtml(viewHref)}" data-tooltip="Ver informações" aria-label="Ver informações do cliente"><span data-lucide="eye" aria-hidden="true"></span></a><a class="product-icon-btn" href="${escapeHtml(editHref)}" data-tooltip="Editar" aria-label="Editar cliente"><span data-lucide="pencil" aria-hidden="true"></span></a><form class="product-delete-form" method="post" action="${escapeHtml(deleteHref)}" onsubmit="return confirm('Excluir permanentemente este cliente e seu acesso ao Portal?');"><button class="product-icon-btn product-icon-btn--danger" type="submit" data-tooltip="Excluir permanentemente" aria-label="Excluir cliente permanentemente"><span data-lucide="trash-2" aria-hidden="true"></span></button></form></span></td></tr>`;
        }).join('') : '<tr><td colspan="6" class="empty">Nenhum cliente possui acesso ao Portal até o momento.</td></tr>';
        clientsPanel.innerHTML = `<table class="user-table"><thead><tr><th>Cliente</th><th>Documento</th><th>E-mail</th><th>Telefone</th><th>Hierarquia</th><th class="actions-col">Ações</th></tr></thead><tbody>${rows}</tbody></table>`;
        usersTable.after(clientsPanel);


        const activate = (key) => {
            tabs.querySelectorAll('[data-people-tab]').forEach((button) => { const active=button.dataset.peopleTab===key; button.classList.toggle('is-active',active); button.setAttribute('aria-selected',active?'true':'false'); });
            card.querySelectorAll('[data-people-panel]').forEach((panel) => { panel.hidden=panel.dataset.peoplePanel!==key; });
            localStorage.setItem('configPeopleTab', key);
        };
        tabs.addEventListener('click', (event) => { const button=event.target.closest('[data-people-tab]'); if(button) activate(button.dataset.peopleTab); });
        const requestedPeopleTab = new URLSearchParams(window.location.search).get('people');
        activate(requestedPeopleTab === 'clients' || localStorage.getItem('configPeopleTab') === 'clients' ? 'clients' : 'users');
        if (window.lucide) window.lucide.createIcons({elements:card.querySelectorAll('[data-lucide]')});
    };
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',bootPeopleLists,{once:true}); else bootPeopleLists();
})();
</script>
