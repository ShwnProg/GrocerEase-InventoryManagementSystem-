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

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <section class="page-content">
            <div class="profile-wrapper">

                <div class="form-card">

                    <div class="card-header">
                        <p class="card-title">Account infromation</p>
                    </div>

                    <form action="../validation/admin_profile/admin_profile.php" method="POST">

                        <div class="section-block">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username"
                                        value="<?= htmlspecialchars($user_info['username']) ?>"
                                        required autocomplete="username">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= htmlspecialchars($user_info['email']) ?>"
                                        required autocomplete="email">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="text" id="contact_number" name="contact_number"
                                        value="<?= htmlspecialchars($user_info['contact_number'] ?? '') ?>"
                                        autocomplete="tel">
                                </div>
                                <div class="form-group">
                                    <label>Role</label>
                                    <div class="role-text"><?= htmlspecialchars($user_info['role'] ?? 'Admin') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="section-block">
                            <p class="section-label">Change Password</p>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" id="password" name="password"
                                        placeholder="Leave blank to keep current"
                                        autocomplete="new-password">
                                    <small>Minimum 6 characters</small>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password"
                                        placeholder="Repeat new password"
                                        autocomplete="new-password">
                                </div>
                            </div>
                        </div>

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