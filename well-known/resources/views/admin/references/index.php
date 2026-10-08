<div class="adm-page-top">
  <div><h2><?= icon('award',24) ?> Referanslarımız</h2>
    <p class="text-sm text-secondary">Gerçek projelerini ekle, görselleri yükle, yayın durumunu ve ana sayfa sırasını yönet.</p></div>
  <a href="/" target="_blank" rel="noopener" class="btn btn-outline btn-sm"><?= icon('external-link',14) ?> Ana Sayfayı Gör</a>
</div>
<div class="adm31-help">
  <?= icon('shield-check',18) ?>
  <div><strong>Yalnızca gerçek çalışmalarınız gösterilir.</strong>
    <p>Henüz referans eklemediysen bölüm otomatik gizlenir; demo logo veya sahte proje yayınlanmaz. En fazla 36 çalışma eklenebilir.</p>
  </div>
</div>
<div class="adm-card">
  <div class="adm-card-header"><h3><?= icon('plus',18) ?> Yeni Referans Ekle</h3></div>
  <div class="adm-card-body">
    <form method="POST" action="/admin/referanslar/ekle" enctype="multipart/form-data" class="adm31-reference-form">
      <?= csrfField() ?>
      <div class="adm31-form-grid">
        <label>Proje / Firma Adı *<input class="form-control" type="text" name="title" required maxlength="140" placeholder="Örn. Firmanın adı"></label>
        <label>Proje Türü / Sektör<input class="form-control" type="text" name="category" maxlength="100" placeholder="Örn. Emlak web yazılımı"></label>
        <label>Canlı Site veya Demo Bağlantısı<input class="form-control" type="url" name="url" placeholder="https://..."></label>
        <label>Sıralama<input class="form-control" type="number" name="sort_order" min="0" max="9999" value="100"></label>
        <label>Proje Görseli (JPG/PNG/WebP)<input class="form-control" type="file" name="image" accept="image/png,image/jpeg,image/webp"></label>
        <label>Yayın Durumu<select class="form-control" name="status"><option value="active">Yayında</option><option value="inactive">Gizli</option></select></label>
      </div>
      <label class="adm31-wide-label">Kısa Proje Açıklaması<textarea class="form-control" name="description" rows="2" maxlength="260" placeholder="Gerçekte yaptığınız çalışmayı kısaca anlatın."></textarea></label>
      <button type="submit" class="btn btn-primary"><?= icon('plus',15) ?> Referansı Kaydet</button>
    </form>
  </div>
</div>
<div class="adm-card">
  <div class="adm-card-header"><h3><?= icon('grid',18) ?> Kayıtlı Referanslar (<?= count($references) ?>)</h3></div>
  <div class="adm-card-body">
  <?php if(empty($references)): ?>
    <div class="adm31-empty"><?= icon('image',28) ?><strong>Henüz referans eklenmemiş.</strong><p>Proje eklediğinde ana sayfada Referanslarımız alanı otomatik oluşur.</p></div>
  <?php else: ?>
    <div class="adm31-existing-refs">
      <?php foreach($references as $row): ?>
        <article class="adm31-ref-editor">
          <div class="adm31-ref-heading">
            <?php if(!empty($row['image'])): ?><img loading="lazy" src="<?= e(upload_url($row['image'])) ?>" alt="<?= e($row['title']) ?>"><?php else: ?><span><?= icon('monitor',22) ?></span><?php endif; ?>
            <div><strong><?= e($row['title']) ?></strong><small><?= e($row['category'] ?: 'Sektör belirtilmedi') ?></small></div>
            <span class="adm31-pkg-status <?= $row['status']==='active'?'on':'off' ?>"><?= $row['status']==='active'?'Yayında':'Gizli' ?></span>
          </div>
          <form method="POST" action="/admin/referanslar/<?= e($row['id']) ?>/guncelle" enctype="multipart/form-data" class="adm31-reference-form">
            <?= csrfField() ?>
            <div class="adm31-form-grid">
              <label>Proje Adı<input class="form-control" name="title" maxlength="140" required value="<?= e($row['title']) ?>"></label>
              <label>Tür / Sektör<input class="form-control" name="category" maxlength="100" value="<?= e($row['category']) ?>"></label>
              <label>Bağlantı<input class="form-control" type="url" name="url" value="<?= e($row['url']) ?>"></label>
              <label>Sıra<input class="form-control" type="number" name="sort_order" min="0" max="9999" value="<?= (int)$row['sort_order'] ?>"></label>
              <label>Görseli Değiştir<input class="form-control" type="file" name="image" accept="image/png,image/jpeg,image/webp"></label>
              <label>Durum<select class="form-control" name="status">
                <option value="active" <?= $row['status']==='active'?'selected':'' ?>>Yayında</option>
                <option value="inactive" <?= $row['status']!=='active'?'selected':'' ?>>Gizli</option>
              </select></label>
            </div>
            <label class="adm31-wide-label">Açıklama<textarea class="form-control" name="description" rows="2" maxlength="260"><?= e($row['description']) ?></textarea></label>
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
