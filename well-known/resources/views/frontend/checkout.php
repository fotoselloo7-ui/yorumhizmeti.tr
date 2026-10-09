<?php $subtotal=0;foreach($cartItems as $item){$subtotal+=$item['price']*$item['quantity'];} ?>
<div class="yv-checkout-v5">
<section class="yv-checkout-hero-v5">
 <div class="container">
  <div class="breadcrumb"><a href="/">Ana Sayfa</a><span class="separator">/</span><a href="/sepet">Sepet</a><span class="separator">/</span><span>Ödeme</span></div>
  <div class="yv-checkout-hero-grid-v5">
   <div><div class="yv-kicker"><?= icon('lock',12) ?> Güvenli ödeme</div><h1><span>Güvenli Ödeme</span> ile Siparişini Tamamla</h1></div>
   <div class="yv-checkout-shield-v5"><span><?= icon('lock',34) ?></span><div><strong>%100 Güvenli Ödeme</strong><small>Tüm ödemeleriniz 256 bit SSL ile korunmaktadır.</small></div></div>
  </div>
  <div class="yv-checkout-steps-v5"><div class="active"><span><?= icon('shopping-cart',15) ?></span><b>1</b><strong>Sepet</strong><small>Ürünlerini gözden geçir</small></div><i></i><div><span><?= icon('credit-card',15) ?></span><b>2</b><strong>Ödeme</strong><small>Bilgilerini gir ve öde</small></div><i></i><div><span><?= icon('check',15) ?></span><b>3</b><strong>Onay</strong><small>Siparişini tamamla</small></div></div>
 </div>
</section>

<section class="yv-checkout-main-v5"><div class="container"><form id="checkoutForm" method="POST" action="/odeme/islem"><?= csrfField() ?><div class="yv-checkout-layout-v5">
 <div class="yv-checkout-left-v5">
  <section class="yv-checkout-card-v5"><div class="yv-checkout-card-head-v5"><span><?= icon('shopping-cart',16) ?></span><div><h2>Sepetindeki Ürünler</h2><small><?= count($cartItems) ?> ürün siparişe hazır</small></div><b><?= count($cartItems) ?> ürün</b></div><div class="yv-checkout-products-v5">
   <?php foreach($cartItems as $item):
    $displayName=package_display_name($item); $nameLower=mb_strtolower($displayName);
    $ico='package';$cls='default';
    if(str_contains($nameLower,'instagram')){$ico='instagram';$cls='instagram';}
    elseif(str_contains($nameLower,'tiktok')){$ico='tiktok';$cls='tiktok';}
    elseif(str_contains($nameLower,'youtube')){$ico='youtube';$cls='youtube';}
    elseif(str_contains($nameLower,'google')){$ico='google';$cls='google';}
    elseif(str_contains($nameLower,'facebook')){$ico='facebook';$cls='facebook';}
   ?><div class="yv-checkout-product-v5">
      <span class="<?= e($cls) ?>"><?= icon($ico,23) ?></span>
      <div><strong><?= e(package_display_name($item)) ?></strong><small><?= e($item['delivery_time']??'Hızlı teslimat') ?></small></div>
      <div class="yv-checkout-qty-v9">
        <button type="button" onclick="changeCheckoutQty(<?= (int)$item['cart_key'] ?>,<?= max(1,(int)$item['quantity']-1) ?>)" aria-label="Azalt">−</button>
        <b><?= (int)$item['quantity'] ?></b>
        <button type="button" onclick="changeCheckoutQty(<?= (int)$item['cart_key'] ?>,<?= min((int)($item['max_quantity']?:9999),(int)$item['quantity']+1) ?>)" aria-label="Artır">+</button>
      </div>
      <strong class="yv-checkout-line-total-v9"><?= money($item['line_total']) ?></strong>
      <button class="yv-checkout-remove-v9" type="button" onclick="removeCheckoutItem(<?= (int)$item['cart_key'] ?>)" aria-label="Sepetten çıkar"><?= icon('trash',13) ?></button>
    </div><?php endforeach; ?>
  </div>
  <div class="yv-checkout-campaign-v9"><span><?= icon('package',16) ?></span><div><strong>Kampanya Avantajı</strong><small>Uygun kampanyalar ve paket indirimleri sipariş özetine otomatik yansıtılır.</small></div><em><?= icon('check-circle',12) ?> Otomatik uygulanır</em></div>
  </section>

  <section class="yv-checkout-card-v5"><div class="yv-checkout-card-head-v5"><span><?= icon('user',16) ?></span><div><h2>Fatura ve İletişim Bilgileri</h2><small>Siparişle ilgili bilgilendirmeler bu bilgiler üzerinden iletilir.</small></div></div><div class="yv-checkout-user-v5"><div><label>Ad Soyad</label><input class="form-control" value="<?= e($user['name']??'') ?>" disabled></div><div><label>E-posta Adresi</label><input class="form-control" value="<?= e($user['email']??'') ?>" disabled></div><div><label>Telefon Numarası</label><input class="form-control" value="<?= e($user['phone']??'') ?>" disabled placeholder="Hesabınızdan güncelleyebilirsiniz"></div><div class="yv-checkout-login-state-v5"><?= icon('check-circle',13) ?> Giriş yapılmış güvenli hesap</div></div></section>

  <?php if(array_filter($cartItems,fn($i)=>!empty($i['fields']))): ?><section class="yv-checkout-card-v5"><div class="yv-checkout-card-head-v5"><span><?= icon('file-text',16) ?></span><div><h2>Hizmet Bilgileri</h2><small>Hizmetin uygulanması için gerekli alanları eksiksiz doldurun.</small></div></div><div class="yv-checkout-fields-v5"><?php foreach($cartItems as $item): if(empty($item['fields'])) continue; ?><div class="yv-checkout-field-group-v5"><h3><?= e(package_display_name($item)) ?></h3><?php foreach($item['fields'] as $field): $savedValue=(string)($item['saved_fields'][$field['field_key']]??''); ?><div class="yv-form-group"><label><?= e($field['field_label']) ?><?= $field['is_required']?' *':'' ?></label><?php if($field['field_type']==='textarea'): ?><textarea class="form-control" name="field_<?= $item['id'] ?>_<?= e($field['field_key']) ?>" rows="2" placeholder="<?= e($field['placeholder']??'') ?>" <?= $field['is_required']?'required':'' ?>><?= e($savedValue) ?></textarea><?php elseif($field['field_type']==='select'): ?><select class="form-control" name="field_<?= $item['id'] ?>_<?= e($field['field_key']) ?>" <?= $field['is_required']?'required':'' ?>><option value="">Seçiniz</option><?php foreach(explode(',',$field['options']??'') as $opt): if(trim($opt)!==''): ?><option value="<?= e(trim($opt)) ?>" <?= $savedValue===trim($opt)?'selected':'' ?>><?= e(trim($opt)) ?></option><?php endif; endforeach; ?></select><?php else: ?><input class="form-control" type="<?= $field['field_type']==='url'?'url':'text' ?>" name="field_<?= $item['id'] ?>_<?= e($field['field_key']) ?>" placeholder="<?= e($field['placeholder']??'') ?>" value="<?= e($savedValue) ?>" <?= $field['is_required']?'required':'' ?>><?php endif; ?></div><?php endforeach; ?></div><?php endforeach; ?></div></section><?php endif; ?>

  <section class="yv-checkout-card-v5"><div class="yv-checkout-card-head-v5"><span><?= icon('credit-card',16) ?></span><div><h2>Ödeme Yöntemi</h2><small>Aktif ödeme yöntemlerinden birini seçin.</small></div></div>
   <?php if(empty($paymentOptions)): ?><div class="alert alert-warning">Aktif ödeme yöntemi bulunamadı.</div><?php else: ?><div class="yv-checkout-payment-grid-v5"><?php foreach($paymentOptions as $i=>$opt): ?><label class="yv-checkout-payment-v5"><input type="radio" name="payment_gateway" value="<?= e($opt['key']) ?>" <?= ($opt['is_default']||$i===0)?'checked':'' ?> required><span><?= icon($opt['key']==='bank_transfer'?'inbox':'credit-card',18) ?></span><div><strong><?= e($opt['name']) ?></strong><small><?= $opt['key']==='bank_transfer'?'Havale / EFT ile güvenli ödeme':'Kredi veya banka kartı ile güvenli online ödeme' ?></small></div></label><?php endforeach; ?></div><?php endif; ?>
   
   <div class="yv-form-group" style="margin-top:12px"><label>Sipariş Notu (Opsiyonel)</label><textarea name="customer_note" class="form-control" rows="2" placeholder="Eklemek istediğiniz not..."></textarea></div>
   <label class="yv-checkout-terms-v5"><input type="checkbox" name="terms_accepted" value="1" required><span>Mesafeli Satış Sözleşmesi ve Gizlilik Politikası'nı okudum, kabul ediyorum.</span></label>
  </section>
 </div>

 <aside class="yv-checkout-right-v5">
  <section class="yv-checkout-summary-v5"><div class="yv-checkout-card-head-v5"><span><?= icon('file-text',16) ?></span><div><h2>Sipariş Özeti</h2></div></div><div class="yv-checkout-summary-row-v5"><span>Ara Toplam</span><b><?= money($subtotal) ?></b></div><?php if($total<$subtotal): ?><div class="yv-checkout-summary-row-v5 discount"><span>İndirim</span><b>-<?= money($subtotal-$total) ?></b></div><?php endif; ?><div class="yv-checkout-summary-total-v5"><span>Toplam Tutar</span><strong><?= money($total) ?></strong></div><button id="btnCheckoutSubmit" type="submit" class="yv-checkout-pay-v5" <?= empty($paymentOptions)?'disabled title="Aktif ödeme yöntemi bulunmuyor"':'' ?>><?= icon('lock',14) ?> <?= money($total) ?> Öde <?= icon('arrow-right',12) ?></button><small class="yv-checkout-ssl-v5"><?= icon('shield',11) ?> Ödemeniz güvenli altyapı ile korunur.</small><?php if(count(array_filter($paymentOptions ?? [], static fn($option) => ($option['type'] ?? '') === 'online')) > 0): ?><div class="yv-checkout-payments-v5 yv-payment-networks-v24" aria-label="Desteklenen kart ağları"><span><img src="<?= asset('img/payments/visa.svg') ?>" alt="Visa" width="74" height="32" loading="lazy"></span><span><img src="<?= asset('img/payments/mastercard.svg') ?>" alt="Mastercard" width="74" height="32" loading="lazy"></span><span><img src="<?= asset('img/payments/troy.svg') ?>" alt="TROY" width="74" height="32" loading="lazy"></span></div><?php else: ?><div class="yv-checkout-payments-v5 yv-payment-transfer-v24"><span><?= icon('landmark',13) ?> Havale / EFT ile ödeme</span></div><?php endif; ?></section>
  <section class="yv-checkout-why-v5"><h3>Neden YorumHizmeti.tr?</h3><div><?= icon('zap',15) ?><span><strong>Anında İşleme Alma</strong><small>Siparişiniz hızlıca işleme alınır.</small></span></div><div><?= icon('shield',15) ?><span><strong>%100 Güvenli Ödeme</strong><small>Tüm süreç korumalı altyapıda ilerler.</small></span></div><div><?= icon('users',15) ?><span><strong>Kaliteli Hizmet</strong><small>Güvenilir ve şeffaf süreç.</small></span></div><div><?= icon('headphones',15) ?><span><strong>7/24 Müşteri Desteği</strong><small>İhtiyacınızda yanınızdayız.</small></span></div></section>
  <section class="yv-checkout-support-v5"><img class="woman" src="<?= e(asset('img/support-woman-cutout.png')) ?>" alt="Canlı destek uzmanı"><div><small>Soruların mı var?</small><h3>7/24 Canlı Destek</h3><a href="/iletisim"><?= icon('headphones',12) ?> Destek Başlat</a></div></section>
 </aside>
</div></form></div></section>

<section class="yv-checkout-proofbar-v5"><div class="container"><div><?= icon('shopping-cart',20) ?><span><strong>50.000+</strong><small>Mutlu müşteri</small></span></div><div><?= icon('shield',20) ?><span><strong>%100</strong><small>Güvenli ödeme</small></span></div><div><?= icon('zap',20) ?><span><strong>Hızlı</strong><small>Teslimat</small></span></div><div><?= icon('star-fill',20) ?><span><strong>4.9/5</strong><small>Müşteri memnuniyeti</small></span></div><div><?= icon('headphones',20) ?><span><strong>7/24</strong><small>Canlı destek</small></span></div></div></section>

<?php $checkoutReviews=$testimonialSection['extra']??[]; if(!empty($checkoutReviews)): ?>
<section class="yv-checkout-reviews-v9"><div class="container"><div class="yv-section-head-v5"><div><div class="yv-kicker"><?= icon('message-circle',11) ?> Gerçek kullanıcı deneyimleri</div><h2>Müşterilerimiz Ne Diyor?</h2><p>Binlerce müşterimizin arasına siz de katılın.</p></div><a href="/" class="yv-link-button">Tüm Yorumları Gör <?= icon('arrow-right',10) ?></a></div><div class="yv-checkout-review-grid-v9"><?php foreach(array_slice($checkoutReviews,0,3) as $review): ?><article><div><span><?= mb_strtoupper(mb_substr($review['name']??'M',0,1)) ?></span><b><?= e($review['name']??'Müşteri') ?></b><em>★★★★★</em></div><p><?= e($review['text']??'') ?></p></article><?php endforeach; ?></div></div></section>
<?php endif; ?>
</div>
<script>
const checkoutCsrf=document.querySelector('#checkoutForm input[name="_csrf_token"]')?.value||'';
function checkoutCartPost(url,data){
  const body=new URLSearchParams({...data,_csrf_token:checkoutCsrf});
  fetch(url,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},body:body.toString()}).then(()=>location.reload());
}
function changeCheckoutQty(key,qty){ if(qty<1)return; checkoutCartPost('/sepet/guncelle',{key,quantity:qty}); }
function removeCheckoutItem(key){ checkoutCartPost('/sepet/sil',{key}); }
document.getElementById('checkoutForm').addEventListener('submit',function(){if(!this.checkValidity())return;const b=document.getElementById('btnCheckoutSubmit');b.disabled=true;b.textContent='Lütfen bekleyin...'});
</script>