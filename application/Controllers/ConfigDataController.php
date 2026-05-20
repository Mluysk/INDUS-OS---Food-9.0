<?php
namespace App\Controllers;

use App\Core\Container;
use App\Core\Controller;
use App\Services\AuditService;
use App\Services\AuthService;
use App\Services\ConfigService;

class ConfigDataController extends Controller
{
    private ConfigService $config;

    public function __construct(Container $container, AuthService $auth, AuditService $audit, ConfigService $config)
    {
        parent::__construct($container, $auth, $audit);
        $this->config = $config;
    }

    public function exportClientes(): void
    {
        $this->auth->ensureRole(['admin']);

        $rows = $this->db()->query("SELECT razao_social, nome_fantasia, cnpj, cpf, ie, email, telefone, contato_tipo, whatsapp, cep, endereco, numero, bairro, cidade, estado, created_at FROM clientes ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
        $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'export', 'clientes', null, [
            'arquivo' => 'clientes.xls',
            'formato' => 'xls',
            'quantidade' => count($rows),
        ]);
        $this->outputExcel('clientes.xls', 'Clientes', [
            'Razao social', 'Nome fantasia', 'CNPJ', 'CPF', 'IE', 'E-mail', 'Contato', 'Tipo do contato', 'WhatsApp', 'CEP', 'Endereco', 'Numero', 'Bairro', 'Cidade', 'Estado', 'Criado em'
        ], $rows);
    }

    public function exportProdutos(): void
    {
        $this->auth->ensureRole(['admin']);

        $rows = $this->db()->query("SELECT sku, nome, tipo, categoria, unidade, peso_liquido, shelf_life_dias, preco, alergicos, created_at FROM produtos ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
        $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'export', 'produtos', null, [
            'arquivo' => 'produtos.xls',
            'formato' => 'xls',
            'quantidade' => count($rows),
        ]);
        $this->outputExcel('produtos.xls', 'Produtos', [
            'SKU', 'Produto', 'Tipo', 'Categoria', 'Unidade', 'Peso liquido', 'Shelf life dias', 'Preco', 'Alergicos', 'Criado em'
        ], $rows);
    }

    public function importClientes(): void
    {
        $this->auth->ensureRole(['admin']);
        $success = false;

        try {
            $rows = $this->uploadedRows('arquivo');
            $columns = $this->tableColumns('clientes');
            $allowed = array_values(array_intersect($columns, [
                'razao_social', 'nome_fantasia', 'cnpj', 'cpf', 'ie', 'email', 'telefone', 'contato_tipo', 'whatsapp', 'cep', 'endereco', 'numero', 'bairro', 'cidade', 'estado'
            ]));

            $count = 0;
            $skipped = 0;
            foreach ($rows as $row) {
                $documento = $this->clientDocument($this->value($row, ['documento', 'cnpj cpf', 'cnpj/cpf', 'cpf cnpj', 'cpf/cnpj']));
                $cnpj = $this->digits($this->value($row, ['cnpj'])) ?: $documento['cnpj'];
                $cpf = $this->digits($this->value($row, ['cpf'])) ?: $documento['cpf'];
                $payload = [
                    'razao_social' => $this->value($row, ['razao social', 'razao_social', 'razao', 'nome', 'cliente', 'nome cliente', 'nome completo', 'empresa']),
                    'nome_fantasia' => $this->value($row, ['nome fantasia', 'nome_fantasia', 'fantasia', 'loja', 'apelido']),
                    'cnpj' => $cnpj,
                    'cpf' => $cpf,
                    'ie' => $this->value($row, ['ie', 'inscricao estadual', 'inscricao_estadual']),
                    'email' => strtolower((string)$this->value($row, ['email', 'e-mail', 'e mail'])),
                    'telefone' => $this->value($row, ['telefone', 'fone', 'contato', 'celular']),
                    'contato_tipo' => $this->contactType($this->value($row, ['tipo do contato', 'contato_tipo', 'tipo contato'])),
                    'whatsapp' => $this->value($row, ['whatsapp', 'whtasapp', 'whats']),
                    'cep' => $this->cep($this->value($row, ['cep'])),
                    'endereco' => $this->value($row, ['endereco', 'endereço', 'rua', 'logradouro']),
                    'numero' => $this->value($row, ['numero', 'número', 'num']),
                    'bairro' => $this->value($row, ['bairro']),
                    'cidade' => $this->value($row, ['cidade']),
                    'estado' => strtoupper((string)$this->value($row, ['estado', 'uf', 'estado uf'])),
                ];
                if (($payload['nome_fantasia'] ?? '') === '' && ($payload['razao_social'] ?? '') !== '') {
                    $payload['nome_fantasia'] = $payload['razao_social'];
                }
                if (($payload['contato_tipo'] ?? '') === '') {
                    $payload['contato_tipo'] = 'celular';
                }

                $payload = $this->cleanPayload(array_intersect_key($payload, array_flip($allowed)));
                if (empty($payload['razao_social']) && empty($payload['nome_fantasia'])) {
                    $skipped++;
                    continue;
                }

                if ($this->saveClientPayload($payload)) {
                    $count++;
                } else {
                    $skipped++;
                }
            }

            $message = $count.' cliente(s) importado(s) com sucesso.';
            if ($skipped > 0) {
                $message .= ' '.$skipped.' linha(s) foram ignoradas por estarem incompletas.';
            }
            $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'import', 'clientes', null, [
                'arquivo' => $this->uploadedFilename('arquivo'),
                'formato' => 'xls',
                'linhas_lidas' => count($rows),
                'importados' => $count,
                'ignorados' => $skipped,
                'resultado' => 'sucesso',
            ]);
            $this->setFlash('success', $message);
            $success = true;
        } catch (\Throwable $exception) {
            $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'import', 'clientes', null, [
                'arquivo' => $this->uploadedFilename('arquivo'),
                'formato' => 'xls',
                'resultado' => 'erro',
                'erro' => $exception->getMessage(),
            ]);
            $this->setFlash('error', 'Nao foi possivel importar clientes: '.$exception->getMessage());
        }

        $this->redirect($success ? '/clientes' : '/config?tab=backup');
    }

    public function importProdutos(): void
    {
        $this->auth->ensureRole(['admin']);
        $success = false;

        try {
            $rows = $this->uploadedRows('arquivo');
            $columns = $this->tableColumns('produtos');
            $allowed = array_values(array_intersect($columns, [
                'sku', 'nome', 'tipo', 'categoria', 'unidade', 'peso_liquido', 'shelf_life_dias', 'preco', 'alergicos'
            ]));

            $count = 0;
            $skipped = 0;
            foreach ($rows as $index => $row) {
                $tipo = strtoupper((string)$this->value($row, ['tipo']));
                $tipo = $tipo === 'MP' ? 'MP' : 'PA';
                $payload = [
                    'sku' => strtoupper((string)$this->value($row, ['sku', 'codigo', 'código', 'codigo sku'])),
                    'nome' => $this->value($row, ['produto', 'nome', 'nome do produto', 'descricao', 'descrição']),
                    'tipo' => $tipo,
                    'categoria' => $this->upper($this->value($row, ['categoria'])),
                    'unidade' => $this->value($row, ['unidade', 'un']),
                    'peso_liquido' => $this->decimal($this->value($row, ['peso liquido', 'peso líquido', 'peso_liquido', 'peso'])),
                    'shelf_life_dias' => $this->integer($this->value($row, ['shelf life dias', 'shelf_life_dias', 'validade dias', 'validade'])),
                    'preco' => $this->decimal($this->value($row, ['preco', 'preço', 'valor'])),
                    'alergicos' => $this->value($row, ['alergicos', 'alérgicos']),
                ];

                if (trim((string)$payload['nome']) === '') {
                    $skipped++;
                    continue;
                }
                if (trim((string)$payload['sku']) === '') {
                    $payload['sku'] = $this->nextImportSku($tipo, $index + 1);
                }
                if (trim((string)$payload['unidade']) === '') {
                    $payload['unidade'] = 'un';
                }

                $payload = $this->cleanPayload(array_intersect_key($payload, array_flip($allowed)));
                try {
                    $existingId = $this->existingProductId((string)$payload['sku']);
                    if ($existingId > 0) {
                        $this->updateRow('produtos', $existingId, $payload);
                    } else {
                        $this->insertRow('produtos', $payload);
                    }
                    $count++;
                } catch (\Throwable $rowException) {
                    $skipped++;
                }
            }

            $message = $count.' produto(s) importado(s) com sucesso.';
            if ($skipped > 0) {
                $message .= ' '.$skipped.' linha(s) foram ignoradas por dados repetidos ou incompletos.';
            }
            $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'import', 'produtos', null, [
                'arquivo' => $this->uploadedFilename('arquivo'),
                'formato' => 'xls',
                'linhas_lidas' => count($rows),
                'importados' => $count,
                'ignorados' => $skipped,
                'resultado' => 'sucesso',
            ]);
            $this->setFlash('success', $message);
            $success = true;
        } catch (\Throwable $exception) {
            $this->audit->log((int)($this->auth->user()['id'] ?? 0), 'import', 'produtos', null, [
                'arquivo' => $this->uploadedFilename('arquivo'),
                'formato' => 'xls',
                'resultado' => 'erro',
                'erro' => $exception->getMessage(),
            ]);
            $this->setFlash('error', 'Nao foi possivel importar produtos: '.$exception->getMessage());
        }

        $this->redirect($success ? '/produtos?tab=lista' : '/config?tab=backup');
    }

    private function db(): \PDO
    {
        return $this->config->getConnection();
    }

    private function outputExcel(string $filename, string $title, array $headers, array $rows): void
    {
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF";
        echo '<html><head><meta charset="UTF-8"><style>';
        echo 'body{font-family:Arial,sans-serif;color:#0f172a}h2{margin:0 0 14px;font-size:18px}table{border-collapse:collapse;width:100%}th{background:#eef2f7;color:#475569;font-weight:700;text-transform:uppercase}th,td{border:1px solid #cbd5e1;padding:8px 10px;font-size:12px;vertical-align:top}tr:nth-child(even) td{background:#f8fafc}.text{mso-number-format:\@}';
        echo '</style></head><body>';
        echo '<h2>'.$this->escape($title).'</h2><table><thead><tr>';
        foreach ($headers as $header) {
            echo '<th>'.$this->escape($header).'</th>';
        }
        echo '</tr></thead><tbody>';
        foreach ($rows as $row) {
            echo '<tr>';
            foreach ($row as $value) {
                echo '<td class="text">'.$this->escape((string)$value).'</td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table></body></html>';
        exit;
    }

    private function uploadedRows(string $field): array
    {
        if (!isset($_FILES[$field]) || !is_array($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('selecione um arquivo valido.');
        }

        $filename = $this->uploadedFilename($field);
        $tmpName = (string)$_FILES[$field]['tmp_name'];
        $content = file_get_contents($tmpName);
        if (!is_string($content) || trim($content) === '') {
            throw new \RuntimeException('o arquivo esta vazio.');
        }

        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'xlsx' || strncmp($content, "PK\x03\x04", 4) === 0) {
            return $this->rowsFromXlsx($tmpName);
        }

        $content = $this->ensureUtf8($content);

        if (stripos($content, '<table') !== false || stripos($content, '<tr') !== false) {
            return $this->rowsFromHtmlTable($content);
        }

        return $this->rowsFromDelimited($content);
    }

    private function rowsFromXlsx(string $path): array
    {
        $files = $this->xlsxFiles($path);
        $sharedStrings = $this->xlsxSharedStrings($files['xl/sharedStrings.xml'] ?? '');
        $worksheets = $this->xlsxWorksheetXmls($files);
        if (!$worksheets) {
            throw new \RuntimeException('nao foi possivel localizar a primeira aba da planilha.');
        }

        $rows = [];
        foreach ($worksheets as $sheetXml) {
            $matrix = $this->xlsxMatrixFromWorksheet($sheetXml, $sharedStrings);
            if (count($matrix) < 2) {
                continue;
            }
            $rows = array_merge($rows, $this->matrixToRows($matrix));
        }

        if (!$rows) {
            throw new \RuntimeException('a planilha XLSX precisa ter cabecalho e ao menos uma linha.');
        }

        return $rows;
    }

    private function xlsxMatrixFromWorksheet(string $sheetXml, array $sharedStrings): array
    {
        $xml = @simplexml_load_string($sheetXml);
        if (!$xml) {
            return [];
        }

        $sheetData = $xml->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main')->sheetData;
        if (!$sheetData || !count($sheetData->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main')->row)) {
            return [];
        }

        $matrix = [];
        foreach ($sheetData->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main')->row as $row) {
            $cells = [];
            foreach ($row->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main')->c as $cell) {
                $reference = (string)($cell['r'] ?? '');
                $columnIndex = $this->xlsxColumnIndex($reference);
                while (count($cells) < $columnIndex) {
                    $cells[] = '';
                }
                $cells[] = $this->xlsxCellValue($cell, $sharedStrings);
            }
            if ($cells && array_filter($cells, static fn($value) => trim((string)$value) !== '')) {
                $matrix[] = $cells;
            }
        }

        return $matrix;
    }

    private function xlsxFiles(string $path): array
    {
        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($path) !== true) {
                throw new \RuntimeException('nao foi possivel abrir a planilha XLSX.');
            }

            $files = [];
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $name = (string)$zip->getNameIndex($index);
                $content = $zip->getFromName($name);
                if (is_string($content)) {
                    $files[$name] = $content;
                }
            }
            $zip->close();
            return $files;
        }

        $content = file_get_contents($path);
        if (!is_string($content) || $content === '') {
            throw new \RuntimeException('nao foi possivel ler a planilha XLSX.');
        }

        return $this->xlsxFilesFromBinary($content);
    }

    private function xlsxFilesFromBinary(string $content): array
    {
        $eocdOffset = strrpos($content, "PK\x05\x06");
        if ($eocdOffset === false || strlen($content) < $eocdOffset + 22) {
            throw new \RuntimeException('arquivo XLSX invalido ou corrompido.');
        }

        $eocd = unpack('vdisk/vstartDisk/ventriesDisk/ventries/Vsize/Voffset/vcommentLength', substr($content, $eocdOffset + 4, 18));
        $centralOffset = (int)($eocd['offset'] ?? 0);
        $entries = (int)($eocd['entries'] ?? 0);
        $files = [];
        $cursor = $centralOffset;

        for ($index = 0; $index < $entries; $index++) {
            if (substr($content, $cursor, 4) !== "PK\x01\x02") {
                break;
            }
            $header = unpack(
                'vmade/vneeded/vflags/vmethod/vtime/vdate/Vcrc/Vcompressed/Vuncompressed/vnameLength/vextraLength/vcommentLength/vdisk/vinternal/Vexternal/VlocalOffset',
                substr($content, $cursor + 4, 42)
            );
            $nameLength = (int)$header['nameLength'];
            $extraLength = (int)$header['extraLength'];
            $commentLength = (int)$header['commentLength'];
            $name = substr($content, $cursor + 46, $nameLength);
            $localOffset = (int)$header['localOffset'];
            $localNameLength = unpack('v', substr($content, $localOffset + 26, 2))[1] ?? 0;
            $localExtraLength = unpack('v', substr($content, $localOffset + 28, 2))[1] ?? 0;
            $dataOffset = $localOffset + 30 + (int)$localNameLength + (int)$localExtraLength;
            $compressed = substr($content, $dataOffset, (int)$header['compressed']);
            $method = (int)$header['method'];

            if ($method === 0) {
                $files[$name] = $compressed;
            } elseif ($method === 8) {
                $inflated = @gzinflate($compressed);
                if ($inflated === false) {
                    throw new \RuntimeException('nao foi possivel descompactar a planilha XLSX.');
                }
                $files[$name] = $inflated;
            }

            $cursor += 46 + $nameLength + $extraLength + $commentLength;
        }

        if (!$files) {
            throw new \RuntimeException('nao foi possivel ler os arquivos internos do XLSX.');
        }
        return $files;
    }

    private function xlsxSharedStrings(string $content): array
    {
        if ($content === '') {
            return [];
        }
        $xml = @simplexml_load_string($content);
        if (!$xml) {
            return [];
        }

        $strings = [];
        foreach ($xml->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main')->si as $item) {
            $itemChildren = $item->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            if (isset($itemChildren->t)) {
                $strings[] = (string)$itemChildren->t;
                continue;
            }
            $parts = [];
            foreach ($itemChildren->r as $run) {
                $parts[] = (string)$run->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main')->t;
            }
            $strings[] = implode('', $parts);
        }
        return $strings;
    }

    private function xlsxWorksheetXmls(array $files): array
    {
        $worksheets = [];
        foreach ($files as $name => $content) {
            if (preg_match('#^xl/worksheets/sheet\d+\.xml$#', $name)) {
                $worksheets[$name] = (string)$content;
            }
        }
        ksort($worksheets, SORT_NATURAL);
        return array_values($worksheets);
    }

    private function xlsxColumnIndex(string $reference): int
    {
        if (!preg_match('/^([A-Z]+)/i', $reference, $matches)) {
            return 0;
        }
        $letters = strtoupper($matches[1]);
        $index = 0;
        for ($i = 0, $length = strlen($letters); $i < $length; $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }
        return max(0, $index - 1);
    }

    private function xlsxCellValue(\SimpleXMLElement $cell, array $sharedStrings): string
    {
        $type = (string)($cell['t'] ?? '');
        $children = $cell->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        if ($type === 's') {
            $index = (int)($children->v ?? -1);
            return trim((string)($sharedStrings[$index] ?? ''));
        }
        if ($type === 'inlineStr') {
            return trim((string)($children->is->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main')->t ?? ''));
        }
        return trim((string)($children->v ?? ''));
    }

    private function uploadedFilename(string $field): string
    {
        return isset($_FILES[$field]) && is_array($_FILES[$field])
            ? (string)($_FILES[$field]['name'] ?? 'arquivo.xls')
            : 'arquivo.xls';
    }

    private function rowsFromDelimited(string $content): array
    {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content) ?? $content;
        $firstLine = strtok($content, "\r\n");
        if ($firstLine === false) {
            throw new \RuntimeException('a planilha precisa ter cabecalho e ao menos uma linha.');
        }

        if (stripos($firstLine, 'sep=') === 0) {
            $content = preg_replace('/^sep=.+\R/u', '', $content, 1) ?? $content;
            $firstLine = strtok($content, "\r\n");
            if ($firstLine === false) {
                throw new \RuntimeException('a planilha precisa ter cabecalho e ao menos uma linha.');
            }
        }

        $delimiter = $this->detectDelimiter($firstLine);
        $handle = fopen('php://temp', 'r+');
        if (!$handle) {
            throw new \RuntimeException('nao foi possivel preparar a leitura do arquivo.');
        }
        fwrite($handle, $content);
        rewind($handle);

        $headerValues = fgetcsv($handle, 0, $delimiter);
        if (!$headerValues) {
            fclose($handle);
            throw new \RuntimeException('a planilha precisa ter cabecalho e ao menos uma linha.');
        }

        $headers = array_map(fn($value) => $this->normalizeKey($value), $headerValues);
        $rows = [];
        while (($values = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (!array_filter($values, static fn($value) => trim((string)$value) !== '')) {
                continue;
            }
            $row = [];
            foreach ($headers as $index => $header) {
                if ($header === '') {
                    continue;
                }
                $row[$header] = trim((string)($values[$index] ?? ''));
            }
            if ($row) {
                $rows[] = $row;
            }
        }
        fclose($handle);

        if (!$rows) {
            throw new \RuntimeException('a planilha precisa ter cabecalho e ao menos uma linha.');
        }

        return $rows;
    }

    private function ensureUtf8(string $content): string
    {
        if (preg_match('//u', $content)) {
            return $content;
        }

        $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $content);
        if (is_string($converted) && $converted !== '') {
            return $converted;
        }

        $converted = @iconv('ISO-8859-1', 'UTF-8//IGNORE', $content);
        return is_string($converted) && $converted !== '' ? $converted : $content;
    }

    private function rowsFromHtmlTable(string $content): array
    {
        $rows = $this->rowsFromHtmlWithDom($content);
        if ($rows) {
            return $rows;
        }

        preg_match_all('/<tr\b[^>]*>(.*?)<\/tr>/is', $content, $rowMatches);
        $matrix = [];
        foreach ($rowMatches[1] ?? [] as $rowHtml) {
            preg_match_all('/<t[dh]\b[^>]*>(.*?)<\/t[dh]>/is', $rowHtml, $cellMatches);
            $cells = array_map(fn($cell) => trim(html_entity_decode(strip_tags($cell), ENT_QUOTES | ENT_HTML5, 'UTF-8')), $cellMatches[1] ?? []);
            if ($cells) {
                $matrix[] = $cells;
            }
        }

        return $this->matrixToRows($matrix);
    }

    private function rowsFromHtmlWithDom(string $content): array
    {
        if (!class_exists(\DOMDocument::class)) {
            return [];
        }

        $previous = libxml_use_internal_errors(true);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $loaded = $dom->loadHTML('<?xml encoding="UTF-8">'.$content, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            return [];
        }

        $tables = $dom->getElementsByTagName('table');
        if ($tables->length === 0) {
            return [];
        }

        $matrix = [];
        $table = $tables->item(0);
        foreach ($table->getElementsByTagName('tr') as $tr) {
            $cells = [];
            foreach ($tr->childNodes as $cell) {
                if (!in_array(strtolower($cell->nodeName), ['th', 'td'], true)) {
                    continue;
                }
                $cells[] = trim(preg_replace('/\s+/', ' ', html_entity_decode($cell->textContent, ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');
            }
            if ($cells) {
                $matrix[] = $cells;
            }
        }

        return $this->matrixToRows($matrix);
    }

    private function matrixToRows(array $matrix): array
    {
        if (count($matrix) < 2) {
            throw new \RuntimeException('nao foi possivel ler a tabela do arquivo.');
        }

        $headers = array_map(fn($value) => $this->normalizeKey((string)$value), array_shift($matrix));
        $rows = [];
        foreach ($matrix as $values) {
            $row = [];
            foreach ($headers as $index => $header) {
                if ($header === '') {
                    continue;
                }
                $row[$header] = trim((string)($values[$index] ?? ''));
            }
            if ($row && array_filter($row, static fn($value) => trim((string)$value) !== '')) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function detectDelimiter(string $line): string
    {
        $delimiters = ["\t" => substr_count($line, "\t"), ';' => substr_count($line, ';'), ',' => substr_count($line, ',')];
        arsort($delimiters);
        $delimiter = (string)array_key_first($delimiters);
        return $delimiters[$delimiter] > 0 ? $delimiter : ';';
    }

    private function value(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            $normalized = $this->normalizeKey($key);
            if (array_key_exists($normalized, $row)) {
                $value = trim((string)$row[$normalized]);
                if ($value !== '') {
                    return $value;
                }
            }
        }
        return null;
    }

    private function saveClientPayload(array $payload): bool
    {
        $existingId = $this->existingClientId($payload);
        if ($existingId > 0) {
            $this->updateRow('clientes', $existingId, $this->withoutConflictingClientUniqueFields($payload, $existingId));
            return true;
        }

        try {
            $this->insertRow('clientes', $this->withoutConflictingClientUniqueFields($payload, 0));
            return true;
        } catch (\PDOException $exception) {
            if ((string)$exception->getCode() !== '23000') {
                throw $exception;
            }

            $retryId = $this->existingClientId($payload);
            if ($retryId > 0) {
                $this->updateRow('clientes', $retryId, $this->withoutConflictingClientUniqueFields($payload, $retryId));
                return true;
            }

            $fallback = $payload;
            unset($fallback['cnpj'], $fallback['cpf'], $fallback['email']);
            $fallback = $this->cleanPayload($fallback);
            if (empty($fallback['razao_social']) && empty($fallback['nome_fantasia'])) {
                return false;
            }
            $this->insertRow('clientes', $fallback);
            return true;
        }
    }

    private function withoutConflictingClientUniqueFields(array $payload, int $targetId): array
    {
        foreach (['cnpj', 'cpf', 'email'] as $field) {
            if (empty($payload[$field])) {
                continue;
            }
            $stmt = $this->db()->prepare("SELECT id FROM clientes WHERE {$field} = :value LIMIT 1");
            $stmt->execute(['value' => $payload[$field]]);
            $foundId = (int)($stmt->fetchColumn() ?: 0);
            if ($foundId > 0 && $foundId !== $targetId) {
                unset($payload[$field]);
            }
        }
        return $payload;
    }

    private function insertRow(string $table, array $payload): void
    {
        if (!$payload) {
            return;
        }
        $fields = array_keys($payload);
        $columns = implode(', ', array_map(fn($field) => '`'.$field.'`', $fields));
        $placeholders = implode(', ', array_map(fn($field) => ':'.$field, $fields));
        $stmt = $this->db()->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($payload);
    }

    private function updateRow(string $table, int $id, array $payload): void
    {
        if (!$payload) {
            return;
        }
        $sets = implode(', ', array_map(fn($field) => '`'.$field.'` = :'.$field, array_keys($payload)));
        $payload['id'] = $id;
        $stmt = $this->db()->prepare("UPDATE {$table} SET {$sets} WHERE id = :id");
        $stmt->execute($payload);
    }

    private function existingClientId(array $payload): int
    {
        foreach (['cnpj', 'cpf', 'email'] as $field) {
            if (empty($payload[$field])) {
                continue;
            }
            $stmt = $this->db()->prepare("SELECT id FROM clientes WHERE {$field} = :value LIMIT 1");
            $stmt->execute(['value' => $payload[$field]]);
            $id = (int)($stmt->fetchColumn() ?: 0);
            if ($id > 0) {
                return $id;
            }
        }
        return 0;
    }

    private function existingProductId(string $sku): int
    {
        $stmt = $this->db()->prepare('SELECT id FROM produtos WHERE sku = :sku LIMIT 1');
        $stmt->execute(['sku' => $sku]);
        return (int)($stmt->fetchColumn() ?: 0);
    }

    private function nextImportSku(string $tipo, int $offset): string
    {
        $prefix = $tipo === 'MP' ? 'MP-IMP-' : 'A-IMP-';
        return $prefix.date('ymdHis').'-'.str_pad((string)$offset, 3, '0', STR_PAD_LEFT);
    }

    private function tableColumns(string $table): array
    {
        $stmt = $this->db()->query('SHOW COLUMNS FROM '.$table);
        return array_values(array_filter(array_map(static fn($row) => (string)($row['Field'] ?? ''), $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [])));
    }

    private function cleanPayload(array $payload): array
    {
        $clean = [];
        foreach ($payload as $key => $value) {
            if (is_string($value)) {
                $value = trim($value);
            }
            if ($value === '' || $value === null) {
                $clean[$key] = null;
                continue;
            }
            $clean[$key] = $value;
        }
        return $clean;
    }

    private function normalizeKey(?string $value): string
    {
        $value = strtolower(trim((string)$value));
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if (is_string($converted) && $converted !== '') {
            $value = $converted;
        }
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;
        return trim($value);
    }

    private function digits(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string)$value);
        return $digits === '' ? null : $digits;
    }

    private function clientDocument(?string $value): array
    {
        $digits = $this->digits($value);
        if ($digits === null) {
            return ['cnpj' => null, 'cpf' => null];
        }

        return strlen($digits) <= 11
            ? ['cnpj' => null, 'cpf' => $digits]
            : ['cnpj' => $digits, 'cpf' => null];
    }

    private function cep(?string $value): ?string
    {
        $digits = $this->digits($value);
        if ($digits === null) {
            return null;
        }
        return strlen($digits) === 8 ? substr($digits, 0, 5).'-'.substr($digits, 5) : $digits;
    }

    private function decimal(?string $value): ?float
    {
        $value = trim((string)$value);
        if ($value === '' || $value === '-') {
            return null;
        }
        $value = str_replace(['R$', ' '], '', $value);
        if (strpos($value, ',') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
        return is_numeric($value) ? (float)$value : null;
    }

    private function integer(?string $value): ?int
    {
        $digits = $this->digits($value);
        return $digits === null ? null : (int)$digits;
    }

    private function contactType(?string $value): ?string
    {
        $value = strtolower(trim((string)$value));
        if ($value === '') {
            return null;
        }
        return str_contains($value, 'tel') ? 'telefone' : 'celular';
    }

    private function upper(?string $value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }
        return function_exists('mb_strtoupper') ? mb_strtoupper($value, 'UTF-8') : strtoupper($value);
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
