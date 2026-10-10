<?php
namespace App\Services;

/**
 * Visual-only official platform identity; no SEO, route or catalogue mutations.
 * Palette tokens are static and safe for CSS; no user-provided style fragments.
 */
final class SocialPlatformIdentity
{
    private const PLATFORMS = [
        'instagram'=>['Instagram','instagram','#E1306C','#833AB4','#FFF0F6'],
        'tiktok'=>['TikTok','tiktok','#111111','#00F2EA','#ECEEFF'],
        'youtube'=>['YouTube','youtube','#FF0033','#CC0000','#FFF0F1'],
        'facebook'=>['Facebook','facebook','#1877F2','#0D5DBB','#E9F2FF'],
        'google'=>['Google','google','#4285F4','#34A853','#EAF2FF'],
        'telegram'=>['Telegram','telegram','#229ED9','#1686C6','#E8F7FE'],
        'twitter'=>['X','twitter','#111111','#343434','#EDEDED'],
        'threads'=>['Threads','threads','#121212','#454545','#F0F0F0'],
        'spotify'=>['Spotify','spotify','#1DB954','#12863A','#E9F9EE'],
        'twitch'=>['Twitch','twitch','#9146FF','#6441A5','#F3EAFF'],
        'discord'=>['Discord','discord','#5865F2','#404EED','#EFF1FF'],
        'linkedin'=>['LinkedIn','linkedin','#0A66C2','#004182','#EBF5FF'],
        'whatsapp'=>['WhatsApp','whatsapp','#25D366','#128C7E','#E8FBF0'],
        'snapchat'=>['Snapchat','snapchat','#FFFC00','#F2EB00','#FFFDEB'],
        'pinterest'=>['Pinterest','pinterest','#E60023','#B8001C','#FFF0F2'],
        'soundcloud'=>['SoundCloud','soundcloud','#FF5500','#FF8800','#FFF1E8'],
        'bluesky'=>['Bluesky','bluesky','#1185FE','#0869D3','#EAF4FF'],
        'kick'=>['Kick','kick','#53FC18','#2D8415','#EDFFE8'],
    ];
    public static function all(): array { return self::PLATFORMS; }
    public static function fromText(string $text): ?string
    {
        $s=mb_strtolower($text,'UTF-8');
        foreach(['instagram'=>'instagram','tik-tok'=>'tiktok','tiktok'=>'tiktok',
          'youtube'=>'youtube','facebook'=>'facebook','google'=>'google',
          'telegram'=>'telegram','twitter'=>'twitter','x-twitter'=>'twitter',
          'threads'=>'threads','spotify'=>'spotify','twitch'=>'twitch',
          'discord'=>'discord','linkedin'=>'linkedin','whatsapp'=>'whatsapp',
          'snapchat'=>'snapchat','pinterest'=>'pinterest','soundcloud'=>'soundcloud',
          'bluesky'=>'bluesky','kick'=>'kick'] as $needle=>$key) {
            if(str_contains($s,$needle))return $key;
        }
        if(preg_match('/(^|[\s_\-\/])x([\s_\-\/]|$)/iu',$s))return 'twitter';
        return null;
    }
    public static function fromCategory(array $category,?array $parent=null):?string
    {
        $subject=implode(' ',[
            (string)($category['slug']??''),
            (string)($category['name']??''),
            (string)($parent['slug']??''),
            (string)($parent['name']??''),
            (string)($category['parent_category_slug']??''),
            (string)($category['parent_category_name']??'')
        ]);
        return self::fromText($subject);
    }
    /**
     * Product identity is inherited from its real DB parent category; child
     * category or product titles cannot accidentally repaint a Google service
     * card as a different brand.
     */
    public static function fromPackage(array $package): ?string
    {
        $parent=self::fromText((string)($package['featured_group_slug']??$package['parent_category_slug']??'').' '.
                               (string)($package['featured_group_name']??$package['parent_category_name']??''));
        if($parent!==null)return $parent;
        return self::fromText((string)($package['category_slug']??'').' '.
                              (string)($package['category_name']??'').' '.
                              (string)($package['name']??''));
    }
    public static function classFor(?string $key):string
    {
        return $key!==null && isset(self::PLATFORMS[$key])?'nv-platform-'.$key:'';
    }
    public static function iconFor(?string $key,string $fallback='package'):string
    {
        return $key!==null && isset(self::PLATFORMS[$key])
            ?self::PLATFORMS[$key][1]:$fallback;
    }
    public static function brand(?string $key):?array
    {
        if($key===null || !isset(self::PLATFORMS[$key]))return null;
        return self::PLATFORMS[$key];
    }
}
