<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$home=(string)file_get_contents($root.'/resources/views/frontend/home.php');
if(!preg_match('/<section class="nv31-software".*?<\/section>/s',$home,$section))throw new RuntimeException('Homepage software showcase missing');
if(!str_contains($section[0],'<div class="container">'))throw new RuntimeException('Software must use the site container');
if(!preg_match('/<section class="nv29-services".*?<div class="container">/s',$home))throw new RuntimeException('Expertise must use the site container');
$expertiseCss=(string)file_get_contents($root.'/public/assets/css/home-service-groups-v29.css');
if(str_contains($home,'nv29-container') || str_contains($expertiseCss,'.nv29-container'))throw new RuntimeException('Independent wide frame returned');
$css=(string)file_get_contents($root.'/public/assets/css/software-showcase-v31.css');
if(!str_contains($css,'.nv31-software .nv31-software-image{height:auto;aspect-ratio:16/10}'))throw new RuntimeException('Homepage cover proportions missing');
if(!str_contains($css,'.nv31-software-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr))'))throw new RuntimeException('Original showcase grid changed');
if(str_contains($section[0],'style='))throw new RuntimeException('Inline layout styling returned');
echo "PASS: expertise/software use the site container, independent wide frame removed, grid/data preserved\n";
