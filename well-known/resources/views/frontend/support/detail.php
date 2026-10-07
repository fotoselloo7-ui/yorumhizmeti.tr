<section class="section panel-section">
    <div class="container">
        <div class="panel-layout">
            <?php include __DIR__ . '/../partials/user-sidebar.php'; ?>
            <div class="panel-content">

                <div class="panel-page-header">
                    <div>
                        <h1><?= icon('message-circle', 24) ?> <?= e($ticket['subject']) ?></h1>
                        <div class="panel-ticket-meta-row">
                            <span class="panel-order-number">#<?= e($ticket['ticket_number']) ?></span>
                            <?php
                            $stBadge = 'badge-info';
                            if ($ticket['status'] === 'closed') $stBadge = 'badge-default';
                            elseif ($ticket['status'] === 'admin_reply') $stBadge = 'badge-success';
                            elseif ($ticket['status'] === 'customer_reply') $stBadge = 'badge-warning';
                            ?>
                            <span class="badge <?= $stBadge ?>"><?= e(supportStatusLabel($ticket['status'])) ?></span>
                            <?php if (!empty($ticket['order_id'])): ?>
                            <span class="badge badge-info"><?= icon('package', 12) ?> Sipariş Bağlantılı</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="/destek" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Taleplerime Dön</a>
                </div>

                <!-- Chat Area -->
                <div class="panel-card panel-chat-card">
                    <div class="panel-card-body" style="padding: 0;">
                        <div class="panel-chat-messages" id="chatMessages">
                            <?php foreach ($messages as $msg): ?>
                            <div class="panel-chat-msg panel-chat-<?= $msg['sender_type'] ?>">
                                <div class="panel-chat-avatar">
                                    <?php if ($msg['sender_type'] === 'admin'): ?>
                                        <?= icon('headphones', 16) ?>
                                    <?php else: ?>
                                        <?= strtoupper(mb_substr($user['name'] ?? 'U', 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="panel-chat-bubble">
                                    <div class="panel-chat-sender">
                                        <?= $msg['sender_type'] === 'admin' ? 'Destek Ekibi' : 'Siz' ?>
                                    </div>
                                    <div class="panel-chat-text">
                                        <?= nl2br(e($msg['message'])) ?>
                                    </div>
                                    <?php if ($msg['attachment']): ?>
                                    <div class="panel-chat-attachment">
                                        <a href="<?= e($msg['attachment']) ?>" target="_blank" class="btn btn-light btn-sm">
                                            <?= icon('link', 14) ?> Ek Dosya
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    <div class="panel-chat-time">
                                        <?= icon('clock', 11) ?> <?= formatDate($msg['created_at'], 'd M Y H:i') ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Reply Form -->
                <?php if ($ticket['status'] !== 'closed'): ?>
                <div class="panel-card">
                    <div class="panel-card-header">
                        <h3><?= icon('send', 18) ?> Yanıt Yaz</h3>
                    </div>
                    <div class="panel-card-body">
                        <form method="POST" action="/destek/<?= $ticket['id'] ?>/yanit" enctype="multipart/form-data" class="panel-form">
                            <?= csrfField() ?>
                            <div class="form-group">
                                <textarea name="message" class="form-control" rows="4" placeholder="Yanıtınızı yazın..." required></textarea>
                            </div>
                            <div class="panel-reply-actions">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label for="replyAttachment" class="btn btn-light btn-sm" style="cursor: pointer; margin: 0;">
                                        <?= icon('upload', 14) ?> Dosya Ekle
                                    </label>
                                    <input type="file" name="attachment" id="replyAttachment" style="display: none;">
                                    <span class="panel-reply-filename" id="replyFilename"></span>
                                </div>
                                <button type="submit" class="btn btn-primary"><?= icon('send', 16) ?> Yanıt Gönder</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <div class="panel-card">
                    <div class="panel-card-body">
                        <div class="panel-closed-notice">
                            <?= icon('lock', 20) ?>
                            <div>
                                <strong>Bu talep kapatılmıştır</strong>
                                <p>Yeni bir sorunuz varsa yeni destek talebi oluşturabilirsiniz.</p>
                            </div>
                            <a href="/destek/yeni" class="btn btn-primary btn-sm"><?= icon('plus', 14) ?> Yeni Talep</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<script>
// Auto-scroll chat to bottom
document.addEventListener('DOMContentLoaded', function() {
    var chat = document.getElementById('chatMessages');
    if (chat) chat.scrollTop = chat.scrollHeight;

    // Reply file name display
    var replyInput = document.getElementById('replyAttachment');
    var replyName = document.getElementById('replyFilename');
    if (replyInput && replyName) {
        replyInput.addEventListener('change', function() {
            replyName.textContent = this.files.length ? this.files[0].name : '';
        });
    }
});
</script>
