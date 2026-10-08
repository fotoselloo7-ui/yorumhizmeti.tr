<?php
namespace App\Services;

use App\Core\Database;

/**
 * Ready-made software taxonomy and its independently ordered home showcase.
 * Existing products, payment flow, ratings and is_featured remain untouched.
 */
final class SoftwareCatalogService
{
    public const ROOT_SLUG = 'hazir-yazilim-scriptleri';
    private const KEY = 'software_showcase_v1';

    public static function definitions(): array
    {
        // SEO-friendly industry and product-based subcategories; no fake products.
        return [
            ['Haber Sitesi Yazılımı', 'haber-sitesi-scripti', 'Haber portalı, editoryal CMS ve haber otomasyonu yazılımı.', 'file-text'],
            ['Blog ve İçerik CMS', 'blog-cms-scripti', 'Blog, dergi ve içerik yönetim sistemi yazılımları.', 'content-create'],
            ['Kurumsal Web Sitesi Scripti', 'kurumsal-site-scripti', 'Firma web sitesi ve kurumsal vitrin yazılımları.', 'monitor'],
            ['Emlak Sitesi Yazılımı', 'emlak-sitesi-scripti', 'Emlak portföyü, ilan ve danışman yönetimi yazılımı.', 'home'],
            ['Oto Galeri Yazılımı', 'oto-galeri-scripti', 'Araç ilan, stok ve otomobil galeri sitesi yazılımı.', 'store'],
            ['E-Ticaret Sitesi Yazılımı', 'e-ticaret-scripti', 'Online mağaza, ürün ve sipariş yönetimi yazılımı.', 'shopping-cart'],
            ['Pazaryeri ve İlan Scripti', 'pazaryeri-ilan-scripti', 'Çok satıcılı pazar yeri ve ilan portalı yazılımları.', 'grid'],
            ['Otel ve Rezervasyon Scripti', 'otel-rezervasyon-scripti', 'Otel tanıtımı, oda yönetimi ve rezervasyon yazılımları.', 'calendar'],
            ['Randevu ve Rezervasyon Yazılımı', 'randevu-rezervasyon-scripti', 'Hizmet takvimi, randevu ve online rezervasyon yazılımları.', 'clock'],
            ['Restoran ve QR Menü Scripti', 'restoran-qr-menu-scripti', 'Restoran web sitesi, QR menü ve sipariş yazılımları.', 'store'],
            ['Güzellik Salonu Yazılımı', 'guzellik-salonu-scripti', 'Güzellik salonu, spa ve kuaför CRM/randevu yazılımları.', 'heart'],
            ['Temizlik Firması Yazılımı', 'temizlik-firmasi-scripti', 'Temizlik firması tanıtım ve hizmet talep yazılımları.', 'shield-check'],
            ['Nakliye ve Lojistik Scripti', 'nakliye-lojistik-scripti', 'Nakliye, taşıma teklifi ve lojistik firması yazılımları.', 'truck'],
            ['İnşaat ve Hafriyat Scripti', 'insaat-hafriyat-scripti', 'İnşaat, müteahhitlik ve hafriyat kurumsal web yazılımları.', 'home'],
            ['CRM ve Müşteri Yönetimi', 'crm-musteri-yonetimi-scripti', 'Müşteri, teklif, satış ve iş takibi CRM sistemleri.', 'users'],
            ['SMM Panel Yazılımı', 'smm-panel-scripti', 'Sosyal medya hizmet paneli ve sipariş yönetimi yazılımı.', 'layers'],
            ['SMS Onay Yazılımı', 'sms-onay-scripti', 'SMS doğrulama, onay siparişi ve entegrasyon yazılımları.', 'mobile-app'],
            ['Eğitim ve Kurs Yazılımı', 'egitim-kurs-scripti', 'Online eğitim, kurs ve öğrenci yönetimi yazılımları.', 'file-text'],
            ['Forum ve Topluluk Yazılımı', 'forum-topluluk-scripti', 'Forum, topluluk ve tartışma portalı yazılımları.', 'message-circle'],
            ['İş İlanı ve Kariyer Portalı', 'is-ilani-kariyer-scripti', 'İş ilanı, başvuru ve kariyer platformu yazılımları.', 'users'],
            ['Tur ve Seyahat Yazılımı', 'tur-seyahat-scripti', 'Tur listesi, seyahat acentesi ve rezervasyon yazılımları.', 'globe'],
            ['Dijital Ürün Satış Scripti', 'dijital-urun-satis-scripti', 'Lisans, yazılım, dijital dosya ve ürün satış platformları.', 'shopping-bag'],
            ['Servis ve Teknik Destek Scripti', 'teknik-servis-scripti', 'Teknik servis talebi, arıza kaydı ve servis CRM yazılımı.', 'settings'],
            ['WordPress Temaları', 'wordpress-temalari', 'WordPress kurumsal, blog, haber, portföy ve e-ticaret temaları.', 'monitor'],
            ['WordPress Eklentileri', 'wordpress-eklentileri', 'WordPress SEO, performans, güvenlik, form ve yönetim eklentileri.', 'settings'],
            ['WooCommerce Eklentileri', 'woocommerce-eklentileri', 'WooCommerce ödeme, kargo, sipariş ve mağaza entegrasyonları.', 'shopping-cart'],
            ['Shopify Temaları', 'shopify-temalari', 'Shopify mağazaları için mobil uyumlu vitrin ve tema çözümleri.', 'store'],
            ['Shopify Uygulama ve Entegrasyonları', 'shopify-uygulamalari', 'Shopify mağaza otomasyonları ve üçüncü taraf entegrasyonları.', 'layers'],
            ['HTML & Tailwind Web Şablonları', 'html-tailwind-sablonlari', 'Modern, mobil uyumlu statik HTML ve Tailwind şablonları.', 'code'],
            ['Laravel Yönetim Paneli Şablonları', 'laravel-admin-panel-sablonlari', 'Laravel ve PHP yönetim paneli şablonları ve modülleri.', 'monitor'],
            ['SaaS & Abonelik Yazılımları', 'saas-abonelik-scripti', 'Abonelik ve lisans yönetimli bulut yazılımları.', 'cloud'],
            ['Masaüstü Bot ve Otomasyon', 'masaustu-bot-otomasyon', 'Windows ve masaüstü otomasyon, veri işleme ve iş akışı yazılımları.', 'settings'],
            ['API ve Entegrasyon Modülleri', 'api-entegrasyon-modulleri', 'Ödeme, kargo, mesajlaşma ve üçüncü taraf API entegrasyonları.', 'code'],
            ['Mobil Uygulama Şablonları', 'mobil-uygulama-sablonlari', 'Flutter, iOS ve Android uygulama arayüz ve başlangıç kitleri.', 'mobile-app'],
        ];
    }

    /**
     * Stable editorial product taxonomy. Labels describe product types rather
     * than suggesting that unavailable digital goods are on sale.
     */
    public static function taxonomyGroups(): array
    {
        return [
            'Sektörel & Kurumsal Yazılımlar' => [
                'haber-sitesi-scripti', 'blog-cms-scripti', 'kurumsal-site-scripti',
                'emlak-sitesi-scripti', 'oto-galeri-scripti', 'otel-rezervasyon-scripti',
                'restoran-qr-menu-scripti', 'guzellik-salonu-scripti',
                'temizlik-firmasi-scripti', 'nakliye-lojistik-scripti',
                'insaat-hafriyat-scripti', 'tur-seyahat-scripti',
            ],
            'E-Ticaret & Platformlar' => [
                'e-ticaret-scripti', 'pazaryeri-ilan-scripti', 'dijital-urun-satis-scripti',
                'randevu-rezervasyon-scripti', 'is-ilani-kariyer-scripti',
            ],
            'WordPress & Eklentiler' => [
                'wordpress-temalari', 'wordpress-eklentileri', 'woocommerce-eklentileri',
            ],
            'Shopify & Web Şablonları' => [
                'shopify-temalari', 'shopify-uygulamalari', 'html-tailwind-sablonlari',
                'laravel-admin-panel-sablonlari',
            ],
            'Otomasyon & Yönetim' => [
                'crm-musteri-yonetimi-scripti', 'smm-panel-scripti', 'sms-onay-scripti',
                'teknik-servis-scripti', 'masaustu-bot-otomasyon', 'saas-abonelik-scripti',
                'api-entegrasyon-modulleri',
            ],
            'Eğitim, Mobil & Topluluk' => [
                'egitim-kurs-scripti', 'forum-topluluk-scripti', 'mobil-uygulama-sablonlari',
            ],
        ];
    }

    public static function softwareCategoryOptions(): array
    {
        $root = self::root();
        if (!$root || $root['status'] !== 'active') return [];
        return Database::getInstance()->fetchAll(
            "SELECT id, name, slug, parent_id
             FROM categories
             WHERE status = 'active' AND (id = ? OR parent_id = ?)
             ORDER BY CASE WHEN parent_id IS NULL THEN 1 ELSE 0 END,
                      sort_order ASC, name ASC",
            [(int)$root['id'], (int)$root['id']]
        );
    }

    public static function isSoftwareCategory(int $id): bool
    {
        foreach (self::softwareCategoryOptions() as $row) {
            if ((int)$row['id'] === $id) return true;
        }
        return false;
    }

    public static function root(): ?array
    {
        return Database::getInstance()->fetch("SELECT * FROM categories WHERE slug = ? LIMIT 1", [self::ROOT_SLUG]);
    }

    public static function installCategories(): array
    {
        $db = Database::getInstance();
        $pdo = $db->getPdo();
        $created = 0; $existing = 0; $conflicts = 0;
        $pdo->beginTransaction();
        try {
            $root = self::root();
            if (!$root) {
                $id = $db->insert('categories', [
                    'parent_id' => null, 'name' => 'Hazır Yazılımlar & Scriptler',
                    'slug' => self::ROOT_SLUG,
                    'description' => 'Sektöre özel hazır web sitesi yazılımları, yönetim panelli PHP scriptleri ve dijital platform çözümleri.',
                    'icon_key' => 'monitor', 'sort_order' => 85, 'status' => 'active',
                    'seo_title' => 'Hazır Yazılım ve Web Sitesi Scriptleri | Yorum Hizmeti',
                    'seo_description' => 'Haber, emlak, e-ticaret, blog, otel, rezervasyon ve kurumsal sektörler için hazır yazılım scriptlerini inceleyin.',
                ]);
                $root = ['id' => $id];
                $created++;
            }
            $rootId = (int)$root['id'];
            foreach (self::definitions() as $index => [$name, $slug, $description, $icon]) {
                $match = $db->fetch("SELECT id, parent_id FROM categories WHERE slug = ? LIMIT 1", [$slug]);
                if ($match) {
                    if ((int)$match['parent_id'] === $rootId) $existing++;
                    else $conflicts++;
                    continue;
                }
                $db->insert('categories', [
                    'parent_id' => $rootId, 'name' => $name, 'slug' => $slug,
                    'description' => $description, 'icon_key' => $icon,
                    'sort_order' => ($index+1)*10, 'status' => 'active',
                    'seo_title' => $name . ' | Hazır Yazılımlar',
                    'seo_description' => $description,
                ]);
                $created++;
            }
            $pdo->commit();
            return compact('created', 'existing', 'conflicts');
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Existing software products may live in older categories (e.g. a demo script
     * under Web Site Hizmetleri). Keep them discoverable until an admin moves
     * them to the new dedicated categories; never move or mutate product data.
     */
    private static function allPackages(): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    c.status AS category_status, c.parent_id AS category_parent_id,
                    parent.status AS parent_status
             FROM packages p
             INNER JOIN categories c ON c.id = p.category_id
             LEFT JOIN categories parent ON parent.id = c.parent_id
             ORDER BY p.sort_order ASC, p.id DESC"
        );
    }

    public static function eligiblePackages(): array
    {
        $rootId = (int)(self::root()['id'] ?? 0);
        $selected = array_fill_keys(self::preferences()['ids'], true);
        $results = [];
        foreach (self::allPackages() as $pkg) {
            $inSoftwareFamily = $rootId > 0
                && ((int)$pkg['category_id'] === $rootId
                    || (int)$pkg['category_parent_id'] === $rootId);
            $label = mb_strtolower(
                (string)$pkg['name'] . ' ' . (string)$pkg['slug'] . ' ' .
                (string)$pkg['category_name'] . ' ' . (string)$pkg['category_slug'],
                'UTF-8'
            );
            // Regex is keyword discovery only. Admin may explicitly mark other
            // products as software without editing or migrating categories.
            $isExistingSoftware = preg_match('/script|yazılım|yazilim|software|cms/u', $label) === 1;
            if ($inSoftwareFamily || $isExistingSoftware || isset($selected[(int)$pkg['id']])) {
                $results[] = $pkg;
            }
        }
        return $results;
    }

    public static function otherPackages(): array
    {
        $recognized = array_fill_keys(array_map('intval', array_column(self::eligiblePackages(), 'id')), true);
        return array_values(array_filter(self::allPackages(), static fn($p) =>
            !isset($recognized[(int)$p['id']])));
    }

    public static function publicPackages(): array
    {
        $list = [];
        foreach (self::eligiblePackages() as $pkg) {
            if (($pkg['status'] ?? '') !== 'active' || ($pkg['category_status'] ?? '') !== 'active') continue;
            if ($pkg['category_parent_id'] !== null && ($pkg['parent_status'] ?? '') !== 'active') continue;
            $list[] = $pkg;
        }
        return $list;
    }

    public static function preferences(): array
    {
        $saved = json_decode(setting(self::KEY, ''), true);
        $ids = [];
        if (is_array($saved['ids'] ?? null)) {
            foreach ($saved['ids'] as $id) {
                $id = (int)$id;
                if ($id > 0 && !in_array($id, $ids, true)) $ids[] = $id;
            }
        }
        return ['enabled' => (bool)($saved['enabled'] ?? true), 'ids' => $ids];
    }

    public static function save(array $ids, bool $enabled): void
    {
        $all = Database::getInstance()->fetchAll("SELECT id FROM packages");
        $allowed = array_fill_keys(array_map('intval', array_column($all, 'id')), true);
        $selected = [];
        foreach ($ids as $id) {
            $id = (int)$id;
            if ($id > 0 && isset($allowed[$id]) && !in_array($id, $selected, true)) {
                $selected[] = $id;
            }
        }
        SiteConfigService::getInstance()->set(
            self::KEY, json_encode(['enabled'=>$enabled,'ids'=>$selected], JSON_UNESCAPED_UNICODE), 'homepage'
        );
    }

    /**
     * Homepage defaults to active software products, ordered by package sort.
     * Explicit admin selections override the automatic order. Disabling the
     * showcase still works and is different from an empty selection.
     */
    public static function featured(): array
    {
        $prefs = self::preferences();
        if (!$prefs['enabled']) return [];
        $eligible = [];
        foreach (self::publicPackages() as $pkg) $eligible[(int)$pkg['id']] = $pkg;
        if (!$prefs['ids']) return array_slice(array_values($eligible), 0, 12);

        $out = [];
        foreach ($prefs['ids'] as $id) {
            if (isset($eligible[$id])) $out[] = $eligible[$id];
            if (count($out) >= 12) break;
        }
        return $out;
    }
}
