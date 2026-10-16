(function(){
 const root=document.querySelector('[data-nv-chat]');
 if(!root)return;
 const toggle=root.querySelector('[data-nv-chat-toggle]');
 const panel=root.querySelector('.nv-chat-panel');
 const toggles=[...document.querySelectorAll('[data-nv-chat-toggle], [data-nv-chat-mobile-toggle]')];
 const close=root.querySelector('[data-nv-chat-close]');
 const form=root.querySelector('[data-nv-chat-form]');
 const status=root.querySelector('[data-nv-chat-status]');
 const list=root.querySelector('[data-nv-chat-messages]');
 const identity=root.querySelector('[data-nv-chat-identity]');
 const idInput=root.querySelector('[data-nv-chat-id]');
 let pending=false;
 let latestCount=null;
 let soundReady=false;
 function open(state){
   panel.hidden=!state;
   toggle.hidden=state;
   toggles.forEach(button=>button.setAttribute('aria-expanded',String(state)));
   if(state){soundReady=window.NvDeskAlerts?.unlock()||false;refresh();}
 }
 toggles.forEach(button=>button.addEventListener('click',()=>open(panel.hidden)));
 function dismiss(){
   open(false);
   const launcher=window.matchMedia('(max-width:768px)').matches
     ?document.querySelector('[data-nv-chat-mobile-toggle]'):toggle;
   launcher?.focus();
 }
 close.addEventListener('click',dismiss);
 document.addEventListener('keydown',event=>{
   if(event.key==='Escape'&&!panel.hidden){event.preventDefault();dismiss();}
 });
 function bubble(message,who){
   const element=document.createElement('div');
   element.className='nv-chat-bubble '+(who==='admin'?'staff':'visitor');
   element.textContent=message;
   list.appendChild(element);
 }
 async function refresh(){
   const id=Number(idInput.value||0);
   if(!id||panel.hidden||pending)return;
   try{
     const response=await fetch('/netvera/canli-destek/mesajlar?id='+id,{credentials:'same-origin',cache:'no-store'});
     if(!response.ok)return;
     const payload=await response.json();
     if(!payload.ok)return;
     const messages=payload.messages||[];
     const prior=latestCount;
     if(prior!==null && messages.length>prior && messages.slice(prior).some(item=>item.sender==='admin') && soundReady){
        window.NvDeskAlerts?.play('soft');
     }
     latestCount=messages.length;
     list.textContent='';
     for(const item of messages)bubble(item.message,item.sender);
     list.scrollTop=list.scrollHeight;
   }catch(e){status.textContent='Mesajlar güncellenemedi. Bağlantıyı kontrol edin.';}
 }
 form.addEventListener('submit',async event=>{
   event.preventDefault();if(pending)return;
   pending=true;status.textContent='Gönderiliyor...';
   const button=form.querySelector('[type=submit]');button.disabled=true;
   try{
     const response=await fetch(form.action,{method:'POST',credentials:'same-origin',body:new FormData(form)});
     const payload=await response.json();
     if(!response.ok||!payload.ok)throw new Error(payload.error||'Mesaj gönderilemedi');
     idInput.value=String(payload.inquiry_id||idInput.value);
     identity.hidden=true;
     identity.querySelectorAll('input').forEach(i=>i.required=false);
     form.querySelector('textarea').value='';
     status.textContent=payload.message||'Mesajınız kaydedildi.';
     await refresh();
   }catch(error){status.textContent=error.message||'Bağlantı hatası.';}
   finally{pending=false;button.disabled=false;refresh();}
 });
 if(Number(idInput.value)>0){identity.querySelectorAll('input').forEach(i=>i.required=false);}
 setInterval(()=>{if(!panel.hidden)refresh()},5000);
}());
