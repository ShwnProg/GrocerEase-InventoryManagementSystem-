<?php
session_start();
require_once '../models/supplier.php';

include "../includes/auth_check.php";

$_SESSION['page_title'] = "SUPPLIERS";

$supplier = new Supplier();
$suppliers = $supplier->GetAllSuppliers();

$errors  = $_SESSION['errors']['add'] ?? [];
$old     = $_SESSION['old'] ?? [];
$success = $_SESSION['success'] ?? [];

$open_add_modal = !empty($errors);

// CONFIRM DELETE
$confirm_delete       = false;
$delete_supplier_id   = '';
$delete_supplier_name = '';

if (isset($_POST['delete_btn'])) {
    $_SESSION['delete_supplier_id'] = $_POST['supplier_id'];
    header("Location: suppliers.php");
    exit;
}

if (isset($_SESSION['delete_supplier_id'])) {
    $delete_supplier_id   = $_SESSION['delete_supplier_id'];
    $sup_data             = $supplier->GetSupplierById($delete_supplier_id);
    $delete_supplier_name = $sup_data['supplier_name'] ?? '';
    $confirm_delete       = true;
}

if (isset($_GET['cancel_delete'])) {
    unset($_SESSION['delete_supplier_id']);
    header("Location: suppliers.php");
    exit;
}

unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php"?>

<body>

    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../includes/topbar.php'; ?>
        <div class="page-content">

            <!-- SUCCESS MESSAGES -->
            <?php if (!empty($success['add'])): ?>
                <div class="success-message"><?= htmlspecialchars($success['add']) ?></div>
            <?php endif; ?>
            <?php if (!empty($success['delete'])): ?>
                <div class="success-message"><?= htmlspecialchars($success['delete']) ?></div>
            <?php endif; ?>

            <div class="toolbar">
                <div class="search-area">
                    <form action="">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search Suppliers...">
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
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($suppliers as $sup): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($sup['supplier_name']) ?></td>
                                <td><?= htmlspecialchars($sup['contact_person']) ?></td>
                                <td><?= htmlspecialchars($sup['phone_number']) ?></td>
                                <td><?= htmlspecialchars($sup['address']) ?></td>
                                <td><?= htmlspecialchars($sup['email']) ?></td>
                                <td><?= htmlspecialchars($sup['company_name']) ?></td>
                                <td>
                                     <span class="badge <?= $sup['status'] == 1 ? 'active' : 'inactive' ?>">
                                        <?= $sup['status'] == 1 ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <!-- EDIT BUTTON -->
                                        <form action="edit_supplier.php" method="GET">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
                                            <button type="submit" class="edit-btn">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </form>

                                        <!-- DELETE BUTTON -->
                                        <form method="POST">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
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
                        <?php if (!empty($errors['contact_person'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['contact_person']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label>Phone Number</label>
                            <i class="fas fa-phone"></i>
                            <input type="text" name="phone_number" placeholder="Phone Number"
                                value="<?= htmlspecialchars($old['phone_number'] ?? '') ?>">
                        </div>
                        <?php if (!empty($errors['phone_number'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['phone_number']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label>Email</label>
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="Email"
                                value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                        </div>
                        <?php if (!empty($errors['email'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label>Address</label>
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" name="address" placeholder="Address"
                                value="<?= htmlspecialchars($old['address'] ?? '') ?>">
                        </div>
                        <?php if (!empty($errors['address'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['address']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label>Company Name</label>
                            <i class="fas fa-building"></i>
                            <input type="text" name="company_name" placeholder="Company Name"
                                value="<?= htmlspecialchars($old['company_name'] ?? '') ?>">
                        </div>
                        <?php if (!empty($errors['company_name'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['company_name']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label>Status</label>
                            <select name="status">
                                <option value="">Select a status</option>
                                <option value="1" <?= ($old['status'] ?? '') === '1' ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= ($old['status'] ?? '') === '0 ' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <?php if (!empty($errors['status'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['status']) ?></div>
                        <?php endif; ?>

                        <button type="submit">Add Supplier</button>
                    </div>
                </form>
            </div>

            <!-- CONFIRM DELETE MODAL -->
            <div class="confirm-modal <?= $confirm_delete ? 'active' : '' ?>" id="confirm-modal">
                <div class="modal-content">
                    <div class="modal-icon">
                        <i class="fa-solid fa-trash"></i>
                    </div>
                    <p>Delete <b><?= htmlspecialchars($delete_supplier_name ?? '') ?></b>?</p>
                    <div class="modal-actions">
                        <button id="cancel-delete" class="cancel-btn">Cancel</button>
                        <form action="../validation/suppliers/delete_supplier.php" method="POST">
                            <input type="hidden" name="supplier_id" value="<?= $delete_supplier_id ?>">
                            <button type="submit" id="confirm-delete">Yes, Delete</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
<script src="../scripts/pages.js"></script>
</html>