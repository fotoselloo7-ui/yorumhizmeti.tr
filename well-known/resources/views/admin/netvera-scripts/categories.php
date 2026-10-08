<div class="adm-page-top" style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap">
  <div>
    <h2><?= icon('layers',20) ?> Netvera Yazılım Kategorileri</h2>
    <p class="text-sm text-secondary">Eski kategori bağlantıları ve ürün slug'ları korunur. Bu bölüm hizmet paketlerinden bağımsızdır.</p>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <a class="btn btn-outline btn-sm" href="/admin/netvera-yazilimlar">Yazılımlara Dön</a>
    <?php if($ready && $categories): ?><a class="btn btn-primary btn-sm" href="/admin/netvera-yazilimlar/ekle"><?= icon('plus',14) ?> Yeni Yazılım</a><?php endif; ?>
  </div>
</div>
<?php if(!$ready): ?>
  <div class="adm-card"><div class="adm-card-body" style="max-width:810px">
    <h3>Staging içerik tabloları henüz hazır değil</h3>
    <p>Gerçek Netvera yedeğinin ürünlerini aktarmak için önce ayrı test veritabanında katalog şemasını hazırlamalısınız. Bu adım kullanıcı, sipariş, lisans veya ödeme tablolarına dokunmaz ve <strong>canlı ortamda çalışmaz</strong>.</p>
    <form method="post" action="/admin/netvera-kategoriler/staging-kur">
      <?= csrfField() ?>
      <button class="btn btn-primary" type="submit"><?= icon('database',16) ?> Staging Katalog Tablolarını Hazırla</button>
    </form>
    <p class="text-sm text-secondary">Güvenlik koşulu: APP_ENV=staging, NETVERA_IMPORT_ALLOWED=1 ve ayrı bir test MySQL veritabanı.</p>
  </div></div>
<?php else: ?>
  <div class="adm-card" style="margin-bottom:20px">
    <div class="adm-card-header"><h3><?= icon('plus',16) ?> Yeni Yazılım Kategorisi</h3></div>
    <div class="adm-card-body">
      <form method="post" action="/admin/netvera-kategoriler/kaydet">
        <?= csrfField() ?><input type="hidden" name="id" value="0">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:12px">
          <div class="form-group"><label for="nvcat-name">Kategori Adı *</label><input id="nvcat-name" class="form-control" name="name" required maxlength="240" placeholder="Örn: Sektörel Web Yazılımları"></div>
          <div class="form-group"><label for="nvcat-slug">SEO Slug (boşsa otomatik)</label><input id="nvcat-slug" class="form-control" name="slug" maxlength="220" placeholder="sektorel-yazilimlar"></div>
          <div class="form-group"><label for="nvcat-parent">Üst Kategori</label>
            <select id="nvcat-parent" class="form-control" name="parent_legacy_id">
              <option value="0">Ana kategori</option>
              <?php foreach($categories as $cat): if(!empty($cat['parent_legacy_id']) || !(int)$cat['active'])continue; ?>
                <option value="<?= (int)$cat['legacy_id'] ?>"><?= e($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group"><label for="nvcat-sort">Sıralama</label><input id="nvcat-sort" class="form-control" name="sort_order" type="number" value="0"></div>
        </div>
        <div class="form-group"><label for="nvcat-desc">Açıklama</label><textarea id="nvcat-desc" class="form-control" name="description" rows="2"></textarea></div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px">
          <div class="form-group"><label for="nvcat-seo-title">SEO Başlığı</label><input id="nvcat-seo-title" class="form-control" name="meta_title" maxlength="255"></div>
          <div class="form-group"><label for="nvcat-seo-desc">Meta Açıklama</label><textarea id="nvcat-seo-desc" class="form-control" name="meta_description" rows="2"></textarea></div>
        </div>
        <label style="display:flex;align-items:center;gap:8px;margin:12px 0"><input type="checkbox" name="active" value="1" checked> Kategori yayında</label>
        <button class="btn btn-primary" type="submit"><?= icon('save',16) ?> Yeni Kategoriyi Kaydet</button>
      </form>
    </div>
  </div>

  <div class="adm-card">
    <div class="adm-card-header"><h3><?= icon('folder',16) ?> Mevcut Kategoriler (<?= count($categories) ?>)</h3></div>
    <div class="adm-card-body">
      <?php if(!$categories): ?>
        <p>Henüz kategori bulunmuyor. Yukarıdan ilk kategoriyi ekleyebilirsiniz.</p>
      <?php else: ?>
      <div style="display:grid;gap:12px">
      <?php foreach($categories as $cat): ?>
        <details style="border:1px solid #e2e6ef;border-radius:12px;padding:12px 15px;background:#fff">
          <summary style="cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <span style="display:flex;gap:8px;align-items:center">
              <?= icon('folder',15) ?> <strong><?= e($cat['name']) ?></strong>
              <small style="color:#71809a"><?= (int)$cat['product_count'] ?> yazılım</small>
            </span>
            <span style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
              <code><?= e($cat['slug']) ?></code>
              <small><?= (int)$cat['active']?'Yayında':'Pasif' ?></small>
              <?= icon('chevron-down',15) ?>
            </span>
          </summary>
          <form method="post" action="/admin/netvera-kategoriler/kaydet" style="margin-top:18px">
            <?= csrfField() ?><input type="hidden" name="id" value="<?= (int)$cat['legacy_id'] ?>">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px">
              <div class="form-group"><label>Kategori Adı</label><input class="form-control" name="name" required maxlength="240" value="<?= e($cat['name']) ?>"></div>
              <div class="form-group"><label>Orijinal Slug (korunur)</label><input class="form-control" readonly value="<?= e($cat['slug']) ?>"></div>
              <div class="form-group"><label>Sıralama</label><input class="form-control" name="sort_order" type="number" value="<?= (int)$cat['sort_order'] ?>"></div>
              <div class="form-group"><label>SEO Başlığı</label><input class="form-control" name="meta_title" maxlength="255" value="<?= e($cat['meta_title']??'') ?>"></div>
            </div>
            <div class="form-group"><label>Kategori Açıklaması</label><textarea class="form-control" rows="2" name="description"><?= e($cat['description']??'') ?></textarea></div>
            <div class="form-group"><label>SEO Meta Açıklama</label><textarea class="form-control" rows="2" name="meta_description"><?= e($cat['meta_description']??'') ?></textarea></div>
            <label style="display:flex;align-items:center;gap:8px;margin:12px 0">
              <input type="checkbox" name="active" value="1" <?= (int)$cat['active']?'checked':'' ?>> Kategori yayında
            </label>
            <p class="text-sm text-secondary">Önceden indekslenen kategori slug'ı, üst kategori ilişkisi ve bağlı ürünlerin URL'leri değişmez.</p>
            <button class="btn btn-primary btn-sm" type="submit"><?= icon('save',14) ?> Değişiklikleri Kaydet</button>
          </form>
        </details>
      <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>
