<?php
session_start();
require_once '../../models/product.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product = new Product();
    $result = $product->HardDeleteProduct($_POST['product_id']);

    $_SESSION['archive_msg'] = $result
        ? ['type' => 'success', 'text' => 'Product permanently deleted.']
        : ['type' => 'error', 'text' => 'Failed to delete product.'];
}

unset($_SESSION['delete_product_id']);
header("Location: ../../pages/archived.php?tab=products");
exit;