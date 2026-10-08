<?php
namespace App\Services;

use App\Core\Database;

/**
 * Shared header navigation. Visible links are validated against real categories.
 * The admin only stores preferences; no schema migration is required.
 */
final class NavigationService
{
    private const KEY = 'navigation_items_v1';

    public static function items(bool $includeHidden = false): array
    {
        $saved = json_decode(setting(self::KEY, '{}'), true);
        if (!is_array($saved)) $saved = [];

        $enabledGroups = [];
        foreach (CatalogMenuService::groups() as $group) $enabledGroups[$group['key']] = true;

        $definitions = [
            ['key'=>'group_social','label'=>'Sosyal Medya Hizmetleri','url'=>'/kategoriler?grup=social','default_enabled'=>true,'sort'=>20,'available'=>isset($enabledGroups['social'])],
            ['key'=>'group_agency','label'=>'Ajans & Yazılım','url'=>'/kategoriler?grup=agency','default_enabled'=>true,'sort'=>30,'available'=>isset($enabledGroups['agency'])],
            ['key'=>'group_marketing','label'=>'SEO & Dijital Pazarlama','url'=>'/kategoriler?grup=marketing','default_enabled'=>true,'sort'=>40,'available'=>isset($enabledGroups['marketing'])],
            ['key' => 'all', 'label' => 'Tüm Hizmetler', 'url' => '/kategoriler', 'default_enabled' => true, 'sort' => 10, 'available' => true],
            ['key' => 'blog', 'label' => 'Blog', 'url' => '/blog', 'default_enabled' => true, 'sort' => 100, 'available' => true],
            ['key' => 'faq', 'label' => 'SSS', 'url' => '/sss', 'default_enabled' => false, 'sort' => 110, 'available' => true],
            ['key' => 'contact', 'label' => 'İletişim', 'url' => '/iletisim', 'default_enabled' => false, 'sort' => 120, 'available' => true],
        ];

        $priorities = [
            'google-hizmetleri' => [20, 'Google'],
            'instagram-hizmetleri' => [30, 'Instagram'],
            'tiktok-hizmetleri' => [40, 'TikTok'],
            'youtube-hizmetleri' => [50, 'YouTube'],
            'web-site-hizmetleri' => [60, 'Web Site'],
        ];

        try {
            $categories = Database::getInstance()->fetchAll(
                "SELECT id, name, slug, status, sort_order FROM categories
                 WHERE parent_id IS NULL ORDER BY sort_order ASC, id ASC"
            );
            foreach ($categories as $cat) {
                $priority = $priorities[$cat['slug']] ?? null;
                $definitions[] = [
                    'key' => 'category_' . (int)$cat['id'],
                    'label' => $priority[1] ?? $cat['name'],
                    'url' => '/kategori/' . $cat['slug'],
                    'default_enabled' => $priority !== null,
                    'sort' => $priority[0] ?? (70 + (int)$cat['sort_order']),
                    'available' => $cat['status'] === 'active',
                ];
            }
        } catch (\Throwable $e) {
            error_log('Navigation categories: ' . $e->getMessage());
        }

        $items = [];
        foreach ($definitions as $item) {
            $pref = $saved[$item['key']] ?? [];
            if (!is_array($pref)) $pref = [];
            $item['enabled'] = array_key_exists('enabled', $pref)
                ? (bool)$pref['enabled']
                : $item['default_enabled'];
            $item['sort'] = isset($pref['sort'])
                ? max(0, min(9999, (int)$pref['sort']))
                : $item['sort'];
            if (isset($pref['label']) && is_string($pref['label'])) {
                $label = trim($pref['label']);
                if ($label !== '') $item['label'] = mb_substr($label, 0, 40);
            }
            if ($includeHidden || ($item['enabled'] && $item['available'])) $items[] = $item;
        }
        usort($items, static fn($a, $b) =>
            ($a['sort'] <=> $b['sort']) ?: strcmp($a['key'], $b['key'])
        );
        return $items;
    }

    public static function save(array $postedEnabled, array $postedOrder, array $postedLabels): void
    {
        $allowed = self::items(true);
        $settings = [];
        foreach ($allowed as $item) {
            $key = $item['key'];
            $label = trim((string)($postedLabels[$key] ?? $item['label']));
            $settings[$key] = [
                'enabled' => in_array($key, $postedEnabled, true),
                'sort' => max(0, min(9999, (int)($postedOrder[$key] ?? $item['sort']))),
                'label' => mb_substr($label !== '' ? $label : $item['label'], 0, 40),
            ];
        }
        SiteConfigService::getInstance()->set(
            self::KEY, json_encode($settings, JSON_UNESCAPED_UNICODE), 'navigation'
        );
    }

    public static function reset(): void
    {
        SiteConfigService::getInstance()->set(self::KEY, '{}', 'navigation');
    }
}
