<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Csrf;
use App\Services\LeadCaptureService;

class PageController extends Controller
{
    public function show(string $slug): void
    {
        $page = Database::getInstance()->fetch("SELECT * FROM pages WHERE slug = ? AND status = 'active'", [$slug]);
        if (!$page) { $this->render('frontend/404', ['pageTitle' => 'Sayfa Bulunamadı']); return; }

        $this->render('frontend/page', [
            'pageTitle' => $page['seo_title'] ?: $page['title'],
            'metaDescription' => $page['seo_description'],
            'canonicalUrl' => url('/sayfa/' . $page['slug']),
            'page' => $page,
        ]);
    }

    public function faq(): void
    {
        $faqs = Database::getInstance()->fetchAll("SELECT * FROM faqs WHERE status = 'active' ORDER BY sort_order ASC");
        $schema = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn($f) => [
                '@type' => 'Question', 'name' => $f['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f['answer'])]
            ], $faqs),
        ], JSON_UNESCAPED_UNICODE);

        $this->render('frontend/faq', [
            'pageTitle' => 'Sıkça Sorulan Sorular - ' . setting('site_name'),
            'schema' => $schema,
            'faqs' => $faqs,
        ]);
    }

    public function contact(): void
    {
        $this->render('frontend/contact', ['pageTitle' => 'İletişim - ' . setting('site_name')]);
    }

    public function contactPost(): void
    {
        Csrf::check();
        if (trim((string)($_POST['website_url'] ?? '')) !== '') {
            redirect('/iletisim');
        }

        $name = trim((string)($_POST['name'] ?? ''));
        $email = mb_strtolower(trim((string)($_POST['email'] ?? '')), 'UTF-8');
        $subject = trim((string)($_POST['subject'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));

        if (mb_strlen($name) < 2 || mb_strlen($name) > 120
            || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190
            || mb_strlen($subject) < 3 || mb_strlen($subject) > 190
            || mb_strlen($message) < 10 || mb_strlen($message) > 10000
            || ($_POST['privacy_consent'] ?? '') !== '1') {
            flash('contact_error', 'Bilgileri kontrol edin, KVKK bilgilendirmesini onaylayın ve en az 10 karakterlik mesaj yazın.');
            redirect('/iletisim');
        }

        if (time() - (int)($_SESSION['last_contact_at'] ?? 0) < 30) {
            flash('contact_error', 'Yeni mesaj göndermeden önce lütfen kısa bir süre bekleyin.');
            redirect('/iletisim');
        }

        try {
            LeadCaptureService::saveContact($name, $email, $subject, $message);
            $_SESSION['last_contact_at'] = time();
            flash('contact_success', 'Mesajınız kaydedildi. Ekibimiz en kısa sürede inceleyecek.');
        } catch (\Throwable $e) {
            error_log('Contact save failed: ' . $e->getMessage());
            flash('contact_error', 'Mesajınız kaydedilemedi. Lütfen daha sonra tekrar deneyin veya e-posta ile iletişim kurun.');
        }
        redirect('/iletisim');
    }
}
