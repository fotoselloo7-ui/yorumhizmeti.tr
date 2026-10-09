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
$nv60ShowAccounts=$nv40DemoAllowed && !empty($productData['demo_credentials_public']);
$nv60DemoAccounts=[];
if ($nv60ShowAccounts) {
    foreach ([
        ['label'=>'Kullanıcı Demo Hesabı','username'=>$productData['demo_username']??'', 'password'=>$productData['demo_password']??'','url'=>$nv60DemoUserUrl?:$demoUrl],
        ['label'=>'Yönetim Paneli Demo Hesabı','username'=>$productData['demo_admin_username']??'', 'password'=>$productData['demo_admin_password']??'','url'=>$nv60DemoAdminUrl?:$demoUrl]
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
          <p>Profesyonel yazılım, kurulum ve lisans seçenekleri hakkında teklif alın veya canlı demoyu inceleyin.</p>
          <div class="nv40-cta-row">
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
          <h2>Canlı Demo & Yazılımı İncele</h2>
          <p>Ürünün çalışan sürümünü ve varsa herkese açık deneme yönetim panelini ziyaret edebilirsiniz.</p>
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
          <div class="nv60-demo-accounts" aria-label="Herkese açık tanıtım demo hesapları">
            <?php foreach($nv60DemoAccounts as $nv60Account): ?>
            <div class="nv60-demo-account">
              <h3><?= icon('key',15) ?> <?= e($nv60Account['label']) ?></h3>
              <?php if(!empty($nv60Account['username'])): ?><div class="nv60-demo-credential"><strong>Kullanıcı Adı</strong><code><?= e($nv60Account['username']) ?></code></div><?php endif; ?>
              <?php if(!empty($nv60Account['password'])): ?><div class="nv60-demo-credential"><strong>Demo Şifresi</strong><code><?= e($nv60Account['password']) ?></code></div><?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <?php if(!empty($productData['demo_note'])): ?><p class="nv60-demo-disclaimer"><?= nl2br(e((string)$productData['demo_note'])) ?></p><?php endif; ?>
          <?php if(!$nv60DemoAccounts): ?><p class="nv60-demo-disclaimer">Yönetim panelinden herkese açık demo hesabı tanımlanmadı. Demo sitesini bağlantılardan inceleyebilir, gerektiğinde satış ekibinden giriş bilgisi isteyebilirsiniz.</p><?php endif; ?>
        </section>
        <?php endif; ?>
        <?php if($nv40InquiryReady): ?>
          <section class="nv40-info-card nv60-offer-panel" id="teklif-al" role="tabpanel" aria-labelledby="nv60-tab-teklif-al" tabindex="0" data-nv60-panel="teklif-al">
            <h2><?= icon('message-square',16) ?> Bu Yazılım İçin Teklif Alın</h2>
            <p>Kurulum, lisans ve proje detaylarını birlikte netleştirelim. Talebinizi satış ekibimize iletin; sizinle kurulum ve lisans seçeneklerini görüşelim.</p>
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
          <?php if(!$reviews): ?><p>Bu yazılım için aktarılmış onaylı değerlendirme henüz bulunmuyor.</p>
          <?php else: ?>
          <p>Mevcut sistemden aktarılan onaylı yorumlar; kişisel hesaplar aktarılmadığı için isimler gösterilmez.</p>
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