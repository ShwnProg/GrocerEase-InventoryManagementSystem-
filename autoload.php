<?php
 session_start();
require_once __DIR__ . "/config/db.php";

if (!defined('BASE_URL')) {
    $documentRootPath = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $documentRoot = $documentRootPath !== '' ? str_replace('\\', '/', realpath($documentRootPath)) : '';
    $projectRoot = str_replace('\\', '/', realpath(__DIR__));
    $baseUrl = '';

    if ($documentRoot && strpos($projectRoot, $documentRoot) === 0) {
        $baseUrl = '/' . trim(str_replace($documentRoot, '', $projectRoot), '/');
    }

    define('BASE_URL', $baseUrl === '/' ? '' : $baseUrl);
    define('ASSET_URL', BASE_URL . '/assets');
    define('IMAGE_URL', BASE_URL . '/images');
}

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
