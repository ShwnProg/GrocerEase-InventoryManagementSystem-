        <div class="menu-table">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($category as $cat): ?>
        
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($cat['category_name']) ?></td>
                                <td><?= htmlspecialchars($cat['category_description']) ?></td>
                                <td>
                                    <div class="actions">
                                        <form  method="POST">
                                            <input type="hidden" name="category_id" value="<?= $cat['category_id_pk'] ?>">
                                            <button type="submit" name="restore_btn" class="edit-btn">
                                                <i class="fa-solid fa-recycle"></i>
                                            </button>
                                        </form>
                                        <!-- DELETE ACTION -->
                                        <form method="POST">
                                            <input type="hidden" name="category_id" value="<?= $cat['category_id_pk'] ?>">
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

        <p>Restore <b><?= htmlspecialchars($restore_category_name ?? '') ?></b>?</p>

        <div class="modal-actions">

            <a href="?cancel_restore=1" class="cancel-btn" id="cancel-restore">Cancel</a>

            <form action="../validation/categories/recover.php" method="POST">
                <input type="hidden" name="category_id" value="<?= $restore_category_id ?>">
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

        <p>Delete <b><?= htmlspecialchars($delete_category_name ?? '') ?></b>?</p>

        <div class="modal-actions">

            <a href="?cancel_delete=1" class="cancel-btn" id="cancel-delete">Cancel</a>

            <form action="../validation/categories/hard_delete_category.php" method="POST">
                <input type="hidden" name="category_id" value="<?= $delete_category_id ?>">
                <button type="submit" id="confirm-delete">Yes, Delete</button>
            </form>

        </div>
    </div>
</div>