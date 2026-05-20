<?php
require_once __DIR__ . '/../autoload.php';

$backupDir = __DIR__ . '/../backups/';
$manager = new BackupManager($db, $backupDir);

function respondJson(array $response, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

function requireLogin(): void 
{
    if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        respondJson([
            'status' => 'error',
            'message' => 'Your session has expired. Please log in again.',
        ], 401);
    }
}

function requireMethod(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        respondJson([
            'status' => 'error',
            'message' => 'Invalid request method.',
        ], 405);
    }
}

requireLogin();

$action = $_REQUEST['action'] ?? '';
if ($action === '') {
    respondJson([
        'status' => 'error',
        'message' => 'Action is required.',
    ], 400);
}

switch ($action) {
    case 'backup':
        requireMethod('POST');
        respondJson($manager->createBackup());
        break;

    case 'restore':
        requireMethod('POST');

        if (empty($_FILES['backup_file']['tmp_name']) || !is_uploaded_file($_FILES['backup_file']['tmp_name'])) {
            respondJson([
                'status' => 'error',
                'message' => 'Please select a .sql backup file before restoring.',
            ], 400);
        }

        if (($_FILES['backup_file']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            respondJson([
                'status' => 'error',
                'message' => 'The backup upload failed. Please try again.',
            ], 400);
        }

        respondJson($manager->restoreBackup($_FILES['backup_file']['tmp_name'], $_FILES['backup_file']['name'] ?? null));
        break;

    case 'restore_specific':
        requireMethod('POST');

        $filename = trim($_POST['filename'] ?? '');
        if ($filename === '') {
            respondJson([
                'status' => 'error',
                'message' => 'Backup filename is required.',
            ], 400);
        }

        $filepath = $manager->resolveBackupPath($filename);
        if ($filepath === null) {
            respondJson([
                'status' => 'error',
                'message' => 'Invalid backup file selected.',
            ], 400);
        }

        respondJson($manager->restoreBackup($filepath));
        break;

    case 'validate':
        requireMethod('POST');

        if (!empty($_FILES['backup_file']['tmp_name']) && is_uploaded_file($_FILES['backup_file']['tmp_name'])) {
            $validation = $manager->validateBackupFile($_FILES['backup_file']['tmp_name'], $_FILES['backup_file']['name'] ?? null);
            respondJson([
                'status' => $validation['valid'] ? 'success' : 'error',
                'message' => $validation['message'],
            ]);
        }

        $filename = trim($_POST['filename'] ?? '');
        $filepath = $manager->resolveBackupPath($filename);
        if ($filepath === null) {
            respondJson([
                'status' => 'error',
                'message' => 'Invalid backup file selected.',
            ], 400);
        }

        $validation = $manager->validateBackupFile($filepath);
        respondJson([
            'status' => $validation['valid'] ? 'success' : 'error',
            'message' => $validation['message'],
        ]);
        break;

    case 'list':
        requireMethod('GET');
        respondJson([
            'status' => 'success',
            'data' => $manager->getBackupList(),
        ]);
        break;

    case 'delete':
        requireMethod('POST');

        $filename = trim($_POST['filename'] ?? '');
        if ($filename === '') {
            respondJson([
                'status' => 'error',
                'message' => 'Backup filename is required.',
            ], 400);
        }

        respondJson($manager->deleteBackup($filename));
        break;

    case 'settings':
        requireMethod('GET');
        respondJson([
            'status' => 'success',
            'settings' => $manager->getBackupSettings(),
        ]);
        break;

    case 'save_settings':
        requireMethod('POST');
        respondJson($manager->saveBackupSettings($_POST));
        break;

    case 'download':
        requireMethod('GET');

        $filename = trim($_GET['file'] ?? '');
        $filepath = $manager->resolveBackupPath($filename);

        if ($filepath === null || !is_file($filepath)) {
            header('HTTP/1.1 404 Not Found');
            echo 'File not found.';
            exit;
        }

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Content-Length: ' . filesize($filepath));
        header('X-Content-Type-Options: nosniff');
        readfile($filepath);
        exit;

    default:
        respondJson([
            'status' => 'error',
            'message' => 'Unknown action.',
        ], 400);
}
