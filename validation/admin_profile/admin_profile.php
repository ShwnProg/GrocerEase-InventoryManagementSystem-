<?php
session_start();
require_once '../../models/user.php';

$redirect = "../../pages/profile.php";

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../../.../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $redirect");
    exit;
}

$user     = new User();
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$contact  = trim($_POST['contact_number'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

$error = null;

if (empty($username) || empty($email)) {
    $error = "Username and email are required.";

} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email address.";

} elseif ($user->UsernameExists($username, $user_id)) {
    $error = "Username is already taken.";

} elseif ($user->EmailExists($email, $user_id)) {
    $error = "Email is already in use.";

} elseif (!empty($password) && strlen($password) < 6) {
    $error = "Password must be at least 6 characters.";

} elseif (!empty($password) && $password !== $confirm) {
    $error = "Passwords do not match.";
}

if ($error) {
    $_SESSION['error'] = $error;
    header("Location: $redirect");
    exit;
}

// Detect changes
$current = $user->GetUserById($user_id);

$info_changed = (
    $username !== $current['username'] ||
    $email    !== $current['email']    ||
    $contact  !== ($current['contact_number'] ?? '')
);

$password_changed = !empty($password);

if (!$info_changed && !$password_changed) {
    $_SESSION['error'] = "No changes were made.";
    header("Location: $redirect");
    exit;
}

if ($info_changed) {
    $user->UpdateUser($user_id, $username, $email, $contact);
    $_SESSION['username'] = $username;
}

if ($password_changed) {
    $user->UpdatePassword($user_id, password_hash($password, PASSWORD_DEFAULT));
}

$_SESSION['success'] = "Profile updated successfully!";
header("Location: $redirect");
exit;
?>