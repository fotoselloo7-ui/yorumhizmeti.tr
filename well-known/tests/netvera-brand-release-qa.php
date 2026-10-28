<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$read=static function(string $path)use($root):string{
    $content=file_get_contents($root.'/'.$path);
    if($content===false)throw new RuntimeException('Missing '.$path);
    return $content;
};
$assert=static function(bool $ok,string $message):void{
    if(!$ok){fwrite(STDERR,"FAIL: $message\n");exit(1);}
};
$sql=$read('database/database.sql');
$assert(str_starts_with($sql,'-- NetVera Teknoloji Yazılım'),'SQL must be NetVera seeded');
$assert(!str_contains($sql,'Yorum Hizmeti')&&!str_contains($sql,'yorumhizmeti.tr'),
    'Retired brand must not leak into public SQL seed');
$assert(str_contains($sql,'DROP TABLE IF EXISTS') && str_contains($sql,'CREATE TABLE'),
    'Full import SQL must be treated as an empty-database install, not an update');
$assert(str_contains($sql,'NetVera Teknoloji Yazılım'),
    'SQL must contain correct corporate name');
$admin=$read('resources/views/layouts/admin.php');
$assert(!str_contains($admin,"'YorumPanel'"),'Admin layout retains retired site-name fallback');
$composer=json_decode($read('composer.json'),true,512,JSON_THROW_ON_ERROR);
$assert($composer['name']==='netvera/teknoloji-yazilim','Composer project name not rebranded');
$readme=$read('README.md');
$assert(str_contains($readme,'https://netvera.tr') && !str_contains($readme,'yorumhizmeti.tr'),
    'Installation guide must refer only to new domain');
$script=$read('bin/netvera-brand-finalize.php');
foreach(['--ack=NETVERA-BRAND-REVIEWED','DRY RUN','storage/backups',
    "'categories'=>","'packages'=>","'pages'=>","'home_sections'=>"] as $frag){
    $assert(str_contains($script,$frag),'Brand cleanup missing safety guard: '.$frag);
}
$assert(!str_contains($script,"'admins'=>")&&!str_contains($script,"'bank_accounts'=>")
    &&!str_contains($script,"'orders'=>")&&!str_contains($script,"'blog_posts'=>"),
    'Brand script may not modify customers, payments, bank details or indexed blogs');
$env=$read('.env.example');
$assert(str_contains($env,'APP_URL=https://netvera.tr'),'Default environment domain must be NetVera');
echo "PASS: NetVera brand SQL, admin interface, composer and docs; safe no-destructive live normalization.\n";
