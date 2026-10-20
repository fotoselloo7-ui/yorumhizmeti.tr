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
        ];
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

    public static function eligiblePackages(): array
    {
        $root = self::root();
        if (!$root) return [];
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    c.status AS category_status
             FROM packages p
             INNER JOIN categories c ON p.category_id = c.id
             WHERE c.id = ? OR c.parent_id = ?
             ORDER BY c.sort_order ASC, p.sort_order ASC, p.id ASC",
            [(int)$root['id'], (int)$root['id']]
        );
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
        $eligible = array_column(self::eligiblePackages(), 'id');
        $allowed = array_fill_keys(array_map('intval', $eligible), true);
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

    public static function featured(): array
    {
        $prefs = self::preferences();
        if (!$prefs['enabled'] || !$prefs['ids']) return [];
        $eligible = [];
        foreach (self::eligiblePackages() as $pkg) {
            if ($pkg['status'] !== 'active' || $pkg['category_status'] !== 'active') continue;
            $root = self::root();
            if (($root['status'] ?? 'inactive') !== 'active') continue;
            $eligible[(int)$pkg['id']] = $pkg;
        }
        $out = [];
        foreach ($prefs['ids'] as $id) {
            if (isset($eligible[$id])) $out[] = $eligible[$id];
            if (count($out) >= 12) break;
        }
        return $out;
    }
}
