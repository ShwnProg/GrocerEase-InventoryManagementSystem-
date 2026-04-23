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
    $result = $product->HardDeleteProduct($product_id);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "Product permanently deleted."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to delete product."
        ]);
    }

    exit;
}