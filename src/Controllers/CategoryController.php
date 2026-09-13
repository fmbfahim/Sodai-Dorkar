<?php

namespace Controllers;

use Core\Controller;
use Models\Category;

class CategoryController extends Controller {
    
    public function index() {
        $categoryModel = new Category();
        $allCategories = $categoryModel->all();

        // 1. Build Tree
        $tree = $this->buildTree($allCategories);

        // 2. Flatten for display with depth
        $categories = $this->flattenTree($tree);

        return $this->view('admin/categories/index', [
            'title' => 'Product Categories', 
            'categories' => $categories,
            'allCategories' => $allCategories // Raw list for dropdown (though flattened with depth could also be used for better dropdowns)
        ]);
    }

    private function buildTree(array $elements, $parentId = null) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    private function flattenTree($tree, $depth = 0) {
        $result = [];
        foreach ($tree as $node) {
            $node['depth'] = $depth;
            $children = $node['children'] ?? [];
            unset($node['children']); // Remove children from flat node to save memory/confusion
            $result[] = $node;
            if (!empty($children)) {
                $result = array_merge($result, $this->flattenTree($children, $depth + 1));
            }
        }
        return $result;
    }

    public function store() {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $parent_id = $_POST['parent_id'] ?? null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (in_array($mime, $allowedMimeTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/categories/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image_path = '/sodai-dorkar/public/uploads/categories/' . $fileName;
                }
            }
        }

        if ($name) {
            $categoryModel = new Category();
            $categoryModel->create([
                'name' => $name,
                'parent_id' => $parent_id,
                'description' => $description,
                'image_path' => $image_path ?? null
            ]);
        }
        
        

        
        $redirectUrl = '/sodai-dorkar/public/admin/categories';
        if ($parent_id) {
            $redirectUrl .= '?parent_id=' . urlencode($parent_id);
        }
        
        header('Location: ' . $redirectUrl);
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/categories');
            exit;
        }

        $categoryModel = new Category();
        $category = $categoryModel->find($id);
        $categories = $categoryModel->all(); // For parent selection

        return $this->view('admin/categories/edit', [
            'title' => 'Edit Category', 
            'category' => $category,
            'categories' => $categories // Pass all categories to populate parent dropdown
        ]);
    }

    public function update() {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $parent_id = $_POST['parent_id'] ?? null;

        $data = [
            'name' => $name,
            'parent_id' => $parent_id,
            'description' => $description
        ];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (in_array($mime, $allowedMimeTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/categories/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                     $data['image_path'] = '/sodai-dorkar/public/uploads/categories/' . $fileName;
                }
            }
        }

        if ($id && $name) {
            $categoryModel = new Category();
            $categoryModel->update($id, $data);
        }
        
        header('Location: /sodai-dorkar/public/admin/categories');
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            try {
                $categoryModel = new Category();
                $categoryModel->delete($id);
            } catch (\PDOException $e) {
                header('Location: /sodai-dorkar/public/admin/categories?error=cannot_delete');
                exit;
            }
        }
        header('Location: /sodai-dorkar/public/admin/categories');
        exit;
    }
}
