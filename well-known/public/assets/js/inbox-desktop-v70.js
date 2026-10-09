(()=>{
  'use strict';
  const el=document.querySelector('[data-nv70-chatdesk]');
  if(!el)return;
  const list=el.querySelector('[data-nv70-chats]');
  const title=el.querySelector('[data-nv70-title]');
  const subtitle=el.querySelector('[data-nv70-subtitle]');
  const thread=el.querySelector('[data-nv70-thread]');
  const form=el.querySelector('[data-nv70-form]');
  const status=el.querySelector('[data-nv70-status]');
  const input=form?.querySelector('textarea[name="message"]');
  const submit=form?.querySelector('button[type="submit"]');
  let selected=null,busy=false,signature='',polling=false;
  const text=(tag,className,value)=>{
    const element=document.createElement(tag);
    element.className=className;element.textContent=String(value??'');
    return element;
  };
  const api=id=>'/admin/netvera-gelen-kutusu/sohbet/'+encodeURIComponent(id);
  async function load(){
    if(!selected||busy||polling||document.hidden)return;
    polling=true;
    const expected=selected;
    try{
      const r=await fetch(api(expected)+'/mesajlar',{cache:'no-store',credentials:'same-origin',headers:{Accept:'application/json'}});
      if(r.redirected || r.status===401){status.textContent='Oturum süresi doldu, sayfayı yenileyin.';return;}
      const data=await r.json();
      if(expected!==selected)return;
      if(!r.ok||!data.ok)throw new Error(data.message||'Konuşma açılamadı.');
      title.textContent=data.chat.visitor_name||'Müşteri';
      subtitle.textContent=[data.chat.visitor_contact,data.chat.status].filter(Boolean).join(' · ');
      const rows=data.messages||[];
      const sig=rows.length+'-'+(rows.at(-1)?.sender||'')+'-'+(rows.at(-1)?.created_at||'')+'-'+(rows.at(-1)?.message||'');
      if(sig!==signature){
        signature=sig;
        const nodes=rows.map(row=>{
          const container=text('div','nv70-message '+(row.sender==='admin'?'out':'in'),'');
          container.append(text('span','',row.message),text('small','',row.created_at));
          return container;
        });
        thread.replaceChildren(...nodes);
        thread.scrollTop=thread.scrollHeight;
      }
      form.hidden=false;
      status.textContent='';
    }catch(e){status.textContent=e.message||'Mesajlar yüklenemedi.'}
    finally{polling=false}
  }
  function choose(button){
    selected=button.dataset.chatId;
    signature='';
    list.querySelectorAll('[data-chat-id]').forEach(b=>b.classList.toggle('active',b===button));
    title.textContent=button.dataset.chatName||'Sohbet';
    subtitle.textContent=button.dataset.chatContact||'';
    thread.replaceChildren(text('div','nv70-loading','Mesajlar yükleniyor…'));
    form.hidden=false;
    load();
  }
  list.querySelectorAll('[data-chat-id]').forEach(b=>b.addEventListener('click',()=>choose(b)));
  form?.addEventListener('submit',async ev=>{
    ev.preventDefault();
    if(!selected||busy)return;
    const message=input.value.trim();
    if(message.length<2||message.length>3000)return;
    busy=true;submit.disabled=true;status.textContent='';
    try{
      const data=new FormData(form);
      const r=await fetch(api(selected)+'/yanit',{method:'POST',body:data,credentials:'same-origin',cache:'no-store',headers:{Accept:'application/json'}});
      const answer=await r.json();
      if(!r.ok||!answer.ok)throw new Error(answer.message||'Mesaj gönderilemedi.');
      input.value='';
      signature='';busy=false;await load();input.focus();
    }catch(e){status.textContent=e.message||'Gönderim başarısız.'}
    finally{busy=false;submit.disabled=false}
  });
  const first=list.querySelector('[data-chat-id]');
  if(first)choose(first);
  setInterval(()=>{if(selected)load()},4500);
})();