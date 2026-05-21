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
                'type' => $this->backupTypeFromFilename(basename($file)),
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

    public function getAutomaticBackupStatus(?int $now = null): array
    {
        $now ??= time();
        $settings = $this->getBackupSettings();

        return [
            'settings' => $settings,
            'enabled' => $settings['auto_backup_enabled'],
            'frequency' => $settings['auto_backup_frequency'],
            'day' => $settings['auto_backup_day'],
            'time' => $settings['auto_backup_time'],
            'last_auto_backup_at' => $settings['last_auto_backup_at'],
            'next_auto_backup_at' => $settings['auto_backup_enabled']
                ? $this->formatTimestamp($this->nextScheduledTimestamp($settings, $now))
                : null,
        ];
    }

    public function shouldRunAutomaticBackup(?int $now = null): array
    {
        $now ??= time();
        $settings = $this->getBackupSettings();

        if (!$settings['auto_backup_enabled']) {
            return [
                'should_run' => false,
                'reason' => 'Automatic backup is disabled.',
                'settings' => $settings,
                'next_auto_backup_at' => null,
            ];
        }

        $scheduledAt = $this->currentScheduledTimestamp($settings, $now);
        $nextAt = $this->nextScheduledTimestamp($settings, $now);

        if ($scheduledAt === null || $now < $scheduledAt) {
            return [
                'should_run' => false,
                'reason' => 'Automatic backup is not due yet.',
                'settings' => $settings,
                'next_auto_backup_at' => $this->formatTimestamp($nextAt),
            ];
        }

        $lastRun = !empty($settings['last_auto_backup_at']) ? strtotime($settings['last_auto_backup_at']) : false;
        if ($lastRun !== false && $lastRun >= $scheduledAt) {
            return [
                'should_run' => false,
                'reason' => 'Automatic backup already ran for this scheduled period.',
                'settings' => $settings,
                'next_auto_backup_at' => $this->formatTimestamp($this->nextScheduledTimestamp($settings, $now + 60)),
            ];
        }

        return [
            'should_run' => true,
            'reason' => ucfirst($settings['auto_backup_frequency']) . ' automatic backup is due.',
            'settings' => $settings,
            'scheduled_at' => $this->formatTimestamp($scheduledAt),
            'next_auto_backup_at' => $this->formatTimestamp($nextAt),
        ];
    }

    public function markAutomaticBackupRun(?int $time = null): array
    {
        $runAt = $time ?? time();
        $settings = $this->getBackupSettings();
        $settings['last_auto_backup_at'] = $this->formatTimestamp($runAt);
        $settings['last_run'] = $settings['last_auto_backup_at'];
        $settings['next_auto_backup_at'] = $settings['auto_backup_enabled']
            ? $this->formatTimestamp($this->nextScheduledTimestamp($settings, $runAt + 60))
            : null;

        return $this->saveBackupSettings($settings);
    }

    public function validateAutomaticBackupInput(array $settings): array
    {
        $frequency = strtolower((string) ($settings['auto_backup_frequency'] ?? $settings['frequency'] ?? 'daily'));
        if (!in_array($frequency, ['daily', 'weekly'], true)) {
            return ['valid' => false, 'message' => 'Frequency must be daily or weekly.'];
        }

        $time = (string) ($settings['auto_backup_time'] ?? $settings['backup_time'] ?? '00:00');
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            return ['valid' => false, 'message' => 'Backup time must use HH:MM format.'];
        }

        if ($frequency === 'weekly') {
            $day = strtolower((string) ($settings['auto_backup_day'] ?? $settings['weekly_day'] ?? ''));
            if (!in_array($day, $this->validWeekdays(), true)) {
                return [
                    'valid' => false,
                    'message' => 'Weekly backup day must be Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, or Sunday.',
                ];
            }
        }

        return ['valid' => true, 'message' => 'Automatic backup settings are valid.'];
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
            'auto_backup_enabled' => false,
            'auto_backup_frequency' => 'daily',
            'auto_backup_day' => 'monday',
            'auto_backup_time' => '00:00',
            'last_auto_backup_at' => null,
            'next_auto_backup_at' => null,
            'auto_backup' => false,
            'frequency' => 'daily',
            'weekly_day' => 'monday',
            'backup_location' => 'local',
            'last_run' => null,
        ];
    }

    private function normalizeBackupSettings(array $settings): array
    {
        $frequency = strtolower((string) ($settings['auto_backup_frequency'] ?? $settings['frequency'] ?? 'daily'));
        if (!in_array($frequency, ['daily', 'weekly'], true)) {
            $frequency = 'daily';
        }

        $weeklyDay = strtolower((string) ($settings['auto_backup_day'] ?? $settings['weekly_day'] ?? 'monday'));
        if (!in_array($weeklyDay, $this->validWeekdays(), true)) {
            $weeklyDay = 'monday';
        }

        $time = (string) ($settings['auto_backup_time'] ?? $settings['backup_time'] ?? '00:00');
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            $time = '00:00';
        }

        $enabledSource = !empty($settings['auto_backup_enabled'])
            ? $settings['auto_backup_enabled']
            : ($settings['auto_backup'] ?? false);
        $enabled = filter_var($enabledSource, FILTER_VALIDATE_BOOLEAN);
        $lastRun = $settings['last_auto_backup_at'] ?? $settings['last_run'] ?? null;
        $lastRun = !empty($lastRun) ? (string) $lastRun : null;
        $normalized = [
            'auto_backup_enabled' => $enabled,
            'auto_backup_frequency' => $frequency,
            'auto_backup_day' => $weeklyDay,
            'auto_backup_time' => $time,
            'last_auto_backup_at' => $lastRun,
            'next_auto_backup_at' => null,
            'backup_location' => 'local',
        ];
        $normalized['next_auto_backup_at'] = $enabled
            ? $this->formatTimestamp($this->nextScheduledTimestamp($normalized))
            : null;

        return [
            'auto_backup_enabled' => $normalized['auto_backup_enabled'],
            'auto_backup_frequency' => $normalized['auto_backup_frequency'],
            'auto_backup_day' => $normalized['auto_backup_day'],
            'auto_backup_time' => $normalized['auto_backup_time'],
            'last_auto_backup_at' => $normalized['last_auto_backup_at'],
            'next_auto_backup_at' => $normalized['next_auto_backup_at'],
            'auto_backup' => $normalized['auto_backup_enabled'],
            'frequency' => $normalized['auto_backup_frequency'],
            'weekly_day' => $normalized['auto_backup_day'],
            'backup_location' => 'local',
            'last_run' => $normalized['last_auto_backup_at'],
        ];
    }

    private function backupTypeFromFilename(string $filename): string
    {
        if (str_starts_with($filename, 'pre_restore_')) {
            return 'Safety Backup';
        }

        if (str_starts_with($filename, 'auto_backup_')) {
            return 'Automatic Backup';
        }

        if (str_starts_with($filename, 'fullbackup_') || str_starts_with($filename, 'backup_')) {
            return 'Full Backup';
        }

        return 'Full Backup';
    }

    private function currentScheduledTimestamp(array $settings, ?int $now = null): ?int
    {
        $now ??= time();
        [$hour, $minute] = array_map('intval', explode(':', $settings['auto_backup_time']));

        if ($settings['auto_backup_frequency'] === 'weekly') {
            $currentDay = strtolower(date('l', $now));
            if ($currentDay !== $settings['auto_backup_day']) {
                return null;
            }
        }

        return mktime($hour, $minute, 0, (int) date('n', $now), (int) date('j', $now), (int) date('Y', $now));
    }

    private function nextScheduledTimestamp(array $settings, ?int $now = null): int
    {
        $now ??= time();
        [$hour, $minute] = array_map('intval', explode(':', $settings['auto_backup_time']));

        if ($settings['auto_backup_frequency'] === 'weekly') {
            $targetDay = ucfirst($settings['auto_backup_day']);
            $candidate = strtotime($targetDay . ' ' . sprintf('%02d:%02d:00', $hour, $minute), $now);
            if ($candidate === false) {
                $candidate = $now;
            }

            if ($candidate <= $now) {
                $candidate = strtotime('next ' . $targetDay . ' ' . sprintf('%02d:%02d:00', $hour, $minute), $now);
            }

            return (int) $candidate;
        }

        $candidate = mktime($hour, $minute, 0, (int) date('n', $now), (int) date('j', $now), (int) date('Y', $now));
        if ($candidate <= $now) {
            $candidate = strtotime('+1 day', $candidate);
        }

        return (int) $candidate;
    }

    private function formatTimestamp(?int $timestamp): ?string
    {
        return $timestamp !== null ? date('Y-m-d H:i:s', $timestamp) : null;
    }

    private function validWeekdays(): array
    {
        return ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
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
