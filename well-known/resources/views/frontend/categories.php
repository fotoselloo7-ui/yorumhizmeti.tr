<?php
function yvCategoryMeta(array $cat): array {
    $s = mb_strtolower(($cat['slug'] ?? '') . ' ' . ($cat['name'] ?? ''));
    if (str_contains($s,'instagram')) return ['instagram','instagram'];
    if (str_contains($s,'tiktok')) return ['tiktok','tiktok'];
    if (str_contains($s,'youtube')) return ['youtube','youtube'];
    if (str_contains($s,'facebook')) return ['facebook','facebook'];
    if (str_contains($s,'twitter') || str_contains($s,' x ')) return ['twitter','twitter'];
    if (str_contains($s,'google')) return ['google','google'];
    if (str_contains($s,'seo')) return ['seo','bar-chart'];
    if (str_contains($s,'web') || str_contains($s,'site')) return ['web','globe'];
    return ['default',$cat['icon_key'] ?? 'package'];
}
?>
<div class="yv-categories">
<section class="yv-page-hero">
  <div class="container">
    <div class="yv-hero-grid">
      <div>
        <div class="yv-kicker"><?= icon('grid',12) ?> Dijital hizmet kataloğu</div>
        <h1>Tüm <span>Hizmetler</span></h1>
        <p>Sosyal medya, Google ve web siteniz için ihtiyacınız olan hizmetleri tek noktadan keşfedin. Güvenli ödeme, hızlı teslimat ve sade satın alma deneyimi.</p>
        <div class="yv-trust-row">
          <span class="yv-trust-pill"><?= icon('shield',13) ?> %100 güvenli ödeme</span>
          <span class="yv-trust-pill"><?= icon('zap',13) ?> Hızlı teslimat</span>
          <span class="yv-trust-pill"><?= icon('headphones',13) ?> 7/24 destek</span>
        </div>
      </div>
      <div class="yv-hero-art">
        <div class="yv-hero-person"></div>
        <div class="yv-float a"><?= icon('star-fill',14) ?> Güçlü sosyal kanıt</div>
        <div class="yv-float b"><?= icon('trending-up',14) ?> Markanı büyüt</div>
        <div class="yv-float c"><?= icon('check-circle',14) ?> Tek panel, kolay işlem</div>
      </div>
    </div>
  </div>
</section>

<section class="yv-category-search">
 <div class="container">
  <div class="yv-filter-shell">
   <div class="search-box"><?= icon('search',17) ?><input id="categorySearch" type="text" value="<?= e($searchQuery ?? '') ?>" placeholder="Hizmet veya platform ara..."></div>
   <a class="yv-btn yv-btn-primary" href="#categories"><?= icon('grid',13) ?> Kategorileri keşfet</a>
  </div>
 </div>
</section>

<section id="categories">
 <div class="container">
  <?php if(!empty($categories)): ?>
  <div class="yv-category-grid" id="platformGrid">
   <?php foreach($categories as $cat): [$cls,$ico]=yvCategoryMeta($cat); ?>
   <a class="yv-category-card <?= e($cls) ?>" data-name="<?= e(mb_strtolower($cat['name'])) ?>" href="/kategori/<?= e($cat['slug']) ?>">
     <div class="yv-cat-icon">
      <?php if(!empty($cat['image'])): ?><img src="<?= e(upload_url($cat['image'])) ?>" alt="<?= e($cat['image_alt'] ?? $cat['name']) ?>" style="width:26px;height:26px;object-fit:contain"><?php else: ?><?= icon($ico,22) ?><?php endif; ?>
     </div>
     <h2><?= e($cat['name']) ?></h2>
     <p><?= e(excerpt(strip_tags($cat['description'] ?? ($cat['name'].' hizmetleri ve paketleri')),80)) ?></p>
     <span>Hizmetleri gör <?= icon('arrow-right',11) ?></span>
   </a>
   <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div class="yv-empty" style="margin:34px 0 70px"><div class="yv-empty-icon"><?= icon('search',30) ?></div><h2>Hizmet bulunamadı</h2><p>"<?= e($searchQuery ?? '') ?>" aramanızla eşleşen kategori bulunamadı.</p><a class="yv-btn yv-btn-primary" href="/kategoriler">Tüm hizmetleri göster</a></div>
  <?php endif; ?>

  <div class="yv-promo">
   <div class="yv-promo-copy">
    <div class="yv-kicker" style="background:rgba(255,255,255,.12);color:#fff">Premium çözümler</div>
    <h2>Sosyal medyada daha güçlü bir marka görünümü.</h2>
    <p>Gerçek ihtiyaçlara göre ayrılmış hizmet grupları, hızlı satın alma ve güven veren modern deneyim.</p>
    <a href="#categories" class="yv-btn yv-btn-light">Hemen incele <?= icon('arrow-right',12) ?></a>
   </div>
   <div class="yv-promo-art"></div>
  </div>
 </div>
</section>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const i=document.getElementById('categorySearch'), cards=[...document.querySelectorAll('.yv-category-card')];
 if(!i)return;i.addEventListener('input',()=>{const q=i.value.toLocaleLowerCase('tr-TR').trim();cards.forEach(c=>c.style.display=(c.dataset.name||'').includes(q)?'flex':'none')});
});
</script>