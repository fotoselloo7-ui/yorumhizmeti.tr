<?php
$nv97Popup=\App\Services\MarketingPopupService::activeForPath($_SERVER['REQUEST_URI']??'/');
if(!$nv97Popup)return;
$nv97Img=\App\Services\MarketingPopupService::imageUrl($nv97Popup['image']??'');
$nv97Type=$nv97Popup['type']??'campaign';
$nv97Coupons=is_array($nv97Popup['coupon_items']??null)?$nv97Popup['coupon_items']:[];
$nv97HasBody=$nv97Type!=='image';
?>
<div class="nv97-popup-shell" data-nv97-popup-shell
     data-popup-id="<?= (int)$nv97Popup['id'] ?>"
     data-delay="<?= (int)$nv97Popup['open_delay_ms'] ?>"
     data-reopen-hours="<?= (int)$nv97Popup['reopen_hours'] ?>"
     data-auto-close="<?= (int)$nv97Popup['auto_close_seconds'] ?>"
     data-closable="<?= !empty($nv97Popup['closable'])?'1':'0' ?>"
     data-close-backdrop="<?= !empty($nv97Popup['close_on_backdrop'])?'1':'0' ?>"
     data-once-session="<?= !empty($nv97Popup['once_per_session'])?'1':'0' ?>"
     hidden>
  <section class="nv97-popup <?= e($nv97Popup['orientation']??'horizontal') ?> <?= $nv97Type==='image'?'image-only':'' ?>"
           role="dialog" aria-modal="true"
           aria-label="<?= e($nv97Popup['title']?:$nv97Popup['internal_name']) ?>"
           tabindex="-1"
           style="--nv97-a:<?= e($nv97Popup['background_start']?:'#151a45') ?>;--nv97-b:<?= e($nv97Popup['background_end']?:'#6b4de8') ?>;--nv97-text:<?= e($nv97Popup['text_color']?:'#ffffff') ?>;--nv97-accent:<?= e($nv97Popup['accent_color']?:'#d936a1') ?>">
    <?php if(!empty($nv97Popup['closable'])): ?>
    <button type="button" class="nv97-popup-close" data-nv97-close aria-label="Popup'ı kapat">×</button>
    <?php endif; ?>

    <?php if($nv97Img): ?>
    <div class="nv97-popup-image">
      <img src="<?= e($nv97Img) ?>" alt="<?= e($nv97Popup['image_alt']?:$nv97Popup['title']) ?>" loading="eager" decoding="async">
    </div>
    <?php endif; ?>

    <?php if($nv97HasBody): ?>
    <div class="nv97-popup-body">
      <?php if(!empty($nv97Popup['eyebrow'])): ?><span class="nv97-popup-eyebrow"><?= e($nv97Popup['eyebrow']) ?></span><?php endif; ?>
      <?php if(!empty($nv97Popup['title'])): ?><h2><?= e($nv97Popup['title']) ?></h2><?php endif; ?>
      <?php if(!empty($nv97Popup['description'])): ?><p class="nv97-popup-description"><?= nl2br(e($nv97Popup['description'])) ?></p><?php endif; ?>

      <?php if($nv97Coupons): ?>
      <div class="nv97-popup-coupons" aria-label="Kuponlar ve indirimler">
        <?php foreach($nv97Coupons as $item): ?>
        <div class="nv97-coupon">
          <div class="nv97-coupon-copy">
            <?php if(!empty($item['title'])): ?><strong><?= e($item['title']) ?></strong><?php endif; ?>
            <?php if(!empty($item['description'])): ?><small><?= e($item['description']) ?></small><?php endif; ?>
          </div>
          <div class="nv97-coupon-side">
            <?php if(!empty($item['discount'])): ?><span class="nv97-coupon-discount"><?= e($item['discount']) ?></span><?php endif; ?>
            <?php if(!empty($item['code'])): ?><button type="button" class="nv97-coupon-code" data-coupon-code="<?= e($item['code']) ?>" title="Kupon kodunu kopyala"><?= e($item['code']) ?></button><?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if((!empty($nv97Popup['primary_button_text'])&&!empty($nv97Popup['primary_button_url']))||(!empty($nv97Popup['secondary_button_text'])&&!empty($nv97Popup['secondary_button_url']))): ?>
      <div class="nv97-popup-actions">
        <?php if(!empty($nv97Popup['primary_button_text'])&&!empty($nv97Popup['primary_button_url'])): ?><a class="nv97-popup-cta primary" href="<?= e($nv97Popup['primary_button_url']) ?>"><?= e($nv97Popup['primary_button_text']) ?> <?= icon('arrow-right',12) ?></a><?php endif; ?>
        <?php if(!empty($nv97Popup['secondary_button_text'])&&!empty($nv97Popup['secondary_button_url'])): ?><a class="nv97-popup-cta secondary" href="<?= e($nv97Popup['secondary_button_url']) ?>"><?= e($nv97Popup['secondary_button_text']) ?></a><?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if((int)$nv97Popup['auto_close_seconds']>0): ?><span class="nv97-popup-progress" aria-hidden="true"></span><?php endif; ?>
  </section>
</div>