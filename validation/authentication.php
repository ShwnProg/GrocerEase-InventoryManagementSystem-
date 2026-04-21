<?php
session_start();
require_once '../models/user.php';
$user = new User();

if ($_SERVER["REQUEST_METHOD"] == "POST") {


// $user->InsertUser("admin",passwo rd_hash("admin123",PASSWORD_DEFAULT),"admin@gmail.com","123-456-7890");

    $username = trim($_POST["username"]) ?? "";
    $password = trim($_POST["password"]) ?? "";

    $error = [];

    if (empty($username))
        $error["username"] = "Username is required";
    if (empty($password))
        $error["password"] = "Password is required";

    if (!empty($username) && !empty($password)) {
        if (!$id =$user->AuthenticateUser($username, $password)) {
            $error["invalid"] = "Invalid username or password";
        }
    }

    if(!empty($error)){
        $_SESSION["error"] = $error;
        $_SESSION["old"] = $_POST;

        header("Location: ../forms/index.php");
        exit;
    }
    $_SESSION['logged_in'] = true;
    $_SESSION['id'] = $id;

    header("Location: ../pages/dashboard.php");
    exit;
}
?>