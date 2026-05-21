<div class="menu-table">
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Category Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = (($page - 1) * $per_page) + 1; ?>
            <?php if (empty($category)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; color:#6b7280;">
                        No archived categories found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($category as $cat): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($cat['category_name']) ?></td>
                    <td><?= htmlspecialchars($cat['category_description'] == '' ? 'No description available' : $cat['category_description']) ?>
                    </td>
                    <td>
                        <span class="badge <?= $cat['status'] == 1 ? 'active' : 'inactive' ?>">
                            <?= $cat['status'] == 1 ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <div class="actions">
                            <button type="button" class="edit-btn" style="display:flex; align-items:center; gap:3px; font-size:12px; padding:3px 6px;"
                                onclick="restoreCategory('<?= $cat['category_id_pk'] ?>', '<?= htmlspecialchars($cat['category_name']) ?>')">
                                <i class="fa-solid fa-recycle"></i> Recover
                            </button>
                            <!-- DELETE ACTION -->
                            <button type="button" class="edit-btn" style="display:flex; align-items:center; gap:3px; font-size:12px; padding:3px 6px;"
                                onclick="hardDeleteCategory('<?= $cat['category_id_pk'] ?>', '<?= htmlspecialchars($cat['category_name']) ?>')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
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
