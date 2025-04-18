<?php
class Candidate {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getCandidatesByPosition($positionId) {
        $stmt = $this->db->prepare("
            SELECT c.id, c.name, c.bio, c.image_path
            FROM candidates c
            WHERE c.position_id = ?
            ORDER BY c.name
        ");
        $stmt->bind_param("i", $positionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $candidates = [];
        while ($row = $result->fetch_assoc()) {
            $candidates[] = $row;
        }
        
        return $candidates;
    }

    public function getCandidateById($candidateId) {
        $stmt = $this->db->prepare("
            SELECT c.id, c.name, c.bio, c.image_path, p.title as position_title
            FROM candidates c
            JOIN positions p ON c.position_id = p.id
            WHERE c.id = ?
        ");
        $stmt->bind_param("i", $candidateId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getVoteCount($candidateId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as vote_count
            FROM votes
            WHERE candidate_id = ?
        ");
        $stmt->bind_param("i", $candidateId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['vote_count'];
    }
} 