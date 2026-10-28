<?php
/** Authentic portfolio entries selected by admin for this category or package. */
$serviceReferences=$serviceReferences??[];
if($serviceReferences):
?>
<section class="nv-service-references" id="hizmet-referanslari" aria-labelledby="nv-service-references-title">
 <div class="container">
  <div class="nv-service-references-head">
   <div><span>NETVERA / GERÇEK ÇALIŞMALAR</span><h2 id="nv-service-references-title">Bu hizmetle ilgili referanslarımız</h2>
     <p>Ekibimizin seçili çalışmalarını ve ilgili proje bağlantılarını inceleyin.</p></div>
   <a href="/#referanslarimiz">Tüm referansları incele <?= icon('arrow-up-right',15) ?></a>
  </div>
  <div class="nv-service-references-grid">
  <?php foreach(array_slice($serviceReferences,0,12) as $ref):
    $title=(string)($ref['title']??'');
    $cover=(string)($ref['image']??'');
    $logo=(string)($ref['logo']??'');
    $media=\App\Services\ReferencesService::normalizedMedia($ref);
    $rawUrl=(string)($ref['url']??'');
    $safeUrl=filter_var($rawUrl,FILTER_VALIDATE_URL) && in_array(strtolower((string)parse_url($rawUrl,PHP_URL_SCHEME)),['https','http'],true)?$rawUrl:'';
    $embed=$media==='youtube_video' ? \App\Services\ReferencesService::externalPlayer($rawUrl)
      : (in_array($media,['instagram_post','instagram_reel'],true)
          ? \App\Services\ReferencesService::externalPlayer((string)($ref['external_video_url']??''))
          : null);
    $refType=$media==='website'?'Web Sitesi':($media==='image'?'Proje Görseli':($media==='youtube_video'?'YouTube':'Instagram'));
  ?>
   <article class="nv-service-reference-card">
     <div class="nv-service-reference-photo">
       <?php if($cover): ?><img src="<?= e(upload_url($cover)) ?>" alt="<?= e($title) ?> referans görseli" loading="lazy" decoding="async">
       <?php elseif($logo): ?><img class="nv-service-reference-logo" src="<?= e(upload_url($logo)) ?>" alt="<?= e($title) ?> logosu" loading="lazy">
       <?php else: ?><div class="nv-service-reference-placeholder"><?= icon($media==='website'?'monitor':($media==='youtube_video'?'video':'image'),36) ?></div><?php endif; ?>
       <?php if($embed && ($embed['type']??'')==='iframe'): ?>
         <button type="button" class="nv-service-reference-play" data-nv-ref-player
                 data-nv-ref-src="<?= e($embed['url']) ?>"
                 aria-label="<?= e($title) ?> videosunu oynat"><?= icon('play',21) ?></button>
       <?php elseif($media==='instagram_post'||$media==='instagram_reel'): ?>
         <?php if($safeUrl): ?><a class="nv-service-reference-play" href="<?= e($safeUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($title) ?> içeriğini aç"><?= icon('play',21) ?></a><?php endif; ?>
       <?php endif; ?>
       <span class="nv-service-reference-kind"><?= e($refType) ?></span>
     </div>
     <div class="nv-service-reference-body">
       <h3><?= e($title) ?></h3>
       <?php if(!empty($ref['description'])): ?><p><?= e(excerpt((string)$ref['description'],130)) ?></p><?php endif; ?>
       <?php if($safeUrl): ?><a href="<?= e($safeUrl) ?>" target="_blank" rel="noopener noreferrer">Çalışmayı incele <?= icon('arrow-up-right',14) ?></a><?php endif; ?>
     </div>
   </article>
  <?php endforeach; ?>
  </div>
 </div>
</section>
<?php endif; ?>
