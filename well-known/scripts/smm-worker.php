<?php
/**
 * cPanel Cron (CLI only), every 5 minutes:
 * /usr/local/bin/php /home/CPANEL_USER/public_html/well-known/scripts/smm-worker.php
 * Verify your hosting document root; keep this script outside publicly served assets.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
define('BASE_PATH', dirname(__DIR__));
spl_autoload_register(static function (string $class): void {
    if (!str_starts_with($class,'App\\')) return;
    $file=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
    if (is_file($file)) require $file;
});
require_once BASE_PATH.'/app/Core/Helpers.php';
$env=BASE_PATH.'/.env';
if (is_file($env)) {
    foreach (file($env,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line=trim($line);
        if ($line==='' || $line[0]==='#' || !str_contains($line,'=')) continue;
        [$k,$v]=explode('=',$line,2);
        $_ENV[trim($k)]=trim($v," \t\n\r\0\x0B\"'");
    }
}
// Prevent concurrent invocations on the same server.
$lock=fopen(sys_get_temp_dir().'/yorumhizmeti_smm_worker.lock','c+');
if (!$lock || !flock($lock,LOCK_EX|LOCK_NB)) exit(0);
try {
    $result=\App\Services\SmmFulfillmentService::process(30);
    fwrite(STDOUT,json_encode($result,JSON_UNESCAPED_UNICODE).PHP_EOL);
} catch (\Throwable $e) {
    error_log('SMM cron failed: '.get_class($e).' '.$e->getMessage());
    fwrite(STDERR,"SMM worker failed; review server logs.\n");
    exit(1);
} finally {
    flock($lock,LOCK_UN);
    fclose($lock);
}
