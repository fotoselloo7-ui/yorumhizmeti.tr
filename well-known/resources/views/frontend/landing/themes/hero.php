<?php
use App\Services\LandingContentService as HeroCopy;
// Highlight the service without replacing, duplicating or rewriting the stored H1.
$heroTitle=e($d['title']);
$serviceAt=$d['service']!==''?mb_stripos($d['title'],$d['service'],0,'UTF-8'):false;
if($serviceAt!==false){$serviceLength=mb_strlen($d['service'],'UTF-8');$heroTitle=e(mb_substr($d['title'],0,$serviceAt,'UTF-8')).'<span class="nvl-hero-title-accent">'.e(mb_substr($d['title'],$serviceAt,$serviceLength,'UTF-8')).'</span>'.e(mb_substr($d['title'],$serviceAt+$serviceLength,null,'UTF-8'));}
// All themes share a text-only hero over the page's managed background image.
$heroCopy=static function()use($d,$heroTitle):void{ ?><div class="nvl-hero-copy"><span class="nvl-eyebrow"><?= icon('map-pin',14) ?> <?= e($d['city']) ?> / <?= e($d['service']) ?></span><h1><?= $heroTitle ?></h1><div class="nvl-hero-description"><?= HeroCopy::rich($d['summary']) ?></div><div class="nvl-actions"><a class="nvl-button" href="/iletisim"><?= e($d['cta_label']?:'Projenizi konuşalım') ?> <?= icon('arrow-up-right',17) ?></a><a class="nvl-secondary" href="#landing-content">Çözümleri keşfedin <?= icon('arrow-down',16) ?></a></div><ul class="nvl-hero-benefits"><li><?= icon('target',17) ?><span>İhtiyaca özel kapsam</span></li><li><?= icon('layers',17) ?><span>Planlı çalışma</span></li><li><?= icon('message-circle',17) ?><span>Doğrudan iletişim</span></li></ul></div><?php };
?>
<section class="nvl-hero nvl-hero-<?= e($heroVariant) ?> <?= !empty($d['location_image'])?'nvl-has-location':'' ?>">
<?php if(!empty($d['location_image'])): ?><div class="nvl-city-backdrop" aria-hidden="true"><img src="<?= e($d['location_image']) ?>" alt="" width="1600" height="900" fetchpriority="high"></div><?php endif; ?>
<div class="nvl-container"><nav class="nvl-breadcrumb" aria-label="Konum"><a href="/">Ana Sayfa</a><span>/</span><a href="/hizmet-bolgeleri">Hizmet Bölgeleri</a><span>/</span><span><?= e($d['city']) ?></span></nav>
<div class="nvl-stage"><?php $heroCopy(); ?></div>

</div></section>
