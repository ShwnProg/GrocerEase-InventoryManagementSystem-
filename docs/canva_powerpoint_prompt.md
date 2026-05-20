# Canva PowerPoint Prompt

Create a professional IT214 Database Administration PowerPoint presentation for **GrocerEase Inventory Management System: Database Backup and Recovery Module**.

Use a clean green, white, and charcoal theme inspired by a grocery inventory system. Use modern flat icons for database, shield, restore, inventory, products, suppliers, and logs. Keep slides readable for classroom defense, with screenshots placeholders where I can insert actual system screenshots from the app.

Slides:

1. **Title Slide**
   - GrocerEase Inventory Management System
   - Database Backup and Recovery Module
   - IT214 Database Administration
   - Presenter names and date

2. **Introduction to the Backup and Recovery Module**
   - Purpose: protect inventory data from deletion, corruption, human error, and system failure
   - Importance: preserves business records, stock counts, suppliers, products, and logs
   - Problems prevented: accidental deletion, data loss, wrong updates, hardware failure, corrupted records

3. **System Overview**
   - Show the complete system flowchart
   - Modules: Login, Dashboard, Products, Categories, Suppliers, Stock Control, Inventory Logs, Archived Records, Backup and Recovery
   - Mention MySQL database as the central storage

4. **Backup Strategy**
   - Type implemented: Full SQL database backup
   - Backup location: local `/backups` folder
   - Backup contents: table structures, product data, category data, supplier data, product-supplier links, stock data, movement logs, users
   - Safety benefit: one file can restore the system state

5. **Backup Module Demonstration**
   - Add screenshot placeholder: Settings Backup and Recovery screen
   - Add screenshot placeholder: Dashboard quick actions
   - Step-by-step:
     1. Admin clicks Backup Now
     2. SweetAlert asks for confirmation
     3. System exports database tables
     4. System validates generated `.sql`
     5. Backup appears in history

6. **Backup Process Flowchart**
   - Insert backup flowchart:
     Admin clicks Backup Now -> Confirmation -> Controller -> BackupManager -> Export table structure -> Export data -> Validate SQL -> Save backup -> Refresh history

7. **Recovery / Restore Module Demonstration**
   - Add screenshot placeholder: Restore selected file
   - Add screenshot placeholder: Backup history restore button
   - Step-by-step:
     1. Admin selects backup file
     2. System validates `.sql`
     3. Admin confirms restore
     4. System creates pre-restore safety backup
     5. System executes SQL restore
     6. System confirms success

8. **Recovery Process Flowchart**
   - Insert recovery flowchart:
     Select backup -> Validate file -> Confirm restore -> Create safety backup -> Disable foreign keys -> Execute SQL -> Enable foreign keys -> Show result

9. **Live Demonstration of Effectiveness**
   - Show existing records
   - Create backup
   - Delete or modify important records
   - Show changed/missing records
   - Restore backup
   - Show original data returned

10. **Validation and Error Handling**
    - Login required before controller actions
    - Only `.sql` files accepted
    - File must be readable, non-empty, and SQL-like
    - Path traversal blocked by safe filename handling
    - Safety backup created before restore
    - SweetAlert success and error feedback

11. **Reliability and System Functionality**
    - Uses one shared BackupManager for manual backup, restore, and automatic backup
    - Does not depend on external `mysqldump` command-line tools
    - Backup history includes file name, date, type, size, validity, download, and restore
    - Dashboard quick actions support fast defense demonstration

12. **Conclusion**
    - Backup and recovery module protects GrocerEase data
    - Full backup supports clear live demonstration
    - Recovery returns the database to its previous state
    - The module improves reliability, data safety, and administrator confidence

Design instructions:
- Use one main idea per slide.
- Use simple arrows and labeled boxes for flowcharts.
- Use screenshots in large readable frames.
- Avoid long paragraphs.
- Use presenter notes explaining each backup and recovery step.

