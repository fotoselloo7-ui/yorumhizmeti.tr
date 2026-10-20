<?php
namespace App\Services;

use App\Core\Database;
use App\Core\Router;

/** Uses the canonical pages table, no migration/DDL or per-city PHP files. */
final class RegionalLandingService
{
    public const PREFIX='{"module":"regional_landing",';
    public const REDIRECTS='regional_landing_redirects_v1';
    public static function decode(array $row):?array
    {
        $data=json_decode((string)($row['content']??''),true);
        return is_array($data)&&($data['module']??'')==='regional_landing'?$data:null;
    }
    public static function isLanding(array $row):bool{return self::decode($row)!==null;}
    public static function defaults():array
    {
        return ['module'=>'regional_landing','version'=>1,'title'=>'','slug'=>'','status'=>'inactive','city'=>'','province'=>'','service'=>'','area'=>'','working_mode'=>'remote','layout'=>'studio','theme'=>'violet','summary'=>'','local_context'=>'','image'=>'/assets/img/category-heroes/ads.webp','mobile_image'=>'','image_alt'=>'Dijital hizmetler için tanıtım görseli','cta_label'=>'Projenizi konuşalım','services_title'=>'İhtiyacınıza uygun hizmetler','references_title'=>'Seçili çalışmalarımız','faq_title'=>'Merak ettikleriniz','cta_title'=>'Bir sonraki adımı birlikte planlayalım.','cta_summary'=>'İhtiyacınızı paylaşın; kapsamı ve çalışma biçimini birlikte değerlendirelim.','sections'=>[],'links'=>[],'faqs'=>[],'service_ids'=>[],'reference_ids'=>[],'seo'=>['robots'=>'noindex,follow']];
    }
    public static function slug(mixed $value):string
    {
        $value=LandingContentService::text($value,180);
        if(!preg_match('/^[\pL\pN]+(?:-[\pL\pN]+)*$/uD',$value)||$value!==mb_strtolower($value,'UTF-8')||Router::isReservedRoot($value))throw new \InvalidArgumentException('Küçük harf, sayı ve tire içeren, mevcut sistem yollarıyla çakışmayan bir slug yazın.');
        return $value;
    }
    /** Editorial starters are drafts, not automatically published city clones. */
    public static function starter(string $kind):array
    {
        $spec=[
            'reklam'=>['Reklam Ajansı','reklam-ajansi','ads','Reklam bütçenizi bir hedefe bağlayın.','Reklama başlamadan önce dönüşümü tanımlayın: teklif talebi, telefon görüşmesi, form veya satış. Alanya için hedeflenen kampanyada hizmet verilebilen bölgeleri ve müşteri profilini birlikte netleştirmek, ilgisiz gösterimlere harcanan bütçeyi azaltmaya yardımcı olur.','Kampanyadan önce hazırlık','Hedef kitle, bütçe aralığı, ölçüm planı ve açılış sayfası birlikte ele alınır. Sezonluk çalışan bir işletmeniz varsa kampanya takvimi çalışma dönemlerinize göre değerlendirilir. Yayın öncesi metinler, görseller ve dönüşüm hedefleri onayınıza sunulur.','Sonuçları nasıl değerlendiriyoruz?','Yalnızca gösterim ve tıklamaya değil, hedeflenen işlemlere ve maliyetlere bakılır. Raporlama sıklığı ve takip edilecek ölçütler kapsam görüşmesinde belirlenir. Reklam platformlarının onayları, açık artırma koşulları ve işletmenin dönüşüm süreci sonuçları etkiler.'],
            'sosyal'=>['Sosyal Medya Uzmanı','sosyal-medya-uzmani','content','İçeriğinizin bir amacı ve devamlılığı olsun.','Alanya için sosyal medya çalışması planlarken işletmenizin verdiği hizmeti, içerik üretim imkânlarını ve hedef kitlesinin sorularını birlikte değerlendirin. Hesap yönetimi, içerik üretimi ve miktar bazlı sosyal medya paketleri aynı hizmet değildir; ihtiyaç duyduğunuz kapsamı önce ayırın.','İçerik planı ve marka dili','Yayın temaları, görsel dil, metin yaklaşımı ve onay akışı başlangıçta belirlenir. İşletme içi çekim gerekiyorsa çekimin kim tarafından yapılacağı ve kullanım hakları teklif kapsamına ayrıca yazılır. Uzaktan çalışma ile yerinde üretim gerektiren işler birbirinden ayrılır.','Yönetim ve paket hizmetlerini karşılaştırın','Düzenli sosyal medya yönetimi; planlama, üretim ve değerlendirme akışıdır. Takipçi, beğeni veya izlenme paketleri ise kartında yazan miktar ve koşullarla satılır. Organik erişim, satış ya da kalıcı takipçi artışı varsayılmamalı; her hizmet kendi açıklamasıyla değerlendirilmelidir.'],
            'seo'=>['SEO Uzmanı','seo-uzmani','search','Arama görünürlüğünü sağlam bir temele kurun.','Alanya hizmet aramalarında görünür olmak isteyen bir işletme için önce mevcut sitenin taranabilirliğini, hizmet anlatımını ve dönüşüm akışını incelemek gerekir. Yerel kapsam, yalnızca başlığa şehir adı eklemek değil; kimlere hangi koşullarda hizmet verildiğini açıkça anlatmaktır.','Teknik inceleme ve içerik öncelikleri','İndekslenebilirlik, canonical adresler, site içi bağlantılar, başlık hiyerarşisi ve mobil deneyim birlikte değerlendirilir. Gereksiz kopya sayfalar çoğaltılmaz; her URL'nin ayrı bir kullanıcı sorusunu cevaplaması hedeflenir. Öncelikler sitenin mevcut durumuna göre belirlenir.','GEO ve AIO için anlaşılır yanıtlar','Hizmet kapsamı, kurum bilgileri, kaynaklar ve kısa cevaplar görünür içerikte tutarlı olmalıdır. Yapılandırılmış veri bu içeriği tarif eder; görünmeyen iddialar veya sahte değerlendirmeler eklemez. Google’da ilk sıra ya da yapay zekâ yanıtlarında gösterim garanti edilemez.'],
        ];
        if(!isset($spec[$kind]))return self::defaults();
        [$service,$slug,$visual,$tagline,$context,$h2a,$bodya,$h2b,$bodyb]=$spec[$kind];$d=self::defaults();
        $d['title']='Alanya '.$service;$d['slug']='alanya-'.$slug;$d['city']='Alanya';$d['province']='Antalya';$d['service']=$service;$d['summary']=$tagline.' Hizmet kapsamını inceleyin; projenizin hedeflerini NetVera ekibiyle değerlendirin.';$d['local_context']=$context;$d['image']='/assets/img/category-heroes/'.$visual.'.webp';$d['image_alt']=$service.' çalışma yaklaşımını temsil eden tanıtım görseli';
        $d['sections']=[['title'=>$h2a,'body'=>$bodya,'subheading'=>'Başlangıçta netleştirilecekler','detail'=>'İhtiyaç, çalışma biçimi, teslimler, onay süreci ve kapsam dışı işler teklif aşamasında birlikte netleştirilir.','image'=>'','alt'=>''],['title'=>$h2b,'body'=>$bodyb,'subheading'=>'Gerçekçi beklentiler','detail'=>'Hizmetin kapsamı ve takip edilecek ölçütler projenize göre belirlenir. Sonuç veya sıralama sözü verilmez.','image'=>'','alt'=>'']];
        $d['links']=[['label'=>'NetVera hizmetlerini karşılaştırın','path'=>'/kategoriler'],['label'=>'Projeniz hakkında iletişime geçin','path'=>'/iletisim']];
        $d['faqs']=[['question'=>'Alanya’da ofisiniz var mı?','answer'=>'Bu sayfa hizmet bölgesini tanımlar; Alanya’da bir ofis veya şube bulunduğu anlamına gelmez. Güncel kurum adresi ve iletişim bilgileri iletişim sayfasındadır.'],['question'=>'Çalışmanın kapsamı nasıl belirlenir?','answer'=>'İhtiyaçlarınızı ve hedeflerinizi paylaştıktan sonra teslimler, çalışma biçimi ve fiyatlandırma teklif aşamasında netleştirilir.']];
        $d['seo']=['title'=>'Alanya '.$service.' | NetVera','description'=>$d['summary'],'focus_keyword'=>'Alanya '.$service,'robots'=>'noindex,follow','main_question'=>'Alanya '.$service.' hizmeti nasıl planlanır?','direct_answer'=>'Önce mevcut durum, hedefler ve ihtiyaç duyulan teslimler değerlendirilir. Çalışma biçimi, kapsam ve teklif onaylandıktan sonra plan oluşturulur.','author_name'=>'NetVera Teknoloji Yazılım'];
        return self::validate($d);
    }
    public static function validate(array $input):array
    {
        $data=['module'=>'regional_landing','version'=>1];
        foreach(['title'=>180,'city'=>100,'province'=>100,'service'=>160,'area'=>300,'summary'=>600,'local_context'=>3000,'image_alt'=>220,'cta_label'=>70,'services_title'=>180,'references_title'=>180,'faq_title'=>180,'cta_title'=>180,'cta_summary'=>500]as $key=>$max)$data[$key]=LandingContentService::text($input[$key]??'',$max);
        $data['slug']=self::slug($input['slug']??'');
        foreach(['status'=>['inactive','active'],'layout'=>['studio','editorial'],'theme'=>['violet','blue','rose'],'working_mode'=>['remote','hybrid','onsite']]as $key=>$allowed){$data[$key]=$input[$key]??$allowed[0];if(!is_string($data[$key])||!in_array($data[$key],$allowed,true))throw new \InvalidArgumentException('Geçersiz yayın veya tasarım tercihi.');}
        foreach(['image','mobile_image']as $key)$data[$key]=LandingContentService::image($input[$key]??'');
        if(($data['image']!==''||$data['mobile_image']!=='')&&$data['image_alt']==='')throw new \InvalidArgumentException('Banner görselinin alt metnini yazın.');
        foreach(['sections','links','faqs']as $kind)$data[$kind]=LandingContentService::rows($input[$kind]??[],$kind,$kind==='links'?12:12);
        foreach(['service_ids','reference_ids']as $key){$ids=$input[$key]??[];if(!is_array($ids)||count($ids)>24)throw new \InvalidArgumentException('En fazla 24 hizmet veya referans seçebilirsiniz.');$data[$key]=array_values(array_unique(array_map(static function($v)use($key){if(!is_scalar($v)||!preg_match('/^[a-zA-Z0-9_-]{1,64}$/D',(string)$v)||($key==='service_ids'&&(!ctype_digit((string)$v)||(int)$v<1)))throw new \InvalidArgumentException('Geçersiz hizmet/referans seçimi.');return $key==='service_ids'?(int)$v:(string)$v;},$ids)));}
        $data['seo']=LandingContentService::seo($input['seo']??[]);
        $data['seo']['service_area']=trim($data['city'].($data['province']!==''?', '.$data['province']:''));
        if($data['status']==='active'){
            foreach(['title','city','service','summary','local_context']as $key)if($data[$key]==='')throw new \InvalidArgumentException('Yayın için başlık, şehir, hizmet, açıklama ve özgün bölgesel kapsam zorunludur.');
            if(!$data['sections']||$data['seo']['title']===''||$data['seo']['description']==='')throw new \InvalidArgumentException('Yayın için en az bir içerik bölümü, meta başlık ve açıklama yazın.');
        }
        if(strlen(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR))>180000)throw new \InvalidArgumentException('Sayfa toplam içerik sınırını aşıyor.');
        return $data;
    }
    public static function redirects():array
    {
        $row=Database::getInstance()->fetch('SELECT setting_value FROM settings WHERE setting_key=?',[self::REDIRECTS]);$data=json_decode($row['setting_value']??'[]',true);return is_array($data)?$data:[];
    }
    public static function byId(int $id):?array
    {$row=Database::getInstance()->fetch('SELECT * FROM pages WHERE id=?',[$id]);return $row&&self::isLanding($row)?$row:null;}
    public static function bySlug(string $slug):?array
    {$row=Database::getInstance()->fetch('SELECT * FROM pages WHERE slug=?',[$slug]);return $row&&self::isLanding($row)?$row:null;}
    public static function revision(array $row):string
    {return hash('sha256',json_encode([$row['id'],$row['title'],$row['slug'],$row['status'],$row['content']],JSON_THROW_ON_ERROR));}
    public static function listing(int $page=1,string $search='',bool $public=false):array
    {
        $db=Database::getInstance();$where='content LIKE ?';$params=[self::PREFIX.'%'];
        if($public){$where.=" AND status='active'";}
        if($search!==''){$where.=' AND (title LIKE ? OR slug LIKE ?)';$params[]='%'.$search.'%';$params[]='%'.$search.'%';}
        $count=$db->count('pages',$where,$params);$page=max(1,min($page,max(1,(int)ceil($count/20))));$offset=($page-1)*20;
        $rows=$db->fetchAll('SELECT * FROM pages WHERE '.$where.' ORDER BY id DESC LIMIT 20 OFFSET '.$offset,$params);
        // A public directory exposes active pages, including noindex pages, as
        // useful navigation. Sitemap separately applies robots/canonical policy.
        return ['rows'=>$rows,'total'=>$count,'page'=>$page,'pages'=>max(1,(int)ceil($count/20))];
    }
    public static function fingerprint(array $data):string
    {
        $text=($data['local_context']??'').' '.implode(' ',array_column($data['sections']??[],'body'));
        $places=array_filter([$data['city']??'',$data['province']??'']);usort($places,static fn($a,$b)=>mb_strlen($b)<=>mb_strlen($a));
        foreach($places as $place)$text=preg_replace('~'.preg_quote($place,'~').'~iu','[yer]',$text)??$text;
        return hash('sha256',preg_replace('/\s+/u',' ',mb_strtolower(trim($text))));
    }
    public static function save(array $data,int $id=0,string $revision=''):int
    {
        $db=Database::getInstance();$pdo=$db->getPdo();$pdo->beginTransaction();
        try{
            $old=$id?$db->fetch('SELECT * FROM pages WHERE id=? FOR UPDATE',[$id]):null;
            if($id&&(!$old||!self::isLanding($old)||!hash_equals(self::revision($old),$revision)))throw new \InvalidArgumentException('Sayfa değişti veya bulunamadı. Editörü yenileyin.');
            $collision=$db->fetch('SELECT id FROM pages WHERE slug=? AND id<>? LIMIT 1',[$data['slug'],$id]);
            if($collision)throw new \InvalidArgumentException('Bu slug başka bir sayfada kullanılıyor.');
            // Serialize alias reservations and never claim an unrelated root URL.
            $db->query('INSERT IGNORE INTO settings(setting_key,setting_value,setting_group) VALUES(?,?,?)',[self::REDIRECTS,'{}','seo']);
            $aliases=json_decode($db->fetch('SELECT setting_value FROM settings WHERE setting_key=? FOR UPDATE',[self::REDIRECTS])['setting_value'],true)?:[];
            if(isset($aliases[$data['slug']])&&(int)$aliases[$data['slug']]!==$id)throw new \InvalidArgumentException('Bu slug eski bir 301 adresi olarak korunuyor.');
            if($data['status']==='active')foreach($db->fetchAll("SELECT id,content FROM pages WHERE content LIKE ? AND status='active' AND id<>?",[self::PREFIX.'%',$id])as $candidate){$other=self::decode($candidate);if($other&&hash_equals(self::fingerprint($other),self::fingerprint($data)))throw new \InvalidArgumentException('Yalnızca şehir adını değiştiren kopya içerik yayınlanamaz. Özgün bölgesel kapsam ve içerik yazın.');}
            $json=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);
            $row=['title'=>$data['title'],'slug'=>$data['slug'],'status'=>$data['status'],'content'=>$json,'seo_title'=>$data['seo']['title'],'seo_description'=>$data['seo']['description'],'seo_focus_keyword'=>$data['seo']['focus_keyword'],'canonical_url'=>$data['seo']['canonical'],'og_title'=>$data['seo']['og_title'],'og_description'=>$data['seo']['og_description'],'og_image'=>$data['seo']['og_image']];
            if($id)$db->update('pages',$row,'id=?',[$id]);else $id=$db->insert('pages',$row);
            if($old&&$old['slug']!==$data['slug']){$aliases[$old['slug']]=$id;unset($aliases[$data['slug']]);$db->query('UPDATE settings SET setting_value=? WHERE setting_key=?',[json_encode($aliases,JSON_THROW_ON_ERROR),self::REDIRECTS]);}
            $pdo->commit();return $id;
        }catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }
    public static function contacts():array
    {
        $out=[['label'=>'İletişime geçin','url'=>'/iletisim','icon'=>'message-circle']];
        $phone=preg_replace('/[^0-9+]/','',(string)setting('site_phone'));if(preg_match('/^\+?[0-9]{7,16}$/D',$phone))$out[]=['label'=>setting('site_phone'),'url'=>'tel:'.$phone,'icon'=>'phone'];
        $wa=preg_replace('/\D/','',(string)setting('whatsapp_number'));if(preg_match('/^[0-9]{7,16}$/D',$wa))$out[]=['label'=>'WhatsApp','url'=>'https://wa.me/'.$wa,'icon'=>'whatsapp'];
        $email=setting('site_email');if(filter_var($email,FILTER_VALIDATE_EMAIL))$out[]=['label'=>$email,'url'=>'mailto:'.$email,'icon'=>'mail'];
        return $out;
    }
    public static function selectedServices(array $ids):array
    {
        if(!$ids)return [];$rows=Database::getInstance()->fetchAll('SELECT c.*,p.status parent_status FROM categories c LEFT JOIN categories p ON p.id=c.parent_id WHERE c.id IN ('.implode(',',array_fill(0,count($ids),'?')).") AND c.status='active' AND (c.parent_id IS NULL OR p.status='active')",$ids);
        return array_values(array_filter($rows,static fn($r)=>ServiceCategoryVisibility::show($r)));
    }
    public static function selectedReferences(array $ids):array
    {return array_values(array_filter(ReferencesService::all(true),static fn($r)=>in_array((string)$r['id'],$ids,true)));}
    public static function schema(array $row,array $data):string
    {
        $own=url('/'.rawurlencode($row['slug']));$site=rtrim(SeoMetadataService::webUrl(setting('site_url'))??url('/'),'/');
        $area=['@type'=>'City','name'=>$data['city']];if($data['province']!=='')$area['containedInPlace']=['@type'=>'AdministrativeArea','name'=>$data['province']];
        $graph=[['@type'=>'Service','@id'=>$own.'#service','name'=>$data['service'],'serviceType'=>$data['service'],'areaServed'=>$area,'description'=>$data['summary'],'provider'=>['@id'=>$site.'/#organization'],'url'=>$own],['@type'=>'BreadcrumbList','itemListElement'=>[['@type'=>'ListItem','position'=>1,'name'=>'Ana Sayfa','item'=>url('/')],['@type'=>'ListItem','position'=>2,'name'=>'Hizmet Bölgeleri','item'=>url('/hizmet-bolgeleri')],['@type'=>'ListItem','position'=>3,'name'=>$data['title'],'item'=>$own]]],['@type'=>'WebPage','@id'=>$own.'#page','url'=>$own,'name'=>$data['title'],'description'=>$data['summary'],'dateModified'=>date('c',strtotime($row['updated_at']))]];
        return json_encode(['@context'=>'https://schema.org','@graph'=>$graph],JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
    }
}
