<div class="adm-page-top">
  <div><h2><?= icon('messages-square',20) ?> Netvera Canlı Destek ve Teklifler</h2>
  <p class="text-sm text-secondary">Yeni yazılım müşterilerinden gelen gerçek mesaj ve teklif talepleri. Bu alan hizmet siparişlerinden ayrıdır.</p></div>
  <a href="/admin/netvera-yazilimlar" class="btn btn-outline btn-sm">Yazılımlar</a>
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
    <thead><tr><th>Talep</th><th>Adı</th><th>Ürün</th><th>Tür</th><th>Durum</th><th>Tarih</th><th>İşlem</th></tr></thead>
    <tbody>
    <?php foreach($inquiries as $item): ?>
      <tr>
       <td>#<?= (int)$item['id'] ?></td>
       <td><?= e($item['visitor_name']) ?></td>
       <td><?= e($item['product_slug']?:'Genel') ?></td>
       <td><?= ($item['source_type']??'')==='offer'?'Fiyat Teklifi':'Canlı Sohbet' ?></td>
       <td><strong><?= e($item['status']) ?></strong></td>
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
