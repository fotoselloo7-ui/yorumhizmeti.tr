<?php
namespace App\Services;

class TocBuilderService
{
    /**
     * Finds h2/h3 tags in HTML, gives them IDs, and builds a TOC JSON array.
     * Returns [ 'html' => string, 'toc' => array ]
     *
     * @param string $html
     * @return array
     */
    public static function build(string $html): array
    {
        $toc = [];
        
        // Use regex to find h2 and h3
        $html = preg_replace_callback('/<h([23])(.*?)>(.*?)<\/h\1>/i', function($matches) use (&$toc) {
            $level = (int)$matches[1];
            $attributes = $matches[2];
            $text = strip_tags($matches[3]);
            
            // Eğer önceden verilmiş bir id varsa onu koru, yoksa yeni slug oluştur
            $id = '';
            if (preg_match('/id=["\']([^"\']+)["\']/i', $attributes, $idMatches)) {
                $id = $idMatches[1];
            } else {
                $id = slugify($text);
                $attributes .= ' id="' . htmlspecialchars($id) . '"';
            }
            
            $toc[] = [
                'level' => $level,
                'id' => $id,
                'text' => $text
            ];
            
            return "<h{$level}{$attributes}>{$matches[3]}</h{$level}>";
        }, $html);

        return [
            'html' => $html,
            'toc' => $toc
        ];
    }
}
