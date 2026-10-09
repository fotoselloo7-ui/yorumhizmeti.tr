<?php
namespace App\Services;

use App\Core\Database;

/** Admin-authored product-card benefits, stored in existing settings table (no schema migration). */
final class PackageHighlightsService
{
    public static function get(int $packageId, array $package=[]): array
    {
        if ($packageId > 0) {
            try {
                $stored=Database::getInstance()->fetch(
                    'SELECT setting_value FROM settings WHERE setting_key=? LIMIT 1',['package_highlights_'.$packageId]
                );
                if ($stored && is_string($stored['setting_value'])) {
                    $list=json_decode($stored['setting_value'],true);
                    if (is_array($list) && array_is_list($list)) {
                        $list=array_values(array_filter(array_map(static fn($s)=>is_string($s)?trim($s):'', $list)));
                        if ($list) return array_slice($list,0,12);
                    }
                }
            } catch (\Throwable $e) {
                error_log('Package benefits read: '.get_class($e));
            }
        }
        $result=['Şifre paylaşmadan sipariş','Sipariş durumunu hesabınızdan takip edin','Güvenli ödeme seçenekleri'];
        $delivery=trim((string)($package['delivery_time']??''));
        $result[]=$delivery!==''?'Tahmini teslim: '.$delivery:'Teslimat bilgisi siparişinizde';
        return $result;
    }

    public static function isCustomized(int $packageId): bool
    {
        try {
            return (bool)Database::getInstance()->fetch(
                'SELECT 1 AS ok FROM settings WHERE setting_key=? LIMIT 1',
                ['package_highlights_'.$packageId]
            );
        } catch (\Throwable $e) { return false; }
    }

    public static function text(int $packageId): string
    {
        if (!self::isCustomized($packageId)) return '';
        try {
            $row=Database::getInstance()->fetch(
                'SELECT setting_value FROM settings WHERE setting_key=?',['package_highlights_'.$packageId]
            );
            $data=json_decode((string)($row['setting_value']??'[]'),true);
            return is_array($data)?implode("\n",array_filter($data,'is_string')):'';
        } catch (\Throwable $e) { return ''; }
    }

    public static function save(int $packageId, string $value): void
    {
        if ($packageId<1) throw new \RuntimeException('Geçersiz paket.');
        $lines=preg_split('/\r\n|\r|\n/',trim($value)) ?: [];
        $lines=array_values(array_filter(array_map(static fn($s)=>trim(strip_tags($s)), $lines),static fn($s)=>$s!==''));
        if (count($lines)>12) throw new \RuntimeException('Paket kartına en fazla 12 açıklama satırı ekleyin.');
        foreach ($lines as $line) if (mb_strlen($line)>115) throw new \RuntimeException('Her özellik en fazla 115 karakter olabilir.');
        $db=Database::getInstance();
        $key='package_highlights_'.$packageId;
        if (!$lines) {
            $db->delete('settings','setting_key=?',[$key]);
            return;
        }
        $db->query(
            'INSERT INTO settings (setting_key,setting_value,setting_group) VALUES (?,?,"packages")
             ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),setting_group=VALUES(setting_group)',
            [$key,json_encode($lines,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)]
        );
    }
}
