<?php
require_once '../../models/product.php';
require_once '../../models/stocks.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_name = ucfirst(trim($_POST['product_name'] ?? ''));
    $category = $_POST['category'] ?? '';
    $selling_price = $_POST['selling_price'] ?? '';
    $description = ucfirst(trim($_POST['product_description'] ?? ''));
    $status = $_POST['status'] ?? '';

    $errors = [];

    // CHECK FOR EMPTY FIELDS
    if (empty($product_name))
        $errors['product_name'] = "Product name is required.";
    if (empty($category))
        $errors['category'] = "Category is required.";
    if (empty($selling_price))
        $errors['selling_price'] = "Selling price is required.";
    if ($status === '')
        $errors['status'] = "Status is required.";

    // VALIDATE INPUTS
    $product = new Product();

    if (empty($errors['product_name']) && strlen($product_name) > 100)
        $errors['product_name'] = "Product name must not exceed 100 characters.";

    if (empty($errors['product_name']) && empty($errors['category']))
        if ($product->CheckDuplicateProduct($product_name, $category))
            $errors['product_name'] = "Product already exists in the selected category.";

    if (empty($errors['selling_price'])) {
        if (!is_numeric($selling_price))
            $errors['selling_price'] = "Selling price must be a valid number.";
        elseif ($selling_price < 0)
            $errors['selling_price'] = "Selling price must be a positive number.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = ['add' => $errors];
        $_SESSION['old'] = $_POST;
        header("Location: ../../pages/products.php");
        exit;
    }

    $stock = new Stock();
    // SANITIZE INPUTS BEFORE DB INSERTION
    $product_name = htmlspecialchars($product_name);
    $description = htmlspecialchars($description);
    $selling_price = filter_var($selling_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // INSERT INTO DB
    $product_id = $product->AddProduct($product_name, $category, $selling_price, $description, $status);

    if ($product_id) {

        $stock = new Stock();

        $stock_result = $stock->AddProductStock(
            $product_id,
            0, // default quantity
            date('Y-m-d H:i:s')
        );

        if ($stock_result) {

            $_SESSION['success'] = ['add' => "Product added successfully."];
        }
    } else {
        $_SESSION['errors'] = ['add' => ['form' => "Failed to add product. Please try again."]];
        $_SESSION['old'] = $_POST;
    }

    header("Location: ../../pages/products.php");
    exit;
}
?>