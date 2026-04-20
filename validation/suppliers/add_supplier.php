<?php
session_start();
require_once "../../models/supplier.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = trim($_POST['supplier_name'] ?? '');
    $person  = trim($_POST['contact_person'] ?? '');
    $number  = trim($_POST['phone_number'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $company = trim($_POST['company_name'] ?? '');
    // $status  = trim($_POST['status'] ?? '');

    $errors = [];

    // VALIDATE
    if (empty($name))
        $errors['supplier_name'] = "Supplier name is required.";
    if (empty($person))
        $errors['contact_person'] = "Contact person is required.";
    if (empty($number))
        $errors['phone_number'] = "Phone number is required.";
    if (empty($email))
        $errors['email'] = "Email is required.";
    if (empty($address))
        $errors['address'] = "Address is required.";
    if (empty($company))
        $errors['company_name'] = "Company name is required.";
    // if (empty($status))
    //     $errors['status'] = "Status is required.";

    if (!empty($errors)) {
        $_SESSION['errors'] = ['add' => $errors];
        $_SESSION['old'] = $_POST;
        header("Location: ../../pages/suppliers.php");
        exit;
    }

    $supplier = new Supplier();

    // CHECK DUPLICATE
    if ($supplier->checkDuplicateSupplier($name)) {
        $_SESSION['errors'] = ['add' => ['supplier_name' => "Supplier already exists."]];
        $_SESSION['old'] = $_POST;
        header("Location: ../../pages/suppliers.php");
        exit;
    }

    // INSERT
    $result = $supplier->AddSupplier($name, $person, $number, $email, $address, $company);

    if ($result) {
        $_SESSION['success'] = ['add' => "Supplier added successfully."];
    } else {
        $_SESSION['errors'] = ['add' => ['form' => "Failed to add supplier. Please try again."]];
        $_SESSION['old'] = $_POST;
    }

    header("Location: ../../pages/suppliers.php");
    exit;
}
?>