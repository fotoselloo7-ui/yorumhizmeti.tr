<div class="adm-page-top nv94-slider-top">
  <div>
    <span class="nv94-kicker"><?= icon('image',15) ?> Ana Sayfa Vitrini</span>
    <h2>Hero Slider Yönetimi</h2>
    <p>Bayigram benzeri premium bannerları sınırsız ekleyin; görsel, buton, ikon, renk, link ve yayın sırasını yönetin.</p>
  </div>
  <a href="/admin/sliderlar/yeni" class="adm-action-btn adm-btn-save"><?= icon('plus',15) ?> Yeni Slider</a>
</div>

<div class="nv94-slider-stats">
  <div><strong><?= count($slides) ?></strong><span>Toplam slider</span></div>
  <div><strong><?= count(array_filter($slides,fn($s)=>$s['status']==='active')) ?></strong><span>Aktif</span></div>
  <div><strong><?= count(array_filter($slides,fn($s)=>$s['status']!=='active')) ?></strong><span>Pasif</span></div>
</div>

<div class="nv94-slider-admin-grid">
<?php foreach($slides as $slide):
  $img=AppServicesHeroSliderService::imageUrl($slide['desktop_image']??'');
?>
  <article class="nv94-slider-admin-card">
    <div class="nv94-slider-admin-preview" style="--nv94-a:<?= e($slide['background_start']?:'#13255b') ?>;--nv94-b:<?= e($slide['background_end']?:'#6d28d9') ?>">
      <?php if($img): ?><img src="<?= e($img) ?>" alt="" loading="lazy"><?php endif; ?>
      <span class="nv94-slider-admin-status <?= $slide['status']==='active'?'is-active':'' ?>"><?= $slide['status']==='active'?'Aktif':'Pasif' ?></span>
      <span class="nv94-slider-admin-order">#<?= (int)$slide['sort_order'] ?></span>
      <div>
        <small><?= e($slide['category_label']?:$slide['eyebrow']) ?></small>
        <strong><?= e($slide['title']) ?></strong>
      </div>
    </div>
    <div class="nv94-slider-admin-meta">
      <span><?= icon('clock',13) ?> <?= number_format(((int)$slide['autoplay_delay'])/1000,1,',','.') ?> sn</span>
      <span><?= icon('link',13) ?> <?= e($slide['primary_button_text']?:'CTA yok') ?></span>
    </div>
    <div class="nv94-slider-admin-actions">
      <a href="/admin/sliderlar/<?= (int)$slide['id'] ?>/duzenle" class="btn btn-outline btn-sm"><?= icon('edit',13) ?> Düzenle</a>
      <form method="POST" action="/admin/sliderlar/<?= (int)$slide['id'] ?>/durum"><?= csrf_field() ?><button class="btn btn-outline btn-sm" type="submit"><?= icon($slide['status']==='active'?'eye-off':'eye',13) ?> <?= $slide['status']==='active'?'Pasif':'Aktif' ?></button></form>
      <form method="POST" action="/admin/sliderlar/<?= (int)$slide['id'] ?>/kopyala"><?= csrf_field() ?><button class="btn btn-outline btn-sm" type="submit"><?= icon('copy',13) ?> Kopyala</button></form>
      <form method="POST" action="/admin/sliderlar/<?= (int)$slide['id'] ?>/sil" onsubmit="return confirm('Bu slider silinsin mi?')"><?= csrf_field() ?><button class="btn btn-outline btn-sm nv94-danger" type="submit"><?= icon('trash',13) ?></button></form>
    </div>
  </article>
<?php endforeach; ?>
</div>

<?php if(!$slides): ?>
<div class="adm-card"><div class="adm-card-body" style="text-align:center;padding:42px"><h3>Henüz slider yok</h3><p>İlk bannerı oluşturup ana sayfayı canlandırın.</p><a class="adm-action-btn adm-btn-save" href="/admin/sliderlar/yeni"><?= icon('plus',14) ?> Slider Ekle</a></div></div>
<?php endif; ?>
