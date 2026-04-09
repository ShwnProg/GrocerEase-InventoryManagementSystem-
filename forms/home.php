<?php
session_start();
require_once '../models/user.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../forms/index.php");
    exit;
}

$user_id = $_SESSION['id'];

$user = new User();
$user_info = $user->GetUserById($user_id);

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

    <div class="side-bar">

        <div class="logo">
            <img src="../images/logo.png" alt="Grocer Ease Logo">
            <p>Grocer Ease</p>
        </div>

        <div class="side-bar-menu">

            <label for="main-menu">Dashboard</label>

            <!-- HOME MENU -->
            <a href="home.php" class="menu-btn">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            <!-- PRODUCTS MENU -->
            <a href="products.php" class="menu-btn">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Products</span>
            </a>

            <!-- CATEGORIES MENU -->
            <a href="categories.php" class="menu-btn">
                <i class="fa-solid fa-table-cells"></i>
                <span>Categories</span>
            </a>

            <!-- SUPPLIERS MENU -->
            <a href="suppliers.php" class="menu-btn">
                <i class="fa-solid fa-users"></i>
                <span>Suppliers</span>
            </a>

            <!-- STOCK  MENU -->
            <a href="stock.php" class="menu-btn">
                <i class="fa-solid fa-box"></i>
                <span>Stock</span>
            </a>

            <label for="system-menu">inventory</label>

            <!-- INVENTORY MENU -->
            <a href="inventory.php" class="menu-btn">
                <i class="fa-solid fa-file-lines"></i>
                <span>inventory Logs</span>
            </a>

            <!-- ARCHIVED MENU -->
            <a href="archived.php" class="menu-btn">
                <i class="fa-solid fa-box-archive"></i>
                <span>Archived Records</span>
            </a>

            <div class="action-group">
                <div class="sb-divider"></div>
                <label>Account</label>

                <a href="profile.php" class="action">
                    <i class="fa-solid fa-user"></i>
                    <span>Profile</span>
                </a>

                <a href="logout.php" class="action logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>


    <div class="main-content">
        <div class="topbar">
            <div class="admin-title">
                <h2>Dashboard</h2>
            </div>

            <div class="profile-dropdown">
                <i class="fa-regular fa-circle-user profile-icon"></i>
                <span class="profile-label">
                    <?= htmlspecialchars($user_info['username']) ?> Profile
                </span>

                <div class="profile-menu">
                    <a href="profile.php">
                        <i class="fa-solid fa-user"></i> View Profile
                    </a>
                    <a href="profile.php?tab=password">
                        <i class="fa-solid fa-lock"></i> Change Password
                    </a>
                    <div class="logout">
                        <a href="/admin/logout.php">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>