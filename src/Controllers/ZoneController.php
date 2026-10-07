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

        $selectedAreaId = $_GET['area_id'] ?? $_SESSION['last_zone_area_id'] ?? ($areas[0]['id'] ?? '');

        return $this->view('admin/zones/index', [
            'title' => 'Manage Zones (Wards)', 
            'zones' => $zones,
            'areas' => $areas,
            'selectedAreaId' => $selectedAreaId
        ]);
    }

    public function store() {
        $rawNames = $_POST['names'] ?? ($_POST['name'] ?? '');
        $area_id = $_POST['area_id'] ?? '';
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false)
                  || !empty($_POST['is_ajax']);

        $items = [];
        if (is_array($rawNames)) {
            foreach ($rawNames as $item) {
                if (is_string($item)) {
                    $splits = preg_split('/[\r\n,;।]+/', $item);
                    foreach ($splits as $s) {
                        $s = trim($s);
                        if ($s !== '') $items[] = $s;
                    }
                }
            }
        } elseif (is_string($rawNames)) {
            $splits = preg_split('/[\r\n,;।]+/', $rawNames);
            foreach ($splits as $s) {
                $s = trim($s);
                if ($s !== '') $items[] = $s;
            }
        }

        // Deduplicate whitespace / empty items
        $items = array_values(array_filter($items, function($val) {
            return trim($val) !== '';
        }));

        if (!empty($items) && $area_id) {
            $zoneModel = new Zone();
            $areaModel = new Area();
            $area = $areaModel->find($area_id);

            $addedZones = [];
            foreach ($items as $name) {
                $newId = $zoneModel->create([
                    'name' => $name, 
                    'area_id' => $area_id
                ]);
                $addedZones[] = [
                    'id' => $newId,
                    'name' => $name,
                    'area_id' => $area_id,
                    'area_name' => $area['name'] ?? ''
                ];
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['last_zone_area_id'] = $area_id;

            $addedCount = count($addedZones);
            $msg = $addedCount > 1 
                ? "একত্রে মোট {$addedCount} টি Zone (ওয়ার্ড) সফলভাবে যোগ করা হয়েছে!" 
                : "Zone (ওয়ার্ড) সফলভাবে যোগ করা হয়েছে!";

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => $msg,
                    'count' => $addedCount,
                    'zones' => $addedZones,
                    'area_id' => $area_id
                ]);
                exit;
            }

            header("Location: {$base}/admin/zones?area_id={$area_id}&success=" . urlencode($msg));
            exit;
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'দয়া করে অন্তত একটি জোনের নাম ও এরিয়া নির্বাচন করুন।'
            ]);
            exit;
        }
        
        header("Location: {$base}/admin/zones" . ($area_id ? "?area_id={$area_id}" : ""));
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        if (!$id) {
            header("Location: {$base}/admin/zones");
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
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        if ($id && $name && $area_id) {
            $zoneModel = new Zone();
            $zoneModel->update($id, [
                'name' => trim($name), 
                'area_id' => $area_id
            ]);
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['last_zone_area_id'] = $area_id;
            header("Location: {$base}/admin/zones?area_id={$area_id}&success=" . urlencode("Zone সফলভাবে আপডেট করা হয়েছে!"));
            exit;
        }
        
        header("Location: {$base}/admin/zones");
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $area_id = $_POST['area_id'] ?? ($_SESSION['last_zone_area_id'] ?? '');

        if ($id) {
            $zoneModel = new Zone();
            $zoneModel->delete($id);
        }
        $redirectUrl = "{$base}/admin/zones" . ($area_id ? "?area_id={$area_id}&success=" . urlencode("Zone মুছে ফেলা হয়েছে!") : "?success=" . urlencode("Zone মুছে ফেলা হয়েছে!"));
        header("Location: {$redirectUrl}");
        exit;
    }
}
