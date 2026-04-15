<?php
session_start();
require_once "../models/product.php";
require_once "../models/supplier.php";
require_once "../models/product_suppliers.php";


include "../includes/auth_check.php";


if (isset($_POST["product_id"])) {
    $product_id = $_POST["product_id"];
} else {
    $product_id = $_GET["product_id"] ?? null;
}
$product = new Product();
$product_supplier = new ProductSuppliers();
$supplier = new Supplier();

$product_suppliers = $product_supplier->GetProductSupplier($product_id);
$suppliers = $supplier->GetAllSuppliers();
$supplier_count = $product_supplier->GetProductSupplierCount($product_id);
$product_name = $product->GetProductNameById($product_id);

$_SESSION['page_title'] = "MANAGE SUPPLIERS FOR " . strtoupper($product_name);


$has_error = isset($_SESSION["error"]["add_supplier"]) || isset($_SESSION['error']);
$has_success = isset($_SESSION["success"]["add_supplier"]);
$open_modal = isset($_POST["add_supplier"]) || $has_error || $has_success;

$error = $_SESSION["error"] ?? null;
$success = $_SESSION["success"] ?? null;
$old = $_SESSION["old"] ?? null;

$confirm_delete = false;
$delete_supplier_id = '';
$delete_supplier_name = '';

if (isset($_POST['delete_btn'])) {
    $_SESSION['delete_product_id'] = $product_id;
    $_SESSION['delete_supplier_id'] = $_POST['supplier_id'];
    $_SESSION['delete_supplier_name'] = $_POST['supplier_name'];
    header("Location: manage_suppliers.php?product_id=" . $product_id);
    exit;
}

if (isset($_SESSION['delete_supplier_id'])) {
    $delete_product_id = $_SESSION['delete_product_id'];
    $delete_supplier_id = $_SESSION['delete_supplier_id'];
    $delete_supplier_name = $_SESSION['delete_supplier_name'];
    $confirm_delete = true;
}

if (isset($_GET['cancel_delete'])) {
    unset($_SESSION['delete_product_id'], $_SESSION['delete_supplier_id'], $_SESSION['delete_supplier_name']);
    header("Location: manage_suppliers.php?product_id=" . $product_id);
    exit;
}

$delete_success = $_SESSION['success']['remove_supplier'] ?? '';
$delete_error = $_SESSION['errors']['remove_supplier'] ?? '';

unset($_SESSION["error"], $_SESSION["success"], $_SESSION["old"]);
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php"?>


<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../includes/topbar.php'; ?>

        <section class="page-content">
            <?php include '../includes/delete_message.php' ?>

            <div class="toolbar">
                <a href="products.php" class="back-btn"><i class="fas fa-arrow-left"></i> back</a>
                <div class="add">
                    <button type="button" id="addbtn">Assign Supplier</button>
                </div>
            </div>
            <div class="menu-table">
                <table>
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>Supplier Name</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Company Name</th>
                            <th>Cost Price</th>
                            <th>Preffered</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($product_suppliers as $index => $sup): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $sup['supplier_name'] ?></td>
                                <td><?= $sup['contact_person'] ?></td>
                                <td><?= $sup['phone_number'] ?></td>
                                <td><?= $sup['email'] ?></td>
                                <td><?= $sup['company_name'] ?></td>
                                <td><?= $sup['cost_price'] ?></td>
                                <td>
                                    <div class="actions">
                                        <!-- PREFERRED BUTTON -->
                                        <form action="../validation/product_suppliers/preferred_supplier.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
                                            <input type="hidden" name="is_preferred" value="<?= $sup['preferred'] ?>">

                                            <button type="submit" class="preferred-btn">
                                                <?php if ($sup['preferred'] == 1): ?>
                                                    <i class="fa-solid fa-check-circle active"></i>
                                                <?php else: ?>
                                                    <i class="fa-regular fa-circle"></i>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <div class="actions">
                                        <!-- EDIT ACTION -->
                                        <form action="edit_product_supplier.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
                                            <button type="submit" class="edit-btn">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </form>
                                        <!-- DELETE BUTTON -->
                                        <form method="POST">
                                            <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
                                            <input type="hidden" name="supplier_name"
                                                value="<?= htmlspecialchars($sup['supplier_name']) ?>">

                                            <button type="submit" name="delete_btn" class="edit-btn">
                                                <i class="fa-solid fa-user-minus"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ADD MODAL -->
            <div class="add-modal <?php echo $open_modal ? 'active' : ''; ?>" id="add-modal">
                <form action="../validation/product_suppliers/add_supplier.php" method="POST">
                    <div class="header">
                        <i class="fas fa-plus"></i>
                        <p>Assign Supplier</p>
                        <span id="close-modal">&times;</span>
                    </div>
                    <div class="body">
                        <?php if (!empty($success['add_supplier'])): ?>
                            <div class="success-message">
                                <?= $success['add_supplier'] ?>
                            </div>
                        <?php endif; ?>

                        <input type="hidden" name="product_id" value="<?= $product_id ?>">

                        <div class="input">
                            <label for="">Supplier Name</label>
                            <select name="suppliers" id="">
                                <option value="">Select a supplier</option>
                                <?php foreach ($suppliers as $sup): ?>
                                    <option value="<?= $sup['supplier_id_pk'] ?>" <?= (isset($old['suppliers']) && $old['suppliers'] == $sup['supplier_id_pk']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sup['supplier_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if (!empty($error['supplier'])): ?>
                            <div class="error-message">
                                <?= htmlspecialchars($error['supplier']) ?>
                            </div>
                        <?php endif; ?>

                        <div class="input">
                            <label for="cost_price">Cost Price</label>
                            <i class="fas fa-tag"></i>
                            <input type="text" name="cost_price"
                                value="<?= htmlspecialchars($old['cost_price'] ?? '') ?>">
                        </div>

                        <?php if (!empty($error['cost_price'])): ?>
                            <div class="error-message">
                                <?= htmlspecialchars($error['cost_price']) ?>
                            </div>
                        <?php endif; ?>

                        <button type="submit" name="add_supplier">Add Supplier</button>
                    </div>
                </form>
            </div>

            <!-- REMOVE MODAL -->
            <div class="confirm-modal warning-orange <?= $confirm_delete ? 'active' : '' ?>" id="confirm-modal">
                <div class="modal-content">
                    <div class="modal-icon">
                        <i class="fa-solid fa-user-minus"></i>
                    </div>
                    <p>Remove <b><?= htmlspecialchars($delete_supplier_name) ?></b> from this product?</p>

                    <div class="modal-actions">
                        <a href="manage_suppliers.php?product_id=<?= $product_id ?>&cancel_delete=1"
                            class="cancel-btn">Cancel</a>

                        <form action="../validation/product_suppliers/remove_suppliers.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $delete_product_id ?>">
                            <input type="hidden" name="supplier_id" value="<?= $delete_supplier_id ?>">
                            <button type="submit" id="confirm-delete">Yes, Remove</button>
                        </form>
                    </div>
                </div>
            </div>

        </section>
    </main>
</body>
<script src="../scripts/pages.js"></script>

</html>