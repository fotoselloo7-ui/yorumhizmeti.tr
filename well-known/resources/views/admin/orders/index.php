<!-- Status Tabs -->
<div class="adm-tabs">
    <a href="/admin/siparisler" class="<?= empty($currentStatus) ? 'active' : '' ?>"><?= icon('package', 14) ?> Tümü <span class="adm-tab-count"><?= $statusCounts['all'] ?></span></a>
    <a href="/admin/siparisler?status=payment_pending" class="<?= $currentStatus === 'payment_pending' ? 'active' : '' ?>"><?= icon('clock', 14) ?> Ödeme Bekliyor <span class="adm-tab-count"><?= $statusCounts['payment_pending'] ?></span></a>
    <a href="/admin/siparisler?status=paid" class="<?= $currentStatus === 'paid' ? 'active' : '' ?>"><?= icon('check', 14) ?> Ödendi <span class="adm-tab-count"><?= $statusCounts['paid'] ?></span></a>
    <a href="/admin/siparisler?status=processing" class="<?= $currentStatus === 'processing' ? 'active' : '' ?>"><?= icon('activity', 14) ?> İşleniyor <span class="adm-tab-count"><?= $statusCounts['processing'] ?></span></a>
    <a href="/admin/siparisler?status=completed" class="<?= $currentStatus === 'completed' ? 'active' : '' ?>"><?= icon('check-circle', 14) ?> Tamamlandı <span class="adm-tab-count"><?= $statusCounts['completed'] ?></span></a>
    <a href="/admin/siparisler?status=cancelled" class="<?= $currentStatus === 'cancelled' ? 'active' : '' ?>"><?= icon('x', 14) ?> İptal <span class="adm-tab-count"><?= $statusCounts['cancelled'] ?? 0 ?></span></a>
</div>

<!-- Desktop Table -->
<div class="adm-card adm-orders-desktop">
    <div class="adm-card-body" style="padding: 0;">
        <?php if (empty($orders)): ?>
        <div class="adm-empty-sm" style="padding: var(--space-8);"><?= icon('package', 32) ?><p>Bu filtrede sipariş bulunamadı.</p></div>
        <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Sipariş No</th>
                    <th>Müşteri</th>
                    <th>Tutar</th>
                    <th>Ödeme</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><a href="/admin/siparis/<?= $o['id'] ?>" class="adm-link-blue font-semibold">#<?= e($o['order_number']) ?></a></td>
                    <td>
                        <div class="text-sm font-semibold"><?= e($o['user_name'] ?? '-') ?></div>
                        <div class="text-xs text-secondary"><?= e($o['user_email'] ?? '') ?></div>
                    </td>
                    <td class="font-semibold"><?= money($o['total_amount']) ?></td>
                    <td>
                        <?php
                        $payBadge = 'warning';
                        if ($o['payment_status'] === 'paid') $payBadge = 'success';
                        elseif ($o['payment_status'] === 'failed') $payBadge = 'danger';
                        elseif ($o['payment_status'] === 'refunded') $payBadge = 'default';
                        ?>
                        <span class="status-badge <?= $payBadge ?>"><?= e(paymentStatusLabel($o['payment_status'])) ?></span>
                    </td>
                    <td><span class="status-badge <?= orderStatusColor($o['order_status']) ?>"><?= e(orderStatusLabel($o['order_status'])) ?></span></td>
                    <td class="text-sm text-secondary"><?= formatDate($o['created_at'], 'd M Y') ?></td>
                    <td><a href="/admin/siparis/<?= $o['id'] ?>" class="btn btn-outline btn-sm"><?= icon('eye', 14) ?> Detay</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Mobile Cards -->
<div class="adm-orders-mobile">
    <?php foreach ($orders as $o): ?>
    <div class="adm-order-card-m">
        <div class="adm-order-card-m-top">
            <a href="/admin/siparis/<?= $o['id'] ?>" class="adm-link-blue font-semibold">#<?= e($o['order_number']) ?></a>
            <span class="text-sm text-secondary"><?= formatDate($o['created_at'], 'd M Y') ?></span>
        </div>
        <div class="adm-order-card-m-body">
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Müşteri</span>
                <span class="text-sm"><?= e($o['user_name'] ?? '-') ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Tutar</span>
                <strong><?= money($o['total_amount']) ?></strong>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Ödeme</span>
                <?php
                $payBadge = 'warning';
                if ($o['payment_status'] === 'paid') $payBadge = 'success';
                elseif ($o['payment_status'] === 'failed') $payBadge = 'danger';
                elseif ($o['payment_status'] === 'refunded') $payBadge = 'default';
                ?>
                <span class="status-badge <?= $payBadge ?>"><?= e(paymentStatusLabel($o['payment_status'])) ?></span>
            </div>
            <div class="adm-order-card-m-row">
                <span class="adm-order-card-m-label">Durum</span>
                <span class="status-badge <?= orderStatusColor($o['order_status']) ?>"><?= e(orderStatusLabel($o['order_status'])) ?></span>
            </div>
        </div>
        <a href="/admin/siparis/<?= $o['id'] ?>" class="adm-order-card-m-action"><?= icon('eye', 14) ?> Detay Görüntüle</a>
    </div>
    <?php endforeach; ?>
</div>
