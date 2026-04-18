<?php
session_start();
require_once "../models/supplier.php";


if (isset($_POST['supplier_id'])) {
    $supplier_id = $_POST['supplier_id'];
} else {
     $supplier_id = $_GET['supplier_id'] ?? null;
}

 if (!$supplier_id) {
        die("Invalid supplier ID.");
    }


$supplier = new Supplier();

$supplier_info = $supplier->GetSupplierById($supplier_id);

// var_dump($_POST);
$_SESSION['page_title'] = "EDIT SUPPLIER";
$_SESSION['supplier_id'] = $supplier_id;

$error = $_SESSION['edit_error_msg'] ?? '';
$old_inputs = $_SESSION['edit_old_inputs'] ?? '';
$success_msg = $_SESSION['edit_success_msg'] ?? '';

unset($_SESSION['edit_error_msg'], $_SESSION['edit_old_inputs'], $_SESSION['edit_success_msg']);
?>
<!DOCTYPE html>
<html lang="en">

<?php include "../includes/head.php" ?>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <main class='main-content'>
        <?php include '../includes/topbar.php'; ?>
        <section class="page-content">
            <div class="tool-bar">
                <a href="suppliers.php" class="back-btn"><i class="fas fa-arrow-left"></i> back</a>
            </div>

            <div class="edit-content">
                <form action="../validation/edit_supplier/edit_supplier.php" method="POST">


                    <i class="fa-solid fa-pen-to-square"></i>
                    <p> EDIT SUPPLIER </p>

                    <?php if (!empty($success_msg)): ?>
                        <div class="success-message">
                            <?= htmlspecialchars($success_msg) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error['no_changes'])): ?>
                        <div class="no-changes">
                            <?= htmlspecialchars($error['no_changes']) ?>
                        </div>
                    <?php endif; ?>
                    <input type="hidden" name="supplier_id" value=<?= htmlspecialchars($supplier_id) ?>>
                    <!-- SUPPLIER NAME -->
                    <div class="input">
                        <label for="supplier_name">Supplier Name</label>
                        <i class="fas fa-box"></i>
                        <input type="text" name="supplier_name"
                            value="<?= htmlspecialchars($old_inputs['supplier_name'] ?? $supplier_info['supplier_name'] ?? '') ?>">
                    </div>

                    <?php if (!empty($error['supplier_name'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['supplier_name']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- CONTACT PERSON -->
                    <div class="input">
                        <label for="contact_person">Contact Person</label>
                        <i class="fas fa-box"></i>
                        <input type="text" name="contact_person"
                            value="<?= htmlspecialchars($old_inputs['contact_person'] ?? $supplier_info['contact_person'] ?? '') ?>">
                    </div>

                    <?php if (!empty($error['contact_person'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['contact_person']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- PHONE NUMBER -->
                    <div class="input">
                        <label for="phone_number">Phone Number</label>
                        <i class="fas fa-box"></i>
                        <input type="text" name="phone_number"
                            value="<?= htmlspecialchars($old_inputs['phone_number'] ?? $supplier_info['phone_number'] ?? '') ?>">
                    </div>

                    <?php if (!empty($error['phone_number'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['phone_number']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- EMAIL -->
                    <div class="input">
                        <label for="email">Email</label>
                        <i class="fas fa-box"></i>
                        <input type="email" name="email"
                            value="<?= htmlspecialchars($old_inputs['email'] ?? $supplier_info['email'] ?? '') ?>">
                    </div>

                    <?php if (!empty($error['email'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['email']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- ADDRESS -->
                    <div class="input">
                        <label for="address">Address</label>
                        <i class="fas fa-box"></i>
                        <input type="text" name="address"
                            value="<?= htmlspecialchars($old_inputs['address'] ?? $supplier_info['address'] ?? '') ?>">
                    </div>

                    <?php if (!empty($error['address'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['address']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- COMPANY NAME -->
                    <div class="input">
                        <label for="company_name">Company Name</label>
                        <i class="fas fa-box"></i>
                        <input type="text" name="company_name"
                            value="<?= htmlspecialchars($old_inputs['company_name'] ?? $supplier_info['company_name'] ?? '') ?>">
                    </div>

                    <?php if (!empty($error['company_name'])): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error['company_name']) ?>
                        </div>
                    <?php endif; ?>



                    <button type="submit" name="edit_btn">SAVE CHANGES</button>
                </form>
            </div>
        </section>
    </main>
</body>

</html>