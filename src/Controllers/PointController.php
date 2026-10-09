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
        $zone_id = $_POST['zone_id'] ?? '';
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
        $ids = $_POST['ids'] ?? [];
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $zone_id = $_POST['zone_id'] ?? ($_SESSION['last_point_zone_id'] ?? '');
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false)
                  || !empty($_POST['is_ajax']);

        $pointModel = new Point();
        $deletedCount = 0;

        if (!empty($ids) && is_array($ids)) {
            $ids = array_values(array_filter(array_map('intval', $ids)));
            if (!empty($ids)) {
                $deletedCount = $pointModel->deleteMultiple($ids);
            }
        } elseif ($id) {
            $pointModel->delete(intval($id));
            $deletedCount = 1;
        }

        $msg = $deletedCount > 1 
            ? "একত্রে {$deletedCount} টি Point মুছে ফেলা হয়েছে!" 
            : "Point মুছে ফেলা হয়েছে!";

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

        $redirectUrl = "{$base}/admin/points" . ($zone_id ? "?zone_id={$zone_id}&success=" . urlencode($msg) : "?success=" . urlencode($msg));
        header("Location: {$redirectUrl}");
        exit;
    }

    public function bulkDestroy() {
        return $this->destroy();
    }

    public function cleanCorrupted() {
        $zone_id = $_POST['zone_id'] ?? null;
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false)
                  || !empty($_POST['is_ajax']);

        $pointModel = new Point();
        $deletedCount = $pointModel->deleteCorrupted($zone_id);

        $msg = $deletedCount > 0 
            ? "মোট {$deletedCount} টি ত্রুটিপূর্ণ/অকেজো (??) Point মুছে ফেলা হয়েছে!" 
            : "কোনো ত্রুটিপূর্ণ (??) Point পাওয়া যায়নি।";

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $msg,
                'count' => $deletedCount
            ]);
            exit;
        }

        $redirectUrl = "{$base}/admin/points" . ($zone_id ? "?zone_id={$zone_id}&success=" . urlencode($msg) : "?success=" . urlencode($msg));
        header("Location: {$redirectUrl}");
        exit;
    }
}
