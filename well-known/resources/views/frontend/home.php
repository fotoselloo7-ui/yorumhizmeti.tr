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
                <strong><?= money($price) ?></strong>
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
                    <h2><span data-featured-title><?= e($firstFeaturedGroup['category']['name']) ?></span> Öne Çıkan Paketler</h2>
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
            <div class="yh24-featured-filter-panel"
                 id="yh24-filter-<?= e($navGroup['key']) ?>"
                 data-featured-filter-panel="<?= e($navGroup['key']) ?>"
                 <?= $groupOpen?'':'hidden' ?>>
                <span class="yh24-featured-filter-caption"><?= icon('sliders-horizontal', 14) ?> <?= e($navGroup['title']) ?></span>
                <div class="yh24-featured-filter-list" role="tablist" aria-label="<?= e($navGroup['title']) ?> alt kategorileri">
                    <?php foreach ($navGroup['categories'] as $filterCategory):
                        [$gcls,$gico,$glabel] = yh6Platform($filterCategory['slug']??'', $filterCategory['name']??'');
                        $selected = ((int)$filterCategory['id'] === (int)$firstFeaturedGroup['category']['id']);
                    ?>
                    <button type="button"
                            class="yh18-featured-tab yh24-featured-filter <?= $selected?'active':'' ?> <?= e($gcls) ?>"
                            data-featured-tab="<?= (int)$filterCategory['id'] ?>"
                            data-featured-parent-group="<?= e($navGroup['key']) ?>"
                            data-title="<?= e($filterCategory['name']) ?>"
                            data-class="<?= e($gcls) ?>"
                            data-url="/kategori/<?= e($filterCategory['slug']) ?>"
                            aria-selected="<?= $selected?'true':'false' ?>"
                            title="<?= e($filterCategory['name']) ?>">
                        <span class="yh26-filter-icon"><?= icon($gico, 24) ?></span>
                        <span class="yh26-filter-name"><?= e($filterCategory['name']) ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
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
<section class="yh6-faq">
    <div class="container">
        <div class="yh6-section-head"><div><span class="yh6-eyebrow">Merak Edilenler</span><h2>Sıkça Sorulan Sorular</h2></div></div>
        <div class="yh6-faq-grid">
            <?php foreach(array_slice($faqs,0,8) as $faq): ?>
            <div class="yh6-faq-item"><button type="button"><span><?= e($faq['question']) ?></span><?= icon('plus',11) ?></button><div><?= nl2br(e($faq['answer'])) ?></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="yh6-final">
    <div class="container">
        <div>
            <span class="yh6-eyebrow">Daha Güçlü Bir Marka İçin</span>
            <h2>YorumHizmeti.tr ile Dijital Varlığınızı Güçlendirin</h2>
            <p>Sosyal medya etkileşim hizmetlerinden Google yorumlarına, web site çözümlerinden SEO hizmetlerine kadar ihtiyaç duyduğunuz tüm dijital çözümler tek platformda.</p>
        </div>
        <a href="/kategoriler" class="yh6-btn primary">Hemen Başlayın <?= icon('arrow-right',11) ?></a>
        <div class="yh6-avatar-proof"><span>A</span><span>E</span><span>M</span><div><strong>+50.000</strong><small>Mutlu Müşteri</small></div></div>
    </div>
</section>

</main>

<script>
document.querySelectorAll('.yh6-faq-item button').forEach(btn=>{
    btn.addEventListener('click',()=>{
        const item=btn.parentElement;
        item.classList.toggle('open');
        const body=btn.nextElementSibling;
        body.style.display=body.style.display==='block'?'none':'block';
    });
});
</script>