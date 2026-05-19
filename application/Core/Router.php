<?php
namespace App\Core;

class Router
{
    private Container $container;
    private array $routes = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function add(string $method, string $uri, callable|array $action): void
    {
        $this->routes[strtoupper($method)][$this->normalize($uri)] = $action;
    }

    public function dispatch(string $method, string $uri)
    {
        $uri = strtok($uri, '?');
        $normalized = $this->normalize($uri);
        $method = strtoupper($method);
        $routes = $this->routes[$method] ?? [];

        if (isset($routes[$normalized])) {
            return $this->call($routes[$normalized]);
        }

        foreach ($routes as $route => $action) {
            $pattern = '#^' . preg_replace('#\{[^/]+\}#', '([^/]+)', $route) . '$#';
            if (preg_match($pattern, $normalized, $matches)) {
                array_shift($matches);
                return $this->call($action, $matches);
            }
        }

        http_response_code(404);
        echo json_encode(['message' => 'Route not found']);
        return null;
    }

    private function call(callable|array $action, array $params = [])
    {
        if (is_array($action)) {
            [$class, $method] = $action;
            $instance = $this->container->get($class);
            return $instance->$method(...$params);
        }

        return $action(...$params);
    }

    private function normalize(string $uri): string
    {
        if ($uri === '') {
            $uri = '/';
        }

        // remove front-controller fragments (e.g. /index.php/login)
        if (str_starts_with($uri, '/index.php')) {
            $uri = substr($uri, strlen('/index.php')) ?: '/';
        }

        $uri = '/' . trim($uri, '/');
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }
}
