<?php
namespace App\Libraries;

class SimplePdf
{
    public function renderFromHtml(string $html): string
    {
        $text = trim(html_entity_decode(strip_tags($html)));
        $text = preg_replace('/\s+/', ' ', $text);
        $lines = explode("\n", wordwrap($text, 90));
        $content = "BT /F1 12 Tf 50 780 Td 14 TL\n";
        foreach ($lines as $index => $line) {
            $escaped = $this->escape($line);
            if ($index === 0) {
                $content .= sprintf('(%s) Tj\n', $escaped);
            } else {
                $content .= sprintf('T* (%s) Tj\n', $escaped);
            }
        }
        $content .= "ET";

        $objects = [];
        $objects[] = "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n";
        $objects[] = "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n";
        $objects[] = "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n";
        $objects[] = sprintf("4 0 obj << /Length %d >> stream\n%s\nendstream\nendobj\n", strlen($content), $content);
        $objects[] = "5 0 obj << /Type /Font /Subtype /Type1 /Name /F1 /BaseFont /Helvetica >> endobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj;
        }
        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPosition . "\n%%EOF";
        return $pdf;
    }

    private function escape(string $text): string
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/u', '', $text) ?? '';
    }
}
