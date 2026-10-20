<?php
$lower=mb_strtolower(($package['name']??'').' '.($package['category_name']??''));
$platform='package';$pclass='default';
if(str_contains($lower,'instagram')){$platform='instagram';$pclass='instagram';}
elseif(str_contains($lower,'tiktok')){$platform='tiktok';$pclass='tiktok';}
elseif(str_contains($lower,'youtube')){$platform='youtube';$pclass='youtube';}
elseif(str_contains($lower,'facebook')){$platform='facebook';$pclass='facebook';}
elseif(str_contains($lower,'twitter')||str_contains($lower,' x ')){$platform='twitter';$pclass='twitter';}
elseif(str_contains($lower,'google')||str_contains($lower,'seo')){$platform='google';$pclass='google';}
elseif(str_contains($lower,'web')||str_contains($lower,'site')){$platform='globe';$pclass='web';}
$price=(!empty($package['discount_price'])&&$package['discount_price']<$package['price'])?$package['discount_price']:$package['price'];
?>
<div class="yv-product-page">
 <div class="container">
  <div class="pkg-breadcrumb"><a href="/"><?= icon('home',11) ?> Ana Sayfa</a> <span class="sep">/</span><a href="/kategori/<?= e($package['category_slug']??'') ?>"><?= e($package['category_name']??'Hizmetler') ?></a> <span class="sep">/</span><span><?= e($package['name']) ?></span></div>
  <div class="yv-product-hero">
   <div class="yv-product-media">
    <div class="yv-product-media-copy">
     <div class="yv-kicker"><?= e($package['category_name']??'Premium hizmet') ?></div>
     <h1><?= e($package['name']) ?></h1>
     <p><?= e($package['short_description'] ?: 'Markanız için güvenli, hızlı ve profesyonel dijital hizmet paketi.') ?></p>
     <div class="yv-trust-row"><span class="yv-trust-pill"><?= icon('shield',11) ?> Güvenli</span><span class="yv-trust-pill"><?= icon('zap',11) ?> Hızlı</span><span class="yv-trust-pill"><?= icon('headphones',11) ?> Destek</span></div>
    </div>
    <?php if(!empty($package['image'])): ?>
    <div class="yv-product-custom-image"><img src="<?= e(upload_url($package['image'])) ?>" alt="<?= e($package['image_alt'] ?? $package['name']) ?>"></div>
    <?php else: ?>
    <div class="yv-product-phone"></div>
    <?php endif; ?>
    <div class="yv-product-logo yv-platform-mark <?= e($pclass) ?>"><?= icon($platform,28) ?></div>
    <div class="yv-product-float one"><?= icon('check-circle',12) ?> Güvenli işlem</div>
    <div class="yv-product-float two"><?= icon('trending-up',12) ?> Marka görünürlüğü</div>
   </div>
   <div class="yv-buy-card">
    <div class="yv-buy-badges"><span><?= e($package['badge'] ?: 'Öne Çıkan Paket') ?></span><?php if(!empty($package['discount_price'])&&$package['discount_price']<$package['price']): ?><span>İndirimli fiyat</span><?php else: ?><span>Aktif paket</span><?php endif; ?></div>
    <h2><?= e($package['name']) ?></h2>
    <div class="yv-rating-line"><b>★★★★★</b><span>Güvenli satın alma</span></div>
    <div class="yv-price-box"><?php if(!empty($package['discount_price'])&&$package['discount_price']<$package['price']): ?><del><?= money($package['price']) ?></del><?php endif; ?><strong><?= money($price) ?></strong><span class="yv-tax">KDV dahildir</span></div>
    <form id="packageForm" method="POST" action="/sepet/ekle" class="yv-buy-form">
     <?= csrfField() ?><input type="hidden" name="package_id" value="<?= $package['id'] ?>">
     <?php if(!empty($fields)): foreach($fields as $field): ?><div class="yv-form-group"><label><?= e($field['field_label']) ?><?= $field['is_required']?' *':'' ?></label>
      <?php if($field['field_type']==='textarea'): ?><textarea class="form-control" name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" rows="2" placeholder="<?= e($field['placeholder']??'') ?>" <?= $field['is_required']?'required':'' ?>></textarea>
      <?php elseif($field['field_type']==='select'): ?><select class="form-control" name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" <?= $field['is_required']?'required':'' ?>><option value="">Seçiniz</option><?php foreach(explode(',',$field['options']??'') as $opt): if(trim($opt)!==''): ?><option value="<?= e(trim($opt)) ?>"><?= e(trim($opt)) ?></option><?php endif; endforeach; ?></select>
      <?php else: ?><input class="form-control" type="<?= $field['field_type']==='url'?'url':'text' ?>" name="field_<?= $package['id'] ?>_<?= e($field['field_key']) ?>" placeholder="<?= e($field['placeholder']??'') ?>" <?= $field['is_required']?'required':'' ?>><?php endif; ?>
     </div><?php endforeach; endif; ?>
     <?php if(($package['max_quantity']??1)>1): ?><div class="yv-form-group"><label>Sipariş adedi</label><input class="form-control" type="number" name="quantity" value="<?= (int)($package['min_quantity']?:1) ?>" min="<?= (int)($package['min_quantity']?:1) ?>" max="<?= (int)$package['max_quantity'] ?>"></div><?php else: ?><input type="hidden" name="quantity" value="1"><?php endif; ?>
     <div class="yv-buy-actions"><button class="yv-add" type="submit"><?= icon('shopping-cart',15) ?> Sepete Ekle</button><button class="yv-now" type="button" onclick="buyNow()"><?= icon('zap',15) ?> Hemen Satın Al</button></div>
    </form>
    <div class="yv-buy-trust"><div><?= icon('shield',14) ?>SSL güvencesi</div><div><?= icon('clock',14) ?><?= e($package['delivery_time'] ?: 'Hızlı teslimat') ?></div><div><?= icon('headphones',14) ?>7/24 destek</div></div>
   </div>
  </div>
  <div class="yv-product-content">
   <div class="yv-product-main">
    <div class="yv-info-strip"><div><?= icon('user',16) ?><strong>Şifresiz İşlem</strong><small>Giriş bilgisi istemeyiz</small></div><div><?= icon('shield',16) ?><strong>Güvenli Ödeme</strong><small>SSL korumalı süreç</small></div><div><?= icon('zap',16) ?><strong>Hızlı Teslimat</strong><small><?= e($package['delivery_time'] ?: 'Hızlı başlangıç') ?></small></div><div><?= icon('headphones',16) ?><strong>Canlı Destek</strong><small>İhtiyacınızda yanınızda</small></div></div>
    <section class="yv-product-section"><h3><?= e($package['name']) ?> Nedir?</h3><div class="blog-content"><?php if(!empty($package['description'])): ?><?= $package['description'] ?><?php else: ?><p>Bu paket, ihtiyacınız olan dijital hizmeti güvenli ve kolay şekilde satın almanızı sağlar. Siparişiniz ödeme sonrasında işleme alınır ve hesabınızdan takip edilebilir.</p><?php endif; ?></div></section>
    <section class="yv-product-section"><h3>Bu paketle neler kazanırsınız?</h3><ul><li><?= icon('check-circle',14) ?> Daha güçlü dijital görünürlük</li><li><?= icon('check-circle',14) ?> Basit ve güvenli sipariş süreci</li><li><?= icon('check-circle',14) ?> Hızlı teslimat ve sipariş takibi</li><li><?= icon('check-circle',14) ?> Satış sonrası destek</li></ul></section>
    <section class="yv-product-section"><h3>Sıkça Sorulan Sorular</h3><div class="premium-faq-item"><button type="button" class="premium-faq-question"><span>Şifremi vermem gerekiyor mu?</span><?= icon('chevron-down',13) ?></button><div class="premium-faq-answer">Hayır. Hizmetlerimizde hesap şifrenizi talep etmeyiz.</div></div><div class="premium-faq-item"><button type="button" class="premium-faq-question"><span>Teslimat ne zaman başlar?</span><?= icon('chevron-down',13) ?></button><div class="premium-faq-answer">Ödeme ve gerekli bilgiler tamamlandıktan sonra siparişiniz işleme alınır.</div></div></section>
   </div>
   <div></div>
  </div>
  <?php if(!empty($relatedPackages)): ?><section class="yv-related"><div class="yv-related-head"><div><div class="yv-kicker">Benzer hizmetler</div><h2>İlgili Paketler</h2></div><a class="yv-text-link" href="/kategori/<?= e($package['category_slug']??'') ?>">Tümünü gör <?= icon('arrow-right',11) ?></a></div><div class="yv-related-grid"><?php foreach($relatedPackages as $rp): $rpPrice=(!empty($rp['discount_price'])&&$rp['discount_price']<$rp['price'])?$rp['discount_price']:$rp['price']; ?><a class="yv-package-card" href="/paket/<?= e($rp['slug']) ?>"><?php if(!empty($rp['image'])): ?><div class="yv-package-cover"><img src="<?= e(upload_url($rp['image'])) ?>" alt="<?= e($rp['image_alt'] ?? $rp['name']) ?>"></div><?php endif; ?><div class="yv-package-top"><span class="yv-platform-mark <?= e($pclass) ?>"><?= icon($platform,18) ?></span></div><h3><?= e($rp['name']) ?></h3><div class="yv-package-price"><strong><?= money($rpPrice) ?></strong></div><div class="yv-package-meta"><span>Hızlı teslimat</span><b>Detay <?= icon('arrow-right',11) ?></b></div></a><?php endforeach; ?></div></section><?php endif; ?>
 </div>
</div>
<script>
document.querySelectorAll('.premium-faq-question').forEach(q=>q.addEventListener('click',()=>{const a=q.nextElementSibling;a.style.display=a.style.display==='block'?'none':'block'}));
function buyNow(){const f=document.getElementById('packageForm');if(!f.reportValidity())return;const b=f.querySelector('.yv-now');b.disabled=true;b.textContent='Yönlendiriliyor...';fetch(f.action,{method:'POST',body:new FormData(f),headers:{'X-Requested-With':'XMLHttpRequest'}}).finally(()=>location.href='/odeme')}
</script>