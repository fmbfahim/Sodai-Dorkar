<?php

namespace Core;

require_once __DIR__ . '/../helpers.php';

class View {
    public static function render($view, $data = []) {
        require_once __DIR__ . '/../helpers.php';
        extract($data);
        $viewPath = __DIR__ . '/../../views/' . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewPath)) {
            ob_start();
            require $viewPath;
            return ob_get_clean();
        } else {
            return "View {$view} not found!";
        }
    }
}
