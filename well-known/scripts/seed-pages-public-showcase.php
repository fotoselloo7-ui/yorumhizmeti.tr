<?php
/**
 * GitHub Pages CI-only PUBLIC presentation fixtures.
 * Only existing public NetVera software covers and published blog artwork.
 * No private customer references, invented Instagram URLs or account details.
 */
declare(strict_types=1);
if (PHP_SAPI !== 'cli' || getenv('PAGES_PREVIEW_CI_ONLY') !== '1') {
    fwrite(STDERR, "Pages CI only.\n"); exit(2);
}
define('BASE_PATH', dirname(__DIR__));
foreach (file(BASE_PATH.'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
    [$key,$value] = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value, " \t\r\n\"'");
}
if (($_ENV['APP_ENV'] ?? '') !== 'local' || ($_ENV['DB_NAME'] ?? '') !== 'yorumhizmeti_ci') {
    fwrite(STDERR, "Refuse non-disposable preview database.\n"); exit(2);
}
spl_autoload_register(static function(string $class):void {
    if (!str_starts_with($class, 'App\\')) return;
    $file = BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
    if (is_file($file)) require $file;
});
require BASE_PATH.'/app/Core/Helpers.php';

$rows = [
  [
    'id'=>'preview-construction','title'=>'NetVera İnşaat Firması Yazılımı',
    'description'=>'Kurumsal inşaat web sitesi arayüzü ve yönetim paneli tasarım örneği.',
    'placements'=>[['group'=>'agency','service'=>'web-site']],
    'media_type'=>'website','url'=>'/hazir-scriptler/insaat-firmasi-scripti',
    'image'=>'scripts/insaat-firmasi-scripti/insaat-firmasi-scripti-kapak.webp'
  ],
  [
    'id'=>'preview-news','title'=>'Haber Sitesi Yazılımı & Dijital Altyapı',
    'description'=>'Otomatik haber yayınlama ve dijital içerik yönetimi için yazılım vitrini.',
    'placements'=>[['group'=>'agency','service'=>'software'],['group'=>'marketing','service'=>'seo']],
    'media_type'=>'website','url'=>'/hazir-scriptler/haber-sitesi-scripti',
    'image'=>'scripts/haber-sitesi-scripti/haber-sitesi-scripti-kapak.webp'
  ],
  [
    'id'=>'preview-social-post','title'=>'Sosyal Medya İçerik Tasarımı',
    'description'=>'Sosyal medya paylaşım planlaması için hazırlanan görsel içerik örneği.',
    'placements'=>[['group'=>'marketing','service'=>'social-management']],
    'media_type'=>'image','url'=>'/blog/sosyal-medya-yonetimi-nedir-isletmeler-icin-rehber',
    'image'=>'blog/sosyal-medya-icerik-planlama-ve-yayin-takvimi-1786623667-633.webp'
  ],
  [
    'id'=>'preview-seo','title'=>'SEO, GEO & Dijital Görünürlük',
    'description'=>'Arama ve yapay zekâ sonuçları için içerik stratejisi tasarım çalışması.',
    'placements'=>[['group'=>'marketing','service'=>'seo']],
    'media_type'=>'image','url'=>'/blog/yapay-zeka-haber-yazilimi-otomatik-haber-sitesi',
    'image'=>'blog/seo-geo-ve-aio-uyumlu-haber-iceriginin-google-ve-yapay-zeka-arama-sistemlerinde-gorunurlugunu-gosteren-netvera-gorseli-1789825378-822.webp'
  ],
  [
    'id'=>'preview-emlak','title'=>'NetVera Emlak Web Sitesi Yazılımı',
    'description'=>'Emlak portföyü, ilan yönetimi ve profesyonel kurumsal tasarım örneği.',
    'placements'=>[['group'=>'agency','service'=>'web-site']],
    'media_type'=>'website','url'=>'/hazir-scriptler/netvera-emlak-script-yazilimi-pro',
    'image'=>'scripts/netvera-emlak-script-yazilimi-pro/netvera-emlak-script-yazilimi-pro-kapak.webp'
  ],
  [
    'id'=>'preview-local','title'=>'Google Maps İşletme Bulucu',
    'description'=>'Yerel işletme keşfi ve özel satış otomasyonu yazılımı vitrini.',
    'placements'=>[['group'=>'marketing','service'=>'local'],['group'=>'agency','service'=>'software']],
    'media_type'=>'website','url'=>'/hazir-scriptler/google-maps-veri-cekme-isletme-bulucu-botu',
    'image'=>'scripts/google-maps-veri-cekme-isletme-bulucu-botu/google-maps-veri-cekme-isletme-bulucu-botu-kapak.webp'
  ],
];
foreach ($rows as $i=>&$row) {
    $imageFile = BASE_PATH.'/public/uploads/'.$row['image'];
    if (!is_file($imageFile)) throw new RuntimeException('Missing public preview cover: '.$row['image']);
    $row['sort_order']=$i+1;
    $row['status']='active';
    $row['group']=$row['placements'][0]['group'];
    $row['service']=$row['placements'][0]['service'];
    $row['logo']='';
}
unset($row);
\App\Services\ReferencesService::save($rows);
$loaded = \App\Services\ReferencesService::all(true);
if(count($loaded)!==6)throw new RuntimeException('Expected six visible public preview portfolio examples');
echo "PAGES_REFERENCES_OK: 6 public-content examples / two active filters / existing local covers\n";
