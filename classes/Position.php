<?php
class Position {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getActivePositions() {
        $stmt = $this->db->prepare("
            SELECT p.id, p.title, p.max_winners, p.description
            FROM positions p
            WHERE p.is_active = 1
            AND EXISTS (
                SELECT 1 FROM candidates c 
                WHERE c.position_id = p.id
            )
            ORDER BY p.id
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $positions = [];
        while ($row = $result->fetch_assoc()) {
            $positions[] = $row;
        }
        
        return $positions;
    }

    public function getPositionById($positionId) {
        $stmt = $this->db->prepare("
            SELECT p.id, p.title, p.max_winners, p.description
            FROM positions p
            WHERE p.id = ?
        ");
        $stmt->bind_param("i", $positionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getVotingResults($positionId) {
        $stmt = $this->db->prepare("
            SELECT 
                c.id as candidate_id,
                c.name as candidate_name,
                c.image_path,
                COUNT(v.id) as vote_count
            FROM candidates c
            LEFT JOIN votes v ON c.id = v.candidate_id
            WHERE c.position_id = ?
            GROUP BY c.id
            ORDER BY vote_count DESC
        ");
        $stmt->bind_param("i", $positionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $results = [];
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        
        return $results;
    }
} 