<?php

class ReviewerPdf
{
    private array $objects = [];
    private array $pages = [];
    private string $current = '';
    private float $y = 792;

    public function __construct(private string $title)
    {
        $this->addPage();
    }

    public function titlePage(string $subtitle, array $lines): void
    {
        $this->space(80);
        $this->text($this->title, 22, true);
        $this->space(8);
        $this->text($subtitle, 13, true);
        $this->line();
        $this->space(12);
        foreach ($lines as $line) {
            $this->paragraph($line);
        }
        $this->addPage();
    }

    public function heading(string $text): void
    {
        $this->space(8);
        $this->text($text, 15, true);
        $this->line();
        $this->space(7);
    }

    public function subheading(string $text): void
    {
        $this->space(7);
        $this->text($text, 12, true);
        $this->space(4);
    }

    public function paragraph(string $text): void
    {
        foreach ($this->wrap($text, 92) as $line) {
            $this->text($line, 10);
        }
        $this->space(5);
    }

    public function bullet(string $text): void
    {
        foreach ($this->wrap('- ' . $text, 88) as $line) {
            $this->text($line, 10);
        }
    }

    public function numbered(array $items): void
    {
        foreach ($items as $index => $item) {
            foreach ($this->wrap(($index + 1) . '. ' . $item, 88) as $line) {
                $this->text($line, 10);
            }
        }
        $this->space(4);
    }

    public function codeBlock(array $lines): void
    {
        $this->space(3);
        foreach ($lines as $line) {
            foreach ($this->wrap($line, 98) as $wrapped) {
                $this->text($wrapped, 8, false, true);
            }
        }
        $this->space(5);
    }

    public function table(array $headers, array $rows, array $widths): void
    {
        $line = [];
        foreach ($headers as $i => $header) {
            $line[] = str_pad(substr($this->ascii($header), 0, $widths[$i]), $widths[$i]);
        }
        $this->codeBlock([implode(' | ', $line), str_repeat('-', array_sum($widths) + (count($widths) - 1) * 3)]);

        foreach ($rows as $row) {
            $wrappedColumns = [];
            $maxLines = 1;
            foreach ($row as $i => $cell) {
                $wrapped = $this->wrap($this->ascii((string) $cell), $widths[$i]);
                $wrappedColumns[$i] = $wrapped;
                $maxLines = max($maxLines, count($wrapped));
            }

            for ($lineIndex = 0; $lineIndex < $maxLines; $lineIndex++) {
                $parts = [];
                foreach ($row as $i => $_) {
                    $value = $wrappedColumns[$i][$lineIndex] ?? '';
                    $parts[] = str_pad(substr($value, 0, $widths[$i]), $widths[$i]);
                }
                $this->text(implode(' | ', $parts), 8, false, true);
            }
            $this->space(3);
        }
        $this->space(6);
    }

    public function output(string $path): void
    {
        $this->finishPage();

        $catalogId = $this->addObject('<< /Type /Catalog /Pages 2 0 R >>');
        $kids = implode(' ', array_map(fn ($page) => $page . ' 0 R', $this->pages));
        $this->objects[2] = "<< /Type /Pages /Kids [{$kids}] /Count " . count($this->pages) . ' >>';

        ksort($this->objects);
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($this->objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$body}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($this->objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($this->objects); $i++) {
            $pdf .= str_pad((string) ($offsets[$i] ?? 0), 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size " . (count($this->objects) + 1) . " /Root {$catalogId} 0 R >>\n";
        $pdf .= "startxref\n{$xref}\n%%EOF";
        file_put_contents($path, $pdf);
    }

    private function addPage(): void
    {
        if ($this->current !== '') {
            $this->finishPage();
        }

        $this->current = '';
        $this->y = 742;
        $this->text($this->title, 17, true);
        $this->text('GrocerEase Inventory Management System', 9);
        $this->line();
        $this->space(10);
    }

    private function finishPage(): void
    {
        if ($this->current === '') {
            return;
        }

        $contentId = $this->addObject('<< /Length ' . strlen($this->current) . " >>\nstream\n" . $this->current . "endstream");
        $pageId = $this->addObject("<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 3 0 R /F2 4 0 R /F3 5 0 R >> >> /Contents {$contentId} 0 R >>");
        $this->pages[] = $pageId;
        $this->current = '';
    }

    private function addObject(string $body): int
    {
        $id = count($this->objects) + 1;
        while (isset($this->objects[$id])) {
            $id++;
        }
        $this->objects[$id] = $body;

        if (!isset($this->objects[3])) {
            $this->objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
            $this->objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';
            $this->objects[5] = '<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>';
        }

        return $id;
    }

    private function text(string $text, int $size = 10, bool $bold = false, bool $mono = false): void
    {
        if ($this->y < 54) {
            $this->addPage();
        }

        $font = $mono ? 'F3' : ($bold ? 'F2' : 'F1');
        $safe = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $this->ascii($text));
        $this->current .= "BT /{$font} {$size} Tf 54 {$this->y} Td ({$safe}) Tj ET\n";
        $this->y -= $size + 4;
    }

    private function line(): void
    {
        $this->current .= "0.85 w 54 {$this->y} m 558 {$this->y} l S\n";
        $this->y -= 8;
    }

    private function space(int $height): void
    {
        $this->y -= $height;
    }

    private function wrap(string $text, int $width): array
    {
        return explode("\n", wordwrap($this->ascii($text), $width));
    }

    private function ascii(string $text): string
    {
        $replacements = [
            '“' => '"',
            '”' => '"',
            '’' => "'",
            '–' => '-',
            '—' => '-',
        ];
        $text = strtr($text, $replacements);
        return preg_replace('/[^\x20-\x7E]/', '', $text);
    }
}

$pdf = new ReviewerPdf('Backup and Recovery Module Reviewer');

$pdf->titlePage('Code Review and Technical Documentation Guide', [
    'This reviewer explains the implemented Backup and Recovery module of GrocerEaseIMS. It is written for student presentation, defense preparation, and technical review.',
    'The document focuses only on the implemented manual features: Create Backup, Restore Backup, View Backup History, and Download Backup. Delete Backup is discussed as a requested feature that is not implemented in the current backup code.',
    'Automatic Backup is intentionally not included in this reviewer because it is outside the requested scope.',
]);

$pdf->heading('1. Module Overview');
$pdf->paragraph('The Backup and Recovery module protects the system database by creating full SQL backup files and allowing an administrator to restore data from a selected SQL backup. In simple terms, it gives the administrator a way to save the current database state and recover it later if records are damaged, deleted, or changed incorrectly.');
$pdf->paragraph('In this system, backups are stored as .sql files inside the local /backups folder. The module is important because GrocerEaseIMS manages products, categories, suppliers, stocks, and transaction-related records. If the database becomes corrupted or important data is accidentally changed, a valid backup can help bring the system back to an earlier working state.');
$pdf->paragraph('The implemented backup module is mainly composed of a frontend page in views/inventory/settings.php, a backend controller in controllers/backup.php, and a reusable model class in models/BackupManager.php.');

$pdf->heading('2. Features Covered');
$pdf->subheading('Create Backup');
$pdf->bullet('Purpose: Creates a full SQL copy of the current MySQL database.');
$pdf->bullet('User/Admin action: Admin clicks Backup now and confirms the SweetAlert prompt.');
$pdf->bullet('System action: The frontend sends POST controllers/backup.php?action=backup. The controller calls BackupManager::createBackup().');
$pdf->bullet('Expected result: A file named backup_YYYY-MM-DD_HH-MM-SS.sql is created in /backups and the backup history refreshes.');
$pdf->bullet('Possible errors: Expired session, invalid request method, unwritable backup folder, no database tables found, invalid generated SQL, or unexpected server response.');

$pdf->subheading('Restore Backup');
$pdf->bullet('Purpose: Replaces the current database content by executing SQL statements from a valid backup file.');
$pdf->bullet('User/Admin action: Admin either uploads a .sql file or selects a valid backup from history and confirms restore.');
$pdf->bullet('System action: The selected file is validated, a pre_restore safety backup is created, foreign key checks are disabled, SQL statements are executed, and foreign key checks are enabled again.');
$pdf->bullet('Expected result: The database is restored and the system shows the safety backup filename.');
$pdf->bullet('Possible errors: Invalid file, unreadable or empty backup, file does not look like SQL, safety backup creation fails, SQL execution fails, or server returns non-JSON output.');

$pdf->subheading('View Backup History');
$pdf->bullet('Purpose: Shows available local backup files so the admin can inspect, download, or restore them.');
$pdf->bullet('User/Admin action: Admin opens the Settings backup page.');
$pdf->bullet('System action: JavaScript calls GET controllers/backup.php?action=list. BackupManager scans /backups/*.sql files and returns filename, date, type, size, readable size, and valid status.');
$pdf->bullet('Expected result: The table displays backup files sorted by newest date first.');
$pdf->bullet('Possible errors: Controller path is wrong, session expired, JSON parsing fails, backup folder cannot be scanned, or no files exist.');

$pdf->subheading('Download Backup');
$pdf->bullet('Purpose: Allows the admin to download an existing SQL backup file.');
$pdf->bullet('User/Admin action: Admin clicks the download icon in the backup history table.');
$pdf->bullet('System action: Browser navigates to controllers/backup.php?action=download&file=filename. The controller resolves the safe path and streams the SQL file.');
$pdf->bullet('Expected result: The selected .sql file is downloaded.');
$pdf->bullet('Possible errors: File is missing, filename is invalid, path validation fails, session expired, or the controller returns 404 File not found.');

$pdf->subheading('Delete Backup');
$pdf->bullet('Purpose requested: Remove an old or unwanted backup file from storage.');
$pdf->bullet('Actual current status: Not implemented in the backup module code reviewed. There is no delete action in controllers/backup.php, no deleteBackup method in BackupManager.php, and no delete button in views/inventory/settings.php.');
$pdf->bullet('Expected result if implemented later: Admin confirms delete, controller validates filename inside /backups, deletes the file with unlink(), returns JSON, and refreshes history.');
$pdf->bullet('Possible errors if implemented later: Invalid filename, missing file, permission denied, attempt to delete outside /backups, or unauthorized request.');

$pdf->heading('3. Workflow Explanation');
$pdf->paragraph('The flow starts when the administrator opens the Backup and Recovery area in the Settings page. The page displays backup status cards, action buttons, restore file controls, and a backup history table. The admin then chooses an action: create backup, restore backup, view history, or download backup. Delete backup should only be shown in the flow as not implemented unless the code is updated.');
$pdf->subheading('Backup Flow');
$pdf->numbered([
    'Admin clicks Backup now.',
    'System shows confirmation asking if the admin wants to create a database backup.',
    'If cancelled, the flow ends with no change.',
    'If confirmed, the frontend sends POST controllers/backup.php?action=backup.',
    'The controller checks if the admin session is valid and if the request method is POST.',
    'BackupManager exports the full database into a SQL file.',
    'The generated SQL file is validated.',
    'If backup creation fails, the UI shows an error message.',
    'If successful, the SQL file is saved in /backups and history is refreshed.',
]);
$pdf->subheading('Recovery Flow');
$pdf->numbered([
    'Admin chooses a backup source: upload a .sql file or restore an existing file from backup history.',
    'The system validates the selected file extension, readability, file size, and SQL-like content.',
    'If invalid, the system shows a validation error.',
    'If valid, the admin confirms the restore warning.',
    'If cancelled, restore stops.',
    'If confirmed, the system creates a pre_restore safety backup first.',
    'The system reads and splits SQL statements, disables foreign key checks, executes the restore, and enables foreign key checks again.',
    'If restore fails, the system shows an error and includes the safety backup filename when available.',
    'If restore succeeds, the system shows a success message and refreshes backup history.',
]);
$pdf->subheading('Backup History Flow');
$pdf->numbered([
    'Settings page calls GET controllers/backup.php?action=list.',
    'BackupManager scans the local /backups folder for .sql files.',
    'Each file is checked for filename, date, type, size, and valid status.',
    'The frontend displays rows with Download and Restore buttons.',
    'Download streams the file through the backup controller.',
    'Delete is not part of the current backup history UI.',
]);
$pdf->subheading('Decision Box Meanings');
$pdf->bullet('Admin chooses backup and recovery action: User chooses backup, restore, history, download, or requested delete.');
$pdf->bullet('Is the database connection valid: The PDO connection must be available from config/db.php and autoload.php.');
$pdf->bullet('Was the backup created successfully: BackupManager must create and validate a non-empty SQL file.');
$pdf->bullet('Does the admin confirm restore: SweetAlert confirmation must be accepted before restore continues.');
$pdf->bullet('Is the selected backup file valid: The file must be .sql, readable, non-empty, and SQL-like.');
$pdf->bullet('Was the restore completed successfully: SQL statements must execute without exception.');
$pdf->bullet('Admin chooses backup history action: Current code supports download and restore; delete is not implemented.');

$pdf->heading('4. Code Review');
$pdf->table(
    ['File', 'Purpose', 'Important Functions'],
    [
        ['models/BackupManager.php', 'Core backup logic. Creates SQL backups, restores SQL, validates files, lists backup history, resolves safe paths.', 'createBackup, restoreBackup, getBackupList, resolveBackupPath, validateBackupFile, dumpTable, splitSqlStatements'],
        ['controllers/backup.php', 'Authenticated HTTP endpoint for backup module actions. Returns JSON for most actions and streams SQL for download.', 'respondJson, requireLogin, requireMethod, switch actions: backup, restore, restore_specific, validate, list, download'],
        ['views/inventory/settings.php', 'Frontend UI for backup controls, restore upload, backup history, download, and restore from history.', 'backupButton click handler, restore file validation, loadBackupHistory, downloadBackup, restoreSpecific'],
        ['views/inventory/dashboard.php', 'Contains a quick action that can trigger backup creation from the dashboard.', 'backupFromDashboard'],
        ['autoload.php', 'Starts session, loads database config, registers model autoloading, creates PDO connection.', 'spl_autoload_register, DB connection creation'],
        ['config/db.php', 'Defines MySQL connection credentials and creates a PDO connection.', 'DB::GetConnection'],
    ],
    [26, 39, 30]
);
$pdf->paragraph('Data moves from frontend to backend through fetch requests. For backup and list, the action can be in the query string. For restore and validate, FormData is used because files or filenames are sent through POST. The controller returns JSON objects with status and message. The frontend reads data.status and shows SweetAlert success or error messages.');

$pdf->heading('5. How the Code Works');
$pdf->subheading('Create Backup Technical Process');
$pdf->numbered([
    'Admin clicks Backup now in settings.php or uses the dashboard quick action.',
    'JavaScript shows SweetAlert confirmation.',
    'If confirmed, JavaScript sends POST ../../controllers/backup.php?action=backup.',
    'controllers/backup.php requires login and POST method.',
    'The controller calls BackupManager::createBackup().',
    'createBackup builds a timestamped filename and opens it in /backups.',
    'It reads all base tables using SHOW FULL TABLES WHERE Table_type = BASE TABLE.',
    'For each table it writes DROP TABLE IF EXISTS, CREATE TABLE SQL, and INSERT INTO rows.',
    'It writes SQL settings including SET FOREIGN_KEY_CHECKS=0 at the start and SET FOREIGN_KEY_CHECKS=1 at the end.',
    'It validates the generated SQL file and returns JSON status, message, filename, size, and table count.',
    'The frontend shows Backup created or Backup failed and refreshes backup history on success.',
]);
$pdf->subheading('Restore Backup Technical Process');
$pdf->numbered([
    'Available backup files are loaded by loadBackupHistory through action=list.',
    'Admin can upload a .sql file or restore a valid file from the backup history table.',
    'Uploaded files are checked first by extension in JavaScript, then by action=validate in the controller.',
    'BackupManager::validateBackupFile checks existence, .sql extension, readability, non-empty file size, and SQL-like content.',
    'Before restore, SweetAlert warns the admin that current database state will be replaced.',
    'For uploaded restore, action=restore sends backup_file through FormData.',
    'For history restore, action=restore_specific sends the selected filename.',
    'The controller resolves stored filenames safely through BackupManager::resolveBackupPath.',
    'BackupManager::restoreBackup validates the file again, creates a pre_restore safety backup, reads SQL content, splits statements, disables foreign key checks, executes each statement, and re-enables foreign key checks.',
    'On success, JSON includes status success and safety_backup. On failure, JSON includes an error message and the safety backup filename when it was created.',
]);
$pdf->subheading('Backup History, Download, and Delete Technical Process');
$pdf->bullet('Backup history is not stored in a database table. It is built dynamically by scanning /backups/*.sql files.');
$pdf->bullet('getBackupList returns filename, file modification date, type, raw size, readable size, and valid status.');
$pdf->bullet('Download uses action=download and file=filename. The controller validates the filename path and streams the SQL file with application/sql headers.');
$pdf->bullet('If a file is missing during download, the controller returns HTTP 404 and plain text File not found.');
$pdf->bullet('Delete backup is not currently implemented. There is no action=delete case in controllers/backup.php and no frontend function that deletes a backup file.');

$pdf->heading('6. Database Involvement');
$pdf->bullet('Database used: MySQL database named grocer_easedb, connected through PDO in config/db.php.');
$pdf->bullet('Tables involved: The backup process includes all MySQL base tables returned by SHOW FULL TABLES. This means products, categories, suppliers, stock, product-supplier relation tables, transaction logs, users, and other base tables are included if present in the database.');
$pdf->bullet('Backup history storage: There is no database table for backup history. History is based on actual SQL files in the /backups folder.');
$pdf->bullet('Filename and date handling: The filename uses a prefix and date/time format. The date displayed in history is the file modification time from filemtime().');
$pdf->bullet('Status handling: Valid status is calculated each time by validating the backup file. It is not permanently stored in the database.');
$pdf->bullet('File path handling: Stored backup filenames are sanitized with basename and a regex, then resolved with realpath to ensure they stay inside /backups.');
$pdf->bullet('During restore: SQL statements from the backup are executed against the current MySQL database. Existing tables may be dropped and recreated based on the backup content.');

$pdf->heading('7. Frontend and Backend Connection');
$pdf->table(
    ['UI Action', 'JavaScript', 'Endpoint', 'Response'],
    [
        ['Backup now', 'backupButton click', 'POST controllers/backup.php?action=backup', 'JSON success/error'],
        ['Upload restore file', 'restoreFileInput change', 'POST controllers/backup.php action=validate', 'JSON validation result'],
        ['Restore uploaded file', 'restoreUploadedButton click', 'POST controllers/backup.php action=restore', 'JSON success/error with safety_backup'],
        ['Load history', 'loadBackupHistory', 'GET controllers/backup.php?action=list', 'JSON list data'],
        ['Download backup', 'downloadBackup', 'GET controllers/backup.php?action=download&file=...', 'SQL file stream or 404'],
        ['Restore from history', 'restoreSpecific', 'POST controllers/backup.php action=restore_specific', 'JSON success/error with safety_backup'],
        ['Delete backup', 'Not present', 'Not present', 'Not implemented'],
    ],
    [21, 24, 32, 22]
);
$pdf->subheading('Common Error Causes');
$pdf->bullet('404 Not Found for backup controller: wrong relative path, controller file missing, app installed in a different folder, server rewrite/path issue, or request sent from a page with a different nesting level.');
$pdf->bullet("Unexpected token '<': JavaScript expected JSON but received HTML. Common causes are PHP fatal error page, login page due to expired session, 404 HTML page, or warning/notice printed before JSON.");
$pdf->bullet('Backup button not working: JavaScript error, SweetAlert not loaded, wrong controller path, session expired, POST blocked, database connection failure, or /backups folder not writable.');
$pdf->bullet('Restore not working: invalid .sql file, file upload error, PHP upload limit, failed safety backup, SQL statement error, permission problem, or foreign key/data incompatibility.');
$pdf->bullet('History not loading: action=list fails, session expired, controller returns HTML, /backups folder missing or unreadable, JSON parse error, or JavaScript rendering error.');

$pdf->heading('8. Security and Safety Review');
$pdf->bullet('Admin-only access: controllers/backup.php calls requireLogin(), so actions require a logged-in session.');
$pdf->bullet('Method restrictions: backup, restore, restore_specific, and validate require POST; list and download require GET.');
$pdf->bullet('Restore file validation: files must be .sql, readable, non-empty, and must contain SQL-like markers such as CREATE TABLE, INSERT INTO, GrocerEaseIMS Backup, or MySQL dump.');
$pdf->bullet('Path validation: resolveBackupPath uses basename, a strict filename regex, realpath, and a directory containment check.');
$pdf->bullet('Safety backup: restore creates a pre_restore backup before applying SQL changes.');
$pdf->bullet('Risk: SQL files are stored under a project folder. If the web server exposes /backups directly, users may access database dumps if they know or list filenames.');
$pdf->bullet('Risk: Any valid-looking SQL file can be restored by an authenticated admin. A bad SQL file can overwrite or damage the database.');
$pdf->bullet('Risk: There is no audit log table recording who created, restored, downloaded, or attempted backup actions.');
$pdf->bullet('Risk: Delete backup is not implemented, so old backups must be removed manually unless another process handles cleanup.');
$pdf->bullet('Recommended improvements: protect /backups with server rules, move backups outside public web root, add CSRF protection, add role-based admin checks, add audit logs, add delete with confirmation and path validation, use transactions where possible, and test restore on a staging copy before production use.');

$pdf->heading('9. Problems Found and Fix Suggestions');
$pdf->table(
    ['Problem Found', 'Location', 'Why It Is A Problem', 'Suggested Fix', 'Priority'],
    [
        ['Delete backup feature requested but not implemented', 'controllers/backup.php, BackupManager.php, settings.php', 'The reviewer/flow includes Delete Backup, but no endpoint, model method, or UI button exists.', 'Add deleteBackup method, action=delete, SweetAlert confirmation, POST only, path validation, and refresh history.', 'High'],
        ['Backups may be publicly accessible', '/backups folder', 'SQL files may contain full database data including sensitive records.', 'Move backups outside public web root or add server deny rules such as .htaccess or web server config.', 'High'],
        ['No audit logging', 'No backup audit table/code', 'Cannot prove who created, restored, downloaded, or attempted backup actions.', 'Create backup_audit_logs table and insert action, admin id, filename, status, IP, and timestamp.', 'Medium'],
        ['No CSRF token on backup/restore requests', 'settings.php and backup.php', 'A logged-in admin could be tricked into sending a backup/restore request.', 'Add CSRF token to forms/fetch requests and verify in controller.', 'High'],
        ['Restore executes uploaded SQL', 'BackupManager::restoreBackup', 'A malicious or wrong SQL file can damage the database if accepted.', 'Restrict restore to trusted generated backups or add stricter signature/header checks.', 'High'],
        ['Download returns plain text 404 instead of JSON', 'controllers/backup.php download action', 'Normal for file download, but inconsistent for AJAX-style error handling.', 'Keep as is for browser download, or add optional JSON error mode for fetch-based UI.', 'Low'],
        ['Backup history not stored in database', 'BackupManager::getBackupList', 'History depends only on files, so deleted/moved files remove history.', 'Optional: add backup_records table if persistent audit/history is required.', 'Low'],
        ['Database credentials are hardcoded', 'config/db.php', 'Credentials in source code are risky if repository is shared.', 'Move credentials to environment config or a local ignored config file.', 'Medium'],
    ],
    [22, 20, 27, 29, 7]
);

$pdf->heading('10. Presentation Guide');
$pdf->subheading('Opening Script');
$pdf->paragraph('Good day. This part of GrocerEaseIMS is the Backup and Recovery module. Its purpose is to protect the inventory database by allowing an administrator to create a full SQL backup, restore a valid backup, view backup history, and download backup files.');
$pdf->subheading('Create Backup Script');
$pdf->paragraph('When the admin clicks Backup now, the system asks for confirmation. After confirmation, the frontend sends a POST request to the backup controller. The controller calls BackupManager, which exports all database tables into a timestamped SQL file inside the /backups folder. If the SQL file is valid, the system shows a success message and refreshes the backup history.');
$pdf->subheading('Restore Backup Script');
$pdf->paragraph('For restore, the admin can upload a SQL file or select a valid file from backup history. The system validates the file before allowing restore. Before changing the database, it creates a pre_restore safety backup. Then it executes the SQL backup into the MySQL database. If successful, the system shows the safety backup filename.');
$pdf->subheading('View History, Download, and Delete Script');
$pdf->paragraph('Backup history is displayed by scanning the local /backups folder. The table shows filename, date, type, size, and validity status. The admin can download a backup through the controller. Delete Backup is not included in the current implemented code, so I will present it as a missing or future improvement, not as a working feature.');
$pdf->subheading('Automatic Backup Statement');
$pdf->paragraph('Automatic Backup is not included in this reviewer because the requested scope is the manual Backup and Recovery module. I will not present it as part of the implemented workflow.');
$pdf->subheading('Panelist Answer Tips');
$pdf->bullet('If asked why backup is needed: It protects the database from accidental data loss or wrong changes.');
$pdf->bullet('If asked where files are stored: They are stored locally in the project /backups folder.');
$pdf->bullet('If asked about delete: The current backup module has no delete endpoint yet; it is a recommended improvement.');
$pdf->bullet('If asked about restore safety: The system creates a pre_restore safety backup before applying restore.');
$pdf->bullet('If asked about security: Login is required and paths are validated, but backups should still be protected from public access.');

$pdf->heading('11. Possible Questions and Answers');
$pdf->table(
    ['Question', 'Answer'],
    [
        ['What is the purpose of backup and recovery?', 'To save the current database state and restore it later if data is lost, damaged, or changed incorrectly.'],
        ['Why is backup important?', 'The system stores important inventory records. A backup helps recover products, suppliers, stocks, and other records after mistakes or failures.'],
        ['What happens when backup fails?', 'The controller returns an error JSON response and the frontend shows a Backup failed SweetAlert message.'],
        ['What happens when restore fails?', 'The system returns a restore error. If the safety backup was created, the message includes its filename.'],
        ['Why should restore be restricted to admin?', 'Restore can replace the entire database, so only trusted administrators should be allowed to do it.'],
        ['Where are backup files stored?', 'They are stored as .sql files in the local /backups folder.'],
        ['How does the system know if backup is successful?', 'BackupManager creates the SQL file, validates it, and returns status=success with filename, size, and table count.'],
        ['Why is automatic backup not included?', 'It is not included in this reviewer because the requested module scope is manual Backup and Recovery only.'],
        ['What are the risks of restore?', 'A restore can overwrite current database data. A wrong or malicious SQL file can damage records or structure.'],
        ['How do you prevent invalid backup files?', 'The code checks file existence, .sql extension, readability, non-empty size, and SQL-like content before restore.'],
        ['Is Delete Backup working?', 'No. Delete Backup is not implemented in the reviewed backup code. It should be added before presenting it as a working feature.'],
        ['What happens if a backup file is missing during download?', 'The controller returns HTTP 404 with File not found.'],
    ],
    [35, 62]
);

$pdf->heading('12. Final Summary');
$pdf->paragraph('The Backup and Recovery module is a practical protection feature for GrocerEaseIMS. The admin can create a full SQL backup, view available backup files, download them, and restore the database from a valid backup. The module uses BackupManager.php for the core database export and restore logic, controllers/backup.php for authenticated backend actions, and views/inventory/settings.php for the user interface.');
$pdf->paragraph('The strongest safety feature is the pre_restore backup created before restore. The important limitations are that Delete Backup is not implemented, backup audit logging is missing, and the /backups folder should be protected from public access. For presentation, the module should be explained as a manual backup and recovery workflow, not an automatic backup system.');

$path = __DIR__ . '/Backup_and_Recovery_Module_Reviewer.pdf';
$pdf->output($path);

echo "Generated: {$path}\n";
