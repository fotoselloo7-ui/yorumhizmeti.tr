<?php
declare(strict_types=1);

// Offline tests: logo is an administrator-owned setting with a bundled default.
// Existing site identity, upload handlers and layout remain editable.
$root=dirname(__DIR__);
$get=static fn(string $path): string => (string)file_get_contents($root.'/'.$path);
$check=static function(bool $valid,string $message):void{
    if(!$valid)throw new RuntimeException($message);
};
$layout=$get('resources/views/layouts/app.php');
$form=$get('resources/views/admin/settings/site.php');
$admin=$get('app/Controllers/Admin/SettingsController.php');
$css=$get('public/assets/css/site-logo-v90.css');
$svg=$get('public/assets/img/netvera-brand-v90.svg');
$check(str_contains($svg,'viewBox="0 0 856 179"'),'Default logo missing dimensions');
$check(str_contains($svg,'SOSYAL MEDYA'),'Default logo tagline missing');
$check(str_contains($layout,"setting('site_logo', '')"),'Header does not read saved logo');
$check(str_contains($layout,"asset('img/netvera-brand-v90.svg')"),'Default fallback missing');
$check(substr_count($layout,'$nv90LogoUrl')>=3,'Footer and header do not share selected logo');
$check(str_contains($layout,"'nv90-logo-default'"),'Footer default contrast variant missing');
$check(str_contains($layout,'site-logo-v90.css'),'Responsive logo styles not loaded');
$check(str_contains($form,'enctype="multipart/form-data"'),'Admin form cannot upload files');
$check(str_contains($form,'name="site_logo_file"'),'Admin upload input missing');
$check(str_contains($form,'name="site_logo_reset"'),'Admin reset missing');
$check(str_contains($admin,'Upload::image($logoFile, \'branding\')'),'Existing secure image upload not used');
$check(str_contains($admin,'$config->set(\'site_logo\', $uploadedLogo, \'branding\')'),'Uploaded logo is not saved');
$check(str_contains($admin,'$config->set(\'site_logo\', \'\', \'branding\')'),'Default logo cannot be restored');
$check(str_contains($css,'.nv90-site-logo')&&str_contains($css,'@media(max-width:480px)'),'Mobile logo sizing missing');
echo "PASS: admin-changeable NetVera site logo, uploaded override, reset, responsive header and footer\n";
