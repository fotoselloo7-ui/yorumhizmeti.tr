<?php
$isEdit=!empty($campaign['id']);
$selected=[];
foreach(($campaign['targets']??[]) as $t)$selected[$t['target_type'].':'.$t['target_id']]=true;
if(!$isEdit && !empty($presetTarget))$selected[(string)$presetTarget]=true;
$kind=$campaign['kind']??'coupon';
$presetName=(string)($presetName??'');
?>
<div class="adm-page-top nv98-top">
  <div>
    <span class="nv98-kicker"><?= icon('tag',15) ?> Kupon Sistemi</span>
    <h2><?= $isEdit?'Kupon / İndirim Düzenle':'Yeni Kupon / İndirim' ?></h2>
    <p>Ürüne veya kategoriye özel kupon, kullanım limiti, tarih aralığı ve müşteri kısıtı tanımlayın.</p>
  </div>
  <a href="/admin/kuponlar" class="btn btn-outline"><?= icon('arrow-left',13) ?> Listeye Dön</a>
</div>

<form method="POST" action="/admin/kuponlar/kaydet" class="nv98-form">
<?= csrfField() ?>
<?php if($isEdit): ?><input type="hidden" name="id" value="<?= (int)$campaign['id'] ?>"><?php endif; ?>
<div class="nv98-layout">
 <div class="nv98-main">
  <section class="adm-card">
   <div class="adm-card-header"><h3><?= icon('edit',15) ?> Kampanya Bilgileri</h3></div>
   <div class="adm-card-body nv98-grid">
    <div class="form-group nv98-span"><label>Kampanya Adı *</label><input class="form-control" name="name" maxlength="160" required value="<?= e($campaign['name']??$presetName) ?>" placeholder="Örn. Haber Yazılımı Lansman Kuponu"></div>
    <div class="form-group"><label>Tür</label><select class="form-control" name="kind" data-nv98-kind>
      <option value="coupon" <?= $kind==='coupon'?'selected':'' ?>>Kupon Kodu</option>
      <option value="automatic" <?= $kind==='automatic'?'selected':'' ?>>Otomatik İndirim</option>
    </select></div>
    <div class="form-group" data-nv98-code><label>Kupon Kodu *</label><input class="form-control nv98-code-input" name="code" maxlength="64" value="<?= e($campaign['code']??'') ?>" placeholder="NETVERA20" autocomplete="off"></div>
    <div class="form-group"><label>İndirim Tipi</label><select class="form-control" name="discount_type">
      <option value="fixed" <?= ($campaign['discount_type']??'fixed')==='fixed'?'selected':'' ?>>Sabit Tutar (TL)</option>
      <option value="percentage" <?= ($campaign['discount_type']??'')==='percentage'?'selected':'' ?>>Yüzde (%)</option>
    </select></div>
    <div class="form-group"><label>İndirim Değeri *</label><input class="form-control" type="number" min="0.01" step="0.01" name="discount_value" required value="<?= e($campaign['discount_value']??'') ?>" placeholder="500"></div>
    <div class="form-group"><label>Minimum Uygun Tutar</label><input class="form-control" type="number" min="0" step="0.01" name="min_subtotal" value="<?= e($campaign['min_subtotal']??'0') ?>"></div>
    <div class="form-group"><label>Maksimum İndirim</label><input class="form-control" type="number" min="0" step="0.01" name="max_discount" value="<?= e($campaign['max_discount']??'') ?>" placeholder="Boş = sınırsız"></div>
   </div>
  </section>

  <section class="adm-card">
   <div class="adm-card-header"><h3><?= icon('target',15) ?> Ürün & Kategori Hedefleri</h3></div>
   <div class="adm-card-body">
    <div class="nv98-target-help"><?= icon('info',14) ?><span>Hiç hedef seçmezseniz kampanya tüm mevcut hizmet paketlerine uygulanır. Ürün özel kupon için yalnızca ilgili ürünü seçin.</span></div>
    <?php
      $groups=[
        'package'=>['Hizmet Paketleri','package'],
        'service_category'=>['Hizmet Kategorileri','grid'],
        'script'=>['Hazır Yazılımlar','monitor'],
        'script_category'=>['Yazılım Kategorileri','layers'],
      ];
    ?>
    <div class="nv98-target-groups">
    <?php foreach($groups as $type=>$meta): $rows=$targetOptions[$type]??[]; ?>
      <details class="nv98-target-group" <?= $type==='package'?'open':'' ?>>
       <summary><?= icon($meta[1],14) ?><strong><?= e($meta[0]) ?></strong><span><?= count($rows) ?> kayıt</span><?= icon('chevron-down',12) ?></summary>
       <div class="nv98-target-search"><input type="search" class="form-control" placeholder="<?= e($meta[0]) ?> ara..." data-nv98-filter="<?= e($type) ?>"></div>
       <div class="nv98-target-list" data-nv98-list="<?= e($type) ?>">
       <?php foreach($rows as $row): $key=$type.':'.(int)$row['id']; ?>
        <label class="nv98-target-option" data-search="<?= e(mb_strtolower(($row['name']??'').' '.($row['slug']??''))) ?>">
          <input type="checkbox" name="targets[]" value="<?= e($key) ?>" <?= isset($selected[$key])?'checked':'' ?>>
          <span><strong><?= e($row['name']) ?></strong><small><?= e($row['slug']??'') ?> · #<?= (int)$row['id'] ?></small></span>
        </label>
       <?php endforeach; ?>
       </div>
      </details>
    <?php endforeach; ?>
    </div>
   </div>
  </section>
 </div>

 <aside class="nv98-side">
  <section class="adm-card">
   <div class="adm-card-header"><h3><?= icon('clock',15) ?> Tarih & Limitler</h3></div>
   <div class="adm-card-body">
    <div class="form-group"><label>Başlangıç</label><input class="form-control" type="datetime-local" name="starts_at" value="<?= !empty($campaign['starts_at'])?e(date('Y-m-d\TH:i',strtotime($campaign['starts_at']))):'' ?>"></div>
    <div class="form-group"><label>Bitiş</label><input class="form-control" type="datetime-local" name="ends_at" value="<?= !empty($campaign['ends_at'])?e(date('Y-m-d\TH:i',strtotime($campaign['ends_at']))):'' ?>"></div>
    <div class="form-group"><label>Toplam Kullanım Limiti</label><input class="form-control" type="number" min="1" name="total_usage_limit" value="<?= e($campaign['total_usage_limit']??'') ?>" placeholder="Boş = sınırsız"></div>
    <div class="form-group"><label>Müşteri Başına Limit</label><input class="form-control" type="number" min="1" name="per_customer_limit" value="<?= e($campaign['per_customer_limit']??'') ?>" placeholder="Boş = sınırsız"></div>
    <div class="form-group"><label>Belirli Üye ID</label><input class="form-control" type="number" min="1" name="customer_id" value="<?= e($campaign['customer_id']??'') ?>" placeholder="Boş = herkese açık"><small class="nv98-hint">Sadece tek bir üyeye özel kupon için doldurun.</small></div>
    <label class="nv98-switch"><input type="checkbox" name="is_active" value="1" <?= !isset($campaign['is_active'])||(int)$campaign['is_active']===1?'checked':'' ?>><span>Aktif ve kullanılabilir</span></label>
   </div>
  </section>
  <button class="adm-action-btn adm-btn-save nv98-save" type="submit"><?= icon('save',14) ?> <?= $isEdit?'Değişiklikleri Kaydet':'Kampanyayı Oluştur' ?></button>
 </aside>
</div>
</form>
<script>
(function(){
 const kind=document.querySelector('[data-nv98-kind]'), code=document.querySelector('[data-nv98-code]');
 function sync(){if(!kind||!code)return;code.style.display=kind.value==='coupon'?'':'none';const input=code.querySelector('input');if(input)input.required=kind.value==='coupon';}
 kind?.addEventListener('change',sync);sync();
 document.querySelectorAll('[data-nv98-filter]').forEach(input=>input.addEventListener('input',()=>{
   const q=input.value.toLocaleLowerCase('tr-TR').trim();
   const list=document.querySelector('[data-nv98-list="'+input.dataset.nv98Filter+'"]');
   list?.querySelectorAll('.nv98-target-option').forEach(row=>row.hidden=q!==''&&!row.dataset.search.includes(q));
 }));
 document.querySelector('.nv98-code-input')?.addEventListener('input',e=>{e.target.value=e.target.value.toLocaleUpperCase('tr-TR').replace(/[^A-Z0-9_-]/g,'')});
})();
</script>