<?php
$active=count(array_filter($campaigns,static fn($c)=>(int)$c['is_active']===1));
$totalUses=array_sum(array_map(static fn($c)=>(int)($c['usage_count']??0),$campaigns));
?>
<div class="adm-page-top nv98-top">
  <div>
    <span class="nv98-kicker"><?= icon('tag',15) ?> Satış Kampanyaları</span>
    <h2>Kupon & İndirim Yönetimi</h2>
    <p>NetVera'daki ürün hedefli kupon altyapısı: paket, kategori, hazır yazılım ve yazılım kategorisine özel kampanyalar oluşturun.</p>
  </div>
  <a href="/admin/kuponlar/yeni" class="adm-action-btn adm-btn-save"><?= icon('plus',14) ?> Yeni Kupon / İndirim</a>
</div>

<div class="nv98-stats">
  <article><span><?= icon('tag',18) ?></span><div><strong><?= count($campaigns) ?></strong><small>Toplam kampanya</small></div></article>
  <article><span><?= icon('check-circle',18) ?></span><div><strong><?= $active ?></strong><small>Aktif kampanya</small></div></article>
  <article><span><?= icon('bar-chart',18) ?></span><div><strong><?= $totalUses ?></strong><small>Toplam kullanım</small></div></article>
</div>

<div class="adm-card">
 <div class="adm-card-body nv98-table-wrap">
  <?php if(!$campaigns): ?>
    <div class="nv98-empty"><span><?= icon('tag',34) ?></span><h3>Henüz kupon yok</h3><p>İlk ürün veya kategori kuponunuzu oluşturun.</p><a href="/admin/kuponlar/yeni" class="adm-action-btn adm-btn-save">Kupon Oluştur</a></div>
  <?php else: ?>
  <table class="nv98-table">
    <thead><tr><th>Kampanya</th><th>İndirim</th><th>Hedef</th><th>Kullanım</th><th>Tarih</th><th>Durum</th><th></th></tr></thead>
    <tbody>
    <?php foreach($campaigns as $c):
      $isCoupon=($c['kind']??'coupon')==='coupon';
      $now=time();
      $expired=!empty($c['ends_at'])&&strtotime($c['ends_at'])<$now;
      $scheduled=!empty($c['starts_at'])&&strtotime($c['starts_at'])>$now;
    ?>
      <tr>
        <td><div class="nv98-campaign-name"><strong><?= e($c['name']) ?></strong>
          <?php if($isCoupon): ?><code><?= e($c['code']) ?></code><?php else: ?><em>Otomatik</em><?php endif; ?>
        </div></td>
        <td><b class="nv98-discount"><?= e(\App\Services\DiscountService::discountLabel($c)) ?></b>
          <?php if((float)($c['min_subtotal']??0)>0): ?><small>Min. <?= money((float)$c['min_subtotal']) ?></small><?php endif; ?>
        </td>
        <td><div class="nv98-targets"><?php if(empty($c['target_labels'])): ?><span>Tüm uygun ürünler</span><?php else: foreach(array_slice($c['target_labels'],0,2) as $label): ?><span><?= e($label) ?></span><?php endforeach; if(count($c['target_labels'])>2): ?><small>+<?= count($c['target_labels'])-2 ?> hedef</small><?php endif; endif; ?></div></td>
        <td><strong><?= (int)$c['usage_count'] ?></strong><?php if($c['total_usage_limit']!==null): ?><small>/ <?= (int)$c['total_usage_limit'] ?></small><?php endif; ?></td>
        <td><div class="nv98-dates"><?php if($c['starts_at']): ?><span><?= date('d.m.Y H:i',strtotime($c['starts_at'])) ?></span><?php endif; ?><?php if($c['ends_at']): ?><span><?= date('d.m.Y H:i',strtotime($c['ends_at'])) ?></span><?php endif; ?><?php if(!$c['starts_at']&&!$c['ends_at']): ?><span>Süresiz</span><?php endif; ?></div></td>
        <td><span class="nv98-status <?= (int)$c['is_active']&&!$expired?'active':'inactive' ?>"><?= $expired?'Süresi Doldu':($scheduled?'Planlandı':((int)$c['is_active']?'Aktif':'Pasif')) ?></span></td>
        <td><div class="nv98-actions">
          <a href="/admin/kuponlar/<?= (int)$c['id'] ?>/duzenle" class="btn btn-outline btn-sm"><?= icon('edit',13) ?> Düzenle</a>
          <form method="POST" action="/admin/kuponlar/<?= (int)$c['id'] ?>/durum"><?= csrfField() ?><button class="btn btn-outline btn-sm" type="submit"><?= icon((int)$c['is_active']?'eye-off':'eye',13) ?></button></form>
          <form method="POST" action="/admin/kuponlar/<?= (int)$c['id'] ?>/sil" onsubmit="return confirm('Bu kampanya arşivlensin mi?')"><?= csrfField() ?><button class="btn btn-outline btn-sm nv98-danger" type="submit"><?= icon('trash',13) ?></button></form>
        </div></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
 </div>
</div>