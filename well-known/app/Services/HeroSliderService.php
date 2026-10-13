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
                secondary_button_bg VARCHAR(40) DEFAULT 'rgba(255,255,255,.12)',
                secondary_button_text_color VARCHAR(20) DEFAULT '#ffffff',
                desktop_image VARCHAR(255) DEFAULT NULL,
                mobile_image VARCHAR(255) DEFAULT NULL,
                image_alt VARCHAR(255) DEFAULT NULL,
                background_start VARCHAR(20) DEFAULT '#13255b',
                background_end VARCHAR(20) DEFAULT '#6d28d9',
                overlay_color VARCHAR(40) DEFAULT 'rgba(7,16,48,.34)',
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
        self::applyBundledCategoryAssets();
        self::upgradeBundledArtworkPaths();
    }

    private static function seedDefaults(): void
    {
        $db = Database::getInstance();
        if ($db->count('hero_slides') > 0) return;

        $rows = [
            [
                'eyebrow'=>'Sosyal medyada güçlü büyüme',
                'title'=>'Sosyal medya hizmetleriyle markanızı büyütün.',
                'description'=>'Instagram, TikTok, YouTube ve diğer platformlarda görünürlük, etkileşim ve hedef kitle büyümesi için profesyonel sosyal medya çözümleri.',
                'category_label'=>'Sosyal Medya Hizmetleri',
                'category_url'=>'/kategoriler/grup/social',
                'category_icon'=>'heart',
                'primary_button_text'=>'Sosyal Medya Hizmetleri',
                'primary_button_url'=>'/kategoriler/grup/social',
                'primary_button_icon'=>'arrow-right',
                'secondary_button_text'=>'Tüm Hizmetler',
                'secondary_button_url'=>'/kategoriler',
                'secondary_button_icon'=>'grid',
                'desktop_image'=>'/assets/img/slider/social-media-v107.webp',
                'mobile_image'=>'/assets/img/slider/social-media-mobile-v107.webp',
                'image_alt'=>'NetVera sosyal medya hizmetleri, içerik etkileşimi ve dijital büyüme çözümleri',
                'background_start'=>'#20104f',
                'background_end'=>'#a32078',
                'overlay_color'=>'rgba(24,10,65,.52)',
                'accent_color'=>'#ff4fa3',
                'stat_text'=>'Instagram, TikTok, YouTube ve daha fazlası',
                'quick_links'=>json_encode([
                    ['label'=>'Instagram','url'=>'/kategoriler/grup/social','icon'=>'instagram','color'=>'#f02d85'],
                    ['label'=>'TikTok','url'=>'/kategoriler/grup/social','icon'=>'tiktok','color'=>'#111827'],
                    ['label'=>'YouTube','url'=>'/kategoriler/grup/social','icon'=>'youtube','color'=>'#ff0033'],
                ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                'autoplay_delay'=>6500,'sort_order'=>10,'status'=>'active'
            ],
            [
                'eyebrow'=>'Ajans ve yazılım çözümleri',
                'title'=>'Hazır scriptlerden özel geliştirmeye güçlü altyapı.',
                'description'=>'Web sitesi, e-ticaret, mobil uygulama, hazır yazılım ve özel geliştirme ihtiyaçlarınız için ölçeklenebilir NetVera çözümlerini keşfedin.',
                'category_label'=>'Ajans & Yazılım',
                'category_url'=>'/kategoriler/grup/agency',
                'category_icon'=>'layers',
                'primary_button_text'=>'Ajans & Yazılımı İncele',
                'primary_button_url'=>'/kategoriler/grup/agency',
                'primary_button_icon'=>'arrow-right',
                'secondary_button_text'=>'Hazır Yazılımlar',
                'secondary_button_url'=>'/hazir-scriptler',
                'secondary_button_icon'=>'code',
                'desktop_image'=>'/assets/img/slider/agency-software-v107.webp',
                'mobile_image'=>'/assets/img/slider/agency-software-mobile-v107.webp',
                'image_alt'=>'NetVera ajans ve yazılım hizmetleri, web sitesi, uygulama ve özel yazılım geliştirme',
                'background_start'=>'#071b3e',
                'background_end'=>'#2140a8',
                'overlay_color'=>'rgba(5,17,54,.55)',
                'accent_color'=>'#1fc7ff',
                'stat_text'=>'Web, e-ticaret, mobil ve hazır yazılım',
                'quick_links'=>json_encode([
                    ['label'=>'Web Site','url'=>'/kategoriler/grup/agency','icon'=>'globe','color'=>'#2c7cff'],
                    ['label'=>'E-Ticaret','url'=>'/kategoriler/grup/agency','icon'=>'store','color'=>'#16a879'],
                    ['label'=>'Hazır Yazılım','url'=>'/hazir-scriptler','icon'=>'code','color'=>'#7c5cff'],
                ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                'autoplay_delay'=>7000,'sort_order'=>20,'status'=>'active'
            ],
            [
                'eyebrow'=>'Arama görünürlüğü ve ölçülebilir büyüme',
                'title'=>'SEO, GEO, AIO ve dijital pazarlama ile öne çıkın.',
                'description'=>'Arama motoru görünürlüğü, yapay zekâ odaklı içerik optimizasyonu, yerel keşfedilebilirlik ve performans reklamlarıyla markanızı ölçülebilir şekilde büyütün.',
                'category_label'=>'SEO & Dijital Pazarlama',
                'category_url'=>'/kategoriler/grup/marketing',
                'category_icon'=>'trending-up',
                'primary_button_text'=>'SEO & Pazarlamayı İncele',
                'primary_button_url'=>'/kategoriler/grup/marketing',
                'primary_button_icon'=>'arrow-right',
                'secondary_button_text'=>'Tüm Hizmetler',
                'secondary_button_url'=>'/kategoriler',
                'secondary_button_icon'=>'grid',
                'desktop_image'=>'/assets/img/slider/seo-digital-marketing-v107.webp',
                'mobile_image'=>'/assets/img/slider/seo-digital-marketing-mobile-v107.webp',
                'image_alt'=>'NetVera SEO, GEO, AIO, yerel arama ve dijital pazarlama analiz çözümleri',
                'background_start'=>'#10134f',
                'background_end'=>'#6d2ca5',
                'overlay_color'=>'rgba(7,16,58,.55)',
                'accent_color'=>'#22d3ee',
                'stat_text'=>'SEO, GEO, AIO, Ads ve yerel görünürlük',
                'quick_links'=>json_encode([
                    ['label'=>'SEO','url'=>'/kategoriler/grup/marketing','icon'=>'search','color'=>'#4f7cff'],
                    ['label'=>'Yerel Arama','url'=>'/kategoriler/grup/marketing','icon'=>'map-pin','color'=>'#22c7a8'],
                    ['label'=>'Dijital Reklam','url'=>'/kategoriler/grup/marketing','icon'=>'trending-up','color'=>'#9b5cff'],
                ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                'autoplay_delay'=>6200,'sort_order'=>30,'status'=>'active'
            ],
        ];
        foreach ($rows as $row) $db->insert('hero_slides', $row);
    }

    private static function applyBundledCategoryAssets(): void
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll("SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC LIMIT 20");

        foreach ($rows as $row) {
            $current = trim((string)($row['desktop_image'] ?? ''));
            $isBundledCandidate = $current === '' || $current === '/assets/img/hero-woman-cutout.png';
            if (!$isBundledCandidate) continue;

            $label = trim((string)($row['category_label'] ?? ''));
            $order = (int)($row['sort_order'] ?? 0);
            $data = null;

            if (($label === 'NetVera Teknoloji Yazılım' || $order === 10) && $current === '/assets/img/hero-woman-cutout.png') {
                $data = [
                    'eyebrow'=>'Sosyal medyada güçlü büyüme',
                    'title'=>'Sosyal medya hizmetleriyle markanızı büyütün.',
                    'description'=>'Instagram, TikTok, YouTube ve diğer platformlarda görünürlük, etkileşim ve hedef kitle büyümesi için profesyonel sosyal medya çözümleri.',
                    'category_label'=>'Sosyal Medya Hizmetleri',
                    'category_url'=>'/kategoriler/grup/social',
                    'category_icon'=>'heart',
                    'primary_button_text'=>'Sosyal Medya Hizmetleri',
                    'primary_button_url'=>'/kategoriler/grup/social',
                    'secondary_button_text'=>'Tüm Hizmetler',
                    'secondary_button_url'=>'/kategoriler',
                    'secondary_button_icon'=>'grid',
                    'desktop_image'=>'/assets/img/slider/social-media-v107.webp',
                'mobile_image'=>'/assets/img/slider/social-media-mobile-v107.webp',
                    'image_alt'=>'NetVera sosyal medya hizmetleri, içerik etkileşimi ve dijital büyüme çözümleri',
                    'background_start'=>'#20104f',
                    'background_end'=>'#a32078',
                    'overlay_color'=>'rgba(24,10,65,.52)',
                    'accent_color'=>'#ff4fa3',
                    'stat_text'=>'Instagram, TikTok, YouTube ve daha fazlası',
                    'quick_links'=>json_encode([
                        ['label'=>'Instagram','url'=>'/kategoriler/grup/social','icon'=>'instagram','color'=>'#f02d85'],
                        ['label'=>'TikTok','url'=>'/kategoriler/grup/social','icon'=>'tiktok','color'=>'#111827'],
                        ['label'=>'YouTube','url'=>'/kategoriler/grup/social','icon'=>'youtube','color'=>'#ff0033'],
                    ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ];
            } elseif ($current === '' && $label === 'Ajans & Yazılım' && trim((string)($row['stat_text'] ?? '')) === 'Hazır ve özel çözümler') {
                $data = [
                    'desktop_image'=>'/assets/img/slider/agency-software-v107.webp',
                'mobile_image'=>'/assets/img/slider/agency-software-mobile-v107.webp',
                    'image_alt'=>'NetVera ajans ve yazılım hizmetleri, web sitesi, uygulama ve özel yazılım geliştirme',
                    'background_start'=>'#071b3e',
                    'background_end'=>'#2140a8',
                    'overlay_color'=>'rgba(5,17,54,.55)',
                    'accent_color'=>'#1fc7ff',
                    'stat_text'=>'Web, e-ticaret, mobil ve hazır yazılım',
                ];
            } elseif ($current === '' && $label === 'SEO & Dijital Pazarlama' && trim((string)($row['stat_text'] ?? '')) === 'SEO, Ads ve sosyal medya') {
                $data = [
                    'title'=>'SEO, GEO, AIO ve dijital pazarlama ile öne çıkın.',
                    'description'=>'Arama motoru görünürlüğü, yapay zekâ odaklı içerik optimizasyonu, yerel keşfedilebilirlik ve performans reklamlarıyla markanızı ölçülebilir şekilde büyütün.',
                    'desktop_image'=>'/assets/img/slider/seo-digital-marketing-v107.webp',
                'mobile_image'=>'/assets/img/slider/seo-digital-marketing-mobile-v107.webp',
                    'image_alt'=>'NetVera SEO, GEO, AIO, yerel arama ve dijital pazarlama analiz çözümleri',
                    'background_start'=>'#10134f',
                    'background_end'=>'#6d2ca5',
                    'overlay_color'=>'rgba(7,16,58,.55)',
                    'accent_color'=>'#22d3ee',
                    'primary_button_text'=>'SEO & Pazarlamayı İncele',
                    'primary_button_url'=>'/kategoriler/grup/marketing',
                    'primary_button_icon'=>'arrow-right',
                    'secondary_button_text'=>'Tüm Hizmetler',
                    'secondary_button_url'=>'/kategoriler',
                    'secondary_button_icon'=>'grid',
                    'stat_text'=>'SEO, GEO, AIO, Ads ve yerel görünürlük',
                    'quick_links'=>json_encode([
                        ['label'=>'SEO','url'=>'/kategoriler/grup/marketing','icon'=>'search','color'=>'#4f7cff'],
                        ['label'=>'Yerel Arama','url'=>'/kategoriler/grup/marketing','icon'=>'map-pin','color'=>'#22c7a8'],
                        ['label'=>'Dijital Reklam','url'=>'/kategoriler/grup/marketing','icon'=>'trending-up','color'=>'#9b5cff'],
                    ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ];
            }

            if ($data) {
                $db->update('hero_slides', $data, 'id=?', [(int)$row['id']]);
            }
        }
    }

    private static function upgradeBundledArtworkPaths(): void
    {
        $db = Database::getInstance();

        // Keep the admin database aligned with the HD artwork bundled in the
        // repository. Only known NetVera bundle paths are upgraded; custom
        // administrator uploads are never overwritten.
        $groups = [
            [
                'desktop'=>'/assets/img/slider/social-media-v107.webp',
                'mobile'=>'/assets/img/slider/social-media-mobile-v107.webp',
                'legacy'=>[
                    '/assets/img/slider/social-media-v95.webp',
                    '/assets/img/slider/social-media-v96.webp',
                    '/assets/img/slider/social-media-v103.webp',
                    '/assets/img/slider/social-media-hd-v105.webp',
                    '/assets/img/slider/social-media-v107.webp',
                ],
            ],
            [
                'desktop'=>'/assets/img/slider/agency-software-v107.webp',
                'mobile'=>'/assets/img/slider/agency-software-mobile-v107.webp',
                'legacy'=>[
                    '/assets/img/slider/agency-software-v95.webp',
                    '/assets/img/slider/agency-software-v96.webp',
                    '/assets/img/slider/agency-software-v103.webp',
                    '/assets/img/slider/agency-software-hd-v105.webp',
                    '/assets/img/slider/agency-software-v107.webp',
                ],
            ],
            [
                'desktop'=>'/assets/img/slider/seo-digital-marketing-v107.webp',
                'mobile'=>'/assets/img/slider/seo-digital-marketing-mobile-v107.webp',
                'legacy'=>[
                    '/assets/img/slider/seo-digital-marketing-v95.webp',
                    '/assets/img/slider/seo-digital-marketing-v96.webp',
                    '/assets/img/slider/seo-digital-marketing-v103.webp',
                    '/assets/img/slider/seo-digital-marketing-hd-v105.webp',
                    '/assets/img/slider/seo-digital-marketing-v107.webp',
                ],
            ],
        ];

        foreach ($groups as $group) {
            foreach ($group['legacy'] as $legacy) {
                $db->query(
                    "UPDATE hero_slides
                     SET desktop_image=?,
                         mobile_image=CASE
                           WHEN mobile_image IS NULL OR mobile_image='' OR mobile_image LIKE '/assets/img/slider/%'
                           THEN ?
                           ELSE mobile_image
                         END
                     WHERE desktop_image=?",
                    [$group['desktop'], $group['mobile'], $legacy]
                );
            }
        }
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
