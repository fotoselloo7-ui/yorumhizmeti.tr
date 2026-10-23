<?php
use App\Services\LandingThemeService as Themes;
$d=$landing;$seo=$d['seo'];$themeKey=Themes::key($d['theme_key']??'');
$profile=Themes::profile($themeKey);
$blocks=Themes::blocks($d,['services'=>$landingServices,'scripts'=>$landingScripts,'references'=>$landingReferences,'blog'=>$landingBlogs]);
$mode=['remote'=>'Uzaktan / çevrim içi','hybrid'=>'Uzaktan ve planlı görüşme','onsite'=>'Planlı yerinde çalışma'][$d['working_mode']];
?>
<div data-landing-theme-host data-page-id="<?= (int)$landingRow['id'] ?>" data-default-theme="<?= e($themeKey) ?>" data-admin-preview="<?= $landingPreview?'1':'0' ?>">
<?php if($themeKey===''): require __DIR__.'/legacy.php'; else: require __DIR__.'/themes/page.php'; endif; ?>
<?php if($d['theme_switcher']??true): require __DIR__.'/themes/switcher.php'; ?>
<?php if($themeKey===''): ?><template data-landing-modern><?php $themeKey=Themes::DEFAULT_KEY;$profile=Themes::profile($themeKey);ob_start();require __DIR__.'/themes/page.php';echo str_replace(['<h1>','</h1>'],['<span data-nvl-h1>','</span>'],ob_get_clean());$themeKey=''; ?></template><?php endif; ?>
<?php foreach(Themes::all() as $heroKey=>$heroProfile): ?><template data-landing-hero="<?= e($heroKey) ?>"><?php $heroVariant=$heroProfile['hero'];ob_start();require __DIR__.'/themes/hero.php';echo str_replace(['<h1>','</h1>'],['<span data-nvl-h1>','</span>'],ob_get_clean()); ?></template><?php endforeach; ?>
<?php endif; ?>
</div>
