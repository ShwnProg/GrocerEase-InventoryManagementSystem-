<?php
/*
 * Automatic Backup runner for GrocerEaseIMS.
 *
 * The Backup and Recovery UI saves the automatic backup schedule. This script
 * reads those settings, checks whether the saved schedule is due, and creates
 * an automatic SQL backup only when needed. It prevents duplicate backups for
 * the same daily or weekly scheduled period.
 *
 * Server cron setup examples:
 * Linux hosting, every 5 minutes:
 *   /usr/bin/php /absolute/path/to/GrocerEaseIMS/cron_backup.php
 *
 * Windows Task Scheduler:
 *   Program: C:\path\to\php.exe
 *   Arguments: C:\path\to\GrocerEaseIMS\cron_backup.php
 *
 * Automatic backup does not run by itself unless the server calls this file
 * regularly. Running it every few minutes or every hour is acceptable because
 * this script decides whether a backup is actually due.
 */

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
