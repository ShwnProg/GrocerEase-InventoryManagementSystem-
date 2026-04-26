<?php
require_once '../../models/user.php';

$page_title = $_SESSION['page_title'] ?? "Dashboard";

$user_id = $_SESSION['id'];
$user = new User($db);
$user_info = $user->GetUserById($user_id);
?>
<div class="topbar">
    <div class="topbar-title">
        <p><?= htmlspecialchars($page_title) ?></p>
    </div>

    <div class="profile-dropdown" id="profile-dropdown">
        <i class="fa-regular fa-circle-user profile-icon"></i>
        <span class="profile-label">
            <?= strtoupper($user_info['username']) ?>
        </span>

        <div class="profile-menu">
            <a href="profile.php" id="profile-link">
                <i class="fa-solid fa-user"></i> View Profile
            </a>
            <!-- <a href="profile.php?tab=password">
                <i class="fa-solid fa-lock"></i> Change Password
            </a> -->
            <div class="logout">
                <a href="/admin/logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>

<script>

    let profileDropdown = document.getElementById('profile-dropdown');
    let profileMenu = document.querySelector('.profile-menu');

    profileDropdown.addEventListener('click', function () {
        profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
    });

    let profileLink = document.getElementById('profile-link');
    let profileSideBar = document.getElementById('profile-menu');

    profileLink.addEventListener('click', function () {
        if (profileSideBar) {
            profileSideBar.classList.add('active');
        }
    });
</script>