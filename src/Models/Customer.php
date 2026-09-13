<?php

namespace Models;

use Core\Database;

class Customer {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all($filters = []) {
        $sql = "SELECT customers.*, 
                       areas.name as area_name,
                       zones.name as zone_name,
                       points.name as point_name
                FROM customers 
                LEFT JOIN areas ON customers.area_id = areas.id
                LEFT JOIN zones ON customers.zone_id = zones.id
                LEFT JOIN points ON customers.point_id = points.id
                WHERE 1=1";
        
        $params = [];
        
        // Search (ID, Name, Phone)
        if (!empty($filters['search'])) {
            $sql .= " AND (customers.unique_code LIKE :search 
                      OR customers.phone LIKE :search 
                      OR customers.name LIKE :search)";
            $params['search'] = "%" . $filters['search'] . "%";
        }

        // Filters
        if (!empty($filters['area_id'])) {
            $sql .= " AND customers.area_id = :area_id";
            $params['area_id'] = $filters['area_id'];
        }
        if (!empty($filters['zone_id'])) {
            $sql .= " AND customers.zone_id = :zone_id";
            $params['zone_id'] = $filters['zone_id'];
        }
        if (!empty($filters['point_id'])) {
            $sql .= " AND customers.point_id = :point_id";
            $params['point_id'] = $filters['point_id'];
        }
        
        $sql .= " ORDER BY customers.id DESC";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM customers WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $unique_code = $this->generateUniqueCode();
        
        $sql = "INSERT INTO customers (unique_code, name, phone, area_id, zone_id, point_id, address_details, demographics_json, latitude, longitude) 
                VALUES (:unique_code, :name, :phone, :area_id, :zone_id, :point_id, :address_details, :demographics_json, :latitude, :longitude)";
        
        $this->db->query($sql, [
            'unique_code' => $unique_code,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'area_id' => $data['area_id'],
            'zone_id' => $data['zone_id'] ?? null,
            'point_id' => $data['point_id'] ?? null,
            'address_details' => $data['address_details'],
            'demographics_json' => $data['demographics_json'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    private function generateUniqueCode() {
        // Simple unique code generation: SD-{YEAR}-{RAND}
        // e.g., SD-2026-8392
        do {
            $code = 'SD-' . date('Y') . '-' . mt_rand(1000, 9999);
            $stmt = $this->db->query("SELECT id FROM customers WHERE unique_code = :code", ['code' => $code]);
        } while ($stmt->rowCount() > 0);
        
        return $code;
    }

    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM customers");
        return $stmt->fetch()['count'] ?? 0;
    }

    public function update($id, $data) {
        $sql = "UPDATE customers SET 
                name = :name, 
                phone = :phone, 
                area_id = :area_id, 
                zone_id = :zone_id,
                point_id = :point_id,
                address_details = :address_details, 
                demographics_json = :demographics_json,
                latitude = :latitude,
                longitude = :longitude
                WHERE id = :id";
        
        $this->db->query($sql, [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'area_id' => $data['area_id'],
            'zone_id' => $data['zone_id'] ?? null,
            'point_id' => $data['point_id'] ?? null,
            'address_details' => $data['address_details'],
            'demographics_json' => $data['demographics_json'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'id' => $id
        ]);
    }

    public function delete($id) {
        $this->db->query("DELETE FROM customers WHERE id = :id", ['id' => $id]);
    }

    public function updateResetCode($id, $code) {
        $this->db->query("UPDATE customers SET reset_code = :code WHERE id = :id", ['code' => $code, 'id' => $id]);
    }
}
