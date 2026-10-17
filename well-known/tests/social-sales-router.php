<?php
/** Dedicated loopback-only test server, never a production entry point. */
declare(strict_types=1);
if(PHP_SAPI!=='cli-server'||!in_array($_SERVER['REMOTE_ADDR']??'',['127.0.0.1','::1'],true)){http_response_code(404);exit;}
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/');
$file=realpath(PUBLIC_PATH.$path);
if($file&&str_starts_with($file,realpath(PUBLIC_PATH).DIRECTORY_SEPARATOR)&&is_file($file))return false;
$_ENV=array_merge($_ENV,parse_ini_file(BASE_PATH.'/.env'));
$_ENV['DB_NAME']='netvera_social_sales_qa_20261017';$_ENV['APP_URL']='http://127.0.0.1:8018';
$_ENV['APP_ENV']='local';$_ENV['APP_DEBUG']='false';$_ENV['NETVERA_LOCAL_AUTO_SETUP']='0';
$_ENV['LICENSE_ENABLED']='false';$_ENV['LICENSE_LOCAL_BYPASS']='true';
spl_autoload_register(static function(string $class):void{if(str_starts_with($class,'App\\')){ $f=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';if(is_file($f))require_once $f;}});
if(is_file(BASE_PATH.'/vendor/autoload.php'))require_once BASE_PATH.'/vendor/autoload.php';
require BASE_PATH.'/app/Core/Helpers.php';
header('Content-Type: text/html; charset=UTF-8');mb_internal_encoding('UTF-8');
session_name('netvera_social_sales_qa');session_start();(new App\Core\App())->run();
