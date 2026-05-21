<?php
// session_start();
require_once __DIR__ . '/../../autoload.php';

include "../../includes/auth_check.php";

$_SESSION['page_title'] = "SUPPLIERS";

$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Suppliers per page
$base_query = $search !== '' ? 'search=' . urlencode($search) : '';

$supplier = new Supplier($db);

if (!empty($search)) {
    $suppliers = $supplier->SearchSupplier($search);
    $total_suppliers = count($suppliers);
    $total_pages = 1;
} else {
    $suppliers = $supplier->GetPaginatedSuppliers($page, $limit);
    $total_suppliers = $supplier->GetTotalActiveSuppliers();
    $total_pages = ceil($total_suppliers / $limit);
}

$open_modal = isset($_SESSION['add_supplier_error']);
$error = $_SESSION['add_supplier_error'] ?? [];
$old_inputs = $_SESSION['old_inputs'] ?? [];
$success_msg = $_SESSION['success_msg'] ?? '';
$archived_duplicate = $_SESSION['archived_duplicate'] ?? null;

$confirm_delete = false;
$delete_supplier_id = '';
$errors = $_SESSION['errors']['add'] ?? [];
$old = $_SESSION['old'] ?? [];
$success = $_SESSION['success'] ?? [];
$alert = null;

$open_add_modal = !empty($errors);

if (!empty($success['add'])) {
    $alert = [
        'icon' => 'success',
        'title' => 'Supplier added',
        'text' => $success['add'],
    ];
} elseif (!empty($errors)) {
    $alert = [
        'icon' => 'error',
        'title' => 'Supplier not saved',
        'html' => implode('<br>', array_map('htmlspecialchars', array_values($errors))),
    ];
} elseif (!empty($success_msg)) {
    $alert = [
        'icon' => 'success',
        'title' => 'Supplier added',
        'text' => $success_msg,
    ];
} elseif (!empty($error)) {
    $alert = [
        'icon' => 'error',
        'title' => 'Supplier not saved',
        'html' => implode('<br>', array_map('htmlspecialchars', array_values($error))),
    ];
}


// echo "hello $user_info[username]";
unset($_SESSION['add_supplier_error'], $_SESSION['old_inputs'], $_SESSION['success_msg'], $_SESSION['archived_duplicate']);
unset($_SESSION['success'], $_SESSION['errors']);

unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);

?>
<!DOCTYPE html>
<html lang="en">

<?php include "../../includes/head.php" ?>

<body>

    <?php include '../../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../../includes/topbar.php'; ?>
        <section class="page-content">
            <div class="toolbar">
                <div class="search-area">
                    <form method='get'>
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="search" placeholder="Search Suppliers"
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button type="submit">SEARCH</button>
                    </form>
                </div>
                <div class="add">
                    <button id="addbtn">Add Supplier</button>
                </div>
            </div>

            <!-- TABLE -->
            <div class="menu-table">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Supplier Name</th>
                            <th>Contact Person</th>
                            <th>Contact Number</th>
                            <th>Address</th>
                            <th>Company Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = ($page - 1) * $limit + 1; ?>
                        <?php if (empty($suppliers)): ?>
                            <tr>
                                <td colspan="8" style="text-align:center; color:#6b7280;">
                                    No suppliers found
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($suppliers as $sup): ?>
                                    <?php if ($sup['is_deleted'] == 1)
                                        continue; ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($sup['supplier_name']) ?></td>
                                <td><?= htmlspecialchars($sup['contact_person']) ?></td>
                                <td><?= htmlspecialchars($sup['phone_number']) ?></td>
                                <td><?= htmlspecialchars($sup['address']) ?></td>
                                <td><?= htmlspecialchars($sup['company_name']) ?></td>
                                <td class="email-cell"><?= htmlspecialchars($sup['email']) ?></td>

                                <!-- <td>
                                     <span class="badge <?= $sup['status'] == 1 ? 'active' : 'inactive' ?>">
                                        <?= $sup['status'] == 1 ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td> -->
                                <td>
                                    <!-- EDIT SUPPLIER -->
                                    <div class="actions">
                                        <!-- EDIT BUTTON -->
                                        <form action="edit_supplier.php" method="GET">
                                            <input type="hidden" name="supplier_id" value="<?= $sup['supplier_id_pk'] ?>">
                                            <button type="submit" class="edit-btn">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                <span>Edit</span>
                                            </button>
                                        </form>

                                        <!-- DELETE BUTTON -->
                                        <button type="button" class="edit-btn"
                                            onclick="deleteSupplier('<?= $sup['supplier_id_pk'] ?>', '<?= htmlspecialchars($sup['supplier_name']) ?>')">
                                            <i class="fa-solid fa-trash"></i>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
                <!-- PAGINATION -->
               <?php
                $window = 5;
                $current_chunk = (int) floor(($page - 1) / $window);
                $start = $current_chunk * $window + 1;
                $end = min($total_pages, $start + $window - 1);
                ?>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">&laquo; Prev</a>
                        <?php else: ?>
                            <span class="page-btn disabled">&laquo; Prev</span>
                        <?php endif; ?>

                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                                class="page-btn <?= $i === $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">Next &raquo;</a>
                        <?php else: ?>
                            <span class="page-btn disabled">Next &raquo;</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>


            </div>

            <!-- ADD SUPPLIER MODAL -->
            <div class="add-modal <?= $open_add_modal ? 'active' : '' ?>" id="add-modal">
                <form action="../../controllers/suppliers/add_supplier.php" method="POST">
                    <div class="header">
                        <i class="fas fa-plus"></i>
                        <p>Add Supplier</p>
                        <span id="close-modal">&times;</span>
                    </div>
                    <div class="body">

                        <div class="input">
                            <label>Supplier Name</label>
                            <i class="fas fa-user"></i>
                            <input type="text" name="supplier_name" placeholder="Supplier Name"
                                value="<?= htmlspecialchars($old['supplier_name'] ?? '') ?>">
                        </div>
                        <div class="input">
                            <label>Contact Person</label>
                            <i class="fas fa-user-tie"></i>
                            <input type="text" name="contact_person" placeholder="Contact Person"
                                value="<?= htmlspecialchars($old['contact_person'] ?? '') ?>">
                        </div>

                        <div class="input">
                            <label>Phone Number</label>
                            <i class="fas fa-phone"></i>
                            <input type="text" name="phone_number" placeholder="Phone Number"
                                value="<?= htmlspecialchars($old['phone_number'] ?? '') ?>">
                        </div>

                        <div class="input">
                            <label>Email</label>
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="Email"
                                value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                        </div>

                        <div class="input">
                            <label>Address</label>
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" name="address" placeholder="Address"
                                value="<?= htmlspecialchars($old['address'] ?? '') ?>">
                        </div>

                        <div class="input">
                            <label>Company Name</label>
                            <i class="fas fa-building"></i>
                            <input type="text" name="company_name" placeholder="Company Name"
                                value="<?= htmlspecialchars($old['company_name'] ?? '') ?>">
                        </div>

                        <button type="submit">Add Supplier</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
<?php if (!empty($alert)): ?>
    <script>
        Swal.fire(<?= json_encode($alert, JSON_UNESCAPED_SLASHES) ?>);
    </script>
<?php endif; ?>
<script src="<?= ASSET_URL ?>/js/pages.js"></script>
<?php if (!empty($archived_duplicate)): ?>
    <script>
        showArchivedDuplicatePrompt(<?= json_encode($archived_duplicate, JSON_UNESCAPED_SLASHES) ?>);
    </script>
<?php endif; ?>

</html>
