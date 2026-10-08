<main class="auth-wrapper auth25-page"><div class="auth25-shell">
<section class="auth25-showcase" aria-label="Üyelik avantajları">
<a class="auth25-brand" href="/" aria-label="Ana sayfa"><span class="auth25-brandmark">Y</span><span><strong><?= e(setting('site_name','YorumHizmeti.tr')) ?></strong><small>Dijital hizmet platformu</small></span></a>
<div class="auth25-orbit auth25-orbit-one" aria-hidden="true"></div><div class="auth25-orbit auth25-orbit-two" aria-hidden="true"></div>
<div class="auth25-showcase-main">
<span class="auth25-eyebrow"><?= icon('sparkles',15) ?> Tüm hizmetler tek panelde</span>
<h2>Siparişlerinizi <em>kolayca yönetin.</em></h2>
<p>Hizmetlerinizi keşfedin, siparişlerinizi takip edin ve desteğe tek noktadan ulaşın.</p>
<div class="auth25-benefits">
<span><?= icon('shield-check',16) ?> Güvenli hesap</span><span><?= icon('package-check',16) ?> Sipariş takibi</span><span><?= icon('headphones',16) ?> Destek merkezi</span>
</div>
</div>
<div class="auth25-art" aria-hidden="true"><div class="auth25-art-ring"></div><img src="<?= asset('img/hero-woman-cutout.png') ?>" alt="" loading="eager"></div>
<div class="auth25-float"><span><?= icon('check-circle',19) ?></span><div><strong>Tüm siparişler kontrolünüzde</strong><small>Her şey tek, düzenli bir panelde.</small></div></div>
<a href="/" class="auth25-back"><?= icon('arrow-left',14) ?> Ana sayfaya dön</a>
</section>
<section class="auth25-panel" aria-labelledby="auth-title"><div class="auth25-panel-body">
<a class="auth25-mobile-logo" href="/"><span class="auth25-brandmark">Y</span><strong><?= e(setting('site_name','YorumHizmeti.tr')) ?></strong></a>
<span class="auth25-tag"><?= icon('log-in',14) ?> Üye Girişi</span>
<h1 id="auth-title">Tekrar hoş geldiniz.</h1>
<p class="auth25-lead">Siparişlerinize ve hesabınıza erişmek için giriş yapın.</p>
<?php if(!empty($flash['error'])): ?><div class="auth25-alert" role="alert"><?= icon('alert-circle',17) ?><span><?= e($flash['error']) ?></span></div><?php endif; ?>
<form method="POST" action="/giris" class="auth25-form">
<?= csrfField() ?>
<div class="auth25-field"><label for="email">E-posta adresi</label><div class="auth25-input-wrap"><span class="auth25-field-icon"><?= icon('mail',17) ?></span><input id="email" name="email" type="email" value="<?= e($flash['old']['email']??'') ?>" placeholder="ornek@email.com" autocomplete="email" inputmode="email" required></div></div>
<div class="auth25-field"><div class="auth25-label-row"><label for="password">Şifre</label><a href="/sifremi-unuttum">Şifremi unuttum</a></div><div class="auth25-input-wrap"><span class="auth25-field-icon"><?= icon('lock',17) ?></span><input id="password" name="password" type="password" autocomplete="current-password" placeholder="Şifrenizi girin" required><button class="auth25-eye" type="button" data-auth-toggle="password" aria-label="Şifreyi göster" aria-pressed="false"><?= icon('eye',17) ?></button></div></div>
<button class="auth25-submit" type="submit"><?= icon('log-in',17) ?><span>Giriş Yap</span><?= icon('arrow-right',17) ?></button>
</form>
<p class="auth25-switch">Henüz hesabınız yok mu? <a href="/kayit">Ücretsiz kayıt olun <?= icon('arrow-up-right',13) ?></a></p>
<div class="auth25-secure"><?= icon('shield-check',15) ?> Bilgileriniz güvenli bağlantıyla iletilir.</div>
</div><footer class="auth25-footer"><span>© <?= date('Y') ?> <?= e(setting('site_name','YorumHizmeti.tr')) ?></span><a href="/sayfa/gizlilik-politikasi">Gizlilik Politikası</a></footer>
</section></div></main>