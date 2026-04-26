<?php
session_start();
require_once '../../autoload.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST["product_id"] ?? "";
    $supplier_id = $_POST["supplier_id"] ?? "";
    $is_preferred = $_POST["is_preferred"] ?? 0;

    $product_supplier = new ProductSuppliers($db);

    if ($is_preferred == 1) {
        $result = $product_supplier->ClearPreferredSupplier($product_id);
    } else {
        $result = $product_supplier->AddPreferedSupplier($product_id, $supplier_id);
    }

    if ($result) {
        $_SESSION['success'] = ["preferred" => "Preferred supplier updated successfully."];
    } else {
        $_SESSION['error'] = ["preferred" => "Failed to update preferred supplier."];
    }

    header("Location: ../../pages/manage_suppliers.php?product_id=$product_id");
    exit;
}
?>