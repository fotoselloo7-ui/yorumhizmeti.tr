<?php use App\Services\MemberPanelService as Member; ?>
<div class="member-order-list">
<?php foreach($orders as $o): ?>
<article class="member-order">
 <div class="member-order-icon"><?= icon('package',20) ?></div>
 <div class="member-order-copy"><a href="/siparis/<?= (int)$o['id'] ?>"><strong>#<?= e($o['order_number']) ?></strong></a><p><?= e($o['item_names']?:'Sipariş detayını inceleyin') ?></p><small><?= formatDate($o['created_at'],'d.m.Y H:i') ?></small></div>
 <div class="member-order-state"><span class="member-badge is-<?= e(orderStatusColor($o['order_status'])) ?>"><?= e(orderStatusLabel($o['order_status'])) ?></span><small><?= e(paymentStatusLabel($o['payment_status'])) ?></small></div>
 <div class="member-order-total"><strong><?= money($o['total_amount']) ?></strong><a href="/siparis/<?= (int)$o['id'] ?>" class="member-text-link">Detay <?= icon('arrow-up-right',14) ?></a></div>
</article>
<?php endforeach; ?>
</div>
