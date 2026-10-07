<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Csrf;

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
        flash('success', 'Mesajınız gönderildi. En kısa sürede dönüş yapacağız.');
        redirect('/iletisim');
    }
}
