<?php
session_start();
require_once '../models/stocks.php';

include "../includes/auth_check.php";

$_SESSION['page_title'] = "STOCK";

$stocks = new Stock();
$all_stocks = $stocks->GetAllStocks();
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php"?>


<body>

    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../includes/topbar.php'; ?>
        <div class="page-content">

            <div class="toolbar">
                <div class="search-area">
                    <form action="">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search a stock">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
                <div class="add">
                    <button id="stockin">Stock IN</button>
                    <button id="stockout">Stock OUT</button>
                </div>
            </div>

            <!-- table -->
            <div class="menu-table">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($all_stocks as $stock): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($stock['product_name']) ?></td>
                                <td><?= htmlspecialchars($stock['category_name']) ?></td>
                                <td><?= htmlspecialchars($stock['quantity']) ?></td>
                                <td><?= htmlspecialchars($stock['last_updated']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
<script src="../scripts/pages.js"></script>
</html>