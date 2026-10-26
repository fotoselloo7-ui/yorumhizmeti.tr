<?php
use App\Services\LandingContentService as Editorial;
use App\Services\RegionalLandingService as Regional;
$d=$landing;$seo=$d['seo'];
$available=['overview'=>$d['local_context']!=='','content'=>(bool)$d['sections'],'services'=>(bool)$landingServices,'scripts'=>(bool)$landingScripts,'blog'=>(bool)$landingBlogs,'references'=>(bool)$landingReferences,'faq'=>(bool)$d['faqs'],'answer'=>$seo['direct_answer']!==''||$seo['geo_summary']!==''];
$visible=array_values(array_filter($d['section_order'],static fn($key)=>in_array($key,$d['enabled_sections'],true)&&$available[$key]));
$mode=['remote'=>'Uzaktan / çevrim içi','hybrid'=>'Uzaktan ve planlı görüşme','onsite'=>'Planlı yerinde çalışma'][$d['working_mode']];
?>
<main class="regional-landing landing-theme-<?= e($d['theme']) ?> landing-layout-<?= e($d['layout']) ?>">
<?php if($landingPreview): ?><div class="landing-preview container" role="status">Taslak önizleme • Yalnızca yetkili yönetici görebilir. Arama motorlarına kapalıdır. <a href="/admin/konumsal-sayfalar/<?= (int)$landingRow['id'] ?>/duzenle">Editöre dön</a></div><?php endif; ?>
<section class="landing-hero"><?php if(!empty($d['location_image'])): ?><div class="landing-hero-background" aria-hidden="true"><img src="<?= e($d['location_image']) ?>" alt="" width="1600" height="900" fetchpriority="high"></div><?php endif; ?><div class="container">
    <nav class="landing-breadcrumb" aria-label="Konum"><a href="/">Ana Sayfa</a><span>/</span><a href="/hizmet-bolgeleri">Hizmet Bölgeleri</a><span>/</span><span><?= e($d['city']) ?></span></nav>
    <div class="landing-hero-grid landing-hero-text-only"><div class="landing-hero-copy"><span class="landing-eyebrow"><span class="landing-status-dot" aria-hidden="true"></span> NETVERA / <?= e($d['service']) ?></span><h1><?= e($d['title']) ?></h1><div class="landing-hero-description"><?= Editorial::rich($d['summary']) ?></div><div class="landing-actions"><a class="landing-button" href="/iletisim"><?= e($d['cta_label']?:'Projenizi konuşalım') ?><?= icon('arrow-up-right',18) ?></a><a class="landing-text-link" href="#landing-content">Çözümleri keşfedin <?= icon('arrow-down',16) ?></a></div><div class="landing-hero-signature"><?= icon('layers',20) ?><span>Yazılım, strateji ve tasarım.<small>Tek ekip, projenize özel bir yaklaşım.</small></span></div></div>
    </div>

</div></section>
<span id="landing-content" class="nvl-sr-anchor" aria-hidden="true"></span>
<?php foreach($visible as $landingSection)require __DIR__.'/sections.php'; ?>
<section class="landing-contact"><div class="container"><div class="landing-contact-shell"><div><span class="landing-eyebrow">BİRLİKTE BİR SONRAKİ ADIMA</span><h2><?= e($d['cta_title']?:'Bir sonraki adımı birlikte planlayalım.') ?></h2><div class="landing-prose"><?= Editorial::rich($d['cta_summary']) ?></div></div><nav aria-label="İletişim seçenekleri"><?php foreach($landingContacts as $n=>$contact): ?><a class="<?= $n===0?'landing-button':'landing-contact-link' ?>" href="<?= e($contact['url']) ?>" <?= str_starts_with($contact['url'],'https:')?'target="_blank" rel="noopener noreferrer"':'' ?>><?= icon($contact['icon'],17) ?><?= e($n===0?($d['cta_label']?:$contact['label']):$contact['label']) ?></a><?php endforeach; ?></nav></div></div></section>
<p><?php if($seo['author_name']!==''): ?>Hazırlayan: <?= e($seo['author_name']) ?>. <?php endif; ?><?php if($seo['reviewer_name']!==''): ?>İçerik kontrolü: <?= e($seo['reviewer_name']) ?>. <?php endif; ?><?php if($seo['last_reviewed']!==''): ?>Son kontrol: <time datetime="<?= e($seo['last_reviewed']) ?>"><?= e($seo['last_reviewed']) ?></time><?php endif; ?></p><?php if($seo['sources']!==''): ?><nav aria-label="Kaynaklar"><strong>Kaynaklar</strong><?php foreach(explode("\n",$seo['sources'])as $source): ?><a href="<?= e($source) ?>" target="_blank" rel="noopener noreferrer"><?= e(parse_url($source,PHP_URL_HOST)) ?><?= icon('arrow-up-right',14) ?></a><?php endforeach; ?></nav><?php endif; ?></div></section><?php endif; ?>
</main>
