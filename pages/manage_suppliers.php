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

$product          = new Product();
$product_supplier = new ProductSuppliers();
$supplier         = new Supplier();

$product_suppliers = $product_supplier->GetProductSupplier($product_id);
$suppliers         = $supplier->GetAllSuppliers();
$supplier_count    = $product_supplier->GetProductSupplierCount($product_id);
$product_name      = $product->GetProductNameById($product_id);

$_SESSION['page_title'] = "MANAGE SUPPLIERS FOR " . strtoupper($product_name);


$error   = $_SESSION["error"]   ?? null;
$success = $_SESSION["success"] ?? null;
$old     = $_SESSION["old"]     ?? null;

// ADD MODAL 
$has_add_error    = isset($error['supplier']) || isset($error['cost_price']);
$has_add_success  = isset($success['add_supplier']);
$open_modal       = isset($_POST["add_supplier"]) || $has_add_error || $has_add_success;

// --- EDIT FLOW ---
$confirm_edit      = false;
$edit_supplier_id  = '';
$edit_supplier_name = '';
$edit_cost_price   = '';
$edit_product_id   = '';

if (isset($_POST['edit_btn'])) {
    $_SESSION['edit_product_id']    = $product_id;
    $_SESSION['edit_supplier_id']   = $_POST['supplier_id'];
    $_SESSION['edit_supplier_name'] = $_POST['supplier_name'];
    $_SESSION['edit_cost_price']    = $_POST['cost_price'];
    header("Location: manage_suppliers.php?product_id=" . $product_id);
    exit;
}

if (isset($_SESSION['edit_supplier_id'])) {
    $edit_product_id    = $_SESSION['edit_product_id'];
    $edit_supplier_id   = $_SESSION['edit_supplier_id'];
    $edit_supplier_name = $_SESSION['edit_supplier_name'];
    $edit_cost_price    = $_SESSION['edit_cost_price'];

    $confirm_edit = true;
}

if (isset($_GET['cancel_edit'])) {
    unset(
        $_SESSION['edit_product_id'],
        $_SESSION['edit_supplier_id'],
        $_SESSION['edit_supplier_name'],
        $_SESSION['edit_cost_price']
    );
    header("Location: manage_suppliers.php?product_id=" . $product_id);
    exit;
}


// Clear session
unset(
    $_SESSION["error"],
    $_SESSION["success"],
    $_SESSION["old"],
    $_SESSION['edit_product_id'],
    $_SESSION['edit_supplier_id'],
    $_SESSION['edit_supplier_name'],
    $_SESSION['edit_cost_price']
);
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php" ?>

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
                            <th>Preferred</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $num = 0; ?>
                        <?php foreach ($product_suppliers as $sup): ?>
                            <tr>
                                <td><?= ++$num ?></td>
                                <td><?= htmlspecialchars($sup['supplier_name']) ?></td>
                                <td><?= htmlspecialchars($sup['contact_person']) ?></td>
                                <td><?= htmlspecialchars($sup['phone_number']) ?></td>
                                <td><?= htmlspecialchars($sup['email']) ?></td>
                                <td><?= htmlspecialchars($sup['company_name']) ?></td>
                                <td><?= htmlspecialchars($sup['cost_price']) ?></td>
                                <td>
                                    <div class="actions">
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
                                        <!-- EDIT -->
                                        <form method="POST">
                                            <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
                                            <input type="hidden" name="supplier_name" value="<?= htmlspecialchars($sup['supplier_name']) ?>">
                                            <input type="hidden" name="cost_price" value="<?= htmlspecialchars($sup['cost_price']) ?>">
                                            <button type="submit" name="edit_btn" class="edit-btn">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </form>
                                        <!-- DELETE -->
                                      <button
                                        type="button"
                                        class="edit-btn"
                                        onclick="removeSupplier(<?= $product_id ?>, <?= $sup['supplier_id_pk'] ?>, '<?= htmlspecialchars($sup['supplier_name']) ?>')">
                                        <i class="fa-solid fa-user-minus"></i>
                                    </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ADD MODAL -->
            <div class="add-modal <?= $open_modal ? 'active' : '' ?>" id="add-modal">
                <form action="../validation/product_suppliers/add_supplier.php" method="POST">
                    <div class="header">
                        <i class="fas fa-plus"></i>
                        <p>Assign Supplier</p>
                        <span id="close-modal">&times;</span>
                    </div>
                    <div class="body">
                        <?php if (!empty($success['add_supplier'])): ?>
                            <div class="success-message">
                                <?= htmlspecialchars($success['add_supplier']) ?>
                            </div>
                        <?php endif; ?>

                        <input type="hidden" name="product_id" value="<?= $product_id ?>">

                        <div class="input">
                            <label for="suppliers">Supplier Name</label>
                            <select name="suppliers" id="suppliers">
                                <option value="">Select a supplier</option>
                                <?php foreach ($suppliers as $sup): ?>
                                    <?php if($sup['is_deleted'] == 1) continue; ?>
                                    <option value="<?= $sup['supplier_id_pk'] ?>"
                                        <?= (isset($old['suppliers']) && $old['suppliers'] == $sup['supplier_id_pk']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sup['supplier_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if (!empty($error['supplier'])): ?>
                            <div class="error-message"><?= htmlspecialchars($error['supplier']) ?></div>
                        <?php endif; ?>

                        <div class="input">
                            <label for="cost_price">Cost Price</label>
                            <i class="fas fa-tag"></i>
                            <input type="text" name="cost_price" id="cost_price"
                                value="<?= htmlspecialchars($old['cost_price'] ?? '') ?>">
                        </div>

                        <?php if (!empty($error['cost_price'])): ?>
                            <div class="error-message"><?= htmlspecialchars($error['cost_price']) ?></div>
                        <?php endif; ?>

                        <button type="submit" name="add_supplier">Add Supplier</button>
                    </div>
                </form>
            </div>

            <!-- EDIT MODAL -->
            <div class="edit-modal <?= $confirm_edit ? 'active' : '' ?>" id="edit-modal"
                data-cancel-url="manage_suppliers.php?product_id=<?= $product_id ?>&cancel_edit=1">
                <div class="modal-content">
                    <div class="modal-header">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <p>Edit Cost Price</p>
                        <span class="modal-close" id="close-edit-modal">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form action="../validation/product_suppliers/edit_process.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $edit_product_id ?>">
                            <input type="hidden" name="supplier_id" value="<?= $edit_supplier_id ?>">
                            <input type="hidden" name="supplier_name" value="<?= htmlspecialchars($edit_supplier_name) ?>">
                            
                            <?php if (!empty($edit_success)): ?>
                                <div class="success-message"><?= htmlspecialchars($edit_success) ?></div>
                            <?php endif; ?>

                            <div class="input">
                                <label for="edit_cost_price"><?= htmlspecialchars($edit_supplier_name) ?></label>
                                <i class="fas fa-tag"></i>
                                <input type="text" name="cost_price" id="edit_cost_price"
                                    value="<?= htmlspecialchars($edit_cost_price) ?>">
                            </div>

                            <?php if (!empty($edit_error)): ?>
                                <div class="error-message"><?= htmlspecialchars($edit_error) ?></div>
                            <?php endif; ?>

                            <button type="submit" id="confirm-edit">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

        </section>
    </main>
</body>
<script src="../scripts/pages.js"></script>
</html>