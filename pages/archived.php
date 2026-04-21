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
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php" ?>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../includes/topbar.php'; ?>
        <div class="page-content">
            <div class="toolbar">
                <div class="search-area">
                    <form action="">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search...">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
                <div class="header-btn">
                    <a href="archived.php?tab=products"
                        class="<?= $tab == 'products' ? 'active' : '' ?>">Products</a>
                    <a href="archived.php?tab=categories"
                        class="<?= $tab == 'categories' ? 'active' : '' ?>">Categories</a>
                    <a href="archived.php?tab=suppliers"
                        class="<?= $tab == 'suppliers' ? 'active' : '' ?>">Suppliers</a>
                </div>
            </div>

            <!-- table -->
            <div class="menu-table">
                <?php
                    if ($tab == 'products') include 'archive_tables/deleted_products.php';
                    elseif ($tab == 'categories') include 'archive_tables/deleted_categories.php';
                    elseif ($tab == 'suppliers') include 'archive_tables/deleted_suppliers.php';
                ?>
            </div>

        </div>
    </div>
</body>
</html>