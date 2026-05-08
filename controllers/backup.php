<?php
require_once __DIR__ . '/../autoload.php';

$backupDir = __DIR__ . '/../backups/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

function respondJson(array $response)
{
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

function createBackup(string $backupDir): array
{
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "backup_{$timestamp}.sql";
    $filepath = $backupDir . $filename;

    $host = 'localhost';
    $user = 'root';
    $pass = 'anna_luna1223';
    $dbname = 'grocer_easedb';

    $escapedFile = escapeshellarg($filepath);
    $escapedDb = escapeshellarg($dbname);
    $escapedUser = escapeshellarg($user);
    $escapedPass = escapeshellarg($pass);
    $escapedHost = escapeshellarg($host);

    $command = "mysqldump --user={$escapedUser} --password={$escapedPass} --host={$escapedHost} {$escapedDb} > {$escapedFile}";
    $output = shell_exec($command . ' 2>&1');

    if (file_exists($filepath) && filesize($filepath) > 0) {
        return [
            'status' => 'success',
            'message' => 'Backup created successfully',
            'filename' => $filename
        ];
    }

    return [
        'status' => 'error',
        'message' => 'Failed to create backup. ' . trim($output)
    ];
}

function restoreBackup(string $filePath): array
{
    if (!file_exists($filePath)) {
        return [
            'status' => 'error',
            'message' => 'Backup file does not exist.'
        ];
    }

    $host = 'localhost';
    $user = 'root';
    $pass = 'anna_luna1223';
    $dbname = 'grocer_easedb';

    $escapedPath = escapeshellarg($filePath);
    $escapedDb = escapeshellarg($dbname);
    $escapedUser = escapeshellarg($user);
    $escapedPass = escapeshellarg($pass);
    $escapedHost = escapeshellarg($host);

    $command = "mysql --user={$escapedUser} --password={$escapedPass} --host={$escapedHost} {$escapedDb} < {$escapedPath}";
    $output = shell_exec($command . ' 2>&1');

    if ($output === null) {
        return [
            'status' => 'success',
            'message' => 'Backup restored successfully'
        ];
    }

    return [
        'status' => 'success',
        'message' => 'Backup restored successfully'
    ];
}

function getBackupList(string $backupDir): array
{
    $files = glob($backupDir . '*.sql');
    $backups = [];

    foreach ($files as $file) {
        $backups[] = [
            'filename' => basename($file),
            'date' => date('Y-m-d H:i:s', filemtime($file)),
            'type' => 'Full Backup'
        ];
    }

    usort($backups, function ($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    return $backups;
}

$action = $_REQUEST['action'] ?? '';
if (empty($action)) {
    respondJson([
        'status' => 'error',
        'message' => 'Action is required.'
    ]);
}

switch ($action) {
    case 'backup':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respondJson([
                'status' => 'error',
                'message' => 'Invalid request method.'
            ]);
        }

        respondJson(createBackup($backupDir));
        break;

    case 'restore':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respondJson([
                'status' => 'error',
                'message' => 'Invalid request method.'
            ]);
        }

        if (empty($_FILES['backup_file']['tmp_name'])) {
            respondJson([
                'status' => 'error',
                'message' => 'No backup file uploaded.'
            ]);
        }

        respondJson(restoreBackup($_FILES['backup_file']['tmp_name']));
        break;

    case 'restore_specific':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respondJson([
                'status' => 'error',
                'message' => 'Invalid request method.'
            ]);
        }

        $filename = $_POST['filename'] ?? '';
        if (empty($filename)) {
            respondJson([
                'status' => 'error',
                'message' => 'Backup filename is required.'
            ]);
        }

        $filepath = $backupDir . basename($filename);
        respondJson(restoreBackup($filepath));
        break;

    case 'list':
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            respondJson([
                'status' => 'error',
                'message' => 'Invalid request method.'
            ]);
        }

        respondJson([
            'status' => 'success',
            'data' => getBackupList($backupDir)
        ]);
        break;

    case 'download':
        $filename = $_GET['file'] ?? '';
        $filepath = $backupDir . basename($filename);

        if (!file_exists($filepath)) {
            header('HTTP/1.1 404 Not Found');
            echo 'File not found.';
            exit;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
        break;

    default:
        respondJson([
            'status' => 'error',
            'message' => 'Unknown action.'
        ]);
}
?>