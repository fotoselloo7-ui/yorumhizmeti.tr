<div class="adm-page-top">
  <div>
    <h2><?= icon('users',24) ?> NetVera Müşteriler & Bayilik</h2>
    <p class="text-sm text-secondary">Eski NetVera profil, satın alma, lisans ve komisyon geçmişinin korunmuş kayıtları.</p>
  </div>
  <a class="btn btn-outline btn-sm" href="/admin/uyeler"><?= icon('arrow-left',14) ?> Mevcut Üyeler</a>
</div>
<?php if (!$ready): ?>
<div class="adm-card" style="margin-top:16px">
 <div class="adm-card-body">
  <h3><?= icon('shield-check',19) ?> Güvenli aktarım henüz uygulanmadı</h3>
  <p>Eski NetVera müşteri ve bayilik tabloları bu veritabanında bulunmuyor. Eski müşteri haklarını korumak için açık GitHub'a veri koymadan ayrı staging veritabanından taşımalıyız.</p>
  <p>Kaynak veritabanı okuma ve staging işlemine ilişkin yönergeler <code>docs/netvera-private-customer-migration-v1.md</code> dosyasındadır. Üretim kullanıcıları silinmedi, admin bilgileri değiştirilmedi.</p>
 </div>
</div>
<?php else: ?>
<div class="grid grid-4" style="margin:15px 0 23px">
  <?php foreach ([
    ['users','Aktarılan Müşteri',$stats['customers']],
    ['shopping-cart','Eski Sipariş',$stats['orders']],
    ['shield-check','Lisans / Hak Kaydı',$stats['licenses']],
    ['user-check','Bayilik Kaydı',$stats['dealers']]
  ] as $metric): ?>
  <div class="adm-card"><div class="adm-card-body" style="display:flex;align-items:center;gap:12px">
    <?= icon($metric[0],23) ?><div><strong style="display:block;font-size:23px"><?= (int)$metric[2] ?></strong>
    <span class="text-sm text-secondary"><?= e($metric[1]) ?></span></div></div></div>
  <?php endforeach; ?>
</div>
<div class="adm-card">
  <div class="adm-card-header"><h3><?= icon('users',18) ?> Kaynak Hesap Eşleştirmeleri</h3></div>
  <div class="adm-card-body" style="padding:0">
    <div class="table-responsive">
      <table class="adm-table">
        <thead><tr><th>Üye</th><th>Eski ID</th><th>Eski Sipariş</th><th>Lisans</th><th>Bayilik</th><th>Durum</th></tr></thead>
        <tbody>
        <?php foreach ($members as $m): ?>
        <tr>
          <td><strong><?= e($m['name']) ?></strong><small style="display:block;color:#8a92a6"><?= e($m['email']) ?></small></td>
          <td><?= (int)$m['old_customer_id'] ?></td>
          <td><?= (int)$m['legacy_orders'] ?></td>
          <td><?= (int)$m['licenses'] ?></td>
          <td><?= e($m['dealer_status'] ?: ($m['want_dealer']?'Başvuru':'—')) ?></td>
          <td><span class="badge <?= $m['status']==='active'?'badge-success':'badge-warning' ?>"><?= e($m['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$members): ?><tr><td colspan="6">Henüz eşleştirilmiş müşteri yok.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="adm-card" style="margin-top:16px">
  <div class="adm-card-header" style="display:flex;flex-wrap:wrap;align-items:center;gap:12px;justify-content:space-between">
    <h3><?= icon('shopping-cart',18) ?> Kimlik Doğrulaması Bekleyen Eski Misafir Siparişleri</h3>
    <span class="badge badge-warning"><?= (int)$stats['unclaimed'] ?> eşleşmemiş</span>
  </div>
  <div class="adm-card-body">
    <p class="text-sm text-secondary" style="margin-bottom:12px">Kaynak NetVera'da müşteri ID'si bulunmayan eski siparişler burada korunur. E-posta eşleşse bile yalnızca bu bilgiyle lisans hakkı verilmez. Önce ödeme ve alıcı kimliği doğrulanmalıdır.</p>
    <div class="table-responsive">
      <table class="adm-table">
        <thead><tr><th>Eski Sipariş</th><th>Müşteri / E-posta</th><th>Ürün</th><th>Tutar</th><th>Ödeme</th></tr></thead>
        <tbody>
        <?php foreach ($unclaimedOrders as $oldOrder): ?>
        <tr>
          <td><strong><?= e($oldOrder['order_no']) ?></strong></td>
          <td><?= e($oldOrder['customer_name'] ?: 'Misafir') ?><small style="display:block;color:#8490a5"><?= e($oldOrder['customer_email'] ?: '-') ?></small></td>
          <td><?= e($oldOrder['product_name'] ?: 'Dijital Ürün') ?></td>
          <td><?= money((float)$oldOrder['amount']) ?></td>
          <td><?= e($oldOrder['payment_status']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$unclaimedOrders): ?><tr><td colspan="5">Eşleşmemiş eski sipariş bulunmuyor.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<p class="text-sm text-secondary" style="margin-top:12px">Bu ekran yalnızca doğrulanmış kayıtları gösterir; yeni satışlara komisyon veya lisans oluşturmaz.</p>
<?php endif; ?>
