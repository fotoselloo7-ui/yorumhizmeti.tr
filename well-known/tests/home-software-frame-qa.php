<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$home=(string)file_get_contents($root.'/resources/views/frontend/home.php');
if(!preg_match('/<section class="nv31-software".*?<\/section>/s',$home,$section))throw new RuntimeException('Homepage software showcase missing');
if(!str_contains($section[0],'<div class="nv29-container nv31-container">'))throw new RuntimeException('Adjacent home sections must share the same frame');
if(substr_count($home,'nv29-container')!==2)throw new RuntimeException('Wide frame scope must remain limited to the two home sections');
$css=(string)file_get_contents($root.'/public/assets/css/software-showcase-v31.css');
if(!str_contains($css,'.nv31-software .nv31-software-image{height:auto;aspect-ratio:16/10}'))throw new RuntimeException('Homepage cover proportions missing');
if(!str_contains($css,'.nv31-software-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr))'))throw new RuntimeException('Original showcase grid changed');
if(str_contains($section[0],'style='))throw new RuntimeException('Inline layout styling returned');
echo "PASS: homepage-only shared expertise/software frame, preserved grid/data, proportional cover images\n";
