<?php
session_start();

if (isset($_SESSION['error']) || isset($_SESSION['old'])) {
    $error = $_SESSION['error'] ?? [];
    $old = $_SESSION['old'] ?? [];
}

unset($_SESSION['error'],$_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocer Ease</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <!-- LOGO -->
        <div class="logo">
            <img src="../images/page-logo.png" alt="">
            <p>Grocer Ease</p>
        </div>
        <form action="../validation/authentication.php" method="post">

            <!-- INVALID AUTHENTICATION -->
            <?php if (!empty($error['invalid'])): ?>
                <div class="error-global">
                    <i class="fa-regular fa-circle-xmark"></i>
                    <span><?= htmlspecialchars($error['invalid']) ?></span>
                </div>
            <?php endif ?>

            <!-- USERNAME -->
            <div class="input">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="username" placeholder="Username"
                    value="<?= htmlspecialchars($old['username'] ?? '') ?>">
            </div>

            <?php if (!empty($error['username'])): ?>
                <span class="error-text"><?= htmlspecialchars($error['username']) ?></span>
            <?php endif ?>

            <!-- PASSWORD -->
            <div class="input">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Password" id="password"
                    value="<?= htmlspecialchars($old['password'] ?? '') ?>">
                <i class="fa-regular fa-eye-slash toggle-password" id="togglePassword"></i>
            </div>
            
            <?php if (!empty($error['password'])): ?>
                <span class="error-text"><?= htmlspecialchars($error['password']) ?></span>
            <?php endif ?>

            <button type="submit">LOGIN</button>
        </form>
    </div>
</body>
<script src="../scripts/script.js"></script>

</html>