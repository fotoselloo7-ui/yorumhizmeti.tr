<?php
/** Merchant-owned frame wrapper only. PayTR owns the actual card entry UI. */
$paytrPreview = !empty($preview);
$paytrToken = trim((string)($iframeToken??''));
$paytrOrder = $order??[];
$paytrTotal = (float)($paytrOrder['total_amount']??0);
$paytrReference = (string)($paytrOrder['order_number']??'');
$paytrCustomer = trim((string)($user['email']??''));
?>
<link rel="stylesheet" href="<?= asset('css/paytr-payment-v45.css') ?>?v=45.1">
<main class="nv45-checkout">
  <div class="container">
    <nav class="nv45-breadcrumb" aria-label="Ödeme adımları">
      <a href="/">Ana Sayfa</a><?= icon('chevron-right',13) ?>
      <a href="/odeme">Sipariş ve Ödeme</a><?= icon('chevron-right',13) ?>
      <span>Güvenli Kart Ödemesi</span>
    </nav>
    <div class="nv45-checkout-header">
      <div>
        <span class="nv45-checkout-kicker"><?= icon('shield-check',15) ?> GÜVENLİ ÖDEME</span>
        <h1>Ödemenizi <span>güvenle tamamlayın.</span></h1>
        <p>Ödeme alanı doğrudan PayTR tarafından sunulur. Kart numaranız ve güvenlik kodunuz bu web sitesi tarafından kaydedilmez.</p>
      </div>
      <div class="nv45-powered"><?= icon('lock-keyhole',17) ?><span>Ödeme sağlayıcısı<strong>PayTR</strong></span></div>
    </div>
    <?php if($paytrPreview): ?>
      <div class="nv45-preview-alert" role="status">
        <?= icon('info',18) ?>
        <div><strong>Yalnızca yerel tasarım önizlemesi</strong><p>Bu ekranda ödeme başlatılmaz ve kart bilgisi istenmez. Gerçek PayTR formu, mağaza bilgileri ve onaylı test işlemiyle açılır.</p></div>
      </div>
    <?php endif; ?>
    <div class="nv45-checkout-grid">
      <section class="nv45-checkout-panel" aria-label="PayTR ödeme alanı">
        <div class="nv45-panel-heading">
          <span class="nv45-payment-step">01</span>
          <div><h2>Kredi veya Banka Kartıyla Ödeme</h2><p>Ödeme işlemini PayTR'nin güvenli formunda tamamlayın.</p></div>
          <?= icon('shield',22) ?>
        </div>
        <?php if(!$paytrPreview && $gateway==='paytr' && $paytrToken!==''): ?>
          <div class="nv45-paytr-frame">
            <iframe
              id="paytriframe"
              title="PayTR güvenli ödeme formu"
              src="https://www.paytr.com/odeme/guvenli/<?= rawurlencode($paytrToken) ?>"
              scrolling="no" loading="eager"
              referrerpolicy="strict-origin-when-cross-origin"
              allow="payment *"
              style="width:100%;min-height:660px;border:0"></iframe>
          </div>
          <script src="https://www.paytr.com/js/iframeResizer.min.js?v2" defer></script>
          <script>
            window.addEventListener('load',function(){
              if(typeof window.iFrameResize==='function'){
                window.iFrameResize({checkOrigin:['https://www.paytr.com'],heightCalculationMethod:'bodyScroll'},'#paytriframe');
              }
            });
          </script>
        <?php else: ?>
          <div class="nv45-preview-frame" aria-label="PayTR ödeme formu örnek konumu">
            <span class="nv45-preview-logo"><?= icon('credit-card',37) ?></span>
            <strong>PayTR Güvenli Kart Formu</strong>
            <p>Gerçek ödeme ekranı burada otomatik olarak açılır.</p>
            <span class="nv45-preview-placeholder"><span></span><span></span><span></span></span>
            <small><?= $paytrPreview?'Önizleme modunda ödeme yapılmaz.':'Ödeme sağlayıcısı başlatılamadı.' ?></small>
          </div>
        <?php endif; ?>
        <div class="nv45-security-row">
          <span><?= icon('shield-check',15) ?> PayTR tarafından işletilen form</span>
          <span><?= icon('credit-card',15) ?> Kart verisi sitemizde saklanmaz</span>
        </div>
      </section>

      <aside class="nv45-summary-panel" aria-label="Sipariş özeti">
        <div class="nv45-summary-header">
          <span class="nv45-summary-icon"><?= icon('receipt-text',19) ?></span>
          <div><h2>Sipariş Özeti</h2><p>Ödeme detaylarınız</p></div>
        </div>
        <?php if($paytrReference!==''): ?>
          <div class="nv45-summary-line"><span>Sipariş No</span><strong><?= e($paytrReference) ?></strong></div>
        <?php endif; ?>
        <?php if($paytrCustomer!==''): ?>
          <div class="nv45-summary-line"><span>E-posta</span><strong class="nv45-break"><?= e($paytrCustomer) ?></strong></div>
        <?php endif; ?>
        <div class="nv45-summary-line"><span>Ödeme yöntemi</span><strong>PayTR · Online Kart</strong></div>
        <?php if($paytrTotal>0): ?>
          <div class="nv45-summary-total"><span>Ödenecek Tutar</span><strong><?= money($paytrTotal) ?></strong></div>
        <?php endif; ?>
        <p class="nv45-summary-notice"><?= icon('info',14) ?> Siparişinizin ödeme onayı PayTR'nin sunucu bildirimiyle kesinleşir. Yönlendirme sayfası tek başına ödeme onayı değildir.</p>
        <a class="nv45-return-link" href="/siparislerim"><?= icon('arrow-left',15) ?> Siparişlerime Dön</a>
        <div class="nv45-cards" aria-label="Kart ağları">
          <img src="<?= asset('img/payments/visa.svg') ?>" alt="Visa" width="55" height="29" loading="lazy">
          <img src="<?= asset('img/payments/mastercard.svg') ?>" alt="Mastercard" width="55" height="29" loading="lazy">
          <img src="<?= asset('img/payments/troy.svg') ?>" alt="TROY" width="55" height="29" loading="lazy">
        </div>
      </aside>
    </div>
  </div>
</main>
