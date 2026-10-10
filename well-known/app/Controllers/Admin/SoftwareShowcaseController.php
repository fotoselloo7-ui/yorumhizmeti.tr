<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\SoftwareCatalogService;

class SoftwareShowcaseController extends Controller
{
    public function index(): void
    {
        $this->renderAdmin('admin/software-showcase/index', [
            'pageTitle' => 'Hazır Yazılımlar Vitrini',
            'root' => SoftwareCatalogService::root(),
            'categories' => SoftwareCatalogService::definitions(),
            'packages' => SoftwareCatalogService::eligiblePackages(),
            'otherPackages' => SoftwareCatalogService::otherPackages(),
            'prefs' => SoftwareCatalogService::preferences(),
            'softwareCategoryReady' => count(SoftwareCatalogService::softwareCategoryOptions()) > 1,
        ]);
    }

    /** Explicit, CSRF-protected, one-click first setup and editor launch. */
    public function setupThenCreate(): void
    {
        Csrf::check();
        flash('success', 'Hazır yazılımlar NetVera katalog yönetiminden eklenir; eski kategori kurulumu kapatıldı.');
        redirect('/admin/netvera-yazilimlar/ekle');
    }

    public function install(): void
    {
        Csrf::check();
        flash('success', 'Mükerrer hazır yazılım kategorileri artık oluşturulmuyor. Mevcut NetVera kataloğunu kullanın.');
        redirect('/admin/netvera-yazilimlar');
    }

    public function save(): void
    {
        Csrf::check();
        flash('success', 'Yazılım vitrini NetVera katalog panelinden yönetiliyor.');
        redirect('/admin/netvera-yazilimlar');
    }


}
