<?php
namespace App\Services;

/**
 * Public content paths only. Search/sort/page filters stay in query strings.
 * Old indexed content paths and checkout/admin endpoints are not rewritten.
 */
final class PublicSeoUrls
{
    /**
     * Friendly indexable service directory roots. Existing category, package,
     * blog, checkout and imported NetVera software URL contracts are unchanged.
     */
    public static function group(string $key): string
    {
        return match ($key) {
            'social' => '/sosyal-medya-hizmetleri',
            'agency' => '/ajans-yazilim-hizmetleri',
            'marketing' => '/seo-dijital-pazarlama-hizmetleri',
            default => self::path('/kategoriler/grup', $key),
        };
    }

    /**
     * All ordinary public categories have one explicit SEO URL. Choose the
     * BUCKET FROM THE ROOT, so "sosyal medya içerik" under an agency parent
     * does not suddenly jump into the social-media section.
     * Imported ready-made software and original blog URLs do not use this.
     */
    public static function category(array $category, ?array $parent=null): string
    {
        $slug=trim((string)($category['slug']??''));
        $parent=$parent??(
            !empty($category['parent_category_slug'])
                ? ['slug'=>$category['parent_category_slug'],
                   'name'=>$category['parent_category_name']??'']
                : null
        );
        $bucket=CatalogMenuService::bucket($parent??$category);
        return self::path(self::group($bucket), $slug);
    }

    /**
     * The default service is THE category page; never repeat its name.
     * Variants use a minimal disambiguating third segment:
     * /sosyal-medya-hizmetleri/tiktok-takipci/kurumsal-takipci.
     */
    public static function socialService(string $categorySlug, string $serviceName): string
    {
        $base=self::path(self::group('social'),$categorySlug);
        $slug=slugify($serviceName);
        if ($slug==='' || $slug===$categorySlug) return $base;
        $platform=strtok($categorySlug,'-');
        if ($platform && str_starts_with($slug,$platform.'-')) {
            $short=substr($slug,strlen($platform)+1);
            if($short!=='' && $short!==$categorySlug) $slug=$short;
        }
        return self::path($base, $slug);
    }

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
