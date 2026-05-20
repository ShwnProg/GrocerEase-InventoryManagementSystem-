<?php

class SimplePdf
{
    private array $objects = [];
    private array $pages = [];
    private string $current = '';
    private float $y = 792;
    private int $fontSize = 10;

    public function __construct(private string $title)
    {
        $this->addPage();
    }

    public function heading(string $text): void
    {
        $this->space(8);
        $this->text($text, 16, true);
        $this->line();
        $this->space(8);
    }

    public function subheading(string $text): void
    {
        $this->space(8);
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

    public function codeBlock(array $lines): void
    {
        $this->space(3);
        foreach ($lines as $line) {
            $this->text($line, 8, false, true);
        }
        $this->space(5);
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
        $this->text($this->title, 18, true);
        $this->text('GrocerEase Inventory Management System', 9);
        $this->line();
        $this->space(12);
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
        $text = str_replace(['->', '=>'], ['->', '=>'], $text);
        return preg_replace('/[^\x20-\x7E]/', '', $text);
    }
}

function buildSystemDocumentation(string $path): void
{
    $pdf = new SimplePdf('System Documentation');
    $pdf->heading('1. Introduction');
    $pdf->paragraph('GrocerEaseIMS is a PHP and MySQL inventory management system for grocery product control. It supports authenticated admin access, product records, categories, suppliers, product-supplier links, stock quantities, inventory movement logs, archived records, reporting charts, and database backup and recovery.');

    $pdf->heading('2. Objectives');
    $pdf->bullet('Maintain accurate product, supplier, category, stock, and movement data.');
    $pdf->bullet('Allow administrators to add, update, archive, restore, and permanently delete records.');
    $pdf->bullet('Provide dashboard visibility for active products, categories, suppliers, stock, low stock, and out-of-stock items.');
    $pdf->bullet('Protect system data through full SQL database backup and verified recovery.');

    $pdf->heading('3. Main Modules');
    $pdf->bullet('Authentication: validates admin login before allowing inventory access.');
    $pdf->bullet('Dashboard: displays KPIs, charts, inventory trends, and quick actions.');
    $pdf->bullet('Products and Categories: manages grocery product details and classification.');
    $pdf->bullet('Suppliers and Product Suppliers: tracks supplier information and preferred supplier cost.');
    $pdf->bullet('Stock Control: records stock in and stock out transactions.');
    $pdf->bullet('Archived Records: supports recovery or hard deletion of archived products, categories, and suppliers.');
    $pdf->bullet('Backup and Recovery: creates full SQL backups, validates restore files, and restores the database.');

    $pdf->heading('4. System Flowchart');
    $pdf->codeBlock([
        '[Start]',
        '   |',
        '[Admin Login]',
        '   |-- invalid --> [Show validation error] --> [Admin Login]',
        '   |-- valid ----> [Dashboard]',
        '                    |',
        '     +--------------+--------------+--------------+',
        '     |              |              |              |',
        '[Products]   [Categories]   [Suppliers]   [Stock Control]',
        '     |              |              |              |',
        '[Add/Edit/Archive] [Add/Edit] [Add/Edit] [Stock In/Out]',
        '     |              |              |              |',
        '     +--------------+-------> [Database] <---------+',
        '                            |',
        '                    [Reports and Logs]',
        '                            |',
        '                    [Backup / Recovery]',
        '                            |',
        '                          [End]',
    ]);

    $pdf->heading('5. Database Tables');
    $pdf->bullet('users: administrator account details.');
    $pdf->bullet('categories: product category names, descriptions, status, and archive fields.');
    $pdf->bullet('products: product names, descriptions, category link, selling price, status, and archive fields.');
    $pdf->bullet('suppliers: supplier contact, address, company, and archive fields.');
    $pdf->bullet('product_supplier: product and supplier link, cost price, and preferred supplier flag.');
    $pdf->bullet('stocks: current stock quantity for each product.');
    $pdf->bullet('stock_movements: stock in/out transaction records and references.');

    $pdf->heading('6. Backup Strategy');
    $pdf->paragraph('The system uses full database backups. Each backup exports table structures and table data into a .sql file inside the backups folder. Full backup was selected because it is simple to demonstrate, easy to restore, and appropriate for the current system size.');

    $pdf->heading('7. Recovery Procedure');
    $pdf->bullet('Open Settings, then use the Backup and Recovery module.');
    $pdf->bullet('Select an existing backup from backup history or upload a .sql backup file.');
    $pdf->bullet('The system validates file type, readability, file size, and SQL contents.');
    $pdf->bullet('Before restore, the current database is automatically backed up as a safety backup.');
    $pdf->bullet('The SQL statements are executed with foreign key checks disabled during restore and enabled again after completion.');
    $pdf->bullet('SweetAlert feedback reports success, validation errors, or recovery errors.');

    $pdf->heading('8. Live Demonstration Script');
    $pdf->bullet('Show existing product, supplier, stock, or category records.');
    $pdf->bullet('Create a backup from Dashboard quick actions or Settings.');
    $pdf->bullet('Delete or modify selected records.');
    $pdf->bullet('Show that the records changed or disappeared.');
    $pdf->bullet('Restore the backup from Settings.');
    $pdf->bullet('Show that the original data returned.');

    $pdf->output($path);
}

function buildBackupDocumentation(string $path): void
{
    $pdf = new SimplePdf('Backup and Recovery Integration Documentation');
    $pdf->heading('1. Integration Summary');
    $pdf->paragraph('The backup and recovery module was rebuilt around a reusable BackupManager class. The controller, Settings page, Dashboard quick actions, and scheduled backup script now use the same backup implementation for consistent behavior.');

    $pdf->heading('2. Files Integrated');
    $pdf->bullet('models/BackupManager.php: creates backups, validates backup files, lists history, and restores SQL.');
    $pdf->bullet('controllers/backup.php: exposes authenticated JSON actions for backup, restore, validation, listing, and download.');
    $pdf->bullet('views/inventory/settings.php: provides backup controls, restore validation, backup history, and SweetAlert feedback.');
    $pdf->bullet('views/inventory/dashboard.php: adds quick actions for backup, recovery, products, stock, and logs.');
    $pdf->bullet('cron_backup.php: uses BackupManager for automatic backups.');
    $pdf->bullet('autoload.php: supports CLI execution for scheduled backups.');

    $pdf->heading('3. Backup Flowchart');
    $pdf->codeBlock([
        '[Admin clicks Backup Now]',
        '          |',
        '[SweetAlert confirmation]',
        '          |-- cancel --> [No change]',
        '          |-- confirm -> [POST controllers/backup.php?action=backup]',
        '                           |',
        '                    [BackupManager reads tables]',
        '                           |',
        '                    [Write CREATE TABLE SQL]',
        '                           |',
        '                    [Write INSERT data SQL]',
        '                           |',
        '                    [Validate .sql file]',
        '                           |-- fail --> [Show error alert]',
        '                           |-- pass --> [Save in /backups]',
        '                                           |',
        '                                    [Refresh history]',
    ]);

    $pdf->heading('4. Recovery Flowchart');
    $pdf->codeBlock([
        '[Admin selects backup]',
        '          |',
        '[Validate file extension, readability, size, SQL contents]',
        '          |-- invalid --> [Show validation error]',
        '          |-- valid ----> [SweetAlert restore confirmation]',
        '                           |',
        '                    [Create pre_restore safety backup]',
        '                           |',
        '                    [Disable foreign key checks]',
        '                           |',
        '                    [Execute SQL statements]',
        '                           |',
        '                    [Enable foreign key checks]',
        '                           |-- error --> [Show error + safety backup name]',
        '                           |-- success -> [Show success + refresh history]',
    ]);

    $pdf->heading('5. Validation Added');
    $pdf->bullet('User must be logged in before backup controller actions run.');
    $pdf->bullet('Only POST can create or restore backups; only GET can list or download.');
    $pdf->bullet('Restore files must be .sql, readable, non-empty, and SQL-like.');
    $pdf->bullet('Stored backup filenames are sanitized and resolved inside the backups folder only.');
    $pdf->bullet('Uploaded files are checked with is_uploaded_file before restore.');
    $pdf->bullet('A safety backup is created before every restore attempt.');

    $pdf->heading('6. Error Handling');
    $pdf->paragraph('All backup controller actions return JSON with a status and message. The interface now checks data.status instead of data.success, so SweetAlert success and error messages match the actual controller response.');

    $pdf->heading('7. Demonstration Notes');
    $pdf->bullet('Use Create Backup before changing records.');
    $pdf->bullet('Modify or archive a visible record, then show that the data changed.');
    $pdf->bullet('Use Recovery Module to restore the selected backup.');
    $pdf->bullet('Show restored products, suppliers, stock, or logs to prove effectiveness.');

    $pdf->output($path);
}

$docsDir = __DIR__;
buildSystemDocumentation($docsDir . '/GrocerEaseIMS_System_Documentation.pdf');
buildBackupDocumentation($docsDir . '/GrocerEaseIMS_Backup_Recovery_Integration.pdf');

echo "PDF documentation generated.\n";
