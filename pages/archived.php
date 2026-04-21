<?php
session_start();
require_once '../models/product.php';
require_once '../models/categories.php';
require_once '../models/supplier.php';

include "../includes/auth_check.php";

$current = basename($_SERVER['PHP_SELF']);
$tab = $_GET['tab'] ?? 'products'; // default: products
$_SESSION['page_title'] = "ARCHIVED";

$product = new Product();
$categories = new Category();
$suppliers = new Supplier();

$products = $product->GetDeletedProducts();
$category = $categories->GetDeletedCategories();
$supplier = $suppliers->GetDeletedSuppliers();

$confirm_restore = false;
$restore_product_id = '';
$restore_product_name = '';

if (isset($_POST['restore_btn'])) {
    $_SESSION['restore_product_id'] = $_POST['product_id'];
    header("Location: archived.php");
    exit;
}


if (isset($_SESSION['restore_product_id'])) {
    $restore_product_id = $_SESSION['restore_product_id'];
    $restore_product_name = $product->GetProductNameById($restore_product_id);
    $confirm_restore = true;
}


if (isset($_GET['cancel_restore'])) {
    unset($_SESSION['restore_product_id']);
    header("Location: archived.php");
    exit;
}

$confirm_delete = false;
$delete_product_id = '';
$delete_product_name = '';

if (isset($_POST['delete_btn'])) {
    $_SESSION['delete_product_id'] = $_POST['product_id'];
    header("Location: archived.php?tab=products");
    exit;
}

if (isset($_SESSION['delete_product_id'])) {
    $delete_product_id = $_SESSION['delete_product_id'];
    $delete_product_name = $product->GetProductNameById($delete_product_id);
    $confirm_delete = true;
}

if (isset($_GET['cancel_delete'])) {
    unset($_SESSION['delete_product_id']);
    header("Location: archived.php?tab=products");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php" ?>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../includes/topbar.php'; ?>
        <div class="page-content">
            <?php if (isset($_SESSION['archive_msg'])): ?>
                <p class="<?= $_SESSION['archive_msg']['type'] === 'success' ? 'success-message' : 'error-message' ?>">
                    <?= $_SESSION['archive_msg']['text'] ?>
                </p>
                <?php unset($_SESSION['archive_msg']); ?>
            <?php endif; ?>
            <div class="toolbar">

                <div class="search-area">
                    <form action="">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search...">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
                <div class="header-btn">
                    <a href="archived.php?tab=products" class="<?= $tab == 'products' ? 'active' : '' ?>">Products</a>
                    <a href="archived.php?tab=categories"
                        class="<?= $tab == 'categories' ? 'active' : '' ?>">Categories</a>
                    <a href="archived.php?tab=suppliers"
                        class="<?= $tab == 'suppliers' ? 'active' : '' ?>">Suppliers</a>
                </div>
            </div>

            <!-- table -->
            <div class="menu-table">
                <?php
                if ($tab == 'products')
                    include 'archive_tables/deleted_products.php';
                elseif ($tab == 'categories')
                    include 'archive_tables/deleted_categories.php';
                elseif ($tab == 'suppliers')
                    include 'archive_tables/deleted_suppliers.php';
                ?>
            </div>

        </div>
    </div>
</body>

</html>