<?php
function yvCatDesign(array $cat): array {
 $s=mb_strtolower(($cat['slug']??'').' '.($cat['name']??''));
 if(str_contains($s,'instagram')) return ['instagram','instagram'];
 if(str_contains($s,'tiktok')) return ['tiktok','tiktok'];
 if(str_contains($s,'youtube')) return ['youtube','youtube'];
 if(str_contains($s,'facebook')) return ['facebook','facebook'];
 if(str_contains($s,'twitter')||str_contains($s,' x ')) return ['twitter','twitter'];
 if(str_contains($s,'google')||str_contains($s,'seo')) return ['google','google'];
 if(str_contains($s,'web')||str_contains($s,'site')) return ['web','globe'];
 return ['default',$cat['icon_key']??'package'];
}
[$platformClass,$platformIcon]=yvCatDesign($category);
?>
<div class="yv-cat-page">
<section class="yv-page-hero yv-cat-hero">
 <div class="container">
  <div class="breadcrumb"><a href="/">Ana Sayfa</a> <span class="separator">/</span><a href="/kategoriler">Hizmetler</a> <span class="separator">/</span><span><?= e($category['name']) ?></span></div>
  <div class="yv-hero-grid" style="margin-top:16px">
   <div>
    <div class="yv-platform-mark <?= e($platformClass) ?>">
     <?php if(!empty($category['image'])): ?><img src="<?= e(upload_url($category['image'])) ?>" alt="<?= e($category['image_alt']??$category['name']) ?>" style="width:38px;height:38px;object-fit:contain"><?php else: ?><?= icon($platformIcon,28) ?><?php endif; ?>
    </div>
    <h1><?= e($category['name']) ?> <span>Paketleri</span></h1>
    <p><?= e($category['description'] ?: 'İhtiyacınıza uygun, güvenli ve hızlı dijital hizmet paketlerini keşfedin. Siparişinizi kolayca oluşturun ve süreci hesabınızdan takip edin.') ?></p>
    <div class="yv-hero-actions"><a class="yv-btn yv-btn-primary" href="#packages">Paketleri incele <?= icon('arrow-right',12) ?></a></div>
    <div class="yv-trust-row"><span class="yv-trust-pill"><?= icon('zap',12) ?> Hızlı teslimat</span><span class="yv-trust-pill"><?= icon('shield',12) ?> Güvenli işlem</span><span class="yv-trust-pill"><?= icon('headphones',12) ?> 7/24 destek</span></div>
   </div>
   <div class="yv-hero-art">
    <div class="yv-hero-person"></div>
    <div class="yv-float a"><?= icon($platformIcon,14) ?> <?= e($category['name']) ?></div>
    <div class="yv-float b"><?= icon('trending-up',14) ?> Daha güçlü görünürlük</div>
    <div class="yv-float c"><?= icon('check-circle',14) ?> Güvenilir hizmet</div>
   </div>
  </div>
 </div>
</section>

<section class="yv-cat-controls">
 <div class="container yv-cat-control-inner">
  <div class="yv-chip-list">
   <a class="yv-chip <?= empty($altSlug)?'active':'' ?>" href="?<?= http_build_query(array_merge($_GET,['alt'=>''])) ?>">Tümü</a>
   <?php foreach($subCategories as $sub): ?><a class="yv-chip <?= $altSlug===$sub['slug']?'active':'' ?>" href="?<?= http_build_query(array_merge($_GET,['alt'=>$sub['slug']])) ?>"><?= e($sub['name']) ?></a><?php endforeach; ?>
  </div>
  <div class="yv-sort-search">
   <form method="GET" style="display:flex;gap:8px">
    <?php if($altSlug): ?><input type="hidden" name="alt" value="<?= e($altSlug) ?>"><?php endif; ?>
    <input type="text" name="q" placeholder="Paketlerde ara..." value="<?= e($searchQuery) ?>">
    <select name="sort" onchange="this.form.submit()"><option value="recommended" <?= $currentSort==='recommended'?'selected':'' ?>>Önerilen</option><option value="price_asc" <?= $currentSort==='price_asc'?'selected':'' ?>>Fiyat artan</option><option value="price_desc" <?= $currentSort==='price_desc'?'selected':'' ?>>Fiyat azalan</option><option value="featured" <?= $currentSort==='featured'?'selected':'' ?>>Popüler</option></select>
   </form>
  </div>
 </div>
</section>

<section class="yv-package-section" id="packages">
 <div class="container">
 <?php if(!empty($packages)): ?>
  <div class="yv-package-grid">
   <?php foreach($packages as $pkg): $price=(!empty($pkg['discount_price'])&&$pkg['discount_price']<$pkg['price'])?$pkg['discount_price']:$pkg['price']; ?>
    <a class="yv-package-card" href="/paket/<?= e($pkg['slug']) ?>">
     <?php if(!empty($pkg['image'])): ?><div class="yv-package-cover"><img src="<?= e(upload_url($pkg['image'])) ?>" alt="<?= e($pkg['image_alt'] ?? $pkg['name']) ?>"></div><?php else: ?><div class="yv-package-cover fallback <?= e($platformClass) ?>"><span><?= icon($platformIcon,30) ?></span><i></i></div><?php endif; ?>
     <div class="yv-package-top"><span class="yv-platform-mark <?= e($platformClass) ?>"><?= icon($platformIcon,18) ?></span><span style="font-size:8px;color:#8790a3;font-weight:700"><?= e($pkg['category_name']??$category['name']) ?></span><?php if(!empty($pkg['is_featured'])||!empty($pkg['badge'])): ?><span class="yv-package-badge"><?= e($pkg['badge'] ?: 'Popüler') ?></span><?php endif; ?></div>
     <h3><?= e($pkg['name']) ?></h3>
     <ul><li><?= icon('check-circle',12) ?> Şifresiz ve güvenli işlem</li><li><?= icon('check-circle',12) ?> Hızlı sipariş takibi</li><li><?= icon('check-circle',12) ?> Satış sonrası destek</li></ul>
     <div class="yv-package-price"><?php if(!empty($pkg['discount_price'])&&$pkg['discount_price']<$pkg['price']): ?><del><?= money($pkg['price']) ?></del><?php endif; ?><strong><?= money($price) ?></strong></div>
     <div class="yv-package-meta"><span><?= e($pkg['delivery_time'] ?: 'Hızlı teslimat') ?></span><b>İncele <?= icon('arrow-right',11) ?></b></div>
    </a>
   <?php endforeach; ?>
  </div>
 <?php else: ?>
  <div class="yv-empty"><div class="yv-empty-icon"><?= icon('search',32) ?></div><h2>Paket bulunamadı</h2><p>Aradığınız kriterlere uygun paket bulunamadı. Filtreleri temizleyerek tekrar deneyebilirsiniz.</p><a class="yv-btn yv-btn-primary" href="/kategori/<?= e($category['slug']) ?>">Tüm paketleri gör</a></div>
 <?php endif; ?>
 </div>
</section>

<section class="yv-category-proof">
 <div class="container">
  <div class="yv-proof-shell">
   <div class="yv-proof-visual"></div>
   <div class="yv-proof-copy">
    <div class="yv-kicker">Neden YorumHizmeti.tr?</div>
    <h2><?= e($category['name']) ?> için güven veren satın alma deneyimi.</h2>
    <p style="color:#6f7990;font-size:11px;line-height:1.75">Paketleri karşılaştırın, ihtiyacınıza uygun hizmeti seçin ve siparişinizi güvenli şekilde tamamlayın.</p>
    <div class="yv-proof-list"><div><?= icon('shield',13) ?> Güvenli ödeme</div><div><?= icon('zap',13) ?> Hızlı teslimat</div><div><?= icon('user',13) ?> Şifresiz işlem</div><div><?= icon('headphones',13) ?> 7/24 destek</div></div>
   </div>
  </div>
 </div>
</section>
</div>