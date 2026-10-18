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
        $add('GEO / AIO veri modeli',true,'Mevcut yazı, kaynak, yazar ve son kontrol alanları kullanılmaktadır. Schema / AI dosyası tek başına görünürlük veya alıntı garantisi vermez.');
        return $checks;
    }
}
