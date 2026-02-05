<?php
namespace App\Services\Genealogy;

use App\Models\Database\DB;
use App\Services\Genealogy\GenealogyServiceInterface;
use Exception;

class GenealogyService implements GenealogyServiceInterface {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function linkMember(int $uplineId, int $downlineId): bool {
        try {
            // Self-relationship (depth 0)
            $self_query = "INSERT IGNORE INTO genealogy_relationships (ancestor_id, descendant_id, depth) 
                           VALUES (:id, :id, 0)";
            $this->db->execute($self_query, [':id' => $downlineId]);

            // Inherit ancestors from the upline
            $link_query = "INSERT INTO genealogy_relationships (ancestor_id, descendant_id, depth)
                           SELECT ancestor_id, :downline_id, depth + 1
                           FROM genealogy_relationships
                           WHERE descendant_id = :upline_id";
            
            $this->db->execute($link_query, [
                ':downline_id' => $downlineId,
                ':upline_id' => $uplineId
            ]);

            return true;
        } catch (Exception $e) {
            error_log("LinkMember Error: " . $e->getMessage());
            return false;
        }
    }

    public function getFullDownline(int $userId): array {
        try {
            $query = "SELECT u.username, r.depth, r.descendant_id 
                      FROM genealogy_users u
                      INNER JOIN genealogy_relationships r ON r.descendant_id = u.id
                      WHERE r.ancestor_id = :u_id AND r.depth > 0
                      ORDER BY r.depth ASC";
            
            return $this->db->fetchAllData($query, [":u_id" => $userId]) ?: [];
        } catch (Exception $error) {
            error_log("Downline Error: " . $error->getMessage());
            throw $error;
        }
    }

    public function getFullUpline(int $userId): array {
        try {
            $query = "SELECT u.username, r.depth, r.ancestor_id 
                      FROM genealogy_users u 
                      INNER JOIN genealogy_relationships r ON r.ancestor_id = u.id 
                      WHERE r.descendant_id = :u_id AND r.depth > 0
                      ORDER BY r.depth ASC";

            return $this->db->fetchAllData($query, [":u_id" => $userId]) ?: [];
        } catch (Exception $error) {
            error_log("Upline Error: " . $error->getMessage());            
            throw $error;
        }
    }

    public function getNetworkStats(int $userId): array {
        try {
            $query = "SELECT 
                        COUNT(*) - 1 AS total_network, 
                        MAX(depth) AS max_depth, 
                        (SELECT COUNT(*) FROM genealogy_users WHERE referrer_id = :u_id) AS direct_referrals
                      FROM genealogy_relationships 
                      WHERE ancestor_id = :u_id";

            $stats = $this->db->fetchSingleData($query, [':u_id' => $userId]);

            return [
                'total_network'    => (int)($stats['total_network'] ?? 0),
                'network_depth'    => (int)($stats['max_depth'] ?? 0),
                'direct_referrals' => (int)($stats['direct_referrals'] ?? 0)
            ];
        } catch (Exception $e) {
            error_log("GetNetworkStats Error: " . $e->getMessage());
            return ['total_network' => 0, 'network_depth' => 0, 'direct_referrals' => 0];
        }
    }

    public function getFormattedTree(int $userId): array {
        $currentUser = $this->db->fetchSingleData(
            "SELECT id, username as name FROM genealogy_users WHERE id = :id", 
            [':id' => $userId]
        );

        if (!$currentUser) return [];

        $query = "SELECT u.id, u.username as name, u.referrer_id, r.depth 
                  FROM genealogy_users u
                  INNER JOIN genealogy_relationships r ON r.descendant_id = u.id
                  WHERE r.ancestor_id = :u_id AND r.depth > 0";
        
        $flatDownline = $this->db->fetchAllData($query, [':u_id' => $userId]);
        $currentUser['children'] = $this->buildNestedTree($flatDownline, $userId);
        
        return $currentUser;
    }

    private function buildNestedTree(array $items, $parentId): array {
        $branch = [];
        foreach ($items as $item) {
            if ($item['referrer_id'] == $parentId) {
                $children = $this->buildNestedTree($items, $item['id']);
                $item['children'] = $children;
                $branch[] = $item;
            }
        }
        return $branch;
    }
}