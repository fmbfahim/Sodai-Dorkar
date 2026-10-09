<?php

namespace Models;

use Core\Database;

class Zone {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        $sql = "SELECT zones.*, areas.name as area_name 
                FROM zones 
                JOIN areas ON zones.area_id = areas.id 
                ORDER BY zones.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getByArea($areaId) {
        $sql = "SELECT * FROM zones WHERE area_id = :area_id ORDER BY name ASC";
        $stmt = $this->db->query($sql, ['area_id' => $areaId]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $this->db->query("INSERT INTO zones (name, area_id) VALUES (:name, :area_id)", [
            'name' => $data['name'],
            'area_id' => $data['area_id']
        ]);
        return $this->db->lastInsertId();
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT zones.*, areas.name as area_name FROM zones LEFT JOIN areas ON zones.area_id = areas.id WHERE zones.id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $this->db->query("UPDATE zones SET name = :name, area_id = :area_id WHERE id = :id", [
            'name' => $data['name'],
            'area_id' => $data['area_id'],
            'id' => $id
        ]);
    }

    public function delete($id) {
        $this->db->query("DELETE FROM zones WHERE id = :id", ['id' => $id]);
    }

    public function deleteMultiple(array $ids) {
        if (empty($ids)) return 0;
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->query("DELETE FROM zones WHERE id IN ($placeholders)", $ids);
        return $stmt->rowCount();
    }

    public function deleteCorrupted($areaId = null) {
        if ($areaId) {
            $stmt = $this->db->query("DELETE FROM zones WHERE area_id = :area_id AND (name = '??' OR name = '?' OR TRIM(name) = '' OR name LIKE '%?%')", ['area_id' => $areaId]);
        } else {
            $stmt = $this->db->query("DELETE FROM zones WHERE name = '??' OR name = '?' OR TRIM(name) = '' OR name LIKE '%?%'");
        }
        return $stmt->rowCount();
    }
}
