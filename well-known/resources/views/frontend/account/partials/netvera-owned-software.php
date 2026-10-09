<?php $nv62Owned=isset($netveraHistory['software'])&&is_array($netveraHistory['software'])?$netveraHistory['software']:[]; ?>
<?php if($nv62Owned): ?>
<section class="panel-card nv57-legacy-history" aria-label="Satın aldığınız NetVera yazılımları">
 <div class="panel-card-header">
   <h3><?= icon('monitor',18) ?> Satın Aldığım Yazılımlar</h3>
   <span class="nv57-history-pill"><?= count($nv62Owned) ?> doğrulanmış ürün</span>
 </div>
 <div class="panel-card-body">
   <p class="nv57-history-note" style="margin-top:0;margin-bottom:14px">Bu liste eski NetVera hesabınıza bağlı, ödeme durumu doğrulanmış yazılımları gösterir. Misafir siparişleri kimlik doğrulanmadan burada görünmez.</p>
   <div class="nv57-purchases">
    <?php foreach(array_slice($nv62Owned,0,30) as $script):
      $nvSlug=(string)($script['item_slug']??'');
      $nvValidSlug=$nvSlug!==''&&preg_match('/^[a-z0-9][a-z0-9-]{0,190}$/D',$nvSlug);
    ?>
    <article class="nv57-purchase">
      <span class="nv57-purchase-icon"><?= icon('monitor',19) ?></span>
      <div class="nv57-purchase-copy">
        <strong><?= e((string)$script['item_name']) ?></strong>
        <small><?= e((string)$script['order_no']) ?> · <?= e((string)$script['payment_status']) ?></small>
        <?php if((int)$script['has_rights']===1): ?>
        <span class="nv57-entitlement"><?= icon('shield-check',12) ?> Eski lisans / kullanım hakkı kaydedilmiş</span>
        <?php else: ?>
        <span class="nv57-history-note">Satın alma geçmişi kayıtlı; lisans bilgisi için destek merkezine başvurun.</span>
        <?php endif; ?>
      </div>
      <?php if($nvValidSlug): ?>
      <a class="btn btn-outline btn-sm" href="/hazir-scriptler/<?= e($nvSlug) ?>"><?= icon('external-link',13) ?> İncele</a>
      <?php endif; ?>
    </article>
    <?php endforeach; ?>
   </div>
   <p class="nv57-history-note">Eski NetVera indirme paketleri veya lisans anahtarı kayıtları burada herkese açık sunulmaz. Ürün teslimatı ve lisans talepleri için <a href="/destek/yeni">destek merkezi</a> kullanılabilir.</p>
 </div>
</section>
<?php endif; ?>
