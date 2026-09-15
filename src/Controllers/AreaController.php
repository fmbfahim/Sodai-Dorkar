<?php

namespace Controllers;

use Core\Controller;
use Models\Area;
use Models\Warehouse;

use Core\Middleware;

class AreaController extends Controller {
    
    public function __construct() {
        Middleware::permission('locations');
    }
    
    public function index() {
        $areaModel = new Area();
        $warehouseModel = new Warehouse();

        $areas = $areaModel->all();
        $warehouses = $warehouseModel->all();

        return $this->view('admin/areas/index', [
            'title' => 'Areas', 
            'areas' => $areas,
            'warehouses' => $warehouses
        ]);
    }

    public function store() {
        $name = $_POST['name'] ?? '';
        $warehouse_id = $_POST['warehouse_id'] ?? '';

        if ($name && $warehouse_id) {
            $areaModel = new Area();
            $areaModel->create([
                'name' => $name, 
                'warehouse_id' => $warehouse_id
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/areas');
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/areas');
            exit;
        }

        $areaModel = new Area();
        $warehouseModel = new Warehouse();

        $area = $areaModel->find($id);
        $warehouses = $warehouseModel->all();

        return $this->view('admin/areas/edit', [
            'title' => 'Edit Area', 
            'area' => $area,
            'warehouses' => $warehouses
        ]);
    }

    public function update() {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $warehouse_id = $_POST['warehouse_id'] ?? '';

        if ($id && $name && $warehouse_id) {
            $areaModel = new Area();
            $areaModel->update($id, [
                'name' => $name, 
                'warehouse_id' => $warehouse_id
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/areas');
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $areaModel = new Area();
            $areaModel->delete($id);
        }
        header('Location: /sodai-dorkar/public/admin/areas');
        exit;
    }
}
