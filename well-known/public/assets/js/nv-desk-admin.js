(()=>{
  'use strict';
  const bell=document.getElementById('nv68AdminBell');
  if(!bell)return;
  let cursor=null,fetching=false,enabled=false;
  const alerts=window.NvDeskAlerts;
  bell.addEventListener('click',async()=>{
    await alerts?.enable();
    enabled=true;bell.classList.add('nv68-bell-enabled');
    bell.title='Bildirimler açık (sekme açıkken)';
    alerts?.play('soft');
  });
  async function check(){
    if(!enabled||fetching||document.hidden)return;
    fetching=true;
    try{
       const r=await fetch('/admin/cep/veri',{cache:'no-store',credentials:'same-origin'});
       if(r.status===401||r.redirected)return;
       if(!r.ok)return;
       const data=await r.json();
       const next=data.cursor;
       if(!next)return;
       if(cursor){
          const chat=next.chat>cursor.chat, ticket=next.ticket>cursor.ticket;
          if(chat||ticket){
             alerts?.play(chat?bell.dataset.soundNew:bell.dataset.soundReply);
             alerts?.notice(chat?'Yeni sohbet mesajı':'Yeni destek yanıtı','Destek merkezini kontrol edin.');
             bell.classList.add('nv68-bell-flash');
             setTimeout(()=>bell.classList.remove('nv68-bell-flash'),2100);
          }
       }
       cursor=next;
    }catch(e){}
    finally{fetching=false}
  }
  setInterval(check,4500);
})();