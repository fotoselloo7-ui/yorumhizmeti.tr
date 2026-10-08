<div class="adm-page-top">
    <div class="adm-page-top-left">
        <a href="/admin/odeme-modulleri" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Geri</a>
        <h2><?= icon('settings', 24) ?> PayTR Ayarları</h2>
    </div>
</div>

<div class="adm-card" style="max-width: 760px; margin: 0 auto;">
    <div class="adm-card-header">
        <h3><?= icon('key', 16) ?> API Bağlantı Bilgileri</h3>
    </div>
    <div class="adm-card-body">
        <div class="adm-alert" style="padding:14px 16px;border:1px solid #e6e4f5;background:#faf9ff;border-radius:12px;margin-bottom:20px">
            <strong><?= icon('shield-check',15) ?> PayTR iFrame v2 Entegrasyonu</strong>
            <p style="font-size:12px;line-height:1.7;margin:7px 0 0">
                <?= !empty($settings['merchant_id'])&& !empty($settings['merchant_key'])&& !empty($settings['merchant_salt'])
                    ? 'Mağaza ayarları kayıtlı. API bilgileri güvenlik için ekranda gösterilmez.'
                    : 'PayTR mağaza panelindeki Merchant ID, Key ve Salt olmadan canlı ödeme başlatılamaz.' ?>
                <br>Bildirim (Callback) URL: <code><?= e(url('/payment/paytr/callback')) ?></code>
                <br>Bu adres PayTR sunucusundan erişilebilir HTTPS alan adında olmalıdır. <code>localhost:8006</code> üzerinden gerçek callback yapılamaz.
            </p>
            <a href="/odeme/paytr-onizleme" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;color:#6347d8;font-size:12px;font-weight:800">
                <?= icon('external-link',14) ?> Yerel PayTR ödeme ekranı tasarımını incele
            </a>
        </div>
        <form method="POST" action="/admin/paytr-ayarlari/kaydet">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Merchant ID</label>
                <input type="text" name="merchant_id" class="form-control" value="<?= e($settings['merchant_id'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Merchant Key</label>
                <input type="password" name="merchant_key" class="form-control" value="" autocomplete="new-password" placeholder="<?= !empty($settings['merchant_key']) ? '•••••••• (tanımlı — değiştirmek için girin)' : 'Merchant Key girin' ?>">
            </div>
            <div class="form-group">
                <label>Merchant Salt</label>
                <input type="password" name="merchant_salt" class="form-control" value="" autocomplete="new-password" placeholder="<?= !empty($settings['merchant_salt']) ? '•••••••• (tanımlı — değiştirmek için girin)' : 'Merchant Salt girin' ?>">
            </div>
            <div class="form-group">
                <label>Test Modu</label>
                <select name="test_mode" class="form-control">
                    <option value="1" <?= ($settings['test_mode'] ?? '1') === '1' ? 'selected' : '' ?>>Açık (Test)</option>
                    <option value="0" <?= ($settings['test_mode'] ?? '1') === '0' ? 'selected' : '' ?>>Kapalı (Canlı)</option>
                </select>
            </div>
            
            <p style="font-size:11px;color:#697891;line-height:1.65;margin-top:13px">Merchant Key ve Salt alanlarını boş bırakırsanız mevcut bilgiler korunur. Test modu açıkken gerçek tahsilat onayı kabul edilmez; canlıya geçmeden önce PayTR mağaza panelinden yetkileri ve bildirim adresini doğrulayın.</p>
            <button type="submit" class="adm-action-btn adm-btn-save" style="width: 100%; justify-content: center; margin-top: 1.5rem;">
                <?= icon('save', 16) ?> Ayarları Kaydet
            </button>
        </form>
    </div>
</div>
