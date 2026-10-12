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
$logoPath='public/assets/img/netvera-logo-current.svg';
$footerLogoPath='public/assets/img/netvera-logo-footer-white.svg';
$check(is_file($root.'/'.$logoPath),'Default NetVera logo asset missing');
$check(is_file($root.'/'.$footerLogoPath),'White footer logo asset missing');
$check(filesize($root.'/'.$logoPath)>5000,'Default NetVera logo asset looks invalid');
$check(filesize($root.'/'.$footerLogoPath)>5000,'White footer logo asset looks invalid');
$service=$get('app/Services/SiteLogoService.php');
$check(str_contains($service,"setting('site_logo_mode', 'default')"),'Legacy imported logo might hide default');
$check(str_contains($service,"setting('site_logo', '')"),'Custom logo setting unreadable');
$check(str_contains($service,'is_file($full)'),'Uploaded logo file is not checked');
$check(str_contains($service,"'img/netvera-logo-current.svg'"),'Default fallback missing');
$check(str_contains($service,"'img/netvera-logo-footer-white.svg'"),'White footer fallback missing');
$check(str_contains($service,'footerUrl()'),'Footer logo resolver missing');
$check(str_contains($layout,'SiteLogoService::url()'),'Header does not use shared logo service');
$check(str_contains($layout,'onerror="this.onerror=null;this.src='),'Broken image fallback missing');
$check(str_contains($layout,'$nv90LogoUrl'),'Header logo URL missing');
$check(str_contains($layout,'$nv90FooterLogoUrl'),'Footer logo URL missing');
$check(str_contains($layout,'$nv90FooterDefaultLogoUrl'),'Footer fallback URL missing');
$check(str_contains($layout,'site-logo-v90.css'),'Responsive logo styles not loaded');
$check(str_contains($form,'enctype="multipart/form-data"'),'Admin form cannot upload files');
$check(str_contains($form,'name="site_logo_file"'),'Admin upload input missing');
$check(str_contains($form,'name="site_logo_reset"'),'Admin reset missing');
$check(str_contains($admin,'Upload::image($logoFile, \'branding\')'),'Existing secure image upload not used');
$check(str_contains($admin,'$config->set(\'site_logo\', $uploadedLogo, \'branding\')'),'Uploaded logo is not saved');
$check(str_contains($admin,'$config->set(\'site_logo_mode\', \'custom\', \'branding\')'),'Admin upload does not activate custom logo');
$check(str_contains($admin,'$config->set(\'site_logo\', \'\', \'branding\')'),'Default logo cannot be restored');
$check(str_contains($admin,'$config->set(\'site_logo_mode\', \'default\', \'branding\')'),'Reset does not restore bundled logo mode');
$check(str_contains($css,'.nv90-site-logo')&&str_contains($css,'@media(max-width:480px)'),'Mobile logo sizing missing');
// Behavioral regression: a stale logo path from the imported project must
// never override the shipped NetVera logo until this admin uploads a new one.
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', $root.'/public');
$GLOBALS['nv90LogoTestSettings']=[];
function setting(string $key,string $default=''):string {
    return (string)($GLOBALS['nv90LogoTestSettings'][$key]??$default);
}
function asset(string $path):string {return '/assets/'.ltrim($path,'/');}
function upload_url(?string $path,string $fallbackType='default'):string {
    return (string)$path;
}
require_once $root.'/app/Services/SiteLogoService.php';
$logoClass=\App\Services\SiteLogoService::class;
$expected='/assets/img/netvera-logo-current.svg';
$expectedFooter='/assets/img/netvera-logo-footer-white.svg';
$GLOBALS['nv90LogoTestSettings']=['site_logo'=>'/uploads/branding/retired-logo.png'];
$check($logoClass::url()===$expected,'Legacy logo path hides NetVera default');
$check($logoClass::footerUrl()===$expectedFooter,'Footer does not use white default logo');
$GLOBALS['nv90LogoTestSettings']['site_logo_mode']='custom';
$check($logoClass::url()===$expected,'Nonexistent uploaded logo hides default');
$GLOBALS['nv90LogoTestSettings']['site_logo']='https://example.org/brand.png';
$check($logoClass::url()==='https://example.org/brand.png','Admin HTTPS logo not honored');
$GLOBALS['nv90LogoTestSettings']['site_logo_mode']='default';
$check($logoClass::url()===$expected,'Reset does not restore default');
$check($logoClass::footerUrl()===$expectedFooter,'Reset does not restore white footer logo');

echo "PASS: admin-changeable NetVera site logo, uploaded override, reset, responsive header and footer\n";
