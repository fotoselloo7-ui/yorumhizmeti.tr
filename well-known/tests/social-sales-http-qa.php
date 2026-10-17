<?php
/** In-memory synthetic HTTP session against the dedicated QA router only. */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
$origin='http://127.0.0.1:8018';
$ch=curl_init();curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_MAXREDIRS=>4,CURLOPT_TIMEOUT=>10,CURLOPT_COOKIEFILE=>'']);
$request=static function(string $path,?array $post=null)use($ch,$origin):array{
 curl_setopt_array($ch,[CURLOPT_URL=>$origin.$path,CURLOPT_POST=>$post!==null]);
 if($post!==null)curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($post));
 $html=curl_exec($ch);if($html===false)throw new RuntimeException('Local QA HTTP request failed');
 return [(int)curl_getinfo($ch,CURLINFO_HTTP_CODE),(string)$html,(string)curl_getinfo($ch,CURLINFO_EFFECTIVE_URL)];
};
$assert=static function(bool $ok,string $why):void{if(!$ok)throw new RuntimeException($why);};
[$code,$catalog]=$request('/kategori/instagram-hizmetleri');
$assert($code===200 && str_contains($catalog,'class="nv-social-service-card"'),'Platform lists service types, not mixed quantity packages');
preg_match_all('~<script type="application/ld\+json">(.*?)</script>~s',$catalog,$schemas);
$collection=null;
foreach($schemas[1] as $json){foreach((json_decode($json,true)['@graph']??[]) as $node){if(($node['@type']??'')==='CollectionPage')$collection=$node;}}
$assert(($collection['mainEntity']['itemListElement'][0]['name']??'')==='Instagram Türk Takipçi','Schema names match visible service hierarchy');
[$code,$selection]=$request('/kategori/instagram-takipci?hizmet=14-instagram-turk-takipci');
$assert($code===200 && str_contains($selection,'100 takipçi') && str_contains($selection,'500 takipçi'),'Service selection keeps both fixed quantities');
$assert(str_contains($selection,'content="noindex,follow"') && str_contains($selection,'rel="canonical" href="'.$origin.'/kategori/instagram-takipci"'),'Query filter remains non-indexed with original canonical URL');
[$code,$html]=$request('/paket/instagram-turk-takipci-baslangic-paketi-100-takipci');
$assert($code===200,'QA package available');
preg_match('/name="_csrf_token" value="([^"]+)"/',$html,$match);$token=$match[1]??'';
$assert($token!=='','QA form has CSRF token');
[$code]=$request('/sepet/ekle',['package_id'=>463,'quantity'=>999,'field_463_smm_target'=>'@synthetic.qa']);
$assert($code===403,'Missing CSRF rejected');
$post=['_csrf_token'=>$token,'package_id'=>463,'quantity'=>999,'field_463_smm_target'=>'@synthetic.qa'];
[$code,$html]=$request('/sepet/ekle',$post);
$assert($code===200 && substr_count($html,'class="yv-cart-row-v5"')===1 && str_contains($html,'100 takipçi') && str_contains($html,'64,90'),'Tampered quantity remains one 100-follower package');
[$code,$html]=$request('/sepet/ekle',$post);
$assert(substr_count($html,'class="yv-cart-row-v5"')===1,'Repeated same target does not multiply package');
$post['field_463_smm_target']='@second.synthetic.qa';
[$code,$html]=$request('/sepet/ekle',$post);
$assert(substr_count($html,'class="yv-cart-row-v5"')===2 && str_contains($html,'129,80'),'Different targets retain separate fixed package lines');
[$code,$html]=$request('/sepet/guncelle',['_csrf_token'=>$token,'key'=>0,'quantity'=>99999]);
$assert(str_contains($html,'129,80'),'Cart update cannot change the advertised social amount');
$post['checkout']=1;
[$code,$html,$url]=$request('/sepet/ekle',$post);
$assert(str_ends_with($url,'/giris'),'Buy Now follows checkout authentication, not an unconditional JS redirect');
curl_close($ch);
echo "PASS: actual HTTP CSRF, tampered add/update quantities, duplicate target, distinct targets, exact 64.90/129.80 totals and native Buy Now redirect; no checkout submission\n";
