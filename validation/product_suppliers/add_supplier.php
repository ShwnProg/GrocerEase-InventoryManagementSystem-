<?php
session_start();
require_once "../../models/product_suppliers.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = $_POST["product_id"];
    $supplier_id = $_POST["suppliers"];
    $cost_price = $_POST["cost_price"];

    $product_supplier = new ProductSuppliers();

    $error = [];

    if (empty($supplier_id)) {
        $error["supplier"] = "Please select a supplier.";
    }
    if (empty($cost_price) || !is_numeric($cost_price) || $cost_price <= 0) {
        $error["cost_price"] = "Please enter a valid cost price.";
    }

    if (!empty($supplier_id) && !empty($cost_price)) {
        if ($product_supplier->CheckDuplicateSupplier($product_id, $supplier_id)) {
            $error['supplier'] = "This supplier is already assigned to the product.";
        }
    }

    // echo $product->CheckDuplicateSupplier($product_id,$supplier_id);

    if (!empty($error)) {
        $_SESSION["error"] = $error;
        $_SESSION["old"] = $_POST;
        header("Location: ../../pages/manage_suppliers.php?product_id=" . $product_id);
        exit;
    }

    $result = $product_supplier->AddSupplierInProduct($product_id, $supplier_id, $cost_price);
    if ($result) {
        $_SESSION["success"] = ["add_supplier" => "Supplier added successfully."];
    } else {
        $_SESSION["error"] = ["add_supplier" => "Failed to add supplier."];
    }
    // echo $product_id;
    // echo $cost_price;
    // var_dump($result);
    header("Location: ../../pages/manage_suppliers.php?product_id=" . $product_id);
    exit;
}

?>