<?php
declare(strict_types=1);
/** Visual copy regression for clean production UI. No DB or network needed. */
$root=dirname(__DIR__);
$files=[
  'resources/views/admin/payment-gateways/paytr.php'=>['PayTR iFrame v2 Entegrasyonu','Yerel PayTR ödeme ekranı tasarımını incele','adm-alert'],
  'resources/views/admin/import/index.php'=>['Kullanım Kılavuzu','Örnek CSV Satırı','adm-import-note'],
  'resources/views/admin/settings/seo.php'=>['SEO İpuçları'],
  'resources/views/admin/navigation/index.php'=>['adm-nav-help','Başka sayfayı yenilediğinde'],
  'resources/views/admin/packages/fields.php'=>['adm-fields-info'],
  'resources/views/admin/references/index.php'=>['adm31-help'],
  'resources/views/admin/blog/form.php'=>['nv44-quality-subtitle'],
  'resources/views/admin/netvera-scripts/form.php'=>['nvpa-note'],
  'resources/views/frontend/support/create.php'=>['<strong>Bilgilendirme</strong>'],
  'resources/views/frontend/software.php'=>['nv36-rating-section','doğrulanmış değerlendirmeler henüz bağlı değil'],
  'resources/views/frontend/netvera-script-detail.php'=>['Yönetim panelinden herkese açık demo hesabı tanımlanmadı'],
  'resources/views/frontend/payment-iframe.php'=>['nv45-preview-alert','nv45-summary-notice','Yalnızca yerel tasarım önizlemesi'],
];
$count=0;
foreach($files as $path=>$disallowed){
   $src=file_get_contents($root.'/'.$path);
   if($src===false)throw new RuntimeException('Missing UI view '.$path);
   foreach($disallowed as $pattern){
     if(str_contains($src,$pattern))throw new RuntimeException('Redundant notice still present: '.$path.' / '.$pattern);
   }
   $count++;
}
$require=[
  'resources/views/admin/payment-gateways/paytr.php'=>['merchant_id','merchant_key','merchant_salt','test_mode','csrfField()'],
  'resources/views/admin/import/index.php'=>['csv_file','import/islem','csrfField()'],
  'resources/views/admin/settings/seo.php'=>['$categories','$packages','$posts'],
  'resources/views/admin/smm/index.php'=>['service_ids[]','smm-bulk-form'],
  'resources/views/frontend/support/create.php'=>['/destek/olustur','name="message"'],
  'resources/views/frontend/netvera-script-detail.php'=>['$product[','demoUrl'],
];
foreach($require as $path=>$needles){
  $src=file_get_contents($root.'/'.$path);
  foreach($needles as $needle){
    if(!str_contains($src,$needle))throw new RuntimeException('Business function removed: '.$path.' / '.$needle);
  }
}
echo "PASS {$count} info panels removed without losing verified core forms\n";
