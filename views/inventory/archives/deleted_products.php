<div class="menu-table">
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Product</th>
                <th>Category</th>
                <th>Cost Price</th>
                <th>Selling Price</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = (($page - 1) * $per_page) + 1; ?>
            <?php foreach ($products as $prod): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($prod['product_name']) ?></td>
                    <td>
                        <?= htmlspecialchars(
                            isset($prod['category_name'])
                                ? $prod['category_name'] . ($prod['category_status'] == 0 ? ' (Inactive)' : '')
                                : 'Uncategorized'
                        ) ?>
                    </td>
                    <td><?= $prod['cost_price'] !== null ? '₱' .  number_format($prod['cost_price'], 2) : '<span style="color:#6b7280;">N/A</span>' ?></td>
                    <td>₱<?= number_format($prod['selling_price'], 2) ?></td>
                    <td><?= htmlspecialchars($prod['product_description'] == '' ? 'No description available' : $prod['product_description']) ?>
                    </td>
                    <td>
                        <span class="badge <?= $prod['status'] == 1 ? 'active' : 'inactive' ?>">
                            <?= $prod['status'] == 1 ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <div class="actions">

                            <button type="button" class="edit-btn" style="display:flex; align-items:center; gap:3px; font-size:12px; padding:3px 6px;"
                                onclick="restoreProduct('<?= $prod['product_id_pk'] ?>', '<?= htmlspecialchars($prod['product_name']) ?>')">
                                <i class="fa-solid fa-recycle"></i> Recover
                            </button>

                            <button type="button" class="edit-btn" style="display:flex; align-items:center; gap:3px; font-size:12px; padding:3px 6px;"
                                onclick="hardDeleteProduct('<?= $prod['product_id_pk'] ?>', '<?= htmlspecialchars($prod['product_name']) ?>')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>

                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- PAGINATION -->
    <?php
    $window = 5;
    $current_chunk = (int) floor(($page - 1) / $window);
    $start = $current_chunk * $window + 1;
    $end = min($total_pages, $start + $window - 1);
    ?>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?<?= $base_query ?>&<?= $page_param ?>=<?= $page - 1 ?>" class="page-btn">&laquo; Prev</a>
            <?php else: ?>
                <span class="page-btn disabled">&laquo; Prev</span>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="?<?= $base_query ?>&<?= $page_param ?>=<?= $i ?>"
                    class="page-btn <?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?<?= $base_query ?>&<?= $page_param ?>=<?= $page + 1 ?>" class="page-btn">Next &raquo;</a>
            <?php else: ?>
                <span class="page-btn disabled">Next &raquo;</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>