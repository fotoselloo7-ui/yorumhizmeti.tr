<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Giriş Yap</h1>
        <p class="subtitle">Hesabınıza giriş yaparak siparişlerinizi takip edin</p>

        <?php if (!empty($flash['error'])): ?>
            <div class="alert alert-error"><?= icon('alert-circle', 16) ?> <span><?= e($flash['error']) ?></span></div>
        <?php endif; ?>

        <form method="POST" action="/giris">
            <?= csrfField() ?>
            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= e($flash['old']['email'] ?? '') ?>" placeholder="ornek@email.com" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-5);">
                <a href="/sifremi-unuttum" class="text-sm" style="color: var(--color-blue);">Şifremi Unuttum</a>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <?= icon('log-in', 18) ?> Giriş Yap
            </button>
        </form>
        <p class="text-center text-sm" style="margin-top: var(--space-6); color: var(--color-text-secondary);">
            Hesabınız yok mu? <a href="/kayit">Kayıt Ol</a>
        </p>
    </div>
</div>
