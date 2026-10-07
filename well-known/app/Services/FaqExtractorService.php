<?php
namespace App\Services;

class FaqExtractorService
{
    /**
     * Extracts FAQs from raw content and returns them as an array.
     * Removes the FAQ section from the text as well, returning both.
     *
     * @param string $text
     * @return array [ 'faqs' => array, 'cleaned_text' => string ]
     */
    public static function extract(string $text): array
    {
        $lines = explode("\n", str_replace("\r", "", $text));
        $faqs = [];
        $cleanedLines = [];
        $inFaqSection = false;
        
        $currentQuestion = null;
        $currentAnswer = [];
        
        $faqHeaders = [
            'sss',
            'sıkça sorulan sorular',
            'sık sorulan sorular',
            'faq'
        ];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            
            // Başlık temizliği (Markdown # işaretleri vb.)
            $cleanHeader = strtolower(trim(preg_replace('/^#+\s*/', '', $trimmed)));
            
            if (!$inFaqSection && in_array($cleanHeader, $faqHeaders, true)) {
                $inFaqSection = true;
                continue;
            }
            
            if ($inFaqSection) {
                // Eğer yeni bir ana başlık (SSS olmayan H2) geldiyse FAQ bölümü bitmiş olabilir
                if (preg_match('/^#{1,2}\s+(.*)/', $trimmed) && !in_array($cleanHeader, $faqHeaders, true)) {
                    $inFaqSection = false;
                    
                    // Kaydet
                    if ($currentQuestion && !empty($currentAnswer)) {
                        $faqs[] = [
                            'question' => $currentQuestion,
                            'answer' => trim(implode("\n", $currentAnswer))
                        ];
                        $currentQuestion = null;
                        $currentAnswer = [];
                    }
                    
                    $cleanedLines[] = $line;
                    continue;
                }
                
                // Soru tespiti
                // 1. Soru: ile başlayan
                // 2. ### veya #### ile başlayan
                // 3. ? ile biten kısa satırlar (ör. max 150 karakter)
                $isQuestion = false;
                $questionText = '';
                
                if (stripos($trimmed, 'soru:') === 0) {
                    $isQuestion = true;
                    $questionText = trim(substr($trimmed, 5));
                } elseif (preg_match('/^#{3,4}\s+(.*)/', $trimmed, $matches)) {
                    $isQuestion = true;
                    $questionText = trim($matches[1]);
                } elseif (str_ends_with($trimmed, '?') && mb_strlen($trimmed) < 150) {
                    $isQuestion = true;
                    $questionText = $trimmed;
                }
                
                if ($isQuestion) {
                    // Önceki soruyu kaydet
                    if ($currentQuestion && !empty($currentAnswer)) {
                        $faqs[] = [
                            'question' => $currentQuestion,
                            'answer' => trim(implode("\n", $currentAnswer))
                        ];
                    }
                    $currentQuestion = $questionText;
                    $currentAnswer = [];
                } else {
                    if ($currentQuestion) {
                        // Cevap kısmında "Cevap:" öneki varsa temizle
                        if (stripos($trimmed, 'cevap:') === 0) {
                            $trimmed = trim(substr($trimmed, 6));
                        }
                        if ($trimmed !== '') {
                            $currentAnswer[] = $trimmed;
                        }
                    } else {
                        // Henüz soru yoksa ve boş değilse FAQ öncesi yanlış algılanan bir şey olabilir, bunu atla.
                    }
                }
            } else {
                $cleanedLines[] = $line;
            }
        }
        
        // Son soruyu kaydet
        if ($currentQuestion && !empty($currentAnswer)) {
            $faqs[] = [
                'question' => $currentQuestion,
                'answer' => trim(implode("\n", $currentAnswer))
            ];
        }
        
        return [
            'faqs' => $faqs,
            'cleaned_text' => trim(implode("\n", $cleanedLines))
        ];
    }
}
