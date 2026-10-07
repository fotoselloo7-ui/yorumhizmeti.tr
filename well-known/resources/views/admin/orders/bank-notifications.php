<div class="adm-page-top">
    <div>
        <h2><?= icon('inbox', 24) ?> Havale Bildirimleri</h2>
        <p class="text-sm text-secondary">Müşterilerin havale/EFT bildirimlerini onaylayın veya reddedin.</p>
    </div>
</div>

<?php if (empty($notifications)): ?>
<div class="adm-card">
    <div class="adm-card-body">
        <div class="adm-empty-sm" style="padding: var(--space-8);"><?= icon('inbox', 32) ?><p>Henüz havale bildirimi yok.</p></div>
    </div>
</div>
<?php else: ?>

<!-- Desktop Table -->
<div class="adm-card adm-bank-desktop">
    <div class="adm-card-body" style="padding: 0;">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Sipariş</th>
                    <th>Müşteri</th>
                    <th>Banka</th>
                    <th>Gönderen</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notifications as $n): ?>
                <tr class="<?= $n['status'] === 'pending' ? 'adm-row-highlight' : '' ?>">
                    <td><a href="/admin/siparis/<?= $n['order_id'] ?>" class="adm-link-blue font-semibold">#<?= e($n['order_number'] ?? '') ?></a></td>
                    <td class="text-sm"><?= e($n['user_name'] ?? '-') ?></td>
                    <td class="text-sm"><?= e($n['bank_name']) ?></td>
                    <td class="text-sm"><?= e($n['sender_name']) ?></td>
                    <td class="font-semibold"><?= money($n['amount']) ?></td>
                    <td>
                        <span class="status-badge <?= $n['status'] === 'confirmed' ? 'success' : ($n['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                            <?= $n['status'] === 'confirmed' ? 'Onaylandı' : ($n['status'] === 'rejected' ? 'Reddedildi' : 'Bekliyor') ?>
                        </span>
                    </td>
                    <td class="text-sm text-secondary"><?= formatDate($n['created_at'], 'd M Y') ?></td>
                    <td>
                        <?php if ($n['status'] === 'pending'): ?>
                        <div class="adm-bank-btn-group">
                            <form method="POST" action="/admin/havale/<?= $n['id'] ?>/onayla"><?= csrfField() ?><button class="btn btn-success btn-sm"><?= icon('check', 14) ?> Onayla</button></form>
                            <form method="POST" action="/admin/havale/<?= $n['id'] ?>/reddet"><?= csrfField() ?><button class="btn btn-danger btn-sm"><?= icon('x', 14) ?> Reddet</button></form>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Cards -->
<div class="adm-bank-mobile">
    <?php foreach ($notifications as $n): ?>
    <div class="adm-order-card-m <?= $n['status'] === 'pending' ? 'adm-card-highlight' : '' ?>">
        <div class="adm-order-card-m-top">
            <a href="/admin/siparis/<?= $n['order_id'] ?>" class="adm-link-blue font-semibold">#<?= e($n['order_number'] ?? '') ?></a>
            <span class="status-badge <?= $n['status'] === 'confirmed' ? 'success' : ($n['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                <?= $n['status'] === 'confirmed' ? 'Onaylandı' : ($n['status'] === 'rejected' ? 'Reddedildi' : 'Bekliyor') ?>
            </span>
        </div>
        <div class="adm-order-card-m-body">
            <div class="adm-order-card-m-row"><span class="adm-order-card-m-label">Müşteri</span><span class="text-sm"><?= e($n['user_name'] ?? '-') ?></span></div>
            <div class="adm-order-card-m-row"><span class="adm-order-card-m-label">Banka</span><span class="text-sm"><?= e($n['bank_name']) ?></span></div>
            <div class="adm-order-card-m-row"><span class="adm-order-card-m-label">Gönderen</span><span class="text-sm"><?= e($n['sender_name']) ?></span></div>
            <div class="adm-order-card-m-row"><span class="adm-order-card-m-label">Tutar</span><strong><?= money($n['amount']) ?></strong></div>
            <div class="adm-order-card-m-row"><span class="adm-order-card-m-label">Tarih</span><span class="text-xs text-secondary"><?= formatDate($n['created_at'], 'd M Y') ?></span></div>
        </div>
        <?php if ($n['status'] === 'pending'): ?>
        <div class="adm-bank-mobile-actions">
            <form method="POST" action="/admin/havale/<?= $n['id'] ?>/onayla"><?= csrfField() ?><button class="btn btn-success btn-sm btn-block"><?= icon('check', 14) ?> Onayla</button></form>
            <form method="POST" action="/admin/havale/<?= $n['id'] ?>/reddet"><?= csrfField() ?><button class="btn btn-danger btn-sm btn-block"><?= icon('x', 14) ?> Reddet</button></form>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
