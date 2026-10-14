# UI dependency inventory — 14 Ekim 2026

`node well-known/scripts/audit-ui-dependencies.mjs` ile üretilen kaynak haritasının gözden geçirilmiş snapshot'ı. `.sync-conflict-*` dosyaları dışarıdadır. Referans bulunması tek başına her route'ta yüklenme anlamına gelmez; layout koşulları korunur. Duplicate selector sayıları medya/state varyantlarını da içerebildiğinden otomatik silme kararı değildir.

## Layout yükleme sırası

### resources/views/layouts/admin.php

CSS (kaynak sırası; koşullu include'lar dahil):

1. `app.css`
2. `admin.css`
3. `admin-v4.css`
4. `admin-smm.css`
5. `admin-customers-v67.css`
6. `admin-showcases-v31.css`
7. `netvera-admin-products.css`
8. `responsive.css`
9. `admin-sidebar-readable-v77.css`
10. `admin-hero-slider-v94.css`
11. `admin-popup-v97.css`
12. `admin-discounts-v98.css`

JS (kaynak sırası; koşullu include'lar dahil):

1. `admin.js`
2. `nv-desk-alerts.js`
3. `nv-desk-admin.js`

### resources/views/layouts/app.php

CSS (kaynak sırası; koşullu include'lar dahil):

1. `app.css`
2. `responsive.css`
3. `premium.css`
4. `storefront-v4.css`
5. `home-v6.css`
6. `approved-v9.css`
7. `visual-consistency-v10.css`
8. `visual-fidelity-v11.css`
9. `visual-demo-v12.css`
10. `responsive-fluid-v13.css`
11. `responsive-balanced-v14.css`
12. `category-brand-v15.css`
13. `typography-readability-v16.css`
14. `section-rhythm-v17.css`
15. `featured-footer-v18.css`
16. `responsive-repair-v19.css`
17. `forms-functional-v20.css`
18. `catalog-bridge-v21.css`
19. `motion-v22.css`
20. `premium-section-footer-v23.css`
21. `featured-groups-v24.css`
22. `featured-software-v41.css`
23. `agency-navigation-v26.css`
24. `agency-catalog-v26.css`
25. `agency-mega-premium-v27.css`
26. `icon-alignment-v28.css`
27. `home-service-groups-v29.css`
28. `premium-trust-steps-v30.css`
29. `software-showcase-v31.css`
30. `software-navigation-v33.css`
31. `software-marketplace-v36.css`
32. `netvera-legacy-premium-v40.css`
33. `netvera-product-detail-v60.css`
34. `netvera-inquiries.css`
35. `netvera-article-v42.css`
36. `featured-category-slider-v43.css`
37. `home-blog-covers-v45.css`
38. `home-faq-final-v47.css`
39. `home-footer-motion-v49.css`
40. `reference-portfolio-v51.css`
41. `reference-player-v52.css`
42. `premium-hover-v55.css`
43. `nav-quick-marquee-v56.css`
44. `netvera-account-history-v57.css`
45. `netvera-dealer-v62.css`
46. `package-benefits-v67.css`
47. `reviews-v68.css`
48. `theme-runtime-v71.css`
49. `netvera-premium-v76.css`
50. `social-platform-identity-v77.css`
51. `platform-inheritance-flat-v80.css`
52. `service-category-identity-v82.css`
53. `header-reputation-parity-v83.css`
54. `catalog-visual-parity-v84.css`
55. `category-pastel-palette-v85.css`
56. `subcategory-neutral-v86.css`
57. `catalog-contrast-v87.css`
58. `category-directory-v88.css`
59. `storefront-design-language-v89.css`
60. `site-logo-v90.css`
61. `home-hero-slider-v94.css`
62. `auth-brand-logo-v94.css`
63. `marketing-popup-v97.css`
64. `coupon-checkout-v98.css`

JS (kaynak sırası; koşullu include'lar dahil):

1. `software-marketplace-v36.js`
2. `netvera-product-tabs-v60.js`
3. `category-marquee-drag-v56.js`
4. `nv-desk-alerts.js`
5. `app.js`
6. `icon-bridge.js`
7. `home-featured-tabs-v18.js`
8. `home-hero-slider-v94.js`
9. `marketing-popup-v97.js`
10. `package-benefits-v67.js`
11. `reviews-v68.js`
12. `agency-navigation-v26.js`

### resources/views/layouts/auth.php

CSS (kaynak sırası; koşullu include'lar dahil):

1. `app.css`
2. `responsive.css`
3. `storefront-v4.css`
4. `responsive-fluid-v13.css`
5. `responsive-balanced-v14.css`
6. `typography-readability-v16.css`
7. `section-rhythm-v17.css`
8. `auth-premium-v25.css`

JS (kaynak sırası; koşullu include'lar dahil):

1. `app.js`
2. `icon-bridge.js`
3. `auth-premium-v25.js`

### resources/views/layouts/mobile.php

CSS (kaynak sırası; koşullu include'lar dahil):



JS (kaynak sırası; koşullu include'lar dahil):

Layout içinde dış JS yok.

## CSS sınıflandırması

AUTHORITATIVE etiketi ilgili onarılan component sınırını gösterir; tüm dosyanın yeni veya override'sız olduğu iddiası değildir. Diğer aktif legacy dosyalar hâlâ gerekli; körlemesine purge yapılmadı.

| Dosya | Sınıf | Bayt | !important | Kaynak referansları |
| --- | --- | ---: | ---: | --- |
| admin-customers-v67.css | LEGACY BUT REQUIRED | 2106 | 0 | layouts/admin.php |
| admin-discounts-v98.css | LEGACY BUT REQUIRED | 4643 | 2 | layouts/admin.php |
| admin-hero-slider-v94.css | LEGACY BUT REQUIRED | 4478 | 2 | layouts/admin.php |
| admin-popup-v97.css | LEGACY BUT REQUIRED | 4690 | 2 | layouts/admin.php |
| admin-showcases-v31.css | LEGACY BUT REQUIRED | 5254 | 0 | layouts/admin.php |
| admin-sidebar-readable-v77.css | LEGACY BUT REQUIRED | 1204 | 11 | layouts/admin.php |
| admin-smm.css | LEGACY BUT REQUIRED | 2795 | 1 | layouts/admin.php |
| admin-v4.css | ACTIVE / AUTHORITATIVE | 9675 | 80 | layouts/admin.php |
| admin.css | ACTIVE / AUTHORITATIVE | 52908 | 25 | layouts/admin.php |
| agency-catalog-v26.css | LEGACY BUT REQUIRED | 12531 | 11 | layouts/app.php |
| agency-mega-premium-v27.css | LEGACY BUT REQUIRED | 10199 | 0 | layouts/app.php |
| agency-navigation-v26.css | ACTIVE / AUTHORITATIVE | 10223 | 3 | layouts/app.php |
| app.css | ACTIVE / AUTHORITATIVE | 107870 | 8 | layouts/admin.php, layouts/app.php, layouts/auth.php |
| approved-v9.css | LEGACY BUT REQUIRED | 51222 | 696 | layouts/app.php |
| auth-brand-logo-v94.css | LEGACY BUT REQUIRED | 971 | 16 | layouts/app.php |
| auth-premium-v25.css | LEGACY BUT REQUIRED | 12201 | 10 | layouts/auth.php |
| blog-editor-v46.css | LEGACY BUT REQUIRED | 13401 | 83 | admin/blog/form.php |
| blog-quality-v44.css | LEGACY BUT REQUIRED | 2792 | 0 | admin/blog/form.php |
| catalog-bridge-v21.css | LEGACY BUT REQUIRED | 1888 | 36 | layouts/app.php |
| catalog-contrast-v87.css | LEGACY BUT REQUIRED | 4384 | 36 | layouts/app.php |
| catalog-visual-parity-v84.css | LEGACY BUT REQUIRED | 12884 | 109 | layouts/app.php |
| category-brand-v15.css | LEGACY BUT REQUIRED | 13792 | 23 | layouts/app.php |
| category-directory-v88.css | LEGACY BUT REQUIRED | 6118 | 73 | layouts/app.php |
| category-pastel-palette-v85.css | LEGACY BUT REQUIRED | 8709 | 102 | layouts/app.php |
| coupon-checkout-v98.css | LEGACY BUT REQUIRED | 4036 | 1 | layouts/app.php |
| featured-category-slider-v43.css | LEGACY BUT REQUIRED | 8671 | 11 | layouts/app.php |
| featured-footer-v18.css | LEGACY BUT REQUIRED | 12889 | 55 | layouts/app.php |
| featured-groups-v24.css | LEGACY BUT REQUIRED | 4776 | 8 | layouts/app.php |
| featured-software-v41.css | LEGACY BUT REQUIRED | 4549 | 1 | layouts/app.php |
| forms-functional-v20.css | LEGACY BUT REQUIRED | 2470 | 32 | layouts/app.php |
| header-reputation-parity-v83.css | LEGACY BUT REQUIRED | 4276 | 1 | layouts/app.php |
| home-blog-covers-v45.css | LEGACY BUT REQUIRED | 2838 | 4 | layouts/app.php |
| home-faq-final-v47.css | LEGACY BUT REQUIRED | 6275 | 26 | layouts/app.php |
| home-footer-motion-v49.css | LEGACY BUT REQUIRED | 14884 | 189 | layouts/app.php |
| home-hero-slider-v94.css | LEGACY BUT REQUIRED | 14433 | 94 | layouts/app.php |
| home-service-groups-v29.css | ACTIVE / AUTHORITATIVE | 9478 | 0 | layouts/app.php |
| home-v6.css | LEGACY BUT REQUIRED | 21708 | 0 | layouts/app.php |
| icon-alignment-v28.css | LEGACY BUT REQUIRED | 3370 | 32 | layouts/app.php |
| inbox-desktop-v70.css | LEGACY BUT REQUIRED | 2952 | 0 | admin/netvera-inbox/index.php |
| marketing-popup-v97.css | ACTIVE / AUTHORITATIVE | 5494 | 2 | layouts/app.php |
| motion-v22.css | LEGACY BUT REQUIRED | 6059 | 40 | layouts/app.php |
| nav-quick-marquee-v56.css | LEGACY BUT REQUIRED | 5002 | 57 | layouts/app.php |
| netvera-account-history-v57.css | LEGACY BUT REQUIRED | 2302 | 0 | layouts/app.php |
| netvera-admin-products.css | LEGACY BUT REQUIRED | 5519 | 3 | layouts/admin.php |
| netvera-article-v42.css | LEGACY BUT REQUIRED | 3654 | 15 | layouts/app.php |
| netvera-dealer-v62.css | LEGACY BUT REQUIRED | 2494 | 2 | layouts/app.php |
| netvera-inquiries.css | LEGACY BUT REQUIRED | 5077 | 3 | layouts/app.php |
| netvera-legacy-premium-v40.css | ACTIVE / AUTHORITATIVE | 13271 | 0 | layouts/app.php |
| netvera-premium-v76.css | LEGACY BUT REQUIRED | 15656 | 68 | layouts/app.php |
| netvera-product-detail-v60.css | ACTIVE / AUTHORITATIVE | 17801 | 59 | layouts/app.php |
| nv-desk.css | LEGACY BUT REQUIRED | 5616 | 0 | layouts/mobile.php |
| package-benefits-v67.css | LEGACY BUT REQUIRED | 2358 | 1 | layouts/app.php |
| paytr-payment-v45.css | LEGACY BUT REQUIRED | 6121 | 0 | frontend/payment-iframe.php |
| platform-inheritance-flat-v80.css | LEGACY BUT REQUIRED | 19795 | 175 | layouts/app.php |
| premium-hover-v55.css | LEGACY BUT REQUIRED | 10214 | 43 | layouts/app.php |
| premium-section-footer-v23.css | LEGACY BUT REQUIRED | 15433 | 266 | layouts/app.php |
| premium-trust-steps-v30.css | LEGACY BUT REQUIRED | 11232 | 4 | layouts/app.php |
| premium.css | LEGACY BUT REQUIRED | 52586 | 140 | layouts/app.php |
| reference-player-v52.css | LEGACY BUT REQUIRED | 5794 | 32 | layouts/app.php |
| reference-portfolio-v51.css | LEGACY BUT REQUIRED | 9650 | 13 | layouts/app.php |
| responsive-balanced-v14.css | LEGACY BUT REQUIRED | 9682 | 116 | layouts/app.php, layouts/auth.php |
| responsive-fluid-v13.css | ACTIVE / AUTHORITATIVE | 19349 | 253 | layouts/app.php, layouts/auth.php |
| responsive-repair-v19.css | LEGACY BUT REQUIRED | 6441 | 96 | layouts/app.php |
| responsive.css | LEGACY BUT REQUIRED | 11400 | 4 | layouts/admin.php, layouts/app.php, layouts/auth.php |
| reviews-v68.css | LEGACY BUT REQUIRED | 2642 | 11 | layouts/app.php |
| section-rhythm-v17.css | LEGACY BUT REQUIRED | 10414 | 151 | layouts/app.php, layouts/auth.php |
| service-category-identity-v82.css | LEGACY BUT REQUIRED | 10031 | 72 | layouts/app.php |
| site-logo-v90.css | LEGACY BUT REQUIRED | 1786 | 18 | layouts/app.php |
| social-platform-identity-v77.css | LEGACY BUT REQUIRED | 21859 | 134 | layouts/app.php |
| software-marketplace-v36.css | LEGACY BUT REQUIRED | 9534 | 0 | layouts/app.php |
| software-navigation-v33.css | LEGACY BUT REQUIRED | 4758 | 0 | layouts/app.php |
| software-showcase-v31.css | LEGACY BUT REQUIRED | 14909 | 1 | layouts/app.php |
| storefront-design-language-v89.css | LEGACY BUT REQUIRED | 14276 | 155 | layouts/app.php |
| storefront-v4.css | LEGACY BUT REQUIRED | 113897 | 501 | layouts/app.php, layouts/auth.php |
| subcategory-neutral-v86.css | LEGACY BUT REQUIRED | 3205 | 34 | layouts/app.php |
| theme-runtime-v71.css | LEGACY BUT REQUIRED | 4569 | 9 | layouts/app.php |
| typography-readability-v16.css | LEGACY BUT REQUIRED | 16563 | 305 | layouts/app.php, layouts/auth.php |
| visual-consistency-v10.css | LEGACY BUT REQUIRED | 13966 | 140 | layouts/app.php |
| visual-demo-v12.css | LEGACY BUT REQUIRED | 13506 | 160 | layouts/app.php |
| visual-fidelity-v11.css | LEGACY BUT REQUIRED | 16510 | 243 | layouts/app.php |

## Kaldırılan/superseded katmanlar

- `home-service-alignment-v45.css`: DUPLICATE / CONFLICTING; v29 içinde birleştirildi.
- `home-expertise-v106.css`: CONFLICTING / superseded; v29 içinde birleştirildi.
- `home-service-cards-v101.css`: DEAD; aktif include yoktu, v29 yerine eski absolute/fixed-height sistemini taşıyordu.
- `home-footer-motion-v49.css` ve `premium-hover-v55.css`: dosyalar gerekli; yalnızca nv29 component override'ları kaldırıldı.
- `responsive-balanced-v14.css`: dosya gerekli; v13 ile çakışan container override'ları kaldırıldı.
- `agency-mega-premium-v27.css` ve `software-navigation-v33.css`: dosyalar gerekli; dropdown yüksekliği v26'ya bırakıldı.

## Router haritası

237 route; 113 GET (43 public, 70 admin). GET listesi sayfa dışında JSON, export, redirect ve oturum işlemlerini de içerir.

| Method | Yol | Controller |
| --- | --- | --- |
| GET | `/` | HomeController@index |
| GET | `/haber-sitesi-scripti` | NetveraScriptController@legacyNews |
| GET | `/temizlik-firmasi-scripti-web-site-yazilimi` | NetveraScriptController@legacyCleaning |
| GET | `/emlak-scripti-hazir-emlak-sitesi-yazilimi` | NetveraScriptController@legacyRealEstate |
| POST | `/netvera/canli-destek/gonder` | NetveraInquiryController@send |
| GET | `/netvera/canli-destek/mesajlar` | NetveraInquiryController@messages |
| GET | `/hazir-scriptler` | NetveraScriptController@index |
| GET | `/hazir-scriptler/kategori/{slug}` | NetveraScriptController@categoryPage |
| GET | `/hazir-scriptler/tur/{slug}` | NetveraScriptController@typePage |
| GET | `/hazir-scriptler/{mainSlug}/{subSlug}` | NetveraScriptController@subCategoryPage |
| GET | `/hazir-scriptler/{slug}` | NetveraScriptController@detail |
| GET | `/hazir-yazilimlar` | SoftwareController@index |
| GET | `/hazir-yazilimlar/tur/{slug}` | SoftwareController@typePage |
| GET | `/kategoriler` | CategoryController@index |
| GET | `/kategoriler/grup/{slug}` | CategoryController@group |
| GET | `/kategori/{slug}` | CategoryController@show |
| GET | `/paket/{slug}` | PackageController@show |
| GET | `/sepet` | CartController@index |
| POST | `/sepet/ekle` | CartController@add |
| POST | `/sepet/guncelle` | CartController@update |
| POST | `/sepet/sil` | CartController@remove |
| POST | `/sepet/kupon/uygula` | CartController@applyCoupon |
| POST | `/sepet/kupon/kaldir` | CartController@removeCoupon |
| GET | `/odeme` | CheckoutController@index |
| GET | `/odeme/paytr-onizleme` | CheckoutController@paytrPreview |
| POST | `/odeme/islem` | CheckoutController@process |
| GET | `/odeme/basarili` | CheckoutController@success |
| GET | `/odeme/basarisiz` | CheckoutController@fail |
| POST | `/havale-bildirimi` | CheckoutController@bankNotify |
| POST | `/payment/paytr/callback` | CheckoutController@paytrCallback |
| POST | `/payment/iyzico/callback` | CheckoutController@iyzicoCallback |
| GET | `/giris` | AuthController@login |
| POST | `/giris` | AuthController@loginPost |
| GET | `/kayit` | AuthController@register |
| POST | `/kayit` | AuthController@registerPost |
| GET | `/cikis` | AuthController@logout |
| GET | `/sifremi-unuttum` | AuthController@forgot |
| POST | `/sifremi-unuttum` | AuthController@forgotPost |
| GET | `/sifre-sifirla/{token}` | AuthController@reset |
| POST | `/sifre-sifirla` | AuthController@resetPost |
| GET | `/hesabim` | AccountController@index |
| POST | `/hesabim/guncelle` | AccountController@update |
| POST | `/hesabim/sifre-degistir` | AccountController@changePassword |
| GET | `/siparislerim` | AccountController@orders |
| GET | `/bayilik` | DealerController@index |
| POST | `/bayilik/basvur` | DealerController@apply |
| GET | `/bayi/{code}` | DealerController@referral |
| GET | `/siparis/{id}` | AccountController@orderDetail |
| GET | `/destek` | SupportController@index |
| GET | `/destek/yeni` | SupportController@create |
| POST | `/destek/olustur` | SupportController@store |
| GET | `/destek/{id}` | SupportController@show |
| POST | `/destek/{id}/yanit` | SupportController@reply |
| GET | `/blog` | BlogController@index |
| GET | `/blog/kategori/{slug}` | BlogController@category |
| GET | `/blog/etiket/{slug}` | BlogController@tag |
| GET | `/blog/{slug}` | BlogController@show |
| GET | `/sss` | PageController@faq |
| GET | `/iletisim` | PageController@contact |
| POST | `/iletisim` | PageController@contactPost |
| POST | `/bulten/kayit` | NewsletterController@subscribe |
| GET | `/sayfa/{slug}` | PageController@show |
| GET | `/sitemap.xml` | SitemapController@index |
| GET | `/robots.txt` | SitemapController@robots |
| GET | `/admin/giris` | Admin\\AuthController@login |
| POST | `/admin/giris` | Admin\\AuthController@loginPost |
| GET | `/admin/cikis` | Admin\\AuthController@logout |
| GET | `/admin` | Admin\\DashboardController@index |
| GET | `/admin/kategoriler` | Admin\\CategoryController@index |
| POST | `/admin/kategoriler/seo-optimize` | Admin\\CategoryController@optimizeCategorySeo |
| POST | `/admin/kategoriler/seo-oneri` | Admin\\CategoryController@seoSuggestion |
| POST | `/admin/kategoriler/toplu-islem` | Admin\\CategoryController@bulkAction |
| POST | `/admin/kategoriler/varsayilan-kur` | Admin\\CategoryController@seedDefault |
| GET | `/admin/kategoriler/export` | Admin\\CategoryController@exportCsv |
| GET | `/admin/kategori/ekle` | Admin\\CategoryController@create |
| POST | `/admin/kategori/kaydet` | Admin\\CategoryController@store |
| GET | `/admin/kategori/{id}/duzenle` | Admin\\CategoryController@edit |
| POST | `/admin/kategori/{id}/guncelle` | Admin\\CategoryController@update |
| POST | `/admin/kategori/{id}/sil` | Admin\\CategoryController@delete |
| POST | `/admin/kategori/{id}/durum` | Admin\\CategoryController@toggleStatus |
| GET | `/admin/paketler` | Admin\\PackageController@index |
| POST | `/admin/paketler/toplu-islem` | Admin\\PackageController@bulkAction |
| POST | `/admin/paketler/yayinla` | Admin\\PackageController@publishCatalog |
| GET | `/admin/yazilim/ekle` | Admin\\PackageController@createSoftware |
| POST | `/admin/yazilim/kaydet` | Admin\\PackageController@storeSoftware |
| GET | `/admin/yazilim/{id}/duzenle` | Admin\\PackageController@editSoftware |
| POST | `/admin/yazilim/{id}/guncelle` | Admin\\PackageController@updateSoftware |
| GET | `/admin/paket/ekle` | Admin\\PackageController@create |
| POST | `/admin/paket/kaydet` | Admin\\PackageController@store |
| GET | `/admin/paket/{id}/duzenle` | Admin\\PackageController@edit |
| POST | `/admin/paket/{id}/guncelle` | Admin\\PackageController@update |
| POST | `/admin/paket/{id}/sil` | Admin\\PackageController@delete |
| POST | `/admin/paket/{id}/durum` | Admin\\PackageController@toggleStatus |
| GET | `/admin/paket/{id}/alanlar` | Admin\\PackageController@fields |
| POST | `/admin/paket/{id}/alanlar-kaydet` | Admin\\PackageController@saveFields |
| GET | `/admin/smm` | Admin\\SmmController@index |
| POST | `/admin/smm/kur` | Admin\\SmmController@install |
| POST | `/admin/smm/tedarikci/kaydet` | Admin\\SmmController@saveProvider |
| POST | `/admin/smm/tedarikciler/esitle` | Admin\\SmmController@syncAll |
| POST | `/admin/smm/paket/toplu-ekle` | Admin\\SmmController@bulkPublish |
| POST | `/admin/smm/tedarikci/{id}/esitle` | Admin\\SmmController@sync |
| POST | `/admin/smm/tedarikci/{id}/test` | Admin\\SmmController@balance |
| POST | `/admin/smm/kategori/ekle` | Admin\\SmmController@createCategory |
| POST | `/admin/smm/paket/olustur` | Admin\\SmmController@publish |
| POST | `/admin/smm/paket/{id}/esle` | Admin\\SmmController@mapping |
| POST | `/admin/smm/calistir` | Admin\\SmmController@run |
| POST | `/admin/smm/is/{id}/yenile` | Admin\\SmmController@refill |
| POST | `/admin/smm/is/{id}/iptal` | Admin\\SmmController@cancel |
| POST | `/admin/smm/is/{id}/uzlastir` | Admin\\SmmController@reconcile |
| GET | `/admin/netvera-gelen-kutusu` | Admin\\NetveraInboxController@index |
| POST | `/admin/netvera-gelen-kutusu/kur` | Admin\\NetveraInboxController@install |
| GET | `/admin/netvera-gelen-kutusu/{id}` | Admin\\NetveraInboxController@detail |
| POST | `/admin/netvera-gelen-kutusu/{id}/yanit` | Admin\\NetveraInboxController@reply |
| GET | `/admin/netvera-gelen-kutusu/sohbet/{id}/mesajlar` | Admin\\NetveraInboxController@conversation |
| POST | `/admin/netvera-gelen-kutusu/sohbet/{id}/yanit` | Admin\\NetveraInboxController@conversationReply |
| POST | `/admin/netvera-gelen-kutusu/telegram/kaydet` | Admin\\NetveraInboxController@saveTelegramSettings |
| POST | `/admin/netvera-gelen-kutusu/temsilci/kaydet` | Admin\\NetveraInboxController@saveAgent |
| POST | `/admin/netvera-gelen-kutusu/telegram/test` | Admin\\NetveraInboxController@testTelegram |
| POST | `/admin/netvera-gelen-kutusu/onem/kur` | Admin\\NetveraInboxController@installImportance |
| POST | `/admin/netvera-gelen-kutusu/{id}/onem` | Admin\\NetveraInboxController@importance |
| POST | `/admin/netvera-gelen-kutusu/{id}/durum` | Admin\\NetveraInboxController@state |
| GET | `/admin/netvera-kategoriler` | Admin\\NetveraScriptController@categories |
| POST | `/admin/netvera-kategoriler/kaydet` | Admin\\NetveraScriptController@saveCategory |
| POST | `/admin/netvera-kategoriler/staging-kur` | Admin\\NetveraScriptController@installBridge |
| GET | `/admin/netvera-yazilimlar` | Admin\\NetveraScriptController@index |
| GET | `/admin/netvera-yazilimlar/ekle` | Admin\\NetveraScriptController@create |
| GET | `/admin/netvera-yazilimlar/{id}/duzenle` | Admin\\NetveraScriptController@edit |
| POST | `/admin/netvera-yazilimlar/kaydet` | Admin\\NetveraScriptController@save |
| POST | `/admin/netvera-yazilimlar/demo-aktar` | Admin\\NetveraScriptController@importDemoAccess |
| POST | `/admin/netvera-yazilimlar/galeri/ekle` | Admin\\NetveraScriptController@galleryAdd |
| POST | `/admin/netvera-yazilimlar/galeri/gizle` | Admin\\NetveraScriptController@galleryHide |
| GET | `/admin/hazir-yazilimlar` | Admin\\NetveraScriptController@index |
| POST | `/admin/hazir-yazilimlar/kategorileri-kur` | Admin\\SoftwareShowcaseController@install |
| POST | `/admin/hazir-yazilimlar/kur-ve-ekle` | Admin\\SoftwareShowcaseController@setupThenCreate |
| POST | `/admin/hazir-yazilimlar/kaydet` | Admin\\SoftwareShowcaseController@save |
| GET | `/admin/referanslar` | Admin\\ReferencesController@index |
| POST | `/admin/referanslar/ekle` | Admin\\ReferencesController@store |
| POST | `/admin/referanslar/{id}/guncelle` | Admin\\ReferencesController@update |
| POST | `/admin/referanslar/{id}/sil` | Admin\\ReferencesController@delete |
| GET | `/admin/siparisler` | Admin\\OrderController@index |
| GET | `/admin/siparis/{id}` | Admin\\OrderController@show |
| POST | `/admin/siparis/{id}/durum` | Admin\\OrderController@updateStatus |
| GET | `/admin/havale-bildirimleri` | Admin\\OrderController@bankNotifications |
| POST | `/admin/havale/{id}/onayla` | Admin\\OrderController@confirmBankTransfer |
| POST | `/admin/havale/{id}/reddet` | Admin\\OrderController@rejectBankTransfer |
| GET | `/admin/yorumlar` | Admin\\TestimonialController@index |
| POST | `/admin/yorumlar/kaydet` | Admin\\TestimonialController@save |
| POST | `/admin/yorumlar/sil` | Admin\\TestimonialController@remove |
| GET | `/admin/cep` | Admin\\MobileDeskController@index |
| GET | `/admin/cep/veri` | Admin\\MobileDeskController@feed |
| GET | `/admin/cep/sinyal` | Admin\\MobileDeskController@signal |
| GET | `/admin/cep/kayit/{type}/{id}` | Admin\\MobileDeskController@detail |
| POST | `/admin/cep/yanit/{type}/{id}` | Admin\\MobileDeskController@reply |
| GET | `/admin/odeme-modulleri` | Admin\\PaymentGatewayController@index |
| POST | `/admin/odeme-modulleri/{id}/toggle` | Admin\\PaymentGatewayController@toggle |
| POST | `/admin/odeme-modulleri/{id}/varsayilan` | Admin\\PaymentGatewayController@setDefault |
| GET | `/admin/paytr-ayarlari` | Admin\\PaymentGatewayController@paytrSettings |
| POST | `/admin/paytr-ayarlari/kaydet` | Admin\\PaymentGatewayController@savePaytrSettings |
| GET | `/admin/iyzico-ayarlari` | Admin\\PaymentGatewayController@iyzicoSettings |
| POST | `/admin/iyzico-ayarlari/kaydet` | Admin\\PaymentGatewayController@saveIyzicoSettings |
| GET | `/admin/blog` | Admin\\BlogController@index |
| GET | `/admin/blog/ekle` | Admin\\BlogController@create |
| POST | `/admin/blog/kaydet` | Admin\\BlogController@store |
| GET | `/admin/blog/{id}/duzenle` | Admin\\BlogController@edit |
| POST | `/admin/blog/{id}/guncelle` | Admin\\BlogController@update |
| POST | `/admin/blog/{id}/sil` | Admin\\BlogController@delete |
| POST | `/admin/blog/preview` | Admin\\BlogController@preview |
| POST | `/admin/blog/upload-image` | Admin\\BlogController@uploadImage |
| GET | `/admin/blog-kategorileri` | Admin\\BlogController@categories |
| POST | `/admin/blog-kategorisi/kaydet` | Admin\\BlogController@storeCategory |
| POST | `/admin/blog-kategorisi/{id}/sil` | Admin\\BlogController@deleteCategory |
| GET | `/admin/menu` | Admin\\NavigationController@index |
| POST | `/admin/menu/kaydet` | Admin\\NavigationController@save |
| POST | `/admin/menu/sifirla` | Admin\\NavigationController@reset |
| GET | `/admin/site-ayarlari` | Admin\\SettingsController@site |
| POST | `/admin/site-ayarlari/kaydet` | Admin\\SettingsController@saveSite |
| GET | `/admin/smtp-ayarlari` | Admin\\SettingsController@smtp |
| POST | `/admin/smtp-ayarlari/kaydet` | Admin\\SettingsController@saveSmtp |
| GET | `/admin/banka-hesaplari` | Admin\\SettingsController@bankAccounts |
| POST | `/admin/banka-hesabi/kaydet` | Admin\\SettingsController@storeBankAccount |
| POST | `/admin/banka-hesabi/{id}/sil` | Admin\\SettingsController@deleteBankAccount |
| GET | `/admin/netvera-musteriler` | Admin\\NetveraCustomersController@index |
| GET | `/admin/bayilik` | Admin\\DealerController@index |
| POST | `/admin/bayilik/kur` | Admin\\DealerController@install |
| POST | `/admin/bayilik/{id}/karar` | Admin\\DealerController@review |
| GET | `/admin/uyeler` | Admin\\SettingsController@users |
| GET | `/admin/admin-kullanicilari` | Admin\\SettingsController@adminUsers |
| GET | `/admin/sayfalar` | Admin\\SettingsController@pages |
| GET | `/admin/sayfa/{id}/duzenle` | Admin\\SettingsController@editPage |
| POST | `/admin/sayfa/{id}/guncelle` | Admin\\SettingsController@updatePage |
| GET | `/admin/sss` | Admin\\SettingsController@faqs |
| POST | `/admin/sss/kaydet` | Admin\\SettingsController@storeFaq |
| POST | `/admin/sss/{id}/sil` | Admin\\SettingsController@deleteFaq |
| GET | `/admin/loglar` | Admin\\SettingsController@logs |
| GET | `/admin/import` | Admin\\SettingsController@importPage |
| POST | `/admin/import/islem` | Admin\\SettingsController@importProcess |
| GET | `/admin/import/bos-sablon` | Admin\\SettingsController@downloadBlankTemplate |
| GET | `/admin/import/ornek-sablon` | Admin\\SettingsController@downloadExampleTemplate |
| GET | `/admin/mesajlar` | Admin\\MessageController@index |
| POST | `/admin/mesaj/{id}/okundu` | Admin\\MessageController@markRead |
| POST | `/admin/bulten/{id}/iptal` | Admin\\MessageController@unsubscribe |
| GET | `/admin/destek` | Admin\\SettingsController@supportIndex |
| GET | `/admin/destek/{id}` | Admin\\SettingsController@supportShow |
| POST | `/admin/destek/{id}/yanit` | Admin\\SettingsController@supportReply |
| GET | `/admin/odemeler` | Admin\\SettingsController@payments |
| GET | `/admin/seo` | Admin\\SettingsController@seoCenter |
| GET | `/admin/ana-sayfa` | Admin\\HomeSectionController@index |
| GET | `/admin/ana-sayfa/{id}/duzenle` | Admin\\HomeSectionController@edit |
| POST | `/admin/ana-sayfa/{id}/guncelle` | Admin\\HomeSectionController@update |
| POST | `/admin/ana-sayfa/{id}/durum` | Admin\\HomeSectionController@toggleStatus |
| GET | `/admin/sliderlar` | Admin\\HeroSliderController@index |
| GET | `/admin/sliderlar/yeni` | Admin\\HeroSliderController@create |
| POST | `/admin/sliderlar/ekle` | Admin\\HeroSliderController@store |
| GET | `/admin/sliderlar/{id}/duzenle` | Admin\\HeroSliderController@edit |
| POST | `/admin/sliderlar/{id}/guncelle` | Admin\\HeroSliderController@update |
| POST | `/admin/sliderlar/{id}/durum` | Admin\\HeroSliderController@toggle |
| POST | `/admin/sliderlar/{id}/kopyala` | Admin\\HeroSliderController@duplicate |
| POST | `/admin/sliderlar/{id}/sil` | Admin\\HeroSliderController@delete |
| GET | `/admin/popuplar` | Admin\\MarketingPopupController@index |
| GET | `/admin/popuplar/yeni` | Admin\\MarketingPopupController@create |
| POST | `/admin/popuplar/ekle` | Admin\\MarketingPopupController@store |
| GET | `/admin/popuplar/{id}/duzenle` | Admin\\MarketingPopupController@edit |
| POST | `/admin/popuplar/{id}/guncelle` | Admin\\MarketingPopupController@update |
| POST | `/admin/popuplar/{id}/durum` | Admin\\MarketingPopupController@toggle |
| POST | `/admin/popuplar/{id}/kopyala` | Admin\\MarketingPopupController@duplicate |
| POST | `/admin/popuplar/{id}/sil` | Admin\\MarketingPopupController@delete |
| GET | `/admin/kuponlar` | Admin\\DiscountController@index |
| GET | `/admin/kuponlar/yeni` | Admin\\DiscountController@create |
| GET | `/admin/kuponlar/{id}/duzenle` | Admin\\DiscountController@edit |
| POST | `/admin/kuponlar/kaydet` | Admin\\DiscountController@save |
| POST | `/admin/kuponlar/{id}/durum` | Admin\\DiscountController@toggle |
| POST | `/admin/kuponlar/{id}/sil` | Admin\\DiscountController@delete |
| GET | `/admin/lisans` | Admin\\LicenseController@index |
| POST | `/admin/lisans/aktifle` | Admin\\LicenseController@activate |
| POST | `/admin/lisans/kontrol` | Admin\\LicenseController@check |
| POST | `/admin/lisans/devre-disi` | Admin\\LicenseController@deactivate |
| POST | `/admin/lisans/kaydet` | Admin\\LicenseController@saveSettings |

## JS bağımlılıkları

- `public/assets/js/admin-references.js`: resources/views/admin/references/index.php
- `public/assets/js/admin.js`: resources/views/layouts/admin.php
- `public/assets/js/agency-navigation-v26.js`: resources/views/layouts/app.php
- `public/assets/js/app.js`: resources/views/layouts/app.php, resources/views/layouts/auth.php
- `public/assets/js/auth-premium-v25.js`: resources/views/layouts/auth.php
- `public/assets/js/blog-quality-v44.js`: resources/views/admin/blog/form.php
- `public/assets/js/category-marquee-drag-v56.js`: resources/views/layouts/app.php
- `public/assets/js/home-featured-tabs-v18.js`: resources/views/layouts/app.php
- `public/assets/js/home-hero-slider-v94.js`: resources/views/layouts/app.php
- `public/assets/js/icon-bridge.js`: resources/views/layouts/app.php, resources/views/layouts/auth.php
- `public/assets/js/inbox-desktop-v70.js`: resources/views/admin/netvera-inbox/index.php
- `public/assets/js/marketing-popup-v97.js`: resources/views/layouts/app.php
- `public/assets/js/netvera-product-tabs-v60.js`: resources/views/layouts/app.php
- `public/assets/js/nv-desk-admin.js`: resources/views/layouts/admin.php
- `public/assets/js/nv-desk-alerts.js`: resources/views/layouts/admin.php, resources/views/layouts/app.php, resources/views/layouts/mobile.php
- `public/assets/js/nv-desk.js`: resources/views/layouts/mobile.php
- `public/assets/js/package-benefits-v67.js`: resources/views/layouts/app.php
- `public/assets/js/reviews-v68.js`: resources/views/layouts/app.php
- `public/assets/js/software-marketplace-v36.js`: resources/views/layouts/app.php

## Template / partial envanteri

- `resources/views/admin/blog/categories.php`
- `resources/views/admin/blog/form.php`
- `resources/views/admin/blog/index.php`
- `resources/views/admin/categories/form.php`
- `resources/views/admin/categories/index.php`
- `resources/views/admin/dashboard.php`
- `resources/views/admin/dealers/index.php`
- `resources/views/admin/discounts/form.php`
- `resources/views/admin/discounts/index.php`
- `resources/views/admin/hero-slider/form.php`
- `resources/views/admin/hero-slider/index.php`
- `resources/views/admin/home-sections/form.php`
- `resources/views/admin/home-sections/index.php`
- `resources/views/admin/import/index.php`
- `resources/views/admin/login.php`
- `resources/views/admin/messages/index.php`
- `resources/views/admin/mobile/index.php`
- `resources/views/admin/navigation/index.php`
- `resources/views/admin/netvera-customers/index.php`
- `resources/views/admin/netvera-inbox/detail.php`
- `resources/views/admin/netvera-inbox/index.php`
- `resources/views/admin/netvera-scripts/categories.php`
- `resources/views/admin/netvera-scripts/form.php`
- `resources/views/admin/netvera-scripts/index.php`
- `resources/views/admin/orders/bank-notifications.php`
- `resources/views/admin/orders/detail.php`
- `resources/views/admin/orders/index.php`
- `resources/views/admin/orders/payments.php`
- `resources/views/admin/packages/fields.php`
- `resources/views/admin/packages/form.php`
- `resources/views/admin/packages/index.php`
- `resources/views/admin/partials/netvera-seo.php`
- `resources/views/admin/payment-gateways/index.php`
- `resources/views/admin/payment-gateways/iyzico.php`
- `resources/views/admin/payment-gateways/paytr.php`
- `resources/views/admin/popups/form.php`
- `resources/views/admin/popups/index.php`
- `resources/views/admin/references/fields.php`
- `resources/views/admin/references/index.php`
- `resources/views/admin/settings/admin-users.php`
- `resources/views/admin/settings/bank-accounts.php`
- `resources/views/admin/settings/faqs.php`
- `resources/views/admin/settings/license.php`
- `resources/views/admin/settings/logs.php`
- `resources/views/admin/settings/page-form.php`
- `resources/views/admin/settings/pages.php`
- `resources/views/admin/settings/seo.php`
- `resources/views/admin/settings/site.php`
- `resources/views/admin/settings/smtp.php`
- `resources/views/admin/settings/users.php`
- `resources/views/admin/smm/index.php`
- `resources/views/admin/software-showcase/index.php`
- `resources/views/admin/software-showcase/setup.php`
- `resources/views/admin/support/detail.php`
- `resources/views/admin/support/index.php`
- `resources/views/admin/testimonials/index.php`
- `resources/views/frontend/404.php`
- `resources/views/frontend/account/dashboard.php`
- `resources/views/frontend/account/dealer.php`
- `resources/views/frontend/account/order-detail.php`
- `resources/views/frontend/account/orders.php`
- `resources/views/frontend/account/partials/netvera-legacy-history.php`
- `resources/views/frontend/account/partials/netvera-owned-software.php`
- `resources/views/frontend/auth/forgot.php`
- `resources/views/frontend/auth/login.php`
- `resources/views/frontend/auth/register.php`
- `resources/views/frontend/auth/reset.php`
- `resources/views/frontend/blog/category.php`
- `resources/views/frontend/blog/detail.php`
- `resources/views/frontend/blog/index.php`
- `resources/views/frontend/cart.php`
- `resources/views/frontend/categories.php`
- `resources/views/frontend/category-detail.php`
- `resources/views/frontend/checkout.php`
- `resources/views/frontend/contact.php`
- `resources/views/frontend/faq.php`
- `resources/views/frontend/home.php`
- `resources/views/frontend/license-block.php`
- `resources/views/frontend/netvera-script-detail.php`
- `resources/views/frontend/netvera-scripts.php`
- `resources/views/frontend/package-detail.php`
- `resources/views/frontend/page.php`
- `resources/views/frontend/partials/marketing-popup.php`
- `resources/views/frontend/partials/netvera-live-chat.php`
- `resources/views/frontend/partials/user-sidebar.php`
- `resources/views/frontend/payment-fail.php`
- `resources/views/frontend/payment-iframe.php`
- `resources/views/frontend/payment-success.php`
- `resources/views/frontend/software.php`
- `resources/views/frontend/support/create.php`
- `resources/views/frontend/support/detail.php`
- `resources/views/frontend/support/index.php`
- `resources/views/layouts/admin.php`
- `resources/views/layouts/app.php`
- `resources/views/layouts/auth.php`
- `resources/views/layouts/mobile.php`

