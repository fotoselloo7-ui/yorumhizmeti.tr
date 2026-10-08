<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Csrf;

class MessageController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $messages = [];
        $subscribers = [];
        try {
            $messages = $db->fetchAll("SELECT id, name, email, subject, body, status, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 100");
        } catch (\Throwable $e) {}
        try {
            $subscribers = $db->fetchAll("SELECT id, email, source, consent_at, status, created_at FROM newsletter_subscribers ORDER BY created_at DESC LIMIT 100");
        } catch (\Throwable $e) {}

        $this->render('admin/messages/index', [
            'pageTitle' => 'İletişim ve Bülten',
            'messages' => $messages,
            'subscribers' => $subscribers,
        ], 'admin');
    }

    public function markRead(string $id): void
    {
        Csrf::check();
        if ((int)$id > 0) {
            Database::getInstance()->query("UPDATE contact_messages SET status = 'read' WHERE id = ?", [(int)$id]);
        }
        flash('success', 'Mesaj okundu olarak işaretlendi.');
        redirect('/admin/mesajlar');
    }

    public function unsubscribe(string $id): void
    {
        Csrf::check();
        if ((int)$id > 0) {
            Database::getInstance()->query("UPDATE newsletter_subscribers SET status = 'unsubscribed' WHERE id = ?", [(int)$id]);
        }
        flash('success', 'Bülten kaydı pasifleştirildi.');
        redirect('/admin/mesajlar');
    }
}
