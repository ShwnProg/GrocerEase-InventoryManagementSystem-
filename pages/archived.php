<?php
session_start();
require_once '../models/product.php';
require_once '../models/categories.php';
require_once '../models/supplier.php';

include "../includes/auth_check.php";

$_SESSION['page_title'] = "ARCHIVED";

$product = new Product();
$categories = new Category();
$suppliers = new Supplier();

$products = $product->GetDeletedProducts();
$category = $categories->GetDeletedCategories();
$supplier = $suppliers->GetDeletedSuppliers();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocer Ease</title>
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/icon.png">
</head>

<body>

    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../includes/topbar.php'; ?>
        <div class="page-content">
            <div class="header-btn">
                <a href="deleted_products.php">Products</a>
                <a href="deleted_categories.php">Categories</a>
                <a href="deleted_suppliers.php">Suppliers</a>
            </div>
            
            <div class="toolbar">

                <div class="search-area">
                    <form action="">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search a product">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
                <div class="add">
                    <button id="addbtn">Add Product</button>
                </div>
            </div>

            <!-- table -->
            <div class="menu-table">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($products as $prod): ?>
                            <?php if ($prod['is_deleted'] == 0)
                                continue; ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($prod['product_name']) ?></td>
                                <td><?= htmlspecialchars($prod['category_name']) ?></td>
                                <td>₱<?= number_format($prod['cost_price'], 2) ?></td>
                                <td>₱<?= number_format($prod['selling_price'], 2) ?></td>
                                <td><?= htmlspecialchars($prod['product_description']) ?></td>
                                <td>
                                    <span class="badge <?= $prod['status'] == 1 ? 'active' : 'inactive' ?>">
                                        <?= $prod['status'] == 1 ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
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
        </div>
    </div>
</body>

</html>