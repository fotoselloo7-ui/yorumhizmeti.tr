<?php
/** Reused by both the new and existing reference forms. */
$ref = isset($row) && is_array($row) ? $row : [];
$refGroup = \App\Services\ReferencesService::normalizedGroup($ref);
$refService = \App\Services\ReferencesService::normalizedService($ref);
$refMedia = \App\Services\ReferencesService::normalizedMedia($ref);
$refPlacements = \App\Services\ReferencesService::normalizedPlacements($ref);
$secondary = $refPlacements[1] ?? ['group'=>'', 'service'=>''];

?>
<div class="adm31-form-grid">
  <label>Proje / Firma Adı *
    <input class="form-control" type="text" name="title" value="<?= e($ref['title'] ?? '') ?>" required maxlength="140" placeholder="Örn. Müşteri markası">
  </label>
  <label>Ana Kategori *
    <select class="form-control" name="group" data-ref-group required>
      <?php foreach ($referenceGroups as $groupKey=>$group): ?>
        <option value="<?= e($groupKey) ?>" <?= $refGroup===$groupKey?'selected':'' ?>><?= e($group['label']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Hizmet / Alt Kategori *
    <select class="form-control" name="service" data-ref-service required>
      <?php foreach ($referenceGroups as $groupKey=>$group): ?>
        <?php foreach ($group['services'] as $serviceKey=>$serviceLabel): ?>
          <option data-parent="<?= e($groupKey) ?>" value="<?= e($serviceKey) ?>" <?= $refService===$serviceKey && $refGroup===$groupKey?'selected':'' ?>>
            <?= e($serviceLabel) ?>
          </option>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </select>
  </label>
  <label>İkinci Ana Kategori (isteğe bağlı)
    <select class="form-control" name="secondary_group" data-ref-second-group>
      <option value="">Yalnızca ilk kategoride göster</option>
      <?php foreach ($referenceGroups as $groupKey=>$group): ?>
        <option value="<?= e($groupKey) ?>" <?= $secondary['group']===$groupKey?'selected':'' ?>><?= e($group['label']) ?></option>
      <?php endforeach; ?>
    </select>
    <small>İki farklı hizmet alanında aynı referansı gösterebilirsin. Kopya kayıt oluşturulmaz.</small>
  </label>
  <label>İkinci Hizmet / Alt Kategori
    <select class="form-control" name="secondary_service" data-ref-second-service>
      <option value="">Alt hizmet seçin</option>
      <?php foreach ($referenceGroups as $groupKey=>$group): ?>
        <?php foreach ($group['services'] as $serviceKey=>$serviceLabel): ?>
          <option data-parent="<?= e($groupKey) ?>" value="<?= e($serviceKey) ?>"
                  <?= $secondary['group']===$groupKey && $secondary['service']===$serviceKey?'selected':'' ?>>
            <?= e($serviceLabel) ?>
          </option>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Referans Türü *
    <select class="form-control" name="media_type" data-ref-media required>
      <?php foreach ($referenceMediaTypes as $mediaKey=>$mediaLabel): ?>
        <option value="<?= e($mediaKey) ?>" <?= $refMedia===$mediaKey?'selected':'' ?>><?= e($mediaLabel) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Web Sitesi / Instagram / YouTube Bağlantısı
    <input class="form-control" type="url" name="url" data-ref-url value="<?= e($ref['url'] ?? '') ?>" maxlength="1000" placeholder="https://www.instagram.com/reel/... veya https://musteri.com">
    <small data-ref-url-hint>Web sitesi referansları doğrudan müşterinin sitesini açar.</small>
  </label>
  <label>Proje Türü / Sektör (isteğe bağlı)
    <input class="form-control" type="text" name="category" maxlength="100" value="<?= e($ref['category'] ?? '') ?>" placeholder="Örn. Otel rezervasyon sistemi">
  </label>
  <label>Kapak / Proje Görseli (JPG, PNG, WebP)
    <input class="form-control" type="file" name="image" accept="image/png,image/jpeg,image/webp">
    <?php if (!empty($ref['image'])): ?><small>Mevcut kapak kayıtlı; yenisini seçmezsen korunur.</small><?php endif; ?>
  </label>
  <label data-ref-hosted-video-field>Harici Video Bağlantısı — Sunucuda Yer Kaplamaz
    <input class="form-control" type="url" name="external_video_url" maxlength="2000"
      value="<?= e($ref['external_video_url'] ?? '') ?>" data-ref-hosted-video
      placeholder="https://youtu.be/... veya Bunny / Cloudflare Stream / Vimeo">
    <small>ÖNERİLEN: Videoyu YouTube'da liste dışı veya bir video servisinde barındır. Paylaşılan bağlantıyı buraya yapıştır. Video referans kartındaki kendi penceremizde açılır; dosya YorumHizmeti.tr sunucusunda tutulmaz. Sadece Instagram linki girildiğinde ziyaretçi Instagram'da izler; harici video da eklendiğinde sitemizde oynar.</small>
    <?php if (!empty($ref['external_video_url'])): ?>
      <span class="adm52-video-saved"><?= icon('check-circle',13) ?> Harici oynatma bağlantısı kayıtlı.</span>
    <?php endif; ?>
  </label>
  <label data-ref-video-field>İsteğe Bağlı Video (MP4 / WebM)
    <input class="form-control" type="file" name="video" accept="video/mp4,video/webm,.mp4,.webm" data-ref-video-input>
    <small>Instagram linkiyle oynatma kısıtlanırsa videoyu doğrudan sitemizde oynatırız. En fazla 80 MB; sunucuda PHP yükleme limiti yeterli olmalı.</small>
    <?php if (!empty($ref['video'])): ?>
      <span class="adm52-video-saved"><?= icon('check-circle',13) ?> Yüklenmiş video mevcut.</span>
      <span class="adm52-remove-video"><input type="checkbox" name="remove_video" value="1"> Videoyu kaldır</span>
    <?php endif; ?>
  </label>
  <label>Müşteri Logosu (isteğe bağlı)
    <input class="form-control" type="file" name="logo" accept="image/png,image/jpeg,image/webp">
    <?php if (!empty($ref['logo'])): ?><small>Mevcut logo kayıtlı; yenisini seçmezsen korunur.</small><?php endif; ?>
  </label>
  <label>Sıralama
    <input class="form-control" type="number" name="sort_order" min="0" max="9999" value="<?= (int)($ref['sort_order'] ?? 100) ?>">
  </label>
  <label>Yayın Durumu
    <select class="form-control" name="status">
      <option value="active" <?= ($ref['status']??'active')==='active'?'selected':'' ?>>Yayında</option>
      <option value="inactive" <?= ($ref['status']??'active')!=='active'?'selected':'' ?>>Gizli</option>
    </select>
  </label>
</div>
<label class="adm31-wide-label">Kısa Açıklama
  <textarea class="form-control" name="description" rows="2" maxlength="260" placeholder="Müşteriniz için yaptığınız çalışmayı anlatın."><?= e($ref['description'] ?? '') ?></textarea>
</label>