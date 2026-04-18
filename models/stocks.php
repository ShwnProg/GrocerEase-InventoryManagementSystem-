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
                                                 p.product_name,
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

}

?>