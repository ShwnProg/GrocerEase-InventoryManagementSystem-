<?php
session_start();
require_once '../models/stock_movements.php';

include "../includes/auth_check.php";

$_SESSION['page_title'] = "INVENTORY LOGS";

$movement = new StockMovements();

$stock_movements = $movement->GetStockMovements();

?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php" ?>


<body>

    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../includes/topbar.php'; ?>
        <section class="page-content">

            <div class="toolbar">
                <div class="search-area">
                    <form action="">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search a inventory">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
                <!-- <div class="filters"> -->
                <!-- 
                    <label for="type">Type</label>
                    <select name="type" id="">
                        <option value="">All</option>
                        <option value="in">STOCK IN</option>
                        <option value="out">STOCK OUT</option>
                    </select>

                    <label for="date">Date Range</label>
                    <select name="date" id="">
                        <option value="">Date Range</option>
                    </select> -->

                <!-- </div> -->
            </div>


            <div class="menu-table">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Type</th>
                            <th>Reference Id</th>
                            <th>Remarks</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>
                        <?php foreach ($stock_movements as $stocks): ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= htmlspecialchars($stocks['product_name'] ?? 'Deleted Product') ?></td>
                                <td>
                                    <?php if ($stocks['reference_type'] == 'IN'): ?>
                                        <span style="color: #32702b; font-weight: 600;">
                                            +<?= $stocks['quantity'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #c82828; font-weight: 600;">
                                            -<?= $stocks['quantity'] ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $stocks['reference_type'] == 'IN' ? 'active' : 'inactive' ?>">
                                        <?= $stocks['reference_type'] == 'IN' ? 'IN' : 'OUT' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($stocks['reference_id']) ?></td>
                                <td><?= htmlspecialchars($stocks['reason'] == ''? 'N/A' : $stocks['reason']) ?></td>
                                <td><?= htmlspecialchars($stocks['date']) ?></td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </section>
    </main>
</body>
<script src="../scripts/pages.js"></script>
</html>