<?php
// session_start();
require_once __DIR__ . '/../../autoload.php';

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

    $product = new Product($db);
    $product_info = $product->GetProductInfoById($product_id);

    if (
        $product_info
        && $product->CheckDuplicateProduct($product_info['product_name'], $product_info['category_id_fk'])
    ) {
        echo json_encode([
            "status" => "error",
            "message" => "Cannot restore because an active product with the same name already exists in this category."
        ]);
        exit;
    }

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
