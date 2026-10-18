<?php
$memberUser=\App\Core\Auth::user()??[];
$memberPath=(string)(parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/');
$memberLinks=[
 ['/hesabim','home','Hesap Özeti'],['/siparislerim','package','Siparişlerim'],
 ['/hesabim/yazilimlar','monitor','Yazılımlarım'],['/hesabim/odemeler','credit-card','Ödemelerim'],
 ['/destek','headphones','Destek Merkezi'],['/hesabim/hareketler','activity','Hesap Hareketleri'],
 ['/bayilik','handshake','Bayilik & Ortaklık'],['/hesabim/profil','user','Profil Bilgilerim'],
 ['/hesabim/guvenlik','shield-check','Hesap Güvenliği'],
];
?>
<aside class="member-sidebar" aria-label="Üye paneli">
 <button class="member-nav-toggle" type="button" data-member-nav-toggle aria-expanded="false" aria-controls="member-navigation" hidden><?= icon('menu',20) ?><span>Hesap menüsü</span><?= icon('chevron-down',16) ?></button>
 <div class="member-nav-body" id="member-navigation">
  <div class="member-identity"><span class="member-avatar"><?= e(mb_strtoupper(mb_substr($memberUser['name']??'Ü',0,1))) ?></span><div><strong><?= e($memberUser['name']??'') ?></strong><small><?= e($memberUser['email']??'') ?></small></div></div>
  <p class="member-nav-label">ÇALIŞMA ALANINIZ</p>
  <nav aria-label="Hesap navigasyonu">
  <?php foreach($memberLinks as [$href,$glyph,$label]): $active=$memberPath===$href||($href==='/siparislerim'&&str_starts_with($memberPath,'/siparis/'))||($href==='/destek'&&str_starts_with($memberPath,'/destek/')); ?>
   <a href="<?= e($href) ?>" <?= $active?'aria-current="page"':'' ?>><?= icon($glyph,18) ?><span><?= e($label) ?></span></a>
  <?php endforeach; ?>
  <a class="member-logout" href="/cikis"><?= icon('log-out',18) ?><span>Çıkış Yap</span></a>
  </nav>
  <div class="member-sidebar-help"><?= icon('sparkles',18) ?><strong>Bir sonraki projeniz</strong><p>Yazılım ve dijital hizmetleri tek yerden keşfedin.</p><a href="/kategoriler">Hizmetleri keşfet <?= icon('arrow-right',14) ?></a></div>
 </div>
</aside>
