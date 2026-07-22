<script>
(()=>{
    const form=document.querySelector('[data-delivery-form]');
    const canvas=form?.querySelector('[data-delivery-canvas]');
    const hidden=form?.elements.assinatura_recebedor;
    if(!form||!canvas||!hidden)return;
    let frame=0;
    const snapshot=()=>{
        frame=0;
        try{if(canvas.width>1&&canvas.height>1)hidden.value=canvas.toDataURL('image/png')}catch(error){}
    };
    const schedule=()=>{if(!frame)frame=requestAnimationFrame(snapshot)};
    const restore=()=>{
        if(!hidden.value.startsWith('data:image/png;base64,'))return;
        const image=new Image();
        image.onload=()=>{
            const rect=canvas.getBoundingClientRect();
            if(rect.width<2||rect.height<2)return;
            const ratio=devicePixelRatio||1,ctx=canvas.getContext('2d');
            canvas.width=Math.round(rect.width*ratio);canvas.height=Math.round(rect.height*ratio);
            ctx.setTransform(1,0,0,1,0,0);ctx.drawImage(image,0,0,canvas.width,canvas.height);
            ctx.setTransform(ratio,0,0,ratio,0,0);ctx.strokeStyle='#111827';ctx.lineWidth=2.2;ctx.lineCap='round';ctx.lineJoin='round';
        };
        image.src=hidden.value;
    };
    canvas.addEventListener('pointermove',event=>{if(event.buttons||event.pressure>0)schedule()},true);
    canvas.addEventListener('pointerup',()=>{snapshot();[40,120,260].forEach(delay=>setTimeout(restore,delay))},true);
    canvas.addEventListener('pointercancel',()=>{snapshot();[40,120,260].forEach(delay=>setTimeout(restore,delay))},true);
    new ResizeObserver(()=>{if(hidden.value)setTimeout(restore,30)}).observe(canvas.closest('.entrega-signature'));
})();
</script>
