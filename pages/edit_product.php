<?php
session_start();
require_once __DIR__ . '../../autoload.php';


if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
} else {
    $product_id = $_GET["product_id"] ?? null;
}

$product = new Product($db);
$category = new Category($db);

$product_name = $product->GetProductNameById($product_id);
$product_info = $product->GetProductInfoById($product_id);
$categories = $category->GetAllCategories();


// var_dump($_POST);
$_SESSION['page_title'] = "EDIT PRODUCT";
$_SESSION['product_id'] = $product_id;

$error = $_SESSION['error'] ?? '';
$old = $_SESSION['old'] ?? '';
$success = $_SESSION['success'] ?? '';

unset($_SESSION['error'], $_SESSION['old'], $_SESSION['success']);
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
                <a href="products.php" class="back-btn"><i class="fas fa-arrow-left"></i> back</a>
            </div>

            <div class="edit-content">
                <form action="../validation/products/edit_process.php" method="POST">


                    <i class="fa-solid fa-pen-to-square"></i>
                    <p> EDIT PRODUCT </p>

                    <?php if (!empty($success)): ?>
                        <div class="success-message">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error['no_changes'])): ?>
                        <div class="no-changes">
                            <?= htmlspecialchars($error['no_changes']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- PRODUCT NAME -->
                    <div class="input">
                        <label for="product_name">Product Name</label>
                        <i class="fas fa-box"></i>
                        <input type="text" name="product_name"
                            value="<?= htmlspecialchars($old['product_name'] ?? $product_info['product_name'] ?? '') ?>">
                    </div>

                    <?php if (!empty($error['product_name'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['product_name']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- CATEGORY -->
                    <div class="input">
                        <label>Category</label>
                        <select name="category">
                            <option value="">Select a category</option>

                            <?php foreach ($categories as $cat): ?>
                                <?php
                                if ($cat['is_deleted'] == 1)
                                    continue;

                                $current = $old['category'] ?? ($product_info['category_id_pk'] ?? '');

                                $isSelected = ($current != '' && $current == $cat['category_id_pk']);
                                $isInactive = ($cat['status'] == 0);
                                ?>

                                <option value="<?= $cat['category_id_pk'] ?>" <?= $isSelected ? 'selected' : '' ?>
                                    <?= (!$isSelected && $isInactive) ? 'disabled' : '' ?>>

                                    <?= htmlspecialchars($cat['category_name']) ?>
                                    <?= $isInactive ? ' (Inactive)' : '' ?>
                                </option>

                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (!empty($error['category'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['category']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- SELLING PRICE -->
                    <div class="input">
                        <label for="selling_price">Selling Price</label>
                        <i class="fas fa-tag"></i>
                        <input type="text" name="selling_price"
                            value="<?= htmlspecialchars($old['selling_price'] ?? $product_info['selling_price'] ?? '') ?>">
                    </div>
                    <?php if (!empty($error['selling_price'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['selling_price']) ?>
                        </div>
                    <?php endif; ?>
                    <!-- DESCFRIPTION -->
                    <div class="input">
                        <label for="product_description">Description (OPTIONAL) </label>
                        <i class="fas fa-align-left"></i>
                        <textarea name="product_description"
                            id=""><?= htmlspecialchars($old['product_description'] ?? $product_info['product_description'] ?? '') ?></textarea>
                    </div>
                    <?php if (!empty($error['description'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['description']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- STATUS -->
                    <div class="input">
                        <label for="status">Status</label>
                        <select name="status" id="">
                            <option value="">Select a status</option>
                            <option value="1" <?= ($old['status'] ?? $product_info['status'] ?? '') == '1' ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= ($old['status'] ?? $product_info['status'] ?? '') == '0' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <?php if (!empty($error['status'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['status']) ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" name="edit_btn">SAVE CHANGES</button>
                </form>
            </div>
        </section>
    </main>
</body>

</html>