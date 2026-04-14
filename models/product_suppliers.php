<?php
require_once(__DIR__ . '/../config/db.php');

class ProductSuppliers
{
    private $conn;

    public function __construct()
    {
        $database = new DB();
        $this->conn = $database->conn;
    }
    public function AddSupplierInProduct($product_id, $supplier_id, $cost_price)
    {
        $stmt = $this->conn->prepare("INSERT INTO product_supplier (product_id_fk, supplier_id_fk, cost_price)
                                      VALUES (:product_id, :supplier_id, :cost_price)");
        $stmt->execute([
            ':product_id' => $product_id,
            ':supplier_id' => $supplier_id,
            ':cost_price' => $cost_price
        ]);
        return $stmt->rowCount() > 0;
    }
    public function GetProductSupplier($id)
    {
        $stmt = $this->conn->prepare("SELECT s.supplier_name,
                                             s.contact_person,
                                             cost_price,
                                             s.phone_number,
                                             s.email,
                                             s.company_name,
                                             ps.preferred,
                                             s.supplier_id_pk
                                             FROM suppliers as s
                                             INNER JOIN product_supplier as ps on s.supplier_id_pk = ps.supplier_id_fk
                                             INNER JOIN products as p on p.product_id_pk = ps.product_id_fk
                                             WHERE ps.product_id_fk = :id");
        $stmt->execute([':id' => $id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public function AddPreferedSupplier($product_id, $supplier_id)
    {
        // Clear all preferred 
        $stmt = $this->conn->prepare(
            "UPDATE product_supplier SET preferred = 0 WHERE product_id_fk = :product_id"
        );
        $stmt->execute([':product_id' => $product_id]);

        // Set the selected supplier as preferred
        $stmt = $this->conn->prepare(
            "UPDATE product_supplier SET preferred = 1 
                 WHERE product_id_fk = :product_id AND supplier_id_fk = :supplier_id"
        );
        $stmt->execute([
            ':product_id' => $product_id,
            ':supplier_id' => $supplier_id
        ]);

        return true;
    }
    public function ClearPreferredSupplier($product_id)
    {
        $stmt = $this->conn->prepare(
            "UPDATE product_supplier SET preferred = 0 WHERE product_id_fk = :product_id"
        );
        $stmt->execute([':product_id' => $product_id]);
        return true;
    }
    public function CheckDuplicateSupplier($product_id, $supplier_id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as product_supplier FROM product_supplier WHERE 
                                          product_id_fk = :product_id 
                                          AND supplier_id_fk = :supplier_id LIMIT 1");

        $stmt->execute([
            ':product_id' => $product_id,
            ':supplier_id' => $supplier_id
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['product_supplier'] > 0;
    }
    public function GetProductSupplierCount($product_id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(preferred)
                                          FROM products as p
                                          INNER JOIN product_supplier as ps on ps.product_id_fk = p.product_id_pk 
                                          INNER JOIN suppliers as s on s.supplier_id_pk = ps.supplier_id_fk WHERE p.product_id_pk = :product_id");

        $stmt->execute([':product_id' => $product_id]);

        return $stmt->rowCount() > 0;
    }
    public function RemoveProductSupplier($product_id,$supplier_id){
        $stmt = $this->conn->prepare("DELETE FROM product_supplier WHERE 
                                      product_id_fk = :product_id AND 
                                      supplier_id_fk = :supplier_id");

        $stmt->execute([':product_id' => $product_id,
                        ':supplier_id' => $supplier_id]);

        return $stmt->rowCount() > 0;
    }   
}
?>