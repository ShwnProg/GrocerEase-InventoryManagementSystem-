<?php
session_start();
require_once __DIR__ . '/../../autoload.php';


include "../../includes/auth_check.php";

$current = basename($_SERVER['PHP_SELF']);
$tab = $_GET['tab'] ?? 'products';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = 10;
$per_page = $limit;
$base_query = 'tab=' . urlencode($tab);
if ($search !== '') {
    $base_query .= '&search=' . urlencode($search);
}
$page_param = 'page';
$_SESSION['page_title'] = "ARCHIVED";

$product = new Product($db);
$categories = new Category($db);
$suppliers = new Supplier($db);

// Initialize all variables to prevent undefined variable notices
$products    = [];
$category    = [];
$supplier    = [];
$total_items = 0;
$total_pages = 1;

if ($tab == 'products') {
    $products = $product->GetDeletedProductsPaginated($page, $limit);
    $total_items = $product->GetTotalDeletedProducts();
    $total_pages = (int) ceil($total_items / $limit);
} elseif ($tab == 'categories') {
    $category = $categories->GetDeletedCategoriesPaginated($page, $limit);
    $total_items = $categories->GetTotalDeletedCategories();
    $total_pages = (int) ceil($total_items / $limit);
} else {
    $supplier = $suppliers->GetDeletedSuppliersPaginated($page, $limit);
    $total_items = $suppliers->GetTotalDeletedSuppliers();
    $total_pages = (int) ceil($total_items / $limit);
}

?>
<!DOCTYPE html>
<html lang="en">

<?php include "../../includes/head.php" ?>

<body>
    <?php include '../../includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../../includes/topbar.php'; ?>
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
                        <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
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
</body>
<script src="<?= ASSET_URL ?>/js/pages.js"></script>
</html>