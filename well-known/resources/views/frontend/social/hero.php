<?php $socialHeroTarget=$socialHeroTarget??'#packages'; ?>
<section class="social-hero" aria-labelledby="social-page-title">
 <div class="container">
  <nav class="social-breadcrumb" aria-label="İçerik yolu"><a href="/">Ana Sayfa</a><span>/</span><a href="<?= e(\App\Services\PublicSeoUrls::group('social')) ?>">Sosyal Medya</a><?php if(!empty($socialParent)): ?><span>/</span><a href="<?= e($socialParent['url']) ?>"><?= e($socialParent['name']) ?></a><?php endif; ?><span>/</span><span><?= e($socialCrumb??$socialPage['title']) ?></span></nav>
  <div class="social-hero-grid">
   <div class="social-hero-copy">
    <span class="social-eyebrow"><?= icon($socialPage['icon'],18) ?> NETVERA SOSYAL MEDYA</span>
    <h1 id="social-page-title" class="nv-managed-headline"><?= \App\Services\HeroHeadline::render((string)$socialPage['title']) ?></h1>
    <p><?= e($socialPage['summary']) ?></p>
    <div class="social-hero-actions"><a href="<?= e($socialHeroTarget) ?>" class="social-button social-button-light"><?= e($socialHeroAction??'Hizmetleri keşfet') ?> <?= icon('arrow-right',17) ?></a><a href="#social-guide" class="social-hero-help">Nasıl çalışır? <?= icon('arrow-up-right',14) ?></a></div>
    <ul class="social-hero-trust"><li><?= icon('lock',14) ?> Şifre paylaşmadan</li><li><?= icon('clipboard',14) ?> Net miktar ve fiyat</li><li><?= icon('headphones',14) ?> Sipariş desteği</li></ul>
   </div>
   <div class="social-hero-art"><picture><?php if($socialPage['mobile_image']!==''): ?><source media="(max-width:680px)" srcset="<?= e($socialPage['mobile_image']) ?>"><?php endif; ?><img src="<?= e($socialPage['image']) ?>" alt="<?= e($socialPage['image_alt']) ?>" width="1536" height="1024" fetchpriority="high" decoding="async"></picture><span class="social-art-label"><?= icon($socialPage['icon'],23) ?><span><?= e($socialPage['platform_label']) ?><small>İçeriğiniz. Hedefiniz. Seçiminiz.</small></span></span></div>
  </div>
 </div>
</section>
