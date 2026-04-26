<?php
session_start();
require_once __DIR__ . '../../autoload.php';


include "../includes/auth_check.php";

$_SESSION['page_title'] = "SUPPLIERS";

$search = $_GET['search'] ?? '';

$supplier = new Supplier($db);

if (!empty($search)) {
    $suppliers = $supplier->SearchSupplier($search);
} else {
    $suppliers = $supplier->GetAllSuppliers();
}

$open_modal = isset($_SESSION['add_supplier_error']) || isset($_SESSION['success_msg']);
$error = $_SESSION['add_supplier_error'] ?? [];
$old_inputs = $_SESSION['old_inputs'] ?? [];
$success_msg = $_SESSION['success_msg'] ?? '';

$confirm_delete = false;
$delete_supplier_id = '';
$errors = $_SESSION['errors']['add'] ?? [];
$old = $_SESSION['old'] ?? [];
$success = $_SESSION['success'] ?? [];

$open_add_modal = !empty($errors);


// echo "hello $user_info[username]";
unset($_SESSION['add_supplier_error'], $_SESSION['old_inputs'], $_SESSION['success_msg']);
unset($_SESSION['success'], $_SESSION['errors']);

unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);

?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php" ?>

<body>

    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../includes/topbar.php'; ?>
        <section class="page-content">
            <div class="toolbar">
                <div class="search-area">
                    <form method='get'>
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search Suppliers"
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
                <div class="add">
                    <button id="addbtn">Add Supplier</button>
                </div>
            </div>

            <!-- TABLE -->
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
                        <?php $no = 1; ?>
                        <?php if (empty($suppliers)): ?>
                            <tr>
                                <td colspan="8" style="text-align:center; color:#6b7280;">
                                    No Supplier found
                                </td>
                            <?php else: ?>
                                <?php foreach ($suppliers as $sup): ?>
                                    <?php if ($sup['is_deleted'] == 1)
                                        continue; ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($sup['supplier_name']) ?></td>
                                    <td><?= htmlspecialchars($sup['contact_person']) ?></td>
                                    <td><?= htmlspecialchars($sup['phone_number']) ?></td>
                                    <td><?= htmlspecialchars($sup['address']) ?></td>
                                    <td><?= htmlspecialchars($sup['company_name']) ?></td>
                                    <td><?= htmlspecialchars($sup['email']) ?></td>

                                    <!-- <td>
                                     <span class="badge <?= $sup['status'] == 1 ? 'active' : 'inactive' ?>">
                                        <?= $sup['status'] == 1 ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td> -->
                                    <td>
                                        <!-- EDIT SUPPLIER -->
                                        <div class="actions">
                                            <!-- EDIT BUTTON -->
                                            <form action="edit_supplier.php" method="GET">
                                                <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
                                                <button type="submit" class="edit-btn">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </form>

                                            <!-- DELETE BUTTON -->
                                            <button type="button" class="edit-btn"
                                                onclick="deleteSupplier('<?= $sup['supplier_id_pk'] ?>', '<?= htmlspecialchars($sup['supplier_name']) ?>')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>


            <!-- ADD SUPPLIER MODAL -->
            <div class="add-modal <?= $open_add_modal ? 'active' : '' ?>" id="add-modal">
                <form action="../validation/suppliers/add_supplier.php" method="POST">
                    <div class="header">
                        <i class="fas fa-plus"></i>
                        <p>Add Supplier</p>
                        <span id="close-modal">&times;</span>
                    </div>
                    <div class="body">

                        <?php if (!empty($errors['form'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['form']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label>Supplier Name</label>
                            <i class="fas fa-user"></i>
                            <input type="text" name="supplier_name" placeholder="Supplier Name"
                                value="<?= htmlspecialchars($old['supplier_name'] ?? '') ?>">
                        </div>
                        <?php if (!empty($errors['supplier_name'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['supplier_name']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label>Contact Person</label>
                            <i class="fas fa-user-tie"></i>
                            <input type="text" name="contact_person" placeholder="Contact Person"
                                value="<?= htmlspecialchars($old['contact_person'] ?? '') ?>">
                        </div>
                        <!-- <?php if (!empty($errors['contact_person'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['contact_person']) ?></div>
                        <?php endif; ?> -->

                        <div class="input">
                            <label>Phone Number</label>
                            <i class="fas fa-phone"></i>
                            <input type="text" name="phone_number" placeholder="Phone Number"
                                value="<?= htmlspecialchars($old['phone_number'] ?? '') ?>">
                        </div>
                        <!-- <?php if (!empty($errors['phone_number'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['phone_number']) ?></div>
                        <?php endif; ?> -->

                        <div class="input">
                            <label>Email</label>
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="Email"
                                value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                        </div>
                        <!-- <?php if (!empty($errors['email'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?> -->

                        <div class="input">
                            <label>Address</label>
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" name="address" placeholder="Address"
                                value="<?= htmlspecialchars($old['address'] ?? '') ?>">
                        </div>
                        <!-- <?php if (!empty($errors['address'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['address']) ?></div>
                        <?php endif; ?> -->

                        <div class="input">
                            <label>Company Name</label>
                            <i class="fas fa-building"></i>
                            <input type="text" name="company_name" placeholder="Company Name"
                                value="<?= htmlspecialchars($old['company_name'] ?? '') ?>">
                        </div>
                        <!-- <?php if (!empty($errors['company_name'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['company_name']) ?></div>
                        <?php endif; ?> -->

                        <!-- <div class="input">
                            <label>Status</label>
                            <select name="status">
                                <option value="">Select a status</option>
                                <option value="1" <?= ($old['status'] ?? '') === '1' ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= ($old['status'] ?? '') === '0 ' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div> -->
                        <!-- <?php if (!empty($errors['status'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['status']) ?></div>
                        <?php endif; ?> -->

                        <button type="submit">Add Supplier</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
<script src="../scripts/pages.js"></script>

</html>