<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;

class SupportController extends Controller
{
    public function index(): void
    {
        $tickets = $this->db->fetchAll("SELECT * FROM support_tickets WHERE user_id = ? ORDER BY updated_at DESC", [Auth::id()]);
        $this->render('frontend/support/index', ['pageTitle' => 'Destek Taleplerim', 'tickets' => $tickets]);
    }

    public function create(): void
    {
        $orders = $this->db->fetchAll("SELECT id, order_number FROM orders WHERE user_id = ? ORDER BY created_at DESC", [Auth::id()]);
        $this->render('frontend/support/create', ['pageTitle' => 'Yeni Destek Talebi', 'orders' => $orders]);
    }

    public function store(): void
    {
        Csrf::check();
        $ticketNumber = 'DT' . date('ymd') . strtoupper(substr(uniqid(), -4));

        $ticketId = $this->db->insert('support_tickets', [
            'user_id' => Auth::id(),
            'order_id' => !empty($_POST['order_id']) ? (int) $_POST['order_id'] : null,
            'ticket_number' => $ticketNumber,
            'subject' => trim($_POST['subject'] ?? ''),
            'priority' => $_POST['priority'] ?? 'medium',
            'status' => 'open',
        ]);

        $attachment = null;
        if (!empty($_FILES['attachment']['name'])) {
            $attachment = \App\Core\Upload::attachment($_FILES['attachment'], 'support');
        }

        $this->db->insert('support_messages', [
            'ticket_id' => $ticketId,
            'sender_type' => 'user',
            'sender_id' => Auth::id(),
            'message' => trim($_POST['message'] ?? ''),
            'attachment' => $attachment,
        ]);

        \App\Services\MailService::sendTemplate('support_ticket_created', Auth::user()['email'], [
            'user_name' => Auth::user()['name'],
            'ticket_number' => $ticketNumber,
            'subject' => $_POST['subject'] ?? '',
        ]);

        flash('success', 'Destek talebiniz oluşturuldu.');
        redirect('/destek/' . $ticketId);
    }

    public function show(string $id): void
    {
        $ticket = $this->db->fetch("SELECT * FROM support_tickets WHERE id = ? AND user_id = ?", [(int) $id, Auth::id()]);
        if (!$ticket) { flash('error', 'Talep bulunamadı.'); redirect('/destek'); }

        $messages = $this->db->fetchAll("SELECT * FROM support_messages WHERE ticket_id = ? ORDER BY created_at ASC", [$ticket['id']]);

        $this->render('frontend/support/detail', [
            'pageTitle' => 'Destek Talebi #' . $ticket['ticket_number'],
            'ticket' => $ticket,
            'messages' => $messages,
        ]);
    }

    public function reply(string $id): void
    {
        Csrf::check();
        $ticket = $this->db->fetch("SELECT * FROM support_tickets WHERE id = ? AND user_id = ?", [(int) $id, Auth::id()]);
        if (!$ticket) { redirect('/destek'); }

        $attachment = null;
        if (!empty($_FILES['attachment']['name'])) {
            $attachment = \App\Core\Upload::attachment($_FILES['attachment'], 'support');
        }

        $this->db->insert('support_messages', [
            'ticket_id' => $ticket['id'],
            'sender_type' => 'user',
            'sender_id' => Auth::id(),
            'message' => trim($_POST['message'] ?? ''),
            'attachment' => $attachment,
        ]);

        $this->db->update('support_tickets', ['status' => 'customer_reply'], 'id = ?', [$ticket['id']]);
        flash('success', 'Yanıtınız gönderildi.');
        redirect('/destek/' . $id);
    }
}
