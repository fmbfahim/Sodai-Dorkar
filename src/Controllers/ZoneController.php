<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Zone;
use Models\Area;

class ZoneController extends Controller {
    
    public function __construct() {
        Middleware::permission('locations');
    }

    public function index() {
        $zoneModel = new Zone();
        $areaModel = new Area();

        $zones = $zoneModel->all();
        $areas = $areaModel->all();

        return $this->view('admin/zones/index', [
            'title' => 'Manage Zones (Wards)', 
            'zones' => $zones,
            'areas' => $areas
        ]);
    }

    public function store() {
        $name = $_POST['name'] ?? '';
        $area_id = $_POST['area_id'] ?? '';

        if ($name && $area_id) {
            $zoneModel = new Zone();
            $zoneModel->create([
                'name' => $name, 
                'area_id' => $area_id
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/zones');
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/zones');
            exit;
        }

        $zoneModel = new Zone();
        $areaModel = new Area();

        $zone = $zoneModel->find($id);
        $areas = $areaModel->all();

        return $this->view('admin/zones/edit', [
            'title' => 'Edit Zone', 
            'zone' => $zone,
            'areas' => $areas
        ]);
    }

    public function update() {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $area_id = $_POST['area_id'] ?? '';

        if ($id && $name && $area_id) {
            $zoneModel = new Zone();
            $zoneModel->update($id, [
                'name' => $name, 
                'area_id' => $area_id
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/zones');
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $zoneModel = new Zone();
            $zoneModel->delete($id);
        }
        header('Location: /sodai-dorkar/public/admin/zones');
        exit;
    }
}
