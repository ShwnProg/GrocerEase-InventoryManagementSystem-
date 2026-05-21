<?php
// session_start();
require_once __DIR__ . '/../../autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $category_id = $_POST['category_id'] ?? null;

    if (!$category_id) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid request."
        ]);
        exit;
    }

    $category = new Category($db);
    $category_info = $category->GetCategoryById($category_id);

    if ($category_info && $category->CheckDuplicateCategory($category_info['category_name'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Cannot restore because an active category with the same name already exists."
        ]);
        exit;
    }

    $result = $category->RestoreCategory($category_id);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "Category restored successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to restore category."
        ]);
    }

    exit;
}
