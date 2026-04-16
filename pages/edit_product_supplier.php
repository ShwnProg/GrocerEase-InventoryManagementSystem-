<?php
session_start();
// require_once "../models/product.php";
require_once "../models/supplier.php";
require_once "../models/product_suppliers.php";
include "../includes/auth_check.php";

$_SESSION['page_title'] = "EDIT PRODUCT SUPPLIERS ";

if (isset($_POST["product_id"]) && isset($_POST["supplier_id"])) {
    $product_id = $_POST["product_id"];
} else {
    $product_id = $_GET["product_id"] ?? null;
}
$supplier = new Supplier();
$suppliers = $supplier->GetAllSuppliers();
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
                <a href="manage_suppliers.php?product_id=<?= $product_id ?>" class="back-btn"><i
                        class="fas fa-arrow-left"></i> back</a>
            </div>
            <div class="edit-content">
                <form action="../validation/product_suppliers/edit_process.php" method="POST">

                    <p> <i class="fa-solid fa-pen-to-square"></i> EDIT PRODUCT SUPPLIER</p>

                    <input type="hidden" name="product_id" value="<?= $product_id ?>">

                    <!-- SUPPLIER NAME -->
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

                    <!-- COST PRICE -->
                    <div class="input">
                        <label for="cost_price">Cost Price</label>
                        <i class="fas fa-tag"></i>
                        <input type="text" name="cost_price" value="<?= htmlspecialchars($old['cost_price'] ?? '') ?>">
                    </div>

                    <button type="submit" name="edit_btn">SAVE CHANGES</button>
                </form>
            </div>
        </section>
    </main>
</body>

</html>