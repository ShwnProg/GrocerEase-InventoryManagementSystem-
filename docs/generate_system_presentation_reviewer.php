<?php

class PresentationPdf
{
    private array $objects = [];
    private array $pages = [];
    private string $current = '';
    private float $y = 792;

    public function __construct(private string $title)
    {
        $this->addPage();
    }

    public function titlePage(array $lines): void
    {
        $this->space(80);
        $this->text($this->title, 22, true);
        $this->space(10);
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
        foreach ($items as $i => $item) {
            foreach ($this->wrap(($i + 1) . '. ' . $item, 88) as $line) {
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

    public function qa(array $items): void
    {
        foreach ($items as $question => $answer) {
            $this->text('Q: ' . $question, 10, true);
            $this->paragraph('A: ' . $answer);
        }
    }

    public function table(array $headers, array $rows, array $widths): void
    {
        $headerLine = [];
        foreach ($headers as $i => $header) {
            $headerLine[] = str_pad(substr($this->ascii($header), 0, $widths[$i]), $widths[$i]);
        }
        $this->codeBlock([implode(' | ', $headerLine), str_repeat('-', array_sum($widths) + (count($widths) - 1) * 3)]);
        foreach ($rows as $row) {
            $wrapped = [];
            $max = 1;
            foreach ($row as $i => $cell) {
                $wrapped[$i] = $this->wrap((string) $cell, $widths[$i]);
                $max = max($max, count($wrapped[$i]));
            }
            for ($line = 0; $line < $max; $line++) {
                $parts = [];
                foreach ($row as $i => $_) {
                    $value = $wrapped[$i][$line] ?? '';
                    $parts[] = str_pad(substr($this->ascii($value), 0, $widths[$i]), $widths[$i]);
                }
                $this->text(implode(' | ', $parts), 8, false, true);
            }
            $this->space(3);
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
        $pdf .= "xref\n0 " . (count($this->objects) + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i <= count($this->objects); $i++) {
            $pdf .= str_pad((string) ($offsets[$i] ?? 0), 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $pdf .= "trailer\n<< /Size " . (count($this->objects) + 1) . " /Root {$catalogId} 0 R >>\nstartxref\n{$xref}\n%%EOF";
        file_put_contents($path, $pdf);
    }

    private function addPage(): void
    {
        if ($this->current !== '') {
            $this->finishPage();
        }
        $this->current = '';
        $this->y = 742;
        $this->text($this->title, 16, true);
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
        $text = strtr($text, ['“' => '"', '”' => '"', '’' => "'", '–' => '-', '—' => '-', '→' => '->', '₱' => 'PHP']);
        return preg_replace('/[^\x20-\x7E]/', '', $text);
    }
}

$pdf = new PresentationPdf('System Presentation Reviewer and Script');

$pdf->titlePage([
    'System name: GrocerEase Inventory Management System (GrocerEaseIMS)',
    'Group members and assigned modules:',
    'Member 1: Login and Dashboard',
    'Member 2: Category and Supplier Modules',
    'Member 3: Products, Stocks, and Inventory Logs',
    'Member 4: Archives, Backup and Recovery',
    'Purpose: Guide for understanding, reviewing, and presenting only the actual implemented modules of the system.',
]);

$pdf->heading('1. System Architecture Overview');
$pdf->paragraph('GrocerEaseIMS is a PHP and MySQL inventory management system. The system uses PHP views for the interface, controllers for processing form/AJAX requests, model classes for database logic, and MySQL as the database.');
$pdf->paragraph('General flow: Admin action -> PHP view or button -> form submit or fetch request -> controller -> model method -> MySQL query -> redirect or JSON response -> UI feedback.');
$pdf->table(
    ['Layer', 'Files', 'Purpose'],
    [
        ['UI Layer', 'views/inventory/*.php, assets/css/home.css, assets/js/pages.js', 'Shows pages, tables, forms, modals, filters, buttons, and SweetAlert prompts.'],
        ['Controller Layer', 'controllers/**/*.php, controllers/backup.php', 'Validates requests, calls models, redirects or returns JSON.'],
        ['Model Layer', 'models/product.php, category.php, supplier.php, Stocks.php, BackupManager.php', 'Contains reusable database operations.'],
        ['Database Layer', 'config/db.php, MySQL grocer_easedb', 'Stores users, products, categories, suppliers, stocks, movements, and logs.'],
    ],
    [14, 35, 42]
);

$pdf->heading('2. Member 1 Reviewer - Login and Dashboard');
$pdf->subheading('Login Module');
$pdf->paragraph('The Login Module authenticates the admin before allowing access to the inventory system. The controller checks if username and password are filled in, calls the User model to authenticate, then creates session values when login is valid.');
$pdf->codeBlock([
    'File: controllers/authentication.php',
    'if (empty($username)) $error["username"] = "Username is required";',
    'if (empty($password)) $error["password"] = "Password is required";',
    'if (!$id=$user->AuthenticateUser($username, $password)) {',
    '    $error["invalid"] = "Invalid username or password";',
    '}',
    '$_SESSION[\'logged_in\'] = true;',
    '$_SESSION[\'id\'] = $id;',
]);
$pdf->paragraph('How it works: if credentials are invalid, errors are saved in session and the user returns to index.php. If valid, the system stores logged_in and id in the session, then redirects to dashboard.php.');
$pdf->subheading('Dashboard Module');
$pdf->paragraph('The Dashboard summarizes the system status. It shows active products, categories, suppliers, total stocks, low stock items, out-of-stock items, backup status widgets, quick actions, and charts.');
$pdf->codeBlock([
    'File: views/inventory/dashboard.php',
    '$total_product = $product->GetTotalProducts();',
    '$total_categories = $category->GetTotalCategories();',
    '$total_suppliers = $supplier->GetTotalSupplier();',
    '$total_quantity = $stock->GetTotalStockQuantity();',
    '$low_stock = $stock->GetTotalLowStockItems();',
    '$out_of_stock = $stock->GetTotalOutOfStockItems();',
]);
$pdf->paragraph('The dashboard creates model objects, asks each model for totals, then displays those totals as cards. Chart data comes from stock and stock movement queries and is passed to Chart.js.');
$pdf->subheading('Member 1 Key Functionalities');
$pdf->bullet('Login validation and session creation.');
$pdf->bullet('Dashboard KPI cards for inventory status.');
$pdf->bullet('Charts for stock overview, stock status, and inventory trend.');
$pdf->bullet('Quick actions for common admin tasks.');
$pdf->subheading('Member 1 Script');
$pdf->paragraph('Good day. I will discuss the Login and Dashboard modules. The Login module protects the system by requiring valid admin credentials before accessing inventory pages. When the user submits the login form, the controller validates the username and password, calls the User model, and creates a session if the account is valid.');
$pdf->paragraph('After login, the user is redirected to the Dashboard. The Dashboard gives a quick overview of the system, including active products, total categories, total suppliers, stock quantity, low stock, out of stock, and backup status. These values come from the model classes and database queries.');
$pdf->paragraph('The dashboard is important because it helps the admin quickly understand the current inventory status and decide what action to take next.');
$pdf->qa([
    'Why is login important?' => 'It prevents unauthorized users from accessing inventory records.',
    'What does the dashboard show?' => 'It shows inventory totals, stock warnings, backup status, quick actions, and charts.',
    'How does the dashboard get data?' => 'It calls model methods that query the MySQL database.',
]);

$pdf->heading('3. Member 2 Reviewer - Category and Supplier Modules');
$pdf->subheading('Category Module');
$pdf->paragraph('The Category Module organizes products into groups. It supports adding, viewing, editing, soft deleting, restoring, and hard deleting categories. Category data is used by products and dashboard reports.');
$pdf->codeBlock([
    'File: models/category.php',
    'public function AddCategory($name, $description)',
    'public function CheckDuplicateCategory($category_name)',
    'public function GetPaginatedCategories($page = 1, $limit = 10)',
    'public function SoftDeleteCategory($id)',
    'public function RestoreCategory($id)',
]);
$pdf->paragraph('Important logic: before adding a category, the system checks duplicate category names where is_deleted = 0. Deleting a category is a soft delete, and products connected to that category are set to NULL to avoid broken category references.');
$pdf->codeBlock([
    'File: models/category.php',
    'UPDATE products SET category_id_fk = NULL WHERE category_id_fk = :id',
    'UPDATE categories SET is_deleted = 1, deleted_at = NOW() WHERE category_id_pk = :id',
]);
$pdf->subheading('Supplier Module');
$pdf->paragraph('The Supplier Module manages supplier records such as supplier name, contact person, phone number, email, address, and company name. Suppliers can be added, viewed, edited, soft deleted, restored, and hard deleted.');
$pdf->codeBlock([
    'File: models/supplier.php',
    'public function AddSupplier($name, $contact_person, $phone_number, $email, $address, $company_name)',
    'public function CheckDuplicateSupplier($supplier_name)',
    'public function EditSupplier($id, $name, $contact_person, $phone_number, $email, $address, $company_name)',
    'public function SoftDeleteSupplier($id)',
]);
$pdf->paragraph('Supplier soft delete also checks product_supplier relationships. If a supplier is connected to products, the relationship is updated to NULL before marking the supplier as deleted.');
$pdf->subheading('Member 2 Key Functionalities');
$pdf->bullet('Category add, edit, view, search/pagination, delete, restore.');
$pdf->bullet('Supplier add, edit, view, search/pagination, delete, restore.');
$pdf->bullet('Duplicate checking for categories and suppliers.');
$pdf->bullet('Soft delete protects records from immediate permanent removal.');
$pdf->subheading('Member 2 Script');
$pdf->paragraph('I will explain the Category and Supplier modules. The Category module is used to organize products into groups. The admin can add, view, edit, and delete categories. The system also checks if a category already exists to avoid duplicate records.');
$pdf->paragraph('When a category is deleted, the system does not immediately remove it permanently. It marks the category as deleted and updates related products so their category reference will not break.');
$pdf->paragraph('The Supplier module stores supplier information such as supplier name, contact person, phone number, email, address, and company name. It also supports add, edit, delete, restore, and duplicate checking.');
$pdf->paragraph('These modules are important because products depend on categories and suppliers for proper organization and purchasing information.');
$pdf->qa([
    'Why do we need categories?' => 'Categories organize products and make inventory easier to manage.',
    'What happens when a category is deleted?' => 'It is soft deleted and connected products are updated to avoid broken references.',
    'What information is stored for suppliers?' => 'Supplier name, contact person, phone, email, address, and company name.',
]);

$pdf->heading('4. Member 3 Reviewer - Products, Stocks, and Inventory');
$pdf->subheading('Product Module');
$pdf->paragraph('The Product Module manages product records. It includes add product, view list, edit product, soft delete, search, pagination, product status, category link, preferred supplier display, selling price, and description.');
$pdf->codeBlock([
    'File: controllers/products/add_product_process.php',
    'if (empty($product_name) || empty($category) || empty($selling_price) || $status === \'\')',
    '    $errors[\'form\'] = "Please fill in all required fields.";',
    'if ($product->CheckDuplicateProduct($product_name, $category))',
    '    $errors[\'product_name\'] = "Product already exists in the selected category.";',
    '$product_id = $product->AddProduct($product_name, $category, $selling_price, $description, $status);',
    '$stock->AddProductStock($product_id, 0, date(\'Y-m-d H:i:s\'));',
]);
$pdf->paragraph('Add Product validates the form, checks duplicate product within the selected category, inserts the product, then creates a stock row with quantity 0.');
$pdf->codeBlock([
    'File: models/product.php',
    'UPDATE products SET is_deleted = 1, deleted_at = NOW() WHERE product_id_pk = :id',
]);
$pdf->paragraph('Delete Product is a soft delete. The product remains in the database but is hidden from the active list.');
$pdf->subheading('Stocks Module');
$pdf->paragraph('The Stocks Module manages product quantities. Stock In adds quantity, Stock Out subtracts quantity, and the system prevents invalid or excessive stock-out quantities.');
$pdf->codeBlock([
    'File: models/Stock/Stocks.php',
    'public function StockIn($product_id, $quantity, $date)',
    'UPDATE stocks set quantity = quantity + :quantity,last_updated = :date WHERE product_id_fk = :id',
    '',
    'public function StockOut($product_id, $quantity, $date)',
    'UPDATE stocks set quantity = quantity - :quantity,last_updated = :date WHERE product_id_fk = :id AND quantity >= :quantity',
]);
$pdf->subheading('Inventory Logs');
$pdf->paragraph('Inventory logs are created from stock movements. The stock_movements table records quantity, reference type IN/OUT, reference ID, reason, date, and product ID. This gives traceability for stock changes.');
$pdf->codeBlock([
    'File: models/Stock/stockmovements.php',
    'INSERT INTO stock_movements (quantity,reference_type,reference_id,reason,date,product_id)',
    'VALUES(:quantity,:type,:ref_id,:reason,:date,:product_id)',
]);
$pdf->subheading('Member 3 Key Functionalities');
$pdf->bullet('Product add, edit, delete, search, pagination, and status display.');
$pdf->bullet('Stock In and Stock Out with quantity validation.');
$pdf->bullet('Inventory logs and trend reporting from stock_movements.');
$pdf->bullet('Transaction logs record stock changes.');
$pdf->subheading('Member 3 Script');
$pdf->paragraph('I will discuss Products, Stocks, and Inventory Logs. The Product Module allows the admin to add, view, edit, delete, and search products. When adding a product, the controller validates required fields and duplicate product names. After saving the product, the system also creates a stock record with zero quantity.');
$pdf->paragraph('For stock management, Stock In increases the product quantity, while Stock Out decreases it. The system validates the quantity and prevents Stock Out if the requested quantity is greater than the current stock.');
$pdf->paragraph('Every stock movement is recorded in inventory logs with reference type, reference ID, quantity, reason, date, and product. This makes the inventory changes traceable.');
$pdf->qa([
    'What happens after adding a product?' => 'The system inserts the product and creates an initial stock record with quantity 0.',
    'How does Stock Out prevent negative stock?' => 'It checks current quantity and the SQL update requires quantity >= requested quantity.',
    'Why are inventory logs important?' => 'They show the history and reason behind stock changes.',
]);

$pdf->heading('5. Member 4 Reviewer - Archives, Backup and Recovery');
$pdf->subheading('Archives Module');
$pdf->paragraph('The Archives Module displays deleted products, categories, and suppliers. It lets the admin restore archived records or permanently delete them when needed. The page uses tabs for products, categories, and suppliers.');
$pdf->codeBlock([
    'File: views/inventory/archived.php',
    '$tab = $_GET[\'tab\'] ?? \'products\';',
    'if ($tab == \'products\') {',
    '    $products = $product->GetDeletedProductsPaginated($page, $limit);',
    '} elseif ($tab == \'categories\') {',
    '    $category = $categories->GetDeletedCategoriesPaginated($page, $limit);',
    '} else {',
    '    $supplier = $suppliers->GetDeletedSuppliersPaginated($page, $limit);',
    '}',
]);
$pdf->paragraph('The archive page checks the selected tab, loads deleted records using the correct model, and includes the matching deleted record table.');
$pdf->bullet('Products archive uses Product::GetDeletedProductsPaginated and Product::RestoreProduct.');
$pdf->bullet('Categories archive uses Category::GetDeletedCategoriesPaginated and Category::RestoreCategory.');
$pdf->bullet('Suppliers archive uses Supplier::GetDeletedSuppliersPaginated and Supplier::RestoreSupplier.');
$pdf->bullet('Hard delete is available in separate controllers, but should be used carefully because it permanently removes data.');
$pdf->subheading('Backup and Recovery Module');
$pdf->paragraph('Backup and Recovery protects system data. The implemented user-facing features are Create Backup, Restore Backup, View Backup History, Download Backup, and Delete Backup. Backups are stored as SQL files in the local /backups folder.');
$pdf->bullet('Main UI: views/inventory/settings.php.');
$pdf->bullet('Controller: controllers/backup.php.');
$pdf->bullet('Model: models/BackupManager.php.');
$pdf->bullet('Access: controller requires logged-in session.');
$pdf->subheading('Create Backup Flow');
$pdf->numbered([
    'Admin opens Backup and Recovery.',
    'Admin clicks Backup now.',
    'Controller checks login and POST method.',
    'BackupManager reads database tables.',
    'System writes table structure and data into a SQL file.',
    'System validates the SQL file.',
    'UI shows success or error and refreshes history.',
]);
$pdf->codeBlock([
    'File: models/BackupManager.php',
    '$tables = $this->getTables();',
    'foreach ($tables as $table) {',
    '    $this->dumpTable($handle, $table);',
    '}',
    '$validation = $this->validateBackupFile($filepath);',
]);
$pdf->subheading('Restore Flow');
$pdf->numbered([
    'Admin uploads/selects a valid SQL backup file.',
    'System validates extension, readability, size, and SQL-like content.',
    'Admin confirms restore warning.',
    'System creates pre_restore safety backup before changing data.',
    'System executes SQL statements into the database.',
    'UI shows restore complete or restore failed.',
]);
$pdf->codeBlock([
    'File: models/BackupManager.php',
    '$validation = $this->validateBackupFile($filepath, $sourceName);',
    '$safetyBackup = $this->createBackup(\'pre_restore\');',
    '$statements = $this->splitSqlStatements($sql);',
    '$this->conn->exec(\'SET FOREIGN_KEY_CHECKS=0\');',
    'foreach ($statements as $statement) { $this->conn->exec(trim($statement)); }',
    '$this->conn->exec(\'SET FOREIGN_KEY_CHECKS=1\');',
]);
$pdf->paragraph('Important explanation: a full backup is a manual saved database state. A safety backup is created right before restore and contains the current state before applying the selected backup.');
$pdf->subheading('History, Download, and Delete');
$pdf->codeBlock([
    'File: controllers/backup.php',
    'case \'list\': respondJson([\'status\' => \'success\', \'data\' => $manager->getBackupList()]);',
    'case \'download\': $filepath = $manager->resolveBackupPath($filename); readfile($filepath);',
    'case \'delete\': respondJson($manager->deleteBackup($filename));',
]);
$pdf->paragraph('History scans /backups/*.sql and returns filename, date, type, size, and status. Download streams the file. Delete validates the filename path first, then removes the selected backup file.');
$pdf->subheading('Security and Common Errors');
$pdf->bullet('Only logged-in users can call backup controller actions.');
$pdf->bullet('Only .sql backup files are accepted for restore.');
$pdf->bullet('File paths are validated using basename, regex, realpath, and folder containment.');
$pdf->bullet('Delete requires confirmation and POST request.');
$pdf->bullet('Common error: Unexpected token < means the frontend expected JSON but received HTML, often login page, 404, or PHP error.');
$pdf->bullet('Common error: 404 means wrong controller path or missing controller file.');
$pdf->subheading('Member 4 Script');
$pdf->paragraph('I will discuss the Archives Module and Backup and Recovery Module. The Archives Module stores records that were soft deleted from Products, Categories, and Suppliers. Instead of immediately removing records, the system marks them as deleted, then displays them in the archive page.');
$pdf->paragraph('In the archive page, the admin can switch tabs between products, categories, and suppliers. The system loads the deleted records based on the selected tab. The admin can restore a record if it was deleted by mistake, or permanently delete it if it is no longer needed.');
$pdf->paragraph('Next is Backup and Recovery. This module protects the database. The admin can create a full SQL backup, restore a valid backup, view backup history, download backup files, and delete old backup files.');
$pdf->paragraph('When the admin clicks Backup now, the button sends a request to controllers/backup.php. The controller calls BackupManager, which reads all database tables and writes the table structure and data into an SQL file in the backups folder.');
$pdf->paragraph('For restore, the system first validates the selected SQL file. The admin must confirm the restore warning. Before restoring, the system creates a pre_restore safety backup. Then it executes the SQL statements into the database and shows whether the restore succeeded or failed.');
$pdf->paragraph('This module is important because it protects the system from accidental data loss and gives the admin a way to recover records after wrong changes.');
$pdf->qa([
    'What is the purpose of archives?' => 'To keep soft-deleted records available for restore or permanent deletion.',
    'What is the difference between full backup and safety backup?' => 'Full backup is manually created as a saved state. Safety backup is created right before restore to save the current state.',
    'Why is restore restricted?' => 'Restore can overwrite database data, so only authorized users should do it.',
    'Where are backups stored?' => 'In the local /backups folder as .sql files.',
]);

$pdf->heading('6. Code Reviewer Summary');
$pdf->table(
    ['Member', 'Modules', 'Key files', 'Main database action'],
    [
        ['1', 'Login, Dashboard', 'controllers/authentication.php, dashboard.php', 'Read users, read dashboard counts/charts.'],
        ['2', 'Category, Supplier', 'models/category.php, models/supplier.php', 'Insert, update, soft delete, restore categories and suppliers.'],
        ['3', 'Products, Stocks, Inventory', 'models/product.php, Stocks.php, stockmovements.php', 'Product CRUD, stock quantity updates, movement logging.'],
        ['4', 'Archives, Backup/Recovery', 'archived.php, BackupManager.php, backup.php', 'Load deleted records, export/import SQL, manage backup files.'],
    ],
    [8, 24, 36, 29]
);

$pdf->heading('7. Final Group Presentation Flow');
$pdf->numbered([
    'Member 1 opens: system introduction, login, authentication, and dashboard overview.',
    'Transition: "Next, Member 2 will explain how records are organized through categories and suppliers."',
    'Member 2 explains Category and Supplier modules, including add, edit, delete, restore, and duplicate checks.',
    'Transition: "Next, Member 3 will explain the main inventory operations: products, stocks, and logs."',
    'Member 3 explains Product Module, Stock In/Out, and Inventory Logs.',
    'Transition: "Finally, Member 4 will explain archives and how the system protects data through backup and recovery."',
    'Member 4 explains Archives and Backup and Recovery.',
    'Final closing: "Overall, GrocerEaseIMS helps administrators manage grocery inventory records, monitor stock, maintain records, and protect data through backup and recovery. Thank you."',
]);

$pdf->heading('8. Final Summary');
$pdf->paragraph('The presentation is now divided according to the actual system modules. Member 1 focuses on secure access and dashboard monitoring. Member 2 focuses on organizing records through categories and suppliers. Member 3 focuses on product and stock operations. Member 4 focuses on archives and database protection through backup and recovery.');
$pdf->paragraph('Each member should study the purpose, user action, controller/model logic, database action, and expected output of their assigned modules.');

$path = __DIR__ . '/System_Presentation_Reviewer_and_Script.pdf';
$pdf->output($path);
echo "Generated: {$path}\n";
