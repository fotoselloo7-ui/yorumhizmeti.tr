<a class="<?= !empty($authBrandMobile)?'auth25-mobile-logo':'auth25-brand' ?>" href="/" aria-label="NetVera ana sayfa">
 <span class="auth25-logo-surface"><img class="auth25-brand-logo" src="<?= e(\App\Services\SiteLogoService::url()) ?>" width="156" height="30" alt="<?= e(setting('site_name','NetVera Teknoloji Yazılım')) ?>"></span>
 <?php if(empty($authBrandMobile)): ?><span class="auth25-brand-copy"><small>Dijital hizmet platformu</small></span><?php endif; ?>
</a>
