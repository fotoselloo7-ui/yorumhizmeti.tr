<?php
namespace App\Services;

use App\Core\Database;

/**
 * Complete curated field suggestions for existing and newly created SERVICE categories.
 * Never touches the legacy NetVera software catalog, manually edited copy or URL slugs.
 * Optional provenance fields are intentionally blank unless independently verified.
 */
final class CategorySeoCompleteService
{
    private const EXTRA_FIELDS = [
        'secondary_keywords','robots','geo_summary','entity_topics','service_area',
        'author_name','author_type','author_url','same_as_urls','content_intent',
        'main_question','direct_answer','sources','reviewer_name','last_reviewed','image_title'
    ];

    private static function software(array $category): bool
    {
        $text=mb_strtolower((string)($category['slug']??'').' '.(string)($category['name']??''),'UTF-8');
        return (bool)preg_match('/haz[ıi]r[- ]?(yaz[ıi]l[ıi]m|script)|sekt[oö]rel[- ]?(y[oö]netim[- ]?)?yaz[ıi]l[ıi]m|yaz[ıi]l[ıi]m|yazilim|software|cms|script|web[- ]?site|wordpress|masa[uü]st[uü][- ]?bot/iu',$text);
    }

    public static function recommendations(array $category): array
    {
        if(self::software($category))return [];
        $name=trim((string)($category['name']??''));
        $slug=trim((string)($category['slug']??''));
        if($name==='')return [];
        $profile=CategorySearchBlueprint::profileForCategory($category);
        // A fallback for real services not yet included in the researched topical map;
        // it is based on the actual category, NEVER creates a new thin SEO URL.
        $commercial=(bool)preg_match('/takipçi|takipci|beğeni|begeni|izlenme|görüntülenme|goruntulenme|abone|satın al|satin al/iu',$name.' '.$slug);
        $title=$profile['title']??($name.' | NetVera Teknoloji Yazılım');
        $defaultDescription=$name.' için NetVera Teknoloji Yazılım hizmet seçeneklerini, paket kapsamlarını, teslimat ve sipariş koşullarını karşılaştırın. Size uygun çözümü inceleyin.';
        $description=$profile['desc']??$defaultDescription;
        $focus=$profile['focus']??mb_strtolower($name,'UTF-8');
        $secondary=$profile['secondary']??($focus.' paketleri, '.$focus.' hizmetleri');
        $question=$profile['question']??($name.' seçerken nelere dikkat etmeliyim?');
        $answer=$profile['answer']??('Bu kategoride yer alan '.mb_strtolower($name,'UTF-8').' hizmetlerini kapsam, ihtiyaç duyulan hedef bilgileri, teslimat ve satın alma koşullarına göre karşılaştırabilirsiniz. Paket detaylarını sipariş vermeden önce kontrol edin.');
        $topics=array_filter(array_unique([$name,$focus,'NetVera Teknoloji Yazılım']));
        $canonical=$slug!==''?'https://netvera.tr/kategori/'.rawurlencode($slug):'';
        $imageExists=trim((string)($category['image']??''))!=='';
        $extras=[
            'secondary_keywords'=>$secondary,
            'robots'=>($category['status']??'active')==='inactive'?'noindex,follow':'index,follow,max-image-preview:large',
            'geo_summary'=>$description,
            'entity_topics'=>implode(', ',$topics),
            'service_area'=>'Türkiye',
            'author_name'=>'NetVera Teknoloji Yazılım',
            'author_type'=>'Organization',
            'author_url'=>'https://netvera.tr',
            'same_as_urls'=>'', // Verified official profile URLs ONLY.
            'content_intent'=>$commercial?'satin-alma':'karsilastirma',
            'main_question'=>$question,
            'direct_answer'=>$answer,
            'sources'=>'', // Never invent citations for generic sales claims.
            'reviewer_name'=>'', // Actual human editor must sign off.
            'last_reviewed'=>'', // Not a fictional publication/review date.
            'image_title'=>$imageExists?($name.' | NetVera Teknoloji Yazılım'):'',
        ];
        return [
            'name'=>$name,'slug'=>$slug,
            'description'=> $profile
               ? mb_substr($profile['desc'],0,300,'UTF-8')
               : ($category['description']??$defaultDescription),
            'image_alt'=>$imageExists?($name.' kategorisi hizmet görseli | NetVera'):'',
            'seo_title'=>$title,
            'seo_description'=>$description,
            'seo_focus_keyword'=>$focus,
            'canonical_url'=>$canonical,
            'og_title'=>$title,
            'og_description'=>$description,
            'extra'=>$extras,
        ];
    }

    public static function isGeneric(string $field,string $value,array $category):bool
    {
        $v=trim($value);
        if($v==='')return true;
        if(preg_match('/YorumHizmeti(?:\.tr)?|Yorum Hizmeti/iu',$v))return true;
        if($field==='seo_title' || $field==='og_title'){
            $name=trim((string)($category['name']??''));
            return $name!=='' && in_array(mb_strtolower($v,'UTF-8'),[
                mb_strtolower($name.' - NetVera','UTF-8'),
                mb_strtolower($name.' | NetVera','UTF-8'),
                mb_strtolower($name,'UTF-8')
            ],true);
        }
        if(in_array($field,['seo_description','og_description'],true)){
            return mb_strlen($v,'UTF-8')<85;
        }
        return false;
    }

    public static function fillForForm(array $category,array $existingSeo=[]):array
    {
        $suggest=self::recommendations($category);
        if(!$suggest)return ['category'=>$category,'seo'=>$existingSeo,'suggestion'=>[]];
        foreach(['description','image_alt','seo_title','seo_description','seo_focus_keyword','canonical_url','og_title','og_description'] as $key){
            $current=(string)($category[$key]??'');
            // Existing category prose is meaningful, so retain it unless completely blank.
            $replace=($key==='description'||$key==='image_alt'||$key==='canonical_url')
                ?trim($current)===''
                :self::isGeneric($key,$current,$category);
            if($replace && $suggest[$key]!=='')$category[$key]=$suggest[$key];
        }
        foreach($suggest['extra'] as $key=>$value){
            if(trim((string)($existingSeo[$key]??''))==='' && $value!=='')$existingSeo[$key]=$value;
        }
        return ['category'=>$category,'seo'=>$existingSeo,'suggestion'=>$suggest];
    }

    /** Admin-only, idempotent, field-preserving persistence. */
    public static function apply():array
    {
        $db=Database::getInstance();
        $rows=$db->fetchAll("SELECT * FROM categories ORDER BY id ASC");
        $counts=['examined'=>0,'categories'=>0,'fields'=>0,'seo_profiles'=>0,'skipped_software'=>0];
        foreach($rows as $row){
            $proposal=self::recommendations($row);
            if(!$proposal){$counts['skipped_software']++;continue;}
            $counts['examined']++;
            $oldSeo=NetveraSeoBridge::get('category',(int)$row['id']);
            $candidate=self::fillForForm($row,$oldSeo);
            $changes=[];
            foreach(['description','image_alt','seo_title','seo_description','seo_focus_keyword',
                      'canonical_url','og_title','og_description'] as $field){
                if(($candidate['category'][$field]??'')!==($row[$field]??'')){
                    $changes[$field]=$candidate['category'][$field];
                }
            }
            if($changes){
                $db->update('categories',$changes,'id=?',[(int)$row['id']]);
                $counts['categories']++;
                $counts['fields']+=count($changes);
            }
            $existingValues=$oldSeo;
            $newValues=$candidate['seo'];
            if($newValues!==$existingValues){
                $input=[];
                foreach(self::EXTRA_FIELDS as $field){
                    $input['nvseo_'.$field]=(string)($newValues[$field]??'');
                }
                NetveraSeoBridge::save('category',(int)$row['id'],$input);
                $counts['seo_profiles']++;
            }
        }
        return $counts;
    }
}
