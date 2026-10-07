<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Database;

class PackageController extends Controller
{
    public function show(string $slug): void
    {
        $db = Database::getInstance();
        $package = $db->fetch("SELECT p.*, c.name as category_name, c.slug as category_slug FROM packages p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ? AND p.status = 'active'", [$slug]);
        if (!$package) { $this->render('frontend/404', ['pageTitle' => 'Paket Bulunamadı']); return; }

        $fields = $db->fetchAll("SELECT * FROM package_fields WHERE package_id = ? ORDER BY sort_order ASC", [$package['id']]);
        $variantPackages = $db->fetchAll("SELECT id, name, slug, price, discount_price FROM packages WHERE category_id = ? AND status = 'active' ORDER BY sort_order ASC LIMIT 3", [$package['category_id']]);
        $relatedPackages = $db->fetchAll("SELECT p.*, c.name as category_name, c.slug as category_slug FROM packages p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id != ? AND p.status = 'active' ORDER BY (p.category_id = ?) ASC, p.is_featured DESC, p.sort_order ASC LIMIT 4", [$package['id'], $package['category_id']]);
        $faqs = $db->fetchAll("SELECT * FROM faqs WHERE status = 'active' ORDER BY sort_order ASC LIMIT 6");
        $testimonialSection = null;
        try {
            $testimonialSection = $db->fetch("SELECT * FROM home_sections WHERE section_key = 'testimonials' AND status = 'active' LIMIT 1");
            if ($testimonialSection) {
                $testimonialSection['extra'] = !empty($testimonialSection['extra_data']) ? json_decode($testimonialSection['extra_data'], true) : [];
            }
        } catch (\Exception $e) {
            $testimonialSection = null;
        }

        $schema = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $package['name'],
            'description' => strip_tags($package['short_description'] ?? ''),
            'offers' => [
                '@type' => 'Offer',
                'price' => $package['discount_price'] ?? $package['price'],
                'priceCurrency' => 'TRY',
                'availability' => 'https://schema.org/InStock',
            ],
        ], JSON_UNESCAPED_UNICODE);

        $this->render('frontend/package-detail', [
            'pageTitle' => $package['seo_title'] ?: $package['name'] . ' - ' . setting('site_name'),
            'metaDescription' => $package['seo_description'] ?: excerpt(strip_tags($package['short_description'] ?? ''), 160),
            'canonicalUrl' => url('/paket/' . $package['slug']),
            'schema' => $schema,
            'package' => $package,
            'fields' => $fields,
            'variantPackages' => $variantPackages,
            'relatedPackages' => $relatedPackages,
            'faqs' => $faqs,
            'testimonialSection' => $testimonialSection,
        ]);
    }
}
