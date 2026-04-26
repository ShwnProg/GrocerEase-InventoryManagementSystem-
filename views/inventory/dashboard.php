<?php
session_start();
require_once __DIR__ . '../../../autoload.php';

include "../../includes/auth_check.php";

$_SESSION['page_title'] = "DASHBOARD";

$product = new Product($db);
$category = new Category($db);
$supplier = new Supplier($db);
$stock = new Stocks($db);

$total_product = $product->GetTotalProducts();
$total_categories = $category->GetTotalCategories();
$total_suppliers = $supplier->GetTotalSupplier();
$total_quantity = $stock->GetTotalStockQuantity();
$low_stock = $stock->GetTotalLowStockItems();
$out_of_stock = $stock->GetTotalOutOfStockItems();

?>
<!DOCTYPE html>
<html lang="en">

<?php include "../../includes/head.php" ?>


<body>
    <?php include '../../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../../includes/topbar.php'; ?>
        <section class='page-content'>
            <!-- KPI GRID -->
            <div class="kpi-grid">
                <div class="grid">
                    <div class="card-icon"><i class="fas fa-box"></i></div>
                    <span>Active Products</span>
                    <p><?= $total_product == '' ? '<small>No Active Products</small>' : $total_product ?></p>
                </div>
                <div class="grid">
                    <div class="card-icon"><i class="fas fa-folder"></i></div>
                    <span>Total Categories</span>
                    <p><?= $total_categories == 0 ? '<small>No categories</small>' : $total_categories ?></p>
                </div>
                <div class="grid">
                    <div class="card-icon"><i class="fas fa-truck"></i></div>
                    <span>Total Supplier</span>
                    <p><?= $total_suppliers == 0 ? '<small>No suppliers</small>' : $total_suppliers ?></p>
                </div>
                <div class="grid">
                    <div class="card-icon"><i class="fas fa-cubes"></i></div>
                    <span>Total Stocks</span>
                    <p><?= $total_quantity == 0 ? '<small>No Stocks</small>' : $total_quantity ?></p>
                </div>
                <div class="grid">
                    <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <span>Low Stock Items</span>
                    <p><?= $low_stock == 0 ? '<small>No Low Stocks</small>' : $low_stock ?></p>
                </div>
                <div class="grid">
                    <div class="card-icon"><i class="fas fa-ban"></i></div>
                    <span>Out of Stock Items</span>
                    <p><?= $out_of_stock == 0 ? '<small>No out of stock</small>' : $out_of_stock ?></p>
                </div>
            </div>
        </section>
    </main>
</body>

</html>