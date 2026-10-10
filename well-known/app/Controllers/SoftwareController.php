<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\SoftwareCatalogService;

class SoftwareController extends Controller
{
    public function typePage(string $slug): void
    {
        $slug = rawurldecode($slug);
        if (!in_array($slug, array_column(SoftwareCatalogService::definitions(), 1), true)) {
            \App\Services\PublicSeoUrls::notFound('Yazılım Türü Bulunamadı'); return;
        }
        $_GET['tur'] = $slug;
        $this->index();
    }

    public function index(): void
    {
        $types = SoftwareCatalogService::definitions();
        $allowed = array_column($types, 1);
        $groupSlugs = SoftwareCatalogService::taxonomyGroups();
        $groups = [];
        foreach ($groupSlugs as $groupName => $slugs) {
            $groups[$groupName] = array_values(array_filter($types, static fn($row) => in_array($row[1], $slugs, true)));
        }
        $params = $_GET;
        $filter = trim((string)($params['tur'] ?? ''));
        if (!in_array($filter, $allowed, true)) $filter = '';
        $query = mb_substr(trim((string)($params['q'] ?? '')), 0, 90, 'UTF-8');
        $sort = (string)($params['siralama'] ?? 'onerilen');
        if (!in_array($sort, ['onerilen','yeni','ucuz','pahali','ad'],true)) $sort='onerilen';
        $min = filter_var($params['min'] ?? null, FILTER_VALIDATE_FLOAT);
        $max = filter_var($params['max'] ?? null, FILTER_VALIDATE_FLOAT);
        $min = $min === false || $min < 0 ? null : min($min, 100000000);
        $max = $max === false || $max < 0 ? null : min($max, 100000000);
        $discountOnly = ($params['indirim'] ?? '') === '1';
        $featuredOnly = ($params['one_cikan'] ?? '') === '1';

        $all = SoftwareCatalogService::publicPackages();
        $counts = [];
        foreach ($all as $pkg) {
            $slug = $pkg['category_slug'] ?? '';
            $counts[$slug] = ($counts[$slug] ?? 0) + 1;
        }
        $products = array_values(array_filter($all, static function($pkg) use ($filter,$query,$min,$max,$discountOnly,$featuredOnly) {
            if ($filter !== '' && ($pkg['category_slug'] ?? '') !== $filter) return false;
            $searchText = strip_tags(implode(' ',[
                (string)($pkg['name'] ?? ''), (string)($pkg['short_description'] ?? ''),
                (string)($pkg['category_name'] ?? ''), (string)($pkg['slug'] ?? '')
            ]));
            if ($query !== '' && mb_stripos($searchText,$query,0,'UTF-8') === false) return false;
            $standard = (float)$pkg['price'];
            $sale = ($pkg['discount_price'] !== null && $pkg['discount_price'] !== ''
                && (float)$pkg['discount_price'] < $standard);
            $price = $sale ? (float)$pkg['discount_price'] : $standard;
            if ($min !== null && $price < $min) return false;
            if ($max !== null && $price > $max) return false;
            if ($discountOnly && !$sale) return false;
            if ($featuredOnly && empty($pkg['is_featured'])) return false;
            return true;
        }));
        if ($sort === 'ucuz' || $sort === 'pahali') {
            usort($products, static function($a,$b) use ($sort) {
                $price = static fn($p) => ($p['discount_price'] !== null &&
                    (float)$p['discount_price'] < (float)$p['price'])
                    ? (float)$p['discount_price'] : (float)$p['price'];
                $cmp = $price($a) <=> $price($b);
                return ($sort === 'pahali' ? -$cmp : $cmp) ?: ((int)$a['id'] <=> (int)$b['id']);
            });
        } elseif ($sort === 'yeni') {
            usort($products, static fn($a,$b) => (int)$b['id'] <=> (int)$a['id']);
        } elseif ($sort === 'ad') {
            usort($products, static fn($a,$b) => strcasecmp($a['name'],$b['name']));
        } else {
            usort($products, static fn($a,$b) =>
                ((int)$b['is_featured'] <=> (int)$a['is_featured'])
                ?: ((int)$a['sort_order'] <=> (int)$b['sort_order'])
                ?: ((int)$b['id'] <=> (int)$a['id']));
        }

        $this->render('frontend/software', [
            'pageTitle' => 'Hazır Yazılımlar ve Web Sitesi Scriptleri',
            'metaDescription' => 'Haber, WordPress, e-ticaret, emlak, otomasyon, tema ve eklenti yazılımlarını filtreleyip inceleyin.',
            'canonicalUrl' => url($filter !== ''
                ? \App\Services\PublicSeoUrls::path('/hazir-yazilimlar/tur', $filter)
                : '/hazir-yazilimlar'),
            'noindex' => $query!=='' || $min!==null || $max!==null || $discountOnly
                || $featuredOnly || $sort!=='onerilen',
            'catalogGroups' => $groups,
            'softwareFilter' => $filter,
            'softwareQuery' => $query,
            'softwareSort' => $sort,
            'softwareMin' => $min,
            'softwareMax' => $max,
            'softwareDiscount' => $discountOnly,
            'softwareFeatured' => $featuredOnly,
            'softwareProducts' => $products,
            'softwareAllCount' => count($all),
            'softwareCounts' => $counts,
        ]);
    }
}
