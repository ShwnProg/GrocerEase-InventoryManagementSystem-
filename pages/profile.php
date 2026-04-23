<?php
session_start();
require_once '../models/user.php';
include "../includes/auth_check.php";

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    die("You are not logged in. Please <a href='../forms/index.php'>login</a> first.");
}

$user      = new User();
$user_info = $user->GetUserById($user_id);
if (!$user_info) {
    die("User profile not found.");
}

$_SESSION['page_title'] = "MY PROFILE";
?>
<!DOCTYPE html>
<html lang="en">
<?php include "../includes/head.php"; ?>

<body>
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include '../includes/topbar.php'; ?>

        <section class="page-content">

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fa fa-circle-check"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <i class="fa fa-circle-exclamation"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="profile-wrapper">
                <div class="form-card">

                    <!-- CARD HEADER -->
                    <div class="card-header">
                        <div class="card-header-icon">
                            <i class="fa fa-user"></i>
                        </div>
                        <div>
                            <p class="card-title">Account Information</p>
                            <p class="card-subtitle">Manage your personal details and password</p>
                        </div>
                    </div>

                    <form action="../validation/admin_profile/admin_profile.php" method="POST">

                        <!-- PERSONAL INFO -->
                        <div class="section-block">
                            <p class="section-label">Personal Details</p>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <div class="input-icon-wrap">
                                        <i class="fa fa-user"></i>
                                        <input type="text" id="username" name="username"
                                            value="<?= htmlspecialchars($user_info['username']) ?>"
                                            required autocomplete="username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <div class="input-icon-wrap">
                                        <i class="fa fa-envelope"></i>
                                        <input type="email" id="email" name="email"
                                            value="<?= htmlspecialchars($user_info['email']) ?>"
                                            required autocomplete="email">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <div class="input-icon-wrap">
                                        <i class="fa fa-phone"></i>
                                        <input type="text" id="contact_number" name="contact_number"
                                            value="<?= htmlspecialchars($user_info['contact_number'] ?? '') ?>"
                                            autocomplete="tel">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Role</label>
                                    <div class="role-text">
                                        <i class="fa fa-shield-halved"></i>
                                        <?= htmlspecialchars($user_info['role'] ?? 'Admin') ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CHANGE PASSWORD -->
                        <div class="section-block">
                            <p class="section-label">Change Password</p>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <div class="input-icon-wrap">
                                        <i class="fa fa-lock"></i>
                                        <input type="password" id="password" name="password"
                                            placeholder="Leave blank to keep current"
                                            autocomplete="new-password">
                                    </div>
                                    <small>Minimum 6 characters</small>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password</label>
                                    <div class="input-icon-wrap">
                                        <i class="fa fa-lock"></i>
                                        <input type="password" id="confirm_password" name="confirm_password"
                                            placeholder="Repeat new password"
                                            autocomplete="new-password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="card-footer">
                            <button type="submit" class="btn-save">
                                <i class="fa fa-save"></i> Save Changes
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </section>
    </main>
</body>
</html>