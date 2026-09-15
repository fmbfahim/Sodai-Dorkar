<?php

namespace Controllers;

use Core\Controller;
use Models\Brand;

use Core\Middleware;

class BrandController extends Controller {
    
    public function __construct() {
        Middleware::permission('categories_brands');
    }
    
    public function index() {
        $brandModel = new Brand();
        $brands = $brandModel->all();

        return $this->view('admin/brands/index', [
            'title' => 'Brands', 
            'brands' => $brands
        ]);
    }

    public function store() {
        $name = $_POST['name'] ?? '';
        $country = $_POST['country'] ?? '';
        $image_path = null;
        
        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (in_array($mime, $allowedMimeTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/brands/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image_path = '/sodai-dorkar/public/uploads/brands/' . $fileName;
                }
            }
        }

        if ($name) {
            $brandModel = new Brand();
            $brandModel->create([
                'name' => $name,
                'image_path' => $image_path,
                'country' => $country
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/brands');
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/brands');
            exit;
        }

        $brandModel = new Brand();
        $brand = $brandModel->find($id);

        return $this->view('admin/brands/edit', [
            'title' => 'Edit Brand', 
            'brand' => $brand
        ]);
    }

    public function update() {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $country = $_POST['country'] ?? '';
        
        $data = [
            'name' => $name,
            'country' => $country
        ];

        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (in_array($mime, $allowedMimeTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/brands/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                     $data['image_path'] = '/sodai-dorkar/public/uploads/brands/' . $fileName;
                }
            }
        }

        if ($id && $name) {
            $brandModel = new Brand();
            $brandModel->update($id, $data);
        }
        
        header('Location: /sodai-dorkar/public/admin/brands');
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $brandModel = new Brand();
            $brandModel->delete($id);
        }
        header('Location: /sodai-dorkar/public/admin/brands');
        exit;
    }
}
