<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Upload;
use App\Services\ReferencesService;

class ReferencesController extends Controller
{
    public function index(): void
    {
        $this->renderAdmin('admin/references/index', [
            'pageTitle' => 'Referanslarımız', 'references' => ReferencesService::all(),
        ]);
    }

    private function fields(?array $existing = null): array
    {
        $title = mb_substr(trim((string)($_POST['title'] ?? '')), 0, 140);
        if ($title === '') throw new \InvalidArgumentException('Proje adı zorunludur.');
        $url = trim((string)($_POST['url'] ?? ''));
        if ($url !== '') {
            $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
            if (!filter_var($url, FILTER_VALIDATE_URL) || !in_array($scheme, ['https','http'], true)) {
                throw new \InvalidArgumentException('Bağlantı yalnızca http/https adresi olabilir.');
            }
        }
        $row = [
            'id' => $existing['id'] ?? bin2hex(random_bytes(8)),
            'title' => $title,
            'category' => mb_substr(trim((string)($_POST['category'] ?? '')), 0, 100),
            'description' => mb_substr(trim((string)($_POST['description'] ?? '')), 0, 260),
            'url' => $url,
            'image' => $existing['image'] ?? '',
            'sort_order' => max(0, min(9999, (int)($_POST['sort_order'] ?? 100))),
            'status' => ($_POST['status'] ?? '') === 'active' ? 'active' : 'inactive',
        ];
        if (!empty($_FILES['image']['name'])) {
            $upload = Upload::image($_FILES['image'], 'references');
            if (!$upload) throw new \InvalidArgumentException('Görsel yüklenemedi; JPG, PNG veya WebP deneyin.');
            $row['image'] = $upload;
        }
        return $row;
    }

    public function store(): void
    {
        Csrf::check();
        try {
            $rows = ReferencesService::all();
            if (count($rows) >= 36) throw new \RuntimeException('En fazla 36 referans eklenebilir.');
            $rows[] = $this->fields();
            ReferencesService::save($rows);
            flash('success','Referans eklendi.');
        } catch (\Throwable $e) {
            flash('error',$e instanceof \InvalidArgumentException ? $e->getMessage() : 'Referans eklenemedi.');
        }
        redirect('/admin/referanslar');
    }

    public function update(string $id): void
    {
        Csrf::check();
        try {
            $rows = ReferencesService::all(); $found = false;
            foreach ($rows as &$row) {
                if ($row['id'] !== $id) continue;
                $row = $this->fields($row);
                $found = true;
                break;
            }
            unset($row);
            if (!$found) throw new \InvalidArgumentException('Referans bulunamadı.');
            ReferencesService::save($rows);
            flash('success', 'Referans güncellendi.');
        } catch (\Throwable $e) {
            flash('error', $e instanceof \InvalidArgumentException ? $e->getMessage() : 'Referans güncellenemedi.');
        }
        redirect('/admin/referanslar');
    }

    public function delete(string $id): void
    {
        Csrf::check();
        $rows = array_values(array_filter(ReferencesService::all(), static fn($r)=>$r['id']!==$id));
        try { ReferencesService::save($rows); flash('success','Referans kaldırıldı.'); }
        catch (\Throwable $e) { flash('error','Referans silinemedi.'); }
        redirect('/admin/referanslar');
    }
}
