<?php
session_start();
require_once __DIR__ . '../../../autoload.php';

include "../../includes/auth_check.php";

$_SESSION['page_title'] = "PRODUCTS";

$search = $_GET['search'] ?? '';
$product = new Product($db);

if (!empty($search)) {
    $products = $product->SearchProduct($search);
} else {
    $products = $product->GetAllProducts();
}

$category = new Category($db);
$categories = $category->GetAllCategories();


$open_modal = isset($_SESSION['errors']['add']) || isset($_SESSION['success']['add']);
$errors = $_SESSION['errors']['add'] ?? [];
$old = $_SESSION['old'] ?? [];
$success = $_SESSION['success']['add'] ?? '';

unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success'], $search);
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

                <div class="search-area">
                    <form method="get">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search a product"
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
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
                            <th>Product</th>
                            <th>Category</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Description</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>

                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="9" style="text-align:center; color:#6b7280;">
                                    No products found
                                </td>
                            <?php else: ?>
                                <?php foreach ($products as $prod): ?>
                                    <?php if ($prod['is_deleted'] == 1)
                                        continue; ?>
                                <tr>
                                    <td><?= $count++ ?></td>
                                    <td><?= htmlspecialchars($prod['product_name']) ?></td>
                                    <td>
                                        <?= htmlspecialchars(
                                            isset($prod['category_name'])
                                            ? $prod['category_name'] . ($prod['category_status'] == 0 ? ' (Inactive)' : '')
                                            : 'Uncategorized'
                                        ) ?>
                                    </td>
                                    <td>
                                        <?= $prod['cost_price']
                                            ? '₱' . number_format($prod['cost_price'], 2)
                                            : '<span style="color:#6b7280;">No cost price</span>' ?>
                                    </td>
                                    <td>₱<?= number_format($prod['selling_price'], 2) ?></td>
                                    <td><?= htmlspecialchars($prod['product_description'] == '' ? 'No description available' : $prod['product_description']) ?>
                                    </td>
                                    <td style="color:#6b7280;">
                                        <?= $prod['preferred_supplier_name'] ?? 'No preferred supplier' ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $prod['status'] == 1 ? 'active' : 'inactive' ?>">
                                            <?= $prod['status'] == 1 ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- EDIT ACTION -->
                                        <div class="actions">
                                            <form action="edit_product.php" method="POST">
                                                <input type="hidden" name="product_id" value="<?= $prod['product_id_pk'] ?>">
                                                <button type="submit" class="edit-btn">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </form>
                                            <!-- DELETE ACTION -->
                                            <button class="edit-btn"
                                                onclick="deleteProduct(<?= $prod['product_id_pk'] ?>, '<?= htmlspecialchars($prod['product_name']) ?>')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                            <?php if ($prod['status'] == 1): ?>
                                                <form action="manage_suppliers.php" method="POST">
                                                    <input type="hidden" name="product_id" value="<?= $prod['product_id_pk'] ?>">
                                                    <button type="submit" class="manage-btn">
                                                        <i class="fa-solid fa-truck"></i>
                                                    </button>
                                                </form>
                                            <?php elseif ($prod['status'] == 0): ?>
                                                <button class="manage-btn" disabled style="opacity:0.5; cursor:not-allowed;">
                                                    <i class="fa-solid fa-truck"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ADD MODAL -->
            <div class="add-modal <?php echo $open_modal ? 'active' : ''; ?>" id="add-modal">
                <form action="../../controllers/products/add_product_process.php" method="POST">
                    <div class="header">

                        <i class="fas fa-plus"></i>
                        <p>Add Product</p>
                        <span id="close-modal">&times;</span>

                    </div>
                    <div class="body">

                        <!-- SUCCESS MESSAGE -->
                        <?php if (!empty($success)): ?>
                            <div class="success-message">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errors['form'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['form']) ?></div>
                        <?php endif; ?>

                        <!-- PRODUCT NAME -->
                        <div class="input">
                            <label for="product name">Product Name</label>
                            <i class="fas fa-box"></i>
                            <input type="text" name="product_name" placeholder="Product Name"
                                value="<?= htmlspecialchars($old['product_name'] ?? '') ?>">
                        </div>

                        <!-- ERROR MESSAGE FOR PRODUCT NAME -->
                        <?php if (!empty($errors['product_name'])): ?>
                            <div class="error-message"><?= htmlspecialchars($errors['product_name']) ?></div>
                        <?php endif; ?>

                        <!-- CATEGORY -->
                        <div class="input">
                            <label>Category</label>
                            <select name="category">
                                <option value="">Select a category</option>

                                <?php foreach ($categories as $cat): ?>
                                    <?php
                                    if ($cat['is_deleted'] == 1)
                                        continue;

                                    $isInactive = ($cat['status'] == 0);
                                    $isSelected = (isset($old['category']) && $old['category'] == $cat['category_id_pk']);
                                    ?>

                                    <option value="<?= $cat['category_id_pk'] ?>" <?= $isSelected ? 'selected' : '' ?>
                                        <?= $isInactive ? 'disabled' : '' ?>>

                                        <?= htmlspecialchars($cat['category_name']) ?>
                                        <?= $isInactive ? ' (Inactive)' : '' ?>
                                    </option>

                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- SELLING PRICE -->
                        <div class="input">
                            <label for="selling price">Selling Price</label>
                            <i class="fas fa-tag"></i>
                            <input type="text" name="selling_price" placeholder="Selling Price"
                                value="<?= htmlspecialchars($old['selling_price'] ?? '') ?>">
                        </div>

                        <!-- ERROR MESSAGE FOR SELLING PRICE -->
                        <?php if (!empty($errors['selling_price'])): ?>
                            <div class="error-message">
                                <?= htmlspecialchars($errors['selling_price']) ?>
                            </div>
                        <?php endif; ?>

                        <!-- DESCRIPTION -->
                        <div class="input">
                            <label for="description">Description (Optional)</label>
                            <i class="fas fa-align-left"></i>
                            <textarea name="product_description"
                                placeholder="Product Description"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                        </div>

                        <!-- STATUS -->
                        <div class="input">
                            <label for="status">Status</label>
                            <select name="status" id="status">
                                <option value="">Select a status</option>

                                <option value="1" <?= isset($old['status']) && $old['status'] == 1 ? 'selected' : '' ?>>
                                    Active
                                </option>

                                <option value="0" <?= isset($old['status']) && $old['status'] == 0 ? 'selected' : '' ?>>
                                    Inactive
                                </option>

                            </select>
                        </div>

                        <button type="submit" name="add_product_btn">Add Product</button>
                    </div>
                </form>
            </div>

        </section>
    </main>
</body>
<script src="../../assets/scripts/pages.js"></script>
</html>