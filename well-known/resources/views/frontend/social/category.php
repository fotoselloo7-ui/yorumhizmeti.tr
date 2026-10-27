<?php
use App\Services\SocialStorefrontService as SocialUi;
$socialGroups=SocialUi::directory($socialCatalog['groups']);
$socialParent=!empty($category['parent_category_slug'])?['name'=>$category['parent_category_name'],'url'=>'/kategori/'.rawurlencode($category['parent_category_slug'])]:null;
$socialCrumb=$serviceLanding['name']??$category['name'];
$socialHeroAction=$socialCatalog['show_services']?'Hizmetleri keşfet':'Paketleri karşılaştır';
?>
<main class="social-storefront social-brand-<?= e($socialPage['platform']) ?>">
 <?php require __DIR__.'/hero.php'; ?>
 <section class="social-catalog-section" id="packages"><div class="container">
  <div class="social-workspace">
   <aside class="social-sidebar <?= !$subCategories?'social-sidebar-empty':'' ?>">
    <div class="social-platform-card"><span class="social-platform-symbol"><?= icon($socialPage['icon'],40) ?></span><h2><?= e($socialPage['platform_label']) ?> hizmetleri</h2><p>İhtiyacınıza uygun hizmeti bulun; miktar ve kapsamı birlikte değerlendirin.</p><dl><div><dt>Hizmet türü</dt><dd><?= count($socialGroups) ?></dd></div><div><dt>Listelenen paket</dt><dd><?= array_sum(array_map(static fn($g)=>count($g['packages']),$socialGroups)) ?></dd></div></dl></div>
    <?php if($subCategories): ?><details class="social-side-nav" open><summary>Hizmet kategorileri <?= icon('chevron-down',14) ?></summary><nav aria-label="Alt kategoriler"><?php foreach($subCategories as $sub): $intent=SocialUi::intent($sub['name']); ?><a href="/kategori/<?= rawurlencode($sub['slug']) ?>"><?= icon($intent['icon'],16) ?><span><?= e($sub['name']) ?></span><?= icon('arrow-up-right',13) ?></a><?php endforeach; ?></nav></details><?php endif; ?>
    <div class="social-help-card"><?= icon('message-circle',24) ?><h3>Kararsız mı kaldınız?</h3><p>Hizmet ve sipariş koşulları hakkında destek alın.</p><a href="/iletisim">Bize ulaşın <?= icon('arrow-right',14) ?></a></div>
   </aside>
   <div class="social-results">
    <header class="social-section-heading"><div><span class="social-eyebrow">HEDEFİNİZE GÖRE SEÇİN</span><h2><?= e($socialCatalog['selected']['name']??($socialCatalog['show_services']?'Size uygun hizmet, tek yerde.':'Paketler ve fiyatlar')) ?></h2><p><?= $socialCatalog['show_services']?'Önce hizmet türünü, sonra ihtiyacınıza uygun miktarı seçin.':'Gösterilen tutar, paketteki miktarın tamamı içindir.' ?></p></div><span class="social-count"><?= $socialCatalog['show_services']?count($socialGroups).' hizmet':count($packages).' paket' ?></span></header>
    <?php if($socialCatalog['show_services']): ?>
    <div class="social-intent-tabs" role="group" aria-label="Hizmet türlerini filtrele" data-social-filter><button type="button" class="is-active" aria-pressed="true" data-intent="all">Tümü</button><?php $intents=[];foreach($socialGroups as $g)$intents[$g['intent']['key']]=$g['intent']['label'];foreach($intents as $key=>$label): ?><button type="button" aria-pressed="false" data-intent="<?= e($key) ?>"><?= e($label) ?></button><?php endforeach; ?></div>
    <?php else: ?>
    <nav class="social-service-nav" aria-label="Hizmet seçimi"><a href="/kategori/<?= rawurlencode($category['slug']) ?>">Tüm hizmetler</a><?php foreach($socialGroups as $g): ?><a href="<?= e($g['url']) ?>" <?= ($socialCatalog['selected']['key']??'')===$g['key']?'aria-current="page"':'' ?>><?= e($g['name']) ?></a><?php endforeach; ?></nav>
    <?php endif; ?>
    <form class="social-search" method="get" role="search" aria-label="Sosyal medya paket araması"><label class="social-search-input"><?= icon('search',17) ?><span class="social-visually-hidden">Hizmet veya paket ara</span><input name="q" type="search" value="<?= e($searchQuery) ?>" maxlength="100" placeholder="Hizmet veya paket ara…"></label><label><span class="social-visually-hidden">Paket sıralaması</span><select name="sort"><option value="recommended">Önerilen sıralama</option><?php foreach(['price_asc'=>'Fiyat: Artan','price_desc'=>'Fiyat: Azalan','featured'=>'Öne çıkanlar']as $value=>$text): ?><option value="<?= $value ?>" <?= $currentSort===$value?'selected':'' ?>><?= $text ?></option><?php endforeach; ?></select></label><button class="social-button" type="submit">Uygula</button></form>
    <?php if($socialCatalog['show_services']): ?>
    <div class="social-service-grid"><?php foreach($socialGroups as $g): ?><a class="social-service-card" href="<?= e($g['url']) ?>" data-social-intent="<?= e($g['intent']['key']) ?>"><span class="social-service-icon"><?= icon($g['intent']['icon'],25) ?></span><div class="social-service-copy"><h3><?= e($g['name']) ?></h3><small><strong><?= money($g['price']) ?></strong> başlangıç fiyatı <span>· <?= count($g['packages']) ?> paket</span></small></div><span class="social-card-arrow" aria-hidden="true"><?= icon('arrow-right',17) ?></span></a><?php endforeach; ?></div>
    <p class="social-filter-feedback" role="status" aria-live="polite"></p>
    <?php elseif($packages): ?>
    <div class="social-package-grid"><?php foreach($packages as $pkg): require __DIR__.'/package-card.php'; endforeach; ?></div>
    <?php else: ?><div class="social-empty"><?= icon('search',32) ?><h3>Uygun paket bulunamadı.</h3><p>Aramanızı değiştirin veya tüm hizmetleri görüntüleyin.</p><a href="/kategori/<?= rawurlencode($category['slug']) ?>" class="social-button">Filtreleri temizle</a></div><?php endif; ?>
    <div class="social-selection-note"><?= icon('shield-check',22) ?><div><strong>Ne satın aldığınızı bilin.</strong><p>Miktar, toplam fiyat, tahmini süre ve hizmet koşulları paket detayında birlikte gösterilir.</p></div><a href="#social-guide" aria-label="Satın alma rehberini incele"><?= icon('arrow-down',19) ?></a></div>
   </div>
  </div>
 </div></section>
 <?php require __DIR__.'/editorial.php'; ?>
</main>
