<section class="section panel-section">
    <div class="container">
        <div class="panel-layout">
            <?php include __DIR__ . '/../partials/user-sidebar.php'; ?>
            <div class="panel-content">

                <div class="panel-page-header">
                    <div>
                        <h1><?= icon('package', 24) ?> Siparişlerim</h1>
                        <p>Tüm siparişlerinizi buradan takip edebilirsiniz.</p>
                    </div>
                    <a href="/kategoriler" class="btn btn-primary btn-sm"><?= icon('shopping-cart', 16) ?> Yeni Sipariş</a>
                </div>

                <?php include __DIR__.'/partials/netvera-legacy-history.php'; ?>

                <?php if (empty($orders)): ?>
                <div class="panel-empty-state">
                    <div class="panel-empty-icon"><?= icon('package', 48) ?></div>
                    <h3>Henüz siparişiniz yok</h3>
                    <p>İlk siparişinizi oluşturmak için hizmetlerimizi inceleyin.</p>
                    <a href="/kategoriler" class="btn btn-primary"><?= icon('shopping-cart', 16) ?> Hizmetleri İncele</a>
                </div>
                <?php else: ?>

                <!-- Desktop Table -->
                <div class="panel-card panel-orders-desktop">
                    <div class="panel-card-body" style="padding: 0;">
                        <table class="panel-table">
                            <thead>
                                <tr>
                                    <th>Sipariş No</th>
                                    <th>Tarih</th>
                                    <th>Toplam</th>
                                    <th>Ödeme</th>
                                    <th>Durum</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td>
                                        <span class="panel-order-number">#<?= e($o['order_number']) ?></span>
                                    </td>
                                    <td class="text-sm text-secondary"><?= formatDate($o['created_at'], 'd M Y') ?></td>
                                    <td><strong><?= money($o['total_amount']) ?></strong></td>
                                    <td>
                                        <?php
                                        $payBadge = 'badge-warning';
                                        if ($o['payment_status'] === 'paid') $payBadge = 'badge-success';
                                        elseif ($o['payment_status'] === 'failed') $payBadge = 'badge-danger';
                                        elseif ($o['payment_status'] === 'refunded') $payBadge = 'badge-default';
                                        ?>
                                        <span class="badge <?= $payBadge ?>"><?= e(paymentStatusLabel($o['payment_status'])) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusBadge = 'badge-info';
                                        $st = $o['order_status'];
                                        if ($st === 'payment_pending') $statusBadge = 'badge-warning';
                                        elseif ($st === 'paid') $statusBadge = 'badge-success';
                                        elseif ($st === 'processing') $statusBadge = 'badge-primary';
                                        elseif ($st === 'completed') $statusBadge = 'badge-success';
                                        elseif ($st === 'cancelled') $statusBadge = 'badge-danger';
                                        elseif ($st === 'refunded') $statusBadge = 'badge-default';
                                        elseif ($st === 'waiting_customer_info') $statusBadge = 'badge-warning';
                                        ?>
                                        <span class="badge <?= $statusBadge ?>"><?= e(orderStatusLabel($o['order_status'])) ?></span>
                                    </td>
                                    <td>
                                        <a href="/siparis/<?= $o['id'] ?>" class="btn btn-outline btn-sm"><?= icon('eye', 14) ?> Detay</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Cards -->
                <div class="panel-orders-mobile">
                    <?php foreach ($orders as $o): ?>
                    <div class="panel-order-card">
                        <div class="panel-order-card-top">
                            <span class="panel-order-number">#<?= e($o['order_number']) ?></span>
                            <span class="text-sm text-secondary"><?= formatDate($o['created_at'], 'd M Y') ?></span>
                        </div>
                        <div class="panel-order-card-body">
                            <div class="panel-order-card-row">
                                <span class="panel-order-card-label">Toplam</span>
                                <strong><?= money($o['total_amount']) ?></strong>
                            </div>
                            <div class="panel-order-card-row">
                                <span class="panel-order-card-label">Ödeme</span>
                                <?php
                                $payBadge = 'badge-warning';
                                if ($o['payment_status'] === 'paid') $payBadge = 'badge-success';
                                elseif ($o['payment_status'] === 'failed') $payBadge = 'badge-danger';
                                elseif ($o['payment_status'] === 'refunded') $payBadge = 'badge-default';
                                ?>
                                <span class="badge <?= $payBadge ?>"><?= e(paymentStatusLabel($o['payment_status'])) ?></span>
                            </div>
                            <div class="panel-order-card-row">
                                <span class="panel-order-card-label">Durum</span>
                                <?php
                                $statusBadge = 'badge-info';
                                $st = $o['order_status'];
                                if ($st === 'payment_pending') $statusBadge = 'badge-warning';
                                elseif ($st === 'paid') $statusBadge = 'badge-success';
                                elseif ($st === 'processing') $statusBadge = 'badge-primary';
                                elseif ($st === 'completed') $statusBadge = 'badge-success';
                                elseif ($st === 'cancelled') $statusBadge = 'badge-danger';
                                elseif ($st === 'refunded') $statusBadge = 'badge-default';
                                elseif ($st === 'waiting_customer_info') $statusBadge = 'badge-warning';
                                ?>
                                <span class="badge <?= $statusBadge ?>"><?= e(orderStatusLabel($o['order_status'])) ?></span>
                            </div>
                        </div>
                        <a href="/siparis/<?= $o['id'] ?>" class="panel-order-card-action"><?= icon('eye', 14) ?> Detayları Gör</a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="panel-pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
