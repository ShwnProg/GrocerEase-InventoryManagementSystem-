<?php
session_start();
require_once '../models/stocks.php';

include "../includes/auth_check.php";

$_SESSION['page_title'] = "STOCK";

$stocks = new Stock();
$all_stocks = $stocks->GetAllStocks();

$open_modal_stockin = isset($_SESSION['error']['in']) || isset($_SESSION['success']['in']);
$open_modal_stockout = isset($_SESSION['error']['out']) || isset($_SESSION['success']['out']);

$error_in = $_SESSION['error']['in'] ?? [];
$error_out = $_SESSION['error']['out'] ?? [];
$success_in = $_SESSION['success']['in'] ?? [];
$success_out = $_SESSION['success']['out'] ?? [];
$old = $_SESSION['old'] ?? [];

unset(
    $_SESSION['error']['in'],
    $_SESSION['success']['in'],
    $_SESSION['error']['out'],
    $_SESSION['success']['out'],
    $_SESSION['old']
);
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
                        <input type="text" name="search" id="search" placeholder="Search a stock">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
            </div>

            <!-- TABLE -->
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
                            <?php if ($stock['is_deleted'] == 1 || $stock['status'] == 0)
                                continue; ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($stock['product_name']) ?></td>
                                <td><?= htmlspecialchars($stock['category_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($stock['quantity']) ?></td>
                                <td><?= htmlspecialchars($stock['last_updated']) ?></td>
                                <td>
                                    <div class="actions">
                                        <button class="btn btn-in open-stock-in" data-id="<?= $stock['product_id_fk'] ?>"
                                            data-name="<?= htmlspecialchars($stock['product_name']) ?>">
                                            <i class="fa-solid fa-circle-plus"></i>
                                            <span>Stock IN</span>
                                        </button>
                                        <button class="btn btn-out open-stock-out" data-id="<?= $stock['product_id_fk'] ?>"
                                            data-name="<?= htmlspecialchars($stock['product_name']) ?>">
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

            <!-- STOCK IN MODAL -->
            <div class="add-modal <?= $open_modal_stockin ? 'active' : '' ?>" id="stock-in-modal">
                <form action="../validation/stock/stock_in_process.php" method="POST">
                    <div class="header">
                        <i class="fa-solid fa-circle-plus"></i>
                        <p>Stock IN — <b id="stock-in-product-name" style="color:#1c5515; font-weight:600;">
                                <?= htmlspecialchars($old['product_name'] ?? '') ?>
                            </b></p>
                        <span id="close-stock-in">&times;</span>
                    </div>
                    <div class="body">

                        <input type="hidden" name="product_id" id="stock-in-product-id"
                            value="<?= htmlspecialchars($old['product_id'] ?? '') ?>">
                        <input type="hidden" name="product_name" id="stock-in-product-name-input"
                            value="<?= htmlspecialchars($old['product_name'] ?? '') ?>">

                        <?php if (!empty($success_in['form'])): ?>
                            <div class="success-message"><?= htmlspecialchars($success_in['form']) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($error_in['form'])): ?>
                            <div class="error-message"><?= htmlspecialchars($error_in['form']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label>Quantity</label>
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <input type="number" name="quantity" placeholder="Enter quantity" min="1"
                                value="<?= htmlspecialchars($old['quantity'] ?? '') ?>">
                        </div>

                        <?php if (!empty($error_in['quantity'])): ?>
                            <div class="error-message"><?= htmlspecialchars($error_in['quantity']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label>Reason (Optional)</label>
                            <i class="fas fa-align-left"></i>
                            <textarea name="reason"
                                placeholder="e.g. New delivery, restocking..."><?= htmlspecialchars($old['reason'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" name="stock_in_btn">Confirm Stock IN</button>
                    </div>
                </form>
            </div>

            <!-- STOCK OUT MODAL -->
            <div class="add-modal <?= $open_modal_stockout ? 'active' : '' ?>" id="stock-out-modal">
                <form action="../validation/stock/stock_out_process.php" method="POST">
                    <div class="header">
                        <i class="fa-solid fa-circle-minus" style="background:rgba(200,40,40,0.08); color:#c82828;"></i>
                        <p>Stock OUT — <b id="stock-out-product-name" style="color:#c82828; font-weight:600;">
                                <?= htmlspecialchars($old['product_name'] ?? '') ?>
                            </b></p>
                        <span id="close-stock-out">&times;</span>
                    </div>
                    <div class="body">

                        <input type="hidden" name="product_id" id="stock-out-product-id"
                            value="<?= htmlspecialchars($old['product_id'] ?? '') ?>">
                        <input type="hidden" name="product_name" id="stock-out-product-name-input"
                            value="<?= htmlspecialchars($old['product_name'] ?? '') ?>">

                        <?php if (!empty($success_out['form'])): ?>
                            <div class="success-message"><?= htmlspecialchars($success_out['form']) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($error_out['form'])): ?>
                            <div class="error-message"><?= htmlspecialchars($error_out['form']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label>Quantity</label>
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <input type="number" name="quantity" placeholder="Enter quantity" min="1"
                                value="<?= htmlspecialchars($old['quantity'] ?? '') ?>">
                        </div>

                        <?php if (!empty($error_out['quantity'])): ?>
                            <div class="error-message"><?= htmlspecialchars($error_out['quantity']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label>Reason (Optional)</label>
                            <i class="fas fa-align-left"></i>
                            <textarea name="reason"
                                placeholder="e.g. Damaged goods, sold out..."><?= htmlspecialchars($old['reason'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" name="stock_out_btn" style="background:#c82828;">Confirm Stock
                            OUT</button>
                    </div>
                </form>
            </div>

        </section>
    </main>
</body>
<script src="../scripts/pages.js"></script>

</html>