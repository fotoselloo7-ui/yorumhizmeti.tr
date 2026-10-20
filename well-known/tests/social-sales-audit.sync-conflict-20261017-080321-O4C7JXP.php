<?php
/** Read-only local inventory. Never prints credentials or customer/order data. */
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$config = parse_ini_file(dirname(__DIR__).'/.env');
$schema = in_array('--qa', $argv, true) ? 'netvera_ui_audit_20261013' : $config['DB_NAME'];
$pdo = new PDO('mysql:host='.$config['DB_HOST'].';dbname='.$schema.';charset=utf8mb4', $config['DB_USER'], $config['DB_PASS'] ?? '', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$columns = $pdo->query('SHOW COLUMNS FROM packages')->fetchAll();
$categories = $pdo->query("SELECT id,parent_id,name,slug FROM categories WHERE name LIKE '%Instagram%' OR slug='sosyal-medya-hizmetleri' ORDER BY parent_id,id")->fetchAll();
$packages = $pdo->query("SELECT p.id,p.category_id,p.name,p.slug,p.price,p.min_quantity,p.max_quantity,LEFT(p.short_description,170) AS summary FROM packages p JOIN categories c ON c.id=p.category_id LEFT JOIN categories parent ON parent.id=c.parent_id WHERE c.name LIKE '%Instagram%' OR parent.name LIKE '%Instagram%' ORDER BY p.category_id,p.sort_order,p.id LIMIT 25")->fetchAll();
$mappings = $pdo->query('SELECT package_id,fulfillment_quantity,enabled FROM smm_package_links ORDER BY package_id LIMIT 30')->fetchAll();
echo json_encode(['target'=>in_array('--qa',$argv,true)?'isolated QA':'configured local','package_columns'=>array_column($columns,'Field'),'categories'=>$categories,'packages'=>$packages,'mappings'=>$mappings],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).PHP_EOL;
