<?php
session_start();
require_once '../models/categories.php';

include "../includes/auth_check.php";

$_SESSION['page_title'] = "CATEGORIES";

$category = new Category();
$categories = $category->GetAllCategories(); // fetch all categories

// check if modal should open (error or success)
$open_modal = isset($_SESSION['add_category_error']) || isset($_SESSION['success_msg']);
$error = $_SESSION['add_category_error'] ?? [];
$old_inputs = $_SESSION['old_inputs'] ?? [];
$success_msg = $_SESSION['success_msg'] ?? '';

$confirm_delete = false;
$delete_category_id = '';
$delete_category_name = '';

// DELETE BUTTON CLICK
if (isset($_POST['delete_btn'])) {
    $_SESSION['delete_category_id'] = $_POST['category_id']; // store id
    header("Location: categories.php"); // reload page
    exit;
}

// SHOW CONFIRM DELETE MODAL
if (isset($_SESSION['delete_category_id'])) {
    $delete_category_id = $_SESSION['delete_category_id'];
    $delete_category_name = $category->GetCategoryNameById($delete_category_id);
    $confirm_delete = true; // show modal
}

if (isset($_GET['cancel_delete'])) {
    unset($_SESSION['delete_category_id']);
    header("Location: categories.php");
    exit;
}

$delete_success = $_SESSION['success']['delete'] ?? '';
$delete_error = $_SESSION['errors']['delete'] ?? '';

// echo "hello $user_info[username]";
unset($_SESSION['add_category_error'], $_SESSION['old_inputs'], $_SESSION['success_msg']);
unset($_SESSION['success'], $_SESSION['errors']);

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
                <div class="search-area">
                    <form action="">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search a category">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
                <div class="add">
                    <button id="addbtn">Add Category</button>
                </div>
            </div>

            <!-- table -->
            <div class="menu-table">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $num = 0;?>
                        <?php foreach ($categories as $categ): ?>
                            <tr>
                                <?php if($categ['is_deleted'] == 1) continue;?>
                                 <!-- skip deleted categories -->
                                <td><?= ++$num ?></td>
                                <td><?= htmlspecialchars($categ['category_name']) ?></td>
                                 <!-- prevent XSS attack -->
                                <td><?= htmlspecialchars($categ['category_description'] == '' ? 'N/A' : $categ['category_description']) ?></td>
                                <td>
                                    <div class="actions">
                                        <form action="edit_category.php" method="POST">
                                            <input type="hidden" name="category_id" value="<?= $categ['category_id_pk'] ?>">
                                            <button type="submit" class="edit-btn">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </form>
                                        <!-- DELETE -->
                                        <form method="POST">
                                            <input type="hidden" name="category_id" value="<?= $categ['category_id_pk'] ?>">
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
            <!-- Add Category Modal -->
            <div class="add-modal <?php echo $open_modal ? 'active' : ''; ?>" id="add-modal">

                <form action="../validation/categories/add_category.php" method="POST">
                    <div class="header">

                        <i class="fas fa-plus"></i>
                        <p>Add Category</p>
                        <span id="close-modal">&times;</span>

                    </div>
                    <div class="body">
                        <!-- SUCCESS MESSAGE -->
                        <?php if (!empty($success_msg)): ?>
                            <div class="success-message">
                                <p><?= htmlspecialchars($success_msg) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="input">
                            <label for="">Category Name</label>
                            <i class="fas fa-tag"></i>
                            <input type="text" name="category_name" placeholder="Category Name">
                        </div>

                        <?php if (!empty($error['category_name'])): ?>
                            <div class="error-message">
                                <p><?= htmlspecialchars($error['category_name']) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="input">
                            <label for="">Description (OPTIONAL) </label>
                            <i class="fas fa-align-left"></i>
                            <textarea name="category_description" placeholder="Category Description"></textarea>
                        </div>

                        <button type="submit">Add Category</button>
                    </div>
                </form>
            </div>

            <div class="confirm-modal <?= $confirm_delete ? 'active' : '' ?>" id="confirm-modal">
                <div class="modal-content">
                    <div class="modal-icon">
                        <i class="fa-solid fa-trash"></i>
                    </div>
                    <p>Delete <b><?= htmlspecialchars($delete_category_name ?? '') ?></b>?</p>

                    <div class="modal-actions">

                        <button id="cancel-delete" class="cancel-btn">Cancel</button>
                        <!-- CONFIRM DELETE -->
                        <form action="../validation/categories/delete_category.php" method="POST">
                            <input type="hidden" name="category_id" value="<?= $delete_category_id ?>">
                            <button type="submit" id="confirm-delete">Yes, Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
<script src="../scripts/pages.js"></script>

</html>