<?php
require_once '../../models/supplier.php';
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $supplier_id = $_POST['supplier_id'] ?? '';

    $Supplier = new Supplier();
    $result = $Supplier->SoftDeleteSupplier($supplier_id);

    unset($_SESSION['delete_supplier_id']);

    if($result){
        $_SESSION['success'] = ['delete' => "Supplier deleted successfully."];
    }
    else{
        $_SESSION['errors'] = ['delete' => "Failed to delete supplier. Please try again."];
    }

    header("Location: ../../pages/suppliers.php");
    exit;

}
?>