<?php
namespace App\Services;
use App\Core\Database;

/**
 * Read-only staging adapter for imported public Netvera scripts.
 * Never touches payment, merchant settings, orders, users or license tables.
 */
final class NetveraBridgeService
{
    /**
     * Read-only first-install source of truth: signed-off public marketing snapshot.
     * The complete original catalogue appears immediately on new installations
     * WITHOUT creating tables or touching orders/payments. DB rows take precedence
     * as soon as the administrator completes the isolated import.
     */
    public static function snapshot(): array
    {
        static $cached = null;
        if ($cached !== null) return $cached;
        $file = BASE_PATH.'/database/netvera-public-catalog.json';
        if (!is_file($file)) return $cached = [];
        try {
            $data = json_decode((string)file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
            if (($data['format']??'')!=='netvera-public-safelist-v1') return $cached = [];
            foreach (['products','categories','images','approved_reviews','blog_posts','blog_categories'] as $section)
                if (!isset($data[$section]) || !is_array($data[$section])) return $cached = [];
            return $cached = $data;
        } catch (\Throwable $e) {
            error_log('Netvera public catalogue invalid: '.get_class($e));
            return $cached = [];
        }
    }

    private static function fallbackCategories(): array
    {
        return array_map(static fn(array $c):array => [
            'legacy_id'=>(int)$c['id'], 'parent_legacy_id'=>(int)($c['parent_id']??0),
            'slug'=>$c['slug'], 'name'=>$c['name'], 'description'=>$c['description']??'',
            'meta_title'=>$c['meta_title']??'', 'meta_description'=>$c['meta_description']??'',
            'sort_order'=>(int)($c['sort_order']??0),'active'=>(int)($c['is_active']??0),
        ], array_values(array_filter(self::snapshot()['categories']??[],
            static fn(array $row):bool=>(int)($row['is_active']??0)===1)));
    }

    private static function fallbackProducts(): array
    {
        $snapshot = self::snapshot();
        $categories = [];
        foreach (self::fallbackCategories() as $cat) $categories[$cat['legacy_id']] = $cat;
        $reviews = [];
        foreach ($snapshot['approved_reviews']??[] as $r) {
            if (($r['status']??'')!=='approved') continue;
            $id=(int)($r['target_id']??0);
            $reviews[$id][] = max(1, min(5, (int)($r['rating']??0)));
        }
        $products = [];
        foreach ($snapshot['products']??[] as $r) {
            if ((int)($r['is_active']??0)!==1) continue;
            $id=(int)$r['id'];
            $category = $categories[(int)($r['category_id']??0)] ?? [];
            $scores = $reviews[$id]??[];
            $products[] = [
                'legacy_id'=>$id,'category_legacy_id'=>(int)($r['category_id']??0),
                'slug'=>$r['slug'], 'name'=>$r['name'],
                'short_desc'=>$r['short_desc']??'', 'description'=>$r['description']??'',
                'cover_image'=>$r['image']??'', 'price'=>(float)($r['price']??0),
                'old_price'=>(float)($r['old_price']??0),
                'badge'=>$r['badge']??'', 'meta_title'=>$r['meta_title']??'',
                'meta_description'=>$r['meta_description']??'',
                'focus_keyword'=>$r['focus_keyword']??'', 'sort_order'=>(int)($r['sort_order']??0),
                'active'=>1,'category_name'=>$category['name']??'',
                'category_slug'=>$category['slug']??'',
                'average_rating'=>$scores?array_sum($scores)/count($scores):0,
                'review_count'=>count($scores),
                'public_json'=>json_encode($r,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ];
        }
        return $products;
    }

    public static function ready(): bool
    {
        try {
            return (bool)Database::getInstance()->fetch(
                "SELECT 1 AS ok FROM information_schema.tables
                 WHERE table_schema = DATABASE() AND table_name = 'nv_legacy_script_products' LIMIT 1"
            );
        } catch (\Throwable $e) { return false; }
    }

    /**
     * Public catalogue filters. All values are parameterized; sorting is whitelisted.
     * Ratings are calculated only from migrated approved reviews, never fabricated.
     */
    public static function all(
        string $q = '', ?int $categoryId = null,
        ?float $minPrice = null, ?float $maxPrice = null,
        int $minRating = 0, string $sort = 'recommended'
    ): array {
        if (!self::ready()) return self::filterFallback(
            $q,$categoryId,$minPrice,$maxPrice,$minRating,$sort
        );
        $where = ['p.active = 1'];
        $args = [];
        if ($q !== '') {
            $where[] = '(p.name LIKE ? OR p.short_desc LIKE ?)';
            $args[] = '%'.$q.'%';
            $args[] = '%'.$q.'%';
        }
        if ($categoryId !== null) {
            $where[] = 'p.category_legacy_id = ?';
            $args[] = $categoryId;
        }
        if ($minPrice !== null) {
            $where[] = 'p.price >= ?';
            $args[] = $minPrice;
        }
        if ($maxPrice !== null) {
            $where[] = 'p.price <= ?';
            $args[] = $maxPrice;
        }
        if ($minRating > 0) {
            $where[] = 'COALESCE(rv.average_rating, 0) >= ?';
            $args[] = min(5, $minRating);
        }
        $order = [
            'recommended' => 'p.sort_order ASC, p.legacy_id DESC',
            'price_asc' => 'p.price ASC, p.sort_order ASC',
            'price_desc' => 'p.price DESC, p.sort_order ASC',
            'newest' => 'p.legacy_id DESC',
            'rating' => 'COALESCE(rv.review_count, 0) DESC, COALESCE(rv.average_rating, 0) DESC, p.sort_order ASC'
        ][$sort] ?? 'p.sort_order ASC, p.legacy_id DESC';

        $records=Database::getInstance()->fetchAll(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    COALESCE(rv.average_rating, 0) AS average_rating,
                    COALESCE(rv.review_count, 0) AS review_count
             FROM nv_legacy_script_products p
             LEFT JOIN nv_legacy_script_categories c ON c.legacy_id = p.category_legacy_id
             LEFT JOIN (
                SELECT product_legacy_id, AVG(rating) AS average_rating, COUNT(*) AS review_count
                FROM nv_legacy_public_reviews
                GROUP BY product_legacy_id
             ) rv ON rv.product_legacy_id = p.legacy_id
             WHERE ".implode(' AND ', $where)."
             ORDER BY ".$order, $args
        );
        // On a fresh installation the tables may exist but the isolated
        // public-content import has not run yet. Show the real catalogue.
        $total=Database::getInstance()->fetch("SELECT COUNT(*) AS c FROM nv_legacy_script_products");
        if ((int)($total['c']??0)===0) return self::filterFallback(
            $q,$categoryId,$minPrice,$maxPrice,$minRating,$sort
        );
        return $records;
    }


    /** Apply the identical allowlisted search facets to the first-install snapshot. */
    private static function filterFallback(
        string $q, ?int $categoryId, ?float $minPrice, ?float $maxPrice,
        int $minRating, string $sort
    ): array {
        $items = array_values(array_filter(self::fallbackProducts(),
            static function(array $p) use ($q,$categoryId,$minPrice,$maxPrice,$minRating):bool {
                if ($categoryId!==null && (int)$p['category_legacy_id']!==$categoryId)return false;
                if ($minPrice!==null && (float)$p['price']<$minPrice)return false;
                if ($maxPrice!==null && (float)$p['price']>$maxPrice)return false;
                if ($minRating>0 && (float)$p['average_rating']<$minRating)return false;
                if ($q!=='' && mb_stripos((string)$p['name'].' '.(string)$p['short_desc'],$q,0,'UTF-8')===false)return false;
                return true;
            }
        ));
        usort($items,static function(array $a,array $b)use($sort):int {
            return match($sort){
                'price_asc' => ($a['price']<=>$b['price']) ?: ($a['sort_order']<=>$b['sort_order']),
                'price_desc' => ($b['price']<=>$a['price']) ?: ($a['sort_order']<=>$b['sort_order']),
                'newest' => $b['legacy_id']<=>$a['legacy_id'],
                'rating' => ($b['review_count']<=>$a['review_count']) ?:
                    ($b['average_rating']<=>$a['average_rating']) ?:
                    ($a['sort_order']<=>$b['sort_order']),
                default => ($a['sort_order']<=>$b['sort_order']) ?: ($b['legacy_id']<=>$a['legacy_id']),
            };
        });
        return $items;
    }

    public static function categories(): array
    {
        if (self::ready()) {
            $rows = Database::getInstance()->fetchAll(
                "SELECT * FROM nv_legacy_script_categories WHERE active = 1
                 ORDER BY sort_order ASC,legacy_id ASC"
            );
            if ($rows) return $rows;
        }
        $rows = self::fallbackCategories();
        usort($rows,static fn($a,$b)=>($a['sort_order']<=>$b['sort_order'])?:($a['legacy_id']<=>$b['legacy_id']));
        return $rows;
    }

    public static function find(string $slug): ?array
    {
        if (self::ready()) {
            $row=Database::getInstance()->fetch(
                "SELECT p.*, c.name AS category_name, c.slug AS category_slug
                 FROM nv_legacy_script_products p
                 LEFT JOIN nv_legacy_script_categories c ON c.legacy_id=p.category_legacy_id
                 WHERE p.slug = ? AND p.active = 1 LIMIT 1",[$slug]
            );
            if ($row) return $row;
            // Existing DB is authoritative once editors have added products;
            // never resurrect a product they deliberately unpublished.
            $count=Database::getInstance()->fetch(
                "SELECT COUNT(*) AS c FROM nv_legacy_script_products"
            );
            if ((int)($count['c']??0)>0)return null;
        }
        foreach (self::fallbackProducts() as $row)
            if ($row['slug']===$slug) return $row;
        return null;
    }

    public static function gallery(int $id): array
    {
        if (self::ready()) {
            $rows=Database::getInstance()->fetchAll(
                "SELECT * FROM nv_legacy_script_images
                 WHERE product_legacy_id=? AND active=1 ORDER BY sort_order,legacy_id",[$id]
            );
            if ($rows) return $rows;
        }
        $rows=[];
        foreach (self::snapshot()['images']??[] as $image) {
            if ((int)$image['script_product_id']!==$id || (int)$image['is_active']!==1)continue;
            $rows[]=[
                'legacy_id'=>(int)$image['id'],'product_legacy_id'=>$id,
                'image_path'=>$image['image_path'],'alt_text'=>$image['alt_text']??'',
                'caption'=>$image['caption']??'','sort_order'=>(int)($image['sort_order']??0),'active'=>1
            ];
        }
        usort($rows,static fn($a,$b)=>($a['sort_order']<=>$b['sort_order'])?:($a['legacy_id']<=>$b['legacy_id']));
        return $rows;
    }

    public static function reviews(int $id): array
    {
        if (self::ready()) {
            $rows=Database::getInstance()->fetchAll(
                "SELECT rating,comment,created_at FROM nv_legacy_public_reviews
                 WHERE product_legacy_id=? ORDER BY created_at DESC",[$id]
            );
            if ($rows) return $rows;
        }
        $rows=[];
        foreach (self::snapshot()['approved_reviews']??[] as $r) {
            if ((int)($r['target_id']??0)!==$id || ($r['status']??'')!=='approved')continue;
            $rows[]=['rating'=>max(1,min(5,(int)$r['rating'])),
                'comment'=>$r['comment']??'','created_at'=>$r['created_at']??''];
        }
        return $rows;
    }

    public static function jsonFields(array $product): array
    {
        $raw=json_decode((string)($product['public_json']??'{}'),true);
        return is_array($raw) ? $raw : [];
    }

    public static function arrayField(array $source,string $key): array
    {
        $value=json_decode((string)($source[$key]??'[]'),true);
        return is_array($value) ? $value : [];
    }

    public static function publicUrl(?string $url): ?string
    {
        $url=trim((string)$url);
        if ($url==='' || strlen($url)>1500 || !filter_var($url,FILTER_VALIDATE_URL)) return null;
        return in_array(strtolower((string)parse_url($url,PHP_URL_SCHEME)),['http','https'],true)?$url:null;
    }
}
