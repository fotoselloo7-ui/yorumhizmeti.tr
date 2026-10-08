<?php
namespace App\Services;
use App\Core\Database;

/**
 * Read-only staging adapter for imported public Netvera scripts.
 * Never touches payment, merchant settings, orders, users or license tables.
 */
final class NetveraBridgeService
{
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
        if (!self::ready()) return [];
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

        return Database::getInstance()->fetchAll(
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
    }

    public static function categories(): array
    {
        if (!self::ready()) return [];
        return Database::getInstance()->fetchAll(
            "SELECT * FROM nv_legacy_script_categories WHERE active = 1
             ORDER BY sort_order ASC,legacy_id ASC"
        );
    }

    public static function find(string $slug): ?array
    {
        if (!self::ready()) return null;
        return Database::getInstance()->fetch(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM nv_legacy_script_products p
             LEFT JOIN nv_legacy_script_categories c ON c.legacy_id=p.category_legacy_id
             WHERE p.slug = ? AND p.active = 1 LIMIT 1",[$slug]
        );
    }

    public static function gallery(int $id): array
    {
        return self::ready() ? Database::getInstance()->fetchAll(
            "SELECT * FROM nv_legacy_script_images
             WHERE product_legacy_id=? AND active=1 ORDER BY sort_order,legacy_id",[$id]
        ) : [];
    }

    public static function reviews(int $id): array
    {
        return self::ready() ? Database::getInstance()->fetchAll(
            "SELECT rating,comment,created_at FROM nv_legacy_public_reviews
             WHERE product_legacy_id=? ORDER BY created_at DESC",[$id]
        ) : [];
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
