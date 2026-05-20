<?php
// Run this file from Windows Task Scheduler or cron for automatic full backups.

require_once __DIR__ . '/autoload.php';

$manager = new BackupManager($db, __DIR__ . '/backups/');
$schedule = $manager->shouldRunAutomaticBackup();

if (!$schedule['should_run']) {
    echo $schedule['reason'] . PHP_EOL;
    exit(0);
}

$result = $manager->createBackup('auto_backup');

if ($result['status'] === 'success') {
    $manager->markAutomaticBackupRun();
    echo 'Backup completed successfully: ' . $result['filename'] . PHP_EOL;
} else {
    echo $result['message'] . PHP_EOL;
    exit(1);
}

$keepDays = 30;
$cutoff = time() - ($keepDays * 24 * 60 * 60);
$files = glob(__DIR__ . '/backups/*.sql') ?: [];

foreach ($files as $file) {
    if (is_file($file) && filemtime($file) < $cutoff) {
        unlink($file);
        echo 'Deleted old backup: ' . basename($file) . PHP_EOL;
    }
}
