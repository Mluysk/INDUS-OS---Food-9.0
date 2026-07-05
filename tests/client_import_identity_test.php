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
$clientIdentity = new ReflectionMethod(ConfigDataController::class, 'clientImportIdentity');
$clientIdentity->setAccessible(true);
$preparePayload = new ReflectionMethod(ConfigDataController::class, 'prepareClientImportPayload');
$preparePayload->setAccessible(true);

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

    $firstResult = $saveClient->invoke($controller, $base + [
        'razao_social' => 'Cliente importado A',
        'cnpj' => $firstCnpj,
    ]);
    $secondResult = $saveClient->invoke($controller, $base + [
        'razao_social' => 'Cliente importado B',
        'cnpj' => $secondCnpj,
    ]);
    if ($firstResult !== 'inserted' || $secondResult !== 'inserted') {
        throw new RuntimeException('A importacao nao informou corretamente os clientes novos.');
    }

    $firstIdentity = $clientIdentity->invoke($controller, ['cnpj' => $firstCnpj]);
    if ($firstIdentity !== 'cnpj:'.$firstCnpj) {
        throw new RuntimeException('A identidade usada para detectar linhas duplicadas esta incorreta.');
    }

    $blankPayload = $preparePayload->invoke(
        $controller,
        ['contato_tipo' => 'celular'],
        ['tipo do contato' => 'celular', 'criado em' => '2026-07-05 10:00:00'],
        2
    );
    if ($blankPayload !== []) {
        throw new RuntimeException('Uma linha vazia foi transformada em cliente.');
    }

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
