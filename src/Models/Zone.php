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
        $stmt = $this->db->query("SELECT * FROM zones WHERE id = :id", ['id' => $id]);
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
}
