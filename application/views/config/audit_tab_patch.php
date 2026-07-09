<?php
$auditRows = [];
try {
    $auditDb = $this->container->get(\App\Services\ConfigService::class)->getConnection();
    $stmt = $auditDb->query("SELECT al.*, u.nome AS usuario_nome, u.email AS usuario_email
        FROM audit_logs al
        LEFT JOIN usuarios u ON u.id = al.usuario_id
        ORDER BY al.id DESC
        LIMIT 300");

    $productCache = [];
    $productLookup = static function ($produtoId) use ($auditDb, &$productCache): ?array {
        $id = (int)($produtoId ?? 0);
        if ($id <= 0) {
            return null;
        }
        if (array_key_exists($id, $productCache)) {
            return $productCache[$id];
        }
        try {
            $productStmt = $auditDb->prepare('SELECT id, sku, nome FROM produtos WHERE id = :id LIMIT 1');
            $productStmt->execute(['id' => $id]);
            $product = $productStmt->fetch(\PDO::FETCH_ASSOC) ?: null;
            if (!$product) {
                $productCache[$id] = null;
                return null;
            }
            $sku = trim((string)($product['sku'] ?? ''));
            $nome = trim((string)($product['nome'] ?? ''));
            $label = trim(($sku !== '' ? $sku . ' - ' : '') . $nome);
            $productCache[$id] = [
                'id' => $id,
                'sku' => $sku,
                'nome' => $nome,
                'label' => $label !== '' ? $label : 'Produto #' . $id,
            ];
            return $productCache[$id];
        } catch (\Throwable $exception) {
            $productCache[$id] = null;
            return null;
        }
    };
    $enrichProducts = null;
    $enrichProducts = static function (&$value) use (&$enrichProducts, $productLookup): void {
        if (!is_array($value)) {
            return;
        }
        if (isset($value['produto_id'])) {
            $product = $productLookup($value['produto_id']);
            if ($product) {
                $value['produto_sku'] = $value['produto_sku'] ?? $product['sku'];
                $value['produto_nome'] = $value['produto_nome'] ?? $product['nome'];
                $value['produto_label'] = $value['produto_label'] ?? $product['label'];
            }
        }
        foreach ($value as &$child) {
            $enrichProducts($child);
        }
        unset($child);
    };

    foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [] as $row) {
        $details = json_decode((string)($row['detalhes'] ?? ''), true);
        if (!is_array($details)) {
            $details = [];
        }
        $enrichProducts($details);
        $userName = trim((string)($row['usuario_nome'] ?? ''));
        if ($userName === '' && !empty($row['usuario_id'])) {
            $userName = 'Usuario #' . (int)$row['usuario_id'];
        }
        $auditRows[] = [
            'id' => (int)($row['id'] ?? 0),
            'usuario' => $userName !== '' ? $userName : 'Sistema',
            'usuario_email' => (string)($row['usuario_email'] ?? ''),
            'usuario_id' => $row['usuario_id'] ?? null,
            'acao' => (string)($row['acao'] ?? ''),
            'entidade' => (string)($row['entidade'] ?? ''),
            'entidade_id' => $row['entidade_id'] ?? null,
            'detalhes' => $details,
            'ip' => (string)($row['ip'] ?? ''),
            'created_at' => (string)($row['created_at'] ?? ''),
        ];
    }
} catch (\Throwable $exception) {
    $auditRows = [];
}
?>
<?php $auditStandalone = (($activeMenu ?? '') === 'auditoria'); ?>
<style>
    html body [data-tab-panel="audit"]{gap:16px!important;padding:22px!important}.audit-header{display:flex;align-items:flex-start;gap:12px;justify-content:space-between;flex-wrap:wrap}.audit-title{display:flex;gap:12px;align-items:center}.audit-icon{display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:13px;background:var(--primary-soft);color:var(--primary);flex:0 0 38px}.audit-title h2{margin:0;font-size:1.18rem!important}.audit-title p{margin:3px 0 0}.audit-summary{display:grid;grid-template-columns:repeat(4,minmax(110px,1fr));gap:10px}.audit-card{border:1px solid var(--border);border-radius:12px;padding:11px 12px;background:var(--surface-muted)}.audit-card span{display:block;color:var(--text-muted);font-size:.66rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase}.audit-card strong{display:block;margin-top:6px;color:var(--text);font-size:1.04rem;line-height:1}.audit-filters{display:grid;grid-template-columns:minmax(220px,1fr) 160px 160px;gap:10px;align-items:end}.audit-filters label{font-size:.78rem!important}.audit-filters input,.audit-filters select{width:100%;min-height:38px!important;padding:8px 11px!important;border-radius:10px!important;font-size:.82rem!important}.audit-table-wrap{overflow-x:auto;border:1px solid var(--border);border-radius:16px;background:var(--surface)}.audit-table{width:100%;min-width:0!important;border-collapse:collapse;table-layout:fixed!important}.audit-table th,.audit-table td{border-bottom:1px solid var(--border);text-align:left;vertical-align:middle}.audit-table th{padding:11px 12px!important;background:var(--surface-muted);color:var(--text-muted);font-size:.66rem!important;font-weight:800;letter-spacing:.06em!important;text-transform:uppercase;white-space:nowrap}.audit-table td{padding:11px 12px!important;font-size:.78rem!important;line-height:1.22!important}.audit-table th:nth-child(1),.audit-table td:nth-child(1){width:112px!important}.audit-table th:nth-child(2),.audit-table td:nth-child(2){width:190px!important}.audit-table th:nth-child(3),.audit-table td:nth-child(3){width:132px!important}.audit-table th:nth-child(4),.audit-table td:nth-child(4){width:140px!important}.audit-table th:nth-child(6),.audit-table td:nth-child(6){width:92px!important}.audit-table th:nth-child(7),.audit-table td:nth-child(7){width:106px!important}.audit-table tbody tr:last-child td{border-bottom:0}.audit-cell-title,.audit-table strong.audit-cell-title{display:block;color:var(--text)!important;font-size:.88rem!important;line-height:1.12!important;font-weight:800!important;letter-spacing:0!important;margin:0!important}.audit-table td:first-child .audit-cell-title{font-size:.82rem!important;white-space:nowrap!important}.audit-cell-sub{display:block;margin-top:3px!important;color:var(--text-muted)!important;font-size:.62rem!important;line-height:1.1!important;font-weight:600}.audit-chip{display:inline-flex;align-items:center;gap:6px;min-height:26px!important;padding:5px 9px!important;border-radius:999px;border:1px solid var(--border);background:var(--surface-muted);color:var(--text);font-size:.74rem!important;font-weight:800;white-space:nowrap}.audit-chip svg{width:14px!important;height:14px!important}.audit-chip.create{color:#047857;background:#ecfdf5;border-color:#a7f3d0}.audit-chip.update,.audit-chip.status{color:var(--primary-dark);background:var(--primary-soft);border-color:color-mix(in srgb,var(--primary) 30%,transparent)}.audit-chip.delete{color:#dc2626;background:#fff1f2;border-color:#fecdd3}.audit-detail-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:30px!important;padding:6px 10px!important;border:1px solid var(--border);border-radius:999px;background:var(--surface-muted);color:var(--text);font-size:.74rem!important;font-weight:800;cursor:pointer;white-space:nowrap}.audit-detail-btn svg{width:14px!important;height:14px!important}.audit-detail-btn:hover{border-color:var(--primary);color:var(--primary-dark);background:var(--primary-soft)}.audit-empty{padding:24px;text-align:center;color:var(--text-muted);font-weight:700}.audit-modal[hidden]{display:none!important}.audit-modal{position:fixed;inset:0;z-index:1100;display:flex;align-items:center;justify-content:center;padding:24px;background:rgba(15,23,42,.46);backdrop-filter:blur(3px)}.audit-dialog{width:min(920px,100%);max-height:min(90vh,860px);display:flex;flex-direction:column;border:1px solid var(--border);border-radius:24px;background:var(--surface);box-shadow:0 24px 70px rgba(15,23,42,.24);overflow:hidden}.audit-dialog-header{display:flex;align-items:center;gap:12px;padding:22px 24px 16px;border-bottom:1px solid var(--border)}.audit-dialog-header h3{margin:0;color:var(--text);font-size:1.15rem}.audit-dialog-header p{margin:3px 0 0;color:var(--text-muted);font-weight:600}.audit-dialog-body{overflow:auto;padding:20px 24px;display:grid;gap:14px}.audit-detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.audit-detail-item{border:1px solid var(--border);border-radius:14px;padding:12px;background:var(--surface-muted);min-width:0}.audit-detail-item span{display:block;color:var(--text-muted);font-size:.72rem;font-weight:800;letter-spacing:.07em;text-transform:uppercase}.audit-detail-item strong{display:block;margin-top:5px;color:var(--text);overflow-wrap:anywhere}.audit-json{margin:0;max-height:280px;overflow:auto;border:1px solid var(--border);border-radius:14px;padding:14px;background:#0f172a;color:#e2e8f0;font-size:.8rem;line-height:1.55;white-space:pre-wrap;overflow-wrap:anywhere}.audit-json-old{color:#fca5a5}.audit-json-new{color:#86efac}.audit-change-summary{display:grid;gap:10px;margin-top:10px;max-height:320px;overflow:auto;padding-right:4px}.audit-change-row{display:grid;grid-template-columns:minmax(120px,.55fr) minmax(0,1fr) 34px minmax(0,1fr);gap:14px;align-items:start;padding:12px 14px;border:1px solid var(--border);border-radius:12px;background:var(--surface-muted);font-size:.78rem}.audit-change-row b{color:var(--text);overflow-wrap:anywhere;padding-top:2px}.audit-change-row span{color:var(--text-muted);overflow-wrap:anywhere;white-space:pre-line;line-height:1.35}.audit-change-row span:nth-of-type(1){text-align:right}.audit-change-row span:nth-of-type(2){text-align:left}.audit-change-row em{display:flex;align-items:flex-start;justify-content:center;font-style:normal;color:var(--primary);font-weight:900;text-align:center;padding-top:2px}.audit-dialog-footer{display:flex;justify-content:flex-end;padding:16px 24px 22px;border-top:1px solid var(--border)}.audit-close{min-width:96px;min-height:40px;border:0;border-radius:13px;background:var(--surface-muted);color:var(--text);font-weight:800;cursor:pointer}@media(max-width:1180px){.audit-table{min-width:900px!important}}@media(max-width:840px){.audit-summary,.audit-filters,.audit-detail-grid,.audit-change-row{grid-template-columns:1fr}.audit-change-row span:nth-of-type(1),.audit-change-row span:nth-of-type(2){text-align:left}.audit-change-row em{display:none}}
</style>
<script>
(function(){
    const rows = <?= json_encode($auditRows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    const auditStandalone = <?= $auditStandalone ? 'true' : 'false'; ?>;
    const actionLabels = {create:'Adicionou',update:'Mudou',delete:'Excluiu',status:'Mudou status',documento:'Gerou DOC',export:'Exportou',import:'Importou',planejamento:'Planejamento',movimento:'Movimenta\u00e7\u00e3o',iniciar:'Iniciou',finalizar:'Finalizou',baixa:'Baixa',pago:'Pago'};
    const actionIcons = {create:'plus-circle',update:'pencil',delete:'trash-2',status:'refresh-cw',documento:'file-text',export:'download',import:'upload',planejamento:'calendar-plus',movimento:'arrow-left-right',iniciar:'play-circle',finalizar:'check-circle-2',baixa:'badge-dollar-sign',pago:'circle-dollar-sign'};
    const entityLabels = {clientes:'Clientes',cliente:'Cliente',produtos:'Produtos',produto:'Produto',pedidos:'Pedidos',pedido:'Pedido',usuarios:'Usu\u00e1rios',usuario:'Usu\u00e1rio',configuracoes:'Configura\u00e7\u00f5es',config:'Configura\u00e7\u00f5es',estoque:'Estoque',receitas:'Receitas',producao:'Produ\u00e7\u00e3o',expedicao:'Expedi\u00e7\u00e3o',financeiro:'Financeiro',portal:'Portal',mapa:'Mapa',mapa_entradas:'Planejamento'};
    const text = (value,fallback='-') => { const t = String(value ?? '').trim(); return t || fallback; };
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g,(c)=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    const actionLabel = (action) => actionLabels[String(action||'').toLowerCase()] || text(action,'A\u00e7\u00e3o');
    const entityLabel = (entity) => entityLabels[String(entity||'').toLowerCase()] || text(entity,'Registro');
    const actionIcon = (action) => actionIcons[String(action||'').toLowerCase()] || 'activity';
    const dateParts = (value) => { const v = text(value,'N\u00e3o registrado'); const m = v.match(/^(\d{4}-\d{2}-\d{2})\s+(\d{2}:\d{2}(?::\d{2})?)$/); return m ? {date:m[1],time:m[2]} : {date:v,time:''}; };
    const detailKeys = (details) => (!details || typeof details !== 'object' || Array.isArray(details)) ? [] : Object.keys(details).filter((key)=>!['dispositivo','localidade','antes','depois','mudancas'].includes(key));
    const changeKeys = (details) => details && details.mudancas && typeof details.mudancas === 'object' && !Array.isArray(details.mudancas) ? Object.keys(details.mudancas) : [];
    const labelField = (key) => ({preco:'Pre\u00e7o',valor:'Valor',total:'Total',frete_valor:'Frete',desconto:'Desconto',desconto_tipo:'Tipo de desconto',desconto_valor_original:'Desconto informado',cliente_id:'Cliente',forma_pagto:'Pagamento',observacoes:'Observa\u00e7\u00f5es',status:'Status',itens:'Itens',nome:'Nome',sku:'SKU',categoria:'Categoria',tipo:'Tipo',unidade:'Unidade',peso_liquido:'Peso l\u00edquido',shelf_life_dias:'Validade',is_ativo:'Ativo',mostrar_portal:'Portal',produto_id:'Produto',produto_nome:'Produto',produto_sku:'SKU do produto',produto_label:'Produto',data_referencia:'Data',quantidade:'Quantidade',lote:'Lote',validade:'Validade',origem:'Origem',destino:'Destino',ref_tipo:'Refer\u00eancia',ref_id:'ID refer\u00eancia',registro:'Registro',observacao:'Observa\u00e7\u00e3o',custo_unit:'Custo unit\u00e1rio',usuario_id:'Usu\u00e1rio'}[key] || String(key||'Campo').replace(/_/g,' '));
    const moneyField = (key) => /(^preco$|valor|total|desconto|frete)/i.test(String(key||''));
    const itemNameMap = (change) => { const map = {}; [change?.antes, change?.depois].forEach((items)=>{ if(!Array.isArray(items)) return; items.forEach((item)=>{ const id = String(item?.produto_id || '').trim(); const name = text(item?.nome || item?.produto_nome || item?.sku, ''); if(id && name) map[id] = name; }); }); return map; };
    const itemName = (item, names = {}) => text(item?.nome || item?.produto_nome || item?.sku || names[String(item?.produto_id || '').trim()] || (item?.produto_id ? `Produto #${item.produto_id}` : ''), '');
    const itemQty = (item) => { const raw = Number(String(item?.quantidade ?? 1).replace(',','.')); return Number.isFinite(raw) && raw > 0 ? raw : 1; };
    const itemQtyText = (qty) => Number(qty || 0).toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 4 });
    const itemGroups = (items, names = {}) => { const groups = {}; items.forEach((item)=>{ const name = itemName(item, names); if(!name) return; const key = `${String(item?.produto_id || name)}|${name}`; if(!groups[key]) groups[key] = { name, linhas: 0, quantidade: 0 }; groups[key].linhas += 1; groups[key].quantidade += itemQty(item); }); return Object.values(groups); };
    const itemListText = (items, names = {}) => { const groups = itemGroups(items, names); const prefix = `${items.length} ${items.length===1?'item':'itens'}`; if(!groups.length) return prefix; return `${prefix}:\n${groups.map((group)=>`${itemQtyText(group.quantidade)}x ${group.name}${group.linhas > 1 ? ` (${group.linhas} lan\u00e7amentos)` : ''}`).join('\n')}`; };
    const valueText = (key,value,change = {}) => { if(String(key)==='produto_id' && change?.produto_label) return change.produto_label; if(value === null || value === undefined || value === '') return 'vazio'; if(Array.isArray(value)) return String(key) === 'itens' ? itemListText(value, itemNameMap(change)) : `${value.length} ${value.length===1?'item':'itens'}`; if(typeof value === 'object') return JSON.stringify(value); if(moneyField(key) && !Number.isNaN(Number(String(value).replace(',','.')))) return Number(String(value).replace(',','.')).toLocaleString('pt-BR',{style:'currency',currency:'BRL'}); return String(value); };
    const productName = (details) => text(details?.produto_label || details?.produto_nome || details?.nome || details?.depois?.produto_label || details?.depois?.produto_nome || details?.depois?.nome || details?.antes?.produto_label || details?.antes?.produto_nome || details?.antes?.nome, '');
    const singleValueRow = (label, value) => `<div class="audit-change-row"><b>${escapeHtml(label)}</b><span style="grid-column:2 / 5;text-align:left;">${escapeHtml(value)}</span></div>`;
    const flatSummary = (details, row = {}) => {
        const rows = [];
        rows.push(singleValueRow('A\u00e7\u00e3o', actionLabel(row.acao)));
        rows.push(singleValueRow('M\u00f3dulo', entityLabel(row.entidade)));
        const product = productName(details);
        if(product) rows.push(singleValueRow('Produto', product));
        const important = ['registro','tipo','data_referencia','quantidade','custo_unit','lote','validade','origem','destino','ref_tipo','ref_id','observacao','status'];
        important.forEach((key) => {
            if(!(key in (details || {}))) return;
            rows.push(singleValueRow(labelField(key), valueText(key, details[key], details)));
        });
        const extras = detailKeys(details).filter((key)=>!important.includes(key) && !['produto_id','produto_nome','produto_sku','produto_label','user_agent'].includes(key)).slice(0,6);
        extras.forEach((key)=>rows.push(singleValueRow(labelField(key), valueText(key, details[key], details))));
        return rows.join('') || '<div class="audit-change-row"><span>Nenhum detalhe adicional registrado.</span></div>';
    };
    const changeSummary = (details, row = {}) => { const changes = details && details.mudancas && typeof details.mudancas === 'object' ? details.mudancas : {}; const entries = Object.entries(changes); const isProduct = ['produto','produtos'].includes(String(row.entidade || '').toLowerCase()); const header = entries.length && (isProduct || productName(details)) && productName(details) ? singleValueRow('Produto', productName(details)) : ''; const body = entries.length ? entries.slice(0,10).map(([key,item])=>`<div class="audit-change-row"><b>${escapeHtml(labelField(key))}</b><span>${escapeHtml(valueText(key,item?.antes,item))}</span><em>&gt;</em><span>${escapeHtml(valueText(key,item?.depois,item))}</span></div>`).join('') : flatSummary(details, row); return header + body; };
    const jsonDepthDelta = (line) => (line.match(/[{\[]/g) || []).length - (line.match(/[}\]]/g) || []).length;
    const colorAuditJson = (details) => {
        const lines = String(JSON.stringify(details,null,2) || '{}').split('\n');
        let activeClass = '';
        let activeDepth = 0;
        return lines.map((line) => {
            const startsOld = /^\s*"antes"\s*:/.test(line);
            const startsNew = /^\s*"depois"\s*:/.test(line);
            let lineClass = activeClass;
            if (startsOld || startsNew) {
                activeClass = startsOld ? 'audit-json-old' : 'audit-json-new';
                lineClass = activeClass;
                activeDepth = Math.max(0, jsonDepthDelta(line));
                if (activeDepth === 0) {
                    activeClass = '';
                }
            } else if (activeClass) {
                lineClass = activeClass;
                activeDepth += jsonDepthDelta(line);
                if (activeDepth <= 0) {
                    activeClass = '';
                    activeDepth = 0;
                }
            }
            const safeLine = escapeHtml(line);
            return lineClass ? `<span class="${lineClass}">${safeLine}</span>` : safeLine;
        }).join('\n');
    };
    const summarize = (row) => { const changed = changeKeys(row.detalhes); const keys = changed.length ? changed : detailKeys(row.detalhes); const action = String(row.acao||'').toLowerCase(); const subject = productName(row.detalhes || {}); const suffix = subject ? ` - ${subject}` : ''; if(!keys.length) return `Sem detalhes adicionais${suffix}`; if(action==='update' || action==='status') return `Campos alterados: ${keys.slice(0,5).map(labelField).join(', ')}${suffix}`; if(action==='create') return `Dados adicionados: ${keys.slice(0,5).map(labelField).join(', ')}${suffix}`; if(action==='delete') return `Registro removido com ${keys.length} dado(s) gravado(s)${suffix}`; if(action==='movimento' || action==='planejamento') return `${actionLabel(action)}${suffix}`; return `Detalhes: ${keys.slice(0,5).map(labelField).join(', ')}${suffix}`; };
    const unique = (key) => Array.from(new Set(rows.map((row)=>String(row[key]||'').toLowerCase()).filter(Boolean))).sort();
    const matches = (row, search, action, entity) => { const all = [row.usuario,row.usuario_email,row.acao,row.entidade,row.entidade_id,row.ip,row.created_at,JSON.stringify(row.detalhes||{})].join(' ').toLowerCase(); return (!search || all.includes(search)) && (!action || String(row.acao||'').toLowerCase()===action) && (!entity || String(row.entidade||'').toLowerCase()===entity); };
    const activate = (tab) => { document.querySelectorAll('.tab-button').forEach((button)=>{ const active = button.dataset.tab === tab; button.classList.toggle('active',active); button.setAttribute('aria-selected',active?'true':'false'); }); document.querySelectorAll('.tab-panel').forEach((panel)=>panel.classList.toggle('active',panel.dataset.tabPanel===tab)); document.querySelectorAll('input[name="active_tab"]').forEach((input)=>{ input.value = tab; }); localStorage.setItem('configTab',tab); };
    const modal = () => { let node = document.querySelector('.audit-modal'); if(node) return node; node = document.createElement('div'); node.className='audit-modal'; node.hidden=true; node.innerHTML = `<div class="audit-dialog" role="dialog" aria-modal="true" aria-labelledby="audit-title"><div class="audit-dialog-header"><span class="audit-icon"><span data-lucide="history" aria-hidden="true"></span></span><div><h3 id="audit-title">Detalhes da auditoria</h3><p data-audit-subtitle>Registro completo da a\u00e7\u00e3o</p></div></div><div class="audit-dialog-body"><div class="audit-detail-grid" data-audit-grid></div><div><strong>Console</strong><pre class="audit-json" data-audit-json>{}</pre><strong style="display:block;margin-top:12px;">Resumo das mudan\u00e7as</strong><div class="audit-change-summary" data-audit-change-summary></div></div></div><div class="audit-dialog-footer"><button type="button" class="audit-close">Fechar</button></div></div>`; document.body.appendChild(node); const close=()=>{node.hidden=true;}; node.querySelector('.audit-close')?.addEventListener('click',close); node.addEventListener('click',(event)=>{ if(event.target===node) close(); }); document.addEventListener('keydown',(event)=>{ if(event.key==='Escape' && !node.hidden) close(); }); if(window.lucide) window.lucide.createIcons({elements:node.querySelectorAll('[data-lucide]')}); return node; };
    const openDetails = (row) => { const node = modal(); const details = row.detalhes && typeof row.detalhes === 'object' ? row.detalhes : {}; const d = dateParts(row.created_at); const fields = [['Usu\u00e1rio',text(row.usuario,'Sistema')],['E-mail',text(row.usuario_email,'N\u00e3o registrado')],['A\u00e7\u00e3o',actionLabel(row.acao)],['M\u00f3dulo/registro',`${entityLabel(row.entidade)} #${text(row.entidade_id,'-')}`],['Data',d.date],['Hor\u00e1rio',d.time||'N\u00e3o registrado'],['IP/localidade',text(details.localidade||row.ip,'N\u00e3o registrada')],['Dispositivo',text(details.dispositivo,'N\u00e3o registrado')]]; const grid = node.querySelector('[data-audit-grid]'); const subtitle = node.querySelector('[data-audit-subtitle]'); const json = node.querySelector('[data-audit-json]'); const changes = node.querySelector('[data-audit-change-summary]'); if(subtitle) subtitle.textContent = `${actionLabel(row.acao)} em ${entityLabel(row.entidade)}`; if(grid) grid.innerHTML = fields.map(([label,value])=>`<div class="audit-detail-item"><span>${escapeHtml(label)}</span><strong>${escapeHtml(value)}</strong></div>`).join(''); if(json) json.innerHTML = colorAuditJson(details); if(changes) changes.innerHTML = changeSummary(details, row); node.hidden=false; };
    const build = () => {
        if(!auditStandalone) { if(localStorage.getItem('configTab')==='audit') localStorage.setItem('configTab','rules'); const activePanel=document.querySelector('.tab-panel.active'); if(!activePanel){ document.querySelector('.tab-button[data-tab="rules"]')?.click(); } return; }
        if(document.querySelector('[data-tab-panel="audit"]')) return;
        const tabs = document.querySelector('.config-tabs'); const panels = document.querySelector('.tab-panels'); if(!tabs || !panels) return;
        document.querySelector('.config-header')?.remove();
        tabs.remove();
        document.querySelectorAll('.tab-panel').forEach((panel)=>{ panel.classList.remove('active'); panel.hidden = true; });
        const total = rows.length; const creates = rows.filter((row)=>String(row.acao||'').toLowerCase()==='create').length; const updates = rows.filter((row)=>['update','status'].includes(String(row.acao||'').toLowerCase())).length; const deletes = rows.filter((row)=>String(row.acao||'').toLowerCase()==='delete').length;
        const panel = document.createElement('div'); panel.className='card tab-panel active'; panel.dataset.tabPanel='audit'; panel.hidden = false; panel.innerHTML = `<div class="audit-header"><div class="audit-title"><span class="audit-icon"><span data-lucide="history" aria-hidden="true"></span></span><div><h2>Auditoria</h2><p class="card-subtitle">Veja quem adicionou, mudou ou excluiu registros e consulte os detalhes completos de cada a\u00e7\u00e3o.</p></div></div></div><div class="audit-summary"><div class="audit-card"><span>Total</span><strong>${total}</strong></div><div class="audit-card"><span>Adicionados</span><strong>${creates}</strong></div><div class="audit-card"><span>Alterados</span><strong>${updates}</strong></div><div class="audit-card"><span>Exclu\u00eddos</span><strong>${deletes}</strong></div></div><div class="audit-filters"><div class="field"><label for="audit-search">Buscar</label><input id="audit-search" type="search" placeholder="Usu\u00e1rio, m\u00f3dulo, IP, campo ou valor"></div><div class="field"><label for="audit-action">A\u00e7\u00e3o</label><select id="audit-action"><option value="">Todas</option></select></div><div class="field"><label for="audit-entity">M\u00f3dulo</label><select id="audit-entity"><option value="">Todos</option></select></div></div><div class="audit-table-wrap"><table class="audit-table"><thead><tr><th>Data</th><th>Usu\u00e1rio</th><th>A\u00e7\u00e3o</th><th>M\u00f3dulo</th><th>Sobre o que mudou</th><th>IP</th><th>Detalhes</th></tr></thead><tbody data-audit-body></tbody></table><div class="audit-empty" data-audit-empty hidden>Nenhum registro encontrado.</div></div>`; panels.appendChild(panel);
        const searchInput = panel.querySelector('#audit-search'); const actionSelect = panel.querySelector('#audit-action'); const entitySelect = panel.querySelector('#audit-entity'); const body = panel.querySelector('[data-audit-body]'); const empty = panel.querySelector('[data-audit-empty]');
        unique('acao').forEach((action)=>{ const option=document.createElement('option'); option.value=action; option.textContent=actionLabel(action); actionSelect?.appendChild(option); });
        unique('entidade').forEach((entity)=>{ const option=document.createElement('option'); option.value=entity; option.textContent=entityLabel(entity); entitySelect?.appendChild(option); });
        const render = () => { const search=String(searchInput?.value||'').trim().toLowerCase(); const action=String(actionSelect?.value||'').trim().toLowerCase(); const entity=String(entitySelect?.value||'').trim().toLowerCase(); const filtered=rows.filter((row)=>matches(row,search,action,entity)); if(empty) empty.hidden = filtered.length>0; if(!body) return; body.innerHTML = filtered.map((row,index)=>{ const d=dateParts(row.created_at); const action=String(row.acao||'').toLowerCase(); const details=row.detalhes||{}; return `<tr><td><strong class="audit-cell-title">${escapeHtml(d.date)}</strong>${d.time?`<small class="audit-cell-sub">${escapeHtml(d.time)}</small>`:''}</td><td><strong class="audit-cell-title">${escapeHtml(text(row.usuario,'Sistema'))}</strong><small class="audit-cell-sub">${escapeHtml(text(row.usuario_email,`ID ${text(row.usuario_id,'-')}`))}</small></td><td><span class="audit-chip ${escapeHtml(action)}"><span data-lucide="${actionIcon(action)}" aria-hidden="true"></span>${escapeHtml(actionLabel(action))}</span></td><td><strong class="audit-cell-title">${escapeHtml(entityLabel(row.entidade))}</strong><small class="audit-cell-sub">ID ${escapeHtml(text(row.entidade_id,'-'))}</small></td><td>${escapeHtml(summarize(row))}</td><td>${escapeHtml(text(details.localidade||row.ip,'N\u00e3o registrado'))}</td><td><button type="button" class="audit-detail-btn" data-audit-index="${index}"><span data-lucide="eye" aria-hidden="true"></span>Ver</button></td></tr>`; }).join(''); body.querySelectorAll('[data-audit-index]').forEach((btn)=>btn.addEventListener('click',()=>openDetails(filtered[Number(btn.dataset.auditIndex)]))); if(window.lucide) window.lucide.createIcons({elements:panel.querySelectorAll('[data-lucide]')}); };
        [searchInput,actionSelect,entitySelect].forEach((node)=>{ node?.addEventListener('input',render); node?.addEventListener('change',render); }); render();
        localStorage.setItem('configTab','audit');
        if(window.lucide) window.lucide.createIcons({elements:panel.querySelectorAll('[data-lucide]')});
    };
    if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded',build,{once:true}); else build();
})();
</script>
