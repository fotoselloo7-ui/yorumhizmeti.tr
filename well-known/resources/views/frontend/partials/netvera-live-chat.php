<?php
$nvAgentProfile=\App\Services\SupportDeskSettings::publicProfile();
$nvCurrentChat=max(0,(int)($_SESSION['nv_active_chat_id']??0));
if($nvCurrentChat>0 && !\App\Services\NetveraInquiryService::visibleToVisitor($nvCurrentChat))
    $nvCurrentChat=0;
?>
<div class="nv-chat" data-nv-chat>
 <button type="button" class="nv-chat-toggle" data-nv-chat-toggle aria-label="Canlı Destek" aria-controls="nv-chat-panel" aria-expanded="false">
   <?= icon('message-circle',20) ?> <span>Canlı Destek</span>
 </button>
 <section class="nv-chat-panel" id="nv-chat-panel" aria-label="NetVera canlı destek" hidden>
   <header class="nv-chat-header">
     <div class="nv68-chat-person">
       <?php if(!empty($nvAgentProfile['photo'])): ?><img src="<?= e(upload_url($nvAgentProfile['photo'])) ?>" alt="">
       <?php else: ?><span class="nv68-chat-avatar"><?= icon('headphones',20) ?></span><?php endif; ?>
       <div><strong><?= e($nvAgentProfile['name']) ?></strong><small><?= e($nvAgentProfile['title']) ?></small></div>
     </div>
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
