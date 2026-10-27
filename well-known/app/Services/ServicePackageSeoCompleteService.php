<?php
namespace App\Services;
use App\Core\Database;

/**
 * Safe one-click fill for currently blank/legacy fields on service packages.
 * Does not insert/delete packages or touch prices, slugs, identifiers,
 * SMM provider mappings, NetVera ready scripts, or existing author-edited text.
 */
final class ServicePackageSeoCompleteService
{
    private const FIELDS=['description','short_description','seo_title','seo_description',
        'seo_focus_keyword','canonical_url','og_title','og_description','image_alt'];
    private const EXTRA=['secondary_keywords','robots','geo_summary','entity_topics',
        'service_area','author_name','author_type','author_url','content_intent',
        'main_question','direct_answer'];

    public static function eligible(array $package):bool
    {
        if(empty($package['category_id'])||trim((string)($package['name']??''))==='')return false;
        if(ServiceCategoryVisibility::isLegacySoftware($package))return false;
        $text=mb_strtolower((string)($package['name']??'').' '.(string)($package['slug']??'').' '.
           (string)($package['category_slug']??'').' '.(string)($package['parent_category_slug']??''),'UTF-8');
        // Ready software catalog and historical script products remain admin-owned.
        if(preg_match('/haz[ıi]r[- ]?yaz[ıi]l[ıi]m|script|haz[ıi]r[- ]?site|wordpress[- ]?tema|software[- ]?license/u',$text))return false;
        return true;
    }

    private static function missing(string $field,string $value):bool
    {
        $value=trim($value);
        if($value==='')return true;
        if(preg_match('/YorumHizmeti(?:\.tr)?|Yorum Hizmeti/iu',$value))return true;
        if($field==='seo_title'||$field==='og_title')return mb_strlen($value,'UTF-8')<12;
        if($field==='seo_description'||$field==='og_description')return mb_strlen($value,'UTF-8')<60;
        return false;
    }

    public static function proposal(array $package):array
    {
        if(!self::eligible($package))return [];
        $name=trim((string)$package['name']);
        $category=trim((string)($package['category_name']??'Dijital hizmet'));
        $slug=trim((string)$package['slug']);
        if(!PublicSeoUrls::isSlug($slug))return [];
        $quote=ServiceQuotePolicy::package($package);
        $guide=ServiceEditorialBlocks::package($package,$quote);
        $short=trim(strip_tags((string)($package['short_description']??'')));
        if(mb_strlen($short,'UTF-8')<45||self::missing('short_description',$short)){
            $short=$quote
                ?$name.' için projenize uygun hizmet kapsamı, teslim takvimi ve ihtiyaçlar birlikte planlanır. Fiyat ve çalışma şartları teklif sürecinde netleşir.'
                :$name.' paketinin hizmet türünü, teslim miktarını, fiyatını, gereken bağlantıyı ve açıklanmış teslim koşullarını siparişten önce karşılaştırın.';
        }
        $short=mb_substr($short,0,495,'UTF-8');
        $intro=mb_substr(trim($short),0,148,'UTF-8');
        $desc='<h2>'.htmlspecialchars($name.' — kapsam ve değerlendirme',ENT_QUOTES,'UTF-8').'</h2>';
        $desc.='<p>'.htmlspecialchars($short,ENT_QUOTES,'UTF-8').'</p>';
        foreach($guide['paragraphs']as $paragraph)$desc.='<p>'.htmlspecialchars($paragraph,ENT_QUOTES,'UTF-8').'</p>';
        $seoTitle=mb_substr($name.' | NetVera Teknoloji Yazılım',0,195,'UTF-8');
        $meta=mb_substr($intro.' | NetVera',0,158,'UTF-8');
        $fields=[
            'short_description'=>$short,
            'description'=>$desc,
            'seo_title'=>$seoTitle,
            'seo_description'=>$meta,
            'seo_focus_keyword'=>mb_substr($name,0,95,'UTF-8'),
            'canonical_url'=>'https://netvera.tr/paket/'.rawurlencode($slug),
            'og_title'=>$seoTitle,
            'og_description'=>$meta,
            'image_alt'=>!empty($package['image'])?mb_substr($name.' hizmet paketi — NetVera',0,245,'UTF-8'):'',
        ];
        $changes=[];
        foreach(self::FIELDS as $key){
            if($fields[$key]===''||!self::missing($key,(string)($package[$key]??'')))continue;
            $changes[$key]=$fields[$key];
        }
        return [
            'fields'=>$changes,
            'extra'=>[
                'secondary_keywords'=>mb_substr($category.', '.$name.', fiyat ve hizmet kapsamı',0,900,'UTF-8'),
                'robots'=>($package['status']??'active')==='active'?'index,follow,max-image-preview:large':'noindex,follow',
                'geo_summary'=>mb_substr($short,0,490,'UTF-8'),
                'entity_topics'=>mb_substr($name.', '.$category.', NetVera Teknoloji Yazılım',0,450,'UTF-8'),
                'service_area'=>'Türkiye',
                'author_name'=>'NetVera Teknoloji Yazılım','author_type'=>'Organization','author_url'=>'https://netvera.tr',
                'content_intent'=>$quote?'teklif-al':'satin-alma',
                'main_question'=>$name.' hizmeti nasıl seçilir?',
                'direct_answer'=>mb_substr($short,0,790,'UTF-8')
            ]
        ];
    }

    public static function apply():array
    {
        $db=Database::getInstance();
        $rows=$db->fetchAll("SELECT p.*,c.name category_name,c.slug category_slug,
                parent.name parent_category_name,parent.slug parent_category_slug
                FROM packages p JOIN categories c ON c.id=p.category_id
                LEFT JOIN categories parent ON parent.id=c.parent_id ORDER BY p.id");
        $report=['examined'=>0,'updated'=>0,'fields'=>0,'extras'=>0,'skipped'=>0];
        $changes=[];$backups=[];
        foreach($rows as $row){
            if(!self::eligible($row)){$report['skipped']++;continue;}
            $report['examined']++;
            $candidate=self::proposal($row);
            if(!$candidate)continue;
            $old=NetveraSeoBridge::get('package',(int)$row['id']);
            $extra=$old;
            foreach($candidate['extra']as $field=>$text){
                if(trim((string)($extra[$field]??''))==='')$extra[$field]=$text;
            }
            if(!$candidate['fields']&&$extra===$old)continue;
            $backup=[];
            foreach($candidate['fields']as $field=>$value)$backup[$field]=$row[$field]??null;
            if($candidate['fields']){
                $merged=array_merge($row,$candidate['fields']);
                $candidate['fields']['seo_score']=(new SeoScoreService())->calculate($merged)['score'];
                $backup['seo_score']=$row['seo_score']??null;
            }
            $changes[]=['id'=>(int)$row['id'],'fields'=>$candidate['fields'],'extra'=>$extra,'oldExtra'=>$old];
            $backups[]=['id'=>(int)$row['id'],'slug'=>$row['slug'],'previousFields'=>$backup,'previousExtra'=>$old];
        }
        if(!$changes)return $report;

        $directory=BASE_PATH.'/storage/backups';
        if(!is_dir($directory)&&!mkdir($directory,0700,true)&&!is_dir($directory))
            throw new \RuntimeException('SEO yedek dizini oluşturulamadı.');
        $file=$directory.'/service-seo-before-'.gmdate('Ymd-His').'.json';
        $backupJson=json_encode($backups,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
        if(file_put_contents($file,$backupJson,LOCK_EX)===false)
            throw new \RuntimeException('SEO alanları değişmeden önce yedek alınamadı.');
        @chmod($file,0600);

        $pdo=$db->getPdo();$pdo->beginTransaction();
        try {
            foreach($changes as $item){
                if($item['fields']){
                    $db->update('packages',$item['fields'],'id=?',[$item['id']]);
                    $report['updated']++;$report['fields']+=count($item['fields']);
                }
                if($item['extra']!==$item['oldExtra']){
                    $input=[];
                    foreach(self::EXTRA as $field)$input['nvseo_'.$field]=(string)($item['extra'][$field]??'');
                    foreach(['same_as_urls','sources','reviewer_name','last_reviewed','image_title']as $field)
                        $input['nvseo_'.$field]=(string)($item['oldExtra'][$field]??'');
                    NetveraSeoBridge::save('package',$item['id'],$input);
                    $report['extras']++;
                }
            }
            $pdo->commit();
        }catch(\Throwable $e){
            if($pdo->inTransaction())$pdo->rollBack();
            throw $e;
        }
        return $report;
    }
}
