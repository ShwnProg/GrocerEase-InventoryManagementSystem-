<?php
require_once '../../autoload.php';

header('Content-Type: application/json');
session_start();

// check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;
}

// get id
$category_id = $_POST['category_id'] ?? '';

// validate
if (empty($category_id)) {
    echo json_encode([
        "status" => "error",
        "message" => "Category ID is required"
    ]);
    exit;
}

// delete
$Category = new Category($db);
$result = $Category->SoftDeleteCategory($category_id);

// response
if ($result) {
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