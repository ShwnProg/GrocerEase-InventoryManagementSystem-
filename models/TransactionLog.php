<?php
require_once __DIR__ . '/../config/db.php';

class TransactionLog
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function logTransaction($product_id, $quantity_changed, $action_type)
    {
        $stmt = $this->conn->prepare("INSERT INTO stock_mvements (product_id, quantity_changed, action_type) VALUES (?, ?, ?)");
        $stmt->execute([$product_id, $quantity_changed, $action_type]);
    }

    public function getRecentLogs($limit = 100)
    {
        $stmt = $this->conn->prepare("SELECT * FROM stock_movements ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>