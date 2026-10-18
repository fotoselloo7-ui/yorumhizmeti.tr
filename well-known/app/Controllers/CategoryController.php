<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Database;

class CategoryController extends Controller
{
    public function group(string $slug): void
    {
        $slug = rawurldecode($slug);
        $keys = array_column(\App\Services\CatalogMenuService::groups(), 'key');
        if (!in_array($slug, $keys, true)) {
            \App\Services\PublicSeoUrls::notFound('Hizmet Grubu Bulunamadı'); return;
        }
        $_GET['grup'] = $slug;
        $this->index();
    }

    public function index(): void
    {
        $q = trim((string)($_GET['q'] ?? ''));
        $q = mb_substr($q, 0, 100);
        $activeGroup = (string)($_GET['grup'] ?? '');
        $groups = \App\Services\CatalogMenuService::groups();
        $keys = array_column($groups, 'key');
        if ($activeGroup !== '' && !in_array($activeGroup, $keys, true)) $activeGroup = '';
        if ($q !== '') {
            $groups = array_map(static function (array $group) use ($q): array {
                $needle = mb_strtolower($q,'UTF-8');
                $group['categories'] = array_values(array_filter($group['categories'], static function (array $cat) use ($needle): bool {
                    if (str_contains(mb_strtolower(($cat['name'] ?? '') . ' ' . ($cat['description'] ?? ''),'UTF-8'), $needle)) return true;
                    foreach ($cat['children'] as $child) {
                        if (str_contains(mb_strtolower($child['name'] ?? '','UTF-8'),$needle)) return true;
                    }
                    return false;
                }));
                return $group;
            }, $groups);
            $groups = array_values(array_filter($groups, static fn($group) => !empty($group['categories'])));
        }

        if ($activeGroup !== '') {
            $groups = array_values(array_filter($groups, static fn($g) => $g['key'] === $activeGroup));
        }

        $this->render('frontend/categories', [
            'pageTitle' => $activeGroup!==''&&!empty($groups[0]['label'])?$groups[0]['label'].' | NetVera':'Yazılım, Sosyal Medya ve Dijital Ajans Hizmetleri | NetVera',
            'metaDescription' => $activeGroup!==''&&!empty($groups[0]['description'])?$groups[0]['label'].': '.$groups[0]['description'].'. Kategori kapsamlarını ve mevcut hizmet seçeneklerini NetVera’da karşılaştırın.':'NetVera Teknoloji Yazılım: yazılım çözümleri, Instagram ve TikTok hizmetleri, SEO, grafik tasarım ve dijital reklam yönetimini sektörlerine göre inceleyin.',
            'canonicalUrl' => url($activeGroup !== ''
                ? \App\Services\PublicSeoUrls::path('/kategoriler/grup', $activeGroup)
                : '/kategoriler'),
            'noindex' => $q!=='',
            'catalogGroups' => $groups,
            'activeCatalogGroup' => $activeGroup,
            'searchQuery' => $q,
        ]);
    }

    public function show(string $slug): void
    {
        // This is an unused, never-indexed page in the new project, not
        // a migrated NetVera address. There must be NO redirect or duplicate
        // public content; all internal links now point to /hazir-scriptler.
        if ($slug === \App\Services\ServiceCategoryVisibility::LEGACY_SOFTWARE_ROOT) {
            \App\Services\PublicSeoUrls::notFound('Kategori Bulunamadı');
            return;
        }

        $db = Database::getInstance();
        $category = $db->fetch("
            SELECT c.*, parent.slug AS parent_category_slug, parent.name AS parent_category_name
            FROM categories c
            LEFT JOIN categories parent ON c.parent_id = parent.id
            WHERE c.slug = ? AND c.status = 'active'
              AND (c.parent_id IS NULL OR parent.status = 'active')
        ", [$slug]);
        if (!$category) { $this->render('frontend/404', ['pageTitle' => 'Sayfa Bulunamadı']); return; }
        // Retired local-only child category pages are also excluded.
        // Keep the DB rows for existing package/order foreign keys.
        if (\App\Services\ServiceCategoryVisibility::isLegacySoftware($category)) {
            \App\Services\PublicSeoUrls::notFound('Kategori Bulunamadı');
            return;
        }
        $category = \App\Services\CategorySearchBlueprint::decorate($category);
        $category = \App\Services\CategoryHeroService::decorateSeo($category);
        $categoryHero = \App\Services\CategoryHeroService::get($category);
        $isSocialSale = \App\Services\SocialPackageService::isSocialCategory((int)$category['id']);

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
            $where .= $isSocialSale ? " AND (p.name LIKE ? OR p.seo_title LIKE ? OR p.short_description LIKE ?)" : " AND p.name LIKE ?";
            foreach (range(1,$isSocialSale ? 3 : 1) as $unused) $params[] = "%{$q}%";
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
        $socialCatalog = $isSocialSale ? \App\Services\SocialPackageService::catalog($packages,$category,mb_substr((string)($_GET['hizmet'] ?? ''),0,160)) : null;
        if ($socialCatalog !== null) $packages = $socialCatalog['packages'];

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

        $categorySeo=\App\Services\NetveraSeoBridge::get('category',(int)$category['id']);
        $complete=\App\Services\CategorySeoCompleteService::fillForForm($category,$categorySeo);
        if($complete['suggestion']){
            $category=$complete['category'];
            $categorySeo=$complete['seo'];
        }

        // Editorial answers are visible immediately, even before the optional admin SEO batch is applied.
        $categoryIntent=\App\Services\CategoryHeroService::seoProfile($category);
        if($categoryIntent){
            if(empty($categorySeo['main_question']))$categorySeo['main_question']=$categoryIntent['question'];
            if(empty($categorySeo['direct_answer']))$categorySeo['direct_answer']=$categoryIntent['answer'];
            if(empty($categorySeo['secondary_keywords']))$categorySeo['secondary_keywords']=$categoryIntent['secondary'];
        }

        $schema=null;
        if(!preg_match('/(yaz[iı]l[iı]m|haz[iı]r.script|software|cms|script|tema|eklenti)/iu',
             (string)$category['slug'].' '.(string)$category['name'])){
            $canonical=url('/kategori/'.$category['slug']);
            $items=[];
            if (!empty($socialCatalog['show_services'])) {
                foreach (array_slice($socialCatalog['groups'],0,35) as $n=>$service) {
                    $items[]=['@type'=>'ListItem','position'=>$n+1,
                        'name'=>$service['name'],'url'=>url($service['url'])];
                }
            } else {
                foreach(array_slice($packages,0,35) as $n=>$p){
                    $items[]=['@type'=>'ListItem','position'=>$n+1,
                        'name'=>$isSocialSale ? package_display_name($p) : $p['name'],'url'=>url('/paket/'.$p['slug'])];
                }
            }
            $breadcrumbs=[
                 ['@type'=>'ListItem','position'=>1,'name'=>'Ana Sayfa','item'=>url('/')],
                 ['@type'=>'ListItem','position'=>2,'name'=>'Hizmetler','item'=>url('/kategoriler')]
            ];
            if (!empty($category['parent_category_slug'])) $breadcrumbs[]=['@type'=>'ListItem','position'=>3,
                'name'=>$category['parent_category_name'],'item'=>url(\App\Services\PublicSeoUrls::path('/kategori',$category['parent_category_slug']))];
            $breadcrumbs[]=['@type'=>'ListItem','position'=>count($breadcrumbs)+1,'name'=>$category['name'],'item'=>$canonical];
            $graph=[
              ['@type'=>'BreadcrumbList','@id'=>$canonical.'#breadcrumbs','itemListElement'=>$breadcrumbs],
              ['@type'=>'CollectionPage','@id'=>$canonical.'#page',
                 'name'=>$category['seo_title']?:$category['name'],
                 'description'=>$category['seo_description']?:$category['description'],
                 'url'=>$canonical,'inLanguage'=>'tr-TR',
                 'mainEntity'=>['@type'=>'ItemList','numberOfItems'=>count($items),'itemListElement'=>$items]]
            ];
            $schema=json_encode(['@context'=>'https://schema.org','@graph'=>$graph],
                JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
        }

        $socialFilter = $isSocialSale && (!empty($_GET['hizmet']) || $q !== '');
        // Filter variants must not inherit an admin-managed index directive.
        if ($socialFilter) $categorySeo['robots'] = 'noindex,follow';

        $this->render('frontend/category-detail', [
            'pageTitle' => $category['seo_title'] ?: $category['name'] . ' - ' . setting('site_name'),
            'metaDescription' => $category['seo_description'] ?: $category['description'],
            'canonicalUrl' => url('/kategori/' . $category['slug']),
            'ogTitle' => $category['og_title'] ?: $category['name'],
            'ogDescription' => $category['og_description'] ?: $category['seo_description'],
            'category' => $category,
            'categoryHero' => $categoryHero,
            'ogImage' => url($categoryHero['src']),
            'schema' => $schema,
            'nvSeoData' => $categorySeo,
            'packages' => $packages,
            'socialCatalog' => $socialCatalog,
            'noindex' => $socialFilter,
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
