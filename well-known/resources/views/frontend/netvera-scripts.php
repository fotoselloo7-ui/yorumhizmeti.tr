<main class="nv40-store">
  <section class="nv40-store-hero"><div class="container">
    <span class="nv40-kicker"><?= icon('monitor',14) ?> NETVERA YAZILIM KOLEKSİYONU</span>
    <h1>Hazır Yazılımlar <span>&amp; Scriptler</span></h1>
    <p>Netvera'nın sektörel ve kurumsal yazılımlarını, mevcut ürün adresleri korunarak inceleyin.</p>
  </div></section>
  <section class="nv40-store-body"><div class="container nv40-store-layout">
    <aside class="nv40-categories">
      <strong><?= icon('layers',17) ?> Yazılım Kategorileri</strong>
      <a href="/hazir-scriptler" class="<?= $filterCategory===''?'active':'' ?>">Tüm Yazılımlar</a>
      <?php foreach($categories as $cat): ?>
      <a href="/hazir-scriptler?category=<?= rawurlencode($cat['slug']) ?>"
         class="<?= $filterCategory===$cat['slug']?'active':'' ?>"><?= e($cat['name']) ?></a>
      <?php endforeach; ?>
      <a class="nv40-catalog-shortcut" href="/hazir-yazilimlar">Diğer Yazılımları Keşfet <?= icon('arrow-right',13) ?></a>
    </aside>
    <div class="nv40-store-main">
      <div class="nv40-store-head"><div><h2>Yazılım Ürünleri</h2><p><?= count($products) ?> ürün</p></div>
        <form role="search" method="GET" action="/hazir-scriptler">
          <?php if($filterCategory!==''): ?><input type="hidden" name="category" value="<?= e($filterCategory) ?>"><?php endif; ?>
          <input type="search" name="q" maxlength="70" placeholder="Yazılım ara..." value="<?= e($filterQuery) ?>">
          <button type="submit"><?= icon('search',15) ?></button>
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
            <span><?= e($item['category_name']??'Hazır Yazılım') ?></span>
            <h3><a href="/hazir-scriptler/<?= e($item['slug']) ?>"><?= e($item['name']) ?></a></h3>
            <p><?= e(mb_strimwidth(strip_tags((string)$item['short_desc']),0,128,'…','UTF-8')) ?></p>
            <div class="nv40-product-bottom"><strong><?= money($item['price']) ?></strong>
              <a href="/hazir-scriptler/<?= e($item['slug']) ?>">İncele <?= icon('arrow-right',14) ?></a></div>
          </div>
        </article>
      <?php endforeach; ?>
      </div>
      <?php if(!$products): ?>
        <div class="nv40-empty"><?= icon('search',24) ?> Bu filtrede yayınlanmış bir yazılım bulunamadı.</div>
      <?php endif; ?>
    </div>
  </div></section>
</main>