<?php

class StockMovements
{
    private $conn = null;

    public function __construct($db)
    {
        // $database = new DB();
        $this->conn = $db;
    }
    public function AddStockMovements($quantity, $reference_type, $reference_id, $reason, $date, $product_id)
    {
        $stmt = $this->conn->prepare("INSERT INTO stock_movements (quantity,reference_type,reference_id,reason,date,product_id)
                                          VALUES(:quantity,:type,:ref_id,:reason,:date,:product_id)");

        $stmt->execute([
            ':quantity' => $quantity,
            ':type' => $reference_type,
            ':ref_id' => $reference_id,
            ':reason' => $reason,
            ':date' => $date,
            ':product_id' => $product_id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function IsRefIdExist($id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as reference_id_num FROM stock_movements WHERE reference_id = :id");

        $stmt->execute([':id' => $id]);

        return $stmt->fetchColumn();
    }
    public function GetStockMovements()
    {
        $stmt = $this->conn->prepare("SELECT p.product_name, m.quantity, m.reference_type, m.reference_id, m.reason,m.date 
                                      FROM stock_movements as m
                                      LEFT JOIN products as p ON m.product_id = p.product_id_pk
                                      ORDER BY movement_id_pk DESC");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function GetStockMovementsPaginated($page = 1, $limit = 10, $search = '')
    {
        $offset = ($page - 1) * $limit;
        $baseQuery = "SELECT p.product_name, m.quantity, m.reference_type, m.reference_id, m.reason, m.date 
                      FROM stock_movements as m
                      LEFT JOIN products as p ON m.product_id = p.product_id_pk";

        if (!empty($search)) {
            $baseQuery .= " WHERE p.product_name LIKE :search OR m.reference_id LIKE :search OR m.reason LIKE :search";
        }

        $baseQuery .= " ORDER BY movement_id_pk DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($baseQuery);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        if (!empty($search)) {
            $query = '%' . $search . '%';
            $stmt->bindParam(':search', $query, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function GetTotalStockMovements($search = '')
    {
        $baseQuery = "SELECT COUNT(*) FROM stock_movements as m
                      LEFT JOIN products as p ON m.product_id = p.product_id_pk";

        if (!empty($search)) {
            $baseQuery .= " WHERE p.product_name LIKE :search OR m.reference_id LIKE :search OR m.reason LIKE :search";
        }

        $stmt = $this->conn->prepare($baseQuery);

        if (!empty($search)) {
            $query = '%' . $search . '%';
            $stmt->bindParam(':search', $query, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function GetInventoryLogsTrend(){
        $stmt = $this->conn->prepare("SELECT DATE(date) as date,
                                      SUM(CASE WHEN reference_type = 'IN' THEN quantity ELSE 0 END) as stock_in,
                                      SUM(CASE WHEN reference_type = 'OUT' THEN quantity ELSE 0 END) as stock_out
                                      FROM stock_movements GROUP BY DATE(date) 
                                      ORDER BY date ASC");
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
?>