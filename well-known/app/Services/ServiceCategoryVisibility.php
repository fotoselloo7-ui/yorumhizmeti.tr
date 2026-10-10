<?php
namespace App\Services;

/**
 * Presentation eligibility. Retired Google review/5-star catalogue entries remain
 * untouched in the DB, existing URLs/orders and admin management for continuity.
 * They are deliberately excluded from the NetVera corporate service showcase.
 */
final class ServiceCategoryVisibility
{
    // Obsolete storefront category from the old standalone software showcase.
    // Its canonical public destination is the imported NetVera /hazir-scriptler catalogue.
    // Keep rows intact for admin, existing orders and product relationships.
    public const LEGACY_SOFTWARE_ROOT = 'hazir-yazilim-scriptleri';

    public static function isLegacySoftware(array $category, ?array $parent=null): bool
    {
        return (string)($category['slug'] ?? '') === self::LEGACY_SOFTWARE_ROOT
            || (string)($parent['slug'] ?? $category['parent_category_slug'] ?? '') === self::LEGACY_SOFTWARE_ROOT
            || (string)($category['featured_group_slug'] ?? '') === self::LEGACY_SOFTWARE_ROOT;
    }

    public static function show(array $category, ?array $parent=null):bool
    {
        if (self::isLegacySoftware($category,$parent)) return false;
        $root=mb_strtolower((string)($parent['slug']??$category['parent_category_slug']??'').' '.(string)($parent['name']??$category['parent_category_name']??''),'UTF-8');
        $text=mb_strtolower((string)($category['slug']??'').' '.(string)($category['name']??''),'UTF-8');
        $isGoogle=str_contains($root,'google') || str_contains($text,'google');
        if(!$isGoogle)return true;
        // Only the retired ratings/review acquisition subservices are hidden.
        // Google Maps, Google Business Profile, local SEO and Ads stay visible.
        $retired=(bool)preg_match('/yorum|değerlendirme|degerlendirme|5[\s-]?y[ıi]ld[ıi]z|5[\s-]?star|review|rating|puan\s*(sat|al)/iu',$text);
        if(!$retired)return true;
        // A Google parent isn't hidden just because it contains "hizmetleri".
        return false;
    }

    public static function showPackage(array $package):bool
    {
        if (self::isLegacySoftware($package)) return false;
        $parent=['name'=>(string)($package['featured_group_name']??''),
                 'slug'=>(string)($package['featured_group_slug']??'')];
        $child=['name'=>(string)($package['category_name']??''),
                'slug'=>(string)($package['category_slug']??'')];
        if(!self::show($child,$parent))return false;
        // Some legacy products are attached directly to the Google root.
        $name=mb_strtolower((string)($package['name']??''),'UTF-8');
        $isGoogle=(bool)preg_match('/google/iu',implode(' ',$parent).' '.implode(' ',$child));
        if($isGoogle && preg_match('/yorum|5[\s-]?y[ıi]ld[ıi]z|5[\s-]?star|rating|değerlendirme/iu',$name))
            return false;
        return true;
    }
}
