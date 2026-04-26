<?php
require_once __DIR__ . '/../config/db.php';

class Category
{
    private $conn;

    public function __construct($db)
    {
        // $database = new DB();
        $this->conn = $db;
    }

    public function AddCategory($name, $description)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO categories (category_name, category_description) 
                                          VALUES (:name, :description)");
            $stmt->execute([':name' => $name, ':description' => $description]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function CheckDuplicateCategory($category_name)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM categories 
                                      WHERE category_name = :name AND is_deleted = 0");
        $stmt->execute([':name' => $category_name]);
        return $stmt->fetchColumn() > 0;
    }

    public function GetAllCategories()
    {
        $stmt = $this->conn->prepare("SELECT category_id_pk, category_name, category_description, 
                                             is_deleted, status 
                                      FROM categories 
                                      ORDER BY category_id_pk DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function SoftDeleteCategory($id)
    {
        try {
            $this->conn->beginTransaction();

            $stmt0 = $this->conn->prepare("UPDATE products SET category_id_fk = NULL 
                                           WHERE category_id_fk = :id");
            $stmt0->execute([':id' => $id]);

            $stmt = $this->conn->prepare("UPDATE categories SET is_deleted = 1, deleted_at = NOW() 
                                          WHERE category_id_pk = :id");
            $stmt->execute([':id' => $id]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function GetCategoryById($id)
    {
        $stmt = $this->conn->prepare("SELECT category_id_pk, category_name, 
                                             category_description, status 
                                      FROM categories 
                                      WHERE category_id_pk = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetDeletedCategories()
    {
        $stmt = $this->conn->prepare("SELECT category_id_pk, category_name, 
                                             category_description, status 
                                      FROM categories 
                                      WHERE is_deleted = 1 
                                      ORDER BY deleted_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetCategoryNameById($id)
    {
        $stmt = $this->conn->prepare("SELECT category_name FROM categories 
                                      WHERE category_id_pk = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn();
    }

    public function EditCategory($id, $name, $description, $status)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE categories 
                                          SET category_name = :name, 
                                              category_description = :description, 
                                              status = :status 
                                          WHERE category_id_pk = :id");
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':status' => $status,
                ':id' => $id,
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function RestoreCategory($id)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE categories SET is_deleted = 0 
                                          WHERE category_id_pk = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function HardDeleteCategory($id)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM categories WHERE category_id_pk = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    public function SearchCategorY($search)
    {
        $stmt = $this->conn->prepare("SELECT category_id_pk, category_name, category_description, 
                                      is_deleted, status 
                                      FROM categories 
                                      WHERE category_name LIKE :search
                                      ORDER BY category_id_pk DESC");

        $stmt->execute([':search' => $search . '%']);

        return $stmt->fetchAll();
    }

    public function GetTotalCategories()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total_categories FROM categories WHERE is_deleted = 0");
        $stmt->execute();

        return $stmt->fetchColumn();

    }
}