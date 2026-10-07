<div class="adm-page-top">
    <div>
        <h2><?= icon('credit-card', 24) ?> Ödeme Kayıtları</h2>
        <p class="text-sm text-secondary">Tüm ödeme işlemlerini görüntüleyin.</p>
    </div>
</div>

<?php if (empty($payments)): ?>
<div class="adm-card">
    <div class="adm-card-body">
        <div class="adm-empty-sm" style="padding: var(--space-8);"><?= icon('credit-card', 32) ?><p>Henüz ödeme kaydı yok.</p></div>
    </div>
</div>
<?php else: ?>

<!-- Desktop Table -->
<div class="adm-card adm-payments-desktop">
    <div class="adm-card-body" style="padding: 0;">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Sipariş</th>
                    <th>Müşteri</th>
                    <th>Yöntem</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th>İşlem No</th>
                    <th>Tarih</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                <tr>
                    <td><a href="/admin/siparis/<?= $p['order_id'] ?>" class="adm-link-blue font-semibold">#<?= e($p['order_number'] ?? '-') ?></a></td>
                    <td class="text-sm"><?= e($p['user_name'] ?? '-') ?></td>
                    <td>
                        <?php
                        $gwLabel = $p['gateway_key'] ?? '-';
                        $gwBadge = 'info';
                        if ($gwLabel === 'bank_transfer') { $gwLabel = 'Havale'; $gwBadge = 'default'; }
                        elseif ($gwLabel === 'paytr') { $gwLabel = 'PayTR'; }
                        elseif ($gwLabel === 'iyzico') { $gwLabel = 'iyzico'; }
                        ?>
                        <span class="status-badge <?= $gwBadge ?>"><?= e($gwLabel) ?></span>
                    </td>
                    <td class="font-semibold"><?= money((float) $p['amount']) ?></td>
                    <td>
                        <span class="status-badge <?= $p['status'] === 'completed' ? 'success' : ($p['status'] === 'failed' ? 'danger' : 'warning') ?>">
                            <?= paymentStatusLabel($p['status'] ?? 'pending') ?>
                        </span>
                    </td>
                    <td class="text-xs text-secondary"><?= e($p['transaction_id'] ?? '-') ?></td>
                    <td class="text-sm text-secondary"><?= formatDate($p['created_at'] ?? null, 'd M Y H:i') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Cards -->
<div class="adm-payments-mobile">
    <?php foreach ($payments as $p): ?>
    <div class="adm-order-card-m">
        <div class="adm-order-card-m-top">
            <a href="/admin/siparis/<?= $p['order_id'] ?>" class="adm-link-blue font-semibold">#<?= e($p['order_number'] ?? '-') ?></a>
            <span class="status-badge <?= $p['status'] === 'completed' ? 'success' : ($p['status'] === 'failed' ? 'danger' : 'warning') ?>">
                <?= paymentStatusLabel($p['status'] ?? 'pending') ?>
            </span>
        </div>
        <div class="adm-order-card-m-body">
            <div class="adm-order-card-m-row"><span class="adm-order-card-m-label">Müşteri</span><span class="text-sm"><?= e($p['user_name'] ?? '-') ?></span></div>
            <div class="adm-order-card-m-row"><span class="adm-order-card-m-label">Yöntem</span><span class="text-sm"><?= e($p['gateway_key'] ?? '-') ?></span></div>
            <div class="adm-order-card-m-row"><span class="adm-order-card-m-label">Tutar</span><strong><?= money((float) $p['amount']) ?></strong></div>
            <div class="adm-order-card-m-row"><span class="adm-order-card-m-label">Tarih</span><span class="text-xs text-secondary"><?= formatDate($p['created_at'] ?? null, 'd M Y') ?></span></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
