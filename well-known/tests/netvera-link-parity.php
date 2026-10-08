<?php
/** Offline gate: compares imported public Netvera URL contract with the backup.
 * php tests/netvera-link-parity.php /private/netvera_public_migration_payload.zip
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli'||!class_exists('ZipArchive'))exit(2);
$zip=new ZipArchive();
if($zip->open((string)($argv[1]??''))!==true){fwrite(STDERR,"ZIP is unavailable.\n");exit(2);}
$json=$zip->getFromName('netvera-public-content.json');
$manifest=json_decode((string)$zip->getFromName('manifest.json'),true);
if($json===false||!is_array($manifest)||!hash_equals((string)($manifest['content_sha256']??''),hash('sha256',$json))){
 fwrite(STDERR,"Input payload integrity invalid.\n");exit(2);
}
$data=json_decode($json,true,512,JSON_THROW_ON_ERROR);
$expectedProducts=[
 'hafriyat-yazilimi','bobinajci-sitesi-yazilimi',
 'netvera-threads','netvera-e-posta','ai-makale-botu',
 'turizm-isletme-dizin-yazilimi','oto-kurtarici-cekici-yazilimi',
 'google-maps-veri-cekme-isletme-bulucu-botu',
 'netvera-smm-panel-paket-satis-scripti','mimarlik-ic-mimarlik-web-site-scripti',
 'oto-cekici-yazilimi','haber-sitesi-scripti',
 'netvera-nakliye-script-yazilimi-pro','netvera-temizlik-firmasi-script-yazilimi-pro',
 'netvera-emlak-script-yazilimi-pro','insaat-firmasi-scripti'
];
$expectedBlogs=[
 'sosyal-medya-yonetimi-nedir-isletmeler-icin-rehber',
 'php-hafriyat-scripti-hazir-hafriyat-web-sitesi',
 'php-bobinajci-scripti-hazir-bobinaj-web-sitesi',
 'google-maps-isletme-bulucu-programi-nasil-olmali',
 'yapay-zeka-haber-yazilimi-otomatik-haber-sitesi'
];
$products=array_column($data['products']??[],'slug');
$blogs=array_column($data['blog_posts']??[],'slug');
$issues=[];
foreach(['product'=>[$expectedProducts,$products],'blog'=>[$expectedBlogs,$blogs]] as $type=>$sets){
 [$expected,$actual]=$sets;
 $missing=array_diff($expected,$actual);
 $extra=array_diff($actual,$expected);
 if($missing)$issues[]=$type.' missing: '.implode(',',$missing);
 if($extra)$issues[]=$type.' additional: '.implode(',',$extra);
 if(count($actual)!==count(array_unique($actual)))$issues[]=$type.' duplicate slug';
}
foreach($data['blog_posts']??[] as $post){
 $canonical=(string)($post['canonical_url']??'');
 $expected='https://netvera.tr/blog/'.$post['slug'];
 if($canonical!==''&&$canonical!==$expected)$issues[]='blog canonical mismatch: '.$post['slug'];
}
$aliases=[
 '/haber-sitesi-scripti'=>'/hazir-scriptler/haber-sitesi-scripti',
 '/temizlik-firmasi-scripti-web-site-yazilimi'=>'/hazir-scriptler/netvera-temizlik-firmasi-script-yazilimi-pro',
 '/emlak-scripti-hazir-emlak-sitesi-yazilimi'=>'/hazir-scriptler/netvera-emlak-script-yazilimi-pro'
];
$report=[
 'pass'=>!$issues,'product_count'=>count($products),
 'blog_count'=>count($blogs),'indexed_product_paths'=>array_map(
  static fn($slug)=>'/hazir-scriptler/'.$slug,$products),
 'indexed_blog_paths'=>array_map(static fn($slug)=>'/blog/'.$slug,$blogs),
 'legacy_301_aliases'=>$aliases,'issues'=>$issues
];
echo json_encode($report,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n";
exit($issues?1:0);
