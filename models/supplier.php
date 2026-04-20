<?php
require_once(__DIR__ . '/../config/db.php');


class Supplier
{
    private $conn;

    public function __construct()
    {
        $database = new DB();
        $this->conn = $database->conn;
    }

    public function GetAllSuppliers()
    {
        $stmt = $this->conn->prepare("SELECT supplier_id_pk, 
                                                 supplier_name, 
                                                 contact_person,
                                                 phone_number,
                                                 email, 
                                                 address, 
                                                 company_name FROM suppliers;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function GetSupplierById($id)
    {
        $stmt = $this->conn->prepare("SELECT supplier_id_pk, 
                                                 supplier_name, 
                                                 contact_person,
                                                 phone_number,
                                                 email, 
                                                 address, 
                                                 company_name FROM suppliers WHERE supplier_id_pk = :id;");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function SoftDeleteSupplier($id)
    {
        $stmt = $this->conn->prepare("UPDATE suppliers SET is_deleted = 1 WHERE supplier_id_pk = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
    public function GetDeletedSuppliers()
    {
        $stmt = $this->conn->prepare("SELECT supplier_id_pk, 
                                                 supplier_name, 
                                                 contact_person,
                                                 phone_number,
                                                 email, 
                                                 address, 
                                                 company_name FROM suppliers WHERE is_deleted = 1;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function CheckDuplicateSupplier($supplier_name)
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM suppliers WHERE supplier_name = :name AND is_deleted = 0');
        $stmt->execute([':name' => $supplier_name]);
        return $stmt->fetchColumn() > 0;
    }
    public function EditSupplier($id, $name, $contact_person, $phone_number, $email, $address, $company_name)
    {
        $stmt = $this->conn->prepare("UPDATE suppliers SET supplier_name = :name, contact_person = :contact_person, phone_number = :phone_number, email = :email, address = :address, company_name = :company_name WHERE supplier_id_pk = :id");
        $stmt->execute([
            ':name' => $name,
            ':contact_person' => $contact_person,
            ':phone_number' => $phone_number,
            ':email' => $email,
            ':address' => $address,
            ':company_name' => $company_name,
            ':id' => $id
        ]);
        return $stmt->rowCount() > 0;
    }
    public function AddSupplier($name, $contact_person, $phone_number, $email, $address, $company_name)
    {
        $stmt = $this->conn->prepare("INSERT INTO suppliers (supplier_name, contact_person, phone_number, email, address, company_name) VALUES (:name, :contact_person, :phone_number, :email, :address, :company_name)");
        $stmt->execute([
            ':name' => $name,
            ':contact_person' => $contact_person,
            ':phone_number' => $phone_number,
            ':email' => $email,
            ':address' => $address,
            ':company_name' => $company_name
        ]);
        return $stmt->rowCount() > 0;
    }
}
