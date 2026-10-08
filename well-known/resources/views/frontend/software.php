<?php
$nv36Active = $softwareFilter !== '' || $softwareQuery !== '' || $softwareMin !== null || $softwareMax !== null || $softwareDiscount || $softwareFeatured;
$nv36Query = static function(array $overrides = []) use($softwareFilter,$softwareQuery,$softwareSort,$softwareMin,$softwareMax,$softwareDiscount,$softwareFeatured): string {
    $args = [
        'tur'=>$softwareFilter,'q'=>$softwareQuery,'siralama'=>$softwareSort,
        'min'=>$softwareMin,'max'=>$softwareMax,
        'indirim'=>$softwareDiscount?'1':'','one_cikan'=>$softwareFeatured?'1':'',
    ];
    foreach($overrides as $key=>$value) $args[$key]=$value;
    return '/hazir-yazilimlar'.(($q=http_build_query(array_filter($args,static fn($v)=>$v!==''&&$v!==null)))?'?'.$q:'');
};
?>
<main class="nv36-marketplace">
  <section class="nv36-hero">
    <div class="container nv36-hero-inner">
      <div>
        <span class="nv31-kicker"><?= icon('monitor',13) ?> YAZILIM MARKETİ</span>
        <h1>Hazır Yazılımlar <span>&amp; Scriptler</span></h1>
        <p>Web yazılımları, WordPress temaları, eklentiler ve otomasyon çözümlerini keşfedin.</p>
      </div>
      <div class="nv36-hero-summary">
        <span class="nv36-hero-icon"><?= icon('layers',21) ?></span>
        <div><strong><?= (int)$softwareAllCount ?> yazılım</strong><small>Aktif ürün kataloğu</small></div>
      </div>
    </div>
  </section>

  <section class="nv36-shop" aria-label="Yazılım kataloğu ve gelişmiş filtreler">
    <div class="container nv36-shop-layout">
      <aside class="nv36-sidebar" id="nv36-sidebar">
        <button type="button" class="nv36-mobile-filter-toggle" data-software-filter-toggle aria-expanded="false" aria-controls="nv36-filter-content">
          <?= icon('sliders',17) ?> Filtreleri Aç <?= icon('chevron-down',15) ?>
        </button>
        <form id="nv36-filter-form" action="/hazir-yazilimlar" method="get" class="nv36-filter-content">
          <div class="nv36-filter-top">
            <div><span><?= icon('sliders',15) ?></span><strong>Filtreler</strong></div>
            <a href="/hazir-yazilimlar">Temizle</a>
          </div>
          <div class="nv36-filter-section nv36-category-section">
            <strong class="nv36-filter-label">Yazılım Kategorileri</strong>
            <nav class="nv36-category-scroll" aria-label="Yazılım kategorileri">
              <a class="nv36-category-choice <?= $softwareFilter===''?'active':'' ?>" href="<?= e($nv36Query(['tur'=>''])) ?>">
                <span><?= icon('grid',15) ?> Tüm Yazılımlar</span><em><?= (int)$softwareAllCount ?></em>
              </a>
              <?php foreach($catalogGroups as $groupTitle=>$groupRows):
                $groupContains = in_array($softwareFilter,array_column($groupRows,1),true);
              ?>
              <details class="nv36-category-group" <?= $groupContains?'open':'' ?>>
                <summary><?= e($groupTitle) ?> <?= icon('chevron-down',14) ?></summary>
                <div class="nv36-category-links">
                  <?php foreach($groupRows as [$label,$slug,$desc,$symbol]): ?>
                  <a class="nv36-category-choice <?= $softwareFilter===$slug?'active':'' ?>"
                     href="<?= e($nv36Query(['tur'=>$slug])) ?>"
                     title="<?= e($desc) ?>">
                    <span><?= icon($symbol,14) ?> <?= e($label) ?></span>
                    <em><?= (int)($softwareCounts[$slug]??0) ?></em>
                  </a>
                  <?php endforeach; ?>
                </div>
              </details>
              <?php endforeach; ?>
            </nav>
          </div>
          <?php if($softwareFilter!==''): ?><input type="hidden" name="tur" value="<?= e($softwareFilter) ?>"><?php endif; ?>
          <?php if($softwareQuery!==''): ?><input type="hidden" name="q" value="<?= e($softwareQuery) ?>"><?php endif; ?>
          <div class="nv36-filter-section">
            <label class="nv36-filter-label" for="nv36-min">Fiyat Aralığı (₺)</label>
            <div class="nv36-price-fields">
              <label><span>En az</span><input id="nv36-min" name="min" type="number" min="0" step="1" value="<?= $softwareMin!==null?e((string)$softwareMin):'' ?>" placeholder="Min"></label>
              <span class="nv36-price-divider">–</span>
              <label><span>En fazla</span><input id="nv36-max" name="max" type="number" min="0" step="1" value="<?= $softwareMax!==null?e((string)$softwareMax):'' ?>" placeholder="Max"></label>
            </div>
          </div>
          <div class="nv36-filter-section">
            <strong class="nv36-filter-label">Ürün Durumu</strong>
            <label class="nv36-check"><input name="indirim" type="checkbox" value="1" <?= $softwareDiscount?'checked':'' ?>><span>İndirimli yazılımlar</span><?= icon('tag',14) ?></label>
            <label class="nv36-check"><input name="one_cikan" type="checkbox" value="1" <?= $softwareFeatured?'checked':'' ?>><span>Öne çıkanlar</span><?= icon('star',14) ?></label>
          </div>
          <div class="nv36-filter-section nv36-rating-section">
            <strong class="nv36-filter-label">Müşteri Değerlendirmesi</strong>
            <p><?= icon('shield-check',14) ?> Ürün bazında doğrulanmış değerlendirmeler henüz bağlı değil. Puan uydurulmaz; gerçek değerlendirmeler eklendiğinde filtre etkinleştirilebilir.</p>
          </div>
          <input type="hidden" name="siralama" value="<?= e($softwareSort) ?>">
          <button class="nv36-apply" type="submit">Filtreleri Uygula <?= icon('arrow-right',14) ?></button>
        </form>
      </aside>
      <div class="nv36-results" id="nv33-products">
        <div class="nv36-results-bar">
          <div class="nv36-results-heading">
            <span class="nv31-kicker"><?= icon('package',12) ?> YAZILIM KATALOĞU</span>
            <h2>Satıştaki <span>Yazılımlar</span></h2>
            <p><strong><?= count($softwareProducts) ?></strong> ürün listeleniyor<?= $softwareAllCount!==count($softwareProducts)?' · toplam '.(int)$softwareAllCount.' yazılım':'' ?></p>
          </div>
          <form action="/hazir-yazilimlar" method="get" class="nv36-search-form" role="search">
            <?php if($softwareFilter!==''): ?><input type="hidden" name="tur" value="<?= e($softwareFilter) ?>"><?php endif; ?>
            <?php if($softwareMin!==null): ?><input type="hidden" name="min" value="<?= e((string)$softwareMin) ?>"><?php endif; ?>
            <?php if($softwareMax!==null): ?><input type="hidden" name="max" value="<?= e((string)$softwareMax) ?>"><?php endif; ?>
            <?php if($softwareDiscount): ?><input type="hidden" name="indirim" value="1"><?php endif; ?>
            <?php if($softwareFeatured): ?><input type="hidden" name="one_cikan" value="1"><?php endif; ?>
            <input type="hidden" name="siralama" value="<?= e($softwareSort) ?>">
            <label for="nv36-search"><?= icon('search',15) ?></label>
            <input id="nv36-search" name="q" type="search" maxlength="90" value="<?= e($softwareQuery) ?>" placeholder="Yazılım, script veya tema ara...">
            <button type="submit" aria-label="Ara"><?= icon('arrow-right',15) ?></button>
          </form>
          <form action="/hazir-yazilimlar" method="get" class="nv36-sort-form">
            <?php if($softwareFilter!==''): ?><input type="hidden" name="tur" value="<?= e($softwareFilter) ?>"><?php endif; ?>
            <?php if($softwareQuery!==''): ?><input type="hidden" name="q" value="<?= e($softwareQuery) ?>"><?php endif; ?>
            <?php if($softwareMin!==null): ?><input type="hidden" name="min" value="<?= e((string)$softwareMin) ?>"><?php endif; ?>
            <?php if($softwareMax!==null): ?><input type="hidden" name="max" value="<?= e((string)$softwareMax) ?>"><?php endif; ?>
            <?php if($softwareDiscount): ?><input type="hidden" name="indirim" value="1"><?php endif; ?>
            <?php if($softwareFeatured): ?><input type="hidden" name="one_cikan" value="1"><?php endif; ?>
            <label for="nv36-sort">Sırala:</label>
            <select id="nv36-sort" name="siralama" data-software-sort>
              <option value="onerilen" <?= $softwareSort==='onerilen'?'selected':'' ?>>Önerilen</option>
              <option value="yeni" <?= $softwareSort==='yeni'?'selected':'' ?>>En Yeni</option>
              <option value="ucuz" <?= $softwareSort==='ucuz'?'selected':'' ?>>Önce En Ucuz</option>
              <option value="pahali" <?= $softwareSort==='pahali'?'selected':'' ?>>Önce En Pahalı</option>
              <option value="ad" <?= $softwareSort==='ad'?'selected':'' ?>>Ada Göre</option>
            </select>
            <button class="nv36-sort-submit" type="submit" aria-label="Sırala"><?= icon('arrow-right',13) ?></button>
          </form>
        </div>
        <?php if($nv36Active): ?>
        <div class="nv36-active-filters">
          <span><?= icon('filter',12) ?> Aktif filtreler:</span>
          <?php if($softwareQuery!==''): ?><span>Arama: <?= e($softwareQuery) ?></span><?php endif; ?>
          <?php if($softwareFilter!==''): ?><span>Kategori: <?= e($softwareFilter) ?></span><?php endif; ?>
          <?php if($softwareMin!==null||$softwareMax!==null): ?><span>Fiyat: <?= $softwareMin===null?'0':e((string)$softwareMin) ?>–<?= $softwareMax===null?'∞':e((string)$softwareMax) ?> ₺</span><?php endif; ?>
          <?php if($softwareDiscount): ?><span>İndirimli</span><?php endif; ?>
          <?php if($softwareFeatured): ?><span>Öne çıkan</span><?php endif; ?>
          <a href="/hazir-yazilimlar">Temizle <?= icon('x',12) ?></a>
        </div>
        <?php endif; ?>
        <?php if(!empty($softwareProducts)): ?>
        <div class="nv31-software-grid nv36-product-grid">
          <?php foreach($softwareProducts as $i=>$sw):
            $price = $sw['discount_price']!==null&&(float)$sw['discount_price']<(float)$sw['price']?$sw['discount_price']:$sw['price'];
            $short = trim(strip_tags((string)($sw['short_description'] ?: $sw['description'] ?? '')));
          ?>
          <article class="nv31-software-card">
            <a href="/paket/<?= e($sw['slug']) ?>" class="nv31-software-image" aria-label="<?= e($sw['name']) ?> yazılım detayları">
              <?php if(!empty($sw['image'])): ?>
              <img src="<?= e(upload_url($sw['image'])) ?>" alt="<?= e($sw['image_alt'] ?: $sw['name']) ?>" loading="lazy">
              <?php else: ?>
              <span class="nv31-software-image-placeholder"><span><?= icon('monitor',45) ?></span><span><?= icon('settings',19) ?></span><span><?= icon('globe',25) ?></span></span>
              <?php endif; ?>
              <?php if(!empty($sw['badge'])): ?><span class="nv36-product-badge"><?= e($sw['badge']) ?></span><?php endif; ?>
              <span class="nv31-software-image-arrow"><?= icon('arrow-up-right',17) ?></span>
            </a>
            <div class="nv31-software-body">
              <span class="nv31-software-tag"><?= icon('layers',11) ?> <?= e($sw['category_name']) ?></span>
              <h3><a href="/paket/<?= e($sw['slug']) ?>"><?= e($sw['name']) ?></a></h3>
              <p><?= e(mb_strimwidth($short,0,112,'…','UTF-8')) ?></p>
              <div class="nv31-software-bottom">
                <strong><?= money($price) ?></strong>
                <a href="/paket/<?= e($sw['slug']) ?>" aria-label="<?= e($sw['name']) ?> incele"><?= icon('arrow-right',16) ?></a>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="nv33-catalog-empty">
          <span><?= icon('search',32) ?></span><h3>Bu filtrelerle eşleşen yazılım bulunamadı.</h3>
          <p>Kategoriyi veya fiyat aralığını değiştirebilir, tüm aktif yazılımlara dönebilirsiniz.</p>
          <a href="/hazir-yazilimlar">Tüm Yazılımları Göster <?= icon('arrow-right',14) ?></a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>
