<?php
namespace App\Services;
use App\Core\Database;

final class HomeKnowledgeService
{
    public const KEY='home_knowledge_v1';
    public static function defaults():array
    {
        $cards=[];
        foreach([
            ['Sosyal medya hizmetini nasıl seçmelisiniz?','Instagram, TikTok ve YouTube paketleri aynı ihtiyacı karşılamaz. Önce hesabınızın ya da içeriğinizin hedefini belirleyin; takipçi, beğeni, yorum ve izlenme seçeneklerini birbirinden ayırın.','Paketin teslim miktarını, toplam fiyatını ve tahmini süresini birlikte karşılaştırın. Hesap şifrenizi paylaşmayın; sipariş formunun istediği kullanıcı adı veya bağlantıyı kullanın.','Sosyal medya paketlerinde fiyat neyi kapsar?','Gösterilen fiyat, kartta belirtilen teslim miktarının toplam tutarıdır. Kapsam ve koşullar her hizmette farklı olabilir.','/kategoriler/grup/social','Sosyal medya hizmetlerini karşılaştırın','users','social'],
            ['SEO, GEO ve AIO birlikte nasıl çalışır?','Arama görünürlüğünün temeli; erişilebilir sayfalar, anlaşılır başlıklar, özgün yanıtlar ve doğru teknik yapıdır. GEO ve AIO için de ziyaretçinin sorusunu açıkça cevaplayan, kaynaklarla desteklenmiş içerikler önem taşır.','İç bağlantılar kullanıcıyı ilgili hizmete taşımalı; yapılandırılmış veriler görünür içerikle eşleşmelidir. Hiçbir teknik ayar tek başına sıralama ya da yapay zekâ yanıtlarında yer alma garantisi vermez.','AI aramalarında görünmek için ayrı bir site gerekir mi?','Hayır. İyi bir kullanıcı deneyimi, taranabilir metinler ve tutarlı kurum bilgileri aynı site üzerinde oluşturulabilir.','/kategoriler/grup/marketing','SEO ve dijital hizmetleri inceleyin','search','search'],
            ['İşletmeniz için doğru dijital yol haritası','Kurumsal web sitesi, reklam ve sosyal medya çalışmalarının ortak bir hedefe bağlanması gerekir. Önce hizmet kapsamınızı, hedef kitlenizi ve ölçmek istediğiniz dönüşümü tanımlayın.','Şehrinize yönelik bir hizmet arıyorsanız sayfadaki hizmet bölgesini ve çalışma biçimini inceleyin. Projenin kapsamını ekiple netleştirin; mevcut hizmetleri ve örnek çalışmaları birlikte değerlendirin.','Hangi hizmetten başlamalıyım?','Mevcut siteniz, içerikleriniz ve hedefleriniz başlangıç noktasını belirler. İletişim sayfasından ihtiyacınızı paylaşarak kapsam hakkında bilgi alabilirsiniz.','/iletisim','Projenizi birlikte değerlendirelim','layers','agency'],
        ]as [$title,$body,$detail,$question,$answer,$path,$label,$icon,$theme])$cards[]=['enabled'=>true,'title'=>$title,'body'=>$body."\n\n".$detail,'question'=>$question,'answer'=>$answer,'links'=>[['label'=>$label,'path'=>$path]],'faqs'=>[],'seo'=>['focus_keyword'=>'','secondary_keywords'=>'','entity_topics'=>'','geo_summary'=>'','direct_answer'=>'','sources'=>''],'icon'=>$icon,'theme'=>$theme];
        return ['enabled'=>true,'title'=>'Dijital hizmetleri bilinçli seçin.','summary'=>'İhtiyacınızı tanıyın, kapsamı karşılaştırın ve doğru soruları sorun.','cards'=>$cards];
    }
    public static function get():array
    {
        $raw=Database::getInstance()->fetch('SELECT setting_value FROM settings WHERE setting_key=?',[self::KEY]);
        if(!$raw)return self::defaults();
        $data=json_decode($raw['setting_value'],true);
        try{return self::validate($data);}catch(\Throwable $ignored){return self::defaults();}
    }
    public static function validate(mixed $input):array
    {
        if(!is_array($input)||!is_array($input['cards']??null)||count($input['cards'])!==3)throw new \InvalidArgumentException('Tam olarak üç bilgi kartı tanımlayın.');
        $data=['enabled'=>!empty($input['enabled']),'title'=>LandingContentService::text($input['title']??'',180),'summary'=>LandingContentService::text($input['summary']??'',500),'cards'=>[]];
        if($data['enabled']&&$data['title']==='')throw new \InvalidArgumentException('Bilgi alanının H2 başlığını yazın.');
        foreach(array_values($input['cards'])as $card){
            if(!is_array($card))throw new \InvalidArgumentException('Geçersiz bilgi kartı.');
            $row=['enabled'=>!empty($card['enabled'])];
            foreach(['title'=>180,'body'=>3000,'question'=>240,'answer'=>1200]as $key=>$limit)$row[$key]=LandingContentService::text($card[$key]??'',$limit);
            if($row['enabled']&&($row['title']===''||$row['body']===''))throw new \InvalidArgumentException('Açık kartlarda başlık ve metin zorunludur.');
            if(($row['question']==='')!==($row['answer']===''))throw new \InvalidArgumentException('Ana soru ve kısa cevap birlikte doldurulmalıdır.');
            $row['links']=LandingContentService::rows($card['links']??[],'links',6);$row['faqs']=LandingContentService::rows($card['faqs']??[],'faqs',4);
            $row['seo']=LandingContentService::seo($card['seo']??[]);
            $row['icon']=in_array($card['icon']??'',['users','search','layers','map-pin','sparkles'],true)?$card['icon']:'layers';
            $row['theme']=in_array($card['theme']??'',['social','search','agency'],true)?$card['theme']:'agency';$data['cards'][]=$row;
        }return $data;
    }
    public static function save(array $data,string $revision):void
    {
        $db=Database::getInstance();$pdo=$db->getPdo();$pdo->beginTransaction();
        try{$row=$db->fetch('SELECT setting_value FROM settings WHERE setting_key=? FOR UPDATE',[self::KEY]);$current=$row['setting_value']??'';
            if(!hash_equals(hash('sha256',$current),$revision))throw new \InvalidArgumentException('İçerik başka bir oturumda değişti. Sayfayı yenileyip yeniden deneyin.');
            $db->query('INSERT INTO settings(setting_key,setting_value,setting_group) VALUES(?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)',[self::KEY,json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),'appearance']);$pdo->commit();
        }catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }
    public static function revision():string
    {return hash('sha256',Database::getInstance()->fetch('SELECT setting_value FROM settings WHERE setting_key=?',[self::KEY])['setting_value']??'');}
}
