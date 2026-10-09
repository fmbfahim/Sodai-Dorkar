<?php

namespace Models;

use Core\Database;
use PDO;

class AgentAllocation {
    protected $db;
    private static $schemaEnsured = false;

    public function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
        $this->ensureSchema();
    }

    public function ensureSchema() {
        if (self::$schemaEnsured) return;
        self::$schemaEnsured = true;

        try {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `agent_allocations` (
                    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `user_id` INT(10) UNSIGNED NOT NULL,
                    `area_id` INT(10) UNSIGNED NOT NULL,
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_agent_allocation` (`user_id`, `area_id`),
                    KEY `area_id` (`area_id`),
                    CONSTRAINT `agent_allocations_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                    CONSTRAINT `agent_allocations_fk_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (\Throwable $e) {
            error_log("AgentAllocation::ensureSchema error: " . $e->getMessage());
        }
    }

    /**
     * Get all assigned area records for a given agent
     */
    public function getByAgent($agentId) {
        $sql = "SELECT aa.*, a.name as area_name, a.code as area_code
                FROM agent_allocations aa
                JOIN areas a ON aa.area_id = a.id
                WHERE aa.user_id = :agent_id
                ORDER BY a.name ASC";
        $stmt = $this->db->query($sql, ['agent_id' => $agentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get just array of area_ids assigned to an agent
     * @return int[]
     */
    public function getAreaIdsByAgent($agentId) {
        $sql = "SELECT area_id FROM agent_allocations WHERE user_id = :agent_id";
        $stmt = $this->db->query($sql, ['agent_id' => $agentId]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', $rows ?: []);
    }

    /**
     * Sync assigned areas for an agent (replaces previous assignments)
     * @param int $agentId
     * @param array $areaIds
     */
    public function syncAgentAreas($agentId, array $areaIds) {
        $pdo = $this->db->getConnection();
        try {
            $pdo->beginTransaction();

            $stmtDel = $pdo->prepare("DELETE FROM agent_allocations WHERE user_id = :agent_id");
            $stmtDel->execute(['agent_id' => $agentId]);

            if (!empty($areaIds)) {
                $stmtIns = $pdo->prepare("INSERT IGNORE INTO agent_allocations (user_id, area_id) VALUES (:agent_id, :area_id)");
                foreach ($areaIds as $areaId) {
                    $areaId = (int)$areaId;
                    if ($areaId > 0) {
                        $stmtIns->execute(['agent_id' => $agentId, 'area_id' => $areaId]);
                    }
                }
            }

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            error_log("syncAgentAreas error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add single allocation
     */
    public function create($agentId, $areaId) {
        return $this->db->query("INSERT IGNORE INTO agent_allocations (user_id, area_id) VALUES (:user_id, :area_id)", [
            'user_id' => $agentId,
            'area_id' => $areaId
        ]);
    }

    /**
     * Remove single allocation
     */
    public function delete($agentId, $areaId) {
        return $this->db->query("DELETE FROM agent_allocations WHERE user_id = :user_id AND area_id = :area_id", [
            'user_id' => $agentId,
            'area_id' => $areaId
        ]);
    }

    /**
     * Get all agents with their assigned areas list
     */
    public function getAgentsWithAreas() {
        $sql = "SELECT u.id, u.name, u.username, u.phone, u.email, u.status, u.created_at,
                       GROUP_CONCAT(DISTINCT a.name ORDER BY a.name SEPARATOR ', ') as area_names,
                       GROUP_CONCAT(DISTINCT a.id ORDER BY a.name SEPARATOR ',') as area_ids,
                       COALESCE(ord_stats.total_orders, 0) as total_orders,
                       COALESCE(ord_stats.total_sales, 0) as total_sales
                FROM users u
                LEFT JOIN agent_allocations aa ON aa.user_id = u.id
                LEFT JOIN areas a ON aa.area_id = a.id
                LEFT JOIN (
                    SELECT agent_id, 
                           COUNT(*) as total_orders, 
                           SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END) as total_sales 
                    FROM orders 
                    GROUP BY agent_id
                ) ord_stats ON ord_stats.agent_id = u.id
                WHERE u.role = 'agent'
                GROUP BY u.id
                ORDER BY u.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
