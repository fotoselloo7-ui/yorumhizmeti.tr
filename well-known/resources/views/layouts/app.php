<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? setting('default_seo_title', 'Yorum Hizmeti')) ?></title>
    <meta name="description" content="<?= e($metaDescription ?? setting('default_seo_description')) ?>">
    <?php if (!empty($canonicalUrl)): ?>
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <?php endif; ?>    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= e(upload_url(setting('site_favicon', '/assets/img/favicon.ico'))) ?>">


    <!-- Open Graph -->
    <meta property="og:type" content="<?= $ogType ?? 'website' ?>">
    <meta property="og:title" content="<?= e($ogTitle ?? $pageTitle ?? setting('default_seo_title')) ?>">
    <meta property="og:description" content="<?= e($ogDescription ?? $metaDescription ?? setting('default_seo_description')) ?>">
    <?php if (!empty($ogImage)): ?>
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <?php endif; ?>
    <meta property="og:url" content="<?= e($canonicalUrl ?? url($_SERVER['REQUEST_URI'] ?? '/')) ?>">
    <meta property="og:site_name" content="<?= e(setting('site_name', 'Yorum Hizmeti')) ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($ogTitle ?? $pageTitle ?? '') ?>">
    <meta name="twitter:description" content="<?= e($ogDescription ?? $metaDescription ?? '') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/premium.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/storefront-v4.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/home-v6.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/approved-v9.css') ?>">

    <!-- Dynamic Theme Colors -->
    <?php
    $themeMap = [
        'theme_primary'      => ['var' => '--color-blue',           'default' => '#2563EB'],
        'theme_secondary'    => ['var' => '--color-primary',        'default' => '#0F172A'],
        'theme_accent'       => ['var' => '--color-blue-light',     'default' => '#3B82F6'],
        'theme_button'       => ['var' => '--color-blue',           'default' => '#2563EB'],
        'theme_button_hover' => ['var' => '--color-blue-hover',     'default' => '#1D4ED8'],
        'theme_bg'           => ['var' => '--color-bg',             'default' => '#F8FAFC'],
        'theme_card'         => ['var' => '--color-card',           'default' => '#FFFFFF'],
        'theme_text'         => ['var' => '--color-text',           'default' => '#111827'],
        'theme_muted'        => ['var' => '--color-text-secondary', 'default' => '#64748B'],
        'theme_border'       => ['var' => '--color-border',         'default' => '#E5E7EB'],
    ];
    $overrides = [];
    foreach ($themeMap as $key => $meta) {
        $val = setting($key, '');
        if ($val && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $val) && $val !== $meta['default']) {
            $overrides[] = $meta['var'] . ':' . $val;
        }
    }
    if ($overrides): ?>
    <style>:root{<?= implode(';', $overrides) ?>}</style>
    <?php endif; ?>

    <?php if (!empty($schema)): ?>
    <script type="application/ld+json"><?= $schema ?></script>
    <?php endif; ?>

    <?= setting('header_script') ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" referrerpolicy="no-referrer">
</head>
<body>

    <div class="nv-topbar">
        <div class="container nv-topbar-inner">
            <div class="nv-topbar-main">
                <span class="nv-topbar-dot"></span>
                <span>Türkiye'nin güvenilir dijital hizmet platformu</span>
            </div>
            <div class="nv-topbar-meta">
                <span><?= icon('shield', 14) ?> Güvenli Ödeme</span>
                <span><?= icon('zap', 14) ?> Hızlı Teslimat</span>
                <span><?= icon('headphones', 14) ?> 7/24 Destek</span>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <a href="/" class="site-logo">
                    <svg class="logo-icon" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs><linearGradient id="yhLogoGrad" x1="4" y1="4" x2="32" y2="32"><stop stop-color="#2868FF"/><stop offset=".55" stop-color="#7437FF"/><stop offset="1" stop-color="#F42E91"/></linearGradient></defs>
                        <rect x="1" y="1" width="34" height="34" rx="11" fill="url(#yhLogoGrad)"/>
                        <path d="M10.5 11.5h15v10.2a2.3 2.3 0 0 1-2.3 2.3h-7.1l-4.6 3.5V24h-1a2 2 0 0 1-2-2V13.5a2 2 0 0 1 2-2Z" fill="white" fill-opacity=".96"/>
                        <path d="m14.4 17.6 2.3 2.2 5-5" stroke="url(#yhLogoGrad)" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?= e(setting('site_name', 'Yorum Hizmeti')) ?>
                </a>

                <nav class="nav-main" id="navMain">
                    <a href="/kategoriler" class="<?= isActive('/kategori') ?>">Tüm Hizmetler</a>
                    <a href="/kategori/google-hizmetleri">Google</a>
                    <a href="/kategori/instagram-hizmetleri">Instagram</a>
                    <a href="/kategori/tiktok-hizmetleri">TikTok</a>
                    <a href="/kategori/youtube-hizmetleri">YouTube</a>
                    <a href="/kategori/web-site-hizmetleri">Web Site</a>
                    <a href="/blog" class="<?= isActive('/blog') ?>">Blog</a>
                </nav>

                <form class="header-search-v4" action="/kategoriler" method="GET">
                    <?= icon('search', 13) ?>
                    <input type="search" name="q" aria-label="Hizmet ara" placeholder="Hizmet ara...">
                </form>

                <div class="header-actions">
                    <?php $cartCount = count($_SESSION['cart'] ?? []); ?>
                    <a href="/sepet" class="btn btn-cart btn-sm">
                        <?= icon('shopping-cart', 18) ?>
                        <?php if ($cartCount > 0): ?>
                            <span class="cart-badge"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>

                    <?php if (\App\Core\Auth::check()): ?>
                        <a href="/hesabim" class="btn btn-primary btn-sm">
                            <?= icon('user', 16) ?>
                            <span class="btn-label">Hesabım</span>
                        </a>
                    <?php else: ?>
                        <a href="/giris" class="btn btn-primary btn-sm">
                            <?= icon('user', 16) ?>
                            <span class="btn-label">Giriş Yap</span>
                        </a>
                    <?php endif; ?>

                    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menü">
                        <?= icon('menu', 24) ?>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if (!empty($flash)): ?>
    <div class="container" style="padding-top: var(--space-4);">
        <?php foreach ($flash as $type => $message): ?>
            <div class="alert alert-<?= $type === 'error' ? 'error' : ($type === 'warning' ? 'warning' : 'success') ?>">
                <?= icon($type === 'error' ? 'alert-circle' : ($type === 'warning' ? 'alert-triangle' : 'check-circle'), 18) ?>
                <span><?= e($message) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Content -->
    <?= $content ?>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h3><?= e(setting('site_name', 'Yorum Hizmeti')) ?></h3>
                    <p><?= e(setting('site_slogan', '')) ?></p>
                    <p style="margin-top: 12px;">
                        <?php if (setting('site_email')): ?>
                            <?= icon('mail', 14) ?> <?= e(setting('site_email')) ?><br>
                        <?php endif; ?>
                        <?php if (setting('site_phone')): ?>
                            <?= icon('phone', 14) ?> <?= e(setting('site_phone')) ?>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="footer-col">
                    <h4>Hizmetler</h4>
                    <?php
                    try {
                        $footerCats = \App\Core\Database::getInstance()->fetchAll("SELECT name, slug FROM categories WHERE status = 'active' ORDER BY sort_order LIMIT 6");
                        foreach ($footerCats as $fc):
                    ?>
                        <a href="/kategori/<?= e($fc['slug']) ?>"><?= e($fc['name']) ?></a>
                    <?php endforeach; } catch(\Exception $e) {} ?>
                </div>

                <div class="footer-col">
                    <h4>Bilgi</h4>
                    <a href="/sayfa/hakkimizda">Hakkımızda</a>
                    <a href="/blog">Blog</a>
                    <a href="/sss">Sıkça Sorulan Sorular</a>
                    <a href="/iletisim">İletişim</a>
                </div>

                <div class="footer-col">
                    <h4>Yasal</h4>
                    <a href="/sayfa/gizlilik-politikasi">Gizlilik Politikası</a>
                    <a href="/sayfa/mesafeli-satis-sozlesmesi">Mesafeli Satış Sözleşmesi</a>
                    <a href="/sayfa/kvkk">KVKK</a>
                    <a href="/sayfa/iade-teslimat-politikasi">İade Politikası</a>
                </div>
            </div>

            <div class="footer-newsletter-v4">
                <div>
                    <span><?= icon('mail', 18) ?></span>
                    <div><strong>Fırsatları kaçırmayın</strong><small>Yeni hizmet ve kampanyalardan haberdar olun.</small></div>
                </div>
                <div class="footer-newsletter-form">
                    <input type="email" aria-label="E-posta" placeholder="E-posta adresiniz">
                    <button type="button" aria-label="Gönder"><?= icon('arrow-right', 14) ?></button>
                </div>
                <div class="footer-payments-v4"><span>VISA</span><span>Mastercard</span><span>TROY</span><span><?= icon('lock', 12) ?> SSL</span></div>
            </div>

            <div class="footer-bottom">
                <span><?= e(setting('footer_text', '© ' . date('Y') . ' Yorum Hizmeti')) ?></span>
                <div class="footer-social">
                    <?php if (setting('social_instagram')): ?>
                        <a href="<?= e(setting('social_instagram')) ?>" target="_blank" rel="noopener" aria-label="Instagram"><?= icon('instagram', 18) ?></a>
                    <?php endif; ?>
                    <?php if (setting('social_youtube')): ?>
                        <a href="<?= e(setting('social_youtube')) ?>" target="_blank" rel="noopener" aria-label="YouTube"><?= icon('youtube', 18) ?></a>
                    <?php endif; ?>
                    <?php if (setting('social_twitter')): ?>
                        <a href="<?= e(setting('social_twitter')) ?>" target="_blank" rel="noopener" aria-label="X"><?= icon('twitter', 18) ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Contact (Desktop) -->
    <div class="floating-contact">
        <?php if (setting('whatsapp_number')): ?>
        <a href="https://wa.me/<?= e(ltrim(str_replace(['+', ' '], '', setting('whatsapp_number')), '0')) ?>" class="whatsapp" target="_blank" rel="noopener" aria-label="WhatsApp">
            <?= icon('whatsapp', 22) ?>
        </a>
        <?php endif; ?>
        <?php if (setting('site_phone')): ?>
        <a href="tel:<?= e(setting('site_phone')) ?>" class="phone-btn" aria-label="Telefon">
            <?= icon('phone', 20) ?>
        </a>
        <?php endif; ?>
    </div>

    <!-- Mobile Bottom Nav -->
    <nav class="mobile-bottom-nav">
        <div class="nav-inner">
            <a href="/" class="<?= ($_SERVER['REQUEST_URI'] ?? '') === '/' ? 'active' : '' ?>">
                <?= icon('home', 20) ?>
                <span>Ana Sayfa</span>
            </a>
            <a href="/kategoriler" class="<?= isActive('/kategori') ?>">
                <?= icon('grid', 20) ?>
                <span>Hizmetler</span>
            </a>
            <a href="/sepet" class="<?= isActive('/sepet') ?>">
                <?= icon('shopping-cart', 20) ?>
                <span>Sepetim</span>
                <?php if ($cartCount > 0): ?>
                    <span class="nav-badge-sm"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
            <a href="/destek" class="<?= isActive('/destek') ?>">
                <?= icon('headphones', 20) ?>
                <span>Destek</span>
            </a>
            <a href="<?= \App\Core\Auth::check() ? '/hesabim' : '/giris' ?>" class="<?= isActive('/hesabim') || isActive('/giris') ? 'active' : '' ?>">
                <?= icon('user', 20) ?>
                <span>Hesabım</span>
            </a>
        </div>
    </nav>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script src="<?= asset('js/icon-bridge.js') ?>"></script>
    <?= setting('footer_script') ?>
</body>
</html>
