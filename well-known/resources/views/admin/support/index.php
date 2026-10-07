<div class="adm-page-top">
    <div>
        <h2><?= icon('headphones', 24) ?> Destek Talepleri</h2>
        <p class="text-sm text-secondary">Tüm müşteri destek taleplerini yönetin.</p>
    </div>
</div>

<!-- Desktop Table -->
<div class="adm-card adm-tickets-desktop">
    <div class="adm-card-body" style="padding: 0;">
        <?php if (empty($tickets)): ?>
        <div class="adm-empty-sm" style="padding: var(--space-8);"><?= icon('headphones', 32) ?><p>Henüz destek talebi yok.</p></div>
        <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Konu</th>
                    <th>Müşteri</th>
                    <th>Öncelik</th>
                    <th>Durum</th>
                    <th>Son Güncelleme</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                <tr class="<?= $t['status'] === 'customer_reply' ? 'adm-row-highlight' : '' ?>">
                    <td><span class="adm-link-blue font-semibold">#<?= e($t['ticket_number']) ?></span></td>
                    <td>
                        <div class="adm-ticket-subject">
                            <a href="/admin/destek/<?= $t['id'] ?>" class="font-semibold"><?= e($t['subject']) ?></a>
                            <?php if (!empty($t['order_id'])): ?>
                            <span class="text-xs text-secondary"><?= icon('package', 11) ?> Sipariş bağlantılı</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="text-sm"><?= e($t['user_name'] ?? '-') ?></td>
                    <td>
                        <?php
                        $priBadge = 'default';
                        if ($t['priority'] === 'high') $priBadge = 'danger';
                        elseif ($t['priority'] === 'medium') $priBadge = 'warning';
                        elseif ($t['priority'] === 'low') $priBadge = 'info';
                        ?>
                        <span class="status-badge <?= $priBadge ?>"><?= e(priorityLabel($t['priority'])) ?></span>
                    </td>
                    <td>
                        <?php
                        $stBadge = 'info';
                        if ($t['status'] === 'closed') $stBadge = 'default';
                        elseif ($t['status'] === 'admin_reply') $stBadge = 'success';
                        elseif ($t['status'] === 'customer_reply') $stBadge = 'warning';
                        ?>
                        <span class="status-badge <?= $stBadge ?>"><?= e(supportStatusLabel($t['status'])) ?></span>
                    </td>
                    <td class="text-sm text-secondary"><?= formatDate($t['updated_at'], 'd M Y H:i') ?></td>
                    <td><a href="/admin/destek/<?= $t['id'] ?>" class="btn btn-outline btn-sm"><?= icon('eye', 14) ?> Görüntüle</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Mobile Cards -->
<div class="adm-tickets-mobile">
    <?php foreach ($tickets as $t): ?>
    <div class="adm-ticket-card-m <?= $t['status'] === 'customer_reply' ? 'adm-card-highlight' : '' ?>">
        <div class="adm-order-card-m-top">
            <span class="adm-link-blue font-semibold">#<?= e($t['ticket_number']) ?></span>
            <?php
            $stBadge = 'info';
            if ($t['status'] === 'closed') $stBadge = 'default';
            elseif ($t['status'] === 'admin_reply') $stBadge = 'success';
            elseif ($t['status'] === 'customer_reply') $stBadge = 'warning';
            ?>
            <span class="status-badge <?= $stBadge ?>"><?= e(supportStatusLabel($t['status'])) ?></span>
        </div>
        <div class="adm-order-card-m-body">
            <div class="font-semibold text-sm" style="padding: var(--space-2) 0;"><?= e($t['subject']) ?></div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Müşteri</span>
                <span class="text-sm"><?= e($t['user_name'] ?? '-') ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Öncelik</span>
                <?php
                $priBadge = 'default';
                if ($t['priority'] === 'high') $priBadge = 'danger';
                elseif ($t['priority'] === 'medium') $priBadge = 'warning';
                elseif ($t['priority'] === 'low') $priBadge = 'info';
                ?>
                <span class="status-badge <?= $priBadge ?>"><?= e(priorityLabel($t['priority'])) ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Güncelleme</span>
                <span class="text-xs text-secondary"><?= formatDate($t['updated_at'], 'd M Y') ?></span>
            </div>
        </div>
        <a href="/admin/destek/<?= $t['id'] ?>" class="adm-order-card-m-action"><?= icon('eye', 14) ?> Görüntüle</a>
    </div>
    <?php endforeach; ?>
</div>
