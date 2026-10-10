<?php
namespace App\Services;
use App\Core\Database;

/** Shared visual taxonomy. Never creates or changes categories: admin database is authoritative. */
final class CatalogMenuService
{
    private static ?array $cache = null;
    public static function groups(): array
    {
        if (self::$cache !== null) return self::$cache;
        $groups = [
          'social'=>['key'=>'social','label'=>'Sosyal Medya Hizmetleri','short'=>'Sosyal Medya','icon'=>'heart','description'=>'Platform bazlı sosyal medya hizmetleri','categories'=>[]],
          'agency'=>['key'=>'agency','label'=>'Ajans & Yazılım','short'=>'Ajans & Yazılım','icon'=>'layers','description'=>'Web sitesi, yazılım, tasarım ve içerik çözümleri','categories'=>[]],
          'marketing'=>['key'=>'marketing','label'=>'SEO & Dijital Pazarlama','short'=>'SEO & Dijital','icon'=>'trending-up','description'=>'SEO, reklam, Google ve işletme hizmetleri','categories'=>[]],
        ];
        try {
            $rows = Database::getInstance()->fetchAll("SELECT id,parent_id,name,slug,description,icon_key,sort_order FROM categories WHERE status = 'active' ORDER BY sort_order ASC,id ASC");
            $roots = []; $children = [];
            foreach ($rows as $cat) {
                $cat['id']=(int)$cat['id'];
                $cat['parent_id']=$cat['parent_id']===null?null:(int)$cat['parent_id'];
                if ($cat['parent_id']===null) $roots[$cat['id']]=$cat;
                else $children[$cat['parent_id']][]=$cat;
            }
            foreach ($roots as $cat) {
                if(!ServiceCategoryVisibility::show($cat))continue;
                $cat['style']=self::style($cat);
                $cat['icon']=self::icon($cat);
                $cat['url']='/kategori/'.rawurlencode($cat['slug']);
                $cat['children']=array_values(array_filter($children[$cat['id']]??[],
                    static fn(array $child):bool=>ServiceCategoryVisibility::show($child,$cat)));
                foreach ($cat['children'] as &$child) {
                    $child['style']=self::style($child,$cat);
                    $child['icon']=self::subcategoryIcon($child,$cat);
                    // Every real non-software child is a standalone indexable service
                    // page with its own title, canonical and visible products.
                    $softwareParent=(bool)preg_match('/yaz[ıi]l[ıi]m|yazilim|script|web.?site|cms|software|wordpress/iu',
                        (string)$cat['name'].' '.(string)$cat['slug']);
                    $child['url']=$softwareParent
                        ?$cat['url'].'?alt='.rawurlencode($child['slug'])
                        :'/kategori/'.rawurlencode($child['slug']);
                }
                unset($child);
                $groups[self::bucket($cat)]['categories'][]=$cat;
            }
        } catch (\Throwable $e) {
            error_log('CatalogMenuService: '.$e->getMessage());
        }
        return self::$cache=array_values(array_filter($groups,static fn($g)=>!empty($g['categories'])));
    }
    public static function bucket(array $cat): string
    {
        $t=preg_replace('/\x{0307}/u','',mb_strtolower(($cat['slug']??'').' '.($cat['name']??''),'UTF-8'));
        if (preg_match('/instagram|tiktok|youtube|facebook|twitter|threads|telegram|spotify|discord|linkedin|twitch|pinterest|snapchat|whatsapp|bluesky|soundcloud|tumblr|kick|sosyal.?medya|x-twitter/u',$t)) return 'social';
        if (preg_match('/web|site|yazılım|yazilim|software|e.?ticaret|eticaret|commerce|mobil|uygulama|app|içerik|icerik|grafik|tasarım|tasarim|kurumsal|wordpress|hosting|ajans|marka.?tescil/u',$t)) return 'agency';
        return 'marketing';
    }
    public static function style(array $cat,?array $parent=null): string
    {
        $t=preg_replace('/\x{0307}/u','',mb_strtolower(($parent['slug']??'').' '.($cat['slug']??'').' '.($cat['name']??''),'UTF-8'));
        foreach (['instagram','tiktok','youtube','facebook','threads','telegram','spotify','discord','linkedin','twitch','twitter','pinterest','snapchat','whatsapp','github','soundcloud','bluesky','google','seo'] as $name)
            if (str_contains($t,$name)) return $name;
        if (preg_match('/(^|[ -])x[ -]|x-twitter/u',$t))return 'twitter';
        foreach (['software'=>'/hazır.?yazılım|hazir.?yazilim|script|yazılım.?script|yazilim.?script/u','web'=>'/web|site|wordpress|domain|hosting/u','ecommerce'=>'/e.?ticaret|eticaret|commerce/u','mobileapp'=>'/mobil|uygulama|app/u','content'=>'/içerik|icerik/u','graphic'=>'/grafik|tasarım|tasarim/u','ads'=>'/reklam|ads/u','local'=>'/yerel|işletme|isletme/u','reputation'=>'/itibar/u'] as $key=>$pattern)
            if(preg_match($pattern,$t))return $key;
        return 'default';
    }
    /**
     * Every child service gets its OWN relevant symbol. Parent platform keywords
     * are deliberately excluded so "Google 5 Yıldız" shows a star, not another G.
     * This is presentation-only; admin category data and URLs stay untouched.
     */
    public static function subcategoryIcon(array $child, ?array $parent=null): string
    {
        // Social-platform service icons inherit the parent brand, including
        // Google Yorum, Google Harita, Instagram Takipçi and Reels.
        $platform=SocialPlatformIdentity::fromCategory($child,$parent);
        if($platform!==null)return SocialPlatformIdentity::iconFor($platform);
        $term = preg_replace('/\x{0307}/u','',mb_strtolower(($child['slug'] ?? '') . ' ' . ($child['name'] ?? ''), 'UTF-8'));
        $rules = [
            ['/yıldız|yildiz|5.?star|puan|rating/u', 'star-fill'],
            ['/harita|maps?|konum|lokasyon|location/u', 'map-pin'],
            ['/yorum|değerlendirme|degerlendirme|şikayet|sikayet|mesaj|review/u', 'message-circle'],
            ['/analiz|analizler|audit|rapor|analytics|istatistik|performans/u', 'bar-chart'],
            ['/backlink|bağlantı|baglanti|link.?building/u', 'link'],
            ['/teknik|technical|altyapı|altyapi/u', 'settings'],
            ['/yerel|local/u', 'map-pin'],
            ['/meta|facebook/u', 'facebook'],
            ['/instagram/u', 'instagram'],
            ['/tiktok/u', 'tiktok'],
            ['/youtube/u', 'youtube'],
            ['/google.?ads|adwords|reklam|advert/u', 'ads'],
            ['/seo|arama.?motoru/u', 'search'],
            ['/profil|işletme|isletme/u', 'store'],
            ['/qr.?kod|karekod/u', 'grid'],
            ['/wordpress|web.?site|website|domain|hosting/u', 'globe'],
            ['/e.?ticaret|eticaret|ecommerce|ürün|urun|sepet/u', 'store'],
            ['/mobil|uygulama|ios|android/u', 'mobile-app'],
            ['/tasarım|tasarim|grafik|logo/u', 'palette'],
            ['/içerik|icerik|blog|makale|metin/u', 'content-create'],
            ['/itibar|güven|guven|koruma/u', 'shield-check'],
            ['/takipçi|takipci|followers|abone/u', 'users'],
            ['/beğeni|begeni|like/u', 'heart'],
            ['/görüntülen|goruntulen|izlenme|view/u', 'eye'],
        ];
        foreach ($rules as [$pattern, $icon]) {
            if (preg_match($pattern, $term)) return $icon;
        }
        $configured = (string)($child['icon_key'] ?? '');
        return IconService::has($configured) ? $configured : 'package';
    }

    public static function icon(array $cat,?array $parent=null): string
    {
        $s=self::style($cat,$parent);
        $map=['software'=>'monitor','seo'=>'bar-chart','web'=>'globe','ecommerce'=>'store','mobileapp'=>'mobile-app','content'=>'content-create','graphic'=>'palette','ads'=>'ads','local'=>'local-business','reputation'=>'reputation'];
        $icon=$map[$s]??($s==='default'?($cat['icon_key']??'package'):$s);
        return IconService::has($icon)?$icon:'package';
    }
}
