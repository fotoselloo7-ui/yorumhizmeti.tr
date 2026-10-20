<?php use App\Services\MemberPanelService as MemberFilters; ?>
<form class="member-filters member-card" action="<?= e($listBase) ?>" method="get">
 <div><label for="member-search">Sipariş veya paket ara</label><div class="member-search"><?= icon('search',18) ?><input id="member-search" class="member-control" type="search" name="q" value="<?= e($filters['q']) ?>" maxlength="80" placeholder="Sipariş no, paket adı…"></div></div>
 <details class="member-filter-options" open>
  <summary><span>Durum ve tarih filtreleri</span><?= icon('chevron-down',16) ?></summary>
  <div class="member-filter-grid">
   <div><label for="member-status">Sipariş durumu</label><select id="member-status" name="status" class="member-control"><option value="">Tüm durumlar</option><option value="active" <?= $filters['status']==='active'?'selected':'' ?>>Devam eden siparişler</option><?php foreach(MemberFilters::ORDER_STATUSES as $v): ?><option value="<?= e($v) ?>" <?= $filters['status']===$v?'selected':'' ?>><?= e(orderStatusLabel($v)) ?></option><?php endforeach; ?></select></div>
   <div><label for="member-payment">Ödeme durumu</label><select id="member-payment" name="payment" class="member-control"><option value="">Tüm ödemeler</option><option value="unpaid" <?= $filters['payment']==='unpaid'?'selected':'' ?>>Ödeme bekleyen açık siparişler</option><?php foreach(MemberFilters::PAYMENT_STATUSES as $v): ?><option value="<?= e($v) ?>" <?= $filters['payment']===$v?'selected':'' ?>><?= e(paymentStatusLabel($v)) ?></option><?php endforeach; ?></select></div>
   <div><label for="member-period">Tarih aralığı</label><select id="member-period" name="period" class="member-control"><option value="">Tüm zamanlar</option><?php foreach(['30'=>'Son 30 gün','90'=>'Son 90 gün','365'=>'Son 1 yıl']as $v=>$label): ?><option value="<?= $v ?>" <?= $filters['period']===(string)$v?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
  </div>
 </details>
 <div class="member-filter-actions"><button class="member-btn" type="submit">Filtrele</button><a class="member-text-link" href="<?= e($listBase) ?>">Temizle</a></div>
</form>
