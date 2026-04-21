<?php
require_once __DIR__ . '/../config/db.php';


class Stock
{
    private $conn;

    public function __construct()
    {
        $database = new DB();
        $this->conn = $database->conn;
    }
    public function GetAllStocks()
    {
        $stmt = $this->conn->prepare("SELECT s.stock_id_pk,
                                         s.product_id_fk,
                                         p.product_name,
                                         p.status,
                                         s.quantity,
                                         c.category_name,
                                         p.is_deleted,
                                         s.last_updated FROM stocks as s 
                                         INNER JOIN products as p ON s.product_id_fk = p.product_id_pk
                                         LEFT JOIN categories as c ON p.category_id_fk = c.category_id_pk AND c.is_deleted = 0
                                         WHERE quantity >= 0 ORDER BY stock_id_pk DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function GetQuantityByProductId($product_id)
    {
        $stmt = $this->conn->prepare("SELECT quantity FROM stocks WHERE product_id_fk = :id");
        $stmt->execute([':id' => $product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function AddProductStock($id, $default_quantity, $last_updated)
    {
        $stmt = $this->conn->prepare("INSERT INTO stocks(product_id_fk,quantity,last_updated)
                                         VALUES(:id,:quantity,:last_updated)");

        $stmt->execute([
            ':id' => $id,
            ':quantity' => $default_quantity,
            ':last_updated' => $last_updated
        ]);

        return $stmt->rowCount() > 0;
    }

    public function StockIn($product_id, $quantity, $date)
    {
        $stmt = $this->conn->prepare("UPDATE stocks set quantity = quantity + :quantity,last_updated = :date WHERE product_id_fk = :id");

        $stmt->execute([
            ':quantity' => $quantity,
            ':date' => $date,
            ':id' => $product_id
        ]);

        return $stmt->rowCount() > 0;
    }
    public function StockOut($product_id, $quantity, $date)
    {
        // $check = $this->conn->prepare("SELECT quantity FROM stocks WHERE product_id_fk = :id");
        // $check->execute([':id' => $product_id]);
        // $current = $check->fetchColumn();

        // if ($current === false || $current < $quantity) {
        //     return false;
        // }
        
        $stmt = $this->conn->prepare("UPDATE stocks set quantity = quantity - :quantity,last_updated = :date WHERE product_id_fk = :id");

        $stmt->execute([
            ':quantity' => $quantity,
            ':date' => $date,
            ':id' => $product_id
        ]);

        return $stmt->rowCount() > 0;
    }


}

?>