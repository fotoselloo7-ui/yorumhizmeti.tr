<?php
/** Disposable visual QA database. Never imports into an existing database. */
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$root = dirname(__DIR__);
$config = parse_ini_file($root.'/.env');
$name = 'netvera_ui_audit_20261013';
$pdo = new PDO('mysql:host='.$config['DB_HOST'].';charset=utf8mb4', $config['DB_USER'], $config['DB_PASS'] ?? '', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$exists = $pdo->prepare('SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME=?');
$exists->execute([$name]);
if ($exists->fetchColumn()) { fwrite(STDERR, "QA database already exists; refusing to overwrite.\n"); exit(2); }
$pdo->exec('CREATE DATABASE '.$name.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$pdo->exec('USE '.$name);
$pdo->exec(file_get_contents($root.'/database/database.sql'));
$pdo->prepare('UPDATE admins SET email=?,password=?,must_change_password=0 WHERE id=1')->execute(['ui-admin@example.test',password_hash('synthetic-ui-audit-only',PASSWORD_DEFAULT)]);
$pdo->exec("UPDATE payment_gateways SET is_active=0");
$pdo->prepare('INSERT INTO users (name,email,password,status) VALUES (?,?,?,?)')->execute(['UI Audit','ui-user@example.test',password_hash('synthetic-ui-audit-only',PASSWORD_DEFAULT),'active']);
echo "Created disposable UI audit schema; production database untouched.\n";
