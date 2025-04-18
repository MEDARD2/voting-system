<?php
class Vote {
    private $db;
    private $userId;

    public function __construct($userId) {
        $this->db = Database::getInstance();
        $this->userId = $userId;
    }

    public function submitVotes($votes) {
        try {
            $this->db->beginTransaction();
            
            // Check if user has already voted
            if ($this->hasUserVoted()) {
                $this->db->rollback();
                throw new Exception('You have already cast your vote');
            }

            // Record votes
            foreach ($votes as $position_id => $candidate_id) {
                if (!empty($candidate_id)) {
                    $this->recordVote($position_id, $candidate_id);
                }
            }
            
            // Update user's voting status
            $this->updateUserVotingStatus();
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function hasUserVoted() {
        $stmt = $this->db->prepare("SELECT has_voted FROM users WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user['has_voted'];
    }

    private function recordVote($positionId, $candidateId) {
        $stmt = $this->db->prepare("INSERT INTO votes (user_id, candidate_id, position_id, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iii", $this->userId, $candidateId, $positionId);
        $stmt->execute();
    }

    private function updateUserVotingStatus() {
        $stmt = $this->db->prepare("UPDATE users SET has_voted = 1, last_vote_time = NOW() WHERE id = ?");
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
    }

    public function getVotingStatus() {
        $stmt = $this->db->prepare("SELECT has_voted, last_vote_time FROM users WHERE id = ?");
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
} 