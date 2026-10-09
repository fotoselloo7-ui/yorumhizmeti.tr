<div class="admin-lead-grid">
    <section class="admin-lead-panel">
        <div class="admin-lead-heading">
            <div>
                <h2>İletişim Mesajları</h2>
                
            </div>
            <strong><?= count($messages) ?> mesaj</strong>
        </div>

        <?php if(empty($messages)): ?>
            <p class="admin-lead-empty">Henüz iletişim mesajı bulunmuyor.</p>
        <?php else: ?>
        <div class="admin-lead-table-wrap">
            <table class="admin-lead-table">
                <thead><tr><th>Tarih</th><th>Gönderen</th><th>Konu ve mesaj</th><th>Durum</th><th>İşlem</th></tr></thead>
                <tbody>
                <?php foreach($messages as $msg): ?>
                <tr>
                    <td><?= e($msg['created_at']) ?></td>
                    <td><strong><?= e($msg['name']) ?></strong><small><a href="mailto:<?= e($msg['email']) ?>"><?= e($msg['email']) ?></a></small></td>
                    <td><strong><?= e($msg['subject']) ?></strong><details><summary>Mesajı oku</summary><p><?= nl2br(e($msg['body'])) ?></p></details></td>
                    <td><span class="admin-lead-status <?= $msg['status']==='new'?'unread':'read' ?>"><?= $msg['status']==='new'?'Yeni':'Okundu' ?></span></td>
                    <td><?php if($msg['status']==='new'): ?><form action="/admin/mesaj/<?= (int)$msg['id'] ?>/okundu" method="POST"><?= csrfField() ?><button type="submit" class="btn btn-light btn-sm">Okundu</button></form><?php endif; ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>

    <section class="admin-lead-panel">
        <div class="admin-lead-heading">
            <div>
                <h2>E-Bülten Kayıtları</h2>
                
            </div>
            <strong><?= count($subscribers) ?> kayıt</strong>
        </div>
        <?php if(empty($subscribers)): ?>
            <p class="admin-lead-empty">Henüz bülten kaydı bulunmuyor.</p>
        <?php else: ?>
        <div class="admin-lead-table-wrap">
            <table class="admin-lead-table">
                <thead><tr><th>E-posta</th><th>Kaynak</th><th>Onay tarihi</th><th>Durum</th><th>İşlem</th></tr></thead>
                <tbody>
                <?php foreach($subscribers as $subscriber): ?>
                <tr>
                    <td><?= e($subscriber['email']) ?></td>
                    <td><?= e($subscriber['source']) ?></td>
                    <td><?= e($subscriber['consent_at']) ?></td>
                    <td><span class="admin-lead-status <?= $subscriber['status']==='active'?'active':'read' ?>"><?= $subscriber['status']==='active'?'Aktif':'İptal' ?></span></td>
                    <td><?php if($subscriber['status']==='active'): ?><form action="/admin/bulten/<?= (int)$subscriber['id'] ?>/iptal" method="POST"><?= csrfField() ?><button type="submit" class="btn btn-light btn-sm">Pasifleştir</button></form><?php endif; ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>
</div>
