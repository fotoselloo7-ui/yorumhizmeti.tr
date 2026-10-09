<div class="adm-page-top"><h2><?= icon('message-circle',22) ?> Müşteri Yorumları</h2><a class="btn btn-outline btn-sm" href="/#nv30-feedback" target="_blank" rel="noopener">Sitede Gör <?= icon('external-link',14) ?></a></div>
<div class="adm-card"><div class="adm-card-header"><h3><?= icon('plus',17) ?> Yorum Ekle / Düzenle</h3></div>
 <div class="adm-card-body">
  <form action="/admin/yorumlar/kaydet" method="post" enctype="multipart/form-data" id="reviewForm">
   <?= csrfField() ?><input type="hidden" name="id" id="review-id">
   <div class="rv-admin-grid">
    <label>Ad Soyad<input class="form-control" name="name" id="review-name" maxlength="90" required></label>
    <label>Meslek / Unvan<input class="form-control" name="role" id="review-role" maxlength="90" placeholder="İşletme Sahibi"></label>
    <label>Yıldız Seviyesi<select class="form-control" name="rating" id="review-rating" required>
     <?php foreach([5,4,3,2,1] as $n): ?><option value="<?= $n ?>"><?= str_repeat('★',$n) ?> (<?= $n ?>/5)</option><?php endforeach; ?></select></label>
    <label>Yayın Durumu<select class="form-control" name="status" id="review-status"><option value="active">Yayında</option><option value="inactive">Taslak</option></select></label>
    <label>Kategori<select class="form-control" name="category_id" id="review-category"><option value="0">Kategori seçilmedi</option>
     <?php foreach($categories as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
    </select></label>
    <label>Satın Alınan Hizmet<select class="form-control" name="package_id" id="review-package"><option value="0">Hizmet seçilmedi</option>
     <?php foreach($packages as $p): ?><option value="<?= (int)$p['id'] ?>" data-category="<?= (int)$p['category_id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?></select></label>
    <label>Profil Fotoğrafı<input class="form-control" type="file" name="image" accept="image/png,image/jpeg,image/webp"></label>
    <label class="rv-admin-wide">Müşterinin Yorumu<textarea class="form-control" name="text" id="review-text" rows="4" minlength="10" maxlength="850" required></textarea></label>
   </div>
   <div class="rv-admin-actions"><button class="btn btn-primary" type="submit"><?= icon('save',16) ?> Yorumu Kaydet</button><button class="btn btn-outline" type="reset">Yeni Yorum</button></div>
  </form>
 </div>
</div>
<div class="adm-card"><div class="adm-card-header"><h3><?= icon('list',17) ?> Kayıtlı Yorumlar (<?= count($reviews) ?>)</h3></div>
 <div class="adm-card-body"><div class="rv-admin-list">
 <?php foreach($reviews as $r): ?>
 <article class="rv-admin-item">
  <div class="rv-admin-person">
   <?php if($r['image']): ?><img src="<?= e(upload_url($r['image'])) ?>" alt="" loading="lazy"><?php else: ?><span><?= e(mb_strtoupper(mb_substr($r['name'],0,1))) ?></span><?php endif; ?>
   <div><strong><?= e($r['name']) ?></strong><small><?= e($r['role']) ?> · <?= str_repeat('★',(int)($r['rating']??0)) ?></small>
    <small><?= e($r['category_name']?:'') ?><?= $r['service_name']?' · '.e($r['service_name']):'' ?></small>
   </div>
  </div>
  <p><?= e($r['text']) ?></p>
  <div class="rv-admin-actions"><span class="badge"><?= $r['status']==='active'?'Yayında':'Pasif' ?></span>
   <button type="button" class="btn btn-outline btn-sm rv-edit" data-review="<?= e(json_encode([
    'id'=>$r['id'],'name'=>$r['name'],'role'=>$r['role'],'text'=>$r['text'],'rating'=>$r['rating'],
    'status'=>$r['status'],'category_id'=>$r['category_id'],'package_id'=>$r['package_id']
   ],JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_TAG)) ?>">Düzenle</button>
   <form method="post" action="/admin/yorumlar/sil" onsubmit="return confirm('Bu yorumu kaldırmak istiyor musunuz?')">
   <?= csrfField() ?><input type="hidden" name="id" value="<?= e($r['id']) ?>"><button class="btn btn-outline btn-sm" type="submit">Kaldır</button></form>
  </div>
 </article>
 <?php endforeach; ?>
 </div></div>
</div>
<style>
.rv-admin-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.rv-admin-grid label{display:grid;gap:6px;font-size:12px;font-weight:600}.rv-admin-wide{grid-column:1/-1}.rv-admin-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:12px}.rv-admin-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}.rv-admin-item{border:1px solid #e4e9f2;border-radius:13px;padding:16px;min-width:0}.rv-admin-person{display:flex;align-items:center;gap:11px}.rv-admin-person img,.rv-admin-person>span{width:48px;height:48px;flex:none;border-radius:14px;object-fit:cover;background:#f0eaff;display:grid;place-items:center}.rv-admin-person strong{display:block}.rv-admin-person small{display:block;color:#687990;margin-top:2px}.rv-admin-item p{margin:13px 0;font-size:12px;line-height:1.7;overflow-wrap:anywhere}@media(max-width:740px){.rv-admin-grid,.rv-admin-list{grid-template-columns:1fr}}
</style>
<script>
(()=>{
 const form=document.getElementById('reviewForm'),category=document.getElementById('review-category'),pkg=document.getElementById('review-package');
 const adjust=()=>{const cat=category.value;Array.from(pkg.options).forEach(o=>{o.hidden=cat!=='0'&&o.value!=='0'&&o.dataset.category!==cat});if(pkg.selectedOptions[0]?.hidden)pkg.value='0'};
 category.addEventListener('change',adjust);
 form.addEventListener('reset',()=>setTimeout(adjust));
 document.querySelectorAll('.rv-edit').forEach(button=>button.addEventListener('click',()=>{
    let item;try{item=JSON.parse(button.dataset.review)}catch(e){return}
    for(const [key,value] of Object.entries(item)){
       const el=document.getElementById('review-'+(key==='category_id'?'category':key==='package_id'?'package':key));
       if(el)el.value=String(value??'');
    }
    adjust();pkg.value=String(item.package_id||0);
    form.scrollIntoView({behavior:'smooth',block:'start'});
 }));
})();
</script>