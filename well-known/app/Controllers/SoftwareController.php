<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Services\SoftwareCatalogService;

class SoftwareController extends Controller
{
    public function index(): void
    {
        $types = SoftwareCatalogService::definitions();
        $filter = trim((string)($_GET['tur'] ?? ''));
        $allowed = array_column($types, 1);
        if ($filter !== '' && !in_array($filter, $allowed, true)) $filter = '';
        $packages = SoftwareCatalogService::publicPackages();
        if ($filter !== '') {
            $packages = array_values(array_filter($packages, static fn(array $p): bool =>
                ($p['category_slug'] ?? '') === $filter));
        }
        $this->render('frontend/software', [
            'pageTitle' => 'Hazır Yazılımlar ve Web Sitesi Scriptleri',
            'metaDescription' => 'Haber sitesi, e-ticaret, emlak, blog, rezervasyon ve kurumsal web çözümlerimizi keşfedin.',
            'catalogTypes' => $types,
            'softwareFilter' => $filter,
            'softwarePackages' => $packages,
        ]);
    }
}
