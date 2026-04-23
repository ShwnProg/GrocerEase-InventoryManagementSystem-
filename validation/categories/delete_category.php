<?php
require_once '../../models/categories.php';

header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
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