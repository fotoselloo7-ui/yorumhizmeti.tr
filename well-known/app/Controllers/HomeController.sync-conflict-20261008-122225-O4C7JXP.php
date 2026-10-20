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
        $featuredNavGroups = [
            'digital' => ['key'=>'digital','title'=>'Dijital Hizmetler','icon'=>'sparkles','categories'=>[]],
            'social'  => ['key'=>'social','title'=>'Sosyal Medya','icon'=>'share-2','categories'=>[]],
            'web'     => ['key'=>'web','title'=>'Web Site Hizmetleri','icon'=>'monitor','categories'=>[]],
        ];
        foreach ($featuredPackageGroups as $group) {
            $term = mb_strtolower(($group['category']['slug'] ?? '') . ' ' . ($group['category']['name'] ?? ''), 'UTF-8');
            if (preg_match('/instagram|tiktok|youtube|facebook|twitter|threads|telegram|spotify|discord|linkedin|twitch|pinterest|snapchat|whatsapp|sosyal.?medya/u', $term)) {
                $bucket = 'social';
            } elseif (preg_match('/web.?site|website|web.?tasar|web.?geliş|e.?ticaret|eticaret|ecommerce|wordpress|woocommerce|shopify|hosting|domain|alan.?ad|internet.?site|site.?kurulum/u', $term)) {
                $bucket = 'web';
            } else {
                $bucket = 'digital';
            }
            $featuredNavGroups[$bucket]['categories'][] = $group['category'];
        }
        // Boş gruplar görünmez. Her filtre gerçek öne çıkarılmış pakete karşılık gelir.
        $featuredNavGroups = array_values(array_filter($featuredNavGroups, static fn($g) => count($g['categories']) > 0));
        $initialFeaturedNavGroup = $featuredNavGroups[0]['key'] ?? 'digital';
        foreach ($featuredNavGroups as $navGroup) {
            foreach ($navGroup['categories'] as $category) {
                if ((int)$category['id'] === (int)($featuredPackageGroups[0]['category']['id'] ?? 0)) {
                    $initialFeaturedNavGroup = $navGroup['key'];
                    break 2;
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

        // Üç mevcut promo kartının TASARIMI korunur; kayıtlar admin kategorilerinden seçilir.
        // Önce Instagram, TikTok, Web Site; bunlar yoksa aktif paketli ilk kategoriler.
        $homeCategoryBlocks = [];
        $preferredSlugs = ['instagram-hizmetleri','tiktok-hizmetleri','web-site-hizmetleri'];
        $candidateCategories = [];
        foreach ($categories as $cat) $candidateCategories[(int)$cat['id']] = $cat;
        uasort($candidateCategories, static function ($a, $b) use ($preferredSlugs) {
            $aa = array_search($a['slug'], $preferredSlugs, true);
            $bb = array_search($b['slug'], $preferredSlugs, true);
            return ($aa === false ? 99 : $aa) <=> ($bb === false ? 99 : $bb)
                ?: ((int)$a['sort_order'] <=> (int)$b['sort_order']);
        });
        foreach ($candidateCategories as $cat) {
            $catId = (int) $cat['id'];
            $hasVisiblePackage = false;
            foreach ($featuredPackages as $p) {
                if ((int)$p['featured_group_id'] === $catId) { $hasVisiblePackage = true; break; }
            }
            if (!$hasVisiblePackage) continue;
            $homeCategoryBlocks[] = ['category' => $cat];
            if (count($homeCategoryBlocks) === 3) break;
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
            'featuredNavGroups' => $featuredNavGroups,
            'initialFeaturedNavGroup' => $initialFeaturedNavGroup,
            'homeCategoryBlocks' => $homeCategoryBlocks,
            'latestPosts' => $latestPosts,
            'faqs' => $faqs,
            'sections' => $sections,
            'schema' => json_encode($schemaOrg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }
}
