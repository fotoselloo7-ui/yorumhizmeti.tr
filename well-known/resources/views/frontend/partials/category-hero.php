<?php
$heroCategory = $heroCategory ?? $category;
$heroPlatformIcon = $heroPlatformIcon ?? $platformIcon;
$heroBreadcrumbs = $heroBreadcrumbs ?? [['Ana Sayfa','/'],['Hizmetler','/kategoriler'],[$heroCategory['name'],null]];
if (!empty($heroCategory['parent_category_slug']) && count($heroBreadcrumbs) === 3) {
    array_splice($heroBreadcrumbs,2,0,[[$heroCategory['parent_category_name'],\App\Services\PublicSeoUrls::path('/kategori',$heroCategory['parent_category_slug'])]]);
}
?>
<section class="nv-category-hero" aria-labelledby="category-hero-heading">
 <div class="container">
  <nav class="breadcrumb" aria-label="İçerik yolu"><?php foreach($heroBreadcrumbs as $index=>[$label,$href]): ?><?php if($index>0): ?><span class="separator" aria-hidden="true">/</span><?php endif; ?><?php if($href): ?><a href="<?= e($href) ?>"><?= e($label) ?></a><?php else: ?><span aria-current="page"><?= e($label) ?></span><?php endif; ?><?php endforeach; ?></nav>
  <div class="nv-category-hero-grid">
   <div class="nv-category-hero-copy">
    <div class="nv-category-hero-kicker"><span><?= icon($heroPlatformIcon,18) ?></span><?= e($categoryHero['eyebrow']) ?></div>
    <h1 id="category-hero-heading"><span><?= e($categoryHero['lead']) ?></span><?php if($categoryHero['accent']!==''): ?> <span class="nv-category-hero-accent"><?= e($categoryHero['accent']) ?></span><?php endif; ?></h1>
    <p class="nv-category-hero-description"><?= e($categoryHero['description']) ?></p>
    <ul class="nv-category-hero-facts"><?php foreach($categoryHero['facts'] as [$factIcon,$factText]): ?><li><?= icon($factIcon,16) ?><span><?= e($factText) ?></span></li><?php endforeach; ?></ul>
    <div class="nv-category-hero-actions"><a class="nv-category-hero-cta" href="#packages"><?= e($categoryHero['cta']) ?> <?= icon('arrow-right',16) ?></a><span>NetVera Teknoloji Yazılım</span></div>
   </div>
   <div class="nv-category-hero-media <?= $categoryHero['custom']?'is-custom':'' ?>" data-hero-visual="<?= e($categoryHero['visual']) ?>">
    <img src="<?= e($categoryHero['src']) ?>" alt="<?= e($categoryHero['alt']) ?>" width="1536" height="1024" loading="eager" fetchpriority="high" decoding="async">
    <div class="nv-category-hero-context"><span><?= icon($heroPlatformIcon,22) ?></span><div><small><?= e($heroCategory['parent_category_name'] ?? 'NetVera çözümleri') ?></small><strong><?= e($heroCategory['name']) ?></strong></div></div>
   </div>
  </div>
 </div>
</section>
