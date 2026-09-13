<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Customer;
use Models\Area;
use Models\Zone;
use Models\Point;
use Models\Order;

class CustomerController extends Controller {
    
    public function __construct() {
        Middleware::auth(['admin', 'agent']); // Agents might need access too
    }

    public function index() {
        $customerModel = new Customer();
        
        // Filters
        $filters = [
            'search' => $_GET['q'] ?? '',
            'area_id' => $_GET['area_id'] ?? '',
            'zone_id' => $_GET['zone_id'] ?? '',
            'point_id' => $_GET['point_id'] ?? ''
        ];

        $customers = $customerModel->all($filters);
        
        // Dropdown Data
        $areaModel = new Area();
        $zoneModel = new Zone();
        $pointModel = new Point();

        // 1. Zones: If Area selected, show only belongs to Area. Else show all.
        $zones = $filters['area_id'] 
            ? $zoneModel->getByArea($filters['area_id']) 
            : $zoneModel->all();

        // 2. Points: If Zone selected, show only belongs to Zone. Else show all (or limit if needed).
        $points = $filters['zone_id']
            ? $pointModel->getByZone($filters['zone_id'])
            : $pointModel->all();

        return $this->view('admin/customers/index', [
            'title' => 'Customers', 
            'customers' => $customers,
            'filters' => $filters,
            'areas' => $areaModel->all(),
            'zones' => $zones,
            'points' => $points
        ]);
    }

    public function create() {
        $areaModel = new Area();
        $areas = $areaModel->all();

        $zoneModel = new Zone();
        $zones = $zoneModel->all(); // Ideally filtered by area in frontend
        
        $pointModel = new Point();
        $points = $pointModel->all(); // Ideally filtered by zone in frontend
        
        return $this->view('admin/customers/create', [
            'title' => 'Register Customer',
            'areas' => $areas,
            'zones' => $zones,
            'points' => $points
        ]);
    }

    public function store() {
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $area_id = $_POST['area_id'] ?? null;
        $zone_id = $_POST['zone_id'] ?? null;
        $point_id = $_POST['point_id'] ?? null;
        $address_details = $_POST['address_details'] ?? '';
        
        // Demographics
        $demographics = [
            'kids_count' => $_POST['kids_count'] ?? 0,
            'elderly_count' => $_POST['elderly_count'] ?? 0,
            'adult_count' => $_POST['adult_count'] ?? 0,
            'exhpat_count' => $_POST['exhpat_count'] ?? 0,
            'notes' => $_POST['notes'] ?? ''
        ];

        $latitude = $_POST['latitude'] ?? null;
        if ($latitude === '') $latitude = null;
        
        $longitude = $_POST['longitude'] ?? null;
        if ($longitude === '') $longitude = null;

        if ($name && $phone && $area_id) {
            $customerModel = new Customer();
            $customerModel->create([
                'name' => $name,
                'phone' => $phone,
                'area_id' => $area_id,
                'zone_id' => $zone_id,
                'point_id' => $point_id,
                'address_details' => $address_details,
                'demographics_json' => json_encode($demographics),
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/customers');
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/customers');
            exit;
        }

        $customerModel = new Customer();
        $customer = $customerModel->find($id);

        $areaModel = new Area();
        $areas = $areaModel->all();

        $zoneModel = new Zone();
        $zones = $zoneModel->all();
        
        $pointModel = new Point();
        $points = $pointModel->all();


        return $this->view('admin/customers/edit', [
            'title' => 'Edit Customer',
            'customer' => $customer,
            'areas' => $areas,
            'zones' => $zones,
            'points' => $points
        ]);
    }

    public function update() {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $area_id = $_POST['area_id'] ?? null;
        $zone_id = $_POST['zone_id'] ?? null;
        $point_id = $_POST['point_id'] ?? null;
        $address_details = $_POST['address_details'] ?? '';
        
        // Demographics
        $demographics = [
            'kids_count' => $_POST['kids_count'] ?? 0,
            'elderly_count' => $_POST['elderly_count'] ?? 0,
            'adult_count' => $_POST['adult_count'] ?? 0,
            'exhpat_count' => $_POST['exhpat_count'] ?? 0,
            'notes' => $_POST['notes'] ?? ''
        ];

        $latitude = $_POST['latitude'] ?? null;
        if ($latitude === '') $latitude = null;
        
        $longitude = $_POST['longitude'] ?? null;
        if ($longitude === '') $longitude = null;

        if ($id && $name && $phone && $area_id) {
            $customerModel = new Customer();
            $customerModel->update($id, [
                'name' => $name,
                'phone' => $phone,
                'area_id' => $area_id,
                'zone_id' => $zone_id,
                'point_id' => $point_id,
                'address_details' => $address_details,
                'demographics_json' => json_encode($demographics),
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/customers');
        exit;
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/customers');
            exit;
        }

        $customerModel = new Customer();
        $customer = $customerModel->find($id);

        if (!$customer) {
            header('Location: /sodai-dorkar/public/admin/customers');
            exit;
        }

        // Fetch location names manually since find() might only return IDs
        $config = require __DIR__ . '/../../config/database.php';
        $db = new \Core\Database($config);
        $stmt = $db->query("
            SELECT c.*, a.name as area_name, z.name as zone_name, p.name as point_name 
            FROM customers c
            LEFT JOIN areas a ON c.area_id = a.id
            LEFT JOIN zones z ON c.zone_id = z.id
            LEFT JOIN points p ON c.point_id = p.id
            WHERE c.id = :id
        ", ['id' => $id]);
        $customerData = $stmt->fetch();

        return $this->view('admin/customers/show', [
            'title' => 'Customer Profile',
            'customer' => $customerData
        ]);
    }

    public function history() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/customers');
            exit;
        }

        $customerModel = new Customer();
        $customer = $customerModel->find($id);

        $orderModel = new Order();
        $orders = $orderModel->getByCustomer($id);

        return $this->view('admin/customers/history', [
            'title' => 'Customer Order History',
            'customer' => $customer,
            'orders' => $orders
        ]);
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $customerModel = new Customer();
            $customerModel->delete($id);
        }
        header('Location: /sodai-dorkar/public/admin/customers');
        exit;
    }
    public function apiSearch() {
        $q = $_GET['q'] ?? '';
        if (strlen($q) < 1) {
            echo json_encode([]);
            exit;
        }

        $customerModel = new Customer();
        // Re-using all() but logic might need tweaking for pure JSON or we can use all()'s filter logic
        // all() returns everything if no filter, or searched if filter.
        // Let's use all(['search' => $q])
        $results = $customerModel->all(['search' => $q]);
        
        // Limit results for API to avoid huge payload
        $results = array_slice($results, 0, 10);
        
        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }

    public function generatePin() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $customerModel = new Customer();
            $customerModel->updateResetCode($id, $pin);
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/sodai-dorkar/public/admin/customers'));
        exit;
    }
}
