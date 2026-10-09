<?php
/** Reused by both the new and existing reference forms. */
$ref = isset($row) && is_array($row) ? $row : [];
$refGroup = \App\Services\ReferencesService::normalizedGroup($ref);
$refService = \App\Services\ReferencesService::normalizedService($ref);
$refMedia = \App\Services\ReferencesService::normalizedMedia($ref);
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
  <label>Referans Türü *
    <select class="form-control" name="media_type" data-ref-media required>
      <?php foreach ($referenceMediaTypes as $mediaKey=>$mediaLabel): ?>
        <option value="<?= e($mediaKey) ?>" <?= $refMedia===$mediaKey?'selected':'' ?>><?= e($mediaLabel) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Web Sitesi / Instagram Bağlantısı
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