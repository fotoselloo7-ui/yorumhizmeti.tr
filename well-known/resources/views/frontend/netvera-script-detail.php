<?php
$nv40Price=(float)$product['price'];
$nv40Old=(float)($product['old_price']??0);
$nv40Url='/hazir-scriptler/'.rawurlencode($product['slug']);
$nv40DemoAllowed=$demoUrl && !empty($productData['demo_is_active']) &&
    !empty($productData['demo_is_public']);
$nv40ListTitle=static function($entry):string {
    return is_array($entry)
        ? trim((string)($entry['title']??$entry['name']??$entry['label']??$entry['text']??''))
        : trim((string)$entry);
};
$nv40InquiryReady=\App\Services\NetveraInquiryService::ready();
$nv40OfferHref=$nv40InquiryReady?'#teklif-al':'/iletisim';
$nv60BuyUrl=\App\Services\NetveraBridgeService::publicUrl($productData['buy_url']??null);
$nv60PurchaseEnabled=array_key_exists('purchase_enabled',$productData)
    ? !empty($productData['purchase_enabled'])
    : (bool)$nv60BuyUrl;
$nv60CanBuy=$nv40Price>0 && $nv60PurchaseEnabled && $nv60BuyUrl;
$nv60DemoNote=trim((string)($productData['demo_note']??''));
$nv60PublicDemo=[];
if ($nv40DemoAllowed) {
    // Public preview destinations only, never private demo passwords or customer login data.
    $nv60DemoFile=BASE_PATH.'/database/netvera-demo-public-links.json';
    if (is_file($nv60DemoFile)) {
        $nv60Data=json_decode((string)file_get_contents($nv60DemoFile),true);
        foreach (($nv60Data['demos']??[]) as $nv60Entry) {
            if (!is_array($nv60Entry) || ($nv60Entry['slug']??'')!==($product['slug']??'')) continue;
            if (rtrim((string)($nv60Entry['demo_url']??''),'/') !== rtrim((string)$demoUrl,'/')) continue;
            $nv60PublicDemo=$nv60Entry;
            break;
        }
    }
}
$nv60DemoAdminUrl=\App\Services\NetveraBridgeService::publicUrl($productData['demo_admin_url']??$nv60PublicDemo['demo_admin_url']??null);
$nv60DemoUserUrl=\App\Services\NetveraBridgeService::publicUrl($productData['demo_user_url']??$nv60PublicDemo['demo_user_url']??null);
$nv60ShowAccounts=$nv40DemoAllowed && (
    !empty($productData['demo_credentials_public']) ||
    trim((string)($productData['demo_admin_username']??''))!=='' ||
    trim((string)($productData['demo_admin_password']??''))!=='' ||
    trim((string)($productData['demo_username']??''))!=='' ||
    trim((string)($productData['demo_password']??''))!==''
);
$nv60DemoAccounts=[];
if ($nv60ShowAccounts) {
    foreach ([
        ['label'=>'Yönetim Paneli Demo Girişi','username'=>$productData['demo_admin_username']??'', 'password'=>$productData['demo_admin_password']??'','url'=>$nv60DemoAdminUrl?:$demoUrl,'type'=>'admin'],
        ['label'=>'Kullanıcı Demo Girişi','username'=>$productData['demo_username']??'', 'password'=>$productData['demo_password']??'','url'=>$nv60DemoUserUrl?:$demoUrl,'type'=>'user']
    ] as $nv60Account) {
        if (trim((string)$nv60Account['username'])!=='' || trim((string)$nv60Account['password'])!=='') $nv60DemoAccounts[]=$nv60Account;
    }
    $nv60ExtraAccounts=json_decode((string)($productData['demo_accounts_json']??'[]'),true);
    if (is_array($nv60ExtraAccounts)) foreach (array_slice($nv60ExtraAccounts,0,6) as $nv60Extra) {
        if (!is_array($nv60Extra)) continue;
        $nv60User=trim((string)($nv60Extra['username']??''));
        if ($nv60User==='') continue;
        $nv60DemoAccounts[]=[
            'label'=>mb_substr((string)($nv60Extra['label']??'Demo Hesabı'),0,80),
            'username'=>$nv60User,'password'=>(string)($nv60Extra['password']??''),
            'url'=>\App\Services\NetveraBridgeService::publicUrl($nv60Extra['url']??'')?:$demoUrl
        ];
    }
}
?>
<main class="nv40-detail">
  <section class="nv40-product-hero"><div class="container">
    <nav class="nv40-breadcrumb" aria-label="Yol haritası"><a href="/">Ana Sayfa</a><span>/</span>
      <a href="/hazir-scriptler">Hazır Scriptler</a><span>/</span>
      <span><?= e($product['name']) ?></span></nav>
    <div class="nv40-product-layout">
      <div class="nv40-product-media">
        <div class="nv40-primary-image">
          <?php if(!empty($product['cover_image'])): ?>
          <img src="<?= e(upload_url($product['cover_image'])) ?>" alt="<?= e($product['name']) ?>">
          <?php else: ?><div class="nv40-no-image"><?= icon('monitor',80) ?></div><?php endif; ?>
        </div>
        <?php if($gallery): ?>
        <div class="nv40-gallery" aria-label="Yazılım ekran görüntüleri">
          <?php foreach($gallery as $shot): ?>
          <a href="<?= e(upload_url($shot['image_path'])) ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?= e(upload_url($shot['image_path'])) ?>"
                 alt="<?= e($shot['alt_text']?:$product['name'].' ekran görüntüsü') ?>" loading="lazy">
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="nv40-summary">
        <span class="nv40-kicker"><?= icon('layers',14) ?> <?= e($product['category_name']??'Profesyonel Yazılım') ?></span>
        <h1><?= e($product['name']) ?></h1>
        <?php if(!empty($product['short_desc'])): ?><p><?= e($product['short_desc']) ?></p><?php endif; ?>
        <div class="nv40-signals">
          <?php if(!empty($productData['current_version'])): ?><span><?= icon('git-branch',13) ?> Sürüm: <?= e($productData['current_version']) ?></span><?php endif; ?>
          <?php if(!empty($productData['install_type'])): ?><span><?= icon('settings',13) ?> Kurulum: <?= e($productData['install_type']) ?></span><?php endif; ?>
          <?php if($reviews): ?><span><?= icon('star',13) ?> <?= count($reviews) ?> onaylı değerlendirme</span><?php endif; ?>
        </div>
        <div class="nv40-price-box">
          <small>Yazılım Fiyatı</small>
          <div><strong><?= money($nv40Price) ?></strong>
            <?php if($nv40Old>$nv40Price): ?><del><?= money($nv40Old) ?></del><?php endif; ?></div>
          <?php if(!empty($productCoupons)): ?>
          <div class="nv98-product-coupons" aria-label="Bu yazılıma özel aktif kuponlar">
            <?php foreach(array_slice($productCoupons,0,3) as $coupon): ?>
            <div class="nv98-product-coupon">
              <span><?= icon('tag',13) ?></span>
              <div><small><?= e($coupon['discount']) ?></small><strong><?= e($coupon['title']) ?></strong></div>
              <button type="button" data-copy-coupon="<?= e($coupon['code']) ?>" title="Kupon kodunu kopyala"><?= e($coupon['code']) ?></button>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <p><?= $nv60CanBuy
            ? 'Yazılımı doğrudan satın alabilir, teklif isteyebilir veya canlı demoyu inceleyebilirsiniz.'
            : 'Profesyonel yazılım, kurulum ve lisans seçenekleri hakkında teklif alın veya canlı demoyu inceleyin.' ?></p>
          <div class="nv40-cta-row">
            <?php if($nv60CanBuy): ?>
            <a href="<?= e($nv60BuyUrl) ?>" class="nv40-cta-buy"><?= icon('shopping-cart',16) ?> Satın Al</a>
            <?php endif; ?>
            <a href="<?= e($nv40OfferHref) ?>" class="nv40-cta-primary" <?= $nv40InquiryReady?'data-nv60-jump-tab="teklif-al"':'' ?>><?= icon('message-square',16) ?> Teklif Al</a>
            <?php if($nv40DemoAllowed): ?><a href="<?= e($demoUrl) ?>" target="_blank" rel="noopener noreferrer" class="nv40-cta-demo"><?= icon('external-link',16) ?> Canlı Demo</a><?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </div></section>
  <section class="nv40-product-body"><div class="container">
    <nav class="nv40-anchor-nav nv60-product-tabs" aria-label="Yazılım ayrıntıları" role="tablist" data-nv60-tabs>
      <button type="button" role="tab" id="nv60-tab-ozellikler" aria-controls="ozellikler" aria-selected="true" tabindex="0" data-nv60-tab="ozellikler">Ürün Açıklaması</button>
      <?php if($nv40DemoAllowed): ?><button type="button" role="tab" id="nv60-tab-demo" aria-controls="demo" aria-selected="false" tabindex="-1" data-nv60-tab="demo">Canlı Demo</button><?php endif; ?>
      <?php if($nv40InquiryReady): ?><button type="button" role="tab" id="nv60-tab-teklif-al" aria-controls="teklif-al" aria-selected="false" tabindex="-1" data-nv60-tab="teklif-al">Teklif Al</button><?php endif; ?>
      <?php if($modules): ?><button type="button" role="tab" id="nv60-tab-moduller" aria-controls="moduller" aria-selected="false" tabindex="-1" data-nv60-tab="moduller">Modüller</button><?php endif; ?>
      <?php if($technical): ?><button type="button" role="tab" id="nv60-tab-teknik" aria-controls="teknik" aria-selected="false" tabindex="-1" data-nv60-tab="teknik">Teknik Özellikler</button><?php endif; ?>
      <?php if($license): ?><button type="button" role="tab" id="nv60-tab-lisans" aria-controls="lisans" aria-selected="false" tabindex="-1" data-nv60-tab="lisans">Lisans</button><?php endif; ?>
      <?php if($faqs): ?><button type="button" role="tab" id="nv60-tab-sorular" aria-controls="sorular" aria-selected="false" tabindex="-1" data-nv60-tab="sorular">SSS</button><?php endif; ?>
      <button type="button" role="tab" id="nv60-tab-yorumlar" aria-controls="yorumlar" aria-selected="false" tabindex="-1" data-nv60-tab="yorumlar">Yorumlar</button>
    </nav>
    <div class="nv40-info-layout">
      <div class="nv40-info-main">
        <section class="nv40-info-card" id="ozellikler" role="tabpanel" aria-labelledby="nv60-tab-ozellikler" tabindex="0" data-nv60-panel="ozellikler"><h2>Yazılım Hakkında</h2>
          <div class="nv40-article nv60-article-collapsed" data-nv60-article><?php
            $description=(string)($product['description']??'');
            echo preg_match('~</?[a-z][^>]*>~i',$description)
              ? \App\Services\SanitizerService::cleanHtml($description)
              : nl2br(e($description));
          ?></div>
          <div class="nv60-description-more">
            <button type="button" class="nv60-description-toggle" data-nv60-description-toggle aria-expanded="false">
              Açıklamanın tamamını göster <?= icon('chevron-down',14) ?>
            </button>
          </div>
        </section>
        <?php if($nv40DemoAllowed): ?>
        <section class="nv40-info-card" id="demo" role="tabpanel" aria-labelledby="nv60-tab-demo" tabindex="0" data-nv60-panel="demo">
          <div class="nv62-demo-panel-head">
            <div><h2>Canlı Demo & Yazılımı İncele</h2><p>Ön yüzü ve varsa yönetim panelini ayrı ayrı açabilir, yayınlanmış demo hesabı bilgilerini aşağıdan kullanabilirsiniz.</p></div>
            <?php if($nv60CanBuy): ?><a class="nv62-demo-buy" href="<?= e($nv60BuyUrl) ?>"><?= icon('shopping-cart',14) ?> Satın Al</a><?php endif; ?>
          </div>
          
          <div class="nv60-demo-grid">
            <a class="nv60-demo-link" href="<?= e($demoUrl) ?>" target="_blank" rel="noopener noreferrer">
              <?= icon('external-link',18) ?> <span><strong>Canlı Siteyi Görüntüle</strong><small><?= e((string)parse_url($demoUrl,PHP_URL_HOST)) ?></small></span><?= icon('arrow-up-right',15) ?>
            </a>
            <?php if($nv60DemoAdminUrl): ?>
            <a class="nv60-demo-link" href="<?= e($nv60DemoAdminUrl) ?>" target="_blank" rel="noopener noreferrer">
              <?= icon('settings',18) ?> <span><strong>Demo Yönetim Paneli</strong><small>Yönetim arayüzünü aç</small></span><?= icon('arrow-up-right',15) ?>
            </a>
            <?php endif; ?>
            <?php if($nv60DemoUserUrl && rtrim($nv60DemoUserUrl,'/')!==rtrim($demoUrl,'/')): ?>
            <a class="nv60-demo-link" href="<?= e($nv60DemoUserUrl) ?>" target="_blank" rel="noopener noreferrer">
              <?= icon('users',18) ?> <span><strong>Demo Kullanıcı Sayfası</strong><small>Örnek kullanıcı arayüzünü aç</small></span><?= icon('arrow-up-right',15) ?>
            </a>
            <?php endif; ?>
          </div>
          <?php if($nv60DemoAccounts): ?>
          <div class="nv62-demo-access">
            <div class="nv62-demo-access-head">
              <div><span><?= icon('monitor',15) ?></span><div><small>Satın almadan önce</small><strong>Demo Yönetim Paneli</strong></div></div>
              <span class="nv62-demo-badge">ADMIN</span>
            </div>
            <?php foreach($nv60DemoAccounts as $nv60Account): ?>
            <?php if(($nv60Account['type']??'')==='admin'): ?>
              <?php $nv62LoginLabel=str_contains((string)($nv60Account['username']??''),'@')?'Admin E-posta':'Admin Kullanıcı Adı'; ?>
              <?php if(!empty($nv60Account['username'])): ?>
              <div class="nv62-demo-row">
                <span><?= e($nv62LoginLabel) ?></span>
                <code data-nv62-value><?= e($nv60Account['username']) ?></code>
                <button type="button" class="nv62-demo-copy" data-nv62-copy>Kopyala</button>
              </div>
              <?php endif; ?>
              <?php if(!empty($nv60Account['password'])): ?>
              <div class="nv62-demo-row">
                <span>Admin Şifresi</span>
                <code data-nv62-value data-nv62-secret><?= e($nv60Account['password']) ?></code>
                <button type="button" class="nv62-demo-toggle" data-nv62-toggle>Göster</button>
                <button type="button" class="nv62-demo-copy" data-nv62-copy>Kopyala</button>
              </div>
              <?php endif; ?>
              <?php if(!empty($nv60Account['url'])): ?>
              <a class="nv62-demo-login" href="<?= e($nv60Account['url']) ?>" target="_blank" rel="noopener noreferrer">Giriş Yap <?= icon('external-link',13) ?></a>
              <?php endif; ?>
            <?php endif; ?>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <?php if($nv60DemoNote!==''): ?><div class="nv62-demo-note"><?= icon('info',14) ?><p><?= nl2br(e($nv60DemoNote)) ?></p></div><?php endif; ?>
          
        </section>
        <?php endif; ?>
        <?php if($nv40InquiryReady): ?>
          <section class="nv40-info-card nv60-offer-panel nv-product-offer" id="teklif-al" role="tabpanel" aria-labelledby="nv60-tab-teklif-al" tabindex="0" data-nv60-panel="teklif-al">
            <h2><?= icon('message-square',16) ?> Bu Yazılım İçin Teklif Alın</h2>
            
            <form method="post" action="/netvera/canli-destek/gonder">
              <?= csrfField() ?>
              <input type="hidden" name="source_type" value="offer">
              <input type="hidden" name="product_slug" value="<?= e($product['slug']) ?>">
              <input type="text" class="nv-chat-honeypot" name="website" autocomplete="off" tabindex="-1" aria-hidden="true">
              <div class="nv-product-offer-grid">
                <label>Adınız<input name="name" required minlength="2" maxlength="140" autocomplete="name" placeholder="Adınız Soyadınız"></label>
                <label>E-posta veya Telefon<input name="contact" required minlength="5" maxlength="190" placeholder="Size ulaşabileceğimiz bilgi"></label>
              </div>
              <label class="nv-offer-message">Proje / Kurulum Talebiniz
                <textarea name="message" required minlength="5" maxlength="3000" placeholder="İhtiyacınızı kısaca anlatın..."></textarea>
              </label>
              <button type="submit"><?= icon('send',14) ?> Teklif Talebi Gönder</button>
              <p class="nv-offer-policy">Gönderdiğiniz bilgiler yalnızca teklifinizi yanıtlamak için kullanılır. <a href="/sayfa/kvkk">KVKK</a></p>
            </form>
          </section>
        <?php endif; ?>
        <?php if($modules): ?><section class="nv40-info-card" id="moduller" role="tabpanel" aria-labelledby="nv60-tab-moduller" tabindex="0" data-nv60-panel="moduller"><h2>Yazılım Modülleri</h2>
          <div class="nv40-feature-list">
          <?php foreach($modules as $module):$title=$nv40ListTitle($module);if($title==='')continue; ?>
            <span><?= icon('check-circle',15) ?> <?= e($title) ?></span>
          <?php endforeach; ?></div></section><?php endif; ?>
        <?php if($technical): ?><section class="nv40-info-card" id="teknik" role="tabpanel" aria-labelledby="nv60-tab-teknik" tabindex="0" data-nv60-panel="teknik"><h2>Teknik Özellikler</h2>
          <div class="nv40-spec-list">
          <?php foreach($technical as $key=>$spec):
            $name=is_array($spec)?trim((string)($spec['label']??$spec['name']??$spec['key']??'')):(is_string($key)?trim($key):'');
            $value=is_array($spec)?($spec['value']??$spec['text']??$spec['title']??''):$spec;
            if(is_array($value))$value=implode(', ',array_filter($value,'is_scalar'));
            if(!is_scalar($value))continue;
            $value=trim((string)$value);
            if($value==='')continue;
            if($name===''): ?>
            <div class="nv61-spec-feature"><?= icon('check-circle',16) ?><span><?= e($value) ?></span></div>
            <?php else: ?>
            <div class="nv61-spec-pair"><strong><?= e($name) ?></strong><span><?= e($value) ?></span></div>
            <?php endif; ?>
          <?php endforeach; ?></div></section><?php endif; ?>
        <?php if($license): ?><section class="nv40-info-card" id="lisans" role="tabpanel" aria-labelledby="nv60-tab-lisans" tabindex="0" data-nv60-panel="lisans"><h2>Lisans ve Kullanım Hakları</h2>
          <div class="nv40-feature-list"><?php foreach($license as $entry):$title=$nv40ListTitle($entry);if($title==='')continue; ?>
            <span><?= icon('shield-check',15) ?> <?= e($title) ?></span>
          <?php endforeach; ?></div></section><?php endif; ?>
        <?php if($faqs): ?><section class="nv40-info-card" id="sorular" role="tabpanel" aria-labelledby="nv60-tab-sorular" tabindex="0" data-nv60-panel="sorular"><h2>Sık Sorulan Sorular</h2>
          <?php foreach($faqs as $faq):if(!is_array($faq))continue; ?>
          <details class="nv40-faq"><summary><?= e($faq['q']??$faq['question']??'') ?></summary>
            <p><?= e($faq['a']??$faq['answer']??'') ?></p></details>
          <?php endforeach; ?></section><?php endif; ?>
        <section class="nv40-info-card" id="yorumlar" role="tabpanel" aria-labelledby="nv60-tab-yorumlar" tabindex="0" data-nv60-panel="yorumlar"><h2>Müşteri Değerlendirmeleri</h2>
          <?php if($reviews): ?>
          
          <?php foreach($reviews as $review): ?><article class="nv40-review">
            <span aria-label="<?= (int)$review['rating'] ?> yıldız"><?= str_repeat('★',max(1,min(5,(int)$review['rating']))) ?></span>
            <p><?= e($review['comment']) ?></p><small>Yayınlanmış müşteri değerlendirmesi</small>
          </article><?php endforeach; ?>
          <?php endif; ?>
        </section>
      </div>
      <aside class="nv40-detail-aside">
        <strong><?= icon('shield-check',16) ?> Ürün Bilgileri</strong>
        <?php foreach(['current_version'=>'Mevcut Sürüm','last_updated_on'=>'Son Güncelleme',
          'install_type'=>'Kurulum Türü','install_info'=>'Kurulum Bilgisi'] as $key=>$label):
          if(empty($productData[$key]))continue; ?>
          <div><small><?= e($label) ?></small><span><?= e($productData[$key]) ?></span></div>
        <?php endforeach; ?>
        <a href="/iletisim">Ürün Hakkında Soru Sor <?= icon('arrow-right',13) ?></a>
      </aside>
    </div>
  </div></section>
</main>
<script>
document.querySelectorAll('[data-nv62-copy]').forEach(function(btn){
  btn.addEventListener('click',function(){
    var row=btn.closest('.nv62-demo-row');
    var value=row&&row.querySelector('[data-nv62-value]');
    if(!value)return;
    var text=value.textContent||'';
    if(navigator.clipboard&&navigator.clipboard.writeText){
      navigator.clipboard.writeText(text);
    }else{
      var area=document.createElement('textarea');
      area.value=text;document.body.appendChild(area);area.select();
      document.execCommand('copy');area.remove();
    }
    btn.textContent='Kopyalandı';
    setTimeout(function(){btn.textContent='Kopyala';},1200);
  });
});
document.querySelectorAll('[data-nv62-toggle]').forEach(function(btn){
  btn.addEventListener('click',function(){
    var row=btn.closest('.nv62-demo-row');
    var value=row&&row.querySelector('[data-nv62-secret]');
    if(!value)return;
    var hidden=value.classList.toggle('is-hidden');
    btn.textContent=hidden?'Göster':'Gizle';
  });
});
document.querySelectorAll('[data-nv62-secret]').forEach(function(value){value.classList.add('is-hidden');});
document.querySelectorAll('[data-copy-coupon]').forEach(function(btn){
  btn.addEventListener('click',async function(){
    var code=btn.getAttribute('data-copy-coupon')||'';
    if(!code)return;
    try{await navigator.clipboard.writeText(code);}catch(e){
      var t=document.createElement('textarea');t.value=code;document.body.appendChild(t);t.select();document.execCommand('copy');t.remove();
    }
    var old=btn.textContent;btn.textContent='Kopyalandı';btn.classList.add('is-copied');
    setTimeout(function(){btn.textContent=old;btn.classList.remove('is-copied');},1500);
  });
});
</script>
