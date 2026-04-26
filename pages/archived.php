<?php
session_start();
require_once __DIR__ . '../../autoload.php';


include "../includes/auth_check.php";

$current = basename($_SERVER['PHP_SELF']);
$tab = $_GET['tab'] ?? 'products'; 
$_SESSION['page_title'] = "ARCHIVED";

$product = new Product($db);
$categories = new Category($db);
$suppliers = new Supplier($db);

$products = $product->GetDeletedProducts();
$category = $categories->GetDeletedCategories();
$supplier = $suppliers->GetDeletedSuppliers();

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
                    <form method="get">
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
                    include 'archives/deleted_products.php';
                elseif ($tab == 'categories')
                    include 'archives/deleted_categories.php';
                elseif ($tab == 'suppliers')
                    include 'archives/deleted_suppliers.php';
                ?>
            </div>

        </div>
    </div>
</body>
<script src="../scripts/pages.js"></script>
</html>