<?php
/**
 * Ana Sayfa — YorumPanel Pro
 * 12 bölüm: Hero, Search, Paketler, Nasıl Çalışır, Güven, Popüler Hizmetler,
 * Yorumlar, Bilgi Merkezi, Blog, SSS, SEO Metin, CTA
 *
 * İçerikler home_sections + DB verilerinden gelir.
 * SEO: Tek H1, düzgün H2/H3, alt metinler, iç linkler
 */

// Helper: section varsa ve aktifse
$sec = function(string $key) use ($sections) {
    return $sections[$key] ?? null;
};
$hero = $sec('hero');
$howItWorks = $sec('how_it_works');
$trust = $sec('trust');
$testimonials = $sec('testimonials');
$infoCenter = $sec('info_center');
$seoText = $sec('seo_text');
$ctaSection = $sec('cta');

// Platform hizmet bölümleri
$platformKeys = ['google_services', 'instagram_services', 'tiktok_services', 'youtube_services', 'seo_services', 'reputation'];
$platformSections = [];
foreach ($platformKeys as $pk) {
    $s = $sec($pk);
    if ($s) $platformSections[] = $s;
}
?>

<!-- ═══ 1. HERO ═══ -->
<section class="hero" id="hero">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content">
                <h1><?= $hero ? $hero['title'] : 'Dijitalde Güven Oluşturur,<br><span class="text-gradient">Markanızı Büyütürüz.</span>' ?></h1>
                <p><?= $hero ? e($hero['subtitle'] ?? '') : 'Gerçek kullanıcılar, kaliteli etkileşimler ve hızlı teslimat ile dijital dünyada güçlü bir itibar inşa edin.' ?></p>
                <div class="hero-actions">
                    <a href="<?= $hero ? e($hero['button_url'] ?? '/kategoriler') : '/kategoriler' ?>" class="btn btn-primary btn-lg">
                        <?= icon('arrow-right', 18) ?> <?= $hero ? e($hero['button_text'] ?? 'Hizmetleri İncele') : 'Hizmetleri İncele' ?>
                    </a>
                    <a href="/sss" class="btn btn-outline btn-lg">
                        <?= icon('play', 16) ?> Nasıl Çalışır?
                    </a>
                </div>
                <div class="hero-trust">
                    <div class="hero-trust-item">
                        <?= icon('shield', 16) ?>
                        <span>%100 Güvenli Ödeme</span>
                    </div>
                    <div class="hero-trust-item">
                        <?= icon('zap', 16) ?>
                        <span>Hızlı Teslimat</span>
                    </div>
                    <div class="hero-trust-item">
                        <?= icon('headphones', 16) ?>
                        <span>7/24 Destek</span>
                    </div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-visual-inner">
                    <!-- Central mockup -->
                    <div class="hero-mockup">
                        <div class="hero-mockup-screen">
                            <div class="hero-stars">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                <svg width="20" height="20" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="#FBBF24" stroke="#FBBF24" stroke-width="1"/></svg>
                                <?php endfor; ?>
                            </div>
                            <div class="hero-rating-text">Müşteri Memnuniyeti %98</div>
                            <div style="display:flex; gap:8px; margin-top:4px;">
                                <span class="badge badge-success">Güvenilir</span>
                                <span class="badge badge-primary">Hızlı</span>
                            </div>
                        </div>
                    </div>
                    <!-- Floating social icons -->
                    <div class="hero-float google">
                        <svg viewBox="0 0 24 24" width="28" height="28"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    </div>
                    <div class="hero-float instagram">
                        <svg viewBox="0 0 24 24" width="28" height="28"><defs><linearGradient id="ig" x1="0" y1="24" x2="24" y2="0"><stop offset="0%" stop-color="#feda75"/><stop offset="25%" stop-color="#fa7e1e"/><stop offset="50%" stop-color="#d62976"/><stop offset="75%" stop-color="#962fbf"/><stop offset="100%" stop-color="#4f5bd5"/></linearGradient></defs><rect x="2" y="2" width="20" height="20" rx="5" fill="url(#ig)"/><circle cx="12" cy="12" r="5" stroke="#fff" stroke-width="1.5" fill="none"/><circle cx="17.5" cy="6.5" r="1.2" fill="#fff"/></svg>
                    </div>
                    <div class="hero-float tiktok">
                        <svg viewBox="0 0 24 24" width="28" height="28"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V9.48a8.2 8.2 0 0 0 4.82 1.56V7.59a4.84 4.84 0 0 1-1.06-.9z" fill="#000"/></svg>
                    </div>
                    <div class="hero-float youtube">
                        <svg viewBox="0 0 24 24" width="28" height="28"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814z" fill="#FF0000"/><polygon points="9.545 15.568 15.818 12 9.545 8.432" fill="#fff"/></svg>
                    </div>
                    <div class="hero-float facebook">
                        <svg viewBox="0 0 24 24" width="28" height="28"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/></svg>
                    </div>
                    <div class="hero-float stats">
                        <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="#2563EB" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ 2. ARAMA / FİLTRE BARI ═══ -->
<section class="search-section" id="search">
    <div class="container">
        <div class="search-box">
            <?= icon('search', 20) ?>
            <input type="text" placeholder="Hizmet veya platform ara..." id="homeSearch" aria-label="Hizmet ara">
        </div>
    </div>
    
    <div class="platform-marquee-wrapper">
        <div class="platform-marquee-content">
            <?php 
            // Seamless scroll için içeriği 2 kez kopyalıyoruz
            for($i=0; $i<2; $i++): 
            ?>
                <?php foreach ($categories as $cat): 
                    $platform = explode('-', $cat['slug'])[0];
                    if (!\App\Services\IconService::has($platform)) $platform = $cat['icon_key'] ?? 'package';
                ?>
                <a href="/kategori/<?= e($cat['slug']) ?>" class="platform-pill">
                    <?= icon($platform, 18, "pill-icon-$platform") ?>
                    <span><?= e($cat['name']) ?></span>
                </a>
                <?php endforeach; ?>
                <a href="/kategoriler" class="platform-pill pill-all">
                    <?= icon('grid', 18) ?>
                    <span>Tüm Hizmetler</span>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</section>

<?php 
// Platform Renk/İkon Bulucu Fonksiyon (Sadece bu sayfaya özel)
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
            'gradient' => 'linear-gradient(135deg, #4285F4 0%, #34A853 50%, #FBBC05 100%)' // Google colors
        ];
        return [
            'icon' => 'box', 'color' => '#0d6efd', 
            'gradient' => 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)'
        ];
    }
}

// Paket Kartını Çizen Fonksiyon
if (!function_exists('renderHomePackageCard')) {
    function renderHomePackageCard($pkg) {
        $price = $pkg['discount_price'] && $pkg['discount_price'] < $pkg['price'] ? $pkg['discount_price'] : $pkg['price'];
        $catSlug = $pkg['category_slug'] ?? 'package';
        $design = getPlatformDesign($catSlug, $pkg['name']);
        
        ob_start();
        ?>
        <a href="/paket/<?= e($pkg['slug']) ?>" class="home-pkg-card fade-in-up">
            <div class="hpc-banner" style="background: <?= $design['gradient'] ?>;">
                <div class="hpc-icon" style="color: <?= $design['color'] ?>;">
                    <?= icon($design['icon'], 26) ?>
                </div>
                <?php if (!empty($pkg['badge'])): ?>
                <span class="hpc-badge" style="color: #fff; background: rgba(0,0,0,0.3); backdrop-filter: blur(4px);"><?= e($pkg['badge']) ?></span>
                <?php endif; ?>
            </div>
            <div class="hpc-body">
                <?php if (!empty($pkg['category_name'])): ?>
                <span class="hpc-cat"><?= e($pkg['category_name']) ?></span>
                <?php endif; ?>
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
        <?php
        return ob_get_clean();
    }
}
?>

<!-- ═══ 3. ÖNE ÇIKAN PAKETLER ═══ -->
<?php if (!empty($featuredPackages)): ?>
<section class="section" id="packages">
    <div class="container">
        <div class="section-header-row">
            <h2>Öne Çıkan Paketler</h2>
            <a href="/kategoriler" class="btn btn-outline btn-sm"><?= icon('arrow-right', 14) ?> Tümünü Gör</a>
        </div>
        <div class="home-pkg-grid">
            <?php foreach ($featuredPackages as $pkg): ?>
                <?= renderHomePackageCard($pkg) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ 3.1 DİNAMİK KATEGORİ BLOKLARI (Instagram, Web vs) ═══ -->
<?php if (!empty($homeCategoryBlocks)): ?>
    <?php foreach ($homeCategoryBlocks as $idx => $block): 
        $bg = ($idx % 2 == 0) ? 'background: var(--color-bg);' : ''; // Arka planları bir gri bir beyaz yap
    ?>
    <section class="section" style="<?= $bg ?>">
        <div class="container">
            <div class="section-header-row">
                <div>
                    <h2 style="margin: 0; font-weight: 800;"><?= e($block['category']['name']) ?></h2>
                    <?php if (!empty($block['category']['description'])): ?>
                    <p style="color: #6c757d; margin-top: 8px; font-size: 15px;"><?= e(strip_tags($block['category']['description'])) ?></p>
                    <?php endif; ?>
                </div>
                <a href="/kategori/<?= e($block['category']['slug']) ?>" class="btn btn-outline btn-sm"><?= icon('arrow-right', 14) ?> Kategoriye Git</a>
            </div>
            <div class="home-pkg-grid">
                <?php foreach ($block['packages'] as $pkg): ?>
                    <?= renderHomePackageCard($pkg) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endforeach; ?>
<?php endif; ?>

<!-- ═══ 4. NASIL ÇALIŞIR? ═══ -->
<?php if ($howItWorks): ?>
<section class="section" style="background: var(--color-bg);" id="how-it-works">
    <div class="container">
        <div class="section-header text-center">
            <h2><?= e($howItWorks['title'] ?? 'Nasıl Çalışır?') ?></h2>
            <p><?= e($howItWorks['subtitle'] ?? '4 basit adımda dijital hizmetinizi alın') ?></p>
        </div>
        <div class="steps-grid">
            <div class="step-card fade-in-up">
                <div class="step-number">1</div>
                <div class="step-icon"><?= icon('search', 24) ?></div>
                <h3>Hizmet Seç</h3>
                <p>İhtiyacınıza uygun hizmeti seçin.</p>
                <span class="step-connector"></span>
            </div>
            <div class="step-card fade-in-up">
                <div class="step-number">2</div>
                <div class="step-icon"><?= icon('credit-card', 24) ?></div>
                <h3>Ödeme Yap</h3>
                <p>Güvenli ödeme yöntemleri ile siparişinizi tamamlayın.</p>
                <span class="step-connector"></span>
            </div>
            <div class="step-card fade-in-up">
                <div class="step-number">3</div>
                <div class="step-icon"><?= icon('zap', 24) ?></div>
                <h3>Sipariş İşleme Alınsın</h3>
                <p>Siparişiniz hızlıca işleme alınır.</p>
                <span class="step-connector"></span>
            </div>
            <div class="step-card fade-in-up">
                <div class="step-number">4</div>
                <div class="step-icon"><?= icon('check-circle', 24) ?></div>
                <h3>Sonuçları Gör</h3>
                <p>Siparişiniz hazır ve güvenli bir şekilde teslim edilir.</p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ 5. GÜVEN / AVANTAJ ŞERİDİ ═══ -->
<?php if ($trust): ?>
<div class="trust-strip" id="trust">
    <div class="container">
        <div class="trust-grid">
            <div class="trust-item fade-in-up">
                <div class="trust-item-icon blue"><?= icon('users', 18) ?></div>
                <div>
                    <h4>Gerçek Kullanıcılar</h4>
                    <p>%100 gerçek kullanıcı etkileşimleri</p>
                </div>
            </div>
            <div class="trust-item fade-in-up">
                <div class="trust-item-icon green"><?= icon('shield', 18) ?></div>
                <div>
                    <h4>Güvenli ve Şeffaf</h4>
                    <p>Kişisel bilgileriniz güvende</p>
                </div>
            </div>
            <div class="trust-item fade-in-up">
                <div class="trust-item-icon amber"><?= icon('clock', 18) ?></div>
                <div>
                    <h4>Hızlı ve Zamanında</h4>
                    <p>Siparişleriniz taahhüt edilen sürede teslim edilir</p>
                </div>
            </div>
            <div class="trust-item fade-in-up">
                <div class="trust-item-icon red"><?= icon('rotate-ccw', 18) ?></div>
                <div>
                    <h4>Para İade Garantisi</h4>
                    <p>Memnun kalmazsanız, iade garantisi</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ═══ 6. POPÜLER HİZMETLER / PLATFORM BLOKLARI ═══ -->
<?php if (!empty($platformSections)): ?>
<section class="section" id="popular-services">
    <div class="container">
        <div class="section-header-row">
            <div>
                <h2>Popüler Hizmetler</h2>
                <p style="color: var(--color-text-secondary); margin-top:var(--space-1);">Markanızı büyütmek için ihtiyacınız olan tüm dijital hizmetler</p>
            </div>
            <a href="/kategoriler" class="btn btn-outline btn-sm"><?= icon('grid', 14) ?> Tüm Hizmetleri Gör</a>
        </div>
        <div class="services-grid">
            <?php foreach ($platformSections as $ps): ?>
            <a href="<?= e($ps['button_url'] ?? '/kategoriler') ?>" class="service-card fade-in-up">
                <div class="service-card-icon <?= e($ps['section_key']) ?>">
                    <?= icon($ps['icon_key'] ?? 'package', 24) ?>
                </div>
                <h3><?= e($ps['title'] ?? '') ?></h3>
                <p><?= e($ps['subtitle'] ?? '') ?></p>
                <?php if (!empty($ps['content'])): ?>
                <p class="service-card-desc"><?= e(excerpt(strip_tags($ps['content']), 120)) ?></p>
                <?php endif; ?>
                <span class="service-card-link">
                    <?= e($ps['button_text'] ?? 'Paketleri İncele') ?> <?= icon('arrow-right', 14) ?>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ 7. MÜŞTERİ YORUMLARI ═══ -->
<?php if ($testimonials && !empty($testimonials['extra'])): ?>
<section class="section" id="testimonials">
    <div class="container">
        <div class="section-header text-center">
            <h2><?= e($testimonials['title'] ?? 'Müşterilerimiz Ne Diyor?') ?></h2>
            <?php if (!empty($testimonials['subtitle'])): ?>
            <p><?= e($testimonials['subtitle']) ?></p>
            <?php endif; ?>
        </div>
        <div class="testimonial-grid">
            <?php foreach ($testimonials['extra'] as $review): ?>
            <div class="testimonial-card fade-in-up">
                <div class="testimonial-header">
                    <div class="testimonial-avatar"><?= mb_strtoupper(mb_substr($review['name'] ?? 'U', 0, 1)) ?></div>
                    <div class="testimonial-info">
                        <h4><?= e($review['name'] ?? '') ?></h4>
                        <p><?= e($review['role'] ?? '') ?></p>
                    </div>
                </div>
                <div class="testimonial-stars">
                    <?php for ($i = 0; $i < ($review['stars'] ?? 5); $i++): ?>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="#FBBF24" stroke="#FBBF24" stroke-width="1"/></svg>
                    <?php endfor; ?>
                </div>
                <blockquote><?= e($review['text'] ?? '') ?></blockquote>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ 8. BİLGİ MERKEZİ (BLOG) ═══ -->
<?php if (!empty($latestPosts)): ?>
<section class="section" style="background: var(--color-bg);" id="info-center">
    <div class="container">
        <div class="section-header-row">
            <div>
                <h2><?= e($infoCenter['title'] ?? 'Bilgi Merkezi') ?></h2>
                <p style="color: var(--color-text-secondary); margin-top:var(--space-1);"><?= e($infoCenter['subtitle'] ?? 'Dijital hizmetler hakkında merak ettikleriniz') ?></p>
            </div>
            <a href="/blog" class="btn btn-outline btn-sm"><?= icon('arrow-right', 14) ?> Tüm Rehberleri Gör</a>
        </div>
        <div class="info-grid">
            <?php foreach ($latestPosts as $post): ?>
            <a href="/blog/<?= e($post['slug']) ?>" class="info-card fade-in-up">
                <div class="info-card-icon">
                    <?php if (!empty($post['image'])): ?>
                    <img src="<?= e(upload_url($post['image'])) ?>" alt="<?= e($post['title']) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:inherit;">
                    <?php else: ?>
                    <?= icon('file-text', 20) ?>
                    <?php endif; ?>
                </div>
                <h3><?= e($post['title']) ?></h3>
                <p><?= e(excerpt(strip_tags($post['excerpt'] ?? $post['content']), 80)) ?></p>
                <span class="info-card-link">Devamını Oku <?= icon('arrow-right', 14) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ 9. SIKÇA SORULAN SORULAR ═══ -->
<?php if (!empty($faqs)): ?>
<section class="section" style="background: var(--color-bg);" id="faq">
    <div class="container">
        <div class="section-header-row">
            <h2>Sıkça Sorulan Sorular</h2>
            <a href="/sss" class="btn btn-outline btn-sm"><?= icon('arrow-right', 14) ?> Tüm soruları gör</a>
        </div>
        <div class="faq-grid">
            <?php foreach ($faqs as $faq): ?>
            <div class="faq-item">
                <div class="faq-question">
                    <span><?= e($faq['question']) ?></span>
                    <span class="faq-chevron"><?= icon('chevron-down', 18) ?></span>
                </div>
                <div class="faq-answer">
                    <p><?= $faq['answer'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ 11. SEO METİN ALANI ═══ -->
<?php if ($seoText && !empty($seoText['content'])): ?>
<section class="section" id="seo-text">
    <div class="container">
        <div class="seo-content-section">
            <?php if (!empty($seoText['title'])): ?>
            <h2><?= e($seoText['title']) ?></h2>
            <?php endif; ?>
            <div class="seo-content-body">
                <?= $seoText['content'] ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ 12. ALT CTA BANDI ═══ -->
<section class="section">
    <div class="container">
        <div class="cta-band">
            <div class="cta-band-content">
                <div class="cta-band-icon">
                    <?= icon('zap', 28) ?>
                </div>
                <div class="cta-band-text">
                    <h3><?= $ctaSection ? e($ctaSection['title'] ?? 'Hemen Sipariş Verin, Farkı Hissedin!') : 'Hemen Sipariş Verin, Farkı Hissedin!' ?></h3>
                    <p><?= $ctaSection ? e($ctaSection['subtitle'] ?? 'Markanız için doğru adım atın, dijitalde bir adım öne geçin.') : 'Markanız için doğru adım atın, dijitalde bir adım öne geçin.' ?></p>
                </div>
            </div>
            <div class="cta-band-actions">
                <a href="<?= $ctaSection ? e($ctaSection['button_url'] ?? '/kategoriler') : '/kategoriler' ?>" class="btn btn-white"><?= $ctaSection ? e($ctaSection['button_text'] ?? 'Hizmetleri Keşfet') : 'Hizmetleri Keşfet' ?></a>
                <?php if (setting('site_whatsapp')): ?>
                <a href="https://wa.me/<?= e(setting('site_whatsapp')) ?>" target="_blank" rel="noopener" class="btn btn-outline-light">
                    <?= icon('whatsapp', 16) ?> WhatsApp'tan Yaz
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
/* Modern Home Package Cards CSS */
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
