<?php
$allCatalogGroups = \App\Services\CatalogMenuService::groups();
$catalogGroups = $catalogGroups ?? $allCatalogGroups;
$activeCatalogGroup = $activeCatalogGroup ?? '';
$searchQuery = $searchQuery ?? '';
?>
<main class="nv26-catalog">
  <section class="nv26-catalog-hero"><div class="container nv26-catalog-hero-inner">
    <div><span class="nv26-catalog-kicker"><?= icon('layers',15) ?> NetVera Dijital Hizmet Merkezi</span>
      <h1>İhtiyacınıza uygun <span>hizmeti keşfedin.</span></h1>
      
    </div>
    <a class="nv26-catalog-hero-link" href="#categories">Kategorileri keşfet <?= icon('arrow-right',15) ?></a>
  </div></section>
  <section id="categories" class="nv26-catalog-body"><div class="container">
    <div class="nv26-catalog-toolbar">
      <div class="nv26-catalog-toolbar-title"><span class="nv26-toolbar-icon"><?= icon('grid',20) ?></span>
        <div><strong>Tüm Hizmet Kategorileri</strong><small>Platformlara ve ihtiyaçlarınıza göre keşfedin.</small></div>
      </div>
      <form class="nv26-catalog-search" method="GET" action="/kategoriler" role="search">
        <?php if($activeCatalogGroup!==''): ?><input type="hidden" name="grup" value="<?= e($activeCatalogGroup) ?>"><?php endif; ?>
        <?= icon('search',17) ?><input type="search" name="q" value="<?= e($searchQuery) ?>" placeholder="Hizmet veya platform ara..." aria-label="Hizmet kategorisi ara">
        <button type="submit" aria-label="Ara"><?= icon('arrow-right',15) ?></button>
      </form>
    </div>
    <nav class="nv26-catalog-tabs" aria-label="Hizmet grupları">
      <a href="/kategoriler#categories" class="<?= $activeCatalogGroup===''?'active':'' ?>" <?= $activeCatalogGroup===''?'aria-current="page"':'' ?>><?= icon('grid',14) ?> Tüm Hizmetler</a>
      <?php foreach($allCatalogGroups as $group): ?>
      <a href="/kategoriler?grup=<?= e($group['key']) ?>#categories" class="<?= $activeCatalogGroup===$group['key']?'active':'' ?>" <?= $activeCatalogGroup===$group['key']?'aria-current="page"':'' ?>>
        <?= icon($group['icon'],14) ?> <?= e($group['label']) ?>
      </a>
      <?php endforeach; ?>
    </nav>
    <?php if(empty($catalogGroups)): ?>
    <div class="nv26-catalog-empty"><h2>Aramanızla eşleşen kategori bulunamadı.</h2>
      <a href="/kategoriler">Tüm hizmetleri görüntüle</a>
    </div>
    <?php endif; ?>
    <?php foreach($catalogGroups as $group): ?>
    <section class="nv26-catalog-group" id="group-<?= e($group['key']) ?>" aria-labelledby="title-<?= e($group['key']) ?>">
      <div class="nv26-catalog-group-head">
        <div class="nv26-catalog-group-heading"><span class="nv26-section-icon"><?= icon($group['icon'],19) ?></span>
          <div><small>Hizmet Kategorileri</small><h2 id="title-<?= e($group['key']) ?>"><?= e($group['label']) ?></h2>
            <p><?= e($group['description']) ?></p></div>
        </div>
        <span class="nv26-catalog-count"><?= count($group['categories']) ?> kategori</span>
      </div>
      <div class="nv26-catalog-tiles">
        <?php foreach($group['categories'] as $cat): ?>
        <article class="nv26-catalog-tile nv26-tile-<?= e($cat['style']) ?> <?= e(\\App\\Services\\SocialPlatformIdentity::classFor(\\App\\Services\\SocialPlatformIdentity::fromCategory($cat))) ?>">
          <a class="nv26-tile-top" href="<?= e($cat['url']) ?>" aria-label="<?= e($cat['name']) ?> kategorisini incele">
            <span class="nv26-tile-icon"><?= icon($cat['icon'],27) ?></span>
            <strong><?= e($cat['name']) ?></strong>
            <small><?= count($cat['children']) ? count($cat['children']).' alt hizmet' : 'Paketleri incele' ?></small>
            <span class="nv88-tile-arrow" aria-hidden="true"><?= icon('arrow-right',15) ?></span>
          </a>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endforeach; ?>
    <aside class="nv26-catalog-banner">
      <div><span><?= icon('sparkles',15) ?> NetVera Premium</span>
        <h2>Markanız için dijital çözümler <em>tek adreste.</em></h2>
        
        <a href="/iletisim">Projenizi konuşalım <?= icon('arrow-right',13) ?></a>
      </div>
      <div class="nv26-banner-icons" aria-hidden="true"><span><?= icon('instagram',27) ?></span><span><?= icon('globe',27) ?></span><span><?= icon('bar-chart',27) ?></span><span><?= icon('palette',27) ?></span></div>
    </aside>
  </div></section>
</main>
