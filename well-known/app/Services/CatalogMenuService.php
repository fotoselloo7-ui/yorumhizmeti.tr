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
                $cat['style']=self::style($cat);
                $cat['icon']=self::icon($cat);
                $cat['url']='/kategori/'.rawurlencode($cat['slug']);
                $cat['children']=$children[$cat['id']]??[];
                foreach ($cat['children'] as &$child) {
                    $child['style']=self::style($child,$cat);
                    $child['icon']=self::icon($child,$cat);
                    $child['url']=$cat['url'].'?alt='.rawurlencode($child['slug']);
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
        $t=mb_strtolower(($cat['slug']??'').' '.($cat['name']??''),'UTF-8');
        if (preg_match('/instagram|tiktok|youtube|facebook|twitter|threads|telegram|spotify|discord|linkedin|twitch|pinterest|snapchat|whatsapp|bluesky|soundcloud|tumblr|kick|sosyal.?medya|x-twitter/u',$t)) return 'social';
        if (preg_match('/web|site|yazılım|yazilim|software|e.?ticaret|eticaret|commerce|mobil|uygulama|app|içerik|icerik|grafik|tasarım|tasarim|kurumsal|wordpress|hosting|ajans|marka.?tescil/u',$t)) return 'agency';
        return 'marketing';
    }
    public static function style(array $cat,?array $parent=null): string
    {
        $t=mb_strtolower(($parent['slug']??'').' '.($cat['slug']??'').' '.($cat['name']??''),'UTF-8');
        foreach (['instagram','tiktok','youtube','facebook','threads','telegram','spotify','discord','linkedin','twitch','twitter','pinterest','snapchat','whatsapp','github','soundcloud','bluesky','google','seo'] as $name)
            if (str_contains($t,$name)) return $name;
        if (preg_match('/(^|[ -])x[ -]|x-twitter/u',$t))return 'twitter';
        foreach (['web'=>'/web|site|wordpress|domain|hosting/u','ecommerce'=>'/e.?ticaret|eticaret|commerce/u','mobileapp'=>'/mobil|uygulama|app/u','content'=>'/içerik|icerik/u','graphic'=>'/grafik|tasarım|tasarim/u','ads'=>'/reklam|ads/u','local'=>'/yerel|işletme|isletme/u','reputation'=>'/itibar/u'] as $key=>$pattern)
            if(preg_match($pattern,$t))return $key;
        return 'default';
    }
    public static function icon(array $cat,?array $parent=null): string
    {
        $s=self::style($cat,$parent);
        $map=['seo'=>'bar-chart','web'=>'globe','ecommerce'=>'store','mobileapp'=>'mobile-app','content'=>'content-create','graphic'=>'palette','ads'=>'ads','local'=>'local-business','reputation'=>'reputation'];
        $icon=$map[$s]??($s==='default'?($cat['icon_key']??'package'):$s);
        return IconService::has($icon)?$icon:'package';
    }
}
