<div class="adm-page-top">
  <div><h2><?= icon('monitor',22) ?> Hazır Yazılım Ekle</h2>
    <p class="text-secondary">Yeni yazılımı ekleyebilmek için önce yazılım kategorileri hazırlanmalı.</p>
  </div>
  <a href="/admin/hazir-yazilimlar" class="btn btn-outline btn-sm"><?= icon('arrow-left',14) ?> Yazılım Vitrini</a>
</div>
<div class="adm-card" style="max-width:820px;margin:24px auto">
  <div class="adm-card-header"><h3><?= icon('layers',18) ?> Tek seferlik kategori kurulumu</h3></div>
  <div class="adm-card-body" style="padding:28px">
    <p>Yazılım ürünleri için <?= (int)$categoryCount ?> hazır alt kategori ilk kez oluşturulacak. Mevcut hizmet kategorileri, paketler, fiyatlar, URL'ler ve siparişler değiştirilmez.</p>
    <p>Kurulum tamamlanınca doğrudan <strong>Hazır Yazılım Ekle</strong> formu açılır. Yalnızca yazılım alt kategorilerini göreceksin.</p>
    <form method="POST" action="/admin/hazir-yazilimlar/kur-ve-ekle">
      <?= csrfField() ?>
      <button class="btn btn-primary" type="submit"><?= icon('plus',15) ?> Kategorileri Kur ve Yazılım Ekle <?= icon('arrow-right',14) ?></button>
    </form>
  </div>
</div>
