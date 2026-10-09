<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\SiteConfigService;

class SettingsController extends Controller
{
    public function site(): void
    {
        // Migrate only missing/retired demo defaults; existing custom settings remain intact.
        \App\Services\NetveraBrandSettings::syncExisting(SiteConfigService::getInstance());
        $groups = ['general', 'contact', 'social', 'seo', 'branding', 'geo', 'aio', 'appearance', 'footer'];
        $settings = [];
        foreach ($groups as $g) {
            $settings[$g] = $this->db->fetchAll("SELECT * FROM settings WHERE setting_group = ?", [$g]);
        }
        $this->renderAdmin('admin/settings/site', ['pageTitle' => 'Site Ayarları', 'settings' => $settings]);
    }

    public function saveSite(): void
    {
        Csrf::check();
        $config = SiteConfigService::getInstance();
        foreach ($_POST as $key => $value) {
            if ($key === '_csrf_token') continue;
            if (str_starts_with($key, '_group_')) continue;
            $group = $_POST['_group_' . $key] ?? 'general';
            // HEX color validation for theme settings
            if ($key === 'theme_preset_enabled') {
                $value = $value === '1' ? '1' : '0';
            } elseif (str_starts_with($key, 'theme_') && $group === 'theme') {
                $value = trim($value);
                if (!preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) {
                    continue; // skip invalid color values
                }
            }
            $config->set($key, $value, $group);
        }
        logActivity('settings_update', 'Site ayarları güncellendi.');
        flash('success', 'Site ayarları kaydedildi.');
        redirect('/admin/site-ayarlari');
    }

    public function smtp(): void
    {
        $this->renderAdmin('admin/settings/smtp', ['pageTitle' => 'SMTP Ayarları']);
    }

    public function saveSmtp(): void
    {
        Csrf::check();
        $config = SiteConfigService::getInstance();
        $keys = ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'smtp_from_email', 'smtp_from_name'];
        foreach ($keys as $k) {
            $config->set($k, trim($_POST[$k] ?? ''), 'smtp');
        }
        logActivity('smtp_update', 'SMTP ayarları güncellendi.');
        flash('success', 'SMTP ayarları kaydedildi.');
        redirect('/admin/smtp-ayarlari');
    }

    public function bankAccounts(): void
    {
        $accounts = $this->db->fetchAll("SELECT * FROM bank_accounts ORDER BY sort_order ASC");
        $this->renderAdmin('admin/settings/bank-accounts', ['pageTitle' => 'Banka Hesapları', 'accounts' => $accounts]);
    }

    public function storeBankAccount(): void
    {
        Csrf::check();
        $this->db->insert('bank_accounts', [
            'bank_name' => trim($_POST['bank_name'] ?? ''),
            'account_holder' => trim($_POST['account_holder'] ?? ''),
            'iban' => trim($_POST['iban'] ?? ''),
            'branch' => trim($_POST['branch'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
        ]);
        flash('success', 'Banka hesabı eklendi.');
        redirect('/admin/banka-hesaplari');
    }

    public function deleteBankAccount(string $id): void
    {
        Csrf::check();
        $this->db->delete('bank_accounts', 'id = ?', [(int) $id]);
        flash('success', 'Banka hesabı silindi.');
        redirect('/admin/banka-hesaplari');
    }

    public function users(): void
    {
        $users = $this->db->fetchAll("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count FROM users u ORDER BY u.created_at DESC");
        $this->renderAdmin('admin/settings/users', ['pageTitle' => 'Üyeler', 'users' => $users]);
    }

    public function adminUsers(): void
    {
        $admins = $this->db->fetchAll("SELECT * FROM admins ORDER BY id ASC");
        $this->renderAdmin('admin/settings/admin-users', ['pageTitle' => 'Admin Kullanıcıları', 'admins' => $admins]);
    }

    public function pages(): void
    {
        $pages = $this->db->fetchAll("SELECT * FROM pages ORDER BY id ASC");
        $this->renderAdmin('admin/settings/pages', ['pageTitle' => 'Sayfalar', 'pages' => $pages]);
    }

    public function editPage(string $id): void
    {
        $page = $this->db->fetch("SELECT * FROM pages WHERE id = ?", [(int) $id]);
        if (!$page) { redirect('/admin/sayfalar'); }
        $this->renderAdmin('admin/settings/page-form', ['pageTitle' => 'Sayfa Düzenle', 'page' => $page]);
    }

    public function updatePage(string $id): void
    {
        Csrf::check();
        $this->db->update('pages', [
            'title' => trim($_POST['title'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'content' => $_POST['content'] ?? '',
            'status' => $_POST['status'] ?? 'active',
            'seo_title' => trim($_POST['seo_title'] ?? ''),
            'seo_description' => trim($_POST['seo_description'] ?? ''),
        ], 'id = ?', [(int) $id]);
        flash('success', 'Sayfa güncellendi.');
        redirect('/admin/sayfalar');
    }

    public function faqs(): void
    {
        $faqs = $this->db->fetchAll("SELECT * FROM faqs ORDER BY sort_order ASC");
        $this->renderAdmin('admin/settings/faqs', ['pageTitle' => 'SSS Yönetimi', 'faqs' => $faqs]);
    }

    public function storeFaq(): void
    {
        Csrf::check();
        $this->db->insert('faqs', [
            'question' => trim($_POST['question'] ?? ''),
            'answer' => $_POST['answer'] ?? '',
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'status' => 'active',
        ]);
        flash('success', 'SSS eklendi.');
        redirect('/admin/sss');
    }

    public function deleteFaq(string $id): void
    {
        Csrf::check();
        $this->db->delete('faqs', 'id = ?', [(int) $id]);
        flash('success', 'SSS silindi.');
        redirect('/admin/sss');
    }

    public function logs(): void
    {
        $logs = $this->db->fetchAll("SELECT id, admin_id, action, description AS detail, ip_address, created_at FROM activity_logs ORDER BY created_at DESC, id DESC LIMIT 200");
        $this->renderAdmin('admin/settings/logs', ['pageTitle' => 'Loglar', 'logs' => $logs]);
    }

    public function importPage(): void
    {
        $importResult = $_SESSION['import_result'] ?? null;
        unset($_SESSION['import_result']);
        $this->renderAdmin('admin/import/index', ['pageTitle' => 'CSV İçe Aktar', 'importResult' => $importResult]);
    }

    public function downloadBlankTemplate(): void
    {
        $importer = new \App\Services\ExcelImportService();
        $headers = $importer->getTemplateHeaders();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=bos_sablon.csv');
        $output = fopen('php://output', 'w');
        fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($output, $headers);
        fclose($output);
        exit;
    }

    public function downloadExampleTemplate(): void
    {
        $importer = new \App\Services\ExcelImportService();
        $headers = $importer->getTemplateHeaders();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=ornek_sablon.csv');
        $output = fopen('php://output', 'w');
        fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($output, $headers);
        
        // Örnek 1
        $row1 = ['Instagram Yorum Paketi', '12', '149.90', '', 'Kısa açıklama', 'Uzun açıklama', '1-3 Gün', '1', '10', 'active', 'instagram-yorum-1', 'SEO Başlık', 'SEO Açıklama', 'Anahtar', ''];
        // Örnek 2
        $row2 = ['TikTok Takipçi Paketi', '20', '99.00', '79.00', 'İndirimli paket', 'Detaylar', 'Anında', '1', '1', 'active', '', '', '', '', 'tiktok-takipci'];
        
        fputcsv($output, $row1);
        fputcsv($output, $row2);
        
        fclose($output);
        exit;
    }

    public function importProcess(): void
    {
        Csrf::check();
        if (empty($_FILES['csv_file']['name'])) {
            flash('error', 'Dosya seçilmedi.');
            redirect('/admin/import');
        }

        $filePath = $_FILES['csv_file']['tmp_name'];
        $updateDuplicate = isset($_POST['update_duplicate']);

        $importer = new \App\Services\ExcelImportService();
        $result = $importer->importCsv($filePath, ['update_on_duplicate' => $updateDuplicate]);

        $_SESSION['import_result'] = $result;
        
        flash('success', "İçe aktarım tamamlandı.");
        logActivity('csv_import', "CSV içe aktarım: {$result['success']} başarılı");
        redirect('/admin/import');
    }

    public function supportIndex(): void
    {
        $tickets = $this->db->fetchAll("SELECT st.*, u.name as user_name FROM support_tickets st LEFT JOIN users u ON st.user_id = u.id ORDER BY st.updated_at DESC");
        $this->renderAdmin('admin/support/index', ['pageTitle' => 'Destek Talepleri', 'tickets' => $tickets]);
    }

    public function supportShow(string $id): void
    {
        $ticket = $this->db->fetch("SELECT st.*, u.name as user_name, u.email as user_email FROM support_tickets st LEFT JOIN users u ON st.user_id = u.id WHERE st.id = ?", [(int) $id]);
        if (!$ticket) { redirect('/admin/destek'); }
        $messages = $this->db->fetchAll("SELECT * FROM support_messages WHERE ticket_id = ? ORDER BY created_at ASC", [(int) $id]);
        $this->renderAdmin('admin/support/detail', ['pageTitle' => 'Destek #' . $ticket['ticket_number'], 'ticket' => $ticket, 'messages' => $messages]);
    }

    public function supportReply(string $id): void
    {
        Csrf::check();
        $ticket = $this->db->fetch("SELECT * FROM support_tickets WHERE id = ?", [(int) $id]);
        if (!$ticket) { redirect('/admin/destek'); }

        $this->db->insert('support_messages', [
            'ticket_id' => (int) $id,
            'sender_type' => 'admin',
            'sender_id' => $_SESSION['admin_id'],
            'message' => trim($_POST['message'] ?? ''),
        ]);

        $status = $_POST['status'] ?? 'admin_reply';
        $this->db->update('support_tickets', ['status' => $status], 'id = ?', [(int) $id]);

        $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$ticket['user_id']]);
        if ($user) {
            \App\Services\MailService::sendTemplate('support_admin_reply', $user['email'], [
                'user_name' => $user['name'],
                'ticket_number' => $ticket['ticket_number'],
            ]);
        }

        flash('success', 'Yanıt gönderildi.');
        redirect('/admin/destek/' . $id);
    }

    public function payments(): void
    {
        $payments = $this->db->fetchAll("SELECT p.*, o.order_number, u.name as user_name FROM payments p LEFT JOIN orders o ON p.order_id = o.id LEFT JOIN users u ON o.user_id = u.id ORDER BY p.created_at DESC LIMIT 200");
        $this->renderAdmin('admin/orders/payments', ['pageTitle' => 'Ödemeler', 'payments' => $payments]);
    }

    public function seoCenter(): void
    {
        $seo = new \App\Services\SeoScoreService();
        $categories = $this->db->fetchAll("SELECT id, name, slug, seo_title, seo_description, seo_score FROM categories WHERE status = 'active' ORDER BY seo_score ASC");
        $packages = $this->db->fetchAll("SELECT id, name, slug, seo_title, seo_description, seo_score FROM packages WHERE status = 'active' ORDER BY seo_score ASC LIMIT 50");
        $posts = $this->db->fetchAll("SELECT id, title, slug, seo_title, seo_description, seo_score FROM blog_posts WHERE status = 'active' ORDER BY seo_score ASC LIMIT 50");

        $avgScore = 0;
        $allItems = array_merge($categories, $packages, $posts);
        if (count($allItems)) {
            $avgScore = round(array_sum(array_column($allItems, 'seo_score')) / count($allItems));
        }

        $this->renderAdmin('admin/settings/seo', [
            'pageTitle' => 'SEO Merkezi',
            'categories' => $categories,
            'packages' => $packages,
            'posts' => $posts,
            'avgScore' => $avgScore,
        ]);
    }
}
