<section class="section panel-section">
    <div class="container">
        <div class="panel-layout">
            <?php include __DIR__ . '/../partials/user-sidebar.php'; ?>
            <div class="panel-content">

                <div class="panel-page-header">
                    <div>
                        <h1><?= icon('headphones', 24) ?> Destek Taleplerim</h1>
                        <p>Destek taleplerinizi görüntüleyin veya yeni talep oluşturun.</p>
                    </div>
                    <a href="/destek/yeni" class="btn btn-primary btn-sm"><?= icon('plus', 16) ?> Yeni Talep Aç</a>
                </div>

                <?php if (empty($tickets)): ?>
                <div class="panel-empty-state">
                    <div class="panel-empty-icon"><?= icon('headphones', 48) ?></div>
                    <h3>Henüz destek talebiniz yok</h3>
                    <p>Bir sorunuz veya sorununuz varsa destek talebi oluşturabilirsiniz.</p>
                    <a href="/destek/yeni" class="btn btn-primary"><?= icon('plus', 16) ?> Yeni Talep Oluştur</a>
                </div>
                <?php else: ?>

                <!-- Desktop Table -->
                <div class="panel-card panel-tickets-desktop">
                    <div class="panel-card-body" style="padding: 0;">
                        <table class="panel-table">
                            <thead>
                                <tr>
                                    <th>Talep No</th>
                                    <th>Konu</th>
                                    <th>Öncelik</th>
                                    <th>Durum</th>
                                    <th>Son Güncelleme</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $t): ?>
                                <tr>
                                    <td>
                                        <span class="panel-order-number">#<?= e($t['ticket_number']) ?></span>
                                    </td>
                                    <td>
                                        <div class="panel-ticket-subject">
                                            <?= e($t['subject']) ?>
                                            <?php if (!empty($t['order_id'])): ?>
                                            <span class="panel-ticket-order"><?= icon('package', 12) ?> Sipariş bağlantılı</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $priBadge = 'badge-default';
                                        if ($t['priority'] === 'high') $priBadge = 'badge-danger';
                                        elseif ($t['priority'] === 'medium') $priBadge = 'badge-warning';
                                        elseif ($t['priority'] === 'low') $priBadge = 'badge-info';
                                        ?>
                                        <span class="badge <?= $priBadge ?>"><?= e(priorityLabel($t['priority'])) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $stBadge = 'badge-info';
                                        if ($t['status'] === 'closed') $stBadge = 'badge-default';
                                        elseif ($t['status'] === 'admin_reply') $stBadge = 'badge-success';
                                        elseif ($t['status'] === 'customer_reply') $stBadge = 'badge-warning';
                                        ?>
                                        <span class="badge <?= $stBadge ?>"><?= e(supportStatusLabel($t['status'])) ?></span>
                                    </td>
                                    <td class="text-sm text-secondary"><?= formatDate($t['updated_at'], 'd M Y H:i') ?></td>
                                    <td>
                                        <a href="/destek/<?= $t['id'] ?>" class="btn btn-outline btn-sm"><?= icon('eye', 14) ?> Görüntüle</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Cards -->
                <div class="panel-tickets-mobile">
                    <?php foreach ($tickets as $t): ?>
                    <div class="panel-ticket-card">
                        <div class="panel-ticket-card-top">
                            <span class="panel-order-number">#<?= e($t['ticket_number']) ?></span>
                            <?php
                            $stBadge = 'badge-info';
                            if ($t['status'] === 'closed') $stBadge = 'badge-default';
                            elseif ($t['status'] === 'admin_reply') $stBadge = 'badge-success';
                            elseif ($t['status'] === 'customer_reply') $stBadge = 'badge-warning';
                            ?>
                            <span class="badge <?= $stBadge ?>"><?= e(supportStatusLabel($t['status'])) ?></span>
                        </div>
                        <div class="panel-ticket-card-subject"><?= e($t['subject']) ?></div>
                        <div class="panel-ticket-card-meta">
                            <?php
                            $priBadge = 'badge-default';
                            if ($t['priority'] === 'high') $priBadge = 'badge-danger';
                            elseif ($t['priority'] === 'medium') $priBadge = 'badge-warning';
                            elseif ($t['priority'] === 'low') $priBadge = 'badge-info';
                            ?>
                            <span class="badge <?= $priBadge ?>"><?= e(priorityLabel($t['priority'])) ?></span>
                            <span class="text-sm text-secondary"><?= formatDate($t['updated_at'], 'd M Y') ?></span>
                        </div>
                        <a href="/destek/<?= $t['id'] ?>" class="panel-order-card-action"><?= icon('eye', 14) ?> Görüntüle</a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
