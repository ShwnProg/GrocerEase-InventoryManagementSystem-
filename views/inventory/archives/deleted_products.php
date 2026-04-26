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
        <?php $no = 1; ?>
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
                <td>₱<?= number_format($prod['cost_price'], 2) ?></td>
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

                        <button type="button" class="edit-btn"
                            onclick="restoreProduct('<?= $prod['product_id_pk'] ?>', '<?= htmlspecialchars($prod['product_name']) ?>')">
                            <i class="fa-solid fa-recycle"></i>
                        </button>

                        <button type="button" class="edit-btn"
                            onclick="hardDeleteProduct('<?= $prod['product_id_pk'] ?>', '<?= htmlspecialchars($prod['product_name']) ?>')">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>