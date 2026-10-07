<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Şifremi Unuttum</h1>
        <p class="subtitle">E-posta adresinizi girin, şifre sıfırlama linki gönderelim</p>
        <?php if (!empty($flash['success'])): ?>
            <div class="alert alert-success"><?= icon('check-circle', 16) ?> <span><?= e($flash['success']) ?></span></div>
        <?php endif; ?>
        <form method="POST" action="/sifremi-unuttum">
            <?= csrfField() ?>
            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg"><?= icon('send', 18) ?> Gönder</button>
        </form>
        <p class="text-center text-sm" style="margin-top: var(--space-6);"><a href="/giris">Giriş sayfasına dön</a></p>
    </div>
</div>
