<?php
declare(strict_types=1);
$root=dirname(__DIR__);
require_once $root.'/app/Services/NetveraBrandSettings.php';
use App\Services\NetveraBrandSettings as Brand;

$defaults=Brand::defaults();
$needed=[
 'site_name','site_url','site_email','site_slogan','brand_short_name','brand_positioning',
 'brand_sector_software','brand_sector_agency','brand_sector_social',
 'default_seo_title','default_seo_description','seo_og_title','seo_og_description',
 'seo_org_type','seo_org_description','seo_geo_summary','seo_entity_topics',
 'seo_service_area','seo_content_intent','seo_home_question','seo_home_answer',
 'seo_default_robots'
];
foreach($needed as $name){
 if(!isset($defaults[$name])||trim($defaults[$name][0])==='')
   throw new RuntimeException('Missing brand SEO/GEO/AIO setting '.$name);
}
if(!Brand::upgradeable('site_name','Yorum Hizmeti'))
  throw new RuntimeException('Old branding cannot migrate.');
if(Brand::display('site_email','info@yorumhizmeti.tr')!=='info@netvera.tr')
  throw new RuntimeException('Legacy public company email not migrated.');
if(Brand::upgradeable('site_email','musteri@ornek.com'))
  throw new RuntimeException('Must never replace an unrelated verified contact email.');
if(!Brand::upgradeable('site_url','https://yorumhizmeti.tr'))
  throw new RuntimeException('Old domain cannot migrate.');
if(!Brand::upgradeable('default_seo_title','Yorum Hizmeti - Dijital Hizmet Platformu'))
  throw new RuntimeException('Old SEO cannot migrate.');
if(!Brand::upgradeable('default_seo_description','Google, Instagram, TikTok, YouTube ve daha fazlası için profesyonel dijital hizmetler.'))
  throw new RuntimeException('Old SEO description cannot migrate.');
// Regression: live Site Settings screenshot retained a new-brand-prefixed old
// Google Yorum SEO title because legacy checks matched only "Yorum Hizmeti".
foreach([
    'default_seo_title'=>'NetVera Teknoloji Yazılım | Google Yorum ve Sosyal Medya Hizmetleri',
    'seo_og_title'=>'NetVera Teknoloji Yazılım | Google Yorum ve Sosyal Medya',
    'default_seo_description'=>'Google yorum, Instagram, TikTok, YouTube ve sosyal medya paketleriyle işletmenizin dijital güvenini artırın.',
    'seo_og_description'=>'Google yorum ve sosyal medya paketleriyle kurumsal markanızın dijital güvenini artırın.'
] as $key=>$old){
    if(!Brand::upgradeable($key,$old))
        throw new RuntimeException('Screenshot legacy SEO missed '.$key);
    $expected=$defaults[$key][0];
    if(Brand::display($key,$old)!==$expected)
        throw new RuntimeException('Global SEO value not normalized: '.$key);
}
if(Brand::display('default_seo_title','NetVera Teknoloji Yazılım | Google Yorum ve Sosyal Medya')!==
    'NetVera Teknoloji Yazılım | Yazılım, Dijital Ajans ve Sosyal Medya')
    throw new RuntimeException('Screenshot SEO title not replaced by agreed positioning.');
if(Brand::upgradeable('default_seo_title',
    'NetVera Teknoloji Yazılım | Yazılım, Dijital Ajans ve Sosyal Medya'))
    throw new RuntimeException('Correct NetVera title should remain stable.');
if(Brand::upgradeable('site_name','NetVera Premium Teknoloji'))
  throw new RuntimeException('Hand-authored brand would be overwritten.');
if(Brand::upgradeable('default_seo_title','Özgün Kurumsal Başlık | NetVera'))
  throw new RuntimeException('Hand-authored SEO title would be overwritten.');
if(Brand::display('brand_positioning','')!==$defaults['brand_positioning'][0])
  throw new RuntimeException('Global brand defaults not visible before admin migration.');
if(Brand::display('site_name','Müşterinin Özel Şirketi')!=='Müşterinin Özel Şirketi')
  throw new RuntimeException('Custom company name lost.');

$read=static function(string $path)use($root):string{
 $f=$root.'/'.$path;
 if(!is_file($f))throw new RuntimeException('File missing: '.$path);
 return file_get_contents($f);
};
$site=$read('resources/views/admin/settings/site.php');
$seoPanelPos=strpos($site,'nv79-global-seo');
$sidebarPos=strpos($site,'<!-- Sidebar (Right) -->');
if($seoPanelPos===false || $sidebarPos===false || $seoPanelPos>=$sidebarPos ||
   !str_contains($site,'id="nv79-seo-title-preview"') ||
   !str_contains($site,'id="nv79-seo-desc-preview"'))
    throw new RuntimeException('Global SEO must be fully visible and editable in the main Site Settings area.');
foreach($needed as $name){
 if(!str_contains($site,'name="'.$name.'"')){
    throw new RuntimeException('Brand settings editor missing field '.$name);
 }
}
foreach(['social_instagram','social_facebook','social_youtube','social_tiktok','social_twitter'] as $key){
 if(!str_contains($site,'name="'.$key.'"'))throw new RuntimeException('Missing official social editor '.$key);
}
$settings=$read('app/Controllers/Admin/SettingsController.php');
if(!str_contains($settings,'NetveraBrandSettings::syncExisting'))
 throw new RuntimeException('Existing Site Settings database will not migrate.');
$runtime=$read('app/Services/SiteConfigService.php');
if(!str_contains($runtime,'NetveraBrandSettings::display'))
 throw new RuntimeException('Brand defaults are not applied at runtime.');
$seed=$read('database/seeds.sql');
if(!str_contains($seed, $defaults['default_seo_title'][0]) ||
   !str_contains($seed, $defaults['default_seo_description'][0]))
    throw new RuntimeException('Fresh-install SEO defaults diverge from live brand settings.');
$home=$read('app/Controllers/HomeController.php');
foreach([
 "setting('seo_org_description')","setting('seo_entity_topics')",
 "setting('seo_service_area')","setting('seo_home_question')",
 "setting('seo_home_answer')","setting('seo_og_title')",
 'NetveraBrandSettings::sameAs',
 "setting('brand_sector_software'","setting('brand_sector_agency'","setting('brand_sector_social'"
] as $marker){
 if(!str_contains($home,$marker))throw new RuntimeException('SEO not wired to website: '.$marker);
}
$seo=$read('resources/views/admin/settings/seo.php');
if(!str_contains($home,"'canonicalUrl'=>url('/')"))
    throw new RuntimeException('Homepage canonical link missing.');
if(!str_contains($seo,'NetVera Kurumsal SEO / GEO / AIO') ||
   !str_contains($seo,'/admin/site-ayarlari'))
 throw new RuntimeException('SEO Center does not show actual global NetVera settings.');
$layout=$read('resources/views/layouts/app.php');
if(!str_contains($layout,"setting('site_slogan', 'Yazılım, Dijital Ajans ve Sosyal Medya Hizmetleri')"))
 throw new RuntimeException('Old review-centric corporate footer still renders.');
echo "PASS: ".count($needed)." persisted NetVera brand/SEO/GEO/AIO fields, safe legacy migration, editable admin form and real homepage schema\n";
