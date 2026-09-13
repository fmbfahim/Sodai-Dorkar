<?php

namespace Core;

class View {
    public static function render($view, $data = []) {
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
