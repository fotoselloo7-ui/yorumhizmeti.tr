<?php
$photo=$agent['photo']??'';
?>
<div class="nd-app" id="nvDesk" data-csrf="<?= e($csrf) ?>" data-sound-new="<?= e($sound['new']) ?>" data-sound-reply="<?= e($sound['reply']) ?>">
 <header class="nd-header">
   <div class="nd-brand"><span class="nd-logo">N<span>V</span></span><div><strong>NetVera <em>Cep</em></strong><small>Destek ve Sipariş Merkezi</small></div></div>
   <div class="nd-header-actions"><button type="button" id="ndAlertEnable" title="Bildirim seslerini etkinleştir" aria-label="Bildirimleri etkinleştir">♬</button><a href="/admin" title="Yönetim paneli" aria-label="Yönetim paneli">⚙</a></div>
 </header>
 <section class="nd-greeting">
  <div class="nd-person">
   <?php if($photo): ?><img src="<?= e(upload_url($photo)) ?>" alt=""><?php else: ?><span><?= e(mb_strtoupper(mb_substr($agent['name']??'N',0,1))) ?></span><?php endif; ?>
   <div><small>Hoş geldiniz</small><strong><?= e($agent['name']??'Destek') ?></strong><p><?= e($agent['title']??'Müşteri Temsilcisi') ?></p></div>
  </div>
  <span class="nd-live"><i></i> Çevrimiçi</span>
 </section>
 <nav class="nd-tabs" aria-label="Cep paneli bölümleri">
  <button type="button" class="active" data-tab="chat"><span>💬</span> Sohbetler <b id="ndChatCount">0</b></button>
  <button type="button" data-tab="ticket"><span>🎧</span> Talepler <b id="ndTicketCount">0</b></button>
  <button type="button" data-tab="order"><span>📦</span> Siparişler <b id="ndOrderCount">0</b></button>
 </nav>
 <main class="nd-content">
  <div class="nd-heading"><div><h1 id="ndTitle">Canlı Sohbetler</h1><p id="ndSubtitle">Gelen konuşmalar</p></div><button type="button" id="ndRefresh" aria-label="Yenile">↻</button></div>
  <div id="ndList" class="nd-list" aria-live="polite"><div class="nd-loading">Konuşmalar yükleniyor…</div></div>
  <section id="ndConversation" class="nd-conversation" hidden>
    <header><button id="ndBack" type="button" aria-label="Geri">←</button><div><strong id="ndDetailTitle"></strong><small id="ndDetailSubtitle"></small></div><span class="nd-status" id="ndDetailStatus"></span></header>
    <div class="nd-thread" id="ndThread"></div>
    <form id="ndReply" class="nd-reply"><textarea name="message" minlength="2" maxlength="3000" required rows="2" placeholder="Yanıtınızı yazın…"></textarea><button type="submit" aria-label="Yanıtı Gönder">➤</button></form>
    <div id="ndFeedback" class="nd-feedback" role="status"></div>
  </section>
 </main>
 <footer class="nd-bottom">
   <div class="nd-bottom-state"><span class="nd-dot"></span><span id="ndOnline">Bağlanıyor…</span></div><a href="/admin/giris">Yönetici Girişi</a>
 </footer>
</div>
