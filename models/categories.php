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
    public function GetAllCategories()
    {
        $stmt = $this->conn->prepare("SELECT category_id_pk, category_name, category_description,is_deleted,status FROM categories ORDER BY category_id_pk DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function SoftDeleteCategory($id)
    {
        $stmt0 = $this->conn->prepare("UPDATE products set category_id_fk = NULL WHERE category_id_fk = :id");
        $result0 = $stmt0->execute([':id' => $id]);

        if ($result0) {
            $stmt = $this->conn->prepare("UPDATE categories SET is_deleted = 1,deleted_at = NOW() WHERE category_id_pk = :id");
            $result = $stmt->execute([':id' => $id]);
            return $result;
        }
        return false;
    }
    public function GetCategoryById($id)
    {
        // get specific category info
        $stmt = $this->conn->prepare("SELECT category_id_pk, category_name, category_description,status FROM categories WHERE category_id_pk = :id;");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function GetDeletedCategories()
    {
        $stmt = $this->conn->prepare("SELECT category_id_pk, category_name, category_description,status 
                                      FROM categories WHERE is_deleted = 1 ORDER BY deleted_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function GetCategoryNameById($id)
    {
        $stmt = $this->conn->prepare("SELECT category_name FROM categories WHERE category_id_pk = :id;");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn();
    }
    public function EditCategory($id, $name, $description,$status)
    {
        $stmt = $this->conn->prepare("UPDATE categories SET category_name = :name, category_description = :description,status = :status WHERE category_id_pk = :id");
        $stmt->execute([':name' => $name, ':description' => $description, ':status' => $status,':id' => $id]);
        return $stmt->rowCount() > 0; // check if updated
    }
    public function RestoreCategory($id){
        $stmt = $this->conn->prepare("UPDATE categories SET is_deleted = 0 WHERE category_id_pk = :id");

        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }
    public function HardDeleteCategory($id){
        $stmt = $this->conn->prepare("DELETE FROM categories WHERE category_id_pk = :id");
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
