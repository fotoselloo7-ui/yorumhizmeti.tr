<?php
namespace App\Services;

/**
 * Central, truthful NetVera corporate positioning for existing and new installs.
 * Do not change user accounts, products, license IDs, routes or manually edited SEO.
 */
final class NetveraBrandSettings
{
    public static function defaults(): array
    {
        return [
            'site_name' => ['NetVera Teknoloji Yazılım','general'],
            'site_url' => ['https://netvera.tr','general'],
            'site_slogan' => ['Yazılım, Dijital Ajans ve Sosyal Medya Hizmetleri','general'],
            'footer_text' => ['© NetVera Teknoloji Yazılım. Tüm hakları saklıdır.','footer'],
            'brand_short_name' => ['NetVera','branding'],
            'brand_positioning' => ['NetVera Teknoloji Yazılım | Yazılım, Dijital Ajans ve Sosyal Medya','branding'],
            'brand_sector_software' => ['Hazır Yazılım ve Özel Web Çözümleri','branding'],
            'brand_sector_agency' => ['Dijital Ajans, SEO ve Reklam Yönetimi','branding'],
            'brand_sector_social' => ['Instagram, TikTok ve YouTube Hizmetleri','branding'],
            'default_seo_title' => ['NetVera Teknoloji Yazılım | Yazılım, Dijital Ajans ve Sosyal Medya','seo'],
            'default_seo_description' => ['NetVera Teknoloji Yazılım: hazır yazılımlar, web çözümleri, dijital ajans, SEO, reklam yönetimi ve Instagram, TikTok, YouTube hizmetlerini keşfedin.','seo'],
            'seo_og_title' => ['NetVera Teknoloji Yazılım | Yazılım, Dijital Ajans ve Sosyal Medya','seo'],
            'seo_og_description' => ['NetVera’nın yazılım çözümleri, dijital ajans, SEO ve sosyal medya hizmetlerini tek adreste inceleyin.','seo'],
            'seo_org_type' => ['Organization','geo'],
            'seo_org_description' => ['NetVera Teknoloji Yazılım; yazılım geliştirme, sektörel web çözümleri, dijital ajans, SEO, reklam yönetimi ve sosyal medya hizmetleri sunar.','geo'],
            'seo_geo_summary' => ['NetVera Teknoloji Yazılım; yazılım, SEO, reklam ve sosyal medya hizmetleri için sektöre uygun dijital çözümler sunar.','geo'],
            'seo_entity_topics' => ['NetVera Teknoloji Yazılım, hazır yazılımlar, web tasarım, dijital ajans, teknik SEO, Google Ads, Instagram hizmetleri, TikTok hizmetleri, YouTube hizmetleri','geo'],
            'seo_service_area' => ['Türkiye','geo'],
            'seo_content_intent' => ['commercial','aio'],
            'seo_home_question' => ['NetVera Teknoloji Yazılım hangi hizmetleri sunuyor?','aio'],
            'seo_home_answer' => ['NetVera; hazır ve özel yazılım çözümleri, web sitesi geliştirme, dijital ajans, SEO, reklam yönetimi ve Instagram, TikTok, YouTube hizmetleri sunar.','aio'],
            'seo_default_robots' => ['index,follow,max-image-preview:large','seo'],
        ];
    }

    /** Only retired or generic demo text is eligible. Never replace original editorial copy. */
    public static function upgradeable(string $key, ?string $raw): bool
    {
        $value=trim((string)$raw);
        if($value==='')return true;
        $lower=mb_strtolower($value,'UTF-8');
        if(preg_match('~yorumhizmeti(?:\.tr)?|yorum\s+hizmeti|yorumpanel~iu',$value))return true;
        if($key==='site_url')return (bool)preg_match('~^https?://(?:www\.)?yorumhizmeti\.tr/?$~iu',$value);
        if($key==='site_slogan')return in_array($lower,[
            'dijital hizmetlerde güvenilir çözüm ortağınız',
            'web yazılımı, sosyal medya ve dijital pazarlama çözümleri',
            'google yorum, sosyal medya ve dijital güven hizmetleri'
        ],true) || (str_contains($lower,'google yorum') && str_contains($lower,'sosyal medya'));
        // The previous platform used long Google Yorum + social-media marketing
        // titles in the DB, including ones prefixed by the new company name.
        // Match ONLY global site/meta defaults, never per-product editorial SEO.
        if(in_array($key,['default_seo_title','default_seo_description',
                          'seo_og_title','seo_og_description','brand_positioning'],true)){
            if(preg_match('/google\\s+yorum|yorum\\s+(?:sat[ıi]n|hizmet|ve\\s+sosyal)|dijital\\s+güven\\s+hizmet/iu',$value))
                return true;
        }
        if($key==='default_seo_title'||$key==='seo_og_title')return in_array($lower,[
            'netvera teknoloji yazılım | sosyal medya, seo ve dijital hizmetler',
            'netvera teknoloji yazılım'
        ],true);
        if($key==='default_seo_description')return str_contains($lower,'netvera teknoloji yazılım ile instagram, tiktok, youtube, seo') || str_contains($lower,'google, instagram, tiktok, youtube ve daha fazlası');
        if($key==='footer_text')return (bool)preg_match('/^©\s*\d{4}\s*NetVera Teknoloji Yazılım\.\s*Tüm hakları saklıdır\.?$/iu',$value);
        return false;
    }

    public static function display(string $key,?string $value):string
    {
        $defaults=self::defaults();
        if(isset($defaults[$key]) && self::upgradeable($key,$value))
            return $defaults[$key][0];
        return (string)$value;
    }

    /** Called only from authenticated admin settings screens. Makes old DB records match the rendered brand. */
    public static function syncExisting(SiteConfigService $config):array
    {
        $raw=$config->all();
        $changed=[];
        foreach(self::defaults() as $key=>[$value,$group]){
            if(self::upgradeable($key,isset($raw[$key])?(string)$raw[$key]:null)){
                $config->set($key,$value,$group);
                $changed[]=$key;
            }
        }
        return $changed;
    }

    /** Validated official profile URLs; do not invent social accounts. */
    public static function sameAs():array
    {
        $profiles=[];
        foreach(['social_instagram','social_facebook','social_youtube','social_tiktok','social_twitter'] as $key){
            $url=trim((string)setting($key));
            if(filter_var($url,FILTER_VALIDATE_URL)
                && strtolower((string)parse_url($url,PHP_URL_SCHEME))==='https')
                $profiles[]=$url;
        }
        return array_values(array_unique($profiles));
    }
}
