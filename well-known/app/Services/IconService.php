<?php
namespace App\Services;

/**
 * IconService - Modern Remix Icon Sistemi
 * HTML içerisine raw SVG gömmek yerine, çok daha temiz ve performanslı
 * olan Remix Icon font sistemini kullanır. Veritabanındaki eski ikon 
 * anahtarları (Lucide) Remix karşılıklarına eşlenir.
 */
class IconService
{
    private static array $map = [
        'home' => 'home',
        'package' => 'box-3',
        'box' => 'box-3',
        'shopping-cart' => 'shopping-cart-2',
        'users' => 'group',
        'user' => 'user',
        'settings' => 'settings-3',
        'search' => 'search',
        'menu' => 'menu',
        'x' => 'close',
        'check' => 'check',
        'check-circle' => 'checkbox-circle',
        'alert-triangle' => 'error-warning',
        'alert-circle' => 'information',
        'info' => 'information',
        'plus' => 'add',
        'minus' => 'subtract',
        'edit' => 'edit-2',
        'trash' => 'delete-bin',
        'eye' => 'eye',
        'eye-off' => 'eye-off',
        'mail' => 'mail',
        'phone' => 'phone',
        'globe' => 'global',
        'star' => 'star',
        'heart' => 'heart',
        'share-2' => 'share',
        'external-link' => 'external-link',
        'lock' => 'lock-2',
        'unlock' => 'lock-unlock',
        'log-out' => 'logout-box-r',
        'log-in' => 'login-box',
        'shield' => 'shield',
        'credit-card' => 'bank-card',
        'truck' => 'truck',
        'headphones' => 'customer-service-2',
        'message-circle' => 'message-3',
        'help-circle' => 'question',
        'bar-chart' => 'bar-chart-box',
        'file-text' => 'file-text',
        'image' => 'image',
        'upload' => 'upload-cloud-2',
        'download' => 'download-cloud-2',
        'filter' => 'filter-3',
        'calendar' => 'calendar',
        'clock' => 'time',
        'tag' => 'price-tag-3',
        'bookmark' => 'bookmark',
        'arrow-left' => 'arrow-left',
        'arrow-right' => 'arrow-right',
        'arrow-up' => 'arrow-up',
        'arrow-down' => 'arrow-down',
        'chevron-right' => 'arrow-right-s',
        'chevron-left' => 'arrow-left-s',
        'chevron-down' => 'arrow-down-s',
        'chevron-up' => 'arrow-up-s',
        'refresh-cw' => 'refresh',
        'more-vertical' => 'more-2',
        'camera' => 'camera',
        'video' => 'video',
        'play-circle' => 'play-circle',
        'thumbs-up' => 'thumb-up',
        'target' => 'focus-2',
        'trending-up' => 'line-chart',
        'layers' => 'stack',
        'grid' => 'layout-grid',
        'list' => 'list-unordered',
        'copy' => 'file-copy',
        'clipboard' => 'clipboard',
        'dollar-sign' => 'money-dollar-circle',
        'percent' => 'percent',
        'activity' => 'pulse',
        'zap' => 'flashlight',
        'award' => 'award',
        'inbox' => 'inbox',
        'send' => 'send-plane',
        'link' => 'links',
        'map-pin' => 'map-pin-2',
        'whatsapp' => 'whatsapp',
        'instagram' => 'instagram',
        'facebook' => 'facebook-circle',
        'tiktok' => 'tiktok',
        'google' => 'google',
        'youtube' => 'youtube',
        'twitter' => 'twitter-x',
        'save' => 'save-3',
        'folder' => 'folder-2',
        'database' => 'database-2',
        'toggle-left' => 'toggle',
        'toggle-right' => 'toggle-fill',
        'power' => 'shut-down',
        'rotate-ccw' => 'arrow-go-back',
        'play' => 'play',
        'bar-chart-2' => 'bar-chart-2'
    ];

    /**
     * İkon render et
     */
    public static function render(string $name, int $size = 20, string $class = ''): string
    {
        // Eğer isim zaten ri- ile başlıyorsa direkt kullanalım, yoksa map'ten bakalım
        if (str_starts_with($name, 'ri-')) {
            $remixName = $name;
        } else {
            $mapped = self::$map[$name] ?? $name; // Mapte yoksa girileni kullan
            
            // Eğer sosyal medya ikonuysa (instagram, facebook vb.) genelde -fill istenir, 
            // ama RemixIcon line versiyonu için -line ekliyoruz.
            // Marka ikonları fill olarak gelir genelde.
            $isBrand = in_array($mapped, ['whatsapp', 'instagram', 'facebook-circle', 'tiktok', 'google', 'youtube', 'twitter-x']);
            
            $suffix = $isBrand ? '-fill' : '-line';
            $remixName = 'ri-' . $mapped . $suffix;
        }

        $classAttr = 'icon ' . $remixName . ($class ? ' ' . $class : '');
        $styleAttr = "font-size: {$size}px; display: inline-flex; align-items: center; justify-content: center; line-height: 1;";

        return '<i class="' . e($classAttr) . '" style="' . $styleAttr . '"></i>';
    }

    /**
     * Tüm ikon listesi (Admin panel vb. yerde ikon seçici için)
     */
    public static function list(): array
    {
        return array_keys(self::$map);
    }

    /**
     * İkon var mı kontrol
     */
    public static function has(string $name): bool
    {
        return isset(self::$map[$name]);
    }
}
