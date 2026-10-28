<?php
if(!empty($socialPage)){require __DIR__.'/social/package.php';return;}
if (!function_exists('yvPkgDesign')) {
    function yvPkgDesign(string $name='', string $category=''): array {
        $s=mb_strtolower($name.' '.$category);
        if(str_contains($s,'instagram')) return ['instagram','instagram'];
        if(str_contains($s,'tiktok')) return ['tiktok','tiktok'];
        if(str_contains($s,'youtube')) return ['youtube','youtube'];
        if(str_contains($s,'facebook')) return ['facebook','facebook'];
        if(str_contains($s,'twitter')||str_contains($s,' x ')) return ['twitter','twitter'];
        if(str_contains($s,'threads')) return ['threads','threads'];
        if(str_contains($s,'telegram')) return ['telegram','telegram'];
        if(str_contains($s,'spotify')) return ['spotify','spotify'];
        if(str_contains($s,'discord')) return ['discord','discord'];
        if(str_contains($s,'linkedin')) return ['linkedin','linkedin'];
        if(str_contains($s,'twitch')) return ['twitch','twitch'];
        if(str_contains($s,'e-ticaret')||str_contains($s,'eticaret')) return ['ecommerce','store'];
        if(str_contains($s,'mobil')) return ['mobileapp','mobile-app'];
        if(str_contains($s,'içerik')||str_contains($s,'icerik')) return ['content','content-create'];
        if(str_contains($s,'grafik')) return ['graphic','palette'];
        if(str_contains($s,'yerel')) return ['local','local-business'];
        if(str_contains($s,'itibar')) return ['reputation','reputation'];
        if(str_contains($s,'reklam')) return ['ads','ads'];
        if(str_contains($s,'google')||str_contains($s,'seo')) return ['google','google'];
        if(str_contains($s,'web')||str_contains($s,'site')) return ['web','globe'];
        return ['default','package'];
    }
}
$lower=mb_strtolower((package_display_name($package)).' '.($package['category_name']??''));
[$pclass,$platform]=yvPkgDesign(package_display_name($package), $package['category_name']??'');
$nvPlatformKey=\App\Services\SocialPlatformIdentity::fromPackage($package);
if($nvPlatformKey!==null){
    $pclass=$nvPlatformKey;
    $platform=\App\Services\SocialPlatformIdentity::iconFor($nvPlatformKey);
}
$nvPlatformClass=\App\Services\SocialPlatformIdentity::classFor($nvPlatformKey);
$price=(!empty($package['discount_price'])&&$package['discount_price']<$package['price'])?$package['discount_price']:$package['price'];
$discount=(!empty($package['discount_price'])&&$package['discount_price']<$package['price']&&$package['price']>0)?round((1-$package['discount_price']/$package['price'])*100):0;
$reviews=$testimonialSection['extra']??[];
$nvParentData=(!empty($package['parent_category_slug']))
    ?['slug'=>$package['parent_category_slug'],'name'=>$package['parent_category_name']??'']
    :null;
$nvChildData=['slug'=>$package['category_slug']??'','name'=>$package['category_name']??''];
$nvShowChild=\App\Services\ServiceCategoryVisibility::show($nvChildData,$nvParentData);
$nvBreadcrumbTarget=\App\Services\ServiceCategoryVisibility::isLegacySoftware($package)
    ?'/hazir-scriptler'
    :\App\Services\PublicSeoUrls::category(
        !$nvShowChild && $nvParentData ? $nvParentData : $nvChildData,
        $nvShowChild?$nvParentData:null
    );
$nvBreadcrumbName=!$nvShowChild && $nvParentData
    ?($nvParentData['name']??'Hizmetler')
    :($package['category_name']??'Hizmetler');

$nvRealGallery=[];
if(!empty($package['image']))$nvRealGallery[]=[
    'src'=>upload_url($package['image']),
    'alt'=>($package['image_alt']??'')?:package_display_name($package)
];
foreach(\App\Services\ServicePackageGalleryService::images((int)$package['id']) as $nvPhoto){
    $nvRealGallery[]=['src'=>upload_url($nvPhoto),'alt'=>package_display_name($package).' çalışma görseli'];
}
$nvHasRealGallery=count($nvRealGallery)>0;
if(!$nvRealGallery)$nvRealGallery[]=[
    'src'=>demo_visual_url(package_display_name($package).' '.($package['category_name']??''),'product'),
    'alt'=>package_display_name($package).' hizmet görseli'
];
$nvServiceIcon=$platform==='store'?'shopping-bag':$platform;
?>
<div class="yv-product-v5 <?= e($nvPlatformClass) ?>">
<section class="yv-product-hero-v5 <?= e($pclass) ?> <?= e($nvPlatformClass) ?>">
 <div class="container">
  <div class="pkg-breadcrumb"><a href="/"><?= icon('home',11) ?> Ana Sayfa</a><span class="sep">/</span><a href="<?= e($nvBreadcrumbTarget) ?>"><?= e($nvBreadcrumbName) ?></a><span class="sep">/</span><span><?= e(package_display_name($package)) ?></span></div>
  <div class="yv-product-hero-grid-v5">
   <div class="yv-product-hero-copy-v5">
    <div class="yv-product-brand-v5"><span><?= icon($platform,30) ?></span><small><?= e($package['category_name']??'Premium Hizmet') ?></small></div>
    <h1><?= e(package_display_name($package)) ?></h1>
    <p><?= e($package['short_description'] ?: 'Markanız için güvenli, hızlı ve profesyonel dijital hizmet paketi.') ?></p>
    <div class="yv-product-proof-v5">
     <div><?= icon('clipboard-list',15) ?><span><b>İhtiyaç Analizi</b><small>Hizmete özel kapsam</small></span></div>
     <div><?= icon('calendar-check',15) ?><span><b>Planlı Süreç</b><small><?= e($package['delivery_time'] ?: 'Teklifte netleşir') ?></small></span></div>
     <div><?= icon('shield',15) ?><span><b>Şeffaf Kapsam</b><small>Koşullar önceden netleşir</small></span></div>
     <div><?= icon('headphones',15) ?><span><b>İletişim</b><small>Çalışma boyunca destek</small></span></div>
    </div>
   </div>
   <div class="yv-product-hero-visual-v5 nv-product-hero-graphic" aria-label="<?= e($package['category_name']??'Dijital hizmet') ?> sunumu">
      <div class="nv-product-hero-graphic-inner">
        <div class="nv-product-hero-icon"><?= icon($platform,46) ?></div>
        <span class="nv-product-hero-kicker">NETVERA / DİJİTAL HİZMETLER</span>
        <strong><?= e($package['category_name']??'Profesyonel Hizmet') ?></strong>
        <p><?= e(excerpt((string)($package['short_description']??'Projenize uygun çözüm'),110)) ?></p>
        <div class="nv-product-hero-steps"><span><?= icon('clipboard-list',14) ?> Planlama</span><span><?= icon('layers',14) ?> Kapsam</span><span><?= icon('check-circle',14) ?> Teslim</span></div>
      </div>
   </div>
  </div>
 </div>
</section>

<section class="yv-product-stage-v5">
 <div class="container">
  <div class="yv-product-stage-grid-v5">
   <div class="yv-product-gallery-v5">
    <div class="yv-product-gallery-main-v5 nv-real-product-gallery" data-nv-service-gallery>
       <img class="nv-service-gallery-main-image" src="<?= e($nvRealGallery[0]['src']) ?>" alt="<?= e($nvRealGallery[0]['alt']) ?>" decoding="async">
       <span class="nv-service-gallery-label"><?= icon('image',14) ?> <?= e($package['category_name']??'Hizmet görseli') ?></span>
    </div>
    <?php if(count($nvRealGallery)>1): ?>
    <div class="nv-real-product-gallery-thumbs" role="group" aria-label="Hizmet galerisindeki diğer fotoğraflar">
      <?php foreach($nvRealGallery as $i=>$img): ?>
      <button type="button" data-nv-gallery-thumb
              data-nv-gallery-src="<?= e($img['src']) ?>"
              data-nv-gallery-alt="<?= e($img['alt']) ?>"
              aria-label="Galeri görseli <?= $i+1 ?>" aria-pressed="<?= $i===0?'true':'false' ?>">
        <img src="<?= e($img['src']) ?>" alt="" loading="lazy">
      </button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="yv-product-info-strip-v5">
     <div><?= icon('file-text',18) ?><span><strong>Hizmet Kapsamı</strong><small>Paket detayında</small></span></div>
     <div><?= icon('shield',18) ?><span><strong>Güvenli Süreç</strong><small>Kontrollü iletişim</small></span></div>
     <div><?= icon('calendar',18) ?><span><strong>Teslim Planı</strong><small><?= e($package['delivery_time'] ?: 'Teklifte belirlenir') ?></small></span></div>
     <div><?= icon('target',18) ?><span><strong>Sipariş Takibi</strong><small>Hesabınız üzerinden</small></span></div>
    </div>
   </div>

   <aside class="yv-product-buy-v5">
    <div class="yv-product-badges-v5"><?php if(!empty($package['badge'])): ?><span><?= e($package['badge']) ?></span><?php elseif(!empty($quoteMode)): ?><span>Size özel proje kapsamı</span><?php else: ?><span>Paket detayları</span><?php endif; ?><?php if($discount && empty($quoteMode)): ?><b>%<?= $discount ?> İndirim</b><?php endif; ?></div>
    <h2><?= e(package_display_name($package)) ?></h2>
    <div class="yv-product-rating-v5 nv-product-process">
      <b><?= icon('shield-check',14) ?> <?= !empty($quoteMode)?'Güvenli Teklif Süreci':'Güvenli Sipariş' ?></b>
      <span><?= !empty($quoteMode)?'Kapsam ve teslimleri birlikte netleştirelim.':'İşlem durumunu hesabınızdan takip edin.' ?></span>
    </div>
    <div class="yv-product-price-v5"><?php if(!empty($quoteMode)): ?><strong>Projenize Özel Teklif</strong><?php else: ?><?php if($discount): ?><del><?= money($package['price']) ?></del><?php endif; ?><strong><?= money($price) ?></strong><?php if($discount): ?><span>%<?= $discount ?> indirim</span><?php endif; ?><?php endif; ?></div>
    <p><?= e((string)($package['short_description'] ?: 'Seçtiğiniz dijital hizmet paketinin ayrıntılarını aşağıda inceleyebilirsiniz.')) ?></p>

    <?php if(!empty($quoteMode)): ?>
      <div class="nv-quote-product"><p>Hizmet kapsamı, teslimler, süre ve ihtiyaç duyulan entegrasyonlar değerlendirilerek size özel teklif hazırlanır.</p><a class="yv-product-add-v5" href="<?= e(\App\Services\ServiceQuotePolicy::url($package)) ?>"><?= icon('message-circle',16) ?> <?= e(\App\Services\ServicePriceMode::cta($package)) ?></a></div>
    <?php else: ?>
    <?php if(!empty($socialSale)): ?>
    <div class="nv-social-order-summary" aria-label="Seçilen sosyal medya paketi">
      <span>Seçtiğiniz paket</span><strong><?= e($socialSale['label']) ?></strong>
      <p><?= e($socialSale['service']) ?> · Fiyat bu miktarın tamamı içindir.</p>
    </div>
    <?php elseif(!empty($variantPackages) && count($variantPackages) > 1): ?>
    <div class="yv-product-variants-v9">
      <label>Paket Seçimi <?= icon('help-circle',11) ?></label>
      <div>
        <?php foreach($variantPackages as $variant): $variantPrice=(!empty($variant['discount_price'])&&$variant['discount_price']<$variant['price'])?$variant['discount_price']:$variant['price']; ?>
        <a class="<?= (int)$variant['id']===(int)$package['id']?'active':'' ?>" href="/paket/<?= e($variant['slug']) ?>"><strong><?= e(excerpt(package_display_name($variant),28)) ?></strong><small><?= money($variantPrice) ?></small></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php $saleClosed=!empty($socialSale) && ($socialSale['quantity']<1 || $socialSale['unit']==='' || !$socialSale['mapping_matches']); ?>
    <?php if($saleClosed): ?><p role="status">Bu paketin miktar yapılandırması tamamlanana kadar sipariş alınamıyor. Lütfen destek ekibine ulaşın.</p><?php endif; ?>
    <form id="packageForm" method="POST" action="/sepet/ekle" class="yv-buy-form-v5">
     <?= csrfField() ?><input type="hidden" name="package_id" value="<?= $package['id'] ?>">
     <?php if(!empty($fields)): foreach($fields as $field): ?><div class="yv-form-group"><label><?= e($field['field_label']) ?><?= $field['is_required']?' *':'' ?></label>
      <?php if($field['field_type']==='textarea'): ?><textarea class="form-control" name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" rows="2" placeholder="<?= e($field['placeholder']??'') ?>" <?= $field['is_required']?'required':'' ?>></textarea>
      <?php elseif($field['field_type']==='select'): ?><select class="form-control" name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" <?= $field['is_required']?'required':'' ?>><option value="">Seçiniz</option><?php foreach(explode(',',$field['options']??'') as $opt): if(trim($opt)!==''): ?><option value="<?= e(trim($opt)) ?>"><?= e(trim($opt)) ?></option><?php endif; endforeach; ?></select>
      <?php else: ?><input class="form-control" type="<?= $field['field_type']==='url'?'url':'text' ?>" name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" placeholder="<?= e($field['placeholder']??'') ?>" <?= $field['is_required']?'required':'' ?>><?php endif; ?>
     </div><?php endforeach; endif; ?>
     <?php if(empty($socialSale) && ($package['max_quantity']??1)>1): ?><div class="yv-product-qty-v9"><label>Adet</label><div><button type="button" onclick="stepProductQty(-1)">−</button><input id="productQty" type="number" name="quantity" value="<?= (int)($package['min_quantity']?:1) ?>" min="<?= (int)($package['min_quantity']?:1) ?>" max="<?= (int)$package['max_quantity'] ?>"><button type="button" onclick="stepProductQty(1)">+</button></div></div><?php else: ?><input type="hidden" name="quantity" value="1"><?php endif; ?>
     <button class="yv-product-add-v5" type="submit" <?= $saleClosed?'disabled':'' ?>><?= icon('shopping-cart',15) ?> Sepete Ekle</button>
     <?php if(!empty($socialSale)): ?>
     <button class="yv-product-now-v5" type="submit" name="checkout" value="1" <?= $saleClosed?'disabled':'' ?>><?= icon('zap',15) ?> Hemen Satın Al</button>
     <?php else: ?><button class="yv-product-now-v5" type="button" onclick="buyNow()"><?= icon('zap',15) ?> Hemen Satın Al</button><?php endif; ?>
    </form>
    <?php endif; ?>
    <div class="yv-product-mini-trust-v5">
       <div><?= icon('shield-check',14) ?><span><strong><?= !empty($quoteMode)?'Teklif Süreci':'Güvenli Ödeme' ?></strong><small><?= !empty($quoteMode)?'Önce kapsam onayı':'Korunan ödeme adımı' ?></small></span></div>
       <div><?= icon('calendar-check',14) ?><span><strong>Teslim Planı</strong><small><?= e($package['delivery_time'] ?: 'Kapsama göre') ?></small></span></div>
       <div><?= icon('headphones',14) ?><span><strong>İletişim</strong><small>Destek kanalları</small></span></div>
    </div>
   </aside>
  </div>
 </div>
</section>

<section class="yv-product-content-v5"><div class="container">
 <nav class="yv-product-tabs-v5 nv-service-section-nav" aria-label="Paket sayfası bölümleri"><a class="active" href="#nv-product-description">Açıklama</a><a href="#nv-product-features">Özellikler</a><?php if(!empty($serviceReferences)): ?><a href="#hizmet-referanslari">Referanslar</a><?php endif; ?><a href="#nv-product-faq">Sık Sorulan Sorular</a></nav>
 <article class="yv-product-copy-v5" id="nv-product-description"><h2><?= e(package_display_name($package)) ?> Nedir?</h2><div class="blog-content"><?php if(!empty($package['description'])): ?><?= $package['description'] ?><?php else: ?><p>Bu paket, ihtiyacınız olan dijital hizmeti güvenli, hızlı ve kolay bir sipariş deneyimiyle sunar. Ödeme ve gerekli bilgiler tamamlandıktan sonra siparişiniz işleme alınır ve hesabınızdan takip edilebilir.</p><?php endif; ?></div></article>
 <div class="yv-product-gains-v5" id="nv-product-features"><h3>Paket Özellikleri</h3><div>
 <?php foreach(\App\Services\PackageHighlightsService::get((int)$package['id'],$package) as $highlight): ?>
  <span><?= icon('check-circle',13) ?> <?= e($highlight) ?></span>
 <?php endforeach; ?>
 </div></div>
</div></section>

<?php if(!empty($reviews)): ?>
<section class="yv-product-reviews-v5"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">Müşteri yorumları</div><h2>Gerçek kullanıcı deneyimleri.</h2></div><div class="yv-product-rating-summary-v5"><span><?= icon('message-square',18) ?></span><small>Paylaşılan müşteri görüşleri</small></div></div><div class="yv-product-review-grid-v5"><?php foreach(array_slice($reviews,0,3) as $review): ?><article><div><span><?= mb_strtoupper(mb_substr($review['name']??'M',0,1)) ?></span><b><?= e($review['name']??'Müşteri') ?></b><em><?php for($i=0;$i<($review['stars']??5);$i++): ?>★<?php endfor; ?></em></div><p><?= e($review['text']??'') ?></p></article><?php endforeach; ?></div></div></section>
<?php endif; ?>

<section class="yv-product-why-v5"><div class="container"><div><div class="yv-kicker">Neden NetVera Teknoloji Yazılım?</div><h2>Güvenli, hızlı ve kullanıcı dostu.</h2></div><div class="yv-product-why-stats-v5"><span><?= icon('users',18) ?><b>İhtiyaç Analizi</b><small>Proje kapsamı</small></span><span><?= icon('star-fill',18) ?><b>Şeffaf Süreç</b><small>Açık teslimler</small></span><span><?= icon('headphones',18) ?><b>İletişim</b><small>Destek kanalları</small></span><span><?= icon('shield',18) ?><b>Güvenli İşlem</b><small>Kontrollü süreç</small></span></div></div></section>

<?php if(!empty($faqs)): ?><section class="yv-product-faq-v5" id="nv-product-faq"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">Sıkça sorulan sorular</div><h2>Bu paket hakkında merak edilenler.</h2></div></div><div class="yv-services-faq-grid"><?php foreach(array_slice($faqs,0,6) as $faq): ?><div class="premium-faq-item"><button type="button" class="premium-faq-question"><span><?= e($faq['question']) ?></span><?= icon('plus',12) ?></button><div class="premium-faq-answer"><?= nl2br(e($faq['answer'])) ?></div></div><?php endforeach; ?></div></div></section><?php endif; ?>

<?php require __DIR__.'/partials/service-references.php'; ?>
<?php $nvSeoGuide=\App\Services\ServiceEditorialBlocks::package($package,!empty($quoteMode)); require __DIR__.'/partials/service-seo-guide.php'; ?>
<?php if(!empty($relatedPackages)): ?><section class="yv-product-related-v5"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">İlgili paketler</div><h2>İlgili Paketler</h2><p>İşletmenizi daha da güçlendirecek diğer popüler paketlere göz atın.</p></div><a class="yv-link-button" href="/kategoriler">Tüm Paketleri Gör <?= icon('arrow-right',11) ?></a></div><div class="yv-product-related-grid-v5"><?php foreach($relatedPackages as $rp): $rpPrice=(!empty($rp['discount_price'])&&$rp['discount_price']<$rp['price'])?$rp['discount_price']:$rp['price']; [$rpClass,$rpIcon]=yvPkgDesign(package_display_name($rp), $rp['category_name']??''); ?><a class="yv-product-related-card-v5 <?= e($rpClass) ?>" href="/paket/<?= e($rp['slug']) ?>"><?php if(!empty($rp['image'])): ?><img src="<?= e(upload_url($rp['image'])) ?>" alt="<?= e($rp['image_alt']??$rp['name']) ?>"><?php else: ?><div class="art <?= e($rpClass) ?>"><span><?= icon($rpIcon,28) ?></span></div><?php endif; ?><div><small><?= e($rp['category_name']??'Hizmet') ?></small><h3><?= e(package_display_name($rp)) ?></h3><ul><li><?= icon('check',10) ?> Hızlı teslimat</li><li><?= icon('check',10) ?> Güvenli işlem</li><li><?= icon('check',10) ?> 7/24 destek</li></ul><strong><?= \App\Services\ServiceQuotePolicy::package($rp)?'Fiyat Al':money($rpPrice) ?></strong><em>Detayları İncele <?= icon('arrow-right',10) ?></em></div></a><?php endforeach; ?></div></div></section><?php endif; ?>
</div>
<?php if(empty($socialSale) && empty($quoteMode)): ?><script>
function stepProductQty(delta){const q=document.getElementById('productQty');if(!q)return;const min=parseInt(q.min||'1',10),max=parseInt(q.max||'999999',10);q.value=Math.min(max,Math.max(min,parseInt(q.value||min,10)+delta));}
function buyNow(){const f=document.getElementById('packageForm');if(!f.reportValidity())return;const b=f.querySelector('.yv-product-now-v5');b.disabled=true;b.textContent='Yönlendiriliyor...';fetch(f.action,{method:'POST',body:new FormData(f),headers:{'X-Requested-With':'XMLHttpRequest'}}).finally(()=>location.href='/odeme')}
</script><?php endif; ?>
