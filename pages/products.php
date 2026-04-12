<?php
session_start();
require_once "../models/product.php";
require_once "../models/categories.php";

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../forms/index.php");
    exit;
}
$_SESSION['page_title'] = "PRODUCTS";


$product = new Product();
$products = $product->GetAllProducts();

$category = new Category();
$categories = $category->GetAllCategories();


$open_modal = isset($_SESSION['errors']) || isset($_SESSION['success']);
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];

unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);
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
                            <?php if ($prod['is_deleted'] == 1)
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
                                        <form action="edit_product.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?= $prod['product_id_pk'] ?>">
                                            <button type="submit" class="edit-btn">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </form>

                                        <form action="delete_product.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?= $prod['product_id_pk'] ?>">
                                            <button type="submit" class="edit-btn">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>

                                        <form action="manage_suppliers.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?= $prod['product_id_pk'] ?>">
                                            <button type="submit" class="manage-btn">
                                                <i class="fa-solid fa-truck"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="add-modal <?= $open_modal ? 'active' : '' ?>" id="add-modal">

                <form action="../validation/products/add_product_process.php" method="POST">
                    <div class="header">

                        <i class="fas fa-plus"></i>
                        <p>Add Product</p>
                        <span id="close-modal">&times;</span>

                    </div>
                    <div class="body">

                        <!-- SUCCESS MESSAGE -->
                        <?php if (!empty($_SESSION['success'])): ?>
                            <div class="success-message">
                                <?= $_SESSION['success'] ?>
                            </div>
                        <?php endif; ?>

                        <!-- PRODUCT NAME -->
                        <div class="input">
                            <label for="">Product Name</label>
                            <i class="fas fa-box"></i>
                            <input type="text" name="product_name" placeholder="Product Name">
                        </div>

                        <!-- ERROR MESSAGE FOR PRODUCT NAME -->
                        <?php if (!empty($errors['product_name'])): ?>
                            <div class="error-message"><?= $errors['product_name'] ?></div>
                        <?php endif; ?>

                        <!-- CATEGORY -->
                        <div class="input">
                            <label for="">Category</label>
                            <select name="category" id="">
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['category_id_pk'] ?>">
                                        <?= htmlspecialchars($cat['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- ERROR MESSAGE FOR CATEGORY -->
                        <?php if (!empty($errors['category'])): ?>
                            <div class="error-message">
                                <?= $errors['category'] ?>
                            </div>
                        <?php endif; ?>

                        <!-- SELLING PRICE -->
                        <div class="input">
                            <label for="">Selling Price</label>
                            <i class="fas fa-tag"></i>
                            <input type="text" name="selling_price" placeholder="Selling Price">
                        </div>

                        <!-- ERROR MESSAGE FOR SELLING PRICE -->
                        <?php if (!empty($errors['selling_price'])): ?>
                            <div class="error-message">
                                <?= $errors['selling_price'] ?>
                            </div>
                        <?php endif; ?>

                        <!-- DESCRIPTION -->
                        <div class="input">
                            <label for="">Description (Optional)</label>
                            <i class="fas fa-align-left"></i>
                            <textarea name="product_description" placeholder="Product Description"></textarea>
                        </div>

                        <!-- STATUS -->
                        <div class="input">
                            <label for="">Status</label>
                            <select name="status" id="">
                                <option value="">Select a status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <!-- ERROR MESSAGE FOR STATUS -->
                        <?php if (!empty($errors['status'])): ?>
                            <div class="error-message">
                                <?= $errors['status'] ?>
                            </div>
                        <?php endif; ?>

                        <button type="submit">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script src="../scripts/pages.js"></script>

</html>