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
                    <td><?= htmlspecialchars($cat['category_description'] == '' ? 'N/A' :$cat['category_description']) ?></td>
                    <td>
                        <div class="actions">
                            <button type="button" class="edit-btn"
                                onclick="restoreCategory('<?= $cat['category_id_pk'] ?>', '<?= htmlspecialchars($cat['category_name']) ?>')">
                                <i class="fa-solid fa-recycle"></i>
                            </button>
                            <!-- DELETE ACTION -->
                            <button type="button" class="edit-btn"
                                onclick="hardDeleteCategory('<?= $cat['category_id_pk'] ?>', '<?= htmlspecialchars($cat['category_name']) ?>')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>