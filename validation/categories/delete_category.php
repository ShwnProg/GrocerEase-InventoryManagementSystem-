<?php
require_once '../../models/categories.php';
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $category_id = $_POST['category_id'] ?? '';

    $Category = new Category();
    $result = $Category->SoftDeleteCategory($category_id);

    unset($_SESSION['delete_category_id']);

    if($result){
        $_SESSION['success'] = ['delete' => "Category deleted successfully."];
    }
    else{
        $_SESSION['errors'] = ['delete' => "Failed to delete category. Please try again."];
    }

    header("Location: ../../pages/categories.php");
    exit;

}
?>