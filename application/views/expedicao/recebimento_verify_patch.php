<style>
.entrega-pad canvas{position:absolute;z-index:2;inset:0;display:block;width:100%;height:100%;touch-action:none;cursor:crosshair;user-select:none}
</style>
<script>
(()=>{
    const form=document.querySelector('.entrega-form');
    const original=form?.querySelector('[data-entrega-svg]');
    const hidden=form?.elements.assinatura_recebedor;
    const name=form?.elements.recebedor_nome;
    const submit=form?.querySelector('[data-entrega-submit]');
    const originalPad=form?.querySelector('.entrega-pad');
    if(!form||!original||!hidden||!name||!submit||!originalPad)return;

    const pad=originalPad.cloneNode(true);
    originalPad.replaceWith(pad);
    const cleanOriginal=pad.querySelector('[data-entrega-svg]');

    const canvas=document.createElement('canvas');
    canvas.dataset.entregaSignature='recebedor';
    cleanOriginal.replaceWith(canvas);
    const ctx=canvas.getContext('2d');
    let drawing=false;
    let ink=false;

    const configure=()=>{
        const rect=canvas.getBoundingClientRect();
        if(rect.width<2||rect.height<2)return;
        const ratio=window.devicePixelRatio||1;
        const saved=hidden.value;
        canvas.width=Math.round(rect.width*ratio);
        canvas.height=Math.round(rect.height*ratio);
        ctx.setTransform(ratio,0,0,ratio,0,0);
        ctx.strokeStyle='#111827';
        ctx.lineWidth=2.2;
        ctx.lineCap='round';
        ctx.lineJoin='round';
        if(saved){
            const image=new Image();
            image.onload=()=>{
                ctx.save();
                ctx.setTransform(1,0,0,1,0,0);
                ctx.drawImage(image,0,0,canvas.width,canvas.height);
                ctx.restore();
                ctx.setTransform(ratio,0,0,ratio,0,0);
            };
            image.src=saved;
        }
    };
    const point=event=>{const rect=canvas.getBoundingClientRect();return{x:event.clientX-rect.left,y:event.clientY-rect.top}};
    const persist=()=>{
        if(!ink)return false;
        hidden.value=canvas.toDataURL('image/png');
        update();
        return true;
    };
    const clear=()=>{
        ctx.save();ctx.setTransform(1,0,0,1,0,0);ctx.clearRect(0,0,canvas.width,canvas.height);ctx.restore();
        hidden.value='';drawing=false;ink=false;pad.classList.remove('has-ink');update();
    };
    const update=()=>{submit.disabled=!(name.value.trim()&&hidden.value.startsWith('data:image/png;base64,'))};

    canvas.addEventListener('pointerdown',event=>{
        event.preventDefault();drawing=true;ink=true;pad.classList.add('has-ink');
        const p=point(event);ctx.beginPath();ctx.moveTo(p.x,p.y);ctx.lineTo(p.x+.1,p.y+.1);ctx.stroke();
        try{canvas.setPointerCapture(event.pointerId)}catch(error){}
    });
    canvas.addEventListener('pointermove',event=>{if(!drawing)return;event.preventDefault();const p=point(event);ctx.lineTo(p.x,p.y);ctx.stroke()});
    const finish=event=>{if(!drawing)return;drawing=false;persist();try{canvas.releasePointerCapture(event.pointerId)}catch(error){}};
    canvas.addEventListener('pointerup',finish);
    canvas.addEventListener('pointercancel',finish);
    canvas.addEventListener('pointerleave',event=>{if(drawing&&event.buttons===0)finish(event)});
    name.addEventListener('input',update);
    form.querySelector('[data-entrega-clear]')?.addEventListener('click',clear);
    document.querySelectorAll('[data-entrega-id]').forEach(button=>button.addEventListener('click',()=>{clear();requestAnimationFrame(()=>requestAnimationFrame(configure))}));
    window.addEventListener('resize',()=>{if(!drawing)configure()});
    configure();update();

    document.addEventListener('submit',async event=>{
        if(event.target!==form)return;
        event.preventDefault();event.stopImmediatePropagation();
        if(drawing){drawing=false;persist()}
        if(!name.value.trim()||!hidden.value){window.showAppToast?.('Informe o recebedor e faça a assinatura.','error');return}
        if(form.dataset.confirming==='1')return;
        form.dataset.confirming='1';submit.disabled=true;
        try{
            const response=await fetch(form.action,{method:'POST',credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},body:new FormData(form)});
            const data=await response.json().catch(()=>({}));
            if(!response.ok)throw new Error(data.message||'Não foi possível confirmar a entrega.');
            if(data.confirmado!==true||data.assinatura_salva!==true||data.status!=='entregue'||!data.recebedor_nome||!data.data_recebimento)throw new Error('O servidor não confirmou todos os dados da entrega.');
            window.showAppToast?.('Assinatura salva com sucesso.','success');
            setTimeout(()=>location.assign(`${location.pathname}?tab=concluidos`),350);
        }catch(error){
            window.showAppToast?.(error.message||'Não foi possível confirmar a entrega. Tente novamente.','error');
        }finally{
            delete form.dataset.confirming;update();
        }
    },true);
})();
</script>
