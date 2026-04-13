<?php
session_start();
require_once "../models/product.php";
require_once "../models/supplier.php";

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../forms/index.php");
    exit;
}
$_SESSION['page_title'] = "MANAGE SUPPLIERS";

if (isset($_POST["product_id"])) {
    $product_id = $_POST["product_id"];
}

$product = new Product();
$supplier = new Supplier();

$product_suppliers = $product->GetSupplier($product_id);
$suppliers = $supplier->GetAllSuppliers();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Suppliers</title>
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="icon" type="image/png" href="../images/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../includes/topbar.php'; ?>

        <div class="page-content">
            <div class="toolbar">
                <a href="products.php" class="back-btn"><i class="fas fa-arrow-left"></i> back</a>
                <div class="add">
                    <button type="button" id="addbtn">Add Supplier</button>
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
                            <th>Actions</th>
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
                                        <form action="remove_supplier.php" method="POST">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['product_id_pk'] ?>">
                                            <button type="submit" class="delete-btn">
                                                <i class="fas fa-trash"></i>
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

                <form action="../validation/categories/add_category.php" method="POST">
                    <div class="header">

                        <i class="fas fa-plus"></i>
                        <p>Add Supplier</p>
                        <span id="close-modal">&times;</span>

                    </div>
                    <div class="body">

                        <div class="input">
                            <label for="">Supplier Name</label>
                            <select name="suppliers" id="">
                                <?php foreach ($suppliers as $sup): ?>
                                    <option value="<?= $sup['supplier_id_pk'] ?>"><?= $sup['supplier_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="input">
                            <label for="">Cost Price</label>
                            <i class="fas fa-tag"></i>
                            <input type="text" name="cost_price">
                        </div>

                        <button type="submit">Add Supplier</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</body>
<script src="../scripts/pages.js"></script>

</html>