<?php
require_once __DIR__ . "/../config/db.php";

class Supplier
{
    private $conn;

    public function __construct()
    {
        $database = new DB();
        $this->conn = $database->conn;
    }

    // GET ALL
    public function GetAllSuppliers()
    {
        $stmt = $this->conn->prepare("
            SELECT supplier_id_pk, 
                   supplier_name, 
                   contact_person,
                   phone_number,
                   email,
                   address, 
                   company_name,
                   status
            FROM suppliers
            WHERE is_deleted = 0
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET BY ID
    public function GetSupplierById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT supplier_id_pk, 
                   supplier_name, 
                   contact_person,
                   phone_number,
                   email,
                   address, 
                   company_name,
                   status
            FROM suppliers 
            WHERE supplier_id_pk = :id
            AND is_deleted = 0
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ADD SUPPLIER
    public function addSupplier($name, $person, $number, $email, $address, $company, $status)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO suppliers 
            (supplier_name, contact_person, phone_number, email, address, company_name, status) 
            VALUES (:name, :person, :number, :email, :address, :company, :status)
        ");

        return $stmt->execute([
            ':name'    => $name,
            ':person'  => $person,
            ':number'  => $number,
            ':email'   => $email,
            ':address' => $address,
            ':company' => $company,
            ':status'  => $status
        ]);
    }

    // UPDATE SUPPLIER
    public function updateSupplier($id, $name, $person, $number, $email, $address, $company, $status)
    {
        $stmt = $this->conn->prepare("
            UPDATE suppliers
            SET supplier_name  = :name,
                contact_person = :person,
                phone_number   = :number,
                email          = :email,
                address        = :address,
                company_name   = :company,
                status         = :status
            WHERE supplier_id_pk = :id
            AND is_deleted = 0
        ");

        return $stmt->execute([
            ':id'      => $id,
            ':name'    => $name,
            ':person'  => $person,
            ':number'  => $number,
            ':email'   => $email,
            ':address' => $address,
            ':company' => $company,
            ':status'  => $status
        ]);
    }

    // CHECK DUPLICATE
    public function checkDuplicateSupplier($name)
    {
        $stmt = $this->conn->prepare("
            SELECT supplier_id_pk FROM suppliers 
            WHERE supplier_name = :name AND is_deleted = 0
        ");
        $stmt->execute([':name' => $name]);
        return $stmt->rowCount() > 0;
    }

    // SOFT DELETE
    public function SoftDeleteSupplier($id)
    {
        $stmt = $this->conn->prepare("
            UPDATE suppliers 
            SET is_deleted = 1 
            WHERE supplier_id_pk = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // GET DELETED
    public function GetDeletedSuppliers()
    {
        $stmt = $this->conn->prepare("
            SELECT supplier_id_pk, 
                   supplier_name, 
                   contact_person,
                   phone_number,
                   email,
                   address, 
                   company_name,
                   status
            FROM suppliers 
            WHERE is_deleted = 1
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function RestoreSupplier($id)
{
    $stmt = $this->conn->prepare("UPDATE suppliers SET is_deleted = 0 WHERE supplier_id_pk = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}

public function PermanentDeleteSupplier($id)
{
    $stmt = $this->conn->prepare("DELETE FROM suppliers WHERE supplier_id_pk = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}


}
?>