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
            <?php foreach ($supplier as $sup): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($sup['supplier_name']) ?></td>
                    <td><?= htmlspecialchars($sup['contact_person']) ?></td>
                    <td><?= htmlspecialchars($sup['phone_number']) ?></td>
                    <td><?= htmlspecialchars($sup['address']) ?></td>
                    <td><?= htmlspecialchars($sup['company_name']) ?></td>
                    <td><?= htmlspecialchars($sup['email']) ?></td>
                    <td>
                        <div class="actions">
                            <button type="button" class="edit-btn"
                                onclick="restoreSupplier('<?= $sup['supplier_id_pk'] ?>', '<?= htmlspecialchars($sup['supplier_name']) ?>')">
                                <i class="fa-solid fa-recycle"></i>
                            </button>
                            <!-- DELETE ACTION -->
                            <button type="button" class="edit-btn"
                                onclick="hardDeleteSupplier('<?= $sup['supplier_id_pk'] ?>', '<?= htmlspecialchars($sup['supplier_name']) ?>')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>