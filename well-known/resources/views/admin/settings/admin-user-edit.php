<div class="adm-page-top">
  <div>
    <p style="color:#6b778f;font-size:12px;margin:0 0 5px">YÖNETİCİ GÜVENLİĞİ</p>
    <h2><?= icon('user-cog',22) ?> <?= e($isSelf?'Hesabımı Düzenle':'Yönetici Düzenle') ?></h2>
    <p style="font-size:12px;color:#718099">E-posta veya şifre değişiklikleri için kendi mevcut yönetici şifrenizle onay verin.</p>
  </div>
  <a class="btn btn-light" href="/admin/admin-kullanicilari"><?= icon('arrow-left',16) ?> Admin Kullanıcıları</a>
</div>
<div class="adm-card" style="max-width:830px">
  <div class="adm-card-body">
    <form method="post" action="/admin/admin-kullanicilari/<?= (int)$targetAdmin['id'] ?>/guncelle" autocomplete="off">
      <?= \App\Core\Csrf::field() ?>
      <div class="form-row">
        <div class="form-group">
          <label for="nv-admin-name">Adı / Görünen İsim</label>
          <input id="nv-admin-name" name="name" class="form-control" type="text" maxlength="100" required value="<?= e($targetAdmin['name']) ?>">
        </div>
        <div class="form-group">
          <label for="nv-admin-email">Yönetici E-postası</label>
          <input id="nv-admin-email" name="email" class="form-control" type="email" maxlength="191" required value="<?= e($targetAdmin['email']) ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="nv-admin-role">Yetki</label>
          <?php if(!$isSelf && $currentAdmin['role']==='super_admin'): ?>
          <select id="nv-admin-role" name="role" class="form-control">
            <option value="super_admin" <?= $targetAdmin['role']==='super_admin'?'selected':'' ?>>Süper Admin</option>
            <option value="admin" <?= $targetAdmin['role']==='admin'?'selected':'' ?>>Admin</option>
            <option value="editor" <?= $targetAdmin['role']==='editor'?'selected':'' ?>>Editör</option>
          </select>
          <?php else: ?>
          <input type="text" class="form-control" value="<?= e($targetAdmin['role']) ?>" readonly>
          <?php endif; ?>
        </div>
        <div class="form-group">
          <label for="nv-admin-status">Hesap Durumu</label>
          <?php if(!$isSelf && $currentAdmin['role']==='super_admin'): ?>
          <select id="nv-admin-status" name="status" class="form-control">
            <option value="active" <?= $targetAdmin['status']==='active'?'selected':'' ?>>Aktif</option>
            <option value="inactive" <?= $targetAdmin['status']==='inactive'?'selected':'' ?>>Pasif</option>
          </select>
          <?php else: ?>
          <input type="text" class="form-control" value="<?= $targetAdmin['status']==='active'?'Aktif':'Pasif' ?>" readonly>
          <?php endif; ?>
        </div>
      </div>
      <div class="adm-card" style="margin:15px 0;padding:20px;background:#f7f9ff;border:1px solid #e1e7f4;border-radius:13px">
        <h3 style="margin:0 0 7px;font-size:16px"><?= icon('lock',17) ?> Şifre Değişikliği</h3>
        <p style="color:#6a7790;font-size:12px;margin-bottom:14px">Şifreyi değiştirmeyecekseniz iki yeni şifre alanını boş bırakın. Eski şifrenizi hiçbir kullanıcıya göndermeyin.</p>
        <div class="form-row">
          <div class="form-group"><label for="nv-admin-new-password">Yeni Şifre (İsteğe Bağlı)</label>
            <input id="nv-admin-new-password" class="form-control" type="password" name="new_password" minlength="12" maxlength="128" autocomplete="new-password">
          </div>
          <div class="form-group"><label for="nv-admin-password-confirm">Yeni Şifre Tekrarı</label>
            <input id="nv-admin-password-confirm" class="form-control" type="password" name="new_password_confirm" minlength="12" maxlength="128" autocomplete="new-password">
          </div>
        </div>
      </div>
      <div class="form-group">
        <label for="nv-admin-current-password">Kendi Mevcut Yönetici Şifreniz <span style="color:#b93066">*</span></label>
        <input id="nv-admin-current-password" name="current_password" class="form-control" type="password" required autocomplete="current-password" placeholder="Güncellemeyi onaylamak için girin">
        <small>Başka bir yönetici hesabını düzenleseniz bile kendi yönetici şifrenizle doğrulama yapılır.</small>
      </div>
      <button class="btn btn-primary" type="submit"><?= icon('save',17) ?> Bilgileri Kaydet</button>
    </form>
  </div>
</div>
