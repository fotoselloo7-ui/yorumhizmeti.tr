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
            'referenceGroups' => ReferencesService::groups(),
            'referenceMediaTypes' => ReferencesService::mediaTypes(),
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
        $groups = ReferencesService::groups();
        $group = (string)($_POST['group'] ?? ($existing['group'] ?? 'agency'));
        $service = (string)($_POST['service'] ?? ($existing['service'] ?? 'web-site'));
        $media = (string)($_POST['media_type'] ?? ($existing['media_type'] ?? 'website'));
        if (!isset($groups[$group]) || !isset($groups[$group]['services'][$service])) {
            throw new \InvalidArgumentException('Lütfen geçerli bir referans kategorisi seçin.');
        }
        $secondGroup = trim((string)($_POST['secondary_group'] ?? ''));
        $secondService = trim((string)($_POST['secondary_service'] ?? ''));
        $placements = [['group'=>$group, 'service'=>$service]];
        if ($secondGroup !== '') {
            if (!isset($groups[$secondGroup]['services'][$secondService])) {
                throw new \InvalidArgumentException('İkinci kategori için geçerli bir alt hizmet seçin.');
            }
            if ($secondGroup === $group && $secondService === $service) {
                throw new \InvalidArgumentException('Aynı kategori ve alt hizmeti iki kez seçmeyin.');
            }
            $placements[] = ['group'=>$secondGroup, 'service'=>$secondService];
        }
        if (!array_key_exists($media, ReferencesService::mediaTypes())) {
            throw new \InvalidArgumentException('Geçersiz referans içerik türü.');
        }
        if (str_starts_with($media, 'instagram_')) {
            if (!ReferencesService::instagramEmbed($url, $media)) {
                throw new \InvalidArgumentException('Herkese açık Instagram gönderi veya Reels bağlantısını (https://www.instagram.com/p/... ya da /reel/...) girin.');
            }
        } elseif ($media === 'website' && $url === '') {
            throw new \InvalidArgumentException('Web sitesi veya yazılım referanslarında müşteri sitesi bağlantısı zorunludur.');
        }
        $row = [
            'id' => $existing['id'] ?? bin2hex(random_bytes(8)),
            'title' => $title,
            'category' => mb_substr(trim((string)($_POST['category'] ?? '')), 0, 100),
            'description' => mb_substr(trim((string)($_POST['description'] ?? '')), 0, 260),
            'url' => $url,
            'group' => $group,
            'service' => $service,
            'placements' => $placements,
            'media_type' => $media,
            'image' => $existing['image'] ?? '',
            'logo' => $existing['logo'] ?? '',
            'video' => $existing['video'] ?? '',
            'sort_order' => max(0, min(9999, (int)($_POST['sort_order'] ?? 100))),
            'status' => ($_POST['status'] ?? '') === 'active' ? 'active' : 'inactive',
        ];
        foreach (['image' => 'Kapak görseli', 'logo' => 'Logo'] as $field => $label) {
            if (!empty($_FILES[$field]['name'])) {
                $upload = Upload::image($_FILES[$field], 'references');
                if (!$upload) {
                    throw new \InvalidArgumentException($label.' yüklenemedi. JPG, PNG veya WebP (en fazla 5 MB) kullanın.');
                }
                $row[$field] = $upload;
            }
        }
        // Uploading a file is optional; Instagram URLs and covers continue to work.
        if (!empty($_POST['remove_video'])) $row['video'] = '';
        if (!empty($_FILES['video']['name'])) {
            if (!str_starts_with($media, 'instagram_')) {
                throw new \InvalidArgumentException('Yerel video yalnızca Instagram gönderisi veya Reels referansında kullanılabilir.');
            }
            $uploadedVideo = Upload::video($_FILES['video'], 'references/videos');
            if (!$uploadedVideo) {
                throw new \InvalidArgumentException('Video yüklenemedi. MP4/WebM (en fazla 80 MB) kullanın; PHP upload_max_filesize ayarını kontrol edin.');
            }
            $row['video'] = $uploadedVideo;
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
