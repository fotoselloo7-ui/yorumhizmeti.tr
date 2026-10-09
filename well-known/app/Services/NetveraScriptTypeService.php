<?php
namespace App\Services;

/**
 * Presentation-only catalogue facets for the ACTUAL imported NetVera products.
 * Never inserts duplicate products/categories, alters original slugs or invents stock.
 */
final class NetveraScriptTypeService
{
    private const MATCHERS = [
        'haber-sitesi-scripti'=>'haber|news|gazete',
        'blog-cms-scripti'=>'blog|cms|içerik yönetimi|icerik yonetimi',
        'kurumsal-site-scripti'=>'kurumsal|firma web|şirket site|sirket site',
        'emlak-sitesi-scripti'=>'emlak|gayrimenkul',
        'oto-galeri-scripti'=>'oto galeri|otogaleri|araç ilan|arac ilan',
        'e-ticaret-scripti'=>'e.?ticaret|eticaret|online mağaza|online magaza',
        'pazaryeri-ilan-scripti'=>'pazaryeri|pazar yeri|ilan portal',
        'otel-rezervasyon-scripti'=>'otel|hotel|konaklama|rezervasyon',
        'randevu-rezervasyon-scripti'=>'randevu|rezervasyon',
        'restoran-qr-menu-scripti'=>'restoran|qr menü|qr menu',
        'guzellik-salonu-scripti'=>'güzellik|guzellik|kuaför|kuafor|salon',
        'temizlik-firmasi-scripti'=>'temizlik',
        'nakliye-lojistik-scripti'=>'nakliye|lojistik|taşıma|tasima',
        'insaat-hafriyat-scripti'=>'inşaat|insaat|hafriyat|müteahhit|muteahhit',
        'crm-musteri-yonetimi-scripti'=>'crm|müşteri yönetimi|musteri yonetimi',
        'smm-panel-scripti'=>'smm|takipçi panel|takipci panel',
        'sms-onay-scripti'=>'sms|onay',
        'egitim-kurs-scripti'=>'eğitim|egitim|kurs|okul',
        'forum-topluluk-scripti'=>'forum|topluluk',
        'is-ilani-kariyer-scripti'=>'iş ilan|is ilan|kariyer|istihdam',
        'tur-seyahat-scripti'=>'tur|seyahat|acente',
        'dijital-urun-satis-scripti'=>'dijital ürün|dijital urun|lisans satış|lisans satis',
        'teknik-servis-scripti'=>'teknik servis|arıza|ariza',
        'wordpress-temalari'=>'wordpress.+tema|tema.+wordpress',
        'wordpress-eklentileri'=>'wordpress.+eklenti|eklenti.+wordpress',
        'woocommerce-eklentileri'=>'woocommerce',
        'shopify-temalari'=>'shopify.+tema|tema.+shopify',
        'shopify-uygulamalari'=>'shopify',
        'html-tailwind-sablonlari'=>'tailwind|html şablon|html sablon',
        'laravel-admin-panel-sablonlari'=>'laravel|admin panel şablon|admin panel sablon',
        'saas-abonelik-scripti'=>'saas|abonelik',
        'masaustu-bot-otomasyon'=>'masaüstü|masaustu|bot|otomasyon',
        'api-entegrasyon-modulleri'=>'api|entegrasyon',
        'mobil-uygulama-sablonlari'=>'mobil uygulama|flutter|android|ios',
    ];

    public static function definitions():array
    {
        return SoftwareCatalogService::definitions();
    }

    public static function valid(string $slug):bool
    {
        foreach(self::definitions() as $row)if($row[1]===$slug)return true;
        return false;
    }

    public static function matches(array $product,string $slug):bool
    {
        if(!isset(self::MATCHERS[$slug]))return false;
        $subject=mb_strtolower(implode(' ',[
           (string)($product['name']??''),(string)($product['slug']??''),
           (string)($product['category_name']??'')
        ]),'UTF-8');
        return preg_match('~(?:'.self::MATCHERS[$slug].')~iu',$subject)===1;
    }

    public static function filter(array $products,string $slug):array
    {
        if($slug===''||!self::valid($slug))return $products;
        return array_values(array_filter($products,static fn(array $p):bool=>self::matches($p,$slug)));
    }

    public static function counts(array $products):array
    {
        $counts=[];
        foreach(self::definitions() as $row){
            $slug=$row[1];$counts[$slug]=0;
            foreach($products as $p)if(self::matches($p,$slug))$counts[$slug]++;
        }
        return $counts;
    }
}
