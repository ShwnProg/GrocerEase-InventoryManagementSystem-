<?php
require_once "../../models/product_suppliers.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $supplier_id = $_POST['supplier_id'];
    $supplier_name = $_POST['supplier_name'] ?? '';
    $cost_price = $_POST['cost_price'];
    $error = [];

    if (empty($cost_price)) {
        $error['edit_cost_price'] = 'Cost price is required';
    } elseif (!is_numeric($cost_price)) {
        $error['edit_cost_price'] = 'Cost price must be a number';
    } elseif ($cost_price <= 0) {
        $error['edit_cost_price'] = 'Cost price must be greater than 0';
    }

    // Helper to reopen the edit modal (used for both error and success)
    $restore_edit_session = function () use ($product_id, $supplier_id, $supplier_name, $cost_price) {
        $_SESSION['edit_product_id'] = $product_id;
        $_SESSION['edit_supplier_id'] = $supplier_id;
        $_SESSION['edit_supplier_name'] = $supplier_name;
        $_SESSION['edit_cost_price'] = $cost_price;
    };

    if (!empty($error)) {
        $_SESSION['error'] = $error;
        $restore_edit_session();
        header("Location: ../../pages/manage_suppliers.php?product_id=" . $product_id);
        exit;
    }

    $product_supplier = new ProductSuppliers();
    $result = $product_supplier->UpdateCostPrice($product_id, $supplier_id, $cost_price);

    if ($result) {
        $_SESSION['success']['edit_supplier'] = 'Cost price updated successfully.';
    } else {
        $_SESSION['error']['edit_cost_price'] = 'Failed to update cost price.';
    }

    // Always restore so the modal reopens to show the message
    $restore_edit_session();

    header("Location: ../../pages/manage_suppliers.php?product_id=" . $product_id);
    exit;
}
?>