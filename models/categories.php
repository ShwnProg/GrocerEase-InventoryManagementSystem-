<?php
require_once(__DIR__ . '/../config/db.php');

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
    public function SoftDeleteCategory($id)
    {
        $stmt = $this->conn->prepare("UPDATE categories SET is_deleted = 1 WHERE category_id_pk = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
    public function GetCategoryById($id)
    {
        $stmt = $this->conn->prepare("SELECT category_id_pk, category_name, category_description FROM categories WHERE category_id_pk = :id;");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function GetDeletedCategories()
    {
        $stmt = $this->conn->prepare("SELECT category_id_pk, category_name, category_description FROM categories WHERE is_deleted = 1;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>