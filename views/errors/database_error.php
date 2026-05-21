<?php
session_start();

$basePath = dirname(__DIR__, 2);
require_once $basePath . '/config/db.php';

if (!defined('BASE_URL')) {
    $documentRootPath = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $documentRootRealPath = $documentRootPath !== '' ? realpath($documentRootPath) : false;
    $projectRootRealPath = realpath($basePath);
    $documentRoot = $documentRootRealPath ? str_replace('\\', '/', $documentRootRealPath) : '';
    $projectRoot = $projectRootRealPath ? str_replace('\\', '/', $projectRootRealPath) : '';
    $baseUrl = '';

    if ($documentRoot !== '' && $projectRoot !== '' && strpos($projectRoot, $documentRoot) === 0) {
        $baseUrl = '/' . trim(str_replace($documentRoot, '', $projectRoot), '/');
    }

    define('BASE_URL', $baseUrl === '/' ? '' : $baseUrl);
    define('ASSET_URL', BASE_URL . '/assets');
    define('IMAGE_URL', BASE_URL . '/images');
}

$databaseError = $_SESSION['database_error'] ?? [];
$errorMessage = (string) ($databaseError['message'] ?? '');
$errorCode = (string) ($databaseError['code'] ?? '');
$checkedAt = (string) ($databaseError['time'] ?? date('Y-m-d H:i:s'));
$retryUrl = (string) ($databaseError['request_uri'] ?? BASE_URL . '/');
$statusTitle = 'Database unavailable';
$statusMessage = 'Grocer Ease cannot connect to its MySQL database right now.';

if (stripos($errorMessage, 'Unknown database') !== false || $errorCode === '1049') {
    $statusTitle = 'Database not found';
    $statusMessage = 'The configured database may have been dropped or has not been restored yet.';
}

http_response_code(503);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($statusTitle) ?> | Grocer Ease</title>
    <link rel="icon" type="image/png" href="<?= IMAGE_URL ?>/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            font-family: Inter, Roboto, Arial, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f6f8f5;
            color: #1f2933;
            padding: 24px;
        }

        .database-status {
            width: 100%;
            max-width: 720px;
            background: #ffffff;
            border: 1px solid #dfe8dc;
            border-radius: 8px;
            box-shadow: 0 14px 35px rgba(21, 45, 18, 0.1);
            overflow: hidden;
        }

        .status-header {
            display: flex;
            gap: 16px;
            align-items: center;
            padding: 28px 30px;
            border-bottom: 1px solid #e7ede5;
        }

        .status-icon {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #eaf4e7;
            color: #1c5515;
            flex: 0 0 auto;
        }

        .status-icon i {
            font-size: 22px;
        }

        .status-eyebrow {
            margin: 0 0 5px;
            color: #5f6f5b;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            color: #173f13;
            font-size: 26px;
            line-height: 1.2;
            font-weight: 700;
        }

        .status-body {
            padding: 26px 30px 30px;
        }

        .status-message {
            margin: 0 0 18px;
            color: #3b4638;
            font-size: 15px;
            line-height: 1.6;
        }

        .status-panel {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin: 20px 0 24px;
        }

        .status-item {
            min-height: 82px;
            padding: 14px;
            border: 1px solid #e4ebe1;
            border-radius: 8px;
            background: #fbfcfb;
        }

        .status-item span {
            display: block;
            margin-bottom: 6px;
            color: #667061;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-item strong {
            display: block;
            color: #20301d;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.35;
        }

        .status-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .status-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid transparent;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: transform 0.15s ease, background 0.15s ease, border-color 0.15s ease;
        }

        .status-button.primary {
            background: #1c5515;
            color: #ffffff;
        }

        .status-button.secondary {
            border-color: #cfdccc;
            background: #ffffff;
            color: #1c5515;
        }

        .status-button:hover {
            transform: translateY(-1px);
        }

        .status-note {
            margin: 18px 0 0;
            padding: 12px 14px;
            border-left: 3px solid #1c5515;
            background: #f1f7ef;
            color: #465543;
            font-size: 13px;
            line-height: 1.5;
        }

        @media (max-width: 640px) {
            body {
                padding: 16px;
                align-items: flex-start;
            }

            .status-header,
            .status-body {
                padding: 22px;
            }

            .status-header {
                align-items: flex-start;
            }

            h1 {
                font-size: 22px;
            }

            .status-panel {
                grid-template-columns: 1fr;
            }

            .status-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <main class="database-status" role="main">
        <div class="status-header">
            <div class="status-icon" aria-hidden="true">
                <i class="fa-solid fa-database"></i>
            </div>
            <div>
                <p class="status-eyebrow">Grocer Ease System Status</p>
                <h1><?= htmlspecialchars($statusTitle) ?></h1>
            </div>
        </div>

        <section class="status-body">
            <p class="status-message">
                <?= htmlspecialchars($statusMessage) ?>
                The application is paused to protect inventory records from incomplete transactions.
            </p>

            <div class="status-panel" aria-label="Database status details">
                <div class="status-item">
                    <span>Status</span>
                    <strong>Service unavailable</strong>
                </div>
                <div class="status-item">
                    <span>Checked</span>
                    <strong><?= htmlspecialchars($checkedAt) ?></strong>
                </div>
                <div class="status-item">
                    <span>Recommended action</span>
                    <strong>Restore the database backup, then retry.</strong>
                </div>
            </div>

            <div class="status-actions">
                <a class="status-button primary" href="<?= htmlspecialchars($retryUrl) ?>">
                    <i class="fa-solid fa-rotate-right"></i>
                    Retry
                </a>
                <a class="status-button secondary" href="<?= BASE_URL ?>/">
                    <i class="fa-solid fa-house"></i>
                    Login page
                </a>
            </div>

            <p class="status-note">
                If the database was dropped, recreate <strong>grocer_easedb</strong> and import a valid SQL backup before using the system again.
            </p>
        </section>
    </main>
</body>
</html>
