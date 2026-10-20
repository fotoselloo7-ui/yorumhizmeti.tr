<?php include __DIR__.'/../partials/member-open.php'; ?>
<header class="member-hero">
 <div><span class="member-eyebrow"><?= icon('sparkles',14) ?> NETVERA MÜŞTERİ MERKEZİ</span><h1>Merhaba, <?= e(explode(' ',trim($user['name']))[0]) ?>.</h1><p>Siparişleriniz, yazılımlarınız ve destek ekibiniz. Hepsi bir arada.</p></div>
 <a href="/kategoriler" class="member-btn"><?= icon('plus',18) ?> Yeni sipariş</a>
</header>
<div class="member-stats">
 <?php foreach([['package','Toplam sipariş',$summary['total'],'/siparislerim'],['zap','Devam eden',$summary['active'],'/siparislerim?status=processing'],['check-circle','Tamamlanan',$summary['completed'],'/siparislerim?status=completed'],['headphones','Açık destek',$summary['open_tickets'],'/destek']]as [$glyph,$label,$value,$href]): ?><a class="member-stat" href="<?= e($href) ?>"><span class="member-stat-icon"><?= icon($glyph,21) ?></span><strong><?= (int)$value ?></strong><span><?= e($label) ?></span><?= icon('arrow-up-right',14) ?></a><?php endforeach; ?>
</div>
<?php if($summary['awaiting_payment']||$summary['needs_info']||$summary['answered_tickets']): ?>
<section class="member-attention" aria-labelledby="member-attention-title"><div><span class="member-icon-soft"><?= icon('bell',18) ?></span><h2 id="member-attention-title">Göz atmanız gerekenler</h2></div><div class="member-attention-links">
 <?php if($summary['awaiting_payment']): ?><a href="/hesabim/odemeler?payment=pending"><strong><?= (int)$summary['awaiting_payment'] ?> siparişte ödeme bekleniyor</strong><span>Bekleyen / başarısız ödemelerinizi inceleyin <?= icon('arrow-right',14) ?></span></a><?php endif; ?>
 <?php if($summary['needs_info']): ?><a href="/siparislerim?status=waiting_customer_info"><strong><?= (int)$summary['needs_info'] ?> sipariş bilgi bekliyor</strong><span>Ekibimizin notunu inceleyin <?= icon('arrow-right',14) ?></span></a><?php endif; ?>
 <?php if($summary['answered_tickets']): ?><a href="/destek?status=answered"><strong><?= (int)$summary['answered_tickets'] ?> yanıtlanmış destek talebi</strong><span>Destek yanıtlarını görüntüleyin <?= icon('arrow-right',14) ?></span></a><?php endif; ?>
</div></section><?php endif; ?>
<div class="member-dashboard-grid">
 <section class="member-card"><div class="member-card-heading"><div><span class="member-eyebrow">SİPARİŞ TAKİBİ</span><h2>Son siparişleriniz</h2></div><a href="/siparislerim" class="member-text-link">Tümünü gör <?= icon('arrow-right',14) ?></a></div>
 <?php if($recentOrders): $orders=$recentOrders;include __DIR__.'/partials/member-order-list.php'; else: ?><div class="member-empty"><?= icon('package',32) ?><h3>Henüz siparişiniz bulunmuyor.</h3><p>İhtiyacınıza uygun hizmeti seçerek başlayabilirsiniz.</p><a class="member-btn" href="/kategoriler">İlk siparişinizi keşfedin</a></div><?php endif; ?></section>
 <section class="member-card"><div class="member-card-heading"><div><span class="member-eyebrow">GÜNCEL DURUM</span><h2>Hesap hareketleri</h2></div><a href="/hesabim/hareketler" class="member-text-link" aria-label="Tüm hesap hareketleri"><?= icon('arrow-up-right',18) ?></a></div><?php include __DIR__.'/partials/member-activity.php'; ?></section>
</div>
<div class="member-shortcuts">
 <a href="/hesabim/yazilimlar"><span><?= icon('monitor',24) ?></span><div><h2>Yazılım kütüphaneniz</h2><p><?= count($netveraHistory['software']??[]) ?> doğrulanmış geçmiş yazılım kaydı</p></div><?= icon('arrow-right',18) ?></a>
 <a href="/destek/yeni"><span><?= icon('headphones',24) ?></span><div><h2>Birlikte çözelim</h2><p>Siparişinize bağlı destek talebi oluşturun.</p></div><?= icon('arrow-right',18) ?></a>
</div>
<div class="member-account-note"><span id="profil"><a href="/hesabim/profil">Profil bilgilerini düzenle</a></span><span id="sifre"><a href="/hesabim/guvenlik">Hesap güvenliğini incele</a></span></div>
<?php include __DIR__.'/../partials/member-close.php'; ?>
