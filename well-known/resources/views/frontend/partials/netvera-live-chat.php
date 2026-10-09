<?php
$nvCurrentChat=max(0,(int)($_SESSION['nv_active_chat_id']??0));
if($nvCurrentChat>0 && !\App\Services\NetveraInquiryService::visibleToVisitor($nvCurrentChat))
    $nvCurrentChat=0;
?>
<div class="nv-chat" data-nv-chat>
 <button type="button" class="nv-chat-toggle" data-nv-chat-toggle aria-controls="nv-chat-panel" aria-expanded="false">
   <?= icon('message-circle',20) ?> <span>Canlı Destek</span>
 </button>
 <section class="nv-chat-panel" id="nv-chat-panel" aria-label="NetVera canlı destek" hidden>
   <header class="nv-chat-header">
     <div><strong><?= icon('headphones',18) ?> NetVera Destek</strong><small>Mesajınızı bırakın, buradan yanıtlayalım.</small></div>
     <button type="button" data-nv-chat-close aria-label="Sohbet penceresini kapat"><?= icon('x',18) ?></button>
   </header>
   <div class="nv-chat-messages" data-nv-chat-messages aria-live="polite">
     <div class="nv-chat-bubble staff">Merhaba! Yazılımlarımız ve hizmetlerimiz hakkında yardımcı olabiliriz.</div>
   </div>
   <p class="nv-chat-status" data-nv-chat-status role="status"></p>
   <form method="post" action="/netvera/canli-destek/gonder" data-nv-chat-form>
     <?= csrfField() ?>
     <input type="hidden" name="transport" value="json">
     <input type="hidden" name="source_type" value="chat">
     <input type="hidden" name="inquiry_id" value="<?= $nvCurrentChat ?>" data-nv-chat-id>
     <input type="text" tabindex="-1" autocomplete="off" name="website" class="nv-chat-honeypot" aria-hidden="true">
     <div class="nv-chat-identity" data-nv-chat-identity <?= $nvCurrentChat?'hidden':'' ?>>
       <label>Adınız<input name="name" autocomplete="name" maxlength="140" required placeholder="Adınız"></label>
       <label>Telefon Numaranız<input name="phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="30" minlength="10" required placeholder="05xx xxx xx xx"></label>
     </div>
     <label class="nv-chat-compose">Mesajınız
       <textarea name="message" rows="2" minlength="5" maxlength="3000" required placeholder="Size nasıl yardımcı olabiliriz?"></textarea>
     </label>
     <button type="submit" class="nv-chat-send"><?= icon('send',15) ?> Gönder</button>
   </form>
   <footer>Bilgileriniz yalnızca talebinizi yanıtlamak için kullanılır. <a href="/sayfa/kvkk">KVKK</a></footer>
 </section>
</div>
<script>
(function(){
 const root=document.querySelector('[data-nv-chat]');
 if(!root)return;
 const toggle=root.querySelector('[data-nv-chat-toggle]');
 const panel=root.querySelector('.nv-chat-panel');
 const close=root.querySelector('[data-nv-chat-close]');
 const form=root.querySelector('[data-nv-chat-form]');
 const status=root.querySelector('[data-nv-chat-status]');
 const list=root.querySelector('[data-nv-chat-messages]');
 const identity=root.querySelector('[data-nv-chat-identity]');
 const idInput=root.querySelector('[data-nv-chat-id]');
 let pending=false;
 function open(state){
   panel.hidden=!state;
   toggle.hidden=state;
   toggle.setAttribute('aria-expanded',String(state));
   if(state)refresh();
 }
 toggle.addEventListener('click',()=>open(panel.hidden));
 close.addEventListener('click',()=>open(false));
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
     list.textContent='';
     for(const item of payload.messages||[])bubble(item.message,item.sender);
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
 setInterval(()=>{if(!panel.hidden)refresh()},12000);
}());
</script>
