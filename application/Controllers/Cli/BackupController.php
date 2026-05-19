<?php
namespace App\Controllers\Cli;

use App\Core\Controller;

class BackupController extends Controller
{
    public function run(): void
    {
        $dbConfig = require __DIR__.'/../../config/database.php';
        $timestamp = date('Ymd_His');
        $dbFile = __DIR__.'/../../../storage/backups/db/backup_'.$timestamp.'.sql';
        $filesArchive = __DIR__.'/../../../storage/backups/files/uploads_'.$timestamp.'.tar.gz';
        $commandDb = sprintf('mysqldump -h%s -u%s -p%s %s > %s',
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password']),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($dbFile)
        );
        system($commandDb);
        $commandFiles = sprintf('tar -czf %s -C %s .', escapeshellarg($filesArchive), escapeshellarg(__DIR__.'/../../../uploads'));
        system($commandFiles);
        $this->json(['message' => 'Backup concluído', 'db' => $dbFile, 'files' => $filesArchive]);
    }
}
