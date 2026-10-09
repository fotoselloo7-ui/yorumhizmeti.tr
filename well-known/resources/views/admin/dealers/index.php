<div class="adm-page-top">
  <div><h2><?= icon('handshake',22) ?> Bayilik ve İş Ortakları</h2>
  </div>
  <a class="btn btn-outline btn-sm" href="/admin/netvera-musteriler"><?= icon('users',15) ?> NetVera Eski Müşterileri</a>
</div>
<?php if(!$ready): ?>
<div class="adm-card" style="margin-top:20px"><div class="adm-card-body">
  <h3><?= icon('handshake',18) ?> Bayilik Yönetimi</h3>
  
  <?php if($canApprove): ?><form method="post" action="/admin/bayilik/kur"><?= csrfField() ?><button class="btn btn-primary" type="submit"><?= icon('database',16) ?> Bayilik Modülünü Etkinleştir</button></form><?php endif; ?>
</div></div>
<?php else: ?>
<div class="adm-card" style="margin-top:18px">
  <div class="adm-card-header">
    <h3><?= icon('users',18) ?> Başvurular ve İş Ortakları (<?= count($dealers) ?>)</h3>
  </div>
  <div class="adm-card-body">
    <div class="table-responsive"><table class="adm-table" style="min-width:850px">
      <thead><tr><th>Müşteri</th><th>Referans Kodu</th><th>Ziyaret</th><th>Durum</th><th>Seviye</th><th>Komisyon Oranı</th><th>İşlem</th></tr></thead>
      <tbody>
        <?php foreach($dealers as $dealer): ?>
        <tr>
          <td><strong><?= e($dealer['name']) ?></strong><small style="display:block"><?= e($dealer['email']) ?></small>
            <?php if($dealer['legacy_affiliate_id']): ?><small>Eski bayi #<?= (int)$dealer['legacy_affiliate_id'] ?></small><?php endif; ?>
          </td>
          <td><code><?= e($dealer['referral_code']) ?></code></td>
          <td><?= (int)$dealer['referral_visits'] ?></td>
          <?php if($canApprove): ?>
            <td colspan="4">
              <form method="post" action="/admin/bayilik/<?= (int)$dealer['id'] ?>/karar"
                style="display:grid;grid-template-columns:repeat(3,minmax(115px,1fr)) auto;gap:8px;align-items:center">
                <?= csrfField() ?>
                <select name="status" class="form-control" aria-label="Bayilik durumu">
                  <?php foreach(['pending'=>'İncelemede','approved'=>'Onaylandı','suspended'=>'Askıya al','rejected'=>'Reddedildi'] as $v=>$label): ?>
                  <option value="<?= e($v) ?>" <?= $dealer['status']===$v?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
                <select name="tier" class="form-control" aria-label="Bayilik seviyesi">
                  <?php foreach(['starter'=>'Başlangıç','pro'=>'Pro','agency'=>'Ajans'] as $v=>$label): ?>
                  <option value="<?= e($v) ?>" <?= $dealer['tier']===$v?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
                <input name="commission_rate" class="form-control" type="number" min="0" max="30" step=".01"
                  value="<?= e(number_format((float)$dealer['commission_rate'],2,'.','')) ?>" aria-label="Komisyon oranı" required>
                <button type="submit" class="btn btn-primary btn-sm"><?= icon('save',14) ?> Kaydet</button>
              </form>
            </td>
          <?php else: ?>
            <td><?= e($dealer['status']) ?></td><td><?= e($dealer['tier']) ?></td>
            <td><?= e((string)$dealer['commission_rate']) ?>%</td><td>İnceleme</td>
          <?php endif; ?>
        </tr>
        <?php endforeach; ?>
        <?php if(!$dealers): ?><tr><td colspan="7">Henüz bayilik başvurusu yok.</td></tr><?php endif; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?php endif; ?>

<?php if($ready && !empty($history)): ?>
<div class="adm-card" style="margin-top:15px">
 <div class="adm-card-header"><h3><?= icon('history',18) ?> Bayilik Karar Geçmişi</h3></div>
 <div class="adm-card-body">
  <div class="table-responsive"><table class="adm-table">
   <thead><tr><th>Tarih</th><th>Müşteri</th><th>İşlem</th><th>Önceki Durum</th><th>Yeni Durum</th><th>Açıklama</th></tr></thead>
   <tbody>
   <?php foreach($history as $entry): ?>
    <tr>
      <td><?= e((string)$entry['created_at']) ?></td>
      <td><?= e($entry['dealer_name']) ?></td>
      <td><?= e($entry['action']) ?></td>
      <td><?= e($entry['old_status']?:'—') ?></td>
      <td><?= e($entry['new_status']?:'—') ?></td>
      <td><?= e($entry['details']) ?></td>
    </tr>
   <?php endforeach; ?>
   </tbody>
  </table></div>
 </div>
</div>
<?php endif; ?>
