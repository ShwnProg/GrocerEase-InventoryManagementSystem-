<?php
require_once '../../autoload.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';

    $product = new Product($db);
    $result = $product->SoftDeleteProduct($product_id);

    unset($_SESSION['delete_product_id']);

    if ($result) {
        $_SESSION['success'] = ['delete' => "Product deleted successfully."];
    } else {
        $_SESSION['errors'] = /*['delete' => "Failed to delete product. Please try again."]*/ $result;
    }

    header("Location: ../../pages/products.php");
    exit;
}
?>