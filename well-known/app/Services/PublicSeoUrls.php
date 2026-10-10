<?php
namespace App\Services;

/**
 * Public content paths only. Search/sort/page filters stay in query strings.
 * Old indexed content paths and checkout/admin endpoints are not rewritten.
 */
final class PublicSeoUrls
{
    public static function path(string $prefix, string $slug): string
    {
        return rtrim($prefix, '/') . '/' . rawurlencode($slug);
    }

    /**
     * Imported NetVera child categories already have indexed /parent/child
     * routes. Keep those originals instead of creating duplicate category URLs.
     */
    public static function scriptCategory(array $category, array $allCategories): string
    {
        $slug = (string)($category['slug'] ?? '');
        $parentId = (int)($category['parent_legacy_id'] ?? 0);
        if ($parentId > 0) {
            foreach ($allCategories as $parent) {
                if ((int)($parent['legacy_id'] ?? 0) === $parentId) {
                    return self::path(
                        self::path('/hazir-scriptler', (string)$parent['slug']),
                        $slug
                    );
                }
            }
        }
        return self::path('/hazir-scriptler/kategori', $slug);
    }

    public static function withQuery(string $path, array $params): string
    {
        $params = array_filter($params, static fn($v): bool => $v !== null && $v !== '');
        $qs = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        return $path . ($qs === '' ? '' : '?' . $qs);
    }

    public static function isSlug(string $slug): bool
    {
        return (bool) preg_match('/^[\pL\pN][\pL\pN_-]{0,199}$/uD', $slug);
    }

    public static function notFound(string $title = 'Sayfa Bulunamadı'): void
    {
        http_response_code(404);
        (new \App\Core\View())->render('frontend/404', ['pageTitle' => $title], 'app');
    }
}
