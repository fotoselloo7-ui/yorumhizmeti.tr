<div class="adm-page-top">
  <div><h2><?= icon('award',24) ?> Referanslarımız</h2>
    <p class="text-sm text-secondary">Ajans & Yazılım ve SEO & Dijital çalışmalarını yönet; bir referansı iki ayrı kategoriye bağla. Post/Reels kapakları ve müşteri logoları ekle.</p></div>
  <a href="/#referanslarimiz" target="_blank" rel="noopener" class="btn btn-outline btn-sm"><?= icon('external-link',14) ?> Referans Vitrinini Gör</a>
</div>

<div class="adm-card">
  <div class="adm-card-header"><h3><?= icon('plus',18) ?> Yeni Referans Ekle</h3></div>
  <div class="adm-card-body">
    <form method="POST" action="/admin/referanslar/ekle" enctype="multipart/form-data" class="adm31-reference-form" data-reference-editor>
      <?= csrfField() ?>
      <?php $row = []; include __DIR__.'/fields.php'; ?>
      <button type="submit" class="btn btn-primary"><?= icon('plus',15) ?> Referansı Kaydet</button>
    </form>
  </div>
</div>
<div class="adm-card">
  <div class="adm-card-header"><h3><?= icon('grid',18) ?> Kayıtlı Referanslar (<?= count($references) ?>)</h3></div>
  <div class="adm-card-body">
  <?php if(empty($references)): ?>
    <div class="adm31-empty"><?= icon('image',28) ?><strong>Henüz referans eklenmemiş.</strong>
      
    </div>
  <?php else: ?>
    <div class="adm31-existing-refs">
      <?php foreach($references as $row): ?>
        <article class="adm31-ref-editor">
          <div class="adm31-ref-heading">
            <?php if(!empty($row['image'])): ?>
              <img loading="lazy" src="<?= e(upload_url($row['image'])) ?>" alt="<?= e($row['title']) ?>">
            <?php elseif(!empty($row['logo'])): ?>
              <img loading="lazy" src="<?= e(upload_url($row['logo'])) ?>" alt="<?= e($row['title']) ?>">
            <?php else: ?><span><?= icon('monitor',22) ?></span><?php endif; ?>
            <div><strong><?= e($row['title']) ?></strong>
              <small>
                <?php foreach (\App\Services\ReferencesService::normalizedPlacements($row) as $pos=>$placement): ?>
                <?= $pos ? ' · ' : '' ?><?= e($referenceGroups[$placement['group']]['label'] ?? '') ?> / <?= e($referenceGroups[$placement['group']]['services'][$placement['service']] ?? '') ?>
                <?php endforeach; ?>
              </small></div>
            <span class="adm31-pkg-status <?= $row['status']==='active'?'on':'off' ?>"><?= $row['status']==='active'?'Yayında':'Gizli' ?></span>
          </div>
          <form method="POST" action="/admin/referanslar/<?= e($row['id']) ?>/guncelle" enctype="multipart/form-data" class="adm31-reference-form" data-reference-editor>
            <?= csrfField() ?>
            <?php include __DIR__.'/fields.php'; ?>
            <button class="btn btn-primary btn-sm" type="submit"><?= icon('save',14) ?> Değişiklikleri Kaydet</button>
          </form>
          <form method="POST" action="/admin/referanslar/<?= e($row['id']) ?>/sil" onsubmit="return confirm('Bu referans vitrinden kaldırılsın mı?')">
            <?= csrfField() ?>
            <button type="submit" class="btn btn-outline btn-sm"><?= icon('trash',14) ?> Kaldır</button>
          </form>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  </div>
</div>
<script src="<?= asset('js/admin-references.js') ?>?v=20261013" defer></script>
