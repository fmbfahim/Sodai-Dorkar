<?php

namespace Core;

use Core\View;

class Controller {
    protected function view($view, $data = [], $layout = 'layouts/admin') {
        // Render the inner view content
        $content = View::render($view, $data);
        
        if ($layout === false) {
            return $content;
        }

        // Pass content and data to the layout
        $layoutData = array_merge($data, ['content' => $content]);
        return View::render($layout, $layoutData);
    }

    protected function redirect($path) {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            header("Location: {$path}");
            exit;
        }
        if (str_starts_with($path, '/sodai-dorkar/public')) {
            $path = substr($path, strlen('/sodai-dorkar/public'));
        }
        header("Location: {$base}{$path}");
        exit;
    }
}
