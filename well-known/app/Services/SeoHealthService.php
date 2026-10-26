<?php
namespace App\Services;

use App\Core\Database;

/** Authenticated, read-only diagnostics. No crawling, DDL, secrets or ranking score. */
final class SeoHealthService
{
    public static function report(): array
    {
        $checks=[];
        $add=static function(string $label,bool $ok,string $detail)use(&$checks):void{$checks[]=['label'=>$label,'status'=>$ok?'pass':'warn','detail'=>$detail];};
        $site=SeoMetadataService::webUrl(setting('site_url'));
        $app=SeoMetadataService::webUrl(url('/'));
        $add('Kanonik alan adı',$site!==null&&$app!==null&&rtrim($site,'/')===rtrim($app,'/'),
            'Site URL: '.($site??'Geçersiz').' · Uygulama URL: '.($app??'Geçersiz').'. Yerel QA ortamında alan adı farkı beklenebilir; canlı ortamda eşleşmelidir.');
        $robots=SeoMetadataService::robots(setting('seo_default_robots'));
        $add('Varsayılan indeksleme',SeoMetadataService::indexable($robots),'Varsayılan: '.$robots.'. İçerik bazlı özel tercihler korunur.');
        $add('Kurumsal kimlik',trim(setting('site_name'))!==''&&trim(setting('seo_org_description'))!=='','Kurum adı / tanımı ve resmî profiller mevcut site ayarlarından alınır; sahte kişi, puan veya adres üretilmez.');
        try{
            $xml=(new SitemapService())->generate();$doc=new \DOMDocument();
            $previous=libxml_use_internal_errors(true);$loaded=$doc->loadXML($xml);libxml_clear_errors();libxml_use_internal_errors($previous);
            $locs=[];foreach($doc->getElementsByTagName('loc')as $loc)$locs[]=$loc->textContent;
            $add('Sitemap doğruluğu',$loaded&&count($locs)>0&&count($locs)===count(array_unique($locs)),count($locs).' benzersiz tercih edilen URL; noindex ve yayın durumu kuralları uygulanır.');
            // No external crawl or arbitrary requests: inspect our generated XML only.
            $expectedOrigin=$site?strtolower((string)parse_url($site,PHP_URL_SCHEME)).'://'.strtolower((string)parse_url($site,PHP_URL_HOST)):'';
            $wrongOrigin=0;$fragmentUrls=0;$insecureUrls=0;
            foreach($locs as $loc){
                $origin=strtolower((string)parse_url($loc,PHP_URL_SCHEME)).'://'.strtolower((string)parse_url($loc,PHP_URL_HOST));
                if($expectedOrigin!==''&&$origin!==$expectedOrigin)$wrongOrigin++;
                if(parse_url($loc,PHP_URL_FRAGMENT)!==null)$fragmentUrls++;
                if(strtolower((string)parse_url($loc,PHP_URL_SCHEME))!=='https')$insecureUrls++;
            }
            $add('Sitemap kanonik alan adı',$wrongOrigin===0&&$fragmentUrls===0,
                $wrongOrigin.' farklı alan adı; '.$fragmentUrls.' URL parçası. Sitemap kanonik sayfa adresleriyle tutarlı olmalı.');
            $add('HTTPS sitemap adresleri',$insecureUrls===0,
                $insecureUrls.' HTTP adresi. Canlı yayında SSL ve kalıcı HTTPS yönlendirmesi kontrol edilmelidir.');
            $add('Sitemap kapasitesi',count($locs)<=50000&&strlen($xml)<=52428800,number_format(strlen($xml)).' bayt; protokol sınırı 50.000 URL / 50 MB. Sınıra yaklaşırsa sitemap index bölümlendirmesi gerekir.');
        }catch(\Throwable $e){$add('Sitemap üretimi',false,'Sitemap üretilemedi; public katalog / şema kurulumunu kontrol edin. Özel hata veya bağlantı bilgileri burada gösterilmez.');}
        try{
            $rows=Database::getInstance()->fetchAll('SELECT entity_type,entity_id,extra_json FROM nv_editorial_seo');
            $invalid=0;$restricted=0;
            foreach($rows as $row){$data=json_decode((string)$row['extra_json'],true);if(!is_array($data)){$invalid++;continue;}
                if(!SeoMetadataService::indexable((string)($data['robots']??''))||($data['max_snippet']??'')==='0'||($data['max_image_preview']??'')==='none')$restricted++;
            }
            $add('Editoryal metadata',$invalid===0,count($rows).' kayıt; '.$invalid.' geçersiz JSON. '.$restricted.' içerikte bilinçli indeksleme / önizleme kısıtı var.');
        }catch(\Throwable $e){$add('Editoryal metadata',false,'Gelişmiş SEO tablosu okunamadı. Temiz kurulum için kanonik database.sql kullanılmalıdır.');}
        // Validate our shared, sitewide graph builder without relying on a live crawler.
        try{
            $public=['canonicalUrl'=>$site?:'https://netvera.tr/','pageTitle'=>'NetVera','metaDescription'=>'Kurumsal dijital hizmetler'];
            $graph=SeoStructuredDataService::graph(null,$public);
            $decoded=json_decode((string)$graph,true);
            $nodes=$decoded['@graph']??[];
            $valid=is_array($nodes)&&count($nodes)===1&&($nodes[0]['@type']??null)==='WebPage'&&
                ($nodes[0]['publisher']['@id']??'')!=='';
            $add('Site geneli JSON-LD',$valid,'Ortak şema üretimi ve kurumsal publisher referansı denetlendi; yalnız doğrulanabilir sayfa bilgileri kullanılmalı.');
            $blocked=SeoStructuredDataService::graph(null,$public+['seoNoindex'=>true])===null;
            $add('Noindex şema koruması',$blocked,'Özel, arama filtresi ve yönetici önizleme adreslerinde keşif grafiği yayınlanmaz.');
        }catch(\Throwable $e){$add('Site geneli JSON-LD',false,'Ortak yapılandırılmış veri servisi kontrol edilemedi.');}
        $add('GEO / AIO editoryal hazırlık',true,'Yazar, kaynak, özgün yanıt ve son kontrol alanları desteklenir; Google AI için ek dosya ya da sıralama garantisi yoktur. Şehir sayfalarının birbirinden anlamlı biçimde farklı olması editörün sorumluluğundadır.');
        return $checks;
    }
}
