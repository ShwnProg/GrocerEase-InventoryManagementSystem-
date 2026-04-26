<?php
session_start();
require_once __DIR__ . '../../../autoload.php';

include "../../includes/auth_check.php";

$product_id = $_POST["product_id"] ?? $_GET["product_id"] ?? null;

$product          = new Product($db);
$product_supplier = new ProductSuppliers($db);
$supplier         = new Supplier($db);

$product_suppliers = $product_supplier->GetProductSupplier($product_id);
$suppliers         = $supplier->GetAllSuppliers();
$product_name      = $product->GetProductNameById($product_id);

$_SESSION['page_title'] = "MANAGE SUPPLIERS FOR " . strtoupper($product_name);

$error   = $_SESSION["error"]   ?? null;
$success = $_SESSION["success"] ?? null;
$old     = $_SESSION["old"]     ?? null;

$has_add_error   = isset($error['supplier']) || isset($error['cost_price']);
$has_add_success = isset($success['add_supplier']);
$open_modal      = $has_add_error || $has_add_success;

unset($_SESSION["error"], $_SESSION["success"], $_SESSION["old"]);
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../../includes/head.php" ?>

<body>
    <?php include '../../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../../includes/topbar.php'; ?>

        <section class="page-content">
            <div class="toolbar">
                <a href="products.php" class="back-btn"><i class="fas fa-arrow-left"></i> back</a>
                <div class="add">
                    <button type="button" id="addbtn">Assign Supplier</button>
                </div>
            </div>

            <div class="menu-table">
                <table>
                    <colgroup>
                        <col style="width: 4%;">
                        <col style="width: 13%;">
                        <col style="width: 12%;">
                        <col style="width: 10%;">
                        <col style="width: 14%;">
                        <col style="width: 13%;">
                        <col style="width: 10%;">
                        <col style="width: 6%;">
                        <col style="width: 18%;">
                    </colgroup>
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
                                <td style="word-break: break-word; white-space: normal;"><?= htmlspecialchars($sup['supplier_name']) ?></td>
                                <td style="word-break: break-word; white-space: normal;"><?= htmlspecialchars($sup['contact_person']) ?></td>
                                <td><?= htmlspecialchars($sup['phone_number']) ?></td>
                                <td style="word-break: break-all; white-space: normal;"><?= htmlspecialchars($sup['email']) ?></td>
                                <td style="word-break: break-word; white-space: normal;"><?= htmlspecialchars($sup['company_name']) ?></td>
                                <td><?= htmlspecialchars($sup['cost_price']) ?></td>
                                <td>
                                    <div class="actions">
                                        <form action="../../controllers/product_suppliers/preferred_supplier.php" method="POST">
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
                           
                                        <button
                                            type="button"
                                            class="edit-btn open-edit-modal"
                                            data-product-id="<?= $product_id ?>"
                                            data-supplier-id="<?= $sup['supplier_id_pk'] ?>"
                                            data-supplier-name="<?= htmlspecialchars($sup['supplier_name'], ENT_QUOTES) ?>"
                                            data-cost-price="<?= htmlspecialchars($sup['cost_price'], ENT_QUOTES) ?>">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <!-- REMOVE -->
                                        <button
                                            type="button"
                                            class="edit-btn"
                                            onclick="removeSupplier(<?= $product_id ?>, <?= $sup['supplier_id_pk'] ?>, '<?= htmlspecialchars($sup['supplier_name'], ENT_QUOTES) ?>')">
                                            <i class="fa-solid fa-user-minus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ADD MODAL — unchanged: posts to add_supplier.php, session drives errors/success/old -->
            <div class="add-modal <?= $open_modal ? 'active' : '' ?>" id="add-modal">
                <form action="../../controllers/product_suppliers/add_supplier.php" method="POST">
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
                                    <?php if ($sup['is_deleted'] == 1) continue; ?>
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

            <div class="edit-modal" id="edit-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <p>Edit Cost Price</p>
                        <span class="modal-close" id="close-edit-modal">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div id="edit-feedback"></div>

                        <input type="hidden" id="edit-product-id">
                        <input type="hidden" id="edit-supplier-id">

                        <div class="input">
                            <label id="edit-supplier-label" for="edit-cost-price"></label>
                            <i class="fas fa-tag"></i>
                            <input type="text" id="edit-cost-price">
                        </div>

                        <button type="button" id="confirm-edit">Save Changes</button>
                    </div>
                </div>
            </div>

        </section>
    </main>
</body>
<script src="../../assets/scripts/pages.js"></script>
</html>