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
        $text = strtr($text, [
            '“' => '"', '”' => '"', '’' => "'", '–' => '-', '—' => '-',
            '→' => '->', '₱' => 'PHP',
        ]);
        return preg_replace('/[^\x20-\x7E]/', '', $text);
    }
}

$pdf = new PresentationPdf('System Presentation Reviewer and Script');

$pdf->titlePage([
    'System name: GrocerEase Inventory Management System (GrocerEaseIMS)',
    'Group members: Member 1, Member 2, Member 3, Member 4',
    'Purpose: This PDF serves as a reviewer, code guide, and speaking script for understanding and presenting the system during capstone defense.',
    'Important note: This reviewer focuses only on the implemented inventory management features visible in the system code.',
]);

$pdf->heading('1. System Architecture Overview');
$pdf->paragraph('GrocerEaseIMS uses a simple PHP and MySQL architecture. The user interacts with PHP views such as dashboard.php, products.php, stock.php, inventory.php, and settings.php. The views display tables, forms, modals, charts, and buttons. When the user submits a form or clicks an AJAX button, the request goes to a PHP controller. The controller validates the request, calls a model class, and the model performs the database query using PDO.');
$pdf->paragraph('Simple data movement: User/Admin action -> UI page/button/form -> JavaScript/AJAX or HTML form request -> PHP controller -> Model method -> MySQL database -> JSON/HTML redirect response -> UI update or SweetAlert message.');
$pdf->table(
    ['Layer', 'Main files', 'Purpose'],
    [
        ['Frontend/UI', 'views/inventory/*.php, assets/css/home.css, assets/js/pages.js', 'Displays pages, tables, forms, modals, buttons, charts, and alerts.'],
        ['Controller', 'controllers/products/*.php, controllers/stock/*.php, controllers/backup.php', 'Receives requests, validates data, calls models, returns redirect or JSON response.'],
        ['Model', 'models/product.php, models/Stock/Stocks.php, models/BackupManager.php', 'Contains database logic and reusable business operations.'],
        ['Database', 'config/db.php, MySQL grocer_easedb', 'Stores products, categories, suppliers, stocks, stock movements, users, logs, and other system records.'],
    ],
    [16, 36, 40]
);

$pdf->heading('2. Member 1 - Reviewer');
$pdf->subheading('System Introduction');
$pdf->paragraph('GrocerEaseIMS is an inventory management system for grocery product control. It helps administrators manage products, categories, suppliers, stock quantities, stock movement logs, dashboard reports, archived records, and backup/recovery.');
$pdf->subheading('Problem Statement');
$pdf->paragraph('Manual inventory tracking can lead to inaccurate product records, delayed stock updates, missing supplier information, and difficulty monitoring stock status. The system was created to organize these records in one place and reduce manual mistakes.');
$pdf->subheading('Objectives');
$pdf->bullet('Maintain accurate product, category, supplier, stock, and movement records.');
$pdf->bullet('Allow administrators to add, update, archive, restore, and permanently remove records where implemented.');
$pdf->bullet('Provide dashboard visibility for products, categories, suppliers, low stock, out of stock, and reports.');
$pdf->bullet('Protect database records through backup and recovery.');
$pdf->subheading('Users, Scope, and Limitations');
$pdf->bullet('Primary user: authenticated administrator or inventory staff.');
$pdf->bullet('Scope: inventory management, admin dashboard, product/category/supplier management, stock control, logs, archived records, and backup/recovery.');
$pdf->bullet('Limitation: the current system focuses on internal inventory operations for authorized users.');
$pdf->subheading('Key Points to Memorize');
$pdf->bullet('The system is built with PHP, MySQL, PDO, JavaScript, CSS, and SweetAlert.');
$pdf->bullet('The main value is organized inventory data and safer database recovery.');
$pdf->bullet('Most modules follow View -> Controller -> Model -> Database.');
$pdf->subheading('Member 1 Q&A');
$pdf->qa([
    'What is the purpose of GrocerEaseIMS?' => 'To help manage grocery inventory records, stocks, suppliers, reports, and backup/recovery in one system.',
    'Who are the users?' => 'The users are administrators or authorized inventory staff.',
    'What is the main limitation?' => 'It is focused on internal inventory operations and authorized system management.',
]);

$pdf->heading('3. Member 1 - Presentation Script');
$pdf->paragraph('Good day everyone. We are presenting GrocerEase Inventory Management System, or GrocerEaseIMS. This system was created to help organize grocery inventory records such as products, categories, suppliers, stock levels, and inventory movement logs.');
$pdf->paragraph('The problem we want to solve is the difficulty of manually tracking grocery inventory. Manual records can become inaccurate, delayed, or hard to monitor, especially when products, suppliers, and stock changes increase.');
$pdf->paragraph('Our objectives are to provide organized inventory management, support admin CRUD operations, monitor stock levels through the dashboard, record inventory movements, and protect the database using backup and recovery. The target users are administrators or authorized inventory staff.');
$pdf->paragraph('I will now pass the discussion to Member 2, who will explain the user flow and operational transaction flow of the system.');

$pdf->heading('4. Member 2 - Reviewer');
$pdf->subheading('User/Operational Side Features');
$pdf->paragraph('For this system, Member 2 should explain the authenticated user flow and operational transaction flow, especially stock-in and stock-out because these are the transaction operations implemented in the code.');
$pdf->bullet('Login/authentication protects inventory pages through includes/auth_check.php.');
$pdf->bullet('Stock Control lets authorized users update product quantity through Stock In and Stock Out.');
$pdf->bullet('Inventory Logs show stock movement records with reference IDs, quantity, reason, date, and product name.');
$pdf->bullet('Validation prevents empty or invalid quantities. Stock Out also prevents subtracting more than current stock.');
$pdf->subheading('Important Files and Functions');
$pdf->table(
    ['File', 'Important code', 'Purpose'],
    [
        ['controllers/stock/stock_in_process.php', 'Stocks::StockIn, StockMovements::AddStockMovements', 'Adds quantity and records an IN movement.'],
        ['controllers/stock/stock_out_process.php', 'Stocks::StockOut, GetQuantityByProductId', 'Subtracts quantity only if enough stock exists.'],
        ['models/Stock/Stocks.php', 'StockIn, StockOut, GetTotalLowStockItems', 'Database operations for stock data.'],
        ['models/Stock/stockmovements.php', 'AddStockMovements, GetInventoryLogsTrend', 'Records and reports stock movement data.'],
    ],
    [30, 31, 34]
);
$pdf->subheading('Sample Code Snippet');
$pdf->codeBlock([
    'File: controllers/stock/stock_out_process.php',
    'elseif ($quantity > $currentStock[\'quantity\'])',
    '    $error[\'quantity\'] = \'Insufficient stock. Current stock: \' . $currentStock[\'quantity\'];',
    '',
    '$result = $stock->StockOut($product_id, $quantity, $date);',
    '$movement_result = $stock_movement->AddStockMovements($quantity, "OUT", $reference_id, $reason, $date_movement, $product_id);',
]);
$pdf->paragraph('This code first checks if the requested stock-out quantity is greater than available stock. If it is too high, the process stops and shows an error. If valid, the system subtracts the quantity and records the movement as OUT.');
$pdf->subheading('Member 2 Q&A');
$pdf->qa([
    'What transaction flow is implemented?' => 'The implemented transaction flow is stock movement, specifically Stock In and Stock Out.',
    'How does Stock Out prevent negative stock?' => 'The controller checks current stock and the SQL update also requires quantity >= requested quantity.',
    'What is recorded after a stock transaction?' => 'The system records a stock movement with quantity, reference type, reference ID, reason, date, and product ID.',
]);

$pdf->heading('5. Member 2 - Presentation Script');
$pdf->paragraph('For my part, I will explain the operational user flow. The main transaction flow is inventory movement, specifically stock in and stock out.');
$pdf->paragraph('When an authorized user performs Stock In, the system validates the quantity, adds it to the current stock, and records the movement with a reference ID. When the user performs Stock Out, the system checks if the requested quantity is valid and if there is enough stock. This prevents negative inventory.');
$pdf->paragraph('Behind the system, the controller receives the form request, calls the Stocks model, updates the database, then records the movement in the stock_movements table. This helps the admin trace why the stock changed. I will now pass the presentation to Member 3 for the admin dashboard and management side.');

$pdf->heading('6. Member 3 - Reviewer');
$pdf->subheading('Admin Dashboard and Management');
$pdf->paragraph('The admin side is the main control center of GrocerEaseIMS. The dashboard shows key performance indicators and charts. Admin modules manage products, categories, suppliers, stock, inventory logs, archives, and backup/recovery.');
$pdf->bullet('Dashboard KPIs: active products, categories, suppliers, total stocks, low stock, out of stock.');
$pdf->bullet('Reports/charts: stock per category, stock status, and inventory trend from stock_movements.');
$pdf->bullet('CRUD modules: products, categories, suppliers, product suppliers, stock control.');
$pdf->bullet('Maintenance: archived records and backup/recovery.');
$pdf->subheading('Important Code Snippets');
$pdf->codeBlock([
    'File: views/inventory/dashboard.php',
    '$total_product = $product->GetTotalProducts();',
    '$total_categories = $category->GetTotalCategories();',
    '$total_suppliers = $supplier->GetTotalSupplier();',
    '$total_quantity = $stock->GetTotalStockQuantity();',
    '$low_stock = $stock->GetTotalLowStockItems();',
    '$out_of_stock = $stock->GetTotalOutOfStockItems();',
]);
$pdf->paragraph('The dashboard creates model objects and asks each model for totals. The returned numbers are displayed as dashboard cards. Chart data comes from model queries and is passed into JavaScript Chart.js using json_encode.');
$pdf->subheading('Admin Database Tables');
$pdf->bullet('products: stores product information and delete/status flags.');
$pdf->bullet('categories: stores product category information.');
$pdf->bullet('suppliers: stores supplier information.');
$pdf->bullet('stocks: stores current stock quantity per product.');
$pdf->bullet('stock_movements: records stock in/out history and chart data.');
$pdf->bullet('transaction_logs: logs quantity changes for stock operations.');
$pdf->subheading('Member 3 Q&A');
$pdf->qa([
    'Why is the dashboard important?' => 'It gives quick visibility of product count, supplier count, stock levels, and stock problems.',
    'What does CRUD mean?' => 'Create, Read, Update, and Delete records.',
    'How do reports help management?' => 'They help the admin see stock status and inventory trends for decision-making.',
]);

$pdf->heading('7. Member 3 - Presentation Script');
$pdf->paragraph('For the admin side, the dashboard acts as the control center of the system. It shows important numbers such as active products, total categories, total suppliers, total stocks, low stock items, and out of stock items.');
$pdf->paragraph('The dashboard also has charts for stock overview, stock status, and inventory trends. These charts help the admin understand what is happening in the inventory without manually checking every record.');
$pdf->paragraph('Admin management features include adding, editing, deleting, and restoring records depending on the module. The system uses models to run database queries and controllers to process admin actions. These features help maintain accurate inventory data. I will now pass the discussion to Member 4 for the Product Module and Backup and Recovery Module.');

$pdf->heading('8. Member 4 Reviewer - Product Module');
$pdf->subheading('Purpose and Importance');
$pdf->paragraph('The Product Module manages grocery products. It is important because products are the center of the inventory system. Stock records, supplier relationships, categories, and reports all depend on accurate product data.');
$pdf->bullet('Access: authenticated admin/inventory staff only.');
$pdf->bullet('Main page: views/inventory/products.php.');
$pdf->bullet('Main model: models/product.php.');
$pdf->bullet('Main controllers: add_product_process.php, edit_process.php, delete_product.php, recover.php, hard_delete_product.php.');
$pdf->subheading('Implemented Product Features');
$pdf->bullet('Add product: creates a product and also creates an initial stock row with quantity 0.');
$pdf->bullet('View product list: products.php displays active products with category, price, description, supplier, and status.');
$pdf->bullet('Edit product: updates product name, category, selling price, description, and status.');
$pdf->bullet('Delete product: soft deletes by setting is_deleted = 1 and deleted_at = NOW().');
$pdf->bullet('Search product: searches product names by prefix using LIKE search%.');
$pdf->bullet('Pagination: products are displayed with page and limit.');
$pdf->bullet('Status: active/inactive status controls whether manage supplier is enabled.');
$pdf->subheading('Product Table Fields Visible in Code');
$pdf->bullet('product_id_pk: primary key.');
$pdf->bullet('product_name: product name.');
$pdf->bullet('category_id_fk: linked category.');
$pdf->bullet('selling_price: selling price.');
$pdf->bullet('product_description: product description.');
$pdf->bullet('status: active/inactive flag.');
$pdf->bullet('is_deleted and deleted_at: archive/soft-delete tracking.');
$pdf->subheading('Add Product Code and Explanation');
$pdf->codeBlock([
    'File: controllers/products/add_product_process.php',
    '$product_name = ucfirst(trim($_POST[\'product_name\'] ?? \'\'));',
    'if (empty($product_name) || empty($category) || empty($selling_price) || $status === \'\')',
    '    $errors[\'form\'] = "Please fill in all required fields.";',
    'if ($product->CheckDuplicateProduct($product_name, $category))',
    '    $errors[\'product_name\'] = "Product already exists in the selected category.";',
    '$product_id = $product->AddProduct($product_name, $category, $selling_price, $description, $status);',
    '$stock->AddProductStock($product_id, 0, date(\'Y-m-d H:i:s\'));',
]);
$pdf->paragraph('The controller reads POST data, validates required fields, checks duplicates, sanitizes input, inserts the product, and then creates a stock record with quantity 0. This means every product automatically has a matching row in stocks.');
$pdf->codeBlock([
    'File: models/product.php',
    'public function AddProduct($name, $category, $selling_price, $description, $status)',
    '{',
    '  $stmt = $this->conn->prepare("INSERT INTO products (product_name,category_id_fk,selling_price,product_description,status) VALUES(:name,:category,:selling_price,:description,:status)");',
    '  $stmt->execute([...]);',
    '  return $this->conn->lastInsertId();',
    '}',
]);
$pdf->paragraph('The model uses a prepared statement, which is safer than directly inserting values into SQL. After insert, it returns the new product ID so the stock table can link to it.');
$pdf->subheading('Edit Product Logic');
$pdf->codeBlock([
    'File: controllers/products/edit_process.php',
    '$original = $product->GetProductInfoById($product_id);',
    'if ($product_name != $original[\'product_name\'] || $category != $original[\'category_id_fk\'])',
    '    if ($product->CheckDuplicateProduct($product_name, $category))',
    '        $errors[\'product_name\'] = "Product already exists in the selected category.";',
    '$result = $product->UpdateProductInfo($product_id, $product_name, $category, $selling_price, $description, $status);',
]);
$pdf->paragraph('The edit process compares the submitted data with the original record. If the name or category changed, it checks for duplicates. Then the model updates the product fields.');
$pdf->subheading('Delete Product Logic');
$pdf->codeBlock([
    'File: models/product.php',
    'public function SoftDeleteProduct($id)',
    '{',
    '  $stmt = $this->conn->prepare("UPDATE products SET is_deleted = 1,deleted_at = NOW() WHERE product_id_pk = :id");',
    '  $stmt->execute([\':id\' => $id]);',
    '  return $stmt->rowCount() > 0;',
    '}',
]);
$pdf->paragraph('Delete is a soft delete. The record remains in the database but is hidden from the active product list. This supports archive and restore behavior.');
$pdf->subheading('Display and Search Logic');
$pdf->codeBlock([
    'File: views/inventory/products.php',
    'if (!empty($search)) {',
    '    $products = $product->SearchProduct($search);',
    '} else {',
    '    $products = $product->GetPaginatedProducts($page, $limit);',
    '}',
]);
$pdf->paragraph('The view decides whether to show search results or paginated product results. The product model joins products with categories, preferred supplier data, and supplier name.');
$pdf->subheading('Validation, Error Handling, and Concerns');
$pdf->bullet('Product name, category, selling price, and status are required.');
$pdf->bullet('Product name must not exceed 100 characters.');
$pdf->bullet('Selling price must be numeric and positive.');
$pdf->bullet('Duplicate product names are blocked within the same category.');
$pdf->bullet('Errors are stored in session and displayed after redirect.');
$pdf->bullet('Possible improvement: use stricter numeric input type and CSRF tokens.');
$pdf->bullet('No product image upload is implemented in the visible product code.');

$pdf->heading('9. Member 4 Reviewer - Backup and Recovery Module');
$pdf->subheading('Purpose and Importance');
$pdf->paragraph('Backup and Recovery protects the database. It allows the admin to create a full SQL backup, restore a valid backup, view backup history, download backup files, and delete backup files. Restore must be restricted to admin because it can overwrite current database data.');
$pdf->bullet('Main UI: views/inventory/settings.php.');
$pdf->bullet('Main controller: controllers/backup.php.');
$pdf->bullet('Main model: models/BackupManager.php.');
$pdf->bullet('Storage: local /backups folder.');
$pdf->bullet('Automatic Backup note: cron_backup.php exists as backend utility, but scheduled backup management is not presented as a user-facing module in the Backup and Recovery UI.');
$pdf->subheading('Backup Flow');
$pdf->numbered([
    'Admin opens Backup and Recovery module.',
    'Admin clicks Backup now.',
    'System checks login/session and request method.',
    'BackupManager reads all MySQL base tables.',
    'System exports table structure and data into a SQL file.',
    'System saves the file in /backups.',
    'System validates the backup file and returns success or failure JSON.',
    'The UI shows SweetAlert and refreshes backup history.',
]);
$pdf->subheading('Restore Flow');
$pdf->numbered([
    'Admin uploads or selects a SQL backup file.',
    'System validates file extension, readability, size, and SQL-like content.',
    'Admin confirms restore warning.',
    'System creates a pre_restore safety backup before changing data.',
    'System disables foreign key checks, executes SQL statements, and enables checks again.',
    'System returns success or failure with safety backup information.',
]);
$pdf->subheading('Backup History Flow');
$pdf->numbered([
    'UI calls controllers/backup.php?action=list.',
    'BackupManager scans /backups/*.sql files.',
    'System returns filename, date, type, size, readable size, and valid status.',
    'Admin can download, restore, or delete selected backup.',
]);
$pdf->subheading('Decision Points');
$pdf->bullet('Is database connection valid? The DB connection is created in autoload.php through config/db.php.');
$pdf->bullet('Was backup created successfully? The SQL file must be written and validated.');
$pdf->bullet('Does admin confirm restore? SweetAlert confirmation is required before restore.');
$pdf->bullet('Is selected backup valid? validateBackupFile checks extension, readability, size, and content.');
$pdf->bullet('Was restore completed successfully? SQL statements must execute without exception.');
$pdf->bullet('Admin chooses backup history action: download, restore, or delete.');
$pdf->subheading('Backup Creation Code');
$pdf->codeBlock([
    'File: models/BackupManager.php',
    '$tables = $this->getTables();',
    '$this->writeLine($handle, \'SET FOREIGN_KEY_CHECKS=0;\');',
    'foreach ($tables as $table) {',
    '    $this->dumpTable($handle, $table);',
    '}',
    '$this->writeLine($handle, \'SET FOREIGN_KEY_CHECKS=1;\');',
    '$validation = $this->validateBackupFile($filepath);',
]);
$pdf->paragraph('The backup process gets all database tables, writes SQL settings, dumps every table structure and data, re-enables foreign key checks, then validates the generated SQL file.');
$pdf->subheading('Restore Code');
$pdf->codeBlock([
    'File: models/BackupManager.php',
    '$validation = $this->validateBackupFile($filepath, $sourceName);',
    '$safetyBackup = $this->createBackup(\'pre_restore\');',
    '$statements = $this->splitSqlStatements($sql);',
    '$this->conn->exec(\'SET FOREIGN_KEY_CHECKS=0\');',
    'foreach ($statements as $statement) { $this->conn->exec(trim($statement)); }',
    '$this->conn->exec(\'SET FOREIGN_KEY_CHECKS=1\');',
]);
$pdf->paragraph('Before restore, the system validates the selected file and creates a safety backup. The safety backup is a copy of the current state before restore, not necessarily the original good state. This is useful if the restore file was wrong.');
$pdf->subheading('Controller Actions');
$pdf->table(
    ['Action', 'Method', 'Result'],
    [
        ['backup', 'POST', 'Creates full SQL backup and returns JSON.'],
        ['restore', 'POST', 'Restores uploaded SQL file.'],
        ['restore_specific', 'POST', 'Restores selected history file.'],
        ['validate', 'POST', 'Validates uploaded or selected SQL file.'],
        ['list', 'GET', 'Returns backup history JSON.'],
        ['download', 'GET', 'Streams SQL file to browser.'],
        ['delete', 'POST', 'Deletes selected backup file after path validation.'],
    ],
    [22, 12, 58]
);
$pdf->subheading('Security Review');
$pdf->bullet('Controller requires logged-in session.');
$pdf->bullet('Request methods are restricted for important actions.');
$pdf->bullet('Only .sql files are accepted for restore.');
$pdf->bullet('resolveBackupPath validates filenames with basename, regex, realpath, and folder containment.');
$pdf->bullet('Delete requires confirmation in the UI and POST in the controller.');
$pdf->bullet('Restore shows warning because it can overwrite database data.');
$pdf->bullet('Recommended improvement: protect /backups from direct public access and add CSRF tokens.');
$pdf->subheading('Common Errors and Fixes');
$pdf->table(
    ['Error', 'Cause', 'Fix'],
    [
        ['404 Not Found', 'Wrong controller path or missing file.', 'Check relative path ../../controllers/backup.php.'],
        ['Unexpected token <', 'HTML returned instead of JSON, usually login page, 404, or PHP error.', 'Check session, controller path, and PHP warnings.'],
        ['History not loading', 'action=list failed or JSON invalid.', 'Check controller response and browser console.'],
        ['Restore button disabled', 'No valid SQL file selected or validation failed.', 'Use valid .sql backup generated by the system.'],
        ['Invalid SQL file', 'File is empty, unreadable, wrong extension, or not SQL-like.', 'Choose a readable .sql backup file.'],
    ],
    [21, 37, 38]
);

$pdf->heading('10. Member 4 - Presentation Script');
$pdf->subheading('Part 1: Product Module Script');
$pdf->paragraph('First, I will explain the Product Module. This module is important because products are the main records of the inventory system. The admin can add, view, edit, delete, and search products.');
$pdf->paragraph('In this part, the admin can click Add Product and enter the product name, category, selling price, description, and status. Behind the system, the form sends a POST request to controllers/products/add_product_process.php. The controller validates required fields, checks if the product already exists in the selected category, and checks if the selling price is numeric and positive.');
$pdf->paragraph('After validation, the controller calls the Product model. The model inserts the product into the products table using a prepared SQL statement. After that, the system also creates a stock record with zero quantity, so every new product is ready for stock control.');
$pdf->paragraph('For editing, the controller gets the original product data and compares it with the submitted data. If the product name or category changes, it checks for duplicates again. Then it updates the products table.');
$pdf->paragraph('For delete, the system uses soft delete. It does not immediately remove the product from the database. Instead, it sets is_deleted to 1 and records deleted_at. This is important because the system can still manage archived records and avoid accidental permanent loss.');
$pdf->paragraph('This module is important because accurate product records affect stock management, suppliers, reports, and backup demonstration.');
$pdf->subheading('Part 2: Backup and Recovery Script');
$pdf->paragraph('Next, I will explain the Backup and Recovery Module. This module protects the system database. The admin can create a backup, restore a valid backup, view backup history, download backup files, and delete old backup files.');
$pdf->paragraph('When the admin clicks Backup now, the button sends a POST request to controllers/backup.php?action=backup. The controller checks if the user is logged in and then calls BackupManager. BackupManager reads all database tables, writes table structures and data into a SQL file, saves it in the backups folder, validates the file, and returns a JSON response.');
$pdf->paragraph('For restore, the admin must upload or select a valid SQL backup file. The system validates the file first. Then the admin confirms the restore warning. Behind the system, BackupManager creates a pre_restore safety backup before applying the selected backup. This is important because restore can overwrite current database data.');
$pdf->paragraph('Backup History loads using controllers/backup.php?action=list. The system scans the backups folder and shows the filename, date, type, size, and status. The admin can download, restore, or delete each backup. Download streams the file, restore validates and applies it, and delete removes the selected backup after confirmation.');
$pdf->paragraph('Automatic Backup is not presented as part of the user-facing module because the Backup and Recovery page focuses on manual backup and restore actions. The important implemented features for this presentation are Create Backup, Restore Backup, View Backup History, Download Backup, and Delete Backup.');
$pdf->paragraph('This module helps protect the system because if product, stock, or supplier records are accidentally changed, a valid backup can restore the database to a previous state. That completes my part. Thank you.');

$pdf->heading('11. Code Reviewer Section');
$pdf->table(
    ['Module', 'Snippet purpose', 'Related table', 'Issue / improvement'],
    [
        ['Product Add', 'AddProduct inserts product and creates stock row with quantity 0.', 'products, stocks', 'Add CSRF token and stricter numeric input.'],
        ['Product Update', 'UpdateProductInfo edits product fields after validation.', 'products', 'Variable typo $error/$errors exists but PHP still uses $errors dynamically. Clean naming.'],
        ['Product Delete', 'SoftDeleteProduct sets is_deleted = 1.', 'products', 'Good for archive; permanent delete must be controlled.'],
        ['Product Search', 'SearchProduct uses product_name LIKE search%.', 'products', 'Search is prefix-based only. Could support wider search.'],
        ['Backup Create', 'createBackup writes table SQL to .sql file.', 'all base tables', 'Protect /backups folder from public access.'],
        ['Restore', 'restoreBackup validates file, creates safety backup, executes SQL.', 'all restored tables', 'Restore is risky; admin and CSRF protection recommended.'],
        ['History List', 'getBackupList scans local .sql files.', 'file system', 'No persistent audit table.'],
        ['Download/Delete', 'resolveBackupPath protects file path.', 'file system', 'Good path validation; still protect directory at server level.'],
    ],
    [18, 37, 18, 31]
);

$pdf->heading('12. System Flow Reviewer');
$pdf->table(
    ['Flow', 'User/Admin action', 'System process', 'Database action', 'Output'],
    [
        ['Login', 'User enters credentials.', 'Authentication checks account.', 'Reads users table.', 'Dashboard if valid.'],
        ['Admin Dashboard', 'Admin opens dashboard.', 'Models collect counts and chart data.', 'Reads products, categories, suppliers, stocks, movements.', 'Cards and charts display.'],
        ['Product Add', 'Admin submits add form.', 'Controller validates and calls Product model.', 'INSERT products; INSERT stocks.', 'Redirect with success/error.'],
        ['Product Delete', 'Admin confirms delete.', 'Controller calls SoftDeleteProduct.', 'UPDATE products SET is_deleted=1.', 'Product hidden from active list.'],
        ['Backup', 'Admin clicks Backup now.', 'BackupManager exports tables.', 'Reads all base tables.', 'SQL backup file saved.'],
        ['Restore', 'Admin selects SQL and confirms.', 'Validate, safety backup, execute SQL.', 'Tables are restored from SQL.', 'Success/error alert.'],
        ['History', 'Admin opens backup history.', 'Controller lists files.', 'No DB table; file scan only.', 'Table of backups.'],
    ],
    [15, 22, 26, 25, 22]
);

$pdf->heading('13. Possible Panel Questions and Answers');
$pdf->subheading('Member 1 Q&A');
$pdf->qa([
    'What problem does the system solve?' => 'It organizes inventory records and reduces manual tracking errors.',
    'What is included in the scope?' => 'Admin inventory management, dashboard, product/category/supplier records, stock control, logs, archives, and backup/recovery.',
    'What is the scope of Member 1?' => 'System purpose, users, objectives, scope, limitations, and the overall inventory workflow.',
]);
$pdf->subheading('Member 2 Q&A');
$pdf->qa([
    'What is the transaction flow?' => 'The implemented transaction flow is Stock In and Stock Out.',
    'How is validation handled?' => 'The controllers check required fields, positive quantity, and sufficient stock before updating records.',
]);
$pdf->subheading('Member 3 Q&A');
$pdf->qa([
    'How does the dashboard help?' => 'It summarizes important inventory information and visualizes stock status and trends.',
    'What are maintenance features?' => 'Archived records and backup/recovery help maintain and protect system data.',
]);
$pdf->subheading('Member 4 Q&A - Product');
$pdf->qa([
    'What is the purpose of the Product Module?' => 'To manage grocery product records used by stock, suppliers, and reports.',
    'How do you add a product?' => 'The admin submits the add form, the controller validates it, then the Product model inserts the record.',
    'What happens when a product is deleted?' => 'It is soft deleted by setting is_deleted to 1, so it is hidden but not immediately removed.',
    'What validations are used?' => 'Required fields, duplicate check, product name length, numeric and positive selling price.',
    'What table stores product data?' => 'The products table stores product information.',
]);
$pdf->subheading('Member 4 Q&A - Backup and Recovery');
$pdf->qa([
    'What is the purpose of backup and recovery?' => 'To protect system data by saving and restoring database state.',
    'Where are backup files stored?' => 'They are stored as .sql files in the local /backups folder.',
    'What happens during restore?' => 'The file is validated, a safety backup is created, and SQL statements are executed into the database.',
    'Why is restore risky?' => 'It can overwrite current database records.',
    'How are backup files validated?' => 'The system checks .sql extension, readability, non-empty size, and SQL-like content.',
    'Why is Automatic Backup not included in the presentation module?' => 'Because the user-facing Backup and Recovery page focuses on manual backup and restore actions. The presented implemented features are create, restore, history, download, and delete.',
    'What if backup fails?' => 'The controller returns error JSON and the UI shows a failure alert.',
    'What if restore fails?' => 'The system returns an error message and includes the safety backup filename if one was created.',
    'How do you protect backup files?' => 'Use login protection, validated paths, and recommended server rules to prevent public access to /backups.',
]);

$pdf->heading('14. Final Group Presentation Flow');
$pdf->numbered([
    'Member 1 opens with greeting, system introduction, problem statement, objectives, users, scope, and overall modules.',
    'Transition: "I will now pass the discussion to Member 2 for the user and transaction flow."',
    'Member 2 explains authenticated user flow, stock in/out transaction flow, validation, and movement logs.',
    'Transition: "Next, Member 3 will explain the admin dashboard and management features."',
    'Member 3 explains dashboard, reports, CRUD management, records, and maintenance.',
    'Transition: "Now Member 4 will discuss the Product Module and Backup and Recovery."',
    'Member 4 explains Product Module in detail, then Backup and Recovery in detail.',
    'Final closing: "Overall, GrocerEaseIMS helps manage grocery inventory records, monitor stock, and protect data through backup and recovery. Thank you."',
]);

$pdf->heading('15. Final Summary');
$pdf->paragraph('GrocerEaseIMS is a PHP and MySQL inventory management system. The modules work together by managing product records, linking categories and suppliers, updating stock quantities, recording inventory movements, showing dashboard reports, and protecting records through backup and recovery.');
$pdf->paragraph('Member 1 should focus on system purpose, objectives, users, and scope. Member 2 should focus on authenticated user flow and stock transaction flow. Member 3 should focus on dashboard, reports, CRUD, and maintenance. Member 4 should focus strongly on Product Module and Backup and Recovery because these parts include important code logic, database operations, validation, and system protection.');

$path = __DIR__ . '/System_Presentation_Reviewer_and_Script.pdf';
$pdf->output($path);
echo "Generated: {$path}\n";
