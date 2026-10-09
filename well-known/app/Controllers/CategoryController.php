<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Database;

class CategoryController extends Controller
{
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
            'pageTitle' => 'Hizmet Kategorileri - ' . setting('site_name'),
            'metaDescription' => 'Sosyal medya, ajans, yazılım, SEO ve dijital pazarlama hizmetlerini keşfedin.',
            'catalogGroups' => $groups,
            'activeCatalogGroup' => $activeGroup,
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
        $category = \App\Services\CategorySearchBlueprint::decorate($category);

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

        $categorySeo=\App\Services\NetveraSeoBridge::get('category',(int)$category['id']);
        // Editorial answers are visible immediately, even before the optional admin SEO batch is applied.
        $categoryIntent=\App\Services\CategorySearchBlueprint::profileForCategory($category);
        if($categoryIntent){
            if(empty($categorySeo['main_question']))$categorySeo['main_question']=$categoryIntent['question'];
            if(empty($categorySeo['direct_answer']))$categorySeo['direct_answer']=$categoryIntent['answer'];
            if(empty($categorySeo['secondary_keywords']))$categorySeo['secondary_keywords']=$categoryIntent['secondary'];
        }

        $schema=null;
        if(!preg_match('/(yaz[iı]l[iı]m|haz[iı]r.script|software|cms|web.site|tema)/iu',
             (string)$category['slug'].' '.(string)$category['name'])){
            $canonical=url('/kategori/'.$category['slug']);
            $items=[];
            foreach(array_slice($packages,0,35) as $n=>$p){
                $items[]=['@type'=>'ListItem','position'=>$n+1,
                    'name'=>$p['name'],'url'=>url('/paket/'.$p['slug'])];
            }
            $graph=[
              ['@type'=>'BreadcrumbList','@id'=>$canonical.'#breadcrumbs','itemListElement'=>[
                 ['@type'=>'ListItem','position'=>1,'name'=>'Ana Sayfa','item'=>url('/')],
                 ['@type'=>'ListItem','position'=>2,'name'=>'Hizmet Kategorileri','item'=>url('/kategoriler')],
                 ['@type'=>'ListItem','position'=>3,'name'=>$category['name'],'item'=>$canonical]
              ]],
              ['@type'=>'CollectionPage','@id'=>$canonical.'#page',
                 'name'=>$category['seo_title']?:$category['name'],
                 'description'=>$category['seo_description']?:$category['description'],
                 'url'=>$canonical,'inLanguage'=>'tr-TR',
                 'mainEntity'=>['@type'=>'ItemList','numberOfItems'=>count($items),'itemListElement'=>$items]]
            ];
            $schema=json_encode(['@context'=>'https://schema.org','@graph'=>$graph],
                JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
        }

        $this->render('frontend/category-detail', [
            'pageTitle' => $category['seo_title'] ?: $category['name'] . ' - ' . setting('site_name'),
            'metaDescription' => $category['seo_description'] ?: $category['description'],
            'canonicalUrl' => url('/kategori/' . $category['slug']),
            'ogTitle' => $category['og_title'] ?: $category['name'],
            'ogDescription' => $category['og_description'] ?: $category['seo_description'],
            'category' => $category,
            'schema' => $schema,
            'nvSeoData' => $categorySeo,
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
