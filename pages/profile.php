<?php
session_start();
require_once '../models/user.php';

include "../includes/auth_check.php";

$_SESSION['page_title'] = "MY PROFILE";

?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php"?>


<body>

    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../includes/topbar.php'; ?>

    </div>
</body>

</html>