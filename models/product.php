<?php
require_once __DIR__ . '/../config/db.php';

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
                                             ps.cost_price,
                                             p.selling_price,
                                             p.product_description,
                                             p.status,
                                             p.is_deleted,
                                             s.supplier_name as preferred_supplier_name
                                             FROM products p
                                             LEFT JOIN categories c ON c.category_id_pk = p.category_id_fk AND c.is_deleted = 0
                                             LEFT JOIN product_supplier ps ON ps.product_id_fk = p.product_id_pk AND ps.preferred = 1
                                             LEFT JOIN suppliers s ON s.supplier_id_pk = ps.supplier_id_fk
                                             ORDER BY p.product_id_pk DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function AddProduct($name, $category, $selling_price, $description, $status)
    {
        $stmt = $this->conn->prepare("INSERT INTO products (product_name,category_id_fk,selling_price,product_description,status)
                                      VALUES(:name,:category,:selling_price,:description,:status)");
        $stmt->execute([
            ':name' => $name,
            ':category' => $category,
            ':selling_price' => $selling_price,
            ':description' => $description,
            ':status' => $status
        ]);

        return $this->conn->lastInsertId();

    }

    public function CheckDuplicateProduct($name, $category)
    {
        $stmt = $this->conn->prepare("SELECT product_name FROM products 
                                      WHERE product_name = :name 
                                      AND category_id_fk = :category 
                                      AND is_deleted = 0");
        $stmt->execute([
            ':name' => $name,
            ':category' => $category
        ]);

        return $stmt->rowCount() > 0;
    }


    public function SoftDeleteProduct($id)
    {
        $stmt = $this->conn->prepare("UPDATE products SET is_deleted = 1,deleted_at = NOW() WHERE product_id_pk = :id");
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function GetProductNameById($id)
    {
        $stmt = $this->conn->prepare("SELECT product_name FROM products WHERE product_id_pk = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product['product_name'] ?? '';
    }
    public function GetDeletedProducts()
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
                                             LEFT JOIN product_supplier ps ON ps.product_id_fk = p.product_id_pk AND ps.preferred = 1
                                             WHERE p.is_deleted = 1
                                             GROUP BY p.product_id_pk
                                             ORDER BY p.deleted_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function GetProductInfoById($product_id)
    {
        $stmt = $this->conn->prepare("SELECT p.product_name,
                                             p.category_id_fk,
                                             c.category_name,
                                             c.category_id_pk,
                                             p.selling_price,
                                             p.product_description
                                             ,p.status
                                             FROM products as p LEFT JOIN categories as c on c.category_id_pk = p.category_id_fk 
                                             WHERE product_id_pk = :product_id");

        $stmt->execute([':product_id' => $product_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function UpdateProductInfo($product_id, $name, $category, $selling_price, $description, $status)
    {
        $stmt = $this->conn->prepare("UPDATE products set 
                                      product_name = :name,
                                      category_id_fk = :category,
                                      selling_price = :selling_price,
                                      product_description = :description,
                                      status = :status WHERE product_id_pk = :product_id");

        $stmt->execute([
            ':name' => $name,
            ':category' => $category,
            ':selling_price' => $selling_price,
            ':description' => $description,
            ':status' => $status,
            ':product_id' => $product_id
        ]);


        return $stmt->rowCount() > 0;
    }

    public function RestoreProduct($id){
        $stmt = $this->conn->prepare("UPDATE products SET is_deleted = 0 WHERE product_id_pk = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
    public function HardDeleteProduct($id)
    {
        try {
            $this->conn->beginTransaction();

            $stmt1 = $this->conn->prepare("DELETE FROM product_supplier WHERE product_id_fk = :id");
            $stmt1->execute([':id' => $id]);

            $stmt2 = $this->conn->prepare("DELETE FROM stocks WHERE product_id_fk = :id");
            $stmt2->execute([':id' => $id]);

            $stmt3 = $this->conn->prepare("DELETE FROM products WHERE product_id_pk = :id");
            $stmt3->execute([':id' => $id]);

            $this->conn->commit();

            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>