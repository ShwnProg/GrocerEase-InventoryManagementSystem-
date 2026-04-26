<?php
require_once "../../models/product_suppliers.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$product_id    = trim($_POST['product_id']  ?? '');
$supplier_id   = trim($_POST['supplier_id'] ?? '');
$supplier_name = trim($_POST['supplier_name'] ?? '');
$cost_price    = trim($_POST['cost_price']  ?? '');

// --- Validate ---
if ($cost_price === '') {
    echo json_encode(['status' => 'error', 'field' => 'cost_price', 'message' => 'Cost price is required.']);
    exit;
}

if (!is_numeric($cost_price)) {
    echo json_encode(['status' => 'error', 'field' => 'cost_price', 'message' => 'Cost price must be a number.']);
    exit;
}

if ((float) $cost_price <= 0) {
    echo json_encode(['status' => 'error', 'field' => 'cost_price', 'message' => 'Cost price must be greater than 0.']);
    exit;
}

//  Updat
$product_supplier = new ProductSuppliers();
$result = $product_supplier->UpdateCostPrice($product_id, $supplier_id, $cost_price);

if ($result) {
    echo json_encode([
        'status'    => 'success',
        'message'   => 'Cost price updated successfully.',
        'new_price' => number_format((float) $cost_price, 2),
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update cost price. Please try again.']);
}
exit;