<!-- Page Header -->
<div class="adm-page-top">
    <div class="adm-page-top-left">
        <a href="/admin/siparisler" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Geri</a>
        <div>
            <h2>Sipariş #<?= e($order['order_number']) ?></h2>
            <span class="text-sm text-secondary"><?= formatDate($order['created_at'], 'd M Y H:i') ?></span>
        </div>
    </div>
    <div class="adm-page-top-badges">
        <span class="status-badge <?= orderStatusColor($order['order_status']) ?>"><?= e(orderStatusLabel($order['order_status'])) ?></span>
        <?php
        $payColor = 'warning';
        if ($order['payment_status'] === 'paid') $payColor = 'success';
        elseif ($order['payment_status'] === 'failed') $payColor = 'danger';
        elseif ($order['payment_status'] === 'refunded') $payColor = 'default';
        ?>
        <span class="status-badge <?= $payColor ?>"><?= e(paymentStatusLabel($order['payment_status'])) ?></span>
    </div>
</div>

<!-- Order Summary Cards -->
<div class="adm-order-summary">
    <div class="adm-order-sum-card">
        <div class="adm-order-sum-icon" style="background: var(--color-soft-blue); color: var(--color-blue);"><?= icon('user', 20) ?></div>
        <div>
            <div class="text-xs text-secondary">Müşteri</div>
            <div class="font-semibold"><?= e($order['user_name']) ?></div>
            <div class="text-xs text-secondary"><?= e($order['user_email']) ?><?= $order['user_phone'] ? ' · ' . e($order['user_phone']) : '' ?></div>
        </div>
    </div>
    <div class="adm-order-sum-card">
        <div class="adm-order-sum-icon" style="background: var(--color-soft-green); color: var(--color-green);"><?= icon('dollar-sign', 20) ?></div>
        <div>
            <div class="text-xs text-secondary">Toplam Tutar</div>
            <div class="font-bold" style="font-size: var(--font-size-xl); color: var(--color-blue);"><?= money($order['total_amount']) ?></div>
        </div>
    </div>
    <div class="adm-order-sum-card">
        <div class="adm-order-sum-icon" style="background: var(--color-soft-amber); color: var(--color-amber);"><?= icon('credit-card', 20) ?></div>
        <div>
            <div class="text-xs text-secondary">Ödeme Yöntemi</div>
            <div class="font-semibold"><?= e($order['payment_gateway'] ?? $order['payment_method'] ?? '-') ?></div>
        </div>
    </div>
    <div class="adm-order-sum-card">
        <div class="adm-order-sum-icon" style="background: var(--color-soft-red); color: var(--color-red);"><?= icon('calendar', 20) ?></div>
        <div>
            <div class="text-xs text-secondary">Sipariş Tarihi</div>
            <div class="font-semibold"><?= formatDate($order['created_at'], 'd M Y H:i') ?></div>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="adm-detail-grid">
    <!-- Left: Order Items & Info -->
    <div class="adm-detail-main">
        <!-- Order Items -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('package', 18) ?> Sipariş Kalemleri</h3>
            </div>
            <div class="adm-card-body">
                <?php foreach ($items as $item): ?>
                <div class="adm-order-item">
                    <div>
                        <div class="font-semibold"><?= e($item['package_name']) ?></div>
                        <div class="text-xs text-secondary">Adet: <?= $item['quantity'] ?></div>
                    </div>
                    <div class="font-semibold"><?= money($item['total']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Internal SMM provider fulfillment (admin only) -->
        <?php if (!empty($smmJobs)): ?>
        <div class="adm-card">
            <div class="adm-card-header"><h3><?= icon('share-2',18) ?> Sosyal Medya Servis Takibi</h3></div>
            <div class="adm-card-body">
                <p class="text-secondary" style="margin-bottom:14px">Bu bilgiler yalnızca yönetici ekranında görünür, müşterilere ve genel ürün sayfalarına aktarılmaz.</p>
                <?php foreach ($smmJobs as $job): ?>
                <div style="padding:12px 0;border-bottom:1px solid #e2e8f0">
                    <div class="font-semibold"><?= e($job['provider_name']) ?> · <?= (int)$job['quantity'] ?> adet</div>
                    <div class="text-sm text-secondary">Servis #<?= e($job['external_service_id']) ?> · Durum: <?= e($job['state']) ?></div>
                    <div class="text-sm text-secondary">Tedarikçi siparişi: <?= e($job['upstream_order_id']??'Henüz gönderilmedi') ?></div>
                    <?php if (!empty($job['last_error'])): ?>
                    <div class="text-sm" style="color:#a84624">Kontrol gerekli: <?= e($job['last_error']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <a class="btn btn-outline btn-sm" href="/admin/smm" style="margin-top:12px">API Siparişlerini Yönet <?= icon('arrow-right',14) ?></a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Order Fields -->
        <?php if (!empty($fields)): ?>
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('edit', 18) ?> Müşteri Bilgileri</h3>
            </div>
            <div class="adm-card-body">
                <div class="adm-fields-list">
                    <?php foreach ($fields as $f): ?>
                    <div class="adm-field-row">
                        <span class="adm-field-label"><?= e($f['field_label']) ?></span>
                        <span class="adm-field-value"><?= e($f['field_value']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Customer Note -->
        <?php if ($order['customer_note']): ?>
        <div class="adm-card">
            <div class="adm-card-header"><h3><?= icon('message-circle', 18) ?> Müşteri Notu</h3></div>
            <div class="adm-card-body">
                <div class="adm-customer-note"><?= nl2br(e($order['customer_note'])) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Timeline -->
        <?php if (!empty($logs)): ?>
        <div class="adm-card">
            <div class="adm-card-header"><h3><?= icon('activity', 18) ?> Sipariş Geçmişi</h3></div>
            <div class="adm-card-body">
                <div class="adm-timeline">
                    <?php foreach ($logs as $log): ?>
                    <div class="adm-timeline-item">
                        <div class="adm-timeline-dot"></div>
                        <div class="adm-timeline-content">
                            <div class="adm-timeline-top">
                                <span class="font-semibold text-sm"><?= e(orderStatusLabel($log['new_status'])) ?></span>
                                <span class="text-xs text-secondary"><?= formatDate($log['created_at'], 'd M Y H:i') ?></span>
                            </div>
                            <?php if ($log['note']): ?>
                            <div class="text-sm text-secondary" style="margin-top: 2px;"><?= e($log['note']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right: Action Panels -->
    <div class="adm-detail-side">
        <!-- Update Status -->
        <div class="adm-card">
            <div class="adm-card-header"><h3><?= icon('refresh-cw', 18) ?> Sipariş Durumu Güncelle</h3></div>
            <div class="adm-card-body">
                <form method="POST" action="/admin/siparis/<?= $order['id'] ?>/durum">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label>Yeni Durum</label>
                        <select name="status" class="form-control">
                            <option value="payment_pending" <?= $order['order_status'] === 'payment_pending' ? 'selected' : '' ?>>Ödeme Bekleniyor</option>
                            <option value="paid" <?= $order['order_status'] === 'paid' ? 'selected' : '' ?>>Ödendi</option>
                            <option value="preparing" <?= $order['order_status'] === 'preparing' ? 'selected' : '' ?>>Hazırlanıyor</option>
                            <option value="processing" <?= $order['order_status'] === 'processing' ? 'selected' : '' ?>>İşleniyor</option>
                            <option value="completed" <?= $order['order_status'] === 'completed' ? 'selected' : '' ?>>Tamamlandı</option>
                            <option value="waiting_customer_info" <?= $order['order_status'] === 'waiting_customer_info' ? 'selected' : '' ?>>Eksik Bilgi Bekleniyor</option>
                            <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>İptal</option>
                            <option value="refunded" <?= $order['order_status'] === 'refunded' ? 'selected' : '' ?>>İade</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Müşteriye Görünür Not</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Müşteriye gösterilecek not..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><?= icon('save', 16) ?> Durumu Güncelle</button>
                </form>
            </div>
        </div>

        <!-- Admin Private Note -->
        <div class="adm-card">
            <div class="adm-card-header"><h3><?= icon('lock', 18) ?> Admin Özel Not</h3></div>
            <div class="adm-card-body">
                <?php if ($order['admin_note']): ?>
                <div class="adm-admin-note"><?= nl2br(e($order['admin_note'])) ?></div>
                <?php else: ?>
                <p class="text-sm text-secondary">Henüz admin notu yok.</p>
                <?php endif; ?>
                <div class="form-hint" style="margin-top: var(--space-2);"><?= icon('lock', 12) ?> Bu not sadece admin panelinde görünür.</div>
            </div>
        </div>

        <!-- Customer Visible Note -->
        <?php if ($order['customer_visible_note']): ?>
        <div class="adm-card">
            <div class="adm-card-header"><h3><?= icon('eye', 18) ?> Müşteriye Görünen Not</h3></div>
            <div class="adm-card-body">
                <div class="adm-visible-note"><?= nl2br(e($order['customer_visible_note'])) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Related Support -->
        <div class="adm-card">
            <div class="adm-card-header"><h3><?= icon('headphones', 18) ?> Destek</h3></div>
            <div class="adm-card-body">
                <a href="/admin/destek" class="btn btn-outline btn-sm btn-block"><?= icon('headphones', 14) ?> Destek Taleplerini Gör</a>
            </div>
        </div>

        <!-- Bank Notifications for this order -->
        <?php if (!empty($bankNotifications)): ?>
        <div class="adm-card">
            <div class="adm-card-header"><h3><?= icon('inbox', 18) ?> Havale Bildirimleri</h3></div>
            <div class="adm-card-body">
                <?php foreach ($bankNotifications as $bn): ?>
                <div class="adm-bank-item">
                    <div class="text-sm"><strong><?= e($bn['sender_name']) ?></strong> · <?= e($bn['bank_name']) ?></div>
                    <div class="font-semibold"><?= money($bn['amount']) ?></div>
                    <div class="adm-bank-actions">
                        <span class="status-badge <?= $bn['status'] === 'confirmed' ? 'success' : ($bn['status'] === 'rejected' ? 'danger' : 'warning') ?>"><?= $bn['status'] === 'confirmed' ? 'Onaylandı' : ($bn['status'] === 'rejected' ? 'Reddedildi' : 'Bekliyor') ?></span>
                        <?php if ($bn['status'] === 'pending'): ?>
                        <form method="POST" action="/admin/havale/<?= $bn['id'] ?>/onayla" style="margin:0;"><?= csrfField() ?><button class="btn btn-success btn-sm"><?= icon('check', 14) ?> Onayla</button></form>
                        <form method="POST" action="/admin/havale/<?= $bn['id'] ?>/reddet" style="margin:0;"><?= csrfField() ?><button class="btn btn-danger btn-sm"><?= icon('x', 14) ?> Reddet</button></form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
