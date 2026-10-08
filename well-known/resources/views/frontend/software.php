<main class="nv33-catalog">
  <section class="nv33-catalog-hero">
    <div class="container nv33-catalog-hero-inner">
      <div>
        <span class="nv31-kicker"><?= icon('monitor',14) ?> YAZILIM & WEB ÇÖZÜMLERİ</span>
        <h1>Hazır Yazılımlar <span>& Scriptler</span></h1>
        <p>Haber sitesi, e-ticaret, emlak, rezervasyon, firma yönetimi ve sektöre özel web yazılımlarını bir arada keşfedin.</p>
        <a href="#nv33-products" class="nv32-primary-cta">Yazılımları İncele <?= icon('arrow-right',15) ?></a>
      </div>
      <div class="nv33-hero-art" aria-hidden="true">
        <span><?= icon('monitor',49) ?></span><span><?= icon('settings',31) ?></span><span><?= icon('layers',30) ?></span>
      </div>
    </div>
  </section>
  <section class="nv33-types" aria-labelledby="nv33-types-title">
    <div class="container">
      <div class="nv31-section-heading"><div>
        <span class="nv31-kicker"><?= icon('grid',13) ?> YAZILIM TÜRLERİ</span>
        <h2 id="nv33-types-title">İhtiyacınıza göre <span>keşfedin.</span></h2>
        <p>Sektör ve kullanım amacına göre hazırlanmış yazılım kategorileri.</p>
      </div></div>
      <nav class="nv33-type-grid" aria-label="Hazır yazılım türleri">
        <a class="nv33-type <?= $softwareFilter===''?'active':'' ?>" href="/hazir-yazilimlar">
          <span><?= icon('grid',19) ?></span><strong>Tüm Yazılımlar</strong><?= icon('arrow-right',13) ?>
        </a>
        <?php foreach ($catalogTypes as [$name,$slug,$description,$symbol]): ?>
        <a class="nv33-type <?= $softwareFilter===$slug?'active':'' ?>" href="/hazir-yazilimlar?tur=<?= rawurlencode($slug) ?>" title="<?= e($description) ?>">
          <span><?= icon($symbol,19) ?></span><strong><?= e($name) ?></strong><?= icon('arrow-right',13) ?>
        </a>
        <?php endforeach; ?>
      </nav>
    </div>
  </section>
  <section class="nv31-software nv33-products" id="nv33-products" aria-labelledby="nv33-products-title">
    <div class="container">
      <div class="nv31-section-heading"><div>
        <span class="nv31-kicker"><?= icon('package',13) ?> GÜNCEL YAZILIM PAKETLERİ</span>
        <h2 id="nv33-products-title"><?= $softwareFilter!==''?'Seçilen kategorideki':'Satıştaki' ?> <span>Yazılımlar</span></h2>
        <p>Aktif olan yazılımları ve mevcut fiyatlarını inceleyin.</p>
      </div></div>
      <?php if (!empty($softwarePackages)): ?>
      <div class="nv31-software-grid">
        <?php foreach($softwarePackages as $i=>$sw):
          $price = !empty($sw['discount_price']) && (float)$sw['discount_price']<(float)$sw['price'] ? $sw['discount_price'] : $sw['price'];
          $short = trim(strip_tags((string)($sw['short_description'] ?: $sw['description'] ?? '')));
        ?>
        <article class="nv31-software-card">
          <a class="nv31-software-image" href="/paket/<?= e($sw['slug']) ?>" aria-label="<?= e($sw['name']) ?>">
            <?php if(!empty($sw['image'])): ?>
            <img src="<?= e(upload_url($sw['image'])) ?>" alt="<?= e($sw['image_alt'] ?: $sw['name']) ?>" loading="lazy">
            <?php else: ?>
            <span class="nv31-software-image-placeholder"><span><?= icon('monitor',45) ?></span><span><?= icon('settings',19) ?></span><span><?= icon('globe',25) ?></span></span>
            <?php endif; ?>
            <span class="nv31-software-number"><?= str_pad((string)($i+1),2,'0',STR_PAD_LEFT) ?></span>
            <span class="nv31-software-image-arrow"><?= icon('arrow-up-right',17) ?></span>
          </a>
          <div class="nv31-software-body">
            <span class="nv31-software-tag"><?= icon('layers',11) ?> <?= e($sw['category_name']) ?></span>
            <h3><a href="/paket/<?= e($sw['slug']) ?>"><?= e($sw['name']) ?></a></h3>
            <p><?= e(mb_strimwidth($short,0,115,'…','UTF-8')) ?></p>
            <div class="nv31-software-bottom"><strong><?= money($price) ?></strong><a href="/paket/<?= e($sw['slug']) ?>"><?= icon('arrow-right',16) ?></a></div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="nv33-catalog-empty">
        <span><?= icon('monitor',32) ?></span>
        <h3>Bu alanda henüz yayınlanmış yazılım bulunmuyor.</h3>
        <p>Farklı yazılım kategorilerini inceleyebilir veya ihtiyacınıza uygun bir çözüm için bizimle iletişime geçebilirsiniz.</p>
        <a href="/iletisim">Projenizi Konuşalım <?= icon('arrow-right',14) ?></a>
      </div>
      <?php endif; ?>
    </div>
  </section>
</main>
