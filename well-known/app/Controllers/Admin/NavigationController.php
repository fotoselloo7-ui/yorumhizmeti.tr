<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\NavigationService;

final class NavigationController extends Controller
{
    public function index(): void
    {
        $this->renderAdmin('admin/navigation/index', [
            'pageTitle' => 'Menü Yönetimi',
            'menuItems' => NavigationService::items(true),
        ]);
    }

    public function save(): void
    {
        Csrf::check();

        $enabled = $_POST['enabled'] ?? [];
        $sort = $_POST['sort'] ?? [];
        $labels = $_POST['label'] ?? [];
        if (!is_array($enabled) || !is_array($sort) || !is_array($labels)) {
            flash('error', 'Geçersiz menü verisi.');
            redirect('/admin/menu');
        }

        NavigationService::save(
            array_values(array_filter($enabled, 'is_string')),
            $sort,
            $labels
        );
        logActivity('navigation_update', 'Üst menü öğeleri ve sıralaması güncellendi.');
        flash('success', 'Menü kaydedildi. Gizlenen öğeleri dilediğiniz zaman yeniden aktif edebilirsiniz.');
        redirect('/admin/menu');
    }

    public function reset(): void
    {
        Csrf::check();
        NavigationService::reset();
        logActivity('navigation_reset', 'Üst menü varsayılan görünümüne alındı.');
        flash('success', 'Varsayılan menü yeniden etkinleştirildi.');
        redirect('/admin/menu');
    }
}
