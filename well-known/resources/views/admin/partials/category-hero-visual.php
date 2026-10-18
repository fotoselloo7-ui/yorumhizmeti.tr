<div class="form-row">
 <div class="form-group"><label for="<?= e($heroFieldId) ?>-visual">Banner Görseli</label><select id="<?= e($heroFieldId) ?>-visual" class="form-control" name="hero_visual">
  <option value="auto" <?= ($heroSaved['visual'] ?? 'auto')==='auto'?'selected':'' ?>>Hizmete göre otomatik</option>
  <?php foreach(\App\Services\CategoryHeroService::families() as $key=>$spec): ?><option value="<?= e($key) ?>" <?= ($heroSaved['visual'] ?? '')===$key?'selected':'' ?>><?= e($spec[0]) ?></option><?php endforeach; ?>
 </select></div>
 <div class="form-group"><label for="<?= e($heroFieldId) ?>-alt">Banner Alt Metni (isteğe bağlı)</label><input id="<?= e($heroFieldId) ?>-alt" class="form-control" name="hero_alt" maxlength="200" value="<?= e($heroSaved['alt'] ?? '') ?>" placeholder="Boşsa kategori ve görsele göre hazırlanır"></div>
</div>
