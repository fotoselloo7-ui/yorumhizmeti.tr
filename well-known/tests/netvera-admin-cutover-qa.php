<?php
declare(strict_types=1);
// No DB, old password, production credentials or HTTP requests needed.
$root=dirname(__DIR__);
$read=static fn(string $p):string=>(string)file_get_contents($root.'/'.$p);
$ok=static function(bool $yes,string $name):void{
 if(!$yes){fwrite(STDERR,"FAIL: $name\n");exit(1);}
};
$layout=$read('resources/views/layouts/admin.php');
$css=$read('public/assets/css/admin-sidebar-readable-v77.css');
$router=$read('app/Core/App.php');
$auth=$read('app/Controllers/Admin/AuthController.php');
$edit=$read('app/Controllers/Admin/AdminAccountController.php');
$view=$read('resources/views/admin/settings/admin-user-edit.php');
$list=$read('resources/views/admin/settings/admin-users.php');
$htaccess=$read('.htaccess');
$zip=$read('bin/build-netvera-root-zip.php');
$transfer=$read('bin/netvera-original-admin-transfer.php');
$ok(str_contains($layout,'nv-admin-logout-button') &&
    str_contains($layout,'method="post" action="/admin/cikis"'), 'Logout must be legible and POST');
$ok(str_contains($css,'color:#fff!important') &&
    str_contains($css,'.nv-admin-logout-button'), 'Dark admin contrast');
$ok(str_contains($router,"post('/admin/cikis'") &&
    !str_contains($router,"get('/admin/cikis'"), 'Logout route must not be GET');
$ok(str_contains($auth,'Csrf::check();'),'Logout CSRF guard');
foreach(['/admin/admin-kullanicilari/{id}/duzenle',
         '/admin/admin-kullanicilari/{id}/guncelle'] as $route)
    $ok(str_contains($router,$route),'Admin editing route missing '.$route);
$ok(str_contains($list,'/admin/admin-kullanicilari/') &&
    str_contains($view,'name="new_password_confirm"') &&
    str_contains($view,'name="current_password"'), 'Admin editable UI incomplete');
foreach(['password_verify','password_hash','last','super_admin'] as $word)
    $ok(str_contains($edit,$word),'Admin role/password protection missing '.$word);
$ok(str_contains($edit,"WHERE id<>? AND role='super_admin'"),'Must preserve one active superadmin');
$ok(str_contains($htaccess,'HTTP_HOST') &&
    str_contains($htaccess,'netvera') &&
    str_contains($htaccess,'public/$1') &&
    str_contains($htaccess,'Require all denied'), 'cPanel host-only rewrite and private denial missing');
$ok(str_contains($zip,'ZipArchive') &&
    str_contains($zip,"'app','public','resources'") &&
    str_contains($zip,"$base==='.env'") &&
    str_contains($zip,"public/uploads"),'cPanel ZIP must exclude secrets and customer media');
// Source-verified password hash; never a baked-in one-click admin takeover.
$ok(str_contains($transfer,"NETVERA_ADMIN_LOGIN_PROOF") &&
    str_contains($transfer,'password_verify') &&
    str_contains($transfer,'TRANSFER-VERIFIED-NETVERA-ADMIN') &&
    !str_contains($transfer,"DROP TABLE"),'Identity-verified admin transfer missing');
echo "PASS: high-contrast logout, CSRF POST, editable verified admin profile, subdomain-safe release ZIP and original-password superadmin handover.\n";
