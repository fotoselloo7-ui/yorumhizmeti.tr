<?php
/**
 * NetVera catalog expansion – additive, reviewed drafts only.
 *
 * php bin/netvera-market-catalog-expand.php
 * php bin/netvera-market-catalog-expand.php --apply-drafts --ack=NETVERA-MARKET-DRAFTS-REVIEWED
 *
 * Creates per-quantity offers for all 49 established social child categories,
 * plus competitor-scoped professional service offers. Exact Bayigram quantities
 * get precisely 10% lower price suggestions. For other categories only the
 * existing NetVera tier curve is used as a DRAFT estimate, NOT a market claim.
 * Never publishes or maps a new SMM product without provider verification.
 * Existing products, blog, ready software, slugs, prices and orders are preserved.
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit(1);}
define('BASE_PATH',dirname(__DIR__));
foreach(@file(BASE_PATH.'/.env',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)?:[] as $line){
    $line=trim($line);
    if($line===''||$line[0]==='#'||!str_contains($line,'='))continue;
    [$key,$value]=explode('=',$line,2);
    $_ENV[trim($key)]=trim($value," \t\n\r\0\x0B\"'");
}
spl_autoload_register(static function(string $class):void{
    if(str_starts_with($class,'App\\')){
        $file=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
        if(is_file($file))require $file;
    }
});
require_once BASE_PATH.'/app/Core/Helpers.php';
$baseline=json_decode((string)file_get_contents(BASE_PATH.'/database/netvera-social-package-baseline.json'),true,512,JSON_THROW_ON_ERROR);
$peers=json_decode((string)file_get_contents(BASE_PATH.'/database/netvera-market-competitor-samples-2026-10-27.json'),true,512,JSON_THROW_ON_ERROR);
if(($baseline['package_count']??0)!==185 || count($baseline['categories']??[])!==60 ||
    ($peers['discount_rate']??0)!==0.10)throw new RuntimeException('Market research snapshot mismatch.');
$apply=in_array('--apply-drafts',$argv,true);
if($apply&&!in_array('--ack=NETVERA-MARKET-DRAFTS-REVIEWED',$argv,true))
    throw new RuntimeException('Explicit acknowledgement required for database writes.');
$dbo=\App\Core\Database::getInstance();
$cats=$dbo->fetchAll("SELECT id,slug,status FROM categories");
$catIds=[];foreach($cats as $c)$catIds[$c['slug']]=$c;
$old=$dbo->fetchAll('SELECT slug FROM packages');
$existing=array_fill_keys(array_column($old,'slug'),true);
$offers=[];
$targetQuantities=[5,10,25,50,75,100,250,500,750,1000,2500,5000,7500,10000,25000,50000];
foreach($baseline['categories'] as $cat){
    if($cat['parent_legacy_id']===null)continue;
    $slug=(string)$cat['slug'];
    $records=array_values(array_filter($baseline['packages'],static fn(array $p):bool=>
        (int)$p['category_legacy_id']===(int)$cat['legacy_id']));
    if(!$records)continue;
    $tierData=[];
    foreach($records as $p){
        if(preg_match('/^(\d[\d.,]*)\s+(.{1,45}?)\s+için/u',(string)$p['short_description'],$m)){
            $qty=(int)preg_replace('/[.,]/','',$m[1]);
            if($qty>0)$tierData[]=['qty'=>$qty,'price'=>(float)$p['price'],'unit'=>trim($m[2])];
        }
    }
    if(!$tierData)continue;
    usort($tierData,static fn(array $a,array $b):int=>$a['qty']<=>$b['qty']);
    $unit=preg_replace('/\x{0307}/u','',(string)$tierData[0]['unit']);
    $existingQuantities=array_fill_keys(array_column($tierData,'qty'),true);
    $desired=max(0,8-count($records));
    // The exact existing package quantity is never recreated in this tier set;
    // keep differences in quality/source under separate verified market proposals.
    $chosen=[];
    foreach($targetQuantities as $qty){
        if(isset($existingQuantities[$qty]))continue;
        if($qty>$tierData[count($tierData)-1]['qty']*5 ||
           $qty<$tierData[0]['qty']/5)continue;
        $chosen[]=$qty;
    }
    usort($chosen,static function(int $a,int $b)use($tierData):int{
        $center=sqrt($tierData[0]['qty']*$tierData[count($tierData)-1]['qty']);
        return abs(log($a/$center))<=>abs(log($b/$center));
    });
    $chosen=array_slice($chosen,0,$desired);
    sort($chosen,SORT_NUMERIC);
    foreach($chosen as $qty){
        $closest=$tierData[0];
        foreach($tierData as $ref){
            if(abs(log($ref['qty']/$qty))<abs(log($closest['qty']/$qty)))$closest=$ref;
        }
        // Budget ESTIMATE only, not a competitor-benchmarked price or cost.
        $draft=round(max(1,((float)$closest['price'])*pow($qty/$closest['qty'],0.93)),2);
        $offerSlug='nv-alt-'.$slug.'-'.$qty;
        $offers[$offerSlug]=[
            'category'=>$slug,'slug'=>$offerSlug,
            'name'=>$cat['name'].' — '.number_format($qty,0,',','.').' '.$unit,
            'quantity'=>$qty,'unit'=>$unit,'service'=>$cat['name'],
            'price'=>$draft,'term'=>'','scope'=>'Miktar: '.$qty.' '.$unit.
                '. Hizmet kaynağı, teslimat hızı, garanti ve SMM servis eşlemesi henüz onaylanmadı.',
            'source'=>'internal_tier_estimate','peer_price'=>null,
            'status'=>'inactive','kind'=>'social'
        ];
    }
}
foreach($peers['social'] as $group){
    foreach($group['quantity_price'] as $qty=>$peer){
        $qty=(int)$qty;
        $offerSlug='nv-pazar-'.$group['category'].'-'.$group['variant'].'-'.$qty;
        $offers[$offerSlug]=[
            'category'=>$group['category'],'slug'=>$offerSlug,
            'name'=>$group['name'].' — '.number_format($qty,0,',','.').' '.$group['unit'],
            'quantity'=>$qty,'unit'=>$group['unit'],'service'=>$group['name'],
            'price'=>round((float)$peer*0.90,2),'term'=>'',
            'scope'=>'Bu hizmet ve miktar için pazar fiyatı esas alınmıştır. Tedarikçi birim maliyeti, kalite ve telafi koşulları teyit edilmeden satışa açılmamalıdır.',
            'source'=>$group['url'],'peer_price'=>(float)$peer,
            'status'=>'inactive','kind'=>'social'
        ];
    }
}
foreach($peers['professional'] as $group){
    $offerSlug='nv-pazar-'.\slugify((string)$group['name']);
    $offers[$offerSlug]=[
        'category'=>$group['category'],'slug'=>$offerSlug,
        'name'=>$group['name'],'quantity'=>0,'unit'=>'','service'=>'',
        'price'=>round((float)$group['peer_try']*.90,2),
        'term'=>$group['term'],'scope'=>$group['scope'].' '.$group['vat'],
        'source'=>$group['source'],'peer_price'=>(float)$group['peer_try'],
        'status'=>'inactive','kind'=>'professional'
    ];
}
$eligible=[];$missingCats=[];$unchanged=0;$knownPeer=0;$estimated=0;
foreach($offers as $offer){
    if(isset($existing[$offer['slug']])){$unchanged++;continue;}
    if(!isset($catIds[$offer['category']])||$catIds[$offer['category']]['status']!=='active'){
        $missingCats[$offer['category']]=true;continue;
    }
    $eligible[]=$offer;
    if($offer['peer_price']!==null)$knownPeer++;else $estimated++;
}
$report=[
    'at'=>gmdate('c'),'mode'=>$apply?'apply-drafts':'dry-run',
    'original_social_children'=>49,'source_social_packages'=>185,
    'new_exact_competitor_offers'=>$knownPeer,
    'other_internal_tier_drafts'=>$estimated,
    'unmodified_existing_offers'=>$unchanged,
    'unknown_or_inactive_categories'=>array_keys($missingCats),
    'proposals'=>$eligible
];
$dir=BASE_PATH.'/storage/backups';
if(!is_dir($dir)&&!mkdir($dir,0700,true)&&!is_dir($dir))throw new RuntimeException('Private audit folder unavailable');
$destination=$dir.'/netvera-market-proposals-'.gmdate('Ymd-His').'-'.getmypid().'.json';
file_put_contents($destination,json_encode($report,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR),LOCK_EX);
@chmod($destination,0600);
echo 'Verified peer-derived suggestions: '.$knownPeer.
    ' | Estimated additional tier drafts: '.$estimated.
    ' | Skipped original slugs: '.$unchanged.
    ' | Unknown/hidden category slugs: '.count($missingCats).PHP_EOL;
echo 'Private report: '.$destination.PHP_EOL;
if(!$apply){echo "DRY RUN: no SQL modified. All new offers are INACTIVE until reviewed. \n";exit(0);}
$created=0;
$pdo=$dbo->getPdo();$pdo->beginTransaction();
try{
    $insert=$pdo->prepare("INSERT INTO packages
        (category_id,name,slug,short_description,description,price,discount_price,
         delivery_time,min_quantity,max_quantity,badge,is_featured,status,sort_order,
         seo_title,seo_description,seo_focus_keyword)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $option=$pdo->prepare("INSERT INTO settings (setting_key,setting_value,setting_group)
             VALUES (?,?,?) ON DUPLICATE KEY UPDATE setting_key=setting_key");
    foreach($eligible as $offer){
        if($dbo->fetch('SELECT id FROM packages WHERE slug=? LIMIT 1',[$offer['slug']]))continue;
        $category=$dbo->fetch('SELECT id,status FROM categories WHERE slug=? LIMIT 1',[$offer['category']]);
        if(!$category||$category['status']!=='active')continue;
        $description='<p>'.htmlspecialchars($offer['scope'],ENT_QUOTES,'UTF-8').'</p>'.
            '<p>Her teklif, hizmet kapsamı ve sağlayıcı uygunluğu kontrol edildikten sonra satışa açılır. Belirli bir etkileşim, sıralama veya gelir sonucu garanti edilmez.</p>';
        $summary=mb_substr($offer['scope'],0,490);
        $insert->execute([(int)$category['id'],$offer['name'],$offer['slug'],$summary,$description,
            $offer['price'],null,'Ön değerlendirme gerekli',1,1,'Yeni alternatif',0,'inactive',990,
            mb_substr($offer['name'].' | NetVera',0,195),
            mb_substr($summary,0,490),'']);
        $id=(int)$pdo->lastInsertId();
        $origin=['source'=>$offer['source'],'peer_price'=>$offer['peer_price'],
                 'reviewed_on'=>$peers['checked_at'],'state'=>'not_yet_available',
                 'kind'=>$offer['kind'],'term'=>$offer['term']];
        $option->execute(['netvera_market_proposal_'.$id,
            json_encode($origin,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),'packages']);
        if($offer['kind']==='social'){
            $option->execute(['social_package_'.$id,json_encode([
                'service'=>$offer['service'],'quantity'=>$offer['quantity'],
                'unit'=>$offer['unit']
            ],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),'packages']);
        }else{
            $option->execute(['package_price_mode_'.$id,'fixed','packages']);
        }
        $created++;
    }
    $pdo->commit();
}catch(\Throwable $e){
    if($pdo->inTransaction())$pdo->rollBack();
    throw $e;
}
echo "Created $created DRAFT offers in admin. All are inactive. Existing data untouched. \n";
