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
    }
?>