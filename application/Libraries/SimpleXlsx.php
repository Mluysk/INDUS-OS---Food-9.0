<?php
namespace App\Libraries;

class SimpleXlsx
{
    private string $sheetName;
    private array $header = [];
    private array $rows = [];

    public function __construct(string $sheetName = 'Planilha')
    {
        $this->sheetName = preg_replace('/[^A-Za-z0-9_ ]/', '', $sheetName) ?: 'Planilha';
    }

    public function setHeader(array $header): void
    {
        $this->header = array_values($header);
    }

    public function addRow(array $row): void
    {
        $this->rows[] = array_values($row);
    }

    public function toString(): string
    {
        $sheetXml = $this->buildSheetXml();
        $workbookXml = $this->buildWorkbookXml();
        $relsXml = $this->buildWorkbookRelsXml();
        $contentTypesXml = $this->buildContentTypesXml();
        $rootRelsXml = $this->buildRootRelsXml();

        $files = [
            '[Content_Types].xml' => $contentTypesXml,
            '_rels/.rels' => $rootRelsXml,
            'xl/workbook.xml' => $workbookXml,
            'xl/_rels/workbook.xml.rels' => $relsXml,
            'xl/worksheets/sheet1.xml' => $sheetXml,
        ];

        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive();
            $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
            $zip->open($tmp, \ZipArchive::OVERWRITE);
            foreach ($files as $path => $content) {
                $zip->addFromString($path, $content);
            }
            $zip->close();
            $binary = file_get_contents($tmp);
            unlink($tmp);
            return $binary === false ? '' : $binary;
        }

        return $this->buildZipArchive($files);
    }

    private function buildSheetXml(): string
    {
        $rows = [];
        if ($this->header) {
            $rows[] = $this->rowXml(1, $this->header, true);
        }
        $index = 2;
        foreach ($this->rows as $row) {
            $rows[] = $this->rowXml($index++, $row, false);
        }
        $rowsXml = implode('', $rows);
        return '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . $rowsXml . '</sheetData></worksheet>';
    }

    private function rowXml(int $rowNumber, array $values, bool $header): string
    {
        $cells = [];
        foreach ($values as $colIndex => $value) {
            $column = $this->columnIndexToName($colIndex + 1) . $rowNumber;
            if (is_numeric($value) && !$header) {
                $cells[] = '<c r="' . $column . '"><v>' . $value . '</v></c>';
            } else {
                $cells[] = '<c r="' . $column . '" t="inlineStr"><is><t>' . htmlspecialchars((string)$value, ENT_QUOTES | ENT_XML1) . '</t></is></c>';
            }
        }
        return '<row r="' . $rowNumber . '">' . implode('', $cells) . '</row>';
    }

    private function columnIndexToName(int $index): string
    {
        $name = '';
        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)) . $name;
            $index = intdiv($index, 26);
        }
        return $name;
    }

    private function buildWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheets><sheet name="' . htmlspecialchars($this->sheetName, ENT_QUOTES | ENT_XML1) . '" sheetId="1" r:id="rId1"/></sheets></workbook>';
    }

    private function buildWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>';
    }

    private function buildContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>';
    }

    private function buildRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
    }

    private function buildZipArchive(array $files): string
    {
        $data = '';
        $centralDirectory = '';
        $offset = 0;
        $fileCount = 0;
        foreach ($files as $path => $content) {
            $fileCount++;
            $compressed = gzcompress($content);
            if ($compressed === false) {
                $compressed = $content;
                $compressedLength = strlen($compressed);
                $crc = crc32($content);
                $nameLength = strlen($path);
                $uncompressedLength = strlen($content);

                $localHeader = pack('VvvvvvVVVvv', 0x04034b50, 20, 0, 0, 0, 0, $crc, $compressedLength, $uncompressedLength, $nameLength, 0) . $path;
                $data .= $localHeader . $compressed;

                $centralHeader = pack('VvvvvvvVVVvvvvvVV', 0x02014b50, 0, 20, 0, 0, 0, 0, $crc, $compressedLength, $uncompressedLength, $nameLength, 0, 0, 0, 0, 0, $offset) . $path;
                $centralDirectory .= $centralHeader;

                $offset += strlen($localHeader) + $compressedLength;
                continue;
            }
            $compressed = substr($compressed, 2, -4); // raw deflate
            $crc = crc32($content);
            $nameLength = strlen($path);
            $compressedLength = strlen($compressed);
            $uncompressedLength = strlen($content);

            $localHeader = pack('VvvvvvVVVvv', 0x04034b50, 20, 0, 8, 0, 0, $crc, $compressedLength, $uncompressedLength, $nameLength, 0) . $path;
            $data .= $localHeader . $compressed;

            $centralHeader = pack('VvvvvvvVVVvvvvvVV', 0x02014b50, 0, 20, 0, 8, 0, 0, $crc, $compressedLength, $uncompressedLength, $nameLength, 0, 0, 0, 0, 0, $offset) . $path;
            $centralDirectory .= $centralHeader;

            $offset += strlen($localHeader) + $compressedLength;
        }

        $endOfCentral = pack('VvvvvVVv', 0x06054b50, 0, 0, $fileCount, $fileCount, strlen($centralDirectory), $offset, 0);
        return $data . $centralDirectory . $endOfCentral;
    }
}
