<?php

namespace Models;

use Core\Database;

class Brand {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM brands ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM brands WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }
    
    public function findByName($name) {
        $stmt = $this->db->query("SELECT * FROM brands WHERE name = :name", ['name' => $name]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->query("INSERT INTO brands (name, image_path, country) VALUES (:name, :image_path, :country)", [
            'name' => $data['name'],
            'image_path' => $data['image_path'] ?? null,
            'country' => $data['country'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        // If image_path is provided, update it too
        if (isset($data['image_path'])) {
            $this->db->query("UPDATE brands SET name = :name, image_path = :image_path, country = :country WHERE id = :id", [
                'name' => $data['name'],
                'image_path' => $data['image_path'],
                'country' => $data['country'] ?? null,
                'id' => $id
            ]);
        } else {
             $this->db->query("UPDATE brands SET name = :name, country = :country WHERE id = :id", [
                'name' => $data['name'],
                'country' => $data['country'] ?? null,
                'id' => $id
            ]);
        }
    }

    public function delete($id) {
        $this->db->query("DELETE FROM brands WHERE id = :id", ['id' => $id]);
    }
}
