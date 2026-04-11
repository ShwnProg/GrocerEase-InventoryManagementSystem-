<?php
session_start();
require_once '../models/supplier.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../forms/index.php");
    exit;
}
$_SESSION['page_title'] = "SUPPLIERS";

$supplier = new Supplier();
$suppliers = $supplier->GetAllSuppliers();

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
                        <input type="text" name="search" id="search" placeholder="Search a supplier">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
                <div class="add">
                    <button id="addbtn">Add Supplier</button>
                </div>
            </div>

            <!-- table -->
            <div class="menu-table">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Supplier Name</th>
                            <th>Contact Person</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Company Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($suppliers as $sup): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($sup['supplier_name']) ?></td>
                                <td><?= htmlspecialchars($sup['contact_person']) ?></td>
                                <td><?= htmlspecialchars($sup['phone_number']) ?></td>
                                <td><?= htmlspecialchars($sup['email']) ?></td>
                                <td><?= htmlspecialchars($sup['address']) ?></td>
                                <td><?= htmlspecialchars($sup['company_name']) ?></td>
                                <td>
                                    <div class="actions">
                                        <form action="edit_supplier.php" method="POST">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
                                            <button type="submit" class="edit-btn">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </form>
                                        <form action="delete_supplier.php" method="POST">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
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

                <form action="../validation/suppliers/add_supplier.php" method="POST">
                    <div class="header">

                        <i class="fas fa-plus"></i>
                        <p>Add Supplier</p>
                        <span id="close-modal">&times;</span>

                    </div>
                    <div class="body">

                        <div class="input">
                            <label for="">Supplier Name</label>
                            <i class="fas fa-user"></i>
                            <input type="text" name="supplier_name" placeholder="Supplier Name">
                        </div>

                        <div class="input">
                            <label for="">Contact Person</label>
                            <i class="fas fa-user-tie"></i>
                            <input type="text" name="contact_person" placeholder="Contact Person">
                        </div>

                        <div class="input">
                            <label for="">Phone Number</label>
                            <i class="fas fa-phone"></i>
                            <input type="text" name="phone_number" placeholder="Phone Number">
                        </div>

                        <div class="input">
                            <label for="">Email</label>
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="Email">
                        </div>

                        <div class="input">
                            <label for="">Address</label>
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" name="address" placeholder="Address">
                        </div>

                        <div class="input">
                            <label for="">Company Name</label>
                            <i class="fas fa-building"></i>
                            <input type="text" name="company_name" placeholder="Company Name">
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