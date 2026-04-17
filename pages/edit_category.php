<?php
session_start();
require_once "../models/categories.php";
require_once "../models/categories.php";

if (isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
} else {
    $category_id = $_GET["category_id"] ?? null;
}


$category = new Category();

$category_name = $category->GetCategoryNameById($category_id);
$category_info = $category->GetCategoryById($category_id);
$categories = $category->GetAllCategories();


// var_dump($_POST);
$_SESSION['page_title'] = "EDIT CATEGORY";
$_SESSION['category_id'] = $category_id;

$error = $_SESSION['edit_error_msg'] ?? '';
$old_inputs = $_SESSION['edit_old_inputs'] ?? '';
$success_msg = $_SESSION['edit_success_msg'] ?? '';

unset($_SESSION['edit_error_msg'], $_SESSION['edit_old_inputs'],$_SESSION['edit_success_msg']);
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php" ?>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class='main-content'>
        <?php include '../includes/topbar.php'; ?>
        <section class="page-content">
            <div class="tool-bar">
                <a href="categories.php" class="back-btn"><i class="fas fa-arrow-left"></i> back</a>
            </div>

            <div class="edit-content">
                <form action="../validation/categories/edit_process.php" method="POST">


                    <i class="fa-solid fa-pen-to-square"></i>
                    <p> EDIT CATEGORY </p>

                    <?php if (!empty($success_msg)): ?>
                        <div class="success-message">
                            <?= htmlspecialchars($success_msg) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error['no_changes'])): ?>
                        <div class="no-changes">
                            <?= htmlspecialchars($error['no_changes']) ?>
                        </div>
                    <?php endif; ?>
                        <input type="hidden" name = 'category_id' value = <?=  $category_id ?>>
                    <!-- CATEGORY NAME -->
                    <div class="input">
                        <label for="category_name">Category Name</label>
                        <i class="fas fa-box"></i>
                        <input type="text" name="category_name"
                            value="<?= htmlspecialchars($old_inputs['category_name'] ?? $category_info['category_name'] ?? '') ?>">
                    </div>

                    <?php if (!empty($error['category_name'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['category_name']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- DESCFRIPTION -->
                    <div class="input">
                        <label for="category_description">Description (OPTIONAL) </label>
                        <i class="fas fa-align-left"></i>
                        <textarea name="category_description"
                            id=""><?= htmlspecialchars($old_inputs['category_description'] ?? $category_info['category_description'] ?? '') ?></textarea>
                    </div>
                    <?php if (!empty($error['category_description'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['description']) ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" name="edit_btn">SAVE CHANGES</button>
                </form>
            </div>
        </section>
    </main>
</body>

</html>