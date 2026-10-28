<style>
.nv-script-checkout{background:#f4f7fb;min-height:65vh;padding:48px 16px 85px;font-family:inherit}
.nv-script-checkout .inner{max-width:920px;margin:auto}
.nv-script-checkout .top{margin-bottom:26px}
.nv-script-checkout h1{font-size:clamp(25px,3.2vw,38px);line-height:1.2;color:#121d38;margin:8px 0}
.nv-script-checkout .subtitle{color:#62718a;font-size:14px}
.nv-script-checkout .grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(280px,.72fr);gap:22px;align-items:start}
.nv-script-checkout .panel{border:1px solid #dde4ef;border-radius:18px;background:#fff;padding:24px;box-shadow:0 12px 30px rgba(31,48,84,.055)}
.nv-script-checkout .panel h2{font-size:19px;color:#122042;margin:0 0 16px}
/* Compact payment summary follows scroll within checkout; no stretched white panel. */
@media(min-width:751px){
 .nv-script-checkout .payment-summary{position:sticky;top:96px;align-self:start;height:fit-content}
}
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

.nv-script-checkout .setup{margin-top:22px;padding-top:22px;border-top:1px solid #e8edf5}
.nv-script-checkout .setup h2{font-size:18px;margin:0 0 6px;color:#122042}
.nv-script-checkout .setup p{font-size:13px;color:#697892;line-height:1.6;margin:0 0 17px}
.nv-script-checkout .setup-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
.nv-script-checkout .setup-field{min-width:0;margin-bottom:14px}
.nv-script-checkout .setup-field label{display:block;color:#233355;font-weight:650;font-size:12px;margin-bottom:7px}
.nv-script-checkout .setup-control{width:100%;max-width:100%;min-height:43px;border:1px solid #d7deeb;border-radius:10px;background:#fff;padding:10px 12px;color:#17244b;font:inherit;font-size:13px;box-sizing:border-box}
.nv-script-checkout textarea.setup-control{min-height:80px;resize:vertical}
.nv-script-checkout .setup-control:focus{outline:2px solid #6c72db;outline-offset:1px}
.nv-script-checkout .setup-hint{display:block;font-size:11px;color:#7b879a;margin-top:6px;line-height:1.5}
.nv-script-checkout .setup-hint.warning-text{color:#9c541e}
.nv-script-checkout .setup-section-title{color:#1b294d;font-size:13px;font-weight:750;margin:16px 0 12px}
.nv-script-checkout .setup-group[hidden]{display:none!important}
@media(max-width:520px){.nv-script-checkout .setup-grid{grid-template-columns:1fr}}
@media(max-width:750px){.nv-script-checkout .grid{grid-template-columns:1fr;align-items:start}.nv-script-checkout .payment-summary{position:static;height:auto}.nv-script-checkout .panel{padding:18px}}
</style>
<main class="nv-script-checkout">
 <div class="inner">
  <header class="top">
   <a href="/hazir-scriptler/<?= rawurlencode((string)$product['slug']) ?>">← Yazılıma dön</a>
   <h1>Yazılımını güvenle satın al</h1>
   <p class="subtitle">Sipariş bilgilerinizi onayladıktan sonra PayTR'nin güvenli kart ödeme ekranı açılır.</p>
  </header>
  <form action="/yazilim-satin-al/<?= rawurlencode((string)$product['slug']) ?>/odeme" method="post" id="nvScriptPayForm">
   <?= csrfField() ?>
   <input type="hidden" name="purchase_nonce" value="<?= e($purchaseNonce) ?>">
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

    <section class="setup" aria-labelledby="nv-install-heading">
     <h2 id="nv-install-heading">Kurulum yapılacak domain ve hosting bilgileri</h2>
     <p>Yazılımınızı doğru adrese kurabilmemiz için bilgileri siparişe ekleyin. Ödeme sonrasında da siparişinizde kayıtlı kalacak.</p>
     <div class="setup-section-title">1. Alan adı</div>
     <div class="setup-field">
      <label for="nv-install-domain-mode">Alan adı durumunuz *</label>
      <select class="setup-control" id="nv-install-domain-mode" name="install_domain_mode" required>
       <option value="existing">Mevcut alan adım var</option>
       <option value="need_domain">Henüz alan adım yok, destek istiyorum</option>
      </select>
     </div>
     <div id="nv-install-domain-fields" class="setup-group">
      <div class="setup-field">
       <label for="nv-install-domain">Kurulum yapılacak domain *</label>
       <input class="setup-control" id="nv-install-domain" name="install_domain" type="text" inputmode="url" autocomplete="off" maxlength="253" placeholder="ornekfirma.com" required pattern="[^/\s:]+\.[^/\s:]+" title="Domain adını http:// yazmadan girin">
       <small class="setup-hint">https:// veya www zorunlu değil; sadece alan adını yazın.</small>
      </div>
     </div>
     <div class="setup-section-title">2. Hosting bilgileri</div>
     <div class="setup-field">
      <label for="nv-install-hosting-mode">Hosting durumunuz *</label>
      <select class="setup-control" id="nv-install-hosting-mode" name="install_hosting_mode" required>
       <option value="existing">Mevcut hosting hesabım var</option>
       <option value="need_hosting">Hostingim yok, NetVera destek versin</option>
      </select>
     </div>
     <div id="nv-install-hosting-fields" class="setup-group">
      <div class="setup-grid">
       <div class="setup-field">
        <label for="nv-install-hosting-provider">Hosting firması *</label>
        <input class="setup-control" id="nv-install-hosting-provider" name="install_hosting_provider" maxlength="120" placeholder="Örn. Turhost / Veridyen" required autocomplete="organization">
       </div>
       <div class="setup-field">
        <label for="nv-install-hosting-username">Hosting kullanıcı adı (opsiyonel)</label>
        <input class="setup-control" id="nv-install-hosting-username" name="install_hosting_username" maxlength="100" autocomplete="off" placeholder="cPanel kullanıcı adı">
       </div>
      </div>
      <div class="setup-field">
       <label for="nv-install-hosting-panel">Hosting panel adresi (opsiyonel)</label>
       <input class="setup-control" id="nv-install-hosting-panel" name="install_hosting_panel" maxlength="250" type="url" placeholder="https://panel.ornekfirma.com" autocomplete="url">
      </div>
     </div>
     <div class="setup-field">
      <label for="nv-install-notes">Kurulum için ek not (opsiyonel)</label>
      <textarea class="setup-control" id="nv-install-notes" name="install_notes" maxlength="500" placeholder="Örneğin mevcut bir web sitem var, kurulumu alt domaine istiyorum."></textarea>
      <small class="setup-hint warning-text">Güvenliğiniz için hosting şifresi, API anahtarı veya doğrulama kodu girmeyin. Gerekirse ekibimiz sizinle güvenli kanaldan iletişime geçer.</small>
     </div>
    </section>

    <p class="safe">Ödeme bilgileri NetVera sisteminde saklanmaz. Sipariş yalnızca PayTR tarafından doğrulanmış başarılı ödeme bildirimi sonrasında ödenmiş görünür. Yazılım lisansı ve teslimat ayrıca yönetilir.</p>
   </section>
   <aside class="panel payment-summary" aria-label="Ödeme özeti">
    <h2>Ödeme özeti</h2>
    <div class="summary-row"><span><?= e($product['name']) ?></span><strong>1 adet</strong></div>
    <div class="summary-row total"><span>Toplam</span><strong><?= money((float)$product['price']) ?></strong></div>
    <?php if($paytrReady): ?>

      <label class="terms">
       <input type="checkbox" name="terms_accepted" value="1" required>
       <span><a href="/sayfa/mesafeli-satis-sozlesmesi" target="_blank" rel="noopener">Mesafeli Satış Sözleşmesi</a>'ni okudum ve kabul ediyorum.</span>
      </label>
      <button class="cta" type="submit" id="nvScriptPayButton">PayTR ile Ödemeye Geç →</button>

    <?php else: ?>
     <div class="warning">PayTR şu anda etkin değil veya ödeme API bilgileri eksik. Sipariş başlatılamıyor; yönetici panelinden PayTR yapılandırması kontrol edilmelidir.</div>
    <?php endif; ?>
    <p class="safe">Fiyat ödeme başlatılırken veritabanından yeniden doğrulanır. SEO ürün adresi ve mevcut sepetiniz değiştirilmez.</p>
   </aside>
  </div>
  </form>
 </div>
</main>

<script>
(() => {
  const domainMode=document.getElementById('nv-install-domain-mode');
  const hostingMode=document.getElementById('nv-install-hosting-mode');
  const domainGroup=document.getElementById('nv-install-domain-fields');
  const hostingGroup=document.getElementById('nv-install-hosting-fields');
  const domain=document.getElementById('nv-install-domain');
  const provider=document.getElementById('nv-install-hosting-provider');
  const hostingFields=[provider,document.getElementById('nv-install-hosting-panel'),document.getElementById('nv-install-hosting-username')];
  function sync(){
    const hasDomain=domainMode?.value==='existing';
    const hasHosting=hostingMode?.value==='existing';
    domainGroup.hidden=!hasDomain;
    hostingGroup.hidden=!hasHosting;
    domain.required=hasDomain;
    domain.disabled=!hasDomain;
    for(const field of hostingFields)if(field)field.disabled=!hasHosting;
    provider.required=hasHosting;
  }
  domainMode?.addEventListener('change',sync);
  hostingMode?.addEventListener('change',sync);
  sync();
  document.getElementById('nvScriptPayForm')?.addEventListener('submit',function(){
    if(!this.checkValidity())return;
    const button=document.getElementById('nvScriptPayButton');
    if(button){button.disabled=true;button.textContent='Güvenli ödeme hazırlanıyor...';}
  });
})();
</script>
