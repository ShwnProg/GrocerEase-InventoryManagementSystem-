<div class="menu-table">
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Supplier Name</th>
                <th>Contact Person</th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Company Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = (($page - 1) * $per_page) + 1; ?>
            <?php foreach ($supplier as $sup): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($sup['supplier_name']) ?></td>
                    <td><?= htmlspecialchars($sup['contact_person']) ?></td>
                    <td><?= htmlspecialchars($sup['phone_number']) ?></td>
                    <td><?= htmlspecialchars($sup['address']) ?></td>
                    <td><?= htmlspecialchars($sup['company_name']) ?></td>
                    <td class="email-cell"><?= htmlspecialchars($sup['email']) ?></td>
                    

                    <td>
                        <div class="actions">
                            <button type="button" class="edit-btn" style="display:flex; align-items:center; gap:3px; font-size:12px; padding:3px 6px;"
                                onclick="restoreSupplier('<?= $sup['supplier_id_pk'] ?>', '<?= htmlspecialchars($sup['supplier_name']) ?>')">
                                <i class="fa-solid fa-recycle"></i> Recover
                            </button>
                            <button type="button" class="edit-btn" style="display:flex; align-items:center; gap:3px; font-size:12px; padding:3px 6px;"
                                onclick="hardDeleteSupplier('<?= $sup['supplier_id_pk'] ?>', '<?= htmlspecialchars($sup['supplier_name']) ?>')">
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
    $window = 10;
    $current_chunk = (int) floor(($page - 1) / $window);
    $start = $current_chunk * $window + 1;
    $end = min($total_pages, $start + $window - 1);
    ?>


    <?php if ($total_pages > 1): ?>
        <div class="pagination-wrapper">
            <?php if ($page > 1): ?>
                <a href="?<?= $base_query ? $base_query . '&' : '' ?><?= $page_param ?>=<?= $page - 1 ?>" class="page-btn">&laquo; Prev</a>
            <?php else: ?>
                <span class="page-btn disabled">&laquo; Prev</span>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="?<?= $base_query ? $base_query . '&' : '' ?><?= $page_param ?>=<?= $i ?>"
                    class="page-btn <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?<?= $base_query ? $base_query . '&' : '' ?><?= $page_param ?>=<?= $page + 1 ?>" class="page-btn">Next &raquo;</a>
            <?php else: ?>
                <span class="page-btn disabled">Next &raquo;</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>