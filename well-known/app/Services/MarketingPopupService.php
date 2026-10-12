<?php
namespace App\Services;

use App\Core\Database;

final class MarketingPopupService
{
    private static bool $ready=false;

    public static function ensureSchema(): void
    {
        if(self::$ready) return;
        $db=Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS marketing_popups (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                internal_name VARCHAR(140) NOT NULL,
                type ENUM('campaign','image','coupons') DEFAULT 'campaign',
                orientation ENUM('horizontal','vertical') DEFAULT 'horizontal',
                eyebrow VARCHAR(120) DEFAULT NULL,
                title VARCHAR(220) DEFAULT NULL,
                description TEXT DEFAULT NULL,
                image VARCHAR(255) DEFAULT NULL,
                image_alt VARCHAR(255) DEFAULT NULL,
                primary_button_text VARCHAR(100) DEFAULT NULL,
                primary_button_url VARCHAR(255) DEFAULT NULL,
                primary_button_icon VARCHAR(60) DEFAULT 'arrow-right',
                primary_button_bg VARCHAR(20) DEFAULT '#ffffff',
                primary_button_text_color VARCHAR(20) DEFAULT '#17264e',
                secondary_button_text VARCHAR(100) DEFAULT NULL,
                secondary_button_url VARCHAR(255) DEFAULT NULL,
                secondary_button_icon VARCHAR(60) DEFAULT 'grid',
                secondary_button_bg VARCHAR(20) DEFAULT '#ffffff',
                secondary_button_text_color VARCHAR(20) DEFAULT '#ffffff',
                background_start VARCHAR(20) DEFAULT '#151a45',
                background_end VARCHAR(20) DEFAULT '#6b4de8',
                text_color VARCHAR(20) DEFAULT '#ffffff',
                accent_color VARCHAR(20) DEFAULT '#d936a1',
                coupon_items JSON DEFAULT NULL,
                open_delay_ms INT UNSIGNED DEFAULT 1800,
                reopen_hours INT UNSIGNED DEFAULT 24,
                auto_close_seconds INT UNSIGNED DEFAULT 0,
                closable TINYINT(1) DEFAULT 1,
                close_on_backdrop TINYINT(1) DEFAULT 1,
                once_per_session TINYINT(1) DEFAULT 0,
                page_mode ENUM('all','home','include','exclude') DEFAULT 'all',
                path_rules TEXT DEFAULT NULL,
                sort_order INT DEFAULT 100,
                status ENUM('active','inactive') DEFAULT 'inactive',
                starts_at DATETIME DEFAULT NULL,
                ends_at DATETIME DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_popup_status_sort (status,sort_order,id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        self::$ready=true;
    }

    public static function all(bool $activeOnly=false): array
    {
        self::ensureSchema();
        $where=$activeOnly
            ?"WHERE status='active' AND (starts_at IS NULL OR starts_at<=NOW()) AND (ends_at IS NULL OR ends_at>=NOW())"
            :"";
        $rows=Database::getInstance()->fetchAll("SELECT * FROM marketing_popups {$where} ORDER BY sort_order ASC,id ASC");
        foreach($rows as &$row) self::decode($row);
        unset($row);
        return $rows;
    }

    public static function find(int $id): ?array
    {
        self::ensureSchema();
        $row=Database::getInstance()->fetch("SELECT * FROM marketing_popups WHERE id=?",[$id]);
        if(!$row) return null;
        self::decode($row);
        return $row;
    }

    public static function activeForPath(string $uri): ?array
    {
        $path=parse_url($uri,PHP_URL_PATH)?:'/';
        foreach(self::all(true) as $popup){
            if(self::matchesPath($popup,$path)) return $popup;
        }
        return null;
    }

    public static function imageUrl(?string $path): string
    {
        $path=trim((string)$path);
        if($path==='') return '';
        if(str_starts_with($path,'/assets/')||str_starts_with($path,'/uploads/')) return $path;
        if(preg_match('~^https://~i',$path)) return $path;
        return upload_url($path);
    }

    private static function decode(array &$row): void
    {
        $items=json_decode((string)($row['coupon_items']??'[]'),true);
        $items=is_array($items)?$items:[];

        // Coupon-center popups automatically include every currently valid,
        // public coupon from the real checkout discount engine.
        if (($row['type']??'')==='coupons') {
            try {
                $realCoupons=DiscountService::publicCoupons();
                $byCode=[];
                foreach($items as $item){
                    $code=strtoupper(trim((string)($item['code']??'')));
                    if($code!=='') $byCode[$code]=true;
                }
                foreach($realCoupons as $coupon){
                    $code=strtoupper(trim((string)($coupon['code']??'')));
                    if($code!=='' && isset($byCode[$code])) continue;
                    $items[]=[
                        'title'=>(string)($coupon['title']??'Kupon'),
                        'code'=>$code,
                        'discount'=>(string)($coupon['discount']??''),
                        'description'=>(string)($coupon['description']??''),
                    ];
                    if($code!=='') $byCode[$code]=true;
                }
            } catch (\Throwable $e) {
                error_log('Popup coupon sync unavailable: '.$e->getMessage());
            }
        }

        $row['coupon_items']=$items;
    }

    private static function matchesPath(array $popup,string $path): bool
    {
        $mode=(string)($popup['page_mode']??'all');
        if($mode==='all') return true;
        if($mode==='home') return $path==='/';

        $rules=array_values(array_filter(array_map('trim',preg_split('/\R/',(string)($popup['path_rules']??''))?:[])));
        if(!$rules) return $mode==='exclude';

        $matched=false;
        foreach($rules as $rule){
            $rule='/'.ltrim($rule,'/');
            if(str_ends_with($rule,'*')){
                if(str_starts_with($path,rtrim($rule,'*'))) {$matched=true;break;}
            }elseif($path===$rule || str_starts_with($path,rtrim($rule,'/').'/')){
                $matched=true;break;
            }
        }
        return $mode==='include'?$matched:!$matched;
    }
}
