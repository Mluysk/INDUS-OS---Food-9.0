<?php
namespace App\Core;

use PDO;
use PDOException;

class Container
{
    private array $config;
    private array $instances = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function get(string $abstract)
    {
        if (!isset($this->instances[$abstract])) {
            if ($abstract === self::class) {
                return $this;
            }
            $this->instances[$abstract] = $this->resolve($abstract);
        }

        return $this->instances[$abstract];
    }

    private function resolve(string $abstract)
    {
        if ($abstract === PDO::class) {
            $dbConfig = require __DIR__.'/../config/database.php';
            $attempts = [
                [
                    'host' => (string)$dbConfig['host'],
                    'password' => (string)$dbConfig['password'],
                ],
            ];
            if (($dbConfig['password'] ?? '') === 'secret') {
                $attempts[] = [
                    'host' => (string)$dbConfig['host'],
                    'password' => '',
                ];
            }
            if (($dbConfig['host'] ?? '') === 'localhost') {
                $attempts[] = [
                    'host' => '127.0.0.1',
                    'password' => (string)$dbConfig['password'],
                ];
            }

            $lastException = null;
            foreach ($attempts as $attempt) {
                $dsn = sprintf('%s:host=%s;dbname=%s;charset=%s',
                    $dbConfig['driver'],
                    $attempt['host'],
                    $dbConfig['database'],
                    $dbConfig['charset']
                );
                try {
                    return new PDO($dsn, $dbConfig['username'], $attempt['password'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                } catch (PDOException $e) {
                    $lastException = $e;
                }
            }

            if ($lastException instanceof PDOException) {
                if (($this->config['debug'] ?? false) === true) {
                    throw $lastException;
                }
                throw new \RuntimeException('Database connection unavailable.');
            }
        }

        if (!class_exists($abstract)) {
            throw new \InvalidArgumentException("Class {$abstract} not found");
        }

        $reflection = new \ReflectionClass($abstract);
        if (!$reflection->getConstructor()) {
            return new $abstract();
        }

        $params = [];
        foreach ($reflection->getConstructor()->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type && !$type->isBuiltin()) {
                $params[] = $this->get($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $params[] = $parameter->getDefaultValue();
            } else {
                $params[] = null;
            }
        }

        return $reflection->newInstanceArgs($params);
    }
}
