<?php
namespace App\Services;

use ZipArchive;

class BackupService
{
    private ConfigService $config;
    private \PDO $pdo;

    public function __construct(ConfigService $config, \PDO $pdo)
    {
        $this->config = $config;
        $this->pdo = $pdo;
    }

    /**
     * Cria um backup com base nas seleções de módulo ou do modo completo.
     *
     * @param bool   $full     se true exporta todas as tabelas disponíveis
     * @param string[] $modules módulos selecionados para backup granular
     * @param string $destination caminho relativo (ex.: storage/backups/uploads)
     * @return array{path:string, relative:string, tables:string[]}
     */
    public function run(bool $full, array $modules, string $destination): array
    {
        $modules = array_values(array_filter(array_map('strtolower', $modules)));
        $tables = $this->resolveTables($full, $modules);
        if (empty($tables)) {
            throw new \RuntimeException('Nenhuma tabela elegível para backup.');
        }

        $cleanDest = ltrim(str_replace('..', '', trim($destination)), '/');
        if ($cleanDest === '') {
            $cleanDest = 'storage/backups/uploads';
        }

        $root = dirname(__DIR__, 2);
        $targetDir = $root.'/'.$cleanDest;
        if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
            throw new \RuntimeException('Não foi possível preparar o diretório de destino do backup.');
        }

        $timestamp = date('Ymd_His');
        $fileName = sprintf('backup_%s.zip', $timestamp);
        $fullPath = rtrim($targetDir, '/').'/'.$fileName;

        $zip = new ZipArchive();
        if ($zip->open($fullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Falha ao inicializar o arquivo de backup.');
        }

        $zip->addFromString('manifest.json', json_encode([
            'generated_at' => $timestamp,
            'full' => $full,
            'modules' => $modules,
            'tables' => $tables,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        foreach ($tables as $table) {
            try {
                $stmt = $this->pdo->query('SELECT * FROM `'.$table.'`');
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $zip->addFromString($table.'.json', json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } catch (\Throwable $exception) {
                // registra erro no manifest para tabelas com falha
                $zip->addFromString($table.'.error.log', $exception->getMessage());
            }
        }

        $zip->close();

        $relative = $cleanDest.'/'.$fileName;
        $this->config->set('BACKUP_LAST_FILE', $relative);

        return [
            'path' => $fullPath,
            'relative' => $relative,
            'tables' => $tables,
        ];
    }

    /**
     * @param bool $full
     * @param string[] $modules
     * @return string[]
     */
    private function resolveTables(bool $full, array $modules): array
    {
        if ($full) {
            try {
                $stmt = $this->pdo->query('SHOW TABLES');
                $list = $stmt->fetchAll(\PDO::FETCH_NUM);
                if ($list) {
                    return array_values(array_unique(array_map(static fn($row) => (string)$row[0], $list)));
                }
            } catch (\Throwable $exception) {
                // fallback para lista conhecida
            }
        }

        $map = [
            'auth' => ['usuarios', 'audit_logs'],
            'clientes' => ['clientes'],
            'produtos' => ['produtos', 'produto_componentes'],
            'producao' => ['ordens_producao'],
            'estoque' => ['estoques', 'movimentos_estoque'],
            'expedicao' => ['expedicoes'],
            'financeiro' => ['financeiro_titulos', 'financeiro_lancamentos'],
            'relatorios' => [],
            'config' => ['configuracoes'],
            'portal' => ['clientes', 'usuarios', 'pedidos', 'pedido_itens'],
            'pedidos' => ['pedidos', 'pedido_itens'],
        ];

        $tables = [];
        foreach ($modules as $module) {
            if (isset($map[$module])) {
                $tables = array_merge($tables, $map[$module]);
            }
        }

        if (empty($tables)) {
            // Fallback mínimo
            $tables = ['configuracoes'];
        }

        return array_values(array_unique($tables));
    }
}
