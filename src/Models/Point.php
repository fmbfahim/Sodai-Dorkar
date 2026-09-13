<?php

namespace Models;

use Core\Database;

class Point {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        $sql = "SELECT points.*, zones.name as zone_name, areas.name as area_name 
                FROM points 
                JOIN zones ON points.zone_id = zones.id 
                JOIN areas ON zones.area_id = areas.id
                ORDER BY points.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getByZone($zoneId) {
        $sql = "SELECT * FROM points WHERE zone_id = :zone_id ORDER BY name ASC";
        $stmt = $this->db->query($sql, ['zone_id' => $zoneId]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $this->db->query("INSERT INTO points (name, zone_id) VALUES (:name, :zone_id)", [
            'name' => $data['name'],
            'zone_id' => $data['zone_id']
        ]);
        return $this->db->lastInsertId();
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM points WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $this->db->query("UPDATE points SET name = :name, zone_id = :zone_id WHERE id = :id", [
            'name' => $data['name'],
            'zone_id' => $data['zone_id'],
            'id' => $id
        ]);
    }

    public function delete($id) {
        $this->db->query("DELETE FROM points WHERE id = :id", ['id' => $id]);
    }
}
