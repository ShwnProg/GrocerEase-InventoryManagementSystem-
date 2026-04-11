<?php
session_start();
require_once '../models/categories.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../forms/index.php");
    exit;
}
$_SESSION['page_title'] = "CATEGORIES";

$category = new Category();
$categories = $category->GetAllCategories();

// echo "hello $user_info[username]";
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
                        <input type="text" name="search" id="search" placeholder="Search a category">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
                <div class="add">
                    <button id = "addbtn">Add Category</button>
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
                        <?php $no = 1; ?>
                        <?php foreach ($categories as $categ): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($categ['category_name']) ?></td>
                                <td><?= htmlspecialchars($categ['category_description']) ?></td>
                                <td>
                                    <div class="actions">
                                        <form action="edit_category.php" method="POST">
                                            <input type="hidden" name="category_id" value="<?= $categ['category_id_pk'] ?>">
                                            <button type="submit" class="edit-btn">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </form>
                                        <form action="delete_category.php" method="POST">
                                            <input type="hidden" name="category_id" value="<?= $categ['category_id_pk'] ?>">
                                            <button type="submit" class="edit-btn">
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

                <form action="../validation/categories/add_category.php" method="POST">
                    <div class="header">

                        <i class="fas fa-plus"></i>
                        <p>Add Category</p>
                        <span id="close-modal">&times;</span>

                    </div>
                    <div class="body">

                        <div class="input">
                            <label for="">Category Name</label>
                            <i class="fas fa-tag"></i>
                            <input type="text" name="category_name" placeholder="Category Name">
                        </div>

                        <div class="input">
                            <label for="">Description</label>
                            <i class="fas fa-align-left"></i>
                            <textarea name="category_description" placeholder="Category Description"></textarea>
                        </div>

                        <button type="submit">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script src="../scripts/pages.js"></script>

</html>