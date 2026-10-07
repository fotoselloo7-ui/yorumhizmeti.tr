<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Upload;
use App\Services\SeoScoreService;

class PackageController extends Controller
{
    public function index(): void
    {
        $packages = $this->db->fetchAll("SELECT p.*, c.name as category_name, c.icon_key FROM packages p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.sort_order ASC, p.id DESC");
        $categories = $this->db->fetchAll("SELECT id, name, parent_id FROM categories WHERE status = 'active' ORDER BY parent_id ASC, name ASC");
        $this->renderAdmin('admin/packages/index', ['pageTitle' => 'Paketler', 'packages' => $packages, 'categories' => $categories]);
    }

    public function create(): void
    {
        $categories = $this->db->fetchAll("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name");
        $this->renderAdmin('admin/packages/form', ['pageTitle' => 'Paket Ekle', 'package' => null, 'categories' => $categories, 'fields' => []]);
    }

    public function store(): void
    {
        Csrf::check();
        $data = $this->getPackageData();
        if (!empty($_FILES['image']['name'])) { $data['image'] = Upload::image($_FILES['image'], 'packages'); }
        $data['seo_score'] = (new SeoScoreService())->calculate($data)['score'];

        $pkgId = $this->db->insert('packages', $data);
        logActivity('package_create', 'Paket oluşturuldu: ' . $data['name']);
        flash('success', 'Paket oluşturuldu.');
        redirect('/admin/paketler');
    }

    public function edit(string $id): void
    {
        $package = $this->db->fetch("SELECT * FROM packages WHERE id = ?", [(int) $id]);
        if (!$package) { redirect('/admin/paketler'); }
        $categories = $this->db->fetchAll("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name");
        $fields = $this->db->fetchAll("SELECT * FROM package_fields WHERE package_id = ? ORDER BY sort_order", [(int) $id]);
        $seo = (new SeoScoreService())->calculate($package);

        $this->renderAdmin('admin/packages/form', [
            'pageTitle' => 'Paket Düzenle', 'package' => $package, 'categories' => $categories, 'fields' => $fields, 'seoResult' => $seo
        ]);
    }

    public function update(string $id): void
    {
        Csrf::check();
        $data = $this->getPackageData();
        if (!empty($_FILES['image']['name'])) { $data['image'] = Upload::image($_FILES['image'], 'packages'); }
        $data['seo_score'] = (new SeoScoreService())->calculate($data)['score'];

        $this->db->update('packages', $data, 'id = ?', [(int) $id]);
        logActivity('package_update', 'Paket güncellendi: ' . $data['name']);
        flash('success', 'Paket güncellendi.');
        redirect('/admin/paketler');
    }

    public function delete(string $id): void
    {
        Csrf::check();
        $this->db->delete('packages', 'id = ?', [(int) $id]);
        $this->db->delete('package_fields', 'package_id = ?', [(int) $id]);
        logActivity('package_delete', 'Paket silindi: ID ' . $id);
        flash('success', 'Paket silindi.');
        redirect('/admin/paketler');
    }

    public function toggleStatus(string $id): void
    {
        Csrf::check();
        $pkg = $this->db->fetch("SELECT status FROM packages WHERE id = ?", [(int) $id]);
        $this->db->update('packages', ['status' => $pkg['status'] === 'active' ? 'inactive' : 'active'], 'id = ?', [(int) $id]);
        flash('success', 'Durum güncellendi.');
        redirect('/admin/paketler');
    }

    public function fields(string $id): void
    {
        $package = $this->db->fetch("SELECT * FROM packages WHERE id = ?", [(int) $id]);
        if (!$package) { redirect('/admin/paketler'); }
        $fields = $this->db->fetchAll("SELECT * FROM package_fields WHERE package_id = ? ORDER BY sort_order", [(int) $id]);
        $this->renderAdmin('admin/packages/fields', ['pageTitle' => 'Paket Alanları: ' . $package['name'], 'package' => $package, 'fields' => $fields]);
    }

    public function saveFields(string $id): void
    {
        Csrf::check();
        $this->db->delete('package_fields', 'package_id = ?', [(int) $id]);
        $keys = $_POST['field_key'] ?? [];
        $labels = $_POST['field_label'] ?? [];
        $types = $_POST['field_type'] ?? [];
        $required = $_POST['field_required'] ?? [];
        $placeholders = $_POST['field_placeholder'] ?? [];

        foreach ($keys as $i => $key) {
            if (empty($key)) continue;
            $this->db->insert('package_fields', [
                'package_id' => (int) $id,
                'field_key' => $key,
                'field_label' => $labels[$i] ?? '',
                'field_type' => $types[$i] ?? 'text',
                'is_required' => isset($required[$i]) ? 1 : 0,
                'placeholder' => $placeholders[$i] ?? '',
                'sort_order' => $i,
            ]);
        }
        flash('success', 'Paket alanları kaydedildi.');
        redirect('/admin/paket/' . $id . '/alanlar');
    }

    public function bulkAction(): void
    {
        Csrf::check();
        $action = $_POST['action'] ?? '';
        $ids = $_POST['ids'] ?? [];

        if (empty($ids) || !is_array($ids)) {
            flash('error', 'Lütfen en az bir paket seçin.');
            redirect('/admin/paketler');
        }

        $ids = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $successCount = 0;
        $skippedCount = 0;

        switch ($action) {
            case 'active':
                $this->db->query("UPDATE packages SET status = 'active' WHERE id IN ($placeholders)", $ids);
                flash('success', count($ids) . ' paket aktif edildi.');
                break;
            case 'inactive':
                $this->db->query("UPDATE packages SET status = 'inactive' WHERE id IN ($placeholders)", $ids);
                flash('success', count($ids) . ' paket pasife alındı.');
                break;
            case 'featured':
                $this->db->query("UPDATE packages SET is_featured = 1 WHERE id IN ($placeholders)", $ids);
                flash('success', count($ids) . ' paket öne çıkarıldı.');
                break;
            case 'unfeatured':
                $this->db->query("UPDATE packages SET is_featured = 0 WHERE id IN ($placeholders)", $ids);
                flash('success', count($ids) . ' paket öne çıkarılanlardan kaldırıldı.');
                break;
            case 'change_category':
                $newCatId = (int) ($_POST['new_category_id'] ?? 0);
                if ($newCatId > 0) {
                    $params = array_merge([$newCatId], $ids);
                    $this->db->query("UPDATE packages SET category_id = ? WHERE id IN ($placeholders)", $params);
                    flash('success', count($ids) . ' paketin kategorisi değiştirildi.');
                }
                break;
            case 'delete':
                foreach ($ids as $id) {
                    // Sipariş kontrolü
                    $inOrders = $this->db->fetch("SELECT id FROM order_items WHERE package_id = ? LIMIT 1", [$id]);
                    if ($inOrders) {
                        // Siparişte kullanılmış, silme yerine pasif yap
                        $this->db->update('packages', ['status' => 'inactive'], 'id = ?', [$id]);
                        $skippedCount++;
                    } else {
                        // Hard delete
                        $this->db->delete('packages', 'id = ?', [$id]);
                        $this->db->delete('package_fields', 'package_id = ?', [$id]);
                        $successCount++;
                    }
                }
                if ($skippedCount > 0) {
                    flash('warning', "$successCount paket silindi. $skippedCount paket sipariş geçmişinde bulunduğu için silinemedi, pasife alındı.");
                } else {
                    flash('success', "$successCount paket başarıyla silindi.");
                }
                break;
            default:
                flash('error', 'Geçersiz işlem.');
        }

        redirect('/admin/paketler');
    }

    private function getPackageData(): array
    {
        return [
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? '') ?: slugify($_POST['name'] ?? ''),
            'short_description' => trim($_POST['short_description'] ?? ''),
            'description' => $_POST['description'] ?? '',
            'image_alt' => trim($_POST['image_alt'] ?? ''),
            'price' => (float) ($_POST['price'] ?? 0),
            'discount_price' => !empty($_POST['discount_price']) ? (float) $_POST['discount_price'] : null,
            'delivery_time' => trim($_POST['delivery_time'] ?? ''),
            'min_quantity' => (int) ($_POST['min_quantity'] ?? 1),
            'max_quantity' => (int) ($_POST['max_quantity'] ?? 1),
            'badge' => trim($_POST['badge'] ?? '') ?: null,
            'status' => $_POST['status'] ?? 'active',
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'seo_title' => trim($_POST['seo_title'] ?? ''),
            'seo_description' => trim($_POST['seo_description'] ?? ''),
            'seo_focus_keyword' => trim($_POST['seo_focus_keyword'] ?? ''),
            'canonical_url' => trim($_POST['canonical_url'] ?? ''),
            'og_title' => trim($_POST['og_title'] ?? ''),
            'og_description' => trim($_POST['og_description'] ?? ''),
        ];
    }
}
