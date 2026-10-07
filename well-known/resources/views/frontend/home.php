<?php
/**
 * YorumHizmeti.tr — Ana Sayfa V3
 * Art direction + bölüm kompozisyonu odaklı paket satış vitrini.
 * Paket isimleri, fiyatları, slug'ları ve sipariş akışı değiştirilmez.
 */

$sec = function(string $key) use ($sections) {
    return $sections[$key] ?? null;
};

$hero = $sec('hero');
$howItWorks = $sec('how_it_works');
$testimonials = $sec('testimonials');
$infoCenter = $sec('info_center');
$seoText = $sec('seo_text');
$ctaSection = $sec('cta');

if (!function_exists('yhPlatformDesign')) {
    function yhPlatformDesign(string $slug = '', string $name = ''): array
    {
        $s = mb_strtolower($slug . ' ' . $name);
        if (str_contains($s, 'instagram')) return ['icon'=>'instagram','class'=>'instagram','label'=>'Instagram'];
        if (str_contains($s, 'tiktok')) return ['icon'=>'tiktok','class'=>'tiktok','label'=>'TikTok'];
        if (str_contains($s, 'youtube')) return ['icon'=>'youtube','class'=>'youtube','label'=>'YouTube'];
        if (str_contains($s, 'facebook')) return ['icon'=>'facebook','class'=>'facebook','label'=>'Facebook'];
        if (str_contains($s, 'twitter') || str_contains($s, 'x-') || $slug === 'x') return ['icon'=>'twitter','class'=>'twitter','label'=>'X'];
        if (str_contains($s, 'google') || str_contains($s, 'seo')) return ['icon'=>'google','class'=>'google','label'=>'Google'];
        if (str_contains($s, 'web') || str_contains($s, 'site')) return ['icon'=>'globe','class'=>'web','label'=>'Web'];
        return ['icon'=>'package','class'=>'default','label'=>'Hizmet'];
    }
}

if (!function_exists('yhPackageCard')) {
    function yhPackageCard(array $pkg, string $variant = 'light'): string
    {
        $price = (!empty($pkg['discount_price']) && $pkg['discount_price'] < $pkg['price'])
            ? $pkg['discount_price']
            : $pkg['price'];
        $platform = yhPlatformDesign($pkg['category_slug'] ?? '', $pkg['name'] ?? '');
        ob_start();
        ?>
        <a class="yh-product-card <?= $variant === 'dark' ? 'is-dark' : '' ?>" href="/paket/<?= e($pkg['slug']) ?>">
            <div class="yh-product-top">
                <span class="yh-platform-icon <?= e($platform['class']) ?>">
                    <?= icon($platform['icon'], 18) ?>
                </span>
                <span class="yh-product-cat"><?= e($pkg['category_name'] ?? $platform['label']) ?></span>
                <?php if (!empty($pkg['badge'])): ?>
                    <span class="yh-product-badge"><?= e($pkg['badge']) ?></span>
                <?php endif; ?>
            </div>

            <h3><?= e($pkg['name']) ?></h3>

            <div class="yh-price-row">
                <?php if (!empty($pkg['discount_price']) && $pkg['discount_price'] < $pkg['price']): ?>
                    <span class="yh-old-price"><?= money((float)$pkg['price']) ?></span>
                <?php endif; ?>
                <strong><?= money((float)$price) ?></strong>
            </div>

            <div class="yh-product-meta">
                <span><?= icon('clock', 13) ?> <?= e($pkg['delivery_time'] ?? 'Hızlı teslimat') ?></span>
                <span class="yh-product-arrow"><?= icon('arrow-right', 13) ?></span>
            </div>
        </a>
        <?php
        return ob_get_clean();
    }
}

$heroImage = ($hero && !empty($hero['image']))
    ? upload_url($hero['image'])
    : 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1400&q=84';
?>

<main class="yh-home">

    <!-- HERO / ART DIRECTION -->
    <section class="yh-hero">
        <div class="container">
            <div class="yh-hero-shell">
                <div class="yh-hero-copy">
                    <div class="yh-kicker">
                        <span></span>
                        YorumHizmeti.tr / Dijital büyüme platformu
                    </div>

                    <h1><?= $hero ? $hero['title'] : 'Markanızı görünür kılan <span>dijital hizmetler.</span>' ?></h1>

                    <p>
                        <?= $hero
                            ? e($hero['subtitle'] ?? '')
                            : 'Sosyal medya, Google, içerik ve web çözümlerini tek noktadan yönetin. Hızlı, sade ve güven veren satın alma deneyimi.' ?>
                    </p>

                    <div class="yh-hero-actions">
                        <a href="<?= e($hero['button_url'] ?? '/kategoriler') ?>" class="yh-btn yh-btn-light">
                            <?= e($hero['button_text'] ?? 'Hizmetleri Keşfet') ?>
                            <?= icon('arrow-right', 15) ?>
                        </a>
                        <a href="#featured" class="yh-btn yh-btn-ghost">
                            <?= icon('play', 13) ?> Popüler paketler
                        </a>
                    </div>

                    <div class="yh-hero-proof">
                        <div>
                            <strong>7/24</strong>
                            <span>Destek</span>
                        </div>
                        <div>
                            <strong>SSL</strong>
                            <span>Güvenli Ödeme</span>
                        </div>
                        <div>
                            <strong>Hızlı</strong>
                            <span>Teslimat</span>
                        </div>
                    </div>
                </div>

                <div class="yh-hero-art" style="--yh-hero-image:url('<?= e($heroImage) ?>')">
                    <div class="yh-art-orbit orbit-a"></div>
                    <div class="yh-art-orbit orbit-b"></div>

                    <div class="yh-hero-person"></div>

                    <div class="yh-floating-card yh-rating-card">
                        <span class="yh-card-label">Müşteri memnuniyeti</span>
                        <div class="yh-stars">
                            <?php for ($i=0;$i<5;$i++): ?><?= icon('star-fill', 13) ?><?php endfor; ?>
                        </div>
                        <strong>%98</strong>
                    </div>

                    <div class="yh-floating-card yh-platform-card">
                        <span class="yh-platform-dot instagram"><?= icon('instagram', 16) ?></span>
                        <span class="yh-platform-dot tiktok"><?= icon('tiktok', 15) ?></span>
                        <span class="yh-platform-dot google"><?= icon('google', 15) ?></span>
                        <div>
                            <strong>Tek panel</strong>
                            <small>Tüm dijital hizmetler</small>
                        </div>
                    </div>

                    <div class="yh-floating-card yh-order-card">
                        <?= icon('check-circle', 18) ?>
                        <div>
                            <strong>Sipariş alındı</strong>
                            <small>İşlem otomatik başlatıldı</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PLATFORM STRIP -->
    <section class="yh-platforms">
        <div class="container">
            <div class="yh-section-mini">
                <span>Popüler platformlar</span>
                <a href="/kategoriler">Tüm hizmetler <?= icon('arrow-right', 12) ?></a>
            </div>

            <div class="yh-platform-grid">
                <?php foreach ($categories as $cat):
                    $pd = yhPlatformDesign($cat['slug'] ?? '', $cat['name'] ?? '');
                ?>
                    <a href="/kategori/<?= e($cat['slug']) ?>" class="yh-platform-tile <?= e($pd['class']) ?>">
                        <span><?= icon($pd['icon'], 22) ?></span>
                        <strong><?= e($cat['name']) ?></strong>
                        <small>Hizmetleri gör</small>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- VALUE COMPOSITION -->
    <section class="yh-story">
        <div class="container">
            <div class="yh-story-grid">
                <div class="yh-story-copy">
                    <span class="yh-eyebrow">Neden YorumHizmeti?</span>
                    <h2>Sadece paket satmıyoruz, <span>satın alma deneyimi</span> tasarlıyoruz.</h2>
                    <p>İhtiyacınızı hızlı bulabileceğiniz, güvenli ödeme yapabileceğiniz ve siparişinizi tek panelden takip edebileceğiniz sade bir sistem.</p>

                    <div class="yh-benefit-list">
                        <div><?= icon('shield', 18) ?><span><strong>Güvenli işlem</strong><small>Ödeme ve kullanıcı verileri için korumalı akış.</small></span></div>
                        <div><?= icon('zap', 18) ?><span><strong>Hızlı süreç</strong><small>Satın alma sonrası sipariş hemen işleme alınır.</small></span></div>
                        <div><?= icon('headphones', 18) ?><span><strong>Gerçek destek</strong><small>Sipariş öncesi ve sonrası tek noktadan iletişim.</small></span></div>
                    </div>
                </div>

                <div class="yh-dashboard-art">
                    <div class="yh-dashboard-window">
                        <div class="yh-dash-top">
                            <span></span><span></span><span></span>
                            <em>YorumHizmeti Panel</em>
                        </div>
                        <div class="yh-dash-body">
                            <div class="yh-dash-sidebar">
                                <i></i><i></i><i></i><i></i><i></i>
                            </div>
                            <div class="yh-dash-content">
                                <div class="yh-dash-heading"></div>
                                <div class="yh-dash-stats">
                                    <span></span><span></span><span></span>
                                </div>
                                <div class="yh-dash-chart">
                                    <b></b><b></b><b></b><b></b><b></b><b></b><b></b>
                                </div>
                                <div class="yh-dash-table">
                                    <i></i><i></i><i></i><i></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="yh-mini-proof proof-one"><?= icon('lock', 14) ?> Güvenli ödeme</div>
                    <div class="yh-mini-proof proof-two"><?= icon('check-circle', 14) ?> Anlık takip</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURED / DARK SALES AREA -->
    <?php if (!empty($featuredPackages)): ?>
    <section class="yh-featured" id="featured">
        <div class="container">
            <div class="yh-featured-head">
                <div>
                    <span class="yh-eyebrow light">En çok tercih edilenler</span>
                    <h2>Öne çıkan paketler</h2>
                    <p>İhtiyacınıza göre hazırlanmış popüler hizmet paketlerini inceleyin.</p>
                </div>
                <a href="/kategoriler" class="yh-btn yh-btn-outline-light">Tüm paketler <?= icon('arrow-right', 13) ?></a>
            </div>

            <div class="yh-featured-grid">
                <?php foreach (array_slice($featuredPackages, 0, 8) as $pkg): ?>
                    <?= yhPackageCard($pkg, 'dark') ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CATEGORY STORIES -->
    <?php if (!empty($homeCategoryBlocks)): ?>
        <?php foreach ($homeCategoryBlocks as $idx => $block):
            $cat = $block['category'];
            $pd = yhPlatformDesign($cat['slug'] ?? '', $cat['name'] ?? '');
        ?>
        <section class="yh-category-story <?= $idx % 2 ? 'is-alt' : '' ?>">
            <div class="container">
                <div class="yh-category-wrap">
                    <div class="yh-category-intro">
                        <span class="yh-platform-big <?= e($pd['class']) ?>"><?= icon($pd['icon'], 34) ?></span>
                        <span class="yh-eyebrow"><?= e($pd['label']) ?> hizmetleri</span>
                        <h2><?= e($cat['name']) ?></h2>
                        <?php if (!empty($cat['description'])): ?>
                            <p><?= e(excerpt(strip_tags($cat['description']), 180)) ?></p>
                        <?php else: ?>
                            <p>Markanızın görünürlüğünü ve dijital performansını artıracak seçili paketler.</p>
                        <?php endif; ?>
                        <a href="/kategori/<?= e($cat['slug']) ?>" class="yh-text-link">
                            Tüm <?= e($pd['label']) ?> paketleri <?= icon('arrow-right', 12) ?>
                        </a>
                    </div>

                    <div class="yh-category-products">
                        <?php foreach (array_slice($block['packages'], 0, 4) as $pkg): ?>
                            <?= yhPackageCard($pkg) ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- STATS BAND -->
    <section class="yh-stats-band">
        <div class="container">
            <div class="yh-stats-grid">
                <div><span><?= icon('users', 20) ?></span><strong>1.000+</strong><small>Mutlu müşteri</small></div>
                <div><span><?= icon('shopping-cart', 20) ?></span><strong>10.000+</strong><small>Tamamlanan sipariş</small></div>
                <div><span><?= icon('clock', 20) ?></span><strong>7/24</strong><small>Destek erişimi</small></div>
                <div><span><?= icon('shield', 20) ?></span><strong>%100</strong><small>Güvenli ödeme</small></div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIAL COMPOSITION -->
    <?php if ($testimonials && !empty($testimonials['extra'])): ?>
    <section class="yh-testimonials">
        <div class="container">
            <div class="yh-testimonial-layout">
                <div class="yh-testimonial-lead">
                    <span class="yh-eyebrow">Müşteri deneyimleri</span>
                    <h2>İnsanların tekrar tercih ettiği bir deneyim.</h2>
                    <div class="yh-rating-big">
                        <strong>4.9</strong>
                        <span><?php for($i=0;$i<5;$i++): ?><?= icon('star-fill', 15) ?><?php endfor; ?></span>
                        <small>Gerçek müşteri değerlendirmeleri</small>
                    </div>
                </div>

                <div class="yh-testimonial-cards">
                    <?php foreach (array_slice($testimonials['extra'], 0, 4) as $review): ?>
                    <article class="yh-review-card">
                        <div class="yh-review-top">
                            <span class="yh-review-avatar"><?= mb_strtoupper(mb_substr($review['name'] ?? 'M', 0, 1)) ?></span>
                            <div>
                                <strong><?= e($review['name'] ?? '') ?></strong>
                                <small><?= e($review['role'] ?? '') ?></small>
                            </div>
                            <div class="yh-review-stars"><?php for($i=0;$i<($review['stars'] ?? 5);$i++): ?><?= icon('star-fill', 11) ?><?php endfor; ?></div>
                        </div>
                        <p><?= e($review['text'] ?? '') ?></p>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- HOW IT WORKS -->
    <section class="yh-process">
        <div class="container">
            <div class="yh-section-title center">
                <span class="yh-eyebrow">Nasıl çalışır?</span>
                <h2>4 adımda siparişinizi tamamlayın.</h2>
            </div>

            <div class="yh-process-grid">
                <div><b>01</b><span><?= icon('search', 20) ?></span><strong>Hizmeti seçin</strong><small>İhtiyacınıza uygun paketi bulun.</small></div>
                <div><b>02</b><span><?= icon('shopping-cart', 20) ?></span><strong>Sepete ekleyin</strong><small>Paket detaylarını kontrol edin.</small></div>
                <div><b>03</b><span><?= icon('credit-card', 20) ?></span><strong>Ödemeyi tamamlayın</strong><small>Güvenli ödeme ile siparişi oluşturun.</small></div>
                <div><b>04</b><span><?= icon('check-circle', 20) ?></span><strong>Sonucu takip edin</strong><small>Hesabınızdan süreci izleyin.</small></div>
            </div>
        </div>
    </section>

    <!-- BLOG / EDITORIAL -->
    <?php if (!empty($latestPosts)): ?>
    <section class="yh-editorial">
        <div class="container">
            <div class="yh-editorial-head">
                <div>
                    <span class="yh-eyebrow">Bilgi merkezi</span>
                    <h2><?= e($infoCenter['title'] ?? 'Dijital büyüme rehberleri') ?></h2>
                </div>
                <a href="/blog" class="yh-text-link">Tüm yazılar <?= icon('arrow-right', 12) ?></a>
            </div>

            <div class="yh-blog-grid">
                <?php foreach ($latestPosts as $i => $post): ?>
                    <a href="/blog/<?= e($post['slug']) ?>" class="yh-blog-card <?= $i === 0 ? 'is-featured' : '' ?>">
                        <div class="yh-blog-image">
                            <?php if (!empty($post['image'])): ?>
                                <img src="<?= e(upload_url($post['image'])) ?>" alt="<?= e($post['image_alt'] ?? $post['title']) ?>">
                            <?php else: ?>
                                <span><?= icon('file-text', 28) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="yh-blog-body">
                            <small><?= e($post['category_name'] ?? 'Rehber') ?></small>
                            <h3><?= e($post['title']) ?></h3>
                            <p><?= e(excerpt(strip_tags($post['excerpt'] ?? $post['content'] ?? ''), 120)) ?></p>
                            <span>Devamını oku <?= icon('arrow-right', 12) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- FAQ -->
    <?php if (!empty($faqs)): ?>
    <section class="yh-faq">
        <div class="container">
            <div class="yh-faq-layout">
                <div class="yh-faq-lead">
                    <span class="yh-eyebrow">Sıkça sorulan sorular</span>
                    <h2>Karar vermeden önce merak edilenler.</h2>
                    <p>Sipariş, ödeme ve teslimat süreciyle ilgili en sık sorulan sorular.</p>
                    <a href="/sss" class="yh-text-link">Tüm soruları görüntüle <?= icon('arrow-right', 12) ?></a>
                </div>

                <div class="yh-faq-list">
                    <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item yh-faq-item">
                        <div class="faq-question">
                            <span><?= e($faq['question']) ?></span>
                            <?= icon('chevron-down', 14, 'faq-chevron') ?>
                        </div>
                        <div class="faq-answer">
                            <p><?= nl2br(e($faq['answer'])) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- SEO / FINAL CTA -->
    <section class="yh-final">
        <div class="container">
            <div class="yh-final-copy">
                <span class="yh-eyebrow">YorumHizmeti.tr</span>
                <h2><?= e($seoText['title'] ?? 'Dijital hizmetleri tek bir güçlü vitrinde topluyoruz.') ?></h2>
                <p><?= e(excerpt(strip_tags($seoText['content'] ?? 'Google, Instagram, TikTok, YouTube ve web hizmetleri için güvenli, hızlı ve kullanıcı dostu çözümler.'), 320)) ?></p>
            </div>

            <div class="yh-final-cta">
                <span><?= icon('zap', 20) ?></span>
                <div>
                    <strong><?= e($ctaSection['title'] ?? 'Hazırsanız başlayalım.') ?></strong>
                    <small><?= e($ctaSection['subtitle'] ?? 'İhtiyacınıza uygun paketi şimdi keşfedin.') ?></small>
                </div>
                <a href="<?= e($ctaSection['button_url'] ?? '/kategoriler') ?>" class="yh-btn yh-btn-light">
                    <?= e($ctaSection['button_text'] ?? 'Paketleri incele') ?>
                    <?= icon('arrow-right', 14) ?>
                </a>
            </div>
        </div>
    </section>

</main>
