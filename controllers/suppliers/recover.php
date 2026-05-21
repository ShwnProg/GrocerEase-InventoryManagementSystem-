<?php
// session_start();
require_once __DIR__ . '/../../autoload.php';

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

    $supplier = new Supplier($db);
    $supplier_info = $supplier->GetSupplierById($supplier_id);

    if ($supplier_info && $supplier->CheckDuplicateSupplier($supplier_info['supplier_name'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Cannot restore because an active supplier with the same name already exists."
        ]);
        exit;
    }

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
