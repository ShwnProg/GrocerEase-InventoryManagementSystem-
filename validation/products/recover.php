<?php
session_start();
require_once '../../models/product.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id = $_POST['product_id'] ?? null;

    if (!$product_id) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid request."
        ]);
        exit;
    }

    $product = new Product();
    $result = $product->RestoreProduct($product_id);

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