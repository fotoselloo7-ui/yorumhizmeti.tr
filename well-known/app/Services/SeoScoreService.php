<?php
namespace App\Services;

/**
 * SEO Score Service
 * 14 kritere göre 0-100 arası SEO puanlaması yapar.
 */
class SeoScoreService
{
    private array $checks = [];
    private int $score = 0;

    /**
     * SEO puanı hesapla
     */
    public function calculate(array $data): array
    {
        $this->checks = [];
        $totalPoints = 0;
        $earnedPoints = 0;

        // 1. SEO title var mı? (10 puan)
        $totalPoints += 10;
        $hasSeoTitle = !empty($data['seo_title']);
        if ($hasSeoTitle) {
            $earnedPoints += 10;
            $this->checks[] = ['pass' => true, 'message' => 'SEO başlığı mevcut.'];
        } else {
            $this->checks[] = ['pass' => false, 'message' => 'SEO başlığı eklenmemiş.'];
        }

        // 2. SEO title 35-60 karakter arası mı? (5 puan)
        $totalPoints += 5;
        if ($hasSeoTitle) {
            $len = mb_strlen($data['seo_title']);
            if ($len >= 35 && $len <= 60) {
                $earnedPoints += 5;
                $this->checks[] = ['pass' => true, 'message' => "SEO başlığı ideal uzunlukta ({$len} karakter)."];
            } else {
                $this->checks[] = ['pass' => false, 'message' => "SEO başlığı 35-60 karakter arası olmalı (şu an: {$len})."];
            }
        }

        // 3. SEO description var mı? (10 puan)
        $totalPoints += 10;
        $hasSeoDesc = !empty($data['seo_description']);
        if ($hasSeoDesc) {
            $earnedPoints += 10;
            $this->checks[] = ['pass' => true, 'message' => 'Meta açıklaması mevcut.'];
        } else {
            $this->checks[] = ['pass' => false, 'message' => 'Meta açıklaması eklenmemiş.'];
        }

        // 4. SEO description 120-160 karakter arası mı? (5 puan)
        $totalPoints += 5;
        if ($hasSeoDesc) {
            $len = mb_strlen($data['seo_description']);
            if ($len >= 120 && $len <= 160) {
                $earnedPoints += 5;
                $this->checks[] = ['pass' => true, 'message' => "Meta açıklaması ideal uzunlukta ({$len} karakter)."];
            } else {
                $this->checks[] = ['pass' => false, 'message' => "Meta açıklaması 120-160 karakter arası olmalı (şu an: {$len})."];
            }
        }

        // 5. Focus keyword var mı? (10 puan)
        $totalPoints += 10;
        $hasKeyword = !empty($data['seo_focus_keyword']);
        if ($hasKeyword) {
            $earnedPoints += 10;
            $this->checks[] = ['pass' => true, 'message' => 'Odak anahtar kelime belirlenmiş.'];
        } else {
            $this->checks[] = ['pass' => false, 'message' => 'Odak anahtar kelime belirlenmemiş.'];
        }

        // 6. Focus keyword başlıkta geçiyor mu? (8 puan)
        $totalPoints += 8;
        if ($hasKeyword && $hasSeoTitle) {
            $keyword = mb_strtolower($data['seo_focus_keyword']);
            if (str_contains(mb_strtolower($data['seo_title']), $keyword)) {
                $earnedPoints += 8;
                $this->checks[] = ['pass' => true, 'message' => 'Odak anahtar kelime başlıkta geçiyor.'];
            } else {
                $this->checks[] = ['pass' => false, 'message' => 'Odak anahtar kelime başlıkta geçmiyor.'];
            }
        }

        // 7. Focus keyword açıklamada geçiyor mu? (8 puan)
        $totalPoints += 8;
        if ($hasKeyword && $hasSeoDesc) {
            $keyword = mb_strtolower($data['seo_focus_keyword']);
            if (str_contains(mb_strtolower($data['seo_description']), $keyword)) {
                $earnedPoints += 8;
                $this->checks[] = ['pass' => true, 'message' => 'Odak anahtar kelime meta açıklamada geçiyor.'];
            } else {
                $this->checks[] = ['pass' => false, 'message' => 'Odak anahtar kelime meta açıklamada geçmiyor.'];
            }
        }

        // 8. Focus keyword slug içinde geçiyor mu? (7 puan)
        $totalPoints += 7;
        if ($hasKeyword && !empty($data['slug'])) {
            $keyword = mb_strtolower($data['seo_focus_keyword']);
            $keywordSlug = slugify($keyword);
            if (str_contains($data['slug'], $keywordSlug)) {
                $earnedPoints += 7;
                $this->checks[] = ['pass' => true, 'message' => 'Odak anahtar kelime URL slug içinde geçiyor.'];
            } else {
                $this->checks[] = ['pass' => false, 'message' => 'Odak anahtar kelime URL slug içinde geçmiyor.'];
            }
        }

        // 9. İçerik yeterli uzunlukta mı? (10 puan)
        $totalPoints += 10;
        $content = strip_tags($data['content'] ?? $data['description'] ?? '');
        $wordCount = str_word_count($content);
        if ($wordCount >= 100) {
            $earnedPoints += 10;
            $this->checks[] = ['pass' => true, 'message' => "İçerik yeterli uzunlukta ({$wordCount} kelime)."];
        } elseif ($wordCount >= 50) {
            $earnedPoints += 5;
            $this->checks[] = ['pass' => false, 'message' => "İçerik biraz kısa ({$wordCount} kelime). En az 100 kelime önerilir."];
        } else {
            $this->checks[] = ['pass' => false, 'message' => "İçerik çok kısa ({$wordCount} kelime). En az 100 kelime önerilir."];
        }

        // 10. Görsel alt metni var mı? (7 puan)
        $totalPoints += 7;
        if (!empty($data['image'])) {
            if (!empty($data['image_alt'])) {
                $earnedPoints += 7;
                $this->checks[] = ['pass' => true, 'message' => 'Görsel alt metni mevcut.'];
            } else {
                $this->checks[] = ['pass' => false, 'message' => 'Görsel alt metni eklenmemiş.'];
            }
        } else {
            $earnedPoints += 3;
            $this->checks[] = ['pass' => false, 'message' => 'Görsel eklenmemiş (opsiyonel).'];
        }

        // 11. H1 başlık var mı? (5 puan)
        $totalPoints += 5;
        $rawContent = $data['content'] ?? $data['description'] ?? '';
        if (!empty($data['name']) || !empty($data['title']) || str_contains($rawContent, '<h1') || str_contains($rawContent, '<h2')) {
            $earnedPoints += 5;
            $this->checks[] = ['pass' => true, 'message' => 'Başlık yapısı mevcut.'];
        } else {
            $this->checks[] = ['pass' => false, 'message' => 'İçerikte başlık (H1/H2) yapısı eksik.'];
        }

        // 12. İç link var mı? (5 puan)
        $totalPoints += 5;
        if (str_contains($rawContent, '<a ') || str_contains($rawContent, 'href=')) {
            $earnedPoints += 5;
            $this->checks[] = ['pass' => true, 'message' => 'İçerikte iç link mevcut.'];
        } else {
            $this->checks[] = ['pass' => false, 'message' => 'İçerikte iç link eklenmemiş.'];
        }

        // 13. Canonical var mı? (5 puan)
        $totalPoints += 5;
        if (!empty($data['canonical_url']) || !empty($data['slug'])) {
            $earnedPoints += 5;
            $this->checks[] = ['pass' => true, 'message' => 'Canonical URL mevcut.'];
        } else {
            $this->checks[] = ['pass' => false, 'message' => 'Canonical URL tanımlanmamış.'];
        }

        // Toplam puan hesapla (0-100)
        $this->score = $totalPoints > 0 ? (int) round(($earnedPoints / $totalPoints) * 100) : 0;

        return [
            'score' => $this->score,
            'checks' => $this->checks,
            'color' => $this->getColor(),
            'label' => $this->getLabel(),
        ];
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getChecks(): array
    {
        return $this->checks;
    }

    public function getColor(): string
    {
        if ($this->score >= 80) return 'success';
        if ($this->score >= 50) return 'warning';
        return 'danger';
    }

    public function getLabel(): string
    {
        if ($this->score >= 80) return 'İyi';
        if ($this->score >= 50) return 'Geliştirilebilir';
        return 'Zayıf';
    }
}
