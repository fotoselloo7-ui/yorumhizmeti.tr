<div class="adm-page-top">
  <div><h2><?= icon('award',24) ?> Referanslarımız</h2>
    <p class="text-sm text-secondary">Ajans & Yazılım ve SEO & Dijital çalışmalarını yönet; bir referansı iki ayrı kategoriye bağla. Post/Reels kapakları ve müşteri logoları ekle.</p></div>
  <a href="/#referanslarimiz" target="_blank" rel="noopener" class="btn btn-outline btn-sm"><?= icon('external-link',14) ?> Referans Vitrinini Gör</a>
</div>
<div class="adm31-help">
  <?= icon('shield-check',18) ?>
  <div><strong>Yayınlanan referanslar yalnızca gerçek çalışmalardan oluşur.</strong>
    <p>Web/yazılım referansları müşteri sitesine gider. Reels ve postların kapağı ana sayfada görünür. Instagram gömmeyi engelleyebildiği için kesintisiz site içi video oynatma amacıyla YouTube (liste dışı), Bunny Stream, Cloudflare Stream, Vimeo veya harici MP4 bağlantısı kullanılabilir. Bu seçenek video dosyasını bizim sunucuda tutmaz. Yerel MP4 yükleme isteğe bağlı olarak korunmuştur.</p>
  </div>
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
      <p>Bir çalışma yayınladığında Ajans & Yazılım veya SEO & Dijital sekmesinde görünür.</p>
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
<script>
document.querySelectorAll('[data-reference-editor]').forEach(function(form) {
  const group = form.querySelector('[data-ref-group]');
  const service = form.querySelector('[data-ref-service]');
  const secondGroup = form.querySelector('[data-ref-second-group]');
  const secondService = form.querySelector('[data-ref-second-service]');
  const media = form.querySelector('[data-ref-media]');
  const videoField = form.querySelector('[data-ref-video-field]');
  const videoInput = form.querySelector('[data-ref-video-input]');
  const hostedVideoField = form.querySelector('[data-ref-hosted-video-field]');
  const hostedVideoInput = form.querySelector('[data-ref-hosted-video]');
  const url = form.querySelector('[data-ref-url]');
  const hint = form.querySelector('[data-ref-url-hint]');
  if (!group || !service || !media || !url) return;
  function sync() {
    let selectedVisible = false;
    Array.from(service.options).forEach(function(option) {
      const visible = option.dataset.parent === group.value;
      option.hidden = !visible;
      option.disabled = !visible;
      if (visible && option.selected) selectedVisible = true;
    });
    if (!selectedVisible) {
      const first = Array.from(service.options).find(o => !o.disabled);
      if (first) service.value = first.value;
    }
    if (secondGroup && secondService) {
      const enabled = secondGroup.value !== '';
      secondService.disabled = !enabled;
      secondService.required = enabled;
      let validSelection = false;
      Array.from(secondService.options).forEach(function(option) {
        if (!option.value) { option.hidden = enabled; return; }
        const visible = enabled && option.dataset.parent === secondGroup.value;
        option.hidden = !visible;
        option.disabled = !visible;
        if (visible && option.selected) validSelection = true;
      });
      if (!enabled) {
        secondService.value = '';
      } else if (!validSelection) {
        const first = Array.from(secondService.options).find(option => !option.disabled && option.value);
        if (first) secondService.value = first.value;
      }
    }
    const instagram = media.value.startsWith('instagram_');
    if (videoField) videoField.style.display = instagram ? '' : 'none';
    if (videoInput) videoInput.disabled = !instagram;
    if (hostedVideoField) hostedVideoField.style.display = instagram ? '' : 'none';
    if (hostedVideoInput) hostedVideoInput.disabled = !instagram;
    url.placeholder = instagram ?
      (media.value === 'instagram_reel' ? 'https://www.instagram.com/reel/ABC123/' : 'https://www.instagram.com/p/ABC123/') :
      'https://musteri-sitesi.com';
    url.required = instagram || media.value === 'website';
    hint.textContent = instagram ?
      'Herkese açık Instagram bağlantısı girin. Video veya gönderi, kapak tıklanınca sayfada açılır.' :
      (media.value === 'website' ? 'Müşteri sitesine direkt yönlendirilir.' : 'İsteğe bağlı görsel bağlantısı.');
  }
  group.addEventListener('change', sync);
  if (secondGroup) secondGroup.addEventListener('change', sync);
  media.addEventListener('change', sync);
  sync();
});
</script>