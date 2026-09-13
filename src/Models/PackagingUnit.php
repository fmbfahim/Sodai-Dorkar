<?php

namespace Models;

use Core\Database;

class PackagingUnit {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM packaging_units ORDER BY base_unit ASC, default_qty DESC, name ASC");
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM packaging_units WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->query("INSERT INTO packaging_units (name, base_unit, default_qty, is_default) VALUES (:name, :base_unit, :default_qty, :is_default)", [
            'name' => trim($data['name']),
            'base_unit' => trim($data['base_unit'] ?? 'kg'),
            'default_qty' => floatval($data['default_qty'] ?? 1.000),
            'is_default' => !empty($data['is_default']) ? 1 : 0
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $this->db->query("UPDATE packaging_units SET name = :name, base_unit = :base_unit, default_qty = :default_qty, is_default = :is_default WHERE id = :id", [
            'name' => trim($data['name']),
            'base_unit' => trim($data['base_unit'] ?? 'kg'),
            'default_qty' => floatval($data['default_qty'] ?? 1.000),
            'is_default' => !empty($data['is_default']) ? 1 : 0,
            'id' => $id
        ]);
    }

    public function delete($id) {
        $this->db->query("DELETE FROM packaging_units WHERE id = :id", ['id' => $id]);
    }
}
