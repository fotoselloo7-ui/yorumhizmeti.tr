<?php
// Keep existing indexed catalogue URLs; GET facets only adjust the visible results.
$nv40Context = array_filter([
    'q' => $filterQuery ?? '',
    'type' => $filterType ?? '',
    'min_price' => $filterMinPrice ?? null,
    'max_price' => $filterMaxPrice ?? null,
    'min_rating' => ($filterMinRating ?? 0) ?: null,
    'sort' => ($filterSort ?? 'recommended') === 'recommended' ? null : $filterSort,
], static fn($value) => $value !== null && $value !== '');
$nv40CatalogPath = $filterCategory !== ''
    ? \App\Services\PublicSeoUrls::path('/hazir-scriptler/kategori', $filterCategory)
    : (($filterType ?? '') !== ''
        ? \App\Services\PublicSeoUrls::path('/hazir-scriptler/tur', $filterType)
        : '/hazir-scriptler');
$nv40CategoryLink = static function (?string $category) use ($nv40Context): string {
    $params = $nv40Context;
    $path = $category !== null && $category !== ''
        ? \App\Services\PublicSeoUrls::path('/hazir-scriptler/kategori', $category)
        : (!empty($params['type']) ? \App\Services\PublicSeoUrls::path('/hazir-scriptler/tur', (string)$params['type']) : '/hazir-scriptler');
    if ($category === null || $category === '') unset($params['type']);
    return \App\Services\PublicSeoUrls::withQuery($path, $params);
};
?>
<main class="nv40-store">
  <section class="nv40-store-hero"><div class="container">
    <span class="nv40-kicker"><?= icon('monitor',14) ?> NETVERA YAZILIM KOLEKSİYONU</span>
    <h1>Hazır Yazılımlar <span>&amp; Scriptler</span></h1>
    <p>Sektörel yazılımları karşılaştırın, demo ve teknik özelliklerini inceleyin.</p>
  </div></section>
  <section class="nv40-store-body"><div class="container nv40-store-layout">
    <aside class="nv40-categories" aria-label="Yazılım filtreleri">
      <div class="nv40-sidebar-heading">
        <strong><?= icon('sliders',17) ?> Yazılımları Filtrele</strong>
        <a href="/hazir-scriptler">Temizle</a>
      </div>
      <details class="nv40-filter-disclosure" open><summary><?= icon('sliders',14) ?> Kategoriler, fiyat ve sıralama <?= icon('chevron-down',14) ?></summary><div class="nv40-filter-inner">
      <nav class="nv40-category-links" aria-label="Yazılım kategorileri">
        <strong><?= icon('layers',15) ?> Kategoriler</strong>
        <a href="<?= e($nv40CategoryLink(null)) ?>" class="<?= $filterCategory === '' ? 'active' : '' ?>">Tüm Yazılımlar</a>
        <?php foreach($categories as $cat): ?>
        <a href="<?= e($nv40CategoryLink((string)$cat['slug'])) ?>"
           class="<?= $filterCategory === $cat['slug'] ? 'active' : '' ?>"><?= e($cat['name']) ?></a>
        <?php endforeach; ?>
      </nav>
      <div class="nv75-types">
        <div class="nv75-types-heading"><?= icon('grid',14) ?> Yazılım Türleri</div>
        <nav class="nv75-types-list" aria-label="NetVera hazır yazılım türleri">
          <?php foreach(($softwareTypes??[]) as [$typeName,$typeSlug,$typeDescription,$typeIcon]): ?>
          <a href="<?= e(\App\Services\PublicSeoUrls::withQuery(
                  $filterCategory !== '' ? $nv40CatalogPath
                      : \App\Services\PublicSeoUrls::path('/hazir-scriptler/tur', $typeSlug),
                  array_filter([
                      'type'=>$filterCategory !== '' ? $typeSlug : '',
                      'q'=>$filterQuery??''
                  ], static fn($v)=>$v!=='')
              )) ?>"
             class="nv75-type-link <?= ($filterType??'')===$typeSlug?'active':'' ?>"
             title="<?= e($typeDescription) ?>">
             <?= icon($typeIcon,14) ?><span><?= e($typeName) ?></span>
             <em><?= (int)($softwareTypeCounts[$typeSlug]??0) ?></em>
          </a>
          <?php endforeach; ?>
        </nav>
      </div>
      <form class="nv40-filter-form" role="search" method="get" action="<?= e($nv40CatalogPath) ?>">
        <?php if(($filterType??'')!=='' && $filterCategory !== ''): ?><input type="hidden" name="type" value="<?= e($filterType) ?>"><?php endif; ?>
        <?php if($filterQuery !== ''): ?><input type="hidden" name="q" value="<?= e($filterQuery) ?>"><?php endif; ?>
        <fieldset class="nv40-filter-group">
          <legend><?= icon('wallet',15) ?> Fiyat Aralığı</legend>
          <div class="nv40-price-inputs">
            <label>En az
              <input type="number" min="0" max="100000000" step="0.01" inputmode="decimal"
                     name="min_price" placeholder="₺ 0" value="<?= $filterMinPrice !== null ? e((string)$filterMinPrice) : '' ?>">
            </label>
            <label>En çok
              <input type="number" min="0" max="100000000" step="0.01" inputmode="decimal"
                     name="max_price" placeholder="₺ sınırsız" value="<?= $filterMaxPrice !== null ? e((string)$filterMaxPrice) : '' ?>">
            </label>
          </div>
        </fieldset>
        <fieldset class="nv40-filter-group">
          <legend><?= icon('star',15) ?> Müşteri Değerlendirmesi</legend>
          <select name="min_rating" aria-label="En düşük müşteri puanı">
            <option value="0">Tüm değerlendirmeler</option>
            <?php foreach([4,3,2,1] as $rating): ?>
              <option value="<?= $rating ?>" <?= (int)$filterMinRating === $rating ? 'selected' : '' ?>><?= $rating ?> yıldız ve üzeri</option>
            <?php endforeach; ?>
          </select>
          <small>Yalnızca gerçek, onaylı değerlendirmeler dikkate alınır.</small>
        </fieldset>
        <fieldset class="nv40-filter-group">
          <legend><?= icon('arrow-down-up',15) ?> Sıralama</legend>
          <select name="sort" aria-label="Yazılımları sırala">
            <?php foreach([
              'recommended'=>'Önerilen sıralama',
              'newest'=>'En yeni yazılımlar',
              'price_asc'=>'Fiyat: düşükten yükseğe',
              'price_desc'=>'Fiyat: yüksekten düşüğe',
              'rating'=>'En çok değerlendirilenler'
            ] as $value=>$label): ?>
              <option value="<?= e($value) ?>" <?= $filterSort === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </fieldset>
        <button class="nv40-apply-filters" type="submit"><?= icon('check',15) ?> Filtreleri Uygula</button>
      </form>
      </div></details>
      <a class="nv40-catalog-shortcut" href="/hazir-scriptler">Tüm Hazır Scriptleri Gör <?= icon('arrow-right',13) ?></a>
    </aside>
    <div class="nv40-store-main">
      <div class="nv40-store-head">
        <div><h2>Yazılım Ürünleri</h2><p><?= count($products) ?> ürün listeleniyor</p></div>
        <form role="search" method="get" action="<?= e($nv40CatalogPath) ?>">
            <?php if(($filterType??'')!==''): ?><input type="hidden" name="type" value="<?= e($filterType) ?>"><?php endif; ?>
        <?php if(($filterType??'')!==''): ?><input type="hidden" name="type" value="<?= e($filterType) ?>"><?php endif; ?>
          <?php if($filterMinPrice !== null): ?><input type="hidden" name="min_price" value="<?= e((string)$filterMinPrice) ?>"><?php endif; ?>
          <?php if($filterMaxPrice !== null): ?><input type="hidden" name="max_price" value="<?= e((string)$filterMaxPrice) ?>"><?php endif; ?>
          <?php if($filterMinRating): ?><input type="hidden" name="min_rating" value="<?= (int)$filterMinRating ?>"><?php endif; ?>
          <?php if($filterSort !== 'recommended'): ?><input type="hidden" name="sort" value="<?= e($filterSort) ?>"><?php endif; ?>
          <label class="nv40-search-label">
            <span class="sr-only">Yazılım ara</span>
            <input type="search" name="q" maxlength="70" placeholder="Yazılım ara..." value="<?= e($filterQuery) ?>">
          </label>
          <button type="submit" aria-label="Ara"><?= icon('search',15) ?></button>
        </form>
      </div>
      <div class="nv40-product-grid">
      <?php foreach($products as $item): ?>
        <article class="nv40-product-card">
          <a href="/hazir-scriptler/<?= e($item['slug']) ?>" class="nv40-product-cover">
            <?php if(!empty($item['cover_image'])): ?>
            <img src="<?= e(upload_url($item['cover_image'])) ?>" alt="<?= e($item['name']) ?>" loading="lazy">
            <?php else: ?><span><?= icon('monitor',40) ?></span><?php endif; ?>
            <?= icon('arrow-up-right',18) ?>
          </a>
          <div class="nv40-product-copy">
            <span><?= e($item['category_name'] ?? 'Hazır Yazılım') ?></span>
            <h3><a href="/hazir-scriptler/<?= e($item['slug']) ?>"><?= e($item['name']) ?></a></h3>
            <p><?= e(mb_strimwidth(strip_tags((string)$item['short_desc']),0,128,'…','UTF-8')) ?></p>
            <?php if((int)($item['review_count'] ?? 0) > 0): ?>
              <div class="nv40-product-rating" aria-label="Müşteri değerlendirmesi">
                <?= icon('star',13) ?>
                <strong><?= e(number_format((float)$item['average_rating'],1,',','.')) ?>/5</strong>
                <span>(<?= (int)$item['review_count'] ?> onaylı değerlendirme)</span>
              </div>
            <?php endif; ?>
            <div class="nv40-product-bottom"><strong><?= money($item['price']) ?></strong>
              <a href="/hazir-scriptler/<?= e($item['slug']) ?>">İncele <?= icon('arrow-right',14) ?></a></div>
          </div>
        </article>
      <?php endforeach; ?>
      </div>
      <?php if(!$products): ?>
        <div class="nv40-empty"><?= icon('search',24) ?> Bu kriterlerde yayınlanmış yazılım bulunamadı. <a href="/hazir-scriptler">Filtreleri temizle</a></div>
      <?php endif; ?>
    </div>
  </div></section>
</main>
<script>
(function () {
  var details = document.querySelector('.nv40-filter-disclosure');
  if (!details || !window.matchMedia) return;
  var mobile = window.matchMedia('(max-width: 750px)');
  function fitWidth() { details.open = !mobile.matches; }
  fitWidth();
  if (mobile.addEventListener) mobile.addEventListener('change', fitWidth);
  else if (mobile.addListener) mobile.addListener(fitWidth);
}());
</script>
