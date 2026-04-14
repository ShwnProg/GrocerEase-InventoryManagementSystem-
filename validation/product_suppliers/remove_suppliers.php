<?php
session_start();
require_once "../../models/product_suppliers.php";

$product_id  = $_POST['product_id']  ?? null;
$supplier_id = $_POST['supplier_id'] ?? null;

if (!$product_id || !$supplier_id) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../../pages/manage_suppliers.php?product_id=" . $product_id);
    exit;
}

$product_supplier = new ProductSuppliers();
$removed = $product_supplier->RemoveProductSupplier($product_id, $supplier_id);

unset($_SESSION['delete_product_id'], $_SESSION['delete_supplier_id'], $_SESSION['delete_supplier_name']);

if ($removed) {
    $_SESSION['success']['remove_supplier'] = "Supplier removed successfully.";
} else {
    $_SESSION['error']['remove_supplier'] = "Failed to remove supplier.";
}

header("Location: ../../pages/manage_suppliers.php?product_id=" . $product_id);
exit;