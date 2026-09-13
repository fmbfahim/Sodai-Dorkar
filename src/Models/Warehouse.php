<?php

namespace Models;

use Core\Database;

class Warehouse {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM warehouses ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $this->db->query("INSERT INTO warehouses (name, location) VALUES (:name, :location)", [
            'name' => $data['name'],
            'location' => $data['location']
        ]);
        return $this->db->lastInsertId();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM warehouses WHERE id = :id", ['id' => $id]);
    }
}
