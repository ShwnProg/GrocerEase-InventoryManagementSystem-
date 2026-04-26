<?php
session_start();
require_once '../../autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $supplier_id = $_POST['supplier_id'] ?? null;

    if (!$supplier_id) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid request."
        ]);
        exit;
    }

    $Supplier = new Supplier($db);
    $result = $Supplier->SoftDeleteSupplier($supplier_id);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "Supplier deleted successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to delete supplier."
        ]);
    }

    exit;
}