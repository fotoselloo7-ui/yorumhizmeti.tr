<?php
$info=\App\Services\SocialPackageService::info($pkg);
$cardPrice=\App\Services\SocialStorefrontService::price($pkg);
$cardFeatures=array_slice(\App\Services\PackageHighlightsService::get((int)$pkg['id'],$pkg),0,4);
$deliveryInFeatures=!empty($pkg['delivery_time'])&&(bool)array_filter($cardFeatures,static fn($text)=>str_contains($text,$pkg['delivery_time']));
?>
<article class="social-package-card">
 <header><span class="social-package-brand"><?= icon($socialPage['icon'],22) ?> <?= e($socialPage['platform_label']) ?></span><?php if(!empty($pkg['badge'])): ?><span class="social-package-badge"><?= e($pkg['badge']) ?></span><?php endif; ?><h3><strong><?= number_format($info['quantity'],0,',','.') ?></strong> <?= e($info['unit']) ?></h3><p><?= e($info['service']) ?></p></header>
 <ul class="social-package-features"><?php foreach($cardFeatures as $n=>$feature): ?><li><?= icon(['shield-check','clipboard','lock','headphones'][$n],19) ?><span><?= e($feature) ?></span></li><?php endforeach; ?></ul>
 <?php if(!$deliveryInFeatures): ?><p class="social-delivery"><?= icon('clock',14) ?> <?= e($pkg['delivery_time']?:'Süre için paket detayını inceleyin') ?></p><?php endif; ?>
 <footer><div><?php if($cardPrice<(float)$pkg['price']): ?><del><?= money($pkg['price']) ?></del><?php endif; ?><strong><?= money($cardPrice) ?></strong><small>Toplam paket fiyatı</small></div><a class="social-button" href="/paket/<?= rawurlencode($pkg['slug']) ?>">Satın al <?= icon('arrow-right',14) ?></a></footer>
</article>
