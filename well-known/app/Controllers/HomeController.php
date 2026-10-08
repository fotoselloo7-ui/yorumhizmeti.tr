<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class HomeController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();

        // Kategoriler
        $categories = $db->fetchAll("SELECT * FROM categories WHERE status = 'active' AND parent_id IS NULL ORDER BY sort_order ASC LIMIT 8");

        // Öne çıkan paketler: kategori bazlı dinamik gruplar.
        // Yeni bir kategoriye aktif + öne çıkan paket eklendiğinde ana sayfa sekmesi otomatik oluşur.
        try {
            $featuredPackages = $db->fetchAll("
                SELECT
                    p.*,
                    c.name AS category_name,
                    c.slug AS category_slug,
                    COALESCE(parent.id, c.id) AS featured_group_id,
                    COALESCE(parent.name, c.name) AS featured_group_name,
                    COALESCE(parent.slug, c.slug) AS featured_group_slug,
                    COALESCE(parent.sort_order, c.sort_order) AS featured_group_sort
                FROM packages p
                INNER JOIN categories c ON p.category_id = c.id AND c.status = 'active'
                LEFT JOIN categories parent ON c.parent_id = parent.id AND parent.status = 'active'
                WHERE p.status = 'active' AND p.is_featured = 1
                ORDER BY featured_group_sort ASC, p.sort_order ASC, p.id ASC
                LIMIT 80
            ");
        } catch (\Exception $e) {
            // Eski kurulumlarda is_featured yoksa aktif paketlerle aynı vitrini kur.
            $featuredPackages = $db->fetchAll("
                SELECT
                    p.*,
                    c.name AS category_name,
                    c.slug AS category_slug,
                    COALESCE(parent.id, c.id) AS featured_group_id,
                    COALESCE(parent.name, c.name) AS featured_group_name,
                    COALESCE(parent.slug, c.slug) AS featured_group_slug,
                    COALESCE(parent.sort_order, c.sort_order) AS featured_group_sort
                FROM packages p
                INNER JOIN categories c ON p.category_id = c.id AND c.status = 'active'
                LEFT JOIN categories parent ON c.parent_id = parent.id AND parent.status = 'active'
                WHERE p.status = 'active'
                ORDER BY featured_group_sort ASC, p.sort_order ASC, p.id ASC
                LIMIT 80
            ");
        }

        $featuredPackageGroups = [];
        foreach ($featuredPackages as $pkg) {
            $groupId = (int)($pkg['featured_group_id'] ?? $pkg['category_id'] ?? 0);
            if ($groupId <= 0) continue;

            if (!isset($featuredPackageGroups[$groupId])) {
                $featuredPackageGroups[$groupId] = [
                    'category' => [
                        'id' => $groupId,
                        'name' => $pkg['featured_group_name'] ?? $pkg['category_name'] ?? 'Hizmetler',
                        'slug' => $pkg['featured_group_slug'] ?? $pkg['category_slug'] ?? '',
                    ],
                    'packages' => [],
                ];
            }

            // Masaüstünde referanstaki gibi dört ana kart; fazlası sekme içinde hazır tutulur.
            if (count($featuredPackageGroups[$groupId]['packages']) < 8) {
                $featuredPackageGroups[$groupId]['packages'][] = $pkg;
            }
        }
        $featuredPackageGroups = array_values($featuredPackageGroups);

        // Son blog yazıları
        $latestPosts = $db->fetchAll("SELECT bp.*, bc.name as category_name, bc.slug as category_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id WHERE bp.status = 'active' ORDER BY bp.published_at DESC LIMIT 4");
        if (count($latestPosts) < 4) {
            foreach (demo_blog_posts() as $demoPost) {
                $latestPosts[] = $demoPost;
                if (count($latestPosts) >= 4) break;
            }
        }

        // Ana Sayfa Özel Kategori Blokları (Instagram, Web Tasarım, TikTok vb.)
        $homeCategoryBlocks = [];
        $targetSlugs = ['instagram-hizmetleri', 'tiktok-hizmetleri', 'web-site-hizmetleri'];
        foreach ($targetSlugs as $tslug) {
            $cat = $db->fetch("SELECT * FROM categories WHERE slug = ? AND status = 'active'", [$tslug]);
            if ($cat) {
                // Bu kategorinin ve alt kategorilerinin paketlerini getir
                $catPackages = $db->fetchAll("
                    SELECT p.*, c.name as category_name, c.slug as category_slug 
                    FROM packages p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE (c.id = ? OR c.parent_id = ?) AND p.status = 'active' 
                    ORDER BY p.sort_order ASC LIMIT 8
                ", [$cat['id'], $cat['id']]);
                
                if (!empty($catPackages)) {
                    $homeCategoryBlocks[] = [
                        'category' => $cat,
                        'packages' => $catPackages
                    ];
                }
            }
        }

        // SSS
        $faqs = $db->fetchAll("SELECT * FROM faqs WHERE status = 'active' ORDER BY sort_order ASC LIMIT 6");

        // Ana sayfa bölümleri (home_sections)
        $sectionsRaw = [];
        try {
            $sectionsRaw = $db->fetchAll("SELECT * FROM home_sections WHERE status = 'active' ORDER BY sort_order ASC");
        } catch (\Exception $e) {
            // Tablo yoksa boş devam et
        }

        // section_key => section data
        $sections = [];
        foreach ($sectionsRaw as $sec) {
            $sec['extra'] = !empty($sec['extra_data']) ? json_decode($sec['extra_data'], true) : [];
            $sections[$sec['section_key']] = $sec;
        }

        // Schema.org JSON-LD
        $siteName = setting('site_name', 'Yorum Hizmeti');
        $siteUrl = rtrim($_ENV['APP_URL'] ?? 'https://yorumhizmeti.tr', '/');
        $sitePhone = setting('site_phone', '');
        $siteEmail = setting('site_email', '');

        $schemaOrg = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    'name' => $siteName,
                    'url' => $siteUrl,
                    'logo' => $siteUrl . '/assets/images/logo.png',
                    'contactPoint' => [
                        '@type' => 'ContactPoint',
                        'telephone' => $sitePhone,
                        'email' => $siteEmail,
                        'contactType' => 'customer service',
                        'availableLanguage' => 'Turkish',
                    ],
                ],
                [
                    '@type' => 'WebSite',
                    'name' => $siteName,
                    'url' => $siteUrl,
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => $siteUrl . '/kategoriler?q={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
            ],
        ];

        // FAQ Schema
        if (!empty($faqs)) {
            $faqItems = [];
            foreach ($faqs as $faq) {
                $faqItems[] = [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => strip_tags($faq['answer']),
                    ],
                ];
            }
            $schemaOrg['@graph'][] = [
                '@type' => 'FAQPage',
                'mainEntity' => $faqItems,
            ];
        }

        $this->render('frontend/home', [
            'pageTitle' => setting('default_seo_title', 'Yorum Hizmeti - Dijital Hizmet Platformu'),
            'metaDescription' => setting('default_seo_description', 'Google, Instagram, TikTok, YouTube yorum ve etkileşim hizmetleri. Güvenli ödeme, hızlı teslimat.'),
            'categories' => $categories,
            'featuredPackages' => $featuredPackages,
            'featuredPackageGroups' => $featuredPackageGroups,
            'homeCategoryBlocks' => $homeCategoryBlocks,
            'latestPosts' => $latestPosts,
            'faqs' => $faqs,
            'sections' => $sections,
            'schema' => json_encode($schemaOrg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }
}
