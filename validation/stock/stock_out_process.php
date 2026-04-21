<?php
require_once "../../models/stocks.php";
require_once "../../models/stock_movements.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $product_id   = $_POST['product_id']   ?? '';
    $product_name = $_POST['product_name'] ?? '';
    $quantity     = trim($_POST['quantity'] ?? '');
    $reason       = trim(ucfirst($_POST['reason'] ?? ''));

    $error = [];
    $stock  = new Stock();
    $currentStock = $stock->GetQuantityByProductId($product_id);

    if (empty($quantity))
        $error['quantity'] = 'Quantity is required';
    elseif ($quantity <= 0)
        $error['quantity'] = 'Please enter a valid quantity greater than 0';
    elseif ($quantity > $currentStock['quantity'])
        $error['quantity'] = 'Insufficient stock. Current stock: ' . $currentStock['quantity'];

    if (!empty($error)) {
        $_SESSION['error'] = ['out' => $error];
        $_SESSION['old']   = [
            'product_id'   => $product_id,
            'product_name' => $product_name,
            'quantity'     => $quantity,
            'reason'       => $reason,
        ];
        header("Location: ../../pages/stock.php");
        exit;
    }

    $date = date("Y-m-d H:i:s");
    $result = $stock->StockOut($product_id, $quantity, $date);

    if ($result) {
        $reference_id   = GeneratedUniqueId();
        $stock_movement = new StockMovements();
        $date_movement  = date("Y-m-d");

        $movement_result = $stock_movement->AddStockMovements(
            $quantity,
            "OUT",
            $reference_id,
            $reason,
            $date_movement,
            $product_id
        );

        if ($movement_result) {
            $_SESSION['success'] = ['out' => ['form' => 'Stock updated successfully']];
        }
    } else {
        $_SESSION['error'] = ['out' => ['form' => 'Something went wrong']];
    }

    header("Location: ../../pages/stock.php");
    exit;
}

function GeneratedUniqueId()
{
    $stock_movement = new StockMovements();
    do {
        $reference_id = "STK";
        for ($i = 0; $i < 3; $i++) {
            $reference_id .= random_int(0, 9);
        }
        $isExist = $stock_movement->IsRefIdExist($reference_id);
    } while ($isExist);

    return $reference_id;
}
