<?php
/** No DB or fake ranking claims. Test actual original Netvera editorial content. */
declare(strict_types=1);
require __DIR__.'/../app/Services/BlogQualityScoreService.php';
use App\Services\BlogQualityScoreService;
$file=__DIR__.'/../database/netvera-public-catalog.json';
$data=json_decode((string)file_get_contents($file),true,512,JSON_THROW_ON_ERROR);
if(count($data['blog_posts']??[])!==5)throw new RuntimeException('Original articles unavailable');
foreach($data['blog_posts'] as $r){
    $seo=['main_question'=>'Hangi özellikler gerekli?',
          'direct_answer'=>'Bu araç; kullanıcıya doğrulanabilir, açık ve doğru bilgi sunan bir yazılım sistemidir.',
          'geo_summary'=>'NetVera yazılım ve içerik kalitesi doğrulanabilir bilgilerle hazırlanır.',
          'sources'=>'https://example.org/research','entity_topics'=>'NetVera, yazılım',
          'author_name'=>'NetVera','content_intent'=>'bilgi'];
    $post=['title'=>$r['title'],'slug'=>$r['slug'],'seo_title'=>$r['seo_title'],
        'seo_description'=>$r['seo_description'],'seo_focus_keyword'=>$r['focus_keyword'],
        'raw_content'=>$r['content'],'excerpt'=>$r['excerpt'],
        'image_alt'=>$r['title'],'blog_category_id'=>1];
    $result=BlogQualityScoreService::evaluate($post,$seo);
    foreach(['overall','seo','geo','content'] as $key){
        $value=$result['scores'][$key]??-1;
        if(!is_int($value)||$value<0||$value>100)
            throw new RuntimeException('Invalid score '.$key.' in '.$r['slug']);
    }
    if($result['words']<400)throw new RuntimeException('Unicode word counter lost Turkish article: '.$r['slug']);
}
$empty=BlogQualityScoreService::evaluate(['title'=>'Deneme','raw_content'=>'Kısa metin','slug'=>'deneme'],[]);
if($empty['scores']['overall']>=70)throw new RuntimeException('Low-quality draft incorrectly scores high');
$good=BlogQualityScoreService::evaluate([
    'title'=>'PHP Haber Sitesi Scripti Rehberi','slug'=>'php-haber-sitesi-scripti-rehberi',
    'seo_title'=>'PHP Haber Sitesi Scripti Özellikleri ve Kurulum Rehberi',
    'seo_description'=>str_repeat('Kapsamlı rehber ve güvenilir bilgiler. ',4),
    'seo_focus_keyword'=>'PHP Haber Sitesi Scripti',
    'raw_content'=>"PHP Haber Sitesi Scripti hakkında bilgi.\n\n## Rehber\n\n### Özellikler\n- Birinci adım\n- İkinci adım",
    'blog_category_id'=>1,'image_alt'=>'PHP haber sitesi arayüz ekranı',
    'excerpt'=>str_repeat('Web yazılım içerik özeti. ',5),
],[
    'main_question'=>'PHP haber sitesi nedir?',
    'direct_answer'=>str_repeat('Bu bilgi doğrulanabilir bir yanıttır. ',3),
    'geo_summary'=>str_repeat('Bu açıklama doğrulanmış veriye dayanır. ',2),
    'entity_topics'=>'PHP, NetVera','author_name'=>'Editoryal Ekip',
    'sources'=>'https://example.org', 'content_intent'=>'bilgi'
]);
if($good['scores']['geo']<= $empty['scores']['geo'])
    throw new RuntimeException('Enriched GEO data did not improve its own quality score');
echo "PASS: 5 original Turkish articles, bounded SEO/GEO/content scores, no fake high score on empty draft.\n";
