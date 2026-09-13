<?php

namespace Models;

use Core\Database;

class Category {
    protected $db;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
    }

    public function all() {
        // Fetch all categories
        $stmt = $this->db->query("SELECT c1.*, c2.name as parent_name 
                                  FROM categories c1 
                                  LEFT JOIN categories c2 ON c1.parent_id = c2.id 
                                  ORDER BY c1.name ASC");
        return $stmt->fetchAll();
    }
    
    public function findByName($name) {
        $stmt = $this->db->query("SELECT * FROM categories WHERE name = :name", ['name' => $name]);
        return $stmt->fetch();
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM categories WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->query("INSERT INTO categories (name, description, parent_id, image_path) VALUES (:name, :description, :parent_id, :image_path)", [
            'name' => $data['name'],
            'description' => $data['description'],
            'parent_id' => $data['parent_id'] ?: null,
            'image_path' => $data['image_path'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        if (isset($data['image_path'])) {
             $this->db->query("UPDATE categories SET name = :name, description = :description, parent_id = :parent_id, image_path = :image_path WHERE id = :id", [
                'name' => $data['name'],
                'description' => $data['description'],
                'parent_id' => $data['parent_id'] ?: null,
                'image_path' => $data['image_path'],
                'id' => $id
            ]);
        } else {
            $this->db->query("UPDATE categories SET name = :name, description = :description, parent_id = :parent_id WHERE id = :id", [
                'name' => $data['name'],
                'description' => $data['description'],
                'parent_id' => $data['parent_id'] ?: null,
                'id' => $id
            ]);
        }
    }

    public function delete($id) {
        // Validation: Check if products exist in category? 
        // For now, constraint set nulls, so safe to delete.
        $this->db->query("DELETE FROM categories WHERE id = :id", ['id' => $id]);
    }
}
