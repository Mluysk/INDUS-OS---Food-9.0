<?php
declare(strict_types=1);

use App\Controllers\ConfigDataController;

ini_set('session.save_path', sys_get_temp_dir());

$bootstrap = require dirname(__DIR__).'/application/bootstrap.php';
$container = $bootstrap['container'];
$controller = $container->get(ConfigDataController::class);
$pdo = $container->get(PDO::class);

$saveClient = new ReflectionMethod(ConfigDataController::class, 'saveClientPayload');
$saveClient->setAccessible(true);

$suffix = str_pad((string)random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
$firstCnpj = '91'.substr($suffix, 0, 12);
$secondCnpj = '92'.substr($suffix, 0, 12);
$sharedEmail = 'import-identity-'.$suffix.'@example.test';

$pdo->beginTransaction();
try {
    $base = [
        'nome_fantasia' => 'Teste de importacao',
        'email' => $sharedEmail,
        'contato_tipo' => 'celular',
    ];

    $saveClient->invoke($controller, $base + [
        'razao_social' => 'Cliente importado A',
        'cnpj' => $firstCnpj,
    ]);
    $saveClient->invoke($controller, $base + [
        'razao_social' => 'Cliente importado B',
        'cnpj' => $secondCnpj,
    ]);

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM clientes WHERE cnpj IN (:first, :second)');
    $stmt->execute(['first' => $firstCnpj, 'second' => $secondCnpj]);
    $count = (int)$stmt->fetchColumn();
    if ($count !== 2) {
        throw new RuntimeException('A importacao mesclou clientes diferentes que possuem o mesmo e-mail.');
    }

    echo "OK: clientes com o mesmo e-mail e documentos diferentes permanecem separados.\n";
} finally {
    $pdo->rollBack();
}
