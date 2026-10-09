<?php $showLegacyArchive = !empty($ready) && (int)($stats['customers']??0) + (int)($stats['orders']??0) + (int)($stats['dealers']??0) > 0; ?>
<div class="adm-page-top">
 <div><h2><?= icon('users',22) ?> Müşteriler, Siparişler ve Bayilik</h2>
   <p class="text-sm text-secondary">Aktif müşteri hesapları, gerçek alışveriş kayıtları ve iş ortaklığı başvuruları tek merkezde.</p>
 </div>
 <div style="display:flex;gap:8px;flex-wrap:wrap">
   <a class="btn btn-outline btn-sm" href="/admin/uyeler"><?= icon('users',14) ?> Tüm Üyeler</a>
   <a class="btn btn-outline btn-sm" href="/admin/siparisler"><?= icon('shopping-cart',14) ?> Siparişler</a>
   <a class="btn btn-primary btn-sm" href="/admin/bayilik"><?= icon('handshake',14) ?> Bayilik Yönetimi</a>
 </div>
</div>
<div class="nv67-customer-overview">
 <div class="nv67-metric-row">
  <?php foreach ([
    ['users','Toplam Üye',$nativeStats['users']??0],
    ['shopping-cart','Toplam Sipariş',$nativeStats['orders']??0],
    ['check-circle','Ödenen Sipariş',$nativeStats['paid_orders']??0],
    ['handshake','Bayi Kaydı',$nativeStats['dealers']??0],
    ['clock','Bekleyen Başvuru',$nativeStats['waiting']??0]
  ] as $metric): ?>
  <div class="adm-card"><div class="adm-card-body nv67-metric">
    <span><?= icon($metric[0],19) ?></span><div><strong><?= number_format((int)$metric[2],0,',','.') ?></strong><small><?= e($metric[1]) ?></small></div>
  </div></div>
  <?php endforeach; ?>
 </div>

 <nav class="nv67-subnav" aria-label="Müşteri yönetimi bölümleri">
  <a href="#current-customers">Mevcut Müşteriler</a>
  <a href="#current-orders">Satın Almalar</a>
  <a href="#partner-accounts">Bayilik</a>
  <?php if($showLegacyArchive): ?><a href="#old-netvera-archive">Geçmiş Satın Almalar</a><?php endif; ?>
 </nav>

 <section class="adm-card" id="current-customers">
  <div class="adm-card-header"><h3><?= icon('users',18) ?> Aktif Sistemdeki Müşteriler</h3><a class="btn btn-outline btn-sm" href="/admin/uyeler">Tümünü Yönet <?= icon('arrow-right',12) ?></a></div>
  <div class="adm-card-body"><div class="table-responsive">
   <table class="adm-table"><thead><tr><th>Üye</th><th>Hesap</th><th>Sipariş</th><th>Ödenen Tutar</th><th>Kayıt</th></tr></thead><tbody>
    <?php foreach($nativeUsers as $customer): ?>
    <tr>
     <td><strong><?= e($customer['name']) ?></strong><small class="nv67-table-muted"><?= e($customer['email']) ?></small></td>
     <td><span class="badge <?= $customer['status']==='active'?'badge-success':'badge-warning' ?>"><?= e($customer['status']) ?></span></td>
     <td><?= (int)$customer['order_count'] ?></td>
     <td><?= money((float)$customer['paid_total']) ?></td>
     <td><?= e((string)$customer['created_at']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if(!$nativeUsers): ?><tr><td colspan="5">Henüz müşteri kaydı bulunmuyor.</td></tr><?php endif; ?>
   </tbody></table>
  </div></div>
 </section>

 <section class="adm-card" id="current-orders">
  <div class="adm-card-header"><h3><?= icon('shopping-cart',18) ?> Gerçek Sipariş ve Satın Alma Geçmişi</h3><a class="btn btn-outline btn-sm" href="/admin/siparisler">Sipariş Merkezi <?= icon('arrow-right',12) ?></a></div>
  <div class="adm-card-body"><div class="table-responsive"><table class="adm-table">
   <thead><tr><th>Sipariş</th><th>Müşteri</th><th>Tutar</th><th>Ödeme</th><th>İşlem Durumu</th></tr></thead><tbody>
    <?php foreach($nativeOrders as $order): ?><tr>
     <td><a href="/admin/siparis/<?= (int)$order['id'] ?>"><strong><?= e($order['order_number']) ?></strong></a><small class="nv67-table-muted"><?= e((string)$order['created_at']) ?></small></td>
     <td><?= e($order['customer_name']??'—') ?><small class="nv67-table-muted"><?= e($order['customer_email']??'') ?></small></td>
     <td><?= money((float)$order['total_amount']) ?></td>
     <td><?= e($order['payment_status']) ?></td><td><?= e(orderStatusLabel($order['order_status'])) ?></td>
    </tr><?php endforeach; ?>
    <?php if(!$nativeOrders): ?><tr><td colspan="5">Henüz sipariş bulunmuyor.</td></tr><?php endif; ?>
   </tbody></table></div>
  </div>
 </section>

 <section class="adm-card" id="partner-accounts">
  <div class="adm-card-header"><h3><?= icon('handshake',18) ?> Bayilik Başvuruları ve İş Ortakları</h3><a class="btn btn-outline btn-sm" href="/admin/bayilik">Bayilik Yönetimine Git <?= icon('arrow-right',12) ?></a></div>
  <div class="adm-card-body">
   <?php if(!\App\Services\DealerProgramService::ready()): ?>
     <p class="text-secondary">Bayilik başvurularını ve iş ortaklarını Bayilik Yönetimi bölümünden yönetin.</p>
   <?php else: ?>
   <div class="table-responsive"><table class="adm-table">
    <thead><tr><th>Müşteri</th><th>Referans</th><th>Durum</th><th>Seviye</th><th>Oran</th></tr></thead><tbody>
    <?php foreach($nativeDealerAccounts as $dealer): ?>
    <tr><td><strong><?= e($dealer['name']) ?></strong><small class="nv67-table-muted"><?= e($dealer['email']) ?></small></td>
     <td><code><?= e($dealer['referral_code']) ?></code></td><td><?= e($dealer['status']) ?></td><td><?= e($dealer['tier']) ?></td><td><?= e((string)$dealer['commission_rate']) ?>%</td></tr>
    <?php endforeach; ?>
    <?php if(!$nativeDealerAccounts): ?><tr><td colspan="5">Henüz bayilik başvurusu bulunmuyor.</td></tr><?php endif; ?>
    </tbody></table></div>
   <?php endif; ?>
  </div>
 </section>

 <?php if($showLegacyArchive): ?>
 <section class="adm-card" id="old-netvera-archive">
   <div class="adm-card-header"><h3><?= icon('archive',18) ?> Eski NetVera Satın Alma ve Lisans Arşivi</h3>
      <span class="badge <?= $ready?'badge-success':'badge-warning' ?>"><?= $ready?'Arşiv tabloları bağlı':'Arşiv bağlantısı bekliyor' ?></span></div>
   <div class="adm-card-body">

      <div class="nv67-archive-stats">
        <span><strong><?= (int)$stats['customers'] ?></strong> Eski müşteri</span>
        <span><strong><?= (int)$stats['orders'] ?></strong> Eski sipariş</span>
        <span><strong><?= (int)$stats['licenses'] ?></strong> Lisans hakkı</span>
        <span><strong><?= (int)$stats['dealers'] ?></strong> Eski bayi</span>
        <span><strong><?= (int)$stats['unclaimed'] ?></strong> Doğrulama bekleyen sipariş</span>
      </div>
      <div class="table-responsive"><table class="adm-table"><thead>
       <tr><th>Eski Müşteri</th><th>E-posta</th><th>Sipariş</th><th>Lisans</th><th>Bayilik</th></tr></thead><tbody>
       <?php foreach($members as $m): ?><tr>
        <td><strong><?= e($m['name']) ?></strong><small class="nv67-table-muted">Eski ID #<?= (int)$m['old_customer_id'] ?></small></td>
        <td><?= e($m['email']) ?></td><td><?= (int)$m['legacy_orders'] ?></td>
        <td><?= (int)$m['licenses'] ?></td><td><?= e($m['dealer_status']?:($m['want_dealer']?'Başvuru':'—')) ?></td>
       </tr><?php endforeach; ?>
       <?php if(!$members): ?><tr><td colspan="5">Arşiv şeması hazır; henüz aktarılmış müşteri kaydı yok.</td></tr><?php endif; ?>
       </tbody></table></div>
       <?php if($unclaimedOrders): ?><details class="nv67-archive-details"><summary>Doğrulanmamış eski siparişleri incele (<?= (int)$stats['unclaimed'] ?>)</summary>
       <div class="table-responsive"><table class="adm-table"><thead><tr><th>Sipariş</th><th>Alıcı</th><th>Ürün</th><th>Ödeme</th></tr></thead><tbody>
       <?php foreach($unclaimedOrders as $o): ?><tr><td><?= e($o['order_no']) ?></td><td><?= e($o['customer_name']?:'Misafir') ?></td><td><?= e($o['product_name']) ?></td><td><?= e($o['payment_status']) ?></td></tr><?php endforeach; ?>
       </tbody></table></div><p class="text-secondary">Misafir siparişleri yalnızca e-posta benzerliğiyle hesaba/lisansa bağlanmaz.</p></details><?php endif; ?>
   </div>
 </section>
 <?php endif; ?>
</div>
