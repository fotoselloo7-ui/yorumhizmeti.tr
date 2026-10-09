(()=>{
 'use strict';
 const root=document.getElementById('nvDesk');if(!root)return;
 let mode='chat',active=null,cache={chat:[],ticket:[],order:[]},cursor=null,loading=false;
 const $=id=>document.getElementById(id);
 const names={chat:['Canlı Sohbetler','Gelen konuşmalar'],ticket:['Destek Talepleri','Müşteri talepleri'],order:['Siparişler','Sipariş ve ödeme durumları']};
 const escape=str=>String(str??''); // DOM always uses textContent, never innerHTML.
 const dt=str=>str?String(str).replace('T',' ').slice(0,16):'';
 const sound=window.NvDeskAlerts;
 function el(tag,cls,txt){const n=document.createElement(tag);if(cls)n.className=cls;if(txt!==undefined)n.textContent=escape(txt);return n}
 function setLoading(msg){$('ndList').replaceChildren(el('div','nd-empty',msg))}
 function card(item){
  const b=el('button','nd-card');b.type='button';
  const name=mode==='chat'?item.name:mode==='ticket'?item.subject:item.order_number;
  const avatar=el('span','nd-avatar',escape(name).charAt(0).toUpperCase()||'N');
  const body=el('div','nd-card-body');body.append(el('strong','',name),el('p','',mode==='order'?((item.name||'Müşteri')+' · '+item.payment_status):(item.preview||item.name||'')));
  const tail=el('div','nd-card-right');tail.append(el('small','',dt(item.updated_at||item.created_at)),el('span','nd-pill',item.status||item.order_status||''));
  b.append(avatar,body,tail);b.addEventListener('click',()=>detail(mode,item.id));return b;
 }
 function showList(){
  active=null;$('ndConversation').hidden=true;$('ndList').hidden=false;
  $('ndTitle').textContent=names[mode][0];$('ndSubtitle').textContent=names[mode][1];
  document.querySelectorAll('[data-tab]').forEach(b=>b.classList.toggle('active',b.dataset.tab===mode));
  const rows=cache[mode]||[];
  $('ndList').replaceChildren(...(rows.length?rows.map(card):[el('div','nd-empty','Henüz kayıt yok')]));
 }
 async function reload(){
  if(loading)return;loading=true;
  try {
    const r=await fetch('/admin/cep/veri',{credentials:'same-origin',cache:'no-store',headers:{Accept:'application/json'}});
    if(r.status===401||r.redirected){location.assign('/admin/giris');return}
    if(!r.ok)throw Error('Bağlantı kurulamadı.');
    const json=await r.json();
    if(!json.ok)throw Error(json.error||'Veri alınamadı');
    if(cursor){
       let kind='';
       if(json.cursor.chat>cursor.chat)kind='chat';
       else if(json.cursor.ticket>cursor.ticket)kind='ticket';
       if(kind){
         sound?.play(kind==='chat'?root.dataset.soundNew:root.dataset.soundReply);
         sound?.notice(kind==='chat'?'Yeni canlı destek mesajı':'Yeni destek talebi','Yeni yanıt var.');
       }
    }
    cursor=json.cursor;
    cache={chat:json.inbox||[],ticket:json.tickets||[],order:json.orders||[]};
    ['chat','ticket','order'].forEach(k=>$(k==='chat'?'ndChatCount':k==='ticket'?'ndTicketCount':'ndOrderCount').textContent=String(cache[k].length));
    $('ndOnline').textContent='Bağlı · '+new Date().toLocaleTimeString('tr-TR',{hour:'2-digit',minute:'2-digit'});
    if(!active)showList();
  }catch(error){$('ndOnline').textContent='Bağlantı kontrol ediliyor';if(!active)setLoading('Bağlantı tekrar deneniyor');}
  finally{loading=false}
 }
 async function detail(type,id){
  active={type,id};
  $('ndList').hidden=true;$('ndConversation').hidden=false;
  $('ndThread').replaceChildren(el('div','nd-loading','Yükleniyor…'));
  $('ndFeedback').textContent='';$('ndReply').hidden=type==='order';
  try{
    const r=await fetch('/admin/cep/kayit/'+type+'/'+encodeURIComponent(id),{credentials:'same-origin',cache:'no-store'});
    if(r.status===401||r.redirected){location.assign('/admin/giris');return}
    const data=await r.json();if(!r.ok||!data.ok)throw Error(data.error||'Kayıt okunamadı');
    const item=data.item;
    $('ndDetailTitle').textContent=type==='chat'?item.visitor_name:type==='ticket'?item.subject:item.order_number;
    $('ndDetailSubtitle').textContent=type==='chat'?(item.visitor_contact||''):type==='ticket'?item.ticket_number:(item.visitor_name||'');
    $('ndDetailStatus').textContent=item.status||item.order_status||'';
    const nodes=(data.messages||[]).map(m=>{
      const who=m.sender||'';
      const box=el('div','nd-message '+(who==='admin'?'admin':''));
      box.append(el('span','',type==='order'?(m.package_name+' × '+m.quantity+' — '+Number(m.total||0).toLocaleString('tr-TR')+' ₺'):m.message),
                 el('small','',dt(m.created_at)));
      return box;
    });
    if(type==='order')nodes.unshift(el('div','nd-message',
      (item.visitor_name||'Müşteri')+'\nÖdeme: '+item.payment_status+'\nTutar: '+Number(item.total_amount||0).toLocaleString('tr-TR')+' ₺'));
    $('ndThread').replaceChildren(...nodes);
    $('ndThread').scrollTop=$('ndThread').scrollHeight;
  }catch(e){$('ndThread').replaceChildren(el('div','nd-empty',e.message))}
 }
 document.querySelectorAll('[data-tab]').forEach(b=>b.addEventListener('click',()=>{mode=b.dataset.tab;showList()}));
 $('ndBack').addEventListener('click',showList);
 $('ndRefresh').addEventListener('click',()=>{reload();if(active)detail(active.type,active.id)});
 $('ndAlertEnable').addEventListener('click',async()=>{await sound?.enable();sound?.play(root.dataset.soundNew);$('ndAlertEnable').title='Bildirimler etkin';});
 $('ndReply').addEventListener('submit',async event=>{
   event.preventDefault();if(!active||active.type==='order')return;
   const form=event.currentTarget;
   const button=form.querySelector('button');button.disabled=true;$('ndFeedback').textContent='';
   const body=new FormData(form);body.append('_csrf_token',root.dataset.csrf);
   try{
     const r=await fetch('/admin/cep/yanit/'+active.type+'/'+active.id,{method:'POST',credentials:'same-origin',body,cache:'no-store'});
     if(r.status===401||r.redirected){location.assign('/admin/giris');return}
     const data=await r.json();if(!r.ok||!data.ok)throw Error(data.error||'Mesaj iletilemedi');
     form.reset();await detail(active.type,active.id);reload();
   }catch(error){$('ndFeedback').textContent=error.message}
   finally{button.disabled=false}
 });
 if('serviceWorker' in navigator)navigator.serviceWorker.register('/nv-desk-sw.js',{scope:'/admin/cep'}).catch(()=>{});
 setInterval(()=>{if(!document.hidden)reload()},4500);
 reload();
})();