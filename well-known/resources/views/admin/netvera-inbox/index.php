<div class="adm-page-top">
  <div><h2><?= icon('messages-square',20) ?> Netvera Canlı Destek ve Teklifler</h2>
  <p class="text-sm text-secondary">Yeni yazılım müşterilerinden gelen gerçek mesaj ve teklif talepleri. Bu alan hizmet siparişlerinden ayrıdır.</p></div>
  <a href="/admin/netvera-yazilimlar" class="btn btn-outline btn-sm">Yazılımlar</a>
</div>
<?php $nvAgent=\App\Services\SupportDeskSettings::publicProfile(); $nvSounds=\App\Services\SupportDeskSettings::sounds(); ?>
<div class="nv68-support-admin">
 <section class="adm-card">
  <div class="adm-card-header"><h3><?= icon('user-round',17) ?> Destek Temsilcisi ve Sesler</h3></div>
  <div class="adm-card-body">
   <form method="post" action="/admin/netvera-gelen-kutusu/temsilci/kaydet" enctype="multipart/form-data">
    <?= csrfField() ?>
    <div class="nv68-agent">
     <div class="nv68-avatar">
      <?php if($nvAgent['photo']): ?><img src="<?= e(upload_url($nvAgent['photo'])) ?>" alt=""><?php else: ?><?= icon('user-round',26) ?><?php endif; ?>
     </div>
     <div class="nv68-agent-fields">
      <label>Temsilci Adı<input class="form-control" name="name" maxlength="90" required value="<?= e($nvAgent['name']) ?>"></label>
      <label>Uzmanlık / Rütbe<input class="form-control" name="title" maxlength="90" required placeholder="Full Stack Developer" value="<?= e($nvAgent['title']) ?>"></label>
      <label>Profil Fotoğrafı<input class="form-control" name="photo" type="file" accept="image/jpeg,image/png,image/webp"></label>
      <label>Yeni Sohbet Sesi<select class="form-control" name="new_sound" data-nv-sound>
       <?php foreach(['chime'=>'Zil','soft'=>'Yumuşak','digital'=>'Dijital','off'=>'Kapalı'] as $key=>$name): ?><option value="<?= e($key) ?>" <?= $nvSounds['new']===$key?'selected':'' ?>><?= e($name) ?></option><?php endforeach; ?></select></label>
      <label>Yeni Yanıt Sesi<select class="form-control" name="reply_sound" data-nv-sound>
       <?php foreach(['chime'=>'Zil','soft'=>'Yumuşak','digital'=>'Dijital','off'=>'Kapalı'] as $key=>$name): ?><option value="<?= e($key) ?>" <?= $nvSounds['reply']===$key?'selected':'' ?>><?= e($name) ?></option><?php endforeach; ?></select></label>
     </div>
    </div>
    <div class="nv68-support-actions"><button type="submit" class="btn btn-primary btn-sm"><?= icon('save',15) ?> Kaydet</button><button type="button" id="nv68TrySound" class="btn btn-outline btn-sm"><?= icon('volume-2',15) ?> Ses Önizle</button><button type="button" id="nv68EnableSound" class="btn btn-outline btn-sm"><?= icon('bell',15) ?> Bildirimleri Aç</button></div>
   </form>
  </div>
 </section>
 <section class="adm-card">
  <div class="adm-card-header"><h3><?= icon('smartphone',17) ?> NetVera Cep</h3></div>
  <div class="adm-card-body">
   <div class="nv68-install-grid">
    <div id="nv68Qr" class="nv68-qr" aria-label="Telefon kurulum karekodu"><img src="<?= asset('img/nv-desk-qr.svg') ?>" alt="NetVera Cep Paneli kurulum karekodu" width="150" height="150"></div>
    <div>
      <strong>Mobil Destek Paneli</strong>
      <p>iPhone ve Android telefonla QR kodu taratın. Yönetici hesabınızla giriş yapıp ana ekrana ekleyin.</p>
      <a class="btn btn-primary btn-sm" href="/admin/cep" target="_blank" rel="noopener"><?= icon('smartphone',14) ?> Cep Panelini Aç</a>
      <p class="nv68-install-note">iPhone: Safari → Paylaş → Ana Ekrana Ekle. Android: Chrome → Uygulamayı yükle / Ana ekrana ekle.</p>
    </div>
   </div>
  </div>
 </section>
</div>
<style>
.nv68-support-admin{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(280px,1fr);gap:16px;margin-bottom:17px}
.nv68-agent{display:flex;align-items:start;gap:13px}.nv68-avatar{width:62px;height:62px;border-radius:17px;background:#eeeaff;color:#6754c8;flex:none;display:grid;place-items:center;overflow:hidden}.nv68-avatar img{width:100%;height:100%;object-fit:cover}
.nv68-agent-fields{flex:1;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.nv68-agent-fields label{display:grid;gap:5px;font-size:11px;font-weight:600}.nv68-agent-fields label:nth-child(3){grid-column:1/-1}
.nv68-support-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}.nv68-install-grid{display:flex;align-items:center;gap:16px}.nv68-install-grid strong{font-size:13px}.nv68-install-grid p{font-size:11px;line-height:1.6;color:#73829a}.nv68-install-note{font-size:10px!important}.nv68-qr{width:152px;height:152px;flex:none;border-radius:14px;background:#f5f3ff;display:grid;place-items:center;color:#6253bd}.nv68-qr canvas,.nv68-qr img{max-width:100%}
@media(max-width:1050px){.nv68-support-admin{grid-template-columns:1fr}}@media(max-width:580px){.nv68-agent{flex-direction:column}.nv68-agent-fields{grid-template-columns:1fr}.nv68-install-grid{align-items:flex-start}.nv68-qr{width:120px;height:120px}}
</style>


<script>
document.addEventListener('DOMContentLoaded',()=>{

 const alerts=window.NvDeskAlerts;
 document.getElementById('nv68TrySound')?.addEventListener('click',()=>{
  alerts?.unlock();alerts?.play(document.querySelector('[name="new_sound"]')?.value||'chime');
 });
 document.getElementById('nv68EnableSound')?.addEventListener('click',async()=>{await alerts?.enable();alerts?.play('soft')});
});
</script>
<div class="adm-card" style="margin-bottom:20px">
  <div class="adm-card-header"><h3><?= icon('bell',17) ?> Telegram Bildirimleri</h3></div>
  <div class="adm-card-body">
    
    <form method="post" action="/admin/netvera-gelen-kutusu/telegram/kaydet">
      <?= csrfField() ?>
      <div style="display:flex;flex-wrap:wrap;gap:12px 22px;margin-bottom:15px">
        <?php foreach([
          'enabled'=>'Telegram bildirimleri açık',
          'notify_chat'=>'Yeni canlı sohbet',
          'notify_offer'=>'Yeni yazılım teklifi',
          'notify_followup'=>'Müşteri yeniden yazdığında',
          'notify_important'=>'Önemli işaretli talep'
        ] as $key=>$label): ?>
        <label style="display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:600;cursor:pointer">
          <input type="checkbox" name="nv_tg_<?= e($key) ?>" value="1" <?= setting('nv_tg_'.$key,'1')==='1'?'checked':'' ?>>
          <?= e($label) ?>
        </label>
        <?php endforeach; ?>
      </div>
      <button class="btn btn-primary btn-sm" type="submit"><?= icon('check',14) ?> Bildirim Tercihlerini Kaydet</button>
    </form>
    <?php if($telegramConfigured): ?>
    <form method="post" action="/admin/netvera-gelen-kutusu/telegram/test" style="margin-top:10px">
      <?= csrfField() ?>
      <button type="submit" class="btn btn-outline btn-sm"><?= icon('send',14) ?> Telegram Test Bildirimi Gönder</button>
    </form>
    <?php endif; ?>
    <?php if(!$importanceReady && $ready): ?>
      <form method="post" action="/admin/netvera-gelen-kutusu/onem/kur" style="margin-top:14px">
        <?= csrfField() ?>
        <button class="btn btn-outline btn-sm" type="submit"><?= icon('star',14) ?> Önemli Talep Özelliğini Kur (Tek Tık)</button>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php if(!$ready): ?>
 <div class="adm-card"><div class="adm-card-body">
   <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
     <div><h3><?= icon('inbox',17) ?> Gelen Kutusu</h3></div>
     <form method="post" action="/admin/netvera-gelen-kutusu/kur">
       <?= csrfField() ?>
       <button class="btn btn-primary btn-sm" type="submit"><?= icon('check-circle',14) ?> Gelen Kutusunu Etkinleştir</button>
     </form>
   </div>
 </div></div>
<?php else: ?>
<div class="adm-card">
 <div class="adm-card-header"><h3><?= icon('inbox',16) ?> Talepler (<?= count($inquiries) ?>)</h3></div>
 <div class="adm-card-body">
  <?php if(!$inquiries): ?><p>Henüz yeni sohbet veya teklif talebi bulunmuyor.</p><?php else: ?>
  <div style="overflow-x:auto">
   <table class="table" style="width:100%;border-collapse:collapse">
    <thead><tr><th>Talep</th><th>Adı</th><th>Telefon / İletişim</th><th>Ürün</th><th>Tür</th><th>Durum</th><th>Önem</th><th>Tarih</th><th>İşlem</th></tr></thead>
    <tbody>
    <?php foreach($inquiries as $item): ?>
      <tr>
       <td>#<?= (int)$item['id'] ?></td>
       <td><?= e($item['visitor_name']) ?></td>
       <td><?= e($item['visitor_contact']) ?></td>
       <td><?= e($item['product_slug']?:'Genel') ?></td>
       <td><?= ($item['source_type']??'')==='offer'?'Fiyat Teklifi':'Canlı Sohbet' ?></td>
       <td><strong><?= e($item['status']) ?></strong></td>
       <td><?= !empty($item['is_important']) ? '⭐ Önemli' : '—' ?></td>
       <td><?= e($item['updated_at']) ?></td>
       <td><a class="btn btn-primary btn-sm" href="/admin/netvera-gelen-kutusu/<?= (int)$item['id'] ?>">Aç <?= icon('arrow-right',13) ?></a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
   </table>
  </div>
  <?php endif; ?>
 </div>
</div>
<?php endif; ?>
