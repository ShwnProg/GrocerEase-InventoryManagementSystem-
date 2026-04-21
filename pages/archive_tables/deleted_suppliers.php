        <div class="menu-table">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Supplier Name</th>
                            <th>Contact Person</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Company Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($categories as $cat): ?>
                            <?php if ($prod['is_deleted'] == 0)
                                continue; ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($cat['category_name']) ?></td>
                                <td><?= htmlspecialchars($prod['category_description']) ?></td>
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