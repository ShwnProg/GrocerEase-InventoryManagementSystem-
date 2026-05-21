<?php
date_default_timezone_set('Asia/Manila');
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

function isDatabaseJsonRequest(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

    return stripos($accept, 'application/json') !== false
        || strtolower($requestedWith) === 'xmlhttprequest';
}

function handleDatabaseFailure(Throwable $exception): void
{
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, "Database connection failed: " . $exception->getMessage() . PHP_EOL);
        exit(1);
    }

    http_response_code(503);
    $_SESSION['database_error'] = [
        'code' => $exception instanceof PDOException ? $exception->getCode() : 'DATABASE_ERROR',
        'message' => $exception->getMessage(),
        'request_uri' => $_SERVER['REQUEST_URI'] ?? BASE_URL . '/',
        'time' => date('Y-m-d H:i:s'),
    ];

    $errorUrl = BASE_URL . '/views/errors/database_error.php';

    if (isDatabaseJsonRequest()) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'The database is currently unavailable. Please restore or reconnect the database, then try again.',
            'redirect' => $errorUrl,
        ]);
        exit;
    }

    header('Location: ' . $errorUrl);
    exit;
}

set_exception_handler(function (Throwable $exception): void {
    if ($exception instanceof PDOException) {
        handleDatabaseFailure($exception);
    }

    throw $exception;
});

try {
    $database = new DB();
    $db = $database->GetConnection();
} catch (Throwable $exception) {
    handleDatabaseFailure($exception);
}
?>
