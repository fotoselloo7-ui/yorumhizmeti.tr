<?php
namespace App\Services;

use App\Core\Database;

final class HeroSliderService
{
    private static bool $ready = false;

    public static function ensureSchema(): void
    {
        if (self::$ready) return;
        $db = Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS hero_slides (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                eyebrow VARCHAR(120) DEFAULT NULL,
                title VARCHAR(220) NOT NULL,
                description TEXT DEFAULT NULL,
                category_label VARCHAR(120) DEFAULT NULL,
                category_url VARCHAR(255) DEFAULT NULL,
                category_icon VARCHAR(60) DEFAULT 'sparkles',
                primary_button_text VARCHAR(100) DEFAULT NULL,
                primary_button_url VARCHAR(255) DEFAULT NULL,
                primary_button_icon VARCHAR(60) DEFAULT 'arrow-right',
                primary_button_bg VARCHAR(20) DEFAULT '#ffffff',
                primary_button_text_color VARCHAR(20) DEFAULT '#14224a',
                secondary_button_text VARCHAR(100) DEFAULT NULL,
                secondary_button_url VARCHAR(255) DEFAULT NULL,
                secondary_button_icon VARCHAR(60) DEFAULT 'play-circle',
                secondary_button_bg VARCHAR(20) DEFAULT 'rgba(255,255,255,.12)',
                secondary_button_text_color VARCHAR(20) DEFAULT '#ffffff',
                desktop_image VARCHAR(255) DEFAULT NULL,
                mobile_image VARCHAR(255) DEFAULT NULL,
                image_alt VARCHAR(255) DEFAULT NULL,
                background_start VARCHAR(20) DEFAULT '#13255b',
                background_end VARCHAR(20) DEFAULT '#6d28d9',
                overlay_color VARCHAR(32) DEFAULT 'rgba(7,16,48,.34)',
                text_color VARCHAR(20) DEFAULT '#ffffff',
                accent_color VARCHAR(20) DEFAULT '#7c5cff',
                stat_text VARCHAR(120) DEFAULT NULL,
                quick_links JSON DEFAULT NULL,
                autoplay_delay INT UNSIGNED DEFAULT 6500,
                sort_order INT DEFAULT 100,
                status ENUM('active','inactive') DEFAULT 'active',
                starts_at DATETIME DEFAULT NULL,
                ends_at DATETIME DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_hero_status_sort (status, sort_order, id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        self::$ready = true;
        self::seedDefaults();
    }

    private static function seedDefaults(): void
    {
        $db = Database::getInstance();
        if ($db->count('hero_slides') > 0) return;

        $rows = [
            [
                'eyebrow'=>'Dijitalde tek merkez',
                'title'=>'Yazılım, dijital ajans ve sosyal medya çözümleri.',
                'description'=>'Hazır ve özel yazılım, SEO, reklam yönetimi ve sosyal medya hizmetlerini NetVera çatısı altında yönetin.',
                'category_label'=>'NetVera Teknoloji Yazılım',
                'category_url'=>'/kategoriler',
                'category_icon'=>'sparkles',
                'primary_button_text'=>'Hizmetleri Görüntüle',
                'primary_button_url'=>'/kategoriler',
                'primary_button_icon'=>'arrow-right',
                'secondary_button_text'=>'Hazır Yazılımlar',
                'secondary_button_url'=>'/hazir-scriptler',
                'secondary_button_icon'=>'code',
                'desktop_image'=>'/assets/img/hero-woman-cutout.png',
                'image_alt'=>'NetVera dijital hizmetler',
                'background_start'=>'#111d56',
                'background_end'=>'#6d28d9',
                'accent_color'=>'#8b5cf6',
                'stat_text'=>'Tüm dijital çözümler',
                'quick_links'=>json_encode([
                    ['label'=>'Instagram','url'=>'/kategoriler/grup/social','icon'=>'instagram','color'=>'#ff2d8d'],
                    ['label'=>'Yazılım','url'=>'/hazir-scriptler','icon'=>'code','color'=>'#5b6cff'],
                    ['label'=>'SEO','url'=>'/kategoriler/grup/marketing','icon'=>'search','color'=>'#17b6d4'],
                ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                'autoplay_delay'=>6500,'sort_order'=>10,'status'=>'active'
            ],
            [
                'eyebrow'=>'Yazılım çözümleri',
                'title'=>'Hazır scriptlerden özel geliştirmeye güçlü altyapı.',
                'description'=>'Sektörel hazır yazılımları keşfedin veya işletmenize özel web ve yazılım çözümleri oluşturun.',
                'category_label'=>'Ajans & Yazılım',
                'category_url'=>'/hazir-scriptler',
                'category_icon'=>'monitor',
                'primary_button_text'=>'Yazılımları Görüntüle',
                'primary_button_url'=>'/hazir-scriptler',
                'primary_button_icon'=>'monitor',
                'secondary_button_text'=>'Ajans Hizmetleri',
                'secondary_button_url'=>'/kategoriler/grup/agency',
                'secondary_button_icon'=>'layers',
                'background_start'=>'#071b3e',
                'background_end'=>'#0c6fb8',
                'accent_color'=>'#1fc7ff',
                'stat_text'=>'Hazır ve özel çözümler',
                'quick_links'=>json_encode([
                    ['label'=>'Web Site','url'=>'/kategoriler/grup/agency','icon'=>'globe','color'=>'#2c7cff'],
                    ['label'=>'E-Ticaret','url'=>'/kategoriler/grup/agency','icon'=>'store','color'=>'#16a879'],
                    ['label'=>'Mobil','url'=>'/kategoriler/grup/agency','icon'=>'mobile-app','color'=>'#7c5cff'],
                ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                'autoplay_delay'=>7000,'sort_order'=>20,'status'=>'active'
            ],
            [
                'eyebrow'=>'Büyüme ve görünürlük',
                'title'=>'SEO, reklam ve sosyal medya ile markanızı büyütün.',
                'description'=>'Arama motoru görünürlüğü, performans reklamları ve sosyal medya etkileşimi için ölçülebilir hizmetler.',
                'category_label'=>'SEO & Dijital Pazarlama',
                'category_url'=>'/kategoriler/grup/marketing',
                'category_icon'=>'trending-up',
                'primary_button_text'=>'Çözümleri İncele',
                'primary_button_url'=>'/kategoriler/grup/marketing',
                'primary_button_icon'=>'arrow-right',
                'secondary_button_text'=>'Sosyal Medya',
                'secondary_button_url'=>'/kategoriler/grup/social',
                'secondary_button_icon'=>'share-2',
                'background_start'=>'#3a124f',
                'background_end'=>'#d24a79',
                'accent_color'=>'#ff4fa3',
                'stat_text'=>'SEO, Ads ve sosyal medya',
                'quick_links'=>json_encode([
                    ['label'=>'Google','url'=>'/kategoriler/grup/marketing','icon'=>'google','color'=>'#4285f4'],
                    ['label'=>'Instagram','url'=>'/kategoriler/grup/social','icon'=>'instagram','color'=>'#f02d85'],
                    ['label'=>'TikTok','url'=>'/kategoriler/grup/social','icon'=>'tiktok','color'=>'#111827'],
                ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                'autoplay_delay'=>6200,'sort_order'=>30,'status'=>'active'
            ],
        ];
        foreach ($rows as $row) $db->insert('hero_slides', $row);
    }

    public static function all(bool $activeOnly = false): array
    {
        self::ensureSchema();
        $where = $activeOnly
            ? "WHERE status='active' AND (starts_at IS NULL OR starts_at<=NOW()) AND (ends_at IS NULL OR ends_at>=NOW())"
            : "";
        $rows = Database::getInstance()->fetchAll("SELECT * FROM hero_slides {$where} ORDER BY sort_order ASC, id ASC");
        foreach ($rows as &$row) {
            $decoded = json_decode((string)($row['quick_links'] ?? '[]'), true);
            $row['quick_links'] = is_array($decoded) ? $decoded : [];
        }
        unset($row);
        return $rows;
    }

    public static function find(int $id): ?array
    {
        self::ensureSchema();
        $row = Database::getInstance()->fetch("SELECT * FROM hero_slides WHERE id=?", [$id]);
        if (!$row) return null;
        $decoded = json_decode((string)($row['quick_links'] ?? '[]'), true);
        $row['quick_links'] = is_array($decoded) ? $decoded : [];
        return $row;
    }

    public static function imageUrl(?string $path): string
    {
        $path = trim((string)$path);
        if ($path === '') return '';
        if (str_starts_with($path, '/assets/') || str_starts_with($path, '/uploads/')) return $path;
        if (preg_match('~^https://~i', $path)) return $path;
        return upload_url($path);
    }
}
