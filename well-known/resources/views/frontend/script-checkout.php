<style>
.nv-script-checkout{background:#f4f7fb;min-height:65vh;padding:48px 16px 85px;font-family:inherit}
.nv-script-checkout .inner{max-width:920px;margin:auto}
.nv-script-checkout .top{margin-bottom:26px}
.nv-script-checkout h1{font-size:clamp(25px,3.2vw,38px);line-height:1.2;color:#121d38;margin:8px 0}
.nv-script-checkout .subtitle{color:#62718a;font-size:14px}
.nv-script-checkout .grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(280px,.72fr);gap:22px}
.nv-script-checkout .panel{border:1px solid #dde4ef;border-radius:18px;background:#fff;padding:24px;box-shadow:0 12px 30px rgba(31,48,84,.055)}
.nv-script-checkout .panel h2{font-size:19px;color:#122042;margin:0 0 16px}
.nv-script-checkout .product{display:flex;align-items:center;gap:14px;margin-bottom:22px}
.nv-script-checkout .product img{width:88px;height:75px;object-fit:cover;border-radius:12px;background:#f1f4f9}
.nv-script-checkout .product strong{display:block;font-size:16px;color:#17244b}
.nv-script-checkout .product small{display:block;color:#63718c;margin-top:5px}
.nv-script-checkout .summary-row{display:flex;justify-content:space-between;gap:15px;padding:12px 0;border-bottom:1px solid #e9eef5;font-size:14px}
.nv-script-checkout .summary-row.total{font-weight:800;color:#19254a;font-size:20px;border-bottom:0}
.nv-script-checkout .terms{display:flex;align-items:flex-start;gap:9px;color:#41516d;font-size:13px;line-height:1.65;margin:20px 0}
.nv-script-checkout .terms input{width:17px;height:17px;margin-top:3px;flex:none}
.nv-script-checkout .cta{background:linear-gradient(120deg,#3158f0,#703cdb);color:#fff;border:0;border-radius:11px;padding:14px 18px;font-weight:800;width:100%;cursor:pointer}
.nv-script-checkout .cta:disabled{cursor:not-allowed;opacity:.5}
.nv-script-checkout .warning{background:#fff6e8;border:1px solid #f8db9e;color:#775011;padding:13px;border-radius:10px;font-size:13px;line-height:1.6}
.nv-script-checkout .safe{color:#687792;font-size:12px;line-height:1.7;margin-top:15px}
.nv-script-checkout a{color:#3355c3;text-decoration:underline}
@media(max-width:750px){.nv-script-checkout .grid{grid-template-columns:1fr}.nv-script-checkout .panel{padding:18px}}
</style>
<main class="nv-script-checkout">
 <div class="inner">
  <header class="top">
   <a href="/hazir-scriptler/<?= rawurlencode((string)$product['slug']) ?>">← Yazılıma dön</a>
   <h1>Yazılımını güvenle satın al</h1>
   <p class="subtitle">Sipariş bilgilerinizi onayladıktan sonra PayTR'nin güvenli kart ödeme ekranı açılır.</p>
  </header>
  <div class="grid">
   <section class="panel">
    <h2>Sipariş bilgileri</h2>
    <div class="product">
     <?php if(!empty($product['cover_image'])): ?><img src="<?= e(upload_url($product['cover_image'])) ?>" alt=""><?php endif; ?>
     <div><strong><?= e($product['name']) ?></strong><small>Hazır yazılım · 1 adet</small></div>
    </div>
    <div class="summary-row"><span>Alıcı</span><strong><?= e($user['name']??'') ?></strong></div>
    <div class="summary-row"><span>E-posta</span><strong><?= e($user['email']??'') ?></strong></div>
    <div class="summary-row"><span>Ödeme yöntemi</span><strong>PayTR · Kart</strong></div>
    <p class="safe">Ödeme bilgileri NetVera sisteminde saklanmaz. Sipariş yalnızca PayTR tarafından doğrulanmış başarılı ödeme bildirimi sonrasında ödenmiş görünür. Yazılım lisansı ve teslimat ayrıca yönetilir.</p>
   </section>
   <aside class="panel">
    <h2>Ödeme özeti</h2>
    <div class="summary-row"><span><?= e($product['name']) ?></span><strong>1 adet</strong></div>
    <div class="summary-row total"><span>Toplam</span><strong><?= money((float)$product['price']) ?></strong></div>
    <?php if($paytrReady): ?>
     <form action="/yazilim-satin-al/<?= rawurlencode((string)$product['slug']) ?>/odeme" method="post" id="nvScriptPayForm">
      <?= csrfField() ?>
      <input type="hidden" name="purchase_nonce" value="<?= e($purchaseNonce) ?>">
      <label class="terms">
       <input type="checkbox" name="terms_accepted" value="1" required>
       <span><a href="/sayfa/mesafeli-satis-sozlesmesi" target="_blank" rel="noopener">Mesafeli Satış Sözleşmesi</a>'ni okudum ve kabul ediyorum.</span>
      </label>
      <button class="cta" type="submit" id="nvScriptPayButton">PayTR ile Ödemeye Geç →</button>
      <script>
       document.getElementById('nvScriptPayForm')?.addEventListener('submit',function(){
         if(!this.checkValidity())return;
         const button=document.getElementById('nvScriptPayButton');
         button.disabled=true;button.textContent='Güvenli ödeme hazırlanıyor...';
       });
      </script>
     </form>
    <?php else: ?>
     <div class="warning">PayTR şu anda etkin değil veya ödeme API bilgileri eksik. Sipariş başlatılamıyor; yönetici panelinden PayTR yapılandırması kontrol edilmelidir.</div>
    <?php endif; ?>
    <p class="safe">Fiyat ödeme başlatılırken veritabanından yeniden doğrulanır. SEO ürün adresi ve mevcut sepetiniz değiştirilmez.</p>
   </aside>
  </div>
 </div>
</main>
