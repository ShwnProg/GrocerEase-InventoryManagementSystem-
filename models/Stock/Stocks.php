<?php


class Stocks
{
    private $conn;

    public function __construct($db)
    {
        // $database = new DB();
        $this->conn = $db;
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
        $stmt = $this->conn->prepare("UPDATE stocks set quantity = quantity - :quantity,last_updated = :date WHERE product_id_fk = :id");

        $stmt->execute([
            ':quantity' => $quantity,
            ':date' => $date,
            ':id' => $product_id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function SearchStock($search)
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
                                      WHERE p.product_name LIKE :search OR c.category_name LIKE :search ORDER BY stock_id_pk DESC");
        $stmt->execute([':search' => $search . '%']);

        return $stmt->fetchAll();
    }
    public function GetTotalStockQuantity()
    {
        $stmt = $this->conn->prepare("SELECT SUM(quantity) as total_quantity FROM stocks");

        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function GetTotalLowStockItems()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM stocks INNER JOIN products on product_id_fk = product_id_pk 
                                      WHERE quantity <= 10");

        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function GetTotalOutOfStockItems()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM stocks INNER JOIN products on product_id_fk = product_id_pk
                                      WHERE quantity <= 0");
        $stmt->execute();

        return $stmt->fetchColumn();
    }
    public function GetTotalStockPerCategory()
    {
        $stmt = $this->conn->prepare("SELECT c.category_name, SUM(s.quantity) as total_stock FROM categories as c 
                                      INNER JOIN products as p on p.category_id_fk = c.category_id_pk
                                      INNER JOIN stocks as s on s.product_id_fk = p.product_id_pk
                                      GROUP BY c.category_name");

        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function GetStockStatus(){
        $stmt = $this->conn->prepare("SELECT CASE WHEN quantity = 0 THEN 'Out of Stock'
                                                  WHEN quantity BETWEEN 1 AND 10 THEN 'Low Stock'
                                                  ELSE 'In Stock'
                                                  END As stock_status, COUNT(*) AS total FROM stocks GROUP BY stock_status");

        $stmt->execute();

        return $stmt->fetchAll();
    }

}

?>