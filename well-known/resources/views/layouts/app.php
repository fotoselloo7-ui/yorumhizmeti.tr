<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? setting('default_seo_title', 'NetVera Teknoloji Yazılım')) ?></title>
    <meta name="description" content="<?= e($metaDescription ?? setting('default_seo_description')) ?>">
    <?php
      $nvRobots = (string)($nvSeoData['robots'] ?? '');
      if ($nvRobots === '' && !empty($noindex)) $nvRobots = 'noindex,follow';
    ?>
    <?php if ($nvRobots !== ''): ?><meta name="robots" content="<?= e($nvRobots) ?>"><?php endif; ?>
    <?php if (!empty($nvSeoData['author_name'])): ?><meta name="author" content="<?= e($nvSeoData['author_name']) ?>"><?php endif; ?>
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
    <meta property="og:site_name" content="<?= e(setting('site_name', 'NetVera Teknoloji Yazılım')) ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($ogTitle ?? $pageTitle ?? '') ?>">
    <meta name="twitter:description" content="<?= e($ogDescription ?? $metaDescription ?? '') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" referrerpolicy="no-referrer">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/premium.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/storefront-v4.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/home-v6.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/approved-v9.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/visual-consistency-v10.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/visual-fidelity-v11.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/visual-demo-v12.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive-fluid-v13.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive-balanced-v14.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/category-brand-v15.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/typography-readability-v16.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/section-rhythm-v17.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/featured-footer-v18.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive-repair-v19.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/forms-functional-v20.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/catalog-bridge-v21.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/motion-v22.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/premium-section-footer-v23.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/featured-groups-v24.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/featured-software-v41.css') ?>?v=41.1">
    <link rel="stylesheet" href="<?= asset('css/agency-navigation-v26.css') ?>?v=26.2">
    <link rel="stylesheet" href="<?= asset('css/agency-catalog-v26.css') ?>?v=26.2">
    <link rel="stylesheet" href="<?= asset('css/agency-mega-premium-v27.css') ?>?v=27.1">
    <link rel="stylesheet" href="<?= asset('css/icon-alignment-v28.css') ?>?v=28.1">
    <link rel="stylesheet" href="<?= asset('css/home-service-groups-v29.css') ?>?v=29.2">
    <link rel="stylesheet" href="<?= asset('css/premium-trust-steps-v30.css') ?>?v=30.1">
    <link rel="stylesheet" href="<?= asset('css/software-showcase-v31.css') ?>?v=32.1">
    <link rel="stylesheet" href="<?= asset('css/software-navigation-v33.css') ?>?v=35.1">
    <link rel="stylesheet" href="<?= asset('css/software-marketplace-v36.css') ?>?v=36.1">
    <link rel="stylesheet" href="<?= asset('css/netvera-legacy-premium-v40.css') ?>?v=40.1">
    <link rel="stylesheet" href="<?= asset('css/netvera-product-detail-v60.css') ?>?v=61.2">
    <link rel="stylesheet" href="<?= asset('css/netvera-inquiries.css') ?>?v=1">
    <link rel="stylesheet" href="<?= asset('css/netvera-article-v42.css') ?>?v=42.1">
    <link rel="stylesheet" href="<?= asset('css/featured-category-slider-v43.css') ?>?v=43.1">
    <link rel="stylesheet" href="<?= asset('css/home-blog-covers-v45.css') ?>?v=45.1">
    <link rel="stylesheet" href="<?= asset('css/home-service-alignment-v45.css') ?>?v=45.1">
    <link rel="stylesheet" href="<?= asset('css/home-faq-final-v47.css') ?>?v=47.1">
    <link rel="stylesheet" href="<?= asset('css/home-footer-motion-v49.css') ?>?v=50.1">
    <link rel="stylesheet" href="<?= asset('css/reference-portfolio-v51.css') ?>?v=51.3">
    <link rel="stylesheet" href="<?= asset('css/reference-player-v52.css') ?>?v=54.1">
    <link rel="stylesheet" href="<?= asset('css/premium-hover-v55.css') ?>?v=55.1">
    <link rel="stylesheet" href="<?= asset('css/nav-quick-marquee-v56.css') ?>?v=56.1">
    <link rel="stylesheet" href="<?= asset('css/netvera-account-history-v57.css') ?>?v=57.1">
    <link rel="stylesheet" href="<?= asset('css/netvera-dealer-v62.css') ?>?v=62.1">
    <link rel="stylesheet" href="<?= asset('css/package-benefits-v67.css') ?>?v=67.1">
    <link rel="stylesheet" href="<?= asset('css/reviews-v68.css') ?>?v=68.1">

    <link rel="stylesheet" href="<?= asset('css/theme-runtime-v71.css') ?>?v=71.1">
    <?php
      $nvThemeEnabled=setting('theme_preset_enabled','0')==='1';
      $nvPalette=[
        'primary'=>['theme_primary','#6B4DE8'],
        'secondary'=>['theme_secondary','#13254B'],
        'accent'=>['theme_accent','#D936A1'],
        'button'=>['theme_button','#6B4DE8'],
        'hover'=>['theme_button_hover','#5136D2'],
        'bg'=>['theme_bg','#FFFFFF'],
        'card'=>['theme_card','#FFFFFF'],
        'text'=>['theme_text','#17264E'],
        'muted'=>['theme_muted','#70809C'],
        'border'=>['theme_border','#E2E6F3'],
      ];
      $nvPaletteVars=[];
      if($nvThemeEnabled){
        foreach($nvPalette as $label=>$metadata){
          $val=(string)setting($metadata[0],$metadata[1]);
          if(!preg_match('/^#[A-Fa-f0-9]{6}$/D',$val))$val=$metadata[1];
          $nvPaletteVars[]='--nv-theme-'.$label.':'.$val;
        }
      }
    ?>
    <?php if($nvPaletteVars): ?><style>:root{<?= implode(';',$nvPaletteVars) ?>}</style><?php endif; ?>

    <?php
      $nvSemantic = null;
      if (!empty($nvSeoData['geo_summary']) || !empty($nvSeoData['entity_topics']) ||
          !empty($nvSeoData['service_area'])) {
        $nvSemantic = [
          '@context'=>'https://schema.org',
          '@type'=>'WebPage',
          'name'=>$pageTitle ?? '',
          'url'=>$canonicalUrl ?? url($_SERVER['REQUEST_URI'] ?? '/')
        ];
        if (!empty($nvSeoData['geo_summary'])) $nvSemantic['description'] = $nvSeoData['geo_summary'];
        $topics = array_filter(array_map('trim', explode(',', (string)($nvSeoData['entity_topics']??''))));
        if ($topics) $nvSemantic['about'] = array_map(static fn($t)=>['@type'=>'Thing','name'=>$t],array_slice(array_values($topics),0,15));
        if (!empty($nvSeoData['service_area'])) $nvSemantic['spatialCoverage'] = $nvSeoData['service_area'];
        if (!empty($nvSeoData['author_name']))
          $nvSemantic['author'] = [
            '@type'=>($nvSeoData['author_type']??'')==='Person'?'Person':'Organization',
            'name'=>$nvSeoData['author_name']
          ];
        $sameAs = array_filter(array_map('trim', preg_split('/\R/', (string)($nvSeoData['same_as_urls']??''))?:[]));
        if($sameAs) $nvSemantic['sameAs']=array_values($sameAs);
      }
    ?>
    <?php if ($nvSemantic): ?>
    <script type="application/ld+json"><?= json_encode($nvSemantic, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?></script>
    <?php endif; ?>
    <?php if (!empty($schema)): ?>
    <script type="application/ld+json"><?= $schema ?></script>
    <?php endif; ?>

    <?= setting('header_script') ?>
</head>
<body<?= $nvThemeEnabled?' class="nv-theme-enabled"':'' ?>>

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
                    <?= e(setting('site_name', 'NetVera Teknoloji Yazılım')) ?>
                </a>

                <?php
                $nv26NavItems = \App\Services\NavigationService::items();
                $nv26Groups = \App\Services\CatalogMenuService::groups();
                $nv26NavPrefs = [];
                $nv26ExtraLinks = [];
                $nv26QuickLinks = [];
                foreach ($nv26NavItems as $navItem) {
                    $nv26NavPrefs[$navItem['key']] = $navItem;
                    if (str_starts_with($navItem['key'], 'category_')) $nv26QuickLinks[] = $navItem;
                    elseif (!str_starts_with($navItem['key'], 'group_')) $nv26ExtraLinks[] = $navItem;
                }
                // Respect the custom order configured in Admin > Menü Yönetimi.
                usort($nv26Groups, static fn(array $a, array $b): int =>
                    (($nv26NavPrefs['group_'.$a['key']]['sort'] ?? 9999)
                      <=> ($nv26NavPrefs['group_'.$b['key']]['sort'] ?? 9999))
                );
                ?>
                <div class="nv26-header-context">
                    <span><?= icon('shield-check', 14) ?> Güvenli dijital hizmetler</span>
                </div>

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

    <!-- Responsive two-level agency navigation: all links originate in live active admin categories. -->
    <nav class="nv26-menu-shell" id="navMain" aria-label="Hizmetler menüsü">
      <div class="container nv26-menu-container">
        <div class="nv26-menu-bar">
          <div class="nv26-menu-groups">
            <?php foreach ($nv26Groups as $nv26Group):
                $nv26Key = 'group_' . $nv26Group['key'];
                if (!isset($nv26NavPrefs[$nv26Key])) continue;
                $nv26Label = $nv26NavPrefs[$nv26Key]['label'];
                $nv26Cols = array_chunk($nv26Group['categories'], max(1, (int)ceil(count($nv26Group['categories']) / 4)));
            ?>
            <div class="nv26-menu-entry" data-mega-entry>
              <button type="button" class="nv26-menu-trigger" data-mega-trigger
                aria-expanded="false" aria-controls="nv26-panel-<?= e($nv26Group['key']) ?>">
                <?= icon($nv26Group['icon'], 16) ?>
                <span><?= e($nv26Label) ?></span>
                <?= icon('chevron-down', 12) ?>
              </button>
              <section class="nv26-mega-panel <?= $nv26Group['key']==='social'?'':'nv27-pro-panel nv27-pro-panel--'.e($nv26Group['key']) ?>" id="nv26-panel-<?= e($nv26Group['key']) ?>" data-mega-panel hidden aria-label="<?= e($nv26Label) ?>">
                <div class="nv26-mega-top">
                  <div><strong><?= e($nv26Label) ?></strong><p><?= e($nv26Group['description']) ?></p></div>
                  <a href="/kategoriler?grup=<?= e($nv26Group['key']) ?>"><?= e($nv26Group['short']) ?> kategorilerini keşfet <?= icon('arrow-right', 13) ?></a>
                </div>
                <?php if ($nv26Group['key'] === 'social'): ?>
                <div class="nv26-social-mega-grid" aria-label="Sosyal medya platformları">
                  <?php foreach ($nv26Group['categories'] as $nv26Cat): ?>
                  <a href="<?= e($nv26Cat['url']) ?>" class="nv26-social-mega-card nv26-social-<?= e($nv26Cat['style']) ?>">
                    <span class="nv26-social-mega-icon"><?= icon($nv26Cat['icon'], 27) ?></span>
                    <strong><?= e($nv26Cat['name']) ?></strong>
                    <small><?= count($nv26Cat['children']) ? count($nv26Cat['children']).' alt hizmet' : 'Hizmetleri gör' ?></small>
                  </a>
                  <?php endforeach; ?>
                </div>
                <?php else: ?>
                <?php $nv27Agency = ($nv26Group['key'] === 'agency'); ?>
                <div class="nv27-service-layout nv27-service-layout--<?= e($nv26Group['key']) ?>">
                  <aside class="nv27-service-spotlight" aria-label="<?= e($nv26Label) ?> kategorileri">
                    <div class="nv27-spotlight-top">
                      <span class="nv27-spotlight-badge"><?= icon($nv27Agency ? 'layers' : 'trending-up', 15) ?> <?= $nv27Agency?'Dijital ajans çözümleri':'Dijital büyüme çözümleri' ?></span>
                      <div class="nv27-spotlight-glyph" aria-hidden="true">
                        <?= icon($nv27Agency ? 'monitor' : 'bar-chart', 58) ?>
                      </div>
                      <strong><?= $nv27Agency?'Fikrinizden dijital ürüne.':'Dijitalde daha görünür olun.' ?></strong>
                      <p><?= $nv27Agency?'Web sitesi, uygulama, tasarım ve içerik hizmetlerini ihtiyacınıza göre keşfedin.':'SEO, reklam ve yerel işletme hizmetlerini tek noktadan inceleyin.' ?></p>
                    </div>
                    <a href="/kategoriler?grup=<?= e($nv26Group['key']) ?>" class="nv27-spotlight-cta">
                      Tüm kategorileri gör <?= icon('arrow-right', 14) ?>
                    </a>
                  </aside>
                  <div class="nv27-service-main">
                    <div class="nv27-service-intro">
                      <div><span class="nv27-service-overline"><?= icon('grid',13) ?> HİZMET KATALOĞU</span><strong><?= e($nv26Label) ?></strong></div>
                      <span class="nv27-service-count"><?= count($nv26Group['categories']) + ($nv27Agency && !in_array(\App\Services\SoftwareCatalogService::ROOT_SLUG, array_column($nv26Group['categories'], 'slug'), true) ? 1 : 0) ?> kategori</span>
                    </div>
                    <div class="nv27-service-grid">
                      <?php foreach ($nv26Group['categories'] as $nv26Cat):
                        if ($nv27Agency && ($nv26Cat['slug'] ?? '') === \App\Services\SoftwareCatalogService::ROOT_SLUG) continue;
                          $nv27ChildCount = count($nv26Cat['children']);
                          $nv27Description = trim(strip_tags((string)($nv26Cat['description'] ?? '')));
                          $nv27Description = $nv27Description !== ''
                              ? mb_strimwidth($nv27Description, 0, 75, '…', 'UTF-8')
                              : ($nv27ChildCount > 0 ? $nv27ChildCount.' alt hizmet ve paket seçeneği' : 'Hizmet seçeneklerini keşfedin');
                      ?>
                      <article class="nv27-service-card nv27-style-<?= e($nv26Cat['style']) ?>">
                        <a href="<?= e($nv26Cat['url']) ?>" class="nv27-service-parent">
                          <span class="nv27-service-icon"><?= icon($nv26Cat['icon'], 23) ?></span>
                          <span class="nv27-service-parent-copy">
                            <strong><?= e($nv26Cat['name']) ?></strong>
                            <small><?= e($nv27Description) ?></small>
                          </span>
                          <span class="nv27-service-parent-arrow"><?= icon('arrow-up-right', 14) ?></span>
                        </a>
                        <?php if ($nv27ChildCount > 0): ?>
                        <div class="nv27-subcategory-area">
                          <span class="nv27-subcategory-caption">ALT HİZMETLER</span>
                          <div class="nv27-subcategory-grid">
                            <?php foreach (array_slice($nv26Cat['children'], 0, 4) as $nv26Sub): ?>
                            <a href="<?= e($nv26Sub['url']) ?>" class="nv27-subcategory-link"
                               title="<?= e($nv26Sub['name']) ?>">
                              <span class="nv27-subcategory-symbol"><?= icon($nv26Sub['icon'], 13) ?></span>
                              <span><?= e($nv26Sub['name']) ?></span>
                            </a>
                            <?php endforeach; ?>
                          </div>
                        </div>
                        <?php else: ?>
                        <p class="nv27-category-empty">Kategorideki paket ve hizmetleri inceleyin.</p>
                        <?php endif; ?>
                        <a href="<?= e($nv26Cat['url']) ?>" class="nv27-category-footer">
                          <?= $nv27ChildCount > 4 ? 'Tüm '.$nv27ChildCount.' alt hizmeti keşfet' : 'Kategori paketlerini incele' ?>
                          <?= icon('arrow-right', 12) ?>
                        </a>
                      </article>
                      <?php endforeach; ?>
                      <?php if ($nv27Agency): ?>
                      <?php
                        // Use the real public Netvera script catalogue, with no demo categories.
                        // Preserve the pre-existing service-package page as a secondary link.
                        $nv35NetveraCategories = \App\Services\NetveraBridgeService::categories();
                        $nv35RealSoftwareCount = count(\App\Services\NetveraBridgeService::all());
                      ?>
                      <article class="nv27-service-card nv27-style-software nv35-software-card" data-software-menu-card>
                        <a href="/hazir-scriptler" class="nv27-service-parent">
                          <span class="nv27-service-icon"><?= icon('monitor', 23) ?></span>
                          <span class="nv27-service-parent-copy">
                            <strong>Hazır Yazılımlar &amp; Scriptler</strong>
                            <small>Sektöre özel yönetim panelli yazılımlar</small>
                          </span>
                          <span class="nv27-service-parent-arrow"><?= icon('arrow-up-right', 14) ?></span>
                        </a>
                        <div class="nv27-subcategory-area">
                          <span class="nv27-subcategory-caption">YAZILIM KATEGORİLERİ</span>
                          <div class="nv27-subcategory-grid">
                            <?php foreach ($nv35NetveraCategories as $nv35Category): ?>
                            <a class="nv27-subcategory-link" href="/hazir-scriptler?category=<?= rawurlencode((string)$nv35Category['slug']) ?>" title="<?= e($nv35Category['name']) ?>">
                              <span class="nv27-subcategory-symbol"><?= icon('folder', 13) ?></span>
                              <span><?= e($nv35Category['name']) ?></span>
                            </a>
                            <?php endforeach; ?>
                            <a class="nv27-subcategory-link" href="/hazir-yazilimlar" title="Yazılım hizmet paketleri">
                              <span class="nv27-subcategory-symbol"><?= icon('layers', 13) ?></span>
                              <span>Yazılım Hizmet Paketleri</span>
                            </a>
                          </div>
                        </div>
                        <a href="/hazir-scriptler" class="nv27-category-footer">
                          <?= $nv35RealSoftwareCount ?> gerçek yazılımı incele <?= icon('arrow-right', 12) ?>
                        </a>
                      </article>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <?php endif; ?>
              </section>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="nv26-menu-end">
            <?php foreach ($nv26ExtraLinks as $navItem): ?>
              <a class="nv26-simple-link <?= isActive($navItem['url']) ? 'active':'' ?>" href="<?= e($navItem['url']) ?>"><?= e($navItem['label']) ?></a>
            <?php endforeach; ?>
            <?php
              // Active live categories, categorized exactly like the main mega menu.
              // Prefer the administrator's enabled custom quick links when present.
              $nv56QuickGroups = [];
              $nv56Visible = array_fill_keys(array_map(static fn($item) => (string)$item['url'], $nv26QuickLinks), true);
              foreach ($nv26Groups as $nv56Group) {
                  $nv56Choices = [];
                  foreach ($nv56Group['categories'] as $nv56Cat) {
                      $nv56Link = (string)($nv56Cat['url'] ?? '');
                      if ($nv56Link === '') continue;
                      // If the admin explicitly configured shortcuts for this group,
                      // show only enabled shortcuts; otherwise choose popular live roots.
                      $nv56Choices[] = [
                          'label' => (string)$nv56Cat['name'],
                          'url' => $nv56Link,
                          'icon' => (string)($nv56Cat['icon'] ?? 'package'),
                          'explicit' => isset($nv56Visible[$nv56Link])
                      ];
                  }
                  usort($nv56Choices, static fn($a,$b) => ((int)$b['explicit'] <=> (int)$a['explicit']));
                  if ($nv56Choices) {
                      $nv56QuickGroups[] = [
                          'key' => (string)$nv56Group['key'],
                          'label' => (string)$nv56Group['short'],
                          'icon' => (string)$nv56Group['icon'],
                          'links' => array_slice($nv56Choices,0,4)
                      ];
                  }
              }
              $nv56Actions = [
                 ['url' => '/siparislerim', 'label' => 'Siparişlerim', 'icon' => 'package'],
                 ['url' => '/destek', 'label' => 'Destek Merkezi', 'icon' => 'headphones'],
                 ['url' => '/hazir-scriptler', 'label' => 'Hazır Yazılımlar', 'icon' => 'monitor']
              ];
            ?>
            <details class="nv26-quick nv56-quick">
              <summary aria-label="Hızlı erişim menüsünü aç">
                <?= icon('grid', 14) ?> Hızlı Erişim <?= icon('chevron-down', 11) ?>
              </summary>
              <div class="nv26-quick-list nv56-quick-panel">
                <div class="nv56-quick-top">
                    <strong><?= icon('zap', 16) ?> Sık Kullanılan İşlemler</strong>
                    <small>Hizmet, sipariş ve desteğe tek tıkla ulaşın.</small>
                </div>
                <div class="nv56-quick-actions" aria-label="Hızlı işlemler">
                  <?php foreach ($nv56Actions as $nv56Action): ?>
                  <a href="<?= e($nv56Action['url']) ?>" class="nv56-quick-action">
                    <?= icon($nv56Action['icon'],16) ?><span><?= e($nv56Action['label']) ?></span><?= icon('arrow-up-right',12) ?>
                  </a>
                  <?php endforeach; ?>
                </div>
                <div class="nv56-quick-groups">
                  <?php foreach ($nv56QuickGroups as $nv56Group): ?>
                  <section class="nv56-quick-group" aria-label="<?= e($nv56Group['label']) ?>">
                    <h3><?= icon($nv56Group['icon'],14) ?> <?= e($nv56Group['label']) ?></h3>
                    <?php foreach ($nv56Group['links'] as $nv56Link): ?>
                    <a href="<?= e($nv56Link['url']) ?>" class="nv56-quick-category">
                      <?= icon($nv56Link['icon'],14) ?><span><?= e($nv56Link['label']) ?></span>
                    </a>
                    <?php endforeach; ?>
                  </section>
                  <?php endforeach; ?>
                </div>
                <a class="nv56-quick-all" href="/kategoriler">Tüm aktif kategorileri görüntüle <?= icon('arrow-right',14) ?></a>
              </div>
            </details>
          </div>
        </div>
      </div>
    </nav>

    <script src="<?= asset('js/software-marketplace-v36.js') ?>?v=36.1" defer></script>
    <script src="<?= asset('js/netvera-product-tabs-v60.js') ?>?v=61.1" defer></script>
    <script src="<?= asset('js/category-marquee-drag-v56.js') ?>?v=56.2" defer></script>

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
            <?php
            // The site footer uses the same published category hierarchy as the mega menu.
            $footerServices = [];
            $footerScripts = [];
            try {
                foreach (\App\Services\CatalogMenuService::groups() as $footerGroup) {
                    foreach (array_slice($footerGroup['categories'], 0, 3) as $footerCat) {
                        $footerServices[] = ['name'=>$footerCat['name'], 'url'=>$footerCat['url']];
                    }
                }
                $footerProducts = \App\Services\NetveraBridgeService::all();
                if ($footerProducts) {
                    foreach (\App\Services\NetveraBridgeService::categories() as $footerCategory) {
                        $footerCategoryId = (int)($footerCategory['legacy_id'] ?? 0);
                        $footerHasProduct = false;
                        foreach ($footerProducts as $footerProduct) {
                            if ((int)($footerProduct['category_legacy_id'] ?? 0) === $footerCategoryId) {
                                $footerHasProduct = true;
                                break;
                            }
                        }
                        if (!$footerHasProduct) continue;
                        $footerScripts[] = [
                            'name' => $footerCategory['name'],
                            'url' => '/hazir-scriptler?category='.rawurlencode((string)$footerCategory['slug'])
                        ];
                        if (count($footerScripts) >= 5) break;
                    }
                }
            } catch (\Throwable $e) {
                error_log('Footer public catalog lookup failed.');
            }
            ?>
            <div class="footer-grid footer-grid-v9 yh49-footer-grid">
                <div class="footer-brand">
                    <h3><?= e(setting('site_name','NetVera Teknoloji Yazılım')) ?></h3>
                    <p><?= e(setting('site_slogan', 'Sosyal medya etkileşim hizmetlerinden Google yorumlarına, web ve dijital çözümlere kadar güvenilir hizmet ortağınız.')) ?></p>
                    <div class="footer-social footer-social-v9">
                        <?php if (setting('social_instagram')): ?><a href="<?= e(setting('social_instagram')) ?>" target="_blank" rel="noopener" aria-label="Instagram"><?= icon('instagram', 15) ?></a><?php endif; ?>
                        <?php if (setting('social_youtube')): ?><a href="<?= e(setting('social_youtube')) ?>" target="_blank" rel="noopener" aria-label="YouTube"><?= icon('youtube', 15) ?></a><?php endif; ?>
                        <?php if (setting('social_twitter')): ?><a href="<?= e(setting('social_twitter')) ?>" target="_blank" rel="noopener" aria-label="X"><?= icon('twitter', 15) ?></a><?php endif; ?>
                    </div>
                </div>

                <nav class="footer-col" aria-label="Hizmet kategorileri">
                    <h4>Hizmet Kategorileri</h4>
                    <?php foreach (array_slice($footerServices, 0, 8) as $footerItem): ?>
                    <a class="yh49-footer-link" href="<?= e($footerItem['url']) ?>"><?= e($footerItem['name']) ?></a>
                    <?php endforeach; ?>
                </nav>

                <nav class="footer-col" aria-label="Hazır yazılımlar">
                    <h4>Hazır Yazılımlar</h4>
                    <a class="yh49-footer-link" href="/hazir-scriptler">Tüm Hazır Yazılımlar</a>
                    <?php foreach ($footerScripts as $footerItem): ?>
                    <a class="yh49-footer-link" href="<?= e($footerItem['url']) ?>"><?= e($footerItem['name']) ?></a>
                    <?php endforeach; ?>
                    <a class="yh49-footer-link" href="/hazir-yazilimlar">Yazılım Hizmet Paketleri</a>
                    <a class="yh49-footer-all" href="/hazir-scriptler">Scriptleri Keşfet <?= icon('arrow-up-right',12) ?></a>
                </nav>

                <nav class="footer-col" aria-label="Kurumsal ve destek">
                    <h4>Kurumsal & Destek</h4>
                    <a class="yh49-footer-link" href="/sayfa/hakkimizda">Hakkımızda</a>
                    <a class="yh49-footer-link" href="/blog">Blog</a>
                    <a class="yh49-footer-link" href="/sss">Sıkça Sorulan Sorular</a>
                    <a class="yh49-footer-link" href="/destek">Destek Merkezi</a>
                    <a class="yh49-footer-link" href="/destek/yeni">Destek Talebi Oluştur</a>
                    <a class="yh49-footer-link" href="/sayfa/gizlilik-politikasi">Gizlilik Politikası</a>
                    <a class="yh49-footer-link" href="/sayfa/kvkk">KVKK</a>
                </nav>

                <div class="footer-col footer-contact-v9">
                    <h4>İletişim</h4>
                    <?php if (setting('site_phone')): ?><a href="tel:<?= e(setting('site_phone')) ?>"><?= icon('phone', 12) ?> <?= e(setting('site_phone')) ?></a><?php endif; ?>
                    <?php if (setting('site_email')): ?><a href="mailto:<?= e(setting('site_email')) ?>"><?= icon('mail', 12) ?> <?= e(setting('site_email')) ?></a><?php endif; ?>
                    <?php if (setting('whatsapp_number')): ?><a href="https://wa.me/<?= e(ltrim(str_replace(['+', ' '], '', setting('whatsapp_number')), '0')) ?>" target="_blank" rel="noopener"><?= icon('whatsapp', 12) ?> WhatsApp Destek</a><?php endif; ?>
                    <span><?= icon('headphones', 12) ?> 7/24 Canlı Destek</span>
                </div>

                <div class="footer-col footer-subscribe-v9" id="newsletter">
                    <h4>E-Bülten</h4>
                    <p>Kampanya ve yeniliklerden haberdar olun.</p>
                    <?php if(!empty($flash['newsletter_success'])): ?><div class="yv-form-feedback success" role="status"><?= e($flash['newsletter_success']) ?></div><?php endif; ?>
                    <?php if(!empty($flash['newsletter_error'])): ?><div class="yv-form-feedback error" role="alert"><?= e($flash['newsletter_error']) ?></div><?php endif; ?>
                    <form method="POST" action="/bulten/kayit" class="footer-newsletter-signup">
                        <?= csrfField() ?>
                        <input type="hidden" name="source" value="footer">
                        <input type="hidden" name="return_to" value="<?= str_starts_with($_SERVER['REQUEST_URI']??'', '/blog') ? '/blog' : '/' ?>">
                        <div class="yv-honeypot" aria-hidden="true"><label>Web sitesi<input name="website_url" type="text" tabindex="-1" autocomplete="off"></label></div>
                        <div class="footer-newsletter-form">
                            <input type="email" name="email" aria-label="E-posta" placeholder="E-posta adresiniz" autocomplete="email" required>
                            <button type="submit" aria-label="Bültene kaydol"><?= icon('arrow-right', 12) ?></button>
                        </div>
                        <label class="yv-opt-in footer-opt-in"><input type="checkbox" name="newsletter_consent" value="1" required><span><a href="/sayfa/kvkk" target="_blank" rel="noopener">KVKK bilgilendirmesini</a> okudum; bülten almak istiyorum.</span></label>
                    </form>
                </div>
            </div>

            <div class="footer-trust-v9">
                <?php
                // Payment methods are verified by checkout; the footer makes no availability claims.
                 $bankGatewayEnabled = false;
                 try {
                     $bankGatewayEnabled = (bool)\App\Core\Database::getInstance()->fetch(
                         "SELECT id FROM payment_gateways WHERE gateway_key = 'bank_transfer' AND is_active = 1 LIMIT 1"
                     );
                 } catch (\Throwable $e) {
                     error_log('Footer bank method lookup failed.');
                 }
                ?>
                <div class="footer-payments-v4 footer-payment-logos footer-payment-visual-v23" aria-label="Ödeme sistemi logoları">
                    <div class="footer-payment-heading-v23">
                        <strong>Ödeme Yöntemleri</strong>
                        <small>Desteklenen kart ağları ve ödeme altyapısı</small>
                    </div>
                    <div class="footer-payment-brands-v23" aria-label="Kart markaları">
                        <span class="footer-payment-mark" title="Visa">
                            <img src="<?= asset('img/payments/visa.svg') ?>" alt="Visa" width="80" height="48" loading="lazy">
                        </span>
                        <span class="footer-payment-mark" title="Mastercard">
                            <img src="<?= asset('img/payments/mastercard.svg') ?>" alt="Mastercard" width="80" height="48" loading="lazy">
                        </span>
                        <span class="footer-payment-mark" title="TROY">
                            <img src="<?= asset('img/payments/troy.svg') ?>" alt="TROY" width="80" height="48" loading="lazy">
                        </span>
                        <?php if($bankGatewayEnabled): ?>
                            <div class="footer-payment-bank" title="Banka havalesi ve EFT">
                                <span class="yh50-bank-inline"><?= icon('landmark', 16) ?><b>Havale / EFT</b></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="footer-payment-info-v23">
                        <span class="footer-payment-security"><?= icon('shield', 15) ?> Güvenli işlem</span>
                    </div>
                </div>
                <div class="footer-legal-v9"><a href="/sayfa/gizlilik-politikasi">Gizlilik Politikası</a><a href="/sayfa/mesafeli-satis-sozlesmesi">Kullanım Şartları</a><a href="/sayfa/iade-teslimat-politikasi">İade Politikası</a></div>
            </div>

            <div class="footer-bottom">
                <span><?= e(setting('footer_text', '© ' . date('Y') . ' NetVera Teknoloji Yazılım · netvera.tr - Tüm hakları saklıdır.')) ?></span>
                <span><?= icon('shield', 11) ?> Güvenli Ödeme · 7/24 Destek · %100 Müşteri Memnuniyeti</span>
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

    <?php if(\App\Services\NetveraInquiryService::ready()): ?>
        <?php require BASE_PATH.'/resources/views/frontend/partials/netvera-live-chat.php'; ?>
    <?php endif; ?>

    <script src="<?= asset('js/nv-desk-alerts.js') ?>?v=1"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    <script src="<?= asset('js/icon-bridge.js') ?>"></script>
    <script src="<?= asset('js/home-featured-tabs-v18.js') ?>?v=43.1"></script>
    <script src="<?= asset('js/package-benefits-v67.js') ?>?v=67.1"></script>
    <script src="<?= asset('js/reviews-v68.js') ?>?v=68.1" defer></script>
    <script src="<?= asset('js/agency-navigation-v26.js') ?>?v=26.2"></script>
    <style>.nv68-chat-person{display:flex;align-items:center;gap:10px}.nv68-chat-person>img,.nv68-chat-avatar{width:39px;height:39px;flex-shrink:0;border-radius:12px;object-fit:cover;background:rgba(255,255,255,.18);display:grid;place-items:center}.nv68-chat-person strong,.nv68-chat-person small{display:block}.nv68-chat-person small{opacity:.8;font-size:10px}</style>
    <?= setting('footer_script') ?>
</body>
</html>
