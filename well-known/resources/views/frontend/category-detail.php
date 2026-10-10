<?php
if (!function_exists('yvCatDesign')) {
    function yvCatDesign(array $cat): array {
        $s=mb_strtolower(($cat['slug']??'').' '.($cat['name']??''));
        if(str_contains($s,'instagram')) return ['instagram','instagram','#E1306C'];
        if(str_contains($s,'tiktok')) return ['tiktok','tiktok','#111111'];
        if(str_contains($s,'youtube')) return ['youtube','youtube','#FF0000'];
        if(str_contains($s,'facebook')) return ['facebook','facebook','#1877F2'];
        if(str_contains($s,'twitter')||str_contains($s,' x ')) return ['twitter','twitter','#111111'];
        if(str_contains($s,'threads')) return ['threads','threads','#111111'];
        if(str_contains($s,'telegram')) return ['telegram','telegram','#229ED9'];
        if(str_contains($s,'spotify')) return ['spotify','spotify','#1DB954'];
        if(str_contains($s,'discord')) return ['discord','discord','#5865F2'];
        if(str_contains($s,'linkedin')) return ['linkedin','linkedin','#0A66C2'];
        if(str_contains($s,'twitch')) return ['twitch','twitch','#9147FF'];
        if(str_contains($s,'dijital reklam')||str_contains($s,'dijital-reklam')) return ['ads','ads','#F97316'];
        if(str_contains($s,'itibar')) return ['reputation','reputation','#0F766E'];
        if(str_contains($s,'e-ticaret')||str_contains($s,'eticaret')) return ['ecommerce','store','#10B981'];
        if(str_contains($s,'mobil uygulama')||str_contains($s,'mobil-uygulama')) return ['mobileapp','mobile-app','#6366F1'];
        if(str_contains($s,'içerik')||str_contains($s,'icerik')) return ['content','content-create','#F97316'];
        if(str_contains($s,'grafik')) return ['graphic','palette','#EC4899'];
        if(str_contains($s,'yerel')) return ['local','local-business','#16A34A'];
        if(str_contains($s,'google')||str_contains($s,'seo')) return ['google','google','#4285F4'];
        if(str_contains($s,'web')||str_contains($s,'site')) return ['web','globe','#2868FF'];
        return ['default',$cat['icon_key']??'package','#6750E7'];
    }
}
[$platformClass,$platformIcon,$platformColor]=yvCatDesign($category);
$categoryPlatform=\App\Services\SocialPlatformIdentity::fromCategory($category);
if($categoryPlatform!==null){
    $platformClass=$categoryPlatform;
    $platformIcon=\App\Services\SocialPlatformIdentity::iconFor($categoryPlatform);
    $platformColor=\App\Services\SocialPlatformIdentity::brand($categoryPlatform)[2];
}
$nvPlatformClass=\App\Services\SocialPlatformIdentity::classFor($categoryPlatform);
$categoryWords = preg_split('/\s+/', trim($category['name'] ?? 'Hizmetler'));
$categoryLead = array_shift($categoryWords) ?: 'Dijital';
$categoryRest = implode(' ', $categoryWords) ?: 'Hizmetleri';
$categoryHeroCutout = asset('img/hero-woman-cutout.png');
?>
<div class="yv-category-v5 <?= e($nvPlatformClass) ?>">
<section class="yv-category-hero-v5 <?= e($platformClass) ?> <?= e($nvPlatformClass) ?>">
 <div class="container">
  <div class="breadcrumb"><a href="/">Ana Sayfa</a><span class="separator">/</span><a href="/kategoriler">Hizmetler</a><span class="separator">/</span><span><?= e($category['name']) ?></span></div>
  <div class="yv-category-hero-grid-v5">
   <div>
    <div class="yv-category-brand-v5"><span><?= icon($platformIcon,26) ?></span><small><?= e($category['name']) ?></small></div>
    <h1><span class="yv-cat-title-lead"><?= e($categoryLead) ?></span><span class="yv-cat-title-accent"><?= e($categoryRest) ?></span></h1>
    <p><?= e($category['description'] ?: 'İhtiyacınıza uygun, güvenli ve hızlı dijital hizmet paketlerini keşfedin. Siparişinizi kolayca oluşturun ve süreci hesabınızdan takip edin.') ?></p>
    <div class="yv-category-benefits-v5">
     <span><?= icon('zap',13) ?><b>Hızlı Teslimat</b><small>Dakikalar içinde</small></span>
     <span><?= icon('shield',13) ?><b>%100 Güvenli</b><small>Hesap bilgisi gerekmez</small></span>
     <span><?= icon('users',13) ?><b>Gerçek & Aktif</b><small>Kaliteli hesaplar</small></span>
     <span><?= icon('headphones',13) ?><b>7/24 Destek</b><small>Her zaman yanınızda</small></span>
    </div>
    <a class="yv-btn yv-btn-primary" href="#packages">Hemen Paketleri İncele <?= icon('arrow-right',12) ?></a>
   </div>
   <div class="yv-category-hero-art-v5">
    <img class="yv-category-hero-woman-v5" src="<?= e($categoryHeroCutout) ?>" alt="<?= e($category['name']) ?>" loading="eager">
    <span class="yv-category-brand-orbit-v5 main"><?= icon($platformIcon,34) ?></span>
    <span class="yv-category-brand-orbit-v5 small one"><?= icon('heart',18) ?></span>
    <span class="yv-category-brand-orbit-v5 small two"><?= icon('trending-up',18) ?></span>
    <div class="yv-category-float-v5 a"><?= icon('heart',12) ?> +1.5K etkileşim</div>
    <div class="yv-category-float-v5 b"><?= icon('users',12) ?> Organik büyüme</div>
    <div class="yv-category-float-v5 c"><?= icon('trending-up',12) ?> Daha güçlü marka</div>
   </div>
  </div>
 </div>
</section>

<section class="yv-category-filter-v5">
 <div class="container">
  <div class="yv-category-chip-row-v5">
   <a class="<?= empty($altSlug)?'active':'' ?>" href="?<?= http_build_query(array_merge($_GET,['alt'=>''])) ?>"><?= icon('grid',12) ?> Tümü</a>
   <?php foreach($subCategories as $sub):
     $subPlatform=\App\Services\SocialPlatformIdentity::fromCategory($sub,$category);
     $subIcon=\App\Services\SocialPlatformIdentity::iconFor($subPlatform,$sub['icon_key']??'package');
   ?><a class="<?= $altSlug===$sub['slug']?'active':'' ?> <?= e(\App\Services\SocialPlatformIdentity::classFor($subPlatform)) ?>" href="?<?= http_build_query(array_merge($_GET,['alt'=>$sub['slug']])) ?>"><?= icon($subIcon,12) ?> <?= e($sub['name']) ?></a><?php endforeach; ?>
  </div>
  <div class="yv-category-filter-tools-v5">
   <form method="GET" class="yv-category-search-form-v5">
    <?php if($altSlug): ?><input type="hidden" name="alt" value="<?= e($altSlug) ?>"><?php endif; ?>
    <?= icon('search',13) ?><input type="text" name="q" value="<?= e($searchQuery) ?>" placeholder="Paketlerde ara...">
    <select name="sort" onchange="this.form.submit()"><option value="recommended" <?= $currentSort==='recommended'?'selected':'' ?>>Sıralama: Önerilen</option><option value="price_asc" <?= $currentSort==='price_asc'?'selected':'' ?>>Fiyat: Artan</option><option value="price_desc" <?= $currentSort==='price_desc'?'selected':'' ?>>Fiyat: Azalan</option><option value="featured" <?= $currentSort==='featured'?'selected':'' ?>>En Popüler</option></select>
   </form>
  </div>
 </div>
</section>

<section class="yv-category-packages-v5" id="packages">
 <div class="container">
  <?php if(!empty($packages)): ?>
  <div class="yv-category-package-grid-v5">
   <?php foreach($packages as $index=>$pkg):
    $price=(!empty($pkg['discount_price'])&&$pkg['discount_price']<$pkg['price'])?$pkg['discount_price']:$pkg['price'];
    $discount=(!empty($pkg['discount_price'])&&$pkg['discount_price']<$pkg['price']&&$pkg['price']>0)?round((1-$pkg['discount_price']/$pkg['price'])*100):0;
   ?>
   <a class="yv-category-package-card-v5 <?= e($nvPlatformClass) ?>" href="/paket/<?= e($pkg['slug']) ?>">
    <div class="yv-category-package-icon-v5 <?= e($platformClass) ?> <?= e($nvPlatformClass) ?>"><?= icon($platformIcon,23) ?></div>
    <?php if($index===0 || !empty($pkg['is_featured'])): ?><span class="yv-package-ribbon-v5"><?= $index===0?'EN POPÜLER':'ÇOK TERCİH EDİLEN' ?></span><?php endif; ?>
    <h3><?= e(package_display_name($pkg)) ?></h3>
    <?php if(!empty($pkg['short_description'])): ?><p class="nv67-category-package-summary"><?= e(excerpt(strip_tags((string)$pkg['short_description']),125)) ?></p><?php endif; ?>
    <ul>
     <?php foreach(array_slice(\App\Services\PackageHighlightsService::get((int)$pkg['id'],$pkg),0,4) as $highlight): ?>
       <li><?= icon('check-circle',12) ?> <?= e($highlight) ?></li>
     <?php endforeach; ?>
    </ul>
    <div class="yv-category-delivery-v5"><?= icon('truck',12) ?> Teslimat: <?= e($pkg['delivery_time'] ?: 'Hızlı') ?></div>
    <div class="yv-category-price-v5"><div><?php if($discount): ?><del><?= money($pkg['price']) ?></del><?php endif; ?><strong><?= money($price) ?></strong></div><?php if($discount): ?><span>%<?= $discount ?> indirim</span><?php endif; ?></div>
    <em>Paketleri İncele <?= icon('arrow-right',11) ?></em>
   </a>
   <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div class="yv-empty"><div class="yv-empty-icon"><?= icon('search',30) ?></div><h2>Paket bulunamadı</h2><p>Seçtiğiniz filtrelere uygun paket yok. Filtreleri temizleyip tekrar deneyin.</p><a class="yv-btn yv-btn-primary" href="/kategori/<?= e($category['slug']) ?>">Tüm paketleri göster</a></div>
  <?php endif; ?>
 </div>
</section>

<section class="yv-category-story-v5 yh-story-premium-v23" aria-labelledby="yh23-story-heading">
 <div class="container">
  <div class="yv-category-story-grid-v5">
   <div class="yv-category-phone-art-v5 <?= e($platformClass) ?> <?= e($nvPlatformClass) ?>" aria-label="<?= e($category['name']) ?> tanıtım görseli">
    <div class="yh23-art-halo" aria-hidden="true"></div>
    <div class="yh23-visual-label"><?= icon('sparkles',13) ?> Dijital büyüme</div>
    <div class="phone" aria-hidden="true">
     <div class="screen">
      <span><?= icon($platformIcon,40) ?></span>
      <small>Örnek etkileşim analizi</small>
      <b>+12.5K</b>
      <div class="yh23-mini-graph" aria-hidden="true">
       <i></i><i></i><i></i><i></i><i></i><i></i>
      </div>
     </div>
    </div>
    <div class="stat">
     <span class="yh23-stat-icon"><?= icon('trending-up',16) ?></span>
     <div><strong>Daha güçlü etkileşim</strong><small>Markanızın görünürlüğünü artırın</small></div>
    </div>
   </div>
   <div class="yv-category-story-copy-v5">
    <div class="yv-kicker">Neden NetVera Teknoloji Yazılım?</div>
    <h2 id="yh23-story-heading"><?= e($category['name']) ?> ile markanızı <span>güvenle büyütün.</span></h2>
    
    <ul>
     <li><?= icon('check-circle',15) ?> Gerçek ve aktif hesaplar</li>
     <li><?= icon('check-circle',15) ?> Hızlı teslimat</li>
     <li><?= icon('check-circle',15) ?> Güvenli sipariş süreci</li>
     <li><?= icon('check-circle',15) ?> Düşüş garantisi</li>
     <li><?= icon('check-circle',15) ?> 7/24 canlı destek</li>
    </ul>
   </div>
   <aside class="yv-category-side-benefits-v5" aria-label="<?= e($category['name']) ?> avantajları">
    <h3><?= e($category['name']) ?> ile bir adım öne çıkın</h3>
    <div><?= icon('trending-up',16) ?> <span>Daha fazla görünürlük</span></div>
    <div><?= icon('users',16) ?> <span>Güçlü sosyal kanıt</span></div>
    <div><?= icon('heart',16) ?> <span>Gerçek etkileşim artışı</span></div>
    <div><?= icon('award',16) ?> <span>Marka bilinirliği</span></div>
    <div><?= icon('zap',16) ?> <span>Keşfet şansı</span></div>
   </aside>
  </div>
 </div>
</section>

<?php if(count($packages)>=3): ?>
<section class="yv-category-compare-v5"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">Paket karşılaştırması</div><h2>İhtiyacınıza uygun paketi seçin.</h2></div></div><div class="yv-compare-table-wrap-v5"><table><thead><tr><th>Özellikler</th><?php foreach(array_slice($packages,0,6) as $pkg): ?><th><?= e(excerpt(package_display_name($pkg),28)) ?></th><?php endforeach; ?></tr></thead><tbody><tr><td>Gerçek Kullanıcılar</td><?php foreach(array_slice($packages,0,6) as $pkg): ?><td><?= icon('check',12) ?></td><?php endforeach; ?></tr><tr><td>Hızlı Teslimat</td><?php foreach(array_slice($packages,0,6) as $pkg): ?><td><?= e($pkg['delivery_time'] ?: 'Hızlı') ?></td><?php endforeach; ?></tr><tr><td>Şifre Gerektirmez</td><?php foreach(array_slice($packages,0,6) as $pkg): ?><td><?= icon('check',12) ?></td><?php endforeach; ?></tr><tr><td>7/24 Destek</td><?php foreach(array_slice($packages,0,6) as $pkg): ?><td><?= icon('check',12) ?></td><?php endforeach; ?></tr><tr><td>Başlangıç Fiyatı</td><?php foreach(array_slice($packages,0,6) as $pkg): $p=(!empty($pkg['discount_price'])&&$pkg['discount_price']<$pkg['price'])?$pkg['discount_price']:$pkg['price']; ?><td><strong><?= money($p) ?></strong></td><?php endforeach; ?></tr></tbody></table></div></div></section>
<?php endif; ?>

<?php $reviews=$testimonialSection['extra']??[]; if(!empty($reviews)): ?>
<section class="yv-category-reviews-v5"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">Gerçek müşteri deneyimleri</div><h2>Müşterilerimiz Ne Diyor?</h2></div></div><div class="yv-category-review-grid-v5"><?php foreach(array_slice($reviews,0,4) as $review): ?><article><div><span><?= mb_strtoupper(mb_substr($review['name']??'M',0,1)) ?></span><b><?= e($review['name']??'Müşteri') ?></b><em><?php for($i=0;$i<($review['stars']??5);$i++): ?>★<?php endfor; ?></em></div><p><?= e($review['text']??'') ?></p></article><?php endforeach; ?></div></div></section>
<?php endif; ?>

<?php if(!empty($nvSeoData['main_question']) && !empty($nvSeoData['direct_answer'])): ?>
<section class="nv71-category-answer"><div class="container">
<h2><?= e($nvSeoData['main_question']) ?></h2><p><?= e($nvSeoData['direct_answer']) ?></p>
</div></section>
<?php endif; ?>
<?php if(!empty($faqs)): ?>
<section class="yv-category-faq-v5"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">Sıkça sorulan sorular</div><h2>Merak Ettikleriniz</h2></div><a href="/sss" class="yv-link-button">Tüm Soruları Gör <?= icon('arrow-right',11) ?></a></div><div class="yv-services-faq-grid"><?php foreach($faqs as $faq): ?><div class="premium-faq-item"><button type="button" class="premium-faq-question"><span><?= e($faq['question']) ?></span><?= icon('plus',12) ?></button><div class="premium-faq-answer"><?= nl2br(e($faq['answer'])) ?></div></div><?php endforeach; ?></div></div></section>
<?php endif; ?>

<section class="yv-category-cta-v5"><div class="container"><div><span><?= icon('zap',20) ?></span><strong><?= e($category['name']) ?>'da Bugün Fark Yaratın!</strong><small>Hemen paketleri inceleyin, hesabınızı güvenle büyütün.</small></div><a class="yv-btn yv-btn-light" href="#packages">Paketleri İncele <?= icon('arrow-right',11) ?></a></div></section>
</div>
<script>document.querySelectorAll('.premium-faq-question').forEach(q=>q.addEventListener('click',()=>{const a=q.nextElementSibling;a.style.display=a.style.display==='block'?'none':'block'}));</script>