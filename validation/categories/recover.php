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
    $result = $category->RestoreCategory($category_id);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "Product restored successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to restore product."
        ]);
    }

    exit;
}