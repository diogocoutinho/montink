<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private string $notFoundHandler = 'ErrorController@notFound';

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, string $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, string $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function setNotFoundHandler(string $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            $pattern = $this->convertRouteToRegex($route['path']);

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove the full match
                // Reindex parameters to avoid named keys causing argument errors
                $matches = array_values($matches);

                [$controller, $action] = explode('@', $route['handler']);
                $controllerClass = "App\\Controllers\\{$controller}";

                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $action)) {
                        call_user_func_array([$controllerInstance, $action], $matches);
                        return;
                    }
                }
            }
        }

        // No route found
        [$controller, $action] = explode('@', $this->notFoundHandler);
        $controllerClass = "App\\Controllers\\{$controller}";
        $controllerInstance = new $controllerClass();
        $controllerInstance->$action();
    }

    private function convertRouteToRegex(string $route): string
    {
        // Convert route parameters to regex patterns (grupos normais)
        $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([^/]+)', $route);
        return "#^{$pattern}$#";
    }
}
