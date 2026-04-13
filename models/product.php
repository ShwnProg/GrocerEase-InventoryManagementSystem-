<?php
require_once(__DIR__ . '/../config/db.php');

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
                                             LEFT JOIN product_supplier ps ON ps.product_id_fk = p.product_id_pk
                                             GROUP BY p.product_id_pk
                                             ORDER BY p.product_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function AddProduct($name,$category,$selling_price,$description,$status){
        $stmt = $this->conn->prepare("INSERT INTO products (product_name,category_id_fk,selling_price,product_description,status)
                                      VALUES(:name,:category,:selling_price,:description,:status)");
        $stmt->execute([
            ':name' => $name,
            ':category' => $category,
            ':selling_price' => $selling_price,
            ':description' => $description,
            ':status' => $status
        ]);

        return $stmt->rowCount() > 0;
        
    }

    public function CheckDuplicateProduct($name,$category){
        $stmt = $this->conn->prepare("SELECT product_name FROM products WHERE product_name = :name AND category_id_fk = :category");
        $stmt->execute([
            ':name' => $name,
            ':category' => $category
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function SoftDeleteProduct($id){
        $stmt = $this->conn->prepare("UPDATE products SET is_deleted = 1 WHERE product_id_pk = :id");
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function GetProductNameById($id){
        $stmt = $this->conn->prepare("SELECT product_name FROM products WHERE product_id_pk = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product['product_name'] ?? '';
    }
    public function GetDeletedProducts(){
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
                                             LEFT JOIN product_supplier ps ON ps.product_id_fk = p.product_id_pk
                                             WHERE p.is_deleted = 1
                                             GROUP BY p.product_id_pk
                                             ORDER BY p.product_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function GetSupplier($id){
        $stmt = $this->conn->prepare("SELECT s.supplier_name,
                                             s.contact_person,
                                             cost_price,
                                             s.phone_number,
                                             s.email,
                                             s.company_name,
                                             p.product_id_pk
                                             FROM suppliers as s
                                             INNER JOIN product_supplier as ps on s.supplier_id_pk = ps.supplier_id_fk
                                             INNER JOIN products as p on p.product_id_pk = ps.product_id_fk
                                             WHERE ps.product_id_fk = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>