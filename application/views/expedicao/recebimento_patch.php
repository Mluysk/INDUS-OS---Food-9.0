<section class="panel expedicao-panel" data-tab-panel="recebimento" hidden>
    <div class="expedicao-heading"><div><h2>Confirmação de entrega</h2><p>Selecione uma entrega própria e registre a assinatura do destinatário.</p></div></div>
    <div class="expedicao-table-wrap">
        <table class="data-table expedicao-table entrega-table">
            <thead><tr><th>Pedido</th><th>Loja</th><th>Romaneio</th><th>Volumes</th><th>Expedido em</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php if (!empty($aguardandoRecebimento)): foreach ($aguardandoRecebimento as $item):
                $pedidoEntrega = $item['pedido'] ?? [];
                $lojaEntrega = trim((string)($pedidoEntrega['cliente_nome_fantasia'] ?? '')) ?: trim((string)($pedidoEntrega['cliente_razao_social'] ?? ''));
                $dataEntrega = !empty($item['data_expedicao']) ? strtotime((string)$item['data_expedicao']) : false;
                $entregaId = (int)$item['id'];
            ?>
                <tr>
                    <td><strong><?= $esc($pedidoEntrega['codigo'] ?? '#'.($pedidoEntrega['id'] ?? 0)); ?></strong></td>
                    <td><span class="entrega-loja"><?= $esc($lojaEntrega ?: 'Loja não identificada'); ?></span></td>
                    <td><?= $esc($item['romaneio'] ?? '—'); ?></td>
                    <td><?= (int)($item['volumes'] ?? 1); ?></td>
                    <td><?php if ($dataEntrega): ?><span class="expedicao-date"><strong><?= date('Y-m-d', $dataEntrega); ?></strong><small><?= date('H:i:s', $dataEntrega); ?></small></span><?php else: ?>—<?php endif; ?></td>
                    <td><span class="entrega-status">Expedido</span></td>
                    <td><button type="button" class="button button-primary" data-open-delivery="<?= $entregaId; ?>">Entregar</button></td>
                </tr>
                <tr class="entrega-inline-row" data-delivery-row="<?= $entregaId; ?>" hidden>
                    <td colspan="7">
                        <form class="entrega-inline-form" action="<?= $esc($url('expedicao/recebimento').'/'.$entregaId); ?>">
                            <div class="entrega-inline-head"><div><h3>Confirmar entrega</h3><p>Informe quem recebeu e assine sobre a linha.</p></div><button type="button" class="button button-outline" data-close-delivery>Fechar</button></div>
                            <label>Nome do recebedor<input type="text" name="recebedor_nome" maxlength="120" autocomplete="name" required></label>
                            <label>Assinatura do recebedor
                                <span class="entrega-canvas-box"><canvas data-delivery-canvas></canvas><i></i><button type="button" class="button button-outline" data-clear-delivery>Limpar</button></span>
                                <input type="hidden" name="assinatura_recebedor">
                            </label>
                            <div class="entrega-inline-actions"><button type="button" class="button button-outline" data-close-delivery>Cancelar</button><button type="submit" class="button button-primary" disabled>Confirmar entrega</button></div>
                        </form>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="7" class="expedicao-empty">Nenhuma entrega própria aguardando confirmação.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<style>
.entrega-table th,.entrega-table td{text-align:center!important;vertical-align:middle!important}.entrega-table th{font-size:10.6px!important}.entrega-table td{font-size:13px!important;white-space:nowrap}.entrega-table :is(th,td):nth-child(2){width:170px;max-width:170px}.entrega-loja{display:-webkit-box;height:32px;overflow:hidden;-webkit-box-orient:vertical;-webkit-line-clamp:2;line-height:16px;white-space:normal;overflow-wrap:anywhere}.entrega-status{display:inline-flex;padding:6px 10px;border-radius:999px;background:#e0f2fe;color:#0369a1;font-size:12px;font-weight:850}.entrega-inline-row>td{padding:0 14px 18px!important;background:color-mix(in srgb,var(--primary) 3%,#fff)}.entrega-inline-form{display:grid;gap:16px;max-width:720px;margin:0 auto;padding:22px;border:1px solid color-mix(in srgb,var(--primary) 25%,var(--border));border-radius:18px;background:#fff;text-align:left!important;box-shadow:0 12px 32px rgba(15,23,42,.08)}.entrega-inline-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px}.entrega-inline-head h3{margin:0 0 4px}.entrega-inline-head p{margin:0;color:var(--text-muted)}.entrega-inline-form label{display:grid;gap:8px;color:var(--text);font-weight:800;text-align:left}.entrega-inline-form input[type=text]{width:100%;min-height:50px;padding:0 14px;border:1px solid var(--border);border-radius:13px;font:inherit}.entrega-canvas-box{position:relative;display:block;height:220px;overflow:hidden;border:1px dashed color-mix(in srgb,var(--primary) 55%,var(--border));border-radius:15px;background:#fff}.entrega-canvas-box.has-ink{border-color:var(--primary)}.entrega-canvas-box canvas{position:absolute;z-index:2;inset:0;display:block;width:100%;height:100%;touch-action:none;cursor:crosshair;user-select:none}.entrega-canvas-box i{position:absolute;z-index:1;left:9%;right:9%;bottom:28%;height:1px;background:#94a3b8}.entrega-canvas-box button{position:absolute;z-index:3;right:9px;top:9px}.entrega-inline-actions{display:flex;justify-content:flex-end;gap:10px}@media(max-width:700px){.entrega-inline-form{padding:16px}.entrega-canvas-box{height:190px}.entrega-inline-actions{display:grid;grid-template-columns:1fr 1fr}}
</style>

<script>
(()=>{
    const configure=form=>{
        if(form.dataset.ready==='1')return;
        form.dataset.ready='1';
        const canvas=form.querySelector('[data-delivery-canvas]'),box=canvas.closest('.entrega-canvas-box'),hidden=form.elements.assinatura_recebedor,name=form.elements.recebedor_nome,submit=form.querySelector('[type="submit"]'),ctx=canvas.getContext('2d');
        let drawing=false,ink=false;
        const resize=()=>{const rect=canvas.getBoundingClientRect();if(rect.width<2||rect.height<2)return;const ratio=devicePixelRatio||1;canvas.width=Math.round(rect.width*ratio);canvas.height=Math.round(rect.height*ratio);ctx.setTransform(ratio,0,0,ratio,0,0);ctx.strokeStyle='#111827';ctx.lineWidth=2.2;ctx.lineCap='round';ctx.lineJoin='round'};
        const update=()=>{submit.disabled=!(name.value.trim()&&hidden.value)};
        const save=()=>{if(ink)hidden.value=canvas.toDataURL('image/png');update()};
        const point=e=>{const rect=canvas.getBoundingClientRect();return{x:e.clientX-rect.left,y:e.clientY-rect.top}};
        const clear=()=>{ctx.save();ctx.setTransform(1,0,0,1,0,0);ctx.clearRect(0,0,canvas.width,canvas.height);ctx.restore();hidden.value='';drawing=false;ink=false;box.classList.remove('has-ink');update()};
        resize();name.addEventListener('input',update);
        canvas.addEventListener('pointerdown',e=>{e.preventDefault();drawing=true;ink=true;box.classList.add('has-ink');const p=point(e);ctx.beginPath();ctx.moveTo(p.x,p.y);ctx.lineTo(p.x+.1,p.y+.1);ctx.stroke();try{canvas.setPointerCapture(e.pointerId)}catch(error){}});
        canvas.addEventListener('pointermove',e=>{if(!drawing)return;e.preventDefault();const p=point(e);ctx.lineTo(p.x,p.y);ctx.stroke()});
        const finish=e=>{if(!drawing)return;drawing=false;save();try{canvas.releasePointerCapture(e.pointerId)}catch(error){}};
        canvas.addEventListener('pointerup',finish);canvas.addEventListener('pointercancel',finish);window.addEventListener('pointerup',finish,true);
        form.querySelector('[data-clear-delivery]').addEventListener('click',clear);
        form.querySelectorAll('[data-close-delivery]').forEach(button=>button.addEventListener('click',()=>{form.closest('[data-delivery-row]').hidden=true;clear();form.reset()}));
        form.addEventListener('submit',async event=>{event.preventDefault();event.stopImmediatePropagation();save();if(!name.value.trim()||!hidden.value){window.showAppToast?.('Informe o recebedor e faça a assinatura.','error');return}if(form.dataset.saving==='1')return;form.dataset.saving='1';submit.disabled=true;try{const response=await fetch(form.action,{method:'POST',credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},body:new FormData(form)}),data=await response.json().catch(()=>({}));if(!response.ok)throw new Error(data.message||'Não foi possível confirmar a entrega.');if(data.confirmado!==true||data.assinatura_salva!==true||data.status!=='entregue')throw new Error('O servidor não confirmou a assinatura.');window.showAppToast?.('Assinatura salva com sucesso.','success');setTimeout(()=>location.assign(`${location.pathname}?tab=concluidos`),350)}catch(error){window.showAppToast?.(error.message||'Não foi possível confirmar a entrega.','error');update()}finally{delete form.dataset.saving;update()}},true);
    };
    document.querySelectorAll('[data-open-delivery]').forEach(button=>button.addEventListener('click',()=>{const row=document.querySelector(`[data-delivery-row="${CSS.escape(button.dataset.openDelivery)}"]`);document.querySelectorAll('[data-delivery-row]').forEach(item=>{if(item!==row)item.hidden=true});row.hidden=false;const form=row.querySelector('form');configure(form);requestAnimationFrame(()=>{window.dispatchEvent(new Event('resize'));form.elements.recebedor_nome.focus();row.scrollIntoView({behavior:'smooth',block:'center'})})}));
})();
</script>
