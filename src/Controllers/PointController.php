<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Point;
use Models\Zone;

class PointController extends Controller {
    
    public function __construct() {
        Middleware::permission('locations');
    }

    public function index() {
        $pointModel = new Point();
        $zoneModel = new Zone();

        $points = $pointModel->all();
        $zones = $zoneModel->all(); // Should optimize to get area name too for filter later

        return $this->view('admin/points/index', [
            'title' => 'Manage Points (Houses)', 
            'points' => $points,
            'zones' => $zones
        ]);
    }

    public function store() {
        $name = $_POST['name'] ?? '';
        $zone_id = $_POST['zone_id'] ?? '';

        if ($name && $zone_id) {
            $pointModel = new Point();
            $pointModel->create([
                'name' => $name, 
                'zone_id' => $zone_id
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/points');
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/points');
            exit;
        }

        $pointModel = new Point();
        $zoneModel = new Zone();

        $point = $pointModel->find($id);
        $zones = $zoneModel->all();

        return $this->view('admin/points/edit', [
            'title' => 'Edit Point', 
            'point' => $point,
            'zones' => $zones
        ]);
    }

    public function update() {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $zone_id = $_POST['zone_id'] ?? '';

        if ($id && $name && $zone_id) {
            $pointModel = new Point();
            $pointModel->update($id, [
                'name' => $name, 
                'zone_id' => $zone_id
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/points');
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $pointModel = new Point();
            $pointModel->delete($id);
        }
        header('Location: /sodai-dorkar/public/admin/points');
        exit;
    }
}
