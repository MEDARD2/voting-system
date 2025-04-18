<?php
class Database {
    private $conn;
    private static $instance = null;

    private function __construct() {
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        require_once __DIR__ . '/../includes/config.php';
        
        $this->conn = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME
        );

        if ($this->conn->connect_error) {
            throw new Exception("Database connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function beginTransaction() {
        return $this->conn->begin_transaction();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollback() {
        return $this->conn->rollback();
    }

    public function inTransaction() {
        return $this->conn->inTransaction();
    }

    public function prepare($query) {
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
        return $stmt;
    }
} 