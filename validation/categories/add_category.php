<?php
require_once '../../models/categories.php';

session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $category_name = trim(ucfirst($_POST['category_name']));
    $category_description = trim(ucfirst($_POST['category_description']));

    $Category = new category();

    $error =  [];
    // REQUIRED FIELD
    if(empty($category_name)){
        $error['category_name'] = 'Category name is required.';
    }
    // DUPLICATE CHECK
    if(!empty($category_name) && $Category->CheckDuplicateCategory($category_name)){
        $error['category_name'] = 'Category name already exists.';
    }

     if(!empty($category_description) && strlen($category_description) > 255){
        $error['category_description'] = 'Category description must not exceed 255 characters.';
    }
        // LENGTH VALIDATION
    if(!empty($category_name) && strlen($category_name) < 4 ){
        $error['category_name'] = 'Category name must be at least 4 characters.';
    }

    if(!empty($category_name) && strlen($category_name) > 50){
        $error['category_name'] = 'Category name must not exceed 50 characters.';
    }

    // IF MAY ERROR → BALIK PAGE
    if(!empty($error)){
        $_SESSION['add_category_error'] = $error;
        $_SESSION['old_inputs'] = $_POST;
        header('Location: ../../pages/categories.php');
        exit;
    }
    // SANITIZE INPUT (security)
    $category_name = filter_var($category_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_description = filter_var($category_description, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // INSERT DATA
    $isAdded = $Category->AddCategory($category_name, $category_description);

    if($isAdded){
        $_SESSION['success_msg'] = 'Category added successfully.';
    }
    else{
        $_SESSION['error_msg'] = 'Failed to add category. Please try again.';
    }

    header('Location: ../../pages/categories.php');
    exit;

}
?>
