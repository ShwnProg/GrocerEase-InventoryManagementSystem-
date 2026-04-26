<?php
require_once(__DIR__ . '/../config/db.php');

class ProductSuppliers
{
    private $conn;

    public function __construct($db)
    {
        // $database = new DB();
        $this->conn = $db;
    }

    // ADD SUPPLIER
    public function AddSupplierInProduct($product_id, $supplier_id, $cost_price)
    {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO product_supplier (product_id_fk, supplier_id_fk, cost_price)
                VALUES (:product_id, :supplier_id, :cost_price)
            ");

            $stmt->execute([
                ':product_id' => $product_id,
                ':supplier_id' => $supplier_id,
                ':cost_price' => $cost_price
            ]);

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("AddSupplierInProduct: " . $e->getMessage());
            return false;
        }
    }

    // GET PRODUCT SUPPLIERS
    public function GetProductSupplier($id)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT s.supplier_name,
                       s.contact_person,
                       ps.cost_price,
                       s.phone_number,
                       s.email,
                       s.company_name,
                       ps.preferred,
                       s.supplier_id_pk
                FROM suppliers as s
                INNER JOIN product_supplier as ps 
                    ON s.supplier_id_pk = ps.supplier_id_fk
                INNER JOIN products as p 
                    ON p.product_id_pk = ps.product_id_fk
                WHERE ps.product_id_fk = :id
            ");

            $stmt->execute([':id' => $id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("GetProductSupplier: " . $e->getMessage());
            return [];
        }
    }

    // SET PREFERRED SUPPLIER
    public function AddPreferedSupplier($product_id, $supplier_id)
    {
        try {
            $stmt = $this->conn->prepare("
                UPDATE product_supplier 
                SET preferred = 0 
                WHERE product_id_fk = :product_id
            ");
            $stmt->execute([':product_id' => $product_id]);

            $stmt = $this->conn->prepare("
                UPDATE product_supplier 
                SET preferred = 1 
                WHERE product_id_fk = :product_id 
                AND supplier_id_fk = :supplier_id
            ");

            $stmt->execute([
                ':product_id' => $product_id,
                ':supplier_id' => $supplier_id
            ]);

            return true;

        } catch (PDOException $e) {
            error_log("AddPreferedSupplier: " . $e->getMessage());
            return false;
        }
    }

    // CLEAR PREFERRED
    public function ClearPreferredSupplier($product_id)
    {
        try {
            $stmt = $this->conn->prepare("
                UPDATE product_supplier 
                SET preferred = 0 
                WHERE product_id_fk = :product_id
            ");

            $stmt->execute([':product_id' => $product_id]);

            return true;

        } catch (PDOException $e) {
            error_log("ClearPreferredSupplier: " . $e->getMessage());
            return false;
        }
    }

    // CHECK DUPLICATE
    public function CheckDuplicateSupplier($product_id, $supplier_id)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as total 
                FROM product_supplier 
                WHERE product_id_fk = :product_id 
                AND supplier_id_fk = :supplier_id
            ");

            $stmt->execute([
                ':product_id' => $product_id,
                ':supplier_id' => $supplier_id
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;

        } catch (PDOException $e) {
            error_log("CheckDuplicateSupplier: " . $e->getMessage());
            return false;
        }
    }

    // COUNT SUPPLIERS
    public function GetProductSupplierCount($product_id)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as total
                FROM product_supplier
                WHERE product_id_fk = :product_id
            ");

            $stmt->execute([':product_id' => $product_id]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];

        } catch (PDOException $e) {
            error_log("GetProductSupplierCount: " . $e->getMessage());
            return 0;
        }
    }

    // REMOVE SUPPLIER
    public function RemoveProductSupplier($product_id, $supplier_id)
    {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM product_supplier 
                WHERE product_id_fk = :product_id 
                AND supplier_id_fk = :supplier_id
            ");

            $stmt->execute([
                ':product_id' => $product_id,
                ':supplier_id' => $supplier_id
            ]);

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("RemoveProductSupplier: " . $e->getMessage());
            return false;
        }
    }

    // UPDATE COST PRICE
    public function UpdateCostPrice($product_id, $supplier_id, $cost_price)
    {
        try {
            $stmt = $this->conn->prepare("
                UPDATE product_supplier 
                SET cost_price = :price 
                WHERE product_id_fk = :product_id 
                AND supplier_id_fk = :supplier_id
            ");

            $stmt->execute([
                ':price' => $cost_price,
                ':product_id' => $product_id,
                ':supplier_id' => $supplier_id
            ]);

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("UpdateCostPrice: " . $e->getMessage());
            return false;
        }
    }
}
?>