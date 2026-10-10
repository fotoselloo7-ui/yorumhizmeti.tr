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

/**
 * Choose a concrete, locally supported icon from the visible service category.
 * More specific child services take precedence over parent/platform branding.
 */
if (!function_exists('yh50MarqueeIcon')) {
    function yh50MarqueeIcon(string $name, string $style = '', string $configured = ''): string {
        $term = mb_strtolower($name, 'UTF-8');
        $rules = [
            ['/qr|karekod/u', 'grid'],
            ['/harita|konum|lokasyon|maps?/u', 'map-pin'],
            ['/yorum|şikayet|sikayet|mesaj|değerlendirme|degerlendirme/u', 'message-circle'],
            ['/takipçi|takipci|abone|follower/u', 'users'],
            ['/beğeni|begeni|like/u', 'heart'],
            ['/izlenme|görüntülenme|goruntulenme|view/u', 'eye'],
            ['/puan|yıldız|yildiz|rating/u', 'star-fill'],
            ['/otomasyon|robot|bot/u', 'robot'],
            ['/analiz|istatistik|rapor|performans/u', 'bar-chart'],
            ['/reklam|ads|kampanya/u', 'ads'],
            ['/backlink|bağlantı|baglanti/u', 'link'],
            ['/profil|işletme|isletme|mağaza|magaza/u', 'store'],
            ['/grafik|tasarım|tasarim|logo/u', 'palette'],
            ['/içerik|icerik|blog|makale|metin/u', 'content-create'],
            ['/mobil|uygulama|android|ios/u', 'mobile-app'],
            ['/e.?ticaret|eticaret|ecommerce/u', 'store'],
            ['/sektörel|sektorel|yönetim yazılım|yonetim yazilim/u', 'monitor'],
            ['/masaüstü|masaustu|desktop/u', 'monitor'],
            ['/hazır yazılım|hazir yazilim|script|yazılım|yazilim|software/u', 'code'],
            ['/web|site|wordpress/u', 'globe'],
            ['/seo|arama motor/u', 'search'],
        ];
        foreach ($rules as [$pattern, $iconName]) {
            if (preg_match($pattern, $term) && \App\Services\IconService::has($iconName)) {
                return $iconName;
            }
        }
        $byStyle = [
            'instagram'=>'instagram', 'tiktok'=>'tiktok', 'youtube'=>'youtube',
            'facebook'=>'facebook', 'google'=>'google', 'seo'=>'search',
            'twitter'=>'twitter', 'threads'=>'threads', 'telegram'=>'telegram',
            'spotify'=>'spotify', 'discord'=>'discord', 'linkedin'=>'linkedin',
            'twitch'=>'twitch', 'pinterest'=>'pinterest', 'snapchat'=>'snapchat',
            'soundcloud'=>'soundcloud', 'software'=>'monitor', 'web'=>'globe',
            'ecommerce'=>'store', 'mobileapp'=>'mobile-app', 'content'=>'content-create',
            'graphic'=>'palette', 'ads'=>'ads', 'local'=>'map-pin',
            'reputation'=>'shield-check',
        ];
        $suggested = $byStyle[$style] ?? '';
        if ($suggested !== '' && \App\Services\IconService::has($suggested)) {
            return $suggested;
        }
        return \App\Services\IconService::has($configured) ? $configured : 'package';
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
                <span class="yh6-package-icon <?= e($cls) ?> <?= e(\App\Services\SocialPlatformIdentity::classFor(\App\Services\SocialPlatformIdentity::fromText(($pkg['category_slug']??'').' '.($pkg['name']??'')))) ?>"><?= icon($ico, 22) ?></span>
                <span class="yh6-package-badge"><?= e($pkg['badge'] ?: (!empty($pkg['is_featured']) ? 'En Popüler' : $label)) ?></span>
            </div>
            <h3><?= e(package_display_name($pkg)) ?></h3>
            <ul>
                <li><?= icon('check-circle', 11) ?> Güvenli ve hızlı işlem</li>
                <li><?= icon('check-circle', 11) ?> Şifresiz sipariş süreci</li>
                <li><?= icon('check-circle', 11) ?> 7/24 destek</li>
            </ul>
            <div class="yh6-rating"><span><?= icon('check-circle',15) ?></span><small>Hizmet ayrıntıları</small></div>
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
        <article class="yh18-featured-card <?= e($cls) ?> <?= e(\App\Services\SocialPlatformIdentity::classFor(\App\Services\SocialPlatformIdentity::fromText(($pkg['category_slug']??'').' '.($pkg['category_name']??'').' '.($pkg['name']??'')))) ?>">
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

            <?php $benefits=\App\Services\PackageHighlightsService::get((int)$pkg['id'],$pkg); $benefitPages=array_chunk($benefits,4); ?>
            <div class="yh18-card-features yh67-card-benefits" data-benefit-carousel aria-label="Paket özellikleri">
                <div class="yh67-benefit-pages" aria-live="polite">
                  <?php foreach($benefitPages as $pageIndex=>$page): ?>
                  <div class="yh67-benefit-page" data-benefit-page="<?= $pageIndex ?>" <?= $pageIndex!==0?'hidden':'' ?>>
                    <?php foreach($page as $benefit): ?><span><?= icon('check-circle',13) ?><b><?= e($benefit) ?></b></span><?php endforeach; ?>
                  </div>
                  <?php endforeach; ?>
                </div>
                <?php if(count($benefitPages)>1): ?>
                <div class="yh67-benefit-nav">
                  <button type="button" class="yh67-benefit-arrow" data-benefit-prev aria-label="Önceki dört özelliği göster"><?= icon('chevron-left',14) ?></button>
                  <small data-benefit-counter>1 / <?= count($benefitPages) ?></small>
                  <button type="button" class="yh67-benefit-arrow" data-benefit-next aria-label="Sonraki dört özelliği göster"><?= icon('chevron-right',14) ?></button>
                </div>
                <?php endif; ?>
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

$reviews = ($testimonials && !empty($testimonials['extra'])) ? \App\Services\TestimonialManager::rows(true) : [];
?>

<main class="yh-home-v6">

<section class="yh6-hero">
    <div class="container">
        <div class="yh6-hero-grid">
            <div class="yh6-hero-copy">
                <span class="yh6-eyebrow"><?= icon('award', 12) ?> NetVera Teknoloji Yazılım</span>
                <h1 class="nv75-hero-heading"><span class="nv75-hero-line">Yazılım,</span><span class="nv75-hero-line nv75-hero-middle">Dijital Ajans ve</span><span class="nv75-hero-line nv75-hero-highlight">Sosyal Medya Hizmetleri</span></h1>
                <p>Hazır ve özel yazılım çözümleri, SEO, dijital reklam yönetimi ve Instagram, TikTok, YouTube hizmetleri NetVera Teknoloji Yazılım çatısı altında.</p>
                <div class="yh6-hero-actions">
                    <a href="/kategoriler" class="yh6-btn primary">Hemen İncele <?= icon('arrow-right', 12) ?></a>
                    <a href="#how" class="yh6-btn ghost"><?= icon('play-circle', 14) ?> Nasıl Çalışır?</a>
                </div>
                <div class="yh6-hero-metrics">
                    <div><?= icon('code', 16) ?><span><strong>Yazılım</strong><small>Sektörel Çözümler</small></span></div>
                    <div><?= icon('layers', 16) ?><span><strong>Dijital Ajans</strong><small>SEO ve Reklam</small></span></div>
                    <div><?= icon('share-2', 16) ?><span><strong>Sosyal Medya</strong><small>Takipçi ve Etkileşim</small></span></div>
                    <div><?= icon('package', 16) ?><span><strong>Hizmet Paketleri</strong><small>Online Sipariş</small></span></div>
                </div>
            </div>

            <div class="yh6-hero-art">
                <div class="yh6-hero-blob"></div>
                <img class="yh6-hero-person" src="<?= e($heroCutout) ?>" alt="NetVera dijital hizmetler" loading="eager" fetchpriority="high">

                <span class="yh6-social-float instagram"><?= icon('instagram', 27) ?></span>
                <span class="yh6-social-float tiktok"><?= icon('tiktok', 24) ?></span>
                <span class="yh6-social-float google"><?= icon('google', 25) ?></span>
                <span class="yh6-social-float youtube"><?= icon('youtube', 24) ?></span>

                <div class="yh6-rating-float"><span>NETVERA</span></div>
                <div class="yh6-review-float">
                    <?= icon('google', 19) ?>
                    <div><strong>Dijital Çözümler</strong><small>SEO, yazılım ve sosyal medya</small></div>
                </div>
                <div class="yh6-growth-float">
                    <strong>NetVera</strong><small>Teknoloji ve Yazılım</small>
                    <i></i><i></i><i></i><i></i><i></i>
                </div>
            </div>
        </div>

        <?php
        // New category rails reuse the same live catalog as the main navigation.
        $yh49Roots = [];
        $yh49Children = [];
        $yh49Seen = [];
        foreach (\App\Services\CatalogMenuService::groups() as $catalogGroup) {
            foreach ($catalogGroup['categories'] as $cat) {
                if (empty($cat['url'])) continue;
                if (!isset($yh49Seen[$cat['url']])) {
                    $yh49Roots[] = [
                        'name' => (string)$cat['name'], 'detail' => (string)$catalogGroup['short'],
                        'url' => (string)$cat['url'], 'icon' => (string)$cat['icon'],
                        'style' => (string)$cat['style']
                    ];
                    $yh49Seen[$cat['url']] = true;
                }
                foreach (array_slice($cat['children'] ?? [], 0, 3) as $child) {
                    if (empty($child['url'])) continue;
                    $yh49Children[] = [
                        'name' => (string)$child['name'], 'detail' => (string)$cat['name'],
                        'url' => (string)$child['url'], 'icon' => (string)$child['icon'],
                        'style' => (string)$cat['style']
                    ];
                }
            }
        }
        if (!empty($featuredSoftwareGroups)) {
            $yh49Roots[] = [
                'name' => 'Hazır Yazılımlar & Scriptler', 'detail' => 'Ajans & Yazılım',
                'url' => '/hazir-scriptler', 'icon' => 'monitor', 'style' => 'software'
            ];
            foreach (\App\Services\NetveraBridgeService::categories() as $scriptCat) {
                if (empty($scriptCat['slug'])) continue;
                $yh49Children[] = [
                    'name' => (string)$scriptCat['name'], 'detail' => 'Hazır Yazılımlar',
                    'url' => '/hazir-scriptler?category='.rawurlencode((string)$scriptCat['slug']),
                    'icon' => 'code', 'style' => 'software'
                ];
            }
        }
        ?>
        <?php if ($yh49Roots): ?>
        <div class="yh49-category-marquee" aria-label="Ana ve alt hizmet kategorileri">
            <?php foreach (['main' => $yh49Roots, 'child' => $yh49Children] as $yh49Row => $yh49Items):
                if (!$yh49Items) continue; ?>
            <div class="yh49-marquee-row yh56-grabbable <?= $yh49Row === 'main' ? 'yh49-marquee-primary' : 'yh49-marquee-secondary' ?>" aria-label="Kategorileri sürükleyerek keşfedin">
                <div class="yh49-marquee-track">
                    <?php for ($yh49Repeat = 0; $yh49Repeat < 2; $yh49Repeat++): ?>
                    <div class="yh49-marquee-set" <?= $yh49Repeat ? 'aria-hidden="true"' : '' ?>>
                        <?php foreach ($yh49Items as $yh49Item): ?>
                        <a href="<?= e($yh49Item['url']) ?>"
                           class="yh49-category-link <?= e($yh49Item['style']) ?> <?= e(\App\Services\SocialPlatformIdentity::classFor(\App\Services\SocialPlatformIdentity::fromText(($yh49Item['style']??'').' '.($yh49Item['name']??'').' '.($yh49Item['detail']??'')))) ?>"
                           <?= $yh49Repeat ? 'tabindex="-1"' : '' ?>
                           title="<?= e($yh49Item['name']) ?>">
                            <span class="yh49-category-icon"><?= icon(yh50MarqueeIcon((string)$yh49Item['name'], (string)$yh49Item['style'], (string)$yh49Item['icon']), 20) ?></span>
                            <span class="yh49-category-copy">
                                <strong><?= e($yh49Item['name']) ?></strong>
                                <small><?= e($yh49Item['detail']) ?></small>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<section class="yh6-why nv75-why" aria-labelledby="nv75-why-heading">
    <div class="container">
        <div class="yh6-why-grid">
            <div class="yh6-why-copy">
                <span class="yh6-eyebrow nv75-why-eyebrow"><?= icon('sparkles',14) ?> NetVera'yı Keşfedin</span>
                <h2 id="nv75-why-heading">Dijital dünyada <span>tüm çözümler bir arada.</span></h2>
                <p>Birden fazla ajans veya yazılım sağlayıcısı arasında kaybolmadan; web altyapısından SEO ve reklam yönetimine, sosyal medya paketlerinden destek süreçlerine kadar ihtiyaçlarınızı tek noktada yönetin.</p>
                <div class="nv75-benefits" aria-label="NetVera hizmet alanları">
                    <article class="nv75-benefit">
                        <span class="nv75-benefit-icon nv75-benefit-purple"><?= icon('code',19) ?></span>
                        <div><strong>Yazılım ve web çözümleri</strong><small>Hazır scriptler ve özel geliştirme</small></div>
                    </article>
                    <article class="nv75-benefit">
                        <span class="nv75-benefit-icon nv75-benefit-blue"><?= icon('search',19) ?></span>
                        <div><strong>SEO ve görünürlük</strong><small>Teknik SEO, GEO ve içerik stratejisi</small></div>
                    </article>
                    <article class="nv75-benefit">
                        <span class="nv75-benefit-icon nv75-benefit-pink"><?= icon('ads',19) ?></span>
                        <div><strong>Dijital reklam yönetimi</strong><small>Google Ads ve sosyal medya kampanyaları</small></div>
                    </article>
                    <article class="nv75-benefit">
                        <span class="nv75-benefit-icon nv75-benefit-indigo"><?= icon('share-2',19) ?></span>
                        <div><strong>Sosyal medya hizmetleri</strong><small>Instagram, TikTok ve YouTube</small></div>
                    </article>
                    <article class="nv75-benefit nv75-benefit-wide">
                        <span class="nv75-benefit-icon nv75-benefit-cyan"><?= icon('headphones',19) ?></span>
                        <div><strong>Sipariş ve destek takibi</strong><small>Hizmet ayrıntıları, süreç yönetimi ve iletişim tek yerde</small></div>
                        <?= icon('arrow-up-right',17) ?>
                    </article>
                </div>
                <a href="/kategoriler" class="yh6-btn primary nv75-why-cta">Hizmet Alanlarını İncele <?= icon('arrow-right',15) ?></a>
            </div>

            <div class="yh6-dashboard nv75-why-dashboard">
                <div class="yh6-browser nv75-ecosystem">
                    <div class="yh6-browser-top">
                        <span></span><span></span><span></span>
                        <b>NetVera Dijital Ekosistemi</b>
                        <em>3 Hizmet Grubu</em>
                    </div>
                    <div class="nv75-ecosystem-body">
                        <div class="nv75-ecosystem-heading">
                            <span class="nv75-ecosystem-mark"><?= icon('layers',21) ?></span>
                            <div><strong>Tek marka, farklı uzmanlıklar.</strong><small>İhtiyacınız olan hizmete doğrudan ulaşın.</small></div>
                        </div>
                        <div class="nv75-ecosystem-items">
                            <a href="/hazir-scriptler" class="nv75-ecosystem-item">
                                <span class="nv75-ecosystem-item-icon nv75-purple"><?= icon('monitor',20) ?></span>
                                <div><small>01 / Yazılım</small><strong>Hazır Scriptler ve Web Çözümleri</strong><em>Yönetim panelli, sektörel yazılımlar</em></div>
                                <?= icon('arrow-up-right',17) ?>
                            </a>
                            <a href="/kategoriler" class="nv75-ecosystem-item">
                                <span class="nv75-ecosystem-item-icon nv75-blue"><?= icon('bar-chart',20) ?></span>
                                <div><small>02 / Dijital Ajans</small><strong>SEO, Reklam ve İçerik</strong><em>Markanız için bütünleşik dijital hizmetler</em></div>
                                <?= icon('arrow-up-right',17) ?>
                            </a>
                            <a href="/kategoriler" class="nv75-ecosystem-item">
                                <span class="nv75-ecosystem-item-icon nv75-pink"><?= icon('instagram',20) ?></span>
                                <div><small>03 / Sosyal Medya</small><strong>Takipçi, Beğeni ve İzlenme</strong><em>Platforma göre hizmet seçenekleri</em></div>
                                <?= icon('arrow-up-right',17) ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="yh6-dash-services nv75-platform-services">
                    <div><span class="google"><?= icon('google',17) ?></span><b>Google SEO</b><small>Arama ve işletme görünürlüğü</small></div>
                    <div><span class="instagram"><?= icon('instagram',17) ?></span><b>Instagram</b><small>Takipçi ve etkileşim</small></div>
                    <div><span class="tiktok"><?= icon('tiktok',17) ?></span><b>TikTok</b><small>Video ve profil hizmetleri</small></div>
                    <div><span class="youtube"><?= icon('youtube',17) ?></span><b>YouTube</b><small>Kanal ve video hizmetleri</small></div>
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
                                class="yh18-featured-tab yh24-featured-filter nv43-category-card <?= $selected?'active':'' ?> <?= e($gcls) ?> <?= e(\App\Services\SocialPlatformIdentity::classFor(\App\Services\SocialPlatformIdentity::fromCategory($filterCategory))) ?>"
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
                    $nvChildParentPlatform=null;
                    foreach($navGroup['categories'] as $nc) {
                        if((int)$nc['id']===(int)$parentId){$hasParentInGroup=true;$nvChildParentPlatform=\App\Services\SocialPlatformIdentity::fromCategory($nc);break;}
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
                            <a class="nv43-subcategory-link <?= e(\App\Services\SocialPlatformIdentity::classFor((\App\Services\SocialPlatformIdentity::fromText(($child['slug']??'').' '.($child['name']??''))??$nvChildParentPlatform))) ?>" href="<?= e($childUrl) ?>"
                               title="<?= e($child['name']) ?>" data-menu-link>
                                <?= icon($childIcon, 15) ?> <span><?= e($child['name']) ?></span>
                                <?= icon('arrow-up-right',12) ?>
                            </a>
                            <?php else: ?>
                            <button class="nv43-subcategory-link nv43-subcategory-tab <?= e(\App\Services\SocialPlatformIdentity::classFor((\App\Services\SocialPlatformIdentity::fromText(($child['slug']??'').' '.($child['name']??''))??$nvChildParentPlatform))) ?>"
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
          <a href="/hazir-scriptler?type=<?= rawurlencode($softwareCategory['slug']) ?>">
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

<section class="yh6-stats" aria-label="Platform istatistikleri">
    <div class="container">
        <?php foreach ([
            ['code','Yazılım','Web & Sektörel Çözümler'],
            ['layers','Ajans','Tasarım ve Reklam'],
            ['share-2','Sosyal Medya','Platform Hizmetleri'],
            ['search','SEO','Arama Optimizasyonu'],
            ['headphones','Destek','Sipariş Takibi']
        ] as $metric): ?>
        <div class="yh49-stat">
            <span class="yh49-stat-icon" aria-hidden="true"><?= icon($metric[0], 22) ?></span>
            <span class="yh49-stat-info"><strong><?= e($metric[1]) ?></strong><small><?= e($metric[2]) ?></small></span>
        </div>
        <?php endforeach; ?>
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
        <?php
          $renderReviewBody=static function(array $review):void{
            $name=trim((string)($review['name']??'Müşteri'));
            $initial=mb_strtoupper(mb_substr($name,0,1,'UTF-8'),'UTF-8');
            $rating=$review['rating']??null;
            $label=trim((string)($review['service_name']??''));
            if($label==='')$label=trim((string)($review['category_name']??''));
        ?>
            <div class="nv30-review-topline">
              <span class="nv30-review-quote" aria-hidden="true"><?= icon('message-circle',23) ?></span>
              <?php if($rating!==null): ?><span class="nv30-review-stars" aria-label="<?= (int)$rating ?>/5 yıldız"><?= str_repeat('★',max(1,min(5,(int)$rating))) ?></span><?php endif; ?>
            </div>
            <blockquote><?= e($review['text']) ?></blockquote>
            <?php if($label!==''): ?><div class="nv68-review-service"><?= icon('check-circle',12) ?> <?= e($label) ?></div><?php endif; ?>
            <div class="nv30-review-author">
              <?php if(!empty($review['image'])): ?><img class="nv68-review-image" src="<?= e(upload_url($review['image'])) ?>" alt="" loading="lazy">
              <?php else: ?><span class="nv30-review-avatar" aria-hidden="true"><?= e($initial) ?></span><?php endif; ?>
              <div class="nv30-review-author-info">
                <strong><?= e($name) ?></strong>
                <?php if(!empty($review['role'])): ?><small><?= e($review['role']) ?></small><?php endif; ?>
              </div>
              <span class="nv30-review-decoration" aria-hidden="true"><?= icon('message-circle',16) ?></span>
            </div>
        <?php }; ?>
        <div class="nv69-review-viewport" data-review-viewport>
          <div class="nv30-review-grid" data-review-track aria-label="Müşteri yorumları" aria-live="off">
            <?php foreach(array_values($reviews) as $index=>$review): ?>
              <article class="nv30-review-card <?= $index===0?'nv30-review-primary':'' ?>">
                <?php $renderReviewBody($review); ?>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="nv30-feedback-foot">
            <span><?= icon('shield-check', 15) ?> Açık ve anlaşılır sipariş takibi</span>
            <span><?= icon('headphones', 15) ?> Destek merkezine kolay erişim</span>
            <a href="/kategoriler">İhtiyacınıza uygun hizmeti bulun <?= icon('arrow-right', 13) ?></a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$nv51ReferenceGroups = \App\Services\ReferencesService::groups();
$nv51InitialGroup = 'agency';
if (!empty($projectReferences)) {
    $nv51InitialGroup = \App\Services\ReferencesService::normalizedGroup($projectReferences[0]);
}
?>
<section class="nv31-portfolio nv51-portfolio" id="referanslarimiz" aria-labelledby="nv31-portfolio-heading" data-reference-gallery>
  <div class="container">
    <div class="nv31-section-heading nv51-heading">
      <div>
        <span class="nv31-kicker"><?= icon('award',14) ?> PROJE PORTFÖYÜMÜZ</span>
        <h2 id="nv31-portfolio-heading">Referanslarımız <span>& Çalışmalarımız</span></h2>
        <p>Web sitesi, yazılım, SEO ve sosyal medya çalışmalarımızı kategoriye göre keşfedin.</p>
      </div>
      <a href="/iletisim" class="nv31-heading-link">Projenizi Konuşalım <?= icon('arrow-up-right',15) ?></a>
    </div>

    <div class="nv51-filter-shell">
      <div class="nv51-filter-heading">
        <span><?= icon('sliders-horizontal',14) ?> Referans Kategorileri</span>
        <small>Çalışmaları hizmet alanına göre filtreleyin</small>
      </div>
      <div class="nv51-main-filters" role="group" aria-label="Referans ana kategorileri">
        <?php foreach ($nv51ReferenceGroups as $groupKey=>$group): ?>
        <button class="nv51-main-filter" type="button" data-ref-group="<?= e($groupKey) ?>"
                aria-pressed="<?= $groupKey===$nv51InitialGroup?'true':'false' ?>">
          <?= icon($group['icon'],16) ?> <?= e($group['label']) ?>
        </button>
        <?php endforeach; ?>
      </div>
      <?php foreach ($nv51ReferenceGroups as $groupKey=>$group): ?>
      <div class="nv51-sub-filters" data-ref-service-panel="<?= e($groupKey) ?>" <?= $groupKey!==$nv51InitialGroup?'hidden':'' ?> role="group" aria-label="<?= e($group['label']) ?> alt hizmetleri">
        <button type="button" class="nv51-sub-filter" data-ref-service="all" aria-pressed="true">Tümü</button>
        <?php foreach ($group['services'] as $serviceKey=>$serviceName): ?>
        <button type="button" class="nv51-sub-filter" data-ref-service="<?= e($serviceKey) ?>" aria-pressed="false">
          <?= e($serviceName) ?>
        </button>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
      <div class="nv51-format-filters" data-ref-format-panel hidden role="group" aria-label="Sosyal medya içerik türleri">
        <span><?= icon('instagram',15) ?> İçerikler</span>
        <button type="button" data-ref-format="all" aria-pressed="true">Tümü</button>
        <button type="button" data-ref-format="posts" aria-pressed="false"><?= icon('image',13) ?> Postlar</button>
        <button type="button" data-ref-format="reels" aria-pressed="false"><?= icon('video',13) ?> Reels</button>
      </div>
    </div>

    <div class="nv31-portfolio-grid nv51-portfolio-grid" aria-live="polite">
      <?php foreach ($projectReferences as $ref):
        $refPlacements = \App\Services\ReferencesService::normalizedPlacements($ref);
        $refGroup = $refPlacements[0]['group'];
        $refService = $refPlacements[0]['service'];
        // The same physical card may appear in either group without duplicate HTML.
        $refPublicPlacements = [];
        foreach ($refPlacements as $placement) {
            $refPublicPlacements[] = [
                'group' => $placement['group'],
                'service' => $placement['service'],
                'label' => $nv51ReferenceGroups[$placement['group']]['services'][$placement['service']] ?? 'Dijital Proje',
            ];
        }
        $refMedia = \App\Services\ReferencesService::normalizedMedia($ref);
        $embedUrl = \App\Services\ReferencesService::instagramEmbed((string)($ref['url']??''), $refMedia);
        $hasInstagram = $embedUrl !== null;
        $isYouTube = $refMedia === 'youtube_video';
        $hostedPlayer = $isYouTube
            ? \App\Services\ReferencesService::externalPlayer((string)($ref['url'] ?? ''))
            : ($hasInstagram ? \App\Services\ReferencesService::externalPlayer((string)($ref['external_video_url'] ?? '')) : null);
        $localVideo = (string)($ref['video'] ?? '');
        // Only first-party files written by the reference upload endpoint may play natively.
        if (!preg_match('~^/uploads/references/videos/[A-Za-z0-9_.-]+\.(?:mp4|webm)$~D', $localVideo)) {
            $localVideo = '';
        }
        $hasVideoMedia = $hasInstagram || $isYouTube;
        $isWebsite = $refMedia === 'website' && !empty($ref['url']);
        $hasOnsitePlayer = $hasVideoMedia && (!empty($hostedPlayer) || $localVideo !== '');
        $instagramOnly = $hasInstagram && !$hasOnsitePlayer;
        $cover = (string)($ref['image']??'');
        $logo = (string)($ref['logo']??'');
        $target = trim((string)($ref['url']??''));
        $serviceLabel = $nv51ReferenceGroups[$refGroup]['services'][$refService] ?? 'Dijital Proje';
        foreach ($refPublicPlacements as $position) {
            if ($position['group'] === $nv51InitialGroup) {
                $serviceLabel = $position['label'];
                break;
            }
        }
      ?>
      <article class="nv31-portfolio-card nv51-reference-card"
               data-ref-card
               data-ref-placements="<?= e(json_encode($refPublicPlacements, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?>"
               data-ref-kind="<?= e($refMedia) ?>"
               <?php if ($hasVideoMedia): ?>
               data-ref-external-url="<?= e($hostedPlayer['url'] ?? '') ?>"
               data-ref-external-type="<?= e($hostedPlayer['type'] ?? '') ?>"
               data-ref-external-provider="<?= e($hostedPlayer['provider'] ?? '') ?>"
               data-ref-aspect="<?= e($hostedPlayer['aspect'] ?? '') ?>"
               data-ref-video="<?= $localVideo !== '' ? e(upload_url($localVideo)) : '' ?>"
               data-ref-title="<?= e($ref['title']) ?>"
               <?php endif; ?>
               <?= in_array($nv51InitialGroup, array_column($refPublicPlacements, 'group'), true)?'':'hidden' ?>>
        <?php if ($isWebsite): ?>
        <a class="nv51-client-link" href="<?= e($target) ?>" target="_blank" rel="noopener noreferrer"
           aria-label="<?= e($ref['title']) ?> <?= str_starts_with($target,'/')?'proje detayını görüntüle':'müşteri sitesine git' ?>">
        <?php endif; ?>

        <div class="nv31-portfolio-image nv51-cover <?= $hasVideoMedia?'nv51-instagram-media':'' ?>">
          <?php if ($hasVideoMedia): ?>
          <?php if ($instagramOnly): ?>
          <a class="nv51-play-embed" href="<?= e($target) ?>" target="_blank" rel="noopener noreferrer"
             aria-label="<?= e($ref['title']) ?> videosunu Instagram'da izle (yeni sekme)">
          <?php else: ?>
          <button type="button" class="nv51-play-embed" data-ref-play
                  aria-label="<?= e($ref['title']) ?> videosunu sitemizde izle">
          <?php endif; ?>
            <?php if ($cover): ?>
              <img src="<?= e(upload_url($cover)) ?>" alt="<?= e($ref['title']) ?> kapak görseli" loading="lazy" decoding="async">
            <?php else: ?>
              <span class="nv51-social-placeholder"><?= icon($isYouTube?'youtube':'instagram',35) ?></span>
            <?php endif; ?>
            <span class="nv51-play-icon"><?= icon($refMedia==='instagram_post'?'instagram':'play',23) ?></span>
            <span class="nv51-media-tag"><?= e($isYouTube
                ? (($hostedPlayer['aspect'] ?? '') === 'portrait' ? 'YouTube Shorts' : 'YouTube Video')
                : ($refMedia==='instagram_reel'?'Reels':'Instagram Post')) ?></span>
          <?php if ($instagramOnly): ?></a><?php else: ?></button><?php endif; ?>

          <?php else: ?>
            <?php if ($cover): ?>
              <img src="<?= e(upload_url($cover)) ?>" alt="<?= e($ref['title']) ?> proje görseli" loading="lazy" decoding="async">
            <?php elseif($logo): ?>
              <img class="nv51-logo-cover" src="<?= e(upload_url($logo)) ?>" alt="<?= e($ref['title']) ?> marka logosu" loading="lazy">
            <?php else: ?>
              <div class="nv31-portfolio-placeholder"><?= icon($refMedia==='website'?'monitor':'image',45) ?></div>
            <?php endif; ?>
          <?php endif; ?>
          <?php if($logo && $cover): ?>
            <span class="nv51-corner-logo"><img src="<?= e(upload_url($logo)) ?>" alt="" loading="lazy"></span>
          <?php endif; ?>
          <?php if($isWebsite): ?>
            <span class="nv31-portfolio-arrow" aria-hidden="true"><?= icon('arrow-up-right',16) ?></span>
          <?php endif; ?>
        </div>

        <div class="nv31-portfolio-body nv51-reference-body">
          <span class="nv31-portfolio-kind"><?= icon($isYouTube?'youtube':($hasInstagram?'instagram':($isWebsite?'globe':'layers')),12) ?> <span data-ref-service-label><?= e($serviceLabel) ?></span></span>
          <h3><?= e($ref['title']) ?></h3>
          <?php if(!empty($ref['description'])): ?><p><?= e($ref['description']) ?></p><?php endif; ?>
          <?php if($isWebsite): ?>
            <span class="nv51-reference-action"><?= str_starts_with($target,'/')?'Proje Detayını Gör':'Müşteri Sitesini Ziyaret Et' ?> <?= icon('arrow-up-right',13) ?></span>
          <?php elseif ($instagramOnly): ?>
            <a class="nv51-reference-action nv52-watch-action" href="<?= e($target) ?>" target="_blank" rel="noopener noreferrer"
               aria-label="<?= e($ref['title']) ?> videosunu Instagram'da izle (yeni sekme)"><?= icon('play',12) ?> Videoyu İzle <?= icon('arrow-up-right',12) ?></a>
          <?php elseif ($hasOnsitePlayer): ?>
            <button type="button" class="nv51-reference-action nv52-watch-action" data-ref-play><?= icon('play',12) ?> Videoyu İzle <?= icon('arrow-right',12) ?></button>
          <?php elseif ($target): ?>
            <a class="nv51-reference-action" href="<?= e($target) ?>" target="_blank" rel="noopener noreferrer">Çalışmayı Gör <?= icon('arrow-up-right',13) ?></a>
          <?php endif; ?>
        </div>

        <?php if ($isWebsite): ?></a><?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="nv51-no-results" data-ref-empty hidden>
      <?= icon('folder',25) ?>
      <strong>Bu kategoride henüz yayınlanmış referans yok.</strong>
      <p>Diğer hizmetleri veya farklı bir kategori seçebilirsiniz.</p>
    </div>

  </div>
</section>
<dialog class="nv52-player-dialog" data-ref-player-modal aria-labelledby="nv52-player-title">
  <div class="nv52-player-window">
    <header class="nv52-player-head">
      <div class="nv52-player-ident">
        <span class="nv52-player-mark"><?= icon('play-circle',19) ?></span>
        <div><span class="nv52-player-eyebrow">REFERANS VİDEOSU</span><h3 id="nv52-player-title" data-ref-player-title>Çalışmamız</h3></div>
      </div>
      <button class="nv52-player-close" type="button" data-ref-player-close aria-label="Videoyu kapat"><?= icon('x',20) ?></button>
    </header>
    <div class="nv52-player-stage" data-ref-player-stage aria-label="Video oynatıcı"></div>
    <div class="nv52-player-foot" data-ref-player-foot role="status" aria-live="polite"></div>
  </div>
</dialog>
<script>
(function(){
 const root=document.querySelector('[data-reference-gallery]');
 if(!root)return;
 const groups=Array.from(root.querySelectorAll('[data-ref-group]'));
 const panels=Array.from(root.querySelectorAll('[data-ref-service-panel]'));
 const cards=Array.from(root.querySelectorAll('[data-ref-card]'));
 const formatPanel=root.querySelector('[data-ref-format-panel]');
 const empty=root.querySelector('[data-ref-empty]');
 const modal=document.querySelector('[data-ref-player-modal]');
 const stage=modal?.querySelector('[data-ref-player-stage]');
 const note=modal?.querySelector('[data-ref-player-foot]');
 const modalTitle=modal?.querySelector('[data-ref-player-title]');
 const closeButton=modal?.querySelector('[data-ref-player-close]');
 let previousFocus=null;

 function clearPlayer(){
   if(modal)modal.removeAttribute('data-video-orientation');
   if(!stage)return;
   const video=stage.querySelector('video');
   if(video){video.pause();video.removeAttribute('src');video.load();}
   stage.replaceChildren();
 }
 function closePlayer(){
   if(!modal)return;
   if(modal.open)modal.close();
   clearPlayer();
 }
 if(modal){
   modal.addEventListener('close',()=>{
     clearPlayer();
     if(previousFocus?.isConnected)previousFocus.focus();
     previousFocus=null;
   });
   modal.addEventListener('click',e=>{if(e.target===modal)closePlayer();});
   closeButton?.addEventListener('click',closePlayer);
 }
 function openPlayer(card,trigger){
   if(!card||!modal||!stage)return;
   const videoPath=card.dataset.refVideo||'';
   const externalUrl=card.dataset.refExternalUrl||'';
   const externalType=card.dataset.refExternalType||'';
   const externalProvider=card.dataset.refExternalProvider||'';
   const aspect=card.dataset.refAspect||'';
   if(!videoPath && !externalUrl)return;
   previousFocus=trigger;
   clearPlayer();
   // Read the canonical YouTube source type; other providers retain legacy layout.
   if(externalProvider==='YouTube' && (aspect==='portrait'||aspect==='landscape')){
     modal.dataset.videoOrientation=aspect;
   }
   modalTitle.textContent=card.dataset.refTitle||'Referans videosu';
   if(externalUrl && (externalType==='video'||externalType==='iframe')){
     // Admin-validated external stream takes precedence over any first-party upload.
     // The video is streamed from its provider, not our site's PHP hosting disk.
     if(externalType==='video'){
       const video=document.createElement('video');
       video.controls=true;video.autoplay=true;video.playsInline=true;
       video.preload='metadata';
       const poster=card.querySelector('.nv51-play-embed img');
       if(poster?.src)video.poster=poster.src;
       video.src=externalUrl;
       video.addEventListener('error',()=>{
         if(note)note.textContent='Harici video yüklenemedi. Video bağlantısını ve sağlayıcının erişim ayarlarını kontrol edin.';
       });
       stage.appendChild(video);
       if(note)note.textContent='Video, harici sunucudan kendi oynatıcımızda yükleniyor.';
       modal.showModal();
       video.play().catch(()=>{});
     }else{
       const frame=document.createElement('iframe');
       frame.title=(externalProvider||'Harici video')+' oynatıcısı';
       frame.src=externalUrl;
       frame.loading='eager';
       frame.allow='autoplay; encrypted-media; picture-in-picture; fullscreen';
       frame.referrerPolicy='strict-origin-when-cross-origin';
       frame.setAttribute('allowfullscreen','');
       // Do not allow third-party video frames to navigate the top page
       // or open new tabs from the reference player's overlay.
       frame.setAttribute('sandbox','allow-scripts allow-same-origin allow-forms allow-presentation');
       stage.appendChild(frame);
       if(note)note.textContent=(externalProvider||'Harici video')+' üzerinden sitemizde oynatılıyor. Video dosyası bizim sunucuda saklanmaz.';
       modal.showModal();
     }
   }else if(videoPath){
     // Local upload remains available, but is not required for hosted streaming.
     const video=document.createElement('video');
     video.controls=true;video.autoplay=true;video.playsInline=true;
     video.preload='metadata';
     const poster=card.querySelector('.nv51-play-embed img');
     if(poster?.src)video.poster=poster.src;
     video.src=videoPath;
     video.addEventListener('error',()=>{
       if(note)note.textContent='Video yüklenemedi. Yönetim panelinden MP4/WebM dosyasını kontrol edin.';
     });
     stage.appendChild(video);
     if(note)note.textContent='Video burada, kendi oynatıcımızda açılıyor.';
     modal.showModal();
     video.play().catch(()=>{});
   }
 }

 let group=groups.find(b=>b.getAttribute('aria-pressed')==='true')?.dataset.refGroup||'agency';
 let service='all',format='all';
 function apply(){
   groups.forEach(b=>b.setAttribute('aria-pressed',String(b.dataset.refGroup===group)));
   panels.forEach(panel=>{
     panel.hidden=panel.dataset.refServicePanel!==group;
     panel.querySelectorAll('[data-ref-service]').forEach(button=>{
       button.setAttribute('aria-pressed',String(panel.dataset.refServicePanel===group && button.dataset.refService===service));
     });
   });
   const social=group==='marketing'&&service==='social-management';
   if(formatPanel)formatPanel.hidden=!social;
   if(formatPanel)formatPanel.querySelectorAll('[data-ref-format]').forEach(b=>b.setAttribute('aria-pressed',String(b.dataset.refFormat===format)));
   let found=0;
   cards.forEach(card=>{
     const t=card.dataset.refKind;
     let placements=[];
     try{placements=JSON.parse(card.dataset.refPlacements||'[]')}catch(e){placements=[]}
     const matched=placements.find(p=>p.group===group && (service==='all'||p.service===service));
     const permitted=!!matched &&
       (!social||format==='all'||(format==='reels'?t==='instagram_reel'||t==='youtube_video':t==='instagram_post'||t==='image'));
     if(matched){
       const label=card.querySelector('[data-ref-service-label]');
       if(label)label.textContent=matched.label||'Dijital Proje';
     }
     if(!permitted && modal?.open)closePlayer();
     card.hidden=!permitted;
     if(permitted)found++;
   });
   if(empty)empty.hidden=found>0;
 }
 groups.forEach(button=>button.addEventListener('click',()=>{group=button.dataset.refGroup;service='all';format='all';apply()}));
 panels.forEach(panel=>panel.querySelectorAll('[data-ref-service]').forEach(button=>{
   button.addEventListener('click',()=>{group=panel.dataset.refServicePanel;service=button.dataset.refService;format='all';apply()});
 }));
 if(formatPanel)formatPanel.querySelectorAll('[data-ref-format]').forEach(button=>button.addEventListener('click',()=>{format=button.dataset.refFormat;apply()}));
 root.querySelectorAll('[data-ref-play]').forEach(button=>button.addEventListener('click',()=>{
   openPlayer(button.closest('[data-ref-card]'),button);
 }));
 apply();
})();
</script>

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
            <h2 id="home-final-title">NetVera Teknoloji Yazılım ile Dijital Varlığınızı Güçlendirin</h2>
            <p>NetVera Teknoloji Yazılım ile web ve sektörel yazılım çözümlerini, dijital ajans hizmetlerini, SEO çalışmalarını ve sosyal medya paketlerini tek adreste inceleyin.</p>
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