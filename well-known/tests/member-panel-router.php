<?php
/** Local-only isolated member QA harness. No production DB or live payments/email. */
declare(strict_types=1);
if(PHP_SAPI!=='cli-server'||!in_array($_SERVER['REMOTE_ADDR']??'',['127.0.0.1','::1'],true)){http_response_code(404);exit;}
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/');$real=realpath(PUBLIC_PATH.$path);
if(preg_match('~^/uploads/support(?:/|$)~i',$path)){http_response_code(403);exit;}
if($real&&str_starts_with($real,realpath(PUBLIC_PATH).DIRECTORY_SEPARATOR)&&is_file($real))return false;
$_ENV=array_merge($_ENV,parse_ini_file(BASE_PATH.'/.env'),['DB_NAME'=>'netvera_member_qa_20261018','APP_ENV'=>'local','APP_URL'=>'http://127.0.0.1:8019','APP_DEBUG'=>'false','NETVERA_LOCAL_AUTO_SETUP'=>'0','LICENSE_ENABLED'=>'false','LICENSE_LOCAL_BYPASS'=>'true']);
spl_autoload_register(static function(string $c):void{if(str_starts_with($c,'App\\')){$file=BASE_PATH.'/app/'.str_replace('\\','/',substr($c,4)).'.php';if(is_file($file))require_once $file;}});
if(is_file(BASE_PATH.'/vendor/autoload.php'))require BASE_PATH.'/vendor/autoload.php';require BASE_PATH.'/app/Core/Helpers.php';
header('Content-Type: text/html; charset=UTF-8');mb_internal_encoding('UTF-8');session_name('netvera_member_qa');session_start();(new App\Core\App())->run();
