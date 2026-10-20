<?php
namespace App\Services;

use App\Core\Database;
use App\Core\Upload;

/** Social-only presentation and admin-managed landing content. No price/order/provider writes. */
final class SocialStorefrontService
{
    private static array $cache=[];
    public static function key(string $type,int $id):string
    {
        if(!in_array($type,['category','package'],true)||$id<1)throw new \InvalidArgumentException('Geçersiz sosyal sayfa.');
        return 'social_landing_'.$type.'_'.$id;
    }
    public static function stored(string $type,int $id):array
    {
        $key=self::key($type,$id);
        if(!array_key_exists($key,self::$cache)){
            $row=Database::getInstance()->fetch('SELECT setting_value FROM settings WHERE setting_key=?',[$key]);
            $value=$row?json_decode((string)$row['setting_value'],true):[];
            self::$cache[$key]=is_array($value)?$value:[];
        }
        return self::$cache[$key];
    }
    public static function plain(mixed $value,int $max):string
    {
        if(!is_string($value)||mb_strlen($value)>$max)throw new \InvalidArgumentException('İçerik alanı geçersiz veya çok uzun.');
        return trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u','',strip_tags($value))??'');
    }
    /** No third-party URLs, SVG/script paths or traversal in editorial media. */
    public static function imagePath(mixed $value):string
    {
        if(!is_string($value))throw new \InvalidArgumentException('Geçersiz görsel yolu.');
        $path=trim($value);
        if($path==='')return '';
        if(!preg_match('~^/(?:uploads|assets/img)/[a-zA-Z0-9_./-]+\.(?:jpe?g|png|webp|gif)$~D',$path)||str_contains($path,'..')||str_contains($path,'//'))throw new \InvalidArgumentException('Görsel kendi sitenizdeki JPG, PNG, WebP veya GIF dosyası olmalıdır.');
        return $path;
    }
    public static function validate(array $input):array
    {
        $mode=$input['social_content_mode']??'default';
        if(!is_string($mode)||!in_array($mode,['default','custom','hidden'],true))throw new \InvalidArgumentException('Geçerli bir içerik modu seçin.');
        $data=['mode'=>$mode];
        foreach(['title'=>180,'summary'=>600,'image_alt'=>220]as $key=>$limit)$data[$key]=self::plain($input['social_page_'.$key]??'',$limit);
        foreach(['image','mobile_image']as $key)$data[$key]=self::imagePath($input['social_page_'.$key]??'');
        $data['sections']=[];$data['faqs']=[];
        foreach(['sections'=>12,'faqs'=>12]as $kind=>$limit){
            $rows=$input['social_'.$kind]??[];
            if(!is_array($rows)||count($rows)>$limit)throw new \InvalidArgumentException('En fazla 12 içerik bölümü ve 12 soru ekleyebilirsiniz.');
            foreach($rows as $row){
                if(!is_array($row))throw new \InvalidArgumentException('İçerik satırı geçersiz.');
                if($kind==='faqs'){
                    $q=self::plain($row['question']??'',240);$a=self::plain($row['answer']??'',1800);
                    if($q===''&&$a==='')continue;
                    if($q===''||$a==='')throw new \InvalidArgumentException('SSS için hem soru hem yanıt yazın.');
                    $data['faqs'][]=['question'=>$q,'answer'=>$a];continue;
                }
                $clean=[];
                foreach(['title'=>220,'body'=>6000,'subheading'=>220,'detail'=>3000,'list'=>2000,'alt'=>220]as $field=>$max)$clean[$field]=self::plain($row[$field]??'',$max);
                $clean['image']=self::imagePath($row['image']??'');
                if(implode('',array_values($clean))==='')continue;
                if($clean['title']===''||$clean['body']==='')throw new \InvalidArgumentException('Her içerik bölümünde H2 başlık ve açıklama olmalıdır.');
                $data['sections'][]=$clean;
            }
        }
        if(strlen(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR))>100000)throw new \InvalidArgumentException('Sayfa içeriği toplam uzunluk sınırını aşıyor.');
        return $data;
    }
    /** Called before any category/package update. Only files from this submission are written. */
    public static function prepare(array $input,array $files):?array
    {
        if(($input['social_content_present']??'')!=='1')return null;
        $data=self::validate($input);
        $uploads=[];
        try{
            foreach(['image','mobile_image']as $name){
                $file=$files['social_upload_'.$name]??[];
                if(($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE){$path=self::upload($file);$uploads[]=$path;$data[$name]=$path;}
            }
            $sectionFiles=$files['social_section_upload']??[];
            // Editor sends a stable per-row index; normalize only after matching uploads.
            $raw=$input['social_sections']??[];$position=0;
            foreach($raw as $index=>$row){
                $hasContent=trim(implode('',array_map(static fn($v)=>is_string($v)?$v:'',$row)))!=='';
                if(!$hasContent)continue;
                if(isset($sectionFiles['error'][$index])&&$sectionFiles['error'][$index]!==UPLOAD_ERR_NO_FILE){
                    $file=[];foreach(['name','type','tmp_name','error','size']as $part)$file[$part]=$sectionFiles[$part][$index]??null;
                    $path=self::upload($file);$uploads[]=$path;$data['sections'][$position]['image']=$path;
                }
                $position++;
            }
            return $data;
        }catch(\Throwable $e){
            foreach($uploads as $path)if(preg_match('~^/uploads/social-landing/[a-zA-Z0-9_.-]+$~D',$path)&&is_file(PUBLIC_PATH.$path))unlink(PUBLIC_PATH.$path);
            throw $e;
        }
    }
    private static function upload(array $file):string
    {
        if(($file['error']??null)!==UPLOAD_ERR_OK||!is_uploaded_file($file['tmp_name']??'')||!in_array(strtolower(pathinfo($file['name']??'',PATHINFO_EXTENSION)),['jpg','jpeg','png','webp','gif'],true)||!@getimagesize($file['tmp_name']))throw new \InvalidArgumentException('Geçerli bir JPG, PNG, WebP veya GIF görseli yükleyin.');
        $path=Upload::image($file,'social-landing');
        if(!$path)throw new \InvalidArgumentException('Görsel yüklenemedi; boyut en fazla 5 MB olmalıdır.');
        return $path;
    }
    public static function save(string $type,int $id,?array $data):void
    {
        if($data===null)return;
        $key=self::key($type,$id);
        Database::getInstance()->query('INSERT INTO settings(setting_key,setting_value,setting_group) VALUES(?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),setting_group=VALUES(setting_group)',[$key,json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),'social_landing']);
        self::$cache[$key]=$data;
    }
    public static function intent(string $text):array
    {
        $text=mb_strtolower($text,'UTF-8');
        foreach([
            ['yorum beğeni|yorum-begeni','engagement','thumbs-up','Yorum beğenisi'],
            ['takipçi|takipci|abone|üye','audience','users','Takipçi & abone'],
            ['beğeni|begeni','engagement','heart','Beğeni'],
            ['yorum','comments','message-circle','Yorum'],
            ['izlenme|dinlenme|reels|shorts|yayın|yayin','views','play','İzlenme & dinlenme'],
            ['kaydet','saved','bookmark','Kaydetme'],
            ['paylaş|paylas','share','share-2','Paylaşım'],
            ['ziyaret|gösterim|gosterim','reach','bar-chart','Erişim'],
        ]as [$pattern,$key,$icon,$label])if(preg_match('~'.$pattern.'~u',$text))return ['key'=>$key,'icon'=>$icon,'label'=>$label];
        return ['key'=>'other','icon'=>'layers','label'=>'Diğer hizmetler'];
    }
    public static function defaults(array $entity,string $type='category'):array
    {
        $platform=$type==='package'?SocialPlatformIdentity::fromPackage($entity):SocialPlatformIdentity::fromCategory($entity);
        $brand=SocialPlatformIdentity::brand($platform);$label=$brand[0]??'Sosyal medya';
        $name=trim((string)($entity['name']??$entity['category_name']??'Sosyal Medya Hizmetleri'));
        if($name==='')$name=(string)($entity['seo_title']??'Sosyal Medya Hizmetleri');
        $intent=self::intent($name.' '.($entity['slug']??''));
        $isPlatform=$intent['key']==='other';
        $title=$type==='package'?package_display_name($entity):$name;
        $subject=$isPlatform?$label.' hizmetleri':$name;
        $visual=in_array($platform,['tiktok','youtube','twitch','kick'],true)?'video-creators':(in_array($platform,['instagram','facebook','pinterest','snapchat'],true)?'social-creator':'social-community');
        $summary=$isPlatform?$label.' için takipçi, etkileşim ve içerik hizmetlerini keşfedin. Hizmet türünü seçin, miktarı ve paket koşullarını karşılaştırın.':$subject.' paketlerini teslim miktarı, toplam fiyat ve belirtilen süreleriyle karşılaştırın. İhtiyacınıza uygun paketi seçerek sipariş bilgilerinizi tamamlayın.';
        $sections=[
            ['title'=>$subject.' nasıl satın alınır?','body'=>'Önce hesabınız veya içeriğiniz için doğru hizmet türünü belirleyin. Paket kartında yazan miktar ve toplam fiyatı inceleyin; ardından paketin detay sayfasında istenen kullanıcı adı ya da içerik bağlantısını girin. Sosyal medya hesabınızın şifresi istenmez.','subheading'=>'Sipariş öncesi kontrol listesi','detail'=>'Kullanıcı adı veya bağlantının doğru olduğundan, hedef hesabın ya da içeriğin paketin gerektirdiği görünürlükte olduğundan emin olun. Süre ve hizmet kapsamı her pakette farklı olabilir.','list'=>"Hizmet türünü ve teslim edilecek miktarı kontrol edin.\nPaketin açıklamasını, koşullarını ve tahmini teslimatını okuyun.\nGerekli bağlantıyı girip ödeme adımını tamamlayın.\nSipariş durumunu hesabınızdan takip edin.",'image'=>'','alt'=>''],
            ['title'=>$subject.' fiyatları nasıl karşılaştırılır?','body'=>'Gösterilen tutar, kartta belirtilen miktarın tamamına ait paket fiyatıdır. Farklı hizmet türleri, hedefleme seçenekleri ve teslimat koşulları aynı değildir; yalnızca birim fiyat üzerinden karar vermeyin. Güncel fiyat ve varsa indirim, seçtiğiniz paketin üzerinde gösterilir.','subheading'=>'İhtiyacınıza uygun miktarı seçin','detail'=>'Küçük bir içerik çalışmasıyla düzenli hesap yönetimi aynı kapsamı gerektirmez. Paketler arasında miktar, hedef ve açıklanan hizmet özelliklerini birlikte değerlendirin. Belirsiz bir koşul varsa satın almadan önce destek ekibine sorun.','list'=>'','image'=>'','alt'=>''],
            ['title'=>'Teslimat, hesap bilgileri ve önemli koşullar','body'=>'Teslimat süreleri paket üzerinde belirtilen tahmini aralıklardır. Platformun teknik durumu, hedefin erişilebilirliği ve sipariş yoğunluğu süreyi etkileyebilir. Başlamayan veya tamamlanmayan bir sipariş için sipariş numaranızla destek talebi oluşturabilirsiniz.','subheading'=>'Beklentilerinizi doğru belirleyin','detail'=>'Sosyal medya hizmetleri belirli bir erişim, satış, keşfet sıralaması veya kalıcı sonuç garantisi değildir. Hesaplar ve etkileşimler zaman içinde değişebilir. Garanti, telafi ve iade yalnızca satın aldığınız pakette ve ilgili sözleşmelerde açıkça belirtilen koşullara tabidir. Platformun kullanım kurallarını da inceleyin.','list'=>'','image'=>'','alt'=>''],
        ];
        $faqs=[
            ['question'=>$subject.' için şifre paylaşmam gerekir mi?','answer'=>'Hayır. Sipariş formunda yalnızca pakete uygun kullanıcı adı veya içerik bağlantısı istenir. Hesap şifrenizi, doğrulama kodunuzu ya da özel giriş bilgilerinizi paylaşmayın.'],
            ['question'=>'Karttaki fiyat hangi miktarı kapsıyor?','answer'=>'Paket kartında yazan fiyat, belirtilen teslim miktarının tamamını kapsar. Seçilen sosyal medya paketi bir kez satın alınır; miktar, sipariş özetinde ayrıca gösterilir.'],
            ['question'=>'Siparişimi nereden takip ederim?','answer'=>'Üye panelindeki Siparişlerim bölümünden sipariş durumunu görebilirsiniz. Sağlayıcıya bağlı bir teslimat kaydı varsa son bildirilen miktar ve durum da sipariş detayında gösterilir.'],
            ['question'=>'Teslimat veya telafi koşulları her pakette aynı mı?','answer'=>'Hayır. Tahmini süre, hizmet kapsamı ve varsa telafi koşulları pakete göre değişir. Satın almadan önce ilgili paketin açıklamasını ve kullanım/iade koşullarını okuyun.'],
        ];
        return ['mode'=>'default','title'=>$title,'summary'=>$summary,'image'=>'/assets/img/social-storefront/'.$visual.'.webp','mobile_image'=>'','image_alt'=>$label.' içerik üretimi ve sosyal medya hizmetlerini temsil eden tanıtım görseli','sections'=>$sections,'faqs'=>$faqs,'platform'=>$platform??'social','platform_label'=>$label,'icon'=>SocialPlatformIdentity::iconFor($platform,'layers'),'intent'=>$intent];
    }
    public static function page(array $entity,string $type='category',?array $options=null):array
    {
        $data=self::defaults($entity,$type);$stored=$options??self::stored($type,(int)$entity['id']);
        if($type==='category'&&$options===null){
            $old=CategoryHeroService::options((int)$entity['id']);
            if(trim(($old['lead']??'').' '.($old['accent']??''))!==''){$hero=CategoryHeroService::get($entity);$data['title']=trim($hero['lead'].' '.$hero['accent']);}
            if(!empty($old['description']))$data['summary']=$old['description'];
            if(!empty($old['visual'])&&$old['visual']!=='auto'){$hero=CategoryHeroService::get($entity);$data['image']=$hero['src'];$data['image_alt']=$hero['alt'];}
        }
        foreach(['title','summary','image','mobile_image','image_alt']as $field)if(is_string($stored[$field]??null)&&trim($stored[$field])!=='')$data[$field]=$stored[$field];
        foreach(['image','mobile_image']as $field){try{$data[$field]=self::imagePath($data[$field]);}catch(\InvalidArgumentException $e){$data[$field]='';}}
        if(($stored['mode']??'default')==='custom'){$data['sections']=is_array($stored['sections']??null)?$stored['sections']:[];$data['faqs']=is_array($stored['faqs']??null)?$stored['faqs']:[];}
        if(($stored['mode']??'default')==='hidden'){$data['sections']=[];$data['faqs']=[];}
        return $data;
    }
    public static function price(array $p):float{return !empty($p['discount_price'])&&$p['discount_price']<$p['price']?(float)$p['discount_price']:(float)$p['price'];}
    public static function directory(array $groups):array
    {
        foreach($groups as &$group)$group['intent']=self::intent($group['name']);unset($group);
        return $groups;
    }
}
