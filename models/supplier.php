<?php
    require_once "../config/db.php";

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
        public function SoftDeleteSupplier($id){
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
    }
?>