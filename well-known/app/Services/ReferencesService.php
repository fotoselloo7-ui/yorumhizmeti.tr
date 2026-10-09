<?php
namespace App\Services;

/**
 * Reference portfolio backed by the existing site-settings store.
 * Existing v1 records are read without a migration or URL change.
 */
final class ReferencesService
{
    private const KEY = 'showcase_references_v1';

    /** Only the two requested public reference groups; service tags match the site taxonomy. */
    public static function groups(): array
    {
        return [
            'agency' => [
                'label' => 'Ajans & Yazılım', 'icon' => 'layers',
                'services' => [
                    'web-site' => 'Web Sitesi',
                    'software' => 'Özel Yazılım',
                    'ecommerce' => 'E-Ticaret',
                    'mobile' => 'Mobil Uygulama',
                    'design' => 'Grafik Tasarım',
                ],
            ],
            'marketing' => [
                'label' => 'SEO & Dijital', 'icon' => 'trending-up',
                'services' => [
                    'social-management' => 'Sosyal Medya Yönetimi',
                    'seo' => 'SEO Çalışmaları',
                    'advertising' => 'Dijital Reklam',
                    'local' => 'Yerel İşletme',
                    'reputation' => 'İtibar Yönetimi',
                ],
            ],
        ];
    }

    /** Media is a standard website/image card, or an Instagram public embed. */
    public static function mediaTypes(): array
    {
        return [
            'website' => 'Web Sitesi / Yazılım',
            'image' => 'Görsel / Post',
            'instagram_post' => 'Instagram Gönderisi',
            'instagram_reel' => 'Instagram Reels',
        ];
    }

    public static function normalizedGroup(array $row): string
    {
        $group = (string)($row['group'] ?? '');
        return isset(self::groups()[$group]) ? $group : 'agency';
    }

    public static function normalizedService(array $row): string
    {
        $group = self::normalizedGroup($row);
        $service = (string)($row['service'] ?? '');
        if (isset(self::groups()[$group]['services'][$service])) return $service;
        return $group === 'marketing' ? 'seo' : 'web-site';
    }

    /**
     * Up to two unique category placements per reference.
     * Reads old single-category rows without changing their IDs or media.
     */
    public static function normalizedPlacements(array $row): array
    {
        $groups = self::groups();
        $placements = [];
        $candidateRows = $row['placements'] ?? [];
        if (!is_array($candidateRows) || !$candidateRows) {
            $candidateRows = [[
                'group' => self::normalizedGroup($row),
                'service' => self::normalizedService($row),
            ]];
        }
        foreach ($candidateRows as $candidate) {
            if (!is_array($candidate)) continue;
            $group = (string)($candidate['group'] ?? '');
            $service = (string)($candidate['service'] ?? '');
            if (!isset($groups[$group]['services'][$service])) continue;
            $key = $group.':'.$service;
            $placements[$key] = ['group'=>$group, 'service'=>$service];
            if (count($placements) >= 2) break;
        }
        if (!$placements) {
            $group = self::normalizedGroup($row);
            $placements[$group.':'.self::normalizedService($row)] = [
                'group'=>$group,
                'service'=>self::normalizedService($row)
            ];
        }
        return array_values($placements);
    }

    public static function normalizedMedia(array $row): string
    {
        $type = (string)($row['media_type'] ?? '');
        return isset(self::mediaTypes()[$type]) ? $type : 'website';
    }

    /**
     * Accept only public canonical Instagram post/reel paths.
     * Link previews and media loads happen at Instagram, not through our backend.
     */
    public static function instagramEmbed(string $url, string $kind): ?string
    {
        if (!in_array($kind, ['instagram_post','instagram_reel'], true)) return null;
        if (!filter_var($url, FILTER_VALIDATE_URL)) return null;
        $parts = parse_url($url);
        if (!is_array($parts) || strtolower((string)($parts['scheme'] ?? '')) !== 'https') return null;
        if (!in_array(strtolower((string)($parts['host'] ?? '')), ['instagram.com','www.instagram.com'], true)) return null;
        $path = (string)($parts['path'] ?? '');
        if (!preg_match('~^/(p|reel|reels)/([A-Za-z0-9_-]{5,64})/?$~D', $path, $m)) return null;
        if ($kind === 'instagram_post' && $m[1] !== 'p') return null;
        if ($kind === 'instagram_reel' && !in_array($m[1], ['reel','reels'], true)) return null;
        $kindPath = $kind === 'instagram_reel' ? 'reel' : 'p';
        return 'https://www.instagram.com/'.$kindPath.'/'.$m[2].'/embed/';
    }

    public static function all(bool $activeOnly = false): array
    {
        $rows = json_decode(setting(self::KEY, '[]'), true);
        if (!is_array($rows)) return [];
        $list = [];
        foreach ($rows as $row) {
            if (!is_array($row) || empty($row['id']) || empty($row['title'])) continue;
            if ($activeOnly && ($row['status'] ?? '') !== 'active') continue;
            $row['placements'] = self::normalizedPlacements($row);
            $row['group'] = $row['placements'][0]['group'];
            $row['service'] = $row['placements'][0]['service'];
            $row['media_type'] = self::normalizedMedia($row);
            $row['logo'] = (string)($row['logo'] ?? '');
            $list[] = $row;
        }
        usort($list, static fn($a,$b)=>
            ((int)($a['sort_order']??0) <=> (int)($b['sort_order']??0))
                ?: strcmp((string)$a['id'],(string)$b['id']));
        return array_slice($list, 0, 36);
    }

    public static function save(array $rows): void
    {
        // Settings field is TEXT: keep the module finite and predictable.
        $rows = array_slice($rows, 0, 36);
        $json = json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!is_string($json) || strlen($json) > 60000) {
            throw new \RuntimeException('Referans verisi çok büyük.');
        }
        SiteConfigService::getInstance()->set(self::KEY, $json, 'homepage');
    }
}
