<div class="adm-page-top nv97-popup-top">
  <div>
    <span class="nv97-popup-kicker"><?= icon('image',15) ?> Kampanya Merkezi</span>
    <h2>Popup & Kampanya Yönetimi</h2>
    <p>Kupon, indirim, görsel duyuru ve CTA kampanyalarını tek panelden yönetin.</p>
  </div>
  <a href="/admin/popuplar/yeni" class="adm-action-btn adm-btn-save"><?= icon('plus',15) ?> Yeni Popup</a>
</div>

<div class="nv97-popup-stats">
  <div><strong><?= count($popups) ?></strong><span>Toplam popup</span></div>
  <div><strong><?= count(array_filter($popups,fn($p)=>$p['status']==='active')) ?></strong><span>Aktif</span></div>
  <div><strong><?= count(array_filter($popups,fn($p)=>$p['type']==='coupons')) ?></strong><span>Kupon merkezi</span></div>
</div>

<div class="nv97-popup-grid">
<?php foreach($popups as $popup):
  $img=\App\Services\MarketingPopupService::imageUrl($popup['image']??'');
  $couponCount=count($popup['coupon_items']??[]);
?>
<article class="nv97-popup-card">
  <div class="nv97-popup-preview <?= e($popup['orientation']) ?>"
       style="--nv97-a:<?= e($popup['background_start']?:'#151a45') ?>;--nv97-b:<?= e($popup['background_end']?:'#6b4de8') ?>;--nv97-accent:<?= e($popup['accent_color']?:'#d936a1') ?>">
    <?php if($img): ?><img src="<?= e($img) ?>" alt="" loading="lazy"><?php endif; ?>
    <div class="nv97-popup-preview-shade"></div>
    <div class="nv97-popup-preview-copy">
      <small><?= e($popup['eyebrow']?:($popup['type']==='coupons'?'KUPON & İNDİRİM':'DUYURU')) ?></small>
      <strong><?= e($popup['title']?:$popup['internal_name']) ?></strong>
      <?php if($couponCount): ?><span><?= $couponCount ?> kupon/indirim</span><?php endif; ?>
    </div>
    <span class="nv97-popup-status <?= $popup['status']==='active'?'is-active':'' ?>"><?= $popup['status']==='active'?'Aktif':'Pasif' ?></span>
    <span class="nv97-popup-order">#<?= (int)$popup['sort_order'] ?></span>
  </div>
  <div class="nv97-popup-meta">
    <span><?= icon('clock',13) ?> <?= number_format(((int)$popup['open_delay_ms'])/1000,1,',','.') ?> sn sonra</span>
    <span><?= icon('image',13) ?> <?= $popup['orientation']==='vertical'?'Dikey':'Yatay' ?></span>
    <span><?= icon('grid',13) ?> <?= $popup['page_mode']==='all'?'Tüm site':($popup['page_mode']==='home'?'Ana sayfa':ucfirst($popup['page_mode'])) ?></span>
  </div>
  <div class="nv97-popup-actions">
    <a href="/admin/popuplar/<?= (int)$popup['id'] ?>/duzenle" class="btn btn-outline btn-sm"><?= icon('edit',13) ?> Düzenle</a>
    <form method="POST" action="/admin/popuplar/<?= (int)$popup['id'] ?>/durum"><?= csrf_field() ?><button class="btn btn-outline btn-sm" type="submit"><?= icon($popup['status']==='active'?'eye-off':'eye',13) ?> <?= $popup['status']==='active'?'Pasif':'Aktif' ?></button></form>
    <form method="POST" action="/admin/popuplar/<?= (int)$popup['id'] ?>/kopyala"><?= csrf_field() ?><button class="btn btn-outline btn-sm" type="submit"><?= icon('copy',13) ?> Kopyala</button></form>
    <form method="POST" action="/admin/popuplar/<?= (int)$popup['id'] ?>/sil" onsubmit="return confirm('Bu popup silinsin mi?')"><?= csrf_field() ?><button class="btn btn-outline btn-sm nv97-danger" type="submit"><?= icon('trash',13) ?></button></form>
  </div>
</article>
<?php endforeach; ?>
</div>

<?php if(!$popups): ?>
<div class="adm-card"><div class="adm-card-body nv97-empty"><h3>Henüz popup yok</h3><p>İlk kampanyanızı oluşturup kupon, indirim veya görsel duyurunuzu yayınlayın.</p><a class="adm-action-btn adm-btn-save" href="/admin/popuplar/yeni"><?= icon('plus',14) ?> Popup Oluştur</a></div></div>
<?php endif; ?>