<?php
namespace App\Services;

use App\Core\Database;

/** Fixed social service packages. Metadata uses the existing admin settings store. */
final class SocialPackageService
{
    private static ?array $categories = null;
    private static ?array $options = null;
    private static array $info = [];

    public static function category(int $id): ?array
    {
        if (self::$categories === null) {
            self::$categories = array_column(Database::getInstance()->fetchAll('SELECT id,parent_id,name,slug FROM categories'), null, 'id');
        }
        return self::$categories[$id] ?? null;
    }

    public static function isSocialCategory(int $id): bool
    {
        $category = self::category($id);
        $seen = [];
        while ($category && !empty($category['parent_id'])) {
            if (isset($seen[$category['id']])) return false;
            $seen[$category['id']] = true;
            $category = self::category((int)$category['parent_id']);
        }
        // Classify the root, not product copy: Instagram Ads belongs to marketing.
        return $category !== null && CatalogMenuService::bucket($category) === 'social';
    }

    public static function info(array $package): ?array
    {
        if (!self::isSocialCategory((int)($package['category_id'] ?? 0))) return null;
        $id = (int)($package['id'] ?? 0);
        if (isset(self::$info[$id])) return self::$info[$id];
        if (self::$options === null) {
            self::$options = [];
            foreach (Database::getInstance()->fetchAll("SELECT setting_key,setting_value FROM settings WHERE setting_key LIKE 'social_package_%'") as $row) {
                $value = json_decode((string)$row['setting_value'], true);
                if (is_array($value)) self::$options[(int)substr($row['setting_key'],15)] = $value;
            }
        }
        $category = self::category((int)$package['category_id']);
        $package['category_name'] = $category['name'];
        return self::$info[$id] = self::describe($package, self::$options[$id] ?? [], SmmCatalogService::mapping($id));
    }

    /** Pure interpretation of explicit metadata / provider mapping / legacy copy. */
    public static function describe(array $package, array $options = [], ?array $mapping = null): array
    {
        $unit = trim((string)($options['unit'] ?? ''));
        $legacyQuantity = null;
        $legacyUnit = '';
        $summary = preg_replace('/\x{0307}/u','',mb_strtolower((string)($package['short_description'] ?? ''),'UTF-8'));
        if (preg_match('/^\s*([1-9][0-9]*(?:[., ][0-9]{3})*)\s+([\p{L}\p{M}][\p{L}\p{M} -]{0,39}?)\s+için\b/u', $summary, $match)) {
            $legacyQuantity = (int)preg_replace('/[., ]/', '', $match[1]);
            $legacyUnit = mb_strtolower($match[2], 'UTF-8');
            if ($legacyUnit === 'üyesi') $legacyUnit = 'üye';
        }
        $quantity = (int)($options['quantity'] ?? $mapping['fulfillment_quantity'] ?? $legacyQuantity ?? 0);
        $title = trim((string)(($package['name'] ?? '') ?: ($package['seo_title'] ?? '')));
        $source = mb_strtolower($title.' '.($package['slug'] ?? '').' '.($package['category_name'] ?? ''), 'UTF-8');
        if ($unit === '') $unit = $legacyUnit;
        if ($unit === '') {
            foreach (['yorum beğeni'=>'yorum beğeni','takipçi'=>'takipçi','takipci'=>'takipçi','beğeni'=>'beğeni','begeni'=>'beğeni','abone'=>'abone','izlenme'=>'izlenme','yorum'=>'yorum','kaydetme'=>'kaydetme','üye'=>'üye','dinlenme'=>'dinlenme','ziyaret'=>'ziyaret'] as $needle=>$value) {
                if (str_contains($source, $needle)) { $unit = $value; break; }
            }
        }
        $service = trim((string)($options['service'] ?? ''));
        if ($service === '') {
            $service = trim((string)($package['category_name'] ?? $title));
            if ($unit === 'takipçi') {
                foreach (['bot'=>'Bot Takipçi','düşmeyen'=>'Düşmeyen Takipçi','dusmeyen'=>'Düşmeyen Takipçi','türk'=>'Türk Takipçi','turk'=>'Türk Takipçi','yabancı'=>'Yabancı Takipçi','yabanci'=>'Yabancı Takipçi','bayan'=>'Bayan Takipçi','erkek'=>'Erkek Takipçi','kurumsal'=>'Kurumsal Takipçi'] as $needle=>$label) {
                    if (str_contains($source, $needle)) {
                        $platform = preg_split('/\s+/', $service)[0] ?? '';
                        $service = trim($platform.' '.$label);
                        break;
                    }
                }
            }
        }
        $key = (int)($package['category_id'] ?? 0).'-'.slugify($service);
        $label = $quantity > 0 ? number_format($quantity,0,',','.').' '.$unit : 'Miktar belirtilmemiş';
        return ['service'=>$service,'key'=>$key,'quantity'=>$quantity,'unit'=>$unit,'label'=>$label,
            'title'=>$service.' — '.$label,'configured'=>!empty($options),
            'mapping_matches'=>$mapping === null || $quantity === (int)$mapping['fulfillment_quantity']];
    }

    public static function cartQuantity(array $package, int $requested): int
    {
        if (self::isSocialCategory((int)$package['category_id'])) return 1;
        $min = max(1,(int)($package['min_quantity'] ?? 1));
        $max = max($min,(int)($package['max_quantity'] ?? 1));
        return max($min,min($max,$requested));
    }

    public static function assertPurchasable(array $package): void
    {
        $info = self::info($package);
        if ($info === null) return;
        if ($info['quantity'] < 1 || $info['unit'] === '') throw new \RuntimeException('Bu paketin teslim miktarı henüz tanımlanmamış. Lütfen destek ekibine ulaşın.');
        if (!$info['mapping_matches']) throw new \RuntimeException('Paketin teslim miktarı ile sağlayıcı eşlemesi uyuşmuyor. Bu paket geçici olarak siparişe kapalı.');
    }

    /** Server-owned snapshot, never sourced from customer POST fields. */
    public static function snapshot(array $package): array
    {
        $info = self::info($package);
        if ($info === null) return [];
        self::assertPurchasable($package);
        return [
            ['field_key'=>'nv_social_service','field_label'=>'Hizmet Türü','value'=>$info['service']],
            ['field_key'=>'nv_social_quantity','field_label'=>'Teslim Edilecek Miktar','value'=>$info['label']],
        ];
    }

    public static function catalog(array $packages, array $category, string $selected): array
    {
        $groups = [];
        foreach ($packages as $package) {
            $info = self::info($package);
            if ($info === null) continue;
            $key = $info['key'];
            $price = !empty($package['discount_price']) && $package['discount_price'] < $package['price'] ? (float)$package['discount_price'] : (float)$package['price'];
            $cat = self::category((int)$package['category_id']);
            if (!isset($groups[$key])) $groups[$key] = ['key'=>$key,'name'=>$info['service'],'price'=>$price,'packages'=>[],
                'url'=>'/kategori/'.rawurlencode($cat['slug']).'?hizmet='.rawurlencode($key).'#packages'];
            $groups[$key]['price'] = min($price,$groups[$key]['price']);
            $groups[$key]['packages'][] = $package;
        }
        $chosen = $groups[$selected] ?? null;
        return ['groups'=>array_values($groups),'selected'=>$chosen,'requested'=>$selected,
            'show_services'=>$selected === '' && (empty($category['parent_id']) || count($groups)>1),
            'packages'=>$selected !== '' ? ($chosen['packages'] ?? []) : $packages];
    }

    public static function validateOptions(array $input): array
    {
        $quantity = filter_var($input['social_quantity'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>100000000]]);
        $unit = trim((string)($input['social_unit'] ?? ''));
        $service = trim(strip_tags((string)($input['social_service'] ?? '')));
        if ($quantity === false || $quantity === null || $service === '' || mb_strlen($service)>120 || !preg_match('/^[\p{L}\p{M} -]{1,40}$/u',$unit)) {
            throw new \RuntimeException('Sosyal medya paketinde hizmet türü, teslim miktarı (1–100.000.000) ve birim zorunludur.');
        }
        return ['quantity'=>$quantity,'unit'=>$unit,'service'=>$service];
    }

    public static function save(int $id, array $input): void
    {
        $package = Database::getInstance()->fetch('SELECT id,category_id FROM packages WHERE id=?',[$id]);
        if (!$package || !self::isSocialCategory((int)$package['category_id'])) return;
        $options = self::validateOptions($input);
        $mapping = SmmCatalogService::mapping($id);
        if ($mapping && $options['quantity'] !== (int)$mapping['fulfillment_quantity']) throw new \RuntimeException('Teslim miktarı SMM sağlayıcı eşlemesindeki miktarla aynı olmalı.');
        Database::getInstance()->query('INSERT INTO settings (setting_key,setting_value,setting_group) VALUES (?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),setting_group=VALUES(setting_group)',
            ['social_package_'.$id,json_encode($options,JSON_UNESCAPED_UNICODE),'packages']);
        self::$options[$id] = $options;
        unset(self::$info[$id]);
    }
}
