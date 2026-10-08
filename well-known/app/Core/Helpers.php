<?php
/**
 * YorumPanel Pro - Global Helper Functions
 */

use App\Core\Database;
use App\Services\SiteConfigService;
use App\Services\IconService;

/**
 * HTML escape
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * URL oluştur
 */
function url(string $path = ''): string
{
    $base = rtrim($_ENV['APP_URL'] ?? '', '/');
    return $base . '/' . ltrim($path, '/');
}

/**
 * Asset URL
 */
function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
}

/**
 * Fallback image helper (inline SVG)
 */
function fallback_image(string $text = 'Görsel Yok'): string
{
    $textEncoded = rawurlencode($text);
    return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='800' height='450' viewBox='0 0 800 450'><rect width='100%' height='100%' fill='%23f1f5f9'/><text x='50%' y='50%' font-family='sans-serif' font-size='24' font-weight='bold' fill='%2394a3b8' dominant-baseline='middle' text-anchor='middle'>{$textEncoded}</text></svg>";
}

/**
 * Upload URL / path helper
 */
function upload_url(?string $path, string $fallbackType = 'default'): string
{
    if (empty($path)) {
        return fallback_image();
    }
    
    // Check if it's already a full URL
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        return $path;
    }
    
    // Windows local path clean-up or absolute path clean-up
    $path = str_replace('\\', '/', $path);
    
    // Remove absolute disk paths if they get outputted
    if (preg_match('/^[A-Z]:\//i', $path) || strpos($path, 'D:/') === 0 || strpos($path, 'C:/') === 0) {
        $parts = explode('/public/uploads/', $path);
        if (count($parts) > 1) {
            $path = '/uploads/' . $parts[1];
        } else {
            $parts2 = explode('/uploads/', $path);
            if (count($parts2) > 1) {
                $path = '/uploads/' . $parts2[1];
            }
        }
    }
    
    // Remove duplicate /uploads/
    if (strpos($path, '/uploads/') === 0) {
        return $path;
    }
    
    if (strpos($path, 'uploads/') === 0) {
        return '/' . $path;
    }
    
    return '/uploads/' . ltrim($path, '/');
}

/**
 * Redirect
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Flash message
 */
function flash(string $key, mixed $value = null): mixed
{
    if ($value !== null) {
        if ($key === 'errors') {
            $_SESSION['flash_errors'] = $value;
        } elseif ($key === 'old') {
            $_SESSION['flash_old'] = $value;
        } else {
            $_SESSION['flash_messages'][$key] = $value;
        }
        return null;
    }
    return $_SESSION['flash_messages'][$key] ?? null;
}

/**
 * Eski form değeri
 */
function old(string $key, string $default = ''): string
{
    return $_SESSION['flash_old'][$key] ?? $default;
}

/**
 * Site ayarı
 */
function setting(string $key, string $default = ''): string
{
    try {
        return SiteConfigService::getInstance()->get($key, $default);
    } catch (\Exception $e) {
        return $default;
    }
}

/**
 * İkon render
 */
function icon(string $name, int $size = 20, string $class = ''): string
{
    return IconService::render($name, $size, $class);
}

/**
 * CSRF field
 */
function csrf_field(): string
{
    return App\Core\Csrf::field();
}

/**
 * Slug oluştur
 */
function slugify(string $text): string
{
    $tr = ['ç'=>'c','ğ'=>'g','ı'=>'i','ö'=>'o','ş'=>'s','ü'=>'u','Ç'=>'c','Ğ'=>'g','İ'=>'i','Ö'=>'o','Ş'=>'s','Ü'=>'u'];
    $text = strtr($text, $tr);
    $text = mb_strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Para formatla
 */
function money(float $amount): string
{
    return number_format($amount, 2, ',', '.') . ' ₺';
}

/**
 * Paket adı boş bırakılmış eski kayıtlar için güvenli görünen ad üretir.
 * Veritabanını değiştirmez; sadece vitrindeki boş başlıkları engeller.
 */
function package_display_name(array $package): string
{
    $name = trim((string)($package['name'] ?? ''));
    if ($name !== '') {
        return $name;
    }

    foreach (['seo_title', 'og_title'] as $key) {
        $candidate = trim((string)($package[$key] ?? ''));
        if ($candidate !== '') {
            $candidate = trim(explode('|', $candidate)[0]);
            if ($candidate !== '') {
                return $candidate;
            }
        }
    }

    $slug = trim((string)($package['slug'] ?? ''));
    if ($slug !== '') {
        $label = str_replace('-', ' ', $slug);
        return mb_convert_case($label, MB_CASE_TITLE, 'UTF-8');
    }

    return 'Dijital Hizmet Paketi';
}


/**
 * Demo vitrinde boş görsel alanlarını konuya uygun stok görsellerle doldurur.
 * DB alanlarını değiştirmez; gerçek görsel yüklendiğinde otomatik olarak devreden çıkar.
 */
function demo_visual_url(string $text = '', string $kind = 'card'): string
{
    $s = mb_strtolower($text . ' ' . $kind, 'UTF-8');

    if (str_contains($s, 'instagram') || str_contains($s, 'sosyal medya')) {
        return 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?auto=format&fit=crop&w=1400&q=86';
    }
    if (str_contains($s, 'tiktok') || str_contains($s, 'reels') || str_contains($s, 'video')) {
        return 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1400&q=86';
    }
    if (str_contains($s, 'youtube') || str_contains($s, 'icerik') || str_contains($s, 'içerik')) {
        return 'https://images.unsplash.com/photo-1492724441997-5dc865305da7?auto=format&fit=crop&w=1400&q=86';
    }
    if (str_contains($s, 'web') || str_contains($s, 'seo') || str_contains($s, 'site') || str_contains($s, 'kod')) {
        return 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1400&q=86';
    }
    if (str_contains($s, 'google') || str_contains($s, 'harita') || str_contains($s, 'işletme') || str_contains($s, 'isletme')) {
        return 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1400&q=86';
    }
    if (str_contains($s, 'reklam') || str_contains($s, 'pazarlama') || str_contains($s, 'analiz') || str_contains($s, 'trend')) {
        return 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1400&q=86';
    }
    if (str_contains($s, 'destek') || str_contains($s, 'iletisim') || str_contains($s, 'iletişim')) {
        return 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=1400&q=86';
    }

    return 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1400&q=86';
}

/**
 * Sadece demo görünümünü dolu göstermek için kullanılan sanal blog kartları.
 * Gerçek içerik sayısı yeterli olduğunda kullanılmaz.
 */
function demo_blog_posts(): array
{
    return [
        [
            'title' => 'Instagram Etkileşimini Artırmanın 10 Etkili Yolu',
            'slug' => 'sosyal-medya-pazarlamasi-2024-trendleri',
            'excerpt' => 'Daha fazla beğeni, yorum ve görünürlük için uygulanabilir Instagram stratejileri.',
            'category_name' => 'Instagram',
            'category_slug' => 'instagram',
            'published_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'views' => 18700,
            'image' => null,
            'image_alt' => 'Instagram etkileşim stratejileri'
        ],
        [
            'title' => 'TikTok’ta Keşfete Çıkma Taktikleri',
            'slug' => 'sosyal-medya-pazarlamasi-2024-trendleri',
            'excerpt' => 'Kısa video içeriklerin daha fazla kişiye ulaşması için içerik ve yayınlama taktikleri.',
            'category_name' => 'TikTok',
            'category_slug' => 'tiktok',
            'published_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
            'views' => 16100,
            'image' => null,
            'image_alt' => 'TikTok keşfet taktikleri'
        ],
        [
            'title' => 'Web Siteniz İçin SEO İpuçları',
            'slug' => 'seo-nedir-baslangic-rehberi',
            'excerpt' => 'Teknik yapıdan içeriğe kadar sitenizi arama sonuçlarında güçlendirecek temel adımlar.',
            'category_name' => 'Web Site / SEO',
            'category_slug' => 'seo-rehberi',
            'published_at' => date('Y-m-d H:i:s', strtotime('-6 days')),
            'views' => 14300,
            'image' => null,
            'image_alt' => 'Web sitesi SEO ipuçları'
        ],
        [
            'title' => 'YouTube Kanalınızı Organik Olarak Büyütün',
            'slug' => 'sosyal-medya-pazarlamasi-2024-trendleri',
            'excerpt' => 'İçerik planı, başlık, küçük resim ve yayın ritmiyle kanal büyümesini hızlandırın.',
            'category_name' => 'YouTube',
            'category_slug' => 'youtube',
            'published_at' => date('Y-m-d H:i:s', strtotime('-8 days')),
            'views' => 11900,
            'image' => null,
            'image_alt' => 'YouTube kanal büyütme'
        ],
        [
            'title' => 'Müşteri Yorumlarıyla Güven Nasıl Artırılır?',
            'slug' => 'google-isletme-profili-nasil-optimize-edilir',
            'excerpt' => 'Gerçek müşteri geri bildirimlerini güven ve dönüşüm avantajına dönüştürmenin yolları.',
            'category_name' => 'Google İşletme Profili',
            'category_slug' => 'google-isletme-profili',
            'published_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
            'views' => 9800,
            'image' => null,
            'image_alt' => 'Müşteri yorumları ve güven'
        ],
        [
            'title' => 'Dijital Reklamda Dönüşüm Odaklı Kampanya Kurulumu',
            'slug' => 'sosyal-medya-pazarlamasi-2024-trendleri',
            'excerpt' => 'Bütçe, hedef kitle ve kreatifleri aynı stratejide buluşturan kampanya yaklaşımı.',
            'category_name' => 'Dijital Pazarlama',
            'category_slug' => 'sosyal-medya',
            'published_at' => date('Y-m-d H:i:s', strtotime('-12 days')),
            'views' => 8700,
            'image' => null,
            'image_alt' => 'Dijital reklam kampanyası'
        ],
    ];
}

/**
 * Tarih formatla
 */
function formatDate(?string $date, string $format = 'd.m.Y H:i'): string
{
    if (!$date) return '-';
    return date($format, strtotime($date));
}

/**
 * Kısa metin
 */
function excerpt(string $text, int $length = 160): string
{
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

/**
 * Aktif menü kontrolü
 */
function isActive(string $path): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if ($path === '/') {
        return $uri === '/' ? 'active' : '';
    }
    return str_starts_with($uri, $path) ? 'active' : '';
}

/**
 * Sipariş durum etiketi
 */
function orderStatusLabel(string $status): string
{
    $labels = [
        'payment_pending' => 'Ödeme Bekliyor',
        'paid' => 'Ödeme Alındı',
        'preparing' => 'Hazırlanıyor',
        'processing' => 'İşlemde',
        'completed' => 'Tamamlandı',
        'cancelled' => 'İptal Edildi',
        'refunded' => 'İade Edildi',
        'waiting_customer_info' => 'Eksik Bilgi Bekleniyor',
    ];
    return $labels[$status] ?? $status;
}

/**
 * Sipariş durum rengi
 */
function orderStatusColor(string $status): string
{
    $colors = [
        'payment_pending' => 'warning',
        'paid' => 'info',
        'preparing' => 'info',
        'processing' => 'primary',
        'completed' => 'success',
        'cancelled' => 'danger',
        'refunded' => 'danger',
        'waiting_customer_info' => 'warning',
    ];
    return $colors[$status] ?? 'default';
}

/**
 * Destek durum etiketi
 */
function ticketStatusLabel(string $status): string
{
    $labels = [
        'open' => 'Açık',
        'answered' => 'Cevaplandı',
        'customer_reply' => 'Müşteri Yanıtı',
        'closed' => 'Kapatıldı',
    ];
    return $labels[$status] ?? $status;
}

/**
 * Öncelik etiketi
 */
function priorityLabel(string $priority): string
{
    $labels = [
        'low' => 'Düşük',
        'medium' => 'Normal',
        'high' => 'Yüksek',
        'urgent' => 'Acil',
    ];
    return $labels[$priority] ?? $priority;
}

/**
 * Activity log kaydet
 */
/**
 * CSRF field alias (csrfField() = csrf_field())
 */
function csrfField(): string
{
    return App\Core\Csrf::field();
}

/**
 * Destek durum etiketi
 */
function supportStatusLabel(string $status): string
{
    $labels = [
        'open' => 'Açık',
        'admin_reply' => 'Yanıtlandı',
        'customer_reply' => 'Müşteri Yanıtı',
        'closed' => 'Kapatıldı',
    ];
    return $labels[$status] ?? $status;
}

/**
 * Ödeme durum etiketi
 */
function paymentStatusLabel(string $status): string
{
    $labels = [
        'pending' => 'Bekliyor',
        'paid' => 'Ödendi',
        'failed' => 'Başarısız',
        'refunded' => 'İade',
    ];
    return $labels[$status] ?? $status;
}

/**
 * Activity log kaydet
 */
function logActivity(string $action, string $detail = '', ?int $adminId = null): void
{
    try {
        $db = Database::getInstance();
        $db->insert('admin_activity_logs', [
            'admin_id' => $adminId ?? ($_SESSION['admin_id'] ?? null),
            'action' => $action,
            'detail' => $detail,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
    } catch (\Exception $e) {
        // Loglama hatası sistemi durdurmamalı
    }
}
