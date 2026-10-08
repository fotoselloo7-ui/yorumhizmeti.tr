<?php
namespace App\Services;

class IconService
{
    private static array $map = [
        'home'=>['solid','house'],'package'=>['solid','box'],'box'=>['solid','box'],
        'shopping-cart'=>['solid','cart-shopping'],'shopping-bag'=>['solid','bag-shopping'],'users'=>['solid','users'],'user'=>['regular','user'],
        'settings'=>['solid','gear'],'search'=>['solid','magnifying-glass'],'menu'=>['solid','bars'],
        'x'=>['solid','xmark'],'x-circle'=>['regular','circle-xmark'],'check'=>['solid','check'],'check-circle'=>['regular','circle-check'],'shield-check'=>['solid','shield-halved'],
        'alert-triangle'=>['solid','triangle-exclamation'],'alert-circle'=>['solid','circle-exclamation'],
        'info'=>['solid','circle-info'],'plus'=>['solid','plus'],'minus'=>['solid','minus'],
        'edit'=>['regular','pen-to-square'],'edit-3'=>['regular','pen-to-square'],'trash'=>['regular','trash-can'],'eye'=>['regular','eye'],
        'eye-off'=>['regular','eye-slash'],'mail'=>['regular','envelope'],'phone'=>['solid','phone'],
        'globe'=>['solid','globe'],'star'=>['regular','star'],'star-fill'=>['solid','star'],'heart'=>['regular','heart'],
        'external-link'=>['solid','arrow-up-right-from-square'],'lock'=>['solid','lock'],
        'unlock'=>['solid','lock-open'],'log-out'=>['solid','right-from-bracket'],'log-in'=>['solid','right-to-bracket'],
        'shield'=>['solid','shield-halved'],'credit-card'=>['regular','credit-card'],'truck'=>['solid','truck-fast'],
        'headphones'=>['solid','headset'],'message-circle'=>['regular','comment-dots'],
        'help-circle'=>['regular','circle-question'],'bar-chart'=>['solid','chart-column'],
        'bar-chart-2'=>['solid','chart-column'],'file-text'=>['regular','file-lines'],'image'=>['regular','image'],
        'upload'=>['solid','cloud-arrow-up'],'download'=>['solid','cloud-arrow-down'],'filter'=>['solid','filter'],
        'calendar'=>['regular','calendar'],'clock'=>['regular','clock'],'tag'=>['solid','tag'],
        'bookmark'=>['regular','bookmark'],'arrow-left'=>['solid','arrow-left'],'arrow-right'=>['solid','arrow-right'],
        'arrow-up'=>['solid','arrow-up'],'arrow-down'=>['solid','arrow-down'],
        'chevron-right'=>['solid','chevron-right'],'chevron-left'=>['solid','chevron-left'],
        'chevron-down'=>['solid','chevron-down'],'chevron-up'=>['solid','chevron-up'],
        'refresh-cw'=>['solid','arrows-rotate'],'more-vertical'=>['solid','ellipsis-vertical'],
        'camera'=>['solid','camera'],'video'=>['solid','video'],'music'=>['solid','music'],'life-buoy'=>['regular','life-ring'],'play-circle'=>['regular','circle-play'],
        'play'=>['solid','play'],'thumbs-up'=>['regular','thumbs-up'],'target'=>['solid','bullseye'],
        'trending-up'=>['solid','arrow-trend-up'],'layers'=>['solid','layer-group'],'grid'=>['solid','grip'],
        'list'=>['solid','list'],'copy'=>['regular','copy'],'clipboard'=>['regular','clipboard'],
        'dollar-sign'=>['solid','turkish-lira-sign'],'percent'=>['solid','percent'],
        'activity'=>['solid','wave-square'],'zap'=>['solid','bolt'],'award'=>['solid','award'],
        'inbox'=>['solid','inbox'],'send'=>['solid','paper-plane'],'link'=>['solid','link'],
        'map-pin'=>['solid','location-dot'],'save'=>['regular','floppy-disk'],'folder'=>['regular','folder'],
        'database'=>['solid','database'],'toggle-left'=>['solid','toggle-off'],'toggle-right'=>['solid','toggle-on'],
        'power'=>['solid','power-off'],'rotate-ccw'=>['solid','rotate-left'],
        'whatsapp'=>['brands','whatsapp'],'instagram'=>['brands','instagram'],'facebook'=>['brands','facebook-f'],
        'tiktok'=>['brands','tiktok'],'google'=>['brands','google'],'youtube'=>['brands','youtube'],
        'twitter'=>['brands','x-twitter'],'threads'=>['brands','threads'],'telegram'=>['brands','telegram'],
        'spotify'=>['brands','spotify'],'discord'=>['brands','discord'],'linkedin'=>['brands','linkedin-in'],
        'twitch'=>['brands','twitch'],'store'=>['solid','store'],'mobile-app'=>['solid','mobile-screen-button'],
        'content-create'=>['solid','pen-nib'],'palette'=>['solid','palette'],'local-business'=>['solid','location-dot'],
        'reputation'=>['solid','shield-halved'],'ads'=>['solid','bullhorn']
    ];

    public static function render(string $name, int $size = 20, string $class = ''): string
    {
        $item = self::$map[$name] ?? ['regular','circle'];
        $prefix = $item[0] === 'brands' ? 'fa-brands' : ($item[0] === 'regular' ? 'fa-regular' : 'fa-solid');
        $safeClass = trim($class);
        return '<i class="icon fa-fw ' . $prefix . ' fa-' . $item[1] . ($safeClass ? ' ' . $safeClass : '') . '" aria-hidden="true" style="font-size:' . (int)$size . 'px"></i>';
    }

    public static function list(): array
    {
        return array_keys(self::$map);
    }

    public static function has(string $name): bool
    {
        return isset(self::$map[$name]);
    }
}
