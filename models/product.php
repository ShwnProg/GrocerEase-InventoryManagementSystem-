<?php
require_once "../config/db.php";

class Product
{
    private $conn;

    public function __construct()
    {
        $database = new DB();
        $this->conn = $database->conn;
    }

    public function GetAllProducts()
    {
        $stmt = $this->conn->prepare("SELECT p.product_id_pk,
                                             p.product_name,
                                             c.category_name,
                                             MIN(ps.cost_price) AS cost_price,
                                             p.selling_price,
                                             p.product_description,
                                             p.status,
                                             p.is_deleted
                                             FROM products p
                                             JOIN categories c ON c.category_id_pk = p.category_id_fk
                                             JOIN product_supplier ps ON ps.product_id_fk = p.product_id_pk
                                             GROUP BY p.product_id_pk;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>