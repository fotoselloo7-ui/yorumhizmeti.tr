<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$read=static fn(string $path):string=>(string)file_get_contents($root.'/'.$path);
$base=$read('public/assets/css/app.css');
foreach(['--page-gutter: 20px','--page-width: calc(100% - (var(--page-gutter) * 2))','width: var(--page-width)','max-width: none','.container .container'] as $token) {
    if(!str_contains($base,$token))throw new RuntimeException('Shared fluid contract missing: '.$token);
}
$styles=[];
foreach(['app','auth'] as $layout) {
    preg_match_all("/asset\('css\/([^']+\.css)'\)/",$read('resources/views/layouts/'.$layout.'.php'),$matches);
    $styles=array_merge($styles,$matches[1]);
}
$styles[]='paytr-payment-v45.css';
foreach(array_unique($styles) as $file) {
    $css=$read('public/assets/css/'.$file);
    if(str_contains($css,'--page-max-width') || preg_match('/--page-gutter\s*:\s*(?!20px)[^;}]+/',$css))throw new RuntimeException('Bounded/variable outer gutter returned: '.$file);
    if($file==='app.css')continue;
    if(preg_match('/--page-(?:gutter|width)\s*:/',$css))throw new RuntimeException('Duplicate frame token owner: '.$file);
    preg_match_all('/([^{}]+)\{([^{}]*)\}/',$css,$rules,PREG_SET_ORDER);
    foreach($rules as $rule) {
        $selector=trim((string)preg_replace('/\/\*.*?\*\//s','',$rule[1]));
        if(preg_match('/\.container\s*(?:,|$)/',$selector) && preg_match('/(?:^|;)\s*(?:width|max-width)\s*:/',$rule[2]))throw new RuntimeException('Competing outer container owner: '.$file.' '.$selector);
    }
}
$home=$read('resources/views/frontend/home.php');
if(str_contains($home,'nv29-container') || str_contains($read('public/assets/css/home-service-groups-v29.css'),'nv29-container'))throw new RuntimeException('Expertise custom frame returned');
$auth=$read('public/assets/css/auth-premium-v25.css');
foreach(['padding:clamp(15px,3.6vw,48px) 20px','padding:19px 20px','padding:12px 20px','max-width:450px'] as $token) {
    if(!str_contains($auth,$token))throw new RuntimeException('Auth outer gutter / readable inner form lost: '.$token);
}
if(str_contains($auth,'width:min(1140px,100%)') || str_contains($auth,'max-width:590px'))throw new RuntimeException('Auth outer shell cap returned');
foreach(['software-showcase-v31.css','netvera-legacy-premium-v40.css'] as $file) {
    $css=$read('public/assets/css/'.$file);
    if(!str_contains($css,'minmax(min(100%,260px),1fr)') || !str_contains($css,'aspect-ratio:16/10'))throw new RuntimeException('Fluid software grid/image ratio missing: '.$file);
}
echo "PASS: one fluid container owner, 20px at every breakpoint, nested gutters excluded, readable forms and adaptive software grids\n";
