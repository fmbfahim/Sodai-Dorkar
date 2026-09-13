<?php

namespace Core;

class Router {
    protected $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function resolve() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Remove project base path if running in subdirectory (local or live)
        $path = str_replace('/sodai-dorkar/public', '', $path);
        $path = preg_replace('#^/public#', '', $path);
        $path = preg_replace('#^/index\.php#', '', $path);
        $path = rtrim($path, '/');
        if ($path === '' || $path === false) $path = '/';
        
        $method = $_SERVER['REQUEST_METHOD'];
        $callback = $this->routes[$method][$path] ?? false;

        // Apply global middleware (CSRF)
        if ($method === 'POST') {
            \Core\Middleware::verifyCsrf();
        }

        if ($callback === false) {
            http_response_code(404);
            echo "404 - Not Found";
            return;
        }

        if (is_string($callback)) {
            // Assume Controller@method format
            $parts = explode('@', $callback);
            $controller = "Controllers\\" . $parts[0];
            $method = $parts[1];
            
            // Check if controller exists, if not, try with full namespace or require it
            // For now assuming Composer psr-4 or manual require in index.php
            
            $controllerInstance = new $controller();
            echo $controllerInstance->$method();
        } else {
            echo call_user_func($callback);
        }
    }
}
