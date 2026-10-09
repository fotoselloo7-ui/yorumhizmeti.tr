<div class="adm-page-top">
    <div>
        <h2><?= icon('mail', 24) ?> SMTP Ayarları</h2>
    </div>
</div>

<div class="adm-card" style="max-width: 700px; margin: 0 auto;">
    <div class="adm-card-header">
        <h3><?= icon('settings', 16) ?> Sunucu Bağlantı Ayarları</h3>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="/admin/smtp-ayarlari/kaydet">
            <?= csrfField() ?>
            <div class="form-group">
                <label>SMTP Host</label>
                <input type="text" name="smtp_host" class="form-control" value="<?= e(setting('smtp_host')) ?>" placeholder="smtp.yandex.com">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Port</label>
                    <input type="number" name="smtp_port" class="form-control" value="<?= e(setting('smtp_port', '587')) ?>">
                </div>
                <div class="form-group">
                    <label>Şifreleme</label>
                    <select name="smtp_encryption" class="form-control">
                        <option value="tls" <?= setting('smtp_encryption') === 'tls' ? 'selected' : '' ?>>TLS</option>
                        <option value="ssl" <?= setting('smtp_encryption') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Kullanıcı Adı</label>
                <input type="text" name="smtp_username" class="form-control" value="<?= e(setting('smtp_username')) ?>">
            </div>
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="smtp_password" class="form-control" value="<?= e(setting('smtp_password')) ?>">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Gönderen E-posta</label>
                    <input type="email" name="smtp_from_email" class="form-control" value="<?= e(setting('smtp_from_email')) ?>">
                </div>
                <div class="form-group">
                    <label>Gönderen Ad</label>
                    <input type="text" name="smtp_from_name" class="form-control" value="<?= e(setting('smtp_from_name')) ?>">
                </div>
            </div>
            <div style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="submit" class="adm-action-btn adm-btn-save" style="flex: 1; justify-content: center;">
                    <?= icon('save', 16) ?> SMTP Ayarlarını Kaydet
                </button>
                <a href="/admin/site-ayarlari" class="btn btn-light" style="display:flex; align-items:center; justify-content:center; padding: 10px 20px; font-weight:600; text-decoration:none; border-radius:8px;">
                    Geri Dön
                </a>
            </div>
        </form>
    </div>
</div>
