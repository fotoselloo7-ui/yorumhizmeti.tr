<?php
/**
 * YorumPanel Pro - Front Controller
 * Tüm istekler bu dosya üzerinden yönlendirilir.
 */

define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

// UTF-8 zorunlu
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

// Kendi autoloader'ımız (Composer gerektirmez)
spl_autoload_register(function (string $class) {
    // App\ prefix'li sınıfları yükle
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relativeClass = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Composer packages are optional in CI, but required for mail and spreadsheet
// integrations on real cPanel installs. Load them only when present.
if (is_file(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

// Helpers yükle
require_once BASE_PATH . '/app/Core/Helpers.php';

// .env yükle (Composer paket gerektirmez)
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}

// Hata raporlama
$debug = ($_ENV['APP_DEBUG'] ?? 'false');
if ($debug === 'true' || $debug === '1') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Uygulama başlat
$app = new App\Core\App();
$app->run();
