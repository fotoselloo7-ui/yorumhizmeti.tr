<?php
$sec = function(string $key) use ($sections) { return $sections[$key] ?? null; };
$hero = $sec('hero');
$testimonials = $sec('testimonials');

if (!function_exists('yh6Platform')) {
    function yh6Platform(string $slug = '', string $name = ''): array {
        $s = mb_strtolower($slug . ' ' . $name);
        if (str_contains($s,'instagram')) return ['instagram','instagram','Instagram'];
        if (str_contains($s,'tiktok')) return ['tiktok','tiktok','TikTok'];
        if (str_contains($s,'youtube')) return ['youtube','youtube','YouTube'];
        if (str_contains($s,'facebook')) return ['facebook','facebook','Facebook'];
        if (str_contains($s,'twitter') || str_contains($s,' x ')) return ['twitter','twitter','X'];
        if (str_contains($s,'threads')) return ['threads','threads','Threads'];
        if (str_contains($s,'telegram')) return ['telegram','telegram','Telegram'];
        if (str_contains($s,'spotify')) return ['spotify','spotify','Spotify'];
        if (str_contains($s,'discord')) return ['discord','discord','Discord'];
        if (str_contains($s,'linkedin')) return ['linkedin','linkedin','LinkedIn'];
        if (str_contains($s,'twitch')) return ['twitch','twitch','Twitch'];
        if (str_contains($s,'google')) return ['google','google','Google'];
        if (str_contains($s,'e-ticaret') || str_contains($s,'eticaret')) return ['ecommerce','store','E-Ticaret'];
        if (str_contains($s,'mobil')) return ['mobileapp','mobile-app','Mobil'];
        if (str_contains($s,'içerik') || str_contains($s,'icerik')) return ['content','content-create','İçerik'];
        if (str_contains($s,'grafik')) return ['graphic','palette','Tasarım'];
        if (str_contains($s,'yerel')) return ['local','local-business','Yerel'];
        if (str_contains($s,'web') || str_contains($s,'site')) return ['web','globe','Web Site'];
        if (str_contains($s,'seo')) return ['seo','bar-chart','SEO'];
        return ['default','package','Hizmet'];
    }
}

if (!function_exists('yh6PackagePrice')) {
    function yh6PackagePrice(array $pkg): float {
        return (!empty($pkg['discount_price']) && $pkg['discount_price'] < $pkg['price'])
            ? (float)$pkg['discount_price']
            : (float)$pkg['price'];
    }
}

if (!function_exists('yh6PackageCard')) {
    function yh6PackageCard(array $pkg): string {
        [$cls,$ico,$label] = yh6Platform($pkg['category_slug'] ?? '', $pkg['name'] ?? '');
        $price = yh6PackagePrice($pkg);
        $old = (!empty($pkg['discount_price']) && $pkg['discount_price'] < $pkg['price']) ? (float)$pkg['price'] : null;
        $discount = ($old && $old > 0) ? round((1 - $price / $old) * 100) : null;
        ob_start(); ?>
        <a class="yh6-package-card" href="/paket/<?= e($pkg['slug']) ?>">
            <div class="yh6-package-head">
                <span class="yh6-package-icon <?= e($cls) ?>"><?= icon($ico, 22) ?></span>
                <span class="yh6-package-badge"><?= e($pkg['badge'] ?: (!empty($pkg['is_featured']) ? 'En Popüler' : $label)) ?></span>
            </div>
            <h3><?= e(package_display_name($pkg)) ?></h3>
            <ul>
                <li><?= icon('check-circle', 11) ?> Güvenli ve hızlı işlem</li>
                <li><?= icon('check-circle', 11) ?> Şifresiz sipariş süreci</li>
                <li><?= icon('check-circle', 11) ?> 7/24 destek</li>
            </ul>
            <div class="yh6-rating"><span>★★★★★</span><small>4.9/5 müşteri puanı</small></div>
            <div class="yh6-price">
                <strong><?= $price>0 ? money($price) : 'Teklif Al' ?></strong>
                <?php if ($old): ?><del><?= money($old) ?></del><?php endif; ?>
                <?php if ($discount): ?><em>%<?= $discount ?></em><?php endif; ?>
            </div>
            <span class="yh6-package-cta">Satın Al <?= icon('arrow-right', 11) ?></span>
        </a>
        <?php return ob_get_clean();
    }
}

if (!function_exists('yh18FeaturedCard')) {
    function yh18FeaturedCard(array $pkg, bool $favorite = false): string {
        [$cls,$ico,$label] = yh6Platform($pkg['category_slug'] ?? '', $pkg['category_name'] ?? $pkg['name'] ?? '');
        $price = yh6PackagePrice($pkg);
        $old = (!empty($pkg['discount_price']) && $pkg['discount_price'] < $pkg['price']) ? (float)$pkg['price'] : null;
        $discount = ($old && $old > 0) ? round((1 - $price / $old) * 100) : null;
        $delivery = trim((string)($pkg['delivery_time'] ?? ''));
        $short = trim(strip_tags((string)($pkg['short_description'] ?? '')));
        ob_start(); ?>
        <article class="yh18-featured-card <?= e($cls) ?>">
            <?php if ($favorite): ?><span class="yh18-favorite">Favori Paket</span><?php endif; ?>
            <div class="yh18-card-brand">
                <span class="yh18-card-icon"><?= icon($ico, 22) ?></span>
                <div>
                    <small><?= e($label) ?></small>
                    <h3><?= e(package_display_name($pkg)) ?></h3>
                </div>
            </div>

            <?php if ($short !== ''): ?>
            <p class="yh18-card-summary"><?= e(excerpt($short, 112)) ?></p>
            <?php endif; ?>

            <div class="yh18-card-features">
                <span><?= icon('check-circle', 13) ?><b>Aktif ve güvenli hizmet</b></span>
                <span><?= icon('zap', 13) ?><b><?= e($delivery !== '' ? $delivery : 'Hızlı teslimat') ?></b></span>
                <span><?= icon('shield', 13) ?><b>Şifresiz sipariş</b></span>
                <span><?= icon('headphones', 13) ?><b>7/24 müşteri desteği</b></span>
            </div>

            <div class="yh18-card-bottom">
                <div class="yh18-card-price">
                    <?php if ($old): ?><del><?= money($old) ?></del><?php endif; ?>
                    <strong><?= money($price) ?></strong>
                    <?php if ($discount): ?><em>%<?= $discount ?> indirim</em><?php endif; ?>
                </div>
                <a href="/paket/<?= e($pkg['slug']) ?>" class="yh18-card-cta">Paketi İncele <?= icon('arrow-right', 11) ?></a>
            </div>
        </article>
        <?php return ob_get_clean();
    }
}


/**
 * Authentic Netvera software card. Product destinations retain every indexed
 * /hazir-scriptler/{slug} route; zero-priced products never invent a free offer.
 */
if (!function_exists('yh41SoftwareCard')) {
    function yh41SoftwareCard(array $product): string {
        $price = (float)($product['price'] ?? 0);
        $old = (float)($product['old_price'] ?? 0);
        $discount = ($old > $price && $price > 0) ? round((1-$price/$old)*100) : 0;
        $detail = '/hazir-scriptler/'.rawurlencode((string)$product['slug']);
        $image = trim((string)($product['cover_image'] ?? ''));
        $short = trim(strip_tags((string)($product['short_desc'] ?? '')));
        $ratingCount = (int)($product['review_count'] ?? 0);
        ob_start(); ?>
        <article class="yh18-featured-card yh41-software-card" data-netvera-software-card>
            <a class="yh41-software-media" href="<?= e($detail) ?>" aria-label="<?= e($product['name']) ?> yazılımını incele">
                <?php if ($image): ?>
                <img src="<?= e(upload_url($image)) ?>" alt="<?= e($product['name']) ?>" loading="lazy" decoding="async">
                <?php else: ?>
                <span class="yh41-software-placeholder"><?= icon('monitor', 35) ?></span>
                <?php endif; ?>
                <span class="yh41-software-media-tag"><?= icon('code', 12) ?> HAZIR YAZILIM</span>
            </a>
            <div class="yh41-software-info">
                <small><?= e($product['category_name'] ?: 'NetVera Yazılım') ?></small>
                <h3><a href="<?= e($detail) ?>"><?= e($product['name']) ?></a></h3>
                <?php if ($short): ?><p><?= e(excerpt($short, 106)) ?></p><?php endif; ?>
                <?php if ($ratingCount > 0): ?>
                  <span class="yh41-software-rating"><?= icon('star', 12) ?> <?= e(number_format((float)$product['average_rating'],1,',','.')) ?> / 5 (<?= $ratingCount ?> değerlendirme)</span>
                <?php endif; ?>
            </div>
            <div class="yh18-card-bottom yh41-software-bottom">
                <div class="yh18-card-price">
                    <?php if ($old > $price && $price > 0): ?><del><?= money($old) ?></del><?php endif; ?>
                    <strong><?= $price > 0 ? money($price) : 'Fiyat Sorunuz' ?></strong>
                    <?php if ($discount > 0): ?><em>%<?= $discount ?> indirim</em><?php endif; ?>
                </div>
                <a href="<?= e($detail) ?>" class="yh18-card-cta">Yazılımı İncele <?= icon('arrow-right', 12) ?></a>
            </div>
        </article>
        <?php return ob_get_clean();
    }
}

$heroCutout = asset('img/hero-woman-cutout.png');

$reviews = ($testimonials && !empty($testimonials['extra'])) ? $testimonials['extra'] : [];
?>

<main class="yh-home-v6">

<section class="yh6-hero">
    <div class="container">
        <div class="yh6-hero-grid">
            <div class="yh6-hero-copy">
                <span class="yh6-eyebrow"><?= icon('award', 12) ?> Dijital Çözümler, Daha Güçlü Markalar</span>
                <h1>Dijitalde <span>Daha Güçlü Bir Marka Yaratın!</span></h1>
                <p>Web sitesi ve özel yazılımdan SEO, dijital reklam ve sosyal medya hizmetlerine kadar markanızın ihtiyaç duyduğu çözümleri tek noktadan keşfedin.</p>
                <div class="yh6-hero-actions">
                    <a href="/kategoriler" class="yh6-btn primary">Hemen İncele <?= icon('arrow-right', 12) ?></a>
                    <a href="#how" class="yh6-btn ghost"><?= icon('play-circle', 14) ?> Nasıl Çalışır?</a>
                </div>
                <div class="yh6-hero-metrics">
                    <div><?= icon('users', 16) ?><span><strong>50.000+</strong><small>Mutlu Müşteri</small></span></div>
                    <div><?= icon('star-fill', 16) ?><span><strong>4.9/5</strong><small>Müşteri Puanı</small></span></div>
                    <div><?= icon('zap', 16) ?><span><strong>Hızlı Teslimat</strong><small>Ortalama 0-6 Saat</small></span></div>
                    <div><?= icon('shield', 16) ?><span><strong>%100 Güvenli</strong><small>SSL ile Koruma</small></span></div>
                </div>
            </div>

            <div class="yh6-hero-art">
                <div class="yh6-hero-blob"></div>
                <img class="yh6-hero-person" src="<?= e($heroCutout) ?>" alt="YorumHizmeti dijital hizmetler" loading="eager" fetchpriority="high">

                <span class="yh6-social-float instagram"><?= icon('instagram', 27) ?></span>
                <span class="yh6-social-float tiktok"><?= icon('tiktok', 24) ?></span>
                <span class="yh6-social-float google"><?= icon('google', 25) ?></span>
                <span class="yh6-social-float youtube"><?= icon('youtube', 24) ?></span>

                <div class="yh6-rating-float"><span>★★★★★</span></div>
                <div class="yh6-review-float">
                    <?= icon('google', 19) ?>
                    <div><strong>Yeni Yorum Geldi!</strong><small>5 yıldızlı değerlendirme</small></div>
                </div>
                <div class="yh6-growth-float">
                    <strong>+285</strong><small>Bu hafta yeni müşteri</small>
                    <i></i><i></i><i></i><i></i><i></i>
                </div>
            </div>
        </div>

        <div class="yh6-platform-bar" aria-label="Popüler dijital hizmet kategorileri">
            <?php foreach (($homeQuickCategories ?? []) as $cat):
                [$cls,$ico,$label] = yh6Platform($cat['slug'] ?? '', $cat['name'] ?? '');
                $title = preg_replace('/\\s+Hizmetleri?$/u', '', (string)$cat['name']);
            ?>
            <a href="<?= e($cat['url']) ?>" class="<?= e($cls) ?>">
                <span><?= icon($cat['icon'], 23) ?></span>
                <div><strong><?= e($title) ?></strong><small><?= e($cat['name']) ?></small></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="yh6-why">
    <div class="container">
        <div class="yh6-why-grid">
            <div class="yh6-why-copy">
                <span class="yh6-eyebrow">Markanız İçin En İyisi</span>
                <h2>Neden YorumHizmeti?</h2>
                <p>Sosyal kanıt, günümüz dijital dünyasında başarının anahtarıdır. Markanızın güvenilirliğini artırmak için hızlı, güvenli ve etkili çözümler sunuyoruz.</p>
                <ul>
                    <li><?= icon('check-circle', 14) ?> Gerçek ve kaliteli etkileşimler</li>
                    <li><?= icon('check-circle', 14) ?> Hızlı teslimat ve 7/24 destek</li>
                    <li><?= icon('check-circle', 14) ?> %100 gizli ve güvenli hizmet</li>
                    <li><?= icon('check-circle', 14) ?> Uygun fiyatlarla yüksek performans</li>
                    <li><?= icon('check-circle', 14) ?> Tüm platformlar için tek adres</li>
                </ul>
                <a href="/kategoriler" class="yh6-btn primary">Hizmetlerimizi Keşfet <?= icon('arrow-right', 12) ?></a>
            </div>

            <div class="yh6-dashboard">
                <div class="yh6-browser">
                    <div class="yh6-browser-top"><span></span><span></span><span></span><b>Hesap İstatistikleri</b><em>Son 30 Gün</em></div>
                    <div class="yh6-dash-stats"><div><small>Toplam Yorum</small><strong>1.248</strong><b>↗ %46</b></div><div><small>Etkileşim</small><strong>25,6K</strong><b>↗ %42</b></div><div><small>Görüntülenme</small><strong>532K</strong><b>↗ %39</b></div></div>
                    <div class="yh6-chart"><svg viewBox="0 0 600 180" preserveAspectRatio="none"><polyline fill="none" stroke="#7b5cff" stroke-width="7" points="0,145 70,125 135,130 210,90 275,105 340,70 405,92 475,54 540,72 600,38"/><polyline fill="none" stroke="#4ca7ff" stroke-width="4" points="0,155 70,142 135,146 210,111 275,121 340,87 405,104 475,74 540,86 600,59"/></svg></div>
                </div>
                <div class="yh6-dash-services">
                    <div><span class="google"><?= icon('google',15) ?></span><b>Google Yorumları</b><small>5 yeni yorum</small></div>
                    <div><span class="instagram"><?= icon('instagram',15) ?></span><b>Instagram Beğeni</b><small>250 yeni beğeni</small></div>
                    <div><span class="tiktok"><?= icon('tiktok',15) ?></span><b>TikTok İzlenme</b><small>12.4K yeni izlenme</small></div>
                    <div><span class="youtube"><?= icon('youtube',15) ?></span><b>YouTube Yorum</b><small>18 yeni yorum</small></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($featuredPackageGroups)): ?>
<section class="yh18-featured" id="featured" data-featured-switcher>
    <div class="container">
        <?php
        $firstFeaturedGroup = $featuredPackageGroups[0];
        [$firstFeaturedClass,$firstFeaturedIcon,$firstFeaturedLabel] = yh6Platform(
            $firstFeaturedGroup['category']['slug'] ?? '',
            $firstFeaturedGroup['category']['name'] ?? ''
        );
        ?>
        <div class="yh18-featured-head">
            <div class="yh18-featured-title">
                <span class="yh18-featured-avatar <?= e($firstFeaturedClass) ?>" data-featured-avatar><?= icon($firstFeaturedIcon, 24) ?></span>
                <div>
                    <span class="yh6-eyebrow">Kategoriye Göre Popüler Paketler</span>
                    <h2><span data-featured-title><?= e($firstFeaturedGroup['category']['name']) ?></span> <span data-featured-kind>Öne Çıkan Paketler</span></h2>
                    <p data-featured-subtitle><?= e(($firstFeaturedGroup['category']['name'] ?? 'Hizmet') . ' kategorisindeki öne çıkan paketleri inceleyin.') ?></p>
                </div>
            </div>

            <nav class="yh24-featured-navigation" aria-label="Öne çıkan paket grupları">
                <div class="yh24-featured-groups" role="group" aria-label="Hizmet türünü seçin">
                    <?php foreach ($featuredNavGroups as $navGroup):
                        $groupOpen = ($navGroup['key'] === $initialFeaturedNavGroup);
                    ?>
                    <button type="button"
                            class="yh24-featured-group <?= $groupOpen?'is-open':'' ?>"
                            data-featured-group="<?= e($navGroup['key']) ?>"
                            aria-controls="yh24-filter-<?= e($navGroup['key']) ?>"
                            aria-expanded="<?= $groupOpen?'true':'false' ?>"
                            aria-label="<?= e($navGroup['title']) ?> alt kategorilerini göster">
                        <?= icon($navGroup['icon'], 17) ?>
                        <span><?= e($navGroup['title']) ?></span>
                        <?= icon('chevron-down', 13) ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </nav>
        </div>

        <div class="yh24-featured-subfilters" aria-label="Kategori filtreleri">
            <?php foreach ($featuredNavGroups as $navGroup):
                $groupOpen = ($navGroup['key'] === $initialFeaturedNavGroup);
            ?>
            <div class="yh24-featured-filter-panel nv43-featured-panel"
                 id="yh24-filter-<?= e($navGroup['key']) ?>"
                 data-featured-filter-panel="<?= e($navGroup['key']) ?>"
                 <?= $groupOpen?'':'hidden' ?>>
                <div class="nv43-rail-heading">
                    <span class="yh24-featured-filter-caption"><?= icon('sliders-horizontal', 14) ?> <?= e($navGroup['title']) ?> <small>ANA KATEGORİLER</small></span>
                    <span class="nv43-rail-instruction"><?= icon('mouse-pointer-2', 12) ?> Tutup kaydırabilir veya okları kullanabilirsiniz</span>
                </div>
                <div class="nv43-rail-shell">
                    <button type="button" class="nv43-rail-arrow" data-featured-rail-prev aria-label="Önceki ana kategoriler"><?= icon('chevron-left',18) ?></button>
                    <div class="yh24-featured-filter-list nv43-root-rail" data-featured-rail data-featured-rail-kind="root"
                         role="tablist" aria-label="<?= e($navGroup['title']) ?> ana kategoriler">
                        <?php foreach ($navGroup['categories'] as $filterCategory):
                            [$gcls,$gico,$glabel] = yh6Platform($filterCategory['slug']??'', $filterCategory['name']??'');
                            $isSoftware = (($filterCategory['kind'] ?? '') === 'software');
                            if ($isSoftware) {
                                $gcls = 'software';
                                $gico = $filterCategory['icon'] ?? 'monitor';
                            } elseif (!empty($filterCategory['icon'])) {
                                $gico = $filterCategory['icon'];
                            }
                            $selected = ((int)$filterCategory['id'] === (int)$firstFeaturedGroup['category']['id']);
                        ?>
                        <button type="button"
                                class="yh18-featured-tab yh24-featured-filter nv43-category-card <?= $selected?'active':'' ?> <?= e($gcls) ?>"
                                data-featured-tab="<?= (int)$filterCategory['id'] ?>"
                                data-featured-root-id="<?= (int)$filterCategory['id'] ?>"
                                data-featured-parent-group="<?= e($navGroup['key']) ?>"
                                data-title="<?= e($filterCategory['name']) ?>"
                                data-class="<?= e($gcls) ?>"
                                data-kind="<?= $isSoftware ? 'software' : 'package' ?>"
                                data-url="<?= e($filterCategory['url'] ?? '/kategori/'.$filterCategory['slug']) ?>"
                                aria-selected="<?= $selected?'true':'false' ?>"
                                title="<?= e($filterCategory['name']) ?>">
                            <span class="yh26-filter-icon"><?= icon($gico, 24) ?></span>
                            <span class="yh26-filter-name"><?= e($filterCategory['name']) ?></span>
                            <?php if(!empty($featuredChildCategories[(int)$filterCategory['id']])): ?>

                            <?php endif; ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="nv43-rail-arrow" data-featured-rail-next aria-label="Sonraki ana kategoriler"><?= icon('chevron-right',18) ?></button>
                </div>
                <?php foreach(($featuredChildCategories ?? []) as $parentId=>$children):
                    $hasParentInGroup=false;
                    foreach($navGroup['categories'] as $nc) {
                        if((int)$nc['id']===(int)$parentId){$hasParentInGroup=true;break;}
                        if((int)($nc['id']??0)===-100000 && (int)$parentId < -100000){$hasParentInGroup=true;break;}
                    }
                    if(!$hasParentInGroup || !$children)continue;
                    $showChildren=$groupOpen && (int)$parentId===(int)$firstFeaturedGroup['category']['id'];
                ?>
                <div class="nv43-child-panel" data-featured-children-for="<?= (int)$parentId ?>"
                     <?= $showChildren?'':'hidden' ?>>
                    <div class="nv43-rail-heading nv43-child-heading">
                        <span class="nv43-child-heading-mark" aria-hidden="true"><?= icon('corner-down-right',14) ?></span>
                        <a href="<?= e((int)$parentId===-100000?'/hazir-scriptler':'/kategoriler') ?>" class="nv43-rail-all">
                            Tümünü gör <?= icon('arrow-up-right',13) ?>
                        </a>
                    </div>
                    <div class="nv43-rail-shell nv43-child-shell">
                        <button type="button" class="nv43-rail-arrow" data-featured-rail-prev aria-label="Önceki alt kategoriler"><?= icon('chevron-left',16) ?></button>
                        <div class="nv43-child-rail" data-featured-rail data-featured-rail-kind="child"
                             role="tablist" aria-label="Alt kategoriler">
                        <?php foreach($children as $child):
                            $childType=$child['kind']??'package';
                            $childRoot=$childType==='software'?-100000:(int)($child['parent_featured_id']??0);
                            $childUrl=$child['url']??'/kategori/'.rawurlencode((string)$child['slug']);
                            $childIcon=$child['icon']??'folder';
                            if($childType==='link'): ?>
                            <a class="nv43-subcategory-link" href="<?= e($childUrl) ?>"
                               title="<?= e($child['name']) ?>" data-menu-link>
                                <?= icon($childIcon, 15) ?> <span><?= e($child['name']) ?></span>
                                <?= icon('arrow-up-right',12) ?>
                            </a>
                            <?php else: ?>
                            <button class="nv43-subcategory-link nv43-subcategory-tab"
                                    type="button" data-featured-tab="<?= (int)$child['id'] ?>"
                                    data-featured-root-id="<?= $childRoot ?>"
                                    data-featured-parent-id="<?= (int)$child['parent_featured_id'] ?>"
                                    data-featured-parent-group="<?= e($navGroup['key']) ?>"
                                    data-title="<?= e($child['name']) ?>"
                                    data-kind="<?= e($childType) ?>"
                                    data-class="<?= $childType==='software'?'software':'default' ?>"
                                    data-url="<?= e($childUrl) ?>"
                                    aria-selected="false"
                                    title="<?= e($child['name']) ?>">
                                <?= icon($childIcon, 15) ?> <span><?= e($child['name']) ?></span>
                            </button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </div>
                        <button type="button" class="nv43-rail-arrow" data-featured-rail-next aria-label="Sonraki alt kategoriler"><?= icon('chevron-right',16) ?></button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="yh18-featured-panes">
            <?php foreach($featuredPackageGroups as $gidx=>$group): ?>
            <div class="yh18-featured-pane <?= $gidx===0?'active':'' ?>"
                 data-featured-pane="<?= e((string)$group['category']['id']) ?>"
                 <?= $gidx===0?'':'hidden' ?>>
                <div class="yh18-featured-grid" data-featured-track role="region" aria-label="<?= e($group['category']['name']) ?> öne çıkan paketler">
                    <?php foreach($group['packages'] as $pidx=>$pkg): ?>
                        <?= yh18FeaturedCard($pkg, $pidx===1) ?>
                    <?php endforeach; ?>
                </div>
                <div class="yh18-carousel-row">
                    <div class="yh18-carousel-controls" aria-label="Paket kaydırma">
                        <button type="button" class="yh18-slide-arrow" data-slide-prev aria-label="Önceki paketler"><?= icon('chevron-left',18) ?></button>
                        <span data-slide-count aria-live="off">1 / <?= count($group['packages']) ?></span>
                        <button type="button" class="yh18-slide-arrow" data-slide-next aria-label="Sonraki paketler"><?= icon('chevron-right',18) ?></button>
                    </div>
                </div>
                <div class="yh18-featured-footer">
                    <a href="/kategori/<?= e($group['category']['slug']) ?>" class="yh18-all-link">
                        <?= e($group['category']['name']) ?> kategorisindeki tüm paketleri gör <?= icon('arrow-right', 11) ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

            <?php foreach(($featuredChildPackageGroups ?? []) as $childGroup): ?>
            <div class="yh18-featured-pane nv43-child-package-pane" hidden
                 data-featured-pane="<?= (int)$childGroup['category']['id'] ?>"
                 data-kind="package">
                <div class="yh18-featured-grid" data-featured-track role="region"
                     aria-label="<?= e($childGroup['category']['name']) ?> paketleri">
                    <?php foreach($childGroup['packages'] as $pidx=>$pkg): ?>
                        <?= yh18FeaturedCard($pkg,$pidx===0 && (int)($pkg['is_featured']??0)===1) ?>
                    <?php endforeach; ?>
                    <?php if(!$childGroup['packages']): ?>
                        <div class="nv43-empty-category">
                            <strong><?= e($childGroup['category']['name']) ?></strong>
                            <p>Bu kategoride henüz yayındaki paket bulunmuyor.</p>
                            <a href="<?= e($childGroup['category']['url']) ?>">Kategoriyi incele <?= icon('arrow-right',14) ?></a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="yh18-carousel-row">
                    <div class="yh18-carousel-controls">
                        <button type="button" class="yh18-slide-arrow" data-slide-prev aria-label="Önceki paketler"><?= icon('chevron-left',18) ?></button>
                        <span data-slide-count>1 / <?= count($childGroup['packages']) ?></span>
                        <button type="button" class="yh18-slide-arrow" data-slide-next aria-label="Sonraki paketler"><?= icon('chevron-right',18) ?></button>
                    </div>
                </div>
                <div class="yh18-featured-footer">
                    <a href="<?= e($childGroup['category']['url']) ?>" class="yh18-all-link">
                        <?= e($childGroup['category']['name']) ?> kategorisine git <?= icon('arrow-right',11) ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

            <?php foreach(($featuredSoftwareGroups ?? []) as $softwareGroup): ?>
            <div class="yh18-featured-pane yh41-software-pane"
                 data-featured-pane="<?= (int)$softwareGroup['category']['id'] ?>"
                 data-kind="software"
                 hidden>
                <div class="yh18-featured-grid yh41-software-grid" data-featured-track role="region"
                     aria-label="<?= e($softwareGroup['category']['name']) ?> hazır yazılımları">
                    <?php foreach($softwareGroup['products'] as $product): ?>
                        <?= yh41SoftwareCard($product) ?>
                    <?php endforeach; ?>
                    <?php if (!$softwareGroup['products']): ?>
                        <div class="yh41-software-empty">Bu kategoride henüz yayındaki bir yazılım bulunmuyor.
                            <a href="/hazir-scriptler">Tüm yazılımlara göz at <?= icon('arrow-right', 12) ?></a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="yh18-carousel-row">
                    <div class="yh18-carousel-controls" aria-label="Yazılımları kaydır">
                        <button type="button" class="yh18-slide-arrow" data-slide-prev aria-label="Önceki yazılımlar"><?= icon('chevron-left',18) ?></button>
                        <span data-slide-count aria-live="off">1 / <?= count($softwareGroup['products']) ?></span>
                        <button type="button" class="yh18-slide-arrow" data-slide-next aria-label="Sonraki yazılımlar"><?= icon('chevron-right',18) ?></button>
                    </div>
                </div>
                <div class="yh18-featured-footer">
                    <a href="<?= e($softwareGroup['category']['url']) ?>" class="yh18-all-link">
                        <?= e($softwareGroup['category']['name']) ?> kategorisindeki tüm yazılımları gör <?= icon('arrow-right', 11) ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="nv29-services" aria-labelledby="nv29-services-title">
    <div class="container">
        <div class="nv29-section-intro">
            <div>
                <span class="nv29-section-kicker"><?= icon('sparkles', 14) ?> HER İHTİYACA UYGUN DİJİTAL ÇÖZÜMLER</span>
                <h2 id="nv29-services-title">Tek platform, <span>üç güçlü uzmanlık alanı.</span></h2>
            </div>
            <a href="/kategoriler" class="nv29-view-all">Tüm Hizmetleri Keşfet <?= icon('arrow-up-right', 15) ?></a>
        </div>
        <div class="nv29-services-grid" aria-label="Dijital hizmet ana kategorileri">
        <?php foreach (($homePromoGroups ?? []) as $promo): ?>
            <article class="nv29-service-card nv29-<?= e($promo['key']) ?>" data-promo-group="<?= e($promo['key']) ?>">
                <div class="nv29-card-glow" aria-hidden="true"></div>
                <div class="nv29-card-content">
                    <span class="nv29-eyebrow"><?= icon($promo['key']==='social'?'heart':($promo['key']==='agency'?'layers':'trending-up'), 12) ?> <?= e($promo['eyebrow']) ?></span>
                    <h3><?= e($promo['title']) ?></h3>
                    <p><?= e($promo['description']) ?></p>
                    <a href="<?= e($promo['url']) ?>" class="nv29-card-cta">
                        <?= e($promo['cta']) ?> <?= icon('arrow-right', 15) ?>
                    </a>
                </div>

                <?php if ($promo['key'] === 'social'): ?>
                <div class="nv29-visual nv29-visual-social" aria-hidden="true">
                    <span class="nv29-visual-disc"></span>
                    <img src="<?= asset('img/hero-woman-cutout.png') ?>" class="nv29-social-person" alt="" loading="lazy" width="240" height="285">
                    <span class="nv29-social-float nv29-float-instagram"><?= icon('instagram', 22) ?></span>
                    <span class="nv29-social-float nv29-float-tiktok"><?= icon('tiktok', 20) ?></span>
                </div>
                <?php elseif ($promo['key'] === 'agency'): ?>
                <div class="nv29-visual nv29-visual-agency" aria-hidden="true">
                    <div class="nv29-agency-window">
                        <div class="nv29-window-top"><i></i><i></i><i></i><span></span></div>
                        <div class="nv29-window-body">
                            <div class="nv29-window-sidebar"><b></b><b></b><b></b><b></b></div>
                            <div class="nv29-window-main">
                                <span class="nv29-window-label"></span>
                                <div class="nv29-window-chart"><i></i><i></i><i></i><i></i><i></i></div>
                                <div class="nv29-window-tiles"><b></b><b></b><b></b></div>
                            </div>
                        </div>
                    </div>
                    <span class="nv29-agency-device"><span></span><b></b></span>
                    <span class="nv29-visual-mini nv29-agency-code"><?= icon('code', 17) ?></span>
                </div>
                <?php else: ?>
                <div class="nv29-visual nv29-visual-marketing" aria-hidden="true">
                    <div class="nv29-analytics-card">
                        <div class="nv29-analytics-top"><span class="nv29-analytics-dot"></span><span class="nv29-analytics-line"></span><?= icon('trending-up', 16) ?></div>
                        <svg class="nv29-analytics-graph" viewBox="0 0 210 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 84H200M10 57H200M10 30H200" stroke="currentColor" stroke-opacity=".14" stroke-dasharray="3 5"/>
                            <path d="M10 89L46 74L79 80L111 46L141 57L175 26L199 16" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="199" cy="16" r="6" fill="white" stroke="currentColor" stroke-width="3"/>
                        </svg>
                        <div class="nv29-analytics-bars"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
                    </div>
                    <span class="nv29-visual-mini nv29-marketing-search"><?= icon('search', 21) ?></span>
                    <span class="nv29-visual-mini nv29-marketing-target"><?= icon('trending-up', 19) ?></span>
                </div>
                <?php endif; ?>

                <div class="nv29-service-chips" aria-label="<?= e($promo['title']) ?> alt kategorileri">
                    <?php foreach ($promo['chips'] as $chip): ?>
                    <a href="<?= e($chip['url']) ?>" title="<?= e($chip['name']) ?>" class="nv29-service-chip">
                        <?= icon($chip['icon'], 12) ?> <span><?= e($chip['name']) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="nv31-software" id="hazir-yazilimlar" aria-labelledby="nv31-software-heading">
  <div class="container">
    <div class="nv31-section-heading">
      <div>
        <span class="nv31-kicker"><?= icon('monitor',14) ?> HAZIR YAZILIM KATALOĞU</span>
        <h2 id="nv31-software-heading">Hazır Yazılımlar <span>& Scriptler</span></h2>
        <p>Yönetim panelli, sektöre özel dijital çözümlerimizi keşfedin. İhtiyacınıza uygun ürünü inceleyin.</p>
      </div>
      <a href="<?= e($softwareCatalogUrl) ?>" class="nv31-heading-link">Tüm Yazılımları Gör <?= icon('arrow-up-right',15) ?></a>
    </div>
    <?php if (!empty($softwareHighlights)): ?>
    <div class="nv31-software-grid">
      <?php foreach($softwareHighlights as $i=>$sw):
        $price = (float)($sw['price'] ?? 0);
        $oldPrice = (float)($sw['old_price'] ?? 0);
        $short = trim(strip_tags((string)($sw['short_desc'] ?: $sw['description'] ?? '')));
        $productUrl = '/hazir-scriptler/'.rawurlencode((string)$sw['slug']);
        $cover = (string)($sw['cover_image']??'');
      ?>
      <article class="nv31-software-card">
        <a href="<?= e($productUrl) ?>" class="nv31-software-image" aria-label="<?= e($sw['name']) ?> yazılımını incele">
          <?php if($cover!==''): ?>
            <img src="<?= e(upload_url($cover)) ?>" loading="lazy" alt="<?= e($sw['name']) ?>">
          <?php else: ?>
            <span class="nv31-software-image-placeholder">
              <span><?= icon('monitor',45) ?></span>
              <span><?= icon('settings',19) ?></span>
              <span><?= icon('globe',25) ?></span>
            </span>
          <?php endif; ?>
          <span class="nv31-software-number"><?= str_pad((string)($i+1),2,'0',STR_PAD_LEFT) ?></span>
          <span class="nv31-software-image-arrow"><?= icon('arrow-up-right',17) ?></span>
        </a>
        <div class="nv31-software-body">
          <span class="nv31-software-tag"><?= icon('layers',11) ?> <?= e($sw['category_name'] ?: 'Hazır Scriptler') ?></span>
          <h3><a href="<?= e($productUrl) ?>"><?= e($sw['name']) ?></a></h3>
          <p><?= e(mb_strimwidth($short, 0, 115, '…','UTF-8')) ?></p>
          <div class="nv31-software-bottom">
            <strong><?= money($price) ?></strong>
            <a href="<?= e($productUrl) ?>" aria-label="<?= e($sw['name']) ?> detayları"><?= icon('arrow-right',16) ?></a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="nv32-software-intro">
      <div class="nv32-intro-main">
        <span class="nv32-intro-kicker"><?= icon('layers',15) ?> SEKTÖRÜNÜZE UYGUN DİJİTAL ALTYAPI</span>
        <h3>İşinize uygun yazılımı <span>birlikte seçelim.</span></h3>
        <p>Haber portalı, e-ticaret, emlak, otel ve kurumsal web çözümlerini tek bir çatı altında keşfedin. Yazılım ürünleri yayına alındığında burada sıralı olarak listelenecek.</p>
        <a class="nv32-primary-cta" href="<?= e($softwareCatalogUrl) ?>">Yazılım Kategorilerini Keşfet <?= icon('arrow-right',16) ?></a>
        <?php if (!empty($softwarePreviewCategories)): ?>
        <div class="nv32-service-list" aria-label="Hazır yazılım kategorileri">
          <?php foreach ($softwarePreviewCategories as $softwareCategory): ?>
          <a href="/hazir-yazilimlar?tur=<?= rawurlencode($softwareCategory['slug']) ?>">
            <?= icon($softwareCategory['icon_key'] ?: 'monitor',14) ?> <?= e($softwareCategory['name']) ?> <?= icon('arrow-up-right',12) ?>
          </a>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="nv32-feature-pills" aria-label="Çözüm alanları">
          <span><?= icon('monitor',14) ?> Kurumsal web yazılımları</span>
          <span><?= icon('store',14) ?> E-ticaret çözümleri</span>
          <span><?= icon('settings',14) ?> Yönetim panelli sistemler</span>
        </div>
        <?php endif; ?>
      </div>
      <div class="nv32-intro-visual" aria-hidden="true">
        <div class="nv32-visual-halo"></div>
        <div class="nv32-screen">
          <div class="nv32-screen-bar"><i></i><i></i><i></i><span></span></div>
          <div class="nv32-screen-body">
            <aside><i></i><i></i><i></i><i></i></aside>
            <main>
              <span class="nv32-screen-line"></span>
              <div class="nv32-screen-columns"><span></span><span></span><span></span></div>
              <div class="nv32-screen-bars"><b></b><b></b><b></b><b></b><b></b></div>
            </main>
          </div>
        </div>
        <span class="nv32-visual-mini nv32-mini-a"><?= icon('settings',18) ?></span>
        <span class="nv32-visual-mini nv32-mini-b"><?= icon('globe',20) ?></span>
      </div>
    </div>
    <?php endif; ?>
    <div class="nv31-software-foot">
      <span><?= icon('shield-check',14) ?> Sektöre uygun yazılım çözümleri</span>
      <span><?= icon('headphones',14) ?> Satış öncesi ve sonrası destek</span>
      <a href="<?= e($softwareCatalogUrl) ?>">Tüm yazılım kategorileri <?= icon('arrow-right',12) ?></a>
    </div>
  </div>
</section>

<section class="yh6-stats">
    <div class="container">
        <div><?= icon('users',20) ?><span><strong>50.000+</strong><small>Mutlu Müşteri</small></span></div>
        <div><?= icon('shopping-cart',20) ?><span><strong>250.000+</strong><small>Tamamlanan Sipariş</small></span></div>
        <div><?= icon('star-fill',20) ?><span><strong>4.9/5</strong><small>Müşteri Memnuniyeti</small></span></div>
        <div><?= icon('headphones',20) ?><span><strong>7/24</strong><small>Canlı Destek</small></span></div>
        <div><?= icon('trending-up',20) ?><span><strong>%98</strong><small>Başarılı Teslimat Oranı</small></span></div>
    </div>
</section>

<?php if (!empty($reviews)): ?>
<section class="nv30-feedback" id="nv30-feedback" aria-labelledby="nv30-feedback-title">
    <div class="container">
        <div class="nv30-section-head">
            <div class="nv30-head-copy">
                <span class="nv30-section-kicker"><?= icon('message-circle', 14) ?> MÜŞTERİ DENEYİMLERİ</span>
                <h2 id="nv30-feedback-title">Bizimle çalışanlar <span>neler söylüyor?</span></h2>
                <p>Hizmetlerimiz hakkında müşterilerimizin paylaştığı deneyimlere göz atın.</p>
            </div>
            <a class="nv30-outline-link" href="/kategoriler">Hizmetleri Keşfet <?= icon('arrow-up-right', 14) ?></a>
        </div>
        <div class="nv30-review-grid" aria-label="Müşteri yorumları">
            <?php foreach(array_slice($reviews, 0, 4) as $index=>$review):
                $reviewName = trim((string)($review['name'] ?? 'Müşteri'));
                $reviewInitial = mb_strtoupper(mb_substr($reviewName !== '' ? $reviewName : 'M', 0, 1, 'UTF-8'), 'UTF-8');
                $reviewRole = trim((string)($review['role'] ?? ''));
                $reviewText = trim((string)($review['text'] ?? ''));
                $reviewRating = isset($review['rating']) && is_numeric($review['rating'])
                    ? max(0, min(5, (int)$review['rating'])) : null;
                if ($reviewText === '') continue;
            ?>
            <article class="nv30-review-card <?= $index === 0 ? 'nv30-review-primary' : '' ?>">
                <div class="nv30-review-topline">
                    <span class="nv30-review-quote" aria-hidden="true"><?= icon('message-circle', 23) ?></span>
                    <?php if ($reviewRating !== null && $reviewRating > 0): ?>
                        <span class="nv30-review-stars" aria-label="<?= (int)$reviewRating ?> üzerinden 5 yıldız">
                            <?php for($r=0;$r<$reviewRating;$r++): ?><?= icon('star-fill', 12) ?><?php endfor; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <blockquote><?= e($reviewText) ?></blockquote>
                <div class="nv30-review-author">
                    <span class="nv30-review-avatar" aria-hidden="true"><?= e($reviewInitial) ?></span>
                    <div class="nv30-review-author-info">
                        <strong><?= e($reviewName !== '' ? $reviewName : 'Müşteri') ?></strong>
                        <?php if ($reviewRole !== ''): ?><small><?= e($reviewRole) ?></small><?php else: ?><small>Müşteri yorumu</small><?php endif; ?>
                    </div>
                    <span class="nv30-review-decoration" aria-hidden="true"><?= icon('message-circle', 16) ?></span>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="nv30-feedback-foot">
            <span><?= icon('shield-check', 15) ?> Açık ve anlaşılır sipariş takibi</span>
            <span><?= icon('headphones', 15) ?> Destek merkezine kolay erişim</span>
            <a href="/kategoriler">İhtiyacınıza uygun hizmeti bulun <?= icon('arrow-right', 13) ?></a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="nv31-portfolio" id="referanslarimiz" aria-labelledby="nv31-portfolio-heading">
  <div class="container">
    <div class="nv31-section-heading">
      <div>
        <span class="nv31-kicker"><?= icon('award',14) ?> PROJE PORTFÖYÜMÜZ</span>
        <h2 id="nv31-portfolio-heading">Referanslarımız <span>& Çalışmalarımız</span></h2>
        <p><?= !empty($projectReferences) ? 'Hayata geçirdiğimiz projelerden örnekler. Sektörlere özel dijital tasarım ve yazılım çözümleri.' : 'Dijital projelerimizin portföyünü bu alanda paylaşacağız. Size özel proje fikirlerini birlikte şekillendirelim.' ?></p>
      </div>
      <a href="/iletisim" class="nv31-heading-link">Projenizi Konuşalım <?= icon('arrow-up-right',15) ?></a>
    </div>
    <?php if (!empty($projectReferences)): ?>
    <div class="nv31-portfolio-grid">
      <?php foreach($projectReferences as $ref): ?>
      <article class="nv31-portfolio-card">
        <div class="nv31-portfolio-image">
          <?php if(!empty($ref['image'])): ?>
            <img src="<?= e(upload_url($ref['image'])) ?>" alt="<?= e($ref['title'].' proje görseli') ?>" loading="lazy">
          <?php else: ?>
            <div class="nv31-portfolio-placeholder"><?= icon('monitor',45) ?></div>
          <?php endif; ?>
          <?php if(!empty($ref['url'])): ?>
          <a class="nv31-portfolio-arrow" href="<?= e($ref['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($ref['title']) ?> projesini ziyaret et"><?= icon('arrow-up-right',16) ?></a>
          <?php endif; ?>
        </div>
        <div class="nv31-portfolio-body">
          <span class="nv31-portfolio-kind"><?= icon('layers',11) ?> <?= e($ref['category'] ?: 'Dijital proje') ?></span>
          <h3><?= e($ref['title']) ?></h3>
          <?php if(!empty($ref['description'])): ?><p><?= e($ref['description']) ?></p><?php endif; ?>
          <?php if(!empty($ref['url'])): ?>
            <a href="<?= e($ref['url']) ?>" target="_blank" rel="noopener noreferrer">Projeyi İncele <?= icon('arrow-right',12) ?></a>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="nv32-portfolio-intro">
      <div class="nv32-portfolio-art" aria-hidden="true">
        <span class="nv32-portfolio-orb nv32-orb-one"><?= icon('monitor',31) ?></span>
        <span class="nv32-portfolio-orb nv32-orb-two"><?= icon('palette',23) ?></span>
        <span class="nv32-portfolio-orb nv32-orb-three"><?= icon('layers',25) ?></span>
        <div class="nv32-portfolio-frames"><div><span></span><span></span><span></span></div><div><span></span><span></span><span></span></div></div>
      </div>
      <div class="nv32-portfolio-copy">
        <span class="nv32-intro-kicker"><?= icon('award',14) ?> YENİ PROJELERE AÇIĞIZ</span>
        <h3>Bir sonraki dijital projeyi <span>birlikte hayata geçirelim.</span></h3>
        <p>Web tasarım, özel yazılım ve dijital büyüme alanlarında işletmenizin ihtiyaçlarını konuşalım. Yayınlanan müşteri çalışmalarını daha sonra bu bölümde görüntüleyebilirsiniz.</p>
        <a class="nv32-primary-cta" href="/iletisim">Projenizi Anlatın <?= icon('arrow-right',15) ?></a>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="nv30-process" id="how" aria-labelledby="nv30-process-title">
    <div class="container">
        <div class="nv30-section-head nv30-process-head">
            <div class="nv30-head-copy">
                <span class="nv30-section-kicker"><?= icon('zap', 14) ?> 4 BASİT ADIM</span>
                <h2 id="nv30-process-title">Nasıl <span>çalışır?</span></h2>
                <p>Hizmet seçiminizden sipariş takibine kadar süreç tek bir panelde ilerler.</p>
            </div>
            <div class="nv30-process-head-mark"><?= icon('shield-check', 17) ?> Kolay ve güvenli işlem</div>
        </div>

        <div class="nv30-process-grid" aria-label="Hizmet alma aşamaları">
            <article class="nv30-step nv30-step-first">
                <div class="nv30-step-top"><span class="nv30-step-icon"><?= icon('search', 22) ?></span><span class="nv30-step-number">01</span></div>
                <div class="nv30-step-content"><h3>Hizmetinizi Seçin</h3><p>İhtiyacınıza uygun kategoriyi ve hizmet paketini keşfedin.</p></div>
                <div class="nv30-step-bottom"><?= icon('check-circle', 13) ?> Hizmet keşfi</div>
            </article>
            <article class="nv30-step">
                <div class="nv30-step-top"><span class="nv30-step-icon"><?= icon('credit-card', 22) ?></span><span class="nv30-step-number">02</span></div>
                <div class="nv30-step-content"><h3>Güvenle Ödeyin</h3><p>Ödeme seçeneklerinden size uygun olanı seçerek siparişinizi oluşturun.</p></div>
                <div class="nv30-step-bottom"><?= icon('shield-check', 13) ?> Güvenli işlem</div>
            </article>
            <article class="nv30-step">
                <div class="nv30-step-top"><span class="nv30-step-icon"><?= icon('zap', 22) ?></span><span class="nv30-step-number">03</span></div>
                <div class="nv30-step-content"><h3>Siparişiniz İşleme Alınsın</h3><p>Hizmetinizin durumunu hesabınız üzerinden kolayca takip edin.</p></div>
                <div class="nv30-step-bottom"><?= icon('clock', 13) ?> Sipariş takibi</div>
            </article>
            <article class="nv30-step">
                <div class="nv30-step-top"><span class="nv30-step-icon"><?= icon('check-circle', 22) ?></span><span class="nv30-step-number">04</span></div>
                <div class="nv30-step-content"><h3>Sonuçları Görün</h3><p>Hizmet tamamlandığında sipariş ayrıntılarını panelinizden inceleyin.</p></div>
                <div class="nv30-step-bottom"><?= icon('eye', 13) ?> Sonuçlarınız</div>
            </article>
        </div>

        <div class="nv30-process-cta">
            <div class="nv30-process-cta-copy">
                <span class="nv30-process-cta-icon"><?= icon('sparkles', 21) ?></span>
                <div><strong>Bir sonraki adımda markanız için doğru hizmeti seçin.</strong><small>Tüm kategorileri tek noktadan inceleyebilirsiniz.</small></div>
            </div>
            <div class="nv30-process-actions">
                <a class="nv30-process-primary-link" href="/kategoriler">Hizmetleri İncele <?= icon('arrow-right', 14) ?></a>
                <a class="nv30-process-secondary-link" href="/sss">Sık Sorulan Sorular <?= icon('arrow-up-right', 13) ?></a>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($latestPosts)): ?>
<section class="yh6-blog">
    <div class="container">
        <div class="yh6-section-head"><div><span class="yh6-eyebrow">Bilgi Merkezi</span><h2>Son Blog Yazıları</h2><p>Sosyal medya ve dijital pazarlama hakkında en güncel içerikler.</p></div><a href="/blog" class="yh6-link">Tüm Yazılar <?= icon('arrow-right',10) ?></a></div>
        <div class="yh6-blog-grid">
            <?php foreach(array_slice($latestPosts,0,4) as $post): [$postCls,$postIco,$postLabel]=yh6Platform($post['category_slug']??'', $post['category_name']??$post['title']); ?>
            <a href="/blog/<?= e($post['slug']) ?>" class="yh6-blog-card">
                <div class="yh6-blog-image <?= e($postCls) ?>"><img src="<?= e(!empty($post['image']) ? upload_url($post['image']) : demo_visual_url($post['title'].' '.($post['category_name']??''),'blog')) ?>" alt="<?= e($post['image_alt']??$post['title']) ?>"><span class="yh6-blog-image-badge"><?= icon($postIco,15) ?></span></div>
                <div><small><?= e($post['category_name']??'Rehber') ?></small><h3><?= e($post['title']) ?></h3><p><?= e(excerpt(strip_tags($post['excerpt']??$post['content']??''),95)) ?></p><em><?= !empty($post['published_at'])?formatDate($post['published_at'],'d M Y'):'Güncel' ?> · 5 dk okuma</em></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($faqs)): ?>
<section class="yh6-faq" aria-labelledby="home-faq-title">
    <div class="container">
        <div class="yh6-section-head">
            <div><span class="yh6-eyebrow">Merak Edilenler</span><h2 id="home-faq-title">Sıkça Sorulan Sorular</h2></div>
        </div>
        <div class="yh6-faq-grid">
            <?php foreach (array_slice($faqs, 0, 8) as $faqIndex => $faq): ?>
            <div class="yh6-faq-item">
                <button type="button" data-faq-trigger aria-expanded="false" aria-controls="home-faq-answer-<?= (int)$faqIndex ?>">
                    <span><?= e($faq['question']) ?></span>
                    <span class="yh6-faq-toggle-icon" aria-hidden="true"><?= icon('plus', 16) ?></span>
                </button>
                <div class="yh6-faq-answer" id="home-faq-answer-<?= (int)$faqIndex ?>" hidden><?= nl2br(e($faq['answer'])) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="yh6-final" aria-labelledby="home-final-title">
    <div class="container">
        <div class="yh6-final-copy">
            <span class="yh6-eyebrow">Daha Güçlü Bir Marka İçin</span>
            <h2 id="home-final-title">YorumHizmeti.tr ile Dijital Varlığınızı Güçlendirin</h2>
            <p>Sosyal medya etkileşim hizmetlerinden Google yorumlarına, web sitesi çözümlerinden SEO hizmetlerine kadar ihtiyacınız olan dijital hizmetler tek platformda.</p>
        </div>
        <div class="yh6-final-actions">
            <a href="/kategoriler" class="yh6-btn primary">Hizmetleri Keşfet <?= icon('arrow-right', 16) ?></a>
            <div class="yh6-final-guarantees">
                <span><?= icon('shield', 14) ?> Güvenli Ödeme</span>
                <span><?= icon('headphones', 14) ?> 7/24 Destek</span>
            </div>
        </div>
    </div>
</section>

</main>

<script>
document.querySelectorAll('.yh6-faq-item [data-faq-trigger]').forEach((button) => {
    button.addEventListener('click', () => {
        const item = button.closest('.yh6-faq-item');
        const answer = document.getElementById(button.getAttribute('aria-controls'));
        if (!item || !answer) return;
        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', String(!isExpanded));
        answer.hidden = isExpanded;
        item.classList.toggle('open', !isExpanded);
    });
});
</script>