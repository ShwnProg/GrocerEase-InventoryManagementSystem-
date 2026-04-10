<?php
    require_once '../config/db.php';

    class Category
    {
        private $conn;

        public function __construct()
        {
            $database = new DB();
            $this->conn = $database->conn;
        }

        public function GetAllCategories()
        {
            $stmt = $this->conn->prepare("SELECT category_id_pk, category_name, category_description FROM categories;");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>