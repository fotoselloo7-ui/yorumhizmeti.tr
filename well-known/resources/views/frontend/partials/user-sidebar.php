<div class="panel-sidebar" id="panelSidebar">
    <div class="panel-sidebar-user">
        <div class="panel-avatar"><?= strtoupper(mb_substr($user['name'] ?? 'U', 0, 1)) ?></div>
        <div class="panel-user-info">
            <strong><?= e($user['name'] ?? '') ?></strong>
            <span><?= e($user['email'] ?? '') ?></span>
        </div>
    </div>
    <nav class="panel-nav">
        <a href="/hesabim" class="<?= ($_SERVER['REQUEST_URI'] ?? '') === '/hesabim' ? 'active' : '' ?>">
            <?= icon('home', 18) ?> <span>Dashboard</span>
        </a>
        <a href="/siparislerim" class="<?= isActive('/siparis') ?>">
            <?= icon('package', 18) ?> <span>Siparişlerim</span>
        </a>
        <a href="/destek" class="<?= isActive('/destek') ?>">
            <?= icon('headphones', 18) ?> <span>Destek Taleplerim</span>
        </a>
        <div class="panel-nav-divider"></div>
        <a href="/hesabim#profil" class="<?= ($_SERVER['REQUEST_URI'] ?? '') === '/hesabim' && isset($_GET['tab']) && $_GET['tab'] === 'profil' ? 'active' : '' ?>">
            <?= icon('user', 18) ?> <span>Profil Bilgilerim</span>
        </a>
        <a href="/hesabim#sifre" class="<?= ($_SERVER['REQUEST_URI'] ?? '') === '/hesabim' && isset($_GET['tab']) && $_GET['tab'] === 'sifre' ? 'active' : '' ?>">
            <?= icon('lock', 18) ?> <span>Şifre Değiştir</span>
        </a>
        <div class="panel-nav-divider"></div>
        <a href="/cikis" class="panel-nav-logout">
            <?= icon('log-out', 18) ?> <span>Çıkış Yap</span>
        </a>
    </nav>
</div>
<button class="panel-sidebar-toggle" id="panelSidebarToggle" type="button" aria-label="Menüyü Aç/Kapat">
    <?= icon('menu', 20) ?> <span>Panel Menüsü</span>
</button>
