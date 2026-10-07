<?php
namespace App\Services;

use App\Core\Database;

class ExcelImportService
{
    public function importCsv(string $filePath, array $options = []): array
    {
        $db = Database::getInstance();
        $results = ['success' => 0, 'skipped' => 0, 'errors' => [], 'created_categories' => 0];

        if (!file_exists($filePath)) {
            $results['errors'][] = ['row' => '-', 'name' => '-', 'error' => 'Dosya bulunamadı.'];
            return $results;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $results['errors'][] = ['row' => '-', 'name' => '-', 'error' => 'Dosya açılamadı.'];
            return $results;
        }

        // Delimiter detection
        $firstLine = fgets($handle);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        rewind($handle);

        // İlk satır başlıklar
        $headers = fgetcsv($handle, 0, $delimiter, '"');
        if (!$headers) {
            $results['errors'][] = ['row' => '1', 'name' => '-', 'error' => 'Dosya başlıkları okunamadı.'];
            fclose($handle);
            return $results;
        }

        $headers = array_map('trim', $headers);
        $rowNum = 1;

        while (($row = fgetcsv($handle, 0, $delimiter, '"')) !== false) {
            $rowNum++;
            if (count($row) < count($headers)) {
                $results['errors'][] = ['row' => $rowNum, 'name' => '-', 'error' => 'Eksik kolon.'];
                continue;
            }

            $data = array_combine($headers, $row);

            // Kategori kontrolü
            $categoryId = !empty($data['category_id']) ? (int) $data['category_id'] : null;
            $categorySlug = trim($data['category_slug'] ?? '');
            
            if ($categoryId) {
                // category_id kontrol
                $existing = $db->fetch("SELECT id FROM categories WHERE id = ?", [$categoryId]);
                if (!$existing) {
                    $results['errors'][] = ['row' => $rowNum, 'name' => $data['name'] ?? '-', 'error' => "Kategori bulunamadı (ID: {$categoryId})."];
                    continue;
                }
            } elseif ($categorySlug) {
                // slug ile kategori bul
                $existing = $db->fetch("SELECT id FROM categories WHERE slug = ?", [$categorySlug]);
                if ($existing) {
                    $categoryId = $existing['id'];
                } else {
                    $results['errors'][] = ['row' => $rowNum, 'name' => $data['name'] ?? '-', 'error' => "Kategori bulunamadı (Slug: {$categorySlug})."];
                    continue;
                }
            } else {
                $results['errors'][] = ['row' => $rowNum, 'name' => $data['name'] ?? '-', 'error' => 'Kategori belirtilmedi (category_id zorunlu).'];
                continue;
            }

            $slug = trim($data['slug'] ?? slugify($data['name'] ?? ''));
            if (empty($slug)) {
                $results['errors'][] = ['row' => $rowNum, 'name' => $data['name'] ?? '-', 'error' => 'Slug boş.'];
                continue;
            }

            // Slug kontrolü
            $existingPkg = $db->fetch("SELECT id FROM packages WHERE slug = ?", [$slug]);

            $updateOnDuplicate = $options['update_on_duplicate'] ?? false;

            if ($existingPkg && !$updateOnDuplicate) {
                $results['skipped']++;
                continue;
            }

            $packageData = [
                'category_id' => $categoryId,
                'name' => trim($data['name'] ?? ''),
                'slug' => $slug,
                'short_description' => trim($data['short_description'] ?? ''),
                'description' => trim($data['description'] ?? ''),
                'image_alt' => trim($data['image_alt'] ?? ''),
                'price' => (float) ($data['price'] ?? 0),
                'discount_price' => !empty($data['discount_price']) ? (float) $data['discount_price'] : null,
                'delivery_time' => trim($data['delivery_time'] ?? ''),
                'min_quantity' => (int) ($data['min_quantity'] ?? 1),
                'max_quantity' => (int) ($data['max_quantity'] ?? 1),
                'badge' => trim($data['badge'] ?? '') ?: null,
                'status' => trim($data['status'] ?? 'active'),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
                'is_featured' => isset($data['is_featured']) && $data['is_featured'] ? 1 : 0,
                'seo_title' => trim($data['seo_title'] ?? ''),
                'seo_description' => trim($data['seo_description'] ?? ''),
                'seo_focus_keyword' => trim($data['seo_focus_keyword'] ?? ''),
            ];

            try {
                if ($existingPkg) {
                    $db->update('packages', $packageData, 'id = ?', [$existingPkg['id']]);
                } else {
                    $packageId = $db->insert('packages', $packageData);

                    // Dinamik alanlar
                    $requiredFields = trim($data['required_fields'] ?? '');
                    if ($requiredFields) {
                        $fields = explode(';', $requiredFields);
                        $sortOrder = 0;
                        foreach ($fields as $fieldDef) {
                            $parts = explode('|', trim($fieldDef));
                            if (count($parts) >= 2) {
                                $db->insert('package_fields', [
                                    'package_id' => $packageId,
                                    'field_key' => trim($parts[0]),
                                    'field_label' => trim($parts[1]),
                                    'field_type' => trim($parts[2] ?? 'text'),
                                    'is_required' => 1,
                                    'placeholder' => trim($parts[3] ?? ''),
                                    'sort_order' => $sortOrder++,
                                ]);
                            }
                        }
                    }
                }
                $results['success']++;
            } catch (\Exception $e) {
                $results['errors'][] = ['row' => $rowNum, 'name' => $data['name'] ?? '-', 'error' => $e->getMessage()];
            }
        }

        fclose($handle);
        return $results;
    }

    /**
     * CSV şablon indir
     */
    public function getTemplateHeaders(): array
    {
        return [
            'name', 'category_id', 'price', 'discount_price', 'short_description', 'description',
            'delivery_time', 'min_quantity', 'max_quantity', 'status', 'slug',
            'seo_title', 'seo_description', 'seo_focus_keyword', 'category_slug'
        ];
    }
}
