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
