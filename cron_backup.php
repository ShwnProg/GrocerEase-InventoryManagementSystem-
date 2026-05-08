<?php
// Cron job script for automatic backup
// Run this script daily or weekly based on settings

require_once __DIR__ . '/autoload.php';

class AutoBackup
{
    private $db;
    private $backupDir = __DIR__ . '/backups/';

    public function __construct($db)
    {
        $this->db = $db;
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function performBackup()
    {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "auto_backup_{$timestamp}.sql";
            $filepath = $this->backupDir . $filename;

            // Database credentials
            $host = "localhost";
            $user = "root";
            $pass = "anna_luna1223";
            $dbname = "grocer_easedb";

            // mysqldump command
            $command = "mysqldump --user={$user} --password={$pass} --host={$host} {$dbname} > \"{$filepath}\"";

            // Execute the command
            $output = shell_exec($command . " 2>&1");

            if (file_exists($filepath) && filesize($filepath) > 0) {
                // Log successful backup
                error_log("Auto backup created: {$filename}");
                return true;
            } else {
                error_log("Auto backup failed: {$output}");
                return false;
            }
        } catch (Exception $e) {
            error_log("Auto backup error: " . $e->getMessage());
            return false;
        }
    }

    public function cleanupOldBackups($keepDays = 30)
    {
        $files = glob($this->backupDir . '*.sql');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= $keepDays * 24 * 60 * 60) {
                    unlink($file);
                    error_log("Deleted old backup: " . basename($file));
                }
            }
        }
    }
}

// Run the backup
$autoBackup = new AutoBackup($db);
$success = $autoBackup->performBackup();

// Cleanup old backups (keep last 30 days)
$autoBackup->cleanupOldBackups(30);

if ($success) {
    echo "Backup completed successfully\n";
} else {
    echo "Backup failed\n";
}
?>