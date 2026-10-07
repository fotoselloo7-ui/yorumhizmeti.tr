<?php
if (!function_exists('yvPkgDesign')) {
    function yvPkgDesign(string $name='', string $category=''): array {
        $s=mb_strtolower($name.' '.$category);
        if(str_contains($s,'instagram')) return ['instagram','instagram'];
        if(str_contains($s,'tiktok')) return ['tiktok','tiktok'];
        if(str_contains($s,'youtube')) return ['youtube','youtube'];
        if(str_contains($s,'facebook')) return ['facebook','facebook'];
        if(str_contains($s,'twitter')||str_contains($s,' x ')) return ['twitter','twitter'];
        if(str_contains($s,'google')||str_contains($s,'seo')) return ['google','google'];
        if(str_contains($s,'web')||str_contains($s,'site')) return ['web','globe'];
        return ['default','package'];
    }
}
$lower=mb_strtolower(($package['name']??'').' '.($package['category_name']??''));
[$pclass,$platform]=yvPkgDesign($package['name']??'', $package['category_name']??'');
$price=(!empty($package['discount_price'])&&$package['discount_price']<$package['price'])?$package['discount_price']:$package['price'];
$discount=(!empty($package['discount_price'])&&$package['discount_price']<$package['price']&&$package['price']>0)?round((1-$package['discount_price']/$package['price'])*100):0;
$reviews=$testimonialSection['extra']??[];
?>
<div class="yv-product-v5">
<section class="yv-product-hero-v5 <?= e($pclass) ?>">
 <div class="container">
  <div class="pkg-breadcrumb"><a href="/"><?= icon('home',11) ?> Ana Sayfa</a><span class="sep">/</span><a href="/kategori/<?= e($package['category_slug']??'') ?>"><?= e($package['category_name']??'Hizmetler') ?></a><span class="sep">/</span><span><?= e($package['name']) ?></span></div>
  <div class="yv-product-hero-grid-v5">
   <div class="yv-product-hero-copy-v5">
    <div class="yv-product-brand-v5"><span><?= icon($platform,30) ?></span><small><?= e($package['category_name']??'Premium Hizmet') ?></small></div>
    <h1><?= e($package['name']) ?></h1>
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
     <?php else: ?><div class="yv-product-gallery-fallback-v5"><img class="woman" src="<?= e(asset('img/hero-woman-cutout.png')) ?>" alt="<?= e($package['name']) ?>"><span class="logo"><?= icon($platform,38) ?></span><div class="review">★★★★★<b>Harika hizmet!</b><small>Kesinlikle tavsiye ederim.</small></div><div class="business"><strong>Markanız</strong><span>4.9 ★★★★★</span><small>Gerçek sosyal kanıt</small></div></div><?php endif; ?>
    </div>
    <div class="yv-product-thumbs-v5">
     <span class="active"><?= icon($platform,18) ?></span><span><?= icon('trending-up',18) ?></span><span><?= icon('shield',18) ?></span><span><?= icon('users',18) ?></span><span><?= icon('star-fill',18) ?></span>
    </div>
    <div class="yv-product-info-strip-v5">
     <div><?= icon('users',18) ?><span><strong>Gerçek Kullanıcılar</strong><small>%100 Organik</small></span></div>
     <div><?= icon('shield',18) ?><span><strong>Düşüş Riski Yok</strong><small>Kalıcı etkileşim</small></span></div>
     <div><?= icon('zap',18) ?><span><strong>Hızlı Teslimat</strong><small><?= e($package['delivery_time'] ?: 'Hızlı') ?></small></span></div>
     <div><?= icon('target',18) ?><span><strong>Lokasyon Uyumu</strong><small>Türkiye geneli</small></span></div>
    </div>
   </div>

   <aside class="yv-product-buy-v5">
    <div class="yv-product-badges-v5"><span><?= e($package['badge'] ?: 'En Çok Tercih Edilen Paket') ?></span><?php if($discount): ?><b>%<?= $discount ?> İndirim</b><?php endif; ?></div>
    <h2><?= e($package['name']) ?></h2>
    <div class="yv-product-rating-v5"><b>★ 4.9</b><span>Güvenli satın alma deneyimi</span></div>
    <div class="yv-product-price-v5"><?php if($discount): ?><del><?= money($package['price']) ?></del><?php endif; ?><strong><?= money($price) ?></strong><?php if($discount): ?><span>%<?= $discount ?> indirim</span><?php endif; ?></div>
    <p>Bu paketle markanızın görünürlüğünü ve sosyal kanıtını güçlendirin.</p>

    <?php if(!empty($variantPackages) && count($variantPackages) > 1): ?>
    <div class="yv-product-variants-v9">
      <label>Paket Seçimi <?= icon('help-circle',11) ?></label>
      <div>
        <?php foreach($variantPackages as $variant): $variantPrice=(!empty($variant['discount_price'])&&$variant['discount_price']<$variant['price'])?$variant['discount_price']:$variant['price']; ?>
        <a class="<?= (int)$variant['id']===(int)$package['id']?'active':'' ?>" href="/paket/<?= e($variant['slug']) ?>"><strong><?= e(excerpt($variant['name'],28)) ?></strong><small><?= money($variantPrice) ?></small></a>
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
 <article class="yv-product-copy-v5"><h2><?= e($package['name']) ?> Nedir?</h2><div class="blog-content"><?php if(!empty($package['description'])): ?><?= $package['description'] ?><?php else: ?><p>Bu paket, ihtiyacınız olan dijital hizmeti güvenli, hızlı ve kolay bir sipariş deneyimiyle sunar. Ödeme ve gerekli bilgiler tamamlandıktan sonra siparişiniz işleme alınır ve hesabınızdan takip edilebilir.</p><?php endif; ?></div></article>
 <div class="yv-product-gains-v5"><h3>Bu Paket ile Neler Kazanırsınız?</h3><div><span><?= icon('check-circle',13) ?> Daha güçlü dijital görünürlük</span><span><?= icon('check-circle',13) ?> Marka güvenilirliğinde artış</span><span><?= icon('check-circle',13) ?> Daha fazla müşteri erişimi</span><span><?= icon('check-circle',13) ?> Gerçek sosyal kanıt</span><span><?= icon('check-circle',13) ?> Hızlı ve güvenli süreç</span><span><?= icon('check-circle',13) ?> 7/24 satış sonrası destek</span></div></div>
</div></section>

<?php if(!empty($reviews)): ?>
<section class="yv-product-reviews-v5"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">Müşteri yorumları</div><h2>Gerçek kullanıcı deneyimleri.</h2></div><div class="yv-product-rating-summary-v5"><strong>4.9</strong><span>★★★★★</span><small>Memnuniyet odaklı hizmet</small></div></div><div class="yv-product-review-grid-v5"><?php foreach(array_slice($reviews,0,3) as $review): ?><article><div><span><?= mb_strtoupper(mb_substr($review['name']??'M',0,1)) ?></span><b><?= e($review['name']??'Müşteri') ?></b><em><?php for($i=0;$i<($review['stars']??5);$i++): ?>★<?php endfor; ?></em></div><p><?= e($review['text']??'') ?></p></article><?php endforeach; ?></div></div></section>
<?php endif; ?>

<section class="yv-product-why-v5"><div class="container"><div><div class="yv-kicker">Neden YorumHizmeti.tr?</div><h2>Güvenli, hızlı ve kullanıcı dostu.</h2></div><div class="yv-product-why-stats-v5"><span><?= icon('users',18) ?><b>50.000+</b><small>Mutlu müşteri</small></span><span><?= icon('star-fill',18) ?><b>4.9/5</b><small>Müşteri puanı</small></span><span><?= icon('headphones',18) ?><b>7/24</b><small>Canlı destek</small></span><span><?= icon('shield',18) ?><b>%98</b><small>Memnuniyet</small></span></div></div></section>

<?php if(!empty($faqs)): ?><section class="yv-product-faq-v5"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">Sıkça sorulan sorular</div><h2>Bu paket hakkında merak edilenler.</h2></div></div><div class="yv-services-faq-grid"><?php foreach(array_slice($faqs,0,6) as $faq): ?><div class="premium-faq-item"><button type="button" class="premium-faq-question"><span><?= e($faq['question']) ?></span><?= icon('plus',12) ?></button><div class="premium-faq-answer"><?= nl2br(e($faq['answer'])) ?></div></div><?php endforeach; ?></div></div></section><?php endif; ?>

<?php if(!empty($relatedPackages)): ?><section class="yv-product-related-v5"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker">İlgili paketler</div><h2>İlgili Paketler</h2><p>İşletmenizi daha da güçlendirecek diğer popüler paketlere göz atın.</p></div><a class="yv-link-button" href="/kategoriler">Tüm Paketleri Gör <?= icon('arrow-right',11) ?></a></div><div class="yv-product-related-grid-v5"><?php foreach($relatedPackages as $rp): $rpPrice=(!empty($rp['discount_price'])&&$rp['discount_price']<$rp['price'])?$rp['discount_price']:$rp['price']; [$rpClass,$rpIcon]=yvPkgDesign($rp['name']??'', $rp['category_name']??''); ?><a class="yv-product-related-card-v5 <?= e($rpClass) ?>" href="/paket/<?= e($rp['slug']) ?>"><?php if(!empty($rp['image'])): ?><img src="<?= e(upload_url($rp['image'])) ?>" alt="<?= e($rp['image_alt']??$rp['name']) ?>"><?php else: ?><div class="art <?= e($rpClass) ?>"><span><?= icon($rpIcon,28) ?></span></div><?php endif; ?><div><small><?= e($rp['category_name']??'Hizmet') ?></small><h3><?= e($rp['name']) ?></h3><ul><li><?= icon('check',10) ?> Hızlı teslimat</li><li><?= icon('check',10) ?> Güvenli işlem</li><li><?= icon('check',10) ?> 7/24 destek</li></ul><strong><?= money($rpPrice) ?></strong><em>Detayları İncele <?= icon('arrow-right',10) ?></em></div></a><?php endforeach; ?></div></div></section><?php endif; ?>
</div>
<script>
document.querySelectorAll('.premium-faq-question').forEach(q=>q.addEventListener('click',()=>{const a=q.nextElementSibling;a.style.display=a.style.display==='block'?'none':'block'}));
function stepProductQty(delta){const q=document.getElementById('productQty');if(!q)return;const min=parseInt(q.min||'1',10),max=parseInt(q.max||'999999',10);q.value=Math.min(max,Math.max(min,parseInt(q.value||min,10)+delta));}
function buyNow(){const f=document.getElementById('packageForm');if(!f.reportValidity())return;const b=f.querySelector('.yv-product-now-v5');b.disabled=true;b.textContent='Yönlendiriliyor...';fetch(f.action,{method:'POST',body:new FormData(f),headers:{'X-Requested-With':'XMLHttpRequest'}}).finally(()=>location.href='/odeme')}
</script>