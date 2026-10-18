<?php include __DIR__.'/../partials/member-open.php'; ?>
<header class="member-heading"><div><span class="member-eyebrow">SİPARİŞ MERKEZİ</span><h1>Siparişlerim</h1><p>Paketlerinizi bulun, teslimatı ve sipariş durumunu takip edin.</p></div><a href="/kategoriler" class="member-btn"><?= icon('plus',16) ?> Yeni sipariş</a></header>
<?php include __DIR__.'/partials/member-filters.php'; ?>
<div class="member-result-heading"><span><?= (int)$total ?> sipariş bulundu</span><span>En yeni sipariş önce</span></div>
<section class="member-card"><?php if($orders): include __DIR__.'/partials/member-order-list.php'; else: ?><div class="member-empty"><?= icon('search',32) ?><h2>Sipariş bulunamadı.</h2><p>Henüz sipariş vermediyseniz hizmetleri keşfedin; arama yaptıysanız filtrelerinizi değiştirin.</p><a href="/siparislerim" class="member-btn is-soft">Filtreleri temizle</a></div><?php endif; ?></section>
<?php include __DIR__.'/partials/member-pagination.php'; ?>
<?php include __DIR__.'/../partials/member-close.php'; ?>
