<?php
session_start();
require_once __DIR__ . '../../../autoload.php';

include "../../includes/auth_check.php";

$_SESSION['page_title'] = "DASHBOARD";

$product = new Product($db);
$category = new Category($db);
$supplier = new Supplier($db);
$stock = new Stocks($db);
$stock_movements = new StockMovements($db);

// KPI counts
$total_product = $product->GetTotalProducts();
$total_categories = $category->GetTotalCategories();
$total_suppliers = $supplier->GetTotalSupplier();
$total_quantity = $stock->GetTotalStockQuantity();
$low_stock = $stock->GetTotalLowStockItems();
$out_of_stock = $stock->GetTotalOutOfStockItems();

// Chart data
$stocks_per_category = $stock->GetTotalStockPerCategory();
$labels = [];
$data = [];

foreach ($stocks_per_category as $row) {
    $labels[] = $row['category_name'];
    $data[] = $row['total_stock'];
}

$stock_status = $stock->GetStockStatus();
$status = [];
$total = [];

foreach ($stock_status as $row) {
    $status[] = $row['stock_status'];
    $total[] = $row['total'];
}

$inventory_logs = $stock_movements->GetInventoryLogsTrend();

$date = [];
$stock_in = [];
$stock_out = [];

if (!empty($inventory_logs)) {
    foreach ($inventory_logs as $row) {
        $date[] = $row['date'];
        $stock_in[] = (int) $row['stock_in'];
        $stock_out[] = (int) $row['stock_out'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../../includes/head.php" ?>

<link rel="stylesheet" href="charts.css">

<body>
    <?php include '../../includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include '../../includes/topbar.php'; ?>

        <section class="page-content">

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

            <!-- CHARTS ROW -->
            <div class="charts-row">

                <!-- Bar Chart -->
                <div class="chart-card">
                    <h3>Stock Overview</h3>
                    <p>Total stock per category</p>
                    <hr>
                    <div class="chart-wrap">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <!-- Doughnut Chart -->
                <div class="chart-card">
                    <h3>Stock Status</h3>
                    <p>Inventory status overview</p>
                    <hr>
                    <div class="chart-wrap">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>

                <!-- Line Chart -->
                <div class="chart-card">
                    <h3>Inventory Trend</h3>
                    <p>Stock in vs stock out over time</p>
                    <hr>
                    <div class="chart-wrap">
                        <canvas id="inventoryChart"></canvas>
                    </div>
                </div>

            </div>

        </section>
    </main>
</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    //  Bar Chart 
    const categoryLabels = <?= json_encode($labels) ?>;
    const categoryData = <?= json_encode($data) ?>;

    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Stock',
                data: categoryData,
                backgroundColor: 'rgba(28, 85, 21, 0.7)',
                borderRadius: 5,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: 'rgba(0,0,0,0.05)' }, beginAtZero: true }
            }
        }
    });

    // PIE CJArt
    const statusLabels = <?= json_encode($status) ?>;
    const statusData = <?= json_encode($total) ?>;

    new Chart(document.getElementById('doughnutChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: [
                    'rgba(130, 185, 90, 0.75)',
                    'rgba(210, 232, 185, 0.9)',
                    'rgba(28, 85, 21, 0.85)'
                ],
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%'
        }
    });

    // Line chart
    const dateLabels = <?= json_encode($date) ?>;
    const stockIn = <?= json_encode($stock_in) ?>;
    const stockOut = <?= json_encode($stock_out) ?>;

    new Chart(document.getElementById('inventoryChart'), {
        type: 'line',
        data: {
            labels: dateLabels,
            datasets: [
                {
                    label: 'Stock In',
                    data: stockIn,
                    borderColor: 'rgba(28, 85, 21, 0.85)',
                    backgroundColor: 'rgba(28, 85, 21, 0.08)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Stock Out',
                    data: stockOut,
                    borderColor: 'rgba(200, 40, 40, 0.85)',
                    backgroundColor: 'rgba(200, 40, 40, 0.08)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: 'rgba(0,0,0,0.05)' }, beginAtZero: true }
            }
        }
    });
</script>

</html>