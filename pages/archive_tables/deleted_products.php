<div class="menu-table">
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Cost Price</th>
                <th>Selling Price</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($products as $prod): ?>
                <?php if ($prod['is_deleted'] == 0)
                    continue; ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($prod['product_name']) ?></td>
                    <td><?= htmlspecialchars($prod['category_name']) ?></td>
                    <td>₱<?= number_format($prod['cost_price'], 2) ?></td>
                    <td>₱<?= number_format($prod['selling_price'], 2) ?></td>
                    <td><?= htmlspecialchars($prod['product_description'] == '' ? 'N/A' : $prod['product_description']) ?>
                    </td>
                    <td>
                        <span class="badge <?= $prod['status'] == 1 ? 'active' : 'inactive' ?>">
                            <?= $prod['status'] == 1 ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <div class="actions">
                            <!-- RESTORE -->
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $prod['product_id_pk'] ?>">
                                <button type="submit" name="restore_btn" class="edit-btn">
                                    <i class="fa-solid fa-recycle"></i>
                                </button>
                            </form>

                            <!-- HARD DELETE -->
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $prod['product_id_pk'] ?>">
                                <button type="submit" name="delete_btn" class="edit-btn">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- CONFIRM RESTORE MODAL -->
<div class="confirm-modal <?= $confirm_restore ? 'active' : '' ?>" id="restore-modal">
    <div class="modal-content">

        <div class="modal-icon">
            <i class="fa-solid fa-recycle"></i>
        </div>

        <p>Restore <b><?= htmlspecialchars($restore_product_name ?? '') ?></b>?</p>

        <div class="modal-actions">

            <a href="?cancel_restore=1" class="cancel-btn" id="cancel-restore">Cancel</a>

            <form action="../../validation/products/recover.php" method="POST">
                <input type="hidden" name="product_id" value="<?= $restore_product_id ?>">
                <button type="submit" id="confirm-restore">Yes, Restore</button>
            </form>

        </div>
    </div>
</div>

<!-- CONFIRM DELETE MODAL -->
<div class="confirm-modal <?= $confirm_delete ? 'active' : '' ?>" id="confirm-modal">
    <div class="modal-content">

        <div class="modal-icon">
            <i class="fa-solid fa-trash"></i>
        </div>

        <p>Delete <b><?= htmlspecialchars($delete_product_name ?? '') ?></b>?</p>

        <div class="modal-actions">

            <a href="?cancel_delete=1" class="cancel-btn" id="cancel-delete">Cancel</a>

            <form action="../validation/products/hard_delete_product.php" method="POST">
                <input type="hidden" name="product_id" value="<?= $delete_product_id ?>">
                <button type="submit" id="confirm-delete">Yes, Delete</button>
            </form>

        </div>
    </div>
</div>