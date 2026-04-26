<?php
require_once '../../autoload.php';

header('Content-Type: application/json');
session_start();

$product_id  = $_POST['product_id']  ?? null;
$supplier_id = $_POST['supplier_id'] ?? null;

if (!$product_id || !$supplier_id) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
    exit;
}

$product_supplier = new ProductSuppliers($db);
$removed = $product_supplier->RemoveProductSupplier($product_id, $supplier_id);

if ($removed) {

    echo json_encode([
        "status" => "success",
        "message" => "Supplier removed successfully."
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Failed to remove supplier."
    ]);

}

exit;