<?php
/** Shared admin multiselect for real References module records. */
$nvRefType=$nvRefType??'package';
$nvRefId=(int)($nvRefId??0);
$nvChosen=$nvRefId?\App\Services\ServiceReferenceSelectionService::ids($nvRefType,$nvRefId):[];
$nvAvailable=\App\Services\ReferencesService::all();
?>
<div class="adm-card nv-service-ref-picker">
  <div class="adm-card-header"><h3><?= icon('briefcase',18) ?> Bu Hizmete Özel Referanslar</h3></div>
  <div class="adm-card-body">
    <p class="text-sm">Aşağıdaki çalışmalar doğrudan mevcut <strong>Referanslarımız</strong> modülünden gelir. İstediğiniz referansları seçin; yalnız yayında olanları ziyaretçiler görür. Bağlanmamış işler bu sayfada gösterilmez.</p>
    <?php if(!$nvAvailable): ?>
      <p>Henüz kayıtlı referans yok. <a href="/admin/referanslar" target="_blank" rel="noopener">Referanslar modülünden ekleyin</a>.</p>
    <?php else: ?>
      <div class="nv-service-ref-picker-list" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:9px;max-height:340px;overflow:auto;margin-top:12px">
      <?php foreach($nvAvailable as $ref):
        $placements=\App\Services\ReferencesService::normalizedPlacements($ref);
        $labels=[];foreach($placements as $placement)$labels[]=$placement['service'];
      ?>
        <label style="display:flex;align-items:flex-start;gap:9px;padding:12px;border:1px solid #dfe4f0;border-radius:10px;cursor:pointer;background:#fff">
          <input type="checkbox" name="service_reference_ids[]" value="<?= e($ref['id']) ?>" <?= in_array($ref['id'],$nvChosen,true)?'checked':'' ?> style="margin-top:3px">
          <span style="min-width:0"><strong><?= e($ref['title']) ?></strong><small style="display:block;margin-top:4px;color:#778097"><?= e(implode(' · ',$labels)) ?> · <?= ($ref['status']??'')==='active'?'Yayında':'Pasif' ?></small></span>
        </label>
      <?php endforeach; ?>
      </div>
      <small style="display:block;margin-top:10px">Bir hizmet için en fazla 12 referans seçilir; modül mevcut orijinal kapakları ve müşteri bağlantılarını kullanır.</small>
    <?php endif; ?>
  </div>
</div>
