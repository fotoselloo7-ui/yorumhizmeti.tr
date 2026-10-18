<?php
/** Pure, synthetic contracts; no DB, external network, users or payment fixtures. */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
$root=dirname(__DIR__);$settings=['site_url'=>'https://netvera.example','site_name'=>'NetVera QA','seo_default_robots'=>'index,follow,max-image-preview:large'];
function setting(string $key,string $default=''):string{return (string)($GLOBALS['settings'][$key]??$default);}
function url(string $path=''):string{return 'https://netvera.example/'.ltrim($path,'/');}
function excerpt(string $text,int $length=160):string{return mb_substr($text,0,$length);}
spl_autoload_register(static function(string $class)use($root):void{if(str_starts_with($class,'App\\'))require_once $root.'/app/'.str_replace('\\','/',substr($class,4)).'.php';});
use App\Services\SeoMetadataService as Meta;
use App\Services\SeoStructuredDataService as Graph;
$n=0;$ok=static function(bool $pass,string $message)use(&$n):void{if(!$pass)throw new RuntimeException($message);$n++;};
foreach(['javascript:alert(1)','data:text/html,x','https://user:private@example.test/x',"https://example.test/\nheader"]as $bad)$ok(Meta::webUrl($bad)===null,'Unsafe canonical / source URL rejected');
$ok(Meta::webUrl('https://example.test/article#section')==='https://example.test/article','Fragments are not canonical documents');
$ok(!Meta::indexable('index,noindex,follow'),'Most restrictive indexing wins');
$ok(Meta::indexable('max-snippet:0'),'Snippet restriction is not an indexing restriction');
$ok(Meta::robots('index,follow',true)==='follow,noindex','Controller noindex cannot be overridden by saved index');
$ok(Meta::robots('none')==='noindex,nofollow','None means noindex/nofollow');
$robots=Meta::robots('',false,['max_snippet'=>'0','max_image_preview'=>'none','max_video_preview'=>'-1']);
foreach(['max-snippet:0','max-image-preview:none','max-video-preview:-1']as $rule)$ok(str_contains($robots,$rule),'Preview controls flow into the one robots tag');
$ok(Meta::preview(['max_snippet'=>['invalid'],'max_image_preview'=>'x','max_video_preview'=>'90000'])===['max_snippet'=>'','max_video_preview'=>'','max_image_preview'=>''],'Malformed optional preview values safely fall back');
foreach(['','garbage','2025-02-30','1970-00-00','2099-01-01']as $date)$ok(Meta::lastmod($date)===null,'No epoch, invalid or fictional future freshness');
$ok(Meta::lastmod('2024-02-29T10:30:00Z')==='2024-02-29','Real ISO publication update supported');
foreach(['/giris','/sepet','/odeme/basarili','/siparis/123','/destek/1','/admin']as $path){
 $d=Meta::prepare(['nvSeoData'=>['robots'=>'index,follow']],'frontend/cart','app',$path);
 $ok(!Meta::indexable($d['nvRobots'])&&!isset($d['canonicalUrl'])&&$d['seoPrivate'],'Private pages stay excluded and have no public canonical');
}
$d=Meta::prepare(['nvSeoData'=>['robots'=>'index,follow']],'frontend/404','app','/kategori/missing');
$dealer=Meta::prepare([],'frontend/account/dealer','app','/bayilik');
$ok($dealer['seoPrivate']&&!Meta::indexable($dealer['nvRobots']),'Member templates remain private even on a legacy alias');
$ok($d['seoError']&&!Meta::indexable($d['nvRobots']),'404 robots override editor index');
$d=Meta::prepare(['canonicalUrl'=>'https://example.test/authored?page=2'],'frontend/blog/index','app','/blog?page=2&utm_source=test');
$ok($d['canonicalUrl']==='https://example.test/authored?page=2'&&Meta::indexable($d['nvRobots']),'Authored canonical and real pagination preserved');
foreach(['/kategori/test?sort=price_asc','/blog?q=test','/hazir-yazilimlar?indirim=1']as $path){$d=Meta::prepare(['nvSeoData'=>['robots'=>'index,follow']],'frontend/categories','app',$path);$ok(!Meta::indexable($d['nvRobots']),'Filter variants cannot override noindex');}
$d=Meta::prepare(['ogImage'=>'/assets/img/synthetic.webp'],'frontend/page','app','/iletisim?utm_source=test');
$ok($d['canonicalUrl']==='https://netvera.example/iletisim'&&$d['ogImage']==='https://netvera.example/assets/img/synthetic.webp','Static fallback canonical / OG use absolute safe origin');
$settings['seo_default_robots']='noindex,follow';$d=Meta::prepare(['nvSeoData'=>['robots'=>'index,follow']],'frontend/page','app','/sayfa/test');$ok(Meta::indexable($d['nvRobots']),'Global default does not erase an explicit editorial choice');$settings['seo_default_robots']='index,follow,max-image-preview:large';
$page=['canonicalUrl'=>url('/example'),'pageTitle'=>'Synthetic </script> title','metaDescription'=>'Public description'];
$existing=['@context'=>'https://schema.org','@graph'=>[['@type'=>'CollectionPage','@id'=>url('/example').'#page','name'=>'Existing collection'],['@type'=>'Product','offers'=>['price'=>99,'priceCurrency'=>'TRY']]]];
$json=Graph::graph(json_encode($existing),$page,['author_name'=>'QA Editor','author_type'=>'Person','author_url'=>'https://example.test/editor','same_as_urls'=>"https://example.test/profile\njavascript:x",'sources'=>"https://example.test/source\ndata:x",'last_reviewed'=>'2024-02-29'],'frontend/blog/detail');
$graph=json_decode($json,true)['@graph'];$pages=array_values(array_filter($graph,static fn($x)=>$x['@type']==='CollectionPage'));
$ok(count($pages)===1&&count($graph)===2,'Existing page identity merged, no competing page schema');
$p=$pages[0];$ok($p['name']==='Existing collection'&&$p['publisher']['@id']==='https://netvera.example/#organization','Existing editorial name and shared publisher identity preserved');
$ok(!isset($p['sameAs'])&&count($p['author']['sameAs'])===1,'Official profile identifies author, not the page');
$ok($p['citation']===['https://example.test/source']&&$p['lastReviewed']==='2024-02-29','Only displayed blog provenance is connected');
$geo=json_decode(Graph::graph(json_encode($existing),$page,['geo_summary'=>'Editor-managed public service summary']),true)['@graph'];
$ok($geo[0]['description']==='Editor-managed public service summary'&&count($geo)===2,'Authored GEO summary survives page graph consolidation');
$ok($graph[1]['offers']['price']===99&&!isset($graph[1]['aggregateRating']),'Existing offer preserved; no fabricated review');
$ok(!str_contains($json,'</script>'),'JSON-LD is safe in an HTML script element');
$ok(Graph::graph('',array_merge($page,['seoPrivate'=>true]))===null,'Private data has no discovery graph');
foreach(['invalid','{"@graph":"bad"}','[]']as $raw){$g=json_decode(Graph::graph($raw,$page),true);$ok(count($g['@graph'])===1&&$g['@graph'][0]['@type']==='WebPage','Malformed old JSON cannot break rendering');}
$g=json_decode(Graph::graph('', $page,['sources'=>'https://example.test/private-note','last_reviewed'=>'2099-01-01'],'frontend/category-detail'),true)['@graph'][0];
$ok(!isset($g['citation'])&&!isset($g['lastReviewed'])&&!isset($g['mainEntity']),'Hidden category notes do not become fake citations, dates or FAQ claims');
$site=new App\Services\SitemapService();$method=new ReflectionMethod($site,'url');
$first=$method->invoke($site,'https://example.test/page?a=1&b=2','0.5','weekly','invalid');
$ok(str_contains($first,'&amp;')&&!str_contains($first,'lastmod'),'Sitemap XML escaping and truthful freshness');
$ok($method->invoke($site,'https://example.test/page?a=1&b=2','0.5','weekly')==='','Sitemap duplicates eliminated');
$ok($method->invoke($site,'https://example.test/hidden','0.5','weekly',null,'noindex,follow')==='','Sitemap shares robots exclusion policy');
echo "PASS $n SEO/GEO/AIO metadata, indexing, privacy, graph, provenance and sitemap contracts\n";
