<?php
require_once '../../autoload.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier = new Supplier($db);

    $supplier_id = $_POST['supplier_id'] ?? '';

    $supplier_name = ucfirst(trim($_POST['supplier_name'] ?? ''));
    $contact_person = trim($_POST['contact_person'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');

    $error = [];

    if (empty($supplier_name) || empty($contact_person) || empty($phone_number) || empty($email) || empty($address) || empty($company_name)) {
        $error['form'] = "All fields are required.";
    }

    $original = $supplier->GetSupplierById($supplier_id);
    var_dump($error);

    $isTrue = false;
    if (empty($error)) {
        $isTrue = IsSameData($original, $supplier_name, $contact_person, $phone_number, $email, $address, $company_name) ? true : false;
        // var_dump(IsSameData($original, $supplier_name, $contact_person, $phone_number, $email, $address, $company_name));
    }

    if (!$isTrue) {
        if ($supplier_name != $original['supplier_name']) {
            if ($supplier->CheckDuplicateSupplier($supplier_name)) {
                $error['supplier_name'] = "Supplier already exists.";
            }
        }

        if (!empty($supplier_name) && strlen($supplier_name) < 4) {
            $error['supplier_name'] = 'Supplier name must be at least 4 characters.';
        }

        if (!empty($supplier_name) && strlen($supplier_name) > 50) {
            $error['supplier_name'] = 'Supplier name must not exceed 50 characters.';
        }

        if (!empty($contact_person) && strlen($contact_person) < 4) {
            $error['contact_person'] = 'Contact person must be at least 4 characters.';
        }

        if (!empty($contact_person) && strlen($contact_person) > 50) {
            $error['contact_person'] = 'Contact person must not exceed 50 characters.';
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error['email'] = 'Invalid email format.';
        }

        if (!empty($address) && strlen($address) < 4) {
            $error['address'] = 'Address must be at least 4 characters.';
        }

        if (!empty($address) && strlen($address) > 255) {
            $error['address'] = 'Address must not exceed 255 characters.';
        }

        if (!empty($company_name) && strlen($company_name) < 4) {
            $error['company_name'] = 'Company name must be at least 4 characters.';
        }

        if (!empty($company_name) && strlen($company_name) > 100) {
            $error['company_name'] = 'Company name must not exceed 100 characters.';
        }
    } else {
        $error['no_changes'] = 'No Changes';
    }


    if (!empty($error)) {
        $_SESSION['edit_error_msg'] = $error;
        $_SESSION['edit_old_inputs'] = $_POST;
        header("Location: ../../views/inventory/edit_supplier.php?supplier_id=$supplier_id");
        exit;
    }

    $supplier_name = filter_var($supplier_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contact_person = filter_var($contact_person, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $phone_number = filter_var($phone_number, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address = filter_var($address, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $company_name = filter_var($company_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $isEdited = $supplier->EditSupplier($supplier_id, $supplier_name, $contact_person, $phone_number, $email, $address, $company_name);

    if ($isEdited) {
        $_SESSION['edit_success_msg'] = 'Supplier edited successfully.';
    } else {
        $_SESSION['edit_error_msg'] = 'Failed to edit supplier. Please try again.';
    }

    header("Location: ../../views/inventory/edit_supplier.php?supplier_id=$supplier_id");
    exit;
}
function IsSameData($original, $supplier_name, $contact_person, $phone_number, $email, $address, $company_name)
{
    if (
        $supplier_name == $original['supplier_name'] &&
        $contact_person == $original['contact_person'] &&
        $phone_number == $original['phone_number'] &&
        $email == $original['email'] &&
        $address == $original['address'] &&
        $company_name == $original['company_name']
    ) {
        return true;
    }
    return false;
}
