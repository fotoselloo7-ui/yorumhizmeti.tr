<div class="adm-page-top">
    <div>
        <h2><?= icon('user', 24) ?> Üyeler / Kullanıcılar</h2>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-body" style="padding: 0;">
        <?php if (empty($users)): ?>
            <div class="adm-empty-sm" style="padding: var(--space-6);"><?= icon('user', 32) ?><p>Henüz üye bulunmuyor.</p></div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Ad Soyad</th>
                            <th>E-posta</th>
                            <th>Telefon</th>
                            <th>Sipariş</th>
                            <th>Durum</th>
                            <th>Kayıt Tarihi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="font-semibold"><?= e($u['name']) ?></td>
                            <td class="text-sm"><?= e($u['email']) ?></td>
                            <td class="text-sm"><?= e($u['phone'] ?? '-') ?></td>
                            <td><span class="adm-cat-badge"><?= $u['order_count'] ?></span></td>
                            <td><span class="status-badge <?= $u['status'] === 'active' ? 'success' : 'default' ?>"><?= $u['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span></td>
                            <td class="text-sm text-secondary"><?= formatDate($u['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="adm-mobile-only" style="display: none; padding: 15px;">
                <?php foreach ($users as $u): ?>
                <div class="adm-order-card-m" style="margin-bottom: var(--space-4); background: #f8f9fa; border: 1px solid var(--color-border); padding: 15px; border-radius: 8px;">
                    <div class="adm-order-card-m-top" style="display:flex; justify-content:space-between; margin-bottom:10px;">
                        <span class="font-semibold"><?= e($u['name']) ?></span>
                        <span class="status-badge <?= $u['status'] === 'active' ? 'success' : 'default' ?>"><?= $u['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span>
                    </div>
                    <div class="adm-order-card-m-body">
                        <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:5px;">
                            <span class="text-secondary">E-posta</span>
                            <span><?= e($u['email']) ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:5px;">
                            <span class="text-secondary">Telefon</span>
                            <span><?= e($u['phone'] ?? '-') ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:5px;">
                            <span class="text-secondary">Sipariş Sayısı</span>
                            <span class="adm-cat-badge"><?= $u['order_count'] ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:13px;">
                            <span class="text-secondary">Kayıt</span>
                            <span><?= formatDate($u['created_at']) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
