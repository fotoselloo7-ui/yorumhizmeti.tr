<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Upload;
use App\Services\SeoScoreService;

class CategoryController extends Controller
{
    public function index(): void
    {
        $categories = $this->db->fetchAll("SELECT c.*, (SELECT COUNT(*) FROM packages WHERE category_id = c.id) as package_count FROM categories c ORDER BY c.sort_order ASC");
        $this->renderAdmin('admin/categories/index', ['pageTitle' => 'Kategoriler', 'categories' => $categories]);
    }

    public function optimizeCategorySeo(): void
    {
        Csrf::check();
        $admin=\App\Core\AdminAuth::admin();
        if(!$admin||($admin['role']??'')!=='super_admin'){
            http_response_code(403);exit('Bu işlem için süper yönetici yetkisi gerekiyor.');
        }
        try{
            $report=\App\Services\CategorySearchBlueprint::apply();
            logActivity('category_seo_blueprint','Kategori SEO/GEO taslakları uygulandı.');
            flash('success',$report['categories'].' kategoride '.$report['fields'].' SEO alanı ve '.$report['seo_profiles'].' GEO/AIO profili güncellendi.');
        }catch(\Throwable $e){
            error_log('Category SEO blueprint: '.get_class($e).' '.$e->getMessage());
            flash('error','SEO profilleri kaydedilemedi. Sunucu kayıtlarını kontrol edin.');
        }
        redirect('/admin/kategoriler');
    }

    public function create(): void
    {
        $parents = $this->db->fetchAll("SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name");
        $icons = \App\Services\IconService::list();
        $this->renderAdmin('admin/categories/form', ['pageTitle' => 'Kategori Ekle', 'category' => null, 'parents' => $parents, 'icons' => $icons]);
    }

    public function store(): void
    {
        Csrf::check();
        $data = $this->getCategoryData();

        if (!empty($_FILES['image']['name'])) {
            $data['image'] = Upload::image($_FILES['image'], 'categories');
        }

        $seo = new SeoScoreService();
        $score = $seo->calculate($data);
        $data['seo_score'] = $score['score'];

        $newId = $this->db->insert('categories', $data);
        \App\Services\NetveraSeoBridge::save('category', $newId, $_POST);
        logActivity('category_create', 'Kategori oluşturuldu: ' . $data['name']);
        flash('success', 'Kategori oluşturuldu.');
        redirect('/admin/kategoriler');
    }

    public function edit(string $id): void
    {
        $category = $this->db->fetch("SELECT * FROM categories WHERE id = ?", [(int) $id]);
        if (!$category) { redirect('/admin/kategoriler'); }

        $parents = $this->db->fetchAll("SELECT id, name FROM categories WHERE parent_id IS NULL AND id != ? ORDER BY name", [$id]);
        $icons = \App\Services\IconService::list();
        $seo = (new SeoScoreService())->calculate($category);

        $this->renderAdmin('admin/categories/form', [
            'pageTitle' => 'Kategori Düzenle',
            'category' => $category,
            'parents' => $parents,
            'icons' => $icons,
            'seoResult' => $seo,
            'nvSeoData' => \App\Services\NetveraSeoBridge::get('category',(int)$id),
        ]);
    }

    public function update(string $id): void
    {
        Csrf::check();
        $data = $this->getCategoryData();

        if (!empty($_FILES['image']['name'])) {
            $data['image'] = Upload::image($_FILES['image'], 'categories');
        }

        $seo = new SeoScoreService();
        $score = $seo->calculate($data);
        $data['seo_score'] = $score['score'];

        $this->db->update('categories', $data, 'id = ?', [(int) $id]);
        \App\Services\NetveraSeoBridge::save('category',(int)$id,$_POST);
        logActivity('category_update', 'Kategori güncellendi: ' . $data['name']);
        flash('success', 'Kategori güncellendi.');
        redirect('/admin/kategoriler');
    }

    public function delete(string $id): void
    {
        Csrf::check();
        $categoryId = (int) $id;
        $hasPackages = $this->db->fetch("SELECT id FROM packages WHERE category_id = ? LIMIT 1", [$categoryId]);
        $hasChildren = $this->db->fetch("SELECT id FROM categories WHERE parent_id = ? LIMIT 1", [$categoryId]);
        if ($hasPackages || $hasChildren) {
            $this->db->update('categories', ['status' => 'inactive'], 'id = ?', [$categoryId]);
            flash('warning', 'Bu kategori bağlı paket veya alt kategoriler içerdiği için silinmedi, pasife alındı.');
        } else {
            $this->db->delete('categories', 'id = ?', [$categoryId]);
            flash('success', 'Kategori silindi.');
        }
        logActivity('category_delete', 'Kategori silme/pasife alma işlemi: ID ' . $id);
        redirect('/admin/kategoriler');
    }

    public function toggleStatus(string $id): void
    {
        Csrf::check();
        $cat = $this->db->fetch("SELECT status FROM categories WHERE id = ?", [(int) $id]);
        $newStatus = $cat['status'] === 'active' ? 'inactive' : 'active';
        $this->db->update('categories', ['status' => $newStatus], 'id = ?', [(int) $id]);
        flash('success', 'Durum güncellendi.');
        redirect('/admin/kategoriler');
    }

    public function bulkAction(): void
    {
        Csrf::check();
        $action = $_POST['action'] ?? '';
        $ids = $_POST['ids'] ?? [];

        if (empty($ids) || !is_array($ids)) {
            flash('error', 'Lütfen en az bir kategori seçin.');
            redirect('/admin/kategoriler');
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn($id) => $id > 0)));
        if (!$ids) {
            flash('error', 'Geçerli bir kategori seçin.');
            redirect('/admin/kategoriler');
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $successCount = 0;
        $skippedCount = 0;

        switch ($action) {
            case 'active':
                $this->db->query("UPDATE categories SET status = 'active' WHERE id IN ($placeholders)", $ids);
                flash('success', count($ids) . ' kategori aktif edildi.');
                break;
            case 'inactive':
                $this->db->query("UPDATE categories SET status = 'inactive' WHERE id IN ($placeholders)", $ids);
                flash('success', count($ids) . ' kategori pasife alındı.');
                break;
            case 'delete':
                foreach ($ids as $id) {
                    // Paket var mı?
                    $hasPackages = $this->db->fetch("SELECT id FROM packages WHERE category_id = ? LIMIT 1", [$id]);
                    // Alt kategori var mı?
                    $hasSub = $this->db->fetch("SELECT id FROM categories WHERE parent_id = ? LIMIT 1", [$id]);
                    
                    if ($hasPackages || $hasSub) {
                        // Pasif yap
                        $this->db->update('categories', ['status' => 'inactive'], 'id = ?', [$id]);
                        $skippedCount++;
                    } else {
                        // Hard delete
                        $this->db->delete('categories', 'id = ?', [$id]);
                        $successCount++;
                    }
                }
                
                if ($skippedCount > 0) {
                    flash('warning', "$successCount kategori silindi. $skippedCount kategori içinde paket veya alt kategori bulunduğu için silinemedi, pasife alındı.");
                } else {
                    flash('success', "$successCount kategori başarıyla silindi.");
                }
                break;
            default:
                flash('error', 'Geçersiz işlem.');
        }

        redirect('/admin/kategoriler');
    }

    public function seedDefault(): void
    {
        Csrf::check();
        
        $sqlPath = dirname(__DIR__, 3) . '/database/seed_default_categories.sql';
        if (!file_exists($sqlPath)) {
            flash('error', 'SQL seed dosyası bulunamadı. Lütfen dosyayı şu konuma yüklediğinizden emin olun: ' . $sqlPath);
            redirect('/admin/kategoriler');
        }

        $sql = file_get_contents($sqlPath);
        
        // Execute SQL script. Since it might have multiple statements, we might need to split or use a loop.
        // Actually PDO doesn't always support multiple queries in a single prepare/execute depending on emulation.
        // But the db class might handle it or we can just parse it. Let's do a simple split by ';'
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        $executed = 0;
        
        try {
            foreach ($queries as $query) {
                if (!empty($query)) {
                    $this->db->query($query);
                    $executed++;
                }
            }
            flash('success', "Varsayılan kategoriler başarıyla kuruldu/güncellendi.");
        } catch (\Exception $e) {
            flash('error', "Kurulum sırasında hata oluştu: " . $e->getMessage());
        }

        redirect('/admin/kategoriler');
    }

    public function exportCsv(): void
    {
        $categories = $this->db->fetchAll("
            SELECT c.id, c.parent_id, c.name, c.slug, p.name as parent_name, c.status 
            FROM categories c 
            LEFT JOIN categories p ON c.parent_id = p.id 
            ORDER BY c.parent_id ASC, c.sort_order ASC
        ");

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=kategoriler_' . date('Y-m-d') . '.csv');
        $output = fopen('php://output', 'w');
        fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); // UTF-8 BOM
        fputcsv($output, ['id', 'parent_id', 'name', 'slug', 'parent_name', 'status']);
        
        foreach ($categories as $cat) {
            fputcsv($output, [
                $cat['id'], 
                $cat['parent_id'], 
                $cat['name'], 
                $cat['slug'], 
                $cat['parent_name'], 
                $cat['status']
            ]);
        }
        fclose($output);
        exit;
    }

    private function getCategoryData(): array
    {
        return [
            'parent_id' => !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null,
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? '') ?: slugify($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'image_alt' => trim($_POST['image_alt'] ?? ''),
            'icon_key' => $_POST['icon_key'] ?? 'package',
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
            'seo_title' => trim($_POST['seo_title'] ?? ''),
            'seo_description' => trim($_POST['seo_description'] ?? ''),
            'seo_focus_keyword' => trim($_POST['seo_focus_keyword'] ?? ''),
            'canonical_url' => trim($_POST['canonical_url'] ?? ''),
            'og_title' => trim($_POST['og_title'] ?? ''),
            'og_description' => trim($_POST['og_description'] ?? ''),
        ];
    }
}
