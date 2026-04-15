<?php 

$current = basename($_SERVER['PHP_SELF']);
$products_active = in_array($current, ['products.php', 'manage_suppliers.php', 'edit_product.php']);

?>

<div class="side-bar">
    <div class="logo">
        <img src="../images/logo.png" alt="Grocer Ease Logo">
        <p>Grocer Ease</p>
    </div>

    <div class="side-bar-menu">
        <label>Dashboard</label>

        <a href="dashboard.php" class="menu-btn <?= $current == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <a href="products.php" class="menu-btn <?= $products_active ? 'active' : '' ?>">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Products</span>
        </a>

        <a href="categories.php" class="menu-btn <?= $current == 'categories.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-table-cells"></i>
            <span>Categories</span>
        </a>

        <a href="suppliers.php" class="menu-btn <?= $current == 'suppliers.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i>
            <span>Suppliers</span>
        </a>

        <a href="stock.php" class="menu-btn <?= $current == 'stock.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-box"></i>
            <span>Stock</span>
        </a>

        <label>Inventory</label>

        <a href="inventory.php" class="menu-btn <?= $current == 'inventory.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-lines"></i>
            <span>Inventory Logs</span>
        </a>

        <a href="archived.php" class="menu-btn <?= $current == 'archived.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-box-archive"></i>
            <span>Archived Records</span>
        </a>

        <div class="action-group">
            <div class="sb-divider"></div>
            <label>Account</label>

            <a href="profile.php" class="action <?= $current == 'profile.php' ? 'active' : '' ?>" id = "profile-menu">
                <i class="fa-solid fa-user"></i>
                <span>Profile</span>
            </a>

            <a href="../validation/logout.php" class="action logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>