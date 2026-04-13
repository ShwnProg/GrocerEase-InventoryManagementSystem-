<?php
    require_once '../config/db.php';
     
    class Stock{
        private $conn;

        public function __construct(){
            $database = new DB();
            $this->conn = $database->conn;
        }   
        public function GetAllStocks(){
            $stmt = $this->conn->prepare("SELECT s.stock_id_pk,
                                                 p.product_name,
                                                 s.quantity,
                                                 c.category_name,
                                                 s.last_updated FROM stocks as s 
                                                 RIGHT JOIN products as p ON s.product_id_fk = p.product_id_pk
                                                 INNER JOIN categories as c ON p.category_id_fk = c.category_id_pk
                                                 WHERE quantity >= 0");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    }

?>