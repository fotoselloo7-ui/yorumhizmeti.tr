<div class="adm-page-top" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
  <div><h2><?= icon('message-circle',20) ?> Talep #<?= (int)$inquiry['id'] ?></h2>
   <p class="text-sm text-secondary"><?= e($inquiry['visitor_name']) ?> · <?= $inquiry['source_type']==='offer'?'Yazılım Teklifi':'Canlı Sohbet' ?> · <?= e($inquiry['created_at']) ?></p></div>
  <a class="btn btn-outline btn-sm" href="/admin/netvera-gelen-kutusu"><?= icon('arrow-left',14) ?> Gelen Kutusu</a>
</div>
<div style="display:grid;grid-template-columns:minmax(0,1fr) 275px;gap:18px;align-items:start" class="nv-inbox-detail">
 <div class="adm-card">
  <div class="adm-card-header"><h3><?= icon('messages-square',17) ?> Konuşma</h3></div>
  <div class="adm-card-body">
   <?php foreach($messages as $m): ?>
   <div style="max-width:86%;padding:13px 15px;border:1px solid #e5e6f3;border-radius:13px;background:<?= $m['sender']==='admin'?'#f0edff':'#fafbfe' ?>;margin:0 0 11px <?= $m['sender']==='admin'?'auto':'0' ?>">
    <strong style="font-size:11px;color:#5b50b6"><?= $m['sender']==='admin'?'NetVera Destek':'Ziyaretçi' ?></strong>
    <p style="font-size:12px;white-space:pre-wrap;overflow-wrap:anywhere;margin:9px 0;color:#374561"><?= e($m['message']) ?></p>
    <small style="font-size:10px;color:#8090a9"><?= e($m['created_at']) ?></small>
   </div>
   <?php endforeach; ?>
   <form method="post" action="/admin/netvera-gelen-kutusu/<?= (int)$inquiry['id'] ?>/yanit" style="border-top:1px solid #eef0f5;padding-top:15px">
    <?= csrfField() ?>
    <div class="form-group"><label>Destek Yanıtı</label><textarea class="form-control" rows="4" maxlength="3000" required name="message" placeholder="Müşteriye yanıtınızı yazın..."></textarea></div>
    <button class="btn btn-primary" type="submit"><?= icon('send',15) ?> Yanıtı Gönder</button>
   </form>
  </div>
 </div>
 <aside class="adm-card">
  <div class="adm-card-header"><h3><?= icon('info',16) ?> Talep Bilgileri</h3></div>
  <div class="adm-card-body" style="overflow-wrap:anywhere">
    <p><strong>İsim:</strong> <?= e($inquiry['visitor_name']) ?></p>
    <p><strong>İletişim:</strong> <?= e($inquiry['visitor_contact']) ?></p>
    <p><strong>Yazılım:</strong> <?= e($inquiry['product_slug']?:'Genel') ?></p>
    <p><strong>Tür:</strong> <?= $inquiry['source_type']==='offer'?'Teklif':'Sohbet' ?></p>
    <form method="post" action="/admin/netvera-gelen-kutusu/<?= (int)$inquiry['id'] ?>/durum">
      <?= csrfField() ?>
      <div class="form-group"><label>Durum</label><select class="form-control" name="status">
        <?php foreach(['new'=>'Yeni','open'=>'Açık','replied'=>'Yanıtlandı','closed'=>'Kapalı'] as $key=>$label): ?>
         <option value="<?= e($key) ?>" <?= $inquiry['status']===$key?'selected':'' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select></div>
      <button type="submit" class="btn btn-outline btn-sm">Durumu Kaydet</button>
    </form>
  </div>
 </aside>
</div>
<style>@media(max-width:730px){.nv-inbox-detail{grid-template-columns:1fr!important}}</style>
