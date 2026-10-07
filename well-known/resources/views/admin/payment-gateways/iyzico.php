<div class="adm-page-top">
    <div class="adm-page-top-left">
        <a href="/admin/odeme-modulleri" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Geri</a>
        <h2><?= icon('settings', 24) ?> iyzico Ayarları</h2>
    </div>
</div>

<div class="adm-card" style="max-width: 600px; margin: 0 auto;">
    <div class="adm-card-header">
        <h3><?= icon('key', 16) ?> API Bağlantı Bilgileri</h3>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="/admin/iyzico-ayarlari/kaydet">
            <?= csrfField() ?>
            <div class="form-group">
                <label>API Key</label>
                <input type="text" name="api_key" class="form-control" value="<?= e($settings['api_key'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Secret Key</label>
                <input type="password" name="secret_key" class="form-control" value="<?= e($settings['secret_key'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>API URL</label>
                <select name="base_url" class="form-control">
                    <option value="https://sandbox-api.iyzipay.com" <?= ($settings['base_url'] ?? '') === 'https://sandbox-api.iyzipay.com' ? 'selected' : '' ?>>Sandbox (Test)</option>
                    <option value="https://api.iyzipay.com" <?= ($settings['base_url'] ?? '') === 'https://api.iyzipay.com' ? 'selected' : '' ?>>Canlı</option>
                </select>
            </div>
            
            <button type="submit" class="adm-action-btn adm-btn-save" style="width: 100%; justify-content: center; margin-top: 1.5rem;">
                <?= icon('save', 16) ?> Ayarları Kaydet
            </button>
        </form>
    </div>
</div>
