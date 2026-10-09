<div class="adm-page-top">
    <div>
        <h2><?= icon('menu', 23) ?> Üst Menü Yönetimi</h2>
    </div>
    <a class="btn btn-outline btn-sm" href="/" target="_blank" rel="noopener"><?= icon('external-link',14) ?> Menü Önizlemesi</a>
</div>

<form method="POST" action="/admin/menu/kaydet" class="adm-menu-editor" id="navMenuEditor">
    <?= csrfField() ?>
    <div class="adm-card">
        <div class="adm-card-header"><h3><?= icon('list',17) ?> Menü Öğeleri</h3></div>
        <div class="adm-card-body">
            <div class="adm-nav-list">
                <?php foreach($menuItems as $item): ?>
                <div class="adm-nav-item <?= !$item['available'] ? 'unavailable' : '' ?>">
                    <label class="adm-nav-switch">
                        <input type="checkbox" name="enabled[]" value="<?= e($item['key']) ?>" <?= $item['enabled'] ? 'checked' : '' ?> <?= !$item['available'] ? 'disabled' : '' ?>>
                        <span class="adm-nav-switch-track" aria-hidden="true"></span>
                        <span class="adm-nav-switch-text"><?= $item['enabled'] ? 'Açık' : 'Kapalı' ?></span>
                    </label>
                    <div class="adm-nav-label">
                        <label for="nav-label-<?= e($item['key']) ?>">Menü Başlığı</label>
                        <input id="nav-label-<?= e($item['key']) ?>" class="form-control" type="text" maxlength="40" name="label[<?= e($item['key']) ?>]" value="<?= e($item['label']) ?>" required>
                        <small><?= e($item['url']) ?></small>
                    </div>
                    <div class="adm-nav-order">
                        <label for="nav-sort-<?= e($item['key']) ?>">Sıra</label>
                        <input id="nav-sort-<?= e($item['key']) ?>" class="form-control" type="number" min="0" max="9999" name="sort[<?= e($item['key']) ?>]" value="<?= (int)$item['sort'] ?>">
                    </div>
                    <?php if(!$item['available']): ?>
                        <span class="adm-nav-unavailable"><?= icon('alert-circle',13) ?> Kategori pasif</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="adm-nav-submit">
        <button type="submit" class="btn btn-primary"><?= icon('save',17) ?> Menüyü Kaydet</button>
        
    </div>
</form>
<form method="POST" action="/admin/menu/sifirla" class="adm-nav-reset" onsubmit="return confirm('Üst menüyü varsayılan sıralamaya ve görünürlüğe döndürmek istiyor musunuz?')">
    <?= csrfField() ?>
    <button type="submit" class="btn btn-outline btn-sm"><?= icon('rotate-ccw',14) ?> Varsayılan Menüyü Geri Yükle</button>
</form>
<script>
document.querySelectorAll('.adm-nav-switch input').forEach(function(input){
  function sync(){ var text=input.closest('label').querySelector('.adm-nav-switch-text');if(text)text.textContent=input.checked?'Açık':'Kapalı'; }
  input.addEventListener('change',sync);sync();
});
</script>
