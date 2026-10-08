<?php
// Router for GitHub Actions' PHP built-in preview; never used for production.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$real = realpath(__DIR__ . '/../public' . rawurldecode($path));
$root = realpath(__DIR__ . '/../public');
if ($real && $root && str_starts_with($real, $root . DIRECTORY_SEPARATOR) && is_file($real)) {
    return false;
}
require __DIR__ . '/../public/index.php';
