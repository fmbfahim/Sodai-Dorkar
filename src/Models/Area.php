<?php

namespace Models;

use Core\Database;

class Area {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        // Join with warehouses to get warehouse name
        $sql = "SELECT areas.*, warehouses.name as warehouse_name 
                FROM areas 
                LEFT JOIN warehouses ON areas.warehouse_id = warehouses.id 
                ORDER BY areas.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $this->db->query("INSERT INTO areas (name, warehouse_id) VALUES (:name, :warehouse_id)", [
            'name' => $data['name'],
            'warehouse_id' => $data['warehouse_id']
        ]);
        return $this->db->lastInsertId();
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM areas WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $this->db->query("UPDATE areas SET name = :name, warehouse_id = :warehouse_id WHERE id = :id", [
            'name' => $data['name'],
            'warehouse_id' => $data['warehouse_id'],
            'id' => $id
        ]);
    }

    public function delete($id) {
        $this->db->query("DELETE FROM areas WHERE id = :id", ['id' => $id]);
    }
}
