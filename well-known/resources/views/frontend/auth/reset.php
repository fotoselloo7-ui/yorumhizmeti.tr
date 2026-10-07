<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Şifre Sıfırla</h1>
        <p class="subtitle">Yeni şifrenizi belirleyin</p>
        <form method="POST" action="/sifre-sifirla">
            <?= csrfField() ?>
            <input type="hidden" name="token" value="<?= e($token) ?>">
            <div class="form-group">
                <label for="password">Yeni Şifre</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="En az 6 karakter" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Şifre Tekrar</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg"><?= icon('lock', 18) ?> Şifreyi Güncelle</button>
        </form>
    </div>
</div>
