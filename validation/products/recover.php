<?php
session_start();
require_once '../../models/product.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product = new Product();
    $result = $product->RestoreProduct($_POST['product_id']);

    $_SESSION['archive_msg'] = $result
        ? ['type' => 'success', 'text' => 'Product restored successfully.']
        : ['type' => 'error', 'text' => 'Failed to restore product.'];
}

unset($_SESSION['restore_product_id']);
header("Location: ../../pages/archived.php?tab=products");
exit;