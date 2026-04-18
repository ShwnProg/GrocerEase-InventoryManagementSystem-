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
                        <input type="text" name="search" id="search" placeholder="Search a stock">
                        <button type="submit">SEARCH</button>
                    </form>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($all_stocks as $stock): ?>
                            <tr>
                                <?php if ($stock['is_deleted'] == 1)
                                    continue; ?>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($stock['product_name']) ?></td>
                                <td><?= htmlspecialchars($stock['category_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($stock['quantity']) ?></td>
                                <td><?= htmlspecialchars($stock['last_updated']) ?></td>
                                <td>
                                    <div class="actions">
                                        <button class="btn btn-in">
                                            <i class="fa-solid fa-circle-plus"></i>
                                            <span>Stock IN</span>
                                        </button>

                                        <button class="btn btn-out">
                                            <i class="fa-solid fa-circle-minus"></i>
                                            <span>Stock OUT</span>
                                        </button>
                                    </div>
                                </td>
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