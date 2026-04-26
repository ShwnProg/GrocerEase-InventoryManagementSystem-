<?php
// session_start();
require_once __DIR__ . "/config/db.php";

spl_autoload_register(function ($class_name) {
    $file = __DIR__ . "/models/" . $class_name . ".php";

    if (file_exists($file)) {
        require_once $file;
    }
});

$database = new DB();
$db = $database->GetConnection();
?>