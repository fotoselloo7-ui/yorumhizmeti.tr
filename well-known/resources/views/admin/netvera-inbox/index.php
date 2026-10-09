<div class="adm-page-top">
  <div><h2><?= icon('messages-square',20) ?> Netvera Canlı Destek ve Teklifler</h2>
  <p class="text-sm text-secondary">Yeni yazılım müşterilerinden gelen gerçek mesaj ve teklif talepleri. Bu alan hizmet siparişlerinden ayrıdır.</p></div>
  <a href="/admin/netvera-yazilimlar" class="btn btn-outline btn-sm">Yazılımlar</a>
</div>
<div class="adm-card" style="margin-bottom:20px">
  <div class="adm-card-header"><h3><?= icon('bell',17) ?> Telegram Bildirimleri</h3></div>
  <div class="adm-card-body">
    <p style="font-size:12px;line-height:1.65;margin:0 0 14px">
      <?= $telegramConfigured
          ? 'Telegram botu bu sunucuda yapılandırılmış. Hangi olaylarda bildirim alacağınızı seçebilirsiniz.'
          : 'Telegram bot bilgileri henüz bu sunucuda tanımlı değil. NETVERA_TELEGRAM_BOT_TOKEN ve NETVERA_TELEGRAM_CHAT_ID değerlerini sunucu .env dosyasına girin; gizli anahtarları GitHub’a yüklemeyin.' ?>
    </p>
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
  <h3>Netvera gelen kutusu henüz kurulmamış</h3>
  <p>Staging aktarımında <code>database/migrations/netvera-inquiries-v1.sql</code> şemasını kurun. Müşteri veya ödeme tablolarına dokunulmaz.</p>
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
