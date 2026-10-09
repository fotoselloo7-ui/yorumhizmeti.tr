<div class="adm-page-top">
    <div>
         <h2><?= icon('shield', 24) ?> Admin Kullanıcıları</h2>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Ad</th>
                        <th>E-posta</th>
                        <th>Rol</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $a): ?>
                    <tr>
                        <td class="font-semibold"><?= e($a['name']) ?></td>
                        <td class="text-sm"><?= e($a['email']) ?></td>
                        <td><span class="badge badge-primary"><?= e($a['role'] ?? 'admin') ?></span></td>
                        <td><span class="status-badge <?= $a['status'] === 'active' ? 'success' : 'default' ?>"><?= $a['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile View -->
        <div class="adm-mobile-only" style="display: none; padding: 15px;">
            <?php foreach ($admins as $a): ?>
            <div class="adm-order-card-m" style="margin-bottom: var(--space-4); background: #f8f9fa; border: 1px solid var(--color-border); padding: 15px; border-radius: 8px;">
                <div class="adm-order-card-m-top" style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <span class="font-semibold"><?= e($a['name']) ?></span>
                    <span class="status-badge <?= $a['status'] === 'active' ? 'success' : 'default' ?>"><?= $a['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span>
                </div>
                <div class="adm-order-card-m-body">
                    <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:5px;">
                        <span class="text-secondary">E-posta</span>
                        <span><?= e($a['email']) ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:13px;">
                        <span class="text-secondary">Rol</span>
                        <span class="badge badge-primary"><?= e($a['role'] ?? 'admin') ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
