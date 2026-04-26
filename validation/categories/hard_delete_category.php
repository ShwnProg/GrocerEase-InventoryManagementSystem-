<?php
session_start();
require_once '../../autoload.php';

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
    $result = $category->HardDeleteCategory($category_id);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "Category permanently deleted."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to delete category."
        ]);
    }

    exit;
}