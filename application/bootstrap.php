<?php
declare(strict_types=1);

use App\Core\Container;
use App\Core\Router;

/**
 * Carrega variáveis de ambiente a partir de um arquivo .env simples (KEY=VALUE).
 */
function loadEnvFile(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $name = trim($parts[0]);
        if ($name === '' || getenv($name) !== false) {
            continue;
        }

        $value = trim($parts[1]);
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

loadEnvFile(dirname(__DIR__) . '/.env');
loadEnvFile(dirname(__DIR__) . '/public/.env');

spl_autoload_register(function (string $class): void {
    if (!str_starts_with($class, 'App\\')) {
        return;
    }

    $relative = substr($class, 4);
    $relativePath = str_replace('\\', '/', $relative) . '.php';
    $basePath = __DIR__ . '/';

    $pathsToCheck = [
        $basePath . $relativePath,
        $basePath . lcfirst($relativePath),
    ];

    // attempt progressively lower-cased directory names to support legacy folders
    $segments = explode('/', $relativePath);
    if (count($segments) > 1) {
        $lastIndex = count($segments) - 1;
        $segmentsLower = [];
        foreach ($segments as $index => $segment) {
            $segmentsLower[] = $index === $lastIndex ? $segment : lcfirst($segment);
        }
        $pathsToCheck[] = $basePath . implode('/', $segmentsLower);
    }

    foreach (array_unique($pathsToCheck) as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once __DIR__.'/helpers/brasil_helper.php';
require_once __DIR__.'/helpers/pix_helper.php';
require_once __DIR__.'/helpers/units_helper.php';

$appConfig = require __DIR__.'/config/app.php';

date_default_timezone_set($appConfig['timezone']);

$container = new Container($appConfig);

return [
    'config' => $appConfig,
    'container' => $container,
    'router' => new Router($container),
];
