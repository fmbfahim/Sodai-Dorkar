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
    
    private static $slugColChecked = null;

    private function checkSlugColumn() {
        if (self::$slugColChecked !== null) return self::$slugColChecked;
        try {
            $col = $this->db->query("SHOW COLUMNS FROM categories LIKE 'slug'")->fetch();
            self::$slugColChecked = !empty($col);
        } catch (\Throwable $e) {
            self::$slugColChecked = false;
        }
        return self::$slugColChecked;
    }

    public function findByName($name) {
        $stmt = $this->db->query("SELECT * FROM categories WHERE name = :name LIMIT 1", ['name' => $name]);
        return $stmt->fetch();
    }

    public function findBySlug($slug) {
        if (!$this->checkSlugColumn()) return null;
        $stmt = $this->db->query("SELECT * FROM categories WHERE slug = :slug LIMIT 1", ['slug' => $slug]);
        return $stmt->fetch();
    }

    public function find($id) {
        $stmt = $this->db->query("SELECT * FROM categories WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $hasSlug = $this->checkSlugColumn();
        $name = trim($data['name'] ?? '');
        $slug = !empty($data['slug']) ? trim($data['slug']) : null;

        if ($hasSlug) {
            $this->db->query("INSERT INTO categories (name, slug, description, parent_id, image_path) VALUES (:name, :slug, :description, :parent_id, :image_path)", [
                'name' => $name,
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null,
                'image_path' => $data['image_path'] ?? null
            ]);
        } else {
            $this->db->query("INSERT INTO categories (name, description, parent_id, image_path) VALUES (:name, :description, :parent_id, :image_path)", [
                'name' => $name,
                'description' => $data['description'] ?? null,
                'parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null,
                'image_path' => $data['image_path'] ?? null
            ]);
        }
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $hasSlug = $this->checkSlugColumn();
        $fields = ['name = :name', 'description = :description', 'parent_id = :parent_id'];
        $params = [
            'id' => $id,
            'name' => trim($data['name'] ?? ''),
            'description' => $data['description'] ?? null,
            'parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null
        ];

        if ($hasSlug && isset($data['slug'])) {
            $fields[] = 'slug = :slug';
            $params['slug'] = trim($data['slug']);
        }

        if (array_key_exists('image_path', $data)) {
            $fields[] = 'image_path = :image_path';
            $params['image_path'] = $data['image_path'];
        }

        $sql = "UPDATE categories SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->db->query($sql, $params);
    }

    public function delete($id) {
        // Validation: Check if products exist in category? 
        // For now, constraint set nulls, so safe to delete.
        $this->db->query("DELETE FROM categories WHERE id = :id", ['id' => $id]);
    }
}
