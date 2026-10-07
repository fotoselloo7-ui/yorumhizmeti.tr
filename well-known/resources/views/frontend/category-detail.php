<div class="cat-page-wrapper">
    <!-- Hero Section -->
    <section class="cat-hero section-dark pb-0">
        <div class="container">
            <div class="breadcrumb" style="margin-bottom: var(--space-4);">
                <a href="/">Ana Sayfa</a> <span class="separator">/</span>
                <a href="/kategoriler">Hizmetler</a> <span class="separator">/</span>
                <span><?= e($category['name']) ?></span>
            </div>
            
            <div class="cat-hero-content">
                <div class="cat-hero-icon">
                    <?php if (!empty($category['image'])): ?>
                        <img src="<?= e(upload_url($category['image'])) ?>" alt="<?= e($category['image_alt'] ?? $category['name']) ?>" style="width: 64px; height: 64px; object-fit: contain;">
                    <?php else: ?>
                        <?= icon($category['icon_key'] ?? 'package', 48) ?>
                    <?php endif; ?>
                </div>
                <div class="cat-hero-text">
                    <h1><?= e($category['name']) ?></h1>
                    <?php if ($category['description']): ?>
                    <p><?= e($category['description']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="cat-trust-badges">
                <div class="trust-badge"><?= icon('shield', 16) ?> Güvenli Ödeme</div>
                <div class="trust-badge"><?= icon('zap', 16) ?> Hızlı Teslimat</div>
                <div class="trust-badge"><?= icon('search', 16) ?> Sipariş Takibi</div>
                <div class="trust-badge"><?= icon('message-circle', 16) ?> 7/24 Destek</div>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <section class="section bg-light">
        <div class="container">
            <!-- Filter & Sort Bar -->
            <div class="cat-filters">
                <?php if (!empty($subCategories)): ?>
                <div class="cat-chip-scroll">
                    <div class="cat-chip-list">
                        <a href="?<?= http_build_query(array_merge($_GET, ['alt' => ''])) ?>" class="cat-chip <?= empty($altSlug) ? 'active' : '' ?>">Tümü</a>
                        <?php foreach ($subCategories as $sub): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['alt' => $sub['slug']])) ?>" class="cat-chip <?= $altSlug === $sub['slug'] ? 'active' : '' ?>">
                            <?= e($sub['name']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="cat-search-sort">
                    <form action="" method="GET" class="cat-search-form">
                        <?php if ($altSlug): ?><input type="hidden" name="alt" value="<?= e($altSlug) ?>"><?php endif; ?>
                        <?php if ($currentSort): ?><input type="hidden" name="sort" value="<?= e($currentSort) ?>"><?php endif; ?>
                        <div class="search-input-wrapper">
                            <?= icon('search', 16) ?>
                            <input type="text" name="q" class="form-control form-control-sm" placeholder="Paket ara..." value="<?= e($searchQuery) ?>">
                            <?php if ($searchQuery): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['q' => ''])) ?>" class="search-clear"><?= icon('x', 14) ?></a>
                            <?php endif; ?>
                        </div>
                    </form>
                    
                    <form action="" method="GET" class="cat-sort-form">
                        <?php if ($altSlug): ?><input type="hidden" name="alt" value="<?= e($altSlug) ?>"><?php endif; ?>
                        <?php if ($searchQuery): ?><input type="hidden" name="q" value="<?= e($searchQuery) ?>"><?php endif; ?>
                        <select name="sort" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="recommended" <?= $currentSort === 'recommended' ? 'selected' : '' ?>>Önerilen</option>
                            <option value="price_asc" <?= $currentSort === 'price_asc' ? 'selected' : '' ?>>En düşük fiyat</option>
                            <option value="price_desc" <?= $currentSort === 'price_desc' ? 'selected' : '' ?>>En yüksek fiyat</option>
                            <option value="featured" <?= $currentSort === 'featured' ? 'selected' : '' ?>>Popüler / Öne Çıkan</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Package Grid -->
            <?php 
            if (!function_exists('getPlatformDesign')) {
                function getPlatformDesign($slug, $name = '') {
                    $l = strtolower($slug . ' ' . $name);
                    if (strpos($l, 'instagram') !== false) return [
                        'icon' => 'instagram', 'color' => '#E1306C', 
                        'gradient' => 'linear-gradient(135deg, #feda75 0%, #fa7e1e 25%, #d62976 50%, #962fbf 75%, #4f5bd5 100%)'
                    ];
                    if (strpos($l, 'tiktok') !== false) return [
                        'icon' => 'tiktok', 'color' => '#000000', 
                        'gradient' => 'linear-gradient(135deg, #000000 0%, #434343 100%)'
                    ];
                    if (strpos($l, 'youtube') !== false) return [
                        'icon' => 'youtube', 'color' => '#FF0000', 
                        'gradient' => 'linear-gradient(135deg, #FF0000 0%, #cc0000 100%)'
                    ];
                    if (strpos($l, 'twitter') !== false || strpos($l, 'x') !== false) return [
                        'icon' => 'twitter', 'color' => '#1DA1F2', 
                        'gradient' => 'linear-gradient(135deg, #1DA1F2 0%, #0d8bd9 100%)'
                    ];
                    if (strpos($l, 'facebook') !== false) return [
                        'icon' => 'facebook', 'color' => '#1877F2', 
                        'gradient' => 'linear-gradient(135deg, #1877F2 0%, #0b5ed7 100%)'
                    ];
                    if (strpos($l, 'google') !== false || strpos($l, 'seo') !== false) return [
                        'icon' => 'google', 'color' => '#4285F4', 
                        'gradient' => 'linear-gradient(135deg, #4285F4 0%, #34A853 50%, #FBBC05 100%)'
                    ];
                    return [
                        'icon' => 'box', 'color' => '#0d6efd', 
                        'gradient' => 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)'
                    ];
                }
            }
            ?>
            <?php if (!empty($packages)): ?>
            <div class="home-pkg-grid">
                <?php foreach ($packages as $pkg): 
                    $price = $pkg['discount_price'] && $pkg['discount_price'] < $pkg['price'] ? $pkg['discount_price'] : $pkg['price'];
                    $catSlug = $pkg['category_slug'] ?? $category['slug'] ?? 'package';
                    $design = getPlatformDesign($catSlug, $pkg['name']);
                ?>
                <a href="/paket/<?= e($pkg['slug']) ?>" class="home-pkg-card fade-in-up">
                    <div class="hpc-banner" style="background: <?= $design['gradient'] ?>;">
                        <div class="hpc-icon" style="color: <?= $design['color'] ?>;">
                            <?= icon($design['icon'], 26) ?>
                        </div>
                        <?php if ($pkg['is_featured']): ?>
                        <span class="hpc-badge" style="color: #fff; background: rgba(0,0,0,0.3); backdrop-filter: blur(4px);"><?= icon('star', 12) ?> Öne Çıkan</span>
                        <?php elseif (!empty($pkg['badge'])): ?>
                        <span class="hpc-badge" style="color: #fff; background: rgba(0,0,0,0.3); backdrop-filter: blur(4px);"><?= e($pkg['badge']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="hpc-body">
                        <span class="hpc-cat"><?= e($pkg['category_name'] ?? $category['name']) ?></span>
                        <h3 class="hpc-title" title="<?= e($pkg['name']) ?>"><?= e($pkg['name']) ?></h3>
                        
                        <div class="hpc-price-box">
                            <?php if ($pkg['discount_price'] && $pkg['discount_price'] < $pkg['price']): ?>
                            <span class="hpc-old"><?= money($pkg['price']) ?></span>
                            <?php endif; ?>
                            <span class="hpc-price"><?= money($price) ?></span>
                        </div>
                        
                        <div class="hpc-footer">
                            <span class="hpc-delivery">
                                <?php if (!empty($pkg['delivery_time'])): ?>
                                    <?= icon('clock', 14) ?> <?= e($pkg['delivery_time']) ?>
                                <?php endif; ?>
                            </span>
                            <span class="hpc-btn">İncele &rarr;</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state modern-empty">
                <?= icon('search', 48) ?>
                <?php if ($searchQuery): ?>
                <h3>Aramanıza uygun paket bulunamadı</h3>
                <p>"<?= e($searchQuery) ?>" araması için sonuç yok. Lütfen başka kelimeler deneyin.</p>
                <a href="?<?= http_build_query(array_merge($_GET, ['q' => ''])) ?>" class="btn btn-outline" style="margin-top: 1rem;">Aramayı Temizle</a>
                <?php else: ?>
                <h3>Bu kategoride henüz paket bulunmuyor</h3>
                <p>Şu an için listelenecek paket bulunmamaktadır.</p>
                <?php if ($altSlug): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['alt' => ''])) ?>" class="btn btn-outline" style="margin-top: 1rem;">Tüm Paketleri Görüntüle</a>
                <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- SEO Area (Only show on main category if no subcategory selected, or if SEO text is needed) -->
    <!-- Assuming the category description is already at top, we don't have a separate HTML content field in DB for categories right now, but if there was, it would go here. -->
</div>

<style>
/* Modern Category Package Cards CSS */
.home-pkg-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 24px;
}
.home-pkg-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
}
.home-pkg-card:hover {
    border-color: #dee2e6;
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    transform: translateY(-4px);
}
.hpc-banner {
    height: 70px;
    position: relative;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    padding: 0 20px;
}
.hpc-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    background: #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    margin-bottom: -28px;
    border: 2px solid #fff;
    z-index: 2;
}
.hpc-badge {
    position: absolute;
    top: 12px; right: 12px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
}
.hpc-body {
    padding: 36px 20px 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.hpc-cat {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}
.hpc-title {
    font-size: 17px;
    font-weight: 800;
    color: #212529;
    margin: 0 0 16px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.hpc-price-box {
    margin-top: auto;
    margin-bottom: 16px;
    display: flex;
    align-items: baseline;
    gap: 8px;
}
.hpc-old {
    font-size: 14px;
    color: #adb5bd;
    text-decoration: line-through;
    font-weight: 500;
}
.hpc-price {
    font-size: 24px;
    font-weight: 800;
    color: #212529;
}
.hpc-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #f1f3f5;
    padding-top: 16px;
}
.hpc-delivery {
    font-size: 13px;
    font-weight: 500;
    color: #6c757d;
    display: flex; align-items: center; gap: 4px;
}
.hpc-btn {
    font-size: 13px;
    font-weight: 700;
    color: var(--color-primary, #0d6efd);
    background: rgba(13, 110, 253, 0.05);
    padding: 6px 12px;
    border-radius: 6px;
    transition: background 0.2s;
}
.home-pkg-card:hover .hpc-btn {
    background: rgba(13, 110, 253, 0.1);
}
</style>
