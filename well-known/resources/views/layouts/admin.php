<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin Panel') ?> - <?= e(setting('site_name', 'YorumPanel')) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= e(upload_url(setting('site_favicon', '/assets/img/favicon.ico'))) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin-v4.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin-smm.css') ?>?v=1">
    <link rel="stylesheet" href="<?= asset('css/admin-customers-v67.css') ?>?v=67.1">
    <link rel="stylesheet" href="<?= asset('css/admin-showcases-v31.css') ?>?v=31.1">
    <link rel="stylesheet" href="<?= asset('css/netvera-admin-products.css') ?>?v=1">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" referrerpolicy="no-referrer">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <a href="/admin" style="color:#fff; text-decoration:none;">
                    <h2><?= e(setting('site_name', 'YorumPanel')) ?></h2>
                </a>
                <span class="badge">PRO</span>
            </div>

            <nav class="sidebar-nav">
                <div class="sidebar-section">Ana Menü</div>
                <a href="/admin" class="<?= isActive('/admin') && $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">
                    <?= icon('bar-chart', 18) ?> Dashboard
                </a>
                <a href="/admin/siparisler" class="<?= isActive('/admin/siparis') ?>">
                    <?= icon('shopping-cart', 18) ?> Siparişler
                </a>

                <div class="sidebar-section">Katalog</div>
                <a href="/admin/kategoriler" class="<?= isActive('/admin/kategori') ?>">
                    <?= icon('grid', 18) ?> Kategoriler
                </a>
                <a href="/admin/paketler" class="<?= isActive('/admin/paket') ?>">
                    <?= icon('package', 18) ?> Paketler
                </a>
                <a href="/admin/smm" class="<?= isActive('/admin/smm') ? 'active' : '' ?>">
                    <?= icon('share-2', 18) ?> Sosyal Medya API &amp; Servisler
                </a>
                <a href="/admin/hazir-yazilimlar" class="<?= isActive('/admin/hazir-yazilimlar') ? 'active' : '' ?>">
                    <?= icon('monitor', 18) ?> Hazır Yazılım Vitrini
                </a>
                <a href="/admin/netvera-yazilimlar" class="<?= isActive('/admin/netvera-yazilimlar') ? 'active' : '' ?>">
                    <?= icon('layers',18) ?> Netvera Yazılımları
                </a>
                <a href="/admin/netvera-kategoriler" class="<?= isActive('/admin/netvera-kategoriler') ? 'active' : '' ?>">
                    <?= icon('folder',18) ?> Yazılım Kategorileri
                </a>
                <a href="/admin/netvera-gelen-kutusu" class="<?= isActive('/admin/netvera-gelen-kutusu') ? 'active' : '' ?>">
                    <?= icon('message-circle',18) ?> Netvera Sohbet ve Teklifler
                </a>
                <a href="/admin/netvera-yazilimlar/ekle" class="<?= isActive('/admin/netvera-yazilimlar/ekle') ? 'active' : '' ?>">
                    <?= icon('plus',18) ?> Netvera Yazılım Ekle
                </a>
                <a href="/admin/yazilim/ekle" class="<?= isActive('/admin/yazilim/ekle') ? 'active' : '' ?>">
                    <?= icon('package',18) ?> Paket Tipi Yazılım Ekle
                </a>
                <a href="/admin/import" class="<?= isActive('/admin/import') ?>">
                    <?= icon('upload', 18) ?> İçe Aktar
                </a>

                <div class="sidebar-section">Finans</div>
                <a href="/admin/odemeler" class="<?= isActive('/admin/odemeler') ?>">
                    <?= icon('credit-card', 18) ?> Ödemeler
                </a>
                <a href="/admin/havale-bildirimleri" class="<?= isActive('/admin/havale') ?>">
                    <?= icon('inbox', 18) ?> Havale Bildirimleri
                </a>
                <a href="/admin/banka-hesaplari" class="<?= isActive('/admin/banka') ?>">
                    <?= icon('dollar-sign', 18) ?> Banka Hesapları
                </a>
                <a href="/admin/odeme-modulleri" class="<?= isActive('/admin/odeme-modul') ?>">
                    <?= icon('layers', 18) ?> Ödeme Modülleri
                </a>

                <div class="sidebar-section">Kullanıcılar</div>
                <a href="/admin/bayilik" class="<?= isActive('/admin/bayilik') ? 'active' : '' ?>">
                    <?= icon('handshake',18) ?> Bayilik & İş Ortakları
                </a>
                <a href="/admin/netvera-musteriler" class="<?= isActive('/admin/netvera-musteriler') ? 'active' : '' ?>">
                    <?= icon('users',18) ?> NetVera Müşteri & Bayilik
                </a>
                <a href="/admin/uyeler" class="<?= isActive('/admin/uye') ?>">
                    <?= icon('users', 18) ?> Üyeler
                </a>
                <a href="/admin/destek" class="<?= isActive('/admin/destek') ?>">
                    <?= icon('headphones', 18) ?> Destek Talepleri
                </a>
                <a href="/admin/mesajlar" class="<?= isActive('/admin/mesaj') ? 'active' : '' ?>">
                    <?= icon('mail', 18) ?> İletişim ve E-Bülten
                </a>

                <div class="sidebar-section">İçerik</div>
                <a href="/admin/menu" class="<?= isActive('/admin/menu') ?>">
                    <?= icon('menu',18) ?> Menü Yönetimi
                </a>
                <a href="/admin/ana-sayfa" class="<?= isActive('/admin/ana-sayfa') ?>">
                    <?= icon('home', 18) ?> Ana Sayfa Yönetimi
                </a>
                <a href="/admin/yorumlar" class="<?= isActive('/admin/yorumlar') ? 'active' : '' ?>">
                    <?= icon('message-circle',18) ?> Müşteri Yorumları
                </a>
                <a href="/admin/referanslar" class="<?= isActive('/admin/referanslar') ? 'active' : '' ?>">
                    <?= icon('award', 18) ?> Referanslarımız
                </a>
                <a href="/admin/blog-kategorileri" class="<?= isActive('/admin/blog-kategori') ?>">
                    <?= icon('folder', 18) ?> Blog Kategorileri
                </a>
                <a href="/admin/blog" class="<?= isActive('/admin/blog') && !isActive('/admin/blog-kategori') ? 'active' : '' ?>">
                    <?= icon('file-text', 18) ?> Blog Yazıları
                </a>
                <a href="/admin/sayfalar" class="<?= isActive('/admin/sayfa') ?>">
                    <?= icon('copy', 18) ?> Sayfalar
                </a>
                <a href="/admin/sss" class="<?= isActive('/admin/sss') ?>">
                    <?= icon('help-circle', 18) ?> SSS
                </a>

                <div class="sidebar-section">Ayarlar</div>
                <a href="/admin/seo" class="<?= isActive('/admin/seo') ?>">
                    <?= icon('search', 18) ?> SEO Merkezi
                </a>
                <a href="/admin/site-ayarlari" class="<?= isActive('/admin/site-ayarlari') ?>">
                    <?= icon('settings', 18) ?> Site Ayarları
                </a>
                <a href="/admin/paytr-ayarlari" class="<?= isActive('/admin/paytr') ?>">
                    <?= icon('credit-card', 18) ?> PayTR Ayarları
                </a>
                <a href="/admin/iyzico-ayarlari" class="<?= isActive('/admin/iyzico') ?>">
                    <?= icon('credit-card', 18) ?> iyzico Ayarları
                </a>
                <a href="/admin/smtp-ayarlari" class="<?= isActive('/admin/smtp') ?>">
                    <?= icon('mail', 18) ?> SMTP Ayarları
                </a>
                <a href="/admin/admin-kullanicilari" class="<?= isActive('/admin/admin-kullanici') ?>">
                    <?= icon('shield', 18) ?> Admin Kullanıcıları
                </a>
                <a href="/admin/loglar" class="<?= isActive('/admin/loglar') ?>">
                    <?= icon('activity', 18) ?> Loglar
                </a>
                <a href="/admin/lisans" class="<?= isActive('/admin/lisans') ?>">
                    <?= icon('shield', 18) ?> Lisans Yönetimi
                </a>

                <div style="padding: var(--space-4) var(--space-5);">
                    <a href="/admin/cikis" class="btn btn-light btn-sm btn-block" style="justify-content: center;">
                        <?= icon('log-out', 16) ?> Çıkış Yap
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Content -->
        <main class="admin-content">
            <!-- Topbar -->
            <div class="admin-topbar">
                <div class="flex-center gap-4">
                    <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menü">
                        <?= icon('menu', 22) ?>
                    </button>
                    <h1><?= e($pageTitle ?? 'Dashboard') ?></h1>
                </div>

                <div class="topbar-actions">
                    <a href="/" target="_blank" class="btn btn-light btn-sm">
                        <?= icon('external-link', 14) ?> Siteyi Gör
                    </a>
                    <div class="admin-user-menu">
                        <div class="admin-user-avatar">
                            <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                        </div>
                        <span><?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (!empty($flash)): ?>
            <div style="padding: var(--space-4) var(--space-6) 0;">
                <?php foreach ($flash as $type => $message): ?>
                    <div class="alert alert-<?= $type === 'error' ? 'error' : ($type === 'warning' ? 'warning' : 'success') ?>">
                        <?= icon($type === 'error' ? 'alert-circle' : 'check-circle', 18) ?>
                        <span><?= e($message) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="admin-page">
                <?= $content ?>
            </div>
        </main>
    </div>

    <script>
    function toggleSidebar() {
        var sidebar = document.getElementById('adminSidebar');
        var overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('open');
        overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
    }
    </script>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
