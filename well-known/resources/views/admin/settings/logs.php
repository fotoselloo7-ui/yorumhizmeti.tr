<div class="adm-page-top">
    <div>
        <h2><?= icon('list', 24) ?> Aktivite Logları</h2>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Aksiyon</th>
                        <th>Detay</th>
                        <th>IP</th>
                        <th>Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $l): ?>
                    <tr>
                        <td class="font-semibold text-sm"><?= e($l['action']) ?></td>
                        <td class="text-sm"><?= e($l['detail']) ?></td>
                        <td class="text-xs text-secondary"><?= e($l['ip_address'] ?? '-') ?></td>
                        <td class="text-xs text-secondary"><?= formatDate($l['created_at'], 'd M Y H:i') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile View -->
        <div class="adm-mobile-only" style="display: none; padding: 15px;">
            <?php foreach ($logs as $l): ?>
            <div class="adm-order-card-m" style="margin-bottom: var(--space-4); background: #f8f9fa; border: 1px solid var(--color-border); padding: 15px; border-radius: 8px;">
                <div class="adm-order-card-m-top" style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <span class="font-semibold text-sm"><?= e($l['action']) ?></span>
                    <span class="text-xs text-secondary"><?= formatDate($l['created_at'], 'd M Y H:i') ?></span>
                </div>
                <div class="adm-order-card-m-body">
                    <div style="font-size:13px; margin-bottom:5px; line-height:1.4;">
                        <?= e($l['detail']) ?>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:11px;" class="text-secondary">
                        <span>IP</span>
                        <span><?= e($l['ip_address'] ?? '-') ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
