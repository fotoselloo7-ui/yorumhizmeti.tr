<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
define('BASE_PATH',dirname(__DIR__));
spl_autoload_register(static function($c){if(str_starts_with($c,'App\\'))require BASE_PATH.'/app/'.str_replace('\\','/',substr($c,4)).'.php';});
use App\Services\ContactPageService as Contact;
$n=0;$ok=static function($value,$why)use(&$n){if(!$value)throw new RuntimeException($why);$n++;};
$url='https://www.google.com/maps/embed?pb=!1m2!1m1!1sSynthetic';
$ok(Contact::mapUrl($url)===$url,'Shared embed URL');
$ok(Contact::defaults()['contact_map_enabled']==='1' && Contact::mapUrl(Contact::defaults()['contact_map_embed'])===Contact::DEFAULT_MAP_EMBED, 'User-supplied NetVera Alanya map is a valid enabled default');
$ok(Contact::mapUrl('<iframe src="'.$url.'" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>')===$url,'Real Google copied iframe');
$ok(Contact::mapUrl('<iframe onload="alert(1)" src="'.$url.'"></iframe>')===$url,'Only src is retained, event stripped');
$ok(Contact::mapUrl('https://maps.google.com/maps?q=Alanya&output=embed')!=='','Legacy embedded map');
$ok(Contact::mapUrl('https://www.google.com/maps/embed/v1/place?key=synthetic-public-client-key&q=Alanya')!=='','Public browser API embed');
$ok(Contact::mapUrl('')==='','Empty removes map');
foreach(['javascript:alert(1)','//www.google.com/maps/embed?pb=x','http://www.google.com/maps/embed?pb=x','https://evil.test/maps/embed?pb=x','https://www.google.com.evil.test/maps/embed?pb=x','https://www.google.com@evil.test/maps/embed?pb=x','https://user@www.google.com/maps/embed?pb=x','https://www.google.com:443/maps/embed?pb=x','https://www.google.com/maps/embed?pb=x#fragment','https://www.google.com/maps/embed?pb[]=x','https://www.google.com/maps/embed','https://www.google.com/maps/preview?pb=x','https://maps.app.goo.gl/example','https://www.google.com/url?q=https://evil.test','<script>alert(1)</script>','<iframe src="'.$url.'"></iframe><script>x</script>','<iframe srcdoc="x"></iframe>',str_repeat('x',12001),[],null]as $bad){
    try{Contact::mapUrl($bad);throw new RuntimeException('Unsafe map accepted');}catch(InvalidArgumentException $e){$n++;}
}
$data=Contact::defaults()+['site_email'=>'qa@example.test','site_phone'=>'+90 555 111 22 33','whatsapp_number'=>'905551112233','site_address'=>'Sentetik QA adresi'];
$data['contact_map_embed']=$url;$data['contact_map_enabled']='1';$clean=Contact::validate($data);
$ok($clean['contact_map_embed']===$url&&$clean['contact_map_enabled']==='1','Validated settings');
$data['contact_heading']='<b>Güvenli başlık</b>';$ok(Contact::validate($data)['contact_heading']==='Güvenli başlık','Plain text heading');
foreach(['contact_heading'=>'','site_email'=>'invalid','whatsapp_number'=>'https://evil.test','site_phone'=>'javascript:x','contact_map_embed'=>'']as $key=>$value){
    try{Contact::validate(array_replace($data,[$key=>$value]));throw new RuntimeException('Bad setting accepted');}catch(InvalidArgumentException $e){$n++;}
}
$view=file_get_contents(BASE_PATH.'/resources/views/frontend/contact.php');
foreach(['csrfField()','privacy_consent','website_url','contact-name','contact-message','data-contact-map','id="contact-map-frame"','src="<?= e($contact[\'map_url\']) ?>"','loading="lazy"','referrerpolicy="strict-origin-when-cross-origin"','allowfullscreen']as $token)$ok(str_contains($view,$token),'Contact behavior '.$token);
$ok(!str_contains($view,'yv-contact-grid')&&!str_contains($view,'support-woman-cutout'),'Retired layout no longer owns contact');
$css=file_get_contents(BASE_PATH.'/public/assets/css/contact-page.css');$ok(!str_contains($css,'!important'),'No override pile');
$ok(str_contains($css,'prefers-reduced-motion')&&str_contains($css,'font-size:16px'),'Motion and mobile input support');
echo "PASS $n contact embed validation, content, form and layout contracts\n";
