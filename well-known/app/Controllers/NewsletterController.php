<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\LeadCaptureService;

class NewsletterController extends Controller
{
    public function subscribe(): void
    {
        Csrf::check();

        $return = ($_POST['return_to'] ?? '/') === '/blog' ? '/blog' : '/';
        $source = ($_POST['source'] ?? '') === 'blog' ? 'blog' : 'footer';

        if (trim((string)($_POST['website_url'] ?? '')) !== '') {
            redirect($return . '#newsletter');
        }

        $email = mb_strtolower(trim((string)($_POST['email'] ?? '')), 'UTF-8');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190
            || ($_POST['newsletter_consent'] ?? '') !== '1') {
            flash('newsletter_error', 'Geçerli e-posta adresi girip bülten onayını işaretleyin.');
            redirect($return . '#newsletter');
        }

        if (time() - (int)($_SESSION['last_newsletter_at'] ?? 0) < 10) {
            flash('newsletter_error', 'Yeni istek için lütfen biraz bekleyin.');
            redirect($return . '#newsletter');
        }

        try {
            LeadCaptureService::subscribe($email, $source);
            $_SESSION['last_newsletter_at'] = time();
            flash('newsletter_success', 'E-posta adresiniz bülten listesine kaydedildi.');
        } catch (\Throwable $e) {
            error_log('Newsletter save failed: ' . $e->getMessage());
            flash('newsletter_error', 'Bülten kaydı şu anda yapılamıyor. Daha sonra tekrar deneyin.');
        }
        redirect($return . '#newsletter');
    }
}
