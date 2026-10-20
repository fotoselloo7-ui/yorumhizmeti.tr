<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Database;

class CategoryController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $q = trim($_GET['q'] ?? '');
        if ($q !== '') {
            $categories = $db->fetchAll(
                "SELECT * FROM categories WHERE status = 'active' AND parent_id IS NULL AND (name LIKE ? OR description LIKE ?) ORDER BY sort_order ASC",
                ["%{$q}%", "%{$q}%"]
            );
        } else {
            $categories = $db->fetchAll("SELECT * FROM categories WHERE status = 'active' AND parent_id IS NULL ORDER BY sort_order ASC");
        }
        $this->render('frontend/categories', [
            'pageTitle' => 'Hizmet Kategorileri - ' . setting('site_name'),
            'metaDescription' => 'Dijital hizmet kategorilerimizi inceleyin.',
            'categories' => $categories,
            'searchQuery' => $q,
        ]);
    }

    public function show(string $slug): void
    {
        $db = Database::getInstance();
        $category = $db->fetch("
            SELECT c.*
            FROM categories c
            LEFT JOIN categories parent ON c.parent_id = parent.id
            WHERE c.slug = ? AND c.status = 'active'
              AND (c.parent_id IS NULL OR parent.status = 'active')
        ", [$slug]);
        if (!$category) { $this->render('frontend/404', ['pageTitle' => 'Sayfa Bulunamadı']); return; }

        $subCategories = $db->fetchAll("SELECT * FROM categories WHERE parent_id = ? AND status = 'active' ORDER BY sort_order ASC", [$category['id']]);

        // Filters
        $altSlug = $_GET['alt'] ?? '';
        $q = trim($_GET['q'] ?? '');
        $sort = $_GET['sort'] ?? 'recommended';

        $targetCategoryIds = [$category['id']];
        $selectedSubCategory = null;

        if ($altSlug) {
            foreach ($subCategories as $sub) {
                if ($sub['slug'] === $altSlug) {
                    $targetCategoryIds = [$sub['id']];
                    $selectedSubCategory = $sub;
                    break;
                }
            }
        } else {
            foreach ($subCategories as $sub) {
                $targetCategoryIds[] = $sub['id'];
            }
        }

        $params = [];
        $where = "WHERE p.status = 'active' AND p.category_id IN (" . implode(',', array_fill(0, count($targetCategoryIds), '?')) . ")";
        $params = array_merge($params, $targetCategoryIds);

        if ($q) {
            $where .= " AND p.name LIKE ?";
            $params[] = "%{$q}%";
        }

        $orderBy = "ORDER BY p.sort_order ASC, p.id DESC";
        if ($sort === 'price_asc') {
            $orderBy = "ORDER BY COALESCE(p.discount_price, p.price) ASC";
        } elseif ($sort === 'price_desc') {
            $orderBy = "ORDER BY COALESCE(p.discount_price, p.price) DESC";
        } elseif ($sort === 'featured') {
            $orderBy = "ORDER BY p.is_featured DESC, p.sort_order ASC";
        }

        $packages = $db->fetchAll("SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon_key FROM packages p LEFT JOIN categories c ON p.category_id = c.id $where $orderBy", $params);

        $faqs = $db->fetchAll("SELECT * FROM faqs WHERE status = 'active' ORDER BY sort_order ASC LIMIT 6");
        $testimonialSection = null;
        try {
            $testimonialSection = $db->fetch("SELECT * FROM home_sections WHERE section_key = 'testimonials' AND status = 'active' LIMIT 1");
            if ($testimonialSection) {
                $testimonialSection['extra'] = !empty($testimonialSection['extra_data'])
                    ? json_decode($testimonialSection['extra_data'], true)
                    : [];
            }
        } catch (\Exception $e) {
            $testimonialSection = null;
        }

        $this->render('frontend/category-detail', [
            'pageTitle' => $category['seo_title'] ?: $category['name'] . ' - ' . setting('site_name'),
            'metaDescription' => $category['seo_description'] ?: $category['description'],
            'canonicalUrl' => url('/kategori/' . $category['slug']),
            'ogTitle' => $category['og_title'] ?: $category['name'],
            'ogDescription' => $category['og_description'] ?: $category['seo_description'],
            'category' => $category,
            'packages' => $packages,
            'subCategories' => $subCategories,
            'selectedSubCategory' => $selectedSubCategory,
            'currentSort' => $sort,
            'searchQuery' => $q,
            'altSlug' => $altSlug,
            'faqs' => $faqs,
            'testimonialSection' => $testimonialSection
        ]);
    }
}
