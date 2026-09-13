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
}
