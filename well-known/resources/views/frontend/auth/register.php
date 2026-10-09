<main class="auth-wrapper auth25-page"><div class="auth25-shell auth25-register">
<section class="auth25-showcase" aria-label="Üyelik avantajları">
<a class="auth25-brand" href="/" aria-label="Ana sayfa"><span class="auth25-brandmark">Y</span><span><strong><?= e(setting('site_name','NetVera Teknoloji Yazılım')) ?></strong><small>Dijital hizmet platformu</small></span></a>
<div class="auth25-orbit auth25-orbit-one" aria-hidden="true"></div><div class="auth25-orbit auth25-orbit-two" aria-hidden="true"></div>
<div class="auth25-showcase-main">
<span class="auth25-eyebrow"><?= icon('sparkles',15) ?> Ücretsiz üyelik</span>
<h2>Dijital dünyada <em>güçlenmeye başlayın.</em></h2>
<p>Tek bir hesapla hizmet paketlerini keşfedin, sipariş oluşturun ve tüm süreci rahatça takip edin.</p>
<div class="auth25-benefits">
<span><?= icon('shield-check',16) ?> Güvenli hesap</span><span><?= icon('package-check',16) ?> Sipariş takibi</span><span><?= icon('headphones',16) ?> Destek merkezi</span>
</div>
</div>
<div class="auth25-art" aria-hidden="true"><div class="auth25-art-ring"></div><img src="<?= asset('img/hero-woman-cutout.png') ?>" alt="" loading="eager"></div>
<div class="auth25-float"><span><?= icon('check-circle',19) ?></span><div><strong>Yeni hesabınız sizi bekliyor</strong><small>Her şey tek, düzenli bir panelde.</small></div></div>
<a href="/" class="auth25-back"><?= icon('arrow-left',14) ?> Ana sayfaya dön</a>
</section>
<section class="auth25-panel" aria-labelledby="auth-title"><div class="auth25-panel-body">
<a class="auth25-mobile-logo" href="/"><span class="auth25-brandmark">Y</span><strong><?= e(setting('site_name','NetVera Teknoloji Yazılım')) ?></strong></a>
<span class="auth25-tag"><?= icon('user-plus',14) ?> Yeni Hesap</span>
<h1 id="auth-title">Hesabınızı oluşturun.</h1>
<p class="auth25-lead">Ücretsiz kaydolun, hizmetleri tek panelden yönetin.</p>
<?php if(!empty($flash['error'])): ?><div class="auth25-alert" role="alert"><?= icon('alert-circle',17) ?><span><?= e($flash['error']) ?></span></div><?php endif; ?>
<?php if(!empty($flash['errors'])): ?><div class="auth25-alert" role="alert"><?= icon('alert-circle',17) ?><div><?php foreach($flash['errors'] as $err): ?><div><?= e($err) ?></div><?php endforeach; ?></div></div><?php endif; ?>
<form method="POST" action="/kayit" class="auth25-form" data-register-form>
<?= csrfField() ?>
<div class="auth25-field"><label for="name">Ad soyad</label><div class="auth25-input-wrap"><span class="auth25-field-icon"><?= icon('user-round',17) ?></span><input id="name" name="name" type="text" value="<?= e($flash['old']['name']??'') ?>" placeholder="Adınız ve soyadınız" autocomplete="name" minlength="2" maxlength="100" required></div></div>
<div class="auth25-fields-two">
<div class="auth25-field"><label for="email">E-posta adresi</label><div class="auth25-input-wrap"><span class="auth25-field-icon"><?= icon('mail',17) ?></span><input id="email" name="email" type="email" value="<?= e($flash['old']['email']??'') ?>" placeholder="ornek@email.com" autocomplete="email" inputmode="email" required></div></div>
<div class="auth25-field"><label for="phone">Telefon <span class="auth25-optional">(isteğe bağlı)</span></label><div class="auth25-input-wrap"><span class="auth25-field-icon"><?= icon('phone',17) ?></span><input id="phone" name="phone" type="tel" value="<?= e($flash['old']['phone']??'') ?>" placeholder="05xx xxx xx xx" autocomplete="tel" maxlength="20"></div></div>
</div>
<div class="auth25-fields-two">
<div class="auth25-field"><label for="password">Şifre</label><div class="auth25-input-wrap"><span class="auth25-field-icon"><?= icon('lock',17) ?></span><input id="password" name="password" type="password" placeholder="En az 6 karakter" autocomplete="new-password" minlength="6" required><button class="auth25-eye" type="button" data-auth-toggle="password" aria-label="Şifreyi göster" aria-pressed="false"><?= icon('eye',17) ?></button></div></div>
<div class="auth25-field"><label for="password_confirmation">Şifre tekrarı</label><div class="auth25-input-wrap"><span class="auth25-field-icon"><?= icon('lock',17) ?></span><input id="password_confirmation" name="password_confirmation" type="password" placeholder="En az 6 karakter" autocomplete="new-password" minlength="6" required><button class="auth25-eye" type="button" data-auth-toggle="password_confirmation" aria-label="Şifreyi göster" aria-pressed="false"><?= icon('eye',17) ?></button></div></div>
</div>
<p class="auth25-hint"><?= icon('info',14) ?> En az 6 karakterli güçlü bir şifre oluşturun.</p>
<button class="auth25-submit" type="submit"><?= icon('user-plus',17) ?><span>Ücretsiz Hesap Oluştur</span><?= icon('arrow-right',17) ?></button>
</form>
<p class="auth25-switch">Zaten hesabınız var mı? <a href="/giris">Giriş yapın <?= icon('arrow-up-right',13) ?></a></p>
<div class="auth25-secure"><?= icon('shield-check',15) ?> Kişisel bilgileriniz güvenle saklanır.</div>
</div><footer class="auth25-footer"><span>© <?= date('Y') ?> <?= e(setting('site_name','NetVera Teknoloji Yazılım')) ?></span><a href="/sayfa/gizlilik-politikasi">Gizlilik Politikası</a></footer>
</section></div></main>