<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Kayıt Ol</h1>
        <p class="subtitle">Hemen ücretsiz hesap oluşturun</p>

        <?php if (!empty($flash['error'])): ?>
            <div class="alert alert-error"><?= icon('alert-circle', 16) ?> <span><?= e($flash['error']) ?></span></div>
        <?php endif; ?>
        <?php if (!empty($flash['errors'])): ?>
            <div class="alert alert-error"><?= icon('alert-circle', 16) ?>
                <div><?php foreach ($flash['errors'] as $err): ?><div><?= e($err) ?></div><?php endforeach; ?></div>
            </div>
        <?php endif; ?>

        <form method="POST" action="/kayit">
            <?= csrfField() ?>
            <div class="form-group">
                <label for="name">Ad Soyad</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= e($flash['old']['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= e($flash['old']['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Telefon <span class="text-secondary text-xs">(opsiyonel)</span></label>
                <input type="tel" id="phone" name="phone" class="form-control" value="<?= e($flash['old']['phone'] ?? '') ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="En az 6 karakter" required>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Şifre Tekrar</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: var(--space-4);">
                <?= icon('user', 18) ?> Kayıt Ol
            </button>
        </form>
        <p class="text-center text-sm" style="margin-top: var(--space-6); color: var(--color-text-secondary);">
            Zaten hesabınız var mı? <a href="/giris">Giriş Yap</a>
        </p>
    </div>
</div>
