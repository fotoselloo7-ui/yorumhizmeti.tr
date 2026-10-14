<?php
/** Isolated PHP built-in-server harness; not a production entry point. */
declare(strict_types=1);
if (PHP_SAPI !== 'cli-server' || !in_array($_SERVER['REMOTE_ADDR']??'', ['127.0.0.1','::1'], true)) { http_response_code(404); exit; }
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH.'/public');
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI']??'/', PHP_URL_PATH) ?: '/');
$real = realpath(PUBLIC_PATH.$path);
if ($real && str_starts_with($real, realpath(PUBLIC_PATH).DIRECTORY_SEPARATOR) && is_file($real)) return false;
$_ENV = array_merge($_ENV, parse_ini_file(BASE_PATH.'/.env'));
$_ENV['DB_NAME'] = 'netvera_ui_audit_20261013';
$_ENV['APP_ENV'] = 'local';
$_ENV['APP_URL'] = 'http://127.0.0.1:8017';
$_ENV['APP_DEBUG'] = 'false';
$_ENV['NETVERA_LOCAL_AUTO_SETUP'] = '0';
$_ENV['LICENSE_ENABLED'] = 'false';
$_ENV['LICENSE_LOCAL_BYPASS'] = 'true';
spl_autoload_register(static function(string $class):void {
    if (!str_starts_with($class, 'App\\')) return;
    $file=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
    if (is_file($file)) require_once $file;
});
if(is_file(BASE_PATH.'/vendor/autoload.php')) require_once BASE_PATH.'/vendor/autoload.php';
require_once BASE_PATH.'/app/Core/Helpers.php';
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
session_name('netvera_ui_qa');
session_start();
(new App\Core\App())->run();
