<?php
/** GET-only audit of the existing isolated 8017 QA server. Never visits a remote sitemap URL. */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
$origin='http://127.0.0.1:8017';$count=0;$crawled=0;
$ok=static function(bool $pass,string $why)use(&$count):void{if(!$pass)throw new RuntimeException($why);$count++;};
$get=static function(string $path)use($origin):array{
 if(!str_starts_with($path,'/')||str_starts_with($path,'//'))throw new RuntimeException('Local path required');
 $headers=[];$ch=curl_init($origin.$path);curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>15,CURLOPT_FOLLOWLOCATION=>false,
 CURLOPT_HEADERFUNCTION=>static function($ch,$line)use(&$headers){$headers[]=trim($line);return strlen($line);}]);
 $html=curl_exec($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);if($html===false)throw new RuntimeException('Local GET failed');return [$code,$html,$headers];
};
$xpath=static function(string $html):DOMXPath{$d=new DOMDocument();$old=libxml_use_internal_errors(true);$d->loadHTML('<?xml encoding="UTF-8">'.$html);libxml_clear_errors();libxml_use_internal_errors($old);return new DOMXPath($d);};
foreach(['/giris','/kayit','/sepet','/admin/giris']as $path){
 [$code,$html,$headers]=$get($path);$x=$xpath($html);$robots=$x->evaluate('string(//meta[@name="robots"]/@content)');
 $ok($code===200&&str_contains($robots,'noindex'),'Private template excluded: '.$path);
 $ok((bool)array_filter($headers,static fn($h)=>stripos($h,'X-Robots-Tag: noindex')===0),'Private HTTP robots header: '.$path);
 $ok($x->query('//script[@type="application/ld+json"]')->length===0,'No public graph on private template: '.$path);
}
foreach(['/kategori/nv-seo-qa-not-found','/paket/nv-seo-qa-not-found','/sayfa/nv-seo-qa-not-found','/blog/nv-seo-qa-not-found','/hazir-scriptler/nv-seo-qa-not-found']as $path){
 [$code,$html]=$get($path);$x=$xpath($html);
 $ok($code===404,'Real 404, not soft 404: '.$path);
 $ok(str_contains($x->evaluate('string(//meta[@name="robots"]/@content)'),'noindex'),'Missing page excluded: '.$path);
 $ok($x->query('//link[@rel="canonical"]')->length===0,'Missing page has no preferred URL: '.$path);
}
foreach(['/kategori/instagram-takipci?sort=price_asc','/blog?q=seo','/hazir-scriptler?min_price=1']as $path){
 [$code,$html]=$get($path);$x=$xpath($html);$robots=$x->evaluate('string(//meta[@name="robots"]/@content)');
 $ok($code===200&&str_contains($robots,'noindex')&&!preg_match('/(?:^|,)index(?:,|$)/',$robots),'Filtered pages cannot re-enable indexing: '.$path);
}
[$code,$xml]=$get('/sitemap.xml');$doc=new DOMDocument();$ok($code===200&&$doc->loadXML($xml),'Sitemap returns valid XML');
$paths=[];foreach($doc->getElementsByTagName('loc')as $loc){$p=parse_url($loc->textContent);$ok(in_array($p['scheme']??'',['http','https'],true)&&empty($p['query']),'Sitemap contains absolute preferred URLs only');$paths[]=$p['path']??'/';}
$ok(count($paths)===count(array_unique($paths)),'No sitemap duplicates');
foreach($doc->getElementsByTagName('lastmod')as $lastmod)$ok(preg_match('/^\d{4}-\d{2}-\d{2}$/D',$lastmod->textContent)&&$lastmod->textContent<=date('Y-m-d')&&$lastmod->textContent!=='1970-01-01','Truthful lastmod');
foreach($paths as $path){
 [$code,$html]=$get($path);$ok($code===200,'Sitemap URL is live, not redirect / soft-404: '.$path);$x=$xpath($html);
 $robots=$x->evaluate('string(//meta[@name="robots"]/@content)');$ok(!str_contains($robots,'noindex'),'Sitemap does not contradict rendered robots: '.$path);
 $canonical=$x->evaluate('string(//link[@rel="canonical"]/@href)');$ok(parse_url($canonical,PHP_URL_PATH)===$path,'Sitemap agrees with canonical path: '.$path);
 $scripts=$x->query('//script[@type="application/ld+json"]');$ok($scripts->length===1,'One connected JSON-LD graph: '.$path);
 $graph=json_decode($scripts->item(0)->textContent,true);$ok(is_array($graph['@graph']??null),'Valid JSON-LD graph: '.$path);
 $pages=array_filter($graph['@graph'],static fn($n)=>(bool)array_intersect((array)($n['@type']??[]),['WebPage','CollectionPage','ContactPage','AboutPage','FAQPage']));
 $ok(count($pages)>=1,'Page semantics present: '.$path);$crawled++;
 foreach($graph['@graph']as $entity){
  if(($entity['@type']??'')==='Product'&&str_starts_with($path,'/paket/')){
   $offer=$entity['offers']??[];$price=number_format((float)($offer['price']??0),2,',','.');
   $visible=$x->evaluate('string(//div[contains(@class,"yv-product-price-v5") or contains(@class,"social-buy-price")]/strong)');
   $ok(str_contains($visible,$price),'Product schema price matches the actual displayed price: '.$path);
   $closed=$x->query('//form[@id="packageForm"]//button[@type="submit"][@disabled]')->length>0;
   $ok(($offer['availability']??'')===($closed?'https://schema.org/OutOfStock':'https://schema.org/InStock'),'Product availability matches the public purchase gate: '.$path);
  }
  if(($entity['@type']??'')==='SoftwareApplication'){
   $buy=$x->query('//a[contains(concat(" ",normalize-space(@class)," ")," nv40-cta-buy ")]')->length>0;
   $ok(isset($entity['offers'])===$buy,'Software offer exists only when the current public purchase CTA exists: '.$path);
  }
 }
}
[$code,$html]=$get('/?utm_source=synthetic');$x=$xpath($html);$ok($x->evaluate('string(//link[@rel="canonical"]/@href)')===$origin.'/','Tracking does not create a new canonical');
$graph=json_decode($x->query('//script[@type="application/ld+json"]')->item(0)->textContent,true)['@graph'];
foreach($graph as $node)if(in_array($node['@type']??'',['Organization','ProfessionalService'],true))$ok(str_contains($node['logo']??'','netvera-logo-current.svg'),'Publisher uses current admin-managed logo, not retired logo.png');
echo "PASS $count SEO HTTP / sitemap / schema contracts; $crawled sitemap URLs crawled through localhost only\n";
