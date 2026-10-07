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
        $zones = $zoneModel->all();

        $selectedZoneId = $_GET['zone_id'] ?? $_SESSION['last_point_zone_id'] ?? ($zones[0]['id'] ?? '');

        return $this->view('admin/points/index', [
            'title' => 'Manage Points (Houses)', 
            'points' => $points,
            'zones' => $zones,
            'selectedZoneId' => $selectedZoneId
        ]);
    }

    public function store() {
        $rawNames = $_POST['names'] ?? ($_POST['name'] ?? '');
        $zone_id = $_POST['zone_id'] ?? '';
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

        if (!empty($items) && $zone_id) {
            $pointModel = new Point();
            $zoneModel = new Zone();
            $zone = $zoneModel->find($zone_id);

            $addedPoints = [];
            foreach ($items as $name) {
                $newId = $pointModel->create([
                    'name' => $name, 
                    'zone_id' => $zone_id
                ]);
                $addedPoints[] = [
                    'id' => $newId,
                    'name' => $name,
                    'zone_id' => $zone_id,
                    'zone_name' => $zone['name'] ?? '',
                    'area_name' => $zone['area_name'] ?? ''
                ];
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['last_point_zone_id'] = $zone_id;

            $addedCount = count($addedPoints);
            $msg = $addedCount > 1 
                ? "একত্রে মোট {$addedCount} টি Point (বাড়ি) সফলভাবে যোগ করা হয়েছে!" 
                : "Point (বাড়ি) সফলভাবে যোগ করা হয়েছে!";

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => $msg,
                    'count' => $addedCount,
                    'points' => $addedPoints,
                    'zone_id' => $zone_id
                ]);
                exit;
            }

            header("Location: {$base}/admin/points?zone_id={$zone_id}&success=" . urlencode($msg));
            exit;
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'দয়া করে অন্তত একটি পয়েন্টের নাম ও জোন নির্বাচন করুন।'
            ]);
            exit;
        }
        
        header("Location: {$base}/admin/points" . ($zone_id ? "?zone_id={$zone_id}" : ""));
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        if (!$id) {
            header("Location: {$base}/admin/points");
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
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        if ($id && $name && $zone_id) {
            $pointModel = new Point();
            $pointModel->update($id, [
                'name' => trim($name), 
                'zone_id' => $zone_id
            ]);
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['last_point_zone_id'] = $zone_id;
            header("Location: {$base}/admin/points?zone_id={$zone_id}&success=" . urlencode("Point সফলভাবে আপডেট করা হয়েছে!"));
            exit;
        }
        
        header("Location: {$base}/admin/points");
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $zone_id = $_POST['zone_id'] ?? ($_SESSION['last_point_zone_id'] ?? '');

        if ($id) {
            $pointModel = new Point();
            $pointModel->delete($id);
        }
        $redirectUrl = "{$base}/admin/points" . ($zone_id ? "?zone_id={$zone_id}&success=" . urlencode("Point মুছে ফেলা হয়েছে!") : "?success=" . urlencode("Point মুছে ফেলা হয়েছে!"));
        header("Location: {$redirectUrl}");
        exit;
    }
}
