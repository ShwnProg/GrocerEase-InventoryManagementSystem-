<?php
    session_start();
    require_once '../../models/product.php';

    $id = $_POST['product_id'] ?? '';
    $product = new Product();

    $result = $product->SoftDeleteProduct($id);

    if($result){
        $_SESSION['success'] = ["success_deleted" => "Product deleted successfully."];
    } else {
        $_SESSION['errors'] = ['delete_error' => "Failed to delete the product. Please try again."];
    }

    header("Location: ../../pages/products.php");
    exit;
?>