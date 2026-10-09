<?php $isEdit = !empty($category); ?>
<div class="adm-page-top">
    <div class="adm-page-top-left">
        <a href="/admin/kategoriler" class="btn btn-outline btn-sm"><?= icon('arrow-left', 14) ?> Geri</a>
        <h2><?= $isEdit ? icon('edit', 22) . ' Kategori Düzenle' : icon('plus', 22) . ' Kategori Ekle' ?></h2>
    </div>
</div>

<form method="POST" action="<?= $isEdit ? '/admin/kategori/' . $category['id'] . '/guncelle' : '/admin/kategori/kaydet' ?>" enctype="multipart/form-data">
    <?= csrfField() ?>
    <div class="adm-form-layout">
        <!-- Main Content -->
        <div class="adm-form-main">
            <!-- General Info -->
            <div class="adm-card">
                <div class="adm-card-header"><h3><?= icon('grid', 18) ?> Genel Bilgiler</h3></div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>Kategori Adı</label>
                        <input type="text" name="name" class="form-control" value="<?= e($category['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" class="form-control" value="<?= e($category['slug'] ?? '') ?>" placeholder="Otomatik oluşturulur">
                    </div>
                    <div class="form-group">
                        <label>Üst Kategori</label>
                        <select name="parent_id" class="form-control">
                            <option value="">Ana Kategori</option>
                            <?php foreach ($parents as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($category['parent_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Açıklama</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Kategori açıklaması..."><?= e($category['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>İkon</label>
                        <select name="icon_key" class="form-control">
                            <?php foreach ($icons as $ic): ?>
                            <option value="<?= $ic ?>" <?= ($category['icon_key'] ?? 'package') === $ic ? 'selected' : '' ?>><?= $ic ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Image & Status -->
            <div class="adm-card">
                <div class="adm-card-header"><h3><?= icon('image', 18) ?> Görsel & Durum</h3></div>
                <div class="adm-card-body">
                    <div class="form-group">
                        <label>Görsel</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Görsel Alt Metni</label>
                        <input type="text" name="image_alt" class="form-control" value="<?= e($category['image_alt'] ?? '') ?>" placeholder="SEO için alt etiket">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Durum</label>
                            <select name="status" class="form-control">
                                <option value="active" <?= ($category['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="inactive" <?= ($category['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Pasif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sıra</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= $category['sort_order'] ?? 0 ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="adm-card">
                <div class="adm-card-header" style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap">
                    <h3><?= icon('search', 18) ?> SEO Ayarları</h3>
                    <?php if(empty($category) || !preg_match('/(haz[ıi]r.?script|yaz[ıi]l[ıi]m|yazilim|software|cms|script|web.?site|wordpress)/iu',(string)($category['slug']??'').' '.(string)($category['name']??''))): ?>
                    <button id="nvSeoFillCategory" type="button" class="btn btn-outline btn-sm"><?= icon('sparkles',15) ?> SEO / GEO Alanlarını Doldur</button>
                    <?php endif; ?>
                </div>
                <div class="adm-card-body">
                    <?php if (!empty($seoResult)): ?>
                    <div class="adm-seo-score-box">
                        <div class="seo-score-circle <?= $seoResult['color'] ?>"><?= $seoResult['score'] ?></div>
                        <div>
                            <div class="font-semibold"><?= $seoResult['label'] ?></div>
                            <div class="text-xs text-secondary">SEO Puanı</div>
                        </div>
                    </div>
                    <?php if (!empty($seoResult['issues'])): ?>
                    <div class="adm-seo-issues">
                        <?php foreach ($seoResult['issues'] as $issue): ?>
                        <div class="adm-seo-issue"><?= icon('alert-circle', 14) ?> <?= e($issue) ?></div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    <div class="form-group">
                        <label>SEO Başlığı</label>
                        <input type="text" name="seo_title" class="form-control" value="<?= e($category['seo_title'] ?? '') ?>" placeholder="Sayfa başlığı...">
                    </div>
                    <div class="form-group">
                        <label>Meta Açıklama</label>
                        <textarea name="seo_description" class="form-control" rows="2" placeholder="Arama motorlarında görünen açıklama..."><?= e($category['seo_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Odak Anahtar Kelime</label>
                        <input type="text" name="seo_focus_keyword" class="form-control" value="<?= e($category['seo_focus_keyword'] ?? '') ?>" placeholder="Ana hedef kelime...">
                    </div>
                    <div class="form-group">
                        <label>Canonical URL</label>
                        <input type="url" name="canonical_url" class="form-control" value="<?= e($category['canonical_url']??'') ?>" placeholder="https://netvera.tr/kategori/orijinal-slug">
                    </div>
                    <div class="form-group">
                        <label>Open Graph Başlığı</label>
                        <input type="text" name="og_title" class="form-control" value="<?= e($category['og_title']??'') ?>">
                    </div>
                    <div class="form-group">
                        <label>Open Graph Açıklaması</label>
                        <textarea name="og_description" class="form-control" rows="2"><?= e($category['og_description']??'') ?></textarea>
                    </div>
                </div>
            </div>
            <?php $nvSeoIsCategory = !preg_match('/(yaz[iı]l[iı]m|haz[iı]r.script|software|cms|web.site|tema)/iu', (string)($category['slug']??'').' '.(string)($category['name']??'')); require BASE_PATH.'/resources/views/admin/partials/netvera-seo.php'; ?>
        </div>

        <!-- Sidebar -->
        <div class="adm-form-side">
            <div class="adm-card" style="position: sticky; top: 76px;">
                <div class="adm-card-body">
                    <button type="submit" class="btn btn-primary btn-block btn-lg"><?= icon('save', 18) ?> <?= $isEdit ? 'Güncelle' : 'Kaydet' ?></button>
                    <a href="/admin/kategoriler" class="btn btn-outline btn-block btn-sm" style="margin-top: var(--space-3);"><?= icon('x', 14) ?> Vazgeç</a>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
(()=>{
  'use strict';
  const button=document.getElementById('nvSeoFillCategory');
  if(!button)return;
  const form=button.closest('form');
  const byName=name=>form.querySelector('[name="'+name+'"]');
  const set=(name,value)=>{
    const element=byName(name);
    if(!element || typeof value!=='string' || value==='')return;
    const current=String(element.value||'').trim();
    const legacy=/YorumHizmeti|Yorum Hizmeti/i.test(current);
    const shortMeta=['seo_description','og_description'].includes(name)&&current.length<75;
    if(!current || legacy || shortMeta)element.value=value;
  };
  button.addEventListener('click',async()=>{
    if(!byName('name')?.value.trim())return;
    const payload=new FormData();
    ['_csrf_token','name','slug','description'].forEach(key=>{
        payload.append(key,byName(key)?.value||'');
    });
    button.disabled=true;
    try{
      const response=await fetch('/admin/kategoriler/seo-oneri',{
        method:'POST',body:payload,credentials:'same-origin',cache:'no-store'
      });
      const data=await response.json();
      if(!response.ok||!data.ok)throw new Error(data.message||'Öneriler getirilemedi.');
      const profile=data.suggestion;
      ['slug','description','image_alt','seo_title','seo_description','seo_focus_keyword',
       'canonical_url','og_title','og_description'].forEach(k=>set(k,profile[k]||''));
      Object.entries(profile.extra||{}).forEach(([key,value])=>set('nvseo_'+key,value));
    }catch(error){
      // Only display an actual error; successful fill needs no instructional banner.
      alert(error.message||'SEO önerisi alınamadı.');
    }finally{button.disabled=false;}
  });
})();
</script>
