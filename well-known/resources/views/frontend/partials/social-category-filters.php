<div class="nv-social-flow" aria-label="Hizmet seçim adımları"><span <?= $socialCatalog['show_services']?'aria-current="step"':'' ?>>1. Hizmet türü</span><span><?= icon('chevron-right',14) ?></span><span <?= !$socialCatalog['show_services']?'aria-current="step"':'' ?>>2. Paket miktarı</span><span><?= icon('chevron-right',14) ?></span><span>3. Kullanıcı adı / bağlantı</span></div>
<nav class="nv-social-service-filters" aria-label="Sosyal medya hizmet türleri">
 <a href="<?= e(\App\Services\PublicSeoUrls::category($category)) ?>" <?= empty($socialCatalog['requested'])?'aria-current="page"':'' ?>>Tüm Hizmetler</a>
 <?php foreach($socialCatalog['groups'] as $service): ?>
 <a href="<?= e($service['url']) ?>" <?= $socialCatalog['requested']===$service['key']?'aria-current="page"':'' ?>><?= e($service['name']) ?></a>
 <?php endforeach; ?>
</nav>
<form method="GET" class="nv-social-search" aria-label="Sosyal medya paket araması">

 <label><?= icon('search',18) ?><input type="search" name="q" value="<?= e($searchQuery) ?>" aria-label="Hizmet veya paket ara" placeholder="Hizmet veya paket ara..."></label>
 <select name="sort" aria-label="Paket sıralaması"><option value="recommended" <?= $currentSort==='recommended'?'selected':'' ?>>Önerilen sıralama</option><option value="price_asc" <?= $currentSort==='price_asc'?'selected':'' ?>>Fiyat: Artan</option><option value="price_desc" <?= $currentSort==='price_desc'?'selected':'' ?>>Fiyat: Azalan</option><option value="featured" <?= $currentSort==='featured'?'selected':'' ?>>Öne çıkanlar</option></select>
 <button type="submit">Uygula <?= icon('arrow-right',16) ?></button>
</form>
