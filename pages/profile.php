<?php
session_start();
require_once __DIR__ . '../../autoload.php';

include "../includes/auth_check.php";

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    die("You are not logged in. Please <a href='../forms/index.php'>login</a> first.");
}

$user      = new User($db);
$user_info = $user->GetUserById($user_id);
if (!$user_info) {
    die("User profile not found.");
}

$_SESSION['page_title'] = "MY PROFILE";

$initials = strtoupper(substr($user_info['username'] ?? 'A', 0, 1));
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
                <form action="../validation/admin_profile/admin_profile.php" method="POST">

                    <!-- IDENTITY CARD -->
                    <div class="profile-card identity-card">
                        <div class="avatar">
                            <?= htmlspecialchars($initials) ?>
                        </div>
                        <div class="identity-info">
                            <p class="identity-name"><?= htmlspecialchars($user_info['username']) ?></p>
                            <p class="identity-email"><?= htmlspecialchars($user_info['email']) ?></p>
                        </div>
                        <span class="role-badge">
                            <i class="fa fa-shield-halved"></i>
                            <?= htmlspecialchars($user_info['role'] ?? 'Admin') ?>
                        </span>
                    </div>

                    <!-- PERSONAL DETAILS -->
                    <div class="profile-card">
                        <p class="card-section-label">Personal Details</p>
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="username">Username</label>
                                <div class="input-wrap">
                                    <i class="fa fa-user"></i>
                                    <input type="text" id="username" name="username"
                                        value="<?= htmlspecialchars($user_info['username']) ?>"
                                        required autocomplete="username">
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="email">Email</label>
                                <div class="input-wrap">
                                    <i class="fa fa-envelope"></i>
                                    <input type="email" id="email" name="email"
                                        value="<?= htmlspecialchars($user_info['email']) ?>"
                                        required autocomplete="email">
                                </div>
                            </div>
                            <div class="form-field">
                                <label for="contact_number">Contact Number</label>
                                <div class="input-wrap">
                                    <i class="fa fa-phone"></i>
                                    <input type="text" id="contact_number" name="contact_number"
                                        value="<?= htmlspecialchars($user_info['contact_number'] ?? '') ?>"
                                        autocomplete="tel">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CHANGE PASSWORD -->
                    <div class="profile-card">
                        <p class="card-section-label">Change Password</p>
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="password">New Password</label>
                                <div class="input-wrap">
                                    <i class="fa fa-lock"></i>
                                    <input type="password" id="password" name="password"
                                        placeholder="Leave blank to keep current"
                                        autocomplete="new-password">
                                </div>
                                <span class="field-hint">Minimum 6 characters</span>
                            </div>
                            <div class="form-field">
                                <label for="confirm_password">Confirm Password</label>
                                <div class="input-wrap">
                                    <i class="fa fa-lock"></i>
                                    <input type="password" id="confirm_password" name="confirm_password"
                                        placeholder="Repeat new password"
                                        autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SAVE -->
                    <div class="form-actions">
                        <button type="submit" class="btn-save">
                            <i class="fa fa-save"></i> Save Changes
                        </button>
                    </div>

                </form>
            </div>

        </section>
    </main>
</body>
</html>