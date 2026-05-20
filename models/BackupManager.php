<?php

class BackupManager
{
    private PDO $conn;
    private string $backupDir;
    private string $databaseName;
    private array $protectedTables = [];

    public function __construct(PDO $db, string $backupDir)
    {
        $this->conn = $db;
        $this->backupDir = rtrim($backupDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->databaseName = (string) $this->conn->query('SELECT DATABASE()')->fetchColumn();

        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function createBackup(string $prefix = 'backup'): array
    {
        $filename = $this->buildBackupFilename($prefix);
        $filepath = $this->backupDir . $filename;

        try {
            $handle = fopen($filepath, 'wb');
            if ($handle === false) {
                throw new RuntimeException('The backup folder is not writable.');
            }

            $tables = $this->getTables();
            if (empty($tables)) {
                throw new RuntimeException('No database tables were found to back up.');
            }

            $this->writeLine($handle, '-- GrocerEaseIMS Backup');
            $this->writeLine($handle, '-- Database: ' . $this->databaseName);
            $this->writeLine($handle, '-- Created at: ' . date('Y-m-d H:i:s'));
            $this->writeLine($handle, '-- Backup type: Full database backup');
            $this->writeLine($handle, '');
            $this->writeLine($handle, 'SET FOREIGN_KEY_CHECKS=0;');
            $this->writeLine($handle, 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";');
            $this->writeLine($handle, 'SET time_zone = "+00:00";');
            $this->writeLine($handle, '');

            foreach ($tables as $table) {
                $this->dumpTable($handle, $table);
            }

            $this->writeLine($handle, 'SET FOREIGN_KEY_CHECKS=1;');
            fclose($handle);

            $validation = $this->validateBackupFile($filepath);
            if (!$validation['valid']) {
                throw new RuntimeException($validation['message']);
            }

            return [
                'status' => 'success',
                'message' => 'Backup created successfully.',
                'filename' => $filename,
                'size' => filesize($filepath),
                'tables' => count($tables),
            ];
        } catch (Throwable $e) {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }

            if (file_exists($filepath) && filesize($filepath) === 0) {
                unlink($filepath);
            }

            return [
                'status' => 'error',
                'message' => 'Backup failed: ' . $e->getMessage(),
            ];
        }
    }

    public function restoreBackup(string $filepath, ?string $sourceName = null): array
    {
        $validation = $this->validateBackupFile($filepath, $sourceName);
        if (!$validation['valid']) {
            return [
                'status' => 'error',
                'message' => $validation['message'],
            ];
        }

        $safetyBackup = $this->createBackup('pre_restore');
        if ($safetyBackup['status'] !== 'success') {
            return [
                'status' => 'error',
                'message' => 'Restore cancelled because the current database could not be backed up first. ' . $safetyBackup['message'],
            ];
        }

        try {
            $sql = file_get_contents($filepath);
            if ($sql === false) {
                throw new RuntimeException('The backup file could not be read.');
            }

            $statements = $this->splitSqlStatements($sql);
            if (empty($statements)) {
                throw new RuntimeException('The backup file does not contain executable SQL.');
            }

            $this->conn->exec('SET FOREIGN_KEY_CHECKS=0');
            foreach ($statements as $statement) {
                $trimmed = trim($statement);
                if ($trimmed === '') {
                    continue;
                }
                $this->conn->exec($trimmed);
            }
            $this->conn->exec('SET FOREIGN_KEY_CHECKS=1');

            return [
                'status' => 'success',
                'message' => 'Database restored successfully.',
                'safety_backup' => $safetyBackup['filename'],
                'statements' => count($statements),
            ];
        } catch (Throwable $e) {
            try {
                $this->conn->exec('SET FOREIGN_KEY_CHECKS=1');
            } catch (Throwable $ignored) {
            }

            return [
                'status' => 'error',
                'message' => 'Restore failed: ' . $e->getMessage() . ' A safety backup was created before restore: ' . $safetyBackup['filename'],
                'safety_backup' => $safetyBackup['filename'],
            ];
        }
    }

    public function getBackupList(): array
    {
        $files = glob($this->backupDir . '*.sql') ?: [];
        $backups = [];

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $backups[] = [
                'filename' => basename($file),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
                'type' => str_starts_with(basename($file), 'pre_restore_') ? 'Safety Backup' : 'Full Backup',
                'size' => filesize($file),
                'readable_size' => $this->formatBytes((int) filesize($file)),
                'valid' => $this->validateBackupFile($file)['valid'],
            ];
        }

        usort($backups, fn ($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));

        return $backups;
    }

    public function deleteBackup(string $filename): array
    {
        $filepath = $this->resolveBackupPath($filename);
        if ($filepath === null || !is_file($filepath)) {
            return [
                'status' => 'error',
                'message' => 'Invalid backup file selected.',
            ];
        }

        if (!is_writable($filepath)) {
            return [
                'status' => 'error',
                'message' => 'The selected backup file cannot be deleted.',
            ];
        }

        if (!unlink($filepath)) {
            return [
                'status' => 'error',
                'message' => 'Failed to delete backup file.',
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Backup deleted successfully.',
            'filename' => basename($filepath),
        ];
    }

    public function getBackupSettings(): array
    {
        $defaults = $this->defaultBackupSettings();
        $path = $this->settingsPath();

        if (!is_file($path)) {
            return $defaults;
        }

        $settings = json_decode((string) file_get_contents($path), true);
        if (!is_array($settings)) {
            return $defaults;
        }

        return $this->normalizeBackupSettings(array_merge($defaults, $settings));
    }

    public function saveBackupSettings(array $settings): array
    {
        $normalized = $this->normalizeBackupSettings($settings);
        $json = json_encode($normalized, JSON_PRETTY_PRINT);

        if ($json === false || file_put_contents($this->settingsPath(), $json) === false) {
            return [
                'status' => 'error',
                'message' => 'Backup settings could not be saved.',
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Backup settings saved successfully.',
            'settings' => $normalized,
        ];
    }

    public function shouldRunAutomaticBackup(?int $now = null): array
    {
        $now ??= time();
        $settings = $this->getBackupSettings();

        if (!$settings['auto_backup']) {
            return [
                'should_run' => false,
                'reason' => 'Automatic backup is disabled.',
                'settings' => $settings,
            ];
        }

        $lastRun = $settings['last_run'] ?? null;
        $today = date('Y-m-d', $now);

        if ($settings['frequency'] === 'weekly') {
            $currentDay = strtolower(date('l', $now));
            if ($currentDay !== $settings['weekly_day']) {
                return [
                    'should_run' => false,
                    'reason' => 'Today is not the selected weekly backup day.',
                    'settings' => $settings,
                ];
            }

            if ($lastRun && date('o-W', strtotime($lastRun)) === date('o-W', $now)) {
                return [
                    'should_run' => false,
                    'reason' => 'Weekly automatic backup already ran this week.',
                    'settings' => $settings,
                ];
            }

            return [
                'should_run' => true,
                'reason' => 'Weekly automatic backup is due.',
                'settings' => $settings,
            ];
        }

        if ($lastRun && date('Y-m-d', strtotime($lastRun)) === $today) {
            return [
                'should_run' => false,
                'reason' => 'Daily automatic backup already ran today.',
                'settings' => $settings,
            ];
        }

        return [
            'should_run' => true,
            'reason' => 'Daily automatic backup is due.',
            'settings' => $settings,
        ];
    }

    public function markAutomaticBackupRun(?int $time = null): array
    {
        $settings = $this->getBackupSettings();
        $settings['last_run'] = date('Y-m-d H:i:s', $time ?? time());

        return $this->saveBackupSettings($settings);
    }

    public function resolveBackupPath(string $filename): ?string
    {
        $safeName = basename($filename);
        if ($safeName === '' || !preg_match('/^[A-Za-z0-9_.-]+\.sql$/', $safeName)) {
            return null;
        }

        $path = realpath($this->backupDir . $safeName);
        $dir = realpath($this->backupDir);

        if ($path === false || $dir === false || strpos($path, $dir) !== 0) {
            return null;
        }

        return $path;
    }

    public function validateBackupFile(string $filepath, ?string $sourceName = null): array
    {
        if (!is_file($filepath)) {
            return ['valid' => false, 'message' => 'Backup file does not exist.'];
        }

        $nameForExtension = $sourceName ?: $filepath;
        if (strtolower(pathinfo($nameForExtension, PATHINFO_EXTENSION)) !== 'sql') {
            return ['valid' => false, 'message' => 'Only .sql backup files are allowed.'];
        }

        if (!is_readable($filepath)) {
            return ['valid' => false, 'message' => 'Backup file is not readable.'];
        }

        if (filesize($filepath) <= 0) {
            return ['valid' => false, 'message' => 'Backup file is empty.'];
        }

        $sample = file_get_contents($filepath, false, null, 0, 12000);
        if ($sample === false) {
            return ['valid' => false, 'message' => 'Backup file could not be inspected.'];
        }

        $looksLikeSql = stripos($sample, 'CREATE TABLE') !== false
            || stripos($sample, 'INSERT INTO') !== false
            || stripos($sample, 'GrocerEaseIMS Backup') !== false
            || stripos($sample, 'MySQL dump') !== false;

        if (!$looksLikeSql) {
            return ['valid' => false, 'message' => 'The selected file does not look like a valid database backup.'];
        }

        return ['valid' => true, 'message' => 'Backup file is valid.'];
    }

    private function dumpTable($handle, string $table): void
    {
        $quotedTable = $this->quoteIdentifier($table);
        $this->writeLine($handle, '--');
        $this->writeLine($handle, '-- Table structure for ' . $quotedTable);
        $this->writeLine($handle, '--');
        $this->writeLine($handle, 'DROP TABLE IF EXISTS ' . $quotedTable . ';');

        $stmt = $this->conn->query('SHOW CREATE TABLE ' . $quotedTable);
        $create = $stmt->fetch(PDO::FETCH_ASSOC);
        $createSql = $create['Create Table'] ?? array_values($create)[1] ?? '';
        if ($createSql === '') {
            throw new RuntimeException('Could not read table structure for ' . $table . '.');
        }

        $this->writeLine($handle, $createSql . ';');
        $this->writeLine($handle, '');

        $this->writeLine($handle, '--');
        $this->writeLine($handle, '-- Data for ' . $quotedTable);
        $this->writeLine($handle, '--');

        $rows = $this->conn->query('SELECT * FROM ' . $quotedTable, PDO::FETCH_ASSOC);
        $batch = [];
        $columns = null;

        foreach ($rows as $row) {
            if ($columns === null) {
                $columns = array_keys($row);
            }

            $values = array_map(fn ($value) => $this->sqlValue($value), array_values($row));
            $batch[] = '(' . implode(',', $values) . ')';

            if (count($batch) >= 50) {
                $this->writeInsertBatch($handle, $table, $columns, $batch);
                $batch = [];
            }
        }

        if (!empty($batch) && $columns !== null) {
            $this->writeInsertBatch($handle, $table, $columns, $batch);
        }

        $this->writeLine($handle, '');
    }

    private function writeInsertBatch($handle, string $table, array $columns, array $batch): void
    {
        $columnSql = implode(',', array_map([$this, 'quoteIdentifier'], $columns));
        $this->writeLine($handle, 'INSERT INTO ' . $this->quoteIdentifier($table) . ' (' . $columnSql . ') VALUES');
        $this->writeLine($handle, implode(',' . PHP_EOL, $batch) . ';');
    }

    private function getTables(): array
    {
        $stmt = $this->conn->query('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
        $tables = [];

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            if (!in_array($row[0], $this->protectedTables, true)) {
                $tables[] = $row[0];
            }
        }

        return $tables;
    }

    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $length = strlen($sql);
        $quote = null;
        $escaped = false;
        $lineComment = false;
        $blockComment = false;

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $next = $i + 1 < $length ? $sql[$i + 1] : '';

            if ($lineComment) {
                $buffer .= $char;
                if ($char === "\n") {
                    $lineComment = false;
                }
                continue;
            }

            if ($blockComment) {
                $buffer .= $char;
                if ($char === '*' && $next === '/') {
                    $buffer .= $next;
                    $i++;
                    $blockComment = false;
                }
                continue;
            }

            if ($quote !== null) {
                $buffer .= $char;
                if ($escaped) {
                    $escaped = false;
                    continue;
                }
                if ($char === '\\') {
                    $escaped = true;
                    continue;
                }
                if ($char === $quote) {
                    $quote = null;
                }
                continue;
            }

            if (($char === '-' && $next === '-') || $char === '#') {
                $lineComment = true;
                $buffer .= $char;
                continue;
            }

            if ($char === '/' && $next === '*') {
                $blockComment = true;
                $buffer .= $char;
                continue;
            }

            if ($char === "'" || $char === '"' || $char === '`') {
                $quote = $char;
                $buffer .= $char;
                continue;
            }

            if ($char === ';') {
                $trimmed = trim($buffer);
                if ($trimmed !== '') {
                    $statements[] = $trimmed;
                }
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        $trimmed = trim($buffer);
        if ($trimmed !== '') {
            $statements[] = $trimmed;
        }

        return $statements;
    }

    private function sqlValue($value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return $this->conn->quote((string) $value);
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function writeLine($handle, string $line): void
    {
        fwrite($handle, $line . PHP_EOL);
    }

    private function buildBackupFilename(string $prefix): string
    {
        $safePrefix = preg_replace('/[^A-Za-z0-9_-]/', '', $prefix) ?: 'backup';
        return $safePrefix . '_' . date('Y-m-d_H-i-s') . '.sql';
    }

    private function settingsPath(): string
    {
        return $this->backupDir . 'backup_settings.json';
    }

    private function defaultBackupSettings(): array
    {
        return [
            'auto_backup' => true,
            'frequency' => 'daily',
            'weekly_day' => 'monday',
            'backup_location' => 'local',
            'last_run' => null,
        ];
    }

    private function normalizeBackupSettings(array $settings): array
    {
        $frequency = strtolower((string) ($settings['frequency'] ?? 'daily'));
        if (!in_array($frequency, ['daily', 'weekly'], true)) {
            $frequency = 'daily';
        }

        $weeklyDay = strtolower((string) ($settings['weekly_day'] ?? 'monday'));
        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        if (!in_array($weeklyDay, $validDays, true)) {
            $weeklyDay = 'monday';
        }

        return [
            'auto_backup' => filter_var($settings['auto_backup'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'frequency' => $frequency,
            'weekly_day' => $weeklyDay,
            'backup_location' => 'local',
            'last_run' => !empty($settings['last_run']) ? (string) $settings['last_run'] : null,
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
