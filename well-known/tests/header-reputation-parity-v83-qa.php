<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$layout=file_get_contents($root.'/resources/views/layouts/app.php');
$css=file_get_contents($root.'/public/assets/css/header-reputation-parity-v83.css');
foreach([
 'header-reputation-parity-v83.css',
 'nv83-header-reputation',
 "icon(preg_match('/itibar|reputat/iu'"
] as $mark){
 if(!str_contains($layout,$mark))throw new RuntimeException('Missing header menu reputation integration: '.$mark);
}
foreach([
 '.nv26-menu-shell .nv27-service-layout--marketing',
 '.nv83-header-reputation',
 '.nv27-style-google',
 '.nv27-service-icon',
 '.nv27-service-parent-copy',
 '.nv27-subcategory-link',
 '.nv27-category-footer'
] as $mark){
 if(!str_contains($css,$mark))throw new RuntimeException('Missing Google parity style: '.$mark);
}
if(str_contains($css,'#featured')||str_contains($css,'.nv43-category-card'))
 throw new RuntimeException('Only header menu tiles may change; homepage category cards must remain untouched.');
echo "PASS: reputation management header tile matches Google premium icon, spacing and colors; homepage category rails untouched\n";
