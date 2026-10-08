<?php
namespace App\Services;

/**
 * Editorial readiness checks, not a Google ranking prediction or an AI-citation
 * guarantee. Data used is the actual draft and saved metadata; no external API.
 */
final class BlogQualityScoreService
{
    public static function evaluate(array $post,array $seo=[]):array
    {
        $raw=trim((string)($post['raw_content']??$post['content']??''));
        $text=trim(preg_replace('/\s+/u',' ',strip_tags(preg_replace('/!\[[^\]]*\]\([^)]+\)/u',' ',$raw))));
        $words=preg_split('/\s+/u',$text,-1,PREG_SPLIT_NO_EMPTY)?:[];
        $wc=count($words);
        $title=trim((string)($post['title']??''));
        $slug=trim((string)($post['slug']??''));
        $seoTitle=trim((string)($post['seo_title']??''));
        $desc=trim((string)($post['seo_description']??''));
        $focus=trim((string)($post['seo_focus_keyword']??''));
        $excerpt=trim((string)($post['excerpt']??''));
        $hasH2=preg_match('/(?:^|\n)\s*#{2}\s+\S|<h2\b/i',$raw)===1;
        $hasH3=preg_match('/(?:^|\n)\s*#{3}\s+\S|<h3\b/i',$raw)===1;
        $hasImage=preg_match('/!\[[^\]]+\]\([^)]+\)|<img\b[^>]*\balt\s*=\s*["\x27][^"\x27]+/iu',$raw)===1
            || trim((string)($post['image_alt']??''))!=='';
        $hasInternal=preg_match('~(?:\[[^\]]+\]\((?:/|https?://(?:www\.)?netvera\.tr/)[^)]+\)|href=["\x27]/)~i',$raw)===1;
        $hasExternal=preg_match('~\[[^\]]+\]\(https?://(?!www\.netvera\.tr|netvera\.tr)[^)]+\)~i',$raw)===1;
        $hasList=preg_match('/(?:^|\n)\s*(?:[-*+]|\d+\.)\s+\S/m',$raw)===1;
        $faqs=$post['faqs']??[];
        if(is_string($faqs))$faqs=json_decode($faqs,true)?:[];
        if(!is_array($faqs))$faqs=[];
        $faqCount=count(array_filter($faqs,static fn($faq)=>is_array($faq)
            && trim((string)($faq['question']??''))!==''
            && trim((string)($faq['answer']??''))!==''));
        $focusLower=mb_strtolower($focus,'UTF-8');
        $intro=mb_substr($text,0,450,'UTF-8');
        $keywordInTitle=$focus!==''&&mb_stripos($seoTitle?:$title,$focus,0,'UTF-8')!==false;
        $keywordInIntro=$focus!==''&&mb_stripos($intro,$focus,0,'UTF-8')!==false;
        $keywordMentions=$focus!==''?mb_substr_count(mb_strtolower($text,'UTF-8'),$focusLower):0;
        $keywordStuffing=$wc>0 && $keywordMentions>max(7,ceil($wc/100)*4);
        $checks=[
            'seo'=>[
                ['SEO başlığı 35–65 karakter',mb_strlen($seoTitle,'UTF-8')>=35&&mb_strlen($seoTitle,'UTF-8')<=65,12],
                ['Meta açıklaması 110–165 karakter',mb_strlen($desc,'UTF-8')>=110&&mb_strlen($desc,'UTF-8')<=165,12],
                ['Odak anahtar kelime belirlendi',$focus!=='',12],
                ['Odak kelime başlıkta doğal kullanıldı',$keywordInTitle,10],
                ['Kısa ve anlamlı URL slug',$slug!==''&&strlen($slug)<=100,10],
                ['Makale kategorisi seçildi',(int)($post['blog_category_id']??0)>0,10],
                ['H2 alt başlık mevcut',$hasH2,10],
                ['Görsel ALT veya görsel açıklaması',$hasImage,10],
                ['Canonical veya sistemin varsayılan canonical adresi',$slug!==''||trim((string)($post['canonical_url']??''))!=='',7],
                ['OG başlığı, açıklaması veya otomatik meta yedeği',$seoTitle!==''&&$desc!=='',7],
            ],
            'geo'=>[
                ['Ziyaretçinin ana sorusu yazıldı',trim((string)($seo['main_question']??''))!=='',17],
                ['Doğrudan cevap / featured snippet özeti',mb_strlen(trim((string)($seo['direct_answer']??'')),'UTF-8')>=40,18],
                ['Doğrulanabilir GEO içerik özeti',mb_strlen(trim((string)($seo['geo_summary']??'')),'UTF-8')>=40,13],
                ['İlgili kişi/ürün/konu varlıkları belirtildi',trim((string)($seo['entity_topics']??''))!=='',10],
                ['Kaynaklar ve referans bağlantıları',trim((string)($seo['sources']??''))!=='',14],
                ['Yazar / kurum açıkça belirtildi',trim((string)($seo['author_name']??''))!=='',10],
                ['Makale niyeti tanımlandı',trim((string)($seo['content_intent']??''))!=='',8],
                ['Görünür SSS içeriği oluşturuldu',$faqCount>=1,10],
            ],
            'content'=>[
                ['En az 600 kelime özgün kapsam',$wc>=600,21],
                ['En az 1200 kelime derinlik',$wc>=1200,10],
                ['H2/H3 başlık hiyerarşisi',$hasH2&&$hasH3,13],
                ['Anlamlı liste / adımlar',$hasList,10],
                ['İç bağlantı mevcut',$hasInternal,12],
                ['Dış kaynak bağlantısı mevcut',$hasExternal,10],
                ['Odak kelime ilk paragrafta',$keywordInIntro,9],
                ['Özet veya açıklama yeterli',mb_strlen($excerpt,'UTF-8')>=80,7],
                ['Anahtar kelime tekrarına aşırı yüklenilmedi',!$keywordStuffing,8],
            ]
        ];
        $scores=[];
        foreach($checks as $facet=>$entries){
            $earned=0;$possible=0;
            foreach($entries as $entry){$possible+=$entry[2];if($entry[1])$earned+=$entry[2];}
            $scores[$facet]=(int)round(($possible>0?$earned/$possible:0)*100);
        }
        $scores['overall']=(int)round($scores['seo']*.4+$scores['geo']*.3+$scores['content']*.3);
        return ['scores'=>$scores,'checks'=>$checks,'words'=>$wc,
            'note'=>'Puanlar editoryal hazırlık göstergesidir; arama sıralaması veya AI kaynak gösterimi garantisi değildir.'];
    }
}
