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

    public function deleteMultiple(array $ids) {
        if (empty($ids)) return 0;
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->query("DELETE FROM points WHERE id IN ($placeholders)", $ids);
        return $stmt->rowCount();
    }

    public function deleteCorrupted($zoneId = null) {
        if ($zoneId) {
            $stmt = $this->db->query("DELETE FROM points WHERE zone_id = :zone_id AND (name = '??' OR name = '?' OR TRIM(name) = '' OR name LIKE '%?%')", ['zone_id' => $zoneId]);
        } else {
            $stmt = $this->db->query("DELETE FROM points WHERE name = '??' OR name = '?' OR TRIM(name) = '' OR name LIKE '%?%'");
        }
        return $stmt->rowCount();
    }
}
