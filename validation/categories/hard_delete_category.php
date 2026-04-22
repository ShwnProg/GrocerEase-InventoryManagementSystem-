<?php
session_start();
require_once '../../models/categories.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $category = new Category();
    $result = $category->HardDeleteCategory($_POST['category_id']);

    $_SESSION['archive_msg'] = $result
        ? ['type' => 'success', 'text' => 'Category permanently deleted.']
        : ['type' => 'error', 'text' => 'Failed to delete category.'];
}

unset($_SESSION['delete_category_id']);
header("Location: ../../pages/archived.php?tab=categories");
exit;