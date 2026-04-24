<?php
session_start();
require_once '../../models/supplier.php';

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

    $supplier = new Supplier();
    $result = $supplier->RestoreSupplier($supplier_id);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "Supplier restored successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to restore supplier."
        ]);
    }

    exit;
}