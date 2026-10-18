<?php
/** Public category inventory only; no writes, customer data or credential output. */
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH.'/public');
$_ENV = parse_ini_file(BASE_PATH.'/.env');
if (in_array('--qa', $argv, true)) $_ENV['DB_NAME'] = 'netvera_ui_audit_20261013';
spl_autoload_register(static function (string $class): void {
    if (str_starts_with($class,'App\\')) require_once BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
});
require BASE_PATH.'/app/Core/Helpers.php';
$rows = App\Core\Database::getInstance()->fetchAll('SELECT id,parent_id,name,slug,status,image,image_alt,seo_title,seo_description,description FROM categories ORDER BY id');
echo json_encode($rows, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).PHP_EOL;
