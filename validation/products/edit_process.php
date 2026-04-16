<?php
require_once "../../models/product.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $product = new Product();
    // GET PRODUCT ID
    $product_id = $_SESSION['product_id'] ?? '';
    // GET INPUT
    $product_name = ucfirst(trim($_POST['product_name'] ?? ''));
    $category = $_POST['category'] ?? '';
    $selling_price = $_POST['selling_price'] ?? '';
    $description = ucfirst(trim($_POST['product_description'] ?? ''));
    $status = $_POST['status'] ?? '';

    $error = [];

    // HANDLE EMPTY FIELDS
    if (empty($product_name))
        $errors['product_name'] = "Product name is required.";
    if (empty($category))
        $errors['category'] = "Category is required.";
    if (empty($selling_price))
        $errors['selling_price'] = "Selling price is required.";
    if ($status === '')
        $errors['status'] = "Status is required.";

    // CHECK OLD INPUT IF NO CHANGES
    $original = $product->GetProductInfoById($product_id);

    // echo "<pre>";
    // var_dump($original);
    // var_dump($_POST);
    // echo "</pre>";

    $is_true = false;
    if (!empty($product_name) && !empty($category) && !empty($selling_price) && $status !== '') {
        $is_true = IsSameData($original, $product_name, $category, $selling_price, $description, $status) ? true : false;
        // var_dump(IsSameData($original, $product_name, $category, $selling_price, $description, $status));
    }

    //IF NOT SAME INFORMATION, VALidATE ALL THE INPUTS
    if (!$is_true) {
        if (empty($errors['product_name']) && strlen($product_name) > 100)
            $errors['product_name'] = "Product name must not exceed 100 characters.";

        if (empty($errors['product_name']) && empty($errors['category']))
            if ($product_name != $original['product_name'] && $category != $original['category_id_fk'])
                if ($product->CheckDuplicateProduct($product_name, $category))
                    $errors['product_name'] = "Product already exists in the selected category.";

        if (empty($errors['selling_price'])) {
            if (!is_numeric($selling_price))
                $errors['selling_price'] = "Selling price must be a valid number.";
            elseif ($selling_price < 0)
                $errors['selling_price'] = "Selling price must be a positive number.";
        }
    } else {
        $errors['no_changes'] = 'No Changes';
    }

    if (!empty($errors)) {
        $_SESSION['error'] = $errors;
        $_SESSION['old'] = $_POST;
        header("Location: ../../pages/edit_product.php?product_id=$product_id");
        exit;
    }
    // var_dump($is_true);

    $product_name = htmlspecialchars($product_name);
    $description = htmlspecialchars($description);
    $selling_price = filter_var($selling_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    $result = $product->UpdateProductInfo($product_id, $product_name, $category, $selling_price, $description, $status);

    if ($result) {
        $_SESSION['success'] = "Product updated successfully";
    } else {
        $_SESSION['error'] = "Something went wrong";
    }
    header("Location: ../../pages/edit_product.php?product_id=$product_id");
    exit;   

}
function IsSameData($original, $product_name, $category, $selling_price, $description, $status)
{
    if (
        $product_name == $original['product_name'] &&
        $category == $original['category_id_fk'] &&
        $description == $original['product_description'] &&
        $selling_price == $original['selling_price'] &&
        $status == $original['status']
    ) {
        return true;
    }
    return false;
}
?>