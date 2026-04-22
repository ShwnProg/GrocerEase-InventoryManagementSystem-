<?php
session_start();
require_once '../../models/categories.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $category = new Category();
    $result = $category->RestoreCategory($_POST['category_id']);

    $_SESSION['archive_msg'] = $result
        ? ['type' => 'success', 'text' => 'Category restored successfully.']
        : ['type' => 'error', 'text' => 'Failed to restore category.'];
}

unset($_SESSION['restore_category_id']);
header("Location: ../../pages/archived.php?tab=categories");
exit;