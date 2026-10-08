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
            'prefs' => SoftwareCatalogService::preferences(),
        ]);
    }

    public function install(): void
    {
        Csrf::check();
        try {
            $result = SoftwareCatalogService::installCategories();
            flash('success', $result['created'].' kategori eklendi. '.$result['existing']
                .' kategori zaten kayıtlı. '.$result['conflicts'].' çakışan slug atlandı; mevcut içerik değiştirilmedi.');
        } catch (\Throwable $e) {
            error_log('Software category setup: '.$e->getMessage());
            flash('error', 'Kategori kurulumu yapılamadı. Veritabanı hata kaydını kontrol edin.');
        }
        redirect('/admin/hazir-yazilimlar');
    }

    public function save(): void
    {
        Csrf::check();
        $chosen = is_array($_POST['featured'] ?? null) ? $_POST['featured'] : [];
        $order = is_array($_POST['positions'] ?? null) ? $_POST['positions'] : [];
        // Each selected product can be ordered independently of ordinary packages.
        $chosen = array_values(array_unique(array_filter(array_map('intval', $chosen))));
        usort($chosen, static fn($a,$b)=>
            ((int)($order[$a] ?? 9999) <=> (int)($order[$b] ?? 9999)) ?: ($a <=> $b));
        try {
            SoftwareCatalogService::save($chosen, isset($_POST['enabled']));
            flash('success', 'Hazır yazılım vitrini ve sıralaması kaydedildi.');
        } catch (\Throwable $e) {
            error_log('Software showcase save: '.$e->getMessage());
            flash('error', 'Vitrin ayarları kaydedilemedi.');
        }
        redirect('/admin/hazir-yazilimlar');
    }
}
