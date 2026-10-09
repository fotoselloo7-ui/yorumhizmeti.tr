<?php
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
$price=(!empty($package['discount_price'])&&$package['discount_price']<$package['price'])?$package['discount_price']:$package['price'];
$discount=(!empty($package['discount_price'])&&$package['discount_price']<$package['price']&&$package['price']>0)?round((1-$package['discount_price']/$package['price'])*100):0;
$reviews=$testimonialSection['extra']??[];
?>
<div class="yv-product-v5">
<section class="yv-product-hero-v5 <?= e($pclass) ?>">
 <div class="container">
  <div class="pkg-breadcrumb"><a href="/"><?= icon('home',11) ?> Ana Sayfa</a><span class="sep">/</span><a href="/kategori/<?= e($package['category_slug']??'') ?>"><?= e($package['category_name']??'Hizmetler') ?></a><span class="sep">/</span><span><?= e(package_display_name($package)) ?></span></div>
  <div class="yv-product-hero-grid-v5">
   <div class="yv-product-hero-copy-v5">
    <div class="yv-product-brand-v5"><span><?= icon($platform,30) ?></span><small><?= e($package['category_name']??'Premium Hizmet') ?></small></div>
    <h1><?= e(package_display_name($package)) ?></h1>
    <p><?= e($package['short_description'] ?: 'Markanız için güvenli, hızlı ve profesyonel dijital hizmet paketi.') ?></p>
    <div class="yv-product-proof-v5">
     <div><?= icon('users',15) ?><span><b>Gerçek Kullanıcılar</b><small>%100 organik</small></span></div>
     <div><?= icon('zap',15) ?><span><b>Hızlı Teslimat</b><small><?= e($package['delivery_time'] ?: 'Hızlı başlangıç') ?></small></span></div>
     <div><?= icon('shield',15) ?><span><b>Güvenli Ödeme</b><small>256 Bit SSL</small></span></div>
     <div><?= icon('headphones',15) ?><span><b>Müşteri Desteği</b><small>7/24 canlı destek</small></span></div>
    </div>
   </div>
   <div class="yv-product-hero-visual-v5">
    <div class="yv-product-device-v5"><div class="screen"><span><?= icon($platform,40) ?></span><div class="rating">4.9 <b>★★★★★</b><small>1.324 değerlendirme</small></div></div></div>
    <span class="yv-product-orbit-v5 brand"><?= icon($platform,28) ?></span>
    <span class="yv-product-float-v5 a"><?= icon('check-circle',12) ?> +500 gerçek etkileşim</span>
    <span class="yv-product-float-v5 b"><?= icon('shield',12) ?> Güvenilirliğinizi artırın</span>
    <span class="yv-product-float-v5 c"><?= icon('trending-up',12) ?> Daha fazla müşteriye ulaşın</span>
   </div>
  </div>
 </div>
</section>

<section class="yv-product-stage-v5">
 <div class="container">
  <div class="yv-product-stage-grid-v5">
   <div class="yv-product-gallery-v5">
    <div class="yv-product-gallery-main-v5">
     <?php if(!empty($package['image'])): ?><img src="<?= e(upload_url($package['image'])) ?>" alt="<?= e($package['image_alt']??$package['name']) ?>">
     <?php else: ?><div class="yv-product-gallery-demo-v12"><img class="yv-product-demo-photo-v12" src="<?= e(demo_visual_url(package_display_name($package).' '.($package['category_name']??''),'product')) ?>" alt="<?= e(package_display_name($package)) ?>"><div class="yv-product-demo-gradient-v12"></div><span class="logo"><?= icon($platform,38) ?></span><div class="review">★★★★★<b>Harika hizmet!</b><small>Kesinlikle tavsiye ederim.</small></div><div class="business"><strong><?= e($package['category_name']??'Dijital Hizmet') ?></strong><span>4.9 ★★★★★</span><small>Gerçek sosyal kanıt</small></div></div><?php endif; ?>
    </div>
    <div class="yv-product-thumbs-v5 yv-product-thumbs-v12">
     <?php $demoThumb=demo_visual_url(package_display_name($package).' '.($package['category_name']??''),'product'); ?>
     <span class="active"><img src="<?= e(!empty($package['image']) ? upload_url($package['image']) : $demoThumb) ?>" alt=""></span>
     <span><img src="<?= e(demo_visual_url(($package['category_name']??''). ' analiz','analysis')) ?>" alt=""></span>
     <span><img src="<?= e(demo_visual_url(($package['category_name']??''). ' sosyal medya','social')) ?>" alt=""></span>
     <span><img src="<?= e(asset('img/hero-woman-cutout.png')) ?>" alt=""></span>
     <span><img src="<?= e(asset('img/support-woman-cutout.png')) ?>" alt=""></span>
    </div>
    <div class="yv-product-info-strip-v5">
     <div><?= icon('users',18) ?><span><strong>Hizmet Bilgisi</strong><small>Paket içeriğine göre</small></span></div>
     <div><?= icon('shield',18) ?><span><strong>Şifresiz İşlem</strong><small>Parola talep edilmez</small></span></div>
     <div><?= icon('zap',18) ?><span><strong>Tahmini Teslimat</strong><small><?= e($package['delivery_time'] ?: 'Sipariş sonrası') ?></small></span></div>
     <div><?= icon('target',18) ?><span><strong>Sipariş Takibi</strong><small>Hesabınız üzerinden</small></span></div>
    </div>
   </div>

   <aside class="yv-product-buy-v5">
    <div class="yv-product-badges-v5"><span><?= e($package['badge'] ?: 'En Çok Tercih Edilen Paket') ?></span><?php if($discount): ?><b>%<?= $discount ?> İndirim</b><?php endif; ?></div>
    <h2><?= e(package_display_name($package)) ?></h2>
    <div class="yv-product-rating-v5"><b><?= icon('shield-check',14) ?> Güvenli Sipariş</b><span>İşlem durumunu hesabınızdan takip edin</span></div>
    <div class="yv-product-price-v5"><?php if($discount): ?><del><?= money($package['price']) ?></del><?php endif; ?><strong><?= money($price) ?></strong><?php if($discount): ?><span>%<?= $discount ?> indirim</span><?php endif; ?></div>
    <p><?= e((string)($package['short_description'] ?: 'Seçtiğiniz dijital hizmet paketinin ayrıntılarını aşağıda inceleyebilirsiniz.')) ?></p>

    <?php if(!empty($variantPackages) && count($variantPackages) > 1): ?>
    <div class="yv-product-variants-v9">
      <label>Paket Seçimi <?= icon('help-circle',11) ?></label>
      <div>
        <?php foreach($variantPackages as $variant): $variantPrice=(!empty($variant['discount_price'])&&$variant['discount_price']<$variant['price'])?$variant['discount_price']:$variant['price']; ?>
        <a class="<?= (int)$variant['id']===(int)$package['id']?'active':'' ?>" href="/paket/<?= e($variant['slug']) ?>"><strong><?= e(excerpt(package_display_name($variant),28)) ?></strong><small><?= money($variantPrice) ?></small></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <form id="packageForm" method="POST" action="/sepet/ekle" class="yv-buy-form-v5">
     <?= csrfField() ?><input type="hidden" name="package_id" value="<?= $package['id'] ?>">
     <?php if(!empty($fields)): foreach($fields as $field): ?><div class="yv-form-group"><label><?= e($field['field_label']) ?><?= $field['is_required']?' *':'' ?></label>
      <?php if($field['field_type']==='textarea'): ?><textarea class="form-control" name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" rows="2" placeholder="<?= e($field['placeholder']??'') ?>" <?= $field['is_required']?'required':'' ?>></textarea>
      <?php elseif($field['field_type']==='select'): ?><select class="form-control" name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" <?= $field['is_required']?'required':'' ?>><option value="">Seçiniz</option><?php foreach(explode(',',$field['options']??'') as $opt): if(trim($opt)!==''): ?><option value="<?= e(trim($opt)) ?>"><?= e(trim($opt)) ?></option><?php endif; endforeach; ?></select>
      <?php else: ?><input class="form-control" type="<?= $field['field_type']==='url'?'url':'text' ?>" name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" placeholder="<?= e($field['placeholder']??'') ?>" <?= $field['is_required']?'required':'' ?>><?php endif; ?>
     </div><?php endforeach; endif; ?>
     <?php if(($package['max_quantity']??1)>1): ?><div class="yv-product-qty-v9"><label>Adet</label><div><button type="button" onclick="stepProductQty(-1)">−</button><input id="productQty" type="number" name="quantity" value="<?= (int)($package['min_quantity']?:1) ?>" min="<?= (int)($package['min_quantity']?:1) ?>" max="<?= (int)$package['max_quantity'] ?>"><button type="button" onclick="stepProductQty(1)">+</button></div></div><?php else: ?><input type="hidden" name="quantity" value="1"><?php endif; ?>
     <button class="yv-product-add-v5" type="submit"><?= icon('shopping-cart',15) ?> Sepete Ekle</button>
     <button class="yv-product-now-v5" type="button" onclick="buyNow()"><?= icon('zap',15) ?> Hemen Satın Al</button>
    </form>
    <div class="yv-product-mini-trust-v5"><div><?= icon('shield',14) ?><span><strong>Güvenli Ödeme</strong><small>256 Bit SSL</small></span></div><div><?= icon('zap',14) ?><span><strong>Hızlı Teslimat</strong><small><?= e($package['delivery_time'] ?: 'Hızlı') ?></small></span></div><div><?= icon('headphones',14) ?><span><strong>7/24 Destek</strong><small>Her zaman yanınızda</small></span></div></div>
   </aside>
  </div>
 </div>
</section>

<section class="yv-product-content-v5"><div class="container">
 <div class="yv-product-tabs-v5"><span class="active">Açıklama</span><span>Özellikler</span><span>Teslimat Süreci</span><span>Sıkça Sorulan Sorular</span></div>
 <article class="yv-product-copy-v5"><h2><?= e(package_display_name($package)) ?> Nedir?</h2><div class="blog-content"><?php if(!empty($package['description'])): ?><?= $package['description'] ?><?php else: ?><p>Bu paket, ihtiyacınız olan dijital hizmeti güvenli, hızlı ve kolay bir sipariş deneyimiyle sunar. Ödeme ve gerekli bilgiler tamamlandıktan sonra siparişiniz işleme alınır ve hesabınızdan takip edilebilir.</p><?php endif; ?></div></article>
 <div class="yv-product-gains-v5"><h3>Paket Özellikleri</h3><div>
 <?php foreach(\App\Services\PackageHighlightsService::get((int)$package['id'],$package) as $highlight): ?>
  <span><?= icon('check-circle',13) ?> <?= e($highlight) ?></span>
 <?php endforeach; ?>
 </div></div>
</div></section>

<?php if(!empty($reviews)): ?>
<section class="yv-product-reviews-v5"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">Müşteri yorumları</div><h2>Gerçek kullanıcı deneyimleri.</h2></div><div class="yv-product-rating-summary-v5"><strong>4.9</strong><span>★★★★★</span><small>Memnuniyet odaklı hizmet</small></div></div><div class="yv-product-review-grid-v5"><?php foreach(array_slice($reviews,0,3) as $review): ?><article><div><span><?= mb_strtoupper(mb_substr($review['name']??'M',0,1)) ?></span><b><?= e($review['name']??'Müşteri') ?></b><em><?php for($i=0;$i<($review['stars']??5);$i++): ?>★<?php endfor; ?></em></div><p><?= e($review['text']??'') ?></p></article><?php endforeach; ?></div></div></section>
<?php endif; ?>

<section class="yv-product-why-v5"><div class="container"><div><div class="yv-kicker">Neden YorumHizmeti.tr?</div><h2>Güvenli, hızlı ve kullanıcı dostu.</h2></div><div class="yv-product-why-stats-v5"><span><?= icon('users',18) ?><b>50.000+</b><small>Mutlu müşteri</small></span><span><?= icon('star-fill',18) ?><b>4.9/5</b><small>Müşteri puanı</small></span><span><?= icon('headphones',18) ?><b>7/24</b><small>Canlı destek</small></span><span><?= icon('shield',18) ?><b>%98</b><small>Memnuniyet</small></span></div></div></section>

<?php if(!empty($faqs)): ?><section class="yv-product-faq-v5"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">Sıkça sorulan sorular</div><h2>Bu paket hakkında merak edilenler.</h2></div></div><div class="yv-services-faq-grid"><?php foreach(array_slice($faqs,0,6) as $faq): ?><div class="premium-faq-item"><button type="button" class="premium-faq-question"><span><?= e($faq['question']) ?></span><?= icon('plus',12) ?></button><div class="premium-faq-answer"><?= nl2br(e($faq['answer'])) ?></div></div><?php endforeach; ?></div></div></section><?php endif; ?>

<?php if(!empty($relatedPackages)): ?><section class="yv-product-related-v5"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">İlgili paketler</div><h2>İlgili Paketler</h2><p>İşletmenizi daha da güçlendirecek diğer popüler paketlere göz atın.</p></div><a class="yv-link-button" href="/kategoriler">Tüm Paketleri Gör <?= icon('arrow-right',11) ?></a></div><div class="yv-product-related-grid-v5"><?php foreach($relatedPackages as $rp): $rpPrice=(!empty($rp['discount_price'])&&$rp['discount_price']<$rp['price'])?$rp['discount_price']:$rp['price']; [$rpClass,$rpIcon]=yvPkgDesign(package_display_name($rp), $rp['category_name']??''); ?><a class="yv-product-related-card-v5 <?= e($rpClass) ?>" href="/paket/<?= e($rp['slug']) ?>"><?php if(!empty($rp['image'])): ?><img src="<?= e(upload_url($rp['image'])) ?>" alt="<?= e($rp['image_alt']??$rp['name']) ?>"><?php else: ?><div class="art <?= e($rpClass) ?>"><span><?= icon($rpIcon,28) ?></span></div><?php endif; ?><div><small><?= e($rp['category_name']??'Hizmet') ?></small><h3><?= e(package_display_name($rp)) ?></h3><ul><li><?= icon('check',10) ?> Hızlı teslimat</li><li><?= icon('check',10) ?> Güvenli işlem</li><li><?= icon('check',10) ?> 7/24 destek</li></ul><strong><?= money($rpPrice) ?></strong><em>Detayları İncele <?= icon('arrow-right',10) ?></em></div></a><?php endforeach; ?></div></div></section><?php endif; ?>
</div>
<script>
document.querySelectorAll('.premium-faq-question').forEach(q=>q.addEventListener('click',()=>{const a=q.nextElementSibling;a.style.display=a.style.display==='block'?'none':'block'}));
function stepProductQty(delta){const q=document.getElementById('productQty');if(!q)return;const min=parseInt(q.min||'1',10),max=parseInt(q.max||'999999',10);q.value=Math.min(max,Math.max(min,parseInt(q.value||min,10)+delta));}
function buyNow(){const f=document.getElementById('packageForm');if(!f.reportValidity())return;const b=f.querySelector('.yv-product-now-v5');b.disabled=true;b.textContent='Yönlendiriliyor...';fetch(f.action,{method:'POST',body:new FormData(f),headers:{'X-Requested-With':'XMLHttpRequest'}}).finally(()=>location.href='/odeme')}
</script>