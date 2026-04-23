<?php
require_once '../../models/categories.php';

header('Content-Type: application/json');
session_start();

<<<<<<< HEAD
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
=======
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $category_id = $_POST['category_id'] ?? '';

    $Category = new Category();
    // soft delete (is_deleted = 1)
    $result = $Category->SoftDeleteCategory($category_id);

    unset($_SESSION['delete_category_id']); // clear session

    if($result){
        $_SESSION['success'] = ['delete' => "Category deleted successfully."];
    }
    else{
        $_SESSION['errors'] = ['delete' => "Failed to delete category. Please try again."];
    }

    header("Location: ../../pages/categories.php");
>>>>>>> cbe618f6972275f215404ae980d7e854aa9781fd
    exit;
}

$category_id = $_POST['category_id'] ?? '';

if (empty($category_id)) {
    echo json_encode([
        "status" => "error",
        "message" => "Category ID is required"
    ]);
    exit;
}

$Category = new Category();
$result = $Category->SoftDeleteCategory($category_id);

if ($result) {

    unset($_SESSION['delete_category_id']); // optional nalang

    echo json_encode([
        "status" => "success",
        "message" => "Category deleted successfully"
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Failed to delete category"
    ]);

}

exit;