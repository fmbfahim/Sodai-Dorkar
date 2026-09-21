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

        try {
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
        } catch (\Throwable $e) {
            error_log("Router error on [{$method}] {$path}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            http_response_code(500);

            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['error'] = "সার্ভার ত্রুটি: " . $e->getMessage();
            }

            // If AJAX / JSON request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                return;
            }

            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
            echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Error - 500</title><meta name='viewport' content='width=device-width, initial-scale=1'></head><body style='font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; padding: 40px 20px; color: #1e293b;'>";
            echo "<div style='max-width: 600px; margin: 40px auto; background: white; border-radius: 16px; padding: 32px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); text-align: center;'>";
            echo "<div style='font-size: 48px; margin-bottom: 16px;'>⚠️</div>";
            echo "<h2 style='font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 8px;'>অনুরোধটি প্রক্রিয়া করার সময় একটি সমস্যা হয়েছে</h2>";
            echo "<p style='font-size: 13px; color: #dc2626; background: #fef2f2; padding: 12px; border-radius: 8px; border: 1px solid #fee2e2; margin-bottom: 24px; word-break: break-word;'>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<div style='display: flex; gap: 12px; justify-content: center;'>";
            if ($referer) {
                echo "<a href='" . htmlspecialchars($referer) . "' style='padding: 10px 20px; border-radius: 10px; background: #059669; color: white; text-decoration: none; font-weight: 600; font-size: 14px;'>পেছনে ফিরে যান</a>";
            }
            echo "<a href='{$base}/admin/products' style='padding: 10px 20px; border-radius: 10px; background: #f1f5f9; color: #334155; text-decoration: none; font-weight: 600; font-size: 14px;'>পণ্য তালিকায় ফিরে যান</a>";
            echo "</div></div></body></html>";
        }
    }
}
