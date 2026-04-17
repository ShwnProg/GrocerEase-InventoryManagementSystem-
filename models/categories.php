<?php
require_once __DIR__ . '/../config/db.php';

class Category
{
    private $conn;

    public function __construct()
    {
        $database = new DB();
        $this->conn = $database->conn;
    }
    public function AddCategory($name, $description)
    {
        $stmt = $this->conn->prepare("INSERT INTO categories(category_name, category_description) 
        VALUES (:name, :description)");
        $stmt->execute([':name' => $name, ':description' => $description]);
        return $stmt->rowCount() > 0;
    }
    public function CheckDuplicateCategory($category_name)
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM categories WHERE category_name = :name AND is_deleted = 0');
        $stmt->execute([':name' => $category_name]);
        return $stmt->fetchColumn() > 0;
    }
    // public function CheckDuplicateCategoryById($category_name, $id)
    // {
    //     $stmt = $this->conn->prepare('SELECT COUNT(*) FROM categories WHERE category_name = :name AND is_deleted = 0 AND category_id_pk = :id');
    //     $stmt->execute([':name' => $category_name, ':id' => $id]);
    //     return $stmt->fetchColumn() > 0;
    // }

    public function GetAllCategories()
    {
        $stmt = $this->conn->prepare("SELECT category_id_pk, category_name, category_description,is_deleted FROM categories ORDER BY category_id_pk DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function SoftDeleteCategory($id)
    {
        $stmt = $this->conn->prepare("UPDATE categories SET is_deleted = 1 WHERE category_id_pk = :id");
        $result = $stmt->execute([':id' => $id]);
        return $result;
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
    public function GetCategoryNameById($id)
    {
        $stmt = $this->conn->prepare("SELECT category_name FROM categories WHERE category_id_pk = :id;");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn();
    }
    public function EditCategory($id, $name, $description)
    {
        $stmt = $this->conn->prepare("UPDATE categories SET category_name = :name, category_description = :description WHERE category_id_pk = :id");
        $stmt->execute([':name' => $name, ':description' => $description, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
