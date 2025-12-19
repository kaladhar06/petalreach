<?php
// order.php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

// --------- DB Config ---------
$DB_HOST = "127.0.0.1";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "petalreach";

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "DB_ERROR"]);
    exit;
}

// --------- Read JSON Request ---------
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
    echo json_encode(["success" => false, "error" => "INVALID_JSON"]);
    exit;
}

// --------- Extract fields ---------
$user_id       = $data["user_id"] ?? null;
$payment_method = strtoupper($data["payment_method"] ?? "");
$items          = $data["items"] ?? [];
$subtotal       = (float)($data["subtotal"] ?? 0);
$delivery_fee   = (float)($data["delivery_fee"] ?? 0);
$total_amount   = (float)($data["total_amount"] ?? 0);

// --------- Validation ---------
$valid_methods = ["UPI", "CARD", "COD"];

$errors = [];
if (!in_array($payment_method, $valid_methods)) $errors[] = "Invalid payment_method";
if ($subtotal < 0 || $total_amount < 0) $errors[] = "Amounts cannot be negative";
if (empty($items)) $errors[] = "No items provided";

foreach ($items as $item) {
    if (empty($item["product_name"]) || empty($item["quantity"]) || empty($item["price"])) {
        $errors[] = "Invalid item data";
        break;
    }
}

if (!empty($errors)) {
    echo json_encode(["success" => false, "errors" => $errors]);
    exit;
}

// --------- Save Order ---------
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, payment_method, subtotal, delivery_fee, total_amount, status)
        VALUES (:user_id, :payment_method, :subtotal, :delivery_fee, :total_amount, 'pending')
    ");

    $stmt->execute([
        ":user_id" => $user_id,
        ":payment_method" => $payment_method,
        ":subtotal" => $subtotal,
        ":delivery_fee" => $delivery_fee,
        ":total_amount" => $total_amount
    ]);

    $order_id = $pdo->lastInsertId();

    // Insert order items
    $itemStmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_name, quantity, price)
        VALUES (:order_id, :product_name, :quantity, :price)
    ");

    foreach ($items as $it) {
        $itemStmt->execute([
            ":order_id" => $order_id,
            ":product_name" => $it["product_name"],
            ":quantity" => $it["quantity"],
            ":price" => $it["price"]
        ]);
    }

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "message" => "Order placed successfully",
        "order_id" => $order_id
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["success" => false, "error" => "ORDER_FAILED"]);
}
?>
