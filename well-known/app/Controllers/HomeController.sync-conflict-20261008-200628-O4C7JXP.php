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
        $categories = $db->fetchAll("SELECT * FROM categories WHERE status = 'active' AND parent_id IS NULL ORDER BY sort_order ASC, id ASC");

        // Admin paketleri aynı veritabanından gelir; manuel/demo paket listesi yoktur.
        // Kategori promosyonları aktif paketlerden; öne çıkan alanı ise yalnızca is_featured=1 kayıtlarından oluşur.
        // Küresel LIMIT, sonradan eklenen kategorileri görünmez kıldığı için kaldırıldı.
        $featuredPackages = $db->fetchAll("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                   COALESCE(parent.id, c.id) AS featured_group_id,
                   COALESCE(parent.name, c.name) AS featured_group_name,
                   COALESCE(parent.slug, c.slug) AS featured_group_slug,
                   COALESCE(parent.sort_order, c.sort_order) AS featured_group_sort
            FROM packages p
            INNER JOIN categories c ON p.category_id = c.id AND c.status = 'active'
            LEFT JOIN categories parent ON c.parent_id = parent.id
            WHERE p.status = 'active'
              AND (c.parent_id IS NULL OR parent.status = 'active')
            ORDER BY featured_group_sort ASC, featured_group_id ASC,
                     p.is_featured DESC, p.sort_order ASC, p.id ASC
        ");

        $featuredPackageGroups = [];
        foreach ($featuredPackages as $pkg) {
            if ((int)($pkg['is_featured'] ?? 0) !== 1) continue;
            $groupId = (int) $pkg['featured_group_id'];
            if ($groupId <= 0) continue;
            if (!isset($featuredPackageGroups[$groupId])) {
                $featuredPackageGroups[$groupId] = [
                    'category' => [
                        'id' => $groupId,
                        'name' => $pkg['featured_group_name'],
                        'slug' => $pkg['featured_group_slug'],
                    ],
                    'packages' => [],
                ];
            }
            // İlk dört kart masaüstünde görünür, gerisi kaydırıcıda tutulur.
            if (count($featuredPackageGroups[$groupId]['packages']) < 16) {
                $featuredPackageGroups[$groupId]['packages'][] = $pkg;
            }
        }
        $featuredPackageGroups = array_values($featuredPackageGroups);

        // Yalnızca sunum grubu: admin kategori ağacına, fiyatlara ve paketlere dokunma.
        // Reuse the same social/agency/marketing taxonomy as the mega menu and category page.
        // Display only categories whose active packages are marked as featured.
        $featuredNavGroups = [];
        foreach (\App\Services\CatalogMenuService::groups() as $catalogGroup) {
            $featuredNavGroups[$catalogGroup['key']] = [
                'key' => $catalogGroup['key'],
                'title' => $catalogGroup['short'],
                'icon' => $catalogGroup['icon'],
                'categories' => [],
            ];
        }
        foreach ($featuredPackageGroups as $group) {
            $key = \App\Services\CatalogMenuService::bucket($group['category']);
            if (isset($featuredNavGroups[$key])) {
                $featuredNavGroups[$key]['categories'][] = $group['category'];
            }
        }

        // The featured-package switcher and the actual Netvera software catalogue
        // have different storage models. Show both here without creating fake
        // packages, prices, ratings or changing the admin's featured selections.
        $featuredSoftwareGroups = [];
        $netveraProducts = \App\Services\NetveraBridgeService::all();
        $netveraCategories = \App\Services\NetveraBridgeService::categories();
        if ($netveraProducts && isset($featuredNavGroups['agency'])) {
            $softwareRoot = [
                'id' => -100000, 'name' => 'Hazır Yazılımlar & Scriptler',
                'slug' => 'hazir-scriptler', 'url' => '/hazir-scriptler',
                'kind' => 'software', 'icon' => 'monitor',
            ];
            $featuredNavGroups['agency']['categories'][] = $softwareRoot;
            $featuredSoftwareGroups[] = [
                'category' => $softwareRoot, 'products' => $netveraProducts,
            ];

            foreach ($netveraCategories as $netveraCategory) {
                $catId = (int) $netveraCategory['legacy_id'];
                $categoryProducts = array_values(array_filter(
                    $netveraProducts,
                    static function (array $product) use ($catId, $netveraCategories): bool {
                        $productCategory = (int) $product['category_legacy_id'];
                        if ($productCategory === $catId) return true;
                        // A parent category also displays items in its child category.
                        foreach ($netveraCategories as $child) {
                            if ((int) $child['legacy_id'] === $productCategory
                                && (int) $child['parent_legacy_id'] === $catId) return true;
                        }
                        return false;
                    }
                ));
                $catIdUrl = '/hazir-scriptler?category='.rawurlencode((string)$netveraCategory['slug']);
                $tabCategory = [
                    'id' => -100000 - $catId,
                    'name' => $netveraCategory['name'],
                    'slug' => $netveraCategory['slug'],
                    'url' => $catIdUrl,
                    'kind' => 'software',
                    'icon' => (int)$netveraCategory['parent_legacy_id'] > 0 ? 'code' : 'layers',
                ];
                $featuredNavGroups['agency']['categories'][] = $tabCategory;
                $featuredSoftwareGroups[] = [
                    'category' => $tabCategory, 'products' => $categoryProducts,
                ];
            }
        }
        $featuredNavGroups = array_values(array_filter($featuredNavGroups, static fn($g) => !empty($g['categories'])));
        $initialFeaturedNavGroup = $featuredNavGroups[0]['key'] ?? 'marketing';
        foreach ($featuredNavGroups as $navGroup) {
            foreach ($navGroup['categories'] as $category) {
                if ((int)$category['id'] === (int)($featuredPackageGroups[0]['category']['id'] ?? 0)) {
                    $initialFeaturedNavGroup = $navGroup['key'];
                    break 2;
                }
            }
        }

        // Balanced quick service links on the homepage: show social, agency and
        // marketing categories, instead of only the first eight database rows.
        $homeQuickCategories = [];
        $homeQuickIds = [];
        foreach (['social' => 4, 'agency' => 2, 'marketing' => 2] as $key => $limit) {
            foreach (\App\Services\CatalogMenuService::groups() as $group) {
                if ($group['key'] !== $key) continue;
                foreach (array_slice($group['categories'], 0, $limit) as $cat) {
                    $homeQuickCategories[] = $cat;
                    $homeQuickIds[$cat['id']] = true;
                }
            }
        }
        if (count($homeQuickCategories) < 8) {
            foreach (\App\Services\CatalogMenuService::groups() as $group) {
                foreach ($group['categories'] as $cat) {
                    if (isset($homeQuickIds[$cat['id']])) continue;
                    $homeQuickCategories[] = $cat;
                    $homeQuickIds[$cat['id']] = true;
                    if (count($homeQuickCategories) >= 8) break 2;
                }
            }
        }

        // Son blog yazıları
        $latestPosts = $db->fetchAll("SELECT bp.*, bc.name as category_name, bc.slug as category_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id WHERE bp.status = 'active' ORDER BY bp.published_at DESC LIMIT 4");
        if (count($latestPosts) < 4) {
            foreach (demo_blog_posts() as $demoPost) {
                $latestPosts[] = $demoPost;
                if (count($latestPosts) >= 4) break;
            }
        }

        // Three permanent promotional service families, backed by the same live
        // catalog as the mega menu. Category chips only link to ACTIVE records.
        $promoConfig = [
            'social' => [
                'title' => 'Sosyal Medya Hizmetleri',
                'eyebrow' => 'MARKANI SOSYALDE BÜYÜT',
                'description' => 'Instagram, TikTok, YouTube ve diğer platformlarda etkileşiminizi ve görünürlüğünüzü artıran çözümler.',
                'cta' => 'Paketleri İncele',
                'preferred' => ['instagram', 'tiktok', 'youtube', 'facebook'],
            ],
            'agency' => [
                'title' => 'Ajans & Yazılım',
                'eyebrow' => 'DİJİTAL ALTYAPINI KUR',
                'description' => 'Web sitesi, e-ticaret, özel yazılım, mobil uygulama ve tasarım için profesyonel çözümler.',
                'cta' => 'Hizmetleri İncele',
                'preferred' => ['web', 'ecommerce', 'mobileapp', 'graphic'],
            ],
            'marketing' => [
                'title' => 'SEO & Dijital Pazarlama',
                'eyebrow' => 'DİJİTALDE DAHA GÖRÜNÜR OL',
                'description' => 'SEO, reklam yönetimi, yerel işletme ve dijital büyüme hizmetleriyle markanızı öne çıkarın.',
                'cta' => 'Çözümleri İncele',
                'preferred' => ['seo', 'ads', 'local', 'reputation'],
            ],
        ];
        $homePromoGroups = [];
        $menuGroups = [];
        foreach (\App\Services\CatalogMenuService::groups() as $group) {
            $menuGroups[$group['key']] = $group;
        }
        foreach ($promoConfig as $key => $config) {
            $groupCategories = $menuGroups[$key]['categories'] ?? [];
            $chips = [];
            $usedIds = [];
            // Prioritize well-known services, without assuming those records exist.
            foreach ($config['preferred'] as $preferredStyle) {
                foreach ($groupCategories as $cat) {
                    if (isset($usedIds[$cat['id']]) || $cat['style'] !== $preferredStyle) continue;
                    $chips[] = [
                        'name' => preg_replace('/\s+Hizmetleri?$/u', '', (string)$cat['name']),
                        'url' => $cat['url'],
                        'icon' => $cat['icon'],
                    ];
                    $usedIds[$cat['id']] = true;
                    break;
                }
            }
            foreach ($groupCategories as $cat) {
                if (count($chips) >= 4) break;
                if (isset($usedIds[$cat['id']])) continue;
                $chips[] = [
                    'name' => preg_replace('/\s+Hizmetleri?$/u', '', (string)$cat['name']),
                    'url' => $cat['url'],
                    'icon' => $cat['icon'],
                ];
                $usedIds[$cat['id']] = true;
            }
            $homePromoGroups[] = [
                'key' => $key,
                'title' => $config['title'],
                'eyebrow' => $config['eyebrow'],
                'description' => $config['description'],
                'cta' => $config['cta'],
                'url' => '/kategoriler?grup=' . $key,
                'chips' => array_slice($chips, 0, 4),
            ];
        }

        // Independent software showcase and real client references.
        // Both remain hidden on the storefront until the admin publishes data.
        $softwareHighlights = \App\Services\SoftwareCatalogService::featured();
        $softwareRoot = \App\Services\SoftwareCatalogService::root();
        $softwareCatalogUrl = '/hazir-yazilimlar';
        $softwarePreviewCategories = [];
        if (($softwareRoot['status'] ?? '') === 'active') {
            $softwarePreviewCategories = $db->fetchAll(
                "SELECT name, slug, icon_key FROM categories
                 WHERE parent_id = ? AND status = 'active'
                 ORDER BY sort_order ASC, id ASC LIMIT 6",
                [(int)$softwareRoot['id']]
            );
        }
        $projectReferences = array_slice(\App\Services\ReferencesService::all(true), 0, 8);

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
            'featuredSoftwareGroups' => $featuredSoftwareGroups,
            'featuredNavGroups' => $featuredNavGroups,
            'initialFeaturedNavGroup' => $initialFeaturedNavGroup,
            'homePromoGroups' => $homePromoGroups,
            'softwareHighlights' => $softwareHighlights,
            'softwareCatalogUrl' => $softwareCatalogUrl,
            'softwarePreviewCategories' => $softwarePreviewCategories,
            'projectReferences' => $projectReferences,
            'homeQuickCategories' => $homeQuickCategories,
            'latestPosts' => $latestPosts,
            'faqs' => $faqs,
            'sections' => $sections,
            'schema' => json_encode($schemaOrg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }
}
