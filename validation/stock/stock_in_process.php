<?php
require_once "../../models/stocks.php";
require_once "../../models/stock_movements.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'] ?? '';

    $quantity = trim($_POST['quantity'] ?? '');
    $reason = trim(ucfirst($_POST['reason'] ?? ''));

    $error = [];

    if (empty($quantity))
        $error['quantity'] = 'Quantity is required';
    elseif ($quantity <= 0)
        $error['quantity'] = 'Please enter a valid quantity greater than 0';


        if (!empty($error)) {
            $_SESSION['error'] = $error;
            $_SESSION['old'] = $_POST;

            header("Location: ../../pages/stock.php");
            exit;
        }

    $stock = new Stock();
    $date = date("Y-m-d H:i:s");
    $result = $stock->StockIn($product_id, $quantity, $date);

    if ($result) {
        $reference_id = GeneratedUniqueId();

        $stock_movement = new StockMovements();
        $date_movement = date("Y-m-d");

        $stock_movements_result = $stock_movement->AddStockMovements($quantity, "STOCK IN", $reference_id, $reason, $date_movement, $product_id);

        if ($stock_movements_result) {
            $_SESSION['success'] = [ 'form' => "Stock Updated"];
        }
    } else {
        $_SESSION['error'] = ['form' => 'Something Went Wrong'];
    }

    header("Location: ../../pages/stock.php");
    exit;

    // var_dump($reference_id); 


}
function GeneratedUniqueId($stock_movement = new StockMovements())
{
    do {
        $reference_id = "STK";
        for ($i = 0; $i < 3; $i++) {
            $reference_id .= random_int(0, 9);
        }
        $isExist = $stock_movement->IsRefIdExist($reference_id);
    } while ($isExist);

    return $reference_id;
}
?>