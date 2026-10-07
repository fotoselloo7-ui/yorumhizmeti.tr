<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Admin Paneli</h1>
        <p class="subtitle">Yönetim paneline giriş yapın</p>
        <?php if (!empty($flash['error'])): ?><div class="alert alert-error"><?= icon('alert-circle', 16) ?> <span><?= e($flash['error']) ?></span></div><?php endif; ?>
        <form method="POST" action="/admin/giris">
            <?= csrfField() ?>
            <div class="form-group"><label for="email">E-posta</label><input type="email" id="email" name="email" class="form-control" required></div>
            <div class="form-group"><label for="password">Şifre</label><input type="password" id="password" name="password" class="form-control" required></div>
            <button type="submit" class="btn btn-primary btn-block btn-lg"><?= icon('lock', 18) ?> Giriş Yap</button>
        </form>
    </div>
</div>
