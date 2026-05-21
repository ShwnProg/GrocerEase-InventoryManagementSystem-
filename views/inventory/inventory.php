<?php
// session_start();
require_once __DIR__ . '/../../autoload.php';

include "../../includes/auth_check.php";

$_SESSION['page_title'] = "INVENTORY LOGS";

$movement = new StockMovements($db);
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = 10;

$stock_movements = $movement->GetStockMovementsPaginated($page, $limit, $search);
$total_movements = $movement->GetTotalStockMovements($search);
$total_pages = (int) ceil($total_movements / $limit);
$base_query = $search !== '' ? 'search=' . urlencode($search) : '';
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../../includes/head.php" ?>


<body>

    <?php include '../../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../../includes/topbar.php'; ?>
        <section class="page-content">

            <div class="toolbar">
                <div class="search-area">
                    <form action="">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search inventory" value="<?= htmlspecialchars($search) ?>">
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
                        <?php $count = ($page - 1) * $limit + 1; ?>
                        <?php if (empty($stock_movements)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center; color:#6b7280;">
                                    No inventory logs found
                                </td>
                            </tr>
                        <?php else: ?>
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
                                <td><?= htmlspecialchars($stocks['reason'] == '' ? 'no remarks' : $stocks['reason']) ?></td>
                                <td><?= htmlspecialchars($stocks['date']) ?></td>

                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php
                $window = 5;
                $half = floor($window / 2);
                $start = max(1, $page - $half);
                $end = min($total_pages, $start + $window - 1);
                if ($end - $start + 1 < $window) {
                    $start = max(1, $end - $window + 1);
                }
                ?>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">&laquo; Prev</a>
                        <?php else: ?>
                            <span class="page-btn disabled">&laquo; Prev</span>
                        <?php endif; ?>

                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                                class="page-btn <?= $i === $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">Next &raquo;</a>
                        <?php else: ?>
                            <span class="page-btn disabled">Next &raquo;</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

        </section>
    </main>
</body>
<script src="<?= ASSET_URL ?>/js/pages.js"></script>

</html>
