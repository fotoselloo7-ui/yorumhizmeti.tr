<?php
$badgeColorMap = [
    'success'   => '#10b981',
    'warning'   => '#f59e0b',
    'danger'    => '#ef4444',
    'secondary' => '#94a3b8',
];
$badgeColor = $badgeColorMap[$info['badge']['color']] ?? '#94a3b8';
$badgeBgMap = [
    'success'   => '#ecfdf5',
    'warning'   => '#fffbeb',
    'danger'    => '#fef2f2',
    'secondary' => '#f1f5f9',
];
$badgeBg = $badgeBgMap[$info['badge']['color']] ?? '#f1f5f9';
?>

<!-- Flash Messages -->
<?php if ($flash['success'] ?? null): ?>
<div class="adm-alert adm-alert-info" style="margin-bottom: var(--space-5);">
    <div class="adm-alert-content"><?= icon('check-circle', 18) ?> <span><?= e($flash['success']) ?></span></div>
</div>
<?php endif; ?>
<?php if ($flash['error'] ?? null): ?>
<div class="adm-alert adm-alert-warning" style="margin-bottom: var(--space-5);">
    <div class="adm-alert-content"><?= icon('alert-triangle', 18) ?> <span><?= e($flash['error']) ?></span></div>
</div>
<?php endif; ?>

<div class="adm-page-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:var(--space-6);">
    <div>
        <h1 style="font-size:var(--font-size-2xl); font-weight:700; color:var(--color-dark); margin:0;"><?= icon('shield', 24) ?> Lisans Yönetimi</h1>
        <p style="color:var(--color-text-secondary); margin-top:var(--space-1); font-size:14px;">Yazılım lisansınızı yönetin ve durumunu kontrol edin.</p>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-5); align-items:start;">
    <!-- Sol Kolon: Durum & Bilgiler -->
    <div>
        <!-- Status Card -->
        <div class="adm-card" style="margin-bottom:var(--space-5);">
            <div class="adm-card-header">
                <h3><?= icon('activity', 18) ?> Lisans Durumu</h3>
            </div>
            <div class="adm-card-body" style="text-align:center; padding:var(--space-8) var(--space-5);">
                <div style="display:inline-flex; align-items:center; gap:10px; padding:12px 28px; border-radius:50px; background:<?= $badgeBg ?>; color:<?= $badgeColor ?>; font-weight:700; font-size:18px; border:2px solid <?= $badgeColor ?>;">
                    <?php if ($info['badge']['color'] === 'success'): ?>
                        <?= icon('check-circle', 22) ?>
                    <?php elseif ($info['badge']['color'] === 'warning'): ?>
                        <?= icon('alert-triangle', 22) ?>
                    <?php elseif ($info['badge']['color'] === 'danger'): ?>
                        <?= icon('x-circle', 22) ?>
                    <?php else: ?>
                        <?= icon('help-circle', 22) ?>
                    <?php endif; ?>
                    <?= e($info['badge']['label']) ?>
                </div>
                <?php if (!empty($info['message']) && $info['message'] !== '-'): ?>
                <p style="margin-top:var(--space-4); color:var(--color-text-secondary); font-size:14px;"><?= e($info['message']) ?></p>
                <?php endif; ?>
                <?php if ($info['local_bypass']): ?>
                <div style="margin-top:var(--space-3); padding:8px 16px; border-radius:8px; background:#eff6ff; color:#2563eb; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
                    <?= icon('info', 14) ?> Local Bypass Aktif — Lisans kontrolü devre dışı
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Table -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('info', 18) ?> Lisans Bilgileri</h3>
            </div>
            <div class="adm-card-body" style="padding:0;">
                <table style="width:100%; border-collapse:collapse;">
                    <?php
                    $rows = [
                        ['Ürün Kodu',           $info['product_code']],
                        ['Domain',              $info['domain']],
                        ['Install ID',          $info['install_id']],
                        ['Son Kontrol',         $info['last_check']],
                        ['Sonraki Kontrol',     $info['next_check']],
                        ['Bitiş Tarihi',        $info['expires_at']],
                        ['Grace Süresi',        $info['grace_until']],
                        ['Kontrol Aralığı',     $info['check_interval'] . ' saat'],
                        ['Grace Gün Sayısı',    $info['grace_days'] . ' gün'],
                    ];
                    foreach ($rows as $i => $row):
                    ?>
                    <tr style="border-bottom:1px solid var(--color-border);">
                        <td style="padding:12px 16px; font-weight:600; color:var(--color-dark); font-size:14px; white-space:nowrap; width:160px; background:<?= $i % 2 === 0 ? '#fafbfc' : '#fff' ?>;"><?= $row[0] ?></td>
                        <td style="padding:12px 16px; font-size:14px; color:var(--color-text-secondary); background:<?= $i % 2 === 0 ? '#fafbfc' : '#fff' ?>; word-break:break-all;"><?= e($row[1]) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Sağ Kolon: Ayarlar & Aksiyonlar -->
    <div>
        <!-- Settings Form -->
        <div class="adm-card" style="margin-bottom:var(--space-5);">
            <div class="adm-card-header">
                <h3><?= icon('settings', 18) ?> Lisans Ayarları</h3>
            </div>
            <div class="adm-card-body">
                <form action="/admin/lisans/kaydet" method="POST">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="license_server_url">Lisans Sunucu URL</label>
                        <input type="url" name="license_server_url" id="license_server_url" class="form-control"
                               value="<?= e($info['server_url']) ?>"
                               placeholder="https://lisans.example.com">
                        <div class="form-hint">Lisans doğrulama sunucusunun tam URL adresi.</div>
                    </div>

                    <div class="form-group">
                        <label for="license_key">Lisans Anahtarı</label>
                        <input type="text" name="license_key" id="license_key" class="form-control"
                               value="<?= e($info['license_key']) ?>"
                               placeholder="XXXX-XXXX-XXXX-XXXX" autocomplete="off">
                        <div class="form-hint">Lisans merkezinden aldığınız ürün anahtarı.</div>
                    </div>

                    <div class="form-group" style="display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:8px; background:#f8fafc; border:1px solid var(--color-border);">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; margin:0; font-weight:600; font-size:14px;">
                            <input type="checkbox" name="license_enabled" value="1"
                                   <?= $info['enabled'] ? 'checked' : '' ?>
                                   style="width:18px; height:18px; accent-color:var(--color-primary); cursor:pointer;">
                            Lisans Kontrolünü Aktifleştir
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:var(--space-3);">
                        <?= icon('save', 16) ?> Ayarları Kaydet
                    </button>
                </form>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><?= icon('zap', 18) ?> Hızlı İşlemler</h3>
            </div>
            <div class="adm-card-body" style="display:flex; flex-direction:column; gap:var(--space-3);">
                <!-- Activate -->
                <form action="/admin/lisans/aktifle" method="POST" style="display:flex; gap:8px;">
                    <?= csrf_field() ?>
                    <input type="text" name="license_key" class="form-control" value="<?= e($info['license_key']) ?>" placeholder="Lisans anahtarı..." style="flex:1;" autocomplete="off">
                    <button type="submit" class="btn btn-primary" style="white-space:nowrap;">
                        <?= icon('check-circle', 16) ?> Aktifleştir
                    </button>
                </form>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                    <!-- Check Now -->
                    <form action="/admin/lisans/kontrol" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-light" style="width:100%; justify-content:center;">
                            <?= icon('refresh-cw', 16) ?> Şimdi Kontrol Et
                        </button>
                    </form>

                    <!-- Deactivate -->
                    <form action="/admin/lisans/devre-disi" method="POST" onsubmit="return confirm('Lisansı devre dışı bırakmak istediğinize emin misiniz?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn" style="width:100%; justify-content:center; background:#fef2f2; color:#ef4444; border:1px solid #fecaca; font-weight:600;">
                            <?= icon('x-circle', 16) ?> Devre Dışı Bırak
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns:1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
