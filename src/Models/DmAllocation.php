<?php

namespace Models;

use Core\Database;

class DmAllocation {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        $sql = "SELECT dm_allocations.*, 
                       users.name as dm_name, 
                       areas.name as area_name 
                FROM dm_allocations 
                JOIN users ON dm_allocations.user_id = users.id 
                JOIN areas ON dm_allocations.area_id = areas.id 
                ORDER BY dm_allocations.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $this->db->query("INSERT INTO dm_allocations (user_id, area_id, time_slot) VALUES (:user_id, :area_id, :time_slot)", [
            'user_id' => $data['user_id'],
            'area_id' => $data['area_id'],
            'time_slot' => $data['time_slot']
        ]);
        return $this->db->lastInsertId();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM dm_allocations WHERE id = :id", ['id' => $id]);
    }
    
    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM dm_allocations WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function getAll() {
         $sql = "SELECT dm_allocations.*, users.name as user_name, areas.name as area_name 
                FROM dm_allocations 
                JOIN users ON dm_allocations.user_id = users.id 
                JOIN areas ON dm_allocations.area_id = areas.id
                ORDER BY areas.name ASC, users.name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $this->db->query("UPDATE dm_allocations SET user_id = :user_id, area_id = :area_id, time_slot = :time_slot WHERE id = :id", [
            'user_id' => $data['user_id'],
            'area_id' => $data['area_id'],
            'time_slot' => $data['time_slot'],
            'id' => $id
        ]);
    }
    
    public function getByDm($dmId) {
         $sql = "SELECT dm_allocations.*, areas.name as area_name 
                FROM dm_allocations 
                JOIN areas ON dm_allocations.area_id = areas.id 
                WHERE dm_allocations.user_id = :dm_id";
        $stmt = $this->db->query($sql, ['dm_id' => $dmId]);
        return $stmt->fetchAll();
    }

    public function getRidersForArea($areaId, $timeSlot = null) {
        $sql = "SELECT users.id, users.name, users.phone, dm_allocations.time_slot 
                FROM dm_allocations 
                JOIN users ON dm_allocations.user_id = users.id 
                WHERE dm_allocations.area_id = :area_id 
                AND users.role = 'delivery_man'";
        $params = ['area_id' => $areaId];

        if ($timeSlot) {
            $sql .= " AND (dm_allocations.time_slot = 'all_time' OR dm_allocations.time_slot = 'both' OR dm_allocations.time_slot = :slot)";
            $params['slot'] = $timeSlot;
        }

        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
}
