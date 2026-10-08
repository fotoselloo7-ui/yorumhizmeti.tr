<?php
namespace App\Services;

final class ReferencesService
{
    private const KEY = 'showcase_references_v1';

    public static function all(bool $activeOnly = false): array
    {
        $rows = json_decode(setting(self::KEY, '[]'), true);
        if (!is_array($rows)) return [];
        $list = [];
        foreach ($rows as $row) {
            if (!is_array($row) || empty($row['id']) || empty($row['title'])) continue;
            if ($activeOnly && ($row['status'] ?? '') !== 'active') continue;
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
