<!-- Page Header -->
<div class="adm-page-top">
    <div class="adm-page-top-left">
        <a href="/admin/destek" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Geri</a>
        <div>
            <h2><?= icon('message-circle', 22) ?> <?= e($ticket['subject']) ?></h2>
            <div class="adm-ticket-meta">
                <span class="adm-link-blue font-semibold">#<?= e($ticket['ticket_number']) ?></span>
                <?php
                $stBadge = 'info';
                if ($ticket['status'] === 'closed') $stBadge = 'default';
                elseif ($ticket['status'] === 'admin_reply') $stBadge = 'success';
                elseif ($ticket['status'] === 'customer_reply') $stBadge = 'warning';
                ?>
                <span class="status-badge <?= $stBadge ?>"><?= e(supportStatusLabel($ticket['status'])) ?></span>
                <?php if (!empty($ticket['order_id'])): ?>
                <a href="/admin/siparis/<?= $ticket['order_id'] ?>" class="btn btn-outline btn-sm"><?= icon('package', 12) ?> Sipariş Detay</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="adm-ticket-customer-info">
        <div class="text-sm"><?= icon('user', 14) ?> <strong><?= e($ticket['user_name'] ?? '') ?></strong></div>
        <div class="text-xs text-secondary"><?= e($ticket['user_email'] ?? '') ?></div>
    </div>
</div>

<!-- Chat Messages -->
<div class="adm-card">
    <div class="adm-card-body" style="padding: 0;">
        <div class="adm-chat-messages" id="admChatMessages">
            <?php foreach ($messages as $msg): ?>
            <div class="adm-chat-msg adm-chat-<?= $msg['sender_type'] ?>">
                <div class="adm-chat-avatar-wrap">
                    <div class="adm-chat-avatar <?= $msg['sender_type'] ?>">
                        <?php if ($msg['sender_type'] === 'admin'): ?>
                            <?= icon('shield', 14) ?>
                        <?php else: ?>
                            <?= icon('user', 14) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="adm-chat-bubble">
                    <div class="adm-chat-sender">
                        <?= $msg['sender_type'] === 'admin' ? 'Admin' : 'Müşteri' ?>
                    </div>
                    <div class="adm-chat-text"><?= nl2br(e($msg['message'])) ?></div>
                    <?php if ($msg['attachment']): ?>
                    <div class="adm-chat-attach">
                        <a href="<?= e($msg['attachment']) ?>" target="_blank" class="btn btn-light btn-sm"><?= icon('link', 14) ?> Ek Dosya</a>
                    </div>
                    <?php endif; ?>
                    <div class="adm-chat-time"><?= icon('clock', 11) ?> <?= formatDate($msg['created_at'], 'd M Y H:i') ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Reply Form -->
<?php if ($ticket['status'] !== 'closed'): ?>
<div class="adm-card">
    <div class="adm-card-header"><h3><?= icon('send', 18) ?> Admin Yanıtı</h3></div>
    <div class="adm-card-body">
        <form method="POST" action="/admin/destek/<?= $ticket['id'] ?>/yanit">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Yanıt Mesajı</label>
                <textarea name="message" class="form-control" rows="4" placeholder="Müşteriye yanıtınızı yazın..." required></textarea>
            </div>
            <div class="adm-reply-bottom">
                <div class="form-group" style="margin-bottom: 0; min-width: 180px;">
                    <label>Talep Durumu</label>
                    <select name="status" class="form-control">
                        <option value="admin_reply">Yanıtlandı</option>
                        <option value="closed">Kapat</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><?= icon('send', 16) ?> Yanıtı Gönder</button>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="adm-card">
    <div class="adm-card-body">
        <div class="adm-closed-notice">
            <?= icon('lock', 20) ?>
            <div>
                <strong>Bu talep kapatılmıştır</strong>
                <p class="text-sm text-secondary" style="margin: 2px 0 0;">Talep <?= formatDate($ticket['updated_at'], 'd M Y H:i') ?> tarihinde kapatıldı.</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var chat = document.getElementById('admChatMessages');
    if (chat) chat.scrollTop = chat.scrollHeight;
});
</script>
