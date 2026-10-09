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
        $area_id = $_POST['area_id'] ?? '';
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false)
                  || !empty($_POST['is_ajax']);

        $rawInputs = [];
        if (!empty($_POST['names'])) {
            if (is_array($_POST['names'])) {
                $rawInputs = array_merge($rawInputs, $_POST['names']);
            } else {
                $rawInputs[] = $_POST['names'];
            }
        }
        if (!empty($_POST['name'])) {
            if (is_array($_POST['name'])) {
                $rawInputs = array_merge($rawInputs, $_POST['name']);
            } else {
                $rawInputs[] = $_POST['name'];
            }
        }

        $items = [];
        foreach ($rawInputs as $item) {
            if (is_string($item)) {
                // Strip outer delimiters and whitespace safely with regex
                $item = preg_replace('/^[\s,;।\x{0964}\x{0965}]+|[\s,;।\x{0964}\x{0965}]+$/u', '', $item);
                // Split by newline, comma, semicolon, and Bengali dāṛi with /u (UTF-8 unicode modifier)
                $splits = preg_split('/[\r\n,;।\x{0964}\x{0965}]+/u', $item);
                if (is_array($splits)) {
                    foreach ($splits as $s) {
                        $s = trim($s);
                        $s = preg_replace('/^[\s,;।\x{0964}\x{0965}]+|[\s,;।\x{0964}\x{0965}]+$/u', '', $s);
                        if ($s !== '') {
                            $items[] = $s;
                        }
                    }
                }
            }
        }

        // Deduplicate whitespace / empty items and duplicate entries in the same batch
        $items = array_values(array_unique(array_filter($items, function($val) {
            return trim($val) !== '';
        })));

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
        $ids = $_POST['ids'] ?? [];
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $area_id = $_POST['area_id'] ?? ($_SESSION['last_zone_area_id'] ?? '');
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false)
                  || !empty($_POST['is_ajax']);

        $zoneModel = new Zone();
        $deletedCount = 0;

        if (!empty($ids) && is_array($ids)) {
            $ids = array_values(array_filter(array_map('intval', $ids)));
            if (!empty($ids)) {
                $deletedCount = $zoneModel->deleteMultiple($ids);
            }
        } elseif ($id) {
            $zoneModel->delete(intval($id));
            $deletedCount = 1;
        }

        $msg = $deletedCount > 1 
            ? "একত্রে {$deletedCount} টি Zone মুছে ফেলা হয়েছে!" 
            : "Zone মুছে ফেলা হয়েছে!";

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $msg,
                'count' => $deletedCount,
                'ids' => !empty($ids) ? $ids : ($id ? [intval($id)] : [])
            ]);
            exit;
        }

        $redirectUrl = "{$base}/admin/zones" . ($area_id ? "?area_id={$area_id}&success=" . urlencode($msg) : "?success=" . urlencode($msg));
        header("Location: {$redirectUrl}");
        exit;
    }

    public function bulkDestroy() {
        return $this->destroy();
    }

    public function cleanCorrupted() {
        $area_id = $_POST['area_id'] ?? null;
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false)
                  || !empty($_POST['is_ajax']);

        $zoneModel = new Zone();
        $deletedCount = $zoneModel->deleteCorrupted($area_id);

        $msg = $deletedCount > 0 
            ? "মোট {$deletedCount} টি ত্রুটিপূর্ণ/অকেজো (??) Zone মুছে ফেলা হয়েছে!" 
            : "কোনো ত্রুটিপূর্ণ (??) Zone পাওয়া যায়নি।";

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $msg,
                'count' => $deletedCount
            ]);
            exit;
        }

        $redirectUrl = "{$base}/admin/zones" . ($area_id ? "?area_id={$area_id}&success=" . urlencode($msg) : "?success=" . urlencode($msg));
        header("Location: {$redirectUrl}");
        exit;
    }
}
