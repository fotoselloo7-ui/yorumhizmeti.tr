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
$nv40Host=strtolower((string)($_SERVER['HTTP_HOST']??''));
$nv40ExternalLive=!in_array($nv40Host,['netvera.tr','www.netvera.tr'],true);
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
          <p>Ödeme sağlayıcısı canlı Netvera'da korunuyor. Bu staging görünümünden ödeme alınmaz.</p>
          <div class="nv40-cta-row">
            <?php if($nv40DemoAllowed): ?><a href="<?= e($demoUrl) ?>" target="_blank" rel="noopener noreferrer" class="nv40-cta-demo"><?= icon('external-link',16) ?> Canlı Demo</a><?php endif; ?>
            <?php if($nv40ExternalLive): ?>
            <a href="https://netvera.tr<?= e($nv40Url) ?>" target="_blank" rel="noopener noreferrer" class="nv40-cta-primary">Netvera'daki Ürünü Aç <?= icon('arrow-up-right',16) ?></a>
            <?php else: ?>
            <a href="/iletisim" class="nv40-cta-primary">Satış Ekibiyle İletişim <?= icon('arrow-right',16) ?></a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div></section>
  <section class="nv40-product-body"><div class="container">
    <nav class="nv40-anchor-nav" aria-label="Yazılım ayrıntıları">
      <a href="#ozellikler">Ürün Açıklaması</a>
      <?php if($modules): ?><a href="#moduller">Modüller</a><?php endif; ?>
      <?php if($technical): ?><a href="#teknik">Teknik Özellikler</a><?php endif; ?>
      <?php if($license): ?><a href="#lisans">Lisans</a><?php endif; ?>
      <?php if($faqs): ?><a href="#sorular">SSS</a><?php endif; ?>
      <a href="#yorumlar">Yorumlar</a>
    </nav>
    <div class="nv40-info-layout">
      <div class="nv40-info-main">
        <section class="nv40-info-card" id="ozellikler"><h2>Yazılım Hakkında</h2>
          <div class="nv40-article"><?= nl2br(e(trim(strip_tags((string)$product['description'])))) ?></div>
        </section>
        <?php if($modules): ?><section class="nv40-info-card" id="moduller"><h2>Yazılım Modülleri</h2>
          <div class="nv40-feature-list">
          <?php foreach($modules as $module):$title=$nv40ListTitle($module);if($title==='')continue; ?>
            <span><?= icon('check-circle',15) ?> <?= e($title) ?></span>
          <?php endforeach; ?></div></section><?php endif; ?>
        <?php if($technical): ?><section class="nv40-info-card" id="teknik"><h2>Teknik Özellikler</h2>
          <div class="nv40-spec-list">
          <?php foreach($technical as $key=>$spec):
            $name=is_array($spec)?($spec['label']??$spec['name']??$spec['key']??''):(is_string($key)?$key:'');
            $value=is_array($spec)?($spec['value']??$spec['text']??''):$spec;
            if(is_array($value))$value=implode(', ',array_filter($value,'is_scalar'));
            if($value===''||!is_scalar($value))continue; ?>
            <div><strong><?= e((string)$name) ?></strong><span><?= e((string)$value) ?></span></div>
          <?php endforeach; ?></div></section><?php endif; ?>
        <?php if($license): ?><section class="nv40-info-card" id="lisans"><h2>Lisans ve Kullanım Hakları</h2>
          <div class="nv40-feature-list"><?php foreach($license as $entry):$title=$nv40ListTitle($entry);if($title==='')continue; ?>
            <span><?= icon('shield-check',15) ?> <?= e($title) ?></span>
          <?php endforeach; ?></div></section><?php endif; ?>
        <?php if($faqs): ?><section class="nv40-info-card" id="sorular"><h2>Sık Sorulan Sorular</h2>
          <?php foreach($faqs as $faq):if(!is_array($faq))continue; ?>
          <details class="nv40-faq"><summary><?= e($faq['q']??$faq['question']??'') ?></summary>
            <p><?= e($faq['a']??$faq['answer']??'') ?></p></details>
          <?php endforeach; ?></section><?php endif; ?>
        <section class="nv40-info-card" id="yorumlar"><h2>Müşteri Değerlendirmeleri</h2>
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