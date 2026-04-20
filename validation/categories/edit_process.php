<?php
require_once '../../models/categories.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = new Category();

    $category_id = $_SESSION['category_id'] ?? '';

    $category_name = ucfirst(trim($_POST['category_name'] ?? ''));
    $category_description = trim($_POST['category_description'] ?? '');

    $error = [];

    if (empty($category_name)) {
        $error['category_name'] = 'Category name is required.';
    }

    $original = $category->GetCategoryById($category_id);
    // var_dump($original);

    $isTrue = false;
    if (!empty($category_name)) {
        $isTrue = IsSameData($original, $category_name, $category_description) ? true : false;
        // var_dump(IsSameData($original, $category_name, $category_id, $category_description));
    }

    if (!$isTrue) {
        if ($category_name != $original['category_name']) {
            if ($category->CheckDuplicateCategory($category_name)) {
                $error['category_name'] = "Category already exists.";
            }
        }

        if (!empty($category_description) && strlen($category_description) > 255) {
            $error['category_description'] = 'Category description must not exceed 255 characters.';
        }

        if (!empty($category_name) && strlen($category_name) < 4) {
            $error['category_name'] = 'Category name must be at least 4 characters.';
        }

        if (!empty($category_name) && strlen($category_name) > 50) {
            $error['category_name'] = 'Category name must not exceed 50 characters.';
        }
    } else {
        $error['no_changes'] = 'No Changes';
    }


    if (!empty($error)) {
        $_SESSION['edit_error_msg'] = $error;
        $_SESSION['edit_old_inputs'] = $_POST;
        header("Location: ../../pages/edit_category.php?category_id=$category_id");
        exit;
    }

    $category_name = filter_var($category_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_description = filter_var($category_description, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $isEdited = $category->EditCategory($category_id, $category_name, $category_description);

    if ($isEdited) {
        $_SESSION['edit_success_msg'] = 'Category edited successfully.';
    } else {
        $_SESSION['edit_error_msg'] = 'Failed to edit category. Please try again.';
    }

    header("Location: ../../pages/edit_category.php?category_id=$category_id");
    exit;
}
function IsSameData($original, $category_name, $category_description)
{
    if (
        $category_name == $original['category_name'] &&
        $category_description == $original['category_description']
    ) {
        return true;
    }
    return false;
}
