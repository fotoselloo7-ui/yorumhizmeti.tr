<!-- Welcome Banner -->
<div class="adm-welcome">
    <div class="adm-welcome-text">
        <h2>Hoş Geldiniz, <?= e($_SESSION['admin_name'] ?? 'Admin') ?></h2>
        
    </div>
    <div class="adm-welcome-actions">
        <a href="/admin/siparisler" class="btn btn-primary btn-sm"><?= icon('shopping-cart', 16) ?> Siparişler</a>
        <a href="/admin/destek" class="btn btn-light btn-sm"><?= icon('headphones', 16) ?> Destek</a>
    </div>
</div>

<!-- Critical Alerts -->
<?php if (!empty($alerts)): ?>
<div class="adm-alerts">
    <?php foreach ($alerts as $a): ?>
    <div class="adm-alert adm-alert-<?= $a['type'] ?>">
        <div class="adm-alert-content"><?= icon($a['icon'], 18) ?> <span><?= $a['text'] ?></span></div>
        <a href="<?= $a['link'] ?>" class="btn btn-sm <?= $a['type'] === 'warning' ? 'btn-warning' : 'btn-primary' ?>"><?= $a['linkText'] ?></a>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Stats Grid -->
<div class="adm-stats-grid">
    <div class="adm-stat-card">
        <div class="adm-stat-icon stat-blue"><?= icon('shopping-cart', 22) ?></div>
        <div class="adm-stat-body">
            <span class="adm-stat-value"><?= $stats['orders_today'] ?></span>
            <span class="adm-stat-label">Bugünkü Siparişler</span>
        </div>
    </div>
    <div class="adm-stat-card">
        <div class="adm-stat-icon stat-indigo"><?= icon('package', 22) ?></div>
        <div class="adm-stat-body">
            <span class="adm-stat-value"><?= $stats['orders_total'] ?></span>
            <span class="adm-stat-label">Toplam Sipariş</span>
        </div>
    </div>
    <div class="adm-stat-card">
        <div class="adm-stat-icon stat-amber"><?= icon('clock', 22) ?></div>
        <div class="adm-stat-body">
            <span class="adm-stat-value"><?= $stats['orders_pending'] ?></span>
            <span class="adm-stat-label">Bekleyen Sipariş</span>
        </div>
    </div>
    <div class="adm-stat-card">
        <div class="adm-stat-icon stat-green"><?= icon('check-circle', 22) ?></div>
        <div class="adm-stat-body">
            <span class="adm-stat-value"><?= $stats['orders_completed'] ?></span>
            <span class="adm-stat-label">Tamamlanan</span>
        </div>
    </div>
    <div class="adm-stat-card">
        <div class="adm-stat-icon stat-emerald"><?= icon('dollar-sign', 22) ?></div>
        <div class="adm-stat-body">
            <span class="adm-stat-value"><?= money($stats['total_revenue']) ?></span>
            <span class="adm-stat-label">Toplam Ciro</span>
        </div>
    </div>
    <div class="adm-stat-card">
        <div class="adm-stat-icon stat-cyan"><?= icon('activity', 22) ?></div>
        <div class="adm-stat-body">
            <span class="adm-stat-value"><?= money($stats['revenue_30d']) ?></span>
            <span class="adm-stat-label">Son 30 Gün</span>
        </div>
    </div>
    <div class="adm-stat-card">
        <div class="adm-stat-icon stat-orange"><?= icon('credit-card', 22) ?></div>
        <div class="adm-stat-body">
            <span class="adm-stat-value"><?= $stats['pending_payments'] ?></span>
            <span class="adm-stat-label">Bekleyen Ödeme</span>
        </div>
    </div>
    <div class="adm-stat-card">
        <div class="adm-stat-icon stat-red"><?= icon('headphones', 22) ?></div>
        <div class="adm-stat-body">
            <span class="adm-stat-value"><?= $stats['open_tickets'] ?></span>
            <span class="adm-stat-label">Açık Destek</span>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="adm-quick-actions">
    <a href="/admin/paket/ekle" class="adm-quick-card">
        <div class="adm-quick-icon" style="background: var(--color-soft-blue); color: var(--color-blue);"><?= icon('plus', 22) ?></div>
        <span>Yeni Paket Ekle</span>
    </a>
    <a href="/admin/siparisler" class="adm-quick-card">
        <div class="adm-quick-icon" style="background: var(--color-soft-green); color: var(--color-green);"><?= icon('shopping-cart', 22) ?></div>
        <span>Siparişleri Gör</span>
    </a>
    <a href="/admin/destek" class="adm-quick-card">
        <div class="adm-quick-icon" style="background: var(--color-soft-amber); color: var(--color-amber);"><?= icon('headphones', 22) ?></div>
        <span>Destek Talepleri</span>
    </a>
    <a href="/admin/odeme-modulleri" class="adm-quick-card">
        <div class="adm-quick-icon" style="background: var(--color-soft-red); color: var(--color-red);"><?= icon('settings', 22) ?></div>
        <span>Ödeme Ayarları</span>
    </a>
</div>

<!-- Tables Row -->
<div class="adm-grid-2">
    <!-- Recent Orders -->
    <div class="adm-card">
        <div class="adm-card-header">
            <h3><?= icon('shopping-cart', 18) ?> Son Siparişler</h3>
            <a href="/admin/siparisler" class="adm-card-link">Tümünü Gör <?= icon('arrow-right', 14) ?></a>
        </div>
        <div class="adm-card-body" style="padding: 0;">
            <?php if (empty($recentOrders)): ?>
            <div class="adm-empty-sm"><?= icon('package', 28) ?><p>Henüz sipariş yok</p></div>
            <?php else: ?>
            <table class="adm-table">
                <thead><tr><th>Sipariş</th><th>Müşteri</th><th>Tutar</th><th>Durum</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($recentOrders, 0, 7) as $o): ?>
                <tr>
                    <td><a href="/admin/siparis/<?= $o['id'] ?>" class="adm-link-blue">#<?= e($o['order_number']) ?></a></td>
                    <td class="text-sm"><?= e($o['user_name'] ?? '-') ?></td>
                    <td class="font-semibold"><?= money($o['total_amount']) ?></td>
                    <td><span class="status-badge <?= orderStatusColor($o['order_status']) ?>"><?= e(orderStatusLabel($o['order_status'])) ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div class="adm-card">
        <div class="adm-card-header">
            <h3><?= icon('headphones', 18) ?> Açık Destek Talepleri</h3>
            <a href="/admin/destek" class="adm-card-link">Tümünü Gör <?= icon('arrow-right', 14) ?></a>
        </div>
        <div class="adm-card-body" style="padding: 0;">
            <?php if (empty($recentTickets)): ?>
            <div class="adm-empty-sm"><?= icon('headphones', 28) ?><p>Açık talep yok</p></div>
            <?php else: ?>
            <table class="adm-table">
                <thead><tr><th>Konu</th><th>Müşteri</th><th>Durum</th></tr></thead>
                <tbody>
                <?php foreach ($recentTickets as $t): ?>
                <tr class="<?= $t['status'] === 'customer_reply' ? 'adm-row-highlight' : '' ?>">
                    <td><a href="/admin/destek/<?= $t['id'] ?>" class="adm-link-blue"><?= e($t['subject']) ?></a></td>
                    <td class="text-sm"><?= e($t['user_name'] ?? '-') ?></td>
                    <td><span class="status-badge <?= $t['status'] === 'admin_reply' ? 'success' : ($t['status'] === 'customer_reply' ? 'warning' : 'info') ?>"><?= e(supportStatusLabel($t['status'])) ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
