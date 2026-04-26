<?php
// session_start();
require_once __DIR__ . "/config/db.php";

spl_autoload_register(function ($class) {

    $paths = [
        __DIR__ . '/models/',
        __DIR__ . '/models/Stock/',
        __DIR__ . '/models/Relations/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$database = new DB();
$db = $database->GetConnection();
?>