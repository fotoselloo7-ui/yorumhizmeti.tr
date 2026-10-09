<div class="adm-page-top">
  <div><h2><?= icon('monitor',24) ?> Hazır Yazılım Vitrini</h2>
    <p class="text-sm text-secondary">Sektöre özel yazılım kategorileri, ayrı vitrin seçimi ve sürükleme gerektirmeyen sıra yönetimi.</p>
  </div>
  <div class="adm-page-top-badges">
    <a href="/hazir-yazilimlar" target="_blank" rel="noopener" class="btn btn-outline btn-sm"><?= icon('external-link',14) ?> Yazılım Kataloğunu Gör</a>
    <?php if (!empty($softwareCategoryReady)): ?>
    <a href="/admin/yazilim/ekle" class="btn btn-primary btn-sm"><?= icon('plus',14) ?> Yeni Yazılım Ekle</a>
    <?php else: ?>
    <form method="POST" action="/admin/hazir-yazilimlar/kur-ve-ekle" class="adm36-start-form">
      <?= csrfField() ?>
      <button class="btn btn-primary btn-sm" type="submit"><?= icon('plus',14) ?> Kategorileri Kur ve Yazılım Ekle</button>
    </form>
    <?php endif; ?>
  </div>
</div>
<div class="adm-card">
  <div class="adm-card-header"><h3><?= icon('layers',18) ?> Hazır Yazılım Kategorileri</h3></div>
  <div class="adm-card-body">
    <div class="adm31-setup-row">
      <div>
        <strong><?= $root ? e($root['name']) : 'Hazır Yazılımlar & Scriptler' ?></strong>
        <p><?= $root ? 'Ana kategori kayıtlı. Eksik alt kategorileri güvenle tamamlayabilirsiniz.' : 'Yazılım kategorilerini buradan yönetebilirsiniz.' ?></p>
        <small>Toplam <?= count($categories) ?> planlı alt kategori · Mevcut kategorilerin başlıkları, sıraları ve içerikleri değiştirilmez.</small>
      </div>
      <form method="POST" action="/admin/hazir-yazilimlar/kategorileri-kur" onsubmit="return confirm('Eksik yazılım kategorilerini oluşturmak istiyor musunuz? Mevcut kategoriler korunur.')">
        <?= csrfField() ?>
        <button type="submit" class="btn btn-primary"><?= icon('plus',15) ?> <?= $root ? 'Eksik Kategorileri Tamamla' : count($categories).' Kategoriyi Kur' ?></button>
      </form>
    </div>
    <details class="adm31-taxonomy"><summary><?= icon('grid',15) ?> Yazılım Alt Kategorilerini Gör (<?= count($categories) ?>)</summary>
      <div class="adm31-tags">
      <?php foreach($categories as [$label,$slug,$description,$symbol]): ?>
        <span title="<?= e($description) ?>"><?= icon($symbol,13) ?> <?= e($label) ?></span>
      <?php endforeach; ?>
      </div>
    </details>
    <p class="form-hint">İlave kategori eklemek veya düzenlemek için <a href="/admin/kategoriler">Kategori Yönetimi</a> bölümünü kullanabilirsin.</p>
  </div>
</div>
<form method="POST" action="/admin/hazir-yazilimlar/kaydet" class="adm31-showcase-form">
  <?= csrfField() ?>
  <div class="adm-card">
    <div class="adm-card-header"><h3><?= icon('star-fill',18) ?> Ana Sayfada Öne Çıkan Hazır Yazılımlar</h3></div>
    <div class="adm-card-body">
      <label class="adm31-switch"><input type="checkbox" name="enabled" value="1" <?= $prefs['enabled'] ? 'checked' : '' ?>>
        <span><strong>Yazılım kartları aktif</strong><small>Kapatırsan yalnız ürün kartları gizlenir; ana sayfadaki Hazır Yazılımlar bölümü görünür kalır.</small></span>
      </label>
      <?php if(empty($packages) && empty($otherPackages)): ?>
        <div class="adm31-empty">
          <?= icon('package',26) ?>
          <strong>Henüz hazır yazılım olarak tanımlanan paket yok.</strong>
          <p>Hazır Yazılımlar kategorisine ürün ekleyebilir ya da aşağıdaki diğer paketler listesinden mevcut bir yazılımı seçebilirsin.</p>
        </div>
      <?php else: ?>
        <p class="form-hint"><strong><?= empty($prefs['ids']) ? 'Otomatik vitrin etkin:' : 'Özel sıralama etkin:' ?></strong> <?= empty($prefs['ids']) ? 'Uygun aktif yazılımlar sırayla kendiliğinden gösterilir. Hiç işaretleme yapman gerekmez.' : 'Sadece seçtiğin ürünler girdiğin sıra ile gösterilir. Otomatiğe dönmek için seçimleri temizleyip kaydet.' ?></p>
        <div class="adm31-package-grid">
        <?php foreach($packages as $pkg):
          $active = $pkg['status']==='active' && $pkg['category_status']==='active'
            && ($pkg['category_parent_id'] === null || ($pkg['parent_status'] ?? '') === 'active');
          $rank = array_search((int)$pkg['id'], $prefs['ids'], true);
        ?>
        <div class="adm31-package-row">
          <label class="adm31-pkg-check"><input type="checkbox" name="featured[]" value="<?= (int)$pkg['id'] ?>" <?= $rank!==false?'checked':'' ?>>
            <span class="adm31-pkg-title"><strong><?= e($pkg['name']) ?></strong><small><?= e($pkg['category_name']) ?></small></span></label>
          <div class="adm31-pkg-right">
            <span class="adm31-pkg-status <?= $active?'on':'off' ?>"><?= $active?'Yayında':'Gizli/Pasif' ?></span>
            <label>Sıra <input aria-label="<?= e($pkg['name']) ?> sıra numarası" name="positions[<?= (int)$pkg['id'] ?>]" type="number" min="1" max="9999" value="<?= $rank!==false?($rank+1):999 ?>"></label>
            <a href="<?= \App\Services\SoftwareCatalogService::isSoftwareCategory((int)$pkg['category_id']) ? '/admin/yazilim/' . (int)$pkg['id'] . '/duzenle' : '/admin/paket/' . (int)$pkg['id'] . '/duzenle' ?>" title="Ürünü düzenle"><?= icon('edit',16) ?></a>
          </div>
        </div>
        <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($otherPackages)): ?>
      <details class="adm34-other-products">
        <summary><?= icon('plus',14) ?> Diğer kategorilerdeki paketlerden yazılım seç (<?= count($otherPackages) ?>)</summary>
        <p>Ürünün adı sadece “Demo” veya “Örnek Paket” ise otomatik tanınmaz. Gerçekten bir yazılımsa buradan seç; kategori ve ürün bilgileri değiştirilmez.</p>
        <div class="adm31-package-grid">
        <?php foreach($otherPackages as $pkg):
          $active = ($pkg['status'] ?? '')==='active' && ($pkg['category_status'] ?? '')==='active'
            && ($pkg['category_parent_id'] === null || ($pkg['parent_status'] ?? '')==='active');
        ?>
          <div class="adm31-package-row">
            <label class="adm31-pkg-check">
              <input type="checkbox" name="featured[]" value="<?= (int)$pkg['id'] ?>">
              <span class="adm31-pkg-title"><strong><?= e($pkg['name']) ?></strong><small><?= e($pkg['category_name']) ?></small></span>
            </label>
            <div class="adm31-pkg-right">
              <span class="adm31-pkg-status <?= $active?'on':'off' ?>"><?= $active?'Yayında':'Pasif' ?></span>
              <label>Sıra <input name="positions[<?= (int)$pkg['id'] ?>]" type="number" min="1" max="9999" value="999" aria-label="<?= e($pkg['name']) ?> vitrin sırası"></label>
            </div>
          </div>
        <?php endforeach; ?>
        </div>
      </details>
      <?php endif; ?>
    </div>
    <div class="adm31-savebar">
      <span><?= icon('info',14) ?> Hiç seçim yoksa aktif yazılımlar otomatik gösterilir; elle seçilenler özel sırayla yayınlanır.</span>
      <button type="submit" class="btn btn-primary"><?= icon('save',16) ?> Vitrini ve Sıralamayı Kaydet</button>
    </div>
  </div>
</form>
