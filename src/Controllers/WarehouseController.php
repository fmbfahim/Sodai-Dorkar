<?php

namespace Controllers;

use Core\Controller;
use Models\Warehouse;

use Core\Middleware;

class WarehouseController extends Controller {
    
    public function __construct() {
        Middleware::permission('locations');
    }
    
    public function index() {
        $warehouseModel = new Warehouse();
        $warehouses = $warehouseModel->all();
        return $this->view('admin/warehouses/index', ['title' => 'Warehouses', 'warehouses' => $warehouses]);
    }

    public function store() {
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';

        if ($name && $location) {
            $warehouseModel = new Warehouse();
            $warehouseModel->create(['name' => $name, 'location' => $location]);
        }
        
        header('Location: /sodai-dorkar/public/admin/warehouses');
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $warehouseModel = new Warehouse();
            $warehouseModel->delete($id);
        }
        header('Location: /sodai-dorkar/public/admin/warehouses');
        exit;
    }
}
