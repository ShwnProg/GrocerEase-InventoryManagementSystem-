<?php
session_start();
require_once '../models/supplier.php';

include "../includes/auth_check.php";

$_SESSION['page_title'] = "SUPPLIERS";

$supplier = new Supplier();
$suppliers = $supplier->GetAllSuppliers();

$open_modal = isset($_SESSION['add_supplier_error']) || isset($_SESSION['success_msg']);
$error = $_SESSION['add_supplier_error'] ?? [];
$old_inputs = $_SESSION['old_inputs'] ?? [];
$success_msg = $_SESSION['success_msg'] ?? '';

$confirm_delete = false;
$delete_supplier_id = '';
$delete_supplier_name = '';

if (isset($_POST['delete_btn'])) {
    $_SESSION['delete_supplier_id'] = $_POST['supplier_id'];
    header("Location: suppliers.php");
    exit;
}

if (isset($_SESSION['delete_supplier_id'])) {
    $delete_supplier_id = $_SESSION['delete_supplier_id'];
    $confirm_delete = true;

     $supplierData = $supplier->GetSupplierById($delete_supplier_id);
    $delete_supplier_name = $supplierData['supplier_name'] ?? '';
}

if (isset($_GET['cancel_delete'])) {
    unset($_SESSION['delete_supplier_id']);
    header("Location: suppliers.php");
    exit;
}

$delete_success = $_SESSION['success']['delete'] ?? '';
$delete_error = $_SESSION['errors']['delete'] ?? '';

// echo "hello $user_info[username]";
unset($_SESSION['add_supplier_error'], $_SESSION['old_inputs'], $_SESSION['success_msg']);
unset($_SESSION['success'], $_SESSION['errors']);

?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php" ?>


<body>

    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../includes/topbar.php'; ?>
        <div class="page-content">
            <div class="toolbar">
                <div class="search-area">
                    <form action="">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search a supplier">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
                <div class="add">
                    <button id="addbtn">Add Supplier</button>
                </div>
            </div>

            <!-- table -->
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
                        <?php foreach ($suppliers as $sup): ?>
                            <?php if($sup['is_deleted'] == 1)
                                continue;  ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($sup['supplier_name']) ?></td>
                                <td><?= htmlspecialchars($sup['contact_person']) ?></td>
                                <td><?= htmlspecialchars($sup['phone_number']) ?></td>
                                <td><?= htmlspecialchars($sup['email']) ?></td>
                                <td><?= htmlspecialchars($sup['address']) ?></td>
                                <td><?= htmlspecialchars($sup['company_name']) ?></td>
                                <td>
                                    <!-- EDIT SUPPLIER -->
                                    <div class="actions">
                                        <form action="edit_supplier.php" method="POST">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
                                            <button type="submit" class="edit-btn">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </form>
                                        <!-- DELETE SUPPLIER -->
                                        <form method="POST">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
                                            <button type="submit" name= "delete_btn" class ="edit-btn">
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
            
            <div class="add-modal" id="add-modal">

                <form action="../validation/suppliers/add_supplier.php" method="POST">
                    <div class="header">

                        <i class="fas fa-plus"></i>
                        <p>Add Supplier</p>
                        <span id="close-modal">&times;</span>

                    </div>
                    <div class="body">

                        <div class="input">
                            <label for="">Supplier Name</label>
                            <i class="fas fa-user"></i>
                            <input type="text" name="supplier_name" placeholder="Supplier Name">
                        </div>

                        <div class="input">
                            <label for="">Contact Person</label>
                            <i class="fas fa-user-tie"></i>
                            <input type="text" name="contact_person" placeholder="Contact Person">
                        </div>

                        <div class="input">
                            <label for="">Phone Number</label>
                            <i class="fas fa-phone"></i>
                            <input type="text" name="phone_number" placeholder="Phone Number">
                        </div>

                        <div class="input">
                            <label for="">Email</label>
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="Email">
                        </div>

                        <div class="input">
                            <label for="">Address</label>
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" name="address" placeholder="Address">
                        </div>

                        <div class="input">
                            <label for="">Company Name</label>
                            <i class="fas fa-building"></i>
                            <input type="text" name="company_name" placeholder="Company Name">
                        </div>

                        <button type="submit">Add Supplier</button>
                    </div>
                </form>
            </div>

            <div class="confirm-modal <?= $confirm_delete ? 'active' : '' ?>" id="confirm-modal">
                <div class="modal-content">
                    <div class="modal-icon">
                        <i class="fa-solid fa-trash"></i>
                    </div>
                    <p>Delete <b><?= htmlspecialchars($delete_supplier_name ?? '') ?></b>?</p>

                    <div class="modal-actions">

                        <button id="cancel-delete" class="cancel-btn">Cancel</button>
                        <!-- CONFIRM DELETE -->
                        <form action="../validation/delete_supplier/delete_supplier.php" method="POST">
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