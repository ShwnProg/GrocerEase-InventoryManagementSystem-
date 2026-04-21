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
                                <td><?= htmlspecialchars($prod['product_description'] == '' ? 'N/A' : ($prod['product_description'])) ?></td>
                                <td>
                                    <span class="badge <?= $prod['status'] == 1 ? 'active' : 'inactive' ?>">
                                        <?= $prod['status'] == 1 ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <form action="recover.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?= $prod['product_id_pk'] ?>">
                                            <button type="submit" class="edit-btn">
                                                <i class="fa-solid fa-recycle"></i>
                                            </button>
                                        </form>
                                        <!-- DELETE ACTION -->
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